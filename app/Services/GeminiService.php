<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class GeminiService
{
    private string $apiKey;
    private string $apiUrl;
    private array $generationConfig;
    private int $timeout;

    public function __construct()
    {
        $this->apiKey = config('gemini.api_key');
        $this->apiUrl = config('gemini.api_url');
        $this->generationConfig = config('gemini.generation_config');
        $this->timeout = config('gemini.timeout');

        if (!$this->apiKey) {
            throw new \Exception('Gemini API key is not configured');
        }
    }

    /**
     * تشخيص مشكلة السيارة
     */
    public function diagnoseProblem(string $problemDescription, array $carInfo = []): string
    {
        $systemPrompt = config('gemini.prompts.diagnosis_system');
        
        $fullContext = $systemPrompt . "\n\n";
        $fullContext .= "وصف المشكلة: " . $problemDescription . "\n\n";
        
        // إضافة معلومات السيارة إذا كانت متوفرة
        if (!empty($carInfo)) {
            $fullContext .= "معلومات السيارة:\n";
            $fullContext .= "الماركة: " . ($carInfo['car_brand'] ?? 'غير محدد') . "\n";
            $fullContext .= "الموديل: " . ($carInfo['car_model'] ?? 'غير محدد') . "\n";
            $fullContext .= "سنة الصنع: " . ($carInfo['car_year'] ?? 'غير محدد') . "\n";
            $fullContext .= "الكيلومترات: " . ($carInfo['current_mileage'] ?? 'غير محدد') . "\n";
            $fullContext .= "آخر تغيير زيت: " . ($carInfo['last_oil_change'] ?? 'غير محدد') . "\n";
            $fullContext .= "آخر صيانة: " . ($carInfo['last_maintenance'] ?? 'غير محدد') . "\n";
            $fullContext .= "مستوى الوقود: " . ($carInfo['fuel_level'] ?? 'غير محدد') . " لتر\n";
        }

        return $this->callGeminiAPI($fullContext);
    }

    /**
     * تحليل حالة السيارة
     */
    public function analyzeCarCondition(array $carInfo): string
    {
        $systemPrompt = config('gemini.prompts.analysis_system');
        
        $analysisPrompt = $systemPrompt . "\n\n";
        $analysisPrompt .= "معلومات السيارة:\n";
        $analysisPrompt .= "الماركة: " . ($carInfo['car_brand'] ?? 'غير محدد') . "\n";
        $analysisPrompt .= "الموديل: " . ($carInfo['car_model'] ?? 'غير محدد') . "\n";
        $analysisPrompt .= "سنة الصنع: " . ($carInfo['car_year'] ?? 'غير محدد') . "\n";
        $analysisPrompt .= "الكيلومترات الحالية: " . ($carInfo['current_mileage'] ?? 'غير محدد') . "\n";
        $analysisPrompt .= "آخر تغيير زيت: " . ($carInfo['last_oil_change'] ?? 'غير محدد') . "\n";
        $analysisPrompt .= "آخر صيانة: " . ($carInfo['last_maintenance'] ?? 'غير محدد') . "\n";
        $analysisPrompt .= "مستوى الوقود: " . ($carInfo['fuel_level'] ?? 'غير محدد') . " لتر\n";
        $analysisPrompt .= "ملاحظات: " . ($carInfo['notes'] ?? 'لا توجد') . "\n\n";
        $analysisPrompt .= "المطلوب: تحليل شامل لحالة السيارة مع نصائح الصيانة والتحسين";

        return $this->callGeminiAPI($analysisPrompt);
    }

    /**
     * استدعاء Gemini API العام
     */
    public function callGeminiAPI(string $prompt): string
    {
        try {
            Log::info('استدعاء Gemini API', [
                'prompt_length' => strlen($prompt),
                'api_url' => $this->apiUrl
            ]);

            // التحقق من وجود مفتاح API
            if (empty($this->apiKey)) {
                throw new \Exception('مفتاح Gemini API غير متوفر أو فارغ');
            }

            // التحقق من طول النص
            if (strlen($prompt) < 10) {
                throw new \Exception('النص المدخل قصير جداً');
            }

            if (strlen($prompt) > 10000) {
                throw new \Exception('النص المدخل طويل جداً');
            }

            $response = Http::timeout($this->timeout)
                ->retry(2, 1000) // إعادة المحاولة مرتين بفاصل ثانية واحدة
                ->post($this->apiUrl . '?key=' . $this->apiKey, [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $prompt
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => $this->generationConfig,
                    'safetySettings' => [
                        [
                            'category' => 'HARM_CATEGORY_HARASSMENT',
                            'threshold' => 'BLOCK_NONE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_HATE_SPEECH',
                            'threshold' => 'BLOCK_NONE'
                        ]
                    ]
                ]);

            if (!$response->successful()) {
                $statusCode = $response->status();
                $responseBody = $response->body();
                
                Log::error('فشل في طلب Gemini API', [
                    'status' => $statusCode,
                    'response' => $responseBody
                ]);
                
                // معالجة أخطاء مختلفة حسب رمز الحالة
                switch ($statusCode) {
                    case 400:
                        throw new \Exception('طلب غير صحيح - تحقق من صيغة البيانات المرسلة');
                    case 401:
                        throw new \Exception('مفتاح API غير صحيح أو منتهي الصلاحية');
                    case 403:
                        throw new \Exception('ليس لديك صلاحية للوصول لهذه الخدمة');
                    case 429:
                        throw new \Exception('تم تجاوز الحد المسموح من الطلبات - حاول مرة أخرى بعد قليل');
                    case 500:
                        throw new \Exception('خطأ في خادم Google - حاول مرة أخرى لاحقاً');
                    default:
                        throw new \Exception("خطأ في الخادم: HTTP {$statusCode}");
                }
            }

            $responseData = $response->json();
            Log::info('استلام رد من Gemini API', ['has_candidates' => isset($responseData['candidates'])]);
            
            // التحقق من وجود المحتوى في الرد
            if (!isset($responseData['candidates']) || empty($responseData['candidates'])) {
                Log::error('رد فارغ من Gemini API', $responseData);
                throw new \Exception('لم يتم الحصول على رد من خدمة الذكاء الاصطناعي');
            }

            $candidate = $responseData['candidates'][0];
            
            // التحقق من حالة المحتوى
            if (isset($candidate['finishReason']) && $candidate['finishReason'] !== 'STOP') {
                $finishReason = $candidate['finishReason'];
                Log::warning('Gemini API توقف لسبب غير عادي', ['finishReason' => $finishReason]);
                
                switch ($finishReason) {
                    case 'SAFETY':
                        throw new \Exception('تم منع الرد لأسباب تتعلق بالأمان - حاول إعادة صياغة السؤال');
                    case 'MAX_TOKENS':
                        throw new \Exception('الرد طويل جداً - حاول تبسيط السؤال');
                    default:
                        throw new \Exception('تم إيقاف التوليد: ' . $finishReason);
                }
            }
            
            if (isset($candidate['content']['parts'][0]['text'])) {
                $result = $candidate['content']['parts'][0]['text'];
                
                // التحقق من جودة الرد
                if (strlen(trim($result)) < 50) {
                    Log::warning('رد قصير جداً من Gemini API', ['response_length' => strlen($result)]);
                }
                
                Log::info('تم معالجة رد Gemini API بنجاح', ['response_length' => strlen($result)]);
                return trim($result);
            } else {
                Log::error('بنية رد غير متوقعة من Gemini API', $responseData);
                throw new \Exception('تنسيق غير متوقع في رد الذكاء الاصطناعي');
            }

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error('فشل في استدعاء Gemini API', [
                'error' => $errorMessage,
                'trace' => $e->getTraceAsString()
            ]);
            
            // رسائل خطأ مخصصة وودودة للمستخدم
            if (str_contains($errorMessage, 'timeout') || str_contains($errorMessage, 'cURL')) {
                return 'انتهت مهلة الاتصال. قد تكون الخدمة مزدحمة، يرجى المحاولة مرة أخرى بعد قليل.';
            } elseif (str_contains($errorMessage, 'API key')) {
                return 'خطأ في إعدادات النظام. يرجى التواصل مع الدعم الفني.';
            } elseif (str_contains($errorMessage, '429') || str_contains($errorMessage, 'الحد المسموح')) {
                return 'تم تجاوز عدد الطلبات المسموح. يرجى الانتظار دقيقة واحدة ثم المحاولة مرة أخرى.';
            } elseif (str_contains($errorMessage, 'قصير جداً')) {
                return 'يرجى إدخال وصف أكثر تفصيلاً للمشكلة.';
            } elseif (str_contains($errorMessage, 'طويل جداً')) {
                return 'الوصف طويل جداً. يرجى تبسيط الوصف والتركيز على النقاط الأساسية.';
            } elseif (str_contains($errorMessage, 'الأمان')) {
                return 'لم يتمكن النظام من معالجة طلبك. يرجى إعادة صياغة الوصف بطريقة أخرى.';
            } else {
                return 'عذراً، واجهنا مشكلة مؤقتة في خدمة التحليل الذكي. يرجى المحاولة مرة أخرى خلال بضع دقائق.';
            }
        }
    }

    /**
     * تغيير نموذج Gemini المستخدم
     */
    public function setModel(string $model): self
    {
        $models = config('gemini.models');
        
        if (!isset($models[$model])) {
            throw new \Exception("Model '{$model}' is not supported");
        }

        $this->apiUrl = $models[$model];
        return $this;
    }

    /**
     * تحديث إعدادات التوليد
     */
    public function setGenerationConfig(array $config): self
    {
        $this->generationConfig = array_merge($this->generationConfig, $config);
        return $this;
    }

    /**
     * الحصول على قائمة النماذج المتاحة
     */
    public function getAvailableModels(): array
    {
        return array_keys(config('gemini.models'));
    }

    /**
     * اختبار الاتصال مع Gemini API
     */
    public function testConnection(): array
    {
        try {
            $testPrompt = "قل مرحبا باللغة العربية";
            $response = $this->callGeminiAPI($testPrompt);
            
            return [
                'success' => true,
                'message' => 'الاتصال مع خدمة Gemini يعمل بشكل صحيح',
                'response' => $response
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'فشل الاتصال مع خدمة Gemini: ' . $e->getMessage(),
                'response' => null
            ];
        }
    }

    /**
     * معلومات حول الاستخدام والحدود
     */
    public function getUsageInfo(): array
    {
        return [
            'api_url' => $this->apiUrl,
            'timeout' => $this->timeout,
            'generation_config' => $this->generationConfig,
            'available_models' => $this->getAvailableModels()
        ];
    }
}