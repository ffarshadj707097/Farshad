<?php
$page_title = 'سرمایه‌گذاری هوشمند';
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

// دریافت اطلاعات کاربر
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// تابع تبدیل اعداد به فارسی
function toPersianNumbers($number) {
    $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    return str_replace($english, $persian, $number);
}

// پردازش فرم سرمایه‌گذاری
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user['account_status'] === 'verified') {
    $amount = (int) str_replace(',', '', $_POST['amount']);
    $duration = (int) $_POST['duration'];
    $plan_type = $_POST['plan_type'];
    
    // ذخیره اطلاعات درخواست در session
    $_SESSION['investment_request'] = [
        'amount' => $amount,
        'duration' => $duration,
        'plan_type' => $plan_type
    ];
    
    // انتقال به صفحه تایید
    header("Location: confirm-investment.php");
    exit;
}

require_once 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

<main class="investment-page">
    <div class="container">
        <!-- هدر صفحه -->
        <div class="page-header text-center">
            <h1 class="animate__animated animate__fadeInDown">سرمایه‌گذاری <span class="text-primary">هوشمند</span></h1>
            <p class="lead animate__animated animate__fadeInUp">سودهای بالا با ریسک کنترل‌شده</p>
            <div class="header-illustration">
                <img src="assets/images/investment-illustration.svg" alt="سرمایه‌گذاری" class="img-fluid">
            </div>
        </div>
        
        <?php if ($user['account_status'] !== 'verified'): ?>
            <!-- پیغام برای حساب‌های تایید نشده -->
            <div class="verification-alert animate__animated animate__shakeX">
                <div class="alert-content">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        <h4>حساب کاربری شما تایید نشده است!</h4>
                        <p>برای استفاده از خدمات سرمایه‌گذاری، لطفا ابتدا حساب کاربری خود را تایید کنید.</p>
                    </div>
                </div>
                <a href="profile.php" class="btn btn-danger">تایید حساب کاربری</a>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <!-- بخش اصلی فرم -->
            <div class="col-lg-8 <?php echo $user['account_status'] !== 'verified' ? 'blur-content' : ''; ?>">
                <div class="investment-form-card animate__animated animate__fadeInLeft">
                    <div class="card-header">
                        <h2><i class="fas fa-file-signature"></i> فرم سرمایه‌گذاری</h2>
                        <div class="progress-steps">
                            <div class="step active">1</div>
                            <div class="step">2</div>
                            <div class="step">3</div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <form method="post" id="investmentForm">
                            <!-- اسلایدر مبلغ سرمایه‌گذاری -->
                            <div class="form-group with-icon">
                                <label for="investmentAmount">
                                    <i class="fas fa-coins"></i> مبلغ سرمایه‌گذاری
                                </label>
                                <div class="slider-container">
                                    <input type="range" id="investmentAmount" name="amount" 
                                           min="1000000" max="500000000" step="1000000" 
                                           value="50000000" class="custom-slider">
                                    <div class="slider-labels">
                                        <span>۱ میلیون</span>
                                        <span>۵۰۰ میلیون</span>
                                    </div>
                                </div>
                                <div class="range-value-box">
                                    <span id="investmentAmountValue">۵۰,۰۰۰,۰۰۰</span>
                                    <span>تومان</span>
                                </div>
                            </div>
                            
                            <!-- اسلایدر مدت سرمایه‌گذاری -->
                            <div class="form-group with-icon">
                                <label for="investmentDuration">
                                    <i class="fas fa-calendar-alt"></i> مدت سرمایه‌گذاری
                                </label>
                                <div class="slider-container">
                                    <input type="range" id="investmentDuration" name="duration" 
                                           min="3" max="36" step="1" value="12" class="custom-slider">
                                    <div class="slider-labels">
                                        <span>۳ ماه</span>
                                        <span>۳۶ ماه</span>
                                    </div>
                                </div>
                                <div class="range-value-box">
                                    <span id="investmentDurationValue">۱۲</span>
                                    <span>ماه</span>
                                </div>
                            </div>
                            
                            <!-- نوع طرح سرمایه‌گذاری -->
                            <div class="form-group with-icon">
                                <label>
                                    <i class="fas fa-shield-alt"></i> نوع طرح سرمایه‌گذاری
                                </label>
                                <div class="investment-options">
                                    <div class="investment-option">
                                        <input type="radio" id="fixed_return" name="plan_type" 
                                               value="fixed_return" checked>
                                        <label for="fixed_return">
                                            <div class="option-icon">
                                                <i class="fas fa-lock"></i>
                                            </div>
                                            <div class="option-content">
                                                <h5>سود ثابت</h5>
                                                <p>سود تضمین‌شده ۱۸٪ سالانه</p>
                                                <div class="option-badge">پیشنهادی</div>
                                            </div>
                                        </label>
                                    </div>
                                    
                                    <div class="investment-option">
                                        <input type="radio" id="variable_return" name="plan_type" value="variable_return">
                                        <label for="variable_return">
                                            <div class="option-icon">
                                                <i class="fas fa-chart-bar"></i>
                                            </div>
                                            <div class="option-content">
                                                <h5>سود متغیر</h5>
                                                <p>سود تا ۲۵٪ سالانه (متوسط ۲۱٪)</p>
                                            </div>
                                        </label>
                                    </div>
                                    
                                    <div class="investment-option">
                                        <input type="radio" id="monthly_income" name="plan_type" value="monthly_income">
                                        <label for="monthly_income">
                                            <div class="option-icon">
                                                <i class="fas fa-wallet"></i>
                                            </div>
                                            <div class="option-content">
                                                <h5>درآمد ماهیانه</h5>
                                                <p>دریافت سود ماهیانه ۱.۵٪</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary btn-lg btn-block btn-with-icon">
                                    <i class="fas fa-arrow-left"></i> مرحله بعد: تایید نهایی
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- اطلاعات تکمیلی -->
                <div class="info-boxes">
                    <div class="info-box">
                        <i class="fas fa-shield-alt"></i>
                        <h4>ضمانت سرمایه</h4>
                        <p>سرمایه شما با بیمه تا سقف ۲۰۰ میلیون تومان تضمین شده است</p>
                    </div>
                    <div class="info-box">
                        <i class="fas fa-percentage"></i>
                        <h4>سود بالا</h4>
                        <p>بهترین نرخ سود بازار با شرایط ویژه برای سرمایه‌گذاران</p>
                    </div>
                    <div class="info-box">
                        <i class="fas fa-clock"></i>
                        <h4>نقدشوندگی</h4>
                        <p>امکان برداشت سرمایه پس از ۳ ماه با کمترین جریمه</p>
                    </div>
                </div>
            </div>
            
            <!-- خلاصه سرمایه‌گذاری -->
            <div class="col-lg-4">
                <div class="investment-summary-card animate__animated animate__fadeInRight">
                    <div class="card-header">
                        <h2><i class="fas fa-calculator"></i> خلاصه سرمایه‌گذاری</h2>
                    </div>
                    <div class="card-body">
                        <div class="summary-item">
                            <span>مبلغ سرمایه‌گذاری:</span>
                            <span id="summaryAmount">۵۰,۰۰۰,۰۰۰ تومان</span>
                        </div>
                        <div class="summary-item">
                            <span>مدت سرمایه‌گذاری:</span>
                            <span id="summaryDuration">۱۲ ماه</span>
                        </div>
                        <div class="summary-item">
                            <span>نوع طرح:</span>
                            <span id="summaryPlan">سود ثابت</span>
                        </div>
                        <div class="summary-item">
                            <span>نرخ سود سالانه:</span>
                            <span id="summaryRate" class="text-success">۱۸٪</span>
                        </div>
                        <div class="summary-item">
                            <span>سود کل:</span>
                            <span id="summaryProfit">۹,۰۰۰,۰۰۰ تومان</span>
                        </div>
                        <div class="summary-item">
                            <span>کل مبلغ دریافتی:</span>
                            <span id="summaryTotal" class="text-primary">۵۹,۰۰۰,۰۰۰ تومان</span>
                        </div>
                        
                        <div class="summary-chart">
                            <canvas id="investmentChart"></canvas>
                        </div>
                        
                        <div class="summary-note">
                            <i class="fas fa-info-circle"></i>
                            <p>این محاسبات بر اساس نرخ سود فعلی است و ممکن است تغییر کند</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- بخش مقایسه طرح‌ها -->
        <div class="plans-comparison mt-5">
            <h3 class="section-title"><i class="fas fa-balance-scale"></i> مقایسه طرح‌های سرمایه‌گذاری</h3>
            
            <div class="comparison-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ویژگی</th>
                            <th>سود ثابت</th>
                            <th>سود متغیر</th>
                            <th>درآمد ماهیانه</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>نرخ سود سالانه</td>
                            <td>۱۸٪ تضمینی</td>
                            <td>۱۵٪ تا ۲۵٪</td>
                            <td>۱۸٪ معادل ۱.۵٪ ماهیانه</td>
                        </tr>
                        <tr>
                            <td>مدت حداقل</td>
                            <td>۳ ماه</td>
                            <td>۶ ماه</td>
                            <td>۳ ماه</td>
                        </tr>
                        <tr>
                            <td>پرداخت سود</td>
                            <td>پایان دوره</td>
                            <td>پایان دوره</td>
                            <td>ماهیانه</td>
                        </tr>
                        <tr>
                            <td>امکان برداشت زودتر</td>
                            <td>دارد (با جریمه ۲٪)</td>
                            <td>ندارد</td>
                            <td>دارد (با جریمه ۱٪)</td>
                        </tr>
                        <tr>
                            <td>مناسب برای</td>
                            <td>افراد محافظه‌کار</td>
                            <td>افراد ریسک‌پذیر</td>
                            <td>نیاز به درآمد ماهیانه</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- استایل‌های اختصاصی -->
