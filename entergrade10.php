<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سیستم مدیریت نمرات - نسخه ساده</title>
    <style>
        * {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
        }
        
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #3498db;
        }
        
        .filter-section {
            background-color: #e8f4fc;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-right: 5px solid #3498db;
        }
        
        .filter-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        
        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        
        button:hover {
            background-color: #2980b9;
        }
        
        button.success {
            background-color: #27ae60;
        }
        
        button.success:hover {
            background-color: #219653;
        }
        
        button.warning {
            background-color: #e74c3c;
        }
        
        button.warning:hover {
            background-color: #c0392b;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            text-align: center;
            border-top: 4px solid #3498db;
        }
        
        .stat-card:nth-child(2) {
            border-top-color: #27ae60;
        }
        
        .stat-card:nth-child(3) {
            border-top-color: #e74c3c;
        }
        
        .stat-card:nth-child(4) {
            border-top-color: #9b59b6;
        }
        
        .stat-value {
            font-size: 36px;
            font-weight: bold;
            color: #2c3e50;
            margin: 10px 0;
        }
        
        .stat-label {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .action-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th {
            background-color: #2c3e50;
            color: white;
            padding: 15px;
            text-align: right;
            font-weight: bold;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            text-align: right;
        }
        
        tr:hover {
            background-color: #f9f9f9;
        }
        
        .status-original {
            color: #27ae60;
            font-weight: bold;
        }
        
        .status-edited {
            color: #e67e22;
            font-weight: bold;
        }
        
        .grade-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
            color: white;
            min-width: 50px;
            text-align: center;
        }
        
        .grade-excellent {
            background-color: #27ae60;
        }
        
        .grade-good {
            background-color: #3498db;
        }
        
        .grade-average {
            background-color: #f1c40f;
            color: #333;
        }
        
        .grade-weak {
            background-color: #e67e22;
        }
        
        .grade-poor {
            background-color: #e74c3c;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 5px 30px rgba(0,0,0,0.3);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        
        .modal-title {
            font-size: 24px;
            color: #2c3e50;
            margin: 0;
        }
        
        .close-btn {
            background: none;
            border: none;
            font-size: 28px;
            color: #7f8c8d;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #7f8c8d;
        }
        
        .empty-state i {
            font-size: 60px;
            margin-bottom: 20px;
            color: #bdc3c7;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
            font-style: italic;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
            gap: 10px;
        }
        
        .pagination button {
            background-color: #ecf0f1;
            color: #2c3e50;
            border: 1px solid #ddd;
        }
        
        .pagination button:hover:not(:disabled) {
            background-color: #3498db;
            color: white;
        }
        
        .pagination button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1>
            <i class="fas fa-graduation-cap"></i>
            سیستم مدیریت نمرات
        </h1>
        
        <!-- بخش فیلتر -->
        <div class="filter-section">
            <h2 style="margin-top: 0; color: #3498db;">
                <i class="fas fa-filter"></i>
                فیلتر بر اساس بازه تاریخ
            </h2>
            
            <div class="filter-row">
                <div class="filter-group">
                    <label for="startDate">از تاریخ</label>
                    <input type="text" id="startDate" placeholder="مثال: 1404/05/01">
                </div>
                
                <div class="filter-group">
                    <label for="endDate">تا تاریخ</label>
                    <input type="text" id="endDate" placeholder="مثال: 1404/05/15">
                </div>
                
                <div class="filter-group">
                    <button onclick="applyDateFilter()" class="success">
                        <i class="fas fa-search"></i> اعمال فیلتر
                    </button>
                </div>
                
                <div class="filter-group">
                    <button onclick="clearDateFilter()">
                        <i class="fas fa-times"></i> حذف فیلتر
                    </button>
                </div>
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                <div>
                    <span style="color: #7f8c8d; font-size: 14px;">
                        <i class="fas fa-info-circle"></i>
                        تاریخ‌ها را به صورت شمسی وارد کنید
                    </span>
                </div>
                <div id="filterInfo" style="background-color: #3498db; color: white; padding: 5px 15px; border-radius: 20px; font-size: 14px;">
                    فیلتر نشده
                </div>
            </div>
        </div>
        
        <!-- کارت‌های آماری -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-label">تعداد کل نمرات</div>
                <div class="stat-value" id="totalGrades">0</div>
                <div><i class="fas fa-list-ol fa-2x" style="color: #3498db;"></i></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">میانگین نمرات</div>
                <div class="stat-value" id="averageGrade">0.00</div>
                <div><i class="fas fa-calculator fa-2x" style="color: #27ae60;"></i></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">کمترین نمره</div>
                <div class="stat-value" id="minGrade">0.00</div>
                <div><i class="fas fa-arrow-down fa-2x" style="color: #e74c3c;"></i></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">بیشترین نمره</div>
                <div class="stat-value" id="maxGrade">0.00</div>
                <div><i class="fas fa-arrow-up fa-2x" style="color: #9b59b6;"></i></div>
            </div>
        </div>
        
        <!-- نوار ابزار -->
        <div class="action-bar">
            <div>
                <button onclick="openNewGradeModal()" class="success">
                    <i class="fas fa-plus"></i> ثبت نمره جدید
                </button>
                
                <button onclick="showAllGrades()" style="background-color: #9b59b6;">
                    <i class="fas fa-eye"></i> نمایش همه
                </button>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <input type="text" id="searchInput" placeholder="جستجوی درس..." style="width: 250px;">
                <select id="sortSelect" onchange="sortGrades()">
                    <option value="date-desc">جدیدترین اول</option>
                    <option value="date-asc">قدیمی‌ترین اول</option>
                    <option value="grade-desc">بیشترین نمره</option>
                    <option value="grade-asc">کمترین نمره</option>
                </select>
            </div>
        </div>
        
        <!-- جدول نمرات -->
        <div id="tableContainer">
            <table>
                <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>نام درس</th>
                        <th>نمره</th>
                        <th>تاریخ ثبت</th>
                        <th>تاریخ ویرایش</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody id="gradesTableBody">
                    <!-- داده‌ها اینجا نمایش داده می‌شوند -->
                </tbody>
            </table>
            
            <div id="emptyTableMessage" class="no-data">
                <i class="fas fa-clipboard-list fa-3x" style="color: #bdc3c7; margin-bottom: 15px;"></i>
                <h3>هیچ نمره‌ای یافت نشد</h3>
                <p>برای شروع، روی دکمه "ثبت نمره جدید" کلیک کنید</p>
                <button onclick="openNewGradeModal()" class="success" style="margin-top: 15px;">
                    <i class="fas fa-plus"></i> ثبت اولین نمره
                </button>
            </div>
        </div>
        
        <!-- صفحه‌بندی -->
        <div class="pagination">
            <button onclick="changePage(-1)" id="prevPageBtn" disabled>
                <i class="fas fa-chevron-right"></i> قبلی
            </button>
            <span id="pageInfo">صفحه 1 از 1</span>
            <button onclick="changePage(1)" id="nextPageBtn" disabled>
                بعدی <i class="fas fa-chevron-left"></i>
            </button>
        </div>
    </div>
    
    <!-- مودال ثبت/ویرایش نمره -->
    <div id="gradeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">ثبت نمره جدید</h3>
                <button class="close-btn" onclick="closeGradeModal()">&times;</button>
            </div>
            
            <div class="form-group">
                <label for="modalLessonName">نام درس</label>
                <input type="text" id="modalLessonName" placeholder="مثال: ریاضی">
            </div>
            
            <div class="form-group">
                <label for="modalGradeValue">نمره (0 تا 20)</label>
                <input type="number" id="modalGradeValue" min="0" max="20" step="0.01" placeholder="مثال: 18.5">
                <div id="gradeHint" style="font-size: 14px; color: #7f8c8d; margin-top: 5px;">
                    مقدار بین 0 تا 20
                </div>
            </div>
            
            <div class="form-group" id="modalDateGroup">
                <label for="modalGradeDate">تاریخ ثبت</label>
                <input type="text" id="modalGradeDate" placeholder="1404/05/15">
            </div>
            
            <div class="modal-footer">
                <button onclick="closeGradeModal()">لغو</button>
                <button onclick="saveGrade()" class="success">ذخیره نمره</button>
            </div>
        </div>
    </div>
    
    <!-- مودال حذف -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">تأیید حذف</h3>
                <button class="close-btn" onclick="closeDeleteModal()">&times;</button>
            </div>
            
            <div style="text-align: center; padding: 20px 0;">
                <i class="fas fa-exclamation-triangle fa-3x" style="color: #e74c3c; margin-bottom: 15px;"></i>
                <p id="deleteMessage">آیا از حذف این نمره اطمینان دارید؟</p>
            </div>
            
            <div class="modal-footer">
                <button onclick="closeDeleteModal()">لغو</button>
                <button onclick="confirmDelete()" class="warning">حذف</button>
            </div>
        </div>
    </div>

    <script>
        // =================== متغیرهای اصلی ===================
        let grades = [];
        let currentFilter = { startDate: null, endDate: null };
        let editingGradeId = null;
        let deletingGradeId = null;
        let currentPage = 1;
        const itemsPerPage = 8;
        let filteredGrades = [];
        
        // =================== توابع کمکی ===================
        function getTodayDate() {
            // تاریخ شمسی ساده - در واقعیت باید از کتابخانه استفاده شود
            return "1404/05/15";
        }
        
        function dateToNumber(dateStr) {
            if (!dateStr) return 0;
            const parts = dateStr.split('/');
            return parseInt(parts[0]) * 10000 + parseInt(parts[1]) * 100 + parseInt(parts[2]);
        }
        
        function getGradeCategory(grade) {
            if (grade >= 18) return { name: 'عالی', class: 'grade-excellent' };
            if (grade >= 15) return { name: 'خوب', class: 'grade-good' };
            if (grade >= 12) return { name: 'متوسط', class: 'grade-average' };
            if (grade >= 10) return { name: 'ضعیف', class: 'grade-weak' };
            return { name: 'مردود', class: 'grade-poor' };
        }
        
        function showNotification(message, type = 'success') {
            // ایجاد یک عنصر برای نمایش پیام
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                left: 50%;
                transform: translateX(-50%);
                background-color: ${type === 'success' ? '#27ae60' : '#e74c3c'};
                color: white;
                padding: 15px 25px;
                border-radius: 5px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
                z-index: 10000;
                font-weight: bold;
                animation: slideIn 0.3s ease;
            `;
            
            notification.innerHTML = `
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'}" style="margin-left: 10px;"></i>
                ${message}
            `;
            
            document.body.appendChild(notification);
            
            // حذف خودکار پس از 3 ثانیه
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => {
                    if (notification.parentNode) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 3000);
            
            // اضافه کردن استایل‌های انیمیشن
            if (!document.querySelector('#notificationStyles')) {
                const style = document.createElement('style');
                style.id = 'notificationStyles';
                style.textContent = `
                    @keyframes slideIn {
                        from { top: -100px; opacity: 0; }
                        to { top: 20px; opacity: 1; }
                    }
                    @keyframes slideOut {
                        from { top: 20px; opacity: 1; }
                        to { top: -100px; opacity: 0; }
                    }
                `;
                document.head.appendChild(style);
            }
        }
        
        // =================== مدیریت داده‌ها ===================
        function loadGrades() {
            // بارگذاری از localStorage یا ایجاد داده‌های نمونه
            const saved = localStorage.getItem('simpleGradeSystem');
            
            if (saved) {
                grades = JSON.parse(saved);
            } else {
                // داده‌های نمونه
                grades = [
                    { id: 1, lesson: "ریاضی", grade: 18.5, date: "1404/05/05", editedDate: null },
                    { id: 2, lesson: "فیزیک", grade: 16, date: "1404/05/06", editedDate: "1404/05/07" },
                    { id: 3, lesson: "شیمی", grade: 19, date: "1404/05/06", editedDate: null },
                    { id: 4, lesson: "زبان انگلیسی", grade: 15.25, date: "1404/05/08", editedDate: "1404/05/10" },
                    { id: 5, lesson: "تاریخ", grade: 17.75, date: "1404/05/10", editedDate: null },
                    { id: 6, lesson: "ادبیات فارسی", grade: 14.5, date: "1404/05/12", editedDate: "1404/05/14" },
                    { id: 7, lesson: "زیست‌شناسی", grade: 18, date: "1404/05/15", editedDate: null }
                ];
                saveGrades();
            }
            
            // تنظیم تاریخ امروز در فیلترها
            document.getElementById('startDate').value = "1404/05/01";
            document.getElementById('endDate').value = getTodayDate();
            
            // اعمال فیلتر اولیه
            applyDateFilter();
            
            showNotification('سیستم با موفقیت بارگذاری شد', 'success');
        }
        
        function saveGrades() {
            localStorage.setItem('simpleGradeSystem', JSON.stringify(grades));
        }
        
        // =================== فیلتر و جستجو ===================
        function applyDateFilter() {
            const start = document.getElementById('startDate').value;
            const end = document.getElementById('endDate').value;
            
            currentFilter.startDate = start || null;
            currentFilter.endDate = end || null;
            
            // اعمال فیلتر
            filteredGrades = grades.filter(grade => {
                if (currentFilter.startDate && currentFilter.endDate) {
                    const gradeDateNum = dateToNumber(grade.date);
                    const startNum = dateToNumber(currentFilter.startDate);
                    const endNum = dateToNumber(currentFilter.endDate);
                    
                    if (gradeDateNum < startNum || gradeDateNum > endNum) {
                        return false;
                    }
                }
                
                // جستجو
                const searchQuery = document.getElementById('searchInput').value.toLowerCase();
                if (searchQuery) {
                    const lessonMatch = grade.lesson.toLowerCase().includes(searchQuery);
                    const gradeMatch = grade.grade.toString().includes(searchQuery);
                    
                    if (!lessonMatch && !gradeMatch) {
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
            
            // به‌روزرسانی اطلاعات فیلتر
            if (currentFilter.startDate && currentFilter.endDate) {
                document.getElementById('filterInfo').textContent = 
                    `${currentFilter.startDate} تا ${currentFilter.endDate}`;
                showNotification('فیلتر تاریخ اعمال شد', 'success');
            } else {
                document.getElementById('filterInfo').textContent = 'فیلتر نشده';
                showNotification('فیلتر حذف شد', 'success');
            }
        }
        
        function clearDateFilter() {
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';
            document.getElementById('searchInput').value = '';
            currentFilter.startDate = null;
            currentFilter.endDate = null;
            
            filteredGrades = [...grades];
            currentPage = 1;
            updateTable();
            updateStats();
            
            document.getElementById('filterInfo').textContent = 'فیلتر نشده';
            showNotification('همه فیلترها حذف شدند', 'success');
        }
        
        function showAllGrades() {
            filteredGrades = [...grades];
            currentPage = 1;
            updateTable();
            updateStats();
            
            document.getElementById('filterInfo').textContent = 'نمایش همه';
            showNotification('همه نمرات نمایش داده شدند', 'success');
        }
        
        function sortGrades() {
            const sortOrder = document.getElementById('sortSelect').value;
            
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
                    default:
                        return 0;
                }
            });
            
            currentPage = 1;
            updateTable();
        }
        
        function sortFilteredGrades() {
            const sortOrder = document.getElementById('sortSelect').value;
            
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
                    default:
                        return 0;
                }
            });
        }
        
        // =================== مدیریت صفحه‌بندی ===================
        function changePage(direction) {
            const totalPages = Math.ceil(filteredGrades.length / itemsPerPage);
            const newPage = currentPage + direction;
            
            if (newPage < 1 || newPage > totalPages) return;
            
            currentPage = newPage;
            updateTable();
        }
        
        // =================== به‌روزرسانی نمایش ===================
        function updateTable() {
            const tbody = document.getElementById('gradesTableBody');
            const emptyMessage = document.getElementById('emptyTableMessage');
            
            if (filteredGrades.length === 0) {
                tbody.innerHTML = '';
                emptyMessage.style.display = 'block';
                
                // غیرفعال کردن صفحه‌بندی
                document.getElementById('prevPageBtn').disabled = true;
                document.getElementById('nextPageBtn').disabled = true;
                document.getElementById('pageInfo').textContent = 'صفحه 1 از 1';
                
                return;
            }
            
            emptyMessage.style.display = 'none';
            
            // محاسبه محدوده ردیف‌های این صفحه
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = Math.min(startIndex + itemsPerPage, filteredGrades.length);
            const pageGrades = filteredGrades.slice(startIndex, endIndex);
            
            let html = '';
            pageGrades.forEach((grade, index) => {
                const actualIndex = startIndex + index + 1;
                const category = getGradeCategory(grade.grade);
                const isEdited = grade.editedDate !== null;
                
                html += `
                <tr>
                    <td>${actualIndex}</td>
                    <td><strong>${grade.lesson}</strong></td>
                    <td>
                        <span class="grade-badge ${category.class}">
                            ${grade.grade.toFixed(2)}
                        </span>
                        <div style="font-size: 12px; color: #7f8c8d; margin-top: 5px;">${category.name}</div>
                    </td>
                    <td>${grade.date}</td>
                    <td>${grade.editedDate || '---'}</td>
                    <td>
                        ${isEdited ? 
                            '<span class="status-edited"><i class="fas fa-edit"></i> ویرایش شده</span>' : 
                            '<span class="status-original"><i class="fas fa-check"></i> اصلی</span>'
                        }
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button onclick="editGrade(${grade.id})" style="background-color: #f39c12; padding: 5px 10px; font-size: 14px;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="openDeleteModal(${grade.id})" class="warning" style="padding: 5px 10px; font-size: 14px;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>`;
            });
            
            tbody.innerHTML = html;
            
            // به‌روزرسانی صفحه‌بندی
            const totalPages = Math.ceil(filteredGrades.length / itemsPerPage);
            document.getElementById('pageInfo').textContent = `صفحه ${currentPage} از ${totalPages}`;
            document.getElementById('prevPageBtn').disabled = currentPage === 1;
            document.getElementById('nextPageBtn').disabled = currentPage === totalPages;
        }
        
        function updateStats() {
            if (filteredGrades.length === 0) {
                document.getElementById('totalGrades').textContent = '0';
                document.getElementById('averageGrade').textContent = '0.00';
                document.getElementById('minGrade').textContent = '0.00';
                document.getElementById('maxGrade').textContent = '0.00';
                return;
            }
            
            const gradesOnly = filteredGrades.map(g => g.grade);
            const total = filteredGrades.length;
            const minGrade = Math.min(...gradesOnly);
            const maxGrade = Math.max(...gradesOnly);
            const averageGrade = gradesOnly.reduce((a, b) => a + b, 0) / total;
            
            document.getElementById('totalGrades').textContent = total;
            document.getElementById('averageGrade').textContent = averageGrade.toFixed(2);
            document.getElementById('minGrade').textContent = minGrade.toFixed(2);
            document.getElementById('maxGrade').textContent = maxGrade.toFixed(2);
        }
        
        // =================== مدیریت مودال‌ها ===================
        function openNewGradeModal() {
            editingGradeId = null;
            document.getElementById('modalTitle').textContent = 'ثبت نمره جدید';
            document.getElementById('modalLessonName').value = '';
            document.getElementById('modalGradeValue').value = '';
            document.getElementById('modalGradeDate').value = getTodayDate();
            document.getElementById('modalDateGroup').style.display = 'block';
            
            document.getElementById('gradeModal').style.display = 'flex';
            document.getElementById('modalLessonName').focus();
        }
        
        function editGrade(id) {
            const grade = grades.find(g => g.id === id);
            if (!grade) return;
            
            editingGradeId = id;
            document.getElementById('modalTitle').textContent = 'ویرایش نمره';
            document.getElementById('modalLessonName').value = grade.lesson;
            document.getElementById('modalGradeValue').value = grade.grade;
            document.getElementById('modalGradeDate').value = grade.date;
            document.getElementById('modalDateGroup').style.display = 'none';
            
            document.getElementById('gradeModal').style.display = 'flex';
            document.getElementById('modalGradeValue').focus();
        }
        
        function closeGradeModal() {
            document.getElementById('gradeModal').style.display = 'none';
            editingGradeId = null;
        }
        
        function saveGrade() {
            const lesson = document.getElementById('modalLessonName').value.trim();
            const gradeValue = parseFloat(document.getElementById('modalGradeValue').value);
            const date = document.getElementById('modalGradeDate').value;
            
            // اعتبارسنجی
            if (!lesson) {
                alert('لطفاً نام درس را وارد کنید.');
                return;
            }
            
            if (isNaN(gradeValue) || gradeValue < 0 || gradeValue > 20) {
                alert('لطفاً نمره معتبر بین 0 تا 20 وارد کنید.');
                return;
            }
            
            if (!date && editingGradeId === null) {
                alert('لطفاً تاریخ را وارد کنید.');
                return;
            }
            
            if (editingGradeId === null) {
                // ثبت جدید
                const newGrade = {
                    id: Date.now(),
                    lesson: lesson,
                    grade: gradeValue,
                    date: date,
                    editedDate: null
                };
                grades.push(newGrade);
                showNotification('نمره جدید با موفقیت ثبت شد', 'success');
            } else {
                // ویرایش
                const gradeIndex = grades.findIndex(g => g.id === editingGradeId);
                if (gradeIndex !== -1) {
                    grades[gradeIndex].lesson = lesson;
                    grades[gradeIndex].grade = gradeValue;
                    grades[gradeIndex].editedDate = getTodayDate();
                }
                showNotification('نمره با موفقیت ویرایش شد', 'success');
            }
            
            saveGrades();
            closeGradeModal();
            applyDateFilter(); // برای به‌روزرسانی فیلتر فعلی
        }
        
        function openDeleteModal(id) {
            const grade = grades.find(g => g.id === id);
            if (!grade) return;
            
            deletingGradeId = id;
            document.getElementById('deleteMessage').innerHTML = `
                آیا از حذف نمره <strong>${grade.lesson}</strong> با نمره <strong>${grade.grade.toFixed(2)}</strong> اطمینان دارید؟
                <br><span style="font-size: 14px; color: #7f8c8d;">این عمل قابل بازگشت نیست.</span>
            `;
            document.getElementById('deleteModal').style.display = 'flex';
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            deletingGradeId = null;
        }
        
        function confirmDelete() {
            if (!deletingGradeId) return;
            
            const gradeIndex = grades.findIndex(g => g.id === deletingGradeId);
            if (gradeIndex !== -1) {
                grades.splice(gradeIndex, 1);
                saveGrades();
                applyDateFilter();
                showNotification('نمره با موفقیت حذف شد', 'success');
            }
            
            closeDeleteModal();
        }
        
        // =================== راه‌اندازی اولیه ===================
        document.addEventListener('DOMContentLoaded', function() {
            loadGrades();
            
            // تنظیم event listener برای جستجو
            document.getElementById('searchInput').addEventListener('input', applyDateFilter);
            
            // تنظیم event listener برای فیلد نمره در مودال
            document.getElementById('modalGradeValue').addEventListener('input', function() {
                const value = parseFloat(this.value) || 0;
                const hint = document.getElementById('gradeHint');
                
                if (value >= 18) {
                    hint.innerHTML = '<span style="color: #27ae60;">عالی (18-20)</span>';
                } else if (value >= 15) {
                    hint.innerHTML = '<span style="color: #3498db;">خوب (15-17.99)</span>';
                } else if (value >= 12) {
                    hint.innerHTML = '<span style="color: #f1c40f;">متوسط (12-14.99)</span>';
                } else if (value >= 10) {
                    hint.innerHTML = '<span style="color: #e67e22;">ضعیف (10-11.99)</span>';
                } else if (value >= 0) {
                    hint.innerHTML = '<span style="color: #e74c3c;">مردود (0-9.99)</span>';
                } else {
                    hint.textContent = 'مقدار بین 0 تا 20';
                }
            });
            
            // بستن مودال با کلیک خارج از آن
            window.onclick = function(event) {
                const gradeModal = document.getElementById('gradeModal');
                const deleteModal = document.getElementById('deleteModal');
                
                if (event.target === gradeModal) {
                    closeGradeModal();
                }
                
                if (event.target === deleteModal) {
                    closeDeleteModal();
                }
            };
            
            // بستن مودال با کلید Escape
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeGradeModal();
                    closeDeleteModal();
                }
            });
            
            console.log('سیستم مدیریت نمرات با موفقیت بارگذاری شد!');
        });
    </script>
</body>
</html>
