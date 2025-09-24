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
            direction: rtl;
        }
        
        .main-container {
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            backdrop-filter: blur(10px);
            margin: 2rem 0;
        }
        
        .header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }
        
        .header h1 {
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
        }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        
        .action-buttons {
            padding: 0 30px;
            margin: 2rem 0;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        
        .btn-full-width {
            flex: 1;
            min-width: 250px;
            min-height: 120px;
            transition: all 0.3s ease;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: white;
        }
        
        .btn-full-width:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            color: white;
        }
        
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            padding: 20px;
        }
        
        .modal-content {
            border-radius: 15px;
            max-width: 60vw;
            width: 100%;
           max-height: 70vh;
            overflow-y: auto;
            position: relative;
        }
        
        .modal-close {
            position: absolute;
            top: 25px;
            left: 20px;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 1.5rem;
            color: #ff0000;
            cursor: pointer;
            z-index: 1001;
        }
        .modal-content::-webkit-scrollbar {
    width: 8px;               /* حجم الشريط */
}

.modal-content::-webkit-scrollbar-track {
    background: transparent;   /* الخلفية شفافة */
}

.modal-content::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.2); /* شبه شفاف */
    border-radius: 10px;
}

.modal-content::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.4); /* يظهر أكثر عند hover */
}

/* لمتصفحات فايرفوكس */
.modal-content {
    scrollbar-width: thin;
    scrollbar-color: rgba(0,0,0,0.2) transparent;
}

        .camera-preview {
            border: 2px solid #007bff;
            border-radius: 15px;
            padding: 1rem;
            background-color: #f8f9fa;
            text-align: center;
        }
        
        .camera-preview video {
            max-height: 400px;
            object-fit: cover;
            border-radius: 10px;
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
        
        @media (max-width: 767px) {
            .phon {
                display: none !important;
            }
                 .modal-content {
            max-width: 90vw;

        }
        
            .action-buttons {
                flex-direction: column;
                align-items: center;
                padding: 0 15px;
            }
            
            .btn-full-width {
                width: 100%;
                min-width: auto;
                min-height: 100px;
                margin-bottom: 15px;
            }
            
            .header h1 {
                font-size: 1.8rem;
            }
            
            .section-card {
                padding: 20px;
            }
        }

        .bg-blue-100 { background-color: #dbeafe; }
        .bg-green-100 { background-color: #dcfce7; }
        .bg-orange-100 { background-color: #fed7aa; }
        .text-blue-600 { color: #2563eb; }
        .text-blue-700 { color: #1d4ed8; }
        .text-green-600 { color: #16a34a; }
        .text-green-700 { color: #15803d; }
        .text-orange-600 { color: #ea580c; }
        .text-orange-700 { color: #c2410c; }
        .line-clamp-3 { 
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

    <div class="container-fluid" style="margin: 60px 0px;">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">
                <div class="main-container">
                    <!-- Header -->
                    <div class="header">
                        <h1><i class="fas fa-car me-3"></i>مكانيكي السيارات الذكي</h1>
                        <p class="mb-0 fs-5">AI Care Assistant - تشخيص ذكي ومتابعة شاملة لسيارتك</p>
                    </div>
                    <br>
                    @if($carInfo)
                    <div class="phon">
                        <!-- إحصائيات السيارة -->
                        <div class="container-fluid">
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <div class="stats-card">
                                        <i class="fas fa-tachometer-alt fa-2x text-danger mb-2"></i>
                                        <h6 class="mb-1">الكيلومترات</h6>
                                        <p class="fw-bold mb-0">{{ $carInfo->current_mileage ?? 'غير محددة' }}</p>
                                    </div>
                                </div>

                                @if($carInfo->last_oil_change)
                                <div class="col-md-3">
                                    <div class="stats-card">
                                        <i class="fas fa-oil-can fa-2x text-dark mb-2"></i>
                                        <h6 class="mb-1">آخر تغيير زيت</h6>
                                        <p class="fw-bold mb-0">{{ $carInfo->last_oil_change->format('Y-m-d') }}</p>
                                    </div>
                                </div>
                                @endif

                                @if($carInfo->last_maintenance)
                                <div class="col-md-3">
                                    <div class="stats-card">
                                        <i class="fas fa-tools fa-2x text-info mb-2"></i>
                                        <h6 class="mb-1">آخر صيانة</h6>
                                        <p class="fw-bold mb-0">{{ $carInfo->last_maintenance->format('Y-m-d') }}</p>
                                    </div>
                                </div>
                                @endif

                                @if($carInfo->fuel_level)
                                <div class="col-md-3">
                                    <div class="stats-card">
                                        <i class="fas fa-gas-pump fa-2x text-secondary mb-2"></i>
                                        <h6 class="mb-1">مستوى الوقود</h6>
                                        <p class="fw-bold mb-0">{{ $carInfo->fuel_level }} لتر</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- إحصائيات التحليلات -->
                    <div class="container-fluid">
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="stats-card bg-blue-100">
                                    <div class="text-3xl font-bold text-blue-600">{{ $analysisCount }}</div>
                                    <div class="text-blue-700 font-semibold">إجمالي التحليلات</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stats-card bg-green-100">
                                    <div class="text-3xl font-bold text-green-600">{{ $conditionAnalysisCount }}</div>
                                    <div class="text-green-700 font-semibold">تحليل الحالة</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stats-card bg-orange-100">
                                    <div class="text-3xl font-bold text-orange-600">{{ $problemDiagnosisCount }}</div>
                                    <div class="text-orange-700 font-semibold">تشخيص الأعطال</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
<!-- الأزرار الرئيسية -->
<div class="action-buttons">
    <button type="button" class="btn btn-warning btn-full-width" onclick="openQuickCamera()">
        <i class="fas fa-camera"></i>
        <span>تحليل الصور</span>
        <small>التقط صورة السيارات لتحليلها</small>
    </button>

    <button type="button" class="btn btn-danger btn-full-width" onclick="openDiagnosisModal()">
        <i class="fas fa-stethoscope"></i>
        <span>تحليل العطب</span>
        <small>تشخيص الأعطال والمشاكل الميكانيكية</small>
    </button>

    <button type="button" class="btn btn-success btn-full-width" onclick="openCarInfoModal()">
        <i class="fas fa-car"></i>
        <span>حفظ ومتابعة</span>
        <small>أدخل تفاصيل سيارتك ومتابعة حالتها</small>
    </button>
</div>

                    <!-- النافذة المنبثقة -->
                    <div id="modalOverlay" class="modal-overlay" onclick="closeModal()">
                        <div class="modal-content" onclick="event.stopPropagation()">
                            <button class="modal-close" onclick="closeModal()">&times;</button>
                            <div id="modalBody"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <br>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // إعداد CSRF Token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        
        if (!csrfToken) {
            console.error('CSRF Token not found!');
        }

        let stream = null;
        let isQuickCamera = false;
        let quickImageFile = null;
        let currentFacingMode = 'environment';
        let selectedImages = [];

        // فتح نافذة معلومات السيارة
        function openCarInfoModal() {
            const modalBody = document.getElementById('modalBody');
            modalBody.innerHTML = `
                <div class="section-card">
                    <h2 class="section-title">
                        <i class="fas fa-tools me-3"></i>
                        متابعة حالة السيارة
                    </h2>
                    <form id="carInfoForm">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="carBrand" class="form-label fw-bold">
                                    <i class="fas fa-car me-2 text-primary"></i>
                                    ماركة السيارة
                                </label>
                                <input type="text" class="form-control" id="carBrand" name="car_brand" placeholder="تويوتا، هونداي، رينو..." value="{{ $carInfo->car_brand ?? '' }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="carModel" class="form-label fw-bold">
                                    <i class="fas fa-tag me-2 text-primary"></i>
                                    الموديل
                                </label>
                                <input type="text" class="form-control" id="carModel" name="car_model" placeholder="كامري، إلنترا، كليو..." value="{{ $carInfo->car_model ?? '' }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="carYear" class="form-label fw-bold">
                                    <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                    سنة الصنع
                                </label>
                                <input type="number" class="form-control" id="carYear" name="car_year" min="1990" max="{{ date('Y') + 1 }}" placeholder="{{ date('Y') }}" value="{{ $carInfo->car_year ?? '' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fuelLevel" class="form-label fw-bold">
                                    <i class="fas fa-gas-pump me-2 text-success"></i>
                                    مستوى الوقود (لتر)
                                </label>
                                <input type="number" class="form-control" id="fuelLevel" name="fuel_level" min="0" max="100" step="0.1" placeholder="45.5" value="{{ $carInfo->fuel_level ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="currentMileage" class="form-label fw-bold">
                                    <i class="fas fa-tachometer-alt me-2 text-primary"></i>
                                    الكيلومترات الحالية
                                </label>
                                <input type="number" class="form-control" id="currentMileage" name="current_mileage" min="0" placeholder="150000" value="{{ $carInfo->current_mileage ?? '' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="lastOilChange" class="form-label fw-bold">
                                    <i class="fas fa-oil-can me-2 text-warning"></i>
                                    آخر تغيير زيت
                                </label>
                                <input type="date" class="form-control" id="lastOilChange" name="last_oil_change" value="{{ $carInfo && $carInfo->last_oil_change ? $carInfo->last_oil_change->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lastMaintenance" class="form-label fw-bold">
                                    <i class="fas fa-wrench me-2 text-info"></i>
                                    آخر صيانة
                                </label>
                                <input type="date" class="form-control" id="lastMaintenance" name="last_maintenance" value="{{ $carInfo && $carInfo->last_maintenance ? $carInfo->last_maintenance->format('Y-m-d') : '' }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label fw-bold">
                                <i class="fas fa-sticky-note me-2 text-secondary"></i>
                                ملاحظات إضافية
                            </label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="أي ملاحظات أو مشاكل تود تسجيلها...">{{ $carInfo->notes ?? '' }}</textarea>
                        </div>
                        
        
                        <div id="analysisResult" class="analysis-result mt-3" style="display: none;"></div>
                        
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-save me-2"></i>
                            حفظ ومتابعة
                        </button>
                    </form>
                </div>
            `;
            
            // إضافة معالج الحدث للنموذج بعد إنشائه
            setTimeout(() => {
                const form = document.getElementById('carInfoForm');
                if (form) {
                    form.addEventListener('submit', handleCarInfoSubmit);
                    
                }
            }, 100);
            
            document.getElementById('modalOverlay').style.display = 'flex';
        }

        // معالج تقديم نموذج معلومات السيارة
        async function handleCarInfoSubmit(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('_token', csrfToken);
            
            // إظهار التحميل
            const loadingElement = document.getElementById('saveLoading');
            const resultElement = document.getElementById('analysisResult');
            const submitButton = this.querySelector('button[type="submit"]');
            
            if (loadingElement) loadingElement.style.display = 'block';
            if (resultElement) resultElement.style.display = 'none';
            
            // تعطيل زر الحفظ
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري الحفظ...';
            
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
                if (loadingElement) loadingElement.style.display = 'none';
                
                if (result.success) {
                    showAlert('تم حفظ معلومات السيارة بنجاح!', 'success');
                    
                    // عرض التحليل إذا كان متوفراً
                    if (result.analysis && resultElement) {
                        displayAnalysisResult(result.analysis, 'analysis', 'analysisResult');
                    }
                    
       
                } else {
                    showAlert('حدث خطأ: ' + (result.message || 'يرجى المحاولة مرة أخرى'), 'danger');
                }
                
            } catch (error) {
                console.error('Error:', error);
                if (loadingElement) loadingElement.style.display = 'none';
                showAlert('حدث خطأ في الاتصال. يرجى المحاولة مرة أخرى.', 'danger');
            } finally {
                // إعادة تفعيل زر الحفظ
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-save me-2"></i>حفظ ومتابعة';
                
            }
        }

        // فتح نافذة تشخيص المشاكل الكاملة
        function openDiagnosisModal() {
            isQuickCamera = false;
            const modalBody = document.getElementById('modalBody');
            modalBody.innerHTML = `
                <div class="section-card">
                    <h2 class="section-title">
                        <i class="fas fa-stethoscope me-3"></i>
                        تشخيص الأعطال الميكانيكية
                    </h2>
                    <form id="diagnosisForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="problemDescription" class="form-label fw-bold">
                                <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                                وصف المشكلة أو العطب
                            </label>
                            <textarea class="form-control" id="problemDescription" name="problem_description" rows="5" placeholder="اكتب هنا وصفاً مفصلاً للمشكلة..." required></textarea>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                كن مفصلاً قدر الإمكان لتشخيص أدق
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-camera me-2 text-primary"></i>
                                إضافة صور للمشكلة (اختياري)
                            </label>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3" onclick="document.getElementById('problemImages').click()">
                                        <i class="fas fa-folder-open fa-2x mb-2"></i>
                                        <span>اختيار من الملفات</span>
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-success w-100 d-flex flex-column align-items-center py-3" onclick="openCamera()">
                                        <i class="fas fa-camera fa-2x mb-2"></i>
                                        <span>التقاط صورة</span>
                                    </button>
                                </div>
                            </div>
                            
                            <input type="file" id="problemImages" name="problem_images[]" multiple accept="image/*" style="display: none;">
                            
                            <div id="cameraPreview" class="camera-preview" style="display: none;">
                                <video id="cameraVideo" autoplay playsinline class="w-100 rounded"></video>
                                <div class="camera-controls mt-2">
                                    <button type="button" class="btn btn-success me-2" onclick="capturePhoto()">
                                        <i class="fas fa-camera me-1"></i>التقاط
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="closeCamera()">
                                        <i class="fas fa-times me-1"></i>إلغاء
                                    </button>
                                </div>
                            </div>
                            
                            <div id="imagePreview" class="image-preview mt-3"></div>
                        </div>
                        
              
                        
                        <div id="diagnosisResult" class="analysis-result mt-3" style="display: none;"></div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>
                            تحليل العطب
                        </button>
                    </form>
                </div>
            `;
            
            // إضافة معالج الحدث للنموذج
            setTimeout(() => {
                const form = document.getElementById('diagnosisForm');
                const fileInput = document.getElementById('problemImages');
                
                if (form) {
                    form.addEventListener('submit', handleDiagnosisSubmit);
                }
                
                if (fileInput) {
                    fileInput.addEventListener('change', handleDiagnosisImages);
                }
                
                // إعادة تهيئة الصور المحددة
                selectedImages = [];
            }, 100);
            
            document.getElementById('modalOverlay').style.display = 'flex';
        }

        // معالج الصور في التشخيص
        function handleDiagnosisImages(e) {
            const preview = document.getElementById('imagePreview');
            const files = Array.from(e.target.files);
            
            files.forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    selectedImages.push(file);
                    displayImagePreview(file, preview, selectedImages.length - 1);
                }
            });
        }

        // عرض معاينة الصورة
        function displayImagePreview(file, preview, index) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imgDiv = document.createElement('div');
                imgDiv.className = 'image-preview-item';
                imgDiv.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <button type="button" onclick="removeDiagnosisImage(${index})" class="remove-image">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                preview.appendChild(imgDiv);
            };
            reader.readAsDataURL(file);
        }

        // إزالة صورة من التشخيص
        function removeDiagnosisImage(index) {
            selectedImages.splice(index, 1);
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            
            // إعادة عرض الصور المتبقية
            selectedImages.forEach((file, i) => {
                displayImagePreview(file, preview, i);
            });
            
            // تحديث input الملفات
            updateFileInput();
        }

        // تحديث input الملفات
        function updateFileInput() {
            const fileInput = document.getElementById('problemImages');
            const dt = new DataTransfer();
            
            selectedImages.forEach(file => {
                dt.items.add(file);
            });
            
            fileInput.files = dt.files;
        }

        // فتح الكاميرا للتشخيص
        async function openCamera() {
            try {
                if (stream) {
                    closeCamera();
                }
                
                stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'environment' }
                });
                
                const video = document.getElementById('cameraVideo');
                const preview = document.getElementById('cameraPreview');
                
                if (video && preview) {
                    video.srcObject = stream;
                    preview.style.display = 'block';
                }
            } catch (err) {
                console.error('خطأ في الوصول للكاميرا:', err);
                showAlert('لا يمكن الوصول للكاميرا. تأكد من الأذونات.', 'danger');
            }
        }

        // إغلاق الكاميرا
        function closeCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            
            const preview = document.getElementById('cameraPreview');
            if (preview) {
                preview.style.display = 'none';
            }
        }

        // التقاط صورة من الكاميرا
        function capturePhoto() {
            const video = document.getElementById('cameraVideo');
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            ctx.drawImage(video, 0, 0);
            
            canvas.toBlob(function(blob) {
                const file = new File([blob], `camera_${Date.now()}.jpg`, { type: 'image/jpeg' });
                selectedImages.push(file);
                
                const preview = document.getElementById('imagePreview');
                displayImagePreview(file, preview, selectedImages.length - 1);
                updateFileInput();
                
                closeCamera();
            }, 'image/jpeg', 0.8);
        }

// معالج تقديم نموذج التشخيص
async function handleDiagnosisSubmit(e) {
    e.preventDefault(); // منع الإرسال التقليدي للنموذج
    
    const formData = new FormData(this);
    formData.append('_token', csrfToken);
    
    // التحقق من وجود وصف للمشكلة
    const problemDescription = formData.get('problem_description');
    if (!problemDescription || problemDescription.trim() === '') {
        showAlert('يرجى إدخال وصف للمشكلة', 'warning');
        return;
    }
    
    // إظهار التحميل
    const resultElement = document.getElementById('diagnosisResult');
    const submitButton = this.querySelector('button[type="submit"]');
    
    if (resultElement) resultElement.style.display = 'none';
    
    // تعطيل زر التحليل
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
        
        if (result.success) {
            showAlert('تم تحليل المشكلة بنجاح!', 'success');
            
            // عرض نتائج التحليل دون إعادة تحميل الصفحة
            if (result.diagnosis && resultElement) {
                displayAnalysisResult(result.diagnosis, 'diagnosis', 'diagnosisResult');
                resultElement.style.display = 'block';
            }
            
        } else {
            showAlert('حدث خطأ: ' + (result.message || 'يرجى المحاولة مرة أخرى'), 'danger');
        }
        
    } catch (error) {
        console.error('Error:', error);
        showAlert('حدث خطأ في الاتصال. يرجى المحاولة مرة أخرى.', 'danger');
    } finally {
        // إعادة تفعيل زر التحليل
        submitButton.disabled = false;
        submitButton.innerHTML = '<i class="fas fa-search me-2"></i>تحليل العطب';
    }
}

        // فتح الكاميرا السريعة للتشخيص
        function openQuickCamera() {
            isQuickCamera = true;
            const modalBody = document.getElementById('modalBody');
            modalBody.innerHTML = `
                <div class="section-card">
                    <h2 class="section-title">
                        <i class="fas fa-camera me-3"></i>
                        تصوير سريع للتشخيص
                    </h2>
                    <p class="text-muted mb-3">التقط صورة للمركبة لتحليل نوعها وسنة الصنع والموديل والحالة الميكانيكية</p>
                    
                    <form id="quickDiagnosisForm">
                        <div id="cameraPreview" class="camera-preview mb-3">
                            <video id="cameraVideo" autoplay playsinline class="w-100 rounded" style="max-height: 400px;"></video>
                            <div class="camera-controls mt-2 text-center">
                                <button type="button" class="btn btn-success me-2" onclick="captureQuickPhoto()">
                                    <i class="fas fa-camera me-1"></i>التقاط صورة
                                </button>
                                <button type="button" class="btn btn-outline-secondary me-2" onclick="switchCamera()">
                                    <i class="fas fa-sync-alt me-1"></i>تبديل الكاميرا
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="closeQuickCamera()">
                                    <i class="fas fa-times me-1"></i>إغلاق
                                </button>
                            </div>
                        </div>
                        
                        <div id="quickImagePreview" class="image-preview mt-3 mb-3 text-center"></div>
                        
                        <button type="button" class="btn btn-primary w-100 mt-3" style="display: none;" id="analyzeQuickBtn" onclick="analyzeQuickImage()">
                            <i class="fas fa-search me-2"></i>
                            تحليل الصورة
                        </button>
                        
                        <button type="button" class="btn btn-outline-secondary w-100 mt-2" style="display: none;" id="retakePhotoBtn" onclick="retakePhoto()">
                            <i class="fas fa-redo me-2"></i>
                            إعادة التقاط الصورة
                        </button>

                        
                        <div id="quickAnalysisResult" class="analysis-result mt-3" style="display: none;"></div>
                    </form>
                </div>
            `;
            document.getElementById('modalOverlay').style.display = 'flex';
            
            // فتح الكاميرا تلقائياً
            setTimeout(() => openQuickCameraStream(), 100);
        }

        // فتح تيار الكاميرا للتصوير السريع
        async function openQuickCameraStream() {
            try {
                if (stream) {
                    closeQuickCamera();
                }
                
                stream = await navigator.mediaDevices.getUserMedia({
                    video: { 
                        facingMode: currentFacingMode,
                        width: { ideal: 1920 },
                        height: { ideal: 1080 }
                    }
                });
                
                const video = document.getElementById('cameraVideo');
                if (video) {
                    video.srcObject = stream;
                }
            } catch (err) {
                console.error('خطأ في الوصول للكاميرا:', err);
                showAlert('لا يمكن الوصول للكاميرا. تأكد من الأذونات.', 'danger');
            }
        }

        // تبديل الكاميرا (أمامية/خلفية)
        function switchCamera() {
            currentFacingMode = currentFacingMode === 'environment' ? 'user' : 'environment';
            openQuickCameraStream();
        }

        // التقاط صورة سريعة
        function captureQuickPhoto() {
            const video = document.getElementById('cameraVideo');
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            ctx.drawImage(video, 0, 0);
            
            canvas.toBlob(function(blob) {
                quickImageFile = new File([blob], `quick_analysis_${Date.now()}.jpg`, { type: 'image/jpeg' });
                displayQuickImagePreview(quickImageFile);
                
                // إظهار أزرار التحليل وإعادة الالتقاط
                document.getElementById('analyzeQuickBtn').style.display = 'block';
                document.getElementById('retakePhotoBtn').style.display = 'block';
                
                // إخفاء عناصر الكاميرا
                document.getElementById('cameraPreview').style.display = 'none';
            }, 'image/jpeg', 0.9);
        }

        // عرض معاينة الصورة السريعة
        function displayQuickImagePreview(file) {
            const preview = document.getElementById('quickImagePreview');
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.innerHTML = `
                    <div class="quick-image-preview text-center">
                        <h5 class="text-success mb-3">
                            <i class="fas fa-check-circle me-2"></i>
                            تم التقاط الصورة بنجاح
                        </h5>
                        <img src="${e.target.result}" class="img-fluid rounded shadow" style="max-height: 300px;">
                        <p class="text-muted mt-2">اضغط على "تحليل الصورة" لبدء التشخيص</p>
                    </div>
                `;
            };
            
            reader.readAsDataURL(file);
        }

