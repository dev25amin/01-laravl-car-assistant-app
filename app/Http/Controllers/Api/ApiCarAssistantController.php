<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarInfo;
use App\Models\CarAnalysis;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class ApiCarAssistantController extends Controller
{
    private GeminiService $geminiService;
    private $geminiApiKey = 'AIzaSyCeeA0hvg49pDCBvV3zRmNkoZySK79U1OI';
    private $geminiApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Get dashboard data and statistics
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $userId = Auth::id();
            
            $carInfo = CarInfo::where('user_id', $userId)->latest()->first();
            
            // Statistics for the current user
            $analysisCount = CarAnalysis::where('user_id', $userId)->count();
            $conditionAnalysisCount = CarAnalysis::where('user_id', $userId)
                ->where('analysis_type', 'condition_analysis')->count();
            $problemDiagnosisCount = CarAnalysis::where('user_id', $userId)
                ->where('analysis_type', 'problem_diagnosis')->count();
            
            $recentAnalyses = CarAnalysis::where('user_id', $userId)
                ->with('carInfo')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'car_info' => $carInfo,
                    'statistics' => [
                        'total_analyses' => $analysisCount,
                        'condition_analyses' => $conditionAnalysisCount,
                        'problem_diagnoses' => $problemDiagnosisCount
                    ],
                    'recent_analyses' => $recentAnalyses
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting dashboard data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في استرجاع البيانات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current car info
     * 
     * @return JsonResponse
     */
    public function getCarInfo(): JsonResponse
    {
        try {
            $userId = Auth::id();
            $carInfo = CarInfo::where('user_id', $userId)->latest()->first();
            
            return response()->json([
                'success' => true,
                'data' => $carInfo
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting car info: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في استرجاع معلومات السيارة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save or update car information
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function saveCarInfo(Request $request): JsonResponse
    {
        $userId = Auth::id();
        
        // Prevent duplication using unique request ID
        $requestId = md5(serialize($request->all()) . time());
        $cacheKey = 'car_analysis_api_' . $userId . '_' . $requestId;
        
        if (Cache::has($cacheKey)) {
            return response()->json([
                'success' => false,
                'message' => 'الطلب قيد المعالجة، يرجى الانتظار'
            ], 429);
        }
        
        Cache::put($cacheKey, true, 60);

        try {
            $validatedData = $request->validate([
                'last_oil_change' => 'nullable|date',
                'current_mileage' => 'nullable|integer|min:0',
                'last_maintenance' => 'nullable|date',
                'fuel_level' => 'nullable|numeric|min:0|max:100',
                'notes' => 'nullable|string|max:1000',
                'car_brand' => 'nullable|string|max:100',
                'car_model' => 'nullable|string|max:100',
                'car_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1)
            ]);

            $result = DB::transaction(function () use ($validatedData, $cacheKey, $userId) {
                // Find existing record for current user or create new one
                $existingCarInfo = CarInfo::where('user_id', $userId)->first();
                
                if ($existingCarInfo) {
                    $existingCarInfo->update($validatedData);
                    $carInfo = $existingCarInfo;
                } else {
                    $carInfo = CarInfo::create(array_merge($validatedData, ['user_id' => $userId]));
                }

                // Check for recent analysis with same data
                $recentAnalysis = CarAnalysis::where('user_id', $userId)
                    ->where('car_info_id', $carInfo->id)
                    ->where('analysis_type', 'condition_analysis')
                    ->where('created_at', '>=', now()->subMinutes(5))
                    ->first();

                if ($recentAnalysis) {
                    Cache::forget($cacheKey);
                    return [
                        'carInfo' => $carInfo,
                        'analysis' => $recentAnalysis->analysis_result,
                        'isNew' => false,
                        'analysisId' => $recentAnalysis->id
                    ];
                }

                // Analyze car condition
                $aiAnalysis = $this->analyzeCarConditionDirect($carInfo);

                // Save analysis
                $analysisId = $this->saveAnalysisOnce(
                    $userId,
                    $carInfo->id,
                    'condition_analysis',
                    $validatedData,
                    $aiAnalysis
                );

                Cache::forget($cacheKey);

                return [
                    'carInfo' => $carInfo,
                    'analysis' => $aiAnalysis,
                    'isNew' => true,
                    'analysisId' => $analysisId
                ];
            }, 3);

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ معلومات السيارة بنجاح',
                'data' => [
                    'car_info' => $result['carInfo'],
                    'analysis' => $result['analysis'],
                    'is_new_analysis' => $result['isNew'],
                    'analysis_id' => $result['analysisId']
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Cache::forget($cacheKey);
            return response()->json([
                'success' => false,
                'message' => 'خطأ في صحة البيانات المدخلة',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Cache::forget($cacheKey);
            Log::error('Error saving car info: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في حفظ المعلومات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Diagnose mechanical problems with image support
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function diagnoseProblem(Request $request): JsonResponse
    {
        $userId = Auth::id();
        
        $requestId = md5($request->input('problem_description', '') . time());
        $cacheKey = 'problem_diagnosis_api_' . $userId . '_' . $requestId;
        
        if (Cache::has($cacheKey)) {
            return response()->json([
                'success' => false,
                'message' => 'الطلب قيد المعالجة، يرجى الانتظار'
            ], 429);
        }
        
        Cache::put($cacheKey, true, 60);

        try {
            $validatedData = $request->validate([
                'problem_description' => 'required|string|min:10|max:2000',
                'problem_images.*' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:5120',
            ]);

            $problemDescription = $validatedData['problem_description'];
            $carInfo = CarInfo::where('user_id', $userId)->latest()->first();
            
            $carInfoArray = [];
            if ($carInfo) {
                $carInfoArray = [
                    'car_brand' => $carInfo->car_brand,
                    'car_model' => $carInfo->car_model,
                    'car_year' => $carInfo->car_year,
                    'current_mileage' => $carInfo->current_mileage,
                    'last_oil_change' => $carInfo->last_oil_change ? $carInfo->last_oil_change->format('Y-m-d') : null,
                    'last_maintenance' => $carInfo->last_maintenance ? $carInfo->last_maintenance->format('Y-m-d') : null,
                    'fuel_level' => $carInfo->fuel_level,
                    'notes' => $carInfo->notes
                ];
            }

            $result = DB::transaction(function () use ($problemDescription, $carInfoArray, $request, $carInfo, $cacheKey, $userId) {
                $imagePaths = [];
                if ($request->hasFile('problem_images')) {
                    foreach ($request->file('problem_images') as $image) {
                        $imagePath = $image->store('problem_images', 'public');
                        $imagePaths[] = $imagePath;
                    }
                }

                $diagnosis = $this->diagnoseProblemWithImages($problemDescription, $carInfoArray, $imagePaths);

                $analysisId = null;
                if ($carInfo) {
                    $inputDataToSave = array_merge($carInfoArray, [
                        'problem_description' => $problemDescription,
                    ]);
                    
                    $analysisId = $this->saveAnalysisOnce(
                        $userId,
                        $carInfo->id,
                        'problem_diagnosis',
                        $inputDataToSave,
                        $diagnosis,
                        $imagePaths
                    );
                }

                Cache::forget($cacheKey);
                
                return [
                    'diagnosis' => $diagnosis,
                    'uploaded_images' => count($imagePaths),
                    'image_paths' => $imagePaths,
                    'analysisId' => $analysisId
                ];
            }, 3);

            return response()->json([
                'success' => true,
                'message' => 'تم تشخيص المشكلة بنجاح',
                'data' => [
                    'diagnosis' => $result['diagnosis'],
                    'uploaded_images_count' => $result['uploaded_images'],
                    'image_paths' => $result['image_paths'],
                    'analysis_id' => $result['analysisId']
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Cache::forget($cacheKey);
            return response()->json([
                'success' => false,
                'message' => 'خطأ في صحة البيانات المدخلة',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Cache::forget($cacheKey);
            Log::error('Error diagnosing problem: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تشخيص العطل',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get analysis history with pagination
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function analysisHistory(Request $request): JsonResponse
    {
        try {
            $userId = Auth::id();
            $perPage = $request->input('per_page', 10);
            $analysisType = $request->input('type'); // 'condition_analysis' or 'problem_diagnosis'
            
            $query = CarAnalysis::where('user_id', $userId)->with('carInfo');
            
            if ($analysisType) {
                $query->where('analysis_type', $analysisType);
            }
            
            $analyses = $query->orderBy('created_at', 'desc')
                            ->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $analyses
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting analysis history: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في استرجاع سجل التحليلات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show specific analysis details
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function showAnalysis(int $id): JsonResponse
    {
        try {
            $userId = Auth::id();
            
            $analysis = CarAnalysis::where('user_id', $userId)
                ->with('carInfo')
                ->findOrFail($id);
            
            // Parse JSON data
            $inputData = json_decode($analysis->input_data, true);
            $images = json_decode($analysis->analysis_images, true);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $analysis->id,
                    'analysis_type' => $analysis->analysis_type,
                    'analysis_result' => $analysis->analysis_result,
                    'analysis_date' => $analysis->analysis_date,
                    'input_data' => $inputData,
                    'images' => $images,
                    'car_info' => $analysis->carInfo,
                    'created_at' => $analysis->created_at,
                    'updated_at' => $analysis->updated_at
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error showing analysis: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في استرجاع التحليل',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Delete analysis
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function deleteAnalysis(int $id): JsonResponse
    {
        try {
            $userId = Auth::id();
            
            $analysis = CarAnalysis::where('user_id', $userId)->findOrFail($id);
            
            // Delete associated images if they exist
            if ($analysis->analysis_images) {
                $images = json_decode($analysis->analysis_images, true);
                if (is_array($images)) {
                    foreach ($images as $imagePath) {
                        Storage::disk('public')->delete($imagePath);
                    }
                }
            }
            
            $analysis->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'تم حذف التحليل بنجاح'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error deleting analysis: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف التحليل',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test Gemini API connection
     * 
     * @return JsonResponse
     */
    public function testGemini(): JsonResponse
    {
        try {
            $response = Http::timeout(10)->post($this->geminiApiUrl . '?key=' . $this->geminiApiKey, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => 'مرحبا، هذا اختبار للاتصال. يرجى الرد بكلمة "تم" فقط.'
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'maxOutputTokens' => 10,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'message' => 'تم الاتصال بنجاح مع خدمة Gemini',
                    'response' => isset($data['candidates'][0]['content']['parts'][0]['text']) ? 
                                 $data['candidates'][0]['content']['parts'][0]['text'] : 'لا يوجد رد',
                    'status_code' => $response->status()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل في الاتصال مع خدمة Gemini',
                    'error' => $response->body(),
                    'status_code' => $response->status()
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في اختبار الاتصال: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Private methods (same as original controller)
    
    private function diagnoseProblemWithImages($problemDescription, $carInfo, $imagePaths)
    {
        try {
            $contents = [];
            
            $prompt = $this->buildDiagnosisPrompt($problemDescription, $carInfo);
            
            $parts = [
                [
                    'text' => $prompt
                ]
            ];

            if (!empty($imagePaths)) {
                foreach ($imagePaths as $imagePath) {
                    $fullPath = storage_path('app/public/' . $imagePath);
                    
                    if (file_exists($fullPath)) {
                        $imageData = base64_encode(file_get_contents($fullPath));
                        $mimeType = mime_content_type($fullPath);
                        
                        $parts[] = [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data' => $imageData
                            ]
                        ];
                    }
                }
                
                $parts[] = [
                    'text' => "\n\nيرجى تحليل الصور المرفوعة وربطها بوصف المشكلة لتقديم تشخيص أكثر دقة."
                ];
            }

            $contents[] = [
                'parts' => $parts
            ];

            $response = Http::timeout(45)->post($this->geminiApiUrl . '?key=' . $this->geminiApiKey, [
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1024,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    return $data['candidates'][0]['content']['parts'][0]['text'];
                }
            }

            Log::error('Gemini API Response Error: ' . $response->body());
            return 'عذراً، حدث خطأ في الاتصال بخدمة الذكاء الاصطناعي. يرجى المحاولة مرة أخرى.';

        } catch (\Exception $e) {
            Log::error('Error diagnosing problem with images: ' . $e->getMessage());
            return 'حدث خطأ في التشخيص، يرجى المحاولة مرة أخرى.';
        }
    }

    private function analyzeCarConditionDirect($carInfo)
    {
        try {
            $prompt = $this->buildCarAnalysisPrompt($carInfo);
            
            $response = Http::timeout(30)->post($this->geminiApiUrl . '?key=' . $this->geminiApiKey, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1024,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    return $data['candidates'][0]['content']['parts'][0]['text'];
                }
            }

            Log::error('Gemini API Response Error: ' . $response->body());
            return 'عذراً، حدث خطأ في الاتصال بخدمة الذكاء الاصطناعي. يرجى المحاولة مرة أخرى.';

        } catch (\Exception $e) {
            Log::error('Error analyzing car condition: ' . $e->getMessage());
            return 'حدث خطأ في التحليل، يرجى المحاولة مرة أخرى.';
        }
    }

    private function buildCarAnalysisPrompt($carInfo)
    {
        $carDetails = [];
        
        if ($carInfo->car_brand) $carDetails[] = "العلامة التجارية: {$carInfo->car_brand}";
        if ($carInfo->car_model) $carDetails[] = "الموديل: {$carInfo->car_model}";
        if ($carInfo->car_year) $carDetails[] = "سنة الصنع: {$carInfo->car_year}";
        if ($carInfo->current_mileage) $carDetails[] = "المسافة المقطوعة: {$carInfo->current_mileage} كم";
        if ($carInfo->last_oil_change) $carDetails[] = "آخر تغيير زيت: {$carInfo->last_oil_change->format('Y-m-d')}";
        if ($carInfo->last_maintenance) $carDetails[] = "آخر صيانة: {$carInfo->last_maintenance->format('Y-m-d')}";
        if ($carInfo->fuel_level) $carDetails[] = "مستوى الوقود: {$carInfo->fuel_level}%";
        if ($carInfo->notes) $carDetails[] = "ملاحظات: {$carInfo->notes}";

        $carDetailsText = implode("\n", $carDetails);

        return "أنت خبير في صيانة السيارات. قم بتحليل حالة السيارة التالية وقدم نصائح للصيانة:

معلومات السيارة:
{$carDetailsText}

التاريخ الحالي: " . now()->format('Y-m-d') . "

يرجى تقديم:
1. تقييم عام لحالة السيارة
2. نصائح للصيانة الوقائية
3. التنبيهات والتحذيرات إن وجدت
4. توقيتات الصيانة القادمة المقترحة

اجعل الإجابة بالعربية وواضحة ومفيدة.";
    }

    private function buildDiagnosisPrompt($problemDescription, $carInfo)
    {
        $carDetails = [];
        
        if (!empty($carInfo['car_brand'])) $carDetails[] = "العلامة التجارية: {$carInfo['car_brand']}";
        if (!empty($carInfo['car_model'])) $carDetails[] = "الموديل: {$carInfo['car_model']}";
        if (!empty($carInfo['car_year'])) $carDetails[] = "سنة الصنع: {$carInfo['car_year']}";
        if (!empty($carInfo['current_mileage'])) $carDetails[] = "المسافة المقطوعة: {$carInfo['current_mileage']} كم";
        if (!empty($carInfo['last_oil_change'])) $carDetails[] = "آخر تغيير زيت: {$carInfo['last_oil_change']}";
        if (!empty($carInfo['last_maintenance'])) $carDetails[] = "آخر صيانة: {$carInfo['last_maintenance']}";
        if (!empty($carInfo['fuel_level'])) $carDetails[] = "مستوى الوقود: {$carInfo['fuel_level']}%";
        if (!empty($carInfo['notes'])) $carDetails[] = "ملاحظات: {$carInfo['notes']}";

        $carDetailsText = !empty($carDetails) ? implode("\n", $carDetails) : "لا توجد معلومات مسجلة عن السيارة";

        return "أنت خبير في تشخيص أعطال السيارات. قم بتشخيص المشكلة التالية:

وصف المشكلة:
{$problemDescription}

معلومات السيارة:
{$carDetailsText}

يرجى تقديم:
1. التشخيص المحتمل للمشكلة
2. الأسباب المحتملة
3. الحلول المقترحة
4. مدى خطورة المشكلة
5. هل يمكن قيادة السيارة أم لا
6. التكلفة التقريبية للإصلاح (إذا أمكن)

اجعل الإجابة بالعربية وواضحة ومفصلة.";
    }

    private function saveAnalysisOnce($userId, $carInfoId, $analysisType, $inputData, $analysisResult, $imagePaths = [])
    {
        try {
            $dataHash = md5(serialize($inputData) . $analysisResult);
            
            $existingAnalysis = CarAnalysis::where('user_id', $userId)
                ->where('car_info_id', $carInfoId)
                ->where('analysis_type', $analysisType)
                ->where('created_at', '>=', now()->subMinutes(5))
                ->whereRaw("MD5(CONCAT(input_data, analysis_result)) = ?", [$dataHash])
                ->first();
                
            if ($existingAnalysis) {
                return $existingAnalysis->id;
            }
            
            $images = [];
            if (!empty($imagePaths)) {
                $images = $imagePaths;
            }
            
            $jsonInputData = json_encode($inputData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            
            if ($jsonInputData === false) {
                $jsonInputData = '{"error": "Failed to encode data"}';
            }
            
            $analysis = CarAnalysis::create([
                'user_id' => $userId,
                'car_info_id' => $carInfoId,
                'analysis_type' => $analysisType,
                'input_data' => $jsonInputData,
                'analysis_result' => $analysisResult,
                'analysis_date' => now(),
                'analysis_images' => !empty($images) ? json_encode($images, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null
            ]);
            
            return $analysis->id;
            
        } catch (\Exception $e) {
            Log::error('Error saving analysis: ' . $e->getMessage());
            return null;
        }
    }
}