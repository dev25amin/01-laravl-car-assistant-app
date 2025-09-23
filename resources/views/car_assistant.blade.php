{{-- car_assistant.blade.php --}}
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

    <div class="container-fluid" style="margin: 60px 0px;">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">
                <div class="main-container">
                    <!-- Header -->
                    <div class="header">
                        <h1><i class="fas fa-car me-3"></i>مساعد السيارات الذكي</h1>
                        <p class="mb-0 fs-5">AI CarCare Assistant - تشخيص ذكي ومتابعة شاملة لسيارتك</p>
                        @auth
                            <div class="user-welcome-section mt-3">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fas fa-user-circle me-2 fs-4"></i>
                                    <span class="fw-semibold fs-6">{{ Auth::user()->name }}</span>
                                </div>
                            </div>
                        @endauth
                    </div>
                    @if($carInfo)

      <br>


            <div class="container-fluid">

                <div class="row g-3">

                    <!-- الكيلومترات -->
                    <div class="col-md-3">
                        <div class="card shadow-sm text-center p-3">
                            <i class="fas fa-tachometer-alt fa-2x text-danger mb-2"></i>
                            <h6 class="mb-1">الكيلومترات</h6>
                            <p class="fw-bold mb-0">{{ $carInfo->current_mileage ?? 'غير محددة' }}</p>
                        </div>
                    </div>

                    <!-- آخر تغيير زيت -->
                    @if($carInfo->last_oil_change)
                    <div class="col-md-3">
                        <div class="card shadow-sm text-center p-3">
                            <i class="fas fa-oil-can fa-2x text-dark mb-2"></i>
                            <h6 class="mb-1">آخر تغيير زيت</h6>
                            <p class="fw-bold mb-0">{{ $carInfo->last_oil_change->format('Y-m-d') }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- آخر صيانة -->
                    @if($carInfo->last_maintenance)
                    <div class="col-md-3">
                        <div class="card shadow-sm text-center p-3">
                            <i class="fas fa-tools fa-2x text-info mb-2"></i>
                            <h6 class="mb-1">آخر صيانة</h6>
                            <p class="fw-bold mb-0">{{ $carInfo->last_maintenance->format('Y-m-d') }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- مستوى الوقود -->
                    @if($carInfo->fuel_level)
                    <div class="col-md-3">
                        <div class="card shadow-sm text-center p-3">
                            <i class="fas fa-gas-pump fa-2x text-secondary mb-2"></i>
                            <h6 class="mb-1">مستوى الوقود</h6>
                            <p class="fw-bold mb-0">{{ $carInfo->fuel_level }} لتر</p>
                        </div>
                    </div>
                    @endif
                </div>
                </div>
            @endif
                    <br>
            <div class="container-fluid">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-blue-100 rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ $analysisCount }}</div>
                    <div class="text-blue-700 font-semibold">إجمالي التحليلات</div>
                </div>
                <div class="bg-green-100 rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-green-600">
                        {{ $conditionAnalysisCount }}
                    </div>
                    <div class="text-green-700 font-semibold">تحليل الحالة</div>
                </div>
                <div class="bg-orange-100 rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-orange-600">
                        {{ $problemDiagnosisCount }}
                    </div>
                    <div class="text-orange-700 font-semibold">تشخيص الأعطال</div>
                </div>
            </div>
    </div>


                    <div class="container-fluid p-4">
                        <!-- رسائل التنبيه -->
                        <div id="alertsContainer"></div>
                        
                        <div class="row">
                            <!-- القسم الأيمن: تشخيص الأعطال -->
                            <div class="col-lg-6">
                                <div class="section-card">
                                    <h2 class="section-title">
                                        <div class="section-icon diagnosis-icon">
                                            <i class="fas fa-stethoscope"></i>
                                        </div>
                                        تشخيص الأعطال الميكانيكية
                                    </h2>
                                    
                                    <form id="diagnosisForm" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="problemDescription" class="form-label fw-bold">
                                                <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                                                وصف المشكلة أو العطل
                                            </label>
                                            <textarea 
                                                class="form-control" 
                                                id="problemDescription" 
                                                rows="5" 
                                                placeholder="اكتب هنا وصفاً مفصلاً للمشكلة... مثال: السيارة تصدر صوت غريب عند الفرملة، أو دخان أبيض من العادم، أو اهتزاز في المقود..."
                                                required></textarea>
                                            <div class="form-text">
                                                <i class="fas fa-info-circle me-1"></i>
                                                كن مفصلاً قدر الإمكان لتشخيص أدق
                                            </div>
                                        </div>
                                        
                                        <!-- قسم رفع الصور -->
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">
                                                <i class="fas fa-camera me-2 text-primary"></i>
                                                إضافة صور للمشكلة (اختياري)
                                            </label>
                                            
                                            <div class="image-upload-container" id="imageUploadContainer">
                                                <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-2"></i>
                                                <div class="image-upload-text">اضغط لاختيار الصور أو اسحبها هنا</div>
                                                <div class="image-upload-hint">يمكنك رفع صور بصيغة JPG, PNG, GIF (حد أقصى 5 ميجابايت لكل صورة)</div>
                                                <input 
                                                    type="file" 
                                                    id="problemImages" 
                                                    name="problem_images[]" 
                                                    multiple 
                                                    accept="image/*"
                                                    style="display: none;">
                                            </div>
                                            
                                            <div id="imagePreview" class="image-preview"></div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-search me-2"></i>
                                            تحليل العطل
                                        </button>
                                    </form>
                                    
                                    <!-- Loading Spinner للتشخيص -->
                                    <div id="diagnosisLoading"></div>
                                    
                                    <!-- نتائج التشخيص -->
                                    <div id="diagnosisResult" style="display: none;"></div>
                                </div>
                            </div>
                            
                            <!-- القسم الأيسر: متابعة حالة السيارة -->
                            <div class="col-lg-6">
                                <div class="section-card">
                                    <h2 class="section-title">
                                        <div class="section-icon maintenance-icon">
                                            <i class="fas fa-tools"></i>
                                        </div>
                                        متابعة حالة السيارة
                                    </h2>
