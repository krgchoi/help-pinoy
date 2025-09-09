<?php
include './template/header.php';
?>
<style>
    .text-container {
    background-color: rgba(255, 255, 255, 0.8);
    border-radius: 5px;
    }
</style>

<section class="hero text-center d-flex align-items-center justify-content-center" style="background: url('../assets/img/disaster-hero.jpg') center/cover no-repeat; height: 60vh; position: relative;">
    <div style="background: rgba(0,0,0,0.6); position:absolute; top:0; left:0; width:100%; height:100%;"></div>
    <div class="container position-relative text-white" data-aos="fade-up" data-aos-duration="1000">
        <h1 class="display-4 fw-bold">Disaster Relief Support</h1>
        <p class="lead mb-4">Helping communities recover and rebuild after disasters through your generous support.</p>
        <a href="donation_form.php" class="btn btn-warning btn-lg px-4 fw-bold">Donate Now</a>
    </div>
</section>

<section class="container py-5" style="background-color: white;">
    <div class="text-center mb-5" data-aos="fade-up">
        <h2 class="fw-bold">How We Help During Disasters</h2>
        <p class="text-muted">Your donation provides life-saving assistance to those in need.</p>
    </div>
    <div class="row g-4 text-center">
        <div class="col-md-4" data-aos="zoom-in" data-aos-delay="100">
            <div class="p-4 shadow rounded bg-white h-100">
                <i class="fas fa-water fa-3x text-primary mb-3"></i>
                <h4 class="fw-bold">Emergency Supplies</h4>
                <p class="text-muted">Providing food, clean water, and hygiene kits to affected families.</p>
            </div>
        </div>
        <div class="col-md-4" data-aos="zoom-in" data-aos-delay="200">
            <div class="p-4 shadow rounded bg-white h-100">
                <i class="fas fa-clinic-medical fa-3x text-primary mb-3"></i>
                <h4 class="fw-bold">Medical Assistance</h4>
                <p class="text-muted">Delivering first aid and healthcare services in disaster-stricken areas.</p>
            </div>
        </div>
        <div class="col-md-4" data-aos="zoom-in" data-aos-delay="300">
            <div class="p-4 shadow rounded bg-white h-100">
                <i class="fas fa-home fa-3x text-primary mb-3"></i>
                <h4 class="fw-bold">Shelter Support</h4>
                <p class="text-muted">Setting up temporary shelters to keep families safe and secure.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light" id="mission">
    <div class=" d-flex flex-column flex-lg-row align-items-center" data-aos="fade-up">
        <div class="col-lg-6 mb-4 mb-lg-0">
            <img src="../assets/img/mission-disaster.jpg" alt="Mission" class="img-fluid rounded shadow">
        </div>
        <div class="col-lg-6 ps-lg-5">
            <h2 class="fw-bold mb-3">Our Mission</h2>
            <p class="text-muted mb-4">We aim to provide immediate disaster relief and long-term recovery assistance to affected communities. Your support helps deliver life-saving aid and rebuild lives.</p>
            <ul class="list-unstyled">
                <li><i class="fas fa-check-circle text-primary me-2"></i> Quick response during disasters</li>
                <li><i class="fas fa-check-circle text-primary me-2"></i> Emergency relief and medical care</li>
                <li><i class="fas fa-check-circle text-primary me-2"></i> Support for recovery and rebuilding</li>
            </ul>
            <a href="donation_form.php" class="btn btn-primary mt-3">Support Our Mission</a>
        </div>
    </div>
</section>

<section class="text-center bg-primary text-white py-5" data-aos="fade-up">
    <h2 class="fw-bold mb-3">Join Us in Making a Difference</h2>
    <p class="mb-4">Together, we can save lives and restore hope to communities in need.</p>
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
