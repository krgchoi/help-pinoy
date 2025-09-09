<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if ($email && $password) {
        if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
            $recaptcha = $_POST['g-recaptcha-response'];
            $secretKey = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe'; // Test key
            // $secretKey = '6LcWT_gqAAAAABhxRTWdczeUyI1SsLKSn48aACYx'; // Production key

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
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-wrapper {
            max-width: 900px;
            width: 100%;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .login-left {
            padding: 40px;
        }

        .login-left h3 {
            font-weight: 700;
            margin-bottom: 20px;
        }

        .login-left .btn-primary {
            background: #0d6efd;
            border: none;
            font-weight: 600;
        }

        .login-left .btn-primary:hover {
            background: #0b5ed7;
        }

        .login-right {
            background: url('../assets/img/login-bg.jpg') no-repeat center center;
            background-size: cover;
            position: relative;
        }

        .login-right::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
        }

        .login-right-text {
            position: absolute;
            bottom: 30px;
            left: 20px;
            color: #fff;
            z-index: 2;
        }

        .brand-link {
            text-decoration: none;
            font-weight: 700;
            color: #0d6efd;
            font-size: 1.8rem;
        }

        .brand-link:hover {
            color: #0b5ed7;
        }
    </style>
</head>

<body>
    <div class="login-wrapper d-flex">
        <div class="col-md-6 login-left">
            <div class="mb-4 text-center">
                <a href="index.php" class="brand-link">Help Pinoy</a>
                <p class="text-muted">Login to continue</p>
            </div>

            <?php if (isset($error)) : ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                </div>

                <!-- <div class="g-recaptcha mb-3" data-sitekey="6LcWT_gqAAAAAF4T9KnGlPJ0aolNshRpCBfAlfXS"></div> -->
                <div class="g-recaptcha mb-3" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></div> 

                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <p class="text-center mt-3">
                Don't have an account? <a href="register.php" class="text-decoration-none">Register here</a>
            </p>
        </div>

        
        <div class="col-md-6 login-right">
            <div class="login-right-text">
                <h4>Welcome Back.</h4>
                <p>Join us and make a difference today.</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
