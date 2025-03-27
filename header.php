
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);


// اتصال مستقیم به پایگاه داده
$db_host = 'localhost';
$db_name = 'bageetir_root';
$db_user = 'bageetir_root';
$db_pass = 'F@rs0553';
$db_charset = 'utf8mb4';

try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=$db_charset";
    $conn = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die("اتصال به پایگاه داده ناموفق بود: " . $e->getMessage());
}

// بررسی لاگین بودن کاربر
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? $_SESSION['user_name'] : '';
$user_avatar = $is_logged_in ? ($_SESSION['avatar'] ?? 'images/default-avatar.png') : '';
$unread_notifications = $is_logged_in ? getUnreadNotificationCount($_SESSION['user_id']) : 0;

// تابع کمک‌کننده برای دریافت تعداد اعلان‌های خوانده نشده
function getUnreadNotificationCount($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn();
}
?>


<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>باجیت - سامانه اعتباری هوشمند</title>
    
    <!-- فونت‌ها -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/vazirmatn@5.0.1/index.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="icon" href="assets/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/apple-touch-icon.png">
    
    <!-- متا تگ‌های SEO -->
    <meta name="description" content="سامانه هوشمند اعتبار و سرمایه‌گذاری باجیت - دریافت وام آنلاین، سرمایه‌گذاری مطمئن">
    <meta name="keywords" content="وام آنلاین, اعتبار سنجی, سرمایه‌گذاری, وام فوری, وام بدون ضامن">
    <meta name="author" content="Bageet Team">
    
    <!-- Open Graph / Social Media -->
    <meta property="og:title" content="باجیت - سامانه اعتباری هوشمند">
    <meta property="og:description" content="دریافت وام آنلاین با سود کم و سرمایه‌گذاری مطمئن با سود بالا">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://bageet.com">
    <meta property="og:image" content="https://bageet.com/assets/images/og-image.jpg">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="https://bageet.com<?php echo $_SERVER['REQUEST_URI']; ?>">
    
    <style>
/* استایل‌های صفحه پروفایل */
.profile-page {
    padding: 100px 0 50px;
    font-family: 'Vazirmatn', sans-serif;
    background-color: #f5f7fa;
}

