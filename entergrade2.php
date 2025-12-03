<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// اتصال به دیتابیس
$host = 'localhost';
$dbname = 'beik';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(Exception $e) {
    die("خطا در اتصال به دیتابیس: " . $e->getMessage());
}

// گرفتن id دانش‌آموز از URL
if (!isset($_GET['id'])) {
    die("کاربر مشخص نشده است.");
}
$user_id = (int)$_GET['id'];

// گرفتن مشخصات دانش‌آموز
$stmt = $pdo->prepare("SELECT id, first_name, last_name FROM saved WHERE id=?");
$stmt->execute([$user_id]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userData) {
    die("دانش‌آموز یافت نشد.");
}

// دروس پیش‌فرض
$lessons = ['فارسی','ریاضی','قرآن','دینی','تاریخ','هنر','ورزش'];

// تاریخ انتخابی کاربر
$selected_date = isset($_GET['date_filter']) ? $_GET['date_filter'] : '';

// گرفتن نمرات ثبت‌شده با فیلتر تاریخ
if (!empty($selected_date)) {
    $sql = "SELECT id, name_dars, score, date_time FROM studennt WHERE user_id=? AND DATE(date_time) = ? ORDER BY date_time DESC";
    $stmt_scores = $pdo->prepare($sql);
    $stmt_scores->execute([$user_id, $selected_date]);
} else {
    $sql = "SELECT id, name_dars, score, date_time FROM studennt WHERE user_id=? ORDER BY date_time DESC";
    $stmt_scores = $pdo->prepare($sql);
    $stmt_scores->execute([$user_id]);
}

$scores = $stmt_scores->fetchAll(PDO::FETCH_ASSOC);

// گرفتن همه تاریخ‌های موجود برای dropdown
$stmt_dates = $pdo->prepare("SELECT DISTINCT DATE(date_time) as date_only FROM studennt WHERE user_id=? ORDER BY date_time DESC");
$stmt_dates->execute([$user_id]);
$available_dates = $stmt_dates->fetchAll(PDO::FETCH_COLUMN);

// داده‌های برای چارت
$chart_labels = [];
$chart_data = [];
$chart_dates = [];
foreach($scores as $score) {
    $chart_labels[] = $score['name_dars'];
    $chart_data[] = $score['score'];
    $chart_dates[] = date('Y-m-d H:i', strtotime($score['date_time']));
}

// محاسبه آمار
$total_scores = count($scores);
$average_score = $total_scores > 0 ? array_sum($chart_data) / $total_scores : 0;
$max_score = $total_scores > 0 ? max($chart_data) : 0;
$min_score = $total_scores > 0 ? min($chart_data) : 0;

// تابع تبدیل اعداد انگلیسی به فارسی
function en_to_fa($string) {
    $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    return str_replace($english, $persian, $string);
}

$message = '';
$edit_mode = false;
$edit_id = 0;

