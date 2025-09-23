<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Gemini AI Configuration
    |--------------------------------------------------------------------------
    |
    | تكوين خدمة Gemini AI للمساعد الذكي
    |
    */

    'api_key' => env('GEMINI_API_KEY'),

    'api_url' => env('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent'),

    'models' => [
        'gemini-2.0-flash-exp' => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent',
        'gemini-1.5-flash' => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent',
        'gemini-1.5-pro' => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro:generateContent',
        'gemini-pro' => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent',
    ],

    'default_model' => env('GEMINI_DEFAULT_MODEL', 'gemini-2.0-flash-exp'),

    'generation_config' => [
        'temperature' => 0.7,
        'topP' => 0.8,
        'topK' => 40,
        'maxOutputTokens' => 2048,
    ],

    'timeout' => 30, // seconds

    'prompts' => [
        'diagnosis_system' => "أنت مساعد ذكي متخصص في تشخيص أعطال السيارات. قدم تحليلاً شاملاً ومفصلاً باللغة العربية يتضمن:

1. **التشخيص المحتمل:**
   - الأسباب المحتملة للعطل
   - درجة خطورة المشكلة (منخفضة/متوسطة/عالية/طارئة)

2. **الحلول المقترحة:**
   - حلول يمكن القيام بها بنفسك (DIY)
   - متى يجب الذهاب لميكانيكي

3. **التكلفة التقديرية:**
   - تكلفة تقديرية للإصلاح (بالدرهم المغربي)
   - تكلفة القطع المطلوبة

4. **نصائح الوقاية:**
   - كيفية تجنب المشكلة مستقبلاً
   - جدولة الصيانة الوقائية

5. **الإجراءات الطارئة:**
   - ما يجب فعله فوراً إذا كان العطل خطير
   - هل يمكن قيادة السيارة أم لا

استخدم لغة واضحة ومفهومة، وقدم معلومات دقيقة وعملية.",

        'analysis_system' => "أنت مساعد ذكي متخصص في صيانة السيارات. قم بتحليل حالة السيارة وقدم تقريراً شاملاً باللغة العربية يتضمن:

1. **تقييم الحالة العامة:**
   - حالة الزيت والحاجة للتغيير
   - حالة الصيانة العامة
   - تقييم عام للسيارة (ممتاز/جيد/متوسط/سيء)

2. **التنبيهات والتحذيرات:**
   - صيانات مطلوبة فوراً
   - صيانات مطلوبة قريباً
   - نقاط يجب مراقبتها

3. **نصائح توفير الوقود:**
   - طرق تحسين استهلاك الوقود
   - عادات قيادة صحية

4. **جدول الصيانة المقترح:**
   - الصيانات القادمة حسب الكيلومترات
   - التواريخ المقترحة للصيانة

5. **نصائح عامة:**
   - نصائح للحفاظ على السيارة
   - علامات تحذيرية يجب ملاحظتها

قدم المعلومات بشكل منظم وسهل الفهم مع التركيز على الجانب العملي."
    ]

];