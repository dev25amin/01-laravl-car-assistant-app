<x-app-layout>

    <!-- العنوان الرئيسي -->
    <div class="mb-8"style="margin: 60px 0px;">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="main-title font-bold mb-2">تفاصيل التحليل</h1>
                <p class="text-gray-600 text-lg">معلومات شاملة عن التحليل المحدد</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('analysis.history') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    العودة للسجل
                </a>
                <button onclick="deleteAnalysis({{ $analysis->id }})" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    حذف
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- العمود الجانبي - المعلومات الأساسية -->
        <div class="lg:col-span-1 space-y-6">
            <!-- نوع التحليل والتاريخ -->
            <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
                <h3 class="section-title font-semibold mb-4">معلومات التحليل</h3>
                
                <div class="space-y-4">
                    <!-- نوع التحليل -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">نوع التحليل</label>
                        @if($analysis->analysis_type == 'condition_analysis')
                            <span class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                تحليل حالة السيارة
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                تشخيص عطل
                            </span>
                        @endif
                    </div>

                    <!-- تاريخ التحليل -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ التحليل</label>
                        <div class="value-large">{{ $analysis->created_at->format('d/m/Y') }}</div>
                        <div class="text-sm text-gray-500">{{ $analysis->created_at->format('H:i') }}</div>
                    </div>

                    <!-- منذ متى -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">منذ</label>
                        <div class="value-medium">{{ $analysis->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            </div>

            <!-- معلومات السيارة -->
            @if($analysis->carInfo)
                <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
                    <h3 class="section-title font-semibold mb-4">معلومات السيارة</h3>
                    
                    <div class="space-y-3">
                        @if($analysis->carInfo->car_brand)
                            <div class="flex justify-between">
                                <span class="text-gray-600">العلامة التجارية:</span>
                                <span class="value-accent">{{ $analysis->carInfo->car_brand }}</span>
                            </div>
                        @endif

                        @if($analysis->carInfo->car_model)
                            <div class="flex justify-between">
                                <span class="text-gray-600">الموديل:</span>
                                <span class="value-accent">{{ $analysis->carInfo->car_model }}</span>
                            </div>
                        @endif

                        @if($analysis->carInfo->car_year)
                            <div class="flex justify-between">
                                <span class="text-gray-600">سنة الصنع:</span>
                                <span class="value-medium">{{ $analysis->carInfo->car_year }}</span>
                            </div>
                        @endif

                        @if($analysis->carInfo->current_mileage)
                            <div class="flex justify-between">
                                <span class="text-gray-600">المسافة المقطوعة:</span>
                                <span class="value-large">{{ number_format($analysis->carInfo->current_mileage) }} كم</span>
                            </div>
                        @endif

                        @if($analysis->carInfo->last_oil_change)
                            <div class="flex justify-between">
                                <span class="text-gray-600">آخر تغيير زيت:</span>
                                <span class="value-medium">{{ $analysis->carInfo->last_oil_change->format('d/m/Y') }}</span>
                            </div>
                        @endif

                        @if($analysis->carInfo->last_maintenance)
                            <div class="flex justify-between">
                                <span class="text-gray-600">آخر صيانة:</span>
                                <span class="value-medium">{{ $analysis->carInfo->last_maintenance->format('d/m/Y') }}</span>
                            </div>
                        @endif

                        @if($analysis->carInfo->fuel_level)
                            <div class="flex justify-between">
                                <span class="text-gray-600">مستوى الوقود:</span>
                                <span class="value-large">{{ $analysis->carInfo->fuel_level }}%</span>
                            </div>
                        @endif
                    </div>

                    @if($analysis->carInfo->notes)
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات:</label>
                            <div class="text-gray-900 text-sm bg-gray-50 p-3 rounded-md">
                                {{ $analysis->carInfo->notes }}
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- المحتوى الرئيسي -->
        <div class="lg:col-span-2 space-y-6">
            <!-- وصف المشكلة (إذا كان تشخيص عطل) -->
            @if($analysis->analysis_type == 'problem_diagnosis')
                @php
                    $inputData = json_decode($analysis->input_data, true);
                @endphp
                @if(isset($inputData['problem_description']))
                    <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
                        <h3 class="section-title font-semibold mb-4">وصف المشكلة</h3>
                        <div class="bg-orange-50 border-r-4 border-orange-400 p-4 rounded">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-orange-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <h4 class="text-orange-800 font-medium mb-2">المشكلة المبلغ عنها:</h4>
                                    <p class="text-orange-700 leading-relaxed">{{ $inputData['problem_description'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <!-- نتيجة التحليل -->
            <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="section-title font-semibold">
                        @if($analysis->analysis_type == 'condition_analysis')
                            تحليل حالة السيارة
                        @else
                            نتيجة التشخيص
                        @endif
                    </h3>
                    <button onclick="copyAnalysis()" 
                            class="inline-flex items-center px-3 py-1 border border-gray-300 rounded text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        نسخ
                    </button>
                </div>

                <div id="analysisContent" class="prose max-w-none">
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 leading-relaxed text-gray-800">
                        {!! nl2br(e($analysis->analysis_result)) !!}
                    </div>
                </div>
            </div>

            <!-- البيانات المدخلة (للمطورين) -->
            @if(config('app.debug'))
                <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
                    <h3 class="section-title font-semibold mb-4">البيانات المدخلة (وضع التطوير)</h3>
                    <div class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto">
                        <pre class="text-sm">{{ json_encode(json_decode($analysis->input_data, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            @endif

            <!-- الإجراءات -->
            <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
                <h3 class="section-title font-semibold mb-4">إجراءات إضافية</h3>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('car.assistant') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        إجراء تحليل جديد
                    </a>
                    
                    @if($analysis->analysis_type == 'condition_analysis')
                        <a href="{{ route('car.assistant') }}#diagnosis" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                            تشخيص مشكلة
                        </a>
                    @else
                        <a href="{{ route('car.assistant') }}#condition" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            تحليل حالة السيارة
                        </a>
                    @endif

                    <button onclick="printAnalysis()" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        طباعة
                    </button>

                    <button onclick="shareAnalysis()" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                        </svg>
                        مشاركة
                    </button>
                </div>
            </div>
        </div>
    </div>

<!-- JavaScript للوظائف الإضافية -->
<script>
// نسخ التحليل
function copyAnalysis() {
    const analysisText = `@if($analysis->analysis_type == 'condition_analysis')تحليل حالة السيارة@else نتيجة تشخيص العطل @endif
التاريخ: {{ $analysis->created_at->format('d/m/Y H:i') }}

@if($analysis->carInfo)
معلومات السيارة:
@if($analysis->carInfo->car_brand)- العلامة التجارية: {{ $analysis->carInfo->car_brand }}@endif
@if($analysis->carInfo->car_model)- الموديل: {{ $analysis->carInfo->car_model }}@endif
@if($analysis->carInfo->car_year)- سنة الصنع: {{ $analysis->carInfo->car_year }}@endif
@if($analysis->carInfo->current_mileage)- المسافة المقطوعة: {{ number_format($analysis->carInfo->current_mileage) }} كم@endif

@endif
@if($analysis->analysis_type == 'problem_diagnosis')
@php $inputData = json_decode($analysis->input_data, true); @endphp
@if(isset($inputData['problem_description']))
وصف المشكلة:
{{ $inputData['problem_description'] }}

@endif
@endif
النتيجة:
{{ $analysis->analysis_result }}`;

    navigator.clipboard.writeText(analysisText).then(function() {
        // إظهار رسالة نجح النسخ
        showNotification('تم نسخ التحليل بنجاح!', 'success');
    }, function() {
        // فشل النسخ
        showNotification('فشل في نسخ التحليل', 'error');
    });
}

// طباعة التحليل
function printAnalysis() {
    window.print();
}

// مشاركة التحليل
function shareAnalysis() {
    if (navigator.share) {
        navigator.share({
            title: '@if($analysis->analysis_type == "condition_analysis")تحليل حالة السيارة@else تشخيص عطل السيارة @endif',
            text: 'تحليل من مساعد السيارة الذكي',
            url: window.location.href
        }).catch(console.error);
    } else {
        // نسخ الرابط
        navigator.clipboard.writeText(window.location.href).then(function() {
            showNotification('تم نسخ رابط التحليل!', 'success');
        });
    }
}

// حذف التحليل
function deleteAnalysis(id) {
    if (confirm('هل أنت متأكد من حذف هذا التحليل؟ لن يمكن استرداده بعد الحذف.')) {
        fetch(`/analysis/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('تم حذف التحليل بنجاح!', 'success');
                setTimeout(() => {
                    window.location.href = '{{ route("analysis.history") }}';
                }, 1500);
            } else {
                showNotification('حدث خطأ أثناء الحذف: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('خطأ:', error);
            showNotification('حدث خطأ أثناء الحذف', 'error');
        });
    }
}

// إظهار الإشعارات
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transform transition-all duration-300 translate-x-full`;
    
    if (type === 'success') {
        notification.classList.add('bg-green-500', 'text-white');
    } else if (type === 'error') {
        notification.classList.add('bg-red-500', 'text-white');
    } else {
        notification.classList.add('bg-blue-500', 'text-white');
    }
    
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                ${type === 'success' ? 
                    '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>' :
                    '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>'
                }
            </svg>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // إظهار الإشعار
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // إخفاء الإشعار بعد 3 ثوان
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}
</script>

<style>

   
        .main-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            backdrop-filter: blur(10px);
        }
        
    .main-title {
        font-size: 2.5rem;
        background: linear-gradient(135deg, #2c3e50 0%, #4a6580 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .section-title {
        font-size: 1.5rem;
        color: #2c3e50;
        border-right: 4px solid #667eea;
        padding-right: 12px;
    }
    
    .value-large {
        font-size: 1.8rem;
        font-weight: 700;
        color: #2c3e50;
    }
    
    .value-medium {
        font-size: 1.4rem;
        font-weight: 600;
        color: #4a6580;
    }
    
    .value-accent {
        font-size: 1.3rem;
        font-weight: 600;
        color: #667eea;
    }
    
    .card-hover {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px -10px rgba(0, 0, 0, 0.15);
        border-left: 4px solid #667eea;
    }
</style>
</x-app-layout>