.profile-header {
    position: relative;
    margin-bottom: 30px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.profile-cover {
    height: 150px;
    background: linear-gradient(135deg, #4a6bff, #3a5bef);
    position: relative;
}

.profile-avatar {
    position: absolute;
    bottom: -50px;
    right: 50px;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 5px solid white;
    background: white;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.btn-edit-avatar {
    position: absolute;
    bottom: 5px;
    left: 5px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #4a6bff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    cursor: pointer;
}

.profile-info {
    padding: 80px 30px 30px;
    text-align: center;
}

.profile-info h1 {
    font-size: 1.8rem;
    color: #2c3e50;
    margin-bottom: 10px;
}

.profile-bio {
    color: #7f8c8d;
    max-width: 600px;
    margin: 0 auto 20px;
}

.profile-stats {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin-top: 20px;
}

.stat-item {
    text-align: center;
}



.stat-label {
    font-size: 0.9rem;
    color: white;
}

.profile-content {
    display: flex;
    gap: 30px;
}

.profile-sidebar {
    flex: 0 0 300px;
}

.profile-main {
    flex: 1;
}

.profile-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    margin-bottom: 30px;
    overflow: hidden;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #eee;
}

.card-header h3 {
    font-size: 1.2rem;
    color: #2c3e50;
    margin: 0;
}

.btn-edit {
    color: #7f8c8d;
    font-size: 0.9rem;
    transition: all 0.2s ease;
}

.btn-edit:hover {
    color: #4a6bff;
}

.btn-view-all {
    font-size: 0.9rem;
    color: #4a6bff;
}

.card-body {
    padding: 20px;
}

.profile-details {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.profile-details li {
    display: flex;
    align-items: center;
    gap: 10px;
}

.profile-details i {
    width: 20px;
    color: #4a6bff;
    text-align: center;
}

.wallet-card .card-body {
    text-align: center;
}

.wallet-balance {
    margin-bottom: 20px;
}

.balance-amount {
    font-size: 1.8rem;
    font-weight: 700;
    color: #2c3e50;
}

.currency {
    font-size: 1rem;
    color: #7f8c8d;
}

.wallet-actions {
    display: flex;
    gap: 10px;
    justify-content: center;
}

.transactions-list, .loans-list, .investments-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.transaction-item, .loan-item, .investment-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    border-radius: 10px;
    background: #f9f9f9;
    transition: all 0.2s ease;
}

.transaction-item:hover, .loan-item:hover, .investment-item:hover {
    background: #f0f4ff;
}

.transaction-icon, .loan-icon, .investment-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(74, 107, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #4a6bff;
    font-size: 1.1rem;
}

.transaction-details, .loan-details, .investment-details {
    flex: 1;
}

.transaction-details h4, .loan-details h4, .investment-details h4 {
    font-size: 0.95rem;
    margin-bottom: 5px;
    color: #2c3e50;
}

.transaction-details small, .loan-details small, .investment-details small {
    font-size: 0.8rem;
    color: #7f8c8d;
}

.transaction-amount, .loan-amount, .investment-profit {
    text-align: left;
    font-weight: 600;
    color: #2c3e50;
}

.transaction-amount small, .loan-amount small, .investment-profit small {
    display: block;
    font-size: 0.7rem;
    color: #7f8c8d;
    font-weight: normal;
}

.text-success {
    color: #2ecc71;
}

.text-danger {
    color: #e74c3c;
}

.text-warning {
    color: #f39c12;
}

.text-info {
    color: #3498db;
}

.empty-state {
    text-align: center;
    padding: 30px 0;
}

.empty-state i {
    font-size: 2.5rem;
    color: #bdc3c7;
    margin-bottom: 15px;
}

.empty-state p {
    color: #7f8c8d;
    margin-bottom: 15px;
}

.profile-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 30px;
}

@media (max-width: 992px) {
    .profile-content {
        flex-direction: column;
    }
    
    .profile-sidebar {
        flex: 1;
    }
    
    .profile-grid {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 768px) {
    .profile-avatar {
        right: 30px;
    }
    
    .profile-stats {
        gap: 15px;
    }
    
    .profile-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 576px) {
    .profile-avatar {
        right: 20px;
        width: 100px;
        height: 100px;
        bottom: -40px;
    }
    
    .profile-info {
        padding-top: 70px;
    }
    
    .wallet-actions {
        flex-direction: column;
    }
}
    /* استایل‌های فوتر */
    .modern-footer {
        background: #2c3e50;
        color: #fff;
        padding: 50px 0 0;
        position: relative;
        font-family: 'Vazirmatn', sans-serif;
    }
    
    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    .footer-top {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
        margin-bottom: 40px;
    }
    
    .footer-column {
        flex: 1;
        min-width: 220px;
    }
    
    .footer-column-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .footer-column-header i {
        font-size: 1.2rem;
        color: #4a6bff;
    }
    
    .footer-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
    }
    
    .footer-links {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    
    .footer-link-item {
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.2s ease;
    }
    
    .footer-link-item i {
        color: #4a6bff;
        font-size: 0.9rem;
    }
    
    .footer-link-item a {
        transition: all 0.2s ease;
    }
    
    .footer-link-item a:hover {
        color: #4a6bff;
    }
    
    .footer-social-links {
        display: flex;
        gap: 12px;
        margin-top: 20px;
    }
    
    .social-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
        transition: all 0.2s ease;
    }
    
    .social-link:hover {
        background: #4a6bff;
        transform: translateY(-3px);
    }
    
    .newsletter-column {
        display: flex;
        flex-direction: column;
    }
    
    .newsletter-desc {
        font-size: 0.9rem;
        color: #bdc3c7;
        margin-bottom: 20px;
    }
    
    .newsletter-form {
        margin-bottom: 30px;
    }
    
    .form-group {
        position: relative;
        display: flex;
    }
    
    .newsletter-form input {
        width: 100%;
        padding: 12px 15px;
        border: none;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
        font-size: 0.9rem;
    }
    
    .newsletter-form input::placeholder {
        color: #bdc3c7;
    }
    
    .newsletter-submit {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #bdc3c7;
        background: none;
        border: none;
        cursor: pointer;
    }
    
    .form-message {
        padding: 8px 0;
        font-size: 0.85rem;
        text-align: center;
        opacity: 0;
        height: 0;
        transition: all 0.3s ease;
    }
    
    .form-message.processing {
        color: #f39c12;
        opacity: 1;
        height: auto;
    }
    
    .form-message.success {
        color: #2ecc71;
        opacity: 1;
        height: auto;
    }
    
    .form-message.error {
        color: #e74c3c;
        opacity: 1;
        height: auto;
    }
    
    .payment-methods {
        margin-top: auto;
    }
    
    .payment-title {
        font-size: 0.95rem;
        margin-bottom: 12px;
        color: #bdc3c7;
    }
    
    .payment-icons {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .payment-icon {
        height: 25px;
        object-fit: contain;
        filter: grayscale(100%) brightness(2);
        opacity: 0.7;
        transition: all 0.2s ease;
    }
    
    .payment-icon:hover {
        filter: grayscale(0) brightness(1);
        opacity: 1;
    }
    
    .footer-bottom {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding: 20px 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .footer-copyright {
        font-size: 0.9rem;
        color: #bdc3c7;
    }
    
    .copyright-link {
        color: #4a6bff;
        transition: all 0.2s ease;
    }
    
    .copyright-link:hover {
        text-decoration: underline;
    }
    
    .footer-logo-img {
        height: 40px;
        opacity: 0.8;
        transition: all 0.2s ease;
    }
    
    .footer-logo-img:hover {
        opacity: 1;
    }
    
    .footer-certificates {
        display: flex;
        gap: 15px;
    }
    
    .certificate-img {
        height: 40px;
        opacity: 0.7;
        transition: all 0.2s ease;
    }
    
    .certificate-img:hover {
        opacity: 1;
    }
    
    /* دکمه بازگشت به بالا */
    .back-to-top {
        position: fixed;
        bottom: 30px;
        left: 30px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #4a6bff;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 999;
    }
    
    .back-to-top.show {
        opacity: 1;
        visibility: visible;
    }
    
    .back-to-top:hover {
        background: #3a5bef;
        transform: translateY(-3px);
    }
    
    /* چت پشتیبانی */
    .support-chat {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 999;
    }
    
    .chat-button {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 20px;
        background: #4a6bff;
        color: #fff;
        border-radius: 50px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        transition: all 0.2s ease;
    }
    
    .chat-button:hover {
        background: #3a5bef;
        transform: translateY(-3px);
    }
    
    .chat-label {
        font-size: 0.9rem;
    }
    
    .chat-box {
        position: absolute;
        bottom: 70px;
        right: 0;
        width: 300px;
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 5px 30px rgba(0, 0, 0, 0.2);
        opacity: 0;
        visibility: hidden;
        transform: translateY(20px);
        transition: all 0.3s ease;
    }
    
    .chat-box.active {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    
    .chat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        background: #4a6bff;
        color: #fff;
        border-radius: 15px 15px 0 0;
    }
    
   .chat-header h5 {
    margin: 0;
    font-size: 1rem;
    color: #000; /* رنگ مشکی */
}
    
    .close-chat {
        color: #fff;
        font-size: 0.9rem;
    }
    
    .chat-messages {
        padding: 15px;
        max-height: 300px;
        overflow-y: auto;
    }
    
    .chat-welcome {
        background: #f5f7fa;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    
    .chat-welcome p {
        margin-bottom: 5px;
        font-size: 0.9rem;
    }
    
   .chat-welcome small {
    font-size: 0.7rem;
    color: green;
}
    
    .chat-input {
        display: flex;
        padding: 10px;
        border-top: 1px solid #28a745;
    }
    
    .chat-input input {
        flex: 1;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 50px;
        font-size: 0.9rem;
        outline: none;
    }
    
    .chat-input button {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #4a6bff;
        color: #fff;
        margin-right: 10px;
    }
    
    /* رسپانسیو */
    @media (max-width: 768px) {
        .footer-top {
            flex-direction: column;
            gap: 30px;
        }
        
        .footer-column {
            width: 100%;
        }
        
        .footer-bottom {
            flex-direction: column;
            text-align: center;
        }
        
        .footer-certificates {
            justify-content: center;
        }
        
        .chat-box {
            width: 280px;
        }
        
        .back-to-top {
            left: 15px;
            bottom: 15px;
            width: 40px;
            height: 40px;
        }
        
        .chat-button {
            padding: 10px 15px;
        }
        /* استایل‌های عمومی */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Vazirmatn', sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        a {
            text-decoration: none;
            color: inherit;
        }
        
        ul {
            list-style: none;
        }
        
        button {
            background: none;
            border: none;
            cursor: pointer;
        }
        
        /* استایل هدر شیشه‌ای */
        .glass-header {
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    width: 100%; /* اضافه کردن این خط */
    max-width: 100%; /* اضافه کردن این خط */
}
        
      .header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1200px; /* عرض حداکثر را حفظ می‌کنیم */
    margin: 0 auto;
    padding: 0 20px;
    height: 70px;
    width: 100%; /* اضافه کردن این خط */
}
        
        .header-left, .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        /* لوگو */
        .logo {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1.5rem;
            font-weight: 700;
            color: #4a6bff;
        }
        
        .logo-icon {
            font-size: 1.7rem;
        }
        
        /* دکمه همبرگر */
        .hamburger-btn {
            display: none;
            flex-direction: column;
            justify-content: space-between;
            width: 24px;
            height: 18px;
            padding: 0;
        }
        
        .hamburger-btn span {
            display: block;
            width: 100%;
            height: 2px;
            background-color: #333;
            transition: all 0.3s ease;
        }
        
        /* منوی اصلی */
        .main-nav {
            display: flex;
        }
        
        .nav-list {
            display: flex;
            gap: 5px;
        }
        
        .nav-item {
            position: relative;
        }
        
        .nav-item.active .nav-link {
            color: #4a6bff;
            background: rgba(74, 107, 255, 0.1);
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }
        
        .nav-link:hover {
            background: rgba(0, 0, 0, 0.05);
        }
        
        /* جستجو */
        .search-container {
            position: relative;
        }
        
        .search-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            transition: all 0.2s ease;
        }
        
        .search-btn:hover {
            background: rgba(0, 0, 0, 0.05);
        }
        
        .search-box {
            position: absolute;
            top: 50px;
            left: -150px;
            width: 250px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            padding: 15px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.2s ease;
            z-index: 100;
        }
        
        .search-box.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .search-box input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.9rem;
            outline: none;
        }
        
        .search-submit {
            position: absolute;
            left: 15px;
            top: 15px;
            color: #777;
        }
        
        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            max-height: 300px;
            overflow-y: auto;
            display: none;
        }
        
        .search-result-item {
            display: flex;
            padding: 10px;
            gap: 10px;
            transition: all 0.2s ease;
        }
        
        .search-result-item:hover {
            background: #f5f7fa;
        }
        
        .search-result-icon {
            color: #4a6bff;
        }
        
        .search-result-content h5 {
            font-size: 0.9rem;
            margin-bottom: 3px;
        }
        
        .search-result-content p {
            font-size: 0.8rem;
            color: #777;
        }
        
        .no-results {
            padding: 15px;
            text-align: center;
            color: #777;
        }
        
        /* اعلان‌ها */
       .notification-dropdown {
    margin-right: auto; /* این خط آیکون را به سمت چپ می‌برد */
}


        
        .notification-btn {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            transition: all 0.2s ease;
        }
        
        .notification-btn:hover {
            background: rgba(0, 0, 0, 0.05);
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            left: -5px;
            background: #ff4757;
            color: white;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: bold;
        }
        
      .notification-dropdown-content {
    position: fixed; /* تغییر از absolute به fixed */
    top: 50%;
    left: 10%;
    transform: translate(-50%, -50%) translateY(10px); /* تغییر برای قرارگیری در مرکز */
    width: 300px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
    opacity: 0;
    visibility: hidden;
    transition: all 0.2s ease;
    z-index: 100;
}

