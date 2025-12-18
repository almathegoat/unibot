<?php
session_start();
require_once __DIR__ . '/../config/db.php';

/* ---------- AUTH ---------- */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

/* ---------- STATS ---------- */
$totalTickets = $conn->query("SELECT COUNT(*) FROM tickets")->fetchColumn();
$pendingTickets = $conn->query("SELECT COUNT(*) FROM tickets WHERE status='pending'")->fetchColumn();
$resolvedTickets = $conn->query("SELECT COUNT(*) FROM tickets WHERE status='resolved'")->fetchColumn();

$recentTickets = $conn->query("
    SELECT id, student_name, subject, status
    FROM tickets
    ORDER BY created_at DESC
    LIMIT 5
");

/* ---------- CHART DATA ---------- */
$monthlyTickets = $conn->query("
    SELECT DATE_FORMAT(created_at, '%b %Y') AS month, COUNT(*) AS total
    FROM tickets
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY YEAR(created_at), MONTH(created_at)
    ORDER BY YEAR(created_at), MONTH(created_at)
")->fetchAll(PDO::FETCH_ASSOC);

$statusData = $conn->query("
    SELECT status, COUNT(*) AS total
    FROM tickets
    GROUP BY status
")->fetchAll(PDO::FETCH_ASSOC);

$categoryData = $conn->query("
    SELECT category, COUNT(*) AS total
    FROM tickets
    GROUP BY category
")->fetchAll(PDO::FETCH_ASSOC);

$email = $_SESSION['email'];
$initials = strtoupper($email[0]);
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Admin Dashboard — Unibot</title>
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
.stat-card{border:none;border-radius:12px;}
.stat-card i{font-size:30px;}
.table th{font-size:13px;text-transform:uppercase;color:#64748b;}
</style>
</head>

<body>
<div class="d-flex">

<aside class="sidebar">
    <div class="sidebar-header">
        <i class="fa-solid fa-shield-halved"></i>
        <h5>ADMIN PANEL</h5>
    </div>

    <div class="sidebar-divider"></div>

    <div class="sidebar-nav px-3">
        <div class="sidebar-section">MAIN</div>
        <a href="dashboard.php" class="active"><i class="fa-solid fa-gauge-high"></i><span>Dashboard</span></a>

        <div class="sidebar-divider"></div>

        <div class="sidebar-section">MANAGEMENT</div>
        <a href="tickets.php"><i class="fa-solid fa-ticket"></i><span>Tickets</span></a>
        <a href="manage_users.php"><i class="fa-solid fa-users"></i><span>Users</span></a>
        <a href="manage_categories.php"><i class="fa-solid fa-layer-group"></i><span>Categories</span></a>
        <a href="manage_faq.php"><i class="fa-solid fa-circle-question"></i><span>FAQ</span></a>

        <div class="sidebar-divider"></div>

        <div class="sidebar-section">SYSTEM</div>
        <a href="chatbot_logs.php"><i class="fa-solid fa-comments"></i><span>Chatbot Logs</span></a>
        <a href="settings.php"><i class="fa-solid fa-gear"></i><span>Settings</span></a>
    </div>
</aside>

<main class="flex-grow-1">

<div class="topbar">
    <h5 class="fw-bold mb-0">Dashboard</h5>
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

<!-- STATS -->
<div class="row g-4">
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

<!-- CHARTS -->
<div class="row g-4 mt-2">
<div class="col-lg-8">
    <div class="card shadow-sm">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Tickets Volume (Last 6 Months)</h6>
            <canvas id="lineChart"></canvas>
        </div>
    </div>
</div>

<div class="col-lg-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Tickets by Status</h6>
            <canvas id="donutChart"></canvas>
        </div>
    </div>
</div>
</div>

<div class="card shadow-sm mt-4">
<div class="card-body">
<h6 class="fw-bold mb-3">Tickets by Category</h6>
<canvas id="barChart"></canvas>
</div>
</div>

<!-- RECENT TICKETS -->
<div class="card shadow-sm mt-4">
<div class="card-body">
<h6 class="fw-bold mb-3">Recent Tickets</h6>
<table class="table align-middle">
<thead>
<tr><th>ID</th><th>Student</th><th>Subject</th><th>Status</th></tr>
</thead>
<tbody>
<?php while($t=$recentTickets->fetch(PDO::FETCH_ASSOC)): ?>
<tr>
<td><?= $t['id'] ?></td>
<td><?= htmlspecialchars($t['student_name']) ?></td>
<td><?= htmlspecialchars($t['subject']) ?></td>
<td>
<span class="badge bg-<?=
$t['status']=='pending'?'warning':
($t['status']=='in_progress'?'primary':'success')
?>">
<?= ucfirst(str_replace('_',' ',$t['status'])) ?>
</span>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</div>

</div>
</main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const lineCtx  = document.getElementById('lineChart');
const donutCtx = document.getElementById('donutChart');
const barCtx   = document.getElementById('barChart');

new Chart(lineCtx,{
 type:'line',
 data:{
   labels:<?= json_encode(array_column($monthlyTickets,'month')) ?>,
   datasets:[{
     data:<?= json_encode(array_column($monthlyTickets,'total')) ?>,
     borderColor:'#2563eb',
     backgroundColor:'rgba(37,99,235,.15)',
     fill:true,
     tension:.4
   }]
 },
 options:{plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
});

new Chart(donutCtx,{
 type:'doughnut',
 data:{
   labels:<?= json_encode(array_column($statusData,'status')) ?>,
   datasets:[{
     data:<?= json_encode(array_column($statusData,'total')) ?>,
     backgroundColor:['#facc15','#3b82f6','#22c55e']
   }]
 },
 options:{cutout:'70%'}
});

new Chart(barCtx,{
 type:'bar',
 data:{
   labels:<?= json_encode(array_column($categoryData,'category')) ?>,
   datasets:[{
     data:<?= json_encode(array_column($categoryData,'total')) ?>,
     backgroundColor:'#0f172a'
   }]
 },
 options:{plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
});
</script>
</body>
</html>
