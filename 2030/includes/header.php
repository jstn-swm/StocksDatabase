<?php
session_start();

// Check if user is logged in
$logged_in = isset($_SESSION['user_id']);
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Stock Market</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Simple Stock Market</h1>
            <p>A beginner-friendly stock tracking application</p>
            
            <nav id="main-nav">
                <button id="menu-toggle" aria-expanded="false" aria-controls="menu-items">
                    <i class="fas fa-bars"></i> Menu
                </button>
                <ul id="menu-items">
                    <li><a href="index.php" <?php echo ($current_page == 'index.php') ? 'class="active"' : ''; ?>>Home</a></li>
                    <li><a href="sectors.php" <?php echo ($current_page == 'sectors.php') ? 'class="active"' : ''; ?>>Sectors</a></li>
                    <li><a href="detail.php" <?php echo ($current_page == 'detail.php') ? 'class="active"' : ''; ?>>Stock Details</a></li>
                    <?php if ($logged_in): ?>
                        <li><a href="watchlist.php" <?php echo ($current_page == 'watchlist.php') ? 'class="active"' : ''; ?>>My Watchlist</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php" <?php echo ($current_page == 'login.php') ? 'class="active"' : ''; ?>>Login</a></li>
                        <li><a href="register.php" <?php echo ($current_page == 'register.php') ? 'class="active"' : ''; ?>>Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </header>
        
        <div class="main-section"> 