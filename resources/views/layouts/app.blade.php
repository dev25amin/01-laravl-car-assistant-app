<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'مكانيكي السيارة الذكي')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- تخصيص Tailwind للغة العربية -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'arabic': ['Cairo', 'Tajawal', 'Arial', 'sans-serif']
                    }
                }
            }
        }
    </script>
    
    <!-- خط جوجل العربي -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome للأيقونات -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * { font-family: 'Cairo', 'Tajawal', Arial, sans-serif; }
        body { min-height: 100vh; }

        .navbar-shadow { box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); }
        .btn-gradient {
            background: linear-gradient(45deg, #667eea, #764ba2);
            transition: all 0.3s ease;
        }
        .btn-gradient:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4); }
        .animate-fade-in { animation: fadeIn 0.5s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .notification { transition: all 0.3s ease; }
        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
    
    @stack('styles')
</head>
<body>
<nav class="bg-white navbar-shadow fixed w-full top-0 z-50" dir="ltr">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <!-- الشعار -->
            <div class="flex items-center">
                <a href="{{ route('car.assistant') }}" class="flex items-center space-x-3 space-x-reverse">
                    <div style="margin: 10px" class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-car text-white text-lg" ></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">مكانيكي السيارة</h1>
                    </div>
                </a>
            </div>

            <!-- قائمة التنقل -->
            <div class="hidden md:flex items-center space-x-6 space-x-reverse">
                <a href="{{ route('car.assistant') }}" 
                   class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition duration-200 {{ request()->routeIs('car.assistant') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-home ml-2"  style="margin: 10px"></i>
                    الرئيسية
                </a>
                <a href="{{ route('analysis.history') }}" 
                   class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition duration-200 {{ request()->routeIs('analysis.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-history ml-2"  style="margin: 10px"></i>
                    سجل التحليلات
                </a>

                <!-- زر المستخدم -->
                <div class="relative">

                    <button id="user-menu-button" class="flex items-center space-x-2 space-x-reverse text-gray-700 hover:text-blue-600 focus:outline-none">
                         <i class="fas fa-user text-2xl" style="margin: 10px"> </i><span> {{ auth()->user()->name }} </span>  
                        <i class="fas fa-caret-down ml-1"> </i>
                    </button>

                    <!-- القائمة المنسدلة -->
                    <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white shadow-lg rounded-md py-2 z-50">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">الملف الشخصي</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">تسجيل الخروج</button>
                        </form>
                    </div>
                </div>
                
            </div>

            <!-- زر القائمة للموبايل -->
            <div class="md:hidden flex items-center space-x-2 space-x-reverse">
                <!-- زر المستخدم للموبايل -->
                <button id="mobile-user-menu-button" class="text-gray-700 hover:text-blue-600 focus:outline-none">
                </button>
                <button id="mobile-menu-button" class="text-gray-700 hover:text-blue-600 focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>

        <!-- القائمة المنسدلة للموبايل -->
        <div id="mobile-menu" class="hidden md:hidden pb-4">
            <div class="space-y-2">
                <a href="{{ route('car.assistant') }}" 
                   class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition duration-200 {{ request()->routeIs('car.assistant') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-home ml-2"></i>
                    الرئيسية
                </a>
                
                <a href="{{ route('analysis.history') }}" 
                   class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition duration-200 {{ request()->routeIs('analysis.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-history ml-2"></i>
                    سجل التحليلات
                </a>

                <!-- المستخدم للموبايل -->
                <a href="{{ route('profile.edit') }}" class="flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-user ml-2"></i>
                    الملف الشخصي
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-100">تسجيل الخروج</button>
                </form>
            </div>
        </div>
    </div>
</nav>

<script>
    // القائمة المنسدلة للمستخدم (desktop)
    const userMenuButton = document.getElementById('user-menu-button');
    const userMenu = document.getElementById('user-menu');
    userMenuButton?.addEventListener('click', () => userMenu.classList.toggle('hidden'));
    window.addEventListener('click', e => {
        if (!userMenuButton?.contains(e.target) && !userMenu?.contains(e.target)) {
            userMenu?.classList.add('hidden');
        }
    });

    // القائمة للموبايل
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    mobileMenuButton.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));
</script>

    
    <!-- المحتوى الرئيسي -->

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

    
    <script>
        // القائمة المنسدلة للموبايل
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
        
        // قائمة الملف الشخصي
        document.getElementById('profile-menu-button').addEventListener('click', function() {
            document.getElementById('profile-menu').classList.toggle('hidden');
        });

        // نظام الإشعارات
        function showNotification(message, type = 'info', duration = 5000) {
            const notificationsContainer = document.getElementById('notifications');
            const notification = document.createElement('div');
            let bgColor = 'bg-blue-500';
            let icon = 'fa-info-circle';
            if (type === 'success') { bgColor = 'bg-green-500'; icon = 'fa-check-circle'; }
            else if (type === 'error') { bgColor = 'bg-red-500'; icon = 'fa-times-circle'; }
            else if (type === 'warning') { bgColor = 'bg-yellow-500'; icon = 'fa-exclamation-triangle'; }
            notification.className = `notification ${bgColor} text-white px-4 py-3 rounded-lg shadow-lg max-w-sm transform translate-x-full`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${icon} ml-3"></i>
                    <span class="flex-1">${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="mr-2 hover:bg-black hover:bg-opacity-20 rounded p-1">
                        <i class="fas fa-times"></i>
                    </button>
                </div>`;
            notificationsContainer.appendChild(notification);
            setTimeout(() => notification.classList.remove('translate-x-full'), 100);
            setTimeout(() => { notification.classList.add('translate-x-full'); setTimeout(() => notification.remove(), 300); }, duration);
        }

        // مؤشر التحميل
        function showLoading(element) {
            if (!element) return;
            const originalContent = element.innerHTML;
            element.innerHTML = '<div class="loading-spinner inline-block mr-2"></div> جاري التحميل...';
            element.disabled = true;
            return function() { element.innerHTML = originalContent; element.disabled = false; };
        }

        // إعداد CSRF token لجميع طلبات fetch
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const originalFetch = window.fetch;
        window.fetch = function(url, options = {}) {
            if (!options.headers) options.headers = {};
            if (!options.headers['X-CSRF-TOKEN']) options.headers['X-CSRF-TOKEN'] = csrfToken;
            return originalFetch(url, options);
        };
    </script>

    @stack('scripts')
</body>
</html>
