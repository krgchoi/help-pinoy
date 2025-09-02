<?php
include('./template/head.php');

// Handle delete user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);

    $data = json_encode(['user_id' => $user_id]);
    $ch = curl_init('http://localhost:5000/admin/delete_user');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "get-token: $jwt_token"
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    echo "<script>alert('" . ($result['status'] === 'success' ? "User deleted successfully!" : "Failed to delete user!") . "'); window.location.href = 'users.php';</script>";
    exit();
}

// Handle edit user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $data = json_encode([
        'user_id' => $user_id,
        'name' => $name,
        'email' => $email,
        'role' => $role
    ]);
    $ch = curl_init('http://localhost:5000/admin/edit_user');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "get-token: $jwt_token"
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    echo "<script>alert('" . ($result['status'] === 'success' ? "User updated successfully!" : "Failed to update user!") . "'); window.location.href = 'users.php';</script>";
    exit();
}

// Fetch users
$url = "http://localhost:5000/admin/get_users";
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
    .badge-role {
        font-size: 0.85rem;
        padding: 6px 10px;
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-people-fill text-primary"></i> Manage Users</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUser">
            <i class="bi bi-person-plus"></i> Add New User
        </button>
    </div>

    <!-- Search -->
    <div class="search-wrapper">
        <i class="bi bi-search"></i>
        <input type="text" id="searchInput" class="form-control" placeholder="Search by Name, Email, or Role...">
    </div>

    <!-- Users Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-striped table-hover align-middle" id="userTable">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data)): ?>
                        <?php foreach ($data as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="badge badge-role bg-<?php
                                        echo $user['role'] === 'Admin' ? 'danger' :
                                            ($user['role'] === 'User' ? 'primary' : 'success');
                                    ?>">
                                        <?php echo htmlspecialchars($user['role']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editUser<?php echo $user['id']; ?>" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                                        <button type="submit" name="delete_user" class="btn btn-danger btn-sm" onclick="return confirm('Delete User?')" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <nav>
                <ul class="pagination justify-content-center mt-3" id="userTablePagination"></ul>
            </nav>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUser" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="bi bi-person-plus text-primary"></i> Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" name="role" required>
                            <option value="Donor">Donor</option>
                            <option value="User">User</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" name="add_user" class="btn btn-primary w-100">
                        <i class="bi bi-plus-lg"></i> Add User
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modals -->
<?php foreach ($data as $user): ?>
    <div class="modal fade" id="editUser<?php echo $user['id']; ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title"><i class="bi bi-pencil-square text-warning"></i> Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select" name="role" required>
                                <option value="Donor" <?php echo ($user['role'] == 'Donor') ? 'selected' : ''; ?>>Donor</option>
                                <option value="User" <?php echo ($user['role'] == 'User') ? 'selected' : ''; ?>>User</option>
                            </select>
                        </div>
                        <button type="submit" name="edit_user" class="btn btn-warning w-100">
                            <i class="bi bi-pencil"></i> Update User
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script>
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('keyup', function() {
        const filter = searchInput.value.toLowerCase();
        const rows = document.querySelectorAll('#userTable tbody tr');

        rows.forEach(row => {
            const name = row.children[0].textContent.toLowerCase();
            const email = row.children[1].textContent.toLowerCase();
            const role = row.children[2].textContent.toLowerCase();

            row.style.display = (name.includes(filter) || email.includes(filter) || role.includes(filter)) ? '' : 'none';
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
        paginateTable('userTable', 'userTablePagination', 10);
    });
</script>

<?php include('./template/foot.php'); ?>
