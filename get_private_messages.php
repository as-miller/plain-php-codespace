<?php

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_GET['recipient_id'])) {
    echo json_encode(['error' => 'Missing required parameters']);
    exit;
}

try {
    $current_user_id = $_SESSION['user_id'];
    $recipient_id = $_GET['recipient_id'];

    $stmt = $db->prepare("
        SELECT 
            pm.*,
            u.username as sender_username,
            datetime(pm.timestamp, 'localtime') as formatted_timestamp
        FROM private_messages pm
        JOIN users u ON pm.sender_id = u.id
        WHERE (pm.sender_id = :current_user_id AND pm.recipient_id = :recipient_id)
        OR (pm.sender_id = :recipient_id AND pm.recipient_id = :current_user_id)
        ORDER BY pm.timestamp ASC
    ");

    $stmt->bindValue(':current_user_id', $current_user_id, SQLITE3_INTEGER);
    $stmt->bindValue(':recipient_id', $recipient_id, SQLITE3_INTEGER);

    $result = $stmt->execute();
    $messages = [];

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        // Format the timestamp for display
        $messages[] = [
            'sender_username' => htmlspecialchars($row['sender_username']),
            'message' => htmlspecialchars($row['message']),
            'timestamp' => $row['formatted_timestamp'],
            'is_sender' => ($row['sender_id'] == $current_user_id)
        ];
    }

    echo json_encode($messages);
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>