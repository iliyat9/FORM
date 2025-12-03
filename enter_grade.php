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

// گرفتن نمرات ثبت‌شده برای چارت و جدول
$stmt_scores = $pdo->prepare("SELECT id, name_dars, score, date_time FROM studennt WHERE user_id=? ORDER BY date_time DESC");
$stmt_scores->execute([$user_id]);
$scores = $stmt_scores->fetchAll(PDO::FETCH_ASSOC);

// داده‌های برای چارت
$chart_labels = [];
$chart_data = [];
$chart_dates = [];
foreach($scores as $score) {
    $chart_labels[] = $score['name_dars'];
    $chart_data[] = $score['score'];
    $chart_dates[] = date('Y-m-d H:i', strtotime($score['date_time']));
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
                    
                    // بازخوانی نمرات
                    $stmt_scores->execute([$user_id]);
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
        
        // بازخوانی نمرات
        $stmt_scores->execute([$user_id]);
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
    }
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>مدیریت نمرات - مشکی قرمز</title>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;600&display=swap');

body {
    margin: 0;
    padding: 0;
    font-family: 'Vazirmatn', sans-serif;
    background: #111;
    color: #fff;
    min-height: 100vh;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.header {
    text-align: center;
    margin-bottom: 30px;
}

h1 {
    color: #ff4d4d;
    font-size: 32px;
    text-shadow: 0 0 10px #ff1a1a88;
    margin-bottom: 10px;
}

.user-info {
    background: #220000;
    padding: 15px;
    border-radius: 10px;
    display: inline-block;
    box-shadow: 0 0 15px #ff1a1aaa;
    margin-bottom: 20px;
}

.main-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 30px;
}

@media (max-width: 768px) {
    .main-content {
        grid-template-columns: 1fr;
    }
}

.form-section, .chart-section {
    background: #1a1a1a;
    padding: 25px;
    border-radius: 20px;
    box-shadow: 0 0 25px #ff1a1a99, 0 0 15px #ff4d4d55 inset;
}

.section-title {
    color: #ff4d4d;
    font-size: 22px;
    margin-bottom: 20px;
    text-align: center;
    border-bottom: 2px solid #ff4d4d;
    padding-bottom: 10px;
}

input, button {
    width: 100%;
    padding: 14px;
    margin: 12px 0;
    border-radius: 12px;
    border: none;
    font-size: 16px;
    box-sizing: border-box;
    transition: all 0.3s ease;
}

input[list], input[type="number"], input[type="text"] {
    background: #222;
    color: #ff4d4d;
    box-shadow: 0 0 8px #ff1a1a55, inset 0 0 5px #ff4d4d22;
    text-align: center;
}

input:focus {
    outline: none;
    box-shadow: 0 0 15px #ff1a1a, inset 0 0 5px #ff4d4d44;
    transform: scale(1.02);
}

