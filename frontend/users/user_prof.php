<?php
include './template/header.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: user_login.php');
    exit();
}


$user_id = $_SESSION['user_id'];
$api_url = 'http://localhost:5000/user/user_profile';
$data = json_encode(['user_id' => $user_id]);

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

echo "<script>console.log('API Response:', " . json_encode($result) . ");</script>";

$user = $result['user'] ?? [];
$donations = $result['donations'] ?? [];
$profile_img = $user['profile_img'] ?? null;

$update_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $update_api_url = 'http://localhost:5000/user/user_update_profile';
    $update_data = [
        'user_id' => $user_id,
        'name' => $_POST['name'] ?? '',
        'contact_number' => $_POST['contact_number'] ?? '',
        'gender' => $_POST['gender'] ?? '',
        'password' => $_POST['password'] ?? '',
        'confirm_password' => $_POST['confirm_password'] ?? ''
    ];
    $ch_update = curl_init($update_api_url);
    curl_setopt($ch_update, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch_update, CURLOPT_POST, true);
    curl_setopt($ch_update, CURLOPT_POSTFIELDS, json_encode($update_data));
    curl_setopt($ch_update, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch_update, CURLOPT_TIMEOUT, 10);
    $update_response = curl_exec($ch_update);
    curl_close($ch_update);

    $update_result = json_decode($update_response, true);
    if ($update_result && isset($update_result['success'])) {
        if ($update_result['success']) {
            $update_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>' . htmlspecialchars($update_result['message']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
            $ch = curl_init($api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $response = curl_exec($ch);
            curl_close($ch);
            $result = json_decode($response, true);
            $user = $result['user'] ?? [];
        } else {
            $update_message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>' . htmlspecialchars($update_result['message']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
        }
    } else {
        $update_message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>Failed to update profile.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>';
    }
}

$usage_data = null;

