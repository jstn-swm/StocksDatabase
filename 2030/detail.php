<?php
include 'config.php';
include 'includes/header.php';

// Check if a stock symbol is provided
if (!isset($_GET['symbol'])) {
    // If no symbol provided, redirect to index
    header("Location: index.php");
    exit;
}

$symbol = $_GET['symbol'];

try {
    // Get stock details
    $stmt = $conn->prepare("SELECT * FROM stocks WHERE symbol = ?");
    $stmt->execute([$symbol]);
    
    if ($stmt->rowCount() == 0) {
        echo "<div class='error-container'><p>Stock not found</p></div>";
        include 'includes/footer.php';
        exit;
    }
    
    $stock = $stmt->fetch();
    $changeClass = $stock["change_percent"] >= 0 ? "positive" : "negative";
    $changeSymbol = $stock["change_percent"] >= 0 ? "+" : "";
?>

<div class="stock-detail">
    <div class="stock-header">
        <h2><?php echo $stock['name']; ?> (<?php echo $stock['symbol']; ?>)</h2>
        <div class="stock-price">
            <span class="current-price">$<?php echo number_format($stock['price'], 2); ?></span>
            <span class="price-change <?php echo $changeClass; ?>">
                <?php echo $changeSymbol . $stock['change_percent']; ?>%
            </span>
        </div>
        
        <?php if ($logged_in): ?>
            <button id="add-to-watchlist" class="btn" data-id="<?php echo $stock['id']; ?>">Add to Watchlist</button>
        <?php endif; ?>
    </div>
    
    <div class="stock-info">
        <div class="info-item">
            <strong>Sector:</strong> <?php echo $stock['sector'] ? $stock['sector'] : 'N/A'; ?>
        </div>
        <div class="info-item">
            <strong>Last Updated:</strong> <?php echo $stock['last_updated']; ?>
        </div>
    </div>
    
    <div class="collapsible">
        <div class="collapsible-header" aria-expanded="false">
            <h3>Company Description</h3>
            <i class="fas fa-chevron-down"></i>
        </div>
        <div class="collapsible-content">
            <p><?php echo $stock['description'] ? $stock['description'] : 'No description available.'; ?></p>
        </div>
    </div>
    
    <div class="related-stocks">
        <h3>Related Stocks</h3>
        <?php
        // Get related stocks in the same sector
        if ($stock['sector']) {
            $sector = $stock['sector'];
            
            $related_stmt = $conn->prepare("SELECT * FROM stocks WHERE sector = ? AND symbol != ? LIMIT 5");
            $related_stmt->execute([$sector, $symbol]);
            
            if ($related_stmt->rowCount() > 0) {
                echo '<div class="related-stocks-container">';
                while ($related = $related_stmt->fetch()) {
                    $relatedChangeClass = $related["change_percent"] >= 0 ? "positive" : "negative";
                    $relatedChangeSymbol = $related["change_percent"] >= 0 ? "+" : "";
                    
                    echo '<div class="related-stock-item">';
                    echo '<a href="detail.php?symbol=' . $related['symbol'] . '">';
                    echo '<div class="related-stock-symbol">' . $related['symbol'] . '</div>';
                    echo '<div class="related-stock-name">' . $related['name'] . '</div>';
                    echo '<div class="related-stock-price">$' . number_format($related['price'], 2) . '</div>';
                    echo '<div class="related-stock-change ' . $relatedChangeClass . '">' . $relatedChangeSymbol . $related['change_percent'] . '%</div>';
                    echo '</a>';
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo '<p>No related stocks found.</p>';
            }
        } else {
            echo '<p>No sector information available to find related stocks.</p>';
        }
        ?>
    </div>
</div>

<script>
$(document).ready(function() {
    // Add to watchlist functionality
    $("#add-to-watchlist").on("click", function() {
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