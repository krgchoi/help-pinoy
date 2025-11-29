<?php
include './template/header.php';

if (!isset($_GET['slug'])) {
    echo "<div class='alert alert-danger'>No news selected.</div>";
    include './template/footer.php';
    exit();
}

$slug = $_GET['slug'];
$api_url = 'http://localhost:5000/user/news_users';
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
$news_item = null;
if (is_array($result)) {
    foreach ($result as $news) {
        if ($news['slug'] === $slug) {
            $news_item = $news;
            break;
        }
    }
}

if (!$news_item) {
    echo "<div class='alert alert-danger'>News not found.</div>";
    include './template/footer.php';
    exit();
}
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

    .news-article-hero {
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
        color: white;
        padding: 120px 0 80px 0;
        position: relative;
        overflow: hidden;
    }

    .news-article-hero::before {
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

    .news-article-container {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        margin: -60px auto 80px;
        position: relative;
        overflow: hidden;
    }

    .article-header {
        padding: 60px 60px 40px;
        border-bottom: 1px solid #e9ecef;
    }

    .article-title {
        font-size: 3rem;
        font-weight: 800;
        color: var(--primary-blue);
        line-height: 1.2;
        margin-bottom: 20px;
    }

    .article-meta {
        display: flex;
        align-items: center;
        gap: 30px;
        margin-bottom: 10px;
        flex-wrap: wrap;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #6c757d;
        font-weight: 500;
    }

    .meta-item i {
        color: var(--secondary-blue);
    }

    .article-image {
        width: 100%;
        height: 500px;
        object-fit: cover;
        border-radius: 15px;
        margin: 30px 0;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .article-image:hover {
        transform: scale(1.02);
    }

    .article-content {
        padding: 40px 60px 60px;
        font-size: 1.2rem;
        line-height: 1.8;
        color: var(--dark-gray);
    }

    .article-content p {
        margin-bottom: 1.5rem;
    }

    .article-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin: 30px 0;
    }

    .tag {
        background: var(--light-blue);
        color: var(--secondary-blue);
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .tag:hover {
        background: var(--secondary-blue);
        color: white;
        transform: translateY(-2px);
    }

    .article-category {
        background: linear-gradient(135deg, var(--accent-yellow) 0%, #ffd700 100%);
        color: var(--primary-blue);
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: 700;
        display: inline-block;
        margin-bottom: 20px;
    }

    .back-to-news {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    color: white !important;
    text-decoration: none;
    font-weight: 600;
    margin-bottom: 30px;
    transition: all 0.3s ease;
    padding: 12px 24px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    position: relative;
    z-index: 100;
}

.back-to-news:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.5);
    transform: translateX(-5px);
    color: white !important;
    text-decoration: none;
}

    .social-share {
        display: flex;
        gap: 15px;
        margin: 40px 0;
        padding: 20px;
        background: var(--light-gray);
        border-radius: 15px;
    }

    .share-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        color: white;
    }

    .share-btn.facebook { background: #3b5998; }
    .share-btn.twitter { background: #1da1f2; }
    .share-btn.linkedin { background: #0077b5; }
    .share-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
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
        .article-header {
            padding: 40px 30px 30px;
        }

        .article-content {
            padding: 30px 30px 40px;
        }

        .article-title {
            font-size: 2.2rem;
        }

        .article-image {
            height: 300px;
        }

        .article-meta {
            gap: 15px;
        }

        .news-article-hero {
            padding: 100px 0 60px 0;
        }

        .news-article-container {
            margin: -40px auto 60px;
        }
    }

    @media (max-width: 576px) {
        .article-header {
            padding: 30px 20px 20px;
        }

        .article-content {
            padding: 20px 20px 30px;
            font-size: 1.1rem;
        }

        .article-title {
            font-size: 1.8rem;
        }

        .article-image {
            height: 250px;
        }

        .social-share {
            flex-direction: column;
        }
    }

    /* Reading progress bar */
    .reading-progress {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: transparent;
        z-index: 9999;
    }

    .reading-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, var(--accent-yellow) 0%, var(--secondary-blue) 100%);
        width: 0%;
        transition: width 0.3s ease;
    }

    /* Content styling */
    .article-content h2 {
        color: var(--primary-blue);
        margin: 2.5rem 0 1.5rem 0;
        font-weight: 700;
    }

    .article-content h3 {
        color: var(--secondary-blue);
        margin: 2rem 0 1rem 0;
        font-weight: 600;
    }

    .article-content blockquote {
        border-left: 4px solid var(--accent-yellow);
        padding-left: 20px;
        margin: 2rem 0;
        font-style: italic;
        color: #6c757d;
        background: var(--light-gray);
        padding: 20px;
        border-radius: 0 10px 10px 0;
    }