// تحليل الصورة السريعة
async function analyzeQuickImage() {
    if (!quickImageFile) {
        showAlert('يرجى التقاط صورة أولاً', 'warning');
        return;
    }

    const formData = new FormData();
    formData.append('quick_image', quickImageFile);
    formData.append('_token', csrfToken);

    // إظهار التحميل وإخفاء النتائج
    const resultElement = document.getElementById('quickAnalysisResult');
    const analyzeBtn = document.getElementById('analyzeQuickBtn');
    const retakeBtn = document.getElementById('retakePhotoBtn');
    
    if (resultElement) resultElement.style.display = 'none';
    if (analyzeBtn) analyzeBtn.style.display = 'none';
    if (retakeBtn) retakeBtn.style.display = 'none';

    // إظهار رسالة تحميل
    const preview = document.getElementById('quickImagePreview');
    if (preview) {
        preview.innerHTML = `
            <div class="quick-image-preview text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">جاري التحليل...</span>
                </div>
                <h5 class="text-primary">جاري تحليل الصورة</h5>
                <p class="text-muted">قد يستغرق هذا بضع ثوانٍ</p>
            </div>
        `;
    }

    try {
        const response = await fetch('/quick-image-analysis', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });

        const result = await response.json();

        if (result.success) {
            // عرض نتائج التحليل الكاملة دون إعادة تحميل الصفحة
            if (result.analysis && resultElement) {
                displayQuickAnalysisResult(result.analysis);
                resultElement.style.display = 'block';
            }
            
            // إعادة عرض أزرار التحكم
            if (analyzeBtn) analyzeBtn.style.display = 'block';
            if (retakeBtn) retakeBtn.style.display = 'block';
            
        } else {
            showAlert('حدث خطأ: ' + result.message, 'danger');
            // إعادة عرض أزرار التحكم
            if (analyzeBtn) analyzeBtn.style.display = 'block';
            if (retakeBtn) retakeBtn.style.display = 'block';
            
            // إعادة عرض الصورة
            displayQuickImagePreview(quickImageFile);
        }

    } catch (error) {
        console.error('Error:', error);
        showAlert('حدث خطأ في الاتصال. يرجى المحاولة مرة أخرى.', 'danger');
        
        // إعادة عرض أزرار التحكم
        if (analyzeBtn) analyzeBtn.style.display = 'block';
        if (retakeBtn) retakeBtn.style.display = 'block';
        
        // إعادة عرض الصورة
        displayQuickImagePreview(quickImageFile);
    }
}

