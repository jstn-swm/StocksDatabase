<?php
// Include database connection
include 'config.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialize errors array
    $errors = [];
    
    // Get form data
    $symbol = strtoupper(trim($_POST['symbol']));
    $name = trim($_POST['name']);
    $sector = trim($_POST['sector']);
    $price = floatval($_POST['price']);
    $change = floatval($_POST['change']);
    $description = trim($_POST['description']);
    
    // Server-side validation using regex
    // Validate symbol - Only 1-5 uppercase letters
    if (empty($symbol)) {
        $errors[] = "Stock symbol is required";
    } elseif (!preg_match('/^[A-Z]{1,5}$/', $symbol)) {
        $errors[] = "Stock symbol must be 1-5 uppercase letters";
    }
    
    // Validate name - Only letters, numbers, spaces, and common punctuation
    if (empty($name)) {
        $errors[] = "Company name is required";
    } elseif (!preg_match('/^[A-Za-z0-9\s\.,\&\(\)\-\']{2,100}$/', $name)) {
        $errors[] = "Company name contains invalid characters or is too long";
    }
    
    // Validate sector - Only letters, spaces, and ampersands
    if (!empty($sector) && !preg_match('/^[A-Za-z\s\&]{2,50}$/', $sector)) {
        $errors[] = "Sector contains invalid characters or is too long";
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
        session_start();
        $_SESSION['form_errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header("Location: index.php");
        exit;
    }
    
    try {
        // Check if stock already exists
        $check_stmt = $conn->prepare("SELECT * FROM stocks WHERE symbol = ?");
        $check_stmt->execute([$symbol]);
        
        if ($check_stmt->rowCount() > 0) {
            session_start();
            $_SESSION['form_errors'] = ["Stock with symbol $symbol already exists!"];
            $_SESSION['form_data'] = $_POST;
            header("Location: index.php");
            exit;
        }
        
        // Insert new stock using prepared statement
        $stmt = $conn->prepare("INSERT INTO stocks (symbol, name, sector, price, change_percent, description) 
                VALUES (?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([$symbol, $name, $sector, $price, $change, $description]);
        
        session_start();
        $_SESSION['success_message'] = "New stock added successfully!";
        header("Location: index.php");
        
    } catch(PDOException $e) {
        session_start();
        $_SESSION['form_errors'] = ["Database error: " . $e->getMessage()];
        $_SESSION['form_data'] = $_POST;
        header("Location: index.php");
    }
} else {
    // Redirect if not submitted via POST
    header("Location: index.php");
    exit;
}
?> 