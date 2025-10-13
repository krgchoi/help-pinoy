<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Pinoy - Liga ng mga Barangay</title>
    <link rel="stylesheet" href="../assets/css/users.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('../assets/img/guimbal_logo.png') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: #333;
        }

        /* ---------- TOP BAR ---------- */
        .top-bar {
            background-color: #003366;
            color: white;
            padding: 8px 20px;
        }

        .top-bar .btn {
            margin: 6px 10px;
            font-size: 15px;
            padding: 7px 25px;
            font-weight: 600;
            border-radius: 25px;
        }

        .top-bar .btn-login,
        .top-bar .btn-register {
            background: transparent;
            border: 2px solid white;
            color: white;
            font-size: small;
            transition: 0.3s;
        }

        .top-bar .btn-login:hover,
        .top-bar .btn-register:hover {
            background: white;
            color: #003366;
        }

        .top-bar .btn-donate {
            background: #FFCC00;
            border: none;
            color: #003366;
            font-weight: 800;
            letter-spacing: 1px;
            font-size: 16px;
            transition: 0.3s ease;
        }

        .top-bar .btn-donate:hover {
            background: #E6B800;
            color: #00254d;
        }

        /* ---------- MAIN NAVBAR ---------- */
        .main-navbar {
            background: rgba(0, 87, 183, 0.85);
            position: absolute;
            top: 60px;
            left: 0;
            width: 100%;
            z-index: 999;
            padding: 12px 30px;
            transition: background 0.3s ease;
            backdrop-filter: blur(8px);
        }

        .main-navbar .nav-link {
            color: white !important;
            font-weight: 600;
            margin-left: 25px;
            font-size: 17px;
            letter-spacing: 1px;
            transition: color 0.3s ease;
        }

        .main-navbar .nav-link:hover {
            color: #FFCC00 !important;
        }

        .navbar-brand img {
            height: 60px;
            filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.5));
        }

        /* ---------- RESPONSIVE ---------- */
        @media (max-width: 991px) {
            .main-navbar {
                background: rgba(0, 51, 102, 0.9) !important;
                position: fixed;
                top: 50px;
                padding: 12px 20px;
            }

            .main-navbar .nav-link {
                color: white !important;
            }
        }

        /* ---------- SCROLL EFFECT ---------- */
        .navbar-scrolled {
            background: rgba(0, 51, 102, 0.95) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.25);
        }
    </style>
</head>

<body>
<div class="wrapper" style="display: flex; flex-direction: column; min-height: 100vh;">
    <!-- TOP BAR -->
    <div class="top-bar d-flex justify-content-end align-items-center">
        <?php if (isset($_SESSION['username'])): ?>
            <div class="dropdown">
                <a href="#" class="dropdown-toggle btn btn-login" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                </a>
                <a href="donation_form.php" class="btn btn-donate">DONATE NOW</a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="user_prof.php">Profile</a></li>
                    <li>
                        <form action="user_logout.php" method="post" style="display:inline;">
                            <button type="submit" class="dropdown-item">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        <?php else: ?>
            <a href="user_login.php" class="btn btn-login">LOGIN</a>
            <a href="register.php" class="btn btn-register">REGISTER</a>
            <a href="donation_form.php" class="btn btn-donate">DONATE NOW</a>
        <?php endif; ?>
    </div>

    <!-- MAIN NAVBAR -->
    <nav class="navbar navbar-expand-lg main-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="../assets/img/hp_logo.png" alt="Help Pinoy Logo">
            </a>
            <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle<?php if (basename($_SERVER['PHP_SELF']) == 'about.php') echo ' active'; ?>" href="about.php" id="aboutDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            ABOUT
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="about.php">WHO WE ARE</a></li>
                            <li><a class="dropdown-item" href="centers.php">WHERE WE ARE</a></li>
                            <li><a class="dropdown-item" href="about.php#mission">MISSION</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link<?php if (basename($_SERVER['PHP_SELF']) == 'news.php') echo ' active'; ?>" href="news.php">STORIES</a></li>
                    <li class="nav-item"><a class="nav-link<?php if (basename($_SERVER['PHP_SELF']) == 'contact.php') echo ' active'; ?>" href="contact.php">CONTACT</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <script>
        window.addEventListener('scroll', function () {
            const navbar = document.querySelector('.main-navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });
    </script>

