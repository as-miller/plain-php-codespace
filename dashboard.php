<?php
// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include config first to ensure BASE_PATH is defined
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// Include header (which now manages db connection)
include BASE_PATH . '/includes/header.php';

// Verify the database connection
if (!isset($db)) {
    die("Database connection is not initialized.");
}

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit();
}

// Fetch user data for personalized greeting
try {
    $stmt = $db->prepare('SELECT username FROM users WHERE id = :user_id');
    $stmt->bindValue(':user_id', $_SESSION['user_id'], SQLITE3_INTEGER);
    $result = $stmt->execute();
    $user = $result ? $result->fetchArray(SQLITE3_ASSOC) : null;
} catch (Exception $e) {
    error_log("Error fetching user data: " . $e->getMessage());
}

// Page-specific settings
$title = "Dashboard";
?>

<div class="container-fluid mb-5">
    <div class="row">
      

        <!-- Main Content -->
        <div class="col-md-8 mt-5">
            <h1>Welcome to the Dashboard!</h1>
            <p>Hello, User! You are now logged in.</p>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
        
          <!-- Sidebar -->
        <div class="col-md-4 sidebar">
            <?php include 'includes/sidebar.php'; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; // Include the footer if you have one ?>
