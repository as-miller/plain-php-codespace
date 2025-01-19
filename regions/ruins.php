<?php

// Include config first to ensure BASE_PATH is defined
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// Include header (which now manages db connection)
include BASE_PATH . '/includes/header.php';
?>

<div class="container mt-4">
    <h1>The Forgotten Ruins</h1>
    <p>Ancient ruins that tell tales of a lost civilization.</p>
    <!-- Add more content specific to the Ruins region -->
</div>

<?php include '../includes/footer.php'; ?>
