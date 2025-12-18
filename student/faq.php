<?php
session_start();
require_once __DIR__ . '/../config/db.php';

/* ---------- AUTH ---------- */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit;
}

$studentId = $_SESSION['user_id'];
$name      = $_SESSION['name'] ?? 'Student';
$email     = $_SESSION['email'] ?? '';
$initials  = strtoupper($name[0]);

/* ---------- HANDLE FAQ QUESTION SUBMISSION ---------- */
$success = false;
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['question'])) {
    $question = trim($_POST['question']);
    if ($question !== '') {
        $stmt = $conn->prepare("INSERT INTO faq_questions (student_id, question, created_at) VALUES (?, ?, NOW())");
        try {
            $stmt->execute([$studentId, $question]);
            $_SESSION['faq_success'] = true;
        } catch (Exception $e) {
            $_SESSION['faq_error'] = "Failed to submit question: " . $e->getMessage();
        }
    } else {
        $_SESSION['faq_error'] = "Question cannot be empty.";
    }
    // Redirect to avoid form resubmission
    header("Location: faq.php");
    exit;
}

/* ---------- HANDLE DELETE ---------- */
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM faq_questions WHERE id = ? AND student_id = ?");
    $stmt->execute([$deleteId, $studentId]);
    // Redirect back to current page to avoid issues
    header("Location: faq.php");
    exit;
}

// Check for session messages
if (isset($_SESSION['faq_success'])) {
    $success = true;
    unset($_SESSION['faq_success']);
}
if (isset($_SESSION['faq_error'])) {
    $error = $_SESSION['faq_error'];
    unset($_SESSION['faq_error']);
}

/* ---------- FETCH FAQ ---------- */
$stmt = $conn->query("SELECT * FROM faqs ORDER BY created_at DESC");
$faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ---------- FETCH STUDENT QUESTIONS ---------- */
$stmt2 = $conn->prepare("SELECT * FROM faq_questions WHERE student_id = ? ORDER BY created_at DESC");
$stmt2->execute([$studentId]);
$studentQuestions = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>


<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>FAQ — Unibot</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
body { font-family:'Inter',sans-serif; background:#f4f6f9; }
.sidebar{width:260px;background:#0f172a;color:white;min-height:100vh;}
.sidebar-header{text-align:center;padding:28px 20px;}
.sidebar-header i{font-size:32px;margin-bottom:8px;}
.sidebar-header h5{font-weight:700;font-size:13px;letter-spacing:.08em;}
.sidebar-divider{height:1px;background:rgba(255,255,255,.08);margin:14px 30px;}
.sidebar-section{text-align:center;font-size:11px;letter-spacing:.15em;color:#94a3b8;margin-bottom:6px;font-weight:600;}
.sidebar-nav a{display:flex;flex-direction:column;align-items:center;padding:14px 0;color:#cbd5f5;text-decoration:none;}
.sidebar-nav a i{font-size:18px;margin-bottom:4px;}
.sidebar-nav a span{font-size:13px;}
.sidebar-nav a.active, .sidebar-nav a:hover{background:rgba(255,255,255,.06);color:white;}
.topbar{background:white;padding:14px 24px;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center;}
.avatar{width:38px;height:38px;border-radius:50%;background:#2563eb;color:white;display:flex;align-items:center;justify-content:center;font-weight:700;cursor:pointer;}
.accordion-button:focus { box-shadow: none; }
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
        <a href="faq.php" class="active"><i class="fa-solid fa-circle-question"></i><span>FAQ</span></a>
        <div class="sidebar-divider"></div>
        <div class="sidebar-section">ACCOUNT</div>
        <a href="../auth/logout.php" class="text-danger"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a>
    </div>
</aside>

<!-- MAIN -->
<main class="flex-grow-1">
<div class="topbar">
    <h5 class="fw-bold mb-0">FAQ</h5>
    <div class="dropdown">
        <div class="avatar dropdown-toggle" data-bs-toggle="dropdown"><?= $initials ?></div>
        <ul class="dropdown-menu dropdown-menu-end shadow">
            <li class="px-3 py-2 small text-muted"><?= htmlspecialchars($email) ?></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a></li>
        </ul>
    </div>
</div>

<div class="p-4">

<?php if($success): ?>
<div class="alert alert-success">Your question has been submitted successfully!</div>
<?php elseif($error): ?>
<div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<h4 class="fw-bold mb-3">Frequently Asked Questions</h4>
<input type="text" id="faqSearch" class="form-control mb-4" placeholder="Search a question...">

<div class="accordion" id="faqAccordion">
<?php if(empty($faqs)): ?>
<div class="text-muted">No FAQs available.</div>
<?php else: foreach($faqs as $faq): ?>
<div class="accordion-item mb-2">
    <h2 class="accordion-header" id="heading<?= $faq['id'] ?>">
        <button class="accordion-button collapsed faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?= $faq['id'] ?>" aria-expanded="false" aria-controls="faq<?= $faq['id'] ?>">
            <?= htmlspecialchars($faq['question']) ?>
        </button>
    </h2>
    <div id="faq<?= $faq['id'] ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $faq['id'] ?>" data-bs-parent="#faqAccordion">
        <div class="accordion-body"><?= nl2br(htmlspecialchars($faq['answer'])) ?></div>
    </div>
</div>
<?php endforeach; endif; ?>
</div>

<hr class="my-4">

<h5 class="fw-semibold mb-3">Ask a Question</h5>
<form method="POST" action="faq.php">
    <textarea name="question" class="form-control mb-3" rows="3" placeholder="Write your question here..." required></textarea>
    <button class="btn btn-primary"><i class="fa-solid fa-paper-plane me-1"></i> Submit</button>
</form>

<?php if(!empty($studentQuestions)): ?>
<h5 class="fw-semibold mt-4 mb-2">Your Questions</h5>
<ul class="list-group mb-3">
<?php foreach($studentQuestions as $q): ?>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <?= htmlspecialchars($q['question']) ?>
        <a href="faq.php?delete_id=<?= $q['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this question?');"><i class="fa-solid fa-trash"></i></a>
    </li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<footer class="text-center mt-4 text-muted small">
    © 2025 Unibot — All rights reserved
</footer>

</div>
</main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('faqSearch').addEventListener('keyup', function(){
    let v = this.value.toLowerCase();
    document.querySelectorAll('.accordion-item').forEach(i=>{
        let q = i.querySelector('.faq-question').innerText.toLowerCase();
        i.style.display = q.includes(v) ? '' : 'none';
    });
});
</script>
</body>
</html>