<?php
    session_start();
    include '../connection.php';

    if ($_SERVER['REQUEST_METHOD'] === "POST"){

        $QUERY = null;

        // Check if Going to be all Visible {Includes Hidden Ones}
        if (isset($_POST['isVisible'])) {
            if ($_POST['isVisible'] == "true"){
                $QUERY = "SELECT * FROM products";
            }else{
                $QUERY = "SELECT * FROM products WHERE Display = 1";
            }
        } else {
            $QUERY = "SELECT * FROM products WHERE Display = 1";
        }

        $queryResult = mysqli_query($connection, $QUERY);

        while ($Row = mysqli_fetch_assoc($queryResult)) {
            echo "<tr>";
     
            echo "<td id='NameColumn' class='ProductNameColumn'>";

            if ($Row['Display'] <= 0){
                echo '<span style="color: yellow;">' . htmlspecialchars($Row['Name']) .' *</span>';
            }else{
                echo htmlspecialchars($Row['Name']); 
            }
    
            echo '<form action="ProductEdit.php" method="POST"> 
                    <input type="hidden" name="ProductID" value="' . $Row['productID'] . '">
                    <input type="submit" value="Update" id="EditButtonInput"> 
                  </form>';
            echo "</td>";
            
            echo "<td id='PriceColumn'>₱" . $Row['Price'] . "</td>";
            
            echo "<td id='DescriptionColumn'>" . htmlspecialchars($Row['Description']) . "</td>";
            
            echo "<td id='QuantityColumn'>";
            if ($Row['StockQuantity'] <= 0) {
                echo '<span style="color: red; font-weight:bold;">Out of Stock</span>';
            } else {
                echo $Row['StockQuantity'];
            }
            echo "</td>";

            echo "<td id='PackSizeColumn'>" . $Row['PackSize'] . "</td>";
            
            echo "<td id='PackSizeColumn'>";  
            if ($Row['Featured'] >= 1){
                echo '<span style="color: #00ff04; font-weight:bold;">Yes</span>';
            }else{
                Echo "No";
            }
            echo "</td>";

            echo "</tr>";
        }
    }
?>