<?php
include('./template/navbar.php');

$jwt_token = $_SESSION['access_token'] ?? null;

if (!$jwt_token) {
    header("Location: admin_login.php");
    exit();
}

// =========================
// FETCH LOCATIONS
// =========================
$url = "http://localhost:5000/admin/get_locations";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    "Authorization: Bearer $jwt_token"
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
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

$locations = is_array($data) ? $data : [];

// =========================
// ADD LOCATION
// =========================
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

    $payload = json_encode([
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
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $jwt_token"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    if ($result['status'] == 'success') {
        $_SESSION['toast_message'] = 'Location added successfully!';
        $_SESSION['toast_type'] = 'success';
        echo "<script>window.location.href = 'location.php';</script>";
        exit();
    } else {
        $_SESSION['toast_message'] = $result['message'] ?? 'Failed adding location!';
        $_SESSION['toast_type'] = 'error';
    }
}

// =========================
// DELETE LOCATION
// =========================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_center'])) {
    $location_id = filter_input(INPUT_POST, 'location_id', FILTER_SANITIZE_NUMBER_INT);

    $payload = json_encode([
        'location_id' => $location_id
    ]);

    $ch = curl_init('http://localhost:5000/admin/delete_location');
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
    if ($result['status'] == 'success') {
        $_SESSION['toast_message'] = 'Location deleted successfully!';
        $_SESSION['toast_type'] = 'success';
        echo "<script>window.location.href = 'location.php';</script>";
        exit();
    } else {
        $_SESSION['toast_message'] = $result['message'] ?? 'Failed deleting location!';
        $_SESSION['toast_type'] = 'error';
    }
}

