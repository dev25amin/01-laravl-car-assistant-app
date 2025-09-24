<?php

namespace App\Http\Controllers;
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

class CarAssistantController extends Controller
{
    private GeminiService $geminiService;
    private $geminiApiKey = 'AIzaSyCeeA0hvg49pDCBvV3zRmNkoZySK79U1OI';
    private $geminiApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
                $this->middleware('auth'); // يتطلب تسجيل الدخول

    }

    /**
     * عرض الصفحة الرئيسية للمساعد الذكي
     */
    public function index()
    {
        $userId = Auth::id();
        
        $carInfo = CarInfo::where('user_id', $userId)->latest()->first();
        
        // إحصائيات للعرض (للمستخدم الحالي فقط)
        $analysisCount = CarAnalysis::where('user_id', $userId)->count();
        $conditionAnalysisCount = CarAnalysis::where('user_id', $userId)
            ->where('analysis_type', 'condition_analysis')->count();
        $problemDiagnosisCount = CarAnalysis::where('user_id', $userId)
            ->where('analysis_type', 'problem_diagnosis')->count();
        
        $analyses = CarAnalysis::where('user_id', $userId)
            ->with('carInfo')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('car_assistant', compact('analyses','carInfo', 'analysisCount', 'conditionAnalysisCount', 'problemDiagnosisCount'));
    }

    /**
     * حفظ أو تحديث معلومات السيارة
     */
    public function saveCarInfo(Request $request)
    {
       
        $userId = Auth::id();
        
        // منع التكرار باستخدام معرف فريد للطلب
        $requestId = md5(serialize($request->all()) . time());
        $cacheKey = 'car_analysis_' . $userId . '_' . $requestId;
        
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
                // البحث عن سجل موجود للمستخدم الحالي أو إنشاء جديد
                $existingCarInfo = CarInfo::where('user_id', $userId)->first();
                
                if ($existingCarInfo) {
                    $existingCarInfo->update($validatedData);
                    $carInfo = $existingCarInfo;
                } else {
                    $carInfo = CarInfo::create(array_merge($validatedData, ['user_id' => $userId]));
                }

                // التحقق من وجود تحليل حديث لنفس البيانات
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
                        'isNew' => false
                    ];
                }

                // تحليل حالة السيارة
                $aiAnalysis = $this->analyzeCarConditionDirect($carInfo);

                // حفظ التحليل
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
                'car_info' => $result['carInfo'],
                'analysis' => $result['analysis'],
                'is_new_analysis' => $result['isNew']
            ]);

        } catch (\Exception $e) {
            Cache::forget($cacheKey);
            Log::error('خطأ في حفظ معلومات السيارة: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في حفظ المعلومات'
            ], 500);
        }
    }


    /**
     * تشخيص الأعطال الميكانيكية مع دعم الصور
     */
    public function diagnoseProblem(Request $request)
    {
        $userId = Auth::id();
        
        $requestId = md5($request->input('problem_description', '') . time());
        $cacheKey = 'problem_diagnosis_' . $userId . '_' . $requestId;
        
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
                    'analysisId' => $analysisId ?? null
                ];
            }, 3);

            return response()->json([
                'success' => true,
                'diagnosis' => $result['diagnosis'],
                'uploaded_images' => $result['uploaded_images']
            ]);

        } catch (\Exception $e) {
            Cache::forget($cacheKey);
            Log::error('خطأ في تشخيص العطل: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تشخيص العطل'
            ], 500);
        }
    }

    /**
     * تشخيص المشاكل مع الصور باستخدام Gemini Vision API
     */
    private function diagnoseProblemWithImages($problemDescription, $carInfo, $imagePaths)
    {
        try {
            $contents = [];
            
            // إضافة النص إلى المحتوى
            $prompt = $this->buildDiagnosisPrompt($problemDescription, $carInfo);
            
            $parts = [
                [
                    'text' => $prompt
                ]
            ];

            // إضافة الصور إلى المحتوى إذا كانت موجودة
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
                
                // إضافة تعليمات خاصة بتحليل الصور
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
            Log::error('خطأ في تشخيص العطل مع الصور: ' . $e->getMessage());
            return 'حدث خطأ في التشخيص، يرجى المحاولة مرة أخرى.';
        }
    }

    /**
     * تحليل حالة السيارة مباشرة باستخدام Gemini API
     */
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
            Log::error('خطأ في تحليل حالة السيارة: ' . $e->getMessage());
            return 'حدث خطأ في التحليل، يرجى المحاولة مرة أخرى.';
        }
    }

    /**
     * تشخيص المشاكل مباشرة باستخدام Gemini API (النسخة الأصلية بدون صور)
     */
    private function diagnoseProblemDirect($problemDescription, $carInfo)
    {
        try {
            $prompt = $this->buildDiagnosisPrompt($problemDescription, $carInfo);
            
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
            Log::error('خطأ في تشخيص العطل: ' . $e->getMessage());
            return 'حدث خطأ في التشخيص، يرجى المحاولة مرة أخرى.';
        }
    }

    /**
     * بناء نص التحليل لحالة السيارة
     */
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

    /**
     * بناء نص التشخيص للمشاكل
     */
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

    /**
     * تحليل حالة السيارة باستخدام الذكاء الاصطناعي (الطريقة الأصلية للاحتياط)
     */
    private function analyzeCarCondition($carInfo)
    {
        try {
            // تحويل معلومات السيارة إلى مصفوفة
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

            return $this->geminiService->analyzeCarCondition($carInfoArray);

        } catch (\Exception $e) {
            Log::error('خطأ في تحليل حالة السيارة: ' . $e->getMessage());
            return 'حدث خطأ في التحليل، يرجى المحاولة مرة أخرى.';
        }
    }

    /**
     * اختبار الاتصال مع خدمة Gemini
     */
    public function testGemini()
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
                                 $data['candidates'][0]['content']['parts'][0]['text'] : 'لا يوجد رد'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل في الاتصال مع خدمة Gemini',
                    'error' => $response->body()
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في اختبار الاتصال: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * حفظ التحليل في قاعدة البيانات مع منع التكرار
     */
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
            Log::error('خطأ في حفظ التحليل: ' . $e->getMessage());
            return null;
        }
    }
    /**
     * حفظ التحليل في قاعدة البيانات مع دعم الصور (الطريقة القديمة للاحتياط)
     */
    private function saveAnalysis($carInfoId, $analysisType, $inputData, $analysisResult, $imagePaths = [])
    {
        try {
            // استخراج مسارات الصور من البيانات المدخلة إذا وجدت
            $images = [];
            if (isset($inputData['images'])) {
                $images = $inputData['images'];
                unset($inputData['images']); // إزالة الصور من البيانات لتجنب التكرار
            } elseif (!empty($imagePaths)) {
                $images = $imagePaths;
            }
            
            // تحويل البيانات إلى JSON مع دعم الأحرف العربية
            $jsonInputData = json_encode($inputData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            
            // إذا فشل الترميز، استخدم سلسلة نصية بدلاً من ذلك
            if ($jsonInputData === false) {
                $jsonInputData = '{"error": "Failed to encode data"}';
            }
            
            CarAnalysis::create([
                'car_info_id' => $carInfoId,
                'analysis_type' => $analysisType,
                'input_data' => $jsonInputData,
                'analysis_result' => $analysisResult,
                'analysis_date' => now(),
                'analysis_images' => !empty($images) ? json_encode($images, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null
            ]);
            
            Log::info("تم حفظ التحليل: {$analysisType} لسيارة ID: {$carInfoId}" . (!empty($images) ? " مع " . count($images) . " صور" : ""));
        } catch (\Exception $e) {
            Log::error('خطأ في حفظ التحليل: ' . $e->getMessage());
            Log::error('تفاصيل الخطأ: ' . $e->getTraceAsString());
        }
    }

    /**
     * عرض صفحة سجل التحليلات
     */
    public function analysisHistory()
    {
        $userId = Auth::id();
        
        $analyses = CarAnalysis::where('user_id', $userId)
            ->with('carInfo')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('analysis_history', compact('analyses'));
    }

    /**
     * عرض تفاصيل تحليل معين
     */
    public function showAnalysis($id)
    {
        $userId = Auth::id();
        
        $analysis = CarAnalysis::where('user_id', $userId)
            ->with('carInfo')
            ->findOrFail($id);
            
        return view('analysis_details', compact('analysis'));
    }

    /**
     * حذف تحليل
     */
    public function deleteAnalysis($id)
    {
        try {
            $userId = Auth::id();
            
            $analysis = CarAnalysis::where('user_id', $userId)->findOrFail($id);
            $analysis->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'تم حذف التحليل بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف التحليل'
            ]);
        }
    }


    /**
 * تحليل الصورة السريع باستخدام Gemini Vision API
 */
