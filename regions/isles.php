<?php

// Include config first to ensure BASE_PATH is defined
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// Include header (which now manages db connection)
include BASE_PATH . '/includes/header.php';
?>

<div class="container mt-4">
    <h1>The Mystic Isles</h1>
    <p>Islands shrouded in mystery and magic.</p>
    <!-- Add more content specific to the Isles region -->
</div>

<?php include '../includes/footer.php'; ?>