// =========================
// EDIT LOCATION
// =========================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_location'])) {
    $location_id = filter_input(INPUT_POST, 'location_id', FILTER_SANITIZE_NUMBER_INT);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $latitude = filter_input(INPUT_POST, 'latitude', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $longitude = filter_input(INPUT_POST, 'longitude', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $contact_number = filter_input(INPUT_POST, 'contact_number', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $operating_hours = filter_input(INPUT_POST, 'operating_hours', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $website_url = filter_input(INPUT_POST, 'website_url', FILTER_SANITIZE_URL);

    $payload = json_encode([
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
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $jwt_token"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    if ($result['status'] == 'success') {
        $_SESSION['toast_message'] = 'Location updated successfully!';
        $_SESSION['toast_type'] = 'success';
        echo "<script>window.location.href = 'location.php';</script>";
        exit();
    } else {
        $_SESSION['toast_message'] = $result['message'] ?? 'Failed updating location!';
        $_SESSION['toast_type'] = 'error';
    }
}

// Calculate statistics
$totalCenters = count($locations);
$uniqueTypes = [];
$contactCount = 0;
$websiteCount = 0;

foreach ($locations as $location) {
    $type = $location['type'] ?: 'General';
    if (!in_array($type, $uniqueTypes)) {
        $uniqueTypes[] = $type;
    }
    if (!empty($location['contact_number'])) $contactCount++;
    if (!empty($location['website_url'])) $websiteCount++;
}
?>

<!-- Page specific CSS -->
<style>
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3a0ca3;
        --accent-color: #4cc9f0;
        --success-color: #28a745;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
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

    .badge-type {
        font-size: 0.75rem;
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 600;
        background: rgba(0, 87, 183, 0.1);
        color: #0057b7;
    }

    .btn-action {
        border-radius: 6px;
        padding: 6px 12px;
        transition: var(--transition);
        font-size: 0.875rem;
        margin: 2px;
    }

    .btn-view {
        background: rgba(13, 110, 253, 0.1);
        border: 1px solid #0d6efd;
        color: #0d6efd;
    }

    .btn-view:hover {
        background: #0d6efd;
        color: white;
        transform: translateY(-2px);
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

    .location-info {
        display: flex;
        align-items: center;
    }

    .location-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0057b7, #002855);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        margin-right: 12px;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .coordinates {
        font-size: 0.75rem;
        color: #6c757d;
        margin-top: 2px;
    }

    .contact-info {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .contact-info div {
        margin-bottom: 4px;
    }

    .map-link {
        color: #0057b7;
        text-decoration: none;
        font-size: 0.875rem;
    }

    .map-link:hover {
        text-decoration: underline;
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
            color: #212529;
        }

        .location-info {
            justify-content: space-between;
        }
    }
</style>

<!-- Dashboard Header -->
<div class="dashboard-header">
    <div class="container-fluid px-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h2 mb-2"><i class="bi bi-geo-alt-fill me-2"></i>Donation Centers</h1>
                <p class="mb-0 opacity-75">Manage donation center locations and information</p>
            </div>
            <div class="col-md-4 text-md-end">
                <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addLocationModal">
                    <i class="bi bi-plus-circle me-2"></i> Add Center
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
                <div class="card-value"><?php echo $totalCenters; ?></div>
                <div class="card-label">Total Centers</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="card-value"><?php echo count($uniqueTypes); ?></div>
                <div class="card-label">Center Types</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="card-value"><?php echo $contactCount; ?></div>
                <div class="card-label">With Contact Info</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="card-value"><?php echo $websiteCount; ?></div>
                <div class="card-label">With Websites</div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="search-wrapper">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" class="form-control" placeholder="Search by Location Name, Address, or Type...">
            </div>
        </div>
        <div class="col-md-4">
            <div class="filter-buttons">
                <button class="filter-btn active" data-filter="all">All</button>
                <?php foreach ($uniqueTypes as $type): ?>
                    <button class="filter-btn" data-filter="<?php echo htmlspecialchars($type); ?>">
                        <?php echo htmlspecialchars($type); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Locations Table -->
    <div class="table-container">
        <table class="table table-hover align-middle mb-0" id="locationTable">
            <thead>
                <tr>
                    <th>Location</th>
                    <th>Contact Information</th>
                    <th>Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($locations)): ?>
                    <?php foreach ($locations as $location): ?>
                        <tr data-type="<?php echo htmlspecialchars($location['type'] ?: 'General'); ?>">
                            <td data-label="Location">
                                <div class="location-info">
                                    <div class="location-icon">
                                        <i class="bi bi-geo-alt"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold"><?php echo htmlspecialchars($location['name']); ?></div>
                                        <div class="text-muted small"><?php echo htmlspecialchars($location['address']); ?></div>
                                        <div class="coordinates">
                                            <i class="bi bi-geo me-1"></i>
                                            <?php echo htmlspecialchars($location['latitude']); ?>, <?php echo htmlspecialchars($location['longitude']); ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Contact Information">
                                <div class="contact-info">
                                    <?php if (!empty($location['contact_number'])): ?>
                                        <div><i class="bi bi-telephone me-2"></i><?php echo htmlspecialchars($location['contact_number']); ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($location['email'])): ?>
                                        <div><i class="bi bi-envelope me-2"></i><?php echo htmlspecialchars($location['email']); ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($location['operating_hours'])): ?>
                                        <div><i class="bi bi-clock me-2"></i><?php echo htmlspecialchars($location['operating_hours']); ?></div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td data-label="Type">
                                <span class="badge-type">
                                    <i class="bi bi-tag me-1"></i>
                                    <?php echo htmlspecialchars($location['type'] ?: 'General'); ?>
                                </span>
                            </td>
                            <td data-label="Actions">
                                <button class="btn btn-action btn-view" data-bs-toggle="modal"
                                    data-bs-target="#locationModal<?php echo $location['id']; ?>" title="View Details">
                                    <i class="bi bi-eye me-1"></i> View
                                </button>
                                <button class="btn btn-action btn-edit" data-bs-toggle="modal"
                                    data-bs-target="#editLocation<?php echo $location['id']; ?>" title="Edit Location">
                                    <i class="bi bi-pencil me-1"></i> Edit
                                </button>
                                <form method="POST" style="display:inline;" onsubmit="return confirmDelete()">
                                    <input type="hidden" name="location_id" value="<?php echo $location['id']; ?>">
                                    <button type="submit" name="delete_center" class="btn btn-action btn-delete" title="Delete Location">
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
                                <i class="bi bi-geo-alt"></i>
                                <h4>No donation centers found</h4>
                                <p>There are no donation centers to display at the moment.</p>
                                <button class="btn btn-add mt-2" data-bs-toggle="modal" data-bs-target="#addLocationModal">
                                    <i class="bi bi-plus-circle me-2"></i> Add Your First Center
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php if (!empty($locations) && count($locations) > 10): ?>
            <div class="p-3 border-top">
                <nav>
                    <ul class="pagination justify-content-center mb-0" id="locationTablePagination"></ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Location Modal -->
<div class="modal fade" id="addLocationModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add New Donation Center</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="addLocationForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Location Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Type</label>
                            <input type="text" class="form-control" name="type" placeholder="e.g., Blood Bank, Food Bank">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Address *</label>
                            <input type="text" class="form-control" name="address" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Latitude *</label>
                            <input type="text" class="form-control" name="latitude" required placeholder="e.g., 14.5995">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Longitude *</label>
                            <input type="text" class="form-control" name="longitude" required placeholder="e.g., 120.9842">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Contact Number</label>
                            <input type="text" class="form-control" name="contact_number">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Operating Hours</label>
                            <input type="text" class="form-control" name="operating_hours" placeholder="e.g., Mon-Fri 9AM-5PM">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Website URL</label>
                            <input type="url" class="form-control" name="website_url" placeholder="https://example.com">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_location" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i> Add Center
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Location Modals -->
<?php if (!empty($locations)): ?>
    <?php foreach ($locations as $location): ?>
        <div class="modal fade" id="locationModal<?php echo $location['id']; ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>Location Details</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <h6 class="fw-bold text-primary mb-3"><?php echo htmlspecialchars($location['name']); ?></h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <strong>Address:</strong><br>
                                    <?php echo htmlspecialchars($location['address']); ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Coordinates:</strong><br>
                                    <a href="https://maps.google.com/?q=<?php echo urlencode($location['latitude'] . ',' . $location['longitude']); ?>"
                                        target="_blank" class="map-link">
                                        <i class="bi bi-geo-alt me-1"></i>
                                        <?php echo htmlspecialchars($location['latitude']); ?>, <?php echo htmlspecialchars($location['longitude']); ?>
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <strong>Type:</strong><br>
                                    <span class="badge-type">
                                        <?php echo htmlspecialchars($location['type'] ?: 'General'); ?>
                                    </span>
                                </div>
                                <?php if (!empty($location['contact_number'])): ?>
                                    <div class="col-md-6">
                                        <strong>Contact:</strong><br>
                                        <i class="bi bi-telephone me-1"></i> <?php echo htmlspecialchars($location['contact_number']); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($location['email'])): ?>
                                    <div class="col-md-6">
                                        <strong>Email:</strong><br>
                                        <i class="bi bi-envelope me-1"></i> <?php echo htmlspecialchars($location['email']); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($location['operating_hours'])): ?>
                                    <div class="col-12">
                                        <strong>Operating Hours:</strong><br>
                                        <i class="bi bi-clock me-1"></i> <?php echo htmlspecialchars($location['operating_hours']); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($location['website_url'])): ?>
                                    <div class="col-12">
                                        <strong>Website:</strong><br>
                                        <a href="<?php echo htmlspecialchars($location['website_url']); ?>" target="_blank" class="map-link">
                                            <i class="bi bi-globe me-1"></i>
                                            <?php echo htmlspecialchars($location['website_url']); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Location Modal -->
        <div class="modal fade" id="editLocation<?php echo $location['id']; ?>" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Donation Center</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Location Name *</label>
                                    <input type="text" class="form-control" name="name"
                                        value="<?php echo htmlspecialchars($location['name']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Type</label>
                                    <input type="text" class="form-control" name="type"
                                        value="<?php echo htmlspecialchars($location['type']); ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-medium">Address *</label>
                                    <input type="text" class="form-control" name="address"
                                        value="<?php echo htmlspecialchars($location['address']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Latitude *</label>
                                    <input type="text" class="form-control" name="latitude"
                                        value="<?php echo htmlspecialchars($location['latitude']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Longitude *</label>
                                    <input type="text" class="form-control" name="longitude"
                                        value="<?php echo htmlspecialchars($location['longitude']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Contact Number</label>
                                    <input type="text" class="form-control" name="contact_number"
                                        value="<?php echo htmlspecialchars($location['contact_number']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Email</label>
                                    <input type="email" class="form-control" name="email"
                                        value="<?php echo htmlspecialchars($location['email']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Operating Hours</label>
                                    <input type="text" class="form-control" name="operating_hours"
                                        value="<?php echo htmlspecialchars($location['operating_hours']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Website URL</label>
                                    <input type="url" class="form-control" name="website_url"
                                        value="<?php echo htmlspecialchars($location['website_url']); ?>">
                                </div>
                            </div>
                            <input type="hidden" name="location_id" value="<?php echo $location['id']; ?>">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="edit_location" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i> Update Center
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<script>
    // Confirm delete function
    function confirmDelete() {
        return confirm('Are you sure you want to delete this donation center? This action cannot be undone.');
    }

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toLowerCase();
            const rows = document.querySelectorAll('#locationTable tbody tr');

            let visibleCount = 0;

            rows.forEach(row => {
                const locationText = row.querySelector('td[data-label="Location"]')?.textContent.toLowerCase() || '';
                const contactText = row.querySelector('td[data-label="Contact Information"]')?.textContent.toLowerCase() || '';
                const typeText = row.querySelector('td[data-label="Type"]')?.textContent.toLowerCase() || '';

                const isVisible = locationText.includes(filter) || contactText.includes(filter) || typeText.includes(filter);
                row.style.display = isVisible ? '' : 'none';
                if (isVisible) visibleCount++;
            });

            // Update pagination after filtering
            if (visibleCount > 0 && typeof paginateTable === 'function') {
                paginateTable('locationTable', 'locationTablePagination', 10);
            }
        });
    }

    // Filter functionality
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const filter = this.getAttribute('data-filter');
            const rows = document.querySelectorAll('#locationTable tbody tr');

            rows.forEach(row => {
                if (filter === 'all') {
                    row.style.display = '';
                } else {
                    const type = row.getAttribute('data-type');
                    row.style.display = type === filter ? '' : 'none';
                }
            });

            if (typeof paginateTable === 'function') {
                paginateTable('locationTable', 'locationTablePagination', 10);
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
        const tableRows = document.querySelectorAll('#locationTable tbody tr');
        if (tableRows.length > 10 && typeof paginateTable === 'function') {
            paginateTable('locationTable', 'locationTablePagination', 10);
        }
    });
</script>

<?php include('./template/foot.php'); ?>