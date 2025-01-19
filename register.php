<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password for security
    $email = $_POST['email'];

    include BASE_PATH . '/bootstrap.php'; // Ensures session and config are loaded

    // Check if the username already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $result = $stmt->execute();
    
    if ($result->fetchArray()) {
        // Username already exists
        echo "Username already taken. Please choose another.";
    } else {
        // Insert new user if username is available
        $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $stmt->bindValue(':password', $password, SQLITE3_TEXT);
        $stmt->execute();

        echo "Registration successful! <a href='login.php'>Login</a>";
    }
}
?>

<!-- Registration form -->
<form method="POST">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br>

    <input type="submit" value="Register">
</form>