<style>
/* استایل کلی صفحه */
.investment-page {
    padding: 30px 0 60px;
    background: linear-gradient(to bottom, #f8f9fa, #f0f9f0);
}

/* هدر صفحه */
.page-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 10px;
}

.page-header .lead {
    font-size: 1.2rem;
    color: #6c757d;
}

.header-illustration {
    max-width: 400px;
    margin: 20px auto 0;
}

.header-illustration img {
    width: 100%;
    height: auto;
}

/* کارت فرم سرمایه‌گذاری */
.investment-form-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    margin-bottom: 30px;
    overflow: hidden;
    border: none;
}

.investment-form-card .card-header {
    background: linear-gradient(to right, #28a745, #218838);
}

/* گزینه‌های سرمایه‌گذاری */
.investment-options {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.investment-option input[type="radio"] {
    display: none;
}

.investment-option label {
    display: flex;
    padding: 15px;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
}

.investment-option input[type="radio"]:checked + label {
    border-color: #28a745;
    background: rgba(40, 167, 69, 0.05);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.1);
}

.investment-option input[type="radio"]:checked + label:after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 3px;
    height: 100%;
    background: #28a745;
}

.investment-option .option-icon {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
}

/* خلاصه سرمایه‌گذاری */
.investment-summary-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    margin-bottom: 30px;
    overflow: hidden;
    border: none;
    position: sticky;
    top: 20px;
}

