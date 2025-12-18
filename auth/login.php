<?php
session_start();
require_once '../config/db.php'; // PDO connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        header("Location: ../login.html?error=empty_fields");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../login.html?error=invalid_email");
        exit();
    }

    // Fetch user using correct column names
    $stmt = $conn->prepare("SELECT id, email, password, role, firstname, lastname FROM users WHERE email = :email LIMIT 1");
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['firstname'] . " " . $user['lastname'];
        $_SESSION['logged_in'] = true;

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: ../student/dashboard.php");
        }

        exit();
    }

    header("Location: ../login.html?error=invalid_credentials");
    exit();
}
else {
    header("Location: ../login.html");
    exit();
}
