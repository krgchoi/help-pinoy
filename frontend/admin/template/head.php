<?php
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
session_start();

if (!isset($_SESSION['jwt_token'])) {
    echo "Session token not set";
    header("Location: admin_login.php");
    exit();
}

$jwt_token = $_SESSION['jwt_token'];
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
    <link rel="stylesheet" href="../assets/css/admin.css">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>

<body>
    <div class="wrapper">
        <aside id="sidebar" class="expand">
            <div class="d-flex">
                <button class="toggle-btn" type="button">
                    <i class="bi bi-list"></i>
                </button>
                <div class="sidebar-logo">

                    <a href="#">Help Pinoy</a>
                </div>
            </div>
            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="dashboard.php" class="sidebar-link">
                        <i class="bi bi-bar-chart"></i>
                        <span>Overview</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="donation.php" class="sidebar-link">
                        <i class="bi bi-hand-thumbs-up"></i>
                        <span>Donations</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="users.php" class="sidebar-link">
                        <i class="bi bi-people"></i>
                        <span>Donors</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="news.php" class="sidebar-link">
                        <i class="bi bi-chat-dots"></i>
                        <span>News</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="location.php" class="sidebar-link">
                        <i class="bi bi-geo-alt"></i>
                        <span>Centers</span>
                    </a>
                </li>
                <!-- <li class="sidebar-item">
                    <a href="report.php" class="sidebar-link">
                        <i class="lni lni-file-pencil"></i>
                        <span>Reports</span>
                    </a>
                </li> -->
            </ul>
            <div class="sidebar-footer">
                <a href="admin_logout.php" class="sidebar-link">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <div class="main p-3">