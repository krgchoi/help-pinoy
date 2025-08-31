<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Pinoy</title>
    <link rel="stylesheet" href="../assets/css/users.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            
        }
        .wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .footer {
            margin-top: auto;
        }
        .transparent-navbar {
            background-color: transparent !important;
            box-shadow: none;
            position: absolute;
            top: 20px;
            width: 100%;
            z-index: 999;
        }

        .transparent-navbar .nav-link,
        .transparent-navbar .navbar-brand,
        .transparent-navbar .btn {
            color: white !important;
        }
        .transparent-navbar .nav-link:hover,
        .transparent-navbar .navbar-brand:hover,
        .transparent-navbar .btn:hover {
            color: #3146ffff !important; 
        }

        .donate-btn {
            background-color: #3146ffff; 
            color: black !important;
            border-radius: 20px;
            padding: 10px 20px;
            font-weight: bold;
        }
        .donate-btn:hover {
            background-color: #ffc107;
            color: black !important;
            
        }



        
    </style>
</head>

<body>
<div class="wrapper">
    <div class="header d-flex justify-content-between align-items-center flex-wrap" style="background-color: rgba(0, 0, 0, 0.3); padding: 0; position: absolute; top: 0; width: 100%; z-index: 1000;">
        <?php if (isset($_SESSION['username'])): ?>
            <div class="dropdown d-inline ms-auto px-3">
                <a href="#" class="dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="text-decoration: none; color: white; background: none; border: none; padding: 0; font-size: 12px;">
                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                </a>
                <ul class="dropdown-menu" aria-labelledby="userDropdown" style="font-size: 12px;">
                    <li><a class="dropdown-item" href="user_prof.php">Profile</a></li>
                    <li>
                        <form action="user_logout.php" method="post" style="display:inline;">
                            <button type="submit" class="dropdown-item">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        <?php else: ?>
            <div class="ms-auto d-flex align-items-center px-3" style="font-size: 10px;">
                <a href="user_login.php" style="text-decoration: none; color: white;">Sign In</a>
                <span class="text-white px-1">|</span>
                <a href="register.php" style="text-decoration: none; color: white;">Sign Up</a>
            </div>
        <?php endif; ?>
    </div>
    <nav class="navbar navbar-expand-lg transparent-navbar" style="position: absolute; width: 100%; z-index: 999;" aria-label="Main navigation">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php" aria-label="Help Pinoy Home">
                <img src="../assets/img/HP_logo.png" alt="Help Pinoy Logo">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link<?php if (basename($_SERVER['PHP_SELF']) == 'about.php') echo ' active'; ?>" href="about.php">What we do</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link<?php //if (basename($_SERVER['PHP_SELF']) == 'centers.php') echo ' active'; ?>" href="centers.php">Locations</a>
                    </li> -->
                    <li class="nav-item">
                        <a class="nav-link<?php if (basename($_SERVER['PHP_SELF']) == 'news.php') echo ' active'; ?>" href="news.php">Stories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php if (basename($_SERVER['PHP_SELF']) == 'contact.php') echo ' active'; ?>" href="donation_form.php">Contact Us</a>
                    </li>
                </ul>
                <a href="donation_form.php" class="btn donate-btn ms-3" aria-label="Donate">Donate</a>
            </div>
        </div>
    </nav>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title text-black" id="mobileMenuLabel">Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close menu"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link<?php if (basename($_SERVER['PHP_SELF']) == 'about.php') echo ' active'; ?>" href="about.php">What we do</a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link<?php //if (basename($_SERVER['PHP_SELF']) == 'centers.php') echo ' active'; ?>" href="centers.php">Locations</a>
                </li> -->
                <li class="nav-item">
                    <a class="nav-link<?php if (basename($_SERVER['PHP_SELF']) == 'news.php') echo ' active'; ?>" href="news.php">What we done</a>
                </li>
                <li class="nav-item mt-3">
                    <a href="donation_form.php" class="btn donate-btn w-100" aria-label="Donate">Donate</a>
                </li>
            </ul>
        </div>
    </div>
