<?php
include('./template/head.php');

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

$donations = $data; // Decrypted data is already received from the API
?>
<input type="text" id="searchInput" class="form-control mb-3" placeholder="Search Donation...">

<div class="container">
    <h2>Manage Donations</h2>
    <table class="table table-striped" id="donationTable">
        <thead>
            <tr>
                <th>Donation ID</th>
                <th>Amount</th>
                <th>Donor Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($donations)): ?>
                <?php foreach ($donations as $donation): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($donation['xendit_payment_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>₱ <?php echo htmlspecialchars(number_format($donation['amount'], 2, '.', ','), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($donation['full_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#donationModal<?php echo htmlspecialchars($donation['donation_id'], ENT_QUOTES, 'UTF-8'); ?>" title="View">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Modal -->
                    <div class="modal fade" id="donationModal<?php echo htmlspecialchars($donation['donation_id'], ENT_QUOTES, 'UTF-8'); ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        Donation Details - <?php echo htmlspecialchars($donation['full_name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Donation ID:</strong> <?php echo htmlspecialchars($donation['donation_id'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p><strong>Donor Name:</strong> <?php echo htmlspecialchars($donation['full_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($donation['email'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($donation['contact_number'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p><strong>Birthday:</strong> <?php echo $donation['birthday'] ? htmlspecialchars(date('F j, Y', strtotime($donation['birthday'])), ENT_QUOTES, 'UTF-8') : 'N/A'; ?></p>
                                    <p><strong>Amount:</strong> ₱ <?php echo htmlspecialchars(number_format($donation['amount'], 2, '.', ','), ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p><strong>Payment Status:</strong> <?php echo htmlspecialchars($donation['payment_status'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($donation['payment_method'] ?: 'N/A', ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p><strong>Paid At:</strong> <?php echo $donation['paid_at'] ? htmlspecialchars(date('F j, Y h:i A', strtotime($donation['paid_at'])), ENT_QUOTES, 'UTF-8') : 'N/A'; ?></p>
                                    <p><strong>Receipt URL:</strong>
                                        <?php if ($donation['receipt_url']): ?>
                                            <a href="<?php echo htmlspecialchars($donation['receipt_url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank">View Receipt</a>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </p>
                                    <p><strong>Donation Date:</strong> <?php echo htmlspecialchars(date('F j, Y h:i A', strtotime($donation['donation_date'])), ENT_QUOTES, 'UTF-8'); ?></p>
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
                    <td colspan="4">No donations found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <nav>
        <ul class="pagination" id="donationTablePagination"></ul>
    </nav>
</div>

<script>
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('keyup', function() {
        const filter = searchInput.value.toLowerCase();
        const rows = document.querySelectorAll('#donationTable tbody tr');

        rows.forEach(row => {
            const recieptNumber = row.children[0].textContent.toLowerCase();
            const donorName = row.children[2].textContent.toLowerCase();

            if (recieptNumber.includes(filter) || donorName.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Pagination logic
    function paginateTable(tableId, paginationId, rowsPerPage = 10) {
        const table = document.getElementById(tableId);
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const pagination = document.getElementById(paginationId);

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