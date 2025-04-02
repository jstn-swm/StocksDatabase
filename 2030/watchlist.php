<?php
include 'config.php';
include 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
?>

<div class="watchlist-container">
    <h2>My Watchlist</h2>
    
    <?php
    try {
        // Get user's watchlist
        $sql = "SELECT s.* FROM stocks s
                JOIN watchlist w ON s.id = w.stock_id
                WHERE w.user_id = :user_id
                ORDER BY s.symbol";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() == 0) {
            echo "<div class='info-message'>";
            echo "<p>Your watchlist is empty. Add stocks from the <a href='index.php'>home page</a>.</p>";
            echo "</div>";
        } else {
            // Display watchlist
            echo "<table id='watchlist-table'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Symbol</th>";
            echo "<th>Name</th>";
            echo "<th>Price ($)</th>";
            echo "<th>Change (%)</th>";
            echo "<th>Sector</th>";
            echo "<th>Added On</th>";
            echo "<th>Actions</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            
            while ($stock = $stmt->fetch()) {
                $changeClass = $stock["change_percent"] >= 0 ? "positive" : "negative";
                $changeSymbol = $stock["change_percent"] >= 0 ? "+" : "";
                
                // Get the date when the stock was added to watchlist
                $stock_id = $stock['id'];
                $added_sql = "SELECT DATE_FORMAT(added_at, '%Y-%m-%d') as added_date 
                            FROM watchlist 
                            WHERE user_id = :user_id AND stock_id = :stock_id";
                            
                $added_stmt = $conn->prepare($added_sql);
                $added_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $added_stmt->bindParam(':stock_id', $stock_id, PDO::PARAM_INT);
                $added_stmt->execute();
                $added_date = "Unknown";
                
                if ($added_stmt->rowCount() > 0) {
                    $added_row = $added_stmt->fetch();
                    $added_date = $added_row['added_date'];
                }
                
                echo "<tr>";
                echo "<td>" . $stock["symbol"] . "</td>";
                echo "<td>" . $stock["name"] . "</td>";
                echo "<td>$" . number_format($stock["price"], 2) . "</td>";
                echo "<td class='" . $changeClass . "'>" . $changeSymbol . $stock["change_percent"] . "%</td>";
                echo "<td>" . ($stock["sector"] ? $stock["sector"] : "N/A") . "</td>";
                echo "<td>" . $added_date . "</td>";
                echo "<td class='actions'>";
                echo "<a href='detail.php?symbol=" . $stock["symbol"] . "' class='btn-small'>Details</a>";
                echo "<button class='btn-small remove-from-watchlist' data-id='" . $stock["id"] . "'>Remove</button>";
                echo "</td>";
                echo "</tr>";
            }
            
            echo "</tbody>";
            echo "</table>";
        }
    } catch(PDOException $e) {
        echo "<div class='error-container'><p>Error retrieving watchlist: " . $e->getMessage() . "</p></div>";
    }
    ?>
</div>

<script>
$(document).ready(function() {
    // Remove from watchlist functionality
    $(".remove-from-watchlist").on("click", function() {
        const stockId = $(this).data("id");
        const row = $(this).closest("tr");
        
        if (confirm("Are you sure you want to remove this stock from your watchlist?")) {
            $.post("remove_from_watchlist.php", { stock_id: stockId }, function(response) {
                alert(response);
                // Remove the row from the table
                row.fadeOut(400, function() {
                    $(this).remove();
                    
                    // If no more stocks in watchlist, show empty message
                    if ($("#watchlist-table tbody tr").length === 0) {
                        $("#watchlist-table").remove();
                        $(".watchlist-container").append(
                            "<div class='info-message'>" +
                            "<p>Your watchlist is empty. Add stocks from the <a href='index.php'>home page</a>.</p>" +
                            "</div>"
                        );
                    }
                });
            }).fail(function() {
                alert("Error removing from watchlist");
            });
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?> 