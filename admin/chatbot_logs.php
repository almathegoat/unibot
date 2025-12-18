<?php
session_start();
require_once "../config/db.php";

/* ---------- AUTH ---------- */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$email = $_SESSION['email'] ?? 'admin@unibot.com';
$initials = strtoupper($email[0]);

/* ---------- DELETE LOG ---------- */
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM chatbot_logs WHERE id=?");
    $stmt->execute([$delId]);
    header("Location: chatbot_logs.php");
    exit;
}

/* ---------- FILTERS ---------- */
$student = $_GET['student'] ?? "";
$date    = $_GET['date'] ?? "";
$keyword = $_GET['keyword'] ?? "";

/* ---------- PAGINATION ---------- */
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

/* ---------- QUERY ---------- */
$sql = "SELECT * FROM chatbot_logs WHERE 1";
$countSql = "SELECT COUNT(*) FROM chatbot_logs WHERE 1";
$params = [];

if ($student) {
    $sql .= " AND student_name LIKE :student";
    $countSql .= " AND student_name LIKE :student";
    $params[':student'] = "%$student%";
}
if ($date) {
    $sql .= " AND DATE(created_at) = :date";
    $countSql .= " AND DATE(created_at) = :date";
    $params[':date'] = $date;
}
if ($keyword) {
    $sql .= " AND (user_message LIKE :kw OR bot_response LIKE :kw)";
    $countSql .= " AND (user_message LIKE :kw OR bot_response LIKE :kw)";
    $params[':kw'] = "%$keyword%";
}

$sql .= " ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalLogs = $conn->prepare($countSql);
$totalLogs->execute($params);
$totalLogs = $totalLogs->fetchColumn();

$totalPages = ceil($totalLogs / $perPage);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Chatbot Logs — Admin</title>
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
    <a href="manage_categories.php"><i class="fa-solid fa-layer-group"></i><span>Categories</span></a>
    <a href="manage_faq.php"><i class="fa-solid fa-circle-question"></i><span>FAQ</span></a>
    <div class="sidebar-divider"></div>
    <div class="sidebar-section">SYSTEM</div>
    <a class="active" href="chatbot_logs.php"><i class="fa-solid fa-comments"></i><span>Chatbot Logs</span></a>
    <a href="settings.php"><i class="fa-solid fa-gear"></i><span>Settings</span></a>
  </div>
</aside>

<!-- ===== MAIN ===== -->
<main class="flex-grow-1">
<div class="topbar">
  <h5 class="fw-bold mb-0">Chatbot Logs</h5>
  <div class="dropdown">
    <div class="avatar dropdown-toggle" data-bs-toggle="dropdown"><?= $initials ?></div>
    <ul class="dropdown-menu dropdown-menu-end shadow">
      <li class="px-3 py-2 text-muted small"><?= htmlspecialchars($email) ?></li>
      <li><hr class="dropdown-divider"></li>
      <li>
        <a class="dropdown-item text-danger" href="../auth/logout.php">
          <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
        </a>
      </li>
    </ul>
  </div>
</div>

<div class="p-4">

<!-- FILTERS -->
<div class="card shadow-sm mb-4">
<div class="card-body">
<form method="GET" class="row g-3 align-items-end">
  <div class="col-md-3">
    <label class="fw-semibold">Student</label>
    <input class="form-control" name="student" value="<?= htmlspecialchars($student) ?>">
  </div>
  <div class="col-md-3">
    <label class="fw-semibold">Date</label>
    <input type="date" class="form-control" name="date" value="<?= htmlspecialchars($date) ?>">
  </div>
  <div class="col-md-3">
    <label class="fw-semibold">Keyword</label>
    <input class="form-control" name="keyword" value="<?= htmlspecialchars($keyword) ?>">
  </div>
  <div class="col-md-3">
    <button class="btn btn-primary w-100"><i class="fa fa-search me-2"></i> Search</button>
  </div>
</form>
</div>
</div>

<!-- LOGS -->
<div class="card shadow-sm">
<div class="card-body p-0">
<table class="table table-hover align-middle mb-0">
<thead class="table-light">
<tr>
  <th>Student</th>
  <th>User Message</th>
  <th>Bot Response</th>
  <th>Time</th>
  <th width="120">Actions</th>
</tr>
</thead>
<tbody>

<?php if(!$logs): ?>
<tr>
<td colspan="5" class="text-center text-muted py-4">No chatbot logs found.</td>
</tr>
<?php else: foreach($logs as $log): ?>
<tr>
  <td><?= htmlspecialchars($log['student_name']) ?></td>
  <td><?= htmlspecialchars(substr($log['user_message'],0,45)) ?>…</td>
  <td><?= htmlspecialchars(substr($log['bot_response'],0,45)) ?>…</td>
  <td><?= $log['created_at'] ?></td>
  <td>
    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#log<?= $log['id'] ?>"><i class="fa fa-eye"></i></button>
    <a href="?delete=<?= $log['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this log?')"><i class="fa fa-trash"></i></a>
  </td>
</tr>

<!-- MODAL -->
<div class="modal fade" id="log<?= $log['id'] ?>">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title"><i class="fa fa-comments me-2"></i> Conversation</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<p><strong>Student:</strong> <?= htmlspecialchars($log['student_name']) ?></p>
<hr>
<p><strong>User:</strong><br><?= nl2br(htmlspecialchars($log['user_message'])) ?></p>
<hr>
<p><strong>Bot:</strong><br><?= nl2br(htmlspecialchars($log['bot_response'])) ?></p>
<hr>
<small class="text-muted"><i class="fa fa-clock me-1"></i> <?= $log['created_at'] ?></small>
</div>
<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
</div>
</div>
</div>

<?php endforeach; endif; ?>

</tbody>
</table>
</div>
</div>

<!-- PAGINATION -->
<?php if($totalPages>1): ?>
<nav class="mt-3">
<ul class="pagination">
<?php for($i=1;$i<=$totalPages;$i++): ?>
<li class="page-item <?= $i==$page?'active':'' ?>">
  <a class="page-link" href="?student=<?= urlencode($student) ?>&date=<?= $date ?>&keyword=<?= urlencode($keyword) ?>&page=<?= $i ?>"><?= $i ?></a>
</li>
<?php endfor; ?>
</ul>
</nav>
<?php endif; ?>

</div>
</main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
