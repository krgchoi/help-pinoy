<?php
if (!isset($_GET['public_id'])) {
    die("Invalid access. Missing donation ID.");
}

$public_id = htmlspecialchars($_GET['public_id']);
$message = '';
$message_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $public_id = $_POST['public_id'];

    if (!isset($_FILES['receipt']) || $_FILES['receipt']['error'] != 0) {
        $message = "No file uploaded.";
        $message_type = "danger";
    } else {

        $file = $_FILES['receipt'];

        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];

        if (!in_array($file['type'], $allowed_types)) {
            $message = "Invalid file type. Only JPG and PNG allowed.";
            $message_type = "danger";
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $message = "File too large (max 2MB).";
            $message_type = "danger";
        } else {

            $cfile = new CURLFile(
                $file['tmp_name'],
                $file['type'],
                $file['name']
            );

            $post_data = [
                'public_id' => $public_id,
                'receipt' => $cfile
            ];

            $ch = curl_init("http://localhost:5000/user/upload_receipt");

            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                $message = "Connection error: " . curl_error($ch);
                $message_type = "danger";
            } else {
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $result = json_decode($response, true);

                if ($httpcode == 200 && isset($result['success']) && $result['success']) {
                    header("Location: thank_you.php?public_id=" . urlencode($public_id));
                    exit();
                    $message_type = "success";
                } else {
                    $message = $result['message'] ?? "Upload failed. Please try again.";
                    $message_type = "danger";
                }
            }

            curl_close($ch);
        }
    }
}
include './template/header.php';
?>

<style>
    .upload-container {
        margin-top: 100px;
        margin-bottom: 50px;
    }

    .upload-box {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .upload-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .upload-header h3 {
        font-weight: 800;
        color: #3a0ca3;
    }

    .form-control {
        border-radius: 10px;
        padding: 12px;
        border: 2px solid #e9ecef;
    }

    .form-control:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
    }

    .btn-primary {
        background: linear-gradient(135deg, #4361ee, #3a0ca3);
        border: none;
        border-radius: 50px;
        padding: 12px 30px;
        font-weight: 600;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
    }

    .gcash-box {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .qr-container {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .qr-image {
        width: 200px;
        height: 200px;
        object-fit: contain;
        border-radius: 12px;
        border: 2px solid #e9ecef;
        padding: 10px;
        background: white;
    }

    .gcash-details {
        font-size: 1rem;
        color: #333;
    }
</style>

<div class="container upload-container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="upload-box">

                <div class="upload-header">
                    <h3>Upload Donation Receipt</h3>
                    <p>Please upload your GCash receipt for verification.</p>
                </div>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $message_type; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                <div class="gcash-box mb-4 text-center">
                    <h5 class="mb-3"><i class="fas fa-qrcode text-success me-2"></i>Pay via GCash</h5>

                    <div class="qr-container mb-3">
                        <img src="<?= BASE_URL ?>assets/img/gcash_qr.jpg" alt="GCash QR" class="qr-image">
                    </div>

                    <div class="gcash-details">
                        <p class="mb-1"><strong>Account Name:</strong> Kim Rholand Guillem</p>
                        <p class="mb-1"><strong>GCash Number:</strong> 0994-361-5121</p>
                        <p class="text-muted small">Scan the QR or send to the number above, then upload your receipt below.</p>
                    </div>
                </div>

                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="public_id" value="<?php echo $public_id; ?>">

                    <div class="mb-3">
                        <label class="form-label">Receipt Image (JPG/PNG only)</label>
                        <input type="file" name="receipt" class="form-control" required>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">
                            Submit Receipt
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?php include './template/footer.php'; ?>