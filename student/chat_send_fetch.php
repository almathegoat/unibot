<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit();
}

$ticket_id = (int)($_POST['ticket_id'] ?? $_GET['ticket_id'] ?? 0);
$role = $_SESSION['role']; // 'admin' or 'student'

// Send a message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    $msg = trim($_POST['message']);
    $stmt = $conn->prepare("INSERT INTO ticket_messages (ticket_id, sender, message) VALUES (?, ?, ?)");
    $stmt->execute([$ticket_id, $role, $msg]);
    echo json_encode(['status'=>'ok']);
    exit;
}

// Fetch messages
$stmt = $conn->prepare("
    SELECT sender, message, DATE_FORMAT(created_at,'%H:%i') AS time 
    FROM ticket_messages 
    WHERE ticket_id = ? 
    ORDER BY created_at ASC
");
$stmt->execute([$ticket_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($messages);
