<?php
session_start();

// Include config first to ensure BASE_PATH is defined
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// Include header (which now manages db connection)
include BASE_PATH . '/includes/header.php';
?>

<div class="container-fluid mt-4">
    <h1>World Map</h1>

    <img src="images/2.png" usemap="#image-map" width="600" height="400">
    
    <canvas id="worldMapCanvas" width="800" height="600" style="border:1px solid #000;"></canvas>

    <map name="image-map">
    <!-- Define a rectangular clickable area -->
    <area shape="rect" coords="34,44,270,350" href="regions/forest.php" alt="Link 1">
    
    <!-- Define a circular clickable area -->
    <area shape="circle" coords="300,200,50" href="link2.html" alt="Link 2">
    
    <!-- Define a polygonal clickable area -->
    <area shape="poly" coords="400,100,450,200,350,200" href="link3.html" alt="Link 3">
</map>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    const canvas = document.getElementById('worldMapCanvas');
    const ctx = canvas.getContext('2d');

    // Draw the world map with regions
    function drawWorldMap() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Region 1: The Enchanted Forest
        ctx.fillStyle = '#8ED6FF'; // Color for Forest
        ctx.fillRect(50, 50, 200, 150); // Coordinates and size
        ctx.fillText("Enchanted Forest", 80, 120); // Label for Region 1

        // Region 2: The Dark Cave
        ctx.fillStyle = '#FF8E8E'; // Color for Cave
        ctx.fillRect(300, 50, 200, 150); // Coordinates and size
        ctx.fillText("Dark Cave", 370, 120); // Label for Region 2

        // Region 3: The Crystal Mountains
        ctx.fillStyle = '#FFD700'; // Color for Mountains
        ctx.fillRect(50, 250, 200, 150); // Coordinates and size
        ctx.fillText("Crystal Mountains", 70, 320); // Label for Region 3

        // Region 4: The Blazing Desert
        ctx.fillStyle = '#FFA500'; // Color for Desert
        ctx.fillRect(300, 250, 200, 150); // Coordinates and size
        ctx.fillText("Blazing Desert", 370, 320); // Label for Region 4

        // Region 5: The Forgotten Ruins
        ctx.fillStyle = '#B22222'; // Color for Ruins
        ctx.fillRect(550, 50, 200, 150); // Coordinates and size
        ctx.fillText("Forgotten Ruins", 580, 120); // Label for Region 5

        // Region 6: The Serpent’s Swamp
        ctx.fillStyle = '#228B22'; // Color for Swamp
        ctx.fillRect(550, 250, 200, 150); // Coordinates and size
        ctx.fillText("Serpent's Swamp", 580, 320); // Label for Region 6

        // Region 7: The Frosty Tundra
        ctx.fillStyle = '#ADD8E6'; // Color for Tundra
        ctx.fillRect(50, 450, 200, 150); // Coordinates and size
        ctx.fillText("Frosty Tundra", 80, 520); // Label for Region 7

        // Region 8: The Mystic Isles
        ctx.fillStyle = '#9370DB'; // Color for Isles
        ctx.fillRect(300, 450, 200, 150); // Coordinates and size
        ctx.fillText("Mystic Isles", 370, 520); // Label for Region 8
    }

    drawWorldMap();

    // Handle clicks on the canvas to redirect to region pages
    canvas.addEventListener('click', function(event) {
        const rect = canvas.getBoundingClientRect();
        const x = event.clientX - rect.left;
        const y = event.clientY - rect.top;

        if (x >= 50 && x <= 250 && y >= 50 && y <= 200) {
            window.location.href = 'regions/forest.php'; 
        } else if (x >= 300 && x <= 500 && y >= 50 && y <= 200) {
            window.location.href = 'regions/cave.php'; 
        } else if (x >= 50 && x <= 250 && y >= 250 && y <= 400) {
            window.location.href = 'regions/mountains.php'; 
        } else if (x >= 300 && x <= 500 && y >= 250 && y <= 400) {
            window.location.href = 'regions/desert.php'; 
        } else if (x >=550 && x <=750 && y >=50 && y <=200) {
            window.location.href = 'regions/ruins.php'; 
        } else if (x >=550 && x <=750 && y >=250 && y <=400) {
            window.location.href = 'regions/swamp.php'; 
        } else if (x >=50 && x <=250 && y >=450 && y <=600) {
            window.location.href = 'regions/tundra.php'; 
        } else if (x >=300 && x <=500 && y >=450 && y <=600) {
            window.location.href = 'regions/isles.php'; 
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>