// پردازش ثبت/ویرایش نمره
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'save_score') {
            if (isset($_POST['score'], $_POST['name_dars']) && is_numeric($_POST['score'])) {
                $score = (int)$_POST['score'];
                $name_dars = trim($_POST['name_dars']);

                if ($score < 0 || $score > 20) {
                    $message = "نمره باید بین 0 تا 20 باشد.";
                } elseif (empty($name_dars)) {
                    $message = "لطفاً نام درس را وارد کنید.";
                } else {
                    $current_datetime = date('Y-m-d H:i:s');
                    
                    if (isset($_POST['edit_id']) && !empty($_POST['edit_id'])) {
                        // ویرایش نمره موجود
                        $edit_id = (int)$_POST['edit_id'];
                        $stmtUpdate = $pdo->prepare("UPDATE studennt SET name_dars=?, score=?, date_time=? WHERE id=? AND user_id=?");
                        $stmtUpdate->execute([$name_dars, $score, $current_datetime, $edit_id, $user_id]);
                        $message = "نمره درس {$name_dars} با موفقیت ویرایش شد ✅";
                        $edit_mode = false;
                    } else {
                        // بررسی وجود رکورد قبلی برای همان درس
                        $stmtCheck = $pdo->prepare("SELECT id FROM studennt WHERE user_id=? AND name_dars=?");
                        $stmtCheck->execute([$user_id, $name_dars]);
                        $exists = $stmtCheck->fetch();

                        if ($exists) {
                            // آپدیت نمره
                            $stmtUpdate = $pdo->prepare("UPDATE studennt SET score=?, date_time=? WHERE id=?");
                            $stmtUpdate->execute([$score, $current_datetime, $exists['id']]);
                            $message = "نمره برای درس {$name_dars} با موفقیت به‌روزرسانی شد ✅";
                        } else {
                            // درج نمره جدید
                            $stmtInsert = $pdo->prepare("INSERT INTO studennt (user_id, name_dars, score, date_time) VALUES (?,?,?,?)");
                            $stmtInsert->execute([$user_id, $name_dars, $score, $current_datetime]);
                            $message = "نمره برای درس {$name_dars} با موفقیت ثبت شد ✅";
                        }
                    }
                    
                    // ریدایرکت
                    $redirect_url = "?id=$user_id";
                    if (!empty($selected_date)) {
                        $redirect_url .= "&date_filter=$selected_date";
                    }
                    header("Location: $redirect_url");
                    exit();
                }
            } else {
                $message = "لطفاً درس و نمره معتبر وارد کنید.";
            }
        }
    }
}

// پردازش درخواست‌های GET برای ویرایش و حذف
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'edit' && isset($_GET['score_id'])) {
        $edit_id = (int)$_GET['score_id'];
        $stmt_edit = $pdo->prepare("SELECT name_dars, score FROM studennt WHERE id=? AND user_id=?");
        $stmt_edit->execute([$edit_id, $user_id]);
        $edit_data = $stmt_edit->fetch(PDO::FETCH_ASSOC);
        
        if ($edit_data) {
            $edit_mode = true;
        }
    } elseif ($_GET['action'] === 'delete' && isset($_GET['score_id'])) {
        $delete_id = (int)$_GET['score_id'];
        $stmt_delete = $pdo->prepare("DELETE FROM studennt WHERE id=? AND user_id=?");
        $stmt_delete->execute([$delete_id, $user_id]);
        $message = "نمره با موفقیت حذف شد ✅";
        
        // ریدایرکت
        $redirect_url = "?id=$user_id";
        if (!empty($selected_date)) {
            $redirect_url .= "&date_filter=$selected_date";
        }
        header("Location: $redirect_url");
        exit();
    } elseif ($_GET['action'] === 'clear_filter') {
        // حذف فیلتر تاریخ
        header("Location: ?id=$user_id");
        exit();
    }
}

// بازخوانی داده‌ها بعد از تغییرات
if (!empty($selected_date)) {
    $stmt_scores->execute([$user_id, $selected_date]);
} else {
    $stmt_scores->execute([$user_id]);
}
$scores = $stmt_scores->fetchAll(PDO::FETCH_ASSOC);

// به‌روزرسانی داده‌های چارت
$chart_labels = [];
$chart_data = [];
$chart_dates = [];
foreach($scores as $score) {
    $chart_labels[] = $score['name_dars'];
    $chart_data[] = $score['score'];
    $chart_dates[] = date('Y-m-d H:i', strtotime($score['date_time']));
}

// محاسبه مجدد آمار
$total_scores = count($scores);
$average_score = $total_scores > 0 ? array_sum($chart_data) / $total_scores : 0;
$max_score = $total_scores > 0 ? max($chart_data) : 0;
$min_score = $total_scores > 0 ? min($chart_data) : 0;
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>سیستم مدیریت نمرات</title>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
@import url('https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;600;700&display=swap');