// عرض نتائج التحليل السريع
function displayQuickAnalysisResult(content) {
    const container = document.getElementById('quickAnalysisResult');
    
    container.innerHTML = `
        <div class="analysis-result-content">
            <div class="analysis-header mb-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="analysis-title">
                        <i class="fas fa-car me-2 text-primary"></i>
                        نتائج التحليل السريع للمركبة
                    </div>
                    <div class="analysis-date">
                        <small class="text-muted">${new Date().toLocaleDateString('ar-SA')} - ${new Date().toLocaleTimeString('ar-SA', {hour: '2-digit', minute: '2-digit'})}</small>
                    </div>
                </div>
            </div>
            <div class="analysis-content">
                ${formatQuickAnalysisContent(content)}
            </div>
            <div class="mt-3 text-center border-top pt-3">
                <button type="button" class="btn btn-success" onclick="saveAndCloseAnalysis()">
                    <i class="fas fa-times me-1"></i>إغلاق
                </button>
            </div>
        </div>
    `;
    
    container.style.display = 'block';
}


        // تنسيق محتوى التحليل السريع
        function formatQuickAnalysisContent(content) {
            let formattedContent = content.replace(/\n/g, '<br>');
            formattedContent = formattedContent.replace(/\*\*([^*]+)\*\*/g, '<strong class="text-dark">$1</strong>');
            return `<div class="quick-analysis-text">${formattedContent}</div>`;
        }

        // إعادة التقاط الصورة
        function retakePhoto() {
            quickImageFile = null;
            
            // إعادة عرض الكاميرا
            document.getElementById('cameraPreview').style.display = 'block';
            document.getElementById('quickImagePreview').innerHTML = '';
            document.getElementById('quickAnalysisResult').style.display = 'none';
            document.getElementById('analyzeQuickBtn').style.display = 'none';
            document.getElementById('retakePhotoBtn').style.display = 'none';
            
            // إعادة فتح الكاميرا
            openQuickCameraStream();
        }

        // إغلاق الكاميرا السريعة
        function closeQuickCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            quickImageFile = null;
            closeModal();
        }