.notification-dropdown-content.show {
    opacity: 1;
    visibility: visible;
    transform: translate(-50%, -50%) translateY(0); /* تغییر برای انیمیشن */
}
        
        .notification-dropdown-content.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .notification-header h4 {
            font-size: 0.95rem;
        }
        
        .view-all {
            font-size: 0.85rem;
            color: #4a6bff;
        }
        
        .notification-list {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .notification-item {
            display: flex;
            gap: 12px;
            padding: 12px 15px;
            transition: all 0.2s ease;
        }
        
        .notification-item:hover {
            background: #f5f7fa;
        }
        
        .notification-item.unread {
            background: rgba(74, 107, 255, 0.05);
        }
        
        .notification-icon {
            color: #4a6bff;
            font-size: 1.1rem;
        }
        
        .notification-content p {
            font-size: 0.85rem;
            margin-bottom: 5px;
        }
        
        .notification-content small {
            font-size: 0.75rem;
            color: #777;
        }
        
        .loading-notifications, .no-notifications, .error-notifications {
            padding: 20px;
            text-align: center;
            color: #777;
        }
        
        .loading-notifications i {
            margin-left: 5px;
        }
        
        /* پروفایل کاربر */
        .profile-dropdown {
            position: relative;
        }
        
        .profile-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 10px 5px 15px;
            border-radius: 50px;
            background: rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }
        
        .profile-btn:hover {
            background: rgba(0, 0, 0, 0.1);
        }
        
        .profile-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .profile-name {
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .profile-arrow {
            font-size: 0.8rem;
            color: #777;
            transition: transform 0.2s ease;
        }
        
        .profile-dropdown-menu {
            position: absolute;
            top: 50px;
            left: 0;
            width: 200px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.2s ease;
            z-index: 100;
        }
        
        .profile-dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }
        
        .dropdown-item:hover {
            background: #f5f7fa;
            color: #4a6bff;
        }
        
        .dropdown-item i {
            width: 18px;
            text-align: center;
        }
        
        .dropdown-divider {
            height: 1px;
            background: #eee;
            margin: 5px 0;
        }
        
        .dropdown-item.logout {
            color: #ff4757;
        }
        
        /* دکمه‌های احراز هویت */
        .auth-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .btn-login {
            border: 1px solid #ddd;
        }
        
        .btn-login:hover {
            background: rgba(0, 0, 0, 0.05);
        }
        
        .btn-primary {
            background: #4a6bff;
            color: white;
        }
        
        .btn-primary:hover {
            background: #3a5bef;
        }
        
        /* منوی موبایل */
        .mobile-menu-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .mobile-menu-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        .mobile-menu {
            position: fixed;
            top: 0;
            right: -300px;
            width: 300px;
            height: 100%;
            background: white;
            z-index: 1000;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        
        .mobile-menu.active {
            right: 0;
        }
        
        .mobile-menu-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .mobile-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .mobile-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .mobile-profile-info h4 {
            font-size: 1rem;
            margin-bottom: 5px;
        }
        
        .profile-link {
            font-size: 0.85rem;
            color: #4a6bff;
        }
        
        .mobile-auth-buttons {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.mobile-auth-buttons .btn {
    flex: 1;
    text-align: center;
    padding: 10px;
    font-size: 0.9rem;
}

.mobile-auth-buttons .btn-login {
    border: 1px solid #ddd;
    background: white;
}

.mobile-auth-buttons .btn-primary {
    background: #4a6bff;
    color: white;
}
        
        .close-menu {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.05);
        }
        
        .mobile-nav-list {
            flex: 1;
            overflow-y: auto;
            padding: 15px 0;
        }
        
        .mobile-nav-item {
            margin-bottom: 5px;
        }
        
        .mobile-nav-item.active .mobile-nav-link {
            color: #4a6bff;
            background: rgba(74, 107, 255, 0.1);
        }
        
        .mobile-nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        
        .mobile-nav-link:hover {
            background: rgba(0, 0, 0, 0.05);
        }
        
        .mobile-nav-link i {
            width: 20px;
            text-align: center;
        }
        
        .mobile-nav-link.logout {
            color: #ff4757;
        }
        
        .mobile-menu-footer {
            padding: 20px;
            border-top: 1px solid #eee;
        }
        
        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.05);
            color: #555;
            transition: all 0.2s ease;
        }
        
        .social-link:hover {
            background: #4a6bff;
            color: white;
        }
        
        .copyright {
            text-align: center;
            font-size: 0.8rem;
            color: #777;
        }
        
        /* رسپانسیو */
        @media (max-width: 992px) {
            .main-nav {
                display: none;
            }
            
            .hamburger-btn {
                display: flex;
            }
        }
        
        @media (max-width: 1200px) {
    .header-container {
        padding: 0 15px;
    }
}