<form id="carInfoForm">
   
    <div class="row">

                <div class="col-md-4 mb-3">
            <label for="carBrand" class="form-label fw-bold">
                <i class="fas fa-car me-2 text-primary"></i>
                ماركة السيارة
            </label>
            <input 
                type="text" 
                class="form-control" 
                id="carBrand" 
                name="car_brand" 
                placeholder="تويوتا، هونداي، رينو..."
                value="{{ $carInfo->car_brand ?? '' }}">
        </div>

        
        <div class="col-md-4 mb-3">
            <label for="carModel" class="form-label fw-bold">
                <i class="fas fa-tag me-2 text-primary"></i>
                الموديل
            </label>
            <input 
                type="text" 
                class="form-control" 
                id="carModel" 
                name="car_model" 
                placeholder="كامري، إلنترا، كليو..."
                value="{{ $carInfo->car_model ?? '' }}">
        </div>



        <div class="col-md-4 mb-3">
            <label for="carYear" class="form-label fw-bold">
                <i class="fas fa-calendar-alt me-2 text-primary"></i>
                سنة الصنع
            </label>
            <input 
                type="number" 
                class="form-control" 
                id="carYear" 
                name="car_year" 
                min="1990" 
                max="{{ date('Y') + 1 }}" 
                placeholder="{{ date('Y') }}"
                value="{{ $carInfo->car_year ?? '' }}">
        </div>
    </div>

        <div class="row">
        <div class="col-md-6 mb-3">
            <label for="fuelLevel" class="form-label fw-bold">
                <i class="fas fa-gas-pump me-2 text-success"></i>
                مستوى الوقود (لتر)
            </label>
            <input 
                type="number" 
                class="form-control" 
                id="fuelLevel" 
                name="fuel_level" 
                min="0" 
                max="100" 
                step="0.1" 
                placeholder="45.5"
                value="{{ $carInfo->fuel_level ?? '' }}">
        </div>

        <div class="col-md-6 mb-3">
            <label for="currentMileage" class="form-label fw-bold">
                <i class="fas fa-tachometer-alt me-2 text-primary"></i>
                الكيلومترات الحالية
            </label>
            <input 
                type="number" 
                class="form-control" 
                id="currentMileage" 
                name="current_mileage" 
                min="0" 
                placeholder="150000"
                value="{{ $carInfo->current_mileage ?? '' }}">
        </div>
    </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="lastOilChange" class="form-label fw-bold">
                                                    <i class="fas fa-oil-can me-2 text-warning"></i>
                                                    آخر تغيير زيت
                                                </label>
                                                <input 
                                                    type="date" 
                                                    class="form-control" 
                                                    id="lastOilChange" 
                                                    name="last_oil_change"
                                                    value="{{ $carInfo && $carInfo->last_oil_change ? $carInfo->last_oil_change->format('Y-m-d') : '' }}">
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label for="lastMaintenance" class="form-label fw-bold">
                                                    <i class="fas fa-wrench me-2 text-info"></i>
                                                    آخر صيانة
                                                </label>
                                                <input 
                                                    type="date" 
                                                    class="form-control" 
                                                    id="lastMaintenance" 
                                                    name="last_maintenance"
                                                    value="{{ $carInfo && $carInfo->last_maintenance ? $carInfo->last_maintenance->format('Y-m-d') : '' }}">
                                            </div>
                                        </div>
                                        
          
                                        
                                        <div class="mb-3">
                                            <label for="notes" class="form-label fw-bold">
                                                <i class="fas fa-sticky-note me-2 text-secondary"></i>
                                                ملاحظات إضافية
                                            </label>
                                            <textarea 
                                                class="form-control" 
                                                id="notes" 
                                                name="notes" 
                                                rows="3" 
                                                placeholder="أي ملاحظات أو مشاكل تود تسجيلها...">{{ $carInfo->notes ?? '' }}</textarea>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-save me-2"></i>
                                            حفظ ومتابعة
                                        </button>
                                    </form>
                                    
                                    <!-- Loading Spinner للحفظ -->
                                    <div id="saveLoading"></div>
                                    
                                    <!-- نتائج تحليل الحالة -->
                                    <div id="analysisResult" style="display: none;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <br>


    <!-- قائمة التحليلات -->
        <div class="container-fluid">

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

            </div>
        @endif
    </div>
    </div>
