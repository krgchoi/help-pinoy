<?php include './template/header.php'; ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
    :root {
        --primary-blue: #003366;
        --secondary-blue: #0057b7;
        --accent-yellow: #FFCC00;
        --success-green: #28a745;
        --light-blue: #e6f2ff;
        --dark-gray: #2c3e50;
        --light-gray: #f8f9fa;
    }

    .thankyou-hero {
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
        color: white;
        padding: 120px 0 80px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .thankyou-hero::before {
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

    .thankyou-hero-content {
        position: relative;
        z-index: 2;
    }

    .thankyou-section {
        background: var(--light-gray);
        padding: 80px 0;
        min-height: 60vh;
        display: flex;
        align-items: center;
    }

    .thankyou-card {
        background: white;
        border-radius: 25px;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
        padding: 60px 50px;
        text-align: center;
        position: relative;
        overflow: hidden;
        border: none;
        max-width: 700px;
        margin: 0 auto;
    }

    .thankyou-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 8px;
        background: linear-gradient(90deg, var(--success-green) 0%, var(--accent-yellow) 100%);
    }

    .thankyou-icon {
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, var(--success-green) 0%, #34ce57 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 30px;
        color: white;
        font-size: 3.5rem;
        box-shadow: 0 15px 35px rgba(40, 167, 69, 0.3);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .thankyou-card h1 {
        font-size: 3rem;
        font-weight: 800;
        color: var(--dark-gray);
        margin-bottom: 20px;
        background: linear-gradient(135deg, var(--success-green) 0%, var(--primary-blue) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .thankyou-card .lead {
        font-size: 1.3rem;
        color: #6c757d;
        line-height: 1.7;
        margin-bottom: 30px;
    }

    .impact-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 20px;
        margin: 40px 0;
        padding: 30px;
        background: var(--light-gray);
        border-radius: 15px;
        border: 2px solid var(--light-blue);
    }

    .stat-item {
        text-align: center;
    }

    .stat-number {
        font-size: 2.2rem;
        font-weight: 800;
        color: var(--primary-blue);
        display: block;
        line-height: 1;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-return {
        background: linear-gradient(135deg, var(--accent-yellow) 0%, #ffd700 100%);
        border: none;
        color: var(--primary-blue);
        padding: 16px 45px;
        font-weight: 700;
        font-size: 1.1rem;
        border-radius: 15px;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(255, 204, 0, 0.3);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin-top: 20px;
    }

    .btn-return:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(255, 204, 0, 0.4);
        background: linear-gradient(135deg, #ffd700 0%, var(--accent-yellow) 100%);
        color: var(--primary-blue);
        text-decoration: none;
    }

    .confetti {
        position: absolute;
        width: 10px;
        height: 10px;
        background: var(--accent-yellow);
        border-radius: 50%;
        animation: confetti-fall 5s linear infinite;
    }

    @keyframes confetti-fall {
        0% {
            transform: translateY(-100px) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: translateY(1000px) rotate(360deg);
            opacity: 0;
        }
    }

    .next-steps {
        background: white;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
        padding: 50px;
        margin-top: 60px;
        text-align: center;
    }

    .next-steps h3 {
        color: var(--primary-blue);
        font-weight: 700;
        margin-bottom: 30px;
        font-size: 1.8rem;
    }

    .steps-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 30px;
        margin-top: 30px;
    }

    .step-card {
        background: var(--light-gray);
        padding: 30px 20px;
        border-radius: 15px;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .step-card:hover {
        transform: translateY(-5px);
        border-color: var(--secondary-blue);
        background: white;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .step-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--secondary-blue) 0%, var(--primary-blue) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        color: white;
        font-size: 1.5rem;
    }

    .step-card h4 {
        color: var(--primary-blue);
        font-weight: 700;
        margin-bottom: 10px;
    }

    .step-card p {
        color: #6c757d;
        font-size: 0.95rem;
        margin: 0;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .thankyou-hero {
            padding: 100px 0 60px;
        }

        .thankyou-section {
            padding: 60px 0;
        }

        .thankyou-card {
            padding: 40px 30px;
            margin: 20px;
        }

        .thankyou-card h1 {
            font-size: 2.2rem;
        }

        .thankyou-icon {
            width: 100px;
            height: 100px;
            font-size: 2.8rem;
        }

        .impact-stats {
            grid-template-columns: repeat(2, 1fr);
            padding: 20px;
        }

        .next-steps {
            padding: 30px 20px;
            margin: 40px 20px 0;
        }

        .steps-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 576px) {
        .thankyou-card h1 {
            font-size: 1.8rem;
        }

        .thankyou-card .lead {
            font-size: 1.1rem;
        }

        .impact-stats {
            grid-template-columns: 1fr;
        }

        .stat-number {
            font-size: 1.8rem;
        }
    }

    /* Celebration animation */
    @keyframes celebrate {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    .celebrate {
        animation: celebrate 0.6s ease-in-out 3;
    }
</style>

<!-- Hero Section -->
<section class="thankyou-hero" data-aos="fade-up">
    <div class="container">
        <div class="thankyou-hero-content">
            <h1>Making a Difference Together</h1>
            <p class="lead">Your contribution helps create positive change in communities across the Philippines</p>
        </div>
    </div>
</section>

<!-- Thank You Section -->
<section class="thankyou-section">
    <div class="container">
        <div class="thankyou-card" data-aos="zoom-in" data-aos-delay="300">
            <div class="thankyou-icon">
                <i class="bi bi-heart-fill"></i>
            </div>
            
            <h1>Thank You for Your Generous Donation!</h1>
            
            <p class="lead">
                Your support means the world to us and will directly help provide relief and support 
                to families affected by disasters across the Philippines.
            </p>

            <!-- Impact Statistics -->
            <div class="impact-stats" data-aos="fade-up" data-aos-delay="600">
                <div class="stat-item">
                    <span class="stat-number">50+</span>
                    <span class="stat-label">Families Helped</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">15</span>
                    <span class="stat-label">Communities</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">100%</span>
                    <span class="stat-label">Direct Impact</span>
                </div>
            </div>

            <p class="text-muted mb-4">
                You'll receive a confirmation email shortly with your donation receipt and tracking details.
            </p>

            <a href="index.php" class="btn-return">
                <i class="bi bi-house-fill"></i>
                Return to Homepage
            </a>
        </div>

        <!-- Next Steps Section -->
        <div class="next-steps" data-aos="fade-up" data-aos-delay="900">
            <h3>Continue Making an Impact</h3>
            <p class="text-muted mb-4">Here are other ways you can help support our mission:</p>
            
            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-icon">
                        <i class="bi bi-share-fill"></i>
                    </div>
                    <h4>Share Our Mission</h4>
                    <p>Spread the word about Help Pinoy on social media and encourage others to contribute.</p>
                </div>
                
                <div class="step-card">
                    <div class="step-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <h4>Set Up Monthly</h4>
                    <p>Consider making your donation recurring to provide sustained support for ongoing relief efforts.</p>
                </div>
                
                <div class="step-card">
                    <div class="step-icon">
                        <i class="bi bi-newspaper"></i>
                    </div>
                    <h4>Stay Updated</h4>
                    <p>Follow our stories and updates to see how your donation is making a real difference.</p>
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

    // Celebration effects
    document.addEventListener('DOMContentLoaded', function() {
        // Add celebrate animation to the icon
        const thankyouIcon = document.querySelector('.thankyou-icon');
        thankyouIcon.classList.add('celebrate');

        // Create confetti effect
        createConfetti();

        // Add number counting animation
        animateNumbers();
    });

    function createConfetti() {
        const colors = ['#FFCC00', '#28a745', '#0057b7', '#dc3545', '#6f42c1'];
        const container = document.querySelector('.thankyou-card');
        
        for (let i = 0; i < 50; i++) {
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.left = Math.random() * 100 + '%';
            confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.animationDelay = Math.random() * 5 + 's';
            confetti.style.width = Math.random() * 10 + 5 + 'px';
            confetti.style.height = confetti.style.width;
            container.appendChild(confetti);
        }
    }

    function animateNumbers() {
        const statNumbers = document.querySelectorAll('.stat-number');
        
        statNumbers.forEach(stat => {
            const target = parseInt(stat.textContent);
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;
            
            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    clearInterval(timer);
                    stat.textContent = target + (stat.textContent.includes('+') ? '+' : '');
                } else {
                    stat.textContent = Math.floor(current) + (stat.textContent.includes('+') ? '+' : '');
                }
            }, 16);
        });
    }
</script>

<?php include './template/footer.php'; ?>