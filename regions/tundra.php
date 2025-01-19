<?php

// Include config first to ensure BASE_PATH is defined
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// Include header (which now manages db connection)
include BASE_PATH . '/includes/header.php';
?>

<div class="container mt-4">
    <h1>The Frosty Tundra</h1>
    <p>A cold, icy landscape where few dare to tread.</p>
    <!-- Add more content specific to the Tundra region -->
</div>

<?php include '../includes/footer.php'; ?>
