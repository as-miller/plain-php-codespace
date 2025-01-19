<?php
// Check if the session is already started before starting it again
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die('You must be logged in to send a message.');
}

try {
    // Debug: Print session user ID
    echo "Session User ID: " . $_SESSION['user_id'] . "<br>";

    // Correct the database path
    $dbPath = __DIR__ . '/../mydatabase.db'; // Navigate up one directory to the root
    if (!file_exists($dbPath)) {
        die("Database file not found at: " . $dbPath);
    }

    // Initialize SQLite database connection
    $db = new SQLite3($dbPath);
    echo "Database connected successfully.<br>";

    // Handle roll submission via AJAX
    if (isset($_POST['action']) && $_POST['action'] === 'roll') {
        if (!isset($_POST['diceType']) || !isset($_POST['result'])) {
            die("Invalid dice roll data.");
        }

        $diceType = (int)$_POST['diceType'];
        $result = (int)$_POST['result'];
        $userId = (int)$_SESSION['user_id'];

        $stmt = $db->prepare('INSERT INTO dice_rolls (user_id, dice_type, result) VALUES (:user_id, :dice_type, :result)');
        if (!$stmt) {
            error_log("Failed to prepare INSERT statement: " . $db->lastErrorMsg());
            die("Failed to prepare statement. Check logs.");
        }
        $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
        $stmt->bindValue(':dice_type', $diceType, SQLITE3_INTEGER);
        $stmt->bindValue(':result', $result, SQLITE3_INTEGER);
        if (!$stmt->execute()) {
            error_log("Failed to insert roll: " . $db->lastErrorMsg());
            die("Failed to insert roll.");
        }
        exit; // End AJAX request
    }

    // Handle clear action
    if (isset($_POST['action']) && $_POST['action'] === 'clear') {
        $userId = (int)$_SESSION['user_id'];
        $stmt = $db->prepare('DELETE FROM dice_rolls WHERE user_id = :user_id');
        if (!$stmt) {
            error_log("Failed to prepare DELETE statement: " . $db->lastErrorMsg());
            die("Failed to prepare statement. Check logs.");
        }
        $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
        if (!$stmt->execute()) {
            error_log("Failed to delete rolls: " . $db->lastErrorMsg());
            die("Failed to delete rolls.");
        }
        exit; // End AJAX request after clearing
    }

    // Get existing rolls for the current user
    $userId = (int)$_SESSION['user_id'];
    $stmt = $db->prepare('SELECT * FROM dice_rolls WHERE user_id = :user_id ORDER BY timestamp DESC LIMIT 50');
    if (!$stmt) {
        error_log("Failed to prepare SELECT statement: " . $db->lastErrorMsg());
        die("Failed to prepare statement. Check logs.");
    }
    $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
    $rolls = $stmt->execute();
    if (!$rolls) {
        error_log("Failed to fetch rolls: " . $db->lastErrorMsg());
        die("Failed to fetch rolls.");
    }

} catch (Exception $e) {
    // Log the error but don't display it to users
    error_log("Database error: " . $e->getMessage());
    die("An error occurred. Check logs.");
}
?>