public function quickImageAnalysis(Request $request)
{
    $userId = Auth::id();
    
    $requestId = md5(time() . $userId);
    $cacheKey = 'quick_analysis_' . $userId . '_' . $requestId;
    
    if (Cache::has($cacheKey)) {
        return response()->json([
            'success' => false,
            'message' => 'الطلب قيد المعالجة، يرجى الانتظار'
        ], 429);
    }
    
    Cache::put($cacheKey, true, 60);

    try {
        $validatedData = $request->validate([
            'quick_image' => 'required|image|mimes:jpeg,jpg,png,gif|max:5120',
        ]);

        $result = DB::transaction(function () use ($validatedData, $cacheKey, $userId) {
            $imagePath = $validatedData['quick_image']->store('quick_analysis', 'public');
            $fullPath = storage_path('app/public/' . $imagePath);

            // تحليل الصورة باستخدام Gemini Vision API
            $analysisResult = $this->analyzeCarImage($fullPath);

            // حفظ التحليل
            $carInfo = CarInfo::where('user_id', $userId)->first();
            $carInfoId = $carInfo ? $carInfo->id : null;

            $inputData = [
                'analysis_type' => 'quick_image_analysis',
                'image_path' => $imagePath,
                'timestamp' => now()->format('Y-m-d H:i:s')
            ];

            $analysisId = $this->saveAnalysisOnce(
                $userId,
                $carInfoId,
                'quick_image_analysis',
                $inputData,
                $analysisResult,
                [$imagePath]
            );

            Cache::forget($cacheKey);

            return [
                'analysis' => $analysisResult,
                'image_path' => $imagePath,
                'analysisId' => $analysisId
            ];
        }, 3);

        return response()->json([
            'success' => true,
            'analysis' => $result['analysis'],
            'image_path' => $result['image_path']
        ]);

    } catch (\Exception $e) {
        Cache::forget($cacheKey);
        Log::error('خطأ في التحليل السريع للصورة: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في تحليل الصورة'
        ], 500);
    }
}

