<?php
// Database configuration
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = "1234"; // Default XAMPP password - typically empty for XAMPP
$dbname = "stock_market";

try {
    // Create PDO connection
    $conn = new PDO("mysql:host=$servername", $username, $password);
    
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if database exists
    $stmt = $conn->query("SHOW DATABASES LIKE 'stock_market'");
    
    if ($stmt->rowCount() == 0) {
        echo "Database does not exist. <a href='db_setup.php'>Run setup first</a>";
        exit;
    }
    
    // Connect to the specific database
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set default fetch mode to associative array
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?> 