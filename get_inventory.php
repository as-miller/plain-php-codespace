<?php

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $character_id = $_SESSION['character_id'] ?? null;
    $item_id = $_POST['item_id'];
    
    if ($character_id) {
        // Check if the item already exists in the inventory
        $stmt = $db->prepare("SELECT * FROM character_inventory WHERE character_id = :character_id AND item_id = :item_id");
        $stmt->execute(['character_id' => $character_id, 'item_id' => $item_id]);
        $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingItem) {
            // Update quantity if item already exists
            $newQuantity = $existingItem['quantity'] + 1;
            $stmt = $db->prepare("UPDATE character_inventory SET quantity = :quantity WHERE id = :id");
            $stmt->execute(['quantity' => $newQuantity, 'id' => $existingItem['id']]);
        } else {
            // Insert new item into inventory
            $stmt = $db->prepare("INSERT INTO character_inventory (character_id, item_id) VALUES (:character_id, :item_id)");
            $stmt->execute(['character_id' => $character_id, 'item_id' => $item_id]);
        }

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Character not found']);
    }
}
