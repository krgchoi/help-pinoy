<?php
session_start();

if (isset($_SESSION['otp_expiry'])) {
    $otp_expiry = $_SESSION['otp_expiry'];
} else {
    $otp_expiry = null;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['otp'])) {

    if (!isset($_SESSION['admin_id'])) {
        die("Session expired. Please login again.");
    }

    $otp = filter_input(INPUT_POST, 'otp', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $admin_id = $_SESSION['admin_id'];

    $api_url = 'http://localhost:5000/admin/verify_otp';

    $data = json_encode([
        'admin_id' => $admin_id,
        'otp' => $otp
    ]);

    $ch = curl_init($api_url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json']
    ]);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);

    if (!$result) {
        $message = "Server error. Invalid response.";
    } else if (isset($result['status']) && $result['status'] == 'success') {

        $_SESSION['access_token'] = $result['access_token'];
        $_SESSION['refresh_token'] = $result['refresh_token'];
        unset($_SESSION['otp_expiry']);

        header('Location: index.php');
        exit();
    } else {
        $message = $result['message'] ?? 'OTP verification failed';

        if ($message === 'OTP expired') {
            unset($_SESSION['otp_expiry']);
            $otp_expiry = null;
        }
    }
}
// Handle AJAX resend OTP
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['resend_otp'])) {
    $admin_id = $_SESSION['admin_id'];
    $api_url = 'http://localhost:5000/admin/resend_otp';
    $data = json_encode(['admin_id' => $admin_id]);

    $ch = curl_init($api_url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json']
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    if ($result['status'] == 'otp_resent') {
        $_SESSION['otp_expiry'] = $result['otp_expiry'];
        echo json_encode(['success' => true, 'otp_expiry' => $result['otp_expiry']]);
    } else {
        echo json_encode(['success' => false, 'message' => $result['message']]);
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification | Help Pinoy Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --accent-color: #4cc9f0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #4bb543;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --border-radius: 12px;
            --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .otp-container {
            max-width: 500px;
            width: 100%;
        }

        .otp-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            transition: var(--transition);
        }

        .otp-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
        }

        .otp-header {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .otp-header h2 {
            font-weight: 700;
            margin-bottom: 5px;
            font-size: 1.8rem;
        }

        .otp-header p {
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .otp-body {
            padding: 30px;
        }

        .brand-logo {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: white;
        }

        .otp-input-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
        }

        .otp-input {
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 600;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            transition: var(--transition);
        }

        .otp-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
            outline: none;
        }

        .otp-input.filled {
            border-color: var(--success-color);
        }

        .verify-btn {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            padding: 14px;
            width: 100%;
            font-size: 1rem;
            transition: var(--transition);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .verify-btn:hover {
            background: linear-gradient(to right, var(--secondary-color), var(--primary-color));
            transform: translateY(-2px);
        }

        .verify-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
        }

        .verify-btn i {
            margin-right: 8px;
        }

        .resend-btn {
            background: transparent;
            border: 2px solid var(--primary-color);
            border-radius: 8px;
            color: var(--primary-color);
            font-weight: 600;
            padding: 12px;
            width: 100%;
            font-size: 1rem;
            transition: var(--transition);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .resend-btn:hover:not(:disabled) {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }

        .resend-btn:disabled {
            border-color: #6c757d;
            color: #6c757d;
            cursor: not-allowed;
            transform: none;
        }

        .resend-btn i {
            margin-right: 8px;
        }

        .timer-container {
            text-align: center;
            margin-bottom: 20px;
        }

        #timer {
            font-weight: 600;
            color: var(--dark-color);
            font-size: 1rem;
        }

        .timer-expired {
            color: var(--danger-color);
        }

        .timer-warning {
            color: var(--warning-color);
        }

        .alert {
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: none;
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
        }

        .alert-danger i {
            margin-right: 8px;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
        }

        .back-link a:hover {
            color: var(--secondary-color);
        }

        .back-link a i {
            margin-right: 5px;
        }

        .otp-instructions {
            text-align: center;
            margin-bottom: 25px;
            color: #6c757d;
        }

        @media (max-width: 576px) {
            .otp-body {
                padding: 20px;
            }

            .otp-header {
                padding: 20px 15px;
            }

            .otp-header h2 {
                font-size: 1.5rem;
            }

            .otp-input {
                width: 45px;
                height: 55px;
                font-size: 1.3rem;
            }
        }
    </style>
</head>

<body>
    <div class="otp-container">
        <div class="otp-card">
            <div class="otp-header">
                <div class="brand-logo">
                    <i class="fas fa-hands-helping"></i>
                </div>
                <h2>Help Pinoy</h2>
                <p>Admin OTP Verification</p>
            </div>

            <div class="otp-body">
                <?php if (!empty($message)) : ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <p class="otp-instructions">Enter the 6-digit verification code sent to your email address</p>

                <form action="" method="post" id="otpForm">
                    <div class="otp-input-container">
                        <input type="text" class="otp-input" maxlength="1" data-index="1" autocomplete="off">
                        <input type="text" class="otp-input" maxlength="1" data-index="2" autocomplete="off">
                        <input type="text" class="otp-input" maxlength="1" data-index="3" autocomplete="off">
                        <input type="text" class="otp-input" maxlength="1" data-index="4" autocomplete="off">
                        <input type="text" class="otp-input" maxlength="1" data-index="5" autocomplete="off">
                        <input type="text" class="otp-input" maxlength="1" data-index="6" autocomplete="off">
                    </div>

                    <input type="hidden" id="otp" name="otp" value="">

                    <button type="submit" class="verify-btn" id="verifyBtn" disabled>
                        <i class="fas fa-shield-check"></i> Verify OTP
                    </button>
                </form>

                <div class="timer-container">
                    <div id="timer"></div>
                </div>

                <button id="resendBtn" class="resend-btn" onclick="resendOtp()" disabled>
                    <i class="fas fa-redo-alt"></i> Resend OTP
                </button>

                <div class="back-link">
                    <a href="login.php"><i class="fas fa-arrow-left"></i> Back to Admin Login</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let otpExpiresIn = null;
        let timerInterval = null;

        // Set expiry from server
        function setExpiryFromServer(expiryTimestamp) {
            if (expiryTimestamp) {
                let now = Math.floor(Date.now() / 1000);
                otpExpiresIn = expiryTimestamp - now;
                if (otpExpiresIn < 0) otpExpiresIn = 0;
            } else {
                otpExpiresIn = 0;
            }
        }

        // Initialize with server value
        setExpiryFromServer(<?php echo $otp_expiry ? intval($otp_expiry) : 'null'; ?>);

        // Start countdown timer
        function startCountdown() {
            const resendBtn = document.getElementById('resendBtn');
            const timerElem = document.getElementById('timer');

            if (timerInterval) clearInterval(timerInterval);

            if (!otpExpiresIn || otpExpiresIn <= 0) {
                resendBtn.disabled = false;
                timerElem.textContent = 'OTP expired. You can resend now.';
                timerElem.className = 'timer-expired';
                return;
            }

            resendBtn.disabled = true;
            timerInterval = setInterval(function() {
                let minutes = Math.floor(otpExpiresIn / 60);
                let seconds = otpExpiresIn % 60;

                // Add visual warning when time is running low
                if (otpExpiresIn <= 60) {
                    timerElem.className = 'timer-warning';
                } else {
                    timerElem.className = '';
                }

                timerElem.textContent = `OTP expires in ${minutes}:${seconds.toString().padStart(2, '0')}`;

                if (otpExpiresIn <= 0) {
                    clearInterval(timerInterval);
                    resendBtn.disabled = false;
                    timerElem.textContent = "OTP expired. You can resend now.";
                    timerElem.className = 'timer-expired';
                }
                otpExpiresIn--;
            }, 1000);
        }

        // Resend OTP function
        function resendOtp() {
            const resendBtn = document.getElementById('resendBtn');
            const timerElem = document.getElementById('timer');

            resendBtn.disabled = true;
            resendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Resending...';
            timerElem.textContent = 'Resending OTP...';

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    let res = JSON.parse(xhr.responseText);
                    if (res.success) {
                        setExpiryFromServer(res.otp_expiry);
                        startCountdown();
                        timerElem.textContent = 'OTP resent! Please check your email.';
                        timerElem.className = '';

                        // Reset OTP inputs
                        document.querySelectorAll('.otp-input').forEach(input => {
                            input.value = '';
                            input.classList.remove('filled');
                        });
                        document.getElementById('otp').value = '';
                        document.getElementById('verifyBtn').disabled = true;

                        setTimeout(() => {
                            startCountdown();
                        }, 2000);
                    } else {
                        timerElem.textContent = res.message || 'Failed to resend OTP.';
                        timerElem.className = 'timer-expired';
                        resendBtn.disabled = false;
                    }
                } else {
                    timerElem.textContent = 'Error resending OTP.';
                    timerElem.className = 'timer-expired';
                    resendBtn.disabled = false;
                }

                resendBtn.innerHTML = '<i class="fas fa-redo-alt"></i> Resend OTP';
            };
            xhr.send('resend_otp=1');
        }

        // OTP input handling
        document.querySelectorAll('.otp-input').forEach(input => {
            input.addEventListener('input', function() {
                const value = this.value;
                const index = parseInt(this.getAttribute('data-index'));

                // Only allow numbers
                if (!/^\d*$/.test(value)) {
                    this.value = '';
                    return;
                }

                // Move to next input if current is filled
                if (value.length === 1 && index < 6) {
                    document.querySelector(`.otp-input[data-index="${index + 1}"]`).focus();
                }

                // Update hidden field with complete OTP
                updateOTPValue();

                // Update UI for filled inputs
                if (value.length === 1) {
                    this.classList.add('filled');
                } else {
                    this.classList.remove('filled');
                }
            });

            // Handle backspace
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value === '' && parseInt(this.getAttribute('data-index')) > 1) {
                    document.querySelector(`.otp-input[data-index="${parseInt(this.getAttribute('data-index')) - 1}"]`).focus();
                }
            });
        });

        // Update the hidden OTP field with the complete code
        function updateOTPValue() {
            let otpValue = '';
            document.querySelectorAll('.otp-input').forEach(input => {
                otpValue += input.value;
            });

            document.getElementById('otp').value = otpValue;

            // Enable verify button if OTP is complete
            document.getElementById('verifyBtn').disabled = otpValue.length !== 6;
        }

        // Form submission handling
        document.getElementById('otpForm').addEventListener('submit', function(e) {
            const otpValue = document.getElementById('otp').value;

            if (otpValue.length !== 6) {
                e.preventDefault();
                return false;
            }

            // Show loading state
            const verifyBtn = document.getElementById('verifyBtn');
            verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
            verifyBtn.disabled = true;
        });

        // Initialize on page load
        window.onload = function() {
            startCountdown();

            // Auto-focus first OTP input
            document.querySelector('.otp-input[data-index="1"]').focus();
        };
    </script>
</body>

</html>