if (isset($_GET['view_usage'])) {

    $donation_id = intval($_GET['view_usage']);

    $url = "http://localhost:5000/user/donation_usage/" . $donation_id;

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $usage_data = json_decode($response, true);
}
?>

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Help Pinoy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #003366;
            --secondary-blue: #0057b7;
            --accent-yellow: #FFCC00;
            --light-blue: #e6f2ff;
            --success-green: #28a745;
            --warning-orange: #fd7e14;
            --danger-red: #dc3545;
            --dark-bg: #0f172a;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .profile-container {
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
            min-height: calc(100vh - 200px);
            padding: 40px 0;
        }

        /* Profile Card */
        .profile-card {
            background: white;
            border-radius: 28px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 35px;
            transition: var(--transition);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .profile-card:hover {
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        /* Profile Header */
        .profile-header {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 50%, #004a8f 100%);
            color: white;
            padding: 50px 0 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 40px 40px;
            animation: slowDrift 40s linear infinite;
            pointer-events: none;
        }

        @keyframes slowDrift {
            from {
                transform: translate(0, 0) rotate(0deg);
            }

            to {
                transform: translate(50px, 50px) rotate(360deg);
            }
        }

        .profile-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--accent-yellow), var(--accent-yellow), transparent);
        }

        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid rgba(255, 255, 255, 0.9);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            margin: 0 auto 20px;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, var(--light-blue), #fff);
            transition: var(--transition);
            cursor: pointer;
        }

        .profile-avatar:hover {
            transform: scale(1.02);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .change-photo-btn {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: linear-gradient(135deg, var(--accent-yellow), #ffb300);
            color: var(--primary-blue);
            border: none;
            border-radius: 50%;
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            z-index: 2;
        }

        .change-photo-btn:hover {
            transform: scale(1.1) rotate(15deg);
            background: linear-gradient(135deg, #ffb300, var(--accent-yellow));
        }

        .profile-header h2 {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 8px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .profile-header p {
            opacity: 0.85;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.15);
            padding: 8px 20px;
            border-radius: 50px;
            backdrop-filter: blur(10px);
        }

        /* Profile Info Section */
        .profile-info {
            padding: 50px;
        }

        .section-title {
            color: var(--primary-blue);
            font-weight: 800;
            margin-bottom: 35px;
            position: relative;
            padding-bottom: 15px;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: var(--accent-yellow);
            font-size: 1.8rem;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 70px;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-yellow), transparent);
            border-radius: 2px;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 28px;
        }

        .form-label {
            font-weight: 700;
            color: var(--primary-blue);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
        }

        .form-label i {
            color: var(--secondary-blue);
            width: 20px;
        }

        .form-control,
        .form-select {
            border: 2px solid #e9ecef;
            border-radius: 14px;
            padding: 12px 18px;
            font-size: 0.95rem;
            transition: var(--transition);
            background: #f8f9fa;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--secondary-blue);
            box-shadow: 0 0 0 4px rgba(0, 87, 183, 0.1);
            background: white;
        }

        .form-control[readonly] {
            background-color: #e9ecef;
            cursor: not-allowed;
        }

        /* Password Strength */
        .password-strength {
            height: 5px;
            border-radius: 3px;
            margin-top: 10px;
            transition: var(--transition);
            background: #e9ecef;
        }

        .strength-weak {
            background: var(--danger-red);
            width: 25%;
        }

        .strength-fair {
            background: var(--warning-orange);
            width: 50%;
        }

        .strength-good {
            background: #ffc107;
            width: 75%;
        }

        .strength-strong {
            background: var(--success-green);
            width: 100%;
        }

        .password-requirements {
            font-size: 0.8rem;
            margin-top: 12px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 12px;
        }

        .requirement {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
            transition: var(--transition);
        }

        .requirement.met {
            color: var(--success-green);
        }

        .requirement.met i {
            color: var(--success-green);
        }

        .requirement.unmet {
            color: #6c757d;
        }

        .requirement i {
            font-size: 0.7rem;
            width: 16px;
        }

        /* Save Button */
        .btn-save {
            background: linear-gradient(135deg, var(--accent-yellow) 0%, #ffb300 100%);
            border: none;
            color: var(--primary-blue);
            padding: 14px 45px;
            font-weight: 800;
            font-size: 1rem;
            border-radius: 50px;
            transition: var(--transition);
            box-shadow: 0 8px 20px rgba(255, 204, 0, 0.3);
            letter-spacing: 0.5px;
        }

        .btn-save:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(255, 204, 0, 0.4);
            background: linear-gradient(135deg, #ffb300 0%, var(--accent-yellow) 100%);
        }

        /* Donations Section */
        .donations-section {
            background: white;
            border-radius: 28px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 50px;
            animation: slideUp 0.6s ease-out 0.2s both;
        }

        .empty-donations {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-donations i {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 20px;
        }

        .empty-donations h5 {
            color: #6c757d;
            margin-bottom: 10px;
        }

        /* Donation Table */
        .donation-table-wrapper {
            overflow-x: auto;
            border-radius: 20px;
        }

        .donation-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .donation-table thead th {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            color: white;
            font-weight: 700;
            padding: 18px 20px;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .donation-table thead th:first-child {
            border-radius: 15px 0 0 0;
        }

        .donation-table thead th:last-child {
            border-radius: 0 15px 0 0;
        }

        .donation-table tbody tr {
            transition: var(--transition);
        }

        .donation-table tbody tr:hover {
            background: #f8f9fa;
            transform: translateX(5px);
        }

        .donation-table td {
            padding: 18px 20px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }

        /* Status Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-paid,
        .status-approved {
            background: rgba(40, 167, 69, 0.12);
            color: var(--success-green);
            border: 1px solid rgba(40, 167, 69, 0.2);
        }

        .status-pending {
            background: rgba(253, 126, 20, 0.12);
            color: var(--warning-orange);
            border: 1px solid rgba(253, 126, 20, 0.2);
        }

        .status-under_review {
            background: rgba(23, 162, 184, 0.12);
            color: #17a2b8;
            border: 1px solid rgba(23, 162, 184, 0.2);
        }

        .status-rejected {
            background: rgba(220, 53, 69, 0.12);
            color: var(--danger-red);
            border: 1px solid rgba(220, 53, 69, 0.2);
        }

        /* Action Buttons */
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.8rem;
            text-decoration: none;
            transition: var(--transition);
            border: none;
        }

        .btn-payment {
            background: linear-gradient(135deg, var(--accent-yellow), #ffb300);
            color: var(--primary-blue);
        }

        .btn-blockchain {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            text-decoration: none;
            filter: brightness(0.95);
        }

        /* Alerts */
        .alert {
            border-radius: 16px;
            border: none;
            padding: 16px 20px;
            margin-bottom: 30px;
            border-left: 4px solid transparent;
            animation: slideDown 0.4s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.08), rgba(40, 167, 69, 0.03));
            color: var(--success-green);
            border-left-color: var(--success-green);
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.08), rgba(220, 53, 69, 0.03));
            color: var(--danger-red);
            border-left-color: var(--danger-red);
        }

        /* Loading State */
        .btn-loading {
            position: relative;
            color: transparent !important;
            pointer-events: none;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid var(--primary-blue);
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Responsive */
        @media (max-width: 992px) {
            .profile-info {
                padding: 35px;
            }

            .donations-section {
                padding: 35px;
            }
        }

        @media (max-width: 768px) {
            .profile-info {
                padding: 25px;
            }

            .donations-section {
                padding: 25px;
            }

            .profile-header h2 {
                font-size: 1.4rem;
            }

            .profile-avatar {
                width: 120px;
                height: 120px;
            }

            .section-title {
                font-size: 1.3rem;
            }

            .donation-table thead th {
                font-size: 0.7rem;
                padding: 12px;
            }

            .donation-table td {
                padding: 12px;
                font-size: 0.85rem;
            }

            .btn-action {
                padding: 6px 12px;
                font-size: 0.7rem;
            }
        }

        @media (max-width: 576px) {
            .profile-container {
                padding: 20px 0;
            }

            .profile-info {
                padding: 20px;
            }

            .donations-section {
                padding: 20px;
            }
        }
    </style>
</head>

<body>

    <div class="profile-container">
        <div class="container">
            <!-- Profile Card -->
            <div class="profile-card">
                <div class="profile-header">
                    <div class="container">
                        <div class="profile-avatar">
                            <img id="profileImgPreview" src="<?= BASE_URL ?>assets/img/profile_img/<?php echo htmlspecialchars($profile_img, ENT_QUOTES, 'UTF-8'); ?>?t=<?php echo time(); ?>"
                                alt="<?php echo htmlspecialchars($user['name'] ?? 'User Profile Image', ENT_QUOTES, 'UTF-8'); ?>"
                                onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'150\' height=\'150\' viewBox=\'0 0 150 150\'%3E%3Crect width=\'150\' height=\'150\' fill=\'%23003366\'/%3E%3Ccircle cx=\'75\' cy=\'60\' r=\'30\' fill=\'%23FFCC00\'/%3E%3Cpath d=\'M75 100 Q40 100 30 140 L120 140 Q110 100 75 100Z\' fill=\'%23FFCC00\'/%3E%3C/svg%3E'">
                            <form id="profileImgForm" enctype="multipart/form-data" method="post">
                                <input type="file" name="profile_img" id="profile_img" accept="image/*" style="display:none;">
                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                                <button type="button" class="change-photo-btn" onclick="document.getElementById('profile_img').click();" title="Change Profile Photo">
                                    <i class="fas fa-camera"></i>
                                </button>
                            </form>
                        </div>
                        <h2><?php echo htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h2>
                        <p>
                            <i class="fas fa-envelope"></i>
                            <?php echo htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                    </div>
                </div>

                <!-- Profile Form -->
                <div class="profile-info">
                    <div class="section-title">
                        <i class="fas fa-user-edit"></i>
                        <span>Profile Information</span>
                    </div>

                    <?php echo $update_message; ?>

                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="profileForm">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-user"></i>
                                        Full Name
                                    </label>
                                    <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-phone"></i>
                                        Mobile Number
                                    </label>
                                    <input type="tel" class="form-control" name="contact_number" value="<?php echo htmlspecialchars($user['contact_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="09XX XXX XXXX">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-envelope"></i>
                                        Email Address
                                    </label>
                                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" readonly disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-lock"></i>
                                        New Password
                                    </label>
                                    <input type="password" class="form-control" name="password" id="password" placeholder="Leave blank to keep current password">
                                    <div id="passwordStrength" class="password-strength"></div>
                                    <div class="password-requirements" id="passwordRequirements" style="display: none;">
                                        <div class="requirement unmet" id="reqLength"><i class="fas fa-circle"></i> At least 8 characters</div>
                                        <div class="requirement unmet" id="reqUpper"><i class="fas fa-circle"></i> One uppercase letter</div>
                                        <div class="requirement unmet" id="reqLower"><i class="fas fa-circle"></i> One lowercase letter</div>
                                        <div class="requirement unmet" id="reqNumber"><i class="fas fa-circle"></i> One number</div>
                                        <div class="requirement unmet" id="reqSpecial"><i class="fas fa-circle"></i> One special character</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-check-circle"></i>
                                        Confirm Password
                                    </label>
                                    <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm new password">
                                    <small class="text-danger" id="confirmPasswordHelp"></small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-venus-mars"></i>
                                        Gender
                                    </label>
                                    <select class="form-select" name="gender">
                                        <option value="Male" <?php if (($user['gender'] ?? '') === 'Male') echo 'selected'; ?>>Male</option>
                                        <option value="Female" <?php if (($user['gender'] ?? '') === 'Female') echo 'selected'; ?>>Female</option>
                                        <option value="Other" <?php if (($user['gender'] ?? '') === 'Other') echo 'selected'; ?>>Other</option>
                                        <option value="Prefer not to say" <?php if (($user['gender'] ?? '') === 'Prefer not to say') echo 'selected'; ?>>Prefer not to say</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button class="btn btn-save" type="submit" name="update_profile" id="saveButton">
                                <i class="fas fa-save me-2"></i>
                                <span id="buttonText">Save Profile Changes</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Donations Section -->
            <div class="donations-section">
                <div class="section-title">
                    <i class="fas fa-hand-holding-heart"></i>
                    <span>Your Donation History</span>
                </div>

                <?php if (empty($donations)): ?>
                    <div class="empty-donations">
                        <i class="fas fa-heart-broken"></i>
                        <h5>No donations yet</h5>
                        <p class="text-muted">Your donation history will appear here once you make your first contribution.</p>
                        <a href="donation_form.php" class="btn btn-save mt-3">
                            <i class="fas fa-hand-holding-heart me-2"></i>Make Your First Donation
                        </a>
                    </div>
                <?php else: ?>
                    <div class="donation-table-wrapper">
                        <table class="donation-table">
                            <thead>
                                <tr>
                                    <th>Block</th>
                                    <th>Donation ID</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($donations as $donation): ?>
                                    <tr>

                                        <!-- BLOCK -->
                                        <td>
                                            <?php if (!empty($donation['blockchain_tx'])): ?>
                                                <a href="https://amoy.polygonscan.com/tx/<?php echo urlencode($donation['blockchain_tx']); ?>" target="_blank">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>

                                        <!-- DONATION ID -->
                                        <td>
                                            <code class="bg-light px-2 py-1 rounded">
                                                <?php echo htmlspecialchars(substr($donation['public_id'] ?? '', 0, 10) . '...'); ?>
                                            </code>
                                        </td>

                                        <!-- AMOUNT -->
                                        <td class="fw-bold text-success">
                                            ₱<?php echo number_format($donation['amount'] ?? 0, 2); ?>
                                        </td>

                                        <!-- STATUS -->
                                        <td>
                                            <?php
                                            $status = strtolower($donation['donation_status'] ?? 'unknown');
                                            $statusClass = 'status-pending';
                                            $statusIcon = 'fa-question-circle';

                                            if ($status === 'approved') {
                                                $statusClass = 'status-approved';
                                                $statusIcon = 'fa-check-circle';
                                            } elseif ($status === 'pending') {
                                                $statusClass = 'status-pending';
                                                $statusIcon = 'fa-clock';
                                            } elseif ($status === 'under_review') {
                                                $statusClass = 'status-under_review';
                                                $statusIcon = 'fa-search';
                                            } elseif ($status === 'rejected') {
                                                $statusClass = 'status-rejected';
                                                $statusIcon = 'fa-times-circle';
                                            }
                                            ?>

                                            <span class="status-badge <?php echo $statusClass; ?>">
                                                <i class="fas <?php echo $statusIcon; ?>"></i>
                                                <?php echo ucfirst($status); ?>
                                            </span>
                                        </td>

                                        <!-- DATE -->
                                        <td>
                                            <?php echo date('M d, Y', strtotime($donation['donation_date'] ?? '')); ?>
                                        </td>

                                        <!-- DETAILS BUTTON -->
                                        <td>

                                            <?php if ($status === 'approved'): ?>

                                                <form method="GET" style="display:inline;">
                                                    <input type="hidden" name="view_usage" value="<?php echo $donation['donation_id']; ?>">
                                                    <button type="submit" class="btn-action btn-blockchain">
                                                        <i class="fas fa-eye"></i> Details
                                                    </button>
                                                </form>

                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>

                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            <?php if ($usage_data && $usage_data['status'] === 'success'): ?>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Donation Usage Details</h5>
                    </div>

                    <div class="card-body">

                        <p><strong>Total:</strong> ₱<?php echo number_format($usage_data['donation']['total_amount'], 2); ?></p>
                        <p><strong>Used:</strong> ₱<?php echo number_format($usage_data['donation']['used_amount'], 2); ?></p>
                        <p><strong>Remaining:</strong> ₱<?php echo number_format($usage_data['donation']['remaining_amount'], 2); ?></p>

                        <hr>

                        <?php if (empty($usage_data['usage'])): ?>
                            <p>No disbursement yet.</p>
                        <?php else: ?>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Project</th>
                                        <th>Amount Used</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($usage_data['usage'] as $u): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($u['project_name']); ?></td>
                                            <td>₱<?php echo number_format($u['allocated_amount'], 2); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($u['disbursement_date'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                        <?php endif; ?>

                    </div>
                </div>

            <?php endif; ?>
        </div>
    </div>

    <?php include './template/footer.php'; ?>

    <script>
        // Password strength checker
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('passwordStrength');
        const requirementsDiv = document.getElementById('passwordRequirements');

        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;

                if (password.length > 0) {
                    requirementsDiv.style.display = 'block';

                    const hasLength = password.length >= 8;
                    const hasUpper = /[A-Z]/.test(password);
                    const hasLower = /[a-z]/.test(password);
                    const hasNumber = /[0-9]/.test(password);
                    const hasSpecial = /[\W_]/.test(password);

                    updateRequirement('reqLength', hasLength);
                    updateRequirement('reqUpper', hasUpper);
                    updateRequirement('reqLower', hasLower);
                    updateRequirement('reqNumber', hasNumber);
                    updateRequirement('reqSpecial', hasSpecial);

                    let strength = [hasLength, hasUpper, hasLower, hasNumber, hasSpecial].filter(Boolean).length;

                    strengthBar.className = 'password-strength';
                    if (strength <= 1) strengthBar.classList.add('strength-weak');
                    else if (strength <= 2) strengthBar.classList.add('strength-fair');
                    else if (strength <= 3) strengthBar.classList.add('strength-good');
                    else strengthBar.classList.add('strength-strong');
                } else {
                    requirementsDiv.style.display = 'none';
                    strengthBar.className = 'password-strength';
                }
            });
        }

        function updateRequirement(id, met) {
            const element = document.getElementById(id);
            if (!element) return;

            element.className = met ? 'requirement met' : 'requirement unmet';
            const icon = element.querySelector('i');
            if (icon) {
                icon.className = met ? 'fas fa-check-circle' : 'fas fa-circle';
            }
        }

        // Confirm password validation
        const confirmInput = document.getElementById('confirm_password');
        if (confirmInput) {
            confirmInput.addEventListener('input', function() {
                const password = document.getElementById('password').value;
                const confirmHelp = document.getElementById('confirmPasswordHelp');

                if (!confirmHelp) return;

                if (this.value && password !== this.value) {
                    confirmHelp.textContent = "Passwords do not match";
                    this.style.borderColor = '#dc3545';
                } else if (this.value && password === this.value) {
                    confirmHelp.textContent = "Passwords match";
                    confirmHelp.style.color = '#28a745';
                    this.style.borderColor = '#28a745';
                } else {
                    confirmHelp.textContent = "";
                    this.style.borderColor = '#e9ecef';
                }
            });
        }

        // Form submission validation
        const profileForm = document.getElementById('profileForm');
        if (profileForm) {
            profileForm.addEventListener('submit', function(e) {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

                if (password.length > 0 && !regex.test(password)) {
                    e.preventDefault();
                    alert('Password does not meet requirements.');
                    return;
                }

                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Passwords do not match.');
                    return;
                }

                profileForm.addEventListener('submit', function(e) {
                    const button = document.getElementById('saveButton');
                    const buttonText = document.getElementById('buttonText');

                    setTimeout(() => {
                        if (button && buttonText) {
                            button.classList.add('btn-loading');
                            buttonText.textContent = 'Saving Changes...';
                            button.disabled = true;
                        }
                    }, 50);
                });

            });
        }
        const fileInput = document.getElementById('profile_img');
        if (fileInput) {
            fileInput.addEventListener('change', function() {

                if (!this.files.length) return;

                const form = document.getElementById('profileImgForm');
                const formData = new FormData(form);

                const button = document.querySelector('.change-photo-btn');
                const originalIcon = button.innerHTML;

                button.innerHTML = '<i class="fas fa-spinner fa-pulse"></i>';
                button.disabled = true;

                fetch('http://localhost:5000/user/user_upload_profile_image', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('profileImgPreview').src =
                                `<?= BASE_URL ?>assets/img/profile_img/${data.filename}?t=${Date.now()}`;

                            showToast('Profile updated', 'success');
                        } else {
                            throw new Error(data.message);
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        showToast('Upload failed', 'error');
                    })
                    .finally(() => {
                        button.innerHTML = originalIcon;
                        button.disabled = false;
                    });
            });
        }

        function showToast(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} fade show`;
            alertDiv.style.position = 'fixed';
            alertDiv.style.top = '20px';
            alertDiv.style.right = '20px';
            alertDiv.style.zIndex = '9999';

            alertDiv.innerHTML = message;
            document.body.appendChild(alertDiv);

            setTimeout(() => alertDiv.remove(), 3000);
        }
    </script>
</body>

</html>