/**
 * تحليل صورة السيارة باستخدام Gemini Vision API
 */
private function analyzeCarImage($imagePath)
{
    try {
        $prompt = $this->buildQuickAnalysisPrompt();
        
        $imageData = base64_encode(file_get_contents($imagePath));
        $mimeType = mime_content_type($imagePath);

        $response = Http::timeout(60)->post($this->geminiApiUrl . '?key=' . $this->geminiApiKey, [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $prompt
                        ],
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data' => $imageData
                            ]
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.3,
                'topK' => 20,
                'topP' => 0.8,
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
        return 'عذراً، حدث خطأ في تحليل الصورة. يرجى المحاولة مرة أخرى.';

    } catch (\Exception $e) {
        Log::error('خطأ في تحليل صورة السيارة: ' . $e->getMessage());
        return 'حدث خطأ في تحليل الصورة، يرجى المحاولة مرة أخرى.';
    }
}

/**
 * بناء النص لتحليل الصورة السريع
 */
private function buildQuickAnalysisPrompt()
{
    return "أنت خبير في السيارات والتعرف على المركبات. قم بتحليل الصورة المرفوعة وأجب باللغة العربية فقط.

من خلال الصورة، قم بتقديم المعلومات التالية بدقة:

1. **نوع المركبة** (سيارة - شاحنة - دراجة نارية - إلخ)
2. **العلامة التجارية** (الماركة)
3. **الموديل** 
4. **سنة الصنع التقريبية**
5. **الحالة الظاهرية** من خلال الصورة:
   - الحالة الخارجية (جيدة - متوسطة - سيئة)
   - أي أضرار مرئية
   - التآكل الظاهر
6. **التقييم الميكانيكي الأولي** بناءً على المظهر الخارجي
7. **ملاحظات عامة** عن المركبة

إذا لم تتمكن من التعرف على عنصر معين، اذكر ذلك بوضوح.

اجعل إجابتك منظمة وواضحة، مع استخدام العناوين لكل قسم.";
}
}