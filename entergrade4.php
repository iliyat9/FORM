<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سیستم مدیریت نمرات - تاریخ شمسی</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css">
</head>
<body class="bg-gray-50 p-4">
    <div class="max-w-7xl mx-auto">
        <!-- هدر -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-6 rounded-2xl shadow-lg mb-6">
            <h1 class="text-3xl font-bold mb-2">سیستم مدیریت نمرات - تاریخ شمسی</h1>
            <p class="text-blue-100">مدیریت، فیلتر و ویرایش نمرات در بازه زمانی مشخص</p>
        </div>

        <!-- فیلتر بازه زمانی -->
        <div class="bg-white p-6 rounded-2xl shadow-lg mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-filter ml-2 text-blue-500"></i>
                فیلتر بر اساس بازه تاریخ
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">از تاریخ</label>
                    <input type="text" id="startDate" 
                           class="w-full p-3 border border-gray-300 rounded-xl text-center text-lg font-medium datepicker"
                           placeholder="1404/05/01">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تا تاریخ</label>
                    <input type="text" id="endDate" 
                           class="w-full p-3 border border-gray-300 rounded-xl text-center text-lg font-medium datepicker"
                           placeholder="1404/05/15">
                </div>
                <div class="flex flex-col justify-end">
                    <button onclick="applyDateFilter()" 
                            class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white p-3 rounded-xl font-semibold transition-all duration-300 shadow-md">
                        <i class="fas fa-search ml-2"></i>
                        اعمال فیلتر
                    </button>
                </div>
                <div class="flex flex-col justify-end">
                    <button onclick="clearDateFilter()" 
                            class="w-full bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white p-3 rounded-xl font-semibold transition-all duration-300 shadow-md">
                        <i class="fas fa-times ml-2"></i>
                        حذف فیلتر
                    </button>
                </div>
                <div class="flex flex-col justify-end">
                    <div class="bg-blue-50 p-3 rounded-xl text-center border border-blue-200">
                        <p class="text-sm text-gray-600">تعداد فیلتر شده</p>
                        <p id="filterCount" class="text-2xl font-bold text-blue-600">0</p>
                    </div>
                </div>
            </div>
            
            <div class="text-center text-gray-600 text-sm">
                <i class="fas fa-info-circle ml-1"></i>
                تاریخ‌ها را به صورت شمسی وارد کنید (مثال: 1404/05/01)
            </div>
        </div>

        <!-- آمار و کارت‌ها -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 p-5 rounded-2xl shadow">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-600 mb-1">تعداد نمرات</p>
                        <p id="totalGrades" class="text-4xl font-bold text-blue-700">0</p>
                    </div>
                    <div class="bg-blue-500 text-white p-3 rounded-full">
                        <i class="fas fa-list-ol text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-emerald-50 to-emerald-100 border border-emerald-200 p-5 rounded-2xl shadow">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-600 mb-1">کمترین نمره</p>
                        <p id="minGrade" class="text-4xl font-bold text-emerald-700">0</p>
                    </div>
                    <div class="bg-emerald-500 text-white p-3 rounded-full">
                        <i class="fas fa-arrow-down text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-rose-50 to-rose-100 border border-rose-200 p-5 rounded-2xl shadow">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-600 mb-1">بیشترین نمره</p>
                        <p id="maxGrade" class="text-4xl font-bold text-rose-700">0</p>
                    </div>
                    <div class="bg-rose-500 text-white p-3 rounded-full">
                        <i class="fas fa-arrow-up text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-violet-50 to-violet-100 border border-violet-200 p-5 rounded-2xl shadow">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-600 mb-1">ثبت نمره جدید</p>
                        <button onclick="openNewGradeModal()" 
                                class="mt-2 w-full bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white p-3 rounded-xl font-semibold transition-all duration-300">
                            <i class="fas fa-plus ml-2"></i> افزودن نمره جدید
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول نمرات -->
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-table ml-2 text-green-500"></i>
                    لیست نمرات
                </h2>
                <div class="text-sm text-gray-600">
                    <span id="tableInfo">در حال بارگذاری...</span>
                </div>
            </div>
            
            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-4 text-right font-semibold text-gray-700 border-l border-gray-200">ردیف</th>
                            <th class="p-4 text-right font-semibold text-gray-700 border-l border-gray-200">نام درس</th>
                            <th class="p-4 text-right font-semibold text-gray-700 border-l border-gray-200">نمره</th>
                            <th class="p-4 text-right font-semibold text-gray-700 border-l border-gray-200">تاریخ ثبت اولیه</th>
                            <th class="p-4 text-right font-semibold text-gray-700 border-l border-gray-200">تاریخ آخرین ویرایش</th>
                            <th class="p-4 text-right font-semibold text-gray-700 border-l border-gray-200">وضعیت</th>
                            <th class="p-4 text-right font-semibold text-gray-700">عملیات</th>
                        </tr>
                    </thead>
                    <tbody id="gradesTableBody" class="divide-y divide-gray-100">
                        <!-- نمرات در اینجا لود می‌شوند -->
                    </tbody>
                </table>
                
                <!-- حالت خالی -->
                <div id="emptyState" class="hidden p-8 text-center">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-clipboard-list text-5xl"></i>
                    </div>
                    <p class="text-gray-500 text-lg">هیچ نمره‌ای یافت نشد</p>
                    <p class="text-gray-400 mt-2">با دکمه "افزودن نمره جدید" اولین نمره را ثبت کنید</p>
                </div>
            </div>
        </div>

        <!-- اطلاعات پایین صفحه -->
        <div class="mt-6 text-center text-gray-500 text-sm">
            <p>سیستم مدیریت نمرات | تاریخ شمسی | تمامی داده‌ها در مرورگر ذخیره می‌شوند</p>
        </div>
    </div>

    <!-- مودال ثبت/ویرایش نمره -->
    <div id="gradeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-md">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4" id="modalTitle">ثبت نمره جدید</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">نام درس</label>
                        <input type="text" id="lessonName" 
                               class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                               placeholder="مثال: ریاضی، فیزیک، ...">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">نمره (0 تا 20)</label>
                        <input type="number" id="gradeValue" min="0" max="20" step="0.25"
                               class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                               placeholder="مثال: 18.5">
                        <div class="mt-2 flex justify-between">
                            <span class="text-xs text-gray-500">مقدار بین 0 تا 20</span>
                            <span class="text-xs text-gray-500" id="gradeHint"></span>
                        </div>
                    </div>
                    
                    <div id="originalDateField">
                        <label class="block text-sm font-medium text-gray-700 mb-2">تاریخ ثبت</label>
                        <input type="text" id="gradeDate" 
                               class="w-full p-3 border border-gray-300 rounded-xl text-center datepicker"
                               placeholder="1404/05/15">
                    </div>
                </div>
                
                <div class="flex justify-between mt-8">
                    <button onclick="closeGradeModal()" 
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-300 transition-all duration-300">
                        <i class="fas fa-times ml-2"></i>لغو
                    </button>
                    <button onclick="saveGrade()" 
                            class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-medium hover:from-blue-600 hover:to-blue-700 transition-all duration-300 shadow-md">
                        <i class="fas fa-save ml-2"></i>ذخیره نمره
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // تنظیمات اولیه
        let grades = [];
        let currentFilter = { startDate: null, endDate: null };
        let editingGradeId = null;

        // تاریخ شمسی امروز
        function getTodayDate() {
            const today = new PersianDate();
            return today.format('YYYY/MM/DD');
        }

        // تبدیل تاریخ شمسی به عدد برای مقایسه
        function dateToNumber(dateStr) {
            if (!dateStr) return 0;
            const parts = dateStr.split('/');
            return parseInt(parts[0]) * 10000 + parseInt(parts[1]) * 100 + parseInt(parts[2]);
        }

        // بارگذاری داده‌ها از localStorage
        function loadGrades() {
            const saved = localStorage.getItem('gradeSystemData');
            if (saved) {
                grades = JSON.parse(saved);
            } else {
                // داده‌های اولیه نمونه
                grades = [
                    { id: 1, lesson: "ریاضی", grade: 18.5, date: "1404/05/05", editedDate: null, editedCount: 0 },
                    { id: 2, lesson: "فیزیک", grade: 16, date: "1404/05/06", editedDate: "1404/05/07", editedCount: 1 },
                    { id: 3, lesson: "شیمی", grade: 19, date: "1404/05/06", editedDate: null, editedCount: 0 },
                    { id: 4, lesson: "زبان انگلیسی", grade: 15.25, date: "1404/05/08", editedDate: "1404/05/10", editedCount: 2 },
                    { id: 5, lesson: "تاریخ", grade: 17.75, date: "1404/05/10", editedDate: null, editedCount: 0 },
                    { id: 6, lesson: "ادبیات", grade: 14.5, date: "1404/05/12", editedDate: "1404/05/14", editedCount: 1 },
                    { id: 7, lesson: "زیست‌شناسی", grade: 18, date: "1404/05/15", editedDate: null, editedCount: 0 }
                ];
                saveGrades();
            }
            updateDisplay();
        }

        // ذخیره داده‌ها در localStorage
        function saveGrades() {
            localStorage.setItem('gradeSystemData', JSON.stringify(grades));
        }

        // اعمال فیلتر تاریخ
        function applyDateFilter() {
            const start = document.getElementById('startDate').value;
            const end = document.getElementById('endDate').value;
            
            if (!start || !end) {
                alert('لطفاً هر دو تاریخ شروع و پایان را وارد کنید.');
                return;
            }
            
            currentFilter.startDate = start;
            currentFilter.endDate = end;
            updateDisplay();
        }

        // حذف فیلتر
        function clearDateFilter() {
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';
            currentFilter.startDate = null;
            currentFilter.endDate = null;
            updateDisplay();
        }

        // بروزرسانی نمایش جدول
        function updateDisplay() {
            let filteredGrades = grades;
            
            // اعمال فیلتر تاریخ
            if (currentFilter.startDate && currentFilter.endDate) {
                const startNum = dateToNumber(currentFilter.startDate);
                const endNum = dateToNumber(currentFilter.endDate);
                
                filteredGrades = grades.filter(grade => {
                    const gradeDateNum = dateToNumber(grade.date);
                    return gradeDateNum >= startNum && gradeDateNum <= endNum;
                });
                
                // بروزرسانی تعداد فیلتر شده
                document.getElementById('filterCount').textContent = filteredGrades.length;
            } else {
                document.getElementById('filterCount').textContent = grades.length;
            }
            
            // مرتب‌سازی بر اساس تاریخ (جدیدترین اول)
            filteredGrades.sort((a, b) => dateToNumber(b.date) - dateToNumber(a.date));
            
            // بروزرسانی جدول
            updateTable(filteredGrades);
            
            // بروزرسانی آمار
            updateStats(filteredGrades);
            
            // بروزرسانی اطلاعات جدول
            document.getElementById('tableInfo').textContent = 
                `نمایش ${filteredGrades.length} نمره از ${grades.length} نمره`;
        }

        // بروزرسانی جدول
        function updateTable(data) {
            const tbody = document.getElementById('gradesTableBody');
            const emptyState = document.getElementById('emptyState');
            
            if (data.length === 0) {
                tbody.innerHTML = '';
                emptyState.classList.remove('hidden');
                return;
            }
            
            emptyState.classList.add('hidden');
            
            let html = '';
            data.forEach((grade, index) => {
                const isEdited = grade.editedDate !== null;
                const statusClass = isEdited ? 'bg-yellow-50 border-r-4 border-r-yellow-400' : 'bg-green-50 border-r-4 border-r-green-400';
                const statusIcon = isEdited ? 
                    '<i class="fas fa-edit text-yellow-600 ml-1"></i>' : 
                    '<i class="fas fa-check text-green-600 ml-1"></i>';
                const statusText = isEdited ? 
                    `<span class="text-yellow-700 font-medium">ویرایش شده</span>` : 
                    `<span class="text-green-700 font-medium">اصلی</span>`;
                
                html += `
                <tr class="${statusClass} hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-medium text-gray-800">${index + 1}</td>
                    <td class="p-4 font-semibold text-gray-900">${grade.lesson}</td>
                    <td class="p-4">
                        <span class="inline-block bg-gray-100 px-4 py-2 rounded-lg font-bold text-lg ${grade.grade >= 18 ? 'text-green-600' : grade.grade >= 15 ? 'text-blue-600' : 'text-red-600'}">
                            ${grade.grade.toFixed(2)}
                        </span>
                    </td>
                    <td class="p-4">
                        <div class="flex flex-col">
                            <span class="font-medium">${grade.date}</span>
                            <span class="text-xs text-gray-500 mt-1">اولیه</span>
                        </div>
                    </td>
                    <td class="p-4">
                        <div class="flex flex-col">
                            <span class="font-medium">${grade.editedDate || '---'}</span>
                            <span class="text-xs text-gray-500 mt-1">${grade.editedDate ? 'آخرین ویرایش' : 'بدون ویرایش'}</span>
                        </div>
                    </td>
                    <td class="p-4">
                        <div class="flex items-center">
                            ${statusIcon}
                            ${statusText}
                            ${grade.editedCount > 0 ? 
                                `<span class="mr-2 text-xs bg-gray-200 px-2 py-1 rounded">${grade.editedCount} بار</span>` : ''}
                        </div>
                    </td>
                    <td class="p-4">
                        <div class="flex space-x-2 space-x-reverse">
                            <button onclick="editGrade(${grade.id})" 
                                    class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-4 py-2 rounded-lg transition-all duration-300">
                                <i class="fas fa-edit ml-1"></i>ویرایش
                            </button>
                            <button onclick="deleteGrade(${grade.id})" 
                                    class="bg-red-100 text-red-700 hover:bg-red-200 px-4 py-2 rounded-lg transition-all duration-300">
                                <i class="fas fa-trash ml-1"></i>حذف
                            </button>
                        </div>
                    </td>
                </tr>`;
            });
            
            tbody.innerHTML = html;
        }

        // بروزرسانی آمار
        function updateStats(data) {
            document.getElementById('totalGrades').textContent = data.length;
            
            if (data.length > 0) {
                const gradesOnly = data.map(g => g.grade);
                const minGrade = Math.min(...gradesOnly);
                const maxGrade = Math.max(...gradesOnly);
                
                document.getElementById('minGrade').textContent = minGrade.toFixed(2);
                document.getElementById('maxGrade').textContent = maxGrade.toFixed(2);
            } else {
                document.getElementById('minGrade').textContent = '0.00';
                document.getElementById('maxGrade').textContent = '0.00';
            }
        }

        // باز کردن مودال برای نمره جدید
        function openNewGradeModal() {
            editingGradeId = null;
            document.getElementById('modalTitle').textContent = 'ثبت نمره جدید';
            document.getElementById('lessonName').value = '';
            document.getElementById('gradeValue').value = '';
            document.getElementById('gradeDate').value = getTodayDate();
            document.getElementById('originalDateField').classList.remove('hidden');
            
            document.getElementById('gradeModal').classList.remove('hidden');
        }

        // باز کردن مودال برای ویرایش
        function editGrade(id) {
            const grade = grades.find(g => g.id === id);
            if (!grade) return;
            
            editingGradeId = id;
            document.getElementById('modalTitle').textContent = 'ویرایش نمره';
            document.getElementById('lessonName').value = grade.lesson;
            document.getElementById('gradeValue').value = grade.grade;
            document.getElementById('gradeDate').value = grade.date;
            document.getElementById('originalDateField').classList.add('hidden');
            
            document.getElementById('gradeModal').classList.remove('hidden');
        }

        // بستن مودال
        function closeGradeModal() {
            document.getElementById('gradeModal').classList.add('hidden');
            editingGradeId = null;
        }

        // ذخیره نمره (جدید یا ویرایش)
        function saveGrade() {
            const lesson = document.getElementById('lessonName').value.trim();
            const gradeValue = parseFloat(document.getElementById('gradeValue').value);
            const date = document.getElementById('gradeDate').value;
            
            // اعتبارسنجی
            if (!lesson) {
                alert('لطفاً نام درس را وارد کنید.');
                return;
            }
            
            if (isNaN(gradeValue) || gradeValue < 0 || gradeValue > 20) {
                alert('لطفاً نمره معتبر بین 0 تا 20 وارد کنید.');
                return;
            }
            
            if (!date || !date.match(/^\d{4}\/\d{2}\/\d{2}$/)) {
                alert('لطفاً تاریخ شمسی معتبر وارد کنید (فرمت: 1404/05/15).');
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
                    editedCount: 0
                };
                grades.push(newGrade);
            } else {
                // ویرایش
                const gradeIndex = grades.findIndex(g => g.id === editingGradeId);
                if (gradeIndex !== -1) {
                    grades[gradeIndex].lesson = lesson;
                    grades[gradeIndex].grade = gradeValue;
                    grades[gradeIndex].editedDate = getTodayDate();
                    grades[gradeIndex].editedCount = (grades[gradeIndex].editedCount || 0) + 1;
                }
            }
            
            saveGrades();
            closeGradeModal();
            updateDisplay();
            
            // پیام موفقیت
            showNotification(editingGradeId === null ? 'نمره جدید با موفقیت ثبت شد.' : 'نمره با موفقیت ویرایش شد.', 'success');
        }

        // حذف نمره
        function deleteGrade(id) {
            if (!confirm('آیا از حذف این نمره اطمینان دارید؟ این عمل قابل بازگشت نیست.')) {
                return;
            }
            
            grades = grades.filter(g => g.id !== id);
            saveGrades();
            updateDisplay();
            showNotification('نمره با موفقیت حذف شد.', 'warning');
        }

        // نمایش نوتیفیکیشن
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 left-4 right-4 md:left-auto md:right-4 md:w-96 p-4 rounded-xl shadow-lg text-white font-medium z-50 transform transition-all duration-500 ${
                type === 'success' ? 'bg-green-500' : 'bg-orange-500'
            }`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} ml-2"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 500);
            }, 3000);
        }

        // راه‌اندازی datepicker
        function initializeDatepickers() {
            $('.datepicker').persianDatepicker({
                format: 'YYYY/MM/DD',
                observer: true,
                initialValue: false,
                autoClose: true
            });
        }

        // راه‌اندازی hint برای نمره
        function setupGradeHint() {
            const gradeInput = document.getElementById('gradeValue');
            const gradeHint = document.getElementById('gradeHint');
            
            gradeInput.addEventListener('input', function() {
                const value = parseFloat(this.value) || 0;
                let hint = '';
                
                if (value < 10) {
                    hint = 'ضعیف';
                    gradeHint.className = 'text-xs text-red-500';
                } else if (value < 12) {
                    hint = 'قابل قبول';
                    gradeHint.className = 'text-xs text-orange-500';
                } else if (value < 15) {
                    hint = 'متوسط';
                    gradeHint.className = 'text-xs text-yellow-500';
                } else if (value < 18) {
                    hint = 'خوب';
                    gradeHint.className = 'text-xs text-blue-500';
                } else {
                    hint = 'عالی';
                    gradeHint.className = 'text-xs text-green-500';
                }
                
                gradeHint.textContent = hint;
            });
        }

        // مقداردهی اولیه هنگام لود صفحه
        document.addEventListener('DOMContentLoaded', function() {
            loadGrades();
            initializeDatepickers();
            setupGradeHint();
            
            // تنظیم تاریخ امروز به صورت پیش‌فرض در فیلترها
            const today = getTodayDate();
            document.getElementById('startDate').value = today;
            document.getElementById('endDate').value = today;
        });
    </script>
</body>
</html>
