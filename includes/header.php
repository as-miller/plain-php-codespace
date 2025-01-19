<?php
// Include config only if not already defined
if (!defined('BASE_PATH')) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
}

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize user data and include database connection
include_once BASE_PATH . '/db.php';

// Check if the user is logged in
$user = null;
if (isset($_SESSION['user_id']) && isset($db)) {
    try {
        $stmt = $db->prepare('SELECT username FROM users WHERE id = :user_id');
        $stmt->bindValue(':user_id', $_SESSION['user_id'], SQLITE3_INTEGER);
        $result = $stmt->execute();
        $user = $result ? $result->fetchArray(SQLITE3_ASSOC) : null;
    } catch (Exception $e) {
        error_log("Error fetching user data: " . $e->getMessage());
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? "My Website"; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">My Website</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="create-character.php">Characters</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="game.php">Game</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="inventory.php">Inventory</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Add other content here -->

</body>
</html>
