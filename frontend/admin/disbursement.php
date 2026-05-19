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
$create_message = '';
$create_message_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_disbursement'])) {

    $amount = floatval($_POST['amount']);
    $project_name = trim($_POST['project_name']);

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
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);

    if (isset($result['status']) && $result['status'] === 'success') {
        $_SESSION['toast_message'] = 'Disbursement created successfully!';
        $_SESSION['toast_type'] = 'success';
        echo "<script>window.location.href='disbursement.php';</script>";
        exit();
    } else {
        $error_msg = $result['message'] ?? 'Error creating disbursement';
        $_SESSION['toast_message'] = $error_msg;
        $_SESSION['toast_type'] = 'error';
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

$allocations = [];
$selected_id = null;
$selected_project = null;

if (isset($_GET['view'])) {
    $selected_id = intval($_GET['view']);

    foreach ($disbursements as $d) {
        if ($d['disbursement_id'] == $selected_id) {
            $selected_project = $d['project_name'];
            break;
        }
    }

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

$totalDisbursed = 0;
$totalProjects = count($disbursements);

foreach ($disbursements as $d) {
    $totalDisbursed += floatval($d['total_amount']);
}

// =========================
// AVAILABLE FUNDS
// =========================
$availablefunds = 0;

$ch = curl_init("http://localhost:5000/admin/available_funds");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    "Authorization: Bearer $jwt_token"
]);

$response = curl_exec($ch);
curl_close($ch);

$fundData = json_decode($response, true);

if (isset($fundData['available_funds'])) {
    $availablefunds = floatval($fundData['available_funds']);
}

// =========================
// COMPLETED PROJECTS
// =========================
$completedProjects = 0;

foreach ($disbursements as $d) {

    $remaining = floatval($d['remaining_amount'] ?? 0);

    if ($remaining <= 0) {
        $completedProjects++;
    }
}
?>

<!-- Page specific CSS (scoped to this page only) -->
<style>
    /* Page-specific styles that won't conflict with admin.css */
    .stats-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
        border-left: 4px solid #0057b7;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
    }

    .stats-card .card-value {
        font-size: 2rem;
        font-weight: 700;
        color: #0057b7;
        margin-bottom: 0.5rem;
    }

    .stats-card .card-label {
        color: #64748b;
        font-weight: 500;
    }

    .stats-card i {
        font-size: 2.5rem;
        color: #0057b7;
        opacity: 0.3;
    }

    .card-custom {
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        border: none;
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .card-custom .card-header {
        background: linear-gradient(135deg, #0057b7, #002855);
        color: white;
        padding: 1rem 1.5rem;
        border: none;
        font-weight: 600;
    }

    .card-custom .card-header i {
        margin-right: 8px;
    }

    .card-custom .card-body {
        padding: 1.5rem;
    }

    .btn-primary-custom {
        background: linear-gradient(135deg, #0057b7, #002855);
        border: none;
        border-radius: 8px;
        padding: 10px 24px;
        transition: all 0.3s ease;
        color: white;
    }

    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 87, 183, 0.3);
        color: white;
    }

    .btn-outline-custom {
        background: transparent;
        border: 1px solid #0057b7;
        color: #0057b7;
        border-radius: 6px;
        padding: 6px 16px;
        transition: all 0.3s ease;
    }

    .btn-outline-custom:hover {
        background: #0057b7;
        color: white;
        transform: translateY(-2px);
    }

    .form-control-custom {
        border-radius: 8px;
        border: 2px solid #e2e8f0;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }

    .form-control-custom:focus {
        border-color: #0057b7;
        box-shadow: 0 0 0 0.2rem rgba(0, 87, 183, 0.25);
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #64748b;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        color: #e2e8f0;
    }

    .table-container {
        overflow-x: auto;
    }

    .table thead {
        background: linear-gradient(135deg, #0057b7, #002855);
        color: white;
    }

    .table thead th {
        border: none;
        padding: 1rem;
        font-weight: 600;
    }

    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 87, 183, 0.05);
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: white;
        color: #0057b7;
        padding: 8px 20px;
        border-radius: 50px;
        text-decoration: none;
        transition: all 0.3s ease;
        margin-bottom: 1rem;
        border: 1px solid #e2e8f0;
    }

    .back-button:hover {
        background: #0057b7;
        color: white;
        transform: translateX(-5px);
    }

    @media (max-width: 768px) {
        .stats-card .card-value {
            font-size: 1.5rem;
        }

        .table thead {
            display: none;
        }

        .table tbody tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1rem;
        }

        .table tbody td {
            display: block;
            text-align: right;
            padding: 0.5rem 0;
            border: none;
        }

        .table tbody td::before {
            content: attr(data-label);
            float: left;
            font-weight: 600;
            color: #1e293b;
        }
    }
</style>

