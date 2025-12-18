<?php
session_start();

/* ---------- DESTROY SESSION ---------- */
$_SESSION = [];
session_unset();
session_destroy();

/* ---------- PREVENT CACHE ---------- */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Logging out…</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
body{
    font-family:'Inter',sans-serif;
    background:linear-gradient(135deg,#0f172a,#1e293b);
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    color:white
}
.card{
    background:#020617;
    border:none;
    border-radius:16px;
    padding:40px;
    text-align:center;
    width:100%;
    max-width:420px;
    box-shadow:0 30px 60px rgba(0,0,0,.4)
}
.spinner{
    width:52px;
    height:52px;
    border:4px solid rgba(255,255,255,.2);
    border-top-color:#38bdf8;
    border-radius:50%;
    animation:spin 1s linear infinite;
    margin:0 auto 20px
}
@keyframes spin{to{transform:rotate(360deg)}}
</style>

<!-- AUTO REDIRECT -->
<meta http-equiv="refresh" content="2;url=login.php">
</head>

<body>
<div class="card">
    <div class="spinner"></div>
    <h5 class="fw-bold mb-2">You’ve been logged out</h5>
    <p class="text-secondary mb-4">Redirecting to login…</p>

    <a href="login.php" class="btn btn-outline-light px-4">
        <i class="fa fa-arrow-right me-2"></i> Go to Login
    </a>
</div>
</body>
</html>
