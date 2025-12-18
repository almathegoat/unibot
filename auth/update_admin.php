<?php
require_once '../config/db.php';

$newPassword = password_hash("Admin1234", PASSWORD_BCRYPT);

$stmt = $conn->prepare("UPDATE users SET password = :pwd WHERE email = 'admin@example.com'");
$stmt->bindParam(":pwd", $newPassword);
$stmt->execute();

echo "Password updated!";