// دالة عرض النتائج
function displayAnalysisResult(content, type, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    container.innerHTML = `
        <div class="analysis-result">
            <div class="analysis-header">
                <div class="analysis-title">
                    <i class="fas fa-${type === 'diagnosis' ? 'stethoscope' : 'chart-line'} me-2"></i>
                    ${type === 'diagnosis' ? 'نتائج التشخيص' : 'تحليل حالة السيارة'}
                </div>
                <div class="analysis-date">
                    ${new Date().toLocaleDateString('ar-SA')} - ${new Date().toLocaleTimeString('ar-SA', {hour: '2-digit', minute: '2-digit'})}
                </div>
            </div>
            <div class="analysis-content">
                ${formatContent(content)}
            </div>
            <div class="mt-3 text-center">
                <button type="button" class="btn btn-success" onclick="saveAndCloseAnalysis()">
                    <i class="fas fa-times me-1"></i>إغلاق
                </button>
            </div>
        </div>
    `;
    
    container.style.display = 'block';
}

// حفظ النتائج وإغلاق النافذة
function saveAndCloseAnalysis() {
    closeModal();
}

        // دالة تنسيق المحتوى
        function formatContent(content) {
            if (!content) return '<p>لا توجد نتائج للعرض</p>';
            
            let formattedContent = content.replace(/\n/g, '<br>');
            formattedContent = formattedContent.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            
            return `<div class="content-text">${formattedContent}</div>`;
        }

        // دالة عرض التنبيهات
        function showAlert(message, type = 'info') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                min-width: 300px;
                direction: rtl;
            `;
            
            const typeText = type === 'success' ? 'نجاح' : type === 'danger' ? 'خطأ' : 'تنبيه';
            alertDiv.innerHTML = `
                <strong>${typeText}</strong> 
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(alertDiv);
            
            // إزالة التنبيه تلقائياً بعد 5 ثوان
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.parentNode.removeChild(alertDiv);
                }
            }, 5000);
        }
