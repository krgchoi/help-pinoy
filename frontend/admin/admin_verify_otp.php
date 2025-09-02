<?php
session_start();

if (isset($_SESSION['otp_expiry'])) {
    $otp_expiry = $_SESSION['otp_expiry'];
} else {
    $otp_expiry = null;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['otp'])) {
    $otp = filter_input(INPUT_POST, 'otp', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $admin_id = $_SESSION['admin_id'];

    $api_url = 'http://localhost:5000/admin/verify_otp';
    $data = json_encode(['admin_id' => $admin_id, 'otp' => $otp]);

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

    if ($result['status'] == 'success') {
        $_SESSION['jwt_token'] = $result['token'];
        unset($_SESSION['otp_expiry']);
        header('Location: dashboard.php');
        exit();
    } else {
        $message = $result['message'];
        if ($result['message'] === 'OTP expired') {
            $otp_expiry = null;
            unset($_SESSION['otp_expiry']);
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
    <title>Admin OTP Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }
        .card {
            border-radius: 12px;
        }
        .card-header {
            border-radius: 12px 12px 0 0;
        }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
        }
        .otp-input {
            text-align: center;
            font-size: 20px;
            letter-spacing: 5px;
        }
        #timer {
            font-weight: bold;
            color: #495057;
        }
    </style>
    <script>
        let otpExpiresIn = null;
        let timerInterval = null;

        function setExpiryFromServer(expiryTimestamp) {
            if (expiryTimestamp) {
                let now = Math.floor(Date.now() / 1000);
                otpExpiresIn = expiryTimestamp - now;
                if (otpExpiresIn < 0) otpExpiresIn = 0;
            } else {
                otpExpiresIn = 0;
            }
        }

        setExpiryFromServer(<?php echo $otp_expiry ? intval($otp_expiry) : 'null'; ?>);

        function startCountdown() {
            const resendBtn = document.getElementById('resendBtn');
            const timerElem = document.getElementById('timer');
            if (timerInterval) clearInterval(timerInterval);

            if (!otpExpiresIn || otpExpiresIn <= 0) {
                resendBtn.disabled = false;
                timerElem.textContent = '';
                return;
            }
            resendBtn.disabled = true;
            timerInterval = setInterval(function() {
                let minutes = Math.floor(otpExpiresIn / 60);
                let seconds = otpExpiresIn % 60;
                timerElem.textContent = `OTP expires in ${minutes}:${seconds.toString().padStart(2, '0')}`;
                if (otpExpiresIn <= 0) {
                    clearInterval(timerInterval);
                    resendBtn.disabled = false;
                    timerElem.textContent = "OTP expired. You can resend now.";
                }
                otpExpiresIn--;
            }, 1000);
        }

        function resendOtp() {
            const resendBtn = document.getElementById('resendBtn');
            const timerElem = document.getElementById('timer');
            resendBtn.disabled = true;
            timerElem.textContent = 'Resending...';
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
                        setTimeout(startCountdown, 2000);
                    } else {
                        timerElem.textContent = res.message || 'Failed to resend OTP.';
                        resendBtn.disabled = false;
                    }
                } else {
                    timerElem.textContent = 'Error resending OTP.';
                    resendBtn.disabled = false;
                }
            };
            xhr.send('resend_otp=1');
        }

        window.onload = function() {
            startCountdown();
        };
    </script>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">🔑 Admin OTP Verification</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($message)) : ?>
                        <div class="alert alert-danger text-center"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>

                    <p class="text-muted text-center">Enter the 6-digit OTP sent to your email</p>

                    <form action="" method="post" class="mb-3">
                        <div class="mb-3">
                            <label for="otp" class="form-label">Enter OTP</label>
                            <input type="text" id="otp" name="otp" class="form-control otp-input" maxlength="6" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-2">Verify OTP</button>
                    </form>

                    <div class="text-center">
                        <button id="resendBtn" class="btn btn-outline-secondary w-100 mb-2" onclick="resendOtp()" disabled>Resend OTP</button>
                        <div id="timer" class="text-muted small"></div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-3">
                <a href="login.php" class="text-decoration-none">← Back to Admin Login</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