:root {
    --primary-color: #FF6B35;
    --primary-dark: #D45A2C;
    --primary-light: #FF8C5A;
    --accent-color: #FF9F5C;
    --bg-dark: #1A0A00;
    --bg-light: #2C1400;
    --text-light: #FFE8D6;
    --text-dark: #331100;
    --success: #4CAF50;
    --warning: #FF9800;
    --danger: #F44336;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Vazirmatn', sans-serif;
    background: linear-gradient(135deg, var(--bg-dark) 0%, var(--bg-light) 100%);
    color: var(--text-light);
    min-height: 100vh;
    padding: 20px;
    line-height: 1.6;
}

.container {
    max-width: 1400px;
    margin: 0 auto;
    background: rgba(44, 20, 0, 0.7);
    border-radius: 25px;
    padding: 30px;
    box-shadow: 0 15px 35px rgba(255, 107, 53, 0.2);
    border: 2px solid var(--primary-color);
}

/* هدر */
.header {
    text-align: center;
    margin-bottom: 40px;
    padding: 30px;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(255, 107, 53, 0.4);
}

.header h1 {
    color: white;
    font-size: 36px;
    margin-bottom: 20px;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.user-info {
    background: rgba(255, 159, 92, 0.9);
    color: var(--text-dark);
    padding: 15px 25px;
    border-radius: 15px;
    display: inline-block;
    font-weight: 700;
    font-size: 18px;
    box-shadow: 0 5px 15px rgba(255, 107, 53, 0.3);
}

/* بخش فیلتر تاریخ */
.date-filter-section {
    background: linear-gradient(135deg, var(--primary-light), var(--primary-color));
    padding: 25px;
    border-radius: 20px;
    margin-bottom: 30px;
    box-shadow: 0 8px 25px rgba(255, 107, 53, 0.3);
}

.date-filter-section h2 {
    color: white;
    font-size: 24px;
    margin-bottom: 20px;
    text-align: center;
}

.date-filter-form {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
}

.date-select {
    padding: 12px 20px;
    border-radius: 12px;
    border: 2px solid white;
    background: white;
    color: var(--text-dark);
    font-size: 16px;
    font-weight: 600;
    min-width: 200px;
    font-family: 'Vazirmatn', sans-serif;
}

.filter-btn, .clear-btn {
    padding: 12px 25px;
    border-radius: 12px;
    border: none;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 10px;
}

.filter-btn {
    background: linear-gradient(135deg, var(--accent-color), var(--primary-dark));
    color: white;
}

.clear-btn {
    background: linear-gradient(135deg, #FFB347, #FF8C42);
    color: white;
    text-decoration: none;
}

.filter-btn:hover, .clear-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.filter-status {
    text-align: center;
    margin-top: 20px;
    padding: 15px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 15px;
    color: var(--text-dark);
    font-weight: 600;
    box-shadow: 0 5px 15px rgba(255, 107, 53, 0.2);
}

/* کارت‌های آمار */
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.stat-card {
    background: linear-gradient(135deg, var(--primary-light), var(--primary-color));
    padding: 25px;
    border-radius: 20px;
    text-align: center;
    box-shadow: 0 8px 25px rgba(255, 107, 53, 0.3);
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-card i {
    font-size: 40px;
    color: white;
    margin-bottom: 15px;
}

.stat-value {
    font-size: 32px;
    font-weight: 700;
    color: white;
    margin: 10px 0;
}

.stat-label {
    font-size: 18px;
    color: var(--text-dark);
    font-weight: 600;
}

/* بخش‌های اصلی */
.main-sections {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 40px;
}

@media (max-width: 1100px) {
    .main-sections {
        grid-template-columns: 1fr;
    }
}

.section {
    background: rgba(255, 255, 255, 0.05);
    padding: 25px;
    border-radius: 20px;
    border: 2px solid var(--primary-light);
    box-shadow: 0 8px 25px rgba(255, 107, 53, 0.2);
}

.section h2 {
    color: var(--accent-color);
    font-size: 24px;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid var(--primary-light);
    text-align: center;
}

/* فرم ثبت نمره */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: var(--text-light);
    font-weight: 600;
}

.form-control {
    width: 100%;
    padding: 15px;
    border-radius: 12px;
    border: 2px solid var(--primary-light);
    background: rgba(255, 255, 255, 0.1);
    color: var(--text-light);
    font-size: 16px;
    font-family: 'Vazirmatn', sans-serif;
}

.form-control:focus {
    outline: none;
    border-color: var(--accent-color);
    box-shadow: 0 0 10px rgba(255, 107, 53, 0.3);
}

.btn {
    padding: 15px 30px;
    border-radius: 12px;
    border: none;
    font-size: 18px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
}

.btn-secondary {
    background: linear-gradient(135deg, #666, #444);
    color: white;
}

.btn-success {
    background: linear-gradient(135deg, var(--success), #388E3C);
    color: white;
}

.btn-warning {
    background: linear-gradient(135deg, var(--warning), #F57C00);
    color: white;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

/* چارت */
.chart-container {
    height: 400px;
    width: 100%;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    padding: 20px;
}

/* جدول نمرات */
.table-container {
    margin-top: 40px;
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    overflow: hidden;
}

.table th {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    padding: 20px;
    text-align: center;
    font-weight: 700;
    font-size: 18px;
}

.table td {
    padding: 15px;
    text-align: center;
    border-bottom: 1px solid rgba(255, 107, 53, 0.2);
    color: var(--text-light);
}

.table tr:hover {
    background: rgba(255, 107, 53, 0.1);
}

.score-badge {
    display: inline-block;
    padding: 8px 15px;
    border-radius: 20px;
    font-weight: 700;
    font-size: 16px;
}

.score-high {
    background: linear-gradient(135deg, var(--success), #388E3C);
    color: white;
}

.score-medium {
    background: linear-gradient(135deg, var(--warning), #F57C00);
    color: white;
}

.score-low {
    background: linear-gradient(135deg, var(--danger), #D32F2F);
    color: white;
}

.action-buttons {
    display: flex;
    gap: 10px;
    justify-content: center;
}

.action-btn {
    padding: 8px 15px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.action-btn-edit {
    background: linear-gradient(135deg, #4CAF50, #388E3C);
    color: white;
}

.action-btn-delete {
    background: linear-gradient(135deg, #F44336, #D32F2F);
    color: white;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
}

/* پیام‌ها */
.message {
    padding: 20px;
    border-radius: 15px;
    margin: 20px 0;
    text-align: center;
    font-weight: 700;
    font-size: 18px;
    animation: slideIn 0.5s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.message-success {
    background: linear-gradient(135deg, #4CAF50, #388E3C);
    color: white;
    box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
}

.message-error {
    background: linear-gradient(135deg, #F44336, #D32F2F);
    color: white;
    box-shadow: 0 5px 15px rgba(244, 67, 54, 0.3);
}

.message-warning {
    background: linear-gradient(135deg, #FF9800, #F57C00);
    color: white;
    box-shadow: 0 5px 15px rgba(255, 152, 0, 0.3);
}

/* حالت ویرایش */
.edit-mode-notice {
    background: linear-gradient(135deg, #FF9800, #F57C00);
    color: white;
    padding: 15px;
    border-radius: 12px;
    text-align: center;
    margin-bottom: 20px;
    font-weight: 700;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 0.8; }
    50% { opacity: 1; }
    100% { opacity: 0.8; }
}

/* دکمه بازگشت */
.back-button {
    position: fixed;
    bottom: 30px;
    left: 30px;
    padding: 15px 30px;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    border: none;
    border-radius: 50px;
    font-weight: 700;
    font-size: 16px;
    cursor: pointer;
    box-shadow: 0 5px 20px rgba(255, 107, 53, 0.4);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 10px;
    z-index: 1000;
}

.back-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(255, 107, 53, 0.6);
}

/* ریسپانسیو */
@media (max-width: 768px) {
    .container {
        padding: 15px;
    }
    
    .header h1 {
        font-size: 28px;
    }
    
    .main-sections {
        gap: 20px;
    }
    
    .date-filter-form {
        flex-direction: column;
    }
    
    .date-select, .filter-btn, .clear-btn {
        width: 100%;
    }
    
    .stats-container {
        grid-template-columns: 1fr;
    }
    
    .table {
        font-size: 14px;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 8px;
    }
    
    .action-btn {
        width: 100%;
        justify-content: center;
    }
    
    .back-button {
        bottom: 20px;
        left: 20px;
        padding: 12px 25px;
        font-size: 14px;
    }
}
</style>
</head>
<body>

<div class="container">
    <!-- هدر -->
    <div class="header">
        <h1><i class="fas fa-graduation-cap"></i> سیستم مدیریت نمرات <i class="fas fa-chart-line"></i></h1>
        <div class="user-info">
            <i class="fas fa-user"></i> 
            دانش‌آموز: <?php echo htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']); ?> 
            | کد: <?php echo en_to_fa($user_id); ?>
        </div>
    </div>

    <!-- پیام‌ها -->
    <?php if($message): ?>
    <div class="message <?php echo strpos($message, '✅') !== false ? 'message-success' : (strpos($message, 'خطا') !== false ? 'message-error' : 'message-warning'); ?>">
        <i class="fas fa-bell"></i> <?php echo htmlspecialchars($message); ?>
    </div>
    <?php endif; ?>

    <!-- بخش فیلتر تاریخ -->
    <div class="date-filter-section">
        <h2><i class="fas fa-filter"></i> فیلتر بر اساس تاریخ</h2>
        
        <form method="get" class="date-filter-form">
            <input type="hidden" name="id" value="<?php echo $user_id; ?>">
            
            <select name="date_filter" class="date-select">
                <option value="">-- همه تاریخ‌ها --</option>
                <?php foreach($available_dates as $date): 
                    $date_display = date('Y/m/d', strtotime($date));
                    $date_fa = en_to_fa($date_display);
                ?>
                    <option value="<?php echo $date; ?>" <?php echo $selected_date == $date ? 'selected' : ''; ?>>
                        <?php echo $date_fa; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <button type="submit" class="filter-btn">
                <i class="fas fa-filter"></i> اعمال فیلتر
            </button>
            
            <?php if(!empty($selected_date)): ?>
                <a href="?id=<?php echo $user_id; ?>&action=clear_filter" class="clear-btn">
                    <i class="fas fa-times"></i> حذف فیلتر
                </a>
            <?php endif; ?>
        </form>
        
        <?php if(!empty($selected_date)): ?>
            <div class="filter-status">
                <i class="fas fa-calendar-check"></i> 
                در حال نمایش نمرات تاریخ: 
                <strong><?php echo en_to_fa(date('Y/m/d', strtotime($selected_date))); ?></strong>
            </div>
        <?php endif; ?>
    </div>

    <!-- آمار کلی -->
    <div class="stats-container">
        <div class="stat-card">
            <i class="fas fa-calculator"></i>
            <div class="stat-value"><?php echo en_to_fa(number_format($average_score, 1)); ?></div>
            <div class="stat-label">میانگین نمرات</div>
        </div>
        
        <div class="stat-card">
            <i class="fas fa-arrow-up"></i>
            <div class="stat-value"><?php echo en_to_fa($max_score); ?></div>
            <div class="stat-label">بیشترین نمره</div>
        </div>
        
        <div class="stat-card">
            <i class="fas fa-arrow-down"></i>
            <div class="stat-value"><?php echo en_to_fa($min_score); ?></div>
            <div class="stat-label">کمترین نمره</div>
        </div>
        
        <div class="stat-card">
            <i class="fas fa-list-ol"></i>
            <div class="stat-value"><?php echo en_to_fa($total_scores); ?></div>
            <div class="stat-label">تعداد نمرات</div>
        </div>
    </div>

    <!-- بخش‌های اصلی -->
    <div class="main-sections">
        <!-- بخش فرم ثبت/ویرایش نمره -->
        <div class="section">
            <h2>
                <?php if($edit_mode): ?>
                    <i class="fas fa-edit"></i> ویرایش نمره
                <?php else: ?>
                    <i class="fas fa-plus-circle"></i> ثبت نمره جدید
                <?php endif; ?>
            </h2>
            
            <?php if($edit_mode): ?>
                <div class="edit-mode-notice">
                    <i class="fas fa-exclamation-triangle"></i> حالت ویرایش فعال است
                </div>
            <?php endif; ?>
            
            <form method="post">
                <input type="hidden" name="action" value="save_score">
                <?php if($edit_mode): ?>
                    <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="name_dars"><i class="fas fa-book"></i> نام درس:</label>
                    <input list="lessons" name="name_dars" id="name_dars" class="form-control" 
                           placeholder="انتخاب یا تایپ نام درس"
                           value="<?php echo $edit_mode ? htmlspecialchars($edit_data['name_dars']) : ''; ?>" 
                           required>
                    <datalist id="lessons">
                        <?php foreach($lessons as $lesson): ?>
                            <option value="<?php echo $lesson; ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>
                
                <div class="form-group">
                    <label for="score"><i class="fas fa-star"></i> نمره (0 تا 20):</label>
                    <input type="number" name="score" id="score" class="form-control" 
                           min="0" max="20" step="0.5" placeholder="مثلاً ۱۷.۵"
                           value="<?php echo $edit_mode ? $edit_data['score'] : ''; ?>" 
                           required>
                </div>
                
                <?php if($edit_mode): ?>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> ذخیره تغییرات
                    </button>
                    <a href="?id=<?php echo $user_id; ?><?php echo !empty($selected_date) ? '&date_filter=' . $selected_date : ''; ?>" 
                       class="btn btn-secondary" style="margin-top: 10px;">
                        <i class="fas fa-times"></i> لغو ویرایش
                    </a>
                <?php else: ?>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check-circle"></i> ثبت نمره
                    </button>
                <?php endif; ?>
            </form>
        </div>

        <!-- بخش نمودار -->
        <div class="section">
            <h2><i class="fas fa-chart-bar"></i> نمودار نمرات</h2>
            <div class="chart-container">
                <canvas id="scoreChart"></canvas>
            </div>
        </div>
    </div>

    <!-- جدول نمرات -->
    <div class="section">
        <h2><i class="fas fa-table"></i> لیست نمرات</h2>
        
        <?php if($total_scores > 0): ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>نام درس</th>
                            <th>نمره</th>
                            <th>وضعیت</th>
                            <th>تاریخ ثبت</th>
                            <th>ساعت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($scores as $index => $score): 
                            $score_num = $score['score'];
                            $score_class = $score_num >= 15 ? 'score-high' : 
                                         ($score_num >= 10 ? 'score-medium' : 'score-low');
                            $score_status = $score_num >= 15 ? 'عالی' : 
                                          ($score_num >= 10 ? 'متوسط' : 'نیاز به تلاش');
                            
                            $date_parts = explode(' ', $score['date_time']);
                            $date_fa = en_to_fa(date('Y/m/d', strtotime($date_parts[0])));
                            $time_fa = en_to_fa(date('H:i', strtotime($date_parts[1])));
                        ?>
                        <tr>
                            <td><?php echo en_to_fa($index + 1); ?></td>
                            <td><strong><?php echo htmlspecialchars($score['name_dars']); ?></strong></td>
                            <td>
                                <span class="score-badge <?php echo $score_class; ?>">
                                    <?php echo en_to_fa($score_num); ?>
                                </span>
                            </td>
                            <td><?php echo $score_status; ?></td>
                            <td><?php echo $date_fa; ?></td>
                            <td><?php echo $time_fa; ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?id=<?php echo $user_id; ?>&action=edit&score_id=<?php echo $score['id']; ?><?php echo !empty($selected_date) ? '&date_filter=' . $selected_date : ''; ?>" 
                                       class="action-btn action-btn-edit">
                                        <i class="fas fa-edit"></i> ویرایش
                                    </a>
                                    <a href="?id=<?php echo $user_id; ?>&action=delete&score_id=<?php echo $score['id']; ?><?php echo !empty($selected_date) ? '&date_filter=' . $selected_date : ''; ?>" 
                                       class="action-btn action-btn-delete"
                                       onclick="return confirm('آیا از حذف این نمره اطمینان دارید؟')">
                                        <i class="fas fa-trash"></i> حذف
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; background: rgba(255,255,255,0.05); border-radius: 15px;">
                <i class="fas fa-clipboard-list" style="font-size: 60px; color: var(--primary-light); margin-bottom: 20px;"></i>
                <h3 style="color: var(--text-light); margin-bottom: 15px;">هنوز نمره‌ای ثبت نشده است</h3>
                <p style="color: var(--text-light); opacity: 0.8;">برای شروع، یک نمره جدید در بخش بالا ثبت کنید.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- دکمه بازگشت -->
<button class="back-button" onclick="window.history.back();">
    <i class="fas fa-arrow-right"></i> بازگشت
</button>

<script>
// ایجاد نمودار
const ctx = document.getElementById('scoreChart').getContext('2d');
const scoreChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($chart_labels); ?>,
        datasets: [{
            label: 'نمرات',
            data: <?php echo json_encode($chart_data); ?>,
            backgroundColor: [
                '#FF6B35', '#FF8C5A', '#FF9F5C', '#FFB347',
                '#FF6B35', '#FF8C5A', '#FF9F5C', '#FFB347'
            ],
            borderColor: '#FFFFFF',
            borderWidth: 2,
            borderRadius: 8,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    color: '#FFE8D6',
                    font: {
                        size: 14,
                        family: 'Vazirmatn'
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'نمره: ' + context.raw;
                    }
                },
                backgroundColor: 'rgba(255, 107, 53, 0.9)',
                titleColor: '#fff',
                bodyColor: '#fff'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 20,
                ticks: {
                    color: '#FFE8D6',
                    font: {
                        size: 14,
                        family: 'Vazirmatn'
                    }
                },
                grid: {
                    color: 'rgba(255, 107, 53, 0.2)'
                }
            },
            x: {
                ticks: {
                    color: '#FFE8D6',
                    font: {
                        size: 14,
                        family: 'Vazirmatn'
                    }
                },
                grid: {
                    color: 'rgba(255, 107, 53, 0.2)'
                }
            }
        }
    }
});

// اگر در حالت ویرایش هستیم، اسکرول به فرم
<?php if($edit_mode): ?>
setTimeout(() => {
    document.querySelector('.section:first-child').scrollIntoView({
        behavior: 'smooth',
        block: 'center'
    });
}, 300);
<?php endif; ?>

// اضافه کردن انیمیشن به کارت‌ها
document.querySelectorAll('.stat-card').forEach((card, index) => {
    card.style.animationDelay = `${index * 0.1}s`;
});

// تغییر رنگ نمرات در جدول بر اساس مقدار
document.querySelectorAll('.score-badge').forEach(badge => {
    const score = parseInt(badge.textContent);
    if (score >= 18) {
        badge.style.background = 'linear-gradient(135deg, #4CAF50, #2E7D32)';
    } else if (score >= 15) {
        badge.style.background = 'linear-gradient(135deg, #8BC34A, #689F38)';
    } else if (score >= 12) {
        badge.style.background = 'linear-gradient(135deg, #FFC107, #FFA000)';
    } else if (score >= 10) {
        badge.style.background = 'linear-gradient(135deg, #FF9800, #F57C00)';
    } else {
        badge.style.background = 'linear-gradient(135deg, #F44336, #C62828)';
    }
});
</script>

</body>
</html>
