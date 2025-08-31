<?php
include './template/header.php';
?>

<section class="hero text-center d-flex align-items-center justify-content-center" style="background: url('../assets/img/daycare-hero.jpg') center/cover no-repeat; height: 60vh; position: relative;">
    <div style="background: rgba(0,0,0,0.6); position:absolute; top:0; left:0; width:100%; height:100%;"></div>
    <div class="container position-relative text-white" data-aos="fade-up" data-aos-duration="1000">
        <h1 class="display-4 fw-bold">What We Do</h1>
        <p class="lead mb-4">Helping children learn, grow, and thrive through your generous support.</p>
        <a href="donation_form.php" class="btn btn-warning btn-lg px-4 fw-bold">Donate Now</a>
    </div>
</section>

<section class="container py-5">
    <div class="text-center mb-5" data-aos="fade-up">
        <h2 class="fw-bold">How We Help Children</h2>
        <p class="text-muted">Your donation makes a big difference in their lives.</p>
    </div>
    <div class="row g-4 text-center">
        <div class="col-md-4" data-aos="zoom-in" data-aos-delay="100">
            <div class="p-4 shadow rounded bg-white h-100">
                <i class="fas fa-book fa-3x text-primary mb-3"></i>
                <h4 class="fw-bold">Education Support</h4>
                <p class="text-muted">Providing books, learning materials, and educational programs for children.</p>
            </div>
        </div>
        <div class="col-md-4" data-aos="zoom-in" data-aos-delay="200">
            <div class="p-4 shadow rounded bg-white h-100">
                <i class="fas fa-utensils fa-3x text-primary mb-3"></i>
                <h4 class="fw-bold">Nutritious Meals</h4>
                <p class="text-muted">Ensuring every child gets healthy meals for proper growth and development.</p>
            </div>
        </div>
        <div class="col-md-4" data-aos="zoom-in" data-aos-delay="300">
            <div class="p-4 shadow rounded bg-white h-100">
                <i class="fas fa-child fa-3x text-primary mb-3"></i>
                <h4 class="fw-bold">Safe Environment</h4>
                <p class="text-muted">Creating a nurturing space where children feel safe and happy.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container d-flex flex-column flex-lg-row align-items-center" data-aos="fade-right">
        <div class="col-lg-6 mb-4 mb-lg-0">
            <img src="../assets/img/mission-banner.jpg" alt="Mission" class="img-fluid rounded shadow">
        </div>
        <div class="col-lg-6 ps-lg-5">
            <h2 class="fw-bold mb-3">Our Mission</h2>
            <p class="text-muted mb-4">We aim to provide quality childcare and education for every child, giving them a brighter future. Your support helps us improve learning spaces, nutrition, and overall well-being for children.</p>
            <ul class="list-unstyled">
                <li><i class="fas fa-check-circle text-primary me-2"></i> Affordable and accessible education</li>
                <li><i class="fas fa-check-circle text-primary me-2"></i> Health and nutrition programs</li>
                <li><i class="fas fa-check-circle text-primary me-2"></i> Loving and caring teachers</li>
            </ul>
            <a href="donation_form.php" class="btn btn-primary mt-3">Support Our Mission</a>
        </div>
    </div>
</section>

<section class="text-center bg-primary text-white py-5" data-aos="fade-up">
    <h2 class="fw-bold mb-3">Join Us in Making a Difference</h2>
    <p class="mb-4">Together, we can build a brighter future for our children.</p>
    <a href="donation_form.php" class="btn btn-light btn-lg px-4 fw-bold">Donate Now</a>
</section>

<?php
include './template/footer.php';
?>


<link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
    AOS.init();
</script>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
