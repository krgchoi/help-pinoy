<?php include './template/header.php';

$api_url = 'http://localhost:5000/user/get_news';
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
curl_close($ch);

$news = json_decode($response, true);

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
.hero-section {
    height: 450px;
    background-size: cover;
    background-position: center;
    position: relative;
    color: white;
    display: flex;
    align-items: center;
    opacity: 0;
    transform: translateY(-30px);
    transition: all 1s ease;
}
.hero-section.visible {
    opacity: 1;
    transform: translateY(0);
}
.hero-section::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 50%;
    height: 100%;
    background: linear-gradient(to right, rgba(0, 0, 0, 0.6), transparent);
    z-index: 1;
}
.hero-content {
    position: relative;
    z-index: 2;
    text-align: left;
    max-width: 500px;
    padding: 20px;
    margin-left: 50px;
}
.overlay {
    position: relative;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    height: 100%;
    padding-left: 50px;
}
.stats-card {
    opacity: 0;
    transform: translateY(30px);
    transition: all 1s ease;
}
.stats-card.visible {
    opacity: 1;
    transform: translateY(0);
}
</style>

<div class="hero-section" style="background-image: url('../assets/img/donation-banner.jpg');" data-aos="fade-up" data-aos-duration="1000">
    <div class="overlay">
        <div class="hero-content">
            <h1>Day Care Center</h1>
            <p>Together, We Shape the Future — Start with a Smile.</p>
            <a href="donation_form.php" class="btn btn-warning btn-lg px-4 fw-bold">Donate Now</a>
        </div>
    </div>
</div>

<!-- <section class="py-5 bg-light">
  <div class="container">
    <div class="row text-center justify-content-center">
      
      <div class="col-md-3 mb-4 stats-card">
        <div class="card shadow-sm border-0">
          <div class="card-body">
            <div class="mb-2">
              <i class="fas fa-smile text-primary" style="font-size: 2rem;"></i>
            </div>
            <h2 class="fw-bold text-primary counter" data-target="152">0</h2>
            <h5 class="card-title text-muted">Donors</h5>
          </div>
        </div>
      </div>
      
      <div class="col-md-3 mb-4 stats-card">
        <div class="card shadow-sm border-0">
          <div class="card-body">
            <div class="mb-2">
              <i class="fas fa-hand-holding-heart text-success" style="font-size: 2rem;"></i>
            </div>
            <h2 class="fw-bold text-success">₱<span class="counter" data-target="45000">0</span></h2>
            <h5 class="card-title text-muted">Total Raised</h5>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-4 stats-card">
        <div class="card shadow-sm border-0">
          <div class="card-body">
            <div class="mb-2">
              <i class="fas fa-child text-warning" style="font-size: 2rem;"></i>
            </div>
            <h2 class="fw-bold text-warning counter" data-target="24">0</h2>
            <h5 class="card-title text-muted">Children Supported</h5>
          </div>
        </div>
      </div>

    </div>
  </div>
</section> -->

<section class="content-section bg-white" data-aos="fade-up" data-aos-duration="1000">
    <div class="container">
        <h2 class="text-center mb-4">Latest News</h2>
        <div class="row">
            <?php
            if (is_array($news) && count($news) > 0) {
                $i = 0;
                foreach ($news as $news) {
                    $delay = 100 + ($i * 100);
            ?>
                    <div class="col-md-4 mb-4" data-aos="zoom-in" data-aos-delay="<?php echo $delay; ?>">
                        <div class="card news-card h-100">
                            <?php if (!empty($news['image_url'])): ?>
                                <img src="http://localhost:5000/static/news_img/<?php echo htmlspecialchars($news['image_url']); ?>" class="card-img-top" alt="News Image" onerror="this.style.display='none'">
                            <?php else: ?>
                                <img src="../assets/img/default-news.jpg" class="card-img-top" alt="Default News Image">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($news['title']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($news['summary']); ?></p>
                                <a href="single_news.php?slug=<?php echo urlencode($news['slug']); ?>" class="btn btn-primary">Read More</a>
                            </div>
                        </div>
                    </div>
            <?php
                    $i++;
                }
            } else {
                echo '<div class="col-12"><p class="text-center">No Stories available.</p></div>';
            }
            ?>
        </div>
    </div>
</section>
<!-- Map -->
<section class="content-section bg-light" data-aos="fade-up" data-aos-duration="1000">
    <div class="container">
        <h2 class="text-center mb-4">Our Location</h2>
        <div class="row">
            <div class="col-md-6">
                <div id="map" class="map-index" style="height: 400px;"></div>
                <script>
                    window.mapConfig = {
                        centers: <?php echo json_encode($centers); ?>,
                        enableSearch: false,
                        enableSort: false,
                        showUserLocation: false
                    };
                </script>
                <?php include './template/map.php'; ?>
            </div>
            <div class="col-md-6 d-flex align-items-center justify-content-center p-3">
                <a href="./centers.php" class="btn btn-success btn-lg">Locate Us</a>
            </div>
        </div>
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
AOS.init();
document.addEventListener("DOMContentLoaded", () => {
    const hero = document.querySelector(".hero-section");
    setTimeout(() => hero.classList.add("visible"), 300);

    const cards = document.querySelectorAll(".stats-card");
    const counters = document.querySelectorAll(".counter");
    const speed = 100;

    const animateCounters = () => {
        counters.forEach(counter => {
            const updateCount = () => {
                const target = +counter.getAttribute("data-target");
                const count = +counter.innerText;
                const increment = Math.ceil(target / speed);

                if (count < target) {
                    counter.innerText = count + increment;
                    setTimeout(updateCount, 20);
                } else {
                    counter.innerText = target.toLocaleString();
                }
            };
            updateCount();
        });
    };

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add("visible");
                if (entry.target === cards[cards.length - 1]) {
                    animateCounters();
                }
            }
        });
    }, { threshold: 0.3 });

    cards.forEach(card => observer.observe(card));
});
</script>

<?php include './template/footer.php'; ?>
