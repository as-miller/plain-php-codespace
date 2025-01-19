<?php
// Start output buffering to avoid "headers already sent" error
ob_start();

session_start();  // This must be the very first line of the file
ini_set('display_errors', 1);
error_reporting(E_ALL);
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include config first to ensure BASE_PATH is defined
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// Include header (which now manages db connection)
include BASE_PATH . '/includes/header.php';

$title = "Create Character";

// Handle form submission for character creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_character'])) {
    // Get form data
    $character_name = $_POST['character_name'];
    $class_and_level = $_POST['class_and_level'];
    $race = $_POST['race'];
    $background = $_POST['background'];
    $alignment = $_POST['alignment'];
    $player_name = $_POST['player_name'];
    $experience_points = $_POST['experience_points'];
    $strength = $_POST['strength'];
    $dexterity = $_POST['dexterity'];
    $constitution = $_POST['constitution'];
    $intelligence = $_POST['intelligence'];
    $wisdom = $_POST['wisdom'];
    $charisma = $_POST['charisma'];

    // Calculate max hit points (simplified)
    $max_hit_points = 10 + floor(($constitution - 10) / 2);

    // Prepare and execute the insertion query
    $stmt = $db->prepare("INSERT INTO characters 
        (user_id, character_name, class_and_level, race, background, alignment, player_name, experience_points, 
        strength, dexterity, constitution, intelligence, wisdom, charisma, max_hit_points, current_hit_points, 
        armor_class, initiative, speed, proficiency_bonus, inspiration, gold, equipment, spells, features_and_traits)
        VALUES 
        (:user_id, :character_name, :class_and_level, :race, :background, :alignment, :player_name, :experience_points, 
        :strength, :dexterity, :constitution, :intelligence, :wisdom, :charisma, :max_hit_points, :current_hit_points, 
        :armor_class, :initiative, :speed, :proficiency_bonus, :inspiration, :gold, :equipment, :spells, :features_and_traits)");

    // Bind values to the statement
    $stmt->bindValue(':user_id', $_SESSION['user_id'], SQLITE3_INTEGER);
    $stmt->bindValue(':character_name', $character_name, SQLITE3_TEXT);
    $stmt->bindValue(':class_and_level', $class_and_level, SQLITE3_TEXT);
    $stmt->bindValue(':race', $race, SQLITE3_TEXT);
    $stmt->bindValue(':background', $background, SQLITE3_TEXT);
    $stmt->bindValue(':alignment', $alignment, SQLITE3_TEXT);
    $stmt->bindValue(':player_name', $player_name, SQLITE3_TEXT);
    $stmt->bindValue(':experience_points', $experience_points, SQLITE3_INTEGER);
    $stmt->bindValue(':strength', $strength ?: 10, SQLITE3_INTEGER);  // Default value for strength
    $stmt->bindValue(':dexterity', $dexterity ?: 10, SQLITE3_INTEGER);  // Default value for dexterity
    $stmt->bindValue(':constitution', $constitution ?: 10, SQLITE3_INTEGER);  // Default value for constitution
    $stmt->bindValue(':intelligence', $intelligence ?: 10, SQLITE3_INTEGER);  // Default value for intelligence
    $stmt->bindValue(':wisdom', $wisdom ?: 10, SQLITE3_INTEGER);  // Default value for wisdom
    $stmt->bindValue(':charisma', $charisma ?: 10, SQLITE3_INTEGER);  // Default value for charisma
    $stmt->bindValue(':max_hit_points', $max_hit_points, SQLITE3_INTEGER);
    $stmt->bindValue(':current_hit_points', $max_hit_points, SQLITE3_INTEGER);

    // Additional fields with default values (if not provided)
    $stmt->bindValue(':armor_class', 10, SQLITE3_INTEGER);  // Default armor class
    $stmt->bindValue(':initiative', 0, SQLITE3_INTEGER);  // Default initiative
    $stmt->bindValue(':speed', 30, SQLITE3_INTEGER);  // Default speed
    $stmt->bindValue(':proficiency_bonus', 2, SQLITE3_INTEGER);  // Default proficiency bonus
    $stmt->bindValue(':inspiration', 0, SQLITE3_INTEGER);  // Default inspiration
    $stmt->bindValue(':gold', 0, SQLITE3_INTEGER);  // Default gold
    $stmt->bindValue(':equipment', '', SQLITE3_TEXT);  // Default empty equipment
    $stmt->bindValue(':spells', '', SQLITE3_TEXT);  // Default empty spells
    $stmt->bindValue(':features_and_traits', '', SQLITE3_TEXT);  // Default empty features

    // Execute the statement
    $stmt->execute();

    // Redirect to the same page to refresh the character list
    header("Location: create-character.php");
    exit();
}

// Handle character deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Check if delete_id is a valid number
    if (is_numeric($delete_id)) {
        // Prepare and execute the delete query
        $stmt = $db->prepare("DELETE FROM characters WHERE id = :id AND user_id = :user_id");
        $stmt->bindValue(':id', $delete_id, SQLITE3_INTEGER);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], SQLITE3_INTEGER);
        $stmt->execute();

        // Redirect to prevent resubmission
        header("Location: create-character.php");
        exit();
    }
}

