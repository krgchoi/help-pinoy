<?php
session_start();

// Get OTP expiry from session if available
if (isset($_SESSION['otp_expiry'])) {
    $otp_expiry = $_SESSION['otp_expiry'];
} else {
    $otp_expiry = null;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['otp'])) {
    $otp = filter_input(INPUT_POST, 'otp', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $admin_id = $_SESSION['admin_id'];

    $api_url = 'http://localhost:5000/admin/verify_otp';
    $data = json_encode(['admin_id' => $admin_id, 'otp' => $otp]);

    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

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
        // If OTP expired, allow resend immediately
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
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

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
    <link href="../assets/css/admin.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                timerElem.textContent = `OTP expires in  ${minutes}:${seconds.toString().padStart(2, '0')}`;
                if (otpExpiresIn <= 0) {
                    clearInterval(timerInterval);
                    resendBtn.disabled = false;
                    timerElem.textContent = "OTP expired. You can resend OTP.";
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
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0">Verify OTP</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($message)) : ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>

                        <form action="" method="post">
                            <div class="mb-3">
                                <label for="otp" class="form-label">OTP</label>
                                <input type="text" id="otp" name="otp" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Verify OTP</button>
                        </form>
                        <div class="mt-3 text-center">
                            <button id="resendBtn" class="btn btn-link" onclick="resendOtp()" disabled>Resend OTP</button>
                            <div id="timer" class="text-muted small"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>