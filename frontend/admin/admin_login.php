<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if ($email && $password) {
        if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
            $recaptcha = $_POST['g-recaptcha-response'];
            $secretKey = '6LcWT_gqAAAAABhxRTWdczeUyI1SsLKSn48aACYx';

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
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body>
    <div class="container login-container d-flex justify-content-center align-items-center">
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <h2 class="text-center mb-4">Help Pinoy</h2>

                <?php if (isset($message)) : ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <form action="" method="post" novalidate autocomplete="off">
                    <div class="mb-3">
                        <label for="email" class="form-label custom-fl">User</label>
                        <input type="text" id="email" name="email" class="form-control" required autocomplete="off">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label custom-fl">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required autocomplete="off">
                    </div>
                    <div class="g-recaptcha" data-sitekey="6LcWT_gqAAAAAF4T9KnGlPJ0aolNshRpCBfAlfXS" data-callback="loginBtn"></div>

                    <button type="submit" id="recaptcha" class="btn btn-primary custom-primary py-2 mt-2">Login</button>
                </form>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>