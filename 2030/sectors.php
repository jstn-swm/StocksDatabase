<?php
include 'config.php';
include 'includes/header.php';

try {
    // Get all sectors with stock counts
    $sql = "SELECT sector, COUNT(*) as stock_count 
            FROM stocks 
            WHERE sector IS NOT NULL AND sector != '' 
            GROUP BY sector 
            ORDER BY sector";
    $sectors_result = $conn->query($sql);
    
    // Check if a specific sector is selected
    $selected_sector = isset($_GET['sector']) ? trim($_GET['sector']) : null;
?>

<div class="sectors-container">
    <h2><?php echo $selected_sector ? "Stocks in " . htmlspecialchars($selected_sector) . " Sector" : "Browse by Sector"; ?></h2>
    
    <?php if (!$selected_sector): ?>
        <div class="sectors-grid">
            <?php
            if (!$sectors_result) {
                echo "<div class='error-container'><p>Error retrieving sectors</p></div>";
            } else if ($sectors_result->rowCount() == 0) {
                echo "<div class='info-message'><p>No sectors found in the database.</p></div>";
            } else {
                while ($sector = $sectors_result->fetch()) {
                    echo "<div class='sector-card'>";
                    echo "<a href='sectors.php?sector=" . urlencode($sector['sector']) . "'>";
                    echo "<h3>" . htmlspecialchars($sector['sector']) . "</h3>";
                    echo "<p>" . $sector['stock_count'] . " stocks</p>";
                    echo "</a>";
                    echo "</div>";
                }
            }
            ?>
        </div>
    <?php else: ?>
        <?php
        // Get stocks in selected sector
        $stocks_stmt = $conn->prepare("SELECT * FROM stocks WHERE sector = ? ORDER BY symbol");
        $stocks_stmt->execute([$selected_sector]);
        
        if ($stocks_stmt->rowCount() == 0) {
            echo "<div class='info-message'><p>No stocks found in the " . htmlspecialchars($selected_sector) . " sector.</p></div>";
        } else {
            // Display sector performance summary
            $avg_stmt = $conn->prepare("SELECT AVG(price) as avg_price, AVG(change_percent) as avg_change FROM stocks WHERE sector = ?");
            $avg_stmt->execute([$selected_sector]);
            $avg_data = $avg_stmt->fetch();
            
            $avg_change_class = $avg_data['avg_change'] >= 0 ? "positive" : "negative";
            $avg_change_symbol = $avg_data['avg_change'] >= 0 ? "+" : "";
            
            echo "<div class='sector-summary'>";
            echo "<div class='summary-item'>";
            echo "<span class='summary-label'>Average Price:</span>";
            echo "<span class='summary-value'>$" . number_format($avg_data['avg_price'], 2) . "</span>";
            echo "</div>";
            echo "<div class='summary-item'>";
            echo "<span class='summary-label'>Average Change:</span>";
            echo "<span class='summary-value " . $avg_change_class . "'>" . $avg_change_symbol . number_format($avg_data['avg_change'], 2) . "%</span>";
            echo "</div>";
            echo "</div>";
            
            // Display stocks in this sector
            echo "<table id='sector-stocks-table'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Symbol</th>";
            echo "<th>Name</th>";
            echo "<th>Price ($)</th>";
            echo "<th>Change (%)</th>";
            echo "<th>Actions</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            
            while ($stock = $stocks_stmt->fetch()) {
                $changeClass = $stock["change_percent"] >= 0 ? "positive" : "negative";
                $changeSymbol = $stock["change_percent"] >= 0 ? "+" : "";
                
                echo "<tr>";
                echo "<td>" . htmlspecialchars($stock["symbol"]) . "</td>";
                echo "<td>" . htmlspecialchars($stock["name"]) . "</td>";
                echo "<td>$" . number_format($stock["price"], 2) . "</td>";
                echo "<td class='" . $changeClass . "'>" . $changeSymbol . $stock["change_percent"] . "%</td>";
                echo "<td class='actions'>";
                echo "<a href='detail.php?symbol=" . urlencode($stock["symbol"]) . "' class='btn-small'>Details</a>";
                
                if ($logged_in) {
                    echo "<button class='btn-small add-to-watchlist' data-id='" . $stock["id"] . "'>Add to Watchlist</button>";
                }
                
                echo "</td>";
                echo "</tr>";
            }
            
            echo "</tbody>";
            echo "</table>";
        }
        ?>
        
        <div class="back-link">
            <a href="sectors.php">← Back to all sectors</a>
        </div>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    // Add to watchlist functionality
    $(".add-to-watchlist").on("click", function() {
        const stockId = $(this).data("id");
        
        $.post("add_to_watchlist.php", { stock_id: stockId }, function(response) {
            alert(response);
        }).fail(function() {
            alert("Error adding to watchlist");
        });
    });
});
</script>

<?php
} catch(PDOException $e) {
    echo "<div class='error-container'><p>Database error: " . $e->getMessage() . "</p></div>";
}

include 'includes/footer.php';
?> 