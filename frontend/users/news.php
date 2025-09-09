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
    /* Text to be black */
    .body{
        background-color: rgba(255, 255, 255, 0.8);
    }
    .main-navbar .nav-link,
    .main-navbar .navbar-brand,
    .main-navbar .btn {
        color: #222 !important;
    }
    .main-navbar .nav-link.active {
        color: #1976d2 !important;
    }
    .main-navbar .nav-link:hover {
        color: #f39c12 !important;
    }
    .transparent-navbar {
        background-color: rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(10px);
    }
    .transparent-navbar .nav-link,
    .transparent-navbar .navbar-brand,
    .transparent-navbar .btn {
        color: black !important;
    }
    .transparent-navbar .nav-link.active {
        color: #1976d2 !important;
    }
    .transparent-navbar .nav-link:hover {
        color: #f39c12 !important;
    }

    .news-section {
        min-height: 60vh;
        padding-bottom: 40px;
    }
    .news-card {
        background: #fff;
        border: none;
        border-radius: 18px;
        box-shadow: 0 4px 24px rgba(25, 118, 210, 0.08), 0 1.5px 4px rgba(60,72,88,0.06);
        margin-bottom: 32px;
        transition: box-shadow 0.2s, transform 0.2s;
        display: flex;
        flex-direction: column;
        height: 100%;
        padding: 0;
    }
    .news-card:hover {
        box-shadow: 0 8px 32px rgba(25, 118, 210, 0.15), 0 2px 8px rgba(60,72,88,0.10);
        transform: translateY(-4px) scale(1.01);
    }
    .news-card img {
        border-top-left-radius: 18px;
        border-top-right-radius: 18px;
        width: 100%;
        height: 220px;
        object-fit: cover;
    }
    .news-card .card-body {
        padding: 22px 22px 10px 22px;
        flex: 1 1 auto;
        display: flex;
        flex-direction: column;
    }
    .news-card .card-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1976d2;
        margin-bottom: 10px;
    }
    .news-card .card-text {
        font-size: 1rem;
        color: #444;
        margin-bottom: 16px;
        flex: 1 1 auto;
    }
    .news-card .card-link {
        font-size: 0.98rem;
        color: #1976d2;
        font-weight: 600;
        text-decoration: none;
        margin-top: auto;
        transition: color 0.2s;
    }
    .news-card .card-link:hover {
        color: #f39c12;
        text-decoration: underline;
    }
    .news-card .card-footer {
        background: #f4f6fb;
        border-top: none;
        border-radius: 0 0 18px 18px;
        padding: 12px 22px;
        font-size: 0.95rem;
        color: #6c757d;
    }
    @media (max-width: 767px) {
        .news-card img { height: 160px; }
        .news-card .card-body { padding: 14px 10px 8px 10px; }
        .news-card .card-footer { padding: 8px 10px; }
    }
</style>

<div class="container news-section" style="padding-top: 120px;">
    <?php if (isset($result) && is_array($result)) :  ?>
        <div class="row g-4">
            <?php foreach ($result as $news) : ?>
                <div class="col-md-4 d-flex">
                    <div class="news-card w-100">
                        <?php if (!empty($news['image_url'])): ?>
                            <img src="http://localhost:5000/static/news_img/<?php echo htmlspecialchars($news['image_url']); ?>" alt="News Image" class="img-fluid">
                        <?php else: ?>
                            <img src="../assets/img/default-news.jpg" alt="Default News Image" class="img-fluid">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($news['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($news['summary']); ?></p>
                            <a href="single_news.php?slug=<?php echo urlencode($news['slug']); ?>" class="card-link">Read more</a>
                        </div>
                        <div class="card-footer text-muted">
                            <small>Published on <?php echo htmlspecialchars($news['published_at']); ?></small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="alert alert-info">No Stories available at the moment.</div>
    <?php endif; ?>
</div>

<?php include './template/footer.php'; ?>