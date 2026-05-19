<?php
define('BASE_URL', '/');
define('API_BASE', 'https://api.helppinoy.org');
?>

<style>
    :root {
        --primary-blue: #003366;
        --secondary-blue: #0057b7;
        --accent-yellow: #FFCC00;
        --dark-blue: #00254D;
        --light-blue: #e8f0fe;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body {
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #333;
        background-color: #ffffff;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* TOP BAR - Enhanced */
    .top-bar {
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--dark-blue) 100%);
        color: white;
        padding: 10px 30px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 15px;
        position: relative;
        z-index: 1030;
        flex-wrap: wrap;
    }

    .top-bar::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, var(--accent-yellow), transparent);
    }

    .top-bar .btn {
        padding: 8px 28px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 14px;
        letter-spacing: 0.5px;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .btn-login,
    .btn-register {
        background: transparent;
        border: 2px solid rgba(255, 255, 255, 0.8);
        color: white;
    }

    .btn-login:hover,
    .btn-register:hover {
        background: white;
        border-color: white;
        color: var(--primary-blue);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .btn-donate {
        background: linear-gradient(135deg, var(--accent-yellow), #ffb300);
        border: none;
        color: var(--primary-blue);
        font-weight: 800;
        box-shadow: 0 2px 8px rgba(255, 204, 0, 0.3);
    }

    .btn-donate:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(255, 204, 0, 0.4);
        background: linear-gradient(135deg, #ffb300, var(--accent-yellow));
    }

    /* User dropdown */
    .user-dropdown .dropdown-toggle {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        padding: 8px 20px;
        border-radius: 50px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .user-dropdown .dropdown-toggle::after {
        margin-left: 8px;
    }

    .user-dropdown .dropdown-toggle i {
        font-size: 1.1rem;
    }

    .dropdown-menu {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        margin-top: 10px;
        padding: 8px 0;
        animation: dropdownFade 0.3s ease;
    }

    @keyframes dropdownFade {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dropdown-menu .dropdown-item {
        padding: 10px 24px;
        font-weight: 500;
        transition: var(--transition);
    }

    .dropdown-menu .dropdown-item:hover {
        background: linear-gradient(90deg, var(--light-blue), transparent);
        color: var(--primary-blue);
        padding-left: 28px;
    }

    .dropdown-menu .dropdown-item i {
        margin-right: 10px;
        width: 20px;
        color: var(--secondary-blue);
    }

    /* MAIN NAVBAR - Modern Glassmorphism */
    .main-navbar {
        background: rgba(0, 87, 183, 0.92);
        backdrop-filter: blur(12px);
        padding: 12px 30px;
        position: sticky;
        top: 0;
        z-index: 1020;
        transition: var(--transition);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .navbar-scrolled {
        background: rgba(0, 51, 102, 0.98) !important;
        padding: 8px 30px !important;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .navbar-brand {
        position: relative;
    }

    .navbar-brand img {
        height: 55px;
        transition: var(--transition);
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
    }

    .navbar-scrolled .navbar-brand img {
        height: 48px;
    }

    .navbar-brand::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        width: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--accent-yellow), transparent);
        transition: var(--transition);
    }

    .navbar-brand:hover::after {
        width: 100%;
    }

    .navbar-toggler {
        border: 2px solid rgba(255, 255, 255, 0.5);
        background: rgba(255, 255, 255, 0.1);
        padding: 8px 12px;
    }

    .navbar-toggler:focus {
        box-shadow: none;
        border-color: var(--accent-yellow);
    }

    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 0.9)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }

    .nav-link {
        color: white !important;
        font-weight: 600;
        font-size: 16px;
        margin-left: 25px;
        letter-spacing: 0.3px;
        transition: var(--transition);
        position: relative;
        padding: 8px 0 !important;
    }

    .nav-link::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 3px;
        background: var(--accent-yellow);
        transition: var(--transition);
        border-radius: 2px;
    }

    .nav-link:hover::before,
    .nav-link.active::before {
        width: 100%;
    }

    .nav-link:hover {
        color: var(--accent-yellow) !important;
        transform: translateY(-2px);
    }

    /* Dropdown in navbar */
    .dropdown-toggle.nav-link::after {
        margin-left: 8px;
        transition: var(--transition);
    }

    .dropdown.show .dropdown-toggle.nav-link::after {
        transform: rotate(180deg);
    }

    /* Mobile responsive */
    @media (max-width: 991px) {
        .top-bar {
            padding: 8px 20px;
            justify-content: center;
            gap: 10px;
        }

        .top-bar .btn {
            padding: 6px 20px;
            font-size: 12px;
        }

        .main-navbar {
            padding: 10px 20px;
        }

        .navbar-nav {
            padding-top: 15px;
        }

        .nav-link {
            margin-left: 0;
            padding: 12px 0 !important;
            text-align: center;
        }

        .nav-link::before {
            bottom: 5px;
            left: 50%;
            transform: translateX(-50%);
        }

        .dropdown-menu {
            text-align: center;
            background: rgba(0, 87, 183, 0.95);
            backdrop-filter: blur(10px);
        }

        .dropdown-menu .dropdown-item {
            color: white;
        }

        .dropdown-menu .dropdown-item:hover {
            background: rgba(255, 255, 255, 0.2);
            color: var(--accent-yellow);
        }
    }

    /* Desktop dropdown enhancement */
    @media (min-width: 992px) {
        .dropdown-menu {
            display: block;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: var(--transition);
        }

        .dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
    }
</style>

<div class="wrapper">

    <!-- TOP BAR -->
    <div class="top-bar">
        <?php if (isset($_SESSION['username'])): ?>
            <div class="dropdown user-dropdown">
                <a href="#" class="dropdown-toggle btn" id="userDropdown" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle"></i>
                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="<?= BASE_URL ?>user_prof.php">
                            <i class="fas fa-id-card"></i> My Profile
                        </a>
                    </li>

                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form action="<?= BASE_URL ?>user_logout.php" method="post">
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            <a href="<?= BASE_URL ?>donation_form.php" class="btn btn-donate">
                <i class="fas fa-hand-holding-heart"></i> DONATE NOW
            </a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>user_login.php" class="btn btn-login">
                <i class="fas fa-sign-in-alt"></i> LOGIN
            </a>
            <a href="<?= BASE_URL ?>register.php" class="btn btn-register">
                <i class="fas fa-user-plus"></i> REGISTER
            </a>
            <a href="<?= BASE_URL ?>donation_form.php" class="btn btn-donate">
                <i class="fas fa-hand-holding-heart"></i> DONATE NOW
            </a>
        <?php endif; ?>
    </div>

    <!-- MAIN NAVBAR -->
    <nav class="navbar navbar-expand-lg main-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= BASE_URL ?>">
                <img src="<?= BASE_URL ?>assets/img/HP_logo12.png" alt="Help Pinoy Logo">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <!-- HOME LINK -->
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>"
                            href="<?= BASE_URL ?>">
                            HOME
                        </a>
                    </li>

                    <!-- ABOUT DROPDOWN -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= in_array(basename($_SERVER['PHP_SELF']), ['about.php', 'centers.php']) ? 'active' : '' ?>"
                            href="#" data-bs-toggle="dropdown">
                            ABOUT
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="<?= BASE_URL ?>about.php">
                                    <i class="fas fa-info-circle"></i> Who We Are
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= BASE_URL ?>centers.php">
                                    <i class="fas fa-map-marker-alt"></i> Where We Are
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= BASE_URL ?>about.php#mission">
                                    <i class="fas fa-bullseye"></i> Our Mission
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- STORIES / NEWS -->
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'news.php' ? 'active' : '' ?>"
                            href="<?= BASE_URL ?>news.php">
                            STORIES
                        </a>
                    </li>

                    <!-- VOLUNTEER -->
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'under_development.php' ? 'active' : '' ?>"
                            href="<?= BASE_URL ?>under_development.php">
                            VOLUNTEER
                        </a>
                    </li>

                    <!-- CONTACT -->
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'contact.php' ? 'active' : '' ?>"
                            href="<?= BASE_URL ?>contact.php">
                            CONTACT
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>

<script>
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.main-navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('navbar-scrolled');
        } else {
            navbar.classList.remove('navbar-scrolled');
        }
    });

    // Active link highlighting
    document.addEventListener('DOMContentLoaded', function() {
        const currentPage = window.location.pathname.split('/').pop();
        const navLinks = document.querySelectorAll('.nav-link');

        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href && href.includes(currentPage)) {
                link.classList.add('active');
            }
        });
    });
</script>