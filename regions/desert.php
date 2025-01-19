<?php

// Include config first to ensure BASE_PATH is defined
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// Include header (which now manages db connection)
include BASE_PATH . '/includes/header.php';
?>

<div class="container mt-4">
    <h1>The Blazing Desert</h1>
    <p>A vast desert with scorching heat and dangerous sandstorms.</p>
    <!-- Add more content specific to the Desert region -->
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>
