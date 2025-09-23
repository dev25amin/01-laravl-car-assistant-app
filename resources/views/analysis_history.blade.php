<x-app-layout>


    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bootstrap RTL CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts Arabic -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 20px 0;
    

            min-height: 100vh;
            padding-left: 30px;
            padding-right: 30px;
        }
   
        .main-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            backdrop-filter: blur(10px);
        }
        
        .header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="%23ffffff10"><polygon points="0,0 1000,0 1000,80 0,100"/></svg>');
            background-size: cover;
        }
        
        .header h1 {
            position: relative;
            z-index: 1;
            margin: 0;
            font-weight: 700;
            font-size: 2.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .section-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin: 20px 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-top: 4px solid #667eea;
        }
        
        .section-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .section-title {
            color: #2c3e50;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ecf0f1;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .section-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }
        
        .diagnosis-icon {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
        }
        
        .maintenance-icon {
            background: linear-gradient(135deg, #4ecdc4, #26d0ce);
        }
        
        /* تنسيق النتائج المحسن */
        .analysis-result {
            background: linear-gradient(145deg, #f8f9fa, #ffffff);
            border-radius: 15px;
            padding: 25px;
            margin: 20px 0;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            border-right: 5px solid #28a745;
        }
        
        .analysis-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .analysis-title {
            color: #2c3e50;
            font-size: 1.6rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .analysis-date {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        /* تنسيق المحتوى بعناوين وألوان */
        .content-section {
            margin: 20px 0;
            padding: 20px;
            background: #ffffff;
            border-radius: 12px;
            border-right: 4px solid;
        }
        
        .content-section.diagnosis { border-right-color: #dc3545; }
        .content-section.recommendation { border-right-color: #ffc107; }
        .content-section.maintenance { border-right-color: #28a745; }
        .content-section.cost { border-right-color: #17a2b8; }
        .content-section.priority { border-right-color: #6f42c1; }
        
        .content-section h4 {
            color: #2c3e50;
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .content-section .section-number {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            font-weight: 700;
        }
        
        /* تنسيق النسب والأرقام */
        .metric-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin: 15px 0;
        }
        
        .metric-item {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 10px;
            padding: 15px;
            min-width: 120px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        
        .metric-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2c3e50;
            display: block;
        }
        
        .metric-label {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 5px;
        }
        
        .percentage {
            color: #28a745;
        }
        
        .percentage.warning {
            color: #ffc107;
        }
        
        .percentage.danger {
            color: #dc3545;
        }
        
        /* تنسيق التواريخ */
        .date-highlight {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 5px 12px;
            border-radius: 15px;
            font-weight: 600;
            display: inline-block;
            margin: 0 5px;
        }
        
        .date-warning {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
        }
        
        .date-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .loading-spinner {
            display: none;
            text-align: center;
            margin: 20px 0;
        }
        
        .alert-custom {
            border-radius: 10px;
            padding: 15px 20px;
            margin: 15px 0;
            border: none;
        }
        
        .alert-success-custom {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border-right: 4px solid #28a745;
        }
        
        .alert-warning-custom {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            color: #856404;
            border-right: 4px solid #ffc107;
        }
        
        .alert-danger-custom {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
            border-right: 4px solid #dc3545;
        }
        
        .image-upload-container {
            border: 2px dashed #667eea;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin: 15px 0;
            background: #f8f9ff;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .image-upload-container:hover {
            border-color: #5a67d8;
            background: #eef2ff;
        }
        
        .image-upload-container.dragover {
            border-color: #4c51bf;
            background: #e6fffa;
        }
        
        .image-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }
        
        .image-preview-item {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .image-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .image-preview-item .remove-image {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255,0,0,0.8);
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .car-info-display {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
        }
        
        .info-value {
            color: #6c757d;
        }
        
        @media (max-width: 768px) {
            .section-title {
                font-size: 1.4rem;
            }
            
            .header h1 {
                font-size: 1.8rem;
            }
            
            .section-card {
                padding: 20px;
                margin: 15px 0;
            }
            
            .image-preview-item {
                width: 80px;
                height: 80px;
            }
        }
        
        .typing-animation {
            display: inline-block;
        }
        
        .typing-animation::after {
            content: '...';
            animation: typing 1.5s infinite;
        }
        
        @keyframes typing {
            0%, 60% { content: ''; }
            25% { content: '.'; }
            50% { content: '..'; }
            75% { content: '...'; }
        }
    </style>


    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8" style="margin: 60px 0px;">
        <div class="bg-blue-100 rounded-lg p-6 text-center">
            <div class="text-3xl font-bold text-blue-600">{{ $analyses->total() }}</div>
            <div class="text-blue-700 font-semibold">إجمالي التحليلات</div>
        </div>
        <div class="bg-green-100 rounded-lg p-6 text-center">
            <div class="text-3xl font-bold text-green-600">
                {{ $analyses->where('analysis_type', 'condition_analysis')->count() }}
            </div>
            <div class="text-green-700 font-semibold">تحليل الحالة</div>
        </div>
        <div class="bg-orange-100 rounded-lg p-6 text-center">
            <div class="text-3xl font-bold text-orange-600">
                {{ $analyses->where('analysis_type', 'problem_diagnosis')->count() }}
            </div>
            <div class="text-orange-700 font-semibold">تشخيص الأعطال</div>
        </div>
    </div>

    <!-- قائمة التحليلات -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b">
            <h2 class="text-xl font-semibold text-gray-800">التحليلات السابقة</h2>
        </div>

        @if($analyses->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($analyses as $analysis)
                    <div class="p-6 hover:bg-gray-50 transition duration-200">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <!-- نوع التحليل -->
                                <div class="flex items-center mb-3">
                                    @if($analysis->analysis_type == 'condition_analysis')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            تحليل حالة السيارة
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            تشخيص عطل
                                        </span>
                                    @endif
                                    <span class="mr-3 text-sm text-gray-500">
                                        {{ $analysis->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>

                                <!-- معلومات السيارة -->
                                @if($analysis->carInfo)
                                    <div class="mb-3">
                                        <div class="text-lg font-semibold text-gray-800">
                                            {{ $analysis->carInfo->car_brand }} {{ $analysis->carInfo->car_model }}
                                            @if($analysis->carInfo->car_year)
                                                - {{ $analysis->carInfo->car_year }}
                                            @endif
                                        </div>
                                        @if($analysis->carInfo->current_mileage)
                                            <div class="text-sm text-gray-600">
                                                المسافة المقطوعة: {{ number_format($analysis->carInfo->current_mileage) }} كم
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <!-- معاينة سريعة للنتيجة -->
                                <div class="text-gray-700">
                                    <p class="line-clamp-3">
                                        {{ Str::limit($analysis->analysis_result, 200) }}
                                    </p>
                                </div>

                                <!-- معلومات إضافية -->
                                @if($analysis->analysis_type == 'problem_diagnosis')
                                    @php
                                        $inputData = json_decode($analysis->input_data, true);
                                    @endphp
                                    @if(isset($inputData['problem_description']))
                                        <div class="mt-3 p-3 bg-gray-100 rounded-lg">
                                            <div class="text-sm font-medium text-gray-700 mb-1">وصف المشكلة:</div>
                                            <div class="text-sm text-gray-600">
                                                {{ Str::limit($inputData['problem_description'], 150) }}
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                            <!-- الأزرار -->
                            <div class="flex gap-x-2 mr-4">
                                <a href="{{ route('analysis.show', $analysis->id) }}" 
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>

                                <button onclick="deleteAnalysis({{ $analysis->id }})" 
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

            <!-- شريط التنقل بين الصفحات -->
            <div class="bg-gray-50 px-6 py-3 border-t">
                {{ $analyses->links() }}
            </div>
        @else
            <!-- رسالة عدم وجود تحليلات -->
            <div class="p-12 text-center">
                <div class="mx-auto w-24 h-24 mb-4">
                    <svg class="w-full h-full text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد تحليلات</h3>
                <p class="text-gray-500 mb-6">لم تقم بإجراء أي تحليلات بعد. ابدأ بإدخال معلومات سيارتك أو تشخيص مشكلة.</p>
                <a href="{{ route('analysis.history') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    إجراء تحليل جديد
                </a>
            </div>
        @endif
    </div>

</div>

<!-- JavaScript لحذف التحليل -->
<script>
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
                location.reload(); // إعادة تحميل الصفحة
            } else {
                alert('حدث خطأ أثناء الحذف: ' + data.message);
            }
        })
        .catch(error => {
            console.error('خطأ:', error);
            alert('حدث خطأ أثناء الحذف');
        });
    }
}
</script>

</x-app-layout>
