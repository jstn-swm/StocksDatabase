<?php
// Include database connection
include 'config.php';
include 'includes/header.php';
?>

<div class="stock-list">
    <h2>Current Stocks</h2>
    
    <div class="filters">
        <label for="sector-filter">Filter by Sector:</label>
        <select id="sector-filter">
            <option value="">All Sectors</option>
            <?php
            // Get all distinct sectors
            $sectors = $conn->query("SELECT DISTINCT sector FROM stocks WHERE sector IS NOT NULL ORDER BY sector");
            
            if ($sectors && $sectors->rowCount() > 0) {
                while($row = $sectors->fetch()) {
                    echo "<option value='" . $row["sector"] . "'>" . $row["sector"] . "</option>";
                }
            }
            ?>
        </select>
        
        <label for="sort-by">Sort by:</label>
        <select id="sort-by">
            <option value="symbol">Symbol</option>
            <option value="name">Company Name</option>
            <option value="price_asc">Price (Low to High)</option>
            <option value="price_desc">Price (High to Low)</option>
            <option value="change_asc">Change % (Low to High)</option>
            <option value="change_desc">Change % (High to Low)</option>
        </select>
    </div>
    
    <table id="stocks-table">
        <thead>
            <tr>
                <th>Symbol</th>
                <th>Name</th>
                <th>Price ($)</th>
                <th>Change (%)</th>
                <th>Sector</th>
                <th>Last Updated</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch all stocks from database
            $stmt = $conn->query("SELECT * FROM stocks ORDER BY symbol");
            
            if ($stmt && $stmt->rowCount() > 0) {
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
                echo "<tr><td colspan='7'>No stocks found. Run <a href='db_setup.php'>setup</a> first.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<div class="forms-section">
    <div class="add-stock-form">
        <h2>Add New Stock</h2>
        
        <?php
        // Display form errors if any
        if (isset($_SESSION['form_errors'])) {
            echo '<div class="error-container">';
            foreach ($_SESSION['form_errors'] as $error) {
                echo '<p class="error">' . $error . '</p>';
            }
            echo '</div>';
            unset($_SESSION['form_errors']);
        }
        
        // Display success message if any
        if (isset($_SESSION['success_message'])) {
            echo '<div class="success-message">';
            echo '<p>' . $_SESSION['success_message'] . '</p>';
            echo '</div>';
            unset($_SESSION['success_message']);
        }
        
        // Pre-fill form with previous data if available
        $symbol = isset($_SESSION['form_data']['symbol']) ? htmlspecialchars($_SESSION['form_data']['symbol']) : '';
        $name = isset($_SESSION['form_data']['name']) ? htmlspecialchars($_SESSION['form_data']['name']) : '';
        $sector = isset($_SESSION['form_data']['sector']) ? htmlspecialchars($_SESSION['form_data']['sector']) : '';
        $price = isset($_SESSION['form_data']['price']) ? htmlspecialchars($_SESSION['form_data']['price']) : '';
        $change = isset($_SESSION['form_data']['change']) ? htmlspecialchars($_SESSION['form_data']['change']) : '';
        $description = isset($_SESSION['form_data']['description']) ? htmlspecialchars($_SESSION['form_data']['description']) : '';
        
        // Unset form data
        if (isset($_SESSION['form_data'])) {
            unset($_SESSION['form_data']);
        }
        ?>
        
        <form id="add-stock-form" action="add_stock.php" method="post">
            <div class="form-group">
                <label for="symbol">Stock Symbol:</label>
                <input type="text" id="symbol" name="symbol" required maxlength="10" value="<?php echo $symbol; ?>">
                <span class="error" id="symbol-error"></span>
            </div>
            
            <div class="form-group">
                <label for="name">Company Name:</label>
                <input type="text" id="name" name="name" required maxlength="100" value="<?php echo $name; ?>">
                <span class="error" id="name-error"></span>
            </div>
            
            <div class="form-group">
                <label for="sector">Sector:</label>
                <input type="text" id="sector" name="sector" maxlength="50" value="<?php echo $sector; ?>">
            </div>
            
            <div class="form-group">
                <label for="price">Current Price ($):</label>
                <input type="number" id="price" name="price" step="0.01" min="0" required value="<?php echo $price; ?>">
                <span class="error" id="price-error"></span>
            </div>
            
            <div class="form-group">
                <label for="change">Change (%):</label>
                <input type="number" id="change" name="change" step="0.01" required value="<?php echo $change; ?>">
                <span class="error" id="change-error"></span>
            </div>
            
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4"><?php echo $description; ?></textarea>
            </div>
            
            <button type="submit">Add Stock</button>
        </form>
    </div>

    <div class="update-stock-form">
        <h2>Update Stock Price</h2>
        
        <?php
        // Display update form errors if any
        if (isset($_SESSION['update_errors'])) {
            echo '<div class="error-container">';
            foreach ($_SESSION['update_errors'] as $error) {
                echo '<p class="error">' . $error . '</p>';
            }
            echo '</div>';
            unset($_SESSION['update_errors']);
        }
        
        // Display update success message if any
        if (isset($_SESSION['update_success'])) {
            echo '<div class="success-message">';
            echo '<p>' . $_SESSION['update_success'] . '</p>';
            echo '</div>';
            unset($_SESSION['update_success']);
        }
        
        // Pre-fill update form with previous data if available
        $update_symbol = isset($_SESSION['update_data']['symbol']) ? htmlspecialchars($_SESSION['update_data']['symbol']) : '';
        $update_price = isset($_SESSION['update_data']['price']) ? htmlspecialchars($_SESSION['update_data']['price']) : '';
        $update_change = isset($_SESSION['update_data']['change']) ? htmlspecialchars($_SESSION['update_data']['change']) : '';
        
        // Unset update form data
        if (isset($_SESSION['update_data'])) {
            unset($_SESSION['update_data']);
        }
        ?>
        
        <form id="update-stock-form" action="update_stock.php" method="post">
            <div class="form-group">
                <label for="update-symbol">Stock Symbol:</label>
                <select id="update-symbol" name="symbol" required>
                    <option value="">Select a stock</option>
                    <?php
                    // Reset the result pointer if result exists
                    if (isset($stmt) && $stmt) {
                        // Use a new query to get fresh data
                        $update_options = $conn->query("SELECT symbol, name FROM stocks ORDER BY symbol");
                        
                        // Generate dropdown options
                        if ($update_options->rowCount() > 0) {
                            while($row = $update_options->fetch()) {
                                $selected = ($update_symbol == $row["symbol"]) ? " selected" : "";
                                echo "<option value='" . $row["symbol"] . "'" . $selected . ">" . $row["symbol"] . " - " . $row["name"] . "</option>";
                            }
                        }
                    }
                    ?>
                </select>
                <span class="error" id="update-symbol-error"></span>
            </div>
            
            <div class="form-group">
                <label for="update-price">New Price ($):</label>
                <input type="number" id="update-price" name="price" step="0.01" min="0" required value="<?php echo $update_price; ?>">
                <span class="error" id="update-price-error"></span>
            </div>
            
            <div class="form-group">
                <label for="update-change">New Change (%):</label>
                <input type="number" id="update-change" name="change" step="0.01" required value="<?php echo $update_change; ?>">
                <span class="error" id="update-change-error"></span>
            </div>
            
            <button type="submit">Update Stock</button>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Client-side validation for adding stocks
        $("#add-stock-form").on("submit", function(e) {
            let isValid = true;
            
            // Validate stock symbol (1-5 uppercase letters)
            const symbol = $("#symbol").val().toUpperCase();
            $("#symbol").val(symbol);
            
            if (!/^[A-Z]{1,5}$/.test(symbol)) {
                $("#symbol-error").text("Symbol must be 1-5 uppercase letters");
                isValid = false;
            } else {
                $("#symbol-error").text("");
            }
            
            // Validate name
            if ($("#name").val().trim() === "") {
                $("#name-error").text("Company name is required");
                isValid = false;
            } else {
                $("#name-error").text("");
            }
            
            // Validate price
            if ($("#price").val() <= 0) {
                $("#price-error").text("Price must be greater than 0");
                isValid = false;
            } else {
                $("#price-error").text("");
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
        
        // Client-side validation for updating stocks
        $("#update-stock-form").on("submit", function(e) {
            let isValid = true;
            
            // Validate symbol selection
            if ($("#update-symbol").val() === "") {
                $("#update-symbol-error").text("Please select a stock");
                isValid = false;
            } else {
                $("#update-symbol-error").text("");
            }
            
            // Validate price
            if ($("#update-price").val() <= 0) {
                $("#update-price-error").text("Price must be greater than 0");
                isValid = false;
            } else {
                $("#update-price-error").text("");
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
        
        // Filtering by sector
        $("#sector-filter").on("change", function() {
            const sector = $(this).val();
            
            if (sector === "") {
                // Show all rows if no sector is selected
                $("#stocks-table tbody tr").show();
            } else {
                // Hide all rows first
                $("#stocks-table tbody tr").hide();
                
                // Show only rows matching the selected sector
                $("#stocks-table tbody tr[data-sector='" + sector + "']").show();
            }
        });
        
        // Sorting
        $("#sort-by").on("change", function() {
            const sortBy = $(this).val();
            const rows = $("#stocks-table tbody tr").get();
            
            rows.sort(function(a, b) {
                let aValue, bValue;
                
                switch(sortBy) {
                    case "symbol":
                        aValue = $(a).find("td:eq(0)").text();
                        bValue = $(b).find("td:eq(0)").text();
                        return aValue.localeCompare(bValue);
                    
                    case "name":
                        aValue = $(a).find("td:eq(1)").text();
                        bValue = $(b).find("td:eq(1)").text();
                        return aValue.localeCompare(bValue);
                    
                    case "price_asc":
                        aValue = parseFloat($(a).find("td:eq(2)").text().replace('$', '').replace(',', ''));
                        bValue = parseFloat($(b).find("td:eq(2)").text().replace('$', '').replace(',', ''));
                        return aValue - bValue;
                    
                    case "price_desc":
                        aValue = parseFloat($(a).find("td:eq(2)").text().replace('$', '').replace(',', ''));
                        bValue = parseFloat($(b).find("td:eq(2)").text().replace('$', '').replace(',', ''));
                        return bValue - aValue;
                    
                    case "change_asc":
                        aValue = parseFloat($(a).find("td:eq(3)").text().replace('%', '').replace('+', ''));
                        bValue = parseFloat($(b).find("td:eq(3)").text().replace('%', '').replace('+', ''));
                        return aValue - bValue;
                    
                    case "change_desc":
                        aValue = parseFloat($(a).find("td:eq(3)").text().replace('%', '').replace('+', ''));
                        bValue = parseFloat($(b).find("td:eq(3)").text().replace('%', '').replace('+', ''));
                        return bValue - aValue;
                }
            });
            
            $.each(rows, function(index, row) {
                $("#stocks-table tbody").append(row);
            });
        });
        
        // Add to watchlist functionality
        $(".add-to-watchlist").on("click", function() {
            const stockId = $(this).data("id");
            
            $.post("add_to_watchlist.php", { stock_id: stockId }, function(response) {
                alert(response);
            }).fail(function() {
                alert("Error adding to watchlist");
            });
        });
        
        // Refresh stock data every 60 seconds
        function refreshStockData() {
            $("#stocks-table tbody").load("refresh_stocks.php", function(response, status, xhr) {
                if (status == "error") {
                    console.log("Error loading data: " + xhr.status + " " + xhr.statusText);
                }
            });
        }
        
        // Refresh every 60 seconds
        setInterval(refreshStockData, 60000);
    });
</script>

<?php include 'includes/footer.php'; ?> 