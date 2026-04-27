<?php include './template/header.php'; ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
    :root {
        --primary-blue: #003366;
        --secondary-blue: #0057b7;
        --accent-yellow: #FFCC00;
        --light-blue: #e6f2ff;
        --dark-gray: #2c3e50;
        --light-gray: #f8f9fa;
    }

    .contact-hero {
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
        color: white;
        padding: 120px 0 80px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .contact-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none"><path d="M1200 120L0 16.48 0 0 1200 0 1200 120z" fill="white"/></svg>');
        background-size: cover;
        background-position: bottom;
        opacity: 0.1;
    }

    .contact-hero-content {
        position: relative;
        z-index: 2;
    }

    .contact-hero h1 {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 20px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .contact-hero p {
        font-size: 1.3rem;
        opacity: 0.9;
        max-width: 600px;
        margin: 0 auto;
    }

    .contact-section {
        background: var(--light-gray);
        padding: 80px 0;
    }

    .contact-container {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 60px;
    }

    .contact-form-section {
        padding: 50px;
    }

    .contact-form-section h2 {
        font-weight: 700;
        color: var(--primary-blue);
        margin-bottom: 10px;
        font-size: 2.2rem;
    }

    .contact-form-section .lead {
        color: #6c757d;
        margin-bottom: 30px;
        font-size: 1.1rem;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-label {
        font-weight: 600;
        color: var(--dark-gray);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
    }

    .form-label i {
        margin-right: 8px;
        color: var(--secondary-blue);
    }

    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 16px;
        transition: all 0.3s ease;
        background: #f8f9fa;
    }

    .form-control:focus {
        border-color: var(--secondary-blue);
        box-shadow: 0 0 0 0.2rem rgba(0, 87, 183, 0.25);
        background: white;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 120px;
    }

    .btn-send {
        background: linear-gradient(135deg, var(--accent-yellow) 0%, #ffd700 100%);
        border: none;
        color: var(--primary-blue);
        padding: 15px 40px;
        font-weight: 700;
        font-size: 1.1rem;
        border-radius: 12px;
        transition: all 0.3s ease;
        width: 100%;
        box-shadow: 0 8px 25px rgba(255, 204, 0, 0.3);
    }

    .btn-send:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(255, 204, 0, 0.4);
        background: linear-gradient(135deg, #ffd700 0%, var(--accent-yellow) 100%);
    }

    .map-section {
        height: 100%;
        min-height: 500px;
        position: relative;
    }

    #map {
        height: 100%;
        width: 100%;
        border-radius: 0 20px 20px 0;
    }

    .contact-info-section {
        background: white;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
        padding: 50px 0;
    }

    .contact-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
    }

    .contact-info-card {
        text-align: center;
        padding: 30px 20px;
        background: var(--light-gray);
        border-radius: 15px;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .contact-info-card:hover {
        transform: translateY(-5px);
        border-color: var(--secondary-blue);
        background: white;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .contact-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, var(--secondary-blue) 0%, var(--primary-blue) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        color: white;
        font-size: 1.5rem;
    }

    .contact-info-card h4 {
        color: var(--primary-blue);
        font-weight: 700;
        margin-bottom: 10px;
    }

    .contact-info-card p {
        color: #6c757d;
        margin: 0;
        line-height: 1.6;
    }

    /* Loading state */
    .btn-loading {
        position: relative;
        color: transparent;
    }

    .btn-loading::after {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        top: 50%;
        left: 50%;
        margin-left: -10px;
        margin-top: -10px;
        border: 2px solid var(--primary-blue);
        border-radius: 50%;
        border-top-color: transparent;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Success/Error Messages */
    .alert-contact {
        border-radius: 12px;
        border: none;
        padding: 15px 20px;
        margin-bottom: 25px;
        border-left: 4px solid transparent;
    }

    .alert-success {
        background: rgba(40, 167, 69, 0.1);
        color: #28a745;
        border-left-color: #28a745;
    }

    .alert-error {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        border-left-color: #dc3545;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .contact-hero {
            padding: 100px 0 60px;
        }

        .contact-hero h1 {
            font-size: 2.5rem;
        }

        .contact-hero p {
            font-size: 1.1rem;
        }

        .contact-section {
            padding: 60px 0;
        }

        .contact-form-section {
            padding: 30px;
        }

        .contact-form-section h2 {
            font-size: 1.8rem;
        }

        .map-section {
            min-height: 400px;
            border-radius: 0 0 20px 20px;
        }

        #map {
            border-radius: 0 0 20px 20px;
        }

        .contact-info-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
    }

    @media (max-width: 576px) {
        .contact-hero h1 {
            font-size: 2rem;
        }

        .contact-form-section {
            padding: 20px;
        }

        .contact-info-section {
            padding: 30px 0;
        }
    }
