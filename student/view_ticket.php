<?php
session_start();
require_once __DIR__ . '/../config/db.php';

/* ---------- AUTH ---------- */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit;
}

/* ---------- USER INFO ---------- */
$studentId = $_SESSION['user_id'];
$name      = $_SESSION['name'] ?? 'Student';
$email     = $_SESSION['email'] ?? '';
$initial   = strtoupper($name[0]);

/* ---------- GET TICKET ---------- */
$ticketId = $_GET['id'] ?? null;
if (!$ticketId) die('Ticket ID is missing.');

$stmt = $conn->prepare("SELECT * FROM tickets WHERE id = ? AND student_id = ?");
$stmt->execute([$ticketId, $studentId]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$ticket) die('Ticket not found or you do not have permission to view it.');

/* ---------- ENSURE KEYS EXIST ---------- */
$ticket['description'] = $ticket['description'] ?? '';
$ticket['attachment'] = $ticket['attachment'] ?? '';
$ticket['category']    = $ticket['category'] ?? 'N/A';
$ticket['status']      = $ticket['status'] ?? 'pending';
$ticket['created_at']  = $ticket['created_at'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Ticket — Unibot</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
body{font-family:'Inter',sans-serif;background:#f4f6f9;}
.sidebar{width:260px;background:#0f172a;color:white;min-height:100vh;}
.sidebar-header{text-align:center;padding:28px 20px;}
.sidebar-header i{font-size:32px;margin-bottom:8px;}
.sidebar-header h5{font-weight:700;font-size:13px;letter-spacing:.08em;}
.sidebar-divider{height:1px;background:rgba(255,255,255,.08);margin:14px 30px;}
.sidebar-section{text-align:center;font-size:11px;letter-spacing:.15em;color:#94a3b8;margin-bottom:6px;font-weight:600;}
.sidebar-nav a{display:flex;flex-direction:column;align-items:center;padding:14px 0;color:#cbd5f5;text-decoration:none;transition:.2s;}
.sidebar-nav a i{font-size:18px;margin-bottom:4px;}
.sidebar-nav a span{font-size:13px;}
.sidebar-nav a:hover,.sidebar-nav a.active{background:rgba(255,255,255,.06);color:white;}
.topbar{background:white;padding:14px 24px;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center;}
.avatar{width:38px;height:38px;border-radius:50%;background:#2563eb;color:white;display:flex;align-items:center;justify-content:center;font-weight:700;cursor:pointer;}
.card{border:none;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.05);}
.ticket-card{padding:20px; margin-bottom:30px;}
.chat-card{max-width:700px;margin:0 auto 30px auto; border-radius:15px; background:white; box-shadow:0 8px 20px rgba(0,0,0,0.08);}
.chat-body{max-height:400px;overflow-y:auto;padding:15px; background:#f9f9f9; border-radius:12px; margin-bottom:10px;}
.message{margin-bottom:10px;padding:10px 15px;border-radius:18px;max-width:75%;position:relative;word-wrap:break-word;}
.message-student{background:#2563eb;color:white;margin-left:auto; border-bottom-right-radius:2px;}
.message-admin{background:#e2e8f0;color:black;margin-right:auto; border-bottom-left-radius:2px;}
.msg-time{font-size:11px;color:#6b7280;margin-top:4px;text-align:right;}
.chat-input{display:flex;gap:10px;margin-top:5px;}
.chat-input input{flex:1;border-radius:25px;border:1px solid #ddd;padding:10px 15px;}
.chat-input button{border:none;background:#2563eb;color:white;padding:0 25px;border-radius:25px;cursor:pointer;transition:0.2s;}
.chat-input button:hover{background:#1d4ed8;}
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
        <a href="dashboard.php"><i class="fa-solid fa-gauge-high"></i><span>Dashboard</span></a>
        <div class="sidebar-divider"></div>
        <div class="sidebar-section">REQUESTS</div>
        <a href="tickets.php"><i class="fa-solid fa-ticket"></i><span>My Tickets</span></a>
        <a href="new_ticket.php"><i class="fa-solid fa-plus-circle"></i><span>New Request</span></a>
        <a href="faq.php"><i class="fa-solid fa-circle-question"></i><span>FAQ</span></a>
        <div class="sidebar-divider"></div>
        <div class="sidebar-section">ACCOUNT</div>
        <a href="../auth/logout.php" class="text-danger"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a>
    </div>
</aside>

<!-- MAIN -->
<main class="flex-grow-1">
<div class="topbar">
    <h5 class="fw-bold mb-0">View Ticket</h5>
    <div class="dropdown">
        <div class="avatar dropdown-toggle" data-bs-toggle="dropdown"><?= $initial ?></div>
        <ul class="dropdown-menu dropdown-menu-end shadow">
            <li class="px-3 py-2 small text-muted"><?= htmlspecialchars($email) ?></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a></li>
        </ul>
    </div>
</div>

<div class="p-4">
<!-- TICKET DETAILS -->
<div class="card ticket-card col-lg-7 mx-auto">
    <h5 class="fw-bold mb-3"><?= htmlspecialchars($ticket['subject']) ?></h5>
    <p><strong>Category:</strong> <?= htmlspecialchars($ticket['category']) ?></p>
    <p><strong>Status:</strong> <?= ucfirst(htmlspecialchars($ticket['status'])) ?></p>
    <p><strong>Created At:</strong> <?= htmlspecialchars($ticket['created_at']) ?></p>
    <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($ticket['description'])) ?: '<em>No description provided</em>' ?></p>
    <?php if (!empty($ticket['attachment'])): ?>
        <p><strong>Attachment:</strong> <a href="../uploads/<?= htmlspecialchars($ticket['attachment']) ?>" target="_blank">View File</a></p>
    <?php endif; ?>
</div>

<!-- CHAT -->
<div class="card chat-card">
    <div class="chat-body" id="chatMessages"></div>
    <div class="chat-input">
        <input type="text" id="chatInput" placeholder="Type a message..." required>
        <button id="sendBtn"><i class="fa-solid fa-paper-plane"></i></button>
    </div>
</div>

<a href="tickets.php" class="btn btn-secondary mt-3 col-lg-7 mx-auto d-block"><i class="fa-solid fa-arrow-left me-1"></i> Back to Tickets</a>
</div>
</main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const chatMessages = document.getElementById('chatMessages');
const chatInput = document.getElementById('chatInput');
const sendBtn = document.getElementById('sendBtn');
const ticketId = <?= $ticketId ?>;
const role = 'student';

function appendMessage(sender, text, time){
    const div = document.createElement('div');
    div.classList.add('message', sender === 'student' ? 'message-student' : 'message-admin');
    div.innerHTML = `<div class="msg-text">${text}</div><div class="msg-time">${time}</div>`;
    chatMessages.appendChild(div);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function loadMessages(){
    fetch('chat_send_fetch.php?ticket_id=' + ticketId)
        .then(res => res.json())
        .then(data => {
            chatMessages.innerHTML = '';
            data.forEach(msg => appendMessage(msg.sender, msg.message, msg.time));
        });
}

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

// Auto-refresh every 2s
setInterval(loadMessages, 2000);
loadMessages();
</script>
</body>
</html>
