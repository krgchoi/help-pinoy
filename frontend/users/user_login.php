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
            --dark-blue: #00254D;
            --light-blue: #e6f2ff;
            --success-green: #28a745;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background particles */
        body::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(circle at 20% 40%, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: floatBG 20s linear infinite;
            pointer-events: none;
        }

        @keyframes floatBG {
            from {
                transform: translateY(0);
            }

            to {
                transform: translateY(-50px);
            }
        }

        .login-wrapper {
            max-width: 1200px;
            width: 100%;
            background: #fff;
            border-radius: 30px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            animation: slideUpFade 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            z-index: 1;
        }

        @keyframes slideUpFade {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Login Form Section */
        .login-left {
            padding: 60px 50px;
            background: #fff;
        }

        .brand-section {
            text-align: center;
            margin-bottom: 40px;
            animation: fadeIn 0.6s ease-out 0.2s both;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .brand-link {
            text-decoration: none;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 2.3rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: var(--transition);
        }

        .brand-link:hover {
            transform: scale(1.02);
            gap: 12px;
        }

        .brand-link i {
            background: linear-gradient(135deg, var(--accent-yellow), #ffb300);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-tagline {
            color: #6c757d;
            font-size: 0.95rem;
            margin-top: 10px;
            letter-spacing: 0.5px;
        }

        .form-header {
            margin-bottom: 35px;
            text-align: center;
        }

        .form-header h2 {
            font-weight: 800;
            color: var(--primary-blue);
            margin-bottom: 8px;
            font-size: 1.8rem;
        }

        .form-header p {
            color: #6c757d;
            margin: 0;
            font-size: 0.95rem;
        }

        .form-floating {
            margin-bottom: 25px;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 14px;
            padding: 16px 20px;
            font-size: 15px;
            transition: var(--transition);
            background: #fff;
        }

        .form-control:focus {
            border-color: var(--secondary-blue);
            box-shadow: 0 0 0 4px rgba(0, 87, 183, 0.1);
            outline: none;
        }

        .form-floating label {
            padding: 16px 20px;
            color: #6c757d;
            font-weight: 500;
        }

        .form-floating>.form-control:focus~label,
        .form-floating>.form-control:not(:placeholder-shown)~label {
            transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #adb5bd;
            cursor: pointer;
            z-index: 3;
            padding: 8px;
            transition: var(--transition);
        }

        .password-toggle:hover {
            color: var(--secondary-blue);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--accent-yellow) 0%, #ffb300 100%);
            border: none;
            color: var(--primary-blue);
            padding: 16px 30px;
            font-weight: 800;
            font-size: 1.05rem;
            border-radius: 50px;
            transition: var(--transition);
            width: 100%;
            margin-top: 15px;
            position: relative;
            overflow: hidden;
            letter-spacing: 1px;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-login:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 204, 0, 0.4);
            background: linear-gradient(135deg, #ffb300 0%, var(--accent-yellow) 100%);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .g-recaptcha {
            margin: 25px 0;
            display: flex;
            justify-content: center;
            transform: scale(0.95);
        }

        /* Right Banner Section */
        .login-right {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 40px;
            overflow: hidden;
        }

        .login-right::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 40px 40px;
            animation: slowRotate 30s linear infinite;
            pointer-events: none;
        }

        @keyframes slowRotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .login-right-content {
            text-align: center;
            color: white;
            z-index: 2;
            position: relative;
        }

        .login-right-content h3 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
            animation: slideInRight 0.6s ease-out 0.3s both;
        }

        .login-right-content p {
            font-size: 1rem;
            margin-bottom: 35px;
            opacity: 0.9;
            animation: slideInRight 0.6s ease-out 0.4s both;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .features-list {
            text-align: left;
            margin-top: 30px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            backdrop-filter: blur(10px);
            transition: var(--transition);
            animation: slideInRight 0.6s ease-out calc(0.5s + var(--delay, 0)) both;
        }

        .feature-item:nth-child(1) {
            --delay: 0s;
        }

        .feature-item:nth-child(2) {
            --delay: 0.1s;
        }

        .feature-item:nth-child(3) {
            --delay: 0.2s;
        }

        .feature-item:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(5px);
        }

        .feature-icon {
            background: var(--accent-yellow);
            color: var(--primary-blue);
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
            font-size: 1.2rem;
            transition: var(--transition);
        }

        .feature-item:hover .feature-icon {
            transform: scale(1.1) rotate(360deg);
        }

        .feature-item h6 {
            font-weight: 700;
            margin-bottom: 5px;
            font-size: 0.95rem;
        }

        .feature-item small {
            font-size: 0.8rem;
            opacity: 0.8;
        }

        .alert {
            border-radius: 14px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 25px;
            animation: shakeError 0.5s ease-out;
        }

        @keyframes shakeError {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-10px);
            }

            75% {
                transform: translateX(10px);
            }
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(220, 53, 69, 0.05));
            color: #dc3545;
            border-left: 4px solid #dc3545;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(40, 167, 69, 0.05));
            color: var(--success-green);
            border-left: 4px solid var(--success-green);
        }

        .register-link {
            text-align: center;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #e9ecef;
        }

        .register-link p {
            margin: 0;
            color: #6c757d;
        }

        .register-link a {
            color: var(--secondary-blue);
            font-weight: 700;
            text-decoration: none;
            transition: var(--transition);
            position: relative;
        }

        .register-link a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--accent-yellow);
            transition: var(--transition);
        }

        .register-link a:hover::after {
            width: 100%;
        }

        .register-link a:hover {
            color: var(--primary-blue);
        }

        /* Loading state */
        .btn-loading {
            position: relative;
            color: transparent !important;
            pointer-events: none;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            width: 22px;
            height: 22px;
            top: 50%;
            left: 50%;
            margin-left: -11px;
            margin-top: -11px;
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

        /* Responsive Design */
        @media (max-width: 992px) {
            .login-left {
                padding: 40px 35px;
            }

            .login-right {
                padding: 40px 30px;
            }
        }

        @media (max-width: 768px) {
            .login-wrapper {
                border-radius: 20px;
            }

            .login-left {
                padding: 35px 25px;
            }

            .login-right {
                display: none;
            }

            .brand-link {
                font-size: 1.8rem;
            }

            .form-header h2 {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            body {
                padding: 15px;
            }

            .login-left {
                padding: 25px 20px;
            }

            .g-recaptcha {
                transform: scale(0.85);
            }
        }

        /* Focus visible for accessibility */
        .form-control:focus-visible {
            outline: none;
        }

        /* Smooth transitions for all interactive elements */
        * {
            -webkit-tap-highlight-color: transparent;
        }
    </style>
</head>

<body>
    <div class="login-wrapper d-flex flex-column flex-md-row">
        <!-- Login Form Section -->
        <div class="col-md-6 login-left">
            <div class="brand-section">
                <a href="index.php" class="brand-link">
                    <i class="fas fa-hands-helping"></i>
                    Help Pinoy
                </a>
                <p class="brand-tagline">Together we make a difference</p>
            </div>

            <div class="form-header">
                <h2>Welcome Back! 👋</h2>
                <p>Sign in to access your account and continue helping</p>
            </div>

            <?php if (isset($error)) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['registered']) && $_GET['registered'] === 'success') : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    Registration successful! Please login to continue.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" id="loginForm">
                <div class="form-floating">
                    <input type="email" class="form-control" id="email" name="email"
                        placeholder="name@example.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    <label for="email"><i class="fas fa-envelope me-2"></i>Email Address</label>
                </div>

                <div class="form-floating position-relative">
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Password" required>
                    <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                    <button type="button" class="password-toggle" id="togglePassword" aria-label="Toggle password visibility">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>

                <div class="recaptcha-container">
                    <div class="g-recaptcha" data-sitekey="6LcWT_gqAAAAAF4T9KnGlPJ0aolNshRpCBfAlfXS"></div>
                </div>

                <button type="submit" class="btn btn-login" id="loginButton">
                    <span id="buttonText">Login to Account</span>
                </button>
            </form>

            <div class="register-link">
                <p>Don't have an account?
                    <a href="register.php">Create an account here</a>
                </p>
                <!-- <p class="mt-2">
                    <a href="forgot_password.php" style="font-size: 0.9rem;">
                        <i class="fas fa-key me-1"></i>Forgot Password?
                    </a>
                </p> -->
            </div>
        </div>

        <!-- Right Banner Section -->
        <div class="col-md-6 login-right">
            <div class="login-right-content">
                <h3>Make an Impact Today</h3>
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
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Track Your Impact</h6>
                            <small>See how your contributions make a difference</small>
                        </div>
                    </div>
                </div>

                <!-- Stats counter -->
                <!-- <div class="row mt-4 g-3" style="margin-top: 30px;">
                    <div class="col-6">
                        <div class="text-center">
                            <h4 class="text-warning mb-0">500+</h4>
                            <small>Projects Completed</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <h4 class="text-warning mb-0">10k+</h4>
                            <small>Lives Impacted</small>
                        </div>
                    </div> -->
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

            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });

        // Form submission loading state
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const button = document.getElementById('loginButton');
            const buttonText = document.getElementById('buttonText');
            const recaptchaResponse = grecaptcha.getResponse();

            if (!recaptchaResponse) {
                e.preventDefault();
                alert('Please complete the reCAPTCHA verification.');
                return;
            }

            button.classList.add('btn-loading');
            buttonText.textContent = 'Signing in...';
            button.disabled = true;
        });

        // Add focus effects and input validation
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
                this.style.borderColor = 'var(--secondary-blue)';
            });

            input.addEventListener('blur', function() {
                if (this.value === '') {
                    this.parentElement.classList.remove('focused');
                }
                this.style.borderColor = '#e9ecef';
            });
        });

        // Auto-dismiss alerts after 5 seconds
        document.querySelectorAll('.alert').forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });

        // Remember email functionality (optional)
        const emailInput = document.getElementById('email');
        const savedEmail = localStorage.getItem('rememberedEmail');
        if (savedEmail && emailInput && !emailInput.value) {
            emailInput.value = savedEmail;
        }

        // Add remember me checkbox (optional enhancement)
        const formContainer = document.querySelector('.form-floating').parentElement;
        const rememberDiv = document.createElement('div');
        rememberDiv.className = 'form-check mb-3';
        rememberDiv.innerHTML = `
            <input class="form-check-input" type="checkbox" id="rememberEmail">
            <label class="form-check-label" for="rememberEmail" style="color: #6c757d;">
                Remember my email
            </label>
        `;

        // Insert remember me checkbox after password field
        const passwordFloating = document.querySelector('.form-floating.position-relative');
        if (passwordFloating) {
            passwordFloating.insertAdjacentElement('afterend', rememberDiv);
        }

        document.getElementById('rememberEmail')?.addEventListener('change', function(e) {
            if (e.target.checked && emailInput.value) {
                localStorage.setItem('rememberedEmail', emailInput.value);
            } else if (!e.target.checked) {
                localStorage.removeItem('rememberedEmail');
            }
        });

        // Pre-check remember me if email exists
        if (savedEmail) {
            const rememberCheckbox = document.getElementById('rememberEmail');
            if (rememberCheckbox) rememberCheckbox.checked = true;
        }
    </script>
</body>

</html>