<?php
include('./template/navbar.php');


$jwt_token = $_SESSION['access_token'];

$url = "http://localhost:5000/admin/news";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    "Authorization: Bearer $jwt_token"
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

#add
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_news'])) {
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $author = filter_input(INPUT_POST, 'author', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $date = filter_input(INPUT_POST, 'published_at', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $summary = filter_input(INPUT_POST, 'summary', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $meta_title = filter_input(INPUT_POST, 'meta_title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $meta_description = filter_input(INPUT_POST, 'meta_description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $slug = filter_input(INPUT_POST, 'slug', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Handle image upload
    $image_url = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $ch = curl_init('http://localhost:5000/admin/upload_news_image');
        $cfile = new CURLFile($_FILES['image']['tmp_name'], $_FILES['image']['type'], $_FILES['image']['name']);
        $postfields = ['image' => $cfile];
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["get-token: $jwt_token"]);
        $img_response = curl_exec($ch);
        curl_close($ch);
        $img_data = json_decode($img_response, true);
        if (isset($img_data['status']) && $img_data['status'] === 'success') {
            $image_url = $img_data['filename'];
        }
    }

    $data = json_encode([
        'title' => $title,
        'content' => $content,
        'author' => $author,
        'published_at' => $date,
        'category' => $category,
        'summary' => $summary,
        'meta_title' => $meta_title,
        'meta_description' => $meta_description,
        'slug' => $slug,
        'image_url' => $image_url
    ]);

    $ch = curl_init('http://localhost:5000/admin/add_news');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "get-token: $jwt_token"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $flask_data = json_decode($response, true);
    if ($flask_data['status'] == 'success') {
        echo "<script>alert('News added successfully!'); window.location.href = 'news.php';</script>";
        exit();
    } else {
        echo "<script>alert('Failed adding news!');</script>";
    }
}

#delete
if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['delete_news'])) {
    $news_id = filter_input(INPUT_POST, 'news_id', FILTER_SANITIZE_NUMBER_INT);

    $data = json_encode([
        'news_id' => $news_id
    ]);

    $ch = curl_init('http://localhost:5000/admin/delete_news');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "get-token: $jwt_token"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $flask_data = json_decode($response, true);
    if ($flask_data['status'] === 'success') {
        echo "<script> alert('News Deleted'); window.location.href ='news.php'</script>";
        exit();
    } else {
        echo "<script> alert('Failed to Delete')</script>";
    }
}

