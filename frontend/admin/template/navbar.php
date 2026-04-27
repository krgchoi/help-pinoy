<?php
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['access_token'])) {
    header("Location: admin_login.php");
    exit();
}

$jwt_token = $_SESSION['access_token'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Pinoy - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Open+Sans:wght@400;700&family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #003366;
            --secondary-blue: #0057b7;
            --accent-yellow: #FFCC00;
            --light-blue: #e6f2ff;
            --dark-gray: #2c3e50;
            --light-gray: #f8f9fa;
            --sidebar-width: 280px;
            --sidebar-collapsed: 80px;
            --transition-speed: 0.3s;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', 'Roboto', sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        #sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: white;
            transition: all var(--transition-speed) ease;
            position: fixed;
            height: 100vh;
            z-index: 1000;
            box-shadow: 5px 0 25px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }

        #sidebar.expand {
            width: var(--sidebar-collapsed);
        }

        #sidebar.expand .sidebar-logo a,
        #sidebar.expand .sidebar-link span {
            opacity: 0;
            visibility: hidden;
            transition: all var(--transition-speed) ease;
        }

        #sidebar.expand .sidebar-link {
            justify-content: center;
            padding: 15px 20px;
        }

        .sidebar-header {
            padding: 25px 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
        }

        .toggle-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 10px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
        }

        .toggle-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
        }

        .sidebar-logo {
            text-align: center;
            margin-top: 15px;
        }

        .sidebar-logo a {
            color: white;
            font-size: 1.5rem;
            font-weight: 800;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all var(--transition-speed) ease;
        }

        .sidebar-logo i {
            color: var(--accent-yellow);
            font-size: 1.8rem;
        }

        .sidebar-nav {
            flex: 1;
            padding: 20px 0;
            list-style: none;
        }

        .sidebar-item {
            margin-bottom: 5px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 25px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .sidebar-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s ease;
        }

        .sidebar-link:hover::before {
            left: 100%;
        }

        .sidebar-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            border-left-color: var(--accent-yellow);
            transform: translateX(5px);
        }

        .sidebar-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.15);
            border-left-color: var(--accent-yellow);
            box-shadow: inset 0 0 20px rgba(255, 255, 255, 0.05);
        }

        .sidebar-link i {
            width: 28px;
            /* fixed icon column */
            min-width: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            line-height: 1;
            vertical-align: middle;
            flex-shrink: 0;
        }

        .sidebar-link:hover i {
            color: var(--accent-yellow);
            transform: scale(1.1);
        }

        .sidebar-link span {
            font-weight: 500;
            font-size: 0.95rem;
            transition: all var(--transition-speed) ease;
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-footer .sidebar-link {
            background: rgba(220, 53, 69, 0.2);
            border-radius: 10px;
            border-left: 4px solid #dc3545;
        }

        .sidebar-footer .sidebar-link:hover {
            background: rgba(220, 53, 69, 0.3);
            transform: translateY(-2px);
        }

        .sidebar-footer .sidebar-link i {
            color: #ff6b7a;
        }

        /* Main Content Area */
        .main {
            flex: 1;
            margin-left: var(--sidebar-width);
            transition: margin-left var(--transition-speed) ease;
            padding: 0;
            min-height: 100vh;
        }

        #sidebar.expand~.main {
            margin-left: var(--sidebar-collapsed);
        }

        .main-content {
            padding: 30px;
            min-height: 100vh;
        }

        /* Top Navigation */
        .top-navbar {
            background: white;
            padding: 20px 30px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            border-bottom: 1px solid #e9ecef;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-blue);
            display: none;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--secondary-blue), var(--primary-blue));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .user-details h5 {
            margin: 0;
            color: var(--dark-gray);
            font-weight: 600;
        }

        .user-details small {
            color: #6c757d;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            #sidebar {
                width: var(--sidebar-collapsed);
                transform: translateX(-100%);
            }

            #sidebar.mobile-open {
                transform: translateX(0);
                width: var(--sidebar-width);
            }

            #sidebar.mobile-open .sidebar-logo a,
            #sidebar.mobile-open .sidebar-link span {
                opacity: 1;
                visibility: visible;
            }

            #sidebar.mobile-open .sidebar-link {
                justify-content: flex-start;
                padding: 15px 25px;
            }

            .main {
                margin-left: 0;
            }

            .nav-brand {
                display: block;
            }

            .mobile-toggle {
                display: block !important;
            }
        }

        /* Mobile Toggle Button */
        .mobile-toggle {
            display: none;
            background: var(--primary-blue);
            color: white;
            border: none;
            border-radius: 8px;
            width: 45px;
            height: 45px;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .mobile-toggle:hover {
            background: var(--secondary-blue);
            transform: scale(1.05);
        }

        /* Animation Classes */
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-in {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(-100%);
            }

            to {
                transform: translateX(0);
            }
        }

        /* Active State Management */
        .sidebar-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.15);
            border-left-color: var(--accent-yellow);
            box-shadow: inset 0 0 20px rgba(255, 255, 255, 0.05);
        }

        /* Scrollbar Styling */
        .sidebar-nav::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
    </style>
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Sidebar Toggle Functionality - FIXED
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById("sidebarToggle");
            const mobileToggle = document.getElementById("mobileToggle");
            const sidebar = document.getElementById("sidebar");

            // Desktop toggle (expand/collapse)
            if (sidebarToggle) {
                sidebarToggle.addEventListener("click", function() {
                    sidebar.classList.toggle("expand");
                });
            }

            // Mobile toggle (show/hide)
            if (mobileToggle) {
                mobileToggle.addEventListener("click", function() {
                    sidebar.classList.toggle("mobile-open");
                });
            }

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768) {
                    const isClickInsideSidebar = sidebar.contains(event.target);
                    const isClickOnMobileToggle = mobileToggle.contains(event.target);

                    if (!isClickInsideSidebar && !isClickOnMobileToggle && sidebar.classList.contains('mobile-open')) {
                        sidebar.classList.remove('mobile-open');
                    }
                }
            });

            // Auto-hide sidebar on mobile when navigating
            document.querySelectorAll('.sidebar-link').forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        sidebar.classList.remove('mobile-open');
                    }
                });
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('mobile-open');
                }
            });
        });

        // Search functionality (if needed)
        $(document).ready(function() {
            $("#searchInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("table tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
    <div class="wrapper">
        <!-- Sidebar -->
        <aside id="sidebar">
            <div class="sidebar-header">
                <div class="d-flex align-items-center justify-content-between gap-3">

                    <button class="toggle-btn" type="button" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>

                    <div class="sidebar-logo">
                        <a href="#">
                            <i class="fas fa-hands-helping"></i>
                            <span>Help Pinoy</span>
                        </a>
                    </div>

                </div>
            </div>

            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="index.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                        <i class="bi bi-bar-chart"></i>
                        <span>Overview</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="donation.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'donation.php' ? 'active' : ''; ?>">
                        <i class="bi bi-hand-thumbs-up"></i>
                        <span>Donations</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="users.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                        <i class="bi bi-people"></i>
                        <span>Donors</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="news.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'news.php' ? 'active' : ''; ?>">
                        <i class="bi bi-newspaper"></i>
                        <span>News</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="location.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'location.php' ? 'active' : ''; ?>">
                        <i class="bi bi-geo-alt"></i>
                        <span>Centers</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <a href="admin_logout.php" class="sidebar-link">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main">
            <!-- Top Navigation -->
            <nav class="top-navbar">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <button class="mobile-toggle" id="mobileToggle">
                                <i class="bi bi-list"></i>
                            </button>
                            <div class="nav-brand">
                                <i class="fas fa-hands-helping me-2"></i>
                                Help Pinoy Admin
                            </div>
                        </div>
                        <div class="user-info">
                            <div class="user-avatar">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div class="user-details">
                                <h5>Administrator</h5>
                                <small>Welcome back!</small>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <div class="main-content">