</style>

<!-- Hero Section -->
<section class="contact-hero" data-aos="fade-up">
    <div class="container">
        <div class="contact-hero-content">
            <h1>Get In Touch</h1>
            <p>We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
        </div>
    </div>
</section>

<!-- Contact Form & Map Section -->
<section class="contact-section">
    <div class="container">
        <div class="contact-container" data-aos="fade-up" data-aos-delay="200">
            <div class="row g-0">
                <!-- Contact Form -->
                <div class="col-lg-6 contact-form-section">
                    <h2>Send us a Message</h2>
                    <p class="lead">Fill out the form below and we'll get back to you shortly.</p>

                    <form action="#" method="post" id="contactForm">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-person"></i>Full Name
                            </label>
                            <input type="text" name="name" class="form-control" placeholder="Enter your full name" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-envelope"></i>Email Address
                            </label>
                            <input type="email" name="email" class="form-control" placeholder="Enter your email address" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-chat-text"></i>Subject
                            </label>
                            <input type="text" name="subject" class="form-control" placeholder="What is this regarding?">
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-pencil"></i>Message
                            </label>
                            <textarea name="message" rows="6" class="form-control" placeholder="Tell us how we can help you..." required></textarea>
                        </div>

                        <button type="submit" class="btn-send" id="submitButton">
                            <span id="buttonText">Send Message</span>
                        </button>
                    </form>
                </div>

                <!-- Map Section -->
                <div class="col-lg-6 map-section">
                    <div id="map"></div>
                    <script>
                        window.mapConfig = {
                            centers: <?= json_encode($centers ?? []); ?>,
                            enableSearch: false,
                            enableSort: false,
                            showUserLocation: false
                        };
                    </script>
                    <?php include './template/map.php'; ?>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="contact-info-section" data-aos="fade-up" data-aos-delay="400">
            <div class="container">
                <div class="contact-info-grid">
                    <div class="contact-info-card">
                        <div class="contact-icon">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <h4>Our Location</h4>
                        <p>Iloilo City, Philippines<br>5000</p>
                    </div>

                    <div class="contact-info-card">
                        <div class="contact-icon">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <h4>Phone Number</h4>
                        <p>+63 912 345 6789<br>Mon - Fri, 9:00 AM - 6:00 PM</p>
                    </div>

                    <div class="contact-info-card">
                        <div class="contact-icon">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <h4>Email Address</h4>
                        <p>info@helppinoy.ph<br>support@helppinoy.ph</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
    AOS.init({
        once: true,
        duration: 1000,
        offset: 100
    });

    // Form submission handling
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        const button = document.getElementById('submitButton');
        const buttonText = document.getElementById('buttonText');

        // Show loading state
        button.classList.add('btn-loading');
        buttonText.textContent = 'Sending...';
        button.disabled = true;

        // In a real application, you would have AJAX submission here
        // For now, we'll simulate a successful submission
        setTimeout(() => {
            // Remove loading state (in real app, do this after server response)
            button.classList.remove('btn-loading');
            buttonText.textContent = 'Message Sent!';

            // Reset form (optional)
            // this.reset();

            // Re-enable button after 3 seconds
            setTimeout(() => {
                button.disabled = false;
                buttonText.textContent = 'Send Another Message';
            }, 3000);
        }, 2000);
    });
</script>

<?php include './template/footer.php'; ?>