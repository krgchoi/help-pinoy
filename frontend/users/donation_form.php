<?php
ob_start();
include './template/header.php';
$default_full_name = '';
$default_email = '';
$default_contact = '';
$default_donor_id = '';
$readonly = '';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $api_url = 'http://localhost:5000/user/user_profile';
    $payload = json_encode(['user_id' => $user_id]);

    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode == 200) {
        $result = json_decode($response, true);
        $user = $result['user'] ?? [];
        $default_donor_id = $user['id'] ?? '';
        $default_full_name = $user['name'] ?? '';
        $default_email = $user['email'] ?? '';
        $default_contact = $user['contact_number'] ?? '';
        $readonly = 'readonly';
    } else {
        $error = "Failed to fetch user data. Please try again.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $anonymous = isset($_POST["anonymous"]) ? true : false;

    if ($anonymous) {
        $donor_id = "";
        $full_name = "Anonymous Donor";
        $email = "anonymous@example.com";
        $contact_number = "0000000000";
        $birthday = date('Y-m-d');
    } else {
        $donor_id = htmlspecialchars(strip_tags(trim($_POST["donor_id"])));
        $full_name = htmlspecialchars(strip_tags(trim($_POST["full_name"])));
        $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
        $contact_number = htmlspecialchars(strip_tags(trim($_POST["contact_number"])));
        $birthday = htmlspecialchars(strip_tags(trim($_POST["birthday"])));
    }
    $amount = (isset($_POST["amount_radio"]) && $_POST["amount_radio"] !== "custom")
        ? floatval($_POST["amount_radio"])
        : floatval($_POST["amount"]);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($amount <= 0) {
        $error = "Donation amount must be greater than zero.";
    } else {
      
        $json_data = json_encode([
            "donor_id" => $donor_id,
            "full_name" => $full_name,
            "email" => $email,
            "contact_number" => $contact_number,
            "birthday" => $birthday,
            "amount" => $amount
        ]);

        $flask_url = "http://localhost:5000/user/donation_form";
        $ch = curl_init($flask_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode == 200) {
            $result = json_decode($response, true);
            $invoice_url = htmlspecialchars($result["invoice_url"]);
            header("Location: " . $invoice_url);
            exit();
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make a Donation | Help Pinoy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --accent-color: #4cc9f0;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --border-radius: 16px;
            --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            --transition: all 0.3s ease;
        }

        body {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
                        url('../assets/img/donation-banner2.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .transparent-navbar {
            background-color: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
        }
        .transparent-navbar .nav-link,
        .transparent-navbar .navbar-brand,
        .transparent-navbar .btn {
            color: white !important;
        }

        .donation-container {
            margin-top: 100px;
            margin-bottom: 50px;
            flex: 1;
        }

        .donation-form-box {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .donation-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .donation-header h3 {
            font-weight: 800;
            color: var(--secondary-color);
            margin-bottom: 10px;
            font-size: 2rem;
        }

        .donation-header p {
            color: #6c757d;
            font-size: 1.1rem;
        }

        .progress-container {
            margin-bottom: 30px;
        }

        .progress-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 15px;
        }

        .progress-steps::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 3px;
            background: #e9ecef;
            transform: translateY(-50%);
            z-index: 1;
        }

        .progress-step {
            flex: 1;
            text-align: center;
            color: #6c757d;
            font-weight: 600;
            position: relative;
            z-index: 2;
            cursor: pointer;
            padding: 0 10px;
        }

        .progress-step .step-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            margin-bottom: 8px;
            transition: var(--transition);
            border: 3px solid white;
        }

        .progress-step.active .step-number {
            background: var(--success-color);
            color: white;
            transform: scale(1.1);
        }

        .progress-step.completed .step-number {
            background: var(--success-color);
            color: white;
        }

        .progress-step.completed .step-number::after {
            content: '✓';
            font-weight: bold;
        }

        .form-step {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .form-step.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .amount-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .amount-option {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 20px 15px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 600;
            font-size: 1.2rem;
            color: var(--dark-color);
        }

        .amount-option:hover {
            border-color: var(--primary-color);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.2);
        }

        .amount-option.active {
            border-color: var(--success-color);
            background: var(--success-color);
            color: white;
            transform: scale(1.05);
        }

        .amount-option.active::after {
            content: '✓';
            margin-left: 8px;
        }

        .custom-amount-container {
            margin-top: 20px;
            position: relative;
        }

        .custom-amount-container .input-group-text {
            background: var(--primary-color);
            color: white;
            border: none;
            font-weight: 600;
        }

        .custom-amount-input {
            border-radius: 0 8px 8px 0 !important;
            border: 2px solid #e9ecef;
            border-left: none;
            padding: 12px 15px;
            font-weight: 600;
            transition: var(--transition);
        }

        .custom-amount-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--dark-color);
        }

        .anonymous-toggle {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 2px solid #e9ecef;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
            margin-right: 15px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: var(--transition);
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: var(--transition);
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: var(--success-color);
        }

        input:checked + .toggle-slider:before {
            transform: translateX(30px);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color), #20c997);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }

        .btn-outline-secondary {
            border: 2px solid #6c757d;
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-outline-secondary:hover {
            background: #6c757d;
            color: white;
        }

        .confirmation-details {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
        }

        .confirmation-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .confirmation-item:last-child {
            border-bottom: none;
        }

        .confirmation-label {
            font-weight: 600;
            color: #6c757d;
        }

        .confirmation-value {
            font-weight: 600;
            color: var(--dark-color);
        }

        .impact-message {
            background: linear-gradient(135deg, #e3f2fd, #f3e5f5);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin-bottom: 25px;
            border-left: 4px solid var(--primary-color);
        }

        .impact-message i {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .step-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        @media (max-width: 768px) {
            .donation-form-box {
                padding: 25px 20px;
                margin: 20px;
            }
            
            .amount-grid {
                grid-template-columns: 1fr;
            }
            
            .step-actions {
                flex-direction: column;
                gap: 10px;
            }
            
            .step-actions .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container donation-container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="donation-form-box">
                    <div class="donation-header">
                        <h3><i class="fas fa-heart text-danger me-2"></i>Make a Donation</h3>
                        <p>Your generosity can change lives. Every contribution makes a difference.</p>
                    </div>

                    <div class="progress-container">
                        <div class="progress-steps">
                            <div class="progress-step active" data-step="0">
                                <div class="step-number">1</div>
                                <div>Amount</div>
                            </div>
                            <div class="progress-step" data-step="1">
                                <div class="step-number">2</div>
                                <div>Your Info</div>
                            </div>
                            <div class="progress-step" data-step="2">
                                <div class="step-number">3</div>
                                <div>Confirm</div>
                            </div>
                        </div>
                    </div>

                    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($error)): ?>
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form action="donation_form.php" method="post" id="donation-form">
                        <input type="hidden" name="donor_id" value="<?php echo htmlspecialchars($default_donor_id); ?>">

                        <!-- Step 1: Amount Selection -->
                        <div class="form-step active">
                            <h4 class="mb-4"><i class="fas fa-coins me-2 text-warning"></i>Select Donation Amount</h4>
                            
                            <div class="amount-grid">
                                <div class="amount-option" data-amount="100">₱100</div>
                                <div class="amount-option" data-amount="200">₱200</div>
                                <div class="amount-option" data-amount="300">₱300</div>
                                <div class="amount-option" data-amount="500">₱500</div>
                                <div class="amount-option" data-amount="1000">₱1,000</div>
                                <div class="amount-option" data-amount="2000">₱2,000</div>
                            </div>

                            <div class="custom-amount-container">
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" name="amount" id="amount" class="form-control custom-amount-input" 
                                           placeholder="Enter custom amount" min="1" step="1">
                                </div>
                            </div>
                            <input type="hidden" name="amount_radio" id="amount_radio">

                            <div class="step-actions">
                                <div></div> <!-- Empty div for spacing -->
                                <button type="button" class="btn btn-primary next-step">
                                    Continue <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Personal Information -->
                        <div class="form-step">
                            <h4 class="mb-4"><i class="fas fa-user me-2 text-primary"></i>Your Information</h4>
                            
                            <div class="anonymous-toggle">
                                <label class="toggle-switch">
                                    <input type="checkbox" id="anonymous" name="anonymous">
                                    <span class="toggle-slider"></span>
                                </label>
                                <span class="fw-bold">Donate Anonymously</span>
                            </div>

                            <div id="info-fields">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="full_name" class="form-label">Full Name</label>
                                        <input type="text" name="full_name" id="full_name" class="form-control"
                                            value="<?php echo htmlspecialchars($default_full_name); ?>" <?php echo $readonly; ?> required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" name="email" id="email" class="form-control"
                                            value="<?php echo htmlspecialchars($default_email); ?>" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="contact_number" class="form-label">Contact Number</label>
                                        <input type="text" name="contact_number" id="contact_number" class="form-control"
                                            value="<?php echo htmlspecialchars($default_contact); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="birthday" class="form-label">Birthday (Optional)</label>
                                        <input type="date" name="birthday" id="birthday" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="step-actions">
                                <button type="button" class="btn btn-outline-secondary prev-step">
                                    <i class="fas fa-arrow-left me-2"></i>Back
                                </button>
                                <button type="button" class="btn btn-primary next-step">
                                    Continue <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Confirmation -->
                        <div class="form-step">
                            <h4 class="mb-4"><i class="fas fa-check-circle me-2 text-success"></i>Confirm Your Donation</h4>
                            
                            <div class="impact-message">
                                <i class="fas fa-hands-helping"></i>
                                <h5>Thank You for Your Generosity!</h5>
                                <p class="mb-0">Your donation will help provide essential support to those in need.</p>
                            </div>

                            <div class="confirmation-details">
                                <div id="confirmation"></div>
                            </div>

                            <div class="step-actions">
                                <button type="button" class="btn btn-outline-secondary prev-step">
                                    <i class="fas fa-arrow-left me-2"></i>Back
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-heart me-2"></i>Complete Donation
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const amountOptions = document.querySelectorAll('.amount-option');
            const customAmountInput = document.getElementById('amount');
            const amountRadioInput = document.getElementById('amount_radio');
            const steps = document.querySelectorAll('.form-step');
            const progressSteps = document.querySelectorAll('.progress-step');
            const nextBtns = document.querySelectorAll('.next-step');
            const prevBtns = document.querySelectorAll('.prev-step');

            let currentStep = 0;
            let maxStepReached = 0;

            // Amount selection
            amountOptions.forEach(option => {
                option.addEventListener('click', () => {
                    amountOptions.forEach(opt => opt.classList.remove('active'));
                    option.classList.add('active');
                    customAmountInput.value = '';
                    amountRadioInput.value = option.dataset.amount;
                });
            });

            customAmountInput.addEventListener('input', () => {
                amountOptions.forEach(opt => opt.classList.remove('active'));
                amountRadioInput.value = 'custom';
            });

            // Anonymous toggle
            const anonymousCheckbox = document.getElementById('anonymous');
            const personalFields = ['full_name', 'email', 'contact_number', 'birthday'];
            
            anonymousCheckbox.addEventListener('change', function () {
                if (this.checked) {
                    personalFields.forEach(id => {
                        const field = document.getElementById(id);
                        field.setAttribute('readonly', 'true');
                        field.style.backgroundColor = '#f8f9fa';
                    });
                } else {
                    personalFields.forEach(id => {
                        const field = document.getElementById(id);
                        field.removeAttribute('readonly');
                        field.style.backgroundColor = '';
                    });
                }
            });

            // Step navigation
            function updateStep() {
                steps.forEach((step, index) => {
                    step.classList.toggle('active', index === currentStep);
                });

                progressSteps.forEach((step, index) => {
                    if (index < currentStep) {
                        step.classList.add('completed');
                        step.classList.remove('active');
                    } else if (index === currentStep) {
                        step.classList.add('active');
                        step.classList.remove('completed');
                    } else {
                        step.classList.remove('active', 'completed');
                    }
                });

                if (currentStep > maxStepReached) {
                    maxStepReached = currentStep;
                }
            }

            function validateStep(step) {
                if (step === 0) {
                    const selectedAmount = amountRadioInput.value || customAmountInput.value;
                    if (!selectedAmount) {
                        showAlert('Please select or enter a donation amount.');
                        return false;
                    }
                    if (amountRadioInput.value === 'custom' && (!customAmountInput.value || customAmountInput.value <= 0)) {
                        showAlert('Please enter a valid donation amount.');
                        return false;
                    }
                }
                
                if (step === 1) {
                    const anonymous = anonymousCheckbox.checked;
                    if (!anonymous) {
                        const fullName = document.getElementById('full_name').value.trim();
                        const email = document.getElementById('email').value.trim();
                        const contact = document.getElementById('contact_number').value.trim();
                        
                        if (!fullName) {
                            showAlert('Full Name is required.');
                            return false;
                        }
                        if (!email) {
                            showAlert('Email is required.');
                            return false;
                        }
                        if (!contact) {
                            showAlert('Contact Number is required.');
                            return false;
                        }
                        
                        // Basic email validation
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(email)) {
                            showAlert('Please enter a valid email address.');
                            return false;
                        }
                    }
                }
                
                return true;
            }

            function showAlert(message) {
                // Create or update alert
                let alert = document.querySelector('.alert-danger');
                if (!alert) {
                    alert = document.createElement('div');
                    alert.className = 'alert alert-danger d-flex align-items-center';
                    alert.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i>`;
                    document.querySelector('.donation-form-box').insertBefore(alert, document.querySelector('form'));
                }
                
                const messageSpan = document.createElement('span');
                messageSpan.textContent = message;
                alert.appendChild(messageSpan);
                
                // Remove alert after 5 seconds
                setTimeout(() => {
                    alert.remove();
                }, 5000);
            }

            function updateConfirmation() {
                const amount = amountRadioInput.value === 'custom' 
                    ? customAmountInput.value 
                    : amountRadioInput.value;
                
                const anonymous = anonymousCheckbox.checked;
                const name = anonymous ? 'Anonymous Donor' : document.getElementById('full_name').value;
                const email = anonymous ? 'anonymous@example.com' : document.getElementById('email').value;
                const contact = anonymous ? 'Not provided' : document.getElementById('contact_number').value;

                document.getElementById('confirmation').innerHTML = `
                    <div class="confirmation-item">
                        <span class="confirmation-label">Donation Amount:</span>
                        <span class="confirmation-value">₱${amount}</span>
                    </div>
                    <div class="confirmation-item">
                        <span class="confirmation-label">Full Name:</span>
                        <span class="confirmation-value">${name}</span>
                    </div>
                    <div class="confirmation-item">
                        <span class="confirmation-label">Email:</span>
                        <span class="confirmation-value">${email}</span>
                    </div>
                    <div class="confirmation-item">
                        <span class="confirmation-label">Contact Number:</span>
                        <span class="confirmation-value">${contact}</span>
                    </div>
                `;
            }

            nextBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    if (!validateStep(currentStep)) return;
                    
                    if (currentStep === 1) {
                        updateConfirmation();
                    }
                    
                    currentStep++;
                    if (currentStep >= steps.length) currentStep = steps.length - 1;
                    updateStep();
                });
            });

            prevBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    currentStep--;
                    if (currentStep < 0) currentStep = 0;
                    updateStep();
                });
            });

            // Allow clicking on progress steps to navigate
            progressSteps.forEach(step => {
                step.addEventListener('click', () => {
                    const stepIndex = parseInt(step.dataset.step);
                    if (stepIndex <= maxStepReached) {
                        currentStep = stepIndex;
                        updateStep();
                    }
                });
            });

            updateStep();
        });
    </script>
</body>
</html>