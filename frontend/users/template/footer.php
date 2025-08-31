<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
    .footer a {
        transition: color 0.3s ease;
    }
    .footer a:hover {
        color: #ffc107 !important;
    }

    .footer .bi {
        transition: transform 0.3s ease, color 0.3s ease;
    }
    .footer .bi:hover {
        transform: scale(1.2);
        color: #ffc107 !important;
    }
</style>

<footer class="footer bg-dark text-white mt-auto">
    <div class="container py-4">
        <div class="row gy-4 align-items-start">

            <!-- Logo and Description -->
            <div class="col-md-4 col-12 text-center text-md-start">
                <h5 class="fw-bold">Help Pinoy</h5>
                <p class="small mb-0">Making a difference, one donation at a time.</p>
            </div>

            <!-- Quick Links -->
            <div class="col-md-4 col-12 text-center">
                <h5 class="fw-bold">Quick Links</h5>
                <ul class="list-unstyled mb-0">
                    <li><a href="about.php" class="text-white text-decoration-none d-block py-1">About Us</a></li>
                    <li><a href="news.php" class="text-white text-decoration-none d-block py-1">What We’ve Done</a></li>
                    <li><a href="contact.php" class="text-white text-decoration-none d-block py-1">Contact Us</a></li>
                    <li><a href="privacy.php" class="text-white text-decoration-none d-block py-1">Privacy Policy</a></li>
                </ul>
            </div>

            <!-- Contact and Social -->
            <div class="col-md-4 col-12 text-center text-md-end">
                <h5 class="fw-bold">Get in Touch</h5>
                <p class="small mb-1">Email: <a href="mailto:support@helppinoy.org" class="text-white">support@helppinoy.org</a></p>
                <div class="mb-2">
                    <a href="#" class="text-white fs-5 me-2"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-white fs-5 me-2"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="text-white fs-5"><i class="bi bi-instagram"></i></a>
                </div>
                <a href="donation_form.php" class="btn btn-warning btn-sm">Donate Now</a>
            </div>
        </div>

        <hr class="border-light mt-4">
        <div class="text-center">
            <p class="small mb-0">&copy; <?php echo date('Y'); ?> Help Pinoy. All rights reserved.</p>
        </div>
    </div>
</footer>
