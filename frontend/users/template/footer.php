<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3a0ca3;
        --accent-color: #4cc9f0;
        --warning-color: #ffc107;
        --light-color: #f8f9fa;
        --dark-color: #212529;
        --transition: all 0.3s ease;
    }

    .footer {
        background: linear-gradient(135deg, #0a0a2a 0%, #0f1a3a 50%, #1a2a4a 100%);
        color: white;
        position: relative;
        overflow: hidden;
        flex-shrink: 0;
        margin-top: auto;
    }

    .footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--warning-color), var(--primary-color), var(--accent-color));
    }

    .footer::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--warning-color), transparent);
    }

    .footer a {
        transition: var(--transition);
        text-decoration: none;
    }

    .footer a:hover {
        color: var(--warning-color) !important;
        transform: translateX(5px);
    }

    .footer .social-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        transition: var(--transition);
        border: 1px solid rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(5px);
    }

    .footer .social-icon:hover {
        background: var(--warning-color);
        transform: translateY(-3px) rotate(360deg);
        box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
    }

    .footer .social-icon i {
        transition: var(--transition);
        font-size: 1.2rem;
    }

    .footer .social-icon:hover i {
        color: #000 !important;
        transform: scale(1.1);
    }

    .footer .btn-donate {
        background: linear-gradient(135deg, var(--warning-color), #ffb300);
        border: none;
        color: #000;
        font-weight: 700;
        padding: 12px 30px;
        border-radius: 50px;
        transition: var(--transition);
        box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
        letter-spacing: 1px;
    }

    .footer .btn-donate:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4);
        background: linear-gradient(135deg, #ffb300, var(--warning-color));
    }

    .footer .section-title {
        position: relative;
        padding-bottom: 12px;
        margin-bottom: 25px;
        font-size: 1.25rem;
        font-weight: 700;
    }

    .footer .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background: var(--warning-color);
        border-radius: 2px;
        transition: var(--transition);
    }

    .footer .text-md-start .section-title::after {
        left: 0;
        transform: none;
    }

    .footer .text-md-end .section-title::after {
        left: auto;
        right: 0;
    }

    .footer .section-title.center::after {
        left: 50%;
        transform: translateX(-50%);
    }

    .footer .quick-links {
        list-style: none;
        padding: 0;
    }

    .footer .quick-links li {
        margin-bottom: 12px;
    }

    .footer .quick-links a {
        display: flex;
        align-items: center;
        color: rgba(255, 255, 255, 0.8);
        transition: var(--transition);
        font-size: 0.95rem;
    }

    .footer .quick-links a::before {
        content: '▸';
        margin-right: 10px;
        color: var(--warning-color);
        transition: var(--transition);
        font-size: 0.9rem;
    }

    .footer .quick-links a:hover {
        color: var(--warning-color);
        transform: translateX(8px);
    }

    .footer .quick-links a:hover::before {
        transform: scale(1.2);
        margin-right: 12px;
    }

    .footer .contact-info {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        color: rgba(255, 255, 255, 0.8);
        transition: var(--transition);
    }

    .footer .contact-info:hover {
        transform: translateX(5px);
    }

    .footer .contact-info i {
        color: var(--warning-color);
        margin-right: 12px;
        font-size: 1.2rem;
        width: 25px;
        text-align: center;
    }

    .footer .logo-text {
        font-size: 2rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--warning-color), var(--accent-color), #fff);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 15px;
        letter-spacing: 1px;
    }

    .footer .tagline {
        font-size: 0.95rem;
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 20px;
        line-height: 1.6;
    }

    .footer .newsletter-form {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .footer .newsletter-input {
        flex: 1;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 50px;
        padding: 12px 20px;
        color: white;
        transition: var(--transition);
        font-size: 0.9rem;
    }

    .footer .newsletter-input:focus {
        outline: none;
        border-color: var(--warning-color);
        box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.1);
        background: rgba(255, 255, 255, 0.15);
    }

    .footer .newsletter-input::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }

    .footer .newsletter-btn {
        background: var(--warning-color);
        border: none;
        border-radius: 50px;
        padding: 12px 25px;
        color: #000;
        font-weight: 700;
        transition: var(--transition);
        white-space: nowrap;
        font-size: 0.9rem;
    }

    .footer .newsletter-btn:hover {
        background: #ffb300;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
    }

    .footer .divider {
        border-color: rgba(255, 255, 255, 0.1);
        margin: 2rem 0;
    }

    .footer .back-to-top {
        position: absolute;
        right: 30px;
        top: -20px;
        background: linear-gradient(135deg, var(--warning-color), #ffb300);
        color: #000;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: var(--transition);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        z-index: 10;
        opacity: 0;
        visibility: hidden;
    }

    .footer .back-to-top:hover {
        transform: translateY(-5px) scale(1.1);
        box-shadow: 0 8px 25px rgba(255, 193, 7, 0.4);
    }

    .payment-methods i {
        font-size: 1.5rem;
        transition: var(--transition);
        cursor: pointer;
    }

    .payment-methods i:hover {
        transform: scale(1.2);
        color: var(--warning-color);
    }

    @media (max-width: 768px) {

        .footer .text-md-start,
        .footer .text-md-end {
            text-align: center !important;
        }

        .footer .section-title::after {
            left: 50% !important;
            transform: translateX(-50%);
            width: 60px;
        }

        .footer .newsletter-form {
            flex-direction: column;
        }

        .footer .back-to-top {
            right: 20px;
            top: -15px;
            width: 40px;
            height: 40px;
        }

        .footer .contact-info {
            justify-content: center;
        }

        .footer .quick-links a {
            justify-content: center;
        }
    }
</style>

<!-- Footer -->
<footer class="footer">
    <a href="#" class="back-to-top">
        <i class="bi bi-arrow-up"></i>
    </a>

    <div class="container py-5">
        <div class="row gy-5 align-items-start">
            <!-- Logo and Description -->
            <div class="col-lg-4 col-md-6 text-center text-md-start">
                <div class="logo-text">Help Pinoy</div>
                <p class="tagline">Making a difference, one donation at a time. Together, we can build a better future for every Filipino in need.</p>

                <!-- Newsletter Subscription -->
                <div class="newsletter-section">
                    <h6 class="fw-bold mb-3" style="color: var(--warning-color);">
                        <i class="bi bi-envelope-paper-fill me-2"></i>Stay Updated
                    </h6>
                    <form class="newsletter-form" id="newsletterForm">
                        <input type="email" class="newsletter-input" placeholder="Enter your email address" required>
                        <button type="submit" class="newsletter-btn">
                            <i class="bi bi-send-fill me-2"></i>Subscribe
                        </button>
                    </form>
                    <small class="text-white-50 mt-2 d-block">No spam, unsubscribe anytime.</small>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6 text-center text-md-start">
                <h5 class="section-title fw-bold">Quick Links</h5>
                <ul class="quick-links">
                    <li><a href="<?= BASE_URL ?>about.php">About Us</a></li>
                    <li><a href="<?= BASE_URL ?>news.php">What We've Done</a></li>
                    <li><a href="<?= BASE_URL ?>donation_form.php">Donate</a></li>
                    <li><a href="<?= BASE_URL ?>volunteer.php">Volunteer</a></li>
                    <li><a href="<?= BASE_URL ?>contact.php">Contact Us</a></li>
                </ul>
            </div>

            <!-- Resources -->
            <div class="col-lg-2 col-md-6 text-center text-md-start">
                <h5 class="section-title fw-bold">Resources</h5>
                <ul class="quick-links">
                    <li><a href="faq.php">FAQ</a></li>
                    <li><a href="resources.php">Resources</a></li>
                    <li><a href="privacy.php">Privacy Policy</a></li>
                    <li><a href="terms.php">Terms of Service</a></li>
                </ul>
            </div>

            <!-- Contact and Social -->
            <div class="col-lg-4 col-md-6 text-center text-md-end">
                <h5 class="section-title fw-bold">Get in Touch</h5>

                <div class="contact-info">
                    <i class="bi bi-envelope-fill"></i>
                    <div>
                        <a href="mailto:support@helppinoy.org" class="text-white-50 text-decoration-none">
                            support@helppinoy.org
                        </a>
                    </div>
                </div>

                <div class="contact-info">
                    <i class="bi bi-telephone-fill"></i>
                    <div>+63 912 3456 789</div>
                </div>

                <div class="contact-info">
                    <i class="bi bi-geo-alt-fill"></i>
                    <div>Guimbal, Iloilo, Philippines</div>
                </div>

                <div class="mt-4 mb-4">
                    <a href="#" class="social-icon me-2">
                        <i class="bi bi-facebook text-white"></i>
                    </a>
                    <a href="#" class="social-icon me-2">
                        <i class="bi bi-twitter-x text-white"></i>
                    </a>
                    <a href="#" class="social-icon me-2">
                        <i class="bi bi-instagram text-white"></i>
                    </a>
                    <a href="#" class="social-icon">
                        <i class="bi bi-youtube text-white"></i>
                    </a>
                </div>

                <a href="donation_form.php" class="btn btn-donate">
                    <i class="bi bi-heart-fill me-2"></i>Donate Now
                </a>
            </div>
        </div>

        <hr class="divider">

        <div class="row align-items-center gy-3">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0 text-white-50">
                    &copy; <?php echo date('Y'); ?> Help Pinoy. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <div class="d-flex justify-content-center justify-content-md-end align-items-center flex-wrap gap-3">
                    <span class="text-white-50">Accepted Payments:</span>
                    <div class="payment-methods">
                        <i class="bi bi-credit-card-2-front me-2 fs-4" title="Credit Cards"></i>
                        <i class="bi bi-paypal me-2 fs-4" title="PayPal"></i>
                        <i class="bi bi-bank me-2 fs-4" title="Bank Transfer"></i>
                        <i class="bi bi-phone fs-4" title="GCash/PayMaya"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Back to top button
        const backToTop = document.querySelector('.back-to-top');

        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTop.style.opacity = '1';
                backToTop.style.visibility = 'visible';
            } else {
                backToTop.style.opacity = '0';
                backToTop.style.visibility = 'hidden';
            }
        });

        backToTop.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Newsletter form submission
        const newsletterForm = document.getElementById('newsletterForm');
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const email = this.querySelector('.newsletter-input').value;
                const btn = this.querySelector('.newsletter-btn');
                const originalText = btn.innerHTML;

                if (email) {
                    btn.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Subscribed!';
                    btn.style.background = '#28a745';

                    setTimeout(() => {
                        btn.innerHTML = originalText;
                        btn.style.background = '';
                        this.querySelector('.newsletter-input').value = '';
                    }, 3000);

                    // Here you would typically send the email to your backend
                    console.log('Newsletter subscription:', email);
                }
            });
        }
    });
</script>