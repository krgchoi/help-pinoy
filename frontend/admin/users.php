<?php
include('./template/navbar.php');

$jwt_token = $_SESSION['access_token'] ?? null;

if (!$jwt_token) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);

    $payload = json_encode(['user_id' => $user_id]);
    $ch = curl_init('http://localhost:5000/admin/delete_user');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: Bearer $jwt_token"
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    if ($result['status'] === 'success') {
        $_SESSION['toast_message'] = 'User deleted successfully!';
        $_SESSION['toast_type'] = 'success';
    } else {
        $_SESSION['toast_message'] = $result['message'] ?? 'Failed to delete user!';
        $_SESSION['toast_type'] = 'error';
    }
    echo "<script>window.location.href = 'users.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $payload = json_encode([
        'user_id' => $user_id,
        'name' => $name,
        'email' => $email,
        'role' => $role
    ]);
    $ch = curl_init('http://localhost:5000/admin/edit_user');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: Bearer $jwt_token"
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    if ($result['status'] === 'success') {
        $_SESSION['toast_message'] = 'User updated successfully!';
        $_SESSION['toast_type'] = 'success';
    } else {
        $_SESSION['toast_message'] = $result['message'] ?? 'Failed to update user!';
        $_SESSION['toast_type'] = 'error';
    }
    echo "<script>window.location.href = 'users.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password']; // Don't sanitize password
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $payload = json_encode([
        'name' => $name,
        'email' => $email,
        'password' => $password,
        'role' => $role
    ]);
    $ch = curl_init('http://localhost:5000/admin/add_user');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: Bearer $jwt_token"
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    if ($result['status'] === 'success') {
        $_SESSION['toast_message'] = 'User added successfully!';
        $_SESSION['toast_type'] = 'success';
    } else {
        $_SESSION['toast_message'] = $result['message'] ?? 'Failed to add user!';
        $_SESSION['toast_type'] = 'error';
    }
    echo "<script>window.location.href = 'users.php';</script>";
    exit();
}

$url = "http://localhost:5000/admin/get_users";
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

$users = is_array($data) ? $data : [];

$totalUsers = count($users);
$adminCount = 0;
$regularUserCount = 0;
$donorCount = 0;

foreach ($users as $user) {
    if ($user['role'] === 'Admin') {
        $adminCount++;
    } elseif ($user['role'] === 'User') {
        $regularUserCount++;
    } elseif ($user['role'] === 'Donor') {
        $donorCount++;
    }
}
?>

<!-- Page specific CSS -->
<style>
    :root {
        --primary-color: #0057b7;
        --secondary-color: #002855;
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
        color: #64748b;
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

    .badge-role {
        font-size: 0.75rem;
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 600;
    }

    .bg-Admin {
        background: #dc3545;
        color: white;
    }

    .bg-User {
        background: #0d6efd;
        color: white;
    }

    .bg-Donor {
        background: #198754;
        color: white;
    }

    .btn-action {
        border-radius: 6px;
        padding: 6px 12px;
        transition: var(--transition);
        font-size: 0.875rem;
        margin: 2px;
    }

    .btn-edit {
        background: rgba(255, 193, 7, 0.1);
        border: 1px solid #ffc107;
        color: #ffc107;
    }

    .btn-edit:hover {
        background: #ffc107;
        color: white;
        transform: translateY(-2px);
    }

    .btn-delete {
        background: rgba(220, 53, 69, 0.1);
        border: 1px solid #dc3545;
        color: #dc3545;
    }

    .btn-delete:hover {
        background: #dc3545;
        color: white;
        transform: translateY(-2px);
    }

    .btn-add {
        background: linear-gradient(135deg, #0057b7, #002855);
        border: none;
        color: white;
        border-radius: 50px;
        padding: 10px 20px;
        font-weight: 600;
        transition: var(--transition);
    }

    .btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 87, 183, 0.3);
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

    .form-control,
    .form-select {
        border-radius: 8px;
        border: 2px solid #e9ecef;
        padding: 10px 15px;
        transition: var(--transition);
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #0057b7;
        box-shadow: 0 0 0 0.2rem rgba(0, 87, 183, 0.25);
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
        color: #64748b;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        color: #e2e8f0;
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

    .user-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0057b7, #002855);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1.1rem;
        margin-right: 12px;
        flex-shrink: 0;
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
            color: #1e293b;
        }

        .user-avatar {
            margin-right: 0;
            margin-bottom: 0;
        }
    }
</style>

