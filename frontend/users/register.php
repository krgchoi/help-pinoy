<?php
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {

    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $contact_number = filter_input(INPUT_POST, 'contact_number', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
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

        // Debug: Log the data being sent
        error_log("Sending data to API: " . $data);

        $ch = curl_init('http://localhost:5000/user/register');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_VERBOSE, true); // Add verbose output

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        
        curl_close($ch);
        
        // Debug: Log the response
        error_log("HTTP Code: " . $http_code);
        error_log("CURL Error: " . $curl_error);
        error_log("API Response: " . $response);

        if ($response === false) {
            $error = 'Server unavailable. Please try again later. CURL Error: ' . $curl_error;
        } else {
            $result = json_decode($response, true);
            
            // Debug: Log the decoded result
            error_log("Decoded Result: " . print_r($result, true));

            if ($result && isset($result['status']) && $result['status'] === 'success') {
                // Debug
                error_log("SUCCESS - Redirecting to OTP page");
                
                echo "<script>
                        alert('Registration successful! Check your email for OTP.');
                        window.location.href = '/help_pinoy/frontend/users/otp_verification2.php?email=" . urlencode($email) . "';
                      </script>";
                exit();

            } else {
                // Check what exactly is in the response
                $error = isset($result['message']) ? $result['message'] : 'Registration failed. No message from server.';
                if (isset($result['status'])) {
                    $error .= ' Status: ' . $result['status'];
                }
                if ($http_code !== 200) {
                    $error .= ' HTTP Code: ' . $http_code;
                }
            }
        }
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        .register-wrapper {
            max-width: 1100px;
            width: 100%;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .register-left {
            background: linear-gradient(135deg, rgba(0, 51, 102, 0.9) 0%, rgba(0, 87, 183, 0.9) 100%), 
                        url('https://images.pexels.com/photos/3184418/pexels-photo-3184418.jpeg') no-repeat center center;
            background-size: cover;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 50px;
        }

        .register-left-content {
            text-align: center;
            color: white;
            z-index: 2;
            position: relative;
        }

        .register-left-content h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .register-left-content p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .benefits-list {
            text-align: left;
            margin-top: 40px;
        }

        .benefit-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }

        .benefit-icon {
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

        .register-right {
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

        .password-strength {
            height: 4px;
            border-radius: 2px;
            margin-top: 5px;
            transition: all 0.3s ease;
        }

        .strength-weak { background: #dc3545; width: 25%; }
        .strength-fair { background: #fd7e14; width: 50%; }
        .strength-good { background: #ffc107; width: 75%; }
        .strength-strong { background: #198754; width: 100%; }

        .btn-register {
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

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 204, 0, 0.4);
            background: linear-gradient(135deg, #ffd700 0%, var(--accent-yellow) 100%);
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

        .alert-success {
            background: rgba(25, 135, 84, 0.1);
            color: #198754;
            border-left: 4px solid #198754;
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #e9ecef;
        }

        .login-link a {
            color: var(--secondary-blue);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .login-link a:hover {
            color: var(--primary-blue);
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 991.98px) {
            .register-wrapper {
                flex-direction: column;
            }
            
            .register-left {
                display: none;
            }
            
            .register-right {
                width: 100%;
                padding: 30px 25px;
            }
            
            .brand-link {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 576px) {
            body {
                padding: 10px;
            }
            
            .register-right {
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
            border: 2px solid var(--primary-blue);
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Password requirements list */
        .password-requirements {
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 5px;
        }

        .requirement {
            display: flex;
            align-items: center;
            margin-bottom: 2px;
        }

        .requirement.met {
            color: #198754;
        }

        .requirement.unmet {
            color: #6c757d;
        }

        .requirement i {
            margin-right: 5px;
            font-size: 0.75rem;
        }
    </style>
</head>

<body>
    <div class="register-wrapper d-flex">
        <div class="col-md-6 register-left">
            <div class="register-left-content">
                <h3>Join Our Community</h3>
                <p>Be part of the change and make a difference in the Philippines</p>
                
                <div class="benefits-list">
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-hand-holding-heart"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Make Real Impact</h6>
                            <small>Support communities across the Philippines</small>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Secure Platform</h6>
                            <small>Your data and donations are protected</small>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Track Your Impact</h6>
                            <small>See how your contributions help others</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Registration Form Section -->
        <div class="col-md-6 register-right">
            <div class="brand-section">
                <a href="index.php" class="brand-link">
                    <i class="fas fa-hands-helping me-2"></i>Help Pinoy
                </a>
                <p class="brand-tagline">Create your account and start helping today</p>
            </div>

            <div class="form-header">
                <h2>Create Account</h2>
                <p>Fill in your details to get started</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="registerForm">
                <div class="row">
                    <div class="col-12">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="name" name="name" 
                                   placeholder="Full Name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                            <label for="name"><i class="fas fa-user me-2"></i>Full Name</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="Email Address" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                            <label for="email"><i class="fas fa-envelope me-2"></i>Email Address</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="tel" class="form-control" id="contact_number" name="contact_number" 
                                   placeholder="Contact Number" value="<?php echo isset($_POST['contact_number']) ? htmlspecialchars($_POST['contact_number']) : ''; ?>" required>
                            <label for="contact_number"><i class="fas fa-phone me-2"></i>Contact Number</label>
                        </div>
                    </div>
                </div>

                <div class="form-floating">
                    <select class="form-control" id="gender" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                    <label for="gender"><i class="fas fa-venus-mars me-2"></i>Gender</label>
                </div>

                <div class="form-floating position-relative">
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Password" required>
                    <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                    <button type="button" class="password-toggle" id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </button>
                    <div class="password-strength" id="passwordStrength"></div>
                    <div class="password-requirements" id="passwordRequirements">
                        <div class="requirement unmet" id="reqLength"><i class="fas fa-circle"></i> At least 8 characters</div>
                        <div class="requirement unmet" id="reqUpper"><i class="fas fa-circle"></i> One uppercase letter</div>
                        <div class="requirement unmet" id="reqLower"><i class="fas fa-circle"></i> One lowercase letter</div>
                        <div class="requirement unmet" id="reqNumber"><i class="fas fa-circle"></i> One number</div>
                        <div class="requirement unmet" id="reqSpecial"><i class="fas fa-circle"></i> One special character</div>
                    </div>
                </div>

                <div class="form-floating position-relative">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                           placeholder="Confirm Password" required>
                    <label for="confirm_password"><i class="fas fa-lock me-2"></i>Confirm Password</label>
                    <button type="button" class="password-toggle" id="toggleConfirmPassword">
                        <i class="fas fa-eye"></i>
                    </button>
                    <div class="form-text text-danger" id="confirmPasswordHelp"></div>
                </div>

                <button type="submit" name="register" class="btn btn-register" id="registerButton">
                    <span id="buttonText">Create Account</span>
                </button>
            </form>

            <div class="login-link">
                <p>Already have an account? 
                    <a href="user_login.php">Sign in here</a>
                </p>
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

        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('confirm_password');
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

        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrength');
            const requirements = document.getElementById('passwordRequirements');
            
            // Check requirements
            const hasLength = password.length >= 8;
            const hasUpper = /[A-Z]/.test(password);
            const hasLower = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSpecial = /[\W_]/.test(password);
            
            // Update requirement indicators
            document.getElementById('reqLength').className = hasLength ? 'requirement met' : 'requirement unmet';
            document.getElementById('reqUpper').className = hasUpper ? 'requirement met' : 'requirement unmet';
            document.getElementById('reqLower').className = hasLower ? 'requirement met' : 'requirement unmet';
            document.getElementById('reqNumber').className = hasNumber ? 'requirement met' : 'requirement unmet';
            document.getElementById('reqSpecial').className = hasSpecial ? 'requirement met' : 'requirement unmet';
            
            // Calculate strength
            let strength = 0;
            if (hasLength) strength++;
            if (hasUpper) strength++;
            if (hasLower) strength++;
            if (hasNumber) strength++;
            if (hasSpecial) strength++;
            
            // Update strength bar
            strengthBar.className = 'password-strength ';
            if (strength <= 1) strengthBar.classList.add('strength-weak');
            else if (strength <= 2) strengthBar.classList.add('strength-fair');
            else if (strength <= 3) strengthBar.classList.add('strength-good');
            else strengthBar.classList.add('strength-strong');
        });

        // Confirm password validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            const help = document.getElementById('confirmPasswordHelp');
            
            if (confirmPassword && password !== confirmPassword) {
                help.textContent = "Passwords do not match";
            } else {
                help.textContent = "";
            }
        });
    </script>
</body>

</html>