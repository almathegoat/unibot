<?php
session_start();
require_once "../config/db.php";

/* ---------- AUTH ---------- */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

/* ---------- DATA ---------- */
$catStmt = $conn->prepare("SELECT * FROM categories ORDER BY name ASC");
$catStmt->execute();
$allCategories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

$search = $_GET['search'] ?? "";
$category_id = $_GET['category'] ?? "";
$status = $_GET['status'] ?? "";

/* ---------- FIXED SQL TO SHOW CATEGORY ---------- */
$sql = "SELECT t.*, COALESCE(c.name, t.category) AS category_name
        FROM tickets t
        LEFT JOIN categories c ON t.category_id = c.id
        WHERE 1";

$params = [];
if ($search) {
    $sql .= " AND (t.subject LIKE :search OR t.id LIKE :search)";
    $params[':search'] = "%$search%";
}
if ($category_id) {
    $sql .= " AND t.category_id = :cat";
    $params[':cat'] = $category_id;
}
if ($status) {
    $sql .= " AND t.status = :stat";
    $params[':stat'] = $status;
}

$sql .= " ORDER BY t.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

$email = $_SESSION['email'];
$initials = strtoupper($email[0]);
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Tickets — Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
body{font-family:'Inter',sans-serif;background:#f4f6f9}
/* SIDEBAR */
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

/* TOPBAR */
.topbar{background:white;padding:14px 24px;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center}
.avatar{width:38px;height:38px;border-radius:50%;background:#2563eb;color:white;display:flex;align-items:center;justify-content:center;font-weight:700;cursor:pointer}

/* TABLE */
.table th{text-transform:uppercase;font-size:12px;color:#64748b}
</style>
</head>

<body>
<div class="d-flex">

<!-- SIDEBAR -->
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
<a href="tickets.php" class="active"><i class="fa-solid fa-ticket"></i><span>Tickets</span></a>
<a href="manage_users.php"><i class="fa-solid fa-users"></i><span>Users</span></a>
<a href="manage_categories.php"><i class="fa-solid fa-layer-group"></i><span>Categories</span></a>
<a href="manage_faq.php"><i class="fa-solid fa-circle-question"></i><span>FAQ</span></a>

<div class="sidebar-divider"></div>
<div class="sidebar-section">SYSTEM</div>
<a href="chatbot_logs.php"><i class="fa-solid fa-comments"></i><span>Chatbot Logs</span></a>
<a href="settings.php"><i class="fa-solid fa-gear"></i><span>Settings</span></a>
</div>
</aside>

<!-- MAIN -->
<main class="flex-grow-1">

<!-- TOPBAR -->
<div class="topbar">
<h5 class="fw-bold mb-0">Tickets</h5>

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

<!-- CONTENT -->
<div class="p-4">

<!-- FILTER -->
<div class="card shadow-sm mb-4">
<div class="card-body">
<form method="GET" class="row g-3 align-items-end">
<div class="col-md-4">
<label class="form-label small text-muted">Search</label>
<input type="text" name="search" class="form-control" value="<?= htmlspecialchars($search) ?>">
</div>

<div class="col-md-3">
<label class="form-label small text-muted">Category</label>
<select name="category" class="form-select">
<option value="">All</option>
<?php foreach($allCategories as $c): ?>
<option value="<?= $c['id'] ?>" <?= $category_id==$c['id']?'selected':'' ?>>
<?= htmlspecialchars($c['name']) ?>
</option>
<?php endforeach; ?>
</select>
</div>

<div class="col-md-3">
<label class="form-label small text-muted">Status</label>
<select name="status" class="form-select">
<option value="">All</option>
<option <?= $status=='pending'?'selected':'' ?>>Pending</option>
<option <?= $status=='resolved'?'selected':'' ?>>Resolved</option>
<option <?= $status=='in_progress'?'selected':'' ?>>In Progress</option>
</select>
</div>

<div class="col-md-2">
<button class="btn btn-primary w-100">
<i class="fa fa-filter me-1"></i> Filter
</button>
</div>
</form>
</div>
</div>

<!-- TABLE -->
<div class="card shadow-sm">
<div class="card-body">
<div class="table-responsive">
<table class="table table-hover align-middle">
<thead>
<tr>
<th>ID</th>
<th>Student</th>
<th>Subject</th>
<th>Category</th>
<th>Assigned</th>
<th>Status</th>
<th>Created</th>
<th>Action</th>
</tr>
</thead>
<tbody>

<?php if(empty($tickets)): ?>
<tr><td colspan="8" class="text-center text-muted py-4">No tickets found</td></tr>
<?php else: foreach($tickets as $t): ?>
<tr>
<td><?= $t['id'] ?></td>
<td><?= htmlspecialchars($t['student_name']) ?></td>
<td><?= htmlspecialchars($t['subject']) ?></td>
<td><?= htmlspecialchars($t['category_name']) ?></td>
<td><?= htmlspecialchars($t['assigned_to'] ?? 'Unassigned') ?></td>
<td>
<?php
$badge = match($t['status']){
'pending'=>'bg-warning text-dark',
'resolved'=>'bg-success',
'in_progress'=>'bg-primary',
default=>'bg-secondary'
};
?>
<span class="badge <?= $badge ?>"><?= ucfirst($t['status']) ?></span>
</td>
<td><?= date('d M Y', strtotime($t['created_at'])) ?></td>
<td>
<a href="ticket-details.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-outline-primary" title="View Ticket">
<i class="fa-solid fa-eye"></i>
</a>
</td>
</tr>
<?php endforeach; endif; ?>

</tbody>
</table>
</div>
</div>
</div>

</div>
</main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
