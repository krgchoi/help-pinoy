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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification | Help Pinoy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #f8f9fa, #e9ecef);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border-radius: 12px;
        }
        .card-header {
            border-radius: 12px 12px 0 0;
        }
        .btn-success {
            background-color: #198754;
            border: none;
        }
        .btn-success:hover {
            background-color: #157347;
        }
        .otp-input {
            text-align: center;
            font-size: 18px;
            letter-spacing: 5px;
        }
        #otp-timer {
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="mb-0">🔐 OTP Verification</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($message); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>

                    <p class="text-muted text-center">Enter the 6-digit code sent to <strong><?php echo htmlspecialchars($email); ?></strong></p>

                    <form method="POST" class="mb-3">
                        <div class="mb-3">
                            <label for="otp" class="form-label">Enter OTP</label>
                            <input type="text" name="otp" class="form-control otp-input" id="otp" maxlength="6" required>
                        </div>
                        <div class="mb-3 text-center">
                            <span id="otp-timer" class="text-primary"></span>
                        </div>
                        <button type="submit" name="verify_otp" class="btn btn-success w-100 mb-2">Verify OTP</button>
                    </form>

                    <form method="POST" class="text-center">
                        <button type="submit" name="resend_otp" id="resend-btn" class="btn btn-outline-secondary w-100" disabled>Resend OTP</button>
                    </form>
                </div>
            </div>

            <div class="text-center mt-3">
                <a href="register.php" class="text-decoration-none">← Back to Registration</a>
            </div>
        </div>
    </div>
</div>

<script>
    let otpExpiresIn = 300; // 5 minutes
    function startCountdown() {
        let timer = otpExpiresIn;
        const countdownElem = document.getElementById('otp-timer');
        const resendBtn = document.getElementById('resend-btn');
        resendBtn.disabled = true;

        const interval = setInterval(() => {
            let minutes = Math.floor(timer / 60);
            let seconds = timer % 60;
            countdownElem.textContent = `OTP expires in ${minutes}:${seconds.toString().padStart(2, '0')}`;
            if (timer <= 0) {
                clearInterval(interval);
                countdownElem.textContent = "OTP expired. You can resend now.";
                resendBtn.disabled = false;
            }
            timer--;
        }, 1000);
    }
    window.onload = startCountdown;
</script>
</body>
</html>