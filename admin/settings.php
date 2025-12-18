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

/* ---------- HANDLE FORM SUBMISSION ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';

    switch ($type) {
        case 'general':
            $system_name = $_POST['system_name'] ?? '';
            $support_email = $_POST['support_email'] ?? '';
            $timezone = $_POST['timezone'] ?? '';

            $stmt = $conn->prepare("UPDATE settings SET system_name=?, support_email=?, timezone=? WHERE id=1");
            $stmt->execute([$system_name, $support_email, $timezone]);
            $msg = "General settings saved!";
            break;

        case 'chatbot':
            $language = $_POST['language'] ?? 'English';
            $confidence = (float)($_POST['confidence'] ?? 0.6);
            $auto_escalation = isset($_POST['auto_escalation']) ? 1 : 0;

            $stmt = $conn->prepare("UPDATE settings SET default_language=?, confidence_threshold=?, auto_escalation=? WHERE id=1");
            $stmt->execute([$language, $confidence, $auto_escalation]);
            $msg = "Chatbot settings saved!";
            break;

        case 'security':
            $require_2fa = isset($_POST['require_2fa']) ? 1 : 0;
            $session_timeout = (int)($_POST['session_timeout'] ?? 30);

            $stmt = $conn->prepare("UPDATE settings SET require_admin_2fa=?, session_timeout=? WHERE id=1");
            $stmt->execute([$require_2fa, $session_timeout]);
            $msg = "Security settings saved!";
            break;

        case 'maintenance':
            $maintenance_mode = isset($_POST['maintenance_mode']) ? 1 : 0;

            $stmt = $conn->prepare("UPDATE settings SET maintenance_mode=? WHERE id=1");
            $stmt->execute([$maintenance_mode]);
            $msg = "Maintenance settings updated!";
            break;
    }
}

/* ---------- FETCH SETTINGS ---------- */
$stmt = $conn->prepare("SELECT * FROM settings WHERE id=1");
$stmt->execute();
$settings = $stmt->fetch(PDO::FETCH_ASSOC);
$settings = $settings ?: [
    'system_name' => 'Unibot',
    'support_email' => 'support@unibot.com',
    'timezone' => 'Africa/Nairobi',
    'default_language' => 'English',
    'confidence_threshold' => 0.6,
    'auto_escalation' => 1,
    'require_admin_2fa' => 1,
    'session_timeout' => 30,
    'maintenance_mode' => 0
];
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Settings — Admin</title>
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
.card-body h6{font-weight:700;}
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
        <a href="tickets.php"><i class="fa-solid fa-ticket"></i><span>Tickets</span></a>
        <a href="manage_users.php"><i class="fa-solid fa-users"></i><span>Users</span></a>
        <a href="manage_categories.php"><i class="fa-solid fa-layer-group"></i><span>Categories</span></a>
        <a href="manage_faq.php"><i class="fa-solid fa-circle-question"></i><span>FAQ</span></a>
        <div class="sidebar-divider"></div>
        <div class="sidebar-section">SYSTEM</div>
        <a href="chatbot_logs.php"><i class="fa-solid fa-comments"></i><span>Chatbot Logs</span></a>
        <a class="active" href="settings.php"><i class="fa-solid fa-gear"></i><span>Settings</span></a>
    </div>
</aside>

<!-- MAIN -->
<main class="flex-grow-1">
<div class="topbar">
    <h5 class="fw-bold mb-0">System Settings</h5>
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
<?php if(!empty($msg)): ?>
<div class="alert alert-success"><?= $msg ?></div>
<?php endif; ?>