<div class="card border-0 mt-5">
    <div class="card-body">
        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs mb-3" id="sidebar-tabs" role="tablist">
            <li class="nav-item">
                <a href="#group-chat" class="nav-link active" data-bs-toggle="tab" role="tab" aria-controls="group-chat" aria-selected="true">
                    Group Chat
                </a>
            </li>
            <li class="nav-item">
                <a href="#private-messages" class="nav-link" data-bs-toggle="tab" role="tab" aria-controls="private-messages" aria-selected="false">
                    DM
                </a>
            </li>
            <li class="nav-item">
                <a href="#characters" class="nav-link" data-bs-toggle="tab" role="tab" aria-controls="characters" aria-selected="false">
                    Characters
                </a>
            </li>
            <li class="nav-item">
                <a href="#dice" class="nav-link" data-bs-toggle="tab" role="tab" aria-controls="dice" aria-selected="false">
                    Dice
                </a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="sidebar-tab-content">
            <!-- Group Chat -->
            <div class="tab-pane show active" id="group-chat" role="tabpanel" aria-labelledby="group-chat-tab">
                <div id="groupChatMessages" class="border rounded p-2" style="height: 300px; overflow-y: auto; background-color: #fff;">
                    <!-- Group chat messages will appear here -->
                </div>
                <form id="groupChatForm" class="mt-2" method="POST" action="send_message.php">
                    <div class="input-group">
                        <input type="text" id="groupMessage" name="groupMessage" class="form-control" placeholder="Type your message..." required>
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                </form>
            </div>

            <!-- Private Messages -->
            <div class="tab-pane" id="private-messages" role="tabpanel" aria-labelledby="private-messages-tab">
                <div class="mb-3">
                    <select id="recipientSelect" class="form-select">
                        <option value="" disabled selected>Select a user to chat with</option>
                        <?php
                        // Load all users dynamically except the current logged-in user
                        include 'db.php'; // Include your database connection

                        $current_user_id = $_SESSION['user_id'];
                        $stmt = $db->prepare("SELECT id, username FROM users WHERE id != :current_user_id");
                        $stmt->bindValue(':current_user_id', $current_user_id, SQLITE3_INTEGER);
                        $result = $stmt->execute();

                        // Loop through the users and create options for the select dropdown
                        while ($user = $result->fetchArray(SQLITE3_ASSOC)) {
                            echo '<option value="' . $user['id'] . '">' . htmlspecialchars($user['username']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div id="privateMessageList" class="border rounded p-2" style="height: 300px; overflow-y: auto;">
                    <!-- Private messages will be displayed here -->
                </div>
                <form id="privateMessageForm" class="mt-2">
                    <div class="input-group">
                        <input type="text" id="privateMessage" class="form-control" placeholder="Type your message...">
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                </form>
            </div>

            <!-- Characters -->
            <div class="tab-pane" id="characters" role="tabpanel" aria-labelledby="characters-tab">
                <div id="characterList" class="border rounded p-2" style="height: 300px; overflow-y: auto;">
                    <?php
                    // Check if the session is started and if the user is logged in
                    if (isset($_SESSION['user_id'])) {
                        include 'db.php'; // Your database connection

                        // Fetch the first character for the logged-in user
                        $stmt = $db->prepare("SELECT * FROM characters WHERE user_id = :user_id LIMIT 1");
                        $stmt->bindValue(':user_id', $_SESSION['user_id'], SQLITE3_INTEGER);
                        $result = $stmt->execute();

                        $character = $result->fetchArray(SQLITE3_ASSOC); // Fetch the first character

                        // Prepare character output
                        $characterOutput = '';
                        if ($character) {
                            $characterOutput .= '
                                <div class="character-item">
                                    <strong>' . htmlspecialchars($character['character_name']) . '</strong><br>
                                    ' . htmlspecialchars($character['class_and_level']) . '<br>
                                    ' . htmlspecialchars($character['race']) . '<br>
                                    ' . htmlspecialchars($character['background']) . '<br>
                                    <a href="edit-character.php?id=' . $character['id'] . '" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="delete-character.php?id=' . $character['id'] . '" class="btn btn-danger btn-sm">Delete</a>
                                </div>
                            ';
                        } else {
                            $characterOutput = '<p>No characters found. Create one now!</p>';
                        }

                        // Display the character output
                        echo $characterOutput;
                    } else {
                        // If not logged in, redirect to login
                        header("Location: login.php");
                        exit();
                    }
                    ?>
                </div>
                <a href="create-character.php" class="btn btn-success mt-2 w-100">Create New Character</a>
            </div>
            <div class="tab-pane" id="dice" role="tabpanel" aria-labelledby="dice-tab">
    <div class="dice-container p-4">
        <div class="controls mb-3">
            <select id="diceType" class="form-select d-inline-block w-auto me-2">
                <option value="4">d4</option>
                <option value="6">d6</option>
                <option value="8">d8</option>
                <option value="10">d10</option>
                <option value="12">d12</option>
                <option value="20" selected>d20</option>
                <option value="100">d100</option>
            </select>
            <button onclick="rollDice()" class="btn btn-primary me-2">Roll</button>
            <button onclick="clearLog()" class="btn btn-secondary">Clear</button>
        </div>
        <div id="diceLog" class="dice-log border p-3" style="height: 300px; overflow-y: auto;">
            <?php while ($row = $rolls->fetchArray(SQLITE3_ASSOC)): ?>
                <div class="log-entry mb-2">
                    <span class="text-muted">[<?= htmlspecialchars($row['timestamp']) ?>]</span>
                    <span class="ms-2">d<?= htmlspecialchars($row['dice_type']) ?> roll:</span>
                    <span class="fw-bold ms-2"><?= htmlspecialchars($row['result']) ?></span>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    </div>
</div>
