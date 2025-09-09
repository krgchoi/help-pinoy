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
    .text-container {
    background-color: rgba(255, 255, 255, 0.8);
    border-radius: 5px;
    }
</style>

<div class="container mt-5 mb-5 text-container" style="padding-top: 120px; min-height: 70vh;">
    <h2><?php echo htmlspecialchars($news_item['title']); ?></h2>
    <p class="text-muted">By <?php echo htmlspecialchars($news_item['author']); ?> | Published on <?php echo htmlspecialchars($news_item['published_at']); ?></p>
    <?php if (!empty($news_item['image_url'])): ?>
        <img src="http://localhost:5000/static/news_img/<?php echo htmlspecialchars($news_item['image_url']); ?>" alt="News" class="img-fluid mb-3">
    <?php else: ?>
        <img src="../assets/img/default-news.jpg" alt="Default News Image" class="img-fluid mb-3">
    <?php endif; ?>
    <div>
        <?php echo nl2br(htmlspecialchars($news_item['content'])); ?>
    </div>
    <hr>
    <p><strong>Category:</strong> <?php echo htmlspecialchars($news_item['category']); ?></p>
    <?php if (!empty($news_item['tags'])): ?>
        <p><strong>Tags:</strong> <?php echo htmlspecialchars($news_item['tags']); ?></p>
    <?php endif; ?>
</div>

<?php include './template/footer.php'; ?>