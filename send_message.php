<?php

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo 'You must be logged in to send a message.';
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['groupMessage'])) {
    $message = $_POST['groupMessage'];
    $user_id = $_SESSION['user_id'];

    // Insert the message into the database
    $stmt = $db->prepare("INSERT INTO messages (user_id, message, timestamp) VALUES (:user_id, :message, datetime('now'))");
    $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
    $stmt->bindValue(':message', $message, SQLITE3_TEXT);
    $stmt->execute();

    // Optionally, you could return the message here or just a success response
    echo 'Message sent successfully!';
} else {
    echo 'Message not submitted or invalid request.';
}
?>