.btn-primary {
    background: linear-gradient(145deg, #ff1a1a, #b30000);
    color: #fff;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 0 20px #ff1a1aaa, inset 0 0 8px #ff4d4d33;
}

.btn-primary:hover {
    background: linear-gradient(145deg, #b30000, #ff1a1a);
    box-shadow: 0 0 35px #ff4d4dbb, inset 0 0 12px #ff1a1a55;
    transform: translateY(-2px) scale(1.02);
}

.btn-secondary {
    background: linear-gradient(145deg, #333, #222);
    color: #ff4d4d;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 0 15px #ff1a1a77;
}

.btn-secondary:hover {
    background: linear-gradient(145deg, #444, #333);
    box-shadow: 0 0 25px #ff4d4daa;
}

.btn-danger {
    background: linear-gradient(145deg, #990000, #660000);
    color: #fff;
    font-weight: 600;
    cursor: pointer;
}

.btn-danger:hover {
    background: linear-gradient(145deg, #cc0000, #990000);
}

.message {
    margin: 15px 0;
    padding: 12px;
    border-radius: 14px;
    font-weight: 600;
    animation: fadeIn 0.6s;
}

.success {
    background:#330000; 
    color:#ff4d4d; 
    box-shadow: 0 0 15px #ff4d4daa;
}

.error { 
    background:#4d0000; 
    color:#ff9999; 
    box-shadow: 0 0 15px #ff6666; 
}

.table-container {
    margin-top: 30px;
    overflow-x: auto;
}

.scores-table {
    width: 100%;
    border-collapse: collapse;
    background: #1a1a1a;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 0 20px #ff1a1a66;
}

.scores-table th {
    background: #ff1a1a;
    color: white;
    padding: 15px;
    text-align: center;
}

.scores-table td {
    padding: 12px;
    text-align: center;
    border-bottom: 1px solid #333;
}

.scores-table tr:hover {
    background: #220000;
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
    transition: all 0.3s;
}

.edit-btn {
    background: #ffcc00;
    color: #000;
}

.edit-btn:hover {
    background: #ffdb4d;
}

.delete-btn {
    background: #ff3333;
    color: white;
}

.delete-btn:hover {
    background: #ff6666;
}

.chart-container {
    position: relative;
    height: 400px;
    width: 100%;
}

.back-button {
    position: fixed;
    bottom: 20px;
    left: 20px;
    padding: 12px 25px;
    background: linear-gradient(145deg, #333, #222);
    color: #ff4d4d;
    border: none;
    border-radius: 50px;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 0 20px #ff1a1a99;
    z-index: 100;
}

.back-button:hover {
    background: linear-gradient(145deg, #444, #333);
    transform: translateY(-2px);
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.edit-mode {
    border: 2px solid #ffcc00;
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(255, 204, 0, 0.4); }
    70% { box-shadow: 0 0 0 10px rgba(255, 204, 0, 0); }
    100% { box-shadow: 0 0 0 0 rgba(255, 204, 0, 0); }
}
</style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>📊 سیستم مدیریت نمرات</h1>
        <div class="user-info">
            <strong>نام دانش‌آموز:</strong> <?php echo htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']); ?><br>
            <strong>کد دانش‌آموزی:</strong> <?php echo $user_id; ?>
        </div>
    </div>

    <?php if($message): ?>
    <div class="message <?php echo strpos($message,'موفقیت')!==false || strpos($message,'حذف')!==false ? 'success':'error'; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
    <?php endif; ?>

    <div class="main-content">
        <!-- بخش فرم ثبت/ویرایش -->
        <div class="form-section <?php echo $edit_mode ? 'edit-mode' : ''; ?>">
            <h2 class="section-title"><?php echo $edit_mode ? '✏️ ویرایش نمره' : '📝 ثبت نمره جدید'; ?></h2>
            
            <form method="post">
                <input type="hidden" name="action" value="save_score">
                <?php if($edit_mode): ?>
                    <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
                    <div style="background: #332200; padding: 10px; border-radius: 10px; margin-bottom: 15px; text-align: center;">
                        🔔 حالت ویرایش فعال است
                    </div>
                <?php endif; ?>
                
                <input list="lessons" name="name_dars" placeholder="انتخاب یا تایپ درس" 
                       value="<?php echo $edit_mode ? htmlspecialchars($edit_data['name_dars']) : ''; ?>" required>
                <datalist id="lessons">
                    <?php foreach($lessons as $lesson): ?>
                        <option value="<?php echo $lesson; ?>">
                    <?php endforeach; ?>
                </datalist>
                
                <input type="number" name="score" min="0" max="20" placeholder="نمره (0 تا 20)" 
                       value="<?php echo $edit_mode ? $edit_data['score'] : ''; ?>" required>
                
                <button type="submit" class="btn-primary">
                    <?php echo $edit_mode ? '💾 ذخیره تغییرات' : '✅ ثبت نمره'; ?>
                </button>
                
                <?php if($edit_mode): ?>
                    <a href="?id=<?php echo $user_id; ?>" class="btn-secondary" style="display: block; text-decoration: none; text-align: center;">
                        ❌ لغو ویرایش
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- بخش چارت -->
        <div class="chart-section">
            <h2 class="section-title">📈 نمودار نمرات</h2>
            <div class="chart-container">
                <canvas id="scoreChart"></canvas>
            </div>
        </div>
    </div>

    <!-- بخش جدول نمرات -->
    <div class="table-container">
        <h2 class="section-title">📋 لیست نمرات ثبت شده</h2>
        
        <?php if(count($scores) > 0): ?>
            <table class="scores-table">
                <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>نام درس</th>
                        <th>نمره</th>
                        <th>تاریخ ویرایش</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($scores as $index => $score): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($score['name_dars']); ?></td>
                        <td><strong style="color: <?php echo $score['score'] >= 10 ? '#4CAF50' : '#ff4d4d'; ?>">
                            <?php echo $score['score']; ?>
                        </strong></td>
                        <td><?php echo date('Y/m/d H:i', strtotime($score['date_time'])); ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="?id=<?php echo $user_id; ?>&action=edit&score_id=<?php echo $score['id']; ?>" 
                                   class="action-btn edit-btn">✏️ ویرایش</a>
                                <a href="?id=<?php echo $user_id; ?>&action=delete&score_id=<?php echo $score['id']; ?>" 
                                   class="action-btn delete-btn" 
                                   onclick="return confirm('آیا از حذف این نمره مطمئن هستید؟')">🗑️ حذف</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 30px; background: #1a1a1a; border-radius: 10px;">
                📭 هنوز نمره‌ای ثبت نشده است
            </div>
        <?php endif; ?>
    </div>
</div>


<script>
// ایجاد چارت با Chart.js
const ctx = document.getElementById('scoreChart').getContext('2d');
const scoreChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($chart_labels); ?>,
        datasets: [{
            label: 'نمرات',
            data: <?php echo json_encode($chart_data); ?>,
            backgroundColor: [
                'rgba(255, 77, 77, 0.8)',
                'rgba(255, 26, 26, 0.8)',
                'rgba(255, 102, 102, 0.8)',
                'rgba(255, 51, 51, 0.8)',
                'rgba(204, 0, 0, 0.8)',
                'rgba(153, 0, 0, 0.8)',
                'rgba(102, 0, 0, 0.8)'
            ],
            borderColor: [
                'rgba(255, 77, 77, 1)',
                'rgba(255, 26, 26, 1)',
                'rgba(255, 102, 102, 1)',
                'rgba(255, 51, 51, 1)',
                'rgba(204, 0, 0, 1)',
                'rgba(153, 0, 0, 1)',
                'rgba(102, 0, 0, 1)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                max: 20,
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                },
                ticks: {
                    color: '#fff'
                }
            },
            x: {
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                },
                ticks: {
                    color: '#fff'
                }
            }
        },
        plugins: {
            legend: {
                labels: {
                    color: '#fff'
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const index = context.dataIndex;
                        const date = <?php echo json_encode($chart_dates); ?>[index];
                        return `نمره: ${context.raw} - تاریخ: ${date}`;
                    }
                }
            }
        }
    }
});

// اگر در حالت ویرایش هستیم، اسکرول به فرم
<?php if($edit_mode): ?>
document.querySelector('.form-section').scrollIntoView({behavior: 'smooth'});
<?php endif; ?>
</script>

</body>
</html>
