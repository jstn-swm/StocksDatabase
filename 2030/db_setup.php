<?php
// Database configuration
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = "1234"; // Default XAMPP password - typically empty for XAMPP

try {
    // Create PDO connection
    $conn = new PDO("mysql:host=$servername", $username, $password);
    
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS stock_market";
    $conn->exec($sql);
    echo "Database created successfully<br>";
    
    // Connect to the specific database
    $conn = new PDO("mysql:host=$servername;dbname=stock_market", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create stocks table
    $sql = "CREATE TABLE IF NOT EXISTS stocks (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        symbol VARCHAR(10) NOT NULL,
        name VARCHAR(100) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        change_percent DECIMAL(5,2) NOT NULL,
        sector VARCHAR(50) DEFAULT NULL,
        description TEXT DEFAULT NULL,
        last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $conn->exec($sql);
    echo "Table 'stocks' created successfully<br>";
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $conn->exec($sql);
    echo "Table 'users' created successfully<br>";
    
    // Create watchlist table
    $sql = "CREATE TABLE IF NOT EXISTS watchlist (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT(6) UNSIGNED NOT NULL,
        stock_id INT(6) UNSIGNED NOT NULL,
        added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (stock_id) REFERENCES stocks(id) ON DELETE CASCADE,
        UNIQUE(user_id, stock_id)
    )";
    
    $conn->exec($sql);
    echo "Table 'watchlist' created successfully<br>";
    
    // Check if sample data already exists
    $check = $conn->query("SELECT * FROM stocks LIMIT 1");
    
    if ($check->rowCount() == 0) {
        // Insert sample data
        $sql = "INSERT INTO stocks (symbol, name, price, change_percent, sector, description)
        VALUES 
            ('AAPL', 'Apple Inc.', 150.25, 1.25, 'Technology', 'Apple Inc. designs, manufactures, and markets smartphones, personal computers, tablets, wearables, and accessories worldwide.'),
            ('MSFT', 'Microsoft Corporation', 280.50, 0.75, 'Technology', 'Microsoft Corporation develops, licenses, and supports software, services, devices, and solutions worldwide.'),
            ('AMZN', 'Amazon.com Inc.', 3200.10, -0.50, 'Consumer Cyclical', 'Amazon.com, Inc. engages in the retail sale of consumer products and subscriptions in North America and internationally.'),
            ('GOOGL', 'Alphabet Inc.', 2750.80, 1.10, 'Communication Services', 'Alphabet Inc. provides various products and platforms in the United States, Europe, the Middle East, Africa, the Asia-Pacific, Canada, and Latin America.'),
            ('META', 'Meta Platforms Inc.', 325.45, -0.80, 'Communication Services', 'Meta Platforms, Inc. develops products that enable people to connect and share with friends and family through mobile devices, personal computers, virtual reality headsets, and wearables worldwide.'),
            ('TSLA', 'Tesla, Inc.', 850.75, 2.15, 'Automotive', 'Tesla, Inc. designs, develops, manufactures, leases, and sells electric vehicles, and energy generation and storage systems in the United States, China, and internationally.'),
            ('NVDA', 'NVIDIA Corporation', 742.35, 3.20, 'Technology', 'NVIDIA Corporation designs, develops, and markets graphics processing units and related software. The company offers products for gaming, professional visualization, data center, and automotive markets.'),
            ('JPM', 'JPMorgan Chase & Co.', 185.67, 0.45, 'Financial Services', 'JPMorgan Chase & Co. is a global financial services firm that provides investment banking, financial services for consumers and small businesses, commercial banking, financial transaction processing, and asset management.'),
            ('DIS', 'The Walt Disney Company', 105.22, -1.35, 'Communication Services', 'The Walt Disney Company is a global entertainment company that operates theme parks, resorts, a cruise line, television networks, streaming services, and film production studios.'),
            ('PFE', 'Pfizer Inc.', 28.15, 0.75, 'Healthcare', 'Pfizer Inc. discovers, develops, manufactures, and sells healthcare products worldwide. It offers medicines and vaccines in various therapeutic areas.'),
            ('KO', 'The Coca-Cola Company', 63.48, 1.10, 'Consumer Defensive', 'The Coca-Cola Company is a beverage company that manufactures, markets, and sells various nonalcoholic beverages worldwide. The company provides beverages through bottling and distribution operations.')";
            
        $conn->exec($sql);
        echo "Sample stock data added successfully<br>";
    } else {
        echo "Sample data already exists<br>";
    }
    
    // Insert demo user with hashed password (password: demo123)
    $demo_user_check = $conn->query("SELECT * FROM users WHERE username = 'demo'");
    
    if ($demo_user_check->rowCount() == 0) {
        $hashed_password = password_hash('demo123', PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
        $stmt->execute(['demo', $hashed_password, 'demo@example.com']);
        
        echo "Demo user created successfully (Username: demo, Password: demo123)<br>";
    } else {
        echo "Demo user already exists<br>";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

// Close connection
$conn = null;
echo "Database setup complete. <a href='index.php'>Go to homepage</a>";
?> 