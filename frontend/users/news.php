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
    .body{
        background-color: rgba(255, 255, 255, 0.8);
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
    .news-card {
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .news-card h5 {
        margin-top: 0;
    }

    .news-card p {
        margin-bottom: 0;
    }

    .news-card a {
        text-decoration: none;
        color: #007bff;
    }

    .news-card a:hover {
        text-decoration: underline;
    }

    .news-card img {
        max-width: 100%;
        height: auto;
        border-radius: 5px;
    }

    .news-card .card-body {
        padding: 15px;
    }

    .news-card .card-title {
        font-size: 1.25rem;
        margin-bottom: 10px;
    }

    .news-card .card-text {
        font-size: 1rem;
        margin-bottom: 10px;
    }

    .news-card .card-link {
        font-size: 0.9rem;
        color: #007bff;
    }

    .news-card .card-link:hover {
        text-decoration: underline;
    }

    .news-card .card-footer {
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
        padding: 10px 15px;
    }

    .news-card .card-footer small {
        color: #6c757d;
    }

    .news-card .card-footer a {
        color: #007bff;
    }

    .news-card .card-footer a:hover {
        text-decoration: underline;
    }
    
</style>

<div class="container" style="padding-top: 120px; !important; background-color: rgba(255, 255, 255, 0.8);">
    <?php if (isset($result) && is_array($result)) :  ?>
        <div class="row">
            <?php foreach ($result as $news) : ?>
                <div class="news-card col-md-4" style="margin-right: 30px;">
                    <?php if (!empty($news['image_url'])): ?>
                        <img src="http://localhost:5000/static/news_img/<?php echo htmlspecialchars($news['image_url']); ?>" alt="News Image" class="img-fluid mb-3">
                    <?php else: ?>
                        <img src="../assets/img/default-news.jpg" alt="Default News Image" class="img-fluid mb-3">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title">
                            <?php echo htmlspecialchars($news['title']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($news['summary']); ?></p>
                        <a href="single_news.php?slug=<?php echo urlencode($news['slug']); ?>" class="card-link">Read more</a>
                    </div>
                    <div class="card-footer text-muted">
                        <small>Published on <?php echo htmlspecialchars($news['published_at']); ?></small>
                    </div>
                </div>
               
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="alert alert-info">No Stories available at the moment.</div>
    <?php endif; ?>

</div>

<?php include './template/footer.php'; ?>