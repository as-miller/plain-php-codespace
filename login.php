<?php
session_start();


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect the form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Define BASE_PATH if not already defined
    if (!defined('BASE_PATH')) {
        define('BASE_PATH', $_SERVER['DOCUMENT_ROOT']);
    }

    // Include config for database connection
    include BASE_PATH . '/config.php';

    try {
        // Query the database for the user's record
        $stmt = $db->prepare("SELECT id, username, password FROM users WHERE username = :username");
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $result = $stmt->execute();

        // Check if the user exists
        if ($result) {
            $user = $result->fetchArray(SQLITE3_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Password is correct, set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                // Redirect to the dashboard
                header("Location: dashboard.php");
                exit;
            } else {
                // Invalid credentials
                echo "Invalid login credentials!";
            }
        } else {
            echo "Database query failed!";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>


<!-- The login form -->
<form method="POST">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br>

    <input type="submit" value="Login">
</form>
