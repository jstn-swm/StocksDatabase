<?php
include 'config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Please log in to add stocks to your watchlist";
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
    // Verify stock exists
    $check_stock = $conn->prepare("SELECT id FROM stocks WHERE id = ?");
    $check_stock->execute([$stock_id]);
    
    if ($check_stock->rowCount() == 0) {
        echo "Stock not found";
        exit;
    }
    
    // Check if stock already in watchlist
    $check_watchlist = $conn->prepare("SELECT id FROM watchlist WHERE user_id = ? AND stock_id = ?");
    $check_watchlist->execute([$user_id, $stock_id]);
    
    if ($check_watchlist->rowCount() > 0) {
        echo "This stock is already in your watchlist";
        exit;
    }
    
    // Add stock to watchlist
    $stmt = $conn->prepare("INSERT INTO watchlist (user_id, stock_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $stock_id]);
    
    echo "Stock added to your watchlist successfully";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 