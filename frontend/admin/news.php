<?php
include('./template/head.php');


$url = "http://localhost:5000/admin/news";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
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
        echo "<script>alert('news added successfully!'); window.location.href = 'news.php';</script>";
        exit();
    } else {
        echo "<script>alert('Fail adding news!');</script>";
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
        echo "<script> alert('Fail to Delete')</script>";
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
    .badge-category {
        font-size: 0.85rem;
        padding: 6px 10px;
    }
</style>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-newspaper text-primary"></i> Manage News</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addnews">
            <i class="bi bi-plus-circle"></i> Add News
        </button>
    </div>

    <!-- Search -->
    <div class="search-wrapper">
        <i class="bi bi-search"></i>
        <input type="text" id="searchInput" class="form-control" placeholder="Search by Title, Author, or Category...">
    </div>

    <!-- News Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-striped table-hover align-middle" id="newsTable">
                <thead class="table-light">
                    <tr>
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
                            <tr>
                                <td><?php echo htmlspecialchars($news['title']); ?></td>
                                <td><?php echo htmlspecialchars($news['author']); ?></td>
                                <td>
                                    <span class="badge badge-category bg-info text-dark">
                                        <?php echo htmlspecialchars($news['category']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($news['published_at']); ?></td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editnews<?php echo $news['news_id']; ?>" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="news_id" value="<?php echo htmlspecialchars($news['news_id']); ?>">
                                        <button type="submit" name="delete_news" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Delete News?')" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No news found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <nav>
                <ul class="pagination justify-content-center mt-3" id="newsTablePagination"></ul>
            </nav>
        </div>
    </div>
</div>

<!-- add news -->
<div class="modal fade" id="addnews" tabindex="-1" aria-labelledby="addnewsLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="addnewsLabel">Add News</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4 text-center">
                            <label class="form-label">Image</label><br>
                            <img src="default.jpg" alt="News Image" class="img-fluid mb-2" id="previewImage">
                            <input type="file" name="image" accept="image/*" class="form-control" onchange="previewImageGeneric(this, 'previewImage')">
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" class="form-control" name="title" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Author</label>
                                    <input type="text" class="form-control" name="author" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Category</label>
                                    <input type="text" class="form-control" name="category" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Publish Date</label>
                                    <input type="date" class="form-control" name="published_at" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Meta Title</label>
                            <input type="text" class="form-control" name="meta_title" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" class="form-control" name="slug" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Meta Description</label>
                            <input type="text" class="form-control" name="meta_description" maxlength="160" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Summary</label>
                            <input type="text" class="form-control" name="summary" maxlength="160" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea class="form-control" name="content" rows="5" required></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" name="add_news" class="btn btn-primary">Add News</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- update news -->
<?php foreach ($data as $news): ?>
    <div class="modal fade" id="editnews<?php echo htmlspecialchars($news['news_id'], ENT_QUOTES, 'utf-8'); ?>" tabindex="-1" aria-labelledby="editnewsLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editnewsLabel">Edit News</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Image and change button -->
                            <div class="col-md-4 text-center">
                                <label class="form-label">Image</label><br>
                                <img src="<?php echo !empty($news['image_url']) ? 'http://localhost:5000/static/news_img/' . htmlspecialchars($news['image_url']) : 'default.jpg'; ?>" alt="News Image" class="img-fluid mb-2" id="previewImageEdit<?php echo $news['news_id']; ?>">
                                <input type="file" name="image" accept="image/*" class="form-control" onchange="previewImageGeneric(this, 'previewImageEdit<?php echo $news['news_id']; ?>')">
                            </div>
                            <!-- Title, Author, Category -->
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Title</label>
                                        <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($news['title'], ENT_QUOTES, 'utf-8'); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Author</label>
                                        <input type="text" class="form-control" name="author" value="<?php echo htmlspecialchars($news['author'], ENT_QUOTES, 'utf-8'); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Category</label>
                                        <input type="text" class="form-control" name="category" value="<?php echo htmlspecialchars($news['category'], ENT_QUOTES, 'utf-8'); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Publish Date</label>
                                        <input type="date" class="form-control" name="published_at" value="<?php echo htmlspecialchars($news['published_at'], ENT_QUOTES, 'utf-8'); ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Meta Title, Slug, Summary -->
                        <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Meta Title</label>
                                <input type="text" class="form-control" name="meta_title" value="<?php echo htmlspecialchars($news['meta_title'], ENT_QUOTES, 'utf-8'); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Slug</label>
                                <input type="text" class="form-control" name="slug" value="<?php echo htmlspecialchars($news['slug'], ENT_QUOTES, 'utf-8'); ?>" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Meta Description</label>
                                <input type="text" class="form-control" name="meta_description" maxlength="160" value="<?php echo htmlspecialchars($news['meta_description'], ENT_QUOTES, 'utf-8'); ?>" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Summary</label>
                                <input type="text" class="form-control" name="summary" maxlength="160" value="<?php echo htmlspecialchars($news['summary'], ENT_QUOTES, 'utf-8'); ?>" required>
                            </div>
                        </div>
                        <!-- Content -->
                        <div class="mb-3">
                            <label class="form-label">Content</label>
                            <textarea class="form-control" name="content" rows="5" required><?php echo htmlspecialchars($news['content'], ENT_QUOTES, 'utf-8') ?></textarea>
                        </div>
                        <input type="hidden" name="news_id" value="<?php echo htmlspecialchars($news['news_id'], ENT_QUOTES, 'utf-8'); ?>">
                        <input type="hidden" name="current_image_url" value="<?php echo htmlspecialchars($news['image_url'] ?? '', ENT_QUOTES, 'utf-8'); ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="update_news" class="btn btn-primary">Update News</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script>
    document.getElementById("searchInput").addEventListener("keyup", function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll("table tbody tr");

        rows.forEach(row => {
            const title = row.cells[1].textContent.toLowerCase();
            const author = row.cells[2].textContent.toLowerCase();
            if (title.includes(filter) || author.includes(filter)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
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
        paginateTable('newsTable', 'newsTablePagination', 10);
    });



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
</script>

<?php
include('./template/foot.php');
?>