<?php
session_start();
require_once __DIR__ . '/../config/db.php';

/* ---------- AUTH ---------- */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$email = $_SESSION['email'];
$initials = strtoupper($email[0]);

/* ---------- SEARCH ---------- */
$search = $_GET['search'] ?? "";

/* ---------- HANDLE ADD CATEGORY ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    if ($name) {
        $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        $stmt->execute([$name, $description]);
        header("Location: manage_categories.php");
        exit;
    }
}

/* ---------- HANDLE DELETE CATEGORY ---------- */
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM categories WHERE id=?");
    $stmt->execute([$delId]);
    header("Location: manage_categories.php");
    exit;
}

/* ---------- FETCH CATEGORIES ---------- */
$sql = "
    SELECT 
        c.id, 
        c.name, 
        c.description,
        (
            SELECT COUNT(*) 
            FROM tickets t 
            WHERE t.category_id = c.id
        ) AS ticket_count
    FROM categories c
    WHERE c.name LIKE :search 
       OR c.description LIKE :search
    ORDER BY c.id DESC
";
$stmt = $conn->prepare($sql);
$searchTerm = "%$search%";
$stmt->bindParam(":search", $searchTerm);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Manage Categories — Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
body{font-family:'Inter',sans-serif;background:#f4f6f9}
.sidebar{width:260px;background:#0f172a;color:white;min-height:100vh;display:flex;flex-direction:column}
.sidebar-header{text-align:center;padding:28px 20px}
.sidebar-header i{font-size:34px;margin-bottom:8px}
.sidebar-header h5{font-weight:700;letter-spacing:.08em;font-size:13px}
.sidebar-divider{height:1px;background:rgba(255,255,255,.08);margin:14px 30px}
.sidebar-section{text-align:center;font-size:11px;letter-spacing:.15em;color:#94a3b8;margin-bottom:6px;font-weight:600}
.sidebar-nav a{display:flex;flex-direction:column;align-items:center;padding:14px 0;color:#cbd5f5;text-decoration:none}
.sidebar-nav a i{font-size:18px;margin-bottom:4px}
.sidebar-nav a span{font-size:13px}
.sidebar-nav a:hover,.sidebar-nav a.active{background:rgba(255,255,255,.06);color:white}
.topbar{background:white;padding:14px 24px;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center}
.avatar{width:38px;height:38px;border-radius:50%;background:#2563eb;color:white;display:flex;align-items:center;justify-content:center;font-weight:700;cursor:pointer}
.table th{font-size:13px;text-transform:uppercase;color:#64748b}
</style>
</head>

<body>
<div class="d-flex">

<!-- ===== SIDEBAR ===== -->
<aside class="sidebar">
    <div class="sidebar-header">
        <i class="fa-solid fa-shield-halved"></i>
        <h5>ADMIN PANEL</h5>
    </div>

    <div class="sidebar-divider"></div>

    <div class="sidebar-nav px-3">
        <div class="sidebar-section">MAIN</div>
        <a href="dashboard.php"><i class="fa-solid fa-gauge-high"></i><span>Dashboard</span></a>

        <div class="sidebar-divider"></div>

        <div class="sidebar-section">MANAGEMENT</div>
        <a href="tickets.php"><i class="fa-solid fa-ticket"></i><span>Tickets</span></a>
        <a href="manage_users.php"><i class="fa-solid fa-users"></i><span>Users</span></a>
        <a href="manage_categories.php" class="active">
            <i class="fa-solid fa-layer-group"></i><span>Categories</span>
        </a>
        <a href="manage_faq.php"><i class="fa-solid fa-circle-question"></i><span>FAQ</span></a>

        <div class="sidebar-divider"></div>

        <div class="sidebar-section">SYSTEM</div>
        <a href="chatbot_logs.php"><i class="fa-solid fa-comments"></i><span>Chatbot Logs</span></a>
        <a href="settings.php"><i class="fa-solid fa-gear"></i><span>Settings</span></a>
    </div>
</aside>

<!-- ===== MAIN ===== -->
<main class="flex-grow-1">

<!-- TOPBAR -->
<div class="topbar">
    <h5 class="fw-bold mb-0">Manage Categories</h5>

    <div class="dropdown">
        <div class="avatar dropdown-toggle" data-bs-toggle="dropdown"><?= $initials ?></div>
        <ul class="dropdown-menu dropdown-menu-end shadow">
            <li class="px-3 py-2 text-muted small"><?= htmlspecialchars($email) ?></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item text-danger" href="../auth/logout.php">
                    <i class="fa-solid fa-right-from-bracket me-2"></i>Logout
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="p-4">

<!-- ADD CATEGORY BUTTON -->
<button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
    <i class="fa-solid fa-plus me-2"></i> Add Category
</button>

<!-- SEARCH -->
<div class="card shadow-sm mb-4">
    <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="Search categories..." value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-primary">
                <i class="fa fa-search me-1"></i> Search
            </button>
        </form>
    </div>
</div>

<!-- TABLE -->
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Category</th>
                <th>Description</th>
                <th>Tickets</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php if(!$categories): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">No categories found</td></tr>
            <?php else: foreach ($categories as $cat): ?>
                <tr>
                    <td><?= $cat['id'] ?></td>
                    <td><?= htmlspecialchars($cat['name']) ?></td>
                    <td><?= htmlspecialchars($cat['description']) ?></td>
                    <td><span class="badge bg-primary"><?= $cat['ticket_count'] ?></span></td>
                    <td>
                        <a href="?delete=<?= $cat['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this category?')">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div>
</main>
</div>

<!-- ADD CATEGORY MODAL -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<form method="POST">
<div class="modal-header">
    <h5 class="modal-title">Add Category</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <div class="mb-3">
        <label class="form-label">Category Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control"></textarea>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
</div>
</form>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
