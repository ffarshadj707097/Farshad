<?php
$page_title = 'پروفایل کاربر';
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// بررسی لاگین بودن کاربر
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// اتصال به پایگاه داده
try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=bageetir_root;charset=utf8mb4',
        'bageetir_root',
        'F@rs0553',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die("خطا در اتصال به پایگاه داده: " . $e->getMessage());
}

// تابع تبدیل اعداد به فارسی
function toPersianNumbers($number) {
    $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    return str_replace($english, $persian, $number);
}

// دریافت اطلاعات کاربر
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// تعیین وضعیت حساب و رنگ مربوطه
$account_status = $user['account_status'] ?? 'pending';
$status_colors = [
    'verified' => 'text-success',
    'rejected' => 'text-danger',
    'pending' => 'text-warning',
    'under_review' => 'text-info'
];
$status_messages = [
    'verified' => 'تایید شده است',
    'rejected' => 'رد شده است',
    'pending' => 'تایید نشده است',
    'under_review' => 'در حال بررسی دقیق است'
];

// دریافت آخرین تراکنش‌ها
$transactions = [];
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$transactions = $stmt->fetchAll();

// دریافت آخرین وام‌ها
$loans = [];
$stmt = $pdo->prepare("SELECT * FROM loans WHERE user_id = ? ORDER BY created_at DESC LIMIT 3");
$stmt->execute([$user_id]);
$loans = $stmt->fetchAll();

// دریافت آخرین سرمایه‌گذاری‌ها
$investments = [];
$stmt = $pdo->prepare("SELECT * FROM investments WHERE user_id = ? ORDER BY created_at DESC LIMIT 3");
$stmt->execute([$user_id]);
$investments = $stmt->fetchAll();
require_once 'includes/jdf.php';
require_once 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/styles.css">

<main class="profile-page">
    <div class="container">
        <!-- بخش هدر پروفایل -->
        <div class="profile-header">
            <div class="profile-cover">
                <div class="profile-avatar">
                   <?php
