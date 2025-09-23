<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('gemini', function ($app) {
            return new GeminiService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // تكوين HTTP client خاص بـ Gemini
        Http::macro('gemini', function () {
            return Http::timeout(config('services.gemini.timeout.request', 180))
                ->connectTimeout(config('services.gemini.timeout.connect', 30))
                ->retry(config('services.gemini.timeout.retry_attempts', 3), config('services.gemini.timeout.retry_delay', 2000))
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'Laravel-Gemini-Client/1.0'
                ]);
        });

        // تسجيل أحداث Gemini للمراقبة
        if (config('services.gemini.logging.enabled', true)) {
            $this->registerGeminiLogging();
        }
    }

    /**
     * تسجيل أحداث Gemini
     */
    protected function registerGeminiLogging(): void
    {
        // يمكن إضافة event listeners هنا للمراقبة
    }
}

/**
 * خدمة Gemini المحسنة
 */
class GeminiService
{
    protected $config;
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->config = config('services.gemini');
        $this->apiKey = $this->getValidApiKey();
        $this->baseUrl = $this->config['base_url'];
    }

    /**
     * الحصول على API Key صحيح
     */
    protected function getValidApiKey(): string
    {
        // محاولة الحصول على API Key من مصادر متعددة
        $apiKey = $this->config['api_key'] ?? 
                  env('GEMINI_API_KEY') ?? 
                  $_ENV['GEMINI_API_KEY'] ?? 
                  getenv('GEMINI_API_KEY');

        if (empty($apiKey)) {
            throw new \Exception($this->config['error_messages']['api_key_missing']);
        }

        if (strlen(trim($apiKey)) < 30) {
            throw new \Exception($this->config['error_messages']['api_key_invalid']);
        }

        return trim($apiKey);
    }

    /**
     * البحث عن الخدمات والشركات مع التفكير خطوة بخطوة
     */
    public function searchServicesStepByStep(array $searchData): array
    {
        try {
            $prompt = $this->buildStepByStepPrompt($searchData);
            $models = $this->config['models']['all'];

            foreach ($models as $model) {
                try {
                    Log::info("Attempting search with model: {$model}");
                    
                    $response = $this->makeRequest($model, $prompt);
                    
                    if ($response['success']) {
                        $parsed = $this->parseResponse($response['data']);
                        
                        if ($parsed['success']) {
                            $parsed['model_used'] = $model;
                            Log::info("Successful search with model: {$model}");
                            return $parsed;
                        }
                    }
                    
                } catch (\Exception $e) {
                    Log::warning("Model {$model} failed: " . $e->getMessage());
                    continue;
                }
            }

            throw new \Exception('فشل في جميع النماذج المتاحة');

        } catch (\Exception $e) {
            Log::error('Error in searchServicesStepByStep: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * إرسال طلب إلى Gemini API
     */
    protected function makeRequest(string $model, string $prompt): array
    {
        try {
            $url = "{$this->baseUrl}/{$model}:generateContent?key={$this->apiKey}";
            
            $requestData = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => $this->config['generation_config'],
                'safetySettings' => $this->buildSafetySettings()
            ];

            if (config('services.gemini.logging.log_requests', true)) {
                Log::info("Sending request to Gemini", ['model' => $model, 'url' => $url]);
            }

            $response = Http::gemini()->post($url, $requestData);

            if ($response->successful()) {
                $responseData = $response->json();
                
                if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                    return [
                        'success' => true,
                        'data' => $responseData['candidates'][0]['content']['parts'][0]['text']
                    ];
                }
            }

            return [
                'success' => false,
                'error' => $this->handleApiError($response)
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * معالجة أخطاء API
     */
    protected function handleApiError($response): string
    {
        $statusCode = $response->status();
        $errorMessages = $this->config['error_messages'];

        switch ($statusCode) {
            case 400:
                return $errorMessages['api_key_invalid'];
            case 403:
                return $errorMessages['quota_exceeded'];
            case 404:
                return $errorMessages['model_not_found'];
            case 503:
                return $errorMessages['service_unavailable'];
            default:
                return "خطأ HTTP: {$statusCode}";
        }
    }

    /**
     * بناء إعدادات الأمان
     */
    protected function buildSafetySettings(): array
    {
        $safetyConfig = $this->config['safety_settings'];
        
        return [
            [
                'category' => 'HARM_CATEGORY_HARASSMENT',
                'threshold' => $safetyConfig['harassment']
            ],
            [
                'category' => 'HARM_CATEGORY_HATE_SPEECH',
                'threshold' => $safetyConfig['hate_speech']
            ],
            [
                'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                'threshold' => $safetyConfig['sexually_explicit']
            ],
            [
                'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                'threshold' => $safetyConfig['dangerous_content']
            ]
        ];
    }

    /**
     * بناء prompt للتفكير خطوة بخطوة
     */
    protected function buildStepByStepPrompt(array $searchData): string
    {
        $serviceConfig = $this->config['service_search'];
        $moroccoConfig = $this->config['morocco_config'];
        
        return "أنت خبير في البحث عن الخدمات والشركات المغربية. يجب أن تفكر خطوة بخطوة وتقدم معلومات حقيقية ومفصلة.

معلومات العميل:
- الاسم: {$searchData['name']}
- الموقع: {$searchData['location']}
- نوع الخدمة: {$searchData['service_type']}
- فئة الخدمة: {$searchData['service_category']}
- وصف الخدمة: {$searchData['service_description']}
- مستوى الإلحاح: {$searchData['urgency_level']}
- الميزانية: {$this->formatBudget($searchData)}
- متطلبات خاصة: " . ($searchData['specific_requirements'] ?: 'لا يوجد') . "

=== يجب أن تفكر خطوة بخطوة كالتالي: ===

الخطوة 1: تحليل احتياجات العميل
- حلل نوع الخدمة المطلوبة بعمق
- حدد المتطلبات الأساسية والإضافية
- قيم مستوى الإلحاح والميزانية

الخطوة 2: البحث في السوق المغربي
- ابحث عن الشركات المتخصصة في هذا المجال
- حدد الشركات الموجودة في نفس المدينة أو قريباً منها
- قيم سمعة وخبرة كل شركة

الخطوة 3: جمع المعلومات التفصيلية
- اجمع معلومات التواصل الحقيقية (أرقام الهاتف، الإيميلات، المواقع الإلكترونية)
- حدد نطاقات الأسعار الفعلية في السوق المغربي
- اجمع تفاصيل عن خدمات كل شركة

الخطوة 4: حساب نسبة المطابقة
- قارن خدمات كل شركة مع احتياجات العميل
- احسب نسبة المطابقة بدقة (1-100%)
- رتب النتائج حسب الأولوية

الخطوة 5: إنشاء التوصيات
- اختر أفضل {$serviceConfig['max_services']} خدمة مناسبة
- اختر أفضل {$serviceConfig['max_companies']} شركة متخصصة
- تأكد من صحة جميع المعلومات

=== متطلبات إلزامية: ===
1. جميع الشركات يجب أن تكون مغربية حقيقية
2. أرقام الهاتف المغربية تبدأ بـ {$moroccoConfig['phone_prefix']}
3. الإيميلات يجب أن تنتهي بـ " . implode(' أو ', $moroccoConfig['email_domains']) . "
4. المواقع الإلكترونية يجب أن تكون واقعية
5. الأسعار بـ{$moroccoConfig['currency']}
6. المواقع الجغرافية من: " . implode(', ', $moroccoConfig['default_cities']) . "

=== أعطني النتائج بتنسيق JSON صحيح فقط: ===

{
  \"thinking_process\": {
    \"step1_analysis\": \"تحليل شامل لاحتياجات العميل...\",
    \"step2_market_research\": \"نتائج البحث في السوق المغربي...\",
    \"step3_company_details\": \"تفاصيل الشركات المختارة...\",
    \"step4_matching\": \"عملية حساب المطابقة...\",
    \"step5_recommendations\": \"الخلاصة والتوصيات...\"
  },
  \"services\": [
    {
      \"service_name\": \"اسم الخدمة المحددة بدقة\",
      \"company\": \"اسم الشركة المغربية الحقيقية\",
      \"location\": \"المدينة، المنطقة، المغرب\",
      \"phone\": \"{$moroccoConfig['phone_prefix']}612345678\",
      \"email\": \"info@company.ma\",
      \"website\": \"https://www.company.ma\",
      \"price_range\": \"نطاق السعر الحقيقي بـ{$moroccoConfig['currency']}\",
      \"service_type\": \"نوع الخدمة\",
      \"description\": \"وصف تفصيلي شامل للخدمة\",
      \"duration\": \"مدة التنفيذ الفعلية\",
      \"match_percentage\": \"نسبة رقمية دقيقة من 1-100\",
      \"availability\": \"أوقات العمل الفعلية\",
      \"contact_method\": \"أفضل طريقة تواصل\",
      \"experience_level\": \"مستوى الخبرة الحقيقي\",
      \"certifications\": \"الشهادات والتراخيص الفعلية\"
    }
  ],
  \"companies\": [
    {
      \"name\": \"اسم الشركة المغربية الحقيقية\",
      \"service_category\": \"فئة الخدمات الرئيسية\",
      \"location\": \"العنوان الكامل في المغرب\",
      \"phone\": \"{$moroccoConfig['phone_prefix']}612345678\",
      \"email\": \"contact@company.ma\",
      \"website\": \"https://www.company.ma\",
      \"size\": \"حجم الشركة (عدد الموظفين)\",
      \"founded\": \"سنة التأسيس الحقيقية\",
      \"owner\": \"اسم المالك أو المدير العام\",
      \"nationality\": \"مغربية\",
      \"services_offered\": [\"قائمة مفصلة بجميع الخدمات\"],
      \"why_good_fit\": \"تحليل مفصل لمناسبة الشركة للعميل\",
      \"contact_info\": \"معلومات التواصل الكاملة\",
      \"operating_hours\": \"{$moroccoConfig['business_hours']}\",
      \"service_areas\": [\"المناطق الجغرافية المخدومة\"],
      \"rating\": \"تقييم واقعي من 5\",
      \"specialties\": [\"التخصصات الرئيسية\"],
      \"certifications\": [\"الشهادات والتراخيص الرسمية\"]
    }
  ],
  \"market_analysis\": {
    \"total_companies_found\": \"عدد الشركات الموجودة\",
    \"average_price_range\": \"متوسط الأسعار في السوق\",
    \"competition_level\": \"مستوى المنافسة\",
    \"market_trends\": \"الاتجاهات الحالية في السوق\"
  }
}

تذكر: يجب أن تكون جميع المعلومات حقيقية وواقعية للشركات المغربية!";
    }

    /**
     * تنسيق الميزانية
     */
    protected function formatBudget(array $searchData): string
    {
        if (!empty($searchData['budget_min']) && !empty($searchData['budget_max'])) {
            return "من {$searchData['budget_min']} إلى {$searchData['budget_max']} درهم";
        } elseif (!empty($searchData['budget_min'])) {
            return "ابتداء من {$searchData['budget_min']} درهم";
        } elseif (!empty($searchData['budget_max'])) {
            return "حتى {$searchData['budget_max']} درهم";
        }
        return "مفتوحة للتفاوض";
    }

    /**
     * تحليل استجابة Gemini
     */
    protected function parseResponse(string $responseText): array
    {
        try {
            // تنظيف النص
            $cleanedText = $this->cleanJsonResponse($responseText);
            
            // تحويل إلى JSON
            $decodedJson = json_decode($cleanedText, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON decode error: ' . json_last_error_msg());
                return [
                    'success' => false,
                    'error' => 'خطأ في تحليل البيانات: ' . json_last_error_msg()
                ];
            }

            // التحقق من وجود البيانات المطلوبة
            if (!isset($decodedJson['services']) || !isset($decodedJson['companies'])) {
                return [
                    'success' => false,
                    'error' => 'البيانات المستلمة غير مكتملة'
                ];
            }

            return [
                'success' => true,
                'services' => $this->validateAndCleanServices($decodedJson['services']),
                'companies' => $this->validateAndCleanCompanies($decodedJson['companies']),
                'thinking_process' => $decodedJson['thinking_process'] ?? null,
                'market_analysis' => $decodedJson['market_analysis'] ?? null
            ];

        } catch (\Exception $e) {
            Log::error('Exception in parseResponse: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'خطأ في معالجة الاستجابة: ' . $e->getMessage()
            ];
        }
    }

    /**
     * تنظيف استجابة JSON
     */
    protected function cleanJsonResponse(string $text): string
    {
        // إزالة أي نص قبل أول {
        $text = preg_replace('/^[^{]*/', '', $text);
        
        // إزالة أي نص بعد آخر }
        $text = preg_replace('/[^}]*$/', '', $text);
        
        // إزالة علامات الكود
        $text = preg_replace('/```json\s*/i', '', $text);
        $text = preg_replace('/```\s*$/i', '', $text);
        
        // إصلاح أخطاء JSON الشائعة
        $text = preg_replace('/}\s*{/', '},{', $text);
        $text = preg_replace('/,\s*}/', '}', $text);
        $text = preg_replace('/,\s*]/', ']', $text);
        $text = str_replace(['"', '"'], '"', $text);
        
        return trim($text);
    }

    /**
     * التحقق من صحة وتنظيف بيانات الخدمات
     */
    protected function validateAndCleanServices(array $services): array
    {
        $cleanedServices = [];
        $moroccoConfig = $this->config['morocco_config'];
        
        foreach ($services as $service) {
            if (!is_array($service) || 
                empty($service['service_name']) || 
                empty($service['company'])) {
                continue;
            }
            
            $cleanedService = [
                'service_name' => trim($service['service_name']),
                'company' => trim($service['company']),
                'location' => $service['location'] ?? 'غير محدد',
                'phone' => $this->validateMoroccanPhone($service['phone'] ?? ''),
                'email' => $this->validateMoroccanEmail($service['email'] ?? ''),
                'website' => $this->validateWebsite($service['website'] ?? ''),
                'price_range' => $service['price_range'] ?? 'حسب الطلب',
                'service_type' => $service['service_type'] ?? 'خدمة عامة',
                'description' => $service['description'] ?? 'وصف سيتم إضافته لاحقاً',
                'duration' => $service['duration'] ?? 'حسب الخدمة',
                'match_percentage' => $this->validateMatchPercentage($service['match_percentage'] ?? '70'),
                'availability' => $service['availability'] ?? $moroccoConfig['business_hours'],
                'contact_method' => $service['contact_method'] ?? 'هاتف',
                'experience_level' => $service['experience_level'] ?? 'متوسط',
                'certifications' => $service['certifications'] ?? 'غير مطلوب'
            ];
            
            $cleanedServices[] = $cleanedService;
        }
        
        // ترتيب حسب نسبة المطابقة
        usort($cleanedServices, function($a, $b) {
            return (int)$b['match_percentage'] - (int)$a['match_percentage'];
        });
        
        // الحد الأقصى للخدمات
        return array_slice($cleanedServices, 0, $this->config['service_search']['max_services']);
    }

    /**
     * التحقق من صحة وتنظيف بيانات الشركات
     */
    protected function validateAndCleanCompanies(array $companies): array
    {
        $cleanedCompanies = [];
        $moroccoConfig = $this->config['morocco_config'];
        
        foreach ($companies as $company) {
            if (!is_array($company) || 
                empty($company['name']) || 
                empty($company['service_category'])) {
                continue;
            }
            
            $cleanedCompany = [
                'name' => trim($company['name']),
                'service_category' => trim($company['service_category']),
                'location' => $company['location'] ?? 'الدار البيضاء',
                'phone' => $this->validateMoroccanPhone($company['phone'] ?? ''),
                'email' => $this->validateMoroccanEmail($company['email'] ?? ''),
                'website' => $this->validateWebsite($company['website'] ?? ''),
                'size' => $company['size'] ?? 'متوسط',
                'founded' => $company['founded'] ?? 'غير محدد',
                'owner' => $company['owner'] ?? 'غير محدد',
                'nationality' => 'مغربية',
                'services_offered' => (array)($company['services_offered'] ?? ['خدمات متنوعة']),
                'why_good_fit' => $company['why_good_fit'] ?? 'مناسبة لاحتياجاتك',
                'contact_info' => $company['contact_info'] ?? 'يمكن التواصل عبر الهاتف',
                'operating_hours' => $company['operating_hours'] ?? $moroccoConfig['business_hours'],
                'service_areas' => (array)($company['service_areas'] ?? ['المدينة المحلية']),
                'rating' => $company['rating'] ?? '4.2/5',
                'specialties' => (array)($company['specialties'] ?? ['خدمات متنوعة']),
                'certifications' => (array)($company['certifications'] ?? ['شهادات أساسية'])
            ];
            
            $cleanedCompanies[] = $cleanedCompany;
        }
        
        // الحد الأقصى للشركات
        return array_slice($cleanedCompanies, 0, $this->config['service_search']['max_companies']);
    }

    /**
     * التحقق من صحة رقم الهاتف المغربي
     */
    protected function validateMoroccanPhone(string $phone): string
    {
        if (empty($phone)) {
            return '+212612345678';
        }
        
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        $prefix = $this->config['morocco_config']['phone_prefix'];
        
        if (strpos($phone, '0') === 0) {
            $phone = $prefix . substr($phone, 1);
        } elseif (strpos($phone, $prefix) !== 0) {
            if (strlen($phone) === 9) {
                $phone = $prefix . $phone;
            }
        }
        
        if (strlen($phone) < 13 || strlen($phone) > 14) {
            return '+212612345678';
        }
        
        return $phone;
    }

    /**
     * التحقق من صحة البريد الإلكتروني المغربي
     */
    protected function validateMoroccanEmail(string $email): string
    {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'info@company.ma';
        }
        
        $domains = $this->config['morocco_config']['email_domains'];
        $validDomain = false;
        
        foreach ($domains as $domain) {
            if (strpos($email, $domain) !== false) {
                $validDomain = true;
                break;
            }
        }
        
        return $validDomain ? strtolower(trim($email)) : 'info@company.ma';
    }

    /**
     * التحقق من صحة الموقع الإلكتروني
     */
    protected function validateWebsite(string $website): string
    {
        if (empty($website) || $website === '#') {
            return 'https://www.company.ma';
        }
        
        if (!preg_match('/^https?:\/\//', $website)) {
            $website = 'https://' . $website;
        }
        
        return filter_var($website, FILTER_VALIDATE_URL) ? $website : 'https://www.company.ma';
    }

    /**
     * التحقق من صحة نسبة المطابقة
     */
    protected function validateMatchPercentage(string $percentage): string
    {
        $numericValue = preg_replace('/[^0-9.]/', '', $percentage);
        
        if (empty($numericValue)) {
            return '70';
        }
        
        $value = (float)$numericValue;
        
        if ($value < 1) {
            return '1';
        } elseif ($value > 100) {
            return '100';
        }
        
        return (string)round($value);
    }

    /**
     * الحصول على إحصائيات الخدمة
     */
    public function getServiceStats(): array
    {
        return [
            'api_key_configured' => !empty($this->apiKey),
            'models_available' => $this->config['models']['all'],
            'current_config' => [
                'max_services' => $this->config['service_search']['max_services'],
                'max_companies' => $this->config['service_search']['max_companies'],
                'step_by_step_enabled' => $this->config['service_search']['enable_step_by_step'],
                'cache_enabled' => $this->config['cache']['enabled']
            ]
        ];
    }
}