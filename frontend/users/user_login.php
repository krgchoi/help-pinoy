<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                $api_url = 'http://localhost:5000/user/user_login';
                $data = json_encode(['email' => $email, 'password' => $password]);

                $ch = curl_init($api_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);

                $response = curl_exec($ch);
                curl_close($ch);

                $result = json_decode($response, true);


                if (isset($result['status']) && $result['status'] === 'success') {
                    $_SESSION['username'] = $result['name'] ?? '';
                    $_SESSION['email'] = $result['email'] ?? $email;
                    $_SESSION['user_id'] = $result['user_id'] ?? null;
                    header('Location: index.php');
                    exit();
                } else {
                    $error = $result['message'] ?? 'Unexpected server response.';
                }
            } else {
                $error = "reCAPTCHA failed. Please try again.";
            }
        } else {
            $error = "Please complete the reCAPTCHA.";
        }
    } else {
        $error = "Invalid input.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login | Help Pinoy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        :root {
            --primary-blue: #003366;
            --secondary-blue: #0057b7;
            --accent-yellow: #FFCC00;
            --light-blue: #e6f2ff;
        }

        body {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }

        .login-wrapper {
            max-width: 1000px;
            width: 100%;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-left {
            padding: 50px;
            background: #fff;
        }

        .brand-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .brand-link {
            text-decoration: none;
            font-weight: 800;
            color: var(--primary-blue);
            font-size: 2.2rem;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .brand-link:hover {
            color: var(--secondary-blue);
            transform: scale(1.02);
        }

        .brand-tagline {
            color: #6c757d;
            font-size: 1.1rem;
            margin-top: 8px;
        }

        .form-header {
            margin-bottom: 30px;
        }

        .form-header h2 {
            font-weight: 700;
            color: var(--primary-blue);
            margin-bottom: 8px;
        }

        .form-header p {
            color: #6c757d;
            margin: 0;
        }

        .form-floating {
            margin-bottom: 20px;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 16px 20px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--secondary-blue);
            box-shadow: 0 0 0 0.2rem rgba(0, 87, 183, 0.25);
        }

        .form-label {
            font-weight: 600;
            color: #495057;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            z-index: 3;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--accent-yellow) 0%, #ffd700 100%);
            border: none;
            color: var(--primary-blue);
            padding: 15px 30px;
            font-weight: 700;
            font-size: 1.1rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 204, 0, 0.4);
            background: linear-gradient(135deg, #ffd700 0%, var(--accent-yellow) 100%);
        }

        .g-recaptcha {
            margin: 25px 0;
            display: flex;
            justify-content: center;
        }

        .login-right {
            background: linear-gradient(135deg, rgba(0, 51, 102, 0.9) 0%, rgba(0, 87, 183, 0.9) 100%),
                url('../assets/img/login-bg.jpg') no-repeat center center;
            background-size: cover;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 50px;
        }

        .login-right-content {
            text-align: center;
            color: white;
            z-index: 2;
            position: relative;
        }

        .login-right-content h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .login-right-content p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .features-list {
            text-align: left;
            margin-top: 40px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }

        .feature-icon {
            background: var(--accent-yellow);
            color: var(--primary-blue);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 25px;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border-left: 4px solid #dc3545;
        }

        .register-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #e9ecef;
        }

        .register-link a {
            color: var(--secondary-blue);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .register-link a:hover {
            color: var(--primary-blue);
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-wrapper {
                border-radius: 15px;
            }

            .login-left {
                padding: 30px 25px;
            }

            .login-right {
                display: none;
            }

            .brand-link {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 576px) {
            body {
                padding: 10px;
            }

            .login-left {
                padding: 25px 20px;
            }

            .form-header h2 {
                font-size: 1.5rem;
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
            border: 2px solid #ffffff;
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
</head>

<body>
    <div class="login-wrapper d-flex flex-column flex-md-row">
        <!-- Login Form Section -->
        <div class="col-md-6 login-left">
            <div class="brand-section">
                <a href="index.php" class="brand-link">
                    <i class="fas fa-hands-helping me-2"></i>Help Pinoy
                </a>
                <p class="brand-tagline">Together we make a difference</p>
            </div>

            <div class="form-header">
                <h2>Welcome Back</h2>
                <p>Sign in to your account to continue</p>
            </div>

            <?php if (isset($error)) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="loginForm">
                <div class="form-floating">
                    <input type="email" class="form-control" id="email" name="email"
                        placeholder="name@example.com" required>
                    <label for="email"><i class="fas fa-envelope me-2"></i>Email Address</label>
                </div>

                <div class="form-floating position-relative">
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Password" required>
                    <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                    <button type="button" class="password-toggle" id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>

                <div class="recaptcha-container">
                    <div class="g-recaptcha" data-sitekey="6LcWT_gqAAAAAF4T9KnGlPJ0aolNshRpCBfAlfXS" data-callback="enableLoginBtn"></div>
                </div>

                <button type="submit" class="btn btn-login" id="loginButton">
                    <span id="buttonText">Login to Account</span>
                </button>
            </form>

            <div class="register-link">
                <p>Don't have an account?
                    <a href="register.php">Create an account here</a>
                </p>
            </div>
        </div>

        <!-- Right Banner Section -->
        <div class="col-md-6 login-right">
            <div class="login-right-content">
                <h3>Make an Impact</h3>
                <p>Join thousands of Filipinos making a difference in their communities</p>

                <div class="features-list">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Support Local Communities</h6>
                            <small>Help those in need across the Philippines</small>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Secure & Trusted</h6>
                            <small>Your donations are safe and transparent</small>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Track Your Impact</h6>
                            <small>See how your contributions make a difference</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password toggle functionality
        document.getElementById('togglePassword').addEventListener('click', function() {
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

        // Form submission loading state
        document.getElementById('loginForm').addEventListener('submit', function() {
            const button = document.getElementById('loginButton');
            const buttonText = document.getElementById('buttonText');

            button.classList.add('btn-loading');
            buttonText.textContent = 'Signing in...';
            button.disabled = true;
        });

        // Add focus effects
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });

            input.addEventListener('blur', function() {
                if (this.value === '') {
                    this.parentElement.classList.remove('focused');
                }
            });
        });
    </script>
</body>

</html>