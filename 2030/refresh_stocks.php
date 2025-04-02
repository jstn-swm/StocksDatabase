<?php
// Include database connection
include 'config.php';
session_start();
$logged_in = isset($_SESSION['user_id']);

// Error handling
try {
    // Fetch all stocks from database
    $stmt = $conn->query("SELECT * FROM stocks ORDER BY symbol");
    
    if ($stmt->rowCount() > 0) {
        while($row = $stmt->fetch()) {
            $changeClass = $row["change_percent"] >= 0 ? "positive" : "negative";
            $changeSymbol = $row["change_percent"] >= 0 ? "+" : "";
            
            echo "<tr data-sector='" . $row["sector"] . "'>";
            echo "<td>" . $row["symbol"] . "</td>";
            echo "<td>" . $row["name"] . "</td>";
            echo "<td>$" . number_format($row["price"], 2) . "</td>";
            echo "<td class='" . $changeClass . "'>" . $changeSymbol . $row["change_percent"] . "%</td>";
            echo "<td>" . $row["sector"] . "</td>";
            echo "<td>" . $row["last_updated"] . "</td>";
            echo "<td class='actions'>";
            echo "<a href='detail.php?symbol=" . $row["symbol"] . "' class='btn-small'>Details</a>";
            
            if ($logged_in) {
                echo "<button class='btn-small add-to-watchlist' data-id='" . $row["id"] . "'>Add to Watchlist</button>";
            }
            
            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No stocks found</td></tr>";
    }
} catch(PDOException $e) {
    // Return error message as a single table row
    echo "<tr><td colspan='7'>Error: " . $e->getMessage() . "</td></tr>";
}
?> 