@media (max-width: 768px) {
    .header-container {
        padding: 0 10px;
    }
}
            
            .profile-name {
                display: none;
            }
            
            .profile-btn {
                padding: 5px;
            }
        // جدید
        /* استایل‌های صفحه اصلی */
.home-page {
    padding-top: 70px;
    font-family: 'Vazirmatn', sans-serif;
}

/* اسلایدر اصلی */
.hero-slider {
    position: relative;
    height: 500px;
    overflow: hidden;
}

.slider-container {
    position: relative;
    height: 100%;
}

.slider-item {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    opacity: 0;
    transition: opacity 1s ease;
    display: flex;
    align-items: center;
}

.slider-item.active {
    opacity: 1;
}

.slider-content {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    color: white;
    text-align: center;
}

.slider-content h1 {
    font-size: 2.5rem;
    margin-bottom: 15px;
    font-weight: 700;
}

.slider-content p {
    font-size: 1.2rem;
    margin-bottom: 30px;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
}

.slider-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
}

.btn-lg {
    padding: 12px 24px;
    font-size: 1.1rem;
}

.slider-controls {
    position: absolute;
    bottom: 30px;
    left: 0;
    right: 0;
    display: flex;
    justify-content: space-between;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.slider-controls button {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.slider-controls button:hover {
    background: rgba(255, 255, 255, 0.3);
}

.slider-dots {
    position: absolute;
    bottom: 40px;
    left: 0;
    right: 0;
    display: flex;
    justify-content: center;
    gap: 10px;
}

.slider-dots .dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.5);
    cursor: pointer;
    transition: all 0.2s ease;
}

.slider-dots .dot.active {
    background: white;
    transform: scale(1.2);
}

