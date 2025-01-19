<?php
try {
    // Absolute path to the SQLite database
    $dbPath = __DIR__ . '/mydatabase.db';
    
    // Create the directory if it doesn't exist
    $dbDir = dirname($dbPath);
    if (!is_dir($dbDir)) {
        mkdir($dbDir, 0755, true);
    }
    
    // Open/create the SQLite database
    $db = new SQLite3($dbPath);
    $db->enableExceptions(true);
    
    // Test the database connection
    $db->query('SELECT 1');
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
