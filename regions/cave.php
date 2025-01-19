<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include config first to ensure BASE_PATH is defined
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// Include header (which now manages db connection)
include BASE_PATH . '/includes/header.php';
?>

<div class="container mt-4">
    <h1>The Dark Cave</h1>
    <p>A dark cave rumored to hold ancient treasures.</p>
    <!-- Add more content specific to the Cave region -->
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>