// حفظ نتائج التحليل السريع
async function saveQuickAnalysis() {
    if (!quickImageFile) {
        showAlert('لا توجد نتائج للحفظ', 'warning');
        return;
    }

    try {
        const formData = new FormData();
        formData.append('quick_image', quickImageFile);
        formData.append('_token', csrfToken);
        formData.append('save_results', 'true');

        const response = await fetch('/save-quick-analysis', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });

        const result = await response.json();

        if (result.success) {
            showAlert('تم حفظ نتائج التحليل بنجاح', 'success');
            setTimeout(() => {
                closeModal();
                // يمكنك إضافة تحديث جزئي للصفحة هنا إذا أردت
            }, 1500);
        } else {
            showAlert('حدث خطأ أثناء الحفظ: ' + result.message, 'danger');
        }

    } catch (error) {
        console.error('Error:', error);
        showAlert('حدث خطأ في الاتصال أثناء الحفظ', 'danger');
    }
}
// حفظ النتائج وإغلاق النافذة
function saveAndCloseAnalysis() {
    showAlert('تم حفظ نتائج التحليل بنجاح', 'success');
    setTimeout(() => {
        closeModal();
        // لا تقم بإعادة تحميل الصفحة تلقائياً
    }, 1000);
}

        // إغلاق النافذة المنبثقة
        function closeModal() {
            document.getElementById('modalOverlay').style.display = 'none';
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
        }

        // دالة حذف التحليل
        function deleteAnalysis(id) {
            if (confirm('هل أنت متأكد من حذف هذا التحليل؟ لن يمكن استرداده بعد الحذف.')) {
                fetch(`/analysis/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
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