#update
if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['update_news'])) {
    $news_id = filter_input(INPUT_POST, 'news_id', FILTER_SANITIZE_NUMBER_INT);
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $author = filter_input(INPUT_POST, 'author', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $summary = filter_input(INPUT_POST, 'summary', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $meta_title = filter_input(INPUT_POST, 'meta_title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $meta_description = filter_input(INPUT_POST, 'meta_description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $slug = filter_input(INPUT_POST, 'slug', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Get the current image_url from the hidden input
    $current_image_url = isset($_POST['current_image_url']) ? $_POST['current_image_url'] : '';

    $image_url = $current_image_url;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $ch = curl_init('http://localhost:5000/admin/upload_news_image');
        $cfile = new CURLFile($_FILES['image']['tmp_name'], $_FILES['image']['type'], $_FILES['image']['name']);
        $postfields = ['image' => $cfile];
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["get-token: $jwt_token"]);
        $img_response = curl_exec($ch);
        curl_close($ch);
        $img_data = json_decode($img_response, true);
        if (isset($img_data['status']) && $img_data['status'] === 'success') {
            $image_url = $img_data['filename'];
        }
    }

    $data = json_encode([
        'news_id' => $news_id,
        'title' => $title,
        'content' => $content,
        'author' => $author,
        'category' => $category,
        'summary' => $summary,
        'meta_title' => $meta_title,
        'meta_description' => $meta_description,
        'slug' => $slug,
        'image_url' => $image_url
    ]);

    $ch = curl_init('http://localhost:5000/admin/edit_news');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "get-token: $jwt_token"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $flask_data = json_decode($response, true);
    if (isset($flask_data['status']) && $flask_data['status'] === 'success') {
        echo "<script>alert('News updated successfully!'); window.location.href = 'news.php';</script>";
        exit();
    } else {
        echo "<script>alert('Failed to update news!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Management | Help Pinoy Admin</title>
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

        .badge-category {
            font-size: 0.75rem;
            padding: 6px 12px;
            border-radius: 50px;
            font-weight: 600;
        }

        .btn-action {
            border-radius: 6px;
            padding: 6px 12px;
            transition: var(--transition);
            font-size: 0.875rem;
        }

        .btn-edit {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid var(--warning-color);
            color: var(--warning-color);
        }

        .btn-edit:hover {
            background: var(--warning-color);
            color: white;
            transform: translateY(-2px);
        }

        .btn-delete {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid var(--danger-color);
            color: var(--danger-color);
        }

        .btn-delete:hover {
            background: var(--danger-color);
            color: white;
            transform: translateY(-2px);
        }

        .btn-add {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border: none;
            color: white;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
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

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 10px 15px;
            transition: var(--transition);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
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

        .news-image {
            width: 60px;
            height: 40px;
            object-fit: cover;
            border-radius: 6px;
        }

        .image-upload-container {
            border: 2px dashed #dee2e6;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            text-align: center;
            transition: var(--transition);
            background: #f8f9fa;
        }

        .image-upload-container:hover {
            border-color: var(--primary-color);
            background: rgba(67, 97, 238, 0.05);
        }

        .image-preview {
            max-width: 100%;
            max-height: 200px;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
        }

        .character-count {
            font-size: 0.75rem;
            color: #6c757d;
            text-align: right;
            margin-top: 0.25rem;
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
                    <h1 class="h2 mb-2"><i class="bi bi-newspaper me-2"></i>News Management</h1>
                    <p class="mb-0 opacity-75">Manage news articles and publications</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addNewsModal">
                        <i class="bi bi-plus-circle me-2"></i> Add News
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Stats Overview -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="card-value"><?php echo !empty($data) ? count($data) : 0; ?></div>
                    <div class="card-label">Total Articles</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="card-value"><?php
                                            $categoryCount = [];
                                            if (!empty($data)) {
                                                foreach ($data as $news) {
                                                    $category = $news['category'];
                                                    if (!isset($categoryCount[$category])) {
                                                        $categoryCount[$category] = 0;
                                                    }
                                                    $categoryCount[$category]++;
                                                }
                                            }
                                            echo count($categoryCount);
                                            ?></div>
                    <div class="card-label">Categories</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="card-value"><?php
                                            $authorCount = [];
                                            if (!empty($data)) {
                                                foreach ($data as $news) {
                                                    $author = $news['author'];
                                                    if (!isset($authorCount[$author])) {
                                                        $authorCount[$author] = 0;
                                                    }
                                                    $authorCount[$author]++;
                                                }
                                            }
                                            echo count($authorCount);
                                            ?></div>
                    <div class="card-label">Authors</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="card-value"><?php
                                            $recentCount = 0;
                                            if (!empty($data)) {
                                                $oneWeekAgo = date('Y-m-d', strtotime('-1 week'));
                                                foreach ($data as $news) {
                                                    if ($news['published_at'] >= $oneWeekAgo) {
                                                        $recentCount++;
                                                    }
                                                }
                                            }
                                            echo $recentCount;
                                            ?></div>
                    <div class="card-label">Last 7 Days</div>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="search-wrapper">
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchInput" class="form-control" placeholder="Search by Title, Author, Category, or Content...">
                </div>
            </div>
            <div class="col-md-4">
                <div class="filter-buttons">
                    <button class="filter-btn active" data-filter="all">All</button>
                    <?php
                    $categories = [];
                    if (!empty($data)) {
                        foreach ($data as $news) {
                            $category = $news['category'];
                            if (!in_array($category, $categories)) {
                                $categories[] = $category;
                                echo '<button class="filter-btn" data-filter="' . htmlspecialchars($category) . '">' . htmlspecialchars($category) . '</button>';
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- News Table -->
        <div class="table-container">
            <table class="table table-hover align-middle mb-0" id="newsTable">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Publish Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data)): ?>
                        <?php foreach ($data as $news): ?>
                            <tr data-category="<?php echo htmlspecialchars($news['category']); ?>">
                                <td data-label="Image">
                                    <?php if (!empty($news['image_url'])): ?>
                                        <img src="http://localhost:5000/static/news_img/<?php echo htmlspecialchars($news['image_url']); ?>"
                                            alt="News Image" class="news-image">
                                    <?php else: ?>
                                        <div class="news-image bg-light d-flex align-items-center justify-content-center">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Title">
                                    <div class="fw-bold"><?php echo htmlspecialchars($news['title']); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($news['summary']); ?></small>
                                </td>
                                <td data-label="Author">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person-circle me-2 text-primary"></i>
                                        <?php echo htmlspecialchars($news['author']); ?>
                                    </div>
                                </td>
                                <td data-label="Category">
                                    <span class="badge-category bg-info text-dark">
                                        <i class="bi bi-tag me-1"></i>
                                        <?php echo htmlspecialchars($news['category']); ?>
                                    </span>
                                </td>
                                <td data-label="Publish Date">
                                    <div class="text-nowrap">
                                        <i class="bi bi-calendar-event me-1 text-muted"></i>
                                        <?php echo htmlspecialchars($news['published_at']); ?>
                                    </div>
                                </td>
                                <td data-label="Actions">
                                    <button class="btn btn-action btn-edit me-2" data-bs-toggle="modal"
                                        data-bs-target="#editNews<?php echo $news['news_id']; ?>" title="Edit News">
                                        <i class="bi bi-pencil me-1"></i> Edit
                                    </button>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="news_id" value="<?php echo htmlspecialchars($news['news_id']); ?>">
                                        <button type="submit" name="delete_news" class="btn btn-action btn-delete"
                                            onclick="return confirm('Are you sure you want to delete this news article? This action cannot be undone.')"
                                            title="Delete News">
                                            <i class="bi bi-trash me-1"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="bi bi-newspaper"></i>
                                    <h4>No news articles found</h4>
                                    <p>There are no news articles to display at the moment.</p>
                                    <button class="btn btn-add mt-2" data-bs-toggle="modal" data-bs-target="#addNewsModal">
                                        <i class="bi bi-plus-circle me-2"></i> Add Your First Article
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php if (!empty($data)): ?>
                <div class="p-3 border-top">
                    <nav>
                        <ul class="pagination justify-content-center mb-0" id="newsTablePagination"></ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add News Modal -->
    <div class="modal fade" id="addNewsModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add New Article</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data" id="addNewsForm">
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Image Upload -->
                            <div class="col-md-4">
                                <div class="image-upload-container">
                                    <img src="default.jpg" alt="News Image" class="image-preview" id="previewImageAdd">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">Article Image</label>
                                        <input type="file" name="image" accept="image/*" class="form-control"
                                            onchange="previewImageGeneric(this, 'previewImageAdd')">
                                    </div>
                                    <small class="text-muted">Recommended: 800x450px, JPG/PNG format</small>
                                </div>
                            </div>

                            <!-- Basic Info -->
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-medium">Title</label>
                                        <input type="text" class="form-control" name="title" required
                                            placeholder="Enter article title">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-medium">Author</label>
                                        <input type="text" class="form-control" name="author" required
                                            placeholder="Enter author name">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-medium">Category</label>
                                        <input type="text" class="form-control" name="category" required
                                            placeholder="Enter category">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-medium">Publish Date</label>
                                        <input type="date" class="form-control" name="published_at" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SEO Fields -->
                        <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Meta Title</label>
                                <input type="text" class="form-control" name="meta_title" required
                                    placeholder="Enter meta title for SEO">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Slug</label>
                                <input type="text" class="form-control" name="slug" required
                                    placeholder="URL-friendly slug">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-medium">Meta Description</label>
                                <input type="text" class="form-control" name="meta_description" maxlength="160" required
                                    placeholder="Brief description for search engines">
                                <div class="character-count">0/160 characters</div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-medium">Summary</label>
                                <input type="text" class="form-control" name="summary" maxlength="160" required
                                    placeholder="Brief summary of the article">
                                <div class="character-count">0/160 characters</div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="mb-3">
                            <label class="form-label fw-medium">Content</label>
                            <textarea class="form-control" name="content" rows="8" required
                                placeholder="Write your article content here..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_news" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i> Publish Article
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit News Modals -->
    <?php if (!empty($data)): ?>
        <?php foreach ($data as $news): ?>
            <div class="modal fade" id="editNews<?php echo htmlspecialchars($news['news_id'], ENT_QUOTES, 'utf-8'); ?>" tabindex="-1">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Article</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="row g-3">
                                    <!-- Image Upload -->
                                    <div class="col-md-4">
                                        <div class="image-upload-container">
                                            <img src="<?php echo !empty($news['image_url']) ? 'http://localhost:5000/static/news_img/' . htmlspecialchars($news['image_url']) : 'default.jpg'; ?>"
                                                alt="News Image" class="image-preview" id="previewImageEdit<?php echo $news['news_id']; ?>">
                                            <div class="mb-3">
                                                <label class="form-label fw-medium">Article Image</label>
                                                <input type="file" name="image" accept="image/*" class="form-control"
                                                    onchange="previewImageGeneric(this, 'previewImageEdit<?php echo $news['news_id']; ?>')">
                                            </div>
                                            <small class="text-muted">Leave empty to keep current image</small>
                                        </div>
                                    </div>

                                    <!-- Basic Info -->
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-medium">Title</label>
                                                <input type="text" class="form-control" name="title"
                                                    value="<?php echo htmlspecialchars($news['title'], ENT_QUOTES, 'utf-8'); ?>" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-medium">Author</label>
                                                <input type="text" class="form-control" name="author"
                                                    value="<?php echo htmlspecialchars($news['author'], ENT_QUOTES, 'utf-8'); ?>" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-medium">Category</label>
                                                <input type="text" class="form-control" name="category"
                                                    value="<?php echo htmlspecialchars($news['category'], ENT_QUOTES, 'utf-8'); ?>" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-medium">Publish Date</label>
                                                <input type="date" class="form-control" name="published_at"
                                                    value="<?php echo htmlspecialchars($news['published_at'], ENT_QUOTES, 'utf-8'); ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- SEO Fields -->
                                <div class="row mt-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-medium">Meta Title</label>
                                        <input type="text" class="form-control" name="meta_title"
                                            value="<?php echo htmlspecialchars($news['meta_title'], ENT_QUOTES, 'utf-8'); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-medium">Slug</label>
                                        <input type="text" class="form-control" name="slug"
                                            value="<?php echo htmlspecialchars($news['slug'], ENT_QUOTES, 'utf-8'); ?>" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-medium">Meta Description</label>
                                        <input type="text" class="form-control" name="meta_description" maxlength="160"
                                            value="<?php echo htmlspecialchars($news['meta_description'], ENT_QUOTES, 'utf-8'); ?>" required>
                                        <div class="character-count"><?php echo strlen($news['meta_description']); ?>/160 characters</div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-medium">Summary</label>
                                        <input type="text" class="form-control" name="summary" maxlength="160"
                                            value="<?php echo htmlspecialchars($news['summary'], ENT_QUOTES, 'utf-8'); ?>" required>
                                        <div class="character-count"><?php echo strlen($news['summary']); ?>/160 characters</div>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Content</label>
                                    <textarea class="form-control" name="content" rows="8" required><?php echo htmlspecialchars($news['content'], ENT_QUOTES, 'utf-8') ?></textarea>
                                </div>

                                <input type="hidden" name="news_id" value="<?php echo htmlspecialchars($news['news_id'], ENT_QUOTES, 'utf-8'); ?>">
                                <input type="hidden" name="current_image_url" value="<?php echo htmlspecialchars($news['image_url'] ?? '', ENT_QUOTES, 'utf-8'); ?>">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="update_news" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i> Update Article
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <script>
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toLowerCase();
            const rows = document.querySelectorAll('#newsTable tbody tr');

            let visibleCount = 0;

            rows.forEach(row => {
                const title = row.children[1].textContent.toLowerCase();
                const author = row.children[2].textContent.toLowerCase();
                const category = row.children[3].textContent.toLowerCase();

                const isVisible = title.includes(filter) || author.includes(filter) || category.includes(filter);
                row.style.display = isVisible ? '' : 'none';

                if (isVisible) visibleCount++;
            });

            // Update pagination after filtering
            if (visibleCount > 0) {
                paginateTable('newsTable', 'newsTablePagination', 10);
            }
        });

        // Filter functionality
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Update active state
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const filter = this.getAttribute('data-filter');
                const rows = document.querySelectorAll('#newsTable tbody tr');

                rows.forEach(row => {
                    if (filter === 'all') {
                        row.style.display = '';
                    } else {
                        const category = row.getAttribute('data-category');
                        row.style.display = category === filter ? '' : 'none';
                    }
                });

                // Update pagination after filtering
                paginateTable('newsTable', 'newsTablePagination', 10);
            });
        });

        // Character count for text inputs
        document.querySelectorAll('input[maxlength="160"]').forEach(input => {
            const counter = input.nextElementSibling;
            if (counter && counter.classList.contains('character-count')) {
                // Update on input
                input.addEventListener('input', function() {
                    counter.textContent = this.value.length + '/160 characters';
                });

                // Initialize count
                counter.textContent = input.value.length + '/160 characters';
            }
        });

        // Image preview function
        function previewImageGeneric(input, previewId) {
            const preview = document.getElementById(previewId);
            const file = input.files[0];
            const reader = new FileReader();

            reader.onload = e => {
                preview.src = e.target.result;
            };

            if (file) {
                reader.readAsDataURL(file);
            }
        }

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
            paginateTable('newsTable', 'newsTablePagination', 10);
        });
    </script>

    <?php include('./template/foot.php'); ?>
</body>

</html>