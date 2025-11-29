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
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            color: white;
            position: relative;
            overflow: hidden;
            flex-shrink: 0;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color), var(--warning-color));
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
        }

        .footer .social-icon:hover {
            background: var(--warning-color);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
        }

        .footer .social-icon i {
            transition: var(--transition);
        }

        .footer .social-icon:hover i {
            color: #000 !important;
        }

        .footer .btn-donate {
            background: linear-gradient(135deg, var(--warning-color), #ffb300);
            border: none;
            color: #000;
            font-weight: 600;
            padding: 10px 24px;
            border-radius: 25px;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
        }

        .footer .btn-donate:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4);
            background: linear-gradient(135deg, #ffb300, var(--warning-color));
        }

        .footer .section-title {
            position: relative;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .footer .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background: var(--warning-color);
            border-radius: 2px;
        }

        .footer .section-title.center::after {
            left: 50%;
            transform: translateX(-50%);
        }

        .footer .section-title.right::after {
            left: auto;
            right: 0;
        }

        .footer .quick-links li {
            margin-bottom: 8px;
        }

        .footer .quick-links a {
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.8);
            transition: var(--transition);
        }

        .footer .quick-links a::before {
            content: '▸';
            margin-right: 8px;
            color: var(--warning-color);
            transition: var(--transition);
        }

        .footer .quick-links a:hover {
            color: var(--warning-color);
            transform: translateX(5px);
        }

        .footer .quick-links a:hover::before {
            transform: scale(1.2);
        }

        .footer .contact-info {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            color: rgba(255, 255, 255, 0.8);
        }

        .footer .contact-info i {
            color: var(--warning-color);
            margin-right: 12px;
            font-size: 1.1rem;
            width: 20px;
        }

        .footer .logo-text {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--warning-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 15px;
        }

        .footer .tagline {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.8);
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
            border-radius: 25px;
            padding: 10px 20px;
            color: white;
            transition: var(--transition);
        }

        .footer .newsletter-input:focus {
            outline: none;
            border-color: var(--warning-color);
            box-shadow: 0 0 0 2px rgba(255, 193, 7, 0.2);
        }

        .footer .newsletter-input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .footer .newsletter-btn {
            background: var(--warning-color);
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            color: #000;
            font-weight: 600;
            transition: var(--transition);
            white-space: nowrap;
        }

        .footer .newsletter-btn:hover {
            background: #ffb300;
            transform: translateY(-2px);
        }

        .footer .divider {
            border-color: rgba(255, 255, 255, 0.2);
            margin: 2rem 0;
        }

        .footer .back-to-top {
            position: absolute;
            right: 30px;
            top: -20px;
            background: var(--warning-color);
            color: #000;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .footer .back-to-top:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4);
        }

        @media (max-width: 768px) {
            .footer .text-md-start, 
            .footer .text-md-end {
                text-align: center !important;
            }
            
            .footer .section-title::after {
                left: 50% !important;
                transform: translateX(-50%);
            }
            
            .footer .newsletter-form {
                flex-direction: column;
            }
            
            .footer .back-to-top {
                right: 20px;
                top: -15px;
                width: 35px;
                height: 35px;
            }
        }
    </style>
</head>
<body>
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
                            <h6 class="fw-bold mb-3">Stay Updated</h6>
                            <form class="newsletter-form">
                                <input type="email" class="newsletter-input" placeholder="Your email address" required>
                                <button type="submit" class="newsletter-btn">Subscribe</button>
                            </form>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="col-lg-2 col-md-6 text-center text-md-start">
                        <h5 class="section-title fw-bold">Quick Links</h5>
                        <ul class="list-unstyled quick-links mb-0">
                            <li><a href="about.php" class="text-decoration-none">About Us</a></li>
                            <li><a href="news.php" class="text-decoration-none">What We've Done</a></li>
                            <li><a href="donation.php" class="text-decoration-none">Donate</a></li>
                            <li><a href="volunteer.php" class="text-decoration-none">Volunteer</a></li>
                            <li><a href="contact.php" class="text-decoration-none">Contact Us</a></li>
                        </ul>
                    </div>

                    <!-- Resources -->
                    <div class="col-lg-2 col-md-6 text-center text-md-start">
                        <h5 class="section-title fw-bold">Resources</h5>
                        <ul class="list-unstyled quick-links mb-0">
                            <li><a href="faq.php" class="text-decoration-none">FAQ</a></li>
                            <li><a href="blog.php" class="text-decoration-none">Blog</a></li>
                            <li><a href="resources.php" class="text-decoration-none">Resources</a></li>
                            <li><a href="partners.php" class="text-decoration-none">Partners</a></li>
                            <li><a href="privacy.php" class="text-decoration-none">Privacy Policy</a></li>
                        </ul>
                    </div>

                    <!-- Contact and Social -->
                    <div class="col-lg-4 col-md-6 text-center text-md-end">
                        <h5 class="section-title right fw-bold">Get in Touch</h5>
                        
                        <div class="contact-info">
                            <i class="bi bi-envelope-fill"></i>
                            <div>
                                <a href="mailto:support@helppinoy.org" class="text-white text-decoration-none">support@helppinoy.org</a>
                            </div>
                        </div>
                        
                        <div class="contact-info">
                            <i class="bi bi-telephone-fill"></i>
                            <div>+63 (2) 1234-5678</div>
                        </div>
                        
                        <div class="contact-info">
                            <i class="bi bi-geo-alt-fill"></i>
                            <div>Manila, Philippines</div>
                        </div>

                        <div class="mt-4 mb-4">
                            <a href="#" class="social-icon me-2">
                                <i class="bi bi-facebook text-white"></i>
                            </a>
                            <a href="#" class="social-icon me-2">
                                <i class="bi bi-twitter text-white"></i>
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

                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start">
                        <p class="mb-0">&copy; <?php echo date('Y'); ?> Help Pinoy. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <div class="d-flex justify-content-center justify-content-md-end align-items-center">
                            <span class="me-3">Accepted Payment Methods:</span>
                            <div>
                                <i class="bi bi-credit-card-2-front me-2 fs-5" title="Credit Cards"></i>
                                <i class="bi bi-paypal me-2 fs-5" title="PayPal"></i>
                                <i class="bi bi-bank me-2 fs-5" title="Bank Transfer"></i>
                                <i class="bi bi-phone me-2 fs-5" title="Mobile Payment"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

    <script>
        // Back to top functionality
        document.querySelector('.back-to-top').addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Show/hide back to top button based on scroll position
        window.addEventListener('scroll', function() {
            const backToTop = document.querySelector('.back-to-top');
            if (window.scrollY > 300) {
                backToTop.style.opacity = '1';
                backToTop.style.visibility = 'visible';
            } else {
                backToTop.style.opacity = '0';
                backToTop.style.visibility = 'hidden';
            }
        });

        // Initialize - hide back to top button on page load
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.back-to-top').style.opacity = '0';
            document.querySelector('.back-to-top').style.visibility = 'hidden';
        });

        // Newsletter form submission
        document.querySelector('.newsletter-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('.newsletter-input').value;
            if (email) {
                alert('Thank you for subscribing to our newsletter!');
                this.querySelector('.newsletter-input').value = '';
            }
        });
    </script>