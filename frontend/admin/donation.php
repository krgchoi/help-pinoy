<?php
include('./template/navbar.php');

$jwt_token = $_SESSION['access_token'];

$url = "http://localhost:5000/admin/donations";
$ch = curl_init($url);
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
    header('Location: admin_login.php');
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {

    $donation_id = filter_input(INPUT_POST, 'donation_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $status = filter_input(INPUT_POST, 'donation_status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $data = json_encode([
        'donation_id' => $donation_id,
        'donation_status' => $status
    ]);

    $ch = curl_init('http://localhost:5000/admin/update_donation_status');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $jwt_token"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    if (isset($result['status']) && $result['status'] == 'success') {
        echo "<script>alert('Donation status updated successfully!'); window.location.href = 'donation.php';</script>";
        exit();
    } else {
        echo "<script>alert('Failed updating donation status!');</script>";
    }
}



$donations = $data;


?>

<title>Donations Management | Help Pinoy Admin</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
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
        --border-radius: 12px;
        --box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        --transition: all 0.3s ease;
    }

    .dashboard-header {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 0 0 var(--border-radius) var(--border-radius);
    }

    .stats-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: var(--transition);
        border-left: 4px solid var(--primary-color);
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
    }

    .stats-card .card-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 0.5rem;
    }

    .stats-card .card-label {
        color: #6c757d;
        font-weight: 500;
    }

    .search-wrapper {
        position: relative;
        max-width: 400px;
        margin-bottom: 1.5rem;
    }

    .search-wrapper .bi-search {
        position: absolute;
        top: 50%;
        left: 15px;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 3;
    }

    #searchInput {
        padding-left: 45px;
        border-radius: 50px;
        border: 2px solid #e9ecef;
        transition: var(--transition);
    }

    #searchInput:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
    }

    .table-container {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        overflow: hidden;
    }

    .table thead {
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
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
        border-color: #f1f1f1;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(67, 97, 238, 0.05);
        transform: scale(1.01);
        transition: var(--transition);
    }

    .badge-status {
        font-size: 0.75rem;
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 600;
    }

    .btn-view {
        background: transparent;
        border: 1px solid var(--primary-color);
        color: var(--primary-color);
        border-radius: 6px;
        padding: 6px 12px;
        transition: var(--transition);
    }

    .btn-view:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-2px);
    }

    .modal-header {
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        color: white;
    }

    .modal-content {
        border-radius: var(--border-radius);
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .donation-detail {
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #f1f1f1;
    }

    .donation-detail:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .donation-detail strong {
        color: var(--dark-color);
        display: inline-block;
        width: 140px;
    }

    .pagination .page-link {
        color: var(--primary-color);
        border: 1px solid #dee2e6;
        margin: 0 2px;
        border-radius: 6px;
    }

    .pagination .page-item.active .page-link {
        background: var(--primary-color);
        border-color: var(--primary-color);
    }

    .pagination .page-link:hover {
        background: rgba(67, 97, 238, 0.1);
        border-color: var(--primary-color);
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        color: #dee2e6;
    }

    .filter-buttons {
        margin-bottom: 1.5rem;
    }

    .filter-btn {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 50px;
        padding: 8px 16px;
        margin-right: 8px;
        margin-bottom: 8px;
        transition: var(--transition);
        font-weight: 500;
    }

    .filter-btn:hover,
    .filter-btn.active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
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
            border: 1px solid #dee2e6;
            border-radius: var(--border-radius);
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
            color: var(--dark-color);
        }
    }
</style>
</head>

