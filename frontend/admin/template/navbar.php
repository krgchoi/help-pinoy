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

$toast_message = isset($_SESSION['toast_message']) ? $_SESSION['toast_message'] : '';
$toast_type    = isset($_SESSION['toast_type'])    ? $_SESSION['toast_type']    : 'success';
unset($_SESSION['toast_message']);
unset($_SESSION['toast_type']);


$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Pinoy — Admin</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <!-- Admin CSS -->
    <link rel="stylesheet" href="assets/css/admin.css">

    <style>
        /* ================================================
           HELP PINOY — SHELL STYLES (navbar + sidebar)
           Scoped to layout only. Page styles go in each file.
        ================================================ */
        :root {
            --hp-navy: #002855;
            --hp-blue: #0057b7;
            --hp-blue-light: #1a73e8;
            --hp-gold: #FFCC00;
            --hp-gold-dark: #c8960c;
            --hp-sidebar-w: 260px;
            --hp-sidebar-collapsed: 72px;
            --hp-topbar-h: 64px;
            --hp-speed: 0.25s;
            --hp-text: #1e293b;
            --hp-muted: #64748b;
            --hp-border: #e2e8f0;
            --hp-surface: #f8fafc;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            color: var(--hp-text);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── Wrapper ── */
        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* ================================================
           SIDEBAR
        ================================================ */
        #sidebar {
            width: var(--hp-sidebar-w);
            background: var(--hp-navy);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            transition: width var(--hp-speed) ease;
            overflow: hidden;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.12);
        }

        /* Collapsed state */
        #sidebar.expand {
            width: var(--hp-sidebar-collapsed);
        }

        #sidebar.expand .sb-logo-text,
        #sidebar.expand .sb-link-label,
        #sidebar.expand .sb-section-label,
        #sidebar.expand .sb-footer-label {
            opacity: 0;
            width: 0;
            overflow: hidden;
            white-space: nowrap;
        }

        #sidebar.expand .sb-link {
            justify-content: center;
            padding: 12px 0;
        }

        #sidebar.expand .sb-footer-link {
            justify-content: center;
            padding: 12px 0;
        }

        /* ── Sidebar Header ── */
        .sb-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            height: var(--hp-topbar-h);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            flex-shrink: 0;
        }

        .sb-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            overflow: hidden;
        }

        .sb-logo-icon {
            width: 36px;
            height: 36px;
            background: var(--hp-gold);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--hp-navy);
            font-size: 1rem;
            flex-shrink: 0;
        }

        .sb-logo-text {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            color: white;
            white-space: nowrap;
            transition: opacity var(--hp-speed), width var(--hp-speed);
        }

        .sb-logo-text span {
            color: var(--hp-gold);
        }

        .sb-toggle {
            background: rgba(255, 255, 255, 0.08);
            border: none;
            color: rgba(255, 255, 255, 0.7);
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.1rem;
            flex-shrink: 0;
            transition: background 0.2s, color 0.2s;
        }

        .sb-toggle:hover {
            background: rgba(255, 255, 255, 0.15);
            color: white;
        }

        /* ── Nav ── */
        .sb-nav {
            flex: 1;
            padding: 12px 0;
            overflow-y: auto;
            overflow-x: hidden;
            list-style: none;
        }

        .sb-nav::-webkit-scrollbar {
            width: 4px;
        }

        .sb-nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sb-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 4px;
        }

        .sb-section-label {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.3);
            padding: 16px 20px 6px;
            white-space: nowrap;
            transition: opacity var(--hp-speed), width var(--hp-speed);
        }

        .sb-item {
            margin: 2px 10px;
        }

        .sb-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: 10px;
            color: rgba(255, 255, 255, 0.65);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
            white-space: nowrap;
            position: relative;
        }

        .sb-link i {
            font-size: 1.1rem;
            width: 22px;
            text-align: center;
            flex-shrink: 0;
        }

        .sb-link-label {
            transition: opacity var(--hp-speed), width var(--hp-speed);
            overflow: hidden;
        }

        .sb-link:hover {
            background: rgba(255, 255, 255, 0.08);
            color: white;
        }

        .sb-link:hover i {
            color: var(--hp-gold);
        }

        .sb-link.active {
            background: rgba(255, 204, 0, 0.12);
            color: white;
            border: 1px solid rgba(255, 204, 0, 0.2);
        }

        .sb-link.active i {
            color: var(--hp-gold);
        }

        .sb-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 20%;
            bottom: 20%;
            width: 3px;
            background: var(--hp-gold);
            border-radius: 0 3px 3px 0;
            margin-left: -10px;
        }

        /* ── Sidebar Footer ── */
        .sb-footer {
            padding: 12px 10px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            flex-shrink: 0;
        }

        .sb-footer-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: 10px;
            color: rgba(255, 100, 100, 0.8);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .sb-footer-link i {
            font-size: 1.1rem;
            width: 22px;
            text-align: center;
            flex-shrink: 0;
        }

        .sb-footer-label {
            transition: opacity var(--hp-speed), width var(--hp-speed);
            overflow: hidden;
        }

        .sb-footer-link:hover {
            background: rgba(220, 53, 69, 0.15);
            color: #ff6b7a;
        }

        /* ================================================
           MAIN AREA
        ================================================ */
        .main {
            flex: 1;
            margin-left: var(--hp-sidebar-w);
            transition: margin-left var(--hp-speed) ease;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        #sidebar.expand~.main {
            margin-left: var(--hp-sidebar-collapsed);
        }

        /* ── Top Navbar ── */
        .top-navbar {
            height: var(--hp-topbar-h);
            background: white;
            border-bottom: 1px solid var(--hp-border);
            display: flex;
            align-items: center;
            padding: 0 24px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 8px rgba(0, 0, 0, 0.06);
        }

        .tn-left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }

        .tn-mobile-toggle {
            display: none;
            background: var(--hp-surface);
            border: 1px solid var(--hp-border);
            color: var(--hp-text);
            width: 38px;
            height: 38px;
            border-radius: 9px;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.1rem;
            transition: all 0.2s;
        }

        .tn-mobile-toggle:hover {
            background: var(--hp-border);
        }

        .tn-brand {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            color: var(--hp-navy);
            display: none;
        }

        /* Page title injected per page */
        .tn-page-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--hp-muted);
        }

        .tn-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Notification bell */
        .tn-icon-btn {
            width: 38px;
            height: 38px;
            background: var(--hp-surface);
            border: 1px solid var(--hp-border);
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--hp-muted);
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.2s;
            text-decoration: none;
        }

        .tn-icon-btn:hover {
            background: var(--hp-border);
            color: var(--hp-text);
        }

        /* User pill */
        .tn-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 12px 6px 6px;
            background: var(--hp-surface);
            border: 1px solid var(--hp-border);
            border-radius: 40px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .tn-user:hover {
            border-color: #cbd5e1;
            background: white;
        }

        .tn-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--hp-navy), var(--hp-blue));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .tn-user-info {
            line-height: 1.2;
        }

        .tn-user-name {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--hp-text);
        }

        .tn-user-role {
            font-size: 0.68rem;
            color: var(--hp-muted);
        }

        /* ── Main Content ── */
        .main-content {
            flex: 1;
            padding: 28px 32px;
            background: #f1f5f9;
        }

        /* ================================================
           TOAST NOTIFICATIONS
        ================================================ */
        .hp-toast-container {
            position: fixed;
            top: 76px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .hp-toast {
            min-width: 300px;
            max-width: 380px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            overflow: hidden;
            transform: translateX(420px);
            animation: hpToastIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        .hp-toast.hp-toast-out {
            animation: hpToastOut 0.25s ease forwards;
        }

        @keyframes hpToastIn {
            to {
                transform: translateX(0);
            }
        }

        @keyframes hpToastOut {
            to {
                transform: translateX(420px);
            }
        }

        .hp-toast-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 14px;
            color: white;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .hp-toast-head-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .hp-toast-close {
            background: none;
            border: none;
            color: white;
            opacity: 0.75;
            cursor: pointer;
            font-size: 1rem;
            line-height: 1;
            padding: 0;
        }

        .hp-toast-close:hover {
            opacity: 1;
        }

        .hp-toast-body {
            padding: 11px 14px;
            font-size: 0.85rem;
            color: #334155;
        }

        .hp-toast-bar {
            height: 3px;
            background: rgba(255, 255, 255, 0.4);
        }

        .hp-toast-bar-fill {
            height: 100%;
            background: white;
            animation: hpBarShrink 3s linear forwards;
        }

        @keyframes hpBarShrink {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }

        .hp-toast-success .hp-toast-head {
            background: linear-gradient(135deg, #16a34a, #15803d);
        }

        .hp-toast-error .hp-toast-head {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
        }

        .hp-toast-warning .hp-toast-head {
            background: linear-gradient(135deg, #d97706, #b45309);
        }

        .hp-toast-info .hp-toast-head {
            background: linear-gradient(135deg, #0284c7, #0369a1);
        }

        /* ================================================
           MOBILE
        ================================================ */
        @media (max-width: 768px) {
            #sidebar {
                transform: translateX(-100%);
                width: var(--hp-sidebar-w);
                transition: transform var(--hp-speed) ease;
            }

            #sidebar.mobile-open {
                transform: translateX(0);
            }

            #sidebar.expand {
                width: var(--hp-sidebar-w);
            }

            #sidebar.expand .sb-logo-text,
            #sidebar.expand .sb-link-label,
            #sidebar.expand .sb-section-label,
            #sidebar.expand .sb-footer-label {
                opacity: 1;
                width: auto;
            }

            #sidebar.expand .sb-link,
            #sidebar.expand .sb-footer-link {
                justify-content: flex-start;
                padding: 11px 14px;
            }

            .main {
                margin-left: 0;
            }

            .tn-mobile-toggle {
                display: flex;
            }

            .tn-brand {
                display: block;
            }

            .main-content {
                padding: 16px;
            }
        }

        /* Mobile overlay backdrop */
        .sb-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1040;
        }

        .sb-backdrop.show {
            display: block;
        }
    </style>