$avatarPath = $user['avatar'] ? 'images/'.$user['avatar'] : 'images/default-avatar.png';
$fullPath = $_SERVER['DOCUMENT_ROOT'].'/'.$avatarPath;
$finalAvatar = file_exists($fullPath) ? $avatarPath : 'images/default-avatar.png';
?>
<img src="<?php echo $finalAvatar; ?>" alt="پروفایل کاربر">
<button class="btn-edit-avatar" id="editAvatarBtn">
                        <i class="fas fa-camera"></i>
                    </button>
                </div>
            </div>
            
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h1>
                <p class="profile-bio"><?php echo htmlspecialchars($user['bio'] ?? 'کاربر سامانه باجیت'); ?></p>
                
                <!-- نمایش وضعیت حساب -->
                <div class="account-status">
                    وضعیت حساب شما 
                    <span class="<?php echo $status_colors[$account_status]; ?>">
                        <?php echo $status_messages[$account_status]; ?>
                    </span>
                </div>
                
                <div class="profile-stats">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo toPersianNumbers(number_format($user['credit_score'] ?? 0)); ?></span>
                        <span class="stat-label">امتیاز اعتباری</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo toPersianNumbers(count($loans)); ?></span>
                        <span class="stat-label">وام دریافتی</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo toPersianNumbers(count($investments)); ?></span>
                        <span class="stat-label">سرمایه‌گذاری</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- بخش اصلی پروفایل -->
        <div class="profile-content">
            <div class="profile-sidebar">
                <!-- اطلاعات شخصی -->
                <div class="profile-card">
                    <div class="card-header">
                        <h3>اطلاعات شخصی</h3>
                        <a href="edit-profile.php" class="btn-edit">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        <ul class="profile-details">
                            <li>
                                <i class="fas fa-mobile-alt"></i>
                                <span><?php echo toPersianNumbers(htmlspecialchars($user['mobile'])); ?></span>
                            </li>
                            <?php if (!empty($user['email'])): ?>
                            <li>
                                <i class="fas fa-envelope"></i>
                                <span><?php echo htmlspecialchars($user['email']); ?></span>
                            </li>
                            <?php endif; ?>
                            <?php if (!empty($user['national_id'])): ?>
                            <li>
                                <i class="fas fa-id-card"></i>
                                <span><?php echo toPersianNumbers(htmlspecialchars($user['national_id'])); ?></span>
                            </li>
                            <?php endif; ?>
                            <li>
                                <i class="fas fa-calendar-alt"></i>
                                <span>تاریخ عضویت: <?php echo toPersianNumbers(jdate('Y/m/d', strtotime($user['created_at']))); ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- کیف پول -->
                <div class="profile-card wallet-card">
                    <div class="card-header">
                        <h3>کیف پول</h3>
                        <a href="wallet.php" class="btn-edit">
                            <i class="fas fa-wallet"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="wallet-balance">
                            <span class="balance-amount"><?php echo toPersianNumbers(number_format($user['wallet_balance'] ?? 0)); ?></span>
                            <span class="currency">تومان</span>
                        </div>
                        <div class="wallet-actions">
                            <a href="deposit.php" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> افزایش موجودی
                            </a>
                            <a href="withdraw.php" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-minus"></i> برداشت
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="profile-main">
                <!-- آخرین تراکنش‌ها -->
                <div class="profile-card">
                    <div class="card-header">
                        <h3>آخرین تراکنش‌ها</h3>
                        <a href="transactions.php" class="btn-view-all">مشاهده همه</a>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($transactions)): ?>
                            <div class="transactions-list">
                                <?php foreach ($transactions as $transaction): ?>
                                <div class="transaction-item">
                                    <div class="transaction-icon">
                                        <i class="fas <?php 
                                            echo $transaction['type'] === 'deposit' ? 'fa-arrow-down text-success' : 
                                                ($transaction['type'] === 'withdraw' ? 'fa-arrow-up text-danger' : 'fa-exchange-alt text-info'); 
                                        ?>"></i>
                                    </div>
                                    <div class="transaction-details">
                                        <h4><?php echo htmlspecialchars($transaction['description']); ?></h4>
                                        <small><?php echo toPersianNumbers(jdate('Y/m/d H:i', strtotime($transaction['created_at']))); ?></small>
                                    </div>
                                    <div class="transaction-amount <?php echo $transaction['type'] === 'deposit' ? 'text-success' : 'text-danger'; ?>">
                                        <?php echo ($transaction['type'] === 'deposit' ? '+' : '-') . toPersianNumbers(number_format($transaction['amount'])); ?> تومان
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-exchange-alt"></i>
                                <p>تراکنشی یافت نشد</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="profile-grid">
                    <!-- آخرین وام‌ها -->
                    <div class="profile-card">
                        <div class="card-header">
                            <h3>آخرین وام‌ها</h3>
                            <a href="loans.php" class="btn-view-all">مشاهده همه</a>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($loans)): ?>
                                <div class="loans-list">
                                    <?php foreach ($loans as $loan): ?>
                                    <div class="loan-item">
                                        <div class="loan-icon">
                                            <i class="fas fa-hand-holding-usd"></i>
                                        </div>
                                        <div class="loan-details">
                                            <h4>وام <?php echo toPersianNumbers(number_format($loan['amount'])); ?> تومان</h4>
                                            <small>
                                                وضعیت: 
                                                <span class="<?php 
                                                    echo $loan['status'] === 'paid' ? 'text-success' : 
                                                        ($loan['status'] === 'pending' ? 'text-warning' : 'text-danger'); 
                                                ?>">
                                                    <?php 
                                                        echo $loan['status'] === 'paid' ? 'تسویه شده' : 
                                                            ($loan['status'] === 'pending' ? 'در انتظار تایید' : 'در جریان'); 
                                                    ?>
                                                </span>
                                            </small>
                                        </div>
                                        <div class="loan-amount">
                                            <?php echo toPersianNumbers(number_format($loan['installment_amount'])); ?> تومان
                                            <small>هر قسط</small>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-hand-holding-usd"></i>
                                    <p>وام ثبت شده‌ای ندارید</p>
                                    <a href="apply-loan.php" class="btn btn-primary">درخواست وام جدید</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- آخرین سرمایه‌گذاری‌ها -->
                    <div class="profile-card">
                        <div class="card-header">
                            <h3>آخرین سرمایه‌گذاری‌ها</h3>
                            <a href="investments.php" class="btn-view-all">مشاهده همه</a>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($investments)): ?>
                                <div class="investments-list">
                                    <?php foreach ($investments as $investment): ?>
                                    <div class="investment-item">
                                        <div class="investment-icon">
                                            <i class="fas fa-chart-line"></i>
                                        </div>
                                        <div class="investment-details">
                                            <h4><?php echo toPersianNumbers(number_format($investment['amount'])); ?> تومان</h4>
                                            <small>
                                          سود: <?php echo toPersianNumbers($investment['profit_percentage']); ?>٪ - 
                                                <span class="<?php 
                                                    echo $investment['status'] === 'active' ? 'text-success' : 
                                                        ($investment['status'] === 'pending' ? 'text-warning' : 'text-danger'); 
                                                ?>">
                                                    <?php 
                                                        echo $investment['status'] === 'active' ? 'فعال' : 
                                                            ($investment['status'] === 'pending' ? 'در انتظار' : 'پایان یافته'); 
                                                    ?>
                                                </span>
                                            </small>
                                        </div>
                                        <div class="investment-profit">
                                            <?php echo toPersianNumbers(number_format($investment['estimated_profit'])); ?> تومان
                                            <small>سود تخمینی</small>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-chart-line"></i>
                                    <p>سرمایه‌گذاری ثبت شده‌ای ندارید</p>
                                    <a href="investment.php" class="btn btn-primary">سرمایه‌گذاری جدید</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="assets/js/profile.js"></script>

<?php
require_once 'includes/footer.php';
?>