.investment-summary-card .card-header {
    background: linear-gradient(to right, #28a745, #218838);
    color: white;
}

.summary-item span:last-child {
    font-weight: 600;
}

/* جدول مقایسه */
.plans-comparison {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
}

.section-title {
    font-size: 1.5rem;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
}

.section-title i {
    margin-left: 10px;
    color: #28a745;
}

.comparison-table {
    overflow-x: auto;
}

.comparison-table table {
    width: 100%;
    border-collapse: collapse;
}

.comparison-table th {
    background: #f8f9fa;
    padding: 12px 15px;
    text-align: center;
    border-bottom: 2px solid #dee2e6;
}

.comparison-table td {
    padding: 12px 15px;
    border-bottom: 1px solid #dee2e6;
    text-align: center;
}

.comparison-table tr:nth-child(even) {
    background-color: #f9f9f9;
}

.comparison-table tr:hover {
    background-color: #f1f1f1;
}

.comparison-table th:first-child,
.comparison-table td:first-child {
    text-align: right;
    font-weight: 500;
}

/* رسپانسیو */
@media (max-width: 992px) {
    .investment-options {
        grid-template-columns: 1fr;
    }
    
    .investment-summary-card {
        position: static;
        margin-top: 30px;
    }
}

@media (max-width: 768px) {
    .page-header h1 {
        font-size: 2rem;
    }
    
    .comparison-table table {
        font-size: 0.9rem;
    }
}
</style>

<!-- اسکریپت‌ها -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// مدیریت اسلایدرها و محاسبات
const investmentAmount = document.getElementById('investmentAmount');
const investmentAmountValue = document.getElementById('investmentAmountValue');
const investmentDuration = document.getElementById('investmentDuration');
const investmentDurationValue = document.getElementById('investmentDurationValue');
let investmentChart;

// به‌روزرسانی نمایش مبلغ سرمایه‌گذاری
investmentAmount.addEventListener('input', function() {
    const value = parseInt(this.value).toLocaleString('fa-IR');
    investmentAmountValue.textContent = value;
    updateInvestmentSummary();
});

// به‌روزرسانی نمایش مدت سرمایه‌گذاری
investmentDuration.addEventListener('input', function() {
    investmentDurationValue.textContent = this.value;
    updateInvestmentSummary();
});

// به‌روزرسانی نمایش نوع طرح
document.querySelectorAll('input[name="plan_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        updateInvestmentSummary();
    });
});

