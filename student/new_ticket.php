<?php
// Enable errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once __DIR__ . '/../config/db.php';

/* ---------- AUTH ---------- */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit;
}

/* ---------- USER INFO ---------- */
$student_id = $_SESSION['user_id'];
$student_name = $_SESSION['name'] ?? 'Student';
$email = $_SESSION['email'] ?? '';
$initial = strtoupper($student_name[0]);

/* ---------- FORM SUBMISSION ---------- */
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $attachment = null;

    // Handle file upload
    if (!empty($_FILES['attachment']['name'])) {
        $fileName = time() . '_' . basename($_FILES['attachment']['name']);
        $targetDir = __DIR__ . '/../uploads/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetFile)) {
            $attachment = $fileName;
        } else {
            $error = 'Failed to upload attachment.';
        }
    }

    if (!$error) {
        try {
            $stmt = $conn->prepare("
                INSERT INTO tickets 
                (student_id, student_name, subject, category, description, attachment, assigned_to, status) 
                VALUES 
                (:student_id, :student_name, :subject, :category, :description, :attachment, :assigned_to, :status)
            ");

            $assigned_to = null;
            $status = 'pending';

            $stmt->bindValue(':student_id', $student_id, PDO::PARAM_INT);
            $stmt->bindValue(':student_name', $student_name, PDO::PARAM_STR);
            $stmt->bindValue(':subject', $subject, PDO::PARAM_STR);
            $stmt->bindValue(':category', $category, PDO::PARAM_STR);
            $stmt->bindValue(':description', $description, PDO::PARAM_STR);
            $stmt->bindValue(':attachment', $attachment, PDO::PARAM_STR);
            $stmt->bindValue(':assigned_to', $assigned_to, PDO::PARAM_NULL);
            $stmt->bindValue(':status', $status, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $success = true;
            } else {
                $error = 'Failed to create ticket.';
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>New Ticket — Unibot</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
body{font-family:'Inter',sans-serif;background:#f4f6f9;}
.sidebar{width:260px;background:#0f172a;min-height:100vh;color:white;}
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
.avatar{width:38px;height:38px;border-radius:50%;background:#2563eb;color:white;display:flex;align-items:center;justify-content:center;font-weight:700;}
.card{border:none;border-radius:12px;}
.form-label{font-weight:600;}
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
        <a href="dashboard.php"><i class="fa-solid fa-house"></i><span>Dashboard</span></a>
        <div class="sidebar-divider"></div>
        <div class="sidebar-section">REQUESTS</div>
        <a href="tickets.php"><i class="fa-solid fa-ticket"></i><span>My Tickets</span></a>
        <a href="new_ticket.php" class="active"><i class="fa-solid fa-circle-plus"></i><span>New Request</span></a>
        <a href="faq.php"><i class="fa-solid fa-circle-question"></i><span>FAQ</span></a>
        <div class="sidebar-divider"></div>
        <div class="sidebar-section">ACCOUNT</div>
        <a href="../auth/logout.php" class="text-danger"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a>
    </div>
</aside>

<!-- MAIN -->
<main class="flex-grow-1">
<div class="topbar">
    <h5 class="fw-bold mb-0">Submit a New Ticket</h5>
    <div class="dropdown">
        <div class="avatar dropdown-toggle" data-bs-toggle="dropdown"><?= $initial ?></div>
        <ul class="dropdown-menu dropdown-menu-end shadow">
            <li class="px-3 py-2 text-muted small"><?= htmlspecialchars($email) ?></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
        </ul>
    </div>
</div>

<div class="p-4">
    <?php if($success): ?>
        <div class="alert alert-success">Ticket submitted successfully!</div>
    <?php elseif($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card shadow-sm p-4 col-lg-7 mx-auto">
        <form method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label class="form-label">Subject</label>
                <input type="text" class="form-control" name="subject" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Category</label>
                <select class="form-select" name="category" required>
                    <option disabled selected value="">Select category</option>
                    <option>Administrative Documents</option>
                    <option>Tuition / Finance</option>
                    <option>Housing</option>
                    <option>Library / Resources</option>
                    <option>Exam Results / Grades</option>
                    <option>Other</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" rows="4" name="description" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Attachment (optional)</label>
                <input type="file" class="form-control" name="attachment">
                <small class="text-muted">PDF, JPG, PNG</small>
            </div>

            <button class="btn btn-primary w-100 fw-semibold">
                <i class="fa-solid fa-paper-plane me-2"></i> Submit Ticket
            </button>
        </form>
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
