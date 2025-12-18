<?php
session_start();
require_once "../config/db.php";

/* ---------- AUTH ---------- */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

/* ---------- VALIDATE TICKET ID ---------- */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: tickets.php");
    exit();
}
$ticket_id = (int)$_GET['id'];

/* ---------- FETCH TICKET ---------- */
$stmt = $conn->prepare("
    SELECT t.*, c.name AS category_name
    FROM tickets t
    LEFT JOIN categories c ON t.category_id = c.id
    WHERE t.id = ?
");
$stmt->execute([$ticket_id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    header("Location: tickets.php");
    exit();
}

/* ---------- STATUS BADGE ---------- */
$badge = match($ticket['status']) {
    'pending' => 'bg-warning text-dark',
    'in_progress' => 'bg-primary',
    'resolved' => 'bg-success',
    default => 'bg-secondary'
};

$email = $_SESSION['email'];
$initials = strtoupper($email[0]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ticket Details – Admin</title>

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

/* CARD */
.card{border-radius:0.5rem}

/* CHAT */
.chat-card .chat-body{max-height:400px;overflow-y:auto}
.message{margin-bottom:10px;padding:8px 12px;border-radius:12px;max-width:80%;position:relative}
.message-student{background:#e2e8f0;margin-right:auto}
.message-admin{background:#2563eb;color:white;margin-left:auto}
.msg-time{font-size:11px;color:#6b7280;margin-top:4px;text-align:right}
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
<h5 class="fw-bold mb-0">Ticket #<?= $ticket['id'] ?></h5>
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
<!-- TICKET INFO -->
<div class="card shadow-sm mb-4">
<div class="card-body">
<h5 class="fw-semibold mb-3">Ticket Information</h5>
<div class="row g-3">
<div class="col-md-4"><p><strong>Student:</strong> <?= htmlspecialchars($ticket['student_name']) ?></p></div>
<div class="col-md-4"><p><strong>Category:</strong> <?= htmlspecialchars($ticket['category_name']) ?></p></div>
<div class="col-md-4"><p><strong>Created:</strong> <?= date('d M Y H:i', strtotime($ticket['created_at'])) ?></p></div>
<div class="col-md-4"><p><strong>Status:</strong> <span class="badge <?= $badge ?> px-2 py-1"><?= ucfirst(str_replace('_',' ',$ticket['status'])) ?></span></p></div>
</div>
</div>
</div>

<!-- CHAT -->
<div class="card shadow-sm chat-card mb-4">
    <div class="card-header bg-white">
        <h5 class="fw-semibold mb-0">Conversation</h5>
    </div>
    <div class="card-body chat-body" id="chatMessages">
        <!-- Messages will load here -->
    </div>
    <div class="card-footer bg-white d-flex gap-2">
        <input type="text" id="chatInput" class="form-control" placeholder="Type a message..." required>
        <button id="sendBtn" class="btn btn-primary px-4"><i class="fa-solid fa-paper-plane"></i></button>
    </div>
</div>

<!-- RESOLVE BUTTON -->
<?php if($ticket['status'] !== 'resolved'): ?>
<form method="post" action="ticket-details.php?id=<?= $ticket_id ?>">
    <button name="resolve" class="btn btn-success px-4 py-2">
    <i class="fa-solid fa-check-circle me-2"></i> Mark as Resolved
    </button>
</form>
<?php endif; ?>

</div>
</main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- CHAT JS -->
<script>
const chatMessages = document.getElementById('chatMessages');
const chatInput = document.getElementById('chatInput');
const sendBtn = document.getElementById('sendBtn');
const ticketId = <?= $ticket_id ?>;
const role = 'admin';

function appendMessage(sender, text, time){
    const div = document.createElement('div');
    div.classList.add('message', sender === 'admin' ? 'message-admin' : 'message-student');
    div.innerHTML = `<div class="msg-text">${text}</div><div class="msg-time">${time}</div>`;
    chatMessages.appendChild(div);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Load messages from server
function loadMessages(){
    fetch('chat_send_fetch.php?ticket_id=' + ticketId)
        .then(res => res.json())
        .then(data => {
            chatMessages.innerHTML = '';
            data.forEach(msg => appendMessage(msg.sender, msg.message, msg.time));
        });
}

// Send message
sendBtn.addEventListener('click', () => {
    const text = chatInput.value.trim();
    if(!text) return;
    chatInput.value='';
    appendMessage(role, text, new Date().toLocaleTimeString());
    const form = new FormData();
    form.append('ticket_id', ticketId);
    form.append('message', text);
    fetch('chat_send_fetch.php', { method:'POST', body:form })
        .then(()=> loadMessages());
});

// Auto-refresh every 2 sec
setInterval(loadMessages, 2000);
loadMessages();
</script>

</body>
</html>
