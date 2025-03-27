<?php
// بررسی وضعیت لاگین کاربر
$is_logged_in = isset($_SESSION['user_id']);
$current_year = date('Y');
?>

<footer class="modern-footer">
    <div class="footer-container">
        <!-- بخش بالایی فوتر -->
        <div class="footer-top">
            <!-- ستون اطلاعات تماس -->
            <div class="footer-column">
                <div class="footer-column-header">
                    <i class="fas fa-map-marker-alt"></i>
                    <h4 class="footer-title">ارتباط با ما</h4>
                </div>
                <ul class="footer-links">
                    <li class="footer-link-item">
                        <i class="fas fa-phone"></i>
                        <span>تلفن پشتیبانی: <a href="tel:02112345678">۰۲۱-۱۲۳۴۵۶۷۸</a></span>
                    </li>
                    <li class="footer-link-item">
                        <i class="fas fa-envelope"></i>
                        <span>ایمیل: <a href="mailto:info@bageet.com">info@bageet.com</a></span>
                    </li>
                    <li class="footer-link-item">
                        <i class="fas fa-clock"></i>
                        <span>ساعات کاری: شنبه تا چهارشنبه ۸:۳۰ تا ۱۶:۳۰</span>
                    </li>
                    <li class="footer-link-item">
                        <i class="fas fa-building"></i>
                        <span>آدرس: تهران، خیابان آزادی، کوچه شهید حسینی، پلاک ۱۲</span>
                    </li>
                </ul>
                
                <div class="footer-social-links">
                    <a href="#" class="social-link" title="اینستاگرام">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="social-link" title="تلگرام">
                        <i class="fab fa-telegram"></i>
                    </a>
                    <a href="#" class="social-link" title="توییتر">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-link" title="آپارات">
                        <i class="fab fa-aparat"></i>
                    </a>
                    <a href="#" class="social-link" title="لینکدین">
                        <i class="fab fa-linkedin"></i>
                    </a>
                </div>
            </div>
            
            <!-- ستون لینک‌های سریع -->
            <div class="footer-column">
                <div class="footer-column-header">
                    <i class="fas fa-link"></i>
                    <h4 class="footer-title">لینک‌های سریع</h4>
                </div>
                <ul class="footer-links">
                    <li class="footer-link-item">
                        <a href="about.php">درباره ما</a>
                    </li>
                    <li class="footer-link-item">
                        <a href="contact.php">تماس با ما</a>
                    </li>
                    <li class="footer-link-item">
                        <a href="faq.php">سوالات متداول</a>
                    </li>
                    <li class="footer-link-item">
                        <a href="blog.php">وبلاگ</a>
                    </li>
                    <li class="footer-link-item">
                        <a href="terms.php">قوانین و مقررات</a>
                    </li>
                    <li class="footer-link-item">
                        <a href="privacy.php">حریم خصوصی</a>
                    </li>
                </ul>
            </div>
            
            <!-- ستون خدمات -->
            <div class="footer-column">
                <div class="footer-column-header">
                    <i class="fas fa-hand-holding-usd"></i>
                    <h4 class="footer-title">خدمات ما</h4>
                </div>
                <ul class="footer-links">
                    <li class="footer-link-item">
                        <a href="fast-loan.php">وام فوری</a>
                    </li>
                    <li class="footer-link-item">
                        <a href="gold-loan.php">وام طلا</a>
                    </li>
                    <li class="footer-link-item">
                        <a href="business-loan.php">وام کسب و کار</a>
                    </li>
                    <li class="footer-link-item">
                        <a href="investment.php">سرمایه‌گذاری</a>
                    </li>
                    <li class="footer-link-item">
                        <a href="insurance.php">بیمه اعتباری</a>
                    </li>
                    <li class="footer-link-item">
                        <a href="installments.php">اقساط من</a>
                    </li>
                </ul>
            </div>
            
            <!-- ستون عضویت در خبرنامه -->
            <div class="footer-column newsletter-column">
                <div class="footer-column-header">
                    <i class="fas fa-envelope-open-text"></i>
                    <h4 class="footer-title">خبرنامه ایمیلی</h4>
                </div>
                <p class="newsletter-desc">
                    با عضویت در خبرنامه از آخرین تخفیف‌ها و پیشنهادات ویژه ما مطلع شوید
                </p>
                
                <form class="newsletter-form" id="newsletterForm">
                    <div class="form-group">
                        <input type="email" name="email" placeholder="آدرس ایمیل شما" required>
                        <button type="submit" class="newsletter-submit">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                    <div class="form-message" id="newsletterMessage"></div>
                </form>
                
                <div class="payment-methods">
                    <h5 class="payment-title">درگاه‌های پرداخت</h5>
                    <div class="payment-icons">
                        <img src="images/saman.png" alt="درگاه سامان" class="payment-icon">
                        <img src="images/melli.png" alt="درگاه ملی" class="payment-icon">
                        <img src="images/pasargad.png" alt="درگاه پاسارگاد" class="payment-icon">
                        <img src="images/mellat.png" alt="درگاه ملت" class="payment-icon">
                        <img src="images/zarinpal.png" alt="زرین پال" class="payment-icon">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- بخش پایینی فوتر -->
        <div class="footer-bottom">
            <div class="footer-copyright">
                <p>
                    کلیه حقوق این سامانه متعلق به © <span id="currentYear"><?php echo $current_year; ?></span> 
                    <a href="index.php" class="copyright-link">باجیت</a> می‌باشد.
                </p>
            </div>
            
            <div class="footer-logo">
                <a href="index.php">
                    <img src="assets/images/logo-footer.png" alt="باجیت" class="footer-logo-img">
                </a>
            </div>
            
            <div class="footer-certificates">
                <a href="#" class="certificate" title="نماد اعتماد الکترونیکی">
                    <img src="images/enamad.png" alt="نماد اعتماد الکترونیکی" class="certificate-img">
                </a>
                <a href="#" class="certificate" title="لوگوی ساماندهی">
                    <img src="images/samandehi.png" alt="لوگوی ساماندهی" class="certificate-img">
                </a>
                <a href="#" class="certificate" title="SSL امن">
                    <img src="images/ssl.png" alt="SSL امن" class="certificate-img">
                </a>
            </div>
        </div>
    </div>
    
    <!-- دکمه بازگشت به بالا -->
    <button class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <!-- چت آنلاین پشتیبانی -->
    <div class="support-chat">
        <button class="chat-button" id="supportChatButton">
            <i class="fas fa-headset"></i>
            <span class="chat-label">پشتیبانی آنلاین</span>
        </button>
        
        <div class="chat-box" id="supportChatBox">
            <div class="chat-header">
                <h5>پشتیبانی آنلاین</h5>
                <button class="close-chat" id="closeChat">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="chat-messages" id="chatMessages">
                <div class="chat-welcome">
                    <p>سلام! چطور می‌تونم کمکتون کنم؟</p>
                    <small><?php echo date('H:i'); ?></small>
                </div>
            </div>
            <div class="chat-input">
                <input type="text" placeholder="پیام شما..." id="chatInput">
                <button id="sendMessage">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
