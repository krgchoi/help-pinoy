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
            $update_message = '<div class="alert alert-success">' . htmlspecialchars($update_result['message']) . '</div>';
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
            $update_message = '<div class="alert alert-danger">' . htmlspecialchars($update_result['message']) . '</div>';
        }
    } else {
        $update_message = '<div class="alert alert-danger">Failed to update profile.</div>';
    }
}

?>
<style>
    :root {
        --primary-blue: #003366;
        --secondary-blue: #0057b7;
        --accent-yellow: #FFCC00;
        --light-blue: #e6f2ff;
        --success-green: #28a745;
        --warning-orange: #fd7e14;
        --danger-red: #dc3545;
    }

    .profile-container {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        min-height: 100vh;
        padding: 20px 0;
    }

    .profile-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 30px;
        border: none;
    }

    .profile-header {
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
        color: white;
        padding: 40px 0;
        text-align: center;
        position: relative;
    }

    .profile-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none"><path d="M1200 120L0 16.48 0 0 1200 0 1200 120z" fill="white"/></svg>');
        background-size: cover;
        background-position: bottom;
        opacity: 0.1;
    }

    .profile-avatar {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        border: 5px solid white;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        margin: 0 auto 20px;
        position: relative;
        overflow: hidden;
        background: var(--light-blue);
        transition: all 0.3s ease;
    }

    .profile-avatar:hover {
        transform: scale(1.05);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
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
        background: var(--accent-yellow);
        color: var(--primary-blue);
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(255, 204, 0, 0.3);
    }

    .change-photo-btn:hover {
        background: #ffd700;
        transform: scale(1.1);
    }

    .profile-info {
        padding: 40px;
    }

    .section-title {
        color: var(--primary-blue);
        font-weight: 700;
        margin-bottom: 30px;
        position: relative;
        padding-bottom: 15px;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 60px;
        height: 3px;
        background: var(--accent-yellow);
        border-radius: 2px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-label {
        font-weight: 600;
        color: var(--primary-blue);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
    }

    .form-label i {
        margin-right: 8px;
        color: var(--secondary-blue);
    }

    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 16px;
        transition: all 0.3s ease;
        background: #f8f9fa;
    }

    .form-control:focus {
        border-color: var(--secondary-blue);
        box-shadow: 0 0 0 0.2rem rgba(0, 87, 183, 0.25);
        background: white;
    }

    .form-control[readonly] {
        background-color: #f8f9fa;
        opacity: 0.8;
    }

    .btn-save {
        background: linear-gradient(135deg, var(--accent-yellow) 0%, #ffd700 100%);
        border: none;
        color: var(--primary-blue);
        padding: 15px 40px;
        font-weight: 700;
        font-size: 1.1rem;
        border-radius: 12px;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(255, 204, 0, 0.3);
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(255, 204, 0, 0.4);
        background: linear-gradient(135deg, #ffd700 0%, var(--accent-yellow) 100%);
    }

    /* Donations Section */
    .donations-section {
        background: white;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        padding: 40px;
        margin-top: 30px;
    }

    .donation-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-top: 20px;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    .donation-table thead {
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
    }

    .donation-table th {
        color: white;
        font-weight: 600;
        padding: 20px;
        text-align: left;
        border: none;
        font-size: 1rem;
    }

    .donation-table td {
        padding: 20px;
        border-bottom: 1px solid #f0f0f0;
        background: white;
        transition: all 0.3s ease;
    }

    .donation-table tbody tr:hover td {
        background: #f8f9fa;
        transform: translateY(-1px);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .donation-table tbody tr:last-child td {
        border-bottom: none;
    }

    .status-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-paid {
        background: rgba(40, 167, 69, 0.1);
        color: var(--success-green);
        border: 1px solid rgba(40, 167, 69, 0.2);
    }

    .status-pending {
        background: rgba(253, 126, 20, 0.1);
        color: var(--warning-orange);
        border: 1px solid rgba(253, 126, 20, 0.2);
    }

    .btn-action {
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        font-size: 0.9rem;
    }

    .btn-blockchain {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-payment {
        background: linear-gradient(135deg, var(--accent-yellow) 0%, #ffd700 100%);
        color: var(--primary-blue);
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        color: white;
        text-decoration: none;
    }

    /* Password Strength */
    .password-strength {
        height: 4px;
        border-radius: 2px;
        margin-top: 5px;
        transition: all 0.3s ease;
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
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 5px;
    }

    .requirement {
        display: flex;
        align-items: center;
        margin-bottom: 2px;
    }

    .requirement.met {
        color: var(--success-green);
    }

    .requirement.unmet {
        color: #6c757d;
    }

    .requirement i {
        margin-right: 5px;
        font-size: 0.75rem;
    }

    /* Alerts */
    .alert {
        border-radius: 12px;
        border: none;
        padding: 15px 20px;
        margin-bottom: 25px;
        border-left: 4px solid transparent;
    }

    .alert-success {
        background: rgba(40, 167, 69, 0.1);
        color: var(--success-green);
        border-left-color: var(--success-green);
    }

    .alert-danger {
        background: rgba(220, 53, 69, 0.1);
        color: var(--danger-red);
        border-left-color: var(--danger-red);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .profile-info {
            padding: 20px;
        }

        .donations-section {
            padding: 20px;
        }

        .donation-table {
            display: block;
            overflow-x: auto;
        }

        .profile-header {
            padding: 30px 0;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
        }
    }

    /* Loading animation */
    .btn-loading {
        position: relative;
        color: transparent;
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
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>

<div class="profile-container">
    <div class="container">
        <!-- Profile Header -->
        <div class="profile-card">
            <div class="profile-header">
                <div class="container">
                    <div class="profile-avatar">
                        <img id="profileImgPreview" src="../../static/profile_img/<?php echo htmlspecialchars($profile_img, ENT_QUOTES, 'UTF-8'); ?>?t=<?php echo time(); ?>"
                            alt="<?php echo htmlspecialchars($user['name'] ?? 'User Profile Image', ENT_QUOTES, 'UTF-8'); ?>"
                            onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTUwIiBoZWlnaHQ9IjE1MCIgdmlld0JveD0iMCAwIDE1MCAxNTAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iNzUiIGN5PSI3NSIgcj0iNzUiIGZpbGw9IiNlNmYyZmYiLz48cGF0aCBkPSJNNzUgODVBNzUgNzUgMCAwIDEgNzUgMTVWNzVINzVINzVaIiBmaWxsPSIjMDA1N2I3Ii8+PHBhdGggZD0iTTc1IDE1QTc1IDc1IDAgMCAwIDc1IDg1VjE1SDc1SDc1WiIgZmlsbD0iIzAwMzM2NiIvPjxjaXJjbGUgY3g9Ijc1IiBjeT0iNjAiIHI9IjI1IiBmaWxsPSIjZmZjYzAwIi8+PC9zdmc+'">
                        <form id="profileImgForm" enctype="multipart/form-data" method="post">
                            <input type="file" name="profile_img" id="profile_img" accept="image/*" style="display:none;">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                            <button type="button" class="change-photo-btn" onclick="document.getElementById('profile_img').click();" title="Change Profile Photo">
                                <i class="fas fa-camera"></i>
                            </button>
                        </form>
                    </div>
                    <h2 class="mb-2"><?php echo htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h2>
                    <p class="mb-0 opacity-75"><?php echo htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </div>

            <!-- Profile Form -->
            <div class="profile-info">
                <h3 class="section-title">Profile Information</h3>

                <?php echo $update_message; ?>

                <form method="post" action="" id="profileForm">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-user"></i>Full Name</label>
                                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-phone"></i>Mobile Number</label>
                                <input type="text" class="form-control" name="contact_number" value="<?php echo htmlspecialchars($user['contact_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-envelope"></i>Email Address</label>
                                <input type="text" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-lock"></i>Password</label>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Enter new password">
                                <div class="password-strength" id="passwordStrength"></div>
                                <div class="password-requirements" id="passwordRequirements">
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
                                <label class="form-label"><i class="fas fa-lock"></i>Confirm Password</label>
                                <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm new password">
                                <div class="form-text text-danger" id="confirmPasswordHelp"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-venus-mars"></i>Gender</label>
                                <select class="form-control" name="gender">
                                    <option value="Male" <?php if (($user['gender'] ?? '') === 'Male') echo 'selected'; ?>>Male</option>
                                    <option value="Female" <?php if (($user['gender'] ?? '') === 'Female') echo 'selected'; ?>>Female</option>
                                    <option value="Other" <?php if (($user['gender'] ?? '') === 'Other') echo 'selected'; ?>>Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button class="btn btn-save" type="submit" name="update_profile" id="saveButton">
                            <span id="buttonText">Save Profile Changes</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Donations Section -->
        <div class="donations-section">
            <h3 class="section-title">Your Donation History</h3>

            <?php if (empty($donations)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-hand-holding-heart fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No donations yet</h5>
                    <p class="text-muted">Your donation history will appear here once you make your first contribution.</p>
                    <a href="donation_form.php" class="btn btn-save mt-3">Make Your First Donation</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="donation-table">
                        <thead>
                            <tr>
                                <th>DONATION #</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($donations as $donation): ?>
                                <tr>
                                    <td>
                                        <?php
                                        $public_id = $donation['public_id'] ?? '';
                                        echo htmlspecialchars(substr($public_id, 0, 8) . '...', ENT_QUOTES, 'UTF-8');
                                        ?>
                                    </td>
                                    <td>₱<?php echo number_format($donation['amount'] ?? 0, 2); ?></td>
                                    <td>
                                        <?php
                                        echo htmlspecialchars(ucfirst($donation['donation_status'] ?? 'Unknown'));
                                        ?>
                                    </td>
                                    <td><?php
                                        echo date('M d, Y', strtotime($donation['donation_date'] ?? '')); ?></td>
                                    <td>
                                        <?php
                                        $status = strtolower($donation['donation_status'] ?? '');
                                        $receipt_uploaded = !empty($donation['receipt_image']);
                                        $tx = $donation['blockchain_tx'] ?? '';
                                        $donation_id = $donation['donation_id'] ?? '';
                                        ?>
                                        <?php if ($status === 'pending' && !$receipt_uploaded): ?>

                                            <a href="upload_receipt.php?donation_id=<?php echo urlencode($donation_id); ?>"
                                                class="btn-action btn-payment">
                                                <i class="fas fa-upload me-1"></i>Upload Receipt
                                            </a>

                                        <?php elseif ($status === 'under_review' && $receipt_uploaded): ?>

                                            <span class="text-warning fw-bold">
                                                <i class="fas fa-clock me-1"></i>Verifying
                                            </span>

                                        <?php elseif (($status === 'approved' || $status === 'paid') && !empty($tx)): ?>
                                            <a href="https://amoy.polygonscan.com/tx/<?php echo urlencode($tx); ?>"
                                                target="_blank"
                                                class="btn-action btn-blockchain">
                                                <i class="fas fa-link me-1"></i>View Blockchain
                                            </a>

                                        <?php elseif ($status === 'rejected'): ?>

                                            <span class="text-danger fw-bold">
                                                <i class="fas fa-times-circle me-1"></i>Rejected
                                            </span>

                                        <?php else: ?>

                                            <span class="text-muted">No action</span>

                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include './template/footer.php'; ?>

<script>
    // Enhanced password strength checker
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const strengthBar = document.getElementById('passwordStrength');

        // Check requirements
        const hasLength = password.length >= 8;
        const hasUpper = /[A-Z]/.test(password);
        const hasLower = /[a-z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasSpecial = /[\W_]/.test(password);

        // Update requirement indicators
        document.getElementById('reqLength').className = hasLength ? 'requirement met' : 'requirement unmet';
        document.getElementById('reqUpper').className = hasUpper ? 'requirement met' : 'requirement unmet';
        document.getElementById('reqLower').className = hasLower ? 'requirement met' : 'requirement unmet';
        document.getElementById('reqNumber').className = hasNumber ? 'requirement met' : 'requirement unmet';
        document.getElementById('reqSpecial').className = hasSpecial ? 'requirement met' : 'requirement unmet';

        // Calculate strength
        let strength = 0;
        if (hasLength) strength++;
        if (hasUpper) strength++;
        if (hasLower) strength++;
        if (hasNumber) strength++;
        if (hasSpecial) strength++;

        // Update strength bar
        strengthBar.className = 'password-strength ';
        if (strength <= 1) strengthBar.classList.add('strength-weak');
        else if (strength <= 2) strengthBar.classList.add('strength-fair');
        else if (strength <= 3) strengthBar.classList.add('strength-good');
        else strengthBar.classList.add('strength-strong');
    });

    // Confirm password validation
    document.getElementById('confirm_password').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirmPassword = this.value;
        const help = document.getElementById('confirmPasswordHelp');

        if (confirmPassword && password !== confirmPassword) {
            help.textContent = "Passwords do not match";
        } else {
            help.textContent = "";
        }
    });

    // Form submission validation
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

        if (password.length > 0 && !regex.test(password)) {
            e.preventDefault();
            alert('Please ensure your password meets all the requirements.');
            return;
        }

        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match.');
            return;
        }

        // Show loading state
        const button = document.getElementById('saveButton');
        const buttonText = document.getElementById('buttonText');
        button.classList.add('btn-loading');
        buttonText.textContent = 'Saving Changes...';
        button.disabled = true;
    });

    // Profile image upload
    document.getElementById('profile_img').addEventListener('change', function() {
        var formData = new FormData(document.getElementById('profileImgForm'));
        const button = document.querySelector('.change-photo-btn');
        const originalIcon = button.innerHTML;

        // Show loading state
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;

        fetch('http://localhost:5000/user/user_upload_profile_image', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(text);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.filename) {
                    document.getElementById('profileImgPreview').src = '../../static/profile_img/' + data.filename + '?t=' + new Date().getTime();
                    // Show success message
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show';
                    alert.innerHTML = `
                        <i class="fas fa-check-circle me-2"></i>
                        Profile picture updated successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.querySelector('.profile-info').insertBefore(alert, document.querySelector('.profile-info').firstChild);
                } else {
                    throw new Error(data.message || 'Unknown error');
                }
            })
            .catch((err) => {
                alert('Failed to upload image: ' + err.message);
            })
            .finally(() => {
                // Restore button state
                button.innerHTML = originalIcon;
                button.disabled = false;
            });
    });
</script>