/* بخش آمار و ارقام */
.stats-section {
    background: #4a6bff;
    color: white;
    padding: 60px 0;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-icon {
    font-size: 2.5rem;
    opacity: 0.8;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    display: block;
}

.stat-label {
    font-size: 1rem;
    opacity: 0.9;
}

/* بخش محصولات و خدمات */
.products-section {
    padding: 80px 0;
}

.section-header {
    text-align: center;
    margin-bottom: 50px;
}

.section-title {
    font-size: 2rem;
    color: #2c3e50;
    margin-bottom: 10px;
}

.section-subtitle {
    font-size: 1.1rem;
    color: #7f8c8d;
    max-width: 700px;
    margin: 0 auto;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}

.product-card {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    text-align: center;
}

.product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
}

.product-icon {
    width: 70px;
    height: 70px;
    background: rgba(74, 107, 255, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    color: #4a6bff;
    font-size: 1.8rem;
}

.product-title {
    font-size: 1.3rem;
    margin-bottom: 15px;
    color: #2c3e50;
}

.product-desc {
    color: #7f8c8d;
    margin-bottom: 20px;
    font-size: 0.95rem;
    line-height: 1.6;
}

/* بخش نحوه کار */
.how-it-works {
    background: #f5f7fa;
    padding: 80px 0;
}

.steps-container {
    position: relative;
    max-width: 800px;
    margin: 0 auto;
    padding: 0 20px;
}

.step-line {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 50%;
    width: 2px;
    background: #4a6bff;
    transform: translateX(-50%);
    z-index: 1;
}

.step-item {
    position: relative;
    margin-bottom: 40px;
    display: flex;
    align-items: center;
    z-index: 2;
}

.step-item:nth-child(odd) {
    flex-direction: row-reverse;
    text-align: left;
}

.step-item:nth-child(even) {
    text-align: right;
}

.step-number {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #4a6bff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 700;
    flex-shrink: 0;
    margin: 0 20px;
}

.step-content {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    flex: 1;
}

.step-content h3 {
    font-size: 1.3rem;
    color: #2c3e50;
    margin-bottom: 10px;
}

.step-content p {
    color: #7f8c8d;
    line-height: 1.6;
}

/* بخش ماشین حساب وام */
.loan-calculator {
    padding: 80px 0;
}

.calculator-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.calculator-form {
    padding: 40px;
}

.calculator-form h2 {
    font-size: 1.8rem;
    color: #2c3e50;
    margin-bottom: 10px;
}

.calculator-form p {
    color: #7f8c8d;
    margin-bottom: 30px;
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #2c3e50;
}

.form-group input[type="range"] {
    width: 100%;
    height: 8px;
    -webkit-appearance: none;
    background: #e0e0e0;
    border-radius: 4px;
    outline: none;
    margin-bottom: 10px;
}

.form-group input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 20px;
    height: 20px;
    background: #4a6bff;
    border-radius: 50%;
    cursor: pointer;
}

.range-value {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 1.1rem;
}

.range-value span:first-child {
    font-weight: 700;
    color: #4a6bff;
}

.form-group select {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    color: #333;
    background: white;
}

.calculator-result {
    background: #4a6bff;
    color: white;
    padding: 40px;
    display: flex;
    align-items: center;
}

.result-box {
    width: 100%;
}

.result-box h3 {
    font-size: 1.5rem;
    margin-bottom: 25px;
    text-align: center;
}

.result-item {
    display: flex;
    justify-content: space-between;
    padding: 15px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.result-item:last-child {
    border-bottom: none;
}

.result-item span:last-child {
    font-weight: 700;
    font-size: 1.1rem;
}

/* بخش نظرات مشتریان */
.testimonials {
    background: #f5f7fa;
    padding: 80px 0;
}

.testimonials-slider {
    position: relative;
    max-width: 800px;
    margin: 0 auto;
}

.testimonial-item {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    opacity: 0;
    transition: opacity 0.5s ease;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
}

.testimonial-item.active {
    opacity: 1;
    position: relative;
}

.testimonial-content {
    position: relative;
    margin-bottom: 30px;
}

.quote-icon {
    position: absolute;
    top: -15px;
    left: -15px;
    width: 40px;
    height: 40px;
    background: #4a6bff;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.testimonial-content p {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #555;
}

.testimonial-author {
    display: flex;
    align-items: center;
    gap: 15px;
}

.testimonial-author img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
}

.author-info h4 {
    font-size: 1.1rem;
    margin-bottom: 5px;
}

.author-info span {
    font-size: 0.9rem;
    color: #7f8c8d;
}

.testimonial-controls {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    display: flex;
    justify-content: space-between;
    transform: translateY(-50%);
}

.testimonial-controls button {
    background: white;
    color: #4a6bff;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
}

.testimonial-dots {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 30px;
}

.testimonial-dots .dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #ddd;
    cursor: pointer;
    transition: all 0.2s ease;
}

.testimonial-dots .dot.active {
    background: #4a6bff;
    transform: scale(1.2);
}

/* بخش اخبار و مقالات */
.blog-section {
    padding: 80px 0;
}

.blog-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}

.blog-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.blog-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
}

.blog-image {
    height: 200px;
    overflow: hidden;
}

.blog-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.blog-card:hover .blog-image img {
    transform: scale(1.1);
}

.blog-content {
    padding: 25px;
}

.blog-date {
    display: block;
    font-size: 0.85rem;
    color: #7f8c8d;
    margin-bottom: 10px;
}

.blog-title {
    font-size: 1.2rem;
    color: #2c3e50;
    margin-bottom: 15px;
    line-height: 1.4;
}

.blog-excerpt {
    color: #7f8c8d;
    margin-bottom: 20px;
    line-height: 1.6;
}

.read-more {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    color: #4a6bff;
    font-weight: 500;
    transition: all 0.2s ease;
}

.read-more:hover {
    gap: 10px;
}

