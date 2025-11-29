<style>
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

    /* TOP BAR */
    .top-bar {
        background-color: #003366;
        color: white;
        padding: 8px 20px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        border-bottom: 2px solid #00254D;
        z-index: 1030;
    }

    .top-bar .btn {
        margin-left: 12px;
        font-size: 15px;
        font-weight: 600;
        padding: 7px 24px;
        border-radius: 25px;
        transition: 0.3s ease;
    }

    .btn-login,
    .btn-register {
        background: transparent;
        border: 2px solid white;
        color: white;
    }

    .btn-login:hover,
    .btn-register:hover {
        background: white;
        color: #003366;
    }

    .btn-donate {
        background: #FFCC00;
        border: none;
        color: #003366;
        font-weight: 800;
        letter-spacing: 1px;
        font-size: 16px;
    }

    .btn-donate:hover {
        background: #E6B800;
        color: #00254d;
    }

    /* MAIN NAVBAR */
    .main-navbar {
        background: rgba(0, 87, 183, 0.85);
        backdrop-filter: blur(10px);
        padding: 12px 30px;
        width: 100%;
        position: sticky;
        top: 0;
        z-index: 1020;
        transition: 0.3s ease;
    }

    .navbar-scrolled {
        background: rgba(0, 51, 102, 0.95) !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    }

    .navbar-brand img {
        height: 60px;
        filter: drop-shadow(0 0 4px rgba(0,0,0,0.5));
    }

    .nav-link {
        color: white !important;
        font-weight: 600;
        font-size: 17px;
        margin-left: 28px;
        letter-spacing: 0.5px;
        transition: 0.3s ease;
    }

    .nav-link:hover {
        color: #FFCC00 !important;
    }

    .dropdown-menu {
        border-radius: 10px;
        padding: 8px 0;
        z-index: 1050;
    }

    .dropdown-menu .dropdown-item:hover {
        background-color: #f4f4f4;
    }

    @media (max-width: 991px) {
        .main-navbar {
            padding: 10px 20px;
        }

        .nav-link {
            margin-left: 0;
            padding: 12px 0;
        }
    }
</style>

<div class="wrapper">

    <!-- TOP BAR -->
    <div class="top-bar">
        <?php if (isset($_SESSION['username'])): ?>
            <div class="dropdown">
                <a href="#" class="dropdown-toggle btn btn-login" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                </a>
                <a href="donation_form.php" class="btn btn-donate">DONATE NOW</a>

                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="user_prof.php">Profile</a></li>
                    <li>
                        <form action="user_logout.php" method="post">
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

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg main-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="/Help_Pinoy/frontend/assets/img/hp_logo.png" alt="Help Pinoy Logo">
            </a>

            <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    
                    <!-- ABOUT DROPDOWN -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php if (basename($_SERVER['PHP_SELF']) == 'about.php') echo 'active'; ?>" 
                           href="#" id="aboutDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            ABOUT
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="about.php">WHO WE ARE</a></li>
                            <li><a class="dropdown-item" href="centers.php">WHERE WE ARE</a></li>
                            <li><a class="dropdown-item" href="about.php#mission">MISSION</a></li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?php if (basename($_SERVER['PHP_SELF']) == 'news.php') echo 'active'; ?>" href="news.php">STORIES</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?php if (basename($_SERVER['PHP_SELF']) == 'contact.php') echo 'active'; ?>" href="contact.php">CONTACT</a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>
</div>

<script>
    window.addEventListener('scroll', function () {
        const navbar = document.querySelector('.main-navbar');
        navbar.classList.toggle('navbar-scrolled', window.scrollY > 50);
    });
</script>
