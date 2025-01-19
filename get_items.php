<?php

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

// Fetch all items from the database
try {
    $stmt = $db->query("SELECT id, name FROM items"); // Execute the query

    // Initialize an array to hold items
    $items = [];
    
    // Fetch all results into the array
    while ($row = $stmt->fetchArray(SQLITE3_ASSOC)) {
        $items[] = $row; // Store each item in the array
    }

    // Return the items as a JSON response
    echo json_encode($items);
} catch (Exception $e) {
    echo "Error fetching items: " . $e->getMessage();
}
?>
