<?php
// send_private_message.php

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sender_id = $_SESSION['user_id'] ?? null;
    $recipient_id = $_POST['recipient_id'] ?? null;
    $message = $_POST['message'] ?? null;

    if (!$sender_id || !$recipient_id || !$message) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required data']);
        exit;
    }

    try {
        $stmt = $db->prepare("INSERT INTO private_messages (sender_id, recipient_id, message, timestamp) VALUES (:sender_id, :recipient_id, :message, datetime('now'))");
        
        $stmt->bindValue(':sender_id', $sender_id, SQLITE3_INTEGER);
        $stmt->bindValue(':recipient_id', $recipient_id, SQLITE3_INTEGER);
        $stmt->bindValue(':message', $message, SQLITE3_TEXT);
        
        $result = $stmt->execute();

        if ($result) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to insert message']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>