// Fetch characters for the logged-in user
$stmt = $db->prepare("SELECT * FROM characters WHERE user_id = :user_id");
$stmt->bindValue(':user_id', $_SESSION['user_id'], SQLITE3_INTEGER);
$result = $stmt->execute();

// Fetch all rows
$characters = [];
while ($character = $result->fetchArray(SQLITE3_ASSOC)) {
    $characters[] = $character;
}
?>


<div class="container mt-5">
    <h1>Create Character</h1>
    <div class="row">
        <!-- Character List -->
        <div class="col-md-6">
            <h3>Your Characters</h3>
            <ul class="list-group">
                <?php if ($characters): ?>
                    <?php foreach ($characters as $character): ?>
                        <li class="list-group-item">
                            <strong><?= htmlspecialchars($character['character_name']) ?></strong><br>
                            <?= htmlspecialchars($character['class_and_level']) ?><br>
                            <?= htmlspecialchars($character['race']) ?><br>
                            <?= htmlspecialchars($character['background']) ?><br>
                            HP: <?= htmlspecialchars($character['current_hit_points']) ?>/<?= htmlspecialchars($character['max_hit_points']) ?><br>
                            STR: <?= htmlspecialchars($character['strength']) ?>, 
                            DEX: <?= htmlspecialchars($character['dexterity']) ?>, 
                            CON: <?= htmlspecialchars($character['constitution']) ?>, 
                            INT: <?= htmlspecialchars($character['intelligence']) ?>, 
                            WIS: <?= htmlspecialchars($character['wisdom']) ?>, 
                            CHA: <?= htmlspecialchars($character['charisma']) ?>
                            <!-- Delete Button -->
                            <a href="create-character.php?delete_id=<?= $character['id'] ?>" class="btn btn-danger btn-sm float-end" onclick="return confirm('Are you sure you want to delete this character?')">Delete</a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="list-group-item">You don't have any characters yet.</li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Character Creation Form -->
        <div class="col-md-6">
            <h3>Create a New Character</h3>
            <form method="POST">
                <div class="accordion" id="characterAccordion">
                    <!-- Basic Information -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="basicInfoHeader">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#basicInfoCollapse" aria-expanded="true" aria-controls="basicInfoCollapse">
                                Basic Information
                            </button>
                        </h2>
                        <div id="basicInfoCollapse" class="accordion-collapse collapse show" aria-labelledby="basicInfoHeader" data-bs-parent="#characterAccordion">
                            <div class="accordion-body">
                                <div class="mb-3">
                                    <label for="character_name" class="form-label">Character Name</label>
                                    <input type="text" class="form-control" id="character_name" name="character_name" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="class_and_level" class="form-label">Class & Level</label>
                                        <input type="text" class="form-control" id="class_and_level" name="class_and_level" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="race" class="form-label">Race</label>
                                        <input type="text" class="form-control" id="race" name="race" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="background" class="form-label">Background</label>
                                        <input type="text" class="form-control" id="background" name="background" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="alignment" class="form-label">Alignment</label>
                                        <input type="text" class="form-control" id="alignment" name="alignment" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ability Scores -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="abilitiesHeader">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#abilitiesCollapse" aria-expanded="false" aria-controls="abilitiesCollapse">
                                Ability Scores
                            </button>
                        </h2>
                        <div id="abilitiesCollapse" class="accordion-collapse collapse" aria-labelledby="abilitiesHeader" data-bs-parent="#characterAccordion">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-2">
                                        <label for="strength" class="form-label">STR</label>
                                        <input type="number" class="form-control" id="strength" name="strength" min="3" max="18" required>
                                    </div>
                                    <div class="col-2">
                                        <label for="dexterity" class="form-label">DEX</label>
                                        <input type="number" class="form-control" id="dexterity" name="dexterity" min="3" max="18" required>
                                    </div>
                                    <div class="col-2">
                                        <label for="constitution" class="form-label">CON</label>
                                        <input type="number" class="form-control" id="constitution" name="constitution" min="3" max="18" required>
                                    </div>
                                    <div class="col-2">
                                        <label for="intelligence" class="form-label">INT</label>
                                        <input type="number" class="form-control" id="intelligence" name="intelligence" min="3" max="18" required>
                                    </div>
                                    <div class="col-2">
                                        <label for="wisdom" class="form-label">WIS</label>
                                        <input type="number" class="form-control" id="wisdom" name="wisdom" min="3" max="18" required>
                                    </div>
                                    <div class="col-2">
                                        <label for="charisma" class="form-label">CHA</label>
                                        <input type="number" class="form-control" id="charisma" name="charisma" min="3" max="18" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="accordion-item">
                        <div class="accordion-body">
                            <button type="submit" class="btn btn-primary" name="create_character">Create Character</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include BASE_PATH . '/includes/footer.php';
ob_end_flush();
?>
