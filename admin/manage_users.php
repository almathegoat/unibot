<?php
session_start();
require_once __DIR__ . '/../config/db.php';

/* ---------- AUTH ---------- */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

/* ---------- ADD USER ---------- */
if (isset($_POST['add_user'])) {
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() == 0) {
        $insert = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
        $insert->execute([$email, $password, $role]);
        $success = "User added successfully.";
    } else {
        $error = "Email already exists.";
    }
}

/* ---------- DELETE USER ---------- */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $delStmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $delStmt->execute([$id]);
    header("Location: manage_users.php");
    exit;
}

/* ---------- FETCH USERS ---------- */
$usersStmt = $conn->query("SELECT id, email, role, created_at FROM users ORDER BY created_at DESC");
$users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

$email = $_SESSION['email'];
$initials = strtoupper($email[0]);

/* ---------- ACTIVE PAGE ---------- */
$currentPage = basename($_SERVER['PHP_SELF']);
function active($pages) {
    global $currentPage;
    return in_array($currentPage, (array)$pages) ? 'active' : '';
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Manage Users — Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
body { font-family:'Inter',sans-serif; background:#f4f6f9; }
.sidebar{width:260px;background:#0f172a;color:white;min-height:100vh;display:flex;flex-direction:column;}
.sidebar-header{text-align:center;padding:28px 20px;}
.sidebar-header i{font-size:34px;margin-bottom:8px;}
.sidebar-header h5{font-weight:700;letter-spacing:.08em;font-size:13px;}
.sidebar-divider{height:1px;background:rgba(255,255,255,.08);margin:14px 30px;}
.sidebar-section{text-align:center;font-size:11px;letter-spacing:.15em;color:#94a3b8;margin-bottom:6px;font-weight:600;}
.sidebar-nav a{display:flex;flex-direction:column;align-items:center;padding:14px 0;color:#cbd5f5;text-decoration:none;transition:.2s;}
.sidebar-nav a i{font-size:18px;margin-bottom:4px;}
.sidebar-nav a span{font-size:13px;}
.sidebar-nav a:hover,.sidebar-nav a.active{background:rgba(255,255,255,.06);color:white;}
.topbar{background:white;padding:14px 24px;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center;}
.avatar{width:38px;height:38px;border-radius:50%;background:#2563eb;color:white;display:flex;align-items:center;justify-content:center;font-weight:700;cursor:pointer;}
.table th{font-size:13px;text-transform:uppercase;color:#64748b;}
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
        <a href="dashboard.php" class="<?= active('dashboard.php') ?>"><i class="fa-solid fa-gauge-high"></i><span>Dashboard</span></a>

        <div class="sidebar-divider"></div>
        <div class="sidebar-section">MANAGEMENT</div>
        <a href="tickets.php" class="<?= active('tickets.php') ?>"><i class="fa-solid fa-ticket"></i><span>Tickets</span></a>
        <a href="manage_users.php" class="<?= active('manage_users.php') ?>"><i class="fa-solid fa-users"></i><span>Users</span></a>
        <a href="manage_categories.php" class="<?= active('manage_categories.php') ?>"><i class="fa-solid fa-layer-group"></i><span>Categories</span></a>
        <a href="manage_faq.php" class="<?= active('manage_faq.php') ?>"><i class="fa-solid fa-circle-question"></i><span>FAQ</span></a>

        <div class="sidebar-divider"></div>
        <div class="sidebar-section">SYSTEM</div>
        <a href="chatbot_logs.php" class="<?= active('chatbot_logs.php') ?>"><i class="fa-solid fa-comments"></i><span>Chatbot Logs</span></a>
        <a href="settings.php" class="<?= active('settings.php') ?>"><i class="fa-solid fa-gear"></i><span>Settings</span></a>
    </div>
</aside>

<!-- MAIN -->
<main class="flex-grow-1">
<div class="topbar">
    <h5 class="fw-bold mb-0">Manage Users</h5>
    <div class="dropdown">
        <div class="avatar dropdown-toggle" data-bs-toggle="dropdown"><?= $initials ?></div>
        <ul class="dropdown-menu dropdown-menu-end shadow">
            <li class="px-3 py-2 text-muted small"><?= htmlspecialchars($email) ?></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a></li>
        </ul>
    </div>
</div>

<div class="p-4">

<?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
<?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

<div class="mb-3 text-end">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal"><i class="fa-solid fa-plus me-1"></i> Add User</button>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h6 class="fw-bold mb-3">All Users</h6>
        <div class="table-responsive">
        <table class="table table-hover align-middle">
        <thead>
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Role</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if(empty($users)): ?>
            <tr><td colspan="5" class="text-center text-muted py-4">No users found</td></tr>
        <?php else: foreach($users as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['role']) ?></td>
            <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
            <td>
                <a href="edit_user.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen"></i></a>
                <a href="manage_users.php?delete=<?= $u['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')"><i class="fa-solid fa-trash"></i></a>
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

<!-- ADD USER MODAL -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<form method="POST">
<div class="modal-header">
    <h5 class="modal-title">Add New User</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Role</label>
        <select name="role" class="form-select" required>
            <option value="student">Student</option>
            <option value="admin">Admin</option>
        </select>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
    <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
</div>
</form>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
