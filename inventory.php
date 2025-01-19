<?php

// Fetch the selected character data
$character_id = $_SESSION['character_id'] ?? null;
$inventory = [];
$character = null;

if ($character_id) {
    // Fetch inventory items
    $stmt = $db->prepare("
        SELECT i.id, i.name, i.description, ci.quantity 
        FROM items i
        JOIN character_inventory ci ON i.id = ci.item_id
        WHERE ci.character_id = :character_id
    ");
    $stmt->execute(['character_id' => $character_id]);
    $inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch character details
    $stmt = $db->prepare("SELECT * FROM characters WHERE id = :id");
    $stmt->execute(['id' => $character_id]);
    $character = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Include config first to ensure BASE_PATH is defined
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// Include header (which now manages db connection)
include BASE_PATH . '/includes/header.php';
?>

<div class="container mt-4">
    <h1>Inventory</h1>
    
    <div class="row">
        <!-- Character Stats Section -->
        <div class="col-md-6">
            <h3>Character Stats</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($character['character_name']); ?></p>
            <p><strong>Level:</strong> <span id="characterLevel"><?php echo htmlspecialchars($character['level']); ?></span></p>
            <p><strong>HP:</strong> <span id="characterHP"><?php echo htmlspecialchars($character['current_hit_points']); ?></span> / <?php echo htmlspecialchars($character['max_hit_points']); ?></p>
            <p><strong>XP:</strong> <span id="characterXP"><?php echo htmlspecialchars($character['experience_points']); ?></span></p>
            <p><strong>Strength:</strong> <span id="characterStrength"><?php echo htmlspecialchars($character['strength']); ?></span></p>
            <p><strong>Dexterity:</strong> <span id="characterDexterity"><?php echo htmlspecialchars($character['dexterity']); ?></span></p>
            <p><strong>Constitution:</strong> <span id="characterConstitution"><?php echo htmlspecialchars($character['constitution']); ?></span></p>
            <p><strong>Intelligence:</strong> <span id="characterIntelligence"><?php echo htmlspecialchars($character['intelligence']); ?></span></p>
            <p><strong>Wisdom:</strong> <span id="characterWisdom"><?php echo htmlspecialchars($character['wisdom']); ?></span></p>
            <p><strong>Charisma:</strong> <span id="characterCharisma"><?php echo htmlspecialchars($character['charisma']); ?></span></p>
            <!-- Add more attributes as needed -->
        </div>

        <!-- Inventory Section -->
        <div class="col-md-6">
            <h3>Your Items</h3>
            <ul id="inventoryList" class="list-group">
                <?php foreach ($inventory as $item): ?>
                    <li class="list-group-item">
                        <strong><?php echo htmlspecialchars($item['name']); ?></strong><br>
                        <?php echo htmlspecialchars($item['description']); ?><br>
                        Quantity: <?php echo htmlspecialchars($item['quantity']); ?>
                        <button class="btn btn-danger btn-sm remove-item" data-id="<?php echo htmlspecialchars($item['id']); ?>">Remove</button>
                        <button class="btn btn-success btn-sm use-item" data-id="<?php echo htmlspecialchars($item['id']); ?>">Use</button>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div id="addItemSection" class="mt-4">
                <h4>Add Item</h4>
                <select id="itemSelect" class="form-select mb-2">
                    <option value="">Select an item</option>
                    <!-- Populate this with items from the database -->
                </select>
                <button id="addItemButton" class="btn btn-primary">Add Item</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// JavaScript code for handling item addition, removal, and usage remains unchanged
$(document).ready(function() {
    // Populate item dropdown
    $.get('get_items.php', function(data) {
        const items = JSON.parse(data);
        items.forEach(item => {
            $('#itemSelect').append(`<option value="${item.id}">${item.name}</option>`);
        });
    }).fail(function() {
        alert('Failed to load items. Please try again later.');
    });

    // Handle adding an item to the inventory
    $('#addItemButton').click(function() {
        const itemId = $('#itemSelect').val();
        
        if (itemId) {
            $.post('add_item.php', { item_id: itemId }, function(data) {
                const result = JSON.parse(data);
                if (result.success) {
                    alert('Item added to inventory!');
                    location.reload(); // Reload to update the inventory list
                } else {
                    alert('Failed to add item: ' + result.message);
                }
            }).fail(function() {
                alert('Error occurred while adding the item. Please try again.');
            });
        } else {
            alert('Please select an item to add.');
        }
    });

    // Handle removing an item from the inventory
    $(document).on('click', '.remove-item', function() {
        const itemId = $(this).data('id');
        
        if (confirm('Are you sure you want to remove this item from your inventory?')) {
            $.post('remove_item.php', { item_id: itemId }, function(data) {
                const result = JSON.parse(data);
                if (result.success) {
                    alert('Item removed from inventory!');
                    location.reload(); // Reload to update the inventory list
                } else {
                    alert('Failed to remove item: ' + result.message);
                }
            }).fail(function() {
                alert('Error occurred while removing the item. Please try again.');
            });
        }
    });

    // Handle using an item from the inventory
    $(document).on('click', '.use-item', function() {
        const itemId = $(this).data('id');
        
        $.post('use_item.php', { item_id: itemId }, function(data) {
            const result = JSON.parse(data);
            if (result.success) {
                alert(result.message);
                location.reload(); // Reload to update the inventory list and character HP
            } else {
                alert('Failed to use item: ' + result.message);
            }
        }).fail(function() {
            alert('Error occurred while using the item. Please try again.');
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>