/* بخش CTA پایانی */
.final-cta {
    background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('assets/images/cta-bg.jpg');
    background-size: cover;
    background-position: center;
    padding: 100px 0;
    color: white;
    text-align: center;
}

.cta-content h2 {
    font-size: 2.2rem;
    margin-bottom: 15px;
}

.cta-content p {
    font-size: 1.2rem;
    margin-bottom: 30px;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
}

.cta-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
}

/* رسپانسیو */
@media (max-width: 992px) {
    .products-grid, .blog-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .calculator-container {
        grid-template-columns: 1fr;
    }
    
    .calculator-result {
        padding: 30px;
    }
}

@media (max-width: 768px) {
    .hero-slider {
        height: 400px;
    }
    
    .slider-content h1 {
        font-size: 2rem;
    }
    
    .slider-content p {
        font-size: 1rem;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    .step-item {
        flex-direction: column !important;
        text-align: center !important;
        margin-bottom: 30px;
    }
    
    .step-line {
        display: none;
    }
    
    .step-number {
        margin-bottom: 15px;
    }
}

@media (max-width: 576px) {
    .products-grid, .blog-grid {
        grid-template-columns: 1fr;
    }
    
    .slider-buttons, .cta-buttons {
        flex-direction: column;
        gap: 10px;
    }
    
    .btn-lg {
        width: 100%;
    }
// new
   
.login-page {
    padding: 100px 0;
    background-color: #f5f7fa;
    min-height: calc(100vh - 70px);
    display: flex;
    align-items: center;
}

.login-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    display: flex;
    align-items: center;
    gap: 50px;
}

.login-form-container {
    flex: 1;
    max-width: 500px;
    background: white;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
}

.login-form-header {
    text-align: center;
    margin-bottom: 30px;
}

.login-form-header h2 {
    font-size: 1.8rem;
    color: #2c3e50;
    margin-bottom: 10px;
}

.login-form-header p {
    color: #7f8c8d;
    font-size: 0.95rem;
}

.login-form-group {
    position: relative;
    margin-bottom: 20px;
}

.login-form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #2c3e50;
    font-size: 0.9rem;
}

