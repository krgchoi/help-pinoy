<?php
include('./template/navbar.php');

$jwt_token = $_SESSION['access_token'] ?? null;

if (!$jwt_token) {
    header("Location: admin_login.php");
    exit();
}

// =========================
// CREATE DISBURSEMENT
// =========================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_disbursement'])) {

    $amount = floatval($_POST['amount']);
    $project_name = $_POST['project_name'];

    $payload = json_encode([
        'amount' => $amount,
        'project_name' => $project_name
    ]);

    $ch = curl_init("http://localhost:5000/admin/create_disbursement");

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $jwt_token"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    if (isset($result['status']) && $result['status'] === 'success') {
        echo "<script>alert('Disbursement created successfully!'); window.location.href='disbursement.php';</script>";
        exit();
    } else {
        $msg = $result['message'] ?? 'Error creating disbursement';
        echo "<script>alert('$msg');</script>";
    }
}


// =========================
// FETCH DISBURSEMENTS
// =========================
$ch = curl_init("http://localhost:5000/admin/disbursements");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    "Authorization: Bearer $jwt_token"
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if (isset($data['status']) && $data['status'] === 'expire') {
    session_destroy();
    header("Location: admin_login.php");
    exit();
}

$disbursements = $data ?? [];


// =========================
// FETCH ALLOCATIONS (IF VIEW)
// =========================
$allocations = [];
$selected_id = null;

if (isset($_GET['view'])) {

    $selected_id = intval($_GET['view']);

    $ch = curl_init("http://localhost:5000/admin/disbursement_allocations/" . $selected_id);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: Bearer $jwt_token"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $allocations = json_decode($response, true) ?? [];
}
?>

<title>Disbursement Management | Help Pinoy Admin</title>

<div class="container mt-4">

    <!-- CREATE FORM -->
    <div class="card">
        <div class="card-header">
            <h5>Create Disbursement</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-2">
                    <label>Project Name</label>
                    <input type="text" name="project_name" class="form-control" required>
                </div>

                <div class="mb-2">
                    <label>Amount</label>
                    <input type="number" name="amount" step="0.01" class="form-control" required>
                </div>

                <button type="submit" name="create_disbursement" class="btn btn-primary mt-2">
                    Create
                </button>
            </form>
        </div>
    </div>


    <!-- DISBURSEMENT TABLE -->
    <div class="card mt-4">
        <div class="card-header">
            <h5>Disbursement Projects</h5>
        </div>

        <div class="card-body">

            <?php if (empty($disbursements)): ?>
                <p>No disbursements found.</p>
            <?php else: ?>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Project</th>
                            <th>Amount</th>
                            <th>Created By</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($disbursements as $d): ?>
                            <tr>
                                <td><?php echo $d['disbursement_id']; ?></td>

                                <td><?php echo htmlspecialchars($d['project_name']); ?></td>

                                <td>₱<?php echo number_format($d['total_amount'], 2); ?></td>

                                <td><?php echo htmlspecialchars($d['created_by_name'] ?? 'Admin'); ?></td>

                                <td><?php echo date('M d, Y', strtotime($d['created_at'])); ?></td>

                                <td>
                                    <a href="?view=<?php echo $d['disbursement_id']; ?>"
                                        class="btn btn-sm btn-primary">
                                        View Allocations
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>

            <?php endif; ?>

        </div>
    </div>


    <!-- ALLOCATION BREAKDOWN -->
    <?php if ($selected_id !== null): ?>

        <div class="card mt-4">
            <div class="card-header">
                <h5>Allocation Breakdown (Project ID: <?php echo $selected_id; ?>)</h5>
            </div>

            <div class="card-body">

                <?php if (empty($allocations)): ?>
                    <p>No allocation data found.</p>
                <?php else: ?>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Donor</th>
                                <th>Donation ID</th>
                                <th>Original Amount</th>
                                <th>Allocated</th>
                                <th>Date</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($allocations as $a): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($a['donor_name'] ?? 'Anonymous'); ?></td>

                                    <td><?php echo htmlspecialchars($a['public_id']); ?></td>

                                    <td>₱<?php echo number_format($a['original_amount'], 2); ?></td>

                                    <td>₱<?php echo number_format($a['allocated_amount'], 2); ?></td>

                                    <td><?php echo date('M d, Y', strtotime($a['donation_date'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>

                <?php endif; ?>

            </div>
        </div>

    <?php endif; ?>

</div>

<?php include('./template/foot.php'); ?>