</head>

<body>

    <!-- Bootstrap JS + jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Toast container -->
    <div class="hp-toast-container" id="hpToastContainer"></div>

    <!-- Mobile backdrop -->
    <div class="sb-backdrop" id="sbBackdrop"></div>

    <script>
        /* ── Toast System ── */
        function showToast(message, type = 'success', duration = 3500) {
            const container = document.getElementById('hpToastContainer');
            const id = 'toast_' + Date.now();
            const icons = {
                success: 'bi-check-circle-fill',
                error: 'bi-x-circle-fill',
                warning: 'bi-exclamation-triangle-fill',
                info: 'bi-info-circle-fill'
            };
            const titles = {
                success: 'Success',
                error: 'Error',
                warning: 'Warning',
                info: 'Info'
            };

            container.insertAdjacentHTML('beforeend', `
        <div class="hp-toast hp-toast-${type}" id="${id}">
            <div class="hp-toast-head">
                <div class="hp-toast-head-left">
                    <i class="bi ${icons[type]}"></i>
                    <span>${titles[type]}</span>
                </div>
                <button class="hp-toast-close" onclick="closeToast('${id}')">&times;</button>
            </div>
            <div class="hp-toast-body">${message}</div>
            <div class="hp-toast-bar"><div class="hp-toast-bar-fill"></div></div>
        </div>`);

            setTimeout(() => closeToast(id), duration);
        }

        function closeToast(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.add('hp-toast-out');
            setTimeout(() => el.remove(), 280);
        }

        // Legacy alias so existing page code still works
        window.triggerToast = showToast;

        /* ── Sidebar ── */
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sbBackdrop');
            const deskBtn = document.getElementById('sidebarToggle');
            const mobileBtn = document.getElementById('mobileToggle');

            if (deskBtn) {
                deskBtn.addEventListener('click', () => sidebar.classList.toggle('expand'));
            }

            function openMobile() {
                sidebar.classList.add('mobile-open');
                backdrop.classList.add('show');
            }

            function closeMobile() {
                sidebar.classList.remove('mobile-open');
                backdrop.classList.remove('show');
            }

            if (mobileBtn) mobileBtn.addEventListener('click', openMobile);
            if (backdrop) backdrop.addEventListener('click', closeMobile);

            document.querySelectorAll('.sb-link, .sb-footer-link').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 768) closeMobile();
                });
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth > 768) closeMobile();
            });

            /* Flash toast from PHP session */
            <?php if ($toast_message): ?>
                showToast('<?php echo addslashes($toast_message); ?>', '<?php echo $toast_type; ?>');
            <?php endif; ?>
        });

        /* ── Table search (global, works on any page with #searchInput) ── */
        $(document).ready(function() {
            $('#searchInput').on('keyup', function() {
                const val = $(this).val().toLowerCase();
                $('table tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().includes(val));
                });
            });
        });
    </script>

    <div class="wrapper">

        <!-- ════ SIDEBAR ════ -->
        <aside id="sidebar">
            <div class="sb-header">
                <a href="index.php" class="sb-logo">
                    <div class="sb-logo-icon"><i class="fas fa-hands-helping"></i></div>
                    <div class="sb-logo-text">Help<span>Pinoy</span></div>
                </a>
                <button class="sb-toggle" id="sidebarToggle" title="Toggle sidebar">
                    <i class="bi bi-layout-sidebar-inset"></i>
                </button>
            </div>

            <ul class="sb-nav">
                <div class="sb-section-label">Main</div>

                <li class="sb-item">
                    <a href="index.php" class="sb-link <?= $current_page === 'index.php' ? 'active' : '' ?>">
                        <i class="bi bi-grid-1x2"></i>
                        <span class="sb-link-label">Overview</span>
                    </a>
                </li>
                <li class="sb-item">
                    <a href="donation.php" class="sb-link <?= $current_page === 'donation.php' ? 'active' : '' ?>">
                        <i class="bi bi-heart"></i>
                        <span class="sb-link-label">Donations</span>
                    </a>
                </li>
                <li class="sb-item">
                    <a href="disbursement.php" class="sb-link <?= $current_page === 'disbursement.php' ? 'active' : '' ?>">
                        <i class="bi bi-cash-stack"></i>
                        <span class="sb-link-label">Disbursement</span>
                    </a>
                </li>

                <div class="sb-section-label">Management</div>

                <li class="sb-item">
                    <a href="users.php" class="sb-link <?= $current_page === 'users.php' ? 'active' : '' ?>">
                        <i class="bi bi-people"></i>
                        <span class="sb-link-label">Donors</span>
                    </a>
                </li>
                <li class="sb-item">
                    <a href="news.php" class="sb-link <?= $current_page === 'news.php' ? 'active' : '' ?>">
                        <i class="bi bi-newspaper"></i>
                        <span class="sb-link-label">News</span>
                    </a>
                </li>
                <li class="sb-item">
                    <a href="location.php" class="sb-link <?= $current_page === 'location.php' ? 'active' : '' ?>">
                        <i class="bi bi-geo-alt"></i>
                        <span class="sb-link-label">Centers</span>
                    </a>
                </li>
            </ul>

            <div class="sb-footer">
                <a href="admin_logout.php" class="sb-footer-link">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="sb-footer-label">Logout</span>
                </a>
            </div>
        </aside>

        <!-- ════ MAIN ════ -->
        <div class="main">

            <!-- Top Navbar -->
            <nav class="top-navbar">
                <div class="tn-left">
                    <button class="tn-mobile-toggle" id="mobileToggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="tn-brand">
                        <i class="fas fa-hands-helping me-1"></i> Help Pinoy
                    </div>
                </div>

                <div class="tn-right">
                    <a href="#" class="tn-icon-btn" title="Notifications">
                        <i class="bi bi-bell"></i>
                    </a>
                    <div class="tn-user">
                        <div class="tn-avatar">AD</div>
                        <div class="tn-user-info">
                            <div class="tn-user-name">Administrator</div>
                            <div class="tn-user-role">Super Admin</div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page content starts here -->
            <div class="main-content">