<body>
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="h2 mb-2"><i class="bi bi-gift-fill me-2"></i>Donations Management</h1>
                    <p class="mb-0 opacity-75">Manage and monitor all donation transactions</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="btn-group">
                        <button type="button" class="btn btn-light">
                            <i class="bi bi-download me-1"></i> Export
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Stats Overview -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="card-value">₱ <?php
                                                $totalAmount = 0;
                                                if (!empty($donations)) {
                                                    foreach ($donations as $donation) {
                                                        if ($donation['donation_status'] === 'APPROVED') {
                                                            $totalAmount += $donation['amount'];
                                                        }
                                                    }
                                                }
                                                echo number_format($totalAmount, 2);
                                                ?></div>
                    <div class="card-label">Total Donations</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="card-value"><?php
                                            $totalDonations = !empty($donations) ? count($donations) : 0;
                                            echo $totalDonations;
                                            ?></div>
                    <div class="card-label">Total Transactions</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="card-value"><?php
                                            $paidCount = 0;
                                            if (!empty($donations)) {
                                                foreach ($donations as $donation) {
                                                    if ($donation['donation_status'] === 'APPROVED') {
                                                        $paidCount++;
                                                    }
                                                }
                                            }
                                            echo $paidCount;
                                            ?></div>
                    <div class="card-label">Successful Payments</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="card-value"><?php
                                            $pendingCount = 0;
                                            if (!empty($donations)) {
                                                foreach ($donations as $donation) {
                                                    if ($donation['donation_status'] === 'PENDING') {
                                                        $pendingCount++;
                                                    }
                                                }
                                            }
                                            echo $pendingCount;
                                            ?></div>
                    <div class="card-label">Pending Payments</div>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="search-wrapper">
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchInput" class="form-control" placeholder="Search by Donation ID, Donor Name, or Email...">
                </div>
            </div>
            <div class="col-md-4">
                <div class="filter-buttons">
                    <button class="filter-btn active" data-filter="all">All</button>
                    <button class="filter-btn" data-filter="PENDING">Pending</button>
                    <button class="filter-btn" data-filter="VERIFYING">Verifying</button>
                    <button class="filter-btn" data-filter="APPROVED">Approved</button>
                    <button class="filter-btn" data-filter="REJECTED">Rejected</button>
                </div>
            </div>
        </div>

        <!-- Donations Table -->
        <div class="table-container">
            <table class="table table-hover align-middle mb-0" id="donationTable">
                <thead>
                    <tr>
                        <th>Donation ID</th>
                        <th>Amount</th>
                        <th>Donor Name</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($donations)): ?>
                        <?php foreach ($donations as $donation): ?>
                            <?php
                            $status = $donation['donation_status'];

                            $badge = match ($status) {
                                'PENDING' => 'secondary',
                                'VERIFYING' => 'warning text-dark',
                                'APPROVED' => 'success',
                                'REJECTED' => 'danger',
                                default => 'secondary'
                            };
                            ?>
                            <tr data-status="<?php echo htmlspecialchars($status); ?>">
                                <td data-label="Donation ID">
                                    <div class="fw-bold"><?php echo htmlspecialchars($donation['public_id']); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($donation['donation_id']); ?></small>
                                </td>
                                <td data-label="Amount">
                                    <strong class="text-success">₱ <?php echo number_format($donation['amount'], 2, '.', ','); ?></strong>
                                </td>
                                <td data-label="Donor Name">
                                    <div class="fw-medium"><?php echo htmlspecialchars($donation['full_name']); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($donation['email']); ?></small>
                                </td>
                                <td data-label="Status">
                                    <span class="badge-status bg-<?php echo $badge; ?>">
                                        <?php echo htmlspecialchars($status); ?>
                                    </span>
                                </td>
                                <td data-label="Date">
                                    <?php echo date('M j, Y', strtotime($donation['donation_date'])); ?>
                                </td>
                                <td data-label="Actions">
                                    <button class="btn btn-view btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#donationModal<?php echo htmlspecialchars($donation['donation_id']); ?>"
                                        title="View Details">
                                        <i class="bi bi-eye me-1"></i> View
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <h4>No donations found</h4>
                                    <p>There are no donation records to display at the moment.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php if (!empty($donations)): ?>
                <div class="p-3 border-top">
                    <nav>
                        <ul class="pagination justify-content-center mb-0" id="donationTablePagination"></ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Donation Details Modals -->
    <?php if (!empty($donations)): ?>
        <?php foreach ($donations as $donation): ?>
            <?php
            $status = $donation['donation_status'];

            $badge = match ($status) {
                'PENDING' => 'secondary',
                'VERIFYING' => 'warning text-dark',
                'APPROVED' => 'success',
                'REJECTED' => 'danger',
                default => 'secondary'
            };
            ?>

            <div class="modal fade" id="donationModal<?php echo htmlspecialchars($donation['donation_id']); ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                        <form method="POST" action="">
                            <input type="hidden" name="donation_id" value="<?php echo $donation['donation_id']; ?>">

                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="bi bi-info-circle me-2"></i>Donation Details
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <div class="row">

                                    <!-- LEFT SIDE -->
                                    <div class="col-md-6">
                                        <h6 class="mb-3 text-primary">Donor Information</h6>

                                        <div class="donation-detail">
                                            <strong>Donation ID:</strong>
                                            <?php echo htmlspecialchars($donation['donation_id']); ?>
                                        </div>

                                        <div class="donation-detail">
                                            <strong>Donor Name:</strong>
                                            <?php echo htmlspecialchars($donation['full_name']); ?>
                                        </div>

                                        <div class="donation-detail">
                                            <strong>Email:</strong>
                                            <?php echo htmlspecialchars($donation['email']); ?>
                                        </div>

                                        <div class="donation-detail">
                                            <strong>Contact:</strong>
                                            <?php echo htmlspecialchars($donation['contact_number']); ?>
                                        </div>

                                        <div class="donation-detail">
                                            <strong>Birthday:</strong>
                                            <?php echo $donation['birthday']
                                                ? htmlspecialchars(date('F j, Y', strtotime($donation['birthday'])))
                                                : 'N/A'; ?>
                                        </div>
                                    </div>

                                    <!-- RIGHT SIDE -->
                                    <div class="col-md-6">
                                        <h6 class="mb-3 text-primary">Payment Information</h6>

                                        <div class="donation-detail">
                                            <strong>Amount:</strong>
                                            ₱ <?php echo number_format($donation['amount'], 2); ?>
                                        </div>

                                        <!-- STATUS -->
                                        <div class="donation-detail">
                                            <strong>Status:</strong>

                                            <?php if ($status === 'APPROVED' || $status === 'REJECTED'): ?>
                                                <!-- LOCKED FINAL STATES -->
                                                <span class="badge-status bg-<?php echo $badge; ?>">
                                                    <?php echo htmlspecialchars($status); ?>
                                                </span>
                                                <small class="text-muted d-block mt-1">Status locked</small>

                                            <?php else: ?>
                                                <!-- ONLY FINAL DECISION OPTIONS -->
                                                <select name="donation_status" class="form-select form-select-sm mt-1">

                                                    <!-- default is current status -->
                                                    <option value="APPROVED" <?php echo ($status === 'APPROVED') ? 'selected' : ''; ?>>
                                                        APPROVED
                                                    </option>

                                                    <option value="REJECTED" <?php echo ($status === 'REJECTED') ? 'selected' : ''; ?>>
                                                        REJECTED
                                                    </option>

                                                </select>
                                            <?php endif; ?>
                                        </div>

                                        <!-- PROOF -->
                                        <div class="donation-detail">
                                            <strong>Proof Image:</strong>
                                            <?php if (!empty($donation['proof_image'])): ?>
                                                <img src="assets/<?php echo htmlspecialchars($donation['proof_image']); ?>"
                                                    style="max-width:100px; cursor:pointer;"
                                                    onclick="openImage(this)">
                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </div>

                                        <div class="donation-detail">
                                            <strong>Donation Date:</strong>
                                            <?php echo date('F j, Y h:i A', strtotime($donation['donation_date'])); ?>
                                        </div>

                                        <div class="donation-detail">
                                            <strong>Receipt:</strong>
                                            <?php if ($donation['receipt_reference']): ?>
                                                <a href="<?php echo htmlspecialchars($donation['receipt_reference']); ?>"
                                                    target="_blank"
                                                    class="btn btn-outline-primary btn-sm ms-2">
                                                    <i class="bi bi-receipt me-1"></i> View Receipt
                                                </a>
                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </div>

                                        <div class="donation-detail">
                                            <strong>Date Donated:</strong>
                                            <?php echo date('F j, Y h:i A', strtotime($donation['donation_date'])); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- FOOTER -->
                            <div class="modal-footer">

                                <?php if ($status !== 'APPROVED' && $status !== 'REJECTED'): ?>
                                    <button type="submit"
                                        name="update_status"
                                        class="btn btn-primary">
                                        Save
                                    </button>
                                <?php endif; ?>

                                <button type="button"
                                    class="btn btn-secondary"
                                    data-bs-dismiss="modal">
                                    Close
                                </button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>

        <?php endforeach; ?>
    <?php endif; ?>

    <div class="modal fade" id="imageViewer" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-body text-center p-0">
                    <img id="imageViewerSrc" class="img-fluid" />
                </div>
            </div>
        </div>
    </div>
    <script>
        function openImage(img) {
            const src = img.getAttribute('src');
            document.getElementById('imageViewerSrc').setAttribute('src', src);
            new bootstrap.Modal(document.getElementById('imageViewer')).show();
        }
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toLowerCase();
            const rows = document.querySelectorAll('#donationTable tbody tr');

            let visibleCount = 0;

            rows.forEach(row => {
                const donationId = row.children[0].textContent.toLowerCase();
                const donorName = row.children[2].textContent.toLowerCase();
                const email = row.querySelector('small.text-muted') ? row.querySelector('small.text-muted').textContent.toLowerCase() : '';

                const isVisible = donationId.includes(filter) || donorName.includes(filter) || email.includes(filter);
                row.style.display = isVisible ? '' : 'none';

                if (isVisible) visibleCount++;
            });

            // Update pagination after filtering
            if (visibleCount > 0) {
                paginateTable('donationTable', 'donationTablePagination', 10);
            }
        });

        // Filter functionality
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Update active state
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const filter = this.getAttribute('data-filter');
                const rows = document.querySelectorAll('#donationTable tbody tr');

                rows.forEach(row => {
                    if (filter === 'all') {
                        row.style.display = '';
                    } else {
                        const status = row.getAttribute('data-status');
                        row.style.display = status === filter ? '' : 'none';
                    }
                });

                // Update pagination after filtering
                paginateTable('donationTable', 'donationTablePagination', 10);
            });
        });

        // Pagination function
        function paginateTable(tableId, paginationId, rowsPerPage = 10) {
            const table = document.getElementById(tableId);
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr')).filter(row => row.style.display !== 'none');
            const pagination = document.getElementById(paginationId);

            if (rows.length === 0) return;

            function showPage(page) {
                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;

                // Hide all rows first
                document.querySelectorAll(`#${tableId} tbody tr`).forEach(row => {
                    row.style.display = 'none';
                });

                // Show only rows for current page
                rows.forEach((row, i) => {
                    if (i >= start && i < end) {
                        row.style.display = '';
                    }
                });
            }

            function renderPagination() {
                pagination.innerHTML = '';
                const pageCount = Math.ceil(rows.length / rowsPerPage);

                if (pageCount <= 1) return;

                // Previous button
                const prevLi = document.createElement('li');
                prevLi.className = 'page-item';
                const prevA = document.createElement('a');
                prevA.className = 'page-link';
                prevA.href = '#';
                prevA.innerHTML = '&laquo;';
                prevA.onclick = function(e) {
                    e.preventDefault();
                    const activePage = document.querySelector('.pagination .active');
                    if (activePage && activePage.previousElementSibling) {
                        activePage.previousElementSibling.querySelector('.page-link').click();
                    }
                };
                prevLi.appendChild(prevA);
                pagination.appendChild(prevLi);

                // Page numbers
                for (let i = 1; i <= pageCount; i++) {
                    const li = document.createElement('li');
                    li.className = 'page-item';
                    const a = document.createElement('a');
                    a.className = 'page-link';
                    a.href = '#';
                    a.textContent = i;
                    a.onclick = function(e) {
                        e.preventDefault();
                        showPage(i);
                        Array.from(pagination.children).forEach(el => el.classList.remove('active'));
                        li.classList.add('active');
                    };
                    li.appendChild(a);
                    pagination.appendChild(li);
                }

                // Next button
                const nextLi = document.createElement('li');
                nextLi.className = 'page-item';
                const nextA = document.createElement('a');
                nextA.className = 'page-link';
                nextA.href = '#';
                nextA.innerHTML = '&raquo;';
                nextA.onclick = function(e) {
                    e.preventDefault();
                    const activePage = document.querySelector('.pagination .active');
                    if (activePage && activePage.nextElementSibling) {
                        activePage.nextElementSibling.querySelector('.page-link').click();
                    }
                };
                nextLi.appendChild(nextA);
                pagination.appendChild(nextLi);

                if (pagination.children.length > 0) pagination.children[1].classList.add('active');
            }

            renderPagination();
            showPage(1);
        }

        // Initialize pagination on page load
        document.addEventListener('DOMContentLoaded', function() {
            paginateTable('donationTable', 'donationTablePagination', 10);
        });
    </script>

    <?php include('./template/foot.php'); ?>