</footer>

<!-- اسکریپت‌های فوتر -->
<script>
    // بازگشت به بالا
    const backToTop = document.getElementById('backToTop');
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            backToTop.classList.add('show');
        } else {
            backToTop.classList.remove('show');
        }
    });
    
    backToTop.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // مدیریت چت پشتیبانی
    const chatButton = document.getElementById('supportChatButton');
    const chatBox = document.getElementById('supportChatBox');
    const closeChat = document.getElementById('closeChat');
    
    if (chatButton && chatBox) {
        chatButton.addEventListener('click', () => {
            chatBox.classList.toggle('active');
        });
        
        closeChat.addEventListener('click', () => {
            chatBox.classList.remove('active');
        });
    }
    
    // ارسال پیام چت
    const chatInput = document.getElementById('chatInput');
    const sendMessage = document.getElementById('sendMessage');
    const chatMessages = document.getElementById('chatMessages');
    
    if (sendMessage && chatInput) {
        sendMessage.addEventListener('click', () => {
            const message = chatInput.value.trim();
            if (message) {
                // نمایش پیام کاربر
                const userMessage = document.createElement('div');
                userMessage.className = 'chat-message user-message';
                userMessage.innerHTML = `
                    <p>${message}</p>
                    <small>${new Date().toLocaleTimeString('fa-IR', {hour: '2-digit', minute:'2-digit'})}</small>
                `;
                chatMessages.appendChild(userMessage);
                
                // شبیه‌سازی پاسخ پشتیبانی
                setTimeout(() => {
                    const botMessage = document.createElement('div');
                    botMessage.className = 'chat-message bot-message';
                    botMessage.innerHTML = `
                        <p>پیام شما دریافت شد. همکاران ما به زودی با شما تماس خواهند گرفت.</p>
                        <small>${new Date().toLocaleTimeString('fa-IR', {hour: '2-digit', minute:'2-digit'})}</small>
                    `;
                    chatMessages.appendChild(botMessage);
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }, 1500);
                
                chatInput.value = '';
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        });
        
        // ارسال با اینتر
        chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                sendMessage.click();
            }
        });
    }
    
    // مدیریت فرم خبرنامه
    const newsletterForm = document.getElementById('newsletterForm');
    const newsletterMessage = document.getElementById('newsletterMessage');
    
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(newsletterForm);
            
            newsletterMessage.innerHTML = '<i class="fas fa-spinner fa-spin"></i> در حال ارسال...';
            newsletterMessage.className = 'form-message processing';
            
            // شبیه‌سازی ارسال به سرور
            setTimeout(() => {
                newsletterMessage.innerHTML = 'عضویت شما در خبرنامه با موفقیت انجام شد!';
                newsletterMessage.className = 'form-message success';
                newsletterForm.reset();
                
                setTimeout(() => {
                    newsletterMessage.innerHTML = '';
                    newsletterMessage.className = 'form-message';
                }, 5000);
            }, 1500);
        });
    }
    
    // به‌روزرسانی سال جاری
    document.getElementById('currentYear').textContent = new Date().getFullYear();
</script>