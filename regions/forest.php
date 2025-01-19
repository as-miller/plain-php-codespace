<?php

// Page-specific logic
$region_name = "The Enchanted Forest";
$description = "A mystical forest filled with magical creatures and hidden secrets.";

// Include config first to ensure BASE_PATH is defined
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// Include header (which now manages db connection)
include BASE_PATH . '/includes/header.php';
?>
<div class="container mt-4">
    <h1><?php echo htmlspecialchars($region_name); ?></h1>
    <p><?php echo htmlspecialchars($description); ?></p>
</div>
<?php include BASE_PATH . '/includes/footer.php'; ?>
