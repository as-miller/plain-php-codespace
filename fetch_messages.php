<?php

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

// Set PHP timezone
date_default_timezone_set('America/New_York');

// Check if the database connection is valid
if (!$db) {
    die('Database connection failed');
}

// Fetch the messages with the usernames and timestamps
try {
    $stmt = $db->prepare("
        SELECT 
            m.*, 
            u.username,
            m.timestamp as local_timestamp  
        FROM messages m 
        JOIN users u ON m.user_id = u.id 
        ORDER BY m.timestamp DESC 
        LIMIT 10
    ");

    $result = $stmt->execute(); // Execute the statement

    // Initialize an array to hold messages
    $messages = [];
    
    // Fetch all results into the array
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $messages[] = $row;
    }

} catch (Exception $e) {
    echo "Error fetching messages: " . $e->getMessage();
}

// Display messages
foreach ($messages as $message) {
    // Only create DateTime object if timestamp exists
    if (!empty($message['local_timestamp'])) {
        $timestamp = new DateTime($message['local_timestamp']);
        // Set timezone to New York for formatting
        $timestamp->setTimezone(new DateTimeZone('America/New_York'));
        $formattedTimestamp = $timestamp->format('M d, h:i A');
    } else {
        $formattedTimestamp = 'Time unknown';
    }

    echo "
        <div>
            <strong>" . htmlspecialchars($message['username']) . "</strong>: " . 
            htmlspecialchars($message['message']) . " 
            <span style='font-size: 0.8em; color: #888;'>($formattedTimestamp)</span>
        </div>
    ";
}
?>