</style>

<!-- Reading Progress Bar -->
<div class="reading-progress">
    <div class="reading-progress-bar" id="readingProgress"></div>
</div>

<!-- Hero Section -->
<section class="news-article-hero">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <a href="<?php echo dirname($_SERVER['PHP_SELF']) . '/news.php'; ?>" class="back-to-news">
    <i class="fas fa-arrow-left"></i>
    Back to All Stories
</a>
                <div class="article-category">
                    <?php echo htmlspecialchars($news_item['category']); ?>
                </div>
                <h1 class="article-title"><?php echo htmlspecialchars($news_item['title']); ?></h1>
                <div class="article-meta">
                    <div class="meta-item">
                        <i class="fas fa-user"></i>
                        <span>By <?php echo htmlspecialchars($news_item['author']); ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-calendar"></i>
                        <span><?php echo htmlspecialchars($news_item['published_at']); ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-clock"></i>
                        <span><?php echo estimateReadingTime($news_item['content']); ?> min read</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Article Content -->
<div class="container">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="news-article-container">
                <!-- Article Image -->
                <?php if (!empty($news_item['image_url'])): ?>
                    <img src="http://localhost:5000/static/news_img/<?php echo htmlspecialchars($news_item['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($news_item['title']); ?>" 
                         class="article-image">
                <?php else: ?>
                    <img src="../assets/img/default-news.jpg" 
                         alt="Default News Image" 
                         class="article-image">
                <?php endif; ?>

                <!-- Article Content -->
                <div class="article-content">
                    <?php echo formatNewsContent($news_item['content']); ?>
                </div>

                <!-- Tags -->
                <?php if (!empty($news_item['tags'])): ?>
                    <div class="article-tags">
                        <?php 
                        $tags = explode(',', $news_item['tags']);
                        foreach ($tags as $tag): 
                        ?>
                            <span class="tag"><?php echo htmlspecialchars(trim($tag)); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Social Share -->
                <div class="social-share">
                    <span class="me-3 fw-bold text-dark">Share this story:</span>
                    <a href="#" class="share-btn facebook" onclick="shareOnFacebook()">
                        <i class="fab fa-facebook-f"></i> Facebook
                    </a>
                    <a href="#" class="share-btn twitter" onclick="shareOnTwitter()">
                        <i class="fab fa-twitter"></i> Twitter
                    </a>
                    <a href="#" class="share-btn linkedin" onclick="shareOnLinkedIn()">
                        <i class="fab fa-linkedin-in"></i> LinkedIn
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// Helper functions
function estimateReadingTime($content) {
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // 200 words per minute
    return max(1, $reading_time); // Minimum 1 minute
}

function formatNewsContent($content) {
    // Convert line breaks to paragraphs
    $paragraphs = explode("\n", trim($content));
    $formatted = '';
    
    foreach ($paragraphs as $paragraph) {
        if (trim($paragraph) !== '') {
            $formatted .= '<p>' . nl2br(htmlspecialchars($paragraph)) . '</p>';
        }
    }
    
    return $formatted;
}
?>

<?php include './template/footer.php'; ?>

<script>
    // Reading progress bar
    window.addEventListener('scroll', function() {
        const winHeight = window.innerHeight;
        const docHeight = document.documentElement.scrollHeight;
        const scrollTop = window.pageYOffset;
        const trackLength = docHeight - winHeight;
        const progress = Math.floor((scrollTop / trackLength) * 100);
        
        document.getElementById('readingProgress').style.width = progress + '%';
    });

    // Social sharing functions
    function shareOnFacebook() {
        const url = encodeURIComponent(window.location.href);
        const title = encodeURIComponent("<?php echo addslashes($news_item['title']); ?>");
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}&quote=${title}`, '_blank');
    }

    function shareOnTwitter() {
        const url = encodeURIComponent(window.location.href);
        const text = encodeURIComponent("<?php echo addslashes($news_item['title']); ?>");
        window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank');
    }

    function shareOnLinkedIn() {
        const url = encodeURIComponent(window.location.href);
        window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank');
    }

    // Add navbar styling for news pages
    document.addEventListener('DOMContentLoaded', function() {
        const navbar = document.querySelector('.main-navbar');
        if (navbar) {
            navbar.classList.add('news-navbar');
        }
    });
</script>