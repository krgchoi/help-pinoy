<?php
include './template/header.php';

$api_url = 'http://localhost:5000/user/news_users';
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
?>

<style>
    :root {
        --primary-blue: #003366;
        --secondary-blue: #0057b7;
        --accent-yellow: #FFCC00;
        --light-blue: #e6f2ff;
        --dark-gray: #2c3e50;
        --light-gray: #f8f9fa;
    }

    .news-hero {
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
        color: white;
        padding: 120px 0 80px 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .news-hero::before {
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

    .news-hero-content {
        position: relative;
        z-index: 2;
    }

    .news-hero h1 {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 20px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .news-hero p {
        font-size: 1.3rem;
        opacity: 0.9;
        max-width: 600px;
        margin: 0 auto;
    }

    .news-container {
        background: var(--light-gray);
        padding: 80px 0;
        min-height: 60vh;
    }

    .section-header {
        text-align: center;
        margin-bottom: 60px;
    }

    .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary-blue);
        margin-bottom: 15px;
        position: relative;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: var(--accent-yellow);
        border-radius: 2px;
    }

    .section-subtitle {
        font-size: 1.2rem;
        color: #6c757d;
        max-width: 600px;
        margin: 0 auto;
    }

    /* News Cards */
    .news-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 30px;
        margin-bottom: 50px;
    }

    .news-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .news-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
    }

    .news-card-image {
        position: relative;
        height: 250px;
        overflow: hidden;
    }

    .news-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .news-card:hover .news-card-image img {
        transform: scale(1.1);
    }

    .news-card-category {
        position: absolute;
        top: 20px;
        left: 20px;
        background: linear-gradient(135deg, var(--accent-yellow) 0%, #ffd700 100%);
        color: var(--primary-blue);
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .news-card-content {
        padding: 30px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .news-card-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--dark-gray);
        line-height: 1.4;
        margin-bottom: 15px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .news-card-summary {
        color: #6c757d;
        line-height: 1.6;
        margin-bottom: 20px;
        flex: 1;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .news-card-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
        padding-top: 20px;
        border-top: 1px solid #f0f0f0;
    }

    .news-card-author {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .news-card-author i {
        color: var(--secondary-blue);
    }

    .news-card-date {
        color: #6c757d;
        font-size: 0.9rem;
    }

    .news-read-more {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--secondary-blue);
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        padding: 10px 0;
    }

    .news-read-more:hover {
        color: var(--primary-blue);
        gap: 12px;
    }

    .news-read-more i {
        transition: transform 0.3s ease;
    }

    .news-read-more:hover i {
        transform: translateX(5px);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }

    .empty-state-icon {
        font-size: 4rem;
        color: #dee2e6;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        color: var(--dark-gray);
        margin-bottom: 10px;
    }

    .empty-state p {
        color: #6c757d;
        margin-bottom: 30px;
    }

    /* Filter and Search (for future enhancement) */
    .news-filters {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-bottom: 40px;
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: 10px 20px;
        border: 2px solid var(--light-blue);
        background: white;
        color: var(--secondary-blue);
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .filter-btn:hover,
    .filter-btn.active {
        background: var(--secondary-blue);
        color: white;
        border-color: var(--secondary-blue);
    }

    /* Navigation styling for news pages */
    .news-navbar {
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(10px);
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
    }

    .news-navbar .nav-link,
    .news-navbar .navbar-brand,
    .news-navbar .btn {
        color: var(--primary-blue) !important;
    }

    .news-navbar .nav-link.active {
        color: var(--secondary-blue) !important;
        font-weight: 700;
    }

    .news-navbar .nav-link:hover {
        color: var(--accent-yellow) !important;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .news-hero {
            padding: 100px 0 60px 0;
        }

        .news-hero h1 {
            font-size: 2.5rem;
        }

        .news-hero p {
            font-size: 1.1rem;
        }

        .news-container {
            padding: 60px 0;
        }

        .section-title {
            font-size: 2rem;
        }

        .news-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .news-card-content {
            padding: 20px;
        }

        .news-card-title {
            font-size: 1.2rem;
        }
    }

    @media (max-width: 576px) {
        .news-hero h1 {
            font-size: 2rem;
        }

        .section-title {
            font-size: 1.8rem;
        }

        .news-card-image {
            height: 200px;
        }
    }

    /* Loading animation */
    .news-card-skeleton {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        height: 400px;
        position: relative;
        overflow: hidden;
    }

    .skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }

    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
</style>

<!-- Hero Section -->
<section class="news-hero">
    <div class="container">
        <div class="news-hero-content">
            <h1>Latest Stories & Updates</h1>
            <p>Stay informed with the latest news, stories, and announcements from our community</p>
        </div>
    </div>
</section>

<!-- News Section -->
<section class="news-container">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Recent News</h2>
            <p class="section-subtitle">Discover the latest happenings and impactful stories from Help Pinoy</p>
        </div>

        <?php if (!empty($result) && is_array($result)) : ?>
            <div class="news-grid">
                <?php foreach ($result as $news) : ?>
                    <article class="news-card">
                        <div class="news-card-image">
                            <?php if (!empty($news['image_url'])): ?>
                                <img src="http://localhost:5000/static/news_img/<?php echo htmlspecialchars($news['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($news['title']); ?>"
                                     onerror="this.src='../assets/img/default-news.jpg'">
                            <?php else: ?>
                                <img src="../assets/img/default-news.jpg" alt="Default News Image">
                            <?php endif; ?>
                            
                            <?php if (!empty($news['category'])): ?>
                                <div class="news-card-category">
                                    <?php echo htmlspecialchars($news['category']); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="news-card-content">
                            <h3 class="news-card-title">
                                <?php echo htmlspecialchars($news['title']); ?>
                            </h3>
                            
                            <p class="news-card-summary">
                                <?php echo htmlspecialchars($news['summary']); ?>
                            </p>
                            
                            <a href="single_news.php?slug=<?php echo urlencode($news['slug']); ?>" class="news-read-more">
                                Read Full Story <i class="fas fa-arrow-right"></i>
                            </a>
                            
                            <div class="news-card-meta">
                                <div class="news-card-author">
                                    <i class="fas fa-user"></i>
                                    <span><?php echo htmlspecialchars($news['author']); ?></span>
                                </div>
                                <div class="news-card-date">
                                    <i class="fas fa-calendar"></i>
                                    <?php echo htmlspecialchars($news['published_at']); ?>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

        <?php else : ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-newspaper"></i>
                </div>
                <h3>No News Available</h3>
                <p>There are no news articles at the moment. Please check back later for updates.</p>
                <a href="index.php" class="btn btn-primary">Return to Homepage</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include './template/footer.php'; ?>

<script>
    // Add navbar styling for news pages
    document.addEventListener('DOMContentLoaded', function() {
        const navbar = document.querySelector('.main-navbar');
        if (navbar) {
            navbar.classList.add('news-navbar');
        }

        // Add loading animation for images
        const images = document.querySelectorAll('.news-card-image img');
        images.forEach(img => {
            img.addEventListener('load', function() {
                this.style.opacity = '1';
            });
            
            // Set initial opacity for fade-in effect
            img.style.opacity = '0';
            img.style.transition = 'opacity 0.3s ease';
            
            // If image is already loaded (cached)
            if (img.complete) {
                img.style.opacity = '1';
            }
        });

        // Add intersection observer for fade-in animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe news cards for animation
        document.querySelectorAll('.news-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });
    });
</script>