<?php
session_start();
require_once __DIR__ . '/../config/db.php';

/* ---------- AUTH ---------- */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit;
}

$studentId = $_SESSION['user_id'];
$name  = $_SESSION['name'] ?? 'Student';
$email = $_SESSION['email'] ?? '';
$initials = strtoupper($name[0]);

/* ---------- STATS ---------- */
$stmt = $conn->prepare("SELECT COUNT(*) FROM tickets WHERE student_id=?");
$stmt->execute([$studentId]);
$totalTickets = $stmt->fetchColumn();

$stmt = $conn->prepare("SELECT COUNT(*) FROM tickets WHERE student_id=? AND status='pending'");
$stmt->execute([$studentId]);
$pendingTickets = $stmt->fetchColumn();

$stmt = $conn->prepare("SELECT COUNT(*) FROM tickets WHERE student_id=? AND status='resolved'");
$stmt->execute([$studentId]);
$resolvedTickets = $stmt->fetchColumn();
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Student Dashboard — Unibot</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
body { font-family:'Inter',sans-serif; background:#f4f6f9; }

/* SIDEBAR */
.sidebar{
    width:260px;
    background:#0f172a;
    color:white;
    min-height:100vh;
    display:flex;
    flex-direction:column;
}
.sidebar-header{text-align:center;padding:28px 20px;}
.sidebar-header i{font-size:32px;margin-bottom:8px;}
.sidebar-header h5{font-weight:700;font-size:13px;letter-spacing:.08em;}
.sidebar-divider{height:1px;background:rgba(255,255,255,.08);margin:14px 30px;}
.sidebar-section{text-align:center;font-size:11px;letter-spacing:.15em;color:#94a3b8;margin-bottom:6px;font-weight:600;}
.sidebar-nav a{
    display:flex;
    flex-direction:column;
    align-items:center;
    padding:14px 0;
    color:#cbd5f5;
    text-decoration:none;
    transition:.2s;
}
.sidebar-nav a i{font-size:18px;margin-bottom:4px;}
.sidebar-nav a span{font-size:13px;}
.sidebar-nav a:hover,
.sidebar-nav a.active{background:rgba(255,255,255,.06);color:white;}

/* TOPBAR */
.topbar{
    background:white;
    padding:14px 24px;
    border-bottom:1px solid #e5e7eb;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.avatar{
    width:38px;height:38px;
    border-radius:50%;
    background:#2563eb;
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:700;
    cursor:pointer;
}

/* CARDS */
.stat-card{border:none;border-radius:12px;}
.stat-card i{font-size:30px;}
.action-card{transition:.2s;border-radius:12px;}
.action-card:hover{transform:translateY(-4px);}

/* TABLE */
.table th{
    font-size:13px;
    text-transform:uppercase;
    color:#64748b;
}
</style>
</head>

<body>
<div class="d-flex">

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-header">
        <i class="fa-solid fa-user-graduate"></i>
        <h5>STUDENT PANEL</h5>
    </div>

    <div class="sidebar-divider"></div>

    <div class="sidebar-nav px-3">
        <div class="sidebar-section">MAIN</div>
        <a href="dashboard.php" class="active">
            <i class="fa-solid fa-gauge-high"></i><span>Dashboard</span>
        </a>

        <div class="sidebar-divider"></div>

        <div class="sidebar-section">REQUESTS</div>
        <a href="tickets.php">
            <i class="fa-solid fa-ticket"></i><span>My Tickets</span>
        </a>
        <a href="new_ticket.php">
            <i class="fa-solid fa-plus-circle"></i><span>New Request</span>
        </a>
        <a href="faq.php">
            <i class="fa-solid fa-circle-question"></i><span>FAQ</span>
        </a>

        <div class="sidebar-divider"></div>

        <div class="sidebar-section">ACCOUNT</div>
        <a href="../auth/logout.php" class="text-danger">
            <i class="fa-solid fa-right-from-bracket"></i><span>Logout</span>
        </a>
    </div>
</aside>

<!-- MAIN -->
<main class="flex-grow-1">

<!-- TOPBAR -->
<div class="topbar">
    <h5 class="fw-bold mb-0">Dashboard</h5>

    <div class="dropdown">
        <div class="avatar dropdown-toggle" data-bs-toggle="dropdown">
            <?= $initials ?>
        </div>
        <ul class="dropdown-menu dropdown-menu-end shadow">
            <li class="px-3 py-2 small text-muted"><?= htmlspecialchars($email) ?></li>
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

<!-- STATS -->
<div class="row g-4 mb-4">
<?php
$stats = [
    ['Total Tickets',$totalTickets,'fa-ticket','text-primary'],
    ['Pending',$pendingTickets,'fa-hourglass-half','text-warning'],
    ['Resolved',$resolvedTickets,'fa-check-circle','text-success']
];
foreach($stats as $s):
?>
<div class="col-md-4">
    <div class="card stat-card shadow-sm">
        <div class="card-body d-flex align-items-center">
            <i class="fa-solid <?= $s[2] ?> <?= $s[3] ?>"></i>
            <div class="ms-3">
                <small><?= $s[0] ?></small>
                <h3><?= $s[1] ?></h3>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>

<!-- QUICK ACTIONS -->
<div class="row g-4">
<div class="col-md-4">
    <a href="tickets.php" class="text-decoration-none text-dark">
        <div class="card shadow-sm p-3 action-card">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-ticket fa-2x text-primary"></i>
                <div class="ms-3">
                    <h6 class="fw-bold mb-0">My Tickets</h6>
                    <small class="text-muted">Track your requests</small>
                </div>
            </div>
        </div>
    </a>
</div>

<div class="col-md-4">
    <a href="new_ticket.php" class="text-decoration-none text-dark">
        <div class="card shadow-sm p-3 action-card">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-plus-circle fa-2x text-success"></i>
                <div class="ms-3">
                    <h6 class="fw-bold mb-0">New Request</h6>
                    <small class="text-muted">Submit a ticket</small>
                </div>
            </div>
        </div>
    </a>
</div>

<div class="col-md-4">
    <a href="faq.php" class="text-decoration-none text-dark">
        <div class="card shadow-sm p-3 action-card">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-circle-question fa-2x text-warning"></i>
                <div class="ms-3">
                    <h6 class="fw-bold mb-0">FAQ</h6>
                    <small class="text-muted">Quick answers</small>
                </div>
            </div>
        </div>
    </a>
</div>
</div>

<footer class="text-center mt-4 text-muted small">
    © 2025 Unibot — All rights reserved
</footer>

</div>
</main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
