<?php
include('./api_helper.php');
include('./template/navbar.php');

$jwt_token = $_SESSION['access_token'] ?? null;

if (!$jwt_token) {
    header("Location: admin_login.php");
    exit();
}

$data = api_call('http://localhost:5000/admin/donations', $jwt_token);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {

    $donation_id = filter_input(INPUT_POST, 'donation_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $status = filter_input(INPUT_POST, 'donation_status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $result = api_call('http://localhost:5000/admin/update_donation_status', $jwt_token, 'POST', [
        'donation_id' => $donation_id,
        'donation_status' => $status
    ]);

    if (isset($result['status']) && $result['status'] == 'success') {
        $_SESSION['toast_message'] = 'Donation status updated successfully!';
        $_SESSION['toast_type'] = 'success';
        echo "<script>window.location.href = 'donation.php';</script>";
        exit();
    } else {
        $_SESSION['toast_message'] = $result['message'] ?? 'Failed updating donation status!';
        $_SESSION['toast_type'] = 'error';
    }
}

$donations = is_array($data) ? $data : [];

$totalAmount = 0;
$totalDonations = count($donations);
$approvedCount = 0;
$pendingCount = 0;
$verifyingCount = 0;
$rejectedCount = 0;

foreach ($donations as $donation) {
    if ($donation['donation_status'] === 'APPROVED') {
        $totalAmount += $donation['amount'];
        $approvedCount++;
    } elseif ($donation['donation_status'] === 'PENDING') {
        $pendingCount++;
    } elseif ($donation['donation_status'] === 'VERIFYING') {
        $verifyingCount++;
    } elseif ($donation['donation_status'] === 'REJECTED') {
        $rejectedCount++;
    }
}
?>

<style>
    :root {
        --primary-color: #0057b7;
        --secondary-color: #002855;
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
        background: linear-gradient(135deg, #0057b7, #002855);
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
        border-color: #0057b7;
        box-shadow: 0 0 0 0.2rem rgba(0, 87, 183, 0.25);
    }

    .table-container {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
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
        border-color: #f1f1f1;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 87, 183, 0.05);
    }

    .badge-status {
        font-size: 0.75rem;
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 600;
    }

    .bg-PENDING {
        background: #6c757d;
        color: white;
    }

    .bg-VERIFYING {
        background: #ffc107;
        color: #212529;
    }

    .bg-APPROVED {
        background: #28a745;
        color: white;
    }

    .bg-REJECTED {
        background: #dc3545;
        color: white;
    }

    .btn-view {
        background: transparent;
        border: 1px solid #0057b7;
        color: #0057b7;
        border-radius: 6px;
        padding: 6px 12px;
        transition: var(--transition);
    }

    .btn-view:hover {
        background: #0057b7;
        color: white;
        transform: translateY(-2px);
    }

    .modal-header {
        background: linear-gradient(135deg, #0057b7, #002855);
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
        color: #0057b7;
        border: 1px solid #dee2e6;
        margin: 0 2px;
        border-radius: 6px;
    }

    .pagination .page-item.active .page-link {
        background: #0057b7;
        border-color: #0057b7;
        color: white;
    }

    .pagination .page-link:hover {
        background: rgba(0, 87, 183, 0.1);
        border-color: #0057b7;
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
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .filter-btn {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 50px;
        padding: 8px 16px;
        transition: var(--transition);
        font-weight: 500;
    }

    .filter-btn:hover,
    .filter-btn.active {
        background: #0057b7;
        color: white;
        border-color: #0057b7;
    }

    .proof-image {
        max-width: 100px;
        cursor: pointer;
        border-radius: 8px;
        transition: var(--transition);
    }

    .proof-image:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
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

        .donation-detail strong {
            width: 120px;
        }
    }
</style>

<!-- Dashboard Header -->
<div class="dashboard-header">
    <div class="container-fluid px-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h2 mb-2"><i class="bi bi-gift-fill me-2"></i>Donations Management</h1>
                <p class="mb-0 opacity-75">Manage and monitor all donation transactions</p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="btn-group">
                    <button type="button" class="btn btn-light" onclick="exportToCSV()">
                        <i class="bi bi-download me-1"></i> Export
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
                <div class="card-value">₱ <?php echo number_format($totalAmount, 2); ?></div>
                <div class="card-label">Total Donations</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="card-value"><?php echo $totalDonations; ?></div>
                <div class="card-label">Total Transactions</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="card-value"><?php echo $approvedCount; ?></div>
                <div class="card-label">Successful Payments</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="card-value"><?php echo $pendingCount + $verifyingCount; ?></div>
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
                        $badgeClass = match ($status) {
                            'PENDING' => 'bg-PENDING',
                            'VERIFYING' => 'bg-VERIFYING',
                            'APPROVED' => 'bg-APPROVED',
                            'REJECTED' => 'bg-REJECTED',
                            default => 'bg-secondary'
                        };
                        ?>
                        <tr data-status="<?php echo htmlspecialchars($status); ?>">
                            <td data-label="Donation ID">
                                <div class="fw-bold"><?php echo htmlspecialchars($donation['public_id']); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($donation['donation_id']); ?></small>
                            </td>
                            <td data-label="Amount">
                                <strong class="text-success">₱ <?php echo number_format($donation['amount'], 2); ?></strong>
                            </td>
                            <td data-label="Donor Name">
                                <div class="fw-medium"><?php echo htmlspecialchars($donation['full_name']); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($donation['email']); ?></small>
                            </td>
                            <td data-label="Status">
                                <span class="badge-status <?php echo $badgeClass; ?>">
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
        <?php if (!empty($donations) && count($donations) > 10): ?>
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
        $badgeClass = match ($status) {
            'PENDING' => 'bg-PENDING',
            'VERIFYING' => 'bg-VERIFYING',
            'APPROVED' => 'bg-APPROVED',
            'REJECTED' => 'bg-REJECTED',
            default => 'bg-secondary'
        };
        ?>

        <div class="modal fade" id="donationModal<?php echo htmlspecialchars($donation['donation_id']); ?>" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="">
                        <input type="hidden" name="donation_id" value="<?php echo htmlspecialchars($donation['donation_id']); ?>">

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
                                        <strong>Public ID:</strong>
                                        <?php echo htmlspecialchars($donation['public_id']); ?>
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
                                        <?php echo htmlspecialchars($donation['contact_number'] ?? 'N/A'); ?>
                                    </div>

                                    <div class="donation-detail">
                                        <strong>Birthday:</strong>
                                        <?php echo !empty($donation['birthday'])
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
                                            <span class="badge-status <?php echo $badgeClass; ?>">
                                                <?php echo htmlspecialchars($status); ?>
                                            </span>
                                            <small class="text-muted d-block mt-1">Status locked</small>
                                        <?php else: ?>
                                            <select name="donation_status" class="form-select form-select-sm mt-1" style="max-width: 200px;">
                                                <option value="APPROVED">APPROVED</option>
                                                <option value="REJECTED">REJECTED</option>
                                            </select>
                                        <?php endif; ?>
                                    </div>

                                    <!-- PROOF IMAGE -->
                                    <div class="donation-detail">
                                        <strong>Proof Image:</strong>
                                        <?php if (!empty($donation['proof_image'])): ?>
                                            <div class="mt-2">
                                                <img src="assets/<?php echo htmlspecialchars($donation['proof_image']); ?>"
                                                    class="proof-image"
                                                    onclick="openImage(this.src)">
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- RECEIPT -->
                                    <div class="donation-detail">
                                        <strong>Receipt:</strong>
                                        <?php if (!empty($donation['receipt_reference'])): ?>
                                            <a href="<?php echo htmlspecialchars($donation['receipt_reference']); ?>"
                                                target="_blank" class="btn btn-outline-primary btn-sm mt-1">
                                                <i class="bi bi-receipt me-1"></i> View Receipt
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="donation-detail">
                                        <strong>Donation Date:</strong>
                                        <?php echo date('F j, Y h:i A', strtotime($donation['donation_date'])); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FOOTER -->
                        <div class="modal-footer">
                            <?php if ($status !== 'APPROVED' && $status !== 'REJECTED'): ?>
                                <button type="submit" name="update_status" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i> Update Status
                                </button>
                            <?php endif; ?>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Close
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Image Viewer Modal -->
<div class="modal fade" id="imageViewer" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-white">Proof of Donation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img id="imageViewerSrc" class="img-fluid" style="max-height: 80vh; width: auto;" />
            </div>
        </div>
    </div>
</div>

<script>
    // Open image viewer
    function openImage(src) {
        document.getElementById('imageViewerSrc').setAttribute('src', src);
        new bootstrap.Modal(document.getElementById('imageViewer')).show();
    }

    // Export to CSV function
    function exportToCSV() {
        const table = document.getElementById('donationTable');
        const rows = table.querySelectorAll('tbody tr');
        let csv = [];

        // Get headers
        const headers = ['Donation ID', 'Amount', 'Donor Name', 'Status', 'Date'];
        csv.push(headers.join(','));

        // Get data from visible rows
        rows.forEach(row => {
            if (row.style.display !== 'none') {
                const rowData = [];
                const cells = row.querySelectorAll('td');
                for (let i = 0; i < 5; i++) {
                    let text = cells[i].textContent.trim();
                    // Remove extra spaces and wrap in quotes if contains comma
                    text = text.replace(/\s+/g, ' ');
                    if (text.includes(',')) text = `"${text}"`;
                    rowData.push(text);
                }
                csv.push(rowData.join(','));
            }
        });

        // Download CSV
        const blob = new Blob([csv.join('\n')], {
            type: 'text/csv'
        });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `donations_${new Date().toISOString().slice(0,19)}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);

        showToast('Export completed successfully!', 'success');
    }

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toLowerCase();
            const rows = document.querySelectorAll('#donationTable tbody tr');

            rows.forEach(row => {
                const donationId = row.querySelector('td[data-label="Donation ID"]')?.textContent.toLowerCase() || '';
                const donorName = row.querySelector('td[data-label="Donor Name"]')?.textContent.toLowerCase() || '';
                const isVisible = donationId.includes(filter) || donorName.includes(filter);
                row.style.display = isVisible ? '' : 'none';
            });

            if (typeof paginateTable === 'function') {
                paginateTable('donationTable', 'donationTablePagination', 10);
            }
        });
    }

    // Filter functionality
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
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

            if (typeof paginateTable === 'function') {
                paginateTable('donationTable', 'donationTablePagination', 10);
            }
        });
    });

    // Pagination function
    function paginateTable(tableId, paginationId, rowsPerPage = 10) {
        const table = document.getElementById(tableId);
        if (!table) return;

        const tbody = table.querySelector('tbody');
        if (!tbody) return;

        const rows = Array.from(tbody.querySelectorAll('tr')).filter(row => row.style.display !== 'none');
        const pagination = document.getElementById(paginationId);

        if (!pagination || rows.length === 0) return;

        function showPage(page) {
            const start = (page - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            rows.forEach((row, i) => {
                row.style.display = (i >= start && i < end) ? '' : 'none';
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
                const activePage = pagination.querySelector('.active');
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
                    pagination.querySelectorAll('.page-item').forEach(el => el.classList.remove('active'));
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
                const activePage = pagination.querySelector('.active');
                if (activePage && activePage.nextElementSibling) {
                    activePage.nextElementSibling.querySelector('.page-link').click();
                }
            };
            nextLi.appendChild(nextA);
            pagination.appendChild(nextLi);

            // Set first page as active
            if (pagination.children.length > 1) {
                pagination.children[1].classList.add('active');
            }
        }

        renderPagination();
        showPage(1);
    }

    // Initialize pagination on page load
    document.addEventListener('DOMContentLoaded', function() {
        const tableRows = document.querySelectorAll('#donationTable tbody tr');
        if (tableRows.length > 10 && typeof paginateTable === 'function') {
            paginateTable('donationTable', 'donationTablePagination', 10);
        }
    });
</script>

<?php include('./template/foot.php'); ?>