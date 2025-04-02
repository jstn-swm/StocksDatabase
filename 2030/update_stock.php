<?php
// Include database connection
include 'config.php';
session_start();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialize errors array
    $errors = [];
    
    // Get form data
    $symbol = trim($_POST['symbol']);
    $price = floatval($_POST['price']);
    $change = floatval($_POST['change']);
    
    // Server-side validation using regex
    // Validate symbol - Should be a valid stock symbol
    if (empty($symbol)) {
        $errors[] = "Please select a stock";
    } elseif (!preg_match('/^[A-Z]{1,5}$/', $symbol)) {
        $errors[] = "Invalid stock symbol format";
    }
    
    // Validate price - Positive number with up to 2 decimal places
    if (empty($price) || $price <= 0) {
        $errors[] = "Price must be greater than 0";
    } elseif (!preg_match('/^\d+(\.\d{1,2})?$/', (string)$price)) {
        $errors[] = "Price must be a valid number with up to 2 decimal places";
    }
    
    // Validate change - Number with up to 2 decimal places, can be negative
    if (!preg_match('/^-?\d+(\.\d{1,2})?$/', (string)$change)) {
        $errors[] = "Change percentage must be a valid number with up to 2 decimal places";
    }
    
    // Check for validation errors
    if (!empty($errors)) {
        $_SESSION['update_errors'] = $errors;
        $_SESSION['update_data'] = $_POST;
        header("Location: index.php");
        exit;
    }
    
    try {
        // Check if the stock exists
        $check_stmt = $conn->prepare("SELECT * FROM stocks WHERE symbol = ?");
        $check_stmt->execute([$symbol]);
        
        if ($check_stmt->rowCount() == 0) {
            $_SESSION['update_errors'] = ["Stock with symbol $symbol does not exist!"];
            header("Location: index.php");
            exit;
        }
        
        // Update stock using prepared statement
        $stmt = $conn->prepare("UPDATE stocks SET price = ?, change_percent = ?, 
                last_updated = CURRENT_TIMESTAMP WHERE symbol = ?");
        
        $stmt->execute([$price, $change, $symbol]);
        
        $_SESSION['update_success'] = "Stock updated successfully!";
        header("Location: index.php");
        
    } catch(PDOException $e) {
        $_SESSION['update_errors'] = ["Database error: " . $e->getMessage()];
        header("Location: index.php");
    }
} else {
    // Redirect if not submitted via POST
    header("Location: index.php");
    exit;
}
?> 