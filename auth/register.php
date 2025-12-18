<?php
require_once "../config/db.php"; // $conn (PDO)

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fullname = trim($_POST["fullname"] ?? "");
    $email    = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm  = $_POST["confirm_password"] ?? "";

    $parts = explode(" ", $fullname, 2);
    $firstname = $parts[0] ?? "";
    $lastname  = $parts[1] ?? "";

    if (!$fullname || !$email || !$password || !$confirm) {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }

    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    if (!$errors) {
        $check = $conn->prepare("SELECT id FROM users WHERE email=?");
        $check->execute([$email]);
        if ($check->rowCount()) {
            $errors[] = "Email already registered.";
        }
    }

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare(
            "INSERT INTO users (firstname, lastname, email, password, role)
             VALUES (?, ?, ?, ?, 'student')"
        );
        if ($stmt->execute([$firstname, $lastname, $email, $hash])) {
            $success = "Account created successfully. Redirecting...";
            header("refresh:2; url=login.php");
        } else {
            $errors[] = "Registration failed.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Unibot | Sign Up</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- FontAwesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{
    min-height:100vh;
    background:linear-gradient(135deg,#0f172a,#1e3a8a);
    display:flex;
    align-items:center;
    justify-content:center;
    font-family:system-ui;
}
.auth-card{
    max-width:420px;
    width:100%;
    border-radius:18px;
}
.logo{
    width:90px;height:90px;
    border-radius:50%;
    background:linear-gradient(135deg,#2563eb,#1e40af);
    display:flex;
    align-items:center;
    justify-content:center;
    box-shadow:0 12px 30px rgba(37,99,235,.5);
}
.logo i{font-size:40px;color:white;}
.btn-primary{
    background:#2563eb;border:none;
}
.btn-primary:hover{background:#1e40af;}
</style>
</head>

<body>

<div class="card auth-card shadow-lg p-4">

    <!-- LOGO -->
    <div class="text-center mb-4">
        <div class="logo mx-auto mb-3">
            <i class="bi bi-mortarboard-fill"></i>
        </div>
        <h4 class="fw-bold mb-0">UNIBOT</h4>
        <small class="text-muted">Student Assistant Platform</small>
    </div>

    <!-- ERRORS -->
    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $e): ?>
                <div><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- SUCCESS -->
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST">

        <div class="mb-3">
            <label class="form-label fw-semibold">Full Name</label>
            <input type="text" name="fullname" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Email Address</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Password</label>
            <div class="input-group">
                <input type="password" id="pass1" name="password" class="form-control" required>
                <button type="button" class="btn btn-outline-secondary" onclick="toggle('pass1',this)">
                    <i class="fa fa-eye"></i>
                </button>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Confirm Password</label>
            <div class="input-group">
                <input type="password" id="pass2" name="confirm_password" class="form-control" required>
                <button type="button" class="btn btn-outline-secondary" onclick="toggle('pass2',this)">
                    <i class="fa fa-eye"></i>
                </button>
            </div>
        </div>

        <button class="btn btn-primary w-100 fw-semibold py-2">
            <i class="fa-solid fa-user-plus me-1"></i> Create Account
        </button>

        <div class="text-center mt-3">
            <span class="text-muted">Already have an account?</span>
            <a href="login.php" class="fw-semibold text-decoration-none">Sign in</a>
        </div>
    </form>
</div>

<script>
function toggle(id,btn){
    const i=document.getElementById(id);
    const icon=btn.querySelector("i");
    if(i.type==="password"){
        i.type="text"; icon.className="fa fa-eye-slash";
    }else{
        i.type="password"; icon.className="fa fa-eye";
    }
}
</script>

</body>
</html>
