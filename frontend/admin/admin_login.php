<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if ($email && $password) {
        if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
            $recaptcha = $_POST['g-recaptcha-response'];
            $secretKey = '6LcWT_gqAAAAABhxRTWdczeUyI1SsLKSn48aACYx'; // Production key
            // $secretKey = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe'; // Test key


            $verify = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secretKey . '&response=' . $recaptcha);
            $captchaResponse = json_decode($verify);

            if ($captchaResponse->success) {
                $api_url = 'http://localhost:5000/admin/login';
                $data = json_encode(['email' => $email, 'password' => $password]);

                $ch = curl_init($api_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

                $response = curl_exec($ch);
                curl_close($ch);

                $result = json_decode($response, true);

                if (isset($result['status']) && $result['status'] == 'otp_sent') {
                    $_SESSION['admin_id'] = $result['admin_id'];
                    if (isset($result['otp_expiry'])) {
                        $_SESSION['otp_expiry'] = $result['otp_expiry'];
                    }
                    header('Location: admin_verify_otp.php');
                    exit();
                } else {
                    $message = $result['message'] ?? 'Cannot Connect to Server.';
                }
            } else {
                $message = "reCAPTCHA failed. Please try again.";
            }
        } else {
            $message = "Please complete the reCAPTCHA.";
        }
    } else {
        $message = "Invalid input.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | Help Pinoy</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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

        .login-container {
            max-width: 450px;
            width: 100%;
        }

        .login-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            transition: var(--transition);
        }

        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
        }

        .login-header {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .login-header h2 {
            font-weight: 700;
            margin-bottom: 5px;
            font-size: 1.8rem;
        }

        .login-header p {
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .login-body {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--dark-color);
            display: flex;
            align-items: center;
        }

        .form-label i {
            margin-right: 8px;
            color: var(--primary-color);
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 40px;
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            transition: var(--transition);
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        .recaptcha-container {
            margin: 25px 0;
            display: flex;
            justify-content: center;
        }

        .login-btn {
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
        }

        .login-btn:hover {
            background: linear-gradient(to right, var(--secondary-color), var(--primary-color));
            transform: translateY(-2px);
        }

        .login-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
        }

        .login-btn i {
            margin-right: 8px;
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

        .footer-text {
            text-align: center;
            margin-top: 20px;
            color: #6c757d;
            font-size: 0.85rem;
        }

        .brand-logo {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: white;
        }

        @media (max-width: 576px) {
            .login-body {
                padding: 20px;
            }
            
            .login-header {
                padding: 20px 15px;
            }
            
            .login-header h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="brand-logo">
                    <i class="fas fa-hands-helping"></i>
                </div>
                <h2>Help Pinoy</h2>
                <p>Admin Portal Access</p>
            </div>
            
            <div class="login-body">
                <?php if (isset($message)) : ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <form action="" method="post" novalidate autocomplete="off" id="loginForm">
                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-user"></i> Username
                        </label>
                        <input type="text" id="email" name="email" class="form-control" required autocomplete="off" placeholder="Enter your username">
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <input type="password" id="password" name="password" class="form-control" required autocomplete="off" placeholder="Enter your password">
                        <button type="button" class="password-toggle" id="passwordToggle">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>

                    <div class="recaptcha-container">
                        <div class="g-recaptcha" data-sitekey="6LcWT_gqAAAAAF4T9KnGlPJ0aolNshRpCBfAlfXS" data-callback="enableLoginBtn"></div>
                    </div>

                    <button type="submit" id="loginBtn" class="login-btn" disabled>
                        <i class="fas fa-sign-in-alt"></i> Login to Dashboard
                    </button>
                </form>
                
                <div class="footer-text">
                    <p>Secure access to Help Pinoy administration panel</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password visibility toggle
        document.getElementById('passwordToggle').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Enable login button after reCAPTCHA verification
        function enableLoginBtn() {
            document.getElementById('loginBtn').disabled = false;
        }
        
        // Form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            if (!email || !password) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }
            
            // Show loading state
            const loginBtn = document.getElementById('loginBtn');
            loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Authenticating...';
            loginBtn.disabled = true;
        });
    </script>
</body>

</html>