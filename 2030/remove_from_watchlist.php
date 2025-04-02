<?php
include 'config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Please log in to manage your watchlist";
    exit;
}

// Check if stock_id is provided
if (!isset($_POST['stock_id']) || !is_numeric($_POST['stock_id'])) {
    echo "Invalid stock ID";
    exit;
}

$user_id = $_SESSION['user_id'];
$stock_id = (int)$_POST['stock_id'];

try {
    // Remove stock from watchlist
    $stmt = $conn->prepare("DELETE FROM watchlist WHERE user_id = ? AND stock_id = ?");
    $stmt->execute([$user_id, $stock_id]);
    
    if ($stmt->rowCount() > 0) {
        echo "Stock removed from your watchlist";
    } else {
        echo "Stock was not in your watchlist";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 