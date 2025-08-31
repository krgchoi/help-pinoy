<?php
$email = filter_input(INPUT_GET, 'email', FILTER_SANITIZE_EMAIL);
$message = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_otp'])) {
    $otp = filter_input(INPUT_POST, 'otp', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $data = json_encode([
        'email' => $email,
        'otp' => $otp
    ]);

    $ch = curl_init('http://localhost:5000/user/verify_otp');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);

    if ($response === false) {
        $message = 'Failed to connect to the server. Please try again later.';
    } else {
        $result = json_decode($response, true);
        if ($result['status'] === 'success') {
            $success = 'OTP verified successfully! You can now log in.';
            echo "<script>alert('OTP Verified! Redirecting to login...'); window.location.href = 'user_login.php';</script>";
            exit();
        } else {
            $message = $result['message'] ?? 'OTP verification failed.';
        }
    }
    curl_close($ch);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['resend_otp'])) {
    $data = json_encode([
        'email' => $email
    ]);

    $ch = curl_init('http://localhost:5000/user/resend_otp');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);

    if ($response === false) {
        $message = 'Failed to connect to the server. Please try again later.';
    } else {
        $result = json_decode($response, true);

        if ($result['status'] === 'success') {
            $success = 'OTP has been resent to your email.';
        } else {
            $message = $result['message'] ?? 'Failed to resend OTP.';
        }
    }
    curl_close($ch);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>OTP Verification | Help Pinoy</title>
    <script>
        // Set OTP expiry time (5 minutes from page load or from server if available)
        let otpExpiresIn = 300; // seconds (5 minutes)
        // Optionally, you can pass expiry from PHP if available
        // let otpExpiresIn = <?php //echo $otp_expiry_from_server ?? 300; 
                                ?>;

        function startCountdown() {
            let timer = otpExpiresIn;
            const countdownElem = document.getElementById('otp-timer');
            const resendBtn = document.getElementById('resend-btn');
            resendBtn.disabled = true;

            const interval = setInterval(() => {
                let minutes = Math.floor(timer / 60);
                let seconds = timer % 60;
                countdownElem.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                if (timer <= 0) {
                    clearInterval(interval);
                    countdownElem.textContent = "OTP expired. You can resend OTP.";
                    resendBtn.disabled = false;
                }
                timer--;
            }, 1000);
        }

        window.onload = function() {
            startCountdown();
        };
    </script>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">OTP Verification</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($message); ?></div>
                        <?php endif; ?>

                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="otp" class="form-label">Enter OTP</label>
                                <input type="text" name="otp" class="form-control" id="otp" maxlength="6" required>
                            </div>
                            <div class="mb-3">
                                <span id="otp-timer" class="text-muted"></span>
                            </div>
                            <button type="submit" name="verify_otp" class="btn btn-success w-100 mb-2">Verify OTP</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <button type="submit" name="resend_otp" id="resend-btn" class="btn btn-link w-100" disabled>Resend OTP</button>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <a href="register.php">Go back to Registration</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>