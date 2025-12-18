<?php
session_start();
require_once __DIR__ . '/../config/db.php';

/* ---------- AUTH ---------- */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$currentPage = basename($_SERVER['PHP_SELF']);
$email = $_SESSION['email'];
$initials = strtoupper($email[0]);

/* ---------- ADD FAQ ---------- */
if (isset($_POST['add_faq'])) {
    $q = trim($_POST['question']);
    $a = trim($_POST['answer']);
    if ($q && $a) {
        $conn->prepare("INSERT INTO faqs (question, answer) VALUES (?, ?)")
             ->execute([$q, $a]);
    }
    header("Location: faq.php");
    exit;
}

/* ---------- DELETE FAQ ---------- */
if (isset($_GET['delete'])) {
    $conn->prepare("DELETE FROM faqs WHERE id=?")->execute([(int)$_GET['delete']]);
    header("Location: faq.php");
    exit;
}

/* ---------- UPDATE FAQ ---------- */
if (isset($_POST['update_faq'])) {
    $conn->prepare("UPDATE faqs SET question=?, answer=? WHERE id=?")
         ->execute([
             trim($_POST['question']),
             trim($_POST['answer']),
             (int)$_POST['id']
         ]);
    header("Location: faq.php");
    exit;
}

$faqs = $conn->query("SELECT * FROM faqs ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>FAQ — Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
body{font-family:'Inter',sans-serif;background:#f4f6f9}

/* === SIDEBAR === */
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

/* === TOPBAR === */
.topbar{background:white;padding:14px 24px;border-bottom:1px solid #e5e7eb;
display:flex;justify-content:space-between;align-items:center}
.avatar{width:38px;height:38px;border-radius:50%;background:#2563eb;color:white;
display:flex;align-items:center;justify-content:center;font-weight:700;cursor:pointer}
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
    <a class="<?= $currentPage==='dashboard.php'?'active':'' ?>" href="dashboard.php">
      <i class="fa-solid fa-gauge-high"></i><span>Dashboard</span>
    </a>

    <div class="sidebar-divider"></div>

    <div class="sidebar-section">MANAGEMENT</div>
    <a class="<?= $currentPage==='tickets.php'?'active':'' ?>" href="tickets.php">
      <i class="fa-solid fa-ticket"></i><span>Tickets</span>
    </a>
    <a class="<?= $currentPage==='manage_users.php'?'active':'' ?>" href="manage_users.php">
      <i class="fa-solid fa-users"></i><span>Users</span>
    </a>
    <a class="<?= $currentPage==='manage_categories.php'?'active':'' ?>" href="manage_categories.php">
      <i class="fa-solid fa-layer-group"></i><span>Categories</span>
    </a>
    <a class="<?= $currentPage==='faq.php'?'active':'' ?>" href="manage_faq.php">
      <i class="fa-solid fa-circle-question"></i><span>FAQ</span>
    </a>

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
  <h5 class="fw-bold mb-0">FAQ Management</h5>

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

<!-- ADD FAQ -->
<div class="card shadow-sm mb-4">
  <div class="card-body">
    <h6 class="fw-bold mb-3">Add New FAQ</h6>
    <form method="POST">
      <input name="question" class="form-control mb-3" placeholder="Question" required>
      <textarea name="answer" class="form-control mb-3" rows="4" placeholder="Answer" required></textarea>
      <button name="add_faq" class="btn btn-primary">
        <i class="fa fa-save me-1"></i> Save FAQ
      </button>
    </form>
  </div>
</div>

<!-- FAQ LIST -->
<div class="card shadow-sm">
  <div class="card-body">
    <h6 class="fw-bold mb-3">FAQ List</h6>

<?php foreach($faqs as $f): ?>
<div class="border rounded p-3 mb-3 bg-white">
  <strong><?= htmlspecialchars($f['question']) ?></strong>
  <p class="text-muted mt-2"><?= nl2br(htmlspecialchars($f['answer'])) ?></p>

  <div class="text-end">
    <button class="btn btn-sm btn-outline-primary me-2"
            data-bs-toggle="modal" data-bs-target="#edit<?= $f['id'] ?>">
      <i class="fa fa-pen"></i>
    </button>
    <a href="?delete=<?= $f['id'] ?>" class="btn btn-sm btn-outline-danger"
       onclick="return confirm('Delete this FAQ?')">
      <i class="fa fa-trash"></i>
    </a>
  </div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="edit<?= $f['id'] ?>">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5>Edit FAQ</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" value="<?= $f['id'] ?>">
        <input name="question" class="form-control mb-3" value="<?= htmlspecialchars($f['question']) ?>" required>
        <textarea name="answer" class="form-control" rows="4" required><?= htmlspecialchars($f['answer']) ?></textarea>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button name="update_faq" class="btn btn-primary">Update</button>
      </div>
    </form>
  </div>
</div>
<?php endforeach; ?>

<?php if(!$faqs): ?>
<div class="text-center text-muted py-4">No FAQs found.</div>
<?php endif; ?>

  </div>
</div>

</div>
</main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
