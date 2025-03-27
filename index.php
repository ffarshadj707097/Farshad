<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'includes/header.php';
require_once 'includes/jdf.php';

// اطلاعات آماری برای نمایش در صفحه اصلی
$stats = [
    'total_loans' => 1500,
    'active_investments' => 5000,
    'satisfaction_rate' => 11,
    'registered_users' => 20
];

// محصولات ویژه
$featured_products = [
    [
        'title' => ' وام فوری بدون چک و ضامن فقط با سفته الکترونیکی',
        'description' => 'دریافت وام تا سقف ۷۵ میلیون تومان در کمتر از ۵ روز ',
        'icon' => 'fa-bolt',
        'link' => 'fast-loan.php'
    ],
    [
        'title' => 'وام با ضمانت طلا',
        'description' => 'وام با ضمانت طلا و سود کم',
        'icon' => 'fa-coins',
        'link' => 'gold-loan.php'
    ],
    [
        'title' => 'سرمایه‌گذاری',
        'description' => 'سرمایه‌گذاری مطمئن با سود ۱۸۵ درصد سالانه',
        'icon' => 'fa-chart-line',
        'link' => 'investment.php'
    ]
];

// دریافت نظرات از پایگاه داده
$comments = [];
$stmt = $conn->query("SELECT * FROM comments WHERE is_active = 1 ORDER BY created_at DESC LIMIT 3");
if ($stmt) {
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// دریافت اسلایدرها از پایگاه داده
$sliders = [];
$stmt = $conn->query("SELECT * FROM sliders WHERE is_active = 1 ORDER BY created_at DESC");
if ($stmt) {
    $sliders = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// دریافت آخرین مقالات از پایگاه داده
$latest_articles = [];
$stmt = $conn->query("SELECT id, title, excerpt, content, created_at, image FROM articles WHERE is_active = 1 ORDER BY created_at DESC LIMIT 3");
if ($stmt) {
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($articles as $article) {
        // تبدیل تاریخ میلادی به شمسی
        $created_at = new DateTime($article['created_at']);
        $year = $created_at->format('Y');
        $month = $created_at->format('m');
        $day = $created_at->format('d');
        $persian_date = jdate('Y/m/d', strtotime($article['created_at']));
        
        $latest_articles[] = [
            'title' => $article['title'],
            'excerpt' => $article['excerpt'],
            'date' => $persian_date,
            'link' => 'blog/article.php?id=' . $article['id'],
            'image' => $article['image'] ?: 'default-article.jpg'
        ];
    }
}
?>
    <section class="final-cta">
        <div class="container">
            <div class="cta-content">
                <h2>آیا به دنبال دریافت وام یا سرمایه‌گذاری امن هستید؟</h2>
                <p>همین حالا در سامانه باجیت ثبت‌نام کنید و از خدمات ما بهره‌مند شوید</p>
                
                <div class="cta-buttons">
                    <?php if ($is_logged_in): ?>
                        <a href="loans.php" class="btn btn-primary btn-lg">درخواست وام</a>
                        <a href="investment.php" class="btn btn-outline-light btn-lg">سرمایه‌گذاری</a>
                    <?php else: ?>
                        <a href="register.php" class="btn btn-primary btn-lg">ثبت‌نام رایگان</a>
                        <a href="login.php" class="btn btn-outline-light btn-lg">ورود به حساب</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>
<main class="home-page">
    <!-- اسلایدر اصلی - فقط نمایش اگر اسلایدر وجود داشته باشد -->
    <?php if (!empty($sliders)): ?>
    <section class="hero-slider">
        <div class="slider-container">
            <?php foreach ($sliders as $index => $slider): ?>
                <div class="slider-item <?php echo $index === 0 ? 'active' : ''; ?>" 
                     style="background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('assets/images/sliders/<?php echo $slider['image']; ?>');">
                    <div class="slider-content">
                        <h1><?php echo $slider['title']; ?></h1>
                        <p><?php echo $slider['description']; ?></p>
                        <div class="slider-buttons">
                            <?php if ($slider['button1_text'] && $slider['button1_link']): ?>
                                <a href="<?php echo $slider['button1_link']; ?>" class="btn btn-primary btn-lg"><?php echo $slider['button1_text']; ?></a>
                            <?php endif; ?>
                            <?php if ($slider['button2_text'] && $slider['button2_link']): ?>
                                <a href="<?php echo $slider['button2_link']; ?>" class="btn btn-outline-light btn-lg"><?php echo $slider['button2_text']; ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if (count($sliders) > 1): ?>
                <div class="slider-controls">
                    <button class="slider-prev"><i class="fas fa-chevron-right"></i></button>
                    <button class="slider-next"><i class="fas fa-chevron-left"></i></button>
                </div>
                <div class="slider-dots">
                    <?php foreach ($sliders as $index => $slider): ?>
                        <span class="dot <?php echo $index === 0 ? 'active' : ''; ?>"></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- بخش آمار و ارقام -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-number" data-count="<?php echo $stats['total_loans']; ?>">0</span>
                        <span class="stat-label">(میلیاد)وام پرداختی</span>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-number" data-count="<?php echo $stats['active_investments']; ?>">0</span>
                        <span class="stat-label">(میلیارد)سرمایه گذاری فعال</span>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-smile"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-number" data-count="<?php echo $stats['satisfaction_rate']; ?>">0</span>
                        <span class="stat-label">(هزار)رضایت مشتریان</span>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-number" data-count="<?php echo $stats['registered_users']; ?>">0</span>
                        <span class="stat-label">(هزار)کاربر ثبت‌نامی</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- بخش محصولات و خدمات -->
    <section class="products-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">محصولات و خدمات ما</h2>
                <p class="section-subtitle">با باجیت، اعتبار خود را هوشمندانه مدیریت کنید</p>
            </div>
            
            <div class="products-grid">
                <?php foreach ($featured_products as $product): ?>
                <div class="product-card">
                    <div class="product-icon">
                        <i class="fas <?php echo $product['icon']; ?>"></i>
                    </div>
                    <h3 class="product-title"><?php echo $product['title']; ?></h3>
                    <p class="product-desc"><?php echo $product['description']; ?></p>
                    <a href="<?php echo $product['link']; ?>" class="btn btn-outline-primary">اطلاعات بیشتر</a>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-4">
                <a href="services.php" class="btn btn-primary">مشاهده همه خدمات</a>
            </div>
        </div>
    </section>

    <!-- بخش نحوه کار -->
    <section class="how-it-works">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">چگونه کار می‌کند؟</h2>
                <p class="section-subtitle">در ۳ مرحله ساده وام دریافت کنید</p>
            </div>
            
            <div class="steps-container">
                <div class="step-line"></div>
                
                <div class="step-item">
                    <div class="step-number">۱</div>
                    <div class="step-content">
                        <h3>ثبت‌نام و احراز هویت</h3>
                        <p>در کمتر از ۵ دقیقه در سامانه ثبت‌نام کنید و احراز هویت انجام دهید</p>
                    </div>
                </div>
                
                <div class="step-item">
                    <div class="step-number">۲</div>
                    <div class="step-content">
                        <h3>درخواست وام</h3>
                        <p>مبلغ و مدت زمان مورد نظر خود را انتخاب کنید</p>
                    </div>
                </div>
                
                <div class="step-item">
                    <div class="step-number">۳</div>
                    <div class="step-content">
                        <h3>دریافت وجه</h3>
                        <p>پس از تایید درخواست، مبلغ به حساب شما واریز می‌شود</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- بخش ماشین حساب وام -->
    <section class="loan-calculator">
        <div class="container">
            <div class="calculator-container">
                <div class="calculator-form">
                    <h2>ماشین حساب وام</h2>
                    <p>مبلغ وام و اقساط خود را محاسبه کنید</p>
                    
                    <div class="form-group">
                        <label for="loanAmount">مبلغ وام (تومان)</label>
                        <input type="range" id="loanAmount" min="1000000" max="75000000" step="1000000" value="10000000">
                        <div class="range-value">
                            <span id="loanAmountValue">۱۰,۰۰۰,۰۰۰</span>
                            <span>تومان</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="loanTerm">مدت بازپرداخت (ماه)</label>
                        <input type="range" id="loanTerm" min="3" max="36" step="1" value="12">
                        <div class="range-value">
                            <span id="loanTermValue">۱۲</span>
                            <span>ماه</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="loanType">نوع وام</label>
                        <select id="loanType">
                            <option value="normal">عادی (سود ۲۲٪)</option>
                            <option value="special">ویژه (سود ۱۸٪)</option>
                            <option value="gold">طلا (سود ۱۵٪)</option>
                        </select>
                    </div>
                    
                    <button id="calculateBtn" class="btn btn-primary">محاسبه اقساط</button>
                </div>
                
                <div class="calculator-result">
                    <div class="result-box">
                        <h3>نتیجه محاسبه</h3>
                        <div class="result-item">
                            <span>مبلغ هر قسط:</span>
                            <span id="monthlyPayment">-</span>
                        </div>
                        <div class="result-item">
                            <span>کل مبلغ قابل پرداخت:</span>
                            <span id="totalPayment">-</span>
                        </div>
                        <div class="result-item">
                            <span>کل سود:</span>
                            <span id="totalInterest">-</span>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="apply-loan.php" class="btn btn-outline-primary">درخواست وام</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- بخش نظرات مشتریان - فقط نمایش اگر نظر وجود داشته باشد -->
    <?php if (!empty($comments)): ?>
    <section class="testimonials">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">نظرات مشتریان</h2>
                <p class="section-subtitle">آنچه کاربران ما می‌گویند</p>
            </div>
            
            <div class="testimonials-slider">
                <?php foreach ($comments as $index => $comment): ?>
                <div class="testimonial-item <?php echo $index === 0 ? 'active' : ''; ?>">
                    <div class="testimonial-content">
                        <div class="quote-icon">
                            <i class="fas fa-quote-right"></i>
                        </div>
                        <p><?php echo $comment['comment']; ?></p>
                    </div>
                    <div class="testimonial-author">
                        <img src="assets/images/users/<?php echo $comment['image'] ? $comment['image'] : 'default-user.jpg'; ?>" alt="<?php echo $comment['name']; ?>">
                        <div class="author-info">
                            <h4><?php echo $comment['name']; ?></h4>
                            <span><?php echo $comment['city']; ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if (count($comments) > 1): ?>
                <div class="testimonial-controls">
                    <button class="testimonial-prev"><i class="fas fa-chevron-right"></i></button>
                    <button class="testimonial-next"><i class="fas fa-chevron-left"></i></button>
                </div>
                <div class="testimonial-dots">
                    <?php foreach ($comments as $index => $comment): ?>
                        <span class="dot <?php echo $index === 0 ? 'active' : ''; ?>"></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="text-center mt-4">
                <a href="comments.php" class="btn btn-outline-primary">مشاهده همه نظرات</a>
                <a href="add-comment.php" class="btn btn-primary">ثبت نظر جدید</a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- بخش اخبار و مقالات - فقط نمایش اگر مقاله وجود داشته باشد -->
    <?php if (!empty($latest_articles)): ?>
    <section class="blog-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">آخرین مقالات</h2>
                <p class="section-subtitle">مطالب آموزشی و اخبار مالی</p>
            </div>
            
            <div class="blog-grid">
                <?php foreach ($latest_articles as $article): ?>
                <div class="blog-card">
                    <div class="blog-image">
                        <img src="assets/images/blog/<?php echo $article['image']; ?>" alt="<?php echo $article['title']; ?>">
                    </div>
                    <div class="blog-content">
                        <span class="blog-date"><?php echo $article['date']; ?></span>
                        <h3 class="blog-title"><?php echo $article['title']; ?></h3>
                        <p class="blog-excerpt"><?php echo $article['excerpt']; ?></p>
                        <a href="<?php echo $article['link']; ?>" class="read-more">ادامه مطلب <i class="fas fa-chevron-left"></i></a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-4">
                <a href="blog.php" class="btn btn-primary">مشاهده همه مقالات</a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- بخش CTA پایانی -->


<!-- لینک به فایل JS صفحه اصلی -->
<script src="assets/js/home.js"></script>

<?php
require_once 'includes/footer.php';
?>