<div class="row g-4">
<!-- GENERAL -->
<div class="col-lg-6">
<div class="card shadow-sm">
<div class="card-body">
<h6><i class="fa fa-gear me-2"></i> General</h6>
<form method="POST">
<input type="hidden" name="type" value="general">
<label class="fw-semibold">System Name</label>
<input class="form-control mb-3" name="system_name" value="<?= htmlspecialchars($settings['system_name']) ?>">
<label class="fw-semibold">Support Email</label>
<input class="form-control mb-3" name="support_email" value="<?= htmlspecialchars($settings['support_email']) ?>">
<label class="fw-semibold">Timezone</label>
<select class="form-select mb-3" name="timezone">
<option <?= $settings['timezone']=='Africa/Nairobi'?'selected':'' ?>>Africa/Nairobi</option>
<option <?= $settings['timezone']=='UTC'?'selected':'' ?>>UTC</option>
<option <?= $settings['timezone']=='Europe/London'?'selected':'' ?>>Europe/London</option>
<option <?= $settings['timezone']=='America/New_York'?'selected':'' ?>>America/New_York</option>
</select>
<button class="btn btn-primary">Save Changes</button>
</form>
</div>
</div>
</div>

<!-- CHATBOT -->
<div class="col-lg-6">
<div class="card shadow-sm">
<div class="card-body">
<h6><i class="fa fa-robot me-2"></i> Chatbot</h6>
<form method="POST">
<input type="hidden" name="type" value="chatbot">
<label class="fw-semibold">Default Language</label>
<select class="form-select mb-3" name="language">
<option <?= $settings['default_language']=='English'?'selected':'' ?>>English</option>
<option <?= $settings['default_language']=='French'?'selected':'' ?>>French</option>
<option <?= $settings['default_language']=='Swahili'?'selected':'' ?>>Swahili</option>
</select>
<label class="fw-semibold">Confidence Threshold</label>
<input type="number" step="0.01" class="form-control mb-1" name="confidence" value="<?= htmlspecialchars($settings['confidence_threshold']) ?>">
<small class="text-muted d-block mb-3">Escalates to human below this value</small>
<div class="form-check form-switch mb-3">
<input class="form-check-input" type="checkbox" name="auto_escalation" <?= $settings['auto_escalation']?'checked':'' ?>>
<label class="form-check-label fw-semibold">Enable Auto-Escalation</label>
</div>
<button class="btn btn-primary">Save Chatbot Settings</button>
</form>
</div>
</div>
</div>

<!-- SECURITY -->
<div class="col-lg-6">
<div class="card shadow-sm">
<div class="card-body">
<h6><i class="fa fa-shield-halved me-2"></i> Security</h6>
<form method="POST">
<input type="hidden" name="type" value="security">
<div class="form-check form-switch mb-3">
<input class="form-check-input" type="checkbox" name="require_2fa" <?= $settings['require_admin_2fa']?'checked':'' ?>>
<label class="form-check-label fw-semibold">Require Admin 2FA</label>
</div>
<label class="fw-semibold">Session Timeout (minutes)</label>
<input type="number" class="form-control mb-3" name="session_timeout" value="<?= htmlspecialchars($settings['session_timeout']) ?>">
<button class="btn btn-primary">Save Security Settings</button>
</form>
</div>
</div>
</div>

<!-- MAINTENANCE -->
<div class="col-lg-6">
<div class="card shadow-sm">
<div class="card-body">
<h6><i class="fa fa-screwdriver-wrench me-2"></i> Maintenance</h6>
<form method="POST">
<input type="hidden" name="type" value="maintenance">
<div class="form-check form-switch mb-3">
<input class="form-check-input" type="checkbox" name="maintenance_mode" <?= $settings['maintenance_mode']?'checked':'' ?>>
<label class="form-check-label fw-semibold">Maintenance Mode</label>
</div>
<button class="btn btn-outline-danger w-100 mb-2"><i class="fa fa-database me-2"></i> Clear Cache</button>
<button class="btn btn-outline-secondary w-100"><i class="fa fa-rotate me-2"></i> Run Diagnostics</button>
<button class="btn btn-primary mt-2">Save Maintenance Settings</button>
</form>
</div>
</div>
</div>

</div>

<footer class="text-center text-muted mt-4">
<small>© <?= date('Y') ?> Unibot — Admin</small>
</footer>
</div>
</main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