<br>
</div>
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

<style>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // إعداد CSRF Token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // متغيرات الصور المرفوعة
        let selectedImages = [];
        
        // تهيئة رفع الصور
        document.addEventListener('DOMContentLoaded', function() {
            initImageUpload();
        });
        
        function initImageUpload() {
            const container = document.getElementById('imageUploadContainer');
            const fileInput = document.getElementById('problemImages');
            const preview = document.getElementById('imagePreview');
            
            // النقر على الحاوية لاختيار الملفات
            container.addEventListener('click', () => fileInput.click());
            
            // معالجة اختيار الملفات
            fileInput.addEventListener('change', handleFileSelect);
            
            // معالجة السحب والإفلات
            container.addEventListener('dragover', handleDragOver);
            container.addEventListener('drop', handleDrop);
            container.addEventListener('dragleave', handleDragLeave);
        }
        
        function handleFileSelect(e) {
            const files = Array.from(e.target.files);
            addImages(files);
        }
        
        function handleDragOver(e) {
            e.preventDefault();
            e.currentTarget.classList.add('dragover');
        }
        
        function handleDrop(e) {
            e.preventDefault();
            e.currentTarget.classList.remove('dragover');
            const files = Array.from(e.dataTransfer.files).filter(file => file.type.startsWith('image/'));
            addImages(files);
        }
        
        function handleDragLeave(e) {
            e.currentTarget.classList.remove('dragover');
        }
        
        function addImages(files) {
            const preview = document.getElementById('imagePreview');
            
            files.forEach(file => {
                // التحقق من حجم الملف (5 ميجابايت)
                if (file.size > 5 * 1024 * 1024) {
                    showAlert('حجم الصورة ' + file.name + ' كبير جداً. الحد الأقصى 5 ميجابايت.', 'warning');
                    return;
                }
                
                // التحقق من نوع الملف
                if (!file.type.startsWith('image/')) {
                    showAlert('الملف ' + file.name + ' ليس صورة صالحة.', 'warning');
                    return;
                }
                
                selectedImages.push(file);
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'image-preview-item';
                    previewItem.innerHTML = `
                        <img src="${e.target.result}" alt="صورة المشكلة">
                        <button type="button" class="remove-image" onclick="removeImage(${selectedImages.length - 1})">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    preview.appendChild(previewItem);
                };
                reader.readAsDataURL(file);
            });
            
            // تحديث النص في حاوية الرفع
            updateUploadContainerText();
        }
        
        function removeImage(index) {
            selectedImages.splice(index, 1);
            
            // إعادة بناء المعاينة
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            
            selectedImages.forEach((file, idx) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'image-preview-item';
                    previewItem.innerHTML = `
                        <img src="${e.target.result}" alt="صورة المشكلة">
                        <button type="button" class="remove-image" onclick="removeImage(${idx})">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    preview.appendChild(previewItem);
                };
                reader.readAsDataURL(file);
            });
            
            updateUploadContainerText();
        }
        
        function updateUploadContainerText() {
            const container = document.getElementById('imageUploadContainer');
            const textElement = container.querySelector('.image-upload-text');
            
            if (selectedImages.length > 0) {
                textElement.textContent = `تم اختيار ${selectedImages.length} صور - اضغط لإضافة المزيد`;
            } else {
                textElement.textContent = 'اضغط لاختيار الصور أو اسحبها هنا';
            }
        }
        
        // معالج تشخيص الأعطال
        document.getElementById('diagnosisForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('problem_description', document.getElementById('problemDescription').value);
            
            // إضافة الصور إلى FormData
            selectedImages.forEach(image => {
                formData.append('problem_images[]', image);
            });
            
            formData.append('_token', csrfToken);
            
            // إظهار التحميل وإخفاء النتائج
            document.getElementById('diagnosisLoading').style.display = 'block';
            document.getElementById('diagnosisResult').style.display = 'none';
            
            // تعطيل زر التحليل
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري التحليل...';
            
            try {
                const response = await fetch('/diagnose-problem', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                const result = await response.json();
                
                // إخفاء التحميل
                document.getElementById('diagnosisLoading').style.display = 'none';
                
                if (result.success) {
                    // عرض التحليل الكامل دائماً
                    displayAnalysisResult(result.diagnosis, 'diagnosis', 'diagnosisResult');
                    
                    if (result.uploaded_images > 0) {
                        showAlert(`تم تحليل ${result.uploaded_images} صور مع التشخيص`, 'success');
                    }
                } else {
                    showAlert('حدث خطأ: ' + result.message, 'danger');
                }
                
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('diagnosisLoading').style.display = 'none';
                showAlert('حدث خطأ في الاتصال. يرجى المحاولة مرة أخرى.', 'danger');
            } finally {
                // إعادة تفعيل زر التحليل
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-search me-2"></i>تحليل العطل';
            }
        });
        
        // معالج حفظ معلومات السيارة
        document.getElementById('carInfoForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('_token', csrfToken);
            
            // إظهار التحميل وإخفاء النتائج
            document.getElementById('saveLoading').style.display = 'block';
            document.getElementById('analysisResult').style.display = 'none';
            
            // تعطيل زر الحفظ
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري الحفظ والتحليل...';
            
            try {
                const response = await fetch('/save-car-info', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                
                const result = await response.json();
                
                // إخفاء التحميل
                document.getElementById('saveLoading').style.display = 'none';
                
                if (result.success) {
                    showAlert('تم حفظ المعلومات بنجاح!', 'success');
                    
                    // عرض التحليل إذا كان متوفراً - دائماً التحليل الكامل
                    if (result.analysis) {
                        displayAnalysisResult(result.analysis, 'analysis', 'analysisResult');
                    }
                    
                } else {
                    showAlert('حدث خطأ: ' + result.message, 'danger');
                }
                
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('saveLoading').style.display = 'none';
                showAlert('حدث خطأ في الاتصال. يرجى المحاولة مرة أخرى.', 'danger');
            } finally {
                // إعادة تفعيل زر الحفظ
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-save me-2"></i>حفظ ومتابعة';
            }
        });
        
        // دالة عرض النتائج الكاملة (دائماً)
        function displayAnalysisResult(content, type, containerId) {
            const container = document.getElementById(containerId);
            const formattedContent = formatAnalysisContent(content);
            
            container.innerHTML = `
                <div class="analysis-result">
                    <div class="analysis-header">
                        <div class="analysis-title">
                            <i class="fas fa-${type === 'diagnosis' ? 'stethoscope' : 'chart-line'} me-2"></i>
                            ${type === 'diagnosis' ? 'نتائج التشخيص' : 'تحليل حالة السيارة'}
                        </div>
                        <div class="analysis-date">
                            ${new Date().toLocaleDateString('en-SA')} - ${new Date().toLocaleTimeString('en-SA', {hour: '2-digit', minute: '2-digit'})}
                        </div>
                    </div>
                    ${formattedContent}
                </div>
            `;
            
            container.style.display = 'block';
        }
        
        // دالة تنسيق المحتوى بعناوين وألوان
        function formatAnalysisContent(content) {
            // تقسيم المحتوى إلى أقسام بناءً على الكلمات المفتاحية
            let formattedContent = content;
            let sectionCounter = 1;
            
            // البحث عن الأقسام المختلفة وتنسيقها
            const sections = [
                {
                    keywords: ['التشخيص', 'تشخيص', 'المشكلة', 'العطل'],
                    className: 'diagnosis',
                    icon: 'fa-stethoscope',
                    title: 'التشخيص'
                },
                {
                    keywords: ['التوصية', 'التوصيات', 'الحل', 'الحلول', 'ينصح'],
                    className: 'recommendation',
                    icon: 'fa-lightbulb',
                    title: 'التوصيات'
                },
                {
                    keywords: ['الصيانة', 'الإصلاح', 'التصليح'],
                    className: 'maintenance',
                    icon: 'fa-tools',
                    title: 'الصيانة المطلوبة'
                },
                {
                    keywords: ['التكلفة', 'السعر', 'ريال', 'درهم'],
                    className: 'cost',
                    icon: 'fa-money-bill',
                    title: 'التكلفة التقديرية'
                },
                {
                    keywords: ['عاجل', 'مهم', 'خطير', 'فوري'],
                    className: 'priority',
                    icon: 'fa-exclamation-triangle',
                    title: 'الأولوية'
                }
            ];
            
            // تنسيق النسب والأرقام
            formattedContent = formattedContent.replace(/(\d+)%/g, '<span class="percentage">$1%</span>');
            formattedContent = formattedContent.replace(/(\d{4}-\d{2}-\d{2})/g, '<span class="date-highlight">$1</span>');
            
            // تقسيم النص إلى فقرات وتنسيق كل قسم
            const paragraphs = formattedContent.split('\n').filter(p => p.trim());
            let structuredContent = '';
            
            paragraphs.forEach((paragraph, index) => {
                let sectionFound = false;
                
                sections.forEach(section => {
                    if (!sectionFound && section.keywords.some(keyword => paragraph.includes(keyword))) {
                        structuredContent += `
                            <div class="content-section ${section.className}">
                                <h4>
                                    <span class="section-number">${sectionCounter}</span>
                                    <i class="fas ${section.icon} me-2"></i>
                                    ${section.title}
                                </h4>
                                <div class="section-content">
                                    ${formatParagraphContent(paragraph)}
                                </div>
                            </div>
                        `;
                        sectionCounter++;
                        sectionFound = true;
                    }
                });
                
                if (!sectionFound) {
                    structuredContent += `
                        <div class="content-section">
                            <div class="section-content">
                                ${formatParagraphContent(paragraph)}
                            </div>
                        </div>
                    `;
                }
            });
            
            return structuredContent;
        }
        
        // دالة تنسيق فقرة المحتوى
        function formatParagraphContent(paragraph) {
            let formatted = paragraph
                .replace(/\*\*(.*?)\*\*/g, '<strong style="color: #2c3e50;">$1</strong>')
                .replace(/\*(.*?)\*/g, '<em style="color: #6c757d;">$1</em>')
                .replace(/(\d+)\s*(كيلومتر|كم)/g, '<span class="metric-value">$1</span> <span class="metric-label">$2</span>')
                .replace(/(\d+)\s*(يوم|أسبوع|شهر|سنة)/g, '<span class="date-highlight">$1 $2</span>');
            
            // إضافة مؤشرات الحالة
            if (formatted.includes('ممتاز') || formatted.includes('جيد جداً')) {
                formatted = formatted.replace(/(ممتاز|جيد جداً)/g, '<span class="percentage">$1</span>');
            } else if (formatted.includes('تحذير') || formatted.includes('انتبه')) {
                formatted = formatted.replace(/(تحذير|انتبه)/g, '<span class="percentage warning">$1</span>');
            } else if (formatted.includes('خطر') || formatted.includes('عاجل')) {
                formatted = formatted.replace(/(خطر|عاجل)/g, '<span class="percentage danger">$1</span>');
            }
            
            return '<p>' + formatted + '</p>';
        }
        
        // دالة عرض التنبيهات
        function showAlert(message, type = 'info') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type === 'success' ? 'success' : type === 'danger' ? 'danger' : 'info'}-custom alert-custom`;
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                ${message}
            `;
            
            // إدراج التنبيه في أعلى الصفحة
            const alertsContainer = document.getElementById('alertsContainer');
            alertsContainer.appendChild(alertDiv);
            
            // إخفاء التنبيه بعد 5 ثوانِ
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
        
        // تحسين تجربة المستخدم - تنشيط الحفظ التلقائي
        const formInputs = document.querySelectorAll('#carInfoForm input, #carInfoForm textarea');
        let autoSaveTimeout;
        
        formInputs.forEach(input => {
            input.addEventListener('input', function() {
                clearTimeout(autoSaveTimeout);
                
                // الحفظ التلقائي بعد 3 ثوان من التوقف عن الكتابة
                autoSaveTimeout = setTimeout(() => {
                    const form = document.getElementById('carInfoForm');
                    if (form.checkValidity()) {
                        // حفظ تلقائي صامت
                        const formData = new FormData(form);
                        formData.append('_token', csrfToken);
                        
                        fetch('/save-car-info', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            }
                        }).catch(error => console.log('Auto-save failed:', error));
                    }
                }, 3000);
            });
        });
    </script>
</x-app-layout>