<!-- Dashboard Header -->
<div class="dashboard-header" style="background: linear-gradient(135deg, #0057b7, #002855); color: white; padding: 2rem 0; margin-bottom: 2rem; border-radius: 0 0 12px 12px;">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h2 mb-2"><i class="bi bi-cash-stack me-2"></i>Disbursement Management</h1>
                <p class="mb-0 opacity-75">Manage project funds and track allocations</p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="btn-group">
                    <button type="button" class="btn btn-light" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i> Print Report
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4">
    <!-- Stats Overview -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="card-value">₱ <?php echo number_format($totalDisbursed, 2); ?></div>
                        <div class="card-label">Total Disbursed</div>
                    </div>
                    <i class="bi bi-piggy-bank"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="card-value"><?php echo $totalProjects; ?></div>
                        <div class="card-label">Total Projects</div>
                    </div>
                    <i class="bi bi-folder2"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="card-value">₱ <?php echo number_format($availablefunds, 2); ?></div>
                        <div class="card-label">Available Funds</div>
                    </div>
                    <i class="bi bi-play-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="card-value"><?php echo $completedProjects; ?></div>
                        <div class="card-label">Completed</div>
                    </div>
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Disbursement Form -->
    <div class="card-custom">
        <div class="card-header">
            <i class="bi bi-plus-circle"></i> Create New Disbursement
        </div>
        <div class="card-body">
            <form method="POST" id="disbursementForm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-building me-1"></i> Project Name
                        </label>
                        <input type="text"
                            name="project_name"
                            class="form-control form-control-custom"
                            placeholder="Enter project name"
                            required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-currency-dollar me-1"></i> Amount
                        </label>
                        <input type="number"
                            name="amount"
                            step="0.01"
                            class="form-control form-control-custom"
                            placeholder="0.00"
                            required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit"
                            name="create_disbursement"
                            class="btn btn-primary-custom w-100">
                            <i class="bi bi-save me-1"></i> Create
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Disbursements Table -->
    <div class="card-custom">
        <div class="card-header">
            <i class="bi bi-table"></i> Disbursement Projects
        </div>
        <div class="card-body p-0">
            <div class="table-container">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Project Name</th>
                            <th>Amount</th>
                            <th>Created By</th>
                            <th>Date Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($disbursements)): ?>
                            <?php foreach ($disbursements as $d): ?>
                                <tr>
                                    <td data-label="ID">
                                        <span class="fw-bold">#<?php echo $d['disbursement_id']; ?></span>
                                    </td>
                                    <td data-label="Project Name">
                                        <div class="fw-medium"><?php echo htmlspecialchars($d['project_name']); ?></div>
                                    </td>
                                    <td data-label="Amount">
                                        <strong class="text-success">₱ <?php echo number_format($d['total_amount'], 2); ?></strong>
                                    </td>
                                    <td data-label="Created By">
                                        <i class="bi bi-person-circle me-1"></i>
                                        <?php echo htmlspecialchars($d['created_by_name'] ?? 'Admin'); ?>
                                    </td>
                                    <td data-label="Date Created">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        <?php echo date('M j, Y', strtotime($d['created_at'])); ?>
                                    </td>
                                    <td data-label="Actions">
                                        <a href="?view=<?php echo $d['disbursement_id']; ?>"
                                            class="btn btn-outline-custom btn-sm">
                                            <i class="bi bi-eye me-1"></i> View Allocations
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <h4>No disbursements found</h4>
                                        <p>Create your first disbursement project using the form above.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Allocation Breakdown Section -->
    <?php if ($selected_id !== null && !empty($allocations)): ?>
        <div class="card-custom mt-4">
            <div class="card-header">
                <i class="bi bi-pie-chart"></i> Allocation Breakdown
                <small class="ms-2"><?php echo htmlspecialchars($selected_project ?? 'Project'); ?></small>
            </div>
            <div class="card-body p-0">
                <div class="table-container">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Donor Name</th>
                                <th>Donation ID</th>
                                <th>Original Amount</th>
                                <th>Allocated Amount</th>
                                <th>Donation Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalAllocated = 0;
                            foreach ($allocations as $a):
                                $totalAllocated += $a['allocated_amount'];
                            ?>
                                <tr>
                                    <td data-label="Donor Name">
                                        <div class="fw-medium">
                                            <i class="bi bi-person me-1"></i>
                                            <?php echo htmlspecialchars($a['donor_name'] ?? 'Anonymous'); ?>
                                        </div>
                                    </td>
                                    <td data-label="Donation ID">
                                        <code><?php echo htmlspecialchars($a['public_id']); ?></code>
                                    </td>
                                    <td data-label="Original Amount">
                                        ₱ <?php echo number_format($a['original_amount'], 2); ?>
                                    </td>
                                    <td data-label="Allocated Amount">
                                        <strong class="text-primary">₱ <?php echo number_format($a['allocated_amount'], 2); ?></strong>
                                    </td>
                                    <td data-label="Donation Date">
                                        <i class="bi bi-calendar me-1"></i>
                                        <?php echo date('M j, Y', strtotime($a['donation_date'])); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <!-- Summary Row -->
                            <tr class="table-light">
                                <td colspan="3" class="text-end fw-bold">Total Allocated:</td>
                                <td colspan="2" class="fw-bold text-primary">
                                    ₱ <?php echo number_format($totalAllocated, 2); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="p-3 text-center border-top">
                    <a href="disbursement.php" class="btn btn-primary-custom">
                        <i class="bi bi-arrow-left me-1"></i> Back to Disbursements
                    </a>
                </div>
            </div>
        </div>
    <?php elseif ($selected_id !== null && empty($allocations)): ?>
        <div class="card-custom mt-4">
            <div class="card-body text-center py-5">
                <i class="bi bi-database-x" style="font-size: 3rem; color: #e2e8f0;"></i>
                <h5 class="mt-3">No allocations found</h5>
                <p class="text-muted">This project doesn't have any allocated donations yet.</p>
                <a href="disbursement.php" class="btn btn-outline-custom mt-2">
                    <i class="bi bi-arrow-left me-1"></i> Go Back
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    // Form validation
    document.getElementById('disbursementForm')?.addEventListener('submit', function(e) {
        const amount = parseFloat(this.querySelector('[name="amount"]').value);
        const projectName = this.querySelector('[name="project_name"]').value.trim();

        if (amount <= 0) {
            e.preventDefault();
            showToast('Please enter a valid amount greater than 0', 'error');
            return false;
        }

        if (projectName === '') {
            e.preventDefault();
            showToast('Please enter a project name', 'error');
            return false;
        }
    });
</script>

<?php include('./template/foot.php'); ?>