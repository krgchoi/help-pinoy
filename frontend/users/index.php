<?php
include './template/header.php';


$news = [];
$ch = curl_init(API_BASE . '/user/get_news');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 15,
]);
$response = curl_exec($ch);
curl_close($ch);

if ($response !== false) {
    $news = json_decode($response, true);
}

$centers = [];
$ch = curl_init(API_BASE . '/user/user_get_locations');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
]);
$response = curl_exec($ch);
curl_close($ch);

if ($response !== false) {
    $centers = json_decode($response, true);
}
?>


<link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">

<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* ===== Hero Section ===== */
    .hero-section {
        height: 100vh;
        background: url('/assets/img/donation-banner4.jpg') center/cover no-repeat;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: white;
    }

    .hero-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 30, 70, 0.55);
    }

    .hero-content {
        position: relative;
        z-index: 2;
        max-width: 700px;
    }

    .hero-content h1 {
        font-size: 3.2rem;
        font-weight: 700;
        margin-bottom: 1.2rem;
        text-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
    }

    .hero-content p {
        font-size: 1.3rem;
        opacity: 0.9;
        margin-bottom: 2rem;
    }

    .hero-content .btn-warning {
        background-color: #FFCC00;
        border: none;
        padding: 0.8rem 2.2rem;
        font-weight: 700;
        font-size: 1.1rem;
        border-radius: 40px;
        letter-spacing: 0.5px;
        transition: 0.3s ease;
        color: #003366;
    }

    .hero-content .btn-warning:hover {
        background-color: #E6B800;
    }

    /* ===== News Section ===== */
    .news-section {
        padding: 80px 0;
        background-color: #fff;
    }

    .news-section h2 {
        font-weight: 800;
        text-align: center;
        margin-bottom: 50px;
        color: #003366;
        letter-spacing: 1px;
    }

    .news-card {
        border: none;
        overflow: hidden;
        border-radius: 14px;
        transition: 0.3s ease;
        background: white;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
    }

    .news-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 14px 30px rgba(0, 0, 0, 0.12);
    }

    .news-card img {
        height: 220px;
        object-fit: cover;
    }

    .news-card .card-body {
        padding: 22px;
    }

    .news-card .card-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #003366;
    }

    .news-card .card-text {
        font-size: 0.95rem;
        color: #555;
        margin-top: 10px;
    }

    .news-card .btn-primary {
        background-color: #0057B7;
        border: none;
        font-weight: 700;
        padding: 8px 20px;
        border-radius: 8px;
    }

    .news-card .btn-primary:hover {
        background-color: #003F87;
    }

    /* ===== Map Section ===== */
    .map-section {
        background: #F4F7FB;
        padding: 80px 0;
    }

    .map-section h2 {
        text-align: center;
        font-weight: 800;
        margin-bottom: 40px;
        color: #003366;
        letter-spacing: 1px;
    }

    .map-box {
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 6px 22px rgba(0, 0, 0, 0.12);
        border: 2px solid #e6e6e6;
    }

    .locate-btn {
        display: inline-block;
        margin-top: 25px;
        padding: 12px 40px;
        background-color: #28A745;
        color: white;
        font-weight: 700;
        border-radius: 40px;
        text-decoration: none;
        letter-spacing: 0.5px;
        transition: 0.3s ease;
    }

    .locate-btn:hover {
        background-color: #1E7E34;
    }
</style>

<!-- Hero Section -->
<section class="hero-section" data-aos="fade-up" data-aos-duration="1200">
    <div class="hero-overlay"></div>
    <div class="hero-content" data-aos="fade-down" data-aos-delay="200">
        <h1>One Liga. One Community. One Purpose.</h1>
        <p>Bringing barangay leaders together to build a stronger and more united municipality.</p>
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
                            <img
                                src="<?= !empty($item['image_url'])
                                            ? 'https://api.helppinoy.org/static/news_img/' . htmlspecialchars($item['image_url'])
                                            : BASE_URL . 'assets/img/default-news.jpg'; ?>"
                                class="card-img-top"
                                alt="News Image"
                                onerror="this.src='<?= BASE_URL ?>assets/img/default-news.jpg'">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($item['title']); ?></h5>
                                <p class="card-text"><?= htmlspecialchars(mb_strimwidth($item['summary'] ?? '', 0, 100, '...')); ?></p>
                                <a href="<?= BASE_URL ?>single_news.php?slug=<?= urlencode($item['slug']); ?>" class="btn btn-primary">Read More</a>
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
                    <a href="<?= BASE_URL ?>centers.php" class="locate-btn">Locate Us</a>
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