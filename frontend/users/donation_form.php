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

<style>
    body {
        background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)),
                    url('../assets/img/donation-banner2.jpg') no-repeat center center fixed;
        background-size: cover;
        font-family: 'Roboto', sans-serif;
        color: #333;
    }

    .transparent-navbar {
        background-color: rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(6px);
    }
    .transparent-navbar .nav-link,
    .transparent-navbar .navbar-brand,
    .transparent-navbar .btn {
        color: white !important;
    }

    .donation-container {
        margin-top: 120px;
        margin-bottom: 50px;
    }

    .donation-form-box {
        background: #fff;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .progress-steps {
        display: flex;
        justify-content: space-between;
        font-size: 14px;
        margin-bottom: 8px;
    }
    .progress-steps div {
        flex: 1;
        text-align: center;
        color: #6c757d;
        font-weight: 500;
        position: relative;
        cursor: pointer;
    }
    .progress-steps div.active {
        color: #198754;
        font-weight: 700;
    }

    .progress {
        height: 6px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .amount-btn-group .btn {
        width: 100%;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .amount-btn-group .btn-outline-primary {
        border: 2px solid #198754;
        color: #198754;
    }
    .amount-btn-group .btn-outline-primary:hover,
    .amount-btn-group .btn-outline-primary.active {
        background-color: #198754;
        color: white;
    }

    .form-step { display: none; }
    .form-step.active { display: block; }

    #confirmation p {
        font-size: 16px;
        margin-bottom: 10px;
    }
    .text-muted {
        font-size: 0.9rem;
    }
</style>

<div class="container donation-container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="donation-form-box">
                <div class="donation-header mb-3 text-center">
                    <h3 class="fw-bold">Make a Donation</h3>
                </div>

                <div class="progress-steps mb-2">
                    <div class="progress-step active" data-step="0">Amount</div>
                    <div class="progress-step" data-step="1">My Info</div>
                    <div class="progress-step" data-step="2">Confirm</div>
                </div>
                <div class="progress mb-4">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 33%;"></div>
                </div>

                <p class="text-center text-muted mb-4">
                    Your contribution can make a difference. Fill out the form below to donate.
                </p>

                <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form action="donation_form.php" method="post" id="donation-form">
                    <input type="hidden" name="donor_id" value="<?php echo htmlspecialchars($default_donor_id); ?>">

                    <div class="form-step active">
                        <h4 class="mb-3">Select Donation Amount</h4>
                        <div class="row g-2 amount-btn-group">
                            <div class="col-6"><button type="button" class="btn btn-outline-primary amount-btn" data-amount="100">₱100</button></div>
                            <div class="col-6"><button type="button" class="btn btn-outline-primary amount-btn" data-amount="200">₱200</button></div>
                            <div class="col-6"><button type="button" class="btn btn-outline-primary amount-btn" data-amount="300">₱300</button></div>
                            <div class="col-6"><button type="button" class="btn btn-outline-primary amount-btn" data-amount="500">₱500</button></div>
                            <div class="col-12"><button type="button" class="btn btn-outline-primary amount-btn other">Other Amount</button></div>
                        </div>
                        <input type="number" name="amount" id="amount" class="form-control mt-2 d-none" placeholder="Enter custom amount" min="1">
                        <input type="hidden" name="amount_radio" id="amount_radio">
                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-success next-step">Continue</button>
                        </div>
                    </div>

                    <div class="form-step">
                        <h4 class="mb-3">Your Information</h4>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="anonymous" name="anonymous">
                            <label class="form-check-label" for="anonymous">Donate Anonymously</label>
                        </div>
                        <div id="info-fields">
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Full Name</label>
                                <input type="text" name="full_name" id="full_name" class="form-control"
                                    value="<?php echo htmlspecialchars($default_full_name); ?>" <?php echo $readonly; ?> required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control"
                                    value="<?php echo htmlspecialchars($default_email); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="contact_number" class="form-label">Contact Number</label>
                                <input type="text" name="contact_number" id="contact_number" class="form-control"
                                    value="<?php echo htmlspecialchars($default_contact); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="birthday" class="form-label">Birthday (Optional)</label>
                                <input type="date" name="birthday" id="birthday" class="form-control">
                            </div>
                        </div>
                        <div class="mt-4 text-end">
                            <button type="button" class="btn btn-success next-step">Donate</button>
                        </div>
                    </div>

                    <div class="form-step">
                        <h4 class="mb-3">Confirm Details</h4>
                        <div id="confirmation"></div>
                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-success">Donate Now</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const amountButtons = document.querySelectorAll('.amount-btn');
    const customAmountInput = document.getElementById('amount');
    const amountRadioInput = document.getElementById('amount_radio');
    const steps = document.querySelectorAll('.form-step');
    const progressSteps = document.querySelectorAll('.progress-step');
    const nextBtns = document.querySelectorAll('.next-step');
    const progressBar = document.querySelector('.progress-bar');

    let currentStep = 0;
    let maxStepReached = 0;

    amountButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            amountButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            if (btn.classList.contains('other')) {
                customAmountInput.classList.remove('d-none');
                amountRadioInput.value = 'custom';
            } else {
                customAmountInput.classList.add('d-none');
                customAmountInput.value = '';
                amountRadioInput.value = btn.dataset.amount;
            }
        });
    });

    const anonymousCheckbox = document.getElementById('anonymous');
    const personalFields = ['full_name', 'email', 'contact_number', 'birthday'];
    anonymousCheckbox.addEventListener('change', function () {
        if (this.checked) {
            personalFields.forEach(id => document.getElementById(id).setAttribute('readonly', 'true'));
        } else {
            personalFields.forEach(id => document.getElementById(id).removeAttribute('readonly'));
        }
    });

    function updateStep() {
        steps.forEach((step, index) => step.classList.toggle('active', index === currentStep));

        progressSteps.forEach((label, i) => {
            label.classList.toggle('active', i <= maxStepReached);
        });

        const stepPercent = ((maxStepReached + 1) / steps.length) * 100;
        progressBar.style.width = stepPercent + '%';
    }

    nextBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            if (currentStep === 0) {
                const selectedAmount = document.getElementById('amount_radio').value || document.getElementById('amount').value;
                if (!selectedAmount) {
                    alert('Please select or enter an amount.');
                    return;
                }
            }
            if (currentStep === 1) {
                const anonymous = document.getElementById('anonymous').checked;
                if (!anonymous) {
                    const fullName = document.getElementById('full_name').value.trim();
                    const email = document.getElementById('email').value.trim();
                    const contact = document.getElementById('contact_number').value.trim();
                    let errorMsg = '';
                    if (!fullName) errorMsg += 'Full Name is required.\n';
                    if (!email) errorMsg += 'Email is required.\n';
                    if (!contact) errorMsg += 'Contact Number is required.\n';
                    if (errorMsg) {
                        alert(errorMsg);
                        return;
                    }
                }
                const amount = document.getElementById('amount_radio').value === 'custom'
                    ? document.getElementById('amount').value
                    : document.getElementById('amount_radio').value;
                const name = anonymous ? 'Anonymous Donor' : document.getElementById('full_name').value;
                const email = anonymous ? 'anonymous@example.com' : document.getElementById('email').value;

                document.getElementById('confirmation').innerHTML = `
                    <p><strong>Amount:</strong> ₱${amount}</p>
                    <p><strong>Name:</strong> ${name}</p>
                    <p><strong>Email:</strong> ${email}</p>
                `;
            }

            currentStep++;
            if (currentStep >= steps.length) currentStep = steps.length - 1;

            if (currentStep > maxStepReached) {
                maxStepReached = currentStep;
            }

            updateStep();
        });
    });

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