<!-- Dashboard Header -->
<div class="dashboard-header">
    <div class="container-fluid px-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h2 mb-2"><i class="bi bi-people-fill me-2"></i>Users Management</h1>
                <p class="mb-0 opacity-75">Manage user accounts and permissions</p>
            </div>
            <div class="col-md-4 text-md-end">
                <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addUser">
                    <i class="bi bi-person-plus me-2"></i> Add New User
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4">
    <!-- Stats Overview -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="card-value"><?php echo $totalUsers; ?></div>
                <div class="card-label">Total Users</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="card-value"><?php echo $adminCount; ?></div>
                <div class="card-label">Administrators</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="card-value"><?php echo $regularUserCount; ?></div>
                <div class="card-label">Regular Users</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="card-value"><?php echo $donorCount; ?></div>
                <div class="card-label">Donors</div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="search-wrapper">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" class="form-control" placeholder="Search by Name, Email, or Role...">
            </div>
        </div>
        <div class="col-md-4">
            <div class="filter-buttons">
                <button class="filter-btn active" data-filter="all">All</button>
                <button class="filter-btn" data-filter="Admin">Admin</button>
                <button class="filter-btn" data-filter="User">User</button>
                <button class="filter-btn" data-filter="Donor">Donor</button>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="table-container">
        <table class="table table-hover align-middle mb-0" id="userTable">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr data-role="<?php echo htmlspecialchars($user['role']); ?>">
                            <td data-label="User">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar">
                                        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold"><?php echo htmlspecialchars($user['name']); ?></div>
                                        <small class="text-muted">ID: <?php echo $user['id']; ?></small>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Email">
                                <i class="bi bi-envelope me-1 text-muted"></i>
                                <?php echo htmlspecialchars($user['email']); ?>
                            </td>
                            <td data-label="Role">
                                <span class="badge-role bg-<?php echo $user['role']; ?>">
                                    <i class="bi bi-<?php echo $user['role'] === 'Admin' ? 'shield-check' : ($user['role'] === 'User' ? 'person' : 'heart'); ?> me-1"></i>
                                    <?php echo htmlspecialchars($user['role']); ?>
                                </span>
                            </td>
                            <td data-label="Actions">
                                <button class="btn btn-action btn-edit" data-bs-toggle="modal"
                                    data-bs-target="#editUser<?php echo $user['id']; ?>" title="Edit User">
                                    <i class="bi bi-pencil me-1"></i> Edit
                                </button>
                                <form method="POST" style="display:inline;" onsubmit="return confirmDelete()">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" name="delete_user" class="btn btn-action btn-delete" title="Delete User">
                                        <i class="bi bi-trash me-1"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <i class="bi bi-people"></i>
                                <h4>No users found</h4>
                                <p>There are no user accounts to display at the moment.</p>
                                <button class="btn btn-add mt-2" data-bs-toggle="modal" data-bs-target="#addUser">
                                    <i class="bi bi-person-plus me-2"></i> Add Your First User
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php if (!empty($users) && count($users) > 10): ?>
            <div class="p-3 border-top">
                <nav>
                    <ul class="pagination justify-content-center mb-0" id="userTablePagination"></ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUser" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Add New User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="addUserForm">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Full Name *</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Email Address *</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Password *</label>
                        <input type="password" class="form-control" name="password" required minlength="8">
                        <div class="form-text">Password must be at least 8 characters long.</div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-medium">Role *</label>
                        <select class="form-select" name="role" required>
                            <option value="">Select a role</option>
                            <option value="Donor">Donor</option>
                            <option value="User">User</option>
                            <option value="Admin">Administrator</option>
                        </select>
                    </div>
                    <button type="submit" name="add_user" class="btn btn-primary w-100 py-2 fw-medium">
                        <i class="bi bi-person-plus me-2"></i> Create User Account
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modals -->
<?php if (!empty($users)): ?>
    <?php foreach ($users as $user): ?>
        <div class="modal fade" id="editUser<?php echo $user['id']; ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit User</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <div class="mb-3">
                                <label class="form-label fw-medium">Full Name *</label>
                                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium">Email Address *</label>
                                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-medium">Role *</label>
                                <select class="form-select" name="role" required>
                                    <option value="Donor" <?php echo ($user['role'] == 'Donor') ? 'selected' : ''; ?>>Donor</option>
                                    <option value="User" <?php echo ($user['role'] == 'User') ? 'selected' : ''; ?>>User</option>
                                    <option value="Admin" <?php echo ($user['role'] == 'Admin') ? 'selected' : ''; ?>>Administrator</option>
                                </select>
                            </div>
                            <button type="submit" name="edit_user" class="btn btn-warning w-100 py-2 fw-medium">
                                <i class="bi bi-pencil me-2"></i> Update User
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<script>
    // Confirm delete function
    function confirmDelete() {
        return confirm('Are you sure you want to delete this user? This action cannot be undone.');
    }

    // Form validation for add user
    document.getElementById('addUserForm')?.addEventListener('submit', function(e) {
        const password = this.querySelector('input[name="password"]').value;
        if (password.length < 8) {
            e.preventDefault();
            showToast('Password must be at least 8 characters long.', 'error');
            return false;
        }
    });

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toLowerCase();
            const rows = document.querySelectorAll('#userTable tbody tr');

            rows.forEach(row => {
                const name = row.querySelector('td[data-label="User"]')?.textContent.toLowerCase() || '';
                const email = row.querySelector('td[data-label="Email"]')?.textContent.toLowerCase() || '';
                const role = row.querySelector('td[data-label="Role"]')?.textContent.toLowerCase() || '';

                const isVisible = name.includes(filter) || email.includes(filter) || role.includes(filter);
                row.style.display = isVisible ? '' : 'none';
            });

            if (typeof paginateTable === 'function') {
                paginateTable('userTable', 'userTablePagination', 10);
            }
        });
    }

    // Filter functionality
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const filter = this.getAttribute('data-filter');
            const rows = document.querySelectorAll('#userTable tbody tr');

            rows.forEach(row => {
                if (filter === 'all') {
                    row.style.display = '';
                } else {
                    const role = row.getAttribute('data-role');
                    row.style.display = role === filter ? '' : 'none';
                }
            });

            if (typeof paginateTable === 'function') {
                paginateTable('userTable', 'userTablePagination', 10);
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

            if (pagination.children.length > 1) {
                pagination.children[1].classList.add('active');
            }
        }

        renderPagination();
        showPage(1);
    }

    // Initialize pagination on page load
    document.addEventListener('DOMContentLoaded', function() {
        const tableRows = document.querySelectorAll('#userTable tbody tr');
        if (tableRows.length > 10 && typeof paginateTable === 'function') {
            paginateTable('userTable', 'userTablePagination', 10);
        }
    });
</script>

<?php include('./template/foot.php'); ?>