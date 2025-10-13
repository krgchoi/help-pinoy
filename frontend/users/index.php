<?php 
include './template/header.php';

// Fetch news
$api_url = 'http://localhost:5000/user/get_news';
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
curl_close($ch);
$news = json_decode($response, true);

// Fetch centers
$url = "http://localhost:5000/user/user_get_locations";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
curl_close($ch);
$centers = json_decode($response, true);
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">

<style>
/* ===== Hero Section ===== */
.hero-section {
    height: 100vh;
    background: url('../assets/img/donation-banner.jpg') center/cover no-repeat;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-align: center;
    overflow: hidden;
}
.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to bottom right, rgba(0,0,0,0.6), rgba(0,0,0,0.3));
    z-index: 1;
}
.hero-content {
    position: relative;
    z-index: 2;
    max-width: 700px;
}
.hero-content h1 {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 1rem;
    letter-spacing: 1px;
}
.hero-content p {
    font-size: 1.25rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}
.hero-content .btn-warning {
    background-color: #ffc107;
    border: none;
    padding: 0.75rem 2rem;
    font-weight: 600;
    border-radius: 50px;
    transition: background-color 0.3s;
}
.hero-content .btn-warning:hover {
    background-color: #ffb300;
}

/* ===== News Section ===== */
.news-section {
    padding: 80px 0;
    background-color: #fff;
}
.news-section h2 {
    font-weight: 700;
    text-align: center;
    margin-bottom: 50px;
    color: #333;
}
.news-card {
    transition: all 0.3s ease;
    border: none;
    overflow: hidden;
    border-radius: 12px;
}
.news-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
.news-card img {
    height: 220px;
    object-fit: cover;
}
.news-card .card-body {
    padding: 20px;
}
.news-card .card-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #333;
}
.news-card .card-text {
    color: #666;
    font-size: 0.95rem;
    margin-top: 10px;
    margin-bottom: 15px;
}
.news-card .btn-primary {
    background-color: #007bff;
    border: none;
    font-weight: 600;
}
.news-card .btn-primary:hover {
    background-color: #0069d9;
}

/* ===== Map Section ===== */
.map-section {
    background: #f9fafb;
    padding: 80px 0;
}
.map-section h2 {
    text-align: center;
    font-weight: 700;
    margin-bottom: 40px;
    color: #333;
}
.map-box {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}
.locate-btn {
    display: inline-block;
    margin-top: 20px;
    padding: 12px 35px;
    background-color: #28a745;
    color: white;
    font-weight: 600;
    border-radius: 50px;
    text-decoration: none;
    transition: background-color 0.3s;
}
.locate-btn:hover {
    background-color: #218838;
}
</style>

<!-- Hero Section -->
<section class="hero-section" data-aos="fade-up" data-aos-duration="1200">
    <div class="hero-overlay"></div>
    <div class="hero-content" data-aos="fade-down" data-aos-delay="200">
        <h1>Together, We Shape the Future</h1>
        <p>Help build brighter days for children — one smile at a time.</p>
        <a href="donation_form.php" class="btn btn-warning btn-lg">Donate Now</a>
    </div>
</section>

<!-- News Section -->
<section class="news-section" data-aos="fade-up" data-aos-duration="1000">
    <div class="container">
        <h2>Latest News & Stories</h2>
        <div class="row">
            <?php if (is_array($news) && count($news) > 0): ?>
                <?php foreach ($news as $index => $item): ?>
                    <div class="col-md-4 mb-4" data-aos="zoom-in" data-aos-delay="<?= 100 + ($index * 100) ?>">
                        <div class="card news-card h-100">
                            <img src="<?= !empty($item['image_url']) 
                                ? 'http://localhost:5000/static/news_img/' . htmlspecialchars($item['image_url']) 
                                : '../assets/img/default-news.jpg'; ?>" 
                                class="card-img-top" alt="News Image" 
                                onerror="this.src='../assets/img/default-news.jpg'">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($item['title']); ?></h5>
                                <p class="card-text"><?= htmlspecialchars(mb_strimwidth($item['summary'] ?? '', 0, 100, '...')); ?></p>
                                <a href="single_news.php?slug=<?= urlencode($item['slug']); ?>" class="btn btn-primary">Read More</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center text-muted">No stories available at the moment.</div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="map-section" data-aos="fade-up" data-aos-duration="1000">
    <div class="container">
        <h2>Find Our Nearest Center</h2>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div id="map" class="map-box" style="height: 400px;"></div>
                <script>
                    window.mapConfig = {
                        centers: <?= json_encode($centers); ?>,
                        enableSearch: false,
                        enableSort: false,
                        showUserLocation: false
                    };
                </script>
                <?php include './template/map.php'; ?>
                <div class="text-center">
                    <a href="./centers.php" class="locate-btn">Locate Us</a>
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
});
</script>

<?php include './template/footer.php'; ?>
