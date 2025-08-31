<?php
include('./template/head.php');

$url = "http://localhost:5000/admin/get_locations";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    "get-token: $jwt_token"
]);


$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
if ($data === null) {
    session_destroy();
    header('Location: admin_login.php');
    exit();
}

if (isset($data['status']) && $data['status'] === 'expire') {
    session_destroy();
    header('Location: admin_login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_location'])) {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $latitude = filter_input(INPUT_POST, 'latitude', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $longitude = filter_input(INPUT_POST, 'longitude', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $contact_number = filter_input(INPUT_POST, 'contact_number', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $operating_hours = filter_input(INPUT_POST, 'operating_hours', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $website_url = filter_input(INPUT_POST, 'website_url', FILTER_SANITIZE_URL);

    $data = json_encode([
        'name' => $name,
        'address' => $address,
        'latitude' => $latitude,
        'longitude' => $longitude,
        'contact_number' => $contact_number,
        'email' => $email,
        'operating_hours' => $operating_hours,
        'type' => $type,
        'website_url' => $website_url
    ]);

    $ch = curl_init('http://localhost:5000/admin/add_location');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "get-token: $jwt_token"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    if ($data['status'] == 'success') {
        echo "<script>alert('Location added successfully!'); window.location.href = 'location.php';</script>";
        exit();
    } else {
        echo "<script>alert('Fail adding location!');</script>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_center'])) {
    $location_id = filter_input(INPUT_POST, 'location_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $data = json_encode([
        'location_id' => $location_id
    ]);

    $ch = curl_init('http://localhost:5000/admin/delete_location');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "get-token: $jwt_token"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    if ($data['status'] == 'success') {
        echo "<script>alert('Location deleted successfully!'); window.location.href = 'location.php';</script>";
        exit();
    } else {
        echo "<script>alert('Fail deleting location!');</script>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_location'])) {
    $location_id = filter_input(INPUT_POST, 'location_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $latitude = filter_input(INPUT_POST, 'latitude', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $longitude = filter_input(INPUT_POST, 'longitude', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $contact_number = filter_input(INPUT_POST, 'contact_number', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $operating_hours = filter_input(INPUT_POST, 'operating_hours', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $website_url = filter_input(INPUT_POST, 'website_url', FILTER_SANITIZE_URL);

    $data = json_encode([
        'location_id' => $location_id,
        'name' => $name,
        'address' => $address,
        'latitude' => $latitude,
        'longitude' => $longitude,
        'contact_number' => $contact_number,
        'email' => $email,
        'operating_hours' => $operating_hours,
        'type' => $type,
        'website_url' => $website_url
    ]);

    $ch = curl_init('http://localhost:5000/admin/edit_location');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "get-token: $jwt_token"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    if ($data['status'] == 'success') {
        echo "<script>alert('Location updated successfully!'); window.location.href = 'location.php';</script>";
        exit();
    } else {
        echo "<script>alert('Fail updating location!');</script>";
    }
}
?>
<input type="text" id="searchInput" class="form-control mb-3" placeholder="Search by Location Name or Address...">
<div class="container">
    <h2>Manage Donation Centers</h2>
    <a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addcenters" title="Add">
        <i class="bi bi-plus"></i>
    </a>

    <table class="table table-striped" id="locationTable">
        <thead>
            <tr>
                <th>Location Name</th>
                <th>Address</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data)): ?>
                <?php foreach ($data as $location): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($location['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($location['address'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#locationModal<?php echo $location['id']; ?>" title="View Details">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editCenter<?php echo $location['id']; ?>" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="location_id" value="<?php echo htmlspecialchars($location['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                <button type="submit" name="delete_center" class="btn btn-danger btn-sm" onclick="return confirm('Delete Center?')" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- View Details Modal -->
                    <div class="modal fade" id="locationModal<?php echo $location['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Location Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Name:</strong> <?php echo htmlspecialchars($location['name'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p><strong>Address:</strong> <?php echo htmlspecialchars($location['address'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($location['contact_number'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($location['email'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p><strong>Operating Hours:</strong> <?php echo htmlspecialchars($location['operating_hours'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p><strong>Type:</strong> <?php echo htmlspecialchars($location['type'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p><strong>Website URL:</strong>
                                        <?php if (!empty($location['website_url'])): ?>
                                            <a href="<?php echo htmlspecialchars($location['website_url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank">Visit Website</a>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </p>
                                    <p><strong>Latitude:</strong> <?php echo htmlspecialchars($location['latitude'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p><strong>Longitude:</strong> <?php echo htmlspecialchars($location['longitude'], ENT_QUOTES, 'UTF-8'); ?></p>
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
                    <td colspan="3">No data found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <nav>
        <ul class="pagination" id="locationTablePagination"></ul>
    </nav>
</div>

<!-- Add Center Modal -->
<div class="modal fade" id="addcenters" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Center</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="locationName" class="form-label">Location Name</label>
                        <input type="text" class="form-control" id="locationName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                    </div>
                    <div class="mb-3">
                        <label for="latitude" class="form-label">Latitude</label>
                        <input type="text" class="form-control" id="latitude" name="latitude" required>
                    </div>
                    <div class="mb-3">
                        <label for="longitude" class="form-label">Longitude</label>
                        <input type="text" class="form-control" id="longitude" name="longitude" required>
                    </div>
                    <div class="mb-3">
                        <label for="contactNumber" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" id="contactNumber" name="contact_number">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="operatingHours" class="form-label">Operating Hours</label>
                        <input type="text" class="form-control" id="operatingHours" name="operating_hours">
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <input type="text" class="form-control" id="type" name="type">
                    </div>
                    <div class="mb-3">
                        <label for="websiteUrl" class="form-label">Website URL</label>
                        <input type="url" class="form-control" id="websiteUrl" name="website_url">
                    </div>
                    <button type="submit" name="add_location" class="btn btn-primary">
                        <i class="bi bi-plus"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Center Modal -->
<?php foreach ($data as $location): ?>
    <div class="modal fade" id="editCenter<?php echo $location['id']; ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Center</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="location_id" value="<?php echo htmlspecialchars($location['id'], ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="mb-3">
                            <label for="locationName" class="form-label">Location Name</label>
                            <input type="text" class="form-control" id="locationName" name="name" value="<?php echo htmlspecialchars($location['name'], ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($location['address'], ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="latitude" class="form-label">Latitude</label>
                            <input type="text" class="form-control" id="latitude" name="latitude" value="<?php echo htmlspecialchars($location['latitude'], ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="longitude" class="form-label">Longitude</label>
                            <input type="text" class="form-control" id="longitude" name="longitude" value="<?php echo htmlspecialchars($location['longitude'], ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="contactNumber" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="contactNumber" name="contact_number" value="<?php echo htmlspecialchars($location['contact_number'], ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($location['email'], ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="operatingHours" class="form-label">Operating Hours</label>
                            <input type="text" class="form-control" id="operatingHours" name="operating_hours" value="<?php echo htmlspecialchars($location['operating_hours'], ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Type</label>
                            <input type="text" class="form-control" id="type" name="type" value="<?php echo htmlspecialchars($location['type'], ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="websiteUrl" class="form-label">Website URL</label>
                            <input type="url" class="form-control" id="websiteUrl" name="website_url" value="<?php echo htmlspecialchars($location['website_url'], ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <button type="submit" name="edit_location" class="btn btn-primary">
                            <i class="bi bi-pencil"></i>
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
        const rows = document.querySelectorAll('#locationTable tbody tr');

        rows.forEach(row => {
            const name = row.children[0].textContent.toLowerCase();
            const address = row.children[1].textContent.toLowerCase();

            if (name.includes(filter) || address.includes(filter)) {
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
        paginateTable('locationTable', 'locationTablePagination', 10);
    });
</script>

<?php include('./template/foot.php'); ?>