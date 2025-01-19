<?php

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

if (isset($_SESSION['user_id'])) {
    $current_user_id = $_SESSION['user_id'];

    // Prepare the SQL query using SQLite3
    $stmt = $db->prepare("SELECT id, username FROM users WHERE id != :current_user_id");

    // Bind the parameter
    $stmt->bindValue(':current_user_id', $current_user_id, SQLITE3_INTEGER);

    // Execute the query and fetch results
    $result = $stmt->execute();

    // Check if the query was successful and fetch all users
    $users = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $users[] = $row; // Add each user to the array
    }

    // Return users as JSON
    echo json_encode($users);
} else {
    // Handle the case where the user isn't logged in
    echo json_encode(['error' => 'User not logged in']);
}
?>
