<?php
include('./template/head.php');

$jwt_token = $_SESSION['jwt_token'];

$url = "http://localhost:5000/admin/donations";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "get-token: $jwt_token"
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if (isset($data['status']) && $data['status'] === 'expire') {
    session_destroy();
    header('Location: admin_login.php');
    exit();
}

$donations = $data;
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<style>
    .search-wrapper {
        position: relative;
        max-width: 400px;
        margin-bottom: 20px;
    }
    .search-wrapper .bi-search {
        position: absolute;
        top: 50%;
        left: 12px;
        transform: translateY(-50%);
        color: #6c757d;
    }
    #searchInput {
        padding-left: 40px;
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    .modal-header {
        background-color: #f1f1f1;
    }
    .badge-status {
        font-size: 0.85rem;
        padding: 6px 10px;
    }
</style>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-gift-fill text-primary"></i> Manage Donations</h2>
    </div>

    <!-- Search Bar -->
    <div class="search-wrapper">
        <i class="bi bi-search"></i>
        <input type="text" id="searchInput" class="form-control" placeholder="Search by Donation ID or Donor Name...">
    </div>

    <!-- Donations Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-striped table-hover align-middle" id="donationTable">
                <thead class="table-light">
                    <tr>
                        <th>Donation ID</th>
                        <th>Amount</th>
                        <th>Donor Name</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($donations)): ?>
                        <?php foreach ($donations as $donation): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($donation['xendit_payment_id']); ?></td>
                                <td><strong>₱ <?php echo number_format($donation['amount'], 2, '.', ','); ?></strong></td>
                                <td><?php echo htmlspecialchars($donation['full_name']); ?></td>
                                <td>
                                    <span class="badge badge-status bg-<?php
                                        echo $donation['payment_status'] === 'PAID' ? 'success' :
                                            ($donation['payment_status'] === 'PENDING' ? 'warning text-dark' : 'secondary');
                                    ?>">
                                        <?php echo htmlspecialchars($donation['payment_status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#donationModal<?php echo htmlspecialchars($donation['donation_id']); ?>"
                                            title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Donation Details Modal -->
                            <div class="modal fade" id="donationModal<?php echo htmlspecialchars($donation['donation_id']); ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"><i class="bi bi-info-circle-fill text-primary"></i> Donation Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <p><strong>Donation ID:</strong> <?php echo htmlspecialchars($donation['donation_id']); ?></p>
                                                    <p><strong>Donor Name:</strong> <?php echo htmlspecialchars($donation['full_name']); ?></p>
                                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($donation['email']); ?></p>
                                                    <p><strong>Contact:</strong> <?php echo htmlspecialchars($donation['contact_number']); ?></p>
                                                    <p><strong>Birthday:</strong>
                                                        <?php echo $donation['birthday'] ? htmlspecialchars(date('F j, Y', strtotime($donation['birthday']))) : 'N/A'; ?>
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Amount:</strong> ₱ <?php echo number_format($donation['amount'], 2); ?></p>
                                                    <p><strong>Status:</strong> <?php echo htmlspecialchars($donation['payment_status']); ?></p>
                                                    <p><strong>Payment Method:</strong> <?php echo $donation['payment_method'] ?: 'N/A'; ?></p>
                                                    <p><strong>Paid At:</strong>
                                                        <?php echo $donation['paid_at'] ? htmlspecialchars(date('F j, Y h:i A', strtotime($donation['paid_at']))) : 'N/A'; ?>
                                                    </p>
                                                    <p><strong>Receipt:</strong>
                                                        <?php if ($donation['receipt_url']): ?>
                                                            <a href="<?php echo htmlspecialchars($donation['receipt_url']); ?>" target="_blank">View Receipt</a>
                                                        <?php else: ?> N/A <?php endif; ?>
                                                    </p>
                                                    <p><strong>Date Donated:</strong> <?php echo date('F j, Y h:i A', strtotime($donation['donation_date'])); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No donations found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <nav>
                <ul class="pagination justify-content-center mt-3" id="donationTablePagination"></ul>
            </nav>
        </div>
    </div>
</div>

<script>
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('keyup', function() {
        const filter = searchInput.value.toLowerCase();
        const rows = document.querySelectorAll('#donationTable tbody tr');

        rows.forEach(row => {
            const donationId = row.children[0].textContent.toLowerCase();
            const donorName = row.children[2].textContent.toLowerCase();

            row.style.display = (donationId.includes(filter) || donorName.includes(filter)) ? '' : 'none';
        });
    });

    function paginateTable(tableId, paginationId, rowsPerPage = 10) {
        const table = document.getElementById(tableId);
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const pagination = document.getElementById(paginationId);

        function showPage(page) {
            const start = (page - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            rows.forEach((row, i) => row.style.display = (i >= start && i < end) ? '' : 'none');
        }

        function renderPagination() {
            pagination.innerHTML = '';
            const pageCount = Math.ceil(rows.length / rowsPerPage);
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
            if (pagination.children.length > 0) pagination.children[0].classList.add('active');
        }

        renderPagination();
        showPage(1);
    }

    document.addEventListener('DOMContentLoaded', function() {
        paginateTable('donationTable', 'donationTablePagination', 10);
    });
</script>

<?php include('./template/foot.php'); ?>
