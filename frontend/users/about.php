<?php
include './template/header.php';
?>

<style>
    .text-container {
        background-color: rgba(255, 255, 255, 0.85);
        border-radius: 5px;
    }
</style>



<!-- HERO SECTION -->
<section class="hero text-center d-flex align-items-center justify-content-center"
    style="background:url('<?= BASE_URL ?>assets/img/disaster-hero.jpg') center/cover no-repeat;height:60vh;position:relative;">

    <div style="background:rgba(0,0,0,0.6);position:absolute;top:0;left:0;width:100%;height:100%;"></div>

    <div class="container position-relative text-white" data-aos="fade-up">
        <h1 class="display-4 fw-bold">Liga ng mga Barangay</h1>

        <p class="lead mb-4">
            Strengthening local governance and empowering barangays to serve communities better.
        </p>

        <a href="<?= BASE_URL ?>contact.php" class="btn btn-warning btn-lg px-4 fw-bold">
            Learn More
        </a>

    </div>
</section>



<!-- WHAT WE DO -->
<section class="container py-5" style="background:white;">
    <div class="text-center mb-5" data-aos="fade-up">

        <h2 class="fw-bold">What the Liga ng mga Barangay Does</h2>

        <p class="text-muted">
            The Liga ng mga Barangay serves as the official organization of all barangays in the Philippines,
            providing representation, coordination, and leadership among barangay officials.
        </p>

    </div>


    <div class="row g-4 text-center">

        <div class="col-md-4" data-aos="zoom-in" data-aos-delay="100">

            <div class="p-4 shadow rounded bg-white h-100">

                <i class="fas fa-users fa-3x text-primary mb-3"></i>

                <h4 class="fw-bold">Community Leadership</h4>

                <p class="text-muted">
                    Promoting leadership among barangay officials and ensuring effective governance in local communities.
                </p>

            </div>
        </div>



        <div class="col-md-4" data-aos="zoom-in" data-aos-delay="200">

            <div class="p-4 shadow rounded bg-white h-100">

                <i class="fas fa-handshake fa-3x text-primary mb-3"></i>

                <h4 class="fw-bold">Coordination & Representation</h4>

                <p class="text-muted">
                    Acting as the collective voice of barangays and representing their concerns at municipal, city, and national levels.
                </p>

            </div>
        </div>



        <div class="col-md-4" data-aos="zoom-in" data-aos-delay="300">

            <div class="p-4 shadow rounded bg-white h-100">

                <i class="fas fa-balance-scale fa-3x text-primary mb-3"></i>

                <h4 class="fw-bold">Policy Support</h4>

                <p class="text-muted">
                    Supporting policies and programs that strengthen barangay administration and improve public services.
                </p>

            </div>
        </div>

    </div>
</section>



<!-- MISSION SECTION -->
<section class="py-5 bg-light" id="mission">

    <div class="container d-flex flex-column flex-lg-row align-items-center" data-aos="fade-up">

        <div class="col-lg-6 mb-4 mb-lg-0">

            <img src="<?= BASE_URL ?>assets/img/mission-disaster.jpg"
                class="img-fluid rounded shadow"
                alt="Liga Mission">

        </div>



        <div class="col-lg-6 ps-lg-5">

            <h2 class="fw-bold mb-3">Our Mission</h2>

            <p class="text-muted mb-4">

                The Liga ng mga Barangay aims to strengthen grassroots leadership,
                promote cooperation among barangays, and support programs that improve
                community development and public welfare.

            </p>


            <ul class="list-unstyled">

                <li><i class="fas fa-check-circle text-primary me-2"></i>Strengthening barangay leadership</li>

                <li><i class="fas fa-check-circle text-primary me-2"></i>Encouraging collaboration between communities</li>

                <li><i class="fas fa-check-circle text-primary me-2"></i>Supporting local development initiatives</li>

            </ul>


            <a href="<?= BASE_URL ?>contact.php" class="btn btn-primary mt-3">
                Get Involved
            </a>

        </div>

    </div>
</section>



<!-- CALL TO ACTION -->
<section class="text-center bg-primary text-white py-5" data-aos="fade-up">

    <h2 class="fw-bold mb-3">Building Stronger Communities Together</h2>

    <p class="mb-4">
        Through cooperation, leadership, and public service,
        the Liga ng mga Barangay continues to empower communities across the nation.
    </p>

    <a href="<?= BASE_URL ?>contact.php"
        class="btn btn-light btn-lg px-4 fw-bold">

        Contact Us

    </a>

</section>



<?php
include './template/footer.php';
?>


<link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">

<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

<script>
    AOS.init({
        once: true,
        duration: 1000
    });
</script>


<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>