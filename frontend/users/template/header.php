<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Pinoy - Animal Shelter</title>
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

        /* Top Black Bar */
        .top-bar {
            background-color: black;
            color: white;
            padding: 8px 20px;
        }
        .top-bar .btn {
            margin: 6px 10px;
            font-size: 16px;
            padding: 8px 30px;
            font-weight: 600;
        }
        .top-bar .btn-login, .top-bar .btn-register {
            background: transparent;
            border: 2px solid white;
            color: white;
            font-size: small;
        }
        .top-bar .btn-login:hover, .top-bar .btn-register:hover {
            background: white;
            color: black;
        }
        .top-bar .btn-donate {
            background: linear-gradient(90deg, #f39c12, #e67e22);
            border: none;
            color: white;
            font-weight: 800;
            letter-spacing: 2px;
            font-size: large;
            
        }
        .top-bar .btn-donate:hover {
            background: #ffc107;
            color: black;
        }

        /* Main Navbar */
        .main-navbar {
            background: transparent !important;
            position: absolute;
            top: 65px; 
            left: 0;
            width: 100%;
            z-index: 999;
            padding: 15px 30px;
            transition: background 0.3s ease;
            backdrop-filter: blur(8px);
        }
       
        .main-navbar .nav-link {
            color: #ffffffff !important;
            font-weight: 600;
            margin-left: 25px;
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 2px;
            transition: color 0.3s ease;
        }
        .main-navbar .nav-link:hover {
            color: #f39c12 !important;
        }
        .navbar-brand img {
            height: 60px;
            filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.7));
        }

        .navbar-toggler {
            transition: transform 0.5s ease;
        }
        @media (max-width: 991px) {
            .main-navbar {
                background: rgba(0, 0, 0, 0.6) !important;
                backdrop-filter: blur(8px); 
                position: fixed; 
                top: 50px; 
                padding: 12px 20px ;
                margin-top: 10px;
            }

            .main-navbar .nav-link {
                color: white !important;
            }
        }

    </style>
</head>

<body>
<div class="wrapper" style="display: flex; flex-direction: column; min-height: 100vh;">
    <!-- Top Black Bar -->
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

    <!-- Main Navbar -->
    <nav class="navbar navbar-expand-lg main-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="../assets/img/hp_logo.png" alt="Help Pinoy Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle<?php if (basename($_SERVER['PHP_SELF']) == 'about.php') echo ' active'; ?>" href="about.php" id="aboutDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            ABOUT
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="aboutDropdown">
                            <li><a class="dropdown-item" href="about.php">WHO WE ARE</a></li>
                            <li><a class="dropdown-item" href="centers.php">WHERE WE ARE</a></li>
                            <li><a class="dropdown-item" href="about.php#mission">MISSION</a></li>
                        </ul>
                    </li>
                    <!-- <li class="nav-item"><a class="nav-link<?php if (basename($_SERVER['PHP_SELF']) == 'adopt.php') echo ' active'; ?>" href="adopt.php">ADOPT</a></li>
                    <li class="nav-item"><a class="nav-link<?php if (basename($_SERVER['PHP_SELF']) == 'volunteer.php') echo ' active'; ?>" href="volunteer.php">GET INVOLVE</a></li> -->
                    <li class="nav-item"><a class="nav-link<?php if (basename($_SERVER['PHP_SELF']) == 'stories.php') echo ' active'; ?>" href="news.php">STORIES</a></li>
                    <li class="nav-item"><a class="nav-link<?php if (basename($_SERVER['PHP_SELF']) == 'contact.php') echo ' active'; ?>" href="contact.php">CONTACT</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <script>
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.main-navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('bg-dark', 'shadow');
                navbar.classList.remove('bg-transparent');
            } else {
                navbar.classList.remove('bg-dark', 'shadow');
                navbar.classList.add('bg-transparent');
            }
        });
        const toggler = document.querySelector('.navbar-toggler');
        toggler.addEventListener('click', function() {
            this.classList.toggle('open');
        });
    </script>
<div>
</body>


