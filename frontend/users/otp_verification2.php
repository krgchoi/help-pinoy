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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #003366;
            --secondary-blue: #0057b7;
            --accent-yellow: #FFCC00;
            --light-blue: #e6f2ff;
            --success-green: #28a745;
            --dark-gray: #2c3e50;
            --light-gray: #f8f9fa;
        }

        body {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .otp-container {
            max-width: 480px;
            width: 100%;
        }

        .otp-card {
            background: white;
            border-radius: 25px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            border: none;
            transition: transform 0.3s ease;
        }

        .otp-card:hover {
            transform: translateY(-5px);
        }

        .otp-header {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: white;
            padding: 40px 30px 30px;
            text-align: center;
            position: relative;
        }

        .otp-header::before {
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

        .otp-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .otp-header h2 {
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 1.8rem;
        }

        .otp-header p {
            opacity: 0.9;
            margin: 0;
        }

        .otp-body {
            padding: 40px 30px;
        }

        .email-display {
            background: var(--light-blue);
            border: 2px solid var(--secondary-blue);
            border-radius: 15px;
            padding: 15px 20px;
            text-align: center;
            margin-bottom: 25px;
        }

        .email-display strong {
            color: var(--primary-blue);
            font-weight: 700;
        }

        .otp-input-group {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-bottom: 30px;
        }

        .otp-digit {
            width: 55px;
            height: 65px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-gray);
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .otp-digit:focus {
            border-color: var(--secondary-blue);
            background: white;
            box-shadow: 0 0 0 0.2rem rgba(0, 87, 183, 0.25);
            transform: translateY(-2px);
        }

        .otp-digit.filled {
            border-color: var(--success-green);
            background: rgba(40, 167, 69, 0.1);
        }

        .timer-container {
            text-align: center;
            margin-bottom: 25px;
            padding: 15px;
            background: var(--light-gray);
            border-radius: 12px;
            border: 2px solid #e9ecef;
        }

        #otp-timer {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-blue);
        }

        .timer-expired {
            color: #dc3545 !important;
        }

        .btn-verify {
            background: linear-gradient(135deg, var(--accent-yellow) 0%, #ffd700 100%);
            border: none;
            color: var(--primary-blue);
            padding: 15px 30px;
            font-weight: 700;
            font-size: 1.1rem;
            border-radius: 15px;
            transition: all 0.3s ease;
            width: 100%;
            box-shadow: 0 8px 25px rgba(255, 204, 0, 0.3);
            margin-bottom: 15px;
        }

        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(255, 204, 0, 0.4);
            background: linear-gradient(135deg, #ffd700 0%, var(--accent-yellow) 100%);
        }

        .btn-resend {
            background: transparent;
            border: 2px solid var(--secondary-blue);
            color: var(--secondary-blue);
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-resend:hover:not(:disabled) {
            background: var(--secondary-blue);
            color: white;
            transform: translateY(-2px);
        }

        .btn-resend:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .back-link {
            text-align: center;
            margin-top: 25px;
        }

        .back-link a {
            color: var(--secondary-blue);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-link a:hover {
            color: var(--primary-blue);
            gap: 12px;
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 25px;
            border-left: 4px solid transparent;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border-left-color: #dc3545;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success-green);
            border-left-color: var(--success-green);
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
            to { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 576px) {
            .otp-container {
                max-width: 100%;
            }

            .otp-body {
                padding: 30px 20px;
            }

            .otp-header {
                padding: 30px 20px 20px;
            }

            .otp-digit {
                width: 45px;
                height: 55px;
                font-size: 1.3rem;
            }

            .otp-input-group {
                gap: 8px;
            }
        }

        /* OTP input animation */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .otp-digit.pulse {
            animation: pulse 0.3s ease-in-out;
        }
    </style>
</head>
<body>
    <div class="otp-container">
        <div class="otp-card">
            <!-- Header -->
            <div class="otp-header">
                <div class="otp-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h2>Secure Verification</h2>
                <p>Enter the code we sent to your email</p>
            </div>

            <!-- Body -->
            <div class="otp-body">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <div class="email-display">
                    <i class="fas fa-envelope me-2"></i>
                    Code sent to: <strong><?php echo htmlspecialchars($email); ?></strong>
                </div>

                <form method="POST" id="otpForm">
                    <div class="otp-input-group">
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                            <input type="text" 
                                   class="otp-digit" 
                                   maxlength="1" 
                                   data-index="<?php echo $i; ?>"
                                   oninput="moveToNext(this)"
                                   onkeydown="handleBackspace(this, event)"
                                   autocomplete="off">
                        <?php endfor; ?>
                        <input type="hidden" name="otp" id="otpInput">
                    </div>

                    <div class="timer-container">
                        <i class="fas fa-clock me-2"></i>
                        <span id="otp-timer">OTP expires in 05:00</span>
                    </div>

                    <button type="submit" name="verify_otp" class="btn-verify" id="verifyButton" disabled>
                        <span id="buttonText">Verify OTP</span>
                    </button>
                </form>

                <form method="POST">
                    <button type="submit" name="resend_otp" class="btn-resend" id="resend-btn" disabled>
                        <i class="fas fa-redo me-2"></i>
                        <span id="resendText">Resend OTP</span>
                    </button>
                </form>

                <div class="back-link">
                    <a href="register.php">
                        <i class="fas fa-arrow-left"></i>
                        Back to Registration
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        let otpExpiresIn = 300; // 5 minutes
        let timerInterval;

        function startCountdown() {
            let timer = otpExpiresIn;
            const countdownElem = document.getElementById('otp-timer');
            const resendBtn = document.getElementById('resend-btn');
            resendBtn.disabled = true;

            timerInterval = setInterval(() => {
                let minutes = Math.floor(timer / 60);
                let seconds = timer % 60;
                countdownElem.textContent = `OTP expires in ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                if (timer <= 30) {
                    countdownElem.classList.add('timer-expired');
                }
                
                if (timer <= 0) {
                    clearInterval(timerInterval);
                    countdownElem.textContent = "OTP expired";
                    countdownElem.classList.add('timer-expired');
                    resendBtn.disabled = false;
                    document.getElementById('resendText').textContent = 'Resend OTP';
                }
                timer--;
            }, 1000);
        }

        function moveToNext(input) {
            const value = input.value;
            const index = parseInt(input.dataset.index);
            
            if (value.length === 1) {
                input.classList.add('filled', 'pulse');
                setTimeout(() => input.classList.remove('pulse'), 300);
                
                if (index < 6) {
                    const nextInput = document.querySelector(`.otp-digit[data-index="${index + 1}"]`);
                    nextInput.focus();
                }
            }
            
            updateOTPValue();
            checkOTPComplete();
        }

        function handleBackspace(input, event) {
            const index = parseInt(input.dataset.index);
            
            if (event.key === 'Backspace' && input.value === '' && index > 1) {
                const prevInput = document.querySelector(`.otp-digit[data-index="${index - 1}"]`);
                prevInput.focus();
                prevInput.value = '';
                prevInput.classList.remove('filled');
            }
            
            updateOTPValue();
            checkOTPComplete();
        }

        function updateOTPValue() {
            const otpDigits = document.querySelectorAll('.otp-digit');
            let otpValue = '';
            otpDigits.forEach(digit => {
                otpValue += digit.value;
            });
            document.getElementById('otpInput').value = otpValue;
        }

        function checkOTPComplete() {
            const otpValue = document.getElementById('otpInput').value;
            const verifyButton = document.getElementById('verifyButton');
            
            if (otpValue.length === 6) {
                verifyButton.disabled = false;
            } else {
                verifyButton.disabled = true;
            }
        }

        // Form submission handling
        document.getElementById('otpForm').addEventListener('submit', function(e) {
            const button = document.getElementById('verifyButton');
            const buttonText = document.getElementById('buttonText');
            
            // Show loading state
            button.classList.add('btn-loading');
            buttonText.textContent = 'Verifying...';
            button.disabled = true;
        });

        // Resend button handling
        document.getElementById('resend-btn').addEventListener('click', function() {
            const button = this;
            const buttonText = document.getElementById('resendText');
            
            // Show loading state
            button.disabled = true;
            buttonText.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
            
            // Reset timer
            clearInterval(timerInterval);
            startCountdown();
            
            // The form will submit normally for resend
        });

        // Auto-focus first input on load
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.otp-digit[data-index="1"]').focus();
            startCountdown();
        });

        // Paste OTP functionality
        document.addEventListener('paste', function(e) {
            const pasteData = e.clipboardData.getData('text');
            if (pasteData.length === 6 && /^\d+$/.test(pasteData)) {
                const digits = document.querySelectorAll('.otp-digit');
                digits.forEach((digit, index) => {
                    digit.value = pasteData[index] || '';
                    digit.classList.add('filled', 'pulse');
                });
                updateOTPValue();
                checkOTPComplete();
                e.preventDefault();
            }
        });
    </script>
</body>
</html>