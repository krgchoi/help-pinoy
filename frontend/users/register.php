<?php
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $contact_number = filter_input(INPUT_POST, 'contact_number', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $confirm_password = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        $data = json_encode([
            'name' => $name,
            'email' => $email,
            'phone_number' => $contact_number,
            'password' => $password,
            'gender' => $gender
        ]);

        $ch = curl_init('http://localhost:5000/user/register');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);

        if ($response === false) {
            $error = 'Failed to connect to the server. Please try again later.';
        } else {
            $result = json_decode($response, true);
            if ($result && $result['status'] === 'success') {
                $success = 'Registration successful. OTP sent to your email.';
                echo "<script>alert('Registration successful! Verify OTP.'); window.location.href='/help_pinoy/frontend/users/otp_verification2.php?email=" . urlencode($email) . "';</script>";
                exit();
            } else {
                $error = $result['message'] ?? 'Registration failed.';
            }
        }
        curl_close($ch);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Register | Help Pinoy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .register-wrapper {
            max-width: 900px;
            width: 100%;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
        }

        .register-left {
            background: url('https://images.pexels.com/photos/3184418/pexels-photo-3184418.jpeg') no-repeat center center;
            background-size: cover;
            position: relative;
            width: 50%;
        }

        .register-left::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
        }

        .register-left-text {
            position: absolute;
            bottom: 30px;
            left: 20px;
            color: #fff;
            z-index: 2;
        }

        .register-right {
            width: 50%;
            padding: 40px;
        }

        .register-right h3 {
            font-weight: 700;
            margin-bottom: 20px;
        }

        .register-right .btn-primary {
            background: #0d6efd;
            border: none;
            font-weight: 600;
        }

        .register-right .btn-primary:hover {
            background: #0b5ed7;
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
    <div class="register-wrapper">
      
        <div class="register-left">
            <div class="register-left-text">
                <h4>Join Us Today.</h4>
                <p>Be part of the change and make a difference.</p>
            </div>
        </div>

   
        <div class="register-right">
            <div class="mb-4 text-center">
                <a href="index.php" class="brand-link">Help Pinoy</a>
                <p class="text-muted">Create your account</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" id="registerForm">
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="contact_number" class="form-label">Contact Number</label>
                    <input type="text" name="contact_number" id="contact_number" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                    <div id="passwordHelp" class="form-text text-danger"></div>
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="gender" class="form-label">Gender</label>
                    <select id="gender" name="gender" class="form-control" required>
                        <option value="">Select Gender</option>
                        <option value="Male" <?php if (isset($_POST['gender']) && $_POST['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                        <option value="Female" <?php if (isset($_POST['gender']) && $_POST['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                        <option value="Other" <?php if (isset($_POST['gender']) && $_POST['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                    </select>
                </div>

                <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
            </form>

            <p class="text-center mt-3">
                Already have an account? <a href="user_login.php" class="text-decoration-none">Login</a>
            </p>
        </div>
    </div>

    <script>
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const help = document.getElementById('passwordHelp');
            const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
            if (!regex.test(password)) {
                help.textContent = "Password must be at least 8 characters, include uppercase, lowercase, number, and special character.";
            } else {
                help.textContent = "";
            }
        });

        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
            if (!regex.test(password)) {
                e.preventDefault();
                document.getElementById('passwordHelp').textContent = "Password must be at least 8 characters, include uppercase, lowercase, number, and special character.";
            }
        });
    </script>
</body>

</html>
