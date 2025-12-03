<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سیستم جامع مدیریت نمرات - تاریخ شمسی</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css">
    <script src="https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js"></script>
    <style>
        .grade-excellent { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .grade-good { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
        .grade-average { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .grade-weak { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
        .grade-poor { background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%); }
        
        .status-original { 
            background-color: #d1fae5; 
            border-right: 4px solid #10b981;
        }
        .status-edited { 
            background-color: #fef3c7; 
            border-right: 4px solid #f59e0b;
        }
        
        .chart-bar { transition: all 0.3s ease; }
        .chart-bar:hover { transform: scaleY(1.1); }
        
        .floating-btn {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); }
            100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
        }
        
        .modal-enter {
            animation: modalEnter 0.3s ease-out;
        }
        
        @keyframes modalEnter {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        
        .sidebar {
            transition: all 0.3s ease;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                right: -300px;
                top: 0;
                height: 100vh;
                z-index: 1000;
                width: 300px;
            }
            .sidebar.active {
                right: 0;
            }
            .overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 999;
            }
            .overlay.active {
                display: block;
            }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">
    <!-- نوار کناری برای فیلترها -->
    <div class="overlay" onclick="toggleSidebar()"></div>
    <div class="sidebar bg-white w-80 p-6 shadow-xl fixed right-0 top-0 h-full overflow-y-auto z-1000">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="fas fa-sliders-h ml-2 text-blue-500"></i>
                فیلترها و تنظیمات
            </h3>
            <button onclick="toggleSidebar()" class="md:hidden text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        
        <!-- فیلتر بازه تاریخ -->
        <div class="mb-8">
            <h4 class="font-semibold text-gray-700 mb-4 flex items-center">
                <i class="fas fa-calendar-alt ml-2 text-green-500"></i>
                فیلتر بر اساس بازه تاریخ
            </h4>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-2">از تاریخ</label>
                    <input type="text" id="filterStartDate" 
                           class="w-full p-3 border border-gray-300 rounded-lg text-center datepicker-input"
                           placeholder="1404/05/01">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-2">تا تاریخ</label>
                    <input type="text" id="filterEndDate" 
                           class="w-full p-3 border border-gray-300 rounded-lg text-center datepicker-input"
                           placeholder="1404/05/15">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <button onclick="applyDateFilter()" 
                            class="bg-green-500 hover:bg-green-600 text-white p-3 rounded-lg font-medium transition">
                        <i class="fas fa-filter ml-1"></i>اعمال فیلتر
                    </button>
                    <button onclick="clearDateFilter()" 
                            class="bg-gray-500 hover:bg-gray-600 text-white p-3 rounded-lg font-medium transition">
                        <i class="fas fa-times ml-1"></i>حذف فیلتر
                    </button>
                </div>
            </div>
        </div>
        
        <!-- فیلتر نمره -->
        <div class="mb-8">
            <h4 class="font-semibold text-gray-700 mb-4 flex items-center">
                <i class="fas fa-chart-bar ml-2 text-purple-500"></i>
                فیلتر بر اساس نمره
            </h4>
            <div class="space-y-2">
                <div class="flex items-center">
                    <input type="checkbox" id="filterGradeExcellent" class="ml-2" checked>
                    <label for="filterGradeExcellent" class="cursor-pointer">عالی (18-20)</label>
                    <span class="mr-auto w-3 h-3 rounded-full bg-green-500"></span>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="filterGradeGood" class="ml-2" checked>
                    <label for="filterGradeGood" class="cursor-pointer">خوب (15-17.99)</label>
                    <span class="mr-auto w-3 h-3 rounded-full bg-blue-500"></span>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="filterGradeAverage" class="ml-2" checked>
                    <label for="filterGradeAverage" class="cursor-pointer">متوسط (12-14.99)</label>
                    <span class="mr-auto w-3 h-3 rounded-full bg-yellow-500"></span>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="filterGradeWeak" class="ml-2" checked>
                    <label for="filterGradeWeak" class="cursor-pointer">ضعیف (10-11.99)</label>
                    <span class="mr-auto w-3 h-3 rounded-full bg-orange-500"></span>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="filterGradePoor" class="ml-2" checked>
                    <label for="filterGradePoor" class="cursor-pointer">مردود (0-9.99)</label>
                    <span class="mr-auto w-3 h-3 rounded-full bg-red-500"></span>
                </div>
            </div>
        </div>
        
        <!-- فیلتر وضعیت ویرایش -->
        <div class="mb-8">
            <h4 class="font-semibold text-gray-700 mb-4 flex items-center">
                <i class="fas fa-edit ml-2 text-yellow-500"></i>
                فیلتر وضعیت ویرایش
            </h4>
            <div class="space-y-2">
                <div class="flex items-center">
                    <input type="checkbox" id="filterStatusOriginal" class="ml-2" checked>
                    <label for="filterStatusOriginal" class="cursor-pointer">نمرات اصلی</label>
                    <span class="mr-auto w-3 h-3 rounded-full bg-green-500"></span>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="filterStatusEdited" class="ml-2" checked>
                    <label for="filterStatusEdited" class="cursor-pointer">نمرات ویرایش شده</label>
                    <span class="mr-auto w-3 h-3 rounded-full bg-yellow-500"></span>
                </div>
            </div>
        </div>
        
        <!-- اطلاعات فیلتر -->
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <h5 class="font-semibold text-blue-700 mb-2">
                <i class="fas fa-info-circle ml-1"></i>
                اطلاعات فیلتر
            </h5>
            <div class="space-y-1 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">تعداد فیلتر شده:</span>
                    <span id="sidebarFilterCount" class="font-bold">0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">کل نمرات:</span>
                    <span id="sidebarTotalCount" class="font-bold">0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">بازه تاریخ:</span>
                    <span id="sidebarDateRange" class="font-bold">---</span>
                </div>
            </div>
        </div>
    </div>

    <!-- محتوای اصلی -->
    <div class="md:mr-80">
        <!-- هدر -->
        <header class="gradient-bg text-white p-6 shadow-lg">
            <div class="container mx-auto">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold mb-2">
                            <i class="fas fa-graduation-cap ml-3"></i>
                            سیستم جامع مدیریت نمرات
                        </h1>
                        <p class="text-blue-100">مدیریت کامل نمرات با تاریخ شمسی و امکان فیلتر پیشرفته</p>
                    </div>
                    <div class="flex items-center space-x-4 space-x-reverse">
                        <button onclick="toggleSidebar()" class="bg-white text-blue-600 px-4 py-2 rounded-lg font-semibold hover:bg-blue-50 transition">
                            <i class="fas fa-sliders-h ml-1"></i>فیلترها
                        </button>
                        <button onclick="exportToExcel()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-semibold transition">
                            <i class="fas fa-file-excel ml-1"></i>خروجی Excel
                        </button>
                    </div>
                </div>
                
                <!-- نوار وضعیت -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white bg-opacity-20 p-4 rounded-xl backdrop-blur-sm">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-blue-100">تاریخ امروز</p>
                                <p id="todayDate" class="text-2xl font-bold">1404/05/15</p>
                            </div>
                            <i class="fas fa-calendar-day text-2xl text-white opacity-80"></i>
                        </div>
                    </div>
                    <div class="bg-white bg-opacity-20 p-4 rounded-xl backdrop-blur-sm">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-blue-100">مدت زمان کار</p>
                                <p id="workingTime" class="text-2xl font-bold">0 روز</p>
                            </div>
                            <i class="fas fa-clock text-2xl text-white opacity-80"></i>
                        </div>
                    </div>
                    <div class="bg-white bg-opacity-20 p-4 rounded-xl backdrop-blur-sm">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-blue-100">آخرین به‌روزرسانی</p>
                                <p id="lastUpdate" class="text-2xl font-bold">هم اکنون</p>
                            </div>
                            <i class="fas fa-sync-alt text-2xl text-white opacity-80"></i>
                        </div>
                    </div>
                    <div class="bg-white bg-opacity-20 p-4 rounded-xl backdrop-blur-sm">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-blue-100">نسخه سیستم</p>
                                <p class="text-2xl font-bold">2.1.0</p>
                            </div>
                            <i class="fas fa-code text-2xl text-white opacity-80"></i>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- کارت‌های آماری -->
        <div class="container mx-auto px-4 py-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-2xl shadow-lg border-t-4 border-t-blue-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-gray-600 mb-1">تعداد کل نمرات</p>
                            <p id="totalGradesCard" class="text-4xl font-bold text-gray-800">0</p>
                        </div>
                        <div class="bg-blue-100 text-blue-600 p-4 rounded-full">
                            <i class="fas fa-database text-2xl"></i>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">اصلی</span>
                            <span id="originalCount" class="font-semibold text-green-600">0</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">ویرایش شده</span>
                            <span id="editedCount" class="font-semibold text-yellow-600">0</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-2xl shadow-lg border-t-4 border-t-green-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-gray-600 mb-1">میانگین نمرات</p>
                            <p id="averageGrade" class="text-4xl font-bold text-gray-800">0.00</p>
                        </div>
                        <div class="bg-green-100 text-green-600 p-4 rounded-full">
                            <i class="fas fa-calculator text-2xl"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div id="averageBar" class="bg-green-500 h-2 rounded-full" style="width: 0%"></div>
                        </div>
                        <div class="flex justify-between text-xs mt-2">
                            <span>0</span>
                            <span>20</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-2xl shadow-lg border-t-4 border-t-red-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-gray-600 mb-1">کمترین نمره</p>
                            <p id="minGradeCard" class="text-4xl font-bold text-gray-800">0.00</p>
                        </div>
                        <div class="bg-red-100 text-red-600 p-4 rounded-full">
                            <i class="fas fa-arrow-down text-2xl"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-sm text-gray-600">درس:
                            <span id="minGradeLesson" class="font-semibold">---</span>
                        </div>
                        <div class="text-sm text-gray-600">تاریخ:
                            <span id="minGradeDate" class="font-semibold">---</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-2xl shadow-lg border-t-4 border-t-purple-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-gray-600 mb-1">بیشترین نمره</p>
                            <p id="maxGradeCard" class="text-4xl font-bold text-gray-800">0.00</p>
                        </div>
                        <div class="bg-purple-100 text-purple-600 p-4 rounded-full">
                            <i class="fas fa-arrow-up text-2xl"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-sm text-gray-600">درس:
                            <span id="maxGradeLesson" class="font-semibold">---</span>
                        </div>
                        <div class="text-sm text-gray-600">تاریخ:
                            <span id="maxGradeDate" class="font-semibold">---</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- نمودار آماری -->
            <div class="bg-white p-6 rounded-2xl shadow-lg mb-8">
                <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-chart-line ml-2 text-blue-500"></i>
                    نمودار توزیع نمرات
                </h3>
                <div class="h-64 flex items-end justify-between space-x-2 space-x-reverse" id="gradeDistributionChart">
                    <!-- نمودار به صورت داینامیک ایجاد می‌شود -->
                </div>
                <div class="mt-6 grid grid-cols-5 gap-4 text-center">
                    <div class="text-sm">
                        <div class="w-3 h-3 rounded-full bg-red-500 mx-auto mb-1"></div>
                        <span class="text-gray-600">مردود</span>
                    </div>
                    <div class="text-sm">
                        <div class="w-3 h-3 rounded-full bg-orange-500 mx-auto mb-1"></div>
                        <span class="text-gray-600">ضعیف</span>
                    </div>
                    <div class="text-sm">
                        <div class="w-3 h-3 rounded-full bg-yellow-500 mx-auto mb-1"></div>
                        <span class="text-gray-600">متوسط</span>
                    </div>
                    <div class="text-sm">
                        <div class="w-3 h-3 rounded-full bg-blue-500 mx-auto mb-1"></div>
                        <span class="text-gray-600">خوب</span>
                    </div>
                    <div class="text-sm">
                        <div class="w-3 h-3 rounded-full bg-green-500 mx-auto mb-1"></div>
                        <span class="text-gray-600">عالی</span>
                    </div>
                </div>
            </div>

            <!-- نوار ابزار -->
            <div class="bg-white p-4 rounded-2xl shadow-lg mb-6 flex flex-wrap justify-between items-center">
                <div class="flex items-center space-x-4 space-x-reverse mb-4 md:mb-0">
                    <button onclick="openNewGradeModal()" 
                            class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-semibold transition-all shadow-md flex items-center">
                        <i class="fas fa-plus ml-2"></i>
                        ثبت نمره جدید
                    </button>
                    <button onclick="openBulkAddModal()" 
                            class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-semibold transition-all shadow-md flex items-center">
                        <i class="fas fa-layer-group ml-2"></i>
                        ثبت گروهی
                    </button>
                    <button onclick="openImportModal()" 
                            class="bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-semibold transition-all shadow-md flex items-center">
                        <i class="fas fa-file-import ml-2"></i>
                        وارد کردن
                    </button>
                </div>
                
                <div class="flex items-center space-x-2 space-x-reverse">
                    <div class="relative">
                        <input type="text" id="searchInput" 
                               class="p-3 pr-12 border border-gray-300 rounded-xl w-64 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                               placeholder="جستجوی درس یا نمره...">
                        <i class="fas fa-search absolute right-4 top-4 text-gray-400"></i>
                    </div>
                    <select id="sortSelect" onchange="sortGrades()" 
                            class="p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="date-desc">تاریخ (جدیدترین)</option>
                        <option value="date-asc">تاریخ (قدیمی‌ترین)</option>
                        <option value="grade-desc">نمره (بیشترین)</option>
                        <option value="grade-asc">نمره (کمترین)</option>
                        <option value="name-asc">نام درس (الف-ی)</option>
                        <option value="name-desc">نام درس (ی-الف)</option>
                    </select>
                </div>
            </div>

            <!-- جدول اصلی نمرات -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-list-ol ml-2 text-green-500"></i>
                            لیست نمرات
                            <span id="tableCount" class="mr-2 text-sm bg-gray-100 px-3 py-1 rounded-full">0</span>
                        </h3>
                        <div class="text-sm text-gray-600">
                            <span id="tableInfo">در حال بارگذاری...</span>
                        </div>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-4 text-right font-semibold text-gray-700">ردیف</th>
                                <th class="p-4 text-right font-semibold text-gray-700">نام درس</th>
                                <th class="p-4 text-right font-semibold text-gray-700">نمره</th>
                                <th class="p-4 text-right font-semibold text-gray-700">تاریخ ثبت</th>
                                <th class="p-4 text-right font-semibold text-gray-700">تاریخ ویرایش</th>
                                <th class="p-4 text-right font-semibold text-gray-700">وضعیت</th>
                                <th class="p-4 text-right font-semibold text-gray-700">عملیات</th>
                            </tr>
                        </thead>
                        <tbody id="gradesTableBody">
                            <!-- داده‌ها به صورت داینامیک لود می‌شوند -->
                        </tbody>
                    </table>
                    
                    <!-- حالت خالی -->
                    <div id="emptyState" class="hidden p-12 text-center">
                        <div class="text-gray-300 mb-6">
                            <i class="fas fa-clipboard-list text-6xl"></i>
                        </div>
                        <h4 class="text-xl font-semibold text-gray-500 mb-2">هیچ نمره‌ای یافت نشد</h4>
                        <p class="text-gray-400 mb-6">با دکمه "ثبت نمره جدید" اولین نمره را ثبت کنید</p>
                        <button onclick="openNewGradeModal()" 
                                class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium">
                            <i class="fas fa-plus ml-2"></i>ثبت اولین نمره
                        </button>
                    </div>
                </div>
                
                <!-- پاورقی جدول -->
                <div class="p-4 border-t border-gray-200 bg-gray-50 flex justify-between items-center">
                    <div class="text-sm text-gray-600">
                        نمایش 
                        <span id="currentPage">1</span> از 
                        <span id="totalPages">1</span>
                    </div>
                    <div class="flex space-x-2 space-x-reverse">
                        <button onclick="changePage(-1)" 
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition disabled:opacity-50"
                                id="prevPageBtn" disabled>
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <button onclick="changePage(1)" 
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition disabled:opacity-50"
                                id="nextPageBtn" disabled>
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- دکمه شناور برای دسترسی سریع -->
    <button onclick="scrollToTop()" 
            class="fixed bottom-6 left-6 w-14 h-14 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 transition-all z-50 floating-btn">
        <i class="fas fa-chevron-up text-xl"></i>
    </button>

    <!-- مودال ثبت/ویرایش نمره -->
    <div id="gradeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg modal-enter">
            <div class="p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-800" id="modalTitle">ثبت نمره جدید</h3>
                    <button onclick="closeGradeModal()" 
                            class="text-gray-500 hover:text-gray-700 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-lg font-medium text-gray-700 mb-2">
                            <i class="fas fa-book ml-2 text-blue-500"></i>
                            نام درس
                        </label>
                        <input type="text" id="lessonName" 
                               class="w-full p-4 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all"
                               placeholder="مثال: ریاضی، فیزیک، شیمی...">
                        <div class="mt-2 text-sm text-gray-500">حداقل 2 حرف و حداکثر 50 حرف</div>
                    </div>
                    
                    <div>
                        <label class="block text-lg font-medium text-gray-700 mb-2">
                            <i class="fas fa-star ml-2 text-yellow-500"></i>
                            نمره (0 تا 20)
                        </label>
                        <div class="relative">
                            <input type="number" id="gradeValue" min="0" max="20" step="0.01"
                                   class="w-full p-4 text-2xl font-bold border-2 border-gray-300 rounded-xl text-center focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all"
                                   placeholder="18.75">
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-percentage"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex justify-between mb-2">
                                <span class="text-sm text-gray-600">میزان نمره</span>
                                <span id="gradeCategory" class="font-semibold">---</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div id="gradeProgress" class="h-3 rounded-full" style="width: 0%"></div>
                            </div>
                            <div class="flex justify-between text-xs mt-1 text-gray-500">
                                <span>0</span>
                                <span>10</span>
                                <span>15</span>
                                <span>18</span>
                                <span>20</span>
                            </div>
                        </div>
                    </div>
                    
                    <div id="originalDateField">
                        <label class="block text-lg font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar ml-2 text-green-500"></i>
                            تاریخ ثبت
                        </label>
                        <input type="text" id="gradeDate" 
                               class="w-full p-4 border-2 border-gray-300 rounded-xl text-center text-lg datepicker-input"
                               placeholder="1404/05/15">
                    </div>
                    
                    <div id="editNotesField" class="hidden">
                        <label class="block text-lg font-medium text-gray-700 mb-2">
                            <i class="fas fa-sticky-note ml-2 text-purple-500"></i>
                            یادداشت ویرایش (اختیاری)
                        </label>
                        <textarea id="editNotes" 
                                  class="w-full p-4 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all"
                                  rows="3" placeholder="علت ویرایش نمره را بنویسید..."></textarea>
                    </div>
                </div>
                
                <div class="flex justify-between mt-10">
                    <button onclick="closeGradeModal()" 
                            class="px-8 py-4 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition-all duration-300 flex items-center">
                        <i class="fas fa-times ml-2"></i>
                        انصراف
                    </button>
                    <button onclick="saveGrade()" 
                            class="px-8 py-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-300 shadow-lg flex items-center">
                        <i class="fas fa-save ml-2"></i>
                        ذخیره نمره
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- مودال حذف -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl w-96 p-8 modal-enter">
            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-3xl text-red-600"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">تأیید حذف</h3>
                <p class="text-gray-600" id="deleteMessage">آیا از حذف این نمره اطمینان دارید؟</p>
            </div>
            <div class="flex justify-center space-x-4 space-x-reverse">
                <button onclick="closeDeleteModal()" 
                        class="px-8 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition">
                    لغو
                </button>
                <button onclick="confirmDelete()" 
                        class="px-8 py-3 bg-red-500 text-white rounded-xl font-semibold hover:bg-red-600 transition">
                    حذف نمره
                </button>
            </div>
        </div>
    </div>

    <!-- مودال جزئیات -->
    <div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl w-full max-w-2xl modal-enter max-h-[90vh] overflow-y-auto">
            <div class="p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-info-circle ml-2 text-blue-500"></i>
                        جزئیات نمره
                    </h3>
                    <button onclick="closeDetailModal()" 
                            class="text-gray-500 hover:text-gray-700 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div id="detailContent">
                    <!-- جزئیات به صورت داینامیک لود می‌شوند -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // =================== متغیرهای اصلی سیستم ===================
        let grades = [];
        let currentFilter = {
            startDate: null,
            endDate: null,
            minGrade: 0,
            maxGrade: 20,
            status: ['original', 'edited'],
            searchQuery: ''
        };
        let editingGradeId = null;
        let deletingGradeId = null;
        let currentPage = 1;
        const itemsPerPage = 10;
        let filteredGrades = [];
        let sortOrder = 'date-desc';
        let editHistory = {};

        // =================== توابع کمکی ===================
        function getTodayDate() {
            const today = new PersianDate();
            return today.format('YYYY/MM/DD');
        }

        function getCurrentTime() {
            const now = new PersianDate();
            return now.format('HH:mm:ss');
        }

        function dateToNumber(dateStr) {
            if (!dateStr) return 0;
            const parts = dateStr.split('/');
            return parseInt(parts[0]) * 10000 + parseInt(parts[1]) * 100 + parseInt(parts[2]);
        }

        function validateJalaliDate(dateStr) {
            if (!dateStr) return false;
            const pattern = /^(\d{4})\/(0[1-9]|1[0-2])\/(0[1-9]|[12]\d|3[01])$/;
            return pattern.test(dateStr);
        }

        function getGradeCategory(grade) {
            if (grade >= 18) return { name: 'عالی', color: 'green', class: 'grade-excellent' };
            if (grade >= 15) return { name: 'خوب', color: 'blue', class: 'grade-good' };
            if (grade >= 12) return { name: 'متوسط', color: 'yellow', class: 'grade-average' };
            if (grade >= 10) return { name: 'ضعیف', color: 'orange', class: 'grade-weak' };
            return { name: 'مردود', color: 'red', class: 'grade-poor' };
        }

        function showNotification(message, type = 'info', duration = 3000) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 left-4 right-4 md:left-auto md:right-4 md:w-96 p-4 rounded-xl shadow-lg text-white font-medium z-50 transform transition-all duration-500 translate-y-[-100px]`;
            
            let bgColor = 'bg-blue-500';
            let icon = 'fa-info-circle';
            
            if (type === 'success') {
                bgColor = 'bg-green-500';
                icon = 'fa-check-circle';
            } else if (type === 'warning') {
                bgColor = 'bg-yellow-500';
                icon = 'fa-exclamation-triangle';
            } else if (type === 'error') {
                bgColor = 'bg-red-500';
                icon = 'fa-times-circle';
            }
            
            notification.className += ` ${bgColor}`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${icon} ml-2 text-xl"></i>
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="mr-auto text-white opacity-80 hover:opacity-100">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // انیمیشن ورود
            setTimeout(() => {
                notification.style.transform = 'translateY(0)';
            }, 10);
            
            // حذف خودکار
            setTimeout(() => {
                notification.style.transform = 'translateY(-100px)';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 500);
            }, duration);
        }

        // =================== مدیریت داده‌ها ===================
        function loadGrades() {
            const saved = localStorage.getItem('gradeSystemData');
            const savedHistory = localStorage.getItem('gradeEditHistory');
            
            if (saved) {
                grades = JSON.parse(saved);
            } else {
                // داده‌های اولیه نمونه
                grades = [
                    { 
                        id: 1, 
                        lesson: "ریاضی", 
                        grade: 18.5, 
                        date: "1404/05/05", 
                        editedDate: null, 
                        editedCount: 0,
                        originalGrade: 18.5,
                        createdAt: new Date().toISOString()
                    },
                    { 
                        id: 2, 
                        lesson: "فیزیک", 
                        grade: 16, 
                        date: "1404/05/06", 
                        editedDate: "1404/05/07", 
                        editedCount: 1,
                        originalGrade: 15,
                        createdAt: new Date().toISOString()
                    },
                    { 
                        id: 3, 
                        lesson: "شیمی", 
                        grade: 19, 
                        date: "1404/05/06", 
                        editedDate: null, 
                        editedCount: 0,
                        originalGrade: 19,
                        createdAt: new Date().toISOString()
                    },
                    { 
                        id: 4, 
                        lesson: "زبان انگلیسی", 
                        grade: 15.25, 
                        date: "1404/05/08", 
                        editedDate: "1404/05/10", 
                        editedCount: 2,
                        originalGrade: 14,
                        createdAt: new Date().toISOString()
                    },
                    { 
                        id: 5, 
                        lesson: "تاریخ", 
                        grade: 17.75, 
                        date: "1404/05/10", 
                        editedDate: null, 
                        editedCount: 0,
                        originalGrade: 17.75,
                        createdAt: new Date().toISOString()
                    },
                    { 
                        id: 6, 
                        lesson: "ادبیات فارسی", 
                        grade: 14.5, 
                        date: "1404/05/12", 
                        editedDate: "1404/05/14", 
                        editedCount: 1,
                        originalGrade: 13.5,
                        createdAt: new Date().toISOString()
                    },
                    { 
                        id: 7, 
                        lesson: "زیست‌شناسی", 
                        grade: 18, 
                        date: "1404/05/15", 
                        editedDate: null, 
                        editedCount: 0,
                        originalGrade: 18,
                        createdAt: new Date().toISOString()
                    },
                    { 
                        id: 8, 
                        lesson: "هندسه", 
                        grade: 9.75, 
                        date: "1404/05/03", 
                        editedDate: "1404/05/04", 
                        editedCount: 1,
                        originalGrade: 8.5,
                        createdAt: new Date().toISOString()
                    },
                    { 
                        id: 9, 
                        lesson: "عربی", 
                        grade: 11.5, 
                        date: "1404/05/07", 
                        editedDate: null, 
                        editedCount: 0,
                        originalGrade: 11.5,
                        createdAt: new Date().toISOString()
                    },
                    { 
                        id: 10, 
                        lesson: "دینی", 
                        grade: 19.75, 
                        date: "1404/05/11", 
                        editedDate: "1404/05/12", 
                        editedCount: 1,
                        originalGrade: 19.5,
                        createdAt: new Date().toISOString()
                    }
                ];
                saveGrades();
            }
            
            if (savedHistory) {
                editHistory = JSON.parse(savedHistory);
            }
            
            updateDisplay();
            updateSidebarInfo();
            showNotification('داده‌ها با موفقیت بارگذاری شدند', 'success');
        }

        function saveGrades() {
            localStorage.setItem('gradeSystemData', JSON.stringify(grades));
            localStorage.setItem('gradeEditHistory', JSON.stringify(editHistory));
            document.getElementById('lastUpdate').textContent = getCurrentTime();
        }

        // =================== فیلتر و جستجو ===================
        function applyDateFilter() {
            const start = document.getElementById('filterStartDate').value;
            const end = document.getElementById('filterEndDate').value;
            
            if (start && !validateJalaliDate(start)) {
                showNotification('تاریخ شروع معتبر نیست', 'error');
                return;
            }
            
            if (end && !validateJalaliDate(end)) {
                showNotification('تاریخ پایان معتبر نیست', 'error');
                return;
            }
            
            if (start && end && dateToNumber(start) > dateToNumber(end)) {
                showNotification('تاریخ شروع باید قبل از تاریخ پایان باشد', 'error');
                return;
            }
            
            currentFilter.startDate = start || null;
            currentFilter.endDate = end || null;
            
            // به‌روزرسانی فیلترهای وضعیت
            updateStatusFilter();
            updateGradeFilter();
            
            applyFilters();
            showNotification('فیلتر تاریخ اعمال شد', 'success');
        }

        function clearDateFilter() {
            document.getElementById('filterStartDate').value = '';
            document.getElementById('filterEndDate').value = '';
            currentFilter.startDate = null;
            currentFilter.endDate = null;
            
            applyFilters();
            showNotification('فیلتر تاریخ حذف شد', 'info');
        }

        function updateStatusFilter() {
            const statusOriginal = document.getElementById('filterStatusOriginal').checked;
            const statusEdited = document.getElementById('filterStatusEdited').checked;
            
            currentFilter.status = [];
            if (statusOriginal) currentFilter.status.push('original');
            if (statusEdited) currentFilter.status.push('edited');
            
            if (currentFilter.status.length === 0) {
                document.getElementById('filterStatusOriginal').checked = true;
                document.getElementById('filterStatusEdited').checked = true;
                currentFilter.status = ['original', 'edited'];
            }
        }

        function updateGradeFilter() {
            const gradeRanges = [];
            
            if (document.getElementById('filterGradeExcellent').checked) gradeRanges.push([18, 20]);
            if (document.getElementById('filterGradeGood').checked) gradeRanges.push([15, 17.99]);
            if (document.getElementById('filterGradeAverage').checked) gradeRanges.push([12, 14.99]);
            if (document.getElementById('filterGradeWeak').checked) gradeRanges.push([10, 11.99]);
            if (document.getElementById('filterGradePoor').checked) gradeRanges.push([0, 9.99]);
            
            currentFilter.gradeRanges = gradeRanges;
        }

        function applyFilters() {
            // اعمال فیلترهای مختلف
            filteredGrades = grades.filter(grade => {
                // فیلتر تاریخ
                if (currentFilter.startDate && currentFilter.endDate) {
                    const gradeDateNum = dateToNumber(grade.date);
                    const startNum = dateToNumber(currentFilter.startDate);
                    const endNum = dateToNumber(currentFilter.endDate);
                    
                    if (gradeDateNum < startNum || gradeDateNum > endNum) {
                        return false;
                    }
                }
                
                // فیلتر وضعیت
                const status = grade.editedDate ? 'edited' : 'original';
                if (!currentFilter.status.includes(status)) {
                    return false;
                }
                
                // فیلتر بازه نمره
                if (currentFilter.gradeRanges && currentFilter.gradeRanges.length > 0) {
                    let inRange = false;
                    for (const [min, max] of currentFilter.gradeRanges) {
                        if (grade.grade >= min && grade.grade <= max) {
                            inRange = true;
                            break;
                        }
                    }
                    if (!inRange) return false;
                }
                
                // فیلتر جستجو
                if (currentFilter.searchQuery) {
                    const query = currentFilter.searchQuery.toLowerCase();
                    const lessonMatch = grade.lesson.toLowerCase().includes(query);
                    const gradeMatch = grade.grade.toString().includes(query);
                    const dateMatch = grade.date.includes(query);
                    
                    if (!lessonMatch && !gradeMatch && !dateMatch) {
                        return false;
                    }
                }
                
                return true;
            });
            
            // مرتب‌سازی
            sortFilteredGrades();
            
            // به‌روزرسانی نمایش
            currentPage = 1;
            updateTable();
            updateStats();
            updateChart();
            updateSidebarInfo();
        }

        function sortGrades() {
            sortOrder = document.getElementById('sortSelect').value;
            sortFilteredGrades();
            updateTable();
        }

        function sortFilteredGrades() {
            filteredGrades.sort((a, b) => {
                switch (sortOrder) {
                    case 'date-desc':
                        return dateToNumber(b.date) - dateToNumber(a.date);
                    case 'date-asc':
                        return dateToNumber(a.date) - dateToNumber(b.date);
                    case 'grade-desc':
                        return b.grade - a.grade;
                    case 'grade-asc':
                        return a.grade - b.grade;
                    case 'name-asc':
                        return a.lesson.localeCompare(b.lesson, 'fa');
                    case 'name-desc':
                        return b.lesson.localeCompare(a.lesson, 'fa');
                    default:
                        return 0;
                }
            });
        }

        // =================== مدیریت صفحه‌بندی ===================
        function changePage(direction) {
            const newPage = currentPage + direction;
            const totalPages = Math.ceil(filteredGrades.length / itemsPerPage);
            
            if (newPage < 1 || newPage > totalPages) return;
            
            currentPage = newPage;
            updateTable();
        }

        // =================== به‌روزرسانی نمایش ===================
        function updateDisplay() {
            applyFilters();
            updateDateDisplay();
        }

        function updateDateDisplay() {
            document.getElementById('todayDate').textContent = getTodayDate();
            
            // محاسبه مدت زمان کار
            const firstRun = localStorage.getItem('systemFirstRun');
            if (firstRun) {
                const startDate = new Date(firstRun);
                const today = new Date();
                const diffTime = Math.abs(today - startDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                document.getElementById('workingTime').textContent = diffDays + ' روز';
            } else {
                localStorage.setItem('systemFirstRun', new Date().toISOString());
                document.getElementById('workingTime').textContent = '1 روز';
            }
        }

        function updateTable() {
            const tbody = document.getElementById('gradesTableBody');
            const emptyState = document.getElementById('emptyState');
            const totalPages = Math.ceil(filteredGrades.length / itemsPerPage);
            
            if (filteredGrades.length === 0) {
                tbody.innerHTML = '';
                emptyState.classList.remove('hidden');
                document.getElementById('tableCount').textContent = '0';
                document.getElementById('tableInfo').textContent = 'هیچ نمره‌ای یافت نشد';
                return;
            }
            
            emptyState.classList.add('hidden');
            
            // محاسبه محدوده ردیف‌های این صفحه
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = Math.min(startIndex + itemsPerPage, filteredGrades.length);
            const pageGrades = filteredGrades.slice(startIndex, endIndex);
            
            let html = '';
            pageGrades.forEach((grade, index) => {
                const actualIndex = startIndex + index + 1;
                const category = getGradeCategory(grade.grade);
                const isEdited = grade.editedDate !== null;
                const statusClass = isEdited ? 'status-edited' : 'status-original';
                
                html += `
                <tr class="${statusClass} hover:bg-gray-50 transition-colors">
                    <td class="p-4 text-center font-bold text-gray-700">${actualIndex}</td>
                    <td class="p-4">
                        <div class="font-semibold text-gray-900 text-lg">${grade.lesson}</div>
                        ${isEdited ? 
                            `<div class="text-xs text-gray-500 mt-1">ویرایش شده: ${grade.editedCount} بار</div>` : 
                            ''
                        }
                    </td>
                    <td class="p-4">
                        <div class="flex flex-col items-center">
                            <span class="inline-block px-4 py-2 rounded-lg text-white font-bold text-xl ${category.class}">
                                ${grade.grade.toFixed(2)}
                            </span>
                            <div class="text-xs text-gray-500 mt-1">${category.name}</div>
                        </div>
                    </td>
                    <td class="p-4">
                        <div class="flex flex-col items-center">
                            <span class="font-medium text-gray-800">${grade.date}</span>
                            <span class="text-xs text-gray-500">ثبت اولیه</span>
                        </div>
                    </td>
                    <td class="p-4">
                        <div class="flex flex-col items-center">
                            <span class="font-medium ${isEdited ? 'text-yellow-700' : 'text-gray-400'}">
                                ${grade.editedDate || '---'}
                            </span>
                            <span class="text-xs text-gray-500">
                                ${isEdited ? 'آخرین ویرایش' : 'بدون ویرایش'}
                            </span>
                        </div>
                    </td>
                    <td class="p-4">
                        <div class="flex flex-col items-center">
                            <div class="flex items-center mb-1">
                                <span class="w-3 h-3 rounded-full ${isEdited ? 'bg-yellow-500' : 'bg-green-500'} ml-1"></span>
                                <span class="font-medium ${isEdited ? 'text-yellow-700' : 'text-green-700'}">
                                    ${isEdited ? 'ویرایش شده' : 'اصلی'}
                                </span>
                            </div>
                            ${isEdited ? 
                                `<div class="text-xs text-gray-600">
                                    <i class="fas fa-history ml-1"></i>
                                    از ${grade.originalGrade.toFixed(2)}
                                </div>` : 
                                ''
                            }
                        </div>
                    </td>
                    <td class="p-4">
                        <div class="flex flex-col space-y-2">
                            <button onclick="viewGradeDetails(${grade.id})" 
                                    class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-4 py-2 rounded-lg transition flex items-center justify-center">
                                <i class="fas fa-eye ml-1"></i>مشاهده
                            </button>
                            <div class="flex space-x-2 space-x-reverse">
                                <button onclick="editGrade(${grade.id})" 
                                        class="flex-1 bg-yellow-100 text-yellow-700 hover:bg-yellow-200 px-3 py-2 rounded-lg transition flex items-center justify-center">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="openDeleteModal(${grade.id})" 
                                        class="flex-1 bg-red-100 text-red-700 hover:bg-red-200 px-3 py-2 rounded-lg transition flex items-center justify-center">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>`;
            });
            
            tbody.innerHTML = html;
            
            // به‌روزرسانی اطلاعات صفحه‌بندی
            document.getElementById('tableCount').textContent = filteredGrades.length;
            document.getElementById('currentPage').textContent = currentPage;
            document.getElementById('totalPages').textContent = totalPages;
            document.getElementById('tableInfo').textContent = 
                `نمایش ${startIndex + 1} تا ${endIndex} از ${filteredGrades.length} نمره`;
            
            // فعال/غیرفعال کردن دکمه‌های صفحه‌بندی
            document.getElementById('prevPageBtn').disabled = currentPage === 1;
            document.getElementById('nextPageBtn').disabled = currentPage === totalPages;
        }

        function updateStats() {
            if (filteredGrades.length === 0) {
                document.getElementById('totalGradesCard').textContent = '0';
                document.getElementById('averageGrade').textContent = '0.00';
                document.getElementById('minGradeCard').textContent = '0.00';
                document.getElementById('maxGradeCard').textContent = '0.00';
                document.getElementById('originalCount').textContent = '0';
                document.getElementById('editedCount').textContent = '0';
                document.getElementById('minGradeLesson').textContent = '---';
                document.getElementById('minGradeDate').textContent = '---';
                document.getElementById('maxGradeLesson').textContent = '---';
                document.getElementById('maxGradeDate').textContent = '---';
                document.getElementById('averageBar').style.width = '0%';
                return;
            }
            
            // محاسبات آماری
            const gradesOnly = filteredGrades.map(g => g.grade);
            const total = filteredGrades.length;
            const originalCount = filteredGrades.filter(g => !g.editedDate).length;
            const editedCount = filteredGrades.filter(g => g.editedDate).length;
            const minGrade = Math.min(...gradesOnly);
            const maxGrade = Math.max(...gradesOnly);
            const averageGrade = gradesOnly.reduce((a, b) => a + b, 0) / total;
            
            // پیدا کردن درس‌های با کمترین و بیشترین نمره
            const minGradeItem = filteredGrades.find(g => g.grade === minGrade);
            const maxGradeItem = filteredGrades.find(g => g.grade === maxGrade);
            
            // به‌روزرسانی کارت‌ها
            document.getElementById('totalGradesCard').textContent = total;
            document.getElementById('averageGrade').textContent = averageGrade.toFixed(2);
            document.getElementById('minGradeCard').textContent = minGrade.toFixed(2);
            document.getElementById('maxGradeCard').textContent = maxGrade.toFixed(2);
            document.getElementById('originalCount').textContent = originalCount;
            document.getElementById('editedCount').textContent = editedCount;
            
            if (minGradeItem) {
                document.getElementById('minGradeLesson').textContent = minGradeItem.lesson;
                document.getElementById('minGradeDate').textContent = minGradeItem.date;
            }
            
            if (maxGradeItem) {
                document.getElementById('maxGradeLesson').textContent = maxGradeItem.lesson;
                document.getElementById('maxGradeDate').textContent = maxGradeItem.date;
            }
            
            // به‌روزرسانی نوار میانگین
            const averagePercent = (averageGrade / 20) * 100;
            document.getElementById('averageBar').style.width = `${averagePercent}%`;
        }

        function updateChart() {
            const chartContainer = document.getElementById('gradeDistributionChart');
            
            if (filteredGrades.length === 0) {
                chartContainer.innerHTML = `
                    <div class="flex items-center justify-center w-full h-full text-gray-400">
                        <i class="fas fa-chart-bar text-4xl ml-2"></i>
                        <span>داده‌ای برای نمایش وجود ندارد</span>
                    </div>
                `;
                return;
            }
            
            // گروه‌بندی نمرات
            const categories = [
                { name: 'مردود', range: [0, 9.99], color: 'bg-red-500', count: 0 },
                { name: 'ضعیف', range: [10, 11.99], color: 'bg-orange-500', count: 0 },
                { name: 'متوسط', range: [12, 14.99], color: 'bg-yellow-500', count: 0 },
                { name: 'خوب', range: [15, 17.99], color: 'bg-blue-500', count: 0 },
                { name: 'عالی', range: [18, 20], color: 'bg-green-500', count: 0 }
            ];
            
            // شمارش نمرات در هر گروه
            filteredGrades.forEach(grade => {
                for (const category of categories) {
                    if (grade.grade >= category.range[0] && grade.grade <= category.range[1]) {
                        category.count++;
                        break;
                    }
                }
            });
            
            // پیدا کردن بیشترین تعداد برای مقیاس نمودار
            const maxCount = Math.max(...categories.map(c => c.count));
            
            // ایجاد نمودار
            let html = '';
            categories.forEach(category => {
                const heightPercent = maxCount > 0 ? (category.count / maxCount) * 100 : 0;
                const percentage = filteredGrades.length > 0 ? 
                    ((category.count / filteredGrades.length) * 100).toFixed(1) : 0;
                
                html += `
                <div class="flex flex-col items-center flex-1">
                    <div class="relative mb-2 w-full flex justify-center">
                        <div class="chart-bar ${category.color} rounded-t-lg w-3/4" 
                             style="height: ${heightPercent}%; max-height: 100%;"
                             title="${category.name}: ${category.count} نمره (${percentage}%)">
                        </div>
                        <div class="absolute bottom-full mb-2 text-sm font-semibold text-gray-700 whitespace-nowrap">
                            ${category.count}
                        </div>
                    </div>
                    <div class="text-xs text-gray-600 text-center">${category.name}</div>
                    <div class="text-xs text-gray-500">${percentage}%</div>
                </div>`;
            });
            
            chartContainer.innerHTML = html;
        }

        function updateSidebarInfo() {
            document.getElementById('sidebarFilterCount').textContent = filteredGrades.length;
            document.getElementById('sidebarTotalCount').textContent = grades.length;
            
            if (currentFilter.startDate && currentFilter.endDate) {
                document.getElementById('sidebarDateRange').textContent = 
                    `${currentFilter.startDate} تا ${currentFilter.endDate}`;
            } else {
                document.getElementById('sidebarDateRange').textContent = 'همه تاریخ‌ها';
            }
        }

        // =================== مدیریت مودال‌ها ===================
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.overlay');
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        function openNewGradeModal() {
            editingGradeId = null;
            document.getElementById('modalTitle').textContent = 'ثبت نمره جدید';
            document.getElementById('lessonName').value = '';
            document.getElementById('gradeValue').value = '';
            document.getElementById('gradeDate').value = getTodayDate();
            document.getElementById('editNotes').value = '';
            document.getElementById('originalDateField').classList.remove('hidden');
            document.getElementById('editNotesField').classList.add('hidden');
            
            // تنظیم پیشرفت نمره
            updateGradeProgress();
            
            document.getElementById('gradeModal').classList.remove('hidden');
            document.getElementById('lessonName').focus();
        }

        function editGrade(id) {
            const grade = grades.find(g => g.id === id);
            if (!grade) return;
            
            editingGradeId = id;
            document.getElementById('modalTitle').textContent = 'ویرایش نمره';
            document.getElementById('lessonName').value = grade.lesson;
            document.getElementById('gradeValue').value = grade.grade;
            document.getElementById('gradeDate').value = grade.date;
            document.getElementById('editNotes').value = '';
            document.getElementById('originalDateField').classList.add('hidden');
            document.getElementById('editNotesField').classList.remove('hidden');
            
            // تنظیم پیشرفت نمره
            updateGradeProgress();
            
            document.getElementById('gradeModal').classList.remove('hidden');
            document.getElementById('gradeValue').focus();
        }

        function closeGradeModal() {
            document.getElementById('gradeModal').classList.add('hidden');
            editingGradeId = null;
        }

        function saveGrade() {
            const lesson = document.getElementById('lessonName').value.trim();
            const gradeValue = parseFloat(document.getElementById('gradeValue').value);
            const date = document.getElementById('gradeDate').value;
            const notes = document.getElementById('editNotes').value.trim();
            
            // اعتبارسنجی
            if (!lesson || lesson.length < 2 || lesson.length > 50) {
                showNotification('نام درس باید بین ۲ تا ۵۰ حرف باشد', 'error');
                return;
            }
            
            if (isNaN(gradeValue) || gradeValue < 0 || gradeValue > 20) {
                showNotification('لطفاً نمره معتبر بین ۰ تا ۲۰ وارد کنید', 'error');
                return;
            }
            
            if (!date || !validateJalaliDate(date)) {
                showNotification('لطفاً تاریخ شمسی معتبر وارد کنید', 'error');
                return;
            }
            
            if (editingGradeId === null) {
                // ثبت جدید
                const newGrade = {
                    id: Date.now(),
                    lesson: lesson,
                    grade: gradeValue,
                    date: date,
                    editedDate: null,
                    editedCount: 0,
                    originalGrade: gradeValue,
                    createdAt: new Date().toISOString(),
                    notes: ''
                };
                grades.push(newGrade);
                showNotification('نمره جدید با موفقیت ثبت شد', 'success');
            } else {
                // ویرایش
                const gradeIndex = grades.findIndex(g => g.id === editingGradeId);
                if (gradeIndex !== -1) {
                    const oldGrade = grades[gradeIndex].grade;
                    
                    // ذخیره تاریخچه ویرایش
                    if (!editHistory[editingGradeId]) {
                        editHistory[editingGradeId] = [];
                    }
                    
                    editHistory[editingGradeId].push({
                        date: new Date().toISOString(),
                        persianDate: getTodayDate(),
                        oldGrade: oldGrade,
                        newGrade: gradeValue,
                        notes: notes
                    });
                    
                    // به‌روزرسانی نمره
                    grades[gradeIndex].lesson = lesson;
                    grades[gradeIndex].grade = gradeValue;
                    grades[gradeIndex].editedDate = getTodayDate();
                    grades[gradeIndex].editedCount = (grades[gradeIndex].editedCount || 0) + 1;
                    if (!grades[gradeIndex].originalGrade) {
                        grades[gradeIndex].originalGrade = oldGrade;
                    }
                    
                    showNotification('نمره با موفقیت ویرایش شد', 'success');
                }
            }
            
            saveGrades();
            closeGradeModal();
            updateDisplay();
        }

        function viewGradeDetails(id) {
            const grade = grades.find(g => g.id === id);
            if (!grade) return;
            
            const category = getGradeCategory(grade.grade);
            const history = editHistory[id] || [];
            
            let historyHtml = '';
            if (history.length > 0) {
                historyHtml = `
                <div class="mt-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-3">
                        <i class="fas fa-history ml-2 text-purple-500"></i>
                        تاریخچه ویرایش‌ها
                    </h4>
                    <div class="space-y-3">
                        ${history.map((h, idx) => `
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <div class="flex justify-between items-start mb-2">
                                <span class="font-medium text-gray-700">ویرایش ${idx + 1}</span>
                                <span class="text-sm text-gray-500">${h.persianDate}</span>
                            </div>
                            <div class="flex items-center space-x-4 space-x-reverse text-sm">
                                <span class="text-red-600">${h.oldGrade.toFixed(2)}</span>
                                <i class="fas fa-arrow-left text-gray-400"></i>
                                <span class="text-green-600 font-bold">${h.newGrade.toFixed(2)}</span>
                                <span class="text-blue-600">(${(h.newGrade - h.oldGrade).toFixed(2)})</span>
                            </div>
                            ${h.notes ? `
                            <div class="mt-2 pt-2 border-t border-gray-200 text-gray-600">
                                <i class="fas fa-sticky-note ml-2"></i>
                                ${h.notes}
                            </div>` : ''}
                        </div>
                        `).join('')}
                    </div>
                </div>`;
            }
            
            const content = `
            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div class="bg-blue-50 p-4 rounded-xl">
                        <div class="text-sm text-gray-600 mb-1">نام درس</div>
                        <div class="text-xl font-bold text-gray-800">${grade.lesson}</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-xl">
                        <div class="text-sm text-gray-600 mb-1">نمره فعلی</div>
                        <div class="text-2xl font-bold ${category.color}-600">${grade.grade.toFixed(2)}</div>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-xl">
                        <div class="text-sm text-gray-600 mb-1">تاریخ ثبت اولیه</div>
                        <div class="text-lg font-medium text-gray-800">${grade.date}</div>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-xl">
                        <div class="text-sm text-gray-600 mb-1">وضعیت</div>
                        <div class="text-lg font-medium ${grade.editedDate ? 'text-yellow-700' : 'text-green-700'}">
                            ${grade.editedDate ? 'ویرایش شده' : 'اصلی'}
                        </div>
                    </div>
                </div>
                
                ${grade.editedDate ? `
                <div class="grid grid-cols-2 gap-6">
                    <div class="bg-purple-50 p-4 rounded-xl">
                        <div class="text-sm text-gray-600 mb-1">تاریخ آخرین ویرایش</div>
                        <div class="text-lg font-medium text-purple-700">${grade.editedDate}</div>
                    </div>
                    <div class="bg-red-50 p-4 rounded-xl">
                        <div class="text-sm text-gray-600 mb-1">نمره اولیه</div>
                        <div class="text-lg font-medium text-red-700">${grade.originalGrade.toFixed(2)}</div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 rounded-xl">
                    <div class="text-sm text-gray-600 mb-1">تعداد ویرایش‌ها</div>
                    <div class="text-lg font-medium text-blue-700">${grade.editedCount} بار</div>
                </div>
                ` : ''}
                
                ${historyHtml}
                
                <div class="mt-8 pt-6 border-t border-gray-200 flex justify-center space-x-4 space-x-reverse">
                    <button onclick="editGrade(${grade.id}); closeDetailModal();" 
                            class="px-6 py-3 bg-yellow-500 text-white rounded-xl font-semibold hover:bg-yellow-600 transition">
                        <i class="fas fa-edit ml-2"></i>ویرایش این نمره
                    </button>
                    <button onclick="closeDetailModal()" 
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition">
                        بستن
                    </button>
                </div>
            </div>`;
            
            document.getElementById('detailContent').innerHTML = content;
            document.getElementById('detailModal').classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        function openDeleteModal(id) {
            const grade = grades.find(g => g.id === id);
            if (!grade) return;
            
            deletingGradeId = id;
            document.getElementById('deleteMessage').innerHTML = `
                آیا از حذف نمره <strong>${grade.lesson}</strong> با نمره <strong>${grade.grade.toFixed(2)}</strong> اطمینان دارید؟
                <br><span class="text-sm text-gray-600">این عمل قابل بازگشت نیست.</span>
            `;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            deletingGradeId = null;
        }

        function confirmDelete() {
            if (!deletingGradeId) return;
            
            const gradeIndex = grades.findIndex(g => g.id === deletingGradeId);
            if (gradeIndex !== -1) {
                const deletedGrade = grades[gradeIndex];
                grades.splice(gradeIndex, 1);
                saveGrades();
                updateDisplay();
                showNotification(`نمره ${deletedGrade.lesson} حذف شد`, 'warning');
            }
            
            closeDeleteModal();
        }

        // =================== توابع کمکی UI ===================
        function updateGradeProgress() {
            const gradeInput = document.getElementById('gradeValue');
            const progressBar = document.getElementById('gradeProgress');
            const categorySpan = document.getElementById('gradeCategory');
            
            gradeInput.addEventListener('input', function() {
                const value = parseFloat(this.value) || 0;
                const percent = (value / 20) * 100;
                
                progressBar.style.width = `${percent}%`;
                
                const category = getGradeCategory(value);
                progressBar.className = `h-3 rounded-full bg-${category.color}-500`;
                categorySpan.textContent = category.name;
                categorySpan.className = `font-semibold text-${category.color}-600`;
            });
        }

        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function exportToExcel() {
            if (filteredGrades.length === 0) {
                showNotification('داده‌ای برای خروجی وجود ندارد', 'warning');
                return;
            }
            
            // ایجاد داده‌های CSV
            let csv = 'نام درس,نمره,تاریخ ثبت,تاریخ ویرایش,وضعیت\n';
            filteredGrades.forEach(grade => {
                csv += `"${grade.lesson}",${grade.grade},${grade.date},${grade.editedDate || ''},${grade.editedDate ? 'ویرایش شده' : 'اصلی'}\n`;
            });
            
            // ایجاد فایل و دانلود
            const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', `نمرات_${getTodayDate()}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            showNotification('فایل Excel با موفقیت دانلود شد', 'success');
        }

        function openBulkAddModal() {
            showNotification('این قابلیت در نسخه بعدی اضافه خواهد شد', 'info');
        }

        function openImportModal() {
            showNotification('این قابلیت در نسخه بعدی اضافه خواهد شد', 'info');
        }

        // =================== راه‌اندازی اولیه ===================
        document.addEventListener('DOMContentLoaded', function() {
            // بارگذاری داده‌ها
            loadGrades();
            
            // راه‌اندازی datepicker
            $('.datepicker-input').persianDatepicker({
                format: 'YYYY/MM/DD',
                observer: true,
                initialValue: false,
                autoClose: true
            });
            
            // تنظیم تاریخ امروز در فیلترها
            document.getElementById('filterStartDate').value = '1404/05/01';
            document.getElementById('filterEndDate').value = getTodayDate();
            
            // تنظیم event listeners
            document.getElementById('searchInput').addEventListener('input', function() {
                currentFilter.searchQuery = this.value;
                applyFilters();
            });
            
            document.getElementById('gradeValue').addEventListener('input', updateGradeProgress);
            
            // اعمال فیلتر اولیه
            applyDateFilter();
            
            // به‌روزرسانی دوره‌ای
            setInterval(() => {
                document.getElementById('lastUpdate').textContent = getCurrentTime();
            }, 60000);
            
            // آموزش اولیه
            if (!localStorage.getItem('tutorialShown')) {
                setTimeout(() => {
                    showNotification('خوش آمدید! برای شروع، نمره جدیدی ثبت کنید یا از فیلترها استفاده نمایید.', 'info', 5000);
                    localStorage.setItem('tutorialShown', 'true');
                }, 1000);
            }
        });

        // اضافه کردن event listener برای فیلترهای وضعیت و نمره
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', applyFilters);
        });
    </script>
</body>
</html>
