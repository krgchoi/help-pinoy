<?php
include './template/header.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: user_login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$api_url = 'http://localhost:5000/user/user_profile';
$data = json_encode(['user_id' => $user_id]);

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

$user = $result['user'] ?? [];
$donations = $result['donations'] ?? [];
$profile_img = $user['profile_img'] ?? null;

$update_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $update_api_url = 'http://localhost:5000/user/user_update_profile';
    $update_data = [
        'user_id' => $user_id,
        'name' => $_POST['name'] ?? '',
        'contact_number' => $_POST['contact_number'] ?? '',
        'gender' => $_POST['gender'] ?? '',
        'password' => $_POST['password'] ?? '',
        'confirm_password' => $_POST['confirm_password'] ?? ''
    ];
    $ch_update = curl_init($update_api_url);
    curl_setopt($ch_update, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch_update, CURLOPT_POST, true);
    curl_setopt($ch_update, CURLOPT_POSTFIELDS, json_encode($update_data));
    curl_setopt($ch_update, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch_update, CURLOPT_TIMEOUT, 10);
    $update_response = curl_exec($ch_update);
    curl_close($ch_update);

    $update_result = json_decode($update_response, true);
    if ($update_result && isset($update_result['success'])) {
        if ($update_result['success']) {
            $update_message = '<div class="alert alert-success">' . htmlspecialchars($update_result['message']) . '</div>';
            $ch = curl_init($api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $response = curl_exec($ch);
            curl_close($ch);
            $result = json_decode($response, true);
            $user = $result['user'] ?? [];
        } else {
            $update_message = '<div class="alert alert-danger">' . htmlspecialchars($update_result['message']) . '</div>';
        }
    } else {
        $update_message = '<div class="alert alert-danger">Failed to update profile.</div>';
    }
}
?>

<style>
    .donation table {
        width: 100%;
        border-collapse: collapse;
    }

    .donation .product-info {
        display: flex;
        flex-wrap: wrap;
    }

    .donation th {
        padding: 5px 10px;
        color: #fff;
        background-color: rgb(29, 69, 202);
    }

    .donation td {
        padding: 10px 20px;
    }

    .donation .donation-details-btn {
        color: #fff;
        background-color: rgb(0, 102, 255);
    }
</style>

<div class="container text-center mt-5">
    <h3>Profile Settings</h3>
    <hr class="mx-auto">
</div>
<div class="container rounded bg-white mb-5">
    <div class="row">
        <div class="col-md-3 donation-right">
            <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                <img class="rounded-circle mt-5" width="150px" id="profileImgPreview" src="../../static/profile_img/<?php echo htmlspecialchars($profile_img, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($user['name'] ?? 'User Profile Image', ENT_QUOTES, 'UTF-8'); ?>">
                <form id="profileImgForm" enctype="multipart/form-data" method="post">
                    <input type="file" name="profile_img" id="profile_img" accept="image/*" style="display:none;">
                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="document.getElementById('profile_img').click();">Change Photo</button>
                </form>
                <span class="font-weight-bold"><?php echo htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="text-black-50"><?php echo htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>
        <div class="col-md-9 donation-right">
            <div class="p-3 py-5">
                <?php echo $update_message; ?>
                <form method="post" action="" id="profileForm">
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <label class="labels">Full Name</label>
                            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12"><label class="labels">Mobile Number</label><input type="text" class="form-control" name="contact_number" value="<?php echo htmlspecialchars($user['contact_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
                        <div class="col-md-12"><label class="labels">Email Address</label><input type="text" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" readonly></div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="labels">Password</label>
                            <input type="password" class="form-control" name="password" id="password" placeholder="Enter Password">
                            <div id="passwordHelp" class="form-text text-danger"></div>
                        </div>
                        <div class="col-md-12">
                            <label class="labels">Confirm Password</label>
                            <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm Password">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="labels">Gender</label>
                            <select class="form-control" name="gender">
                                <option value="Male" <?php if (($user['gender'] ?? '') === 'Male') echo 'selected'; ?>>Male</option>
                                <option value="Female" <?php if (($user['gender'] ?? '') === 'Female') echo 'selected'; ?>>Female</option>
                                <option value="Other" <?php if (($user['gender'] ?? '') === 'Other') echo 'selected'; ?>>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-5 text-center"><button class="btn btn-primary profile-button" type="submit" name="update_profile">Save Profile</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

<section class="donation container my-5 pb-5">
    <div class="container text-center mt-5">
        <h3>Your donation</h3>
        <hr class="mx-auto">
    </div>

    <table class="mt-5 pt-5">
        <tr>
            <th>Reciept #</th>
            <th>Amount</th>
            <th>Donation Status</th>
            <th>Donation Date</th>
            <th>Details</th>
        </tr>
            <?php foreach ($donations as $donation): ?>
                <tr>
                    <td>
                        <span><?php echo htmlspecialchars($donation['receipt_no'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                    </td>
                    <td>
                        <span>₱<?php echo htmlspecialchars($donation['amount'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                    </td>
                    <td>
                        <span><?php echo htmlspecialchars($donation['status'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                    </td>
                    <td>
                        <span><?php echo htmlspecialchars($donation['date'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                    </td>
                    <td>
                        <?php if (strtolower($donation['status']) === 'paid' && !empty($donation['blockchain_tx'])): ?>
                            <a href="https://sepolia.etherscan.io/tx/<?php echo urlencode($donation['blockchain_tx']); ?>" target="_blank">View on Blockchain</a>
                        <?php elseif (strtolower($donation['status']) === 'pending' && !empty($donation['invoice_url'])): ?>
                            <a href="<?php echo htmlspecialchars($donation['invoice_url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" >Complete Payment</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
    </table>
</section>

<?php include './template/footer.php'; ?>

<script>
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const help = document.getElementById('passwordHelp');
        if (password.length === 0) {
            help.textContent = "";
            return;
        }
        const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
        if (!regex.test(password)) {
            help.textContent = "Password must be at least 8 characters, include uppercase, lowercase, number, and special character.";
        } else {
            help.textContent = "";
        }
    });

    document.getElementById('profileForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const help = document.getElementById('passwordHelp');
        const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
        if (password.length > 0 && !regex.test(password)) {
            e.preventDefault();
            help.textContent = "Password must be at least 8 characters, include uppercase, lowercase, number, and special character.";
        }
    });

    document.getElementById('profile_img').addEventListener('change', function() {
        var formData = new FormData(document.getElementById('profileImgForm'));
        fetch('http://localhost:5000/user/user_upload_profile_image', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(text);
                    });
                }
                return response.json();
            })

            .then(data => {
                if (data.success && data.filename) {
                    console.log(data);
                    document.getElementById('profileImgPreview').src = '../../static/profile_img/' + data.filename + '?t=' + new Date().getTime();
                } else {
                    alert('Failed to upload image: ' + (data.message || 'Unknown error'));
                }
            })

            .catch((err) => {
                alert('Failed to upload image. Server response: ' + err.message);
            });
    });
</script>