.login-form-group input {
    width: 100%;
    padding: 12px 15px 12px 40px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.login-form-group input:focus {
    border-color: #4a6bff;
    box-shadow: 0 0 0 3px rgba(74, 107, 255, 0.2);
}

.login-input-icon {
    position: absolute;
    left: 15px;
    top: 40px;
    color: #7f8c8d;
    font-size: 1rem;
}

.login-forgot-password {
    display: block;
    text-align: left;
    margin-top: 5px;
    font-size: 0.85rem;
    color: #4a6bff;
}

.login-remember-me {
    display: flex;
    align-items: center;
    gap: 8px;
}

.login-remember-me input {
    width: auto;
    padding: 0;
}

.login-btn-block {
    width: 100%;
    padding: 12px;
    font-size: 1rem;
}

.login-form-footer {
    text-align: center;
    margin-top: 20px;
    font-size: 0.9rem;
    color: #7f8c8d;
}

.login-form-footer a {
    color: #4a6bff;
    font-weight: 500;
}

.login-social-login {
    margin-top: 30px;
}

.login-divider {
    position: relative;
    text-align: center;
    margin: 20px 0;
}

.login-divider::before {
    content: "";
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: #eee;
    z-index: 1;
}

.login-divider span {
    position: relative;
    display: inline-block;
    padding: 0 10px;
    background: white;
    z-index: 2;
    color: #7f8c8d;
    font-size: 0.85rem;
}

.login-social-buttons {
    display: flex;
    gap: 10px;
}

.login-social-btn {
    flex: 1;
    padding: 10px;
    border-radius: 8px;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.login-social-btn.google {
    background: #db4437;
    color: white;
}

.login-social-btn.telegram {
    background: #0088cc;
    color: white;
}

.login-image {
    flex: 1;
    display: flex;
    justify-content: center;
}

.login-image img {
    max-width: 100%;
    height: auto;
}

@media (max-width: 992px) {
    .login-container {
        flex-direction: column;
    }
    
    .login-form-container {
        max-width: 100%;
    }
    
    .login-image {
        display: none;
    }
}

@media (max-width: 576px) {
    .login-form-container {
        padding: 30px 20px;
    }
    
    .login-social-buttons {
        flex-direction: column;
    }
}

    </style>
</head>
<body>
    <!-- هدر شیشه‌ای مدرن -->
    <header class="glass-header">
        <div class="header-container">
            <!-- لوگو و منوی همبرگر -->
            <div class="header-left">
                <button class="hamburger-btn" id="hamburgerBtn">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                
                <a href="index.php" class="logo">
                    <span>باجیت</span>
                </a>
            </div>
            
            <!-- منوی اصلی -->
            <nav class="main-nav">
                <ul class="nav-list">
                    <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                        <a href="index.php" class="nav-link">
                            <i class="fas fa-home"></i>
                            <span>صفحه اصلی</span>
                        </a>
                    </li>
                    <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'loans.php' ? 'active' : ''; ?>">
                        <a href="loans.php" class="nav-link">
                            <i class="fas fa-hand-holding-usd"></i>
                            <span>تسهیلات</span>
                        </a>
                    </li>
                    <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'investment.php' ? 'active' : ''; ?>">
                        <a href="investment.php" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>سرمایه‌گذاری</span>
                        </a>
                    </li>
                    <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'installments.php' ? 'active' : ''; ?>">
                        <a href="installments.php" class="nav-link">
                            <i class="fas fa-calendar-check"></i>
                            <span>اقساط من</span>
                        </a>
                    </li>
                    <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'support.php' ? 'active' : ''; ?>">
                        <a href="support.php" class="nav-link">
                            <i class="fas fa-headset"></i>
                            <span>پشتیبانی</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- بخش کاربر و جستجو -->
<div class="header-right">
    <!-- جستجو -->
    <div class="search-container">
        <button class="search-btn" id="searchToggle">
            <i class="fas fa-search"></i>
        </button>
        <div class="search-box">
            <input type="text" placeholder="جستجو..." id="globalSearch">
            <button class="search-submit">
                <i class="fas fa-search"></i>
            </button>
            <div class="search-results" id="searchResults"></div>
        </div>
    </div>
    
    <?php if ($is_logged_in): ?>
        <!-- اعلان‌ها -->
        <div class="notification-dropdown">
            <button class="notification-btn" id="notificationBtn">
                <i class="fas fa-bell"></i>
                <?php if ($unread_notifications > 0): ?>
                    <span class="notification-badge"><?php echo $unread_notifications; ?></span>
                <?php endif; ?>
            </button>
            <div class="notification-dropdown-content">
                <div class="notification-header">
                    <h4>اعلان‌های اخیر</h4>
                    <a href="notifications.php" class="view-all">مشاهده همه</a>
                </div>
                <div class="notification-list">
                    <!-- محتوای اعلان‌ها از طریق AJAX لود می‌شود -->
                    <div class="loading-notifications">
                        <i class="fas fa-spinner fa-spin"></i>
                        در حال بارگذاری...
                    </div>
                </div>
            </div>
        </div>
        
        <!-- پروفایل کاربر -->
        <div class="profile-dropdown">
            <button class="profile-btn" id="profileBtn">
                <img src="<?php echo htmlspecialchars($user_avatar); ?>" alt="پروفایل کاربر" class="profile-avatar">
                <span class="profile-name"><?php echo htmlspecialchars($user_name); ?></span>
                <i class="fas fa-chevron-down profile-arrow"></i>
            </button>
            <div class="profile-dropdown-menu">
                <a href="profile.php" class="dropdown-item">
                    <i class="fas fa-user"></i>
                    <span>پروفایل من</span>
                </a>
                <a href="wallet.php" class="dropdown-item">
                    <i class="fas fa-wallet"></i>
                    <span>کیف پول</span>
                </a>
                <a href="settings.php" class="dropdown-item">
                    <i class="fas fa-cog"></i>
                    <span>تنظیمات</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="logout.php" class="dropdown-item logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>خروج</span>
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- نمایش هیچ دکمه‌ای برای کاربران مهمان در هدر اصلی -->
    <?php endif; ?>
</div>
        </div>
    </header>

    <!-- منوی موبایل -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>
    <nav class="mobile-menu" id="mobileMenu">
        <div class="mobile-menu-header">
            <?php if ($is_logged_in): ?>
                <div class="mobile-profile">
                    <img src="<?php echo htmlspecialchars($user_avatar); ?>" alt="پروفایل کاربر" class="mobile-avatar">
                    <div class="mobile-profile-info">
                        <h4><?php echo htmlspecialchars($user_name); ?></h4>
                        <a href="profile.php" class="profile-link">مشاهده پروفایل</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="mobile-auth-buttons">
                    <a href="login.php" class="btn btn-login">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>ورود</span>
                    </a>
                    <a href="register.php" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i>
                        <span>ثبت‌نام</span>
                    </a>
                </div>
            <?php endif; ?>
            <button class="close-menu" id="closeMobileMenu">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <ul class="mobile-nav-list">
            <li class="mobile-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                <a href="index.php" class="mobile-nav-link">
                    <i class="fas fa-home"></i>
                    <span>صفحه اصلی</span>
                </a>
            </li>
            <li class="mobile-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'loans.php' ? 'active' : ''; ?>">
                <a href="loans.php" class="mobile-nav-link">
                    <i class="fas fa-hand-holding-usd"></i>
                    <span>تسهیلات</span>
                </a>
            </li>
            <li class="mobile-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'investment.php' ? 'active' : ''; ?>">
                <a href="investment.php" class="mobile-nav-link">
                    <i class="fas fa-chart-line"></i>
                    <span>سرمایه‌گذاری</span>
                </a>
            </li>
            <li class="mobile-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'installments.php' ? 'active' : ''; ?>">
                <a href="installments.php" class="mobile-nav-link">
                    <i class="fas fa-calendar-check"></i>
                    <span>اقساط من</span>
                </a>
            </li>
            <?php if ($is_logged_in): ?>
                <li class="mobile-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'wallet.php' ? 'active' : ''; ?>">
                    <a href="wallet.php" class="mobile-nav-link">
                        <i class="fas fa-wallet"></i>
                        <span>کیف پول</span>
                    </a>
                </li>
                <li class="mobile-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'transactions.php' ? 'active' : ''; ?>">
                    <a href="transactions.php" class="mobile-nav-link">
                        <i class="fas fa-exchange-alt"></i>
                        <span>تراکنش‌ها</span>
                    </a>
                </li>
            <?php endif; ?>
            <li class="mobile-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'support.php' ? 'active' : ''; ?>">
                <a href="support.php" class="mobile-nav-link">
                    <i class="fas fa-headset"></i>
                    <span>پشتیبانی</span>
                </a>
            </li>
            <li class="mobile-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>">
                <a href="about.php" class="mobile-nav-link">
                    <i class="fas fa-info-circle"></i>
                    <span>درباره ما</span>
                </a>
            </li>
            <?php if ($is_logged_in): ?>
                <li class="mobile-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                    <a href="settings.php" class="mobile-nav-link">
                        <i class="fas fa-cog"></i>
                        <span>تنظیمات</span>
                    </a>
                </li>
                <li class="mobile-nav-item">
                    <a href="logout.php" class="mobile-nav-link logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>خروج از حساب</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
        
        <div class="mobile-menu-footer">
            <div class="social-links">
                <a href="#" class="social-link" title="اینستاگرام">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" class="social-link" title="تلگرام">
                    <i class="fab fa-telegram"></i>
                </a>
                <a href="#" class="social-link" title="توییتر">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="#" class="social-link" title="لینکدین">
                    <i class="fab fa-linkedin"></i>
                </a>
            </div>
            <p class="copyright">© <?php echo date('Y'); ?> باجیت - تمام حقوق محفوظ است</p>
        </div>
    </nav>

    <!-- اسکریپت‌های هدر -->
    <script>
        // مدیریت منوی موبایل
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
        const closeMobileMenu = document.getElementById('closeMobileMenu');
        
        hamburgerBtn.addEventListener('click', () => {
            mobileMenu.classList.add('active');
            mobileMenuOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
        
        closeMobileMenu.addEventListener('click', () => {
            mobileMenu.classList.remove('active');
            mobileMenuOverlay.classList.remove('active');
            document.body.style.overflow = '';
        });
        
        mobileMenuOverlay.addEventListener('click', () => {
            mobileMenu.classList.remove('active');
            mobileMenuOverlay.classList.remove('active');
            document.body.style.overflow = '';
        });
        
        // مدیریت جستجو
        const searchToggle = document.getElementById('searchToggle');
        const searchBox = document.querySelector('.search-box');
        
        searchToggle.addEventListener('click', () => {
            searchBox.classList.toggle('active');
            if (searchBox.classList.contains('active')) {
                document.getElementById('globalSearch').focus();
            }
        });
        
        // مدیریت اعلان‌ها
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationDropdown = document.querySelector('.notification-dropdown-content');
        
        notificationBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notificationDropdown.classList.toggle('show');
            
            // بارگذاری اعلان‌ها
            if (notificationDropdown.classList.contains('show')) {
                loadNotifications();
            }
        });
        
        // بستن منوی اعلان با کلیک خارج
        document.addEventListener('click', (e) => {
            if (!notificationBtn.contains(e.target)) {
                notificationDropdown.classList.remove('show');
            }
        });
        
        // تابع بارگذاری اعلان‌ها
        function loadNotifications() {
            const notificationList = document.querySelector('.notification-list');
            notificationList.innerHTML = '<div class="loading-notifications"><i class="fas fa-spinner fa-spin"></i> در حال بارگذاری اعلان‌ها...</div>';
            
            // شبیه‌سازی درخواست AJAX
            setTimeout(() => {
                fetch('api/get_notifications.php?limit=5')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.notifications.length > 0) {
                            let html = '';
                            data.notifications.forEach(notification => {
                                html += `
                                    <div class="notification-item ${notification.is_read ? '' : 'unread'}">
                                        <div class="notification-icon">
                                            <i class="fas ${getNotificationIcon(notification.type)}"></i>
                                        </div>
                                        <div class="notification-content">
                                            <p>${notification.message}</p>
                                            <small>${notification.time_ago}</small>
                                        </div>
                                    </div>
                                `;
                            });
                            notificationList.innerHTML = html;
                        } else {
                            notificationList.innerHTML = '<div class="no-notifications">اعلانی برای نمایش وجود ندارد</div>';
                        }
                    })
                    .catch(error => {
                        notificationList.innerHTML = '<div class="error-notifications">خطا در بارگذاری اعلان‌ها</div>';
                    });
            }, 500);
        }
        
        // تابع کمک‌کننده برای آیکون اعلان‌ها
        function getNotificationIcon(type) {
            const icons = {
                'payment': 'fa-credit-card',
                'investment': 'fa-chart-line',
                'loan': 'fa-hand-holding-usd',
                'system': 'fa-info-circle',
                'warning': 'fa-exclamation-triangle'
            };
            return icons[type] || 'fa-bell';
        }
        
        // مدیریت پروفایل کاربر
        const profileBtn = document.getElementById('profileBtn');
        const profileDropdown = document.querySelector('.profile-dropdown-menu');
        
        profileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            profileDropdown.classList.toggle('show');
        });
        
        // بستن منوی پروفایل با کلیک خارج
        document.addEventListener('click', (e) => {
            if (!profileBtn.contains(e.target)) {
                profileDropdown.classList.remove('show');
            }
        });
        
        // جستجوی زنده
        const globalSearch = document.getElementById('globalSearch');
        const searchResults = document.getElementById('searchResults');
        
        if (globalSearch && searchResults) {
            globalSearch.addEventListener('input', debounce(function() {
                const query = this.value.trim();
                
                if (query.length < 2) {
                    searchResults.style.display = 'none';
                    return;
                }
                
                // شبیه‌سازی جستجو
                setTimeout(() => {
                    fetch(`api/search.php?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.results.length > 0) {
                                let html = '';
                                data.results.forEach(result => {
                                    html += `
                                        <a href="${result.link}" class="search-result-item">
                                            <div class="search-result-icon">
                                                <i class="fas ${result.icon}"></i>
                                            </div>
                                            <div class="search-result-content">
                                                <h5>${result.title}</h5>
                                                <p>${result.description}</p>
                                            </div>
                                        </a>
                                    `;
                                });
                                searchResults.innerHTML = html;
                                searchResults.style.display = 'block';
                            } else {
                                searchResults.innerHTML = '<div class="no-results">نتیجه‌ای یافت نشد</div>';
                                searchResults.style.display = 'block';
                            }
                        });
                }, 300);
            }, 300));
            
            // بستن نتایج جستجو با کلیک خارج
            document.addEventListener('click', (e) => {
                if (!globalSearch.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.style.display = 'none';
                }
            });
        }
        
        // تابع کمک‌کننده برای تاخیر در جستجو
        function debounce(func, wait) {
            let timeout;
            return function() {
                const context = this, args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    func.apply(context, args);
                }, wait);
            };
        }
    </script>