// محاسبه و نمایش خلاصه سرمایه‌گذاری
function updateInvestmentSummary() {
    const amount = parseInt(investmentAmount.value);
    const duration = parseInt(investmentDuration.value);
    const planType = document.querySelector('input[name="plan_type"]:checked').value;
    
    // محاسبه سود بر اساس نوع طرح
    let interestRate, profit, total;
    
    switch(planType) {
        case 'fixed_return':
            interestRate = 0.18; // 18% سالانه
            break;
        case 'variable_return':
            interestRate = 0.21; // متوسط 21% سالانه
            break;
        case 'monthly_income':
            interestRate = 0.18; // 18% سالانه (1.5% ماهیانه)
            break;
        default:
            interestRate = 0.18;
    }
    
    profit = amount * interestRate * (duration / 12);
    total = amount + profit;
    
    // به‌روزرسانی خلاصه
    document.getElementById('summaryAmount').textContent = amount.toLocaleString('fa-IR') + ' تومان';
    document.getElementById('summaryDuration').textContent = duration.toLocaleString('fa-IR') + ' ماه';
    document.getElementById('summaryPlan').textContent = getPlanName(planType);
    document.getElementById('summaryRate').textContent = (interestRate * 100).toLocaleString('fa-IR') + '%';
    document.getElementById('summaryProfit').textContent = Math.round(profit).toLocaleString('fa-IR') + ' تومان';
    document.getElementById('summaryTotal').textContent = Math.round(total).toLocaleString('fa-IR') + ' تومان';
    
    // به‌روزرسانی نمودار
    updateChart(amount, profit);
}

// تبدیل نام انگلیسی طرح به فارسی
function getPlanName(type) {
    const names = {
        'fixed_return': 'سود ثابت',
        'variable_return': 'سود متغیر',
        'monthly_income': 'درآمد ماهیانه'
    };
    return names[type] || '';
}

// ایجاد و به‌روزرسانی نمودار
function initChart() {
    const ctx = document.getElementById('investmentChart').getContext('2d');
    investmentChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['سرمایه اولیه', 'سود'],
            datasets: [{
                data: [50000000, 9000000],
                backgroundColor: ['#28a745', '#20c997'],
                borderWidth: 0
            }]
        },
        options: {
            cutoutPercentage: 70,
            legend: {
                display: false
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        const label = data.labels[tooltipItem.index];
                        const value = data.datasets[0].data[tooltipItem.index];
                        return label + ': ' + value.toLocaleString('fa-IR') + ' تومان';
                    }
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    });
}

function updateChart(amount, profit) {
    if (investmentChart) {
        investmentChart.data.datasets[0].data = [amount, profit];
        investmentChart.update();
    }
}

// مقداردهی اولیه
document.addEventListener('DOMContentLoaded', function() {
    updateInvestmentSummary();
    initChart();
});
</script>

<?php
require_once 'includes/footer.php';
?>