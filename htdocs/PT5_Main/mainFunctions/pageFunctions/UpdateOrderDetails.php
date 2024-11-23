<?php
    include '../connection.php';
    
    if ($_SERVER['REQUEST_METHOD'] === "POST"){
        $OrderID = $_POST['OrderID'];
        $OrderStatus = $_POST['OrderStatus'];

        if ($OrderStatus == null or $OrderID == null){
            echo "Error: OrderID or OrderStatus is null";
        }

        if ($OrderStatus == "Cancelled"){
            $GetOrderDetailsInfo = "SELECT * FROM order_details WHERE OrderID = '$OrderID'";
            $OD_Information = $connection -> query($GetOrderDetailsInfo);
            
            $OD_Info_Results = $OD_Information -> fetch_assoc();

            $OrderInfo_ProductID = $OD_Info_Results['productID'];

            $GetProductsInfo = "SELECT * FROM products WHERE productID ='$OrderInfo_ProductID'";
            $Products_Information = $connection -> query($GetProductsInfo);

            $Products_Info_Results = $Products_Information -> fetch_assoc();

            
            $UpdateStockQuantity = $Products_Info_Results['StockQuantity'] + $OD_Info_Results['Quant'];

            // We Update Product Quantity Since they've Cancelled (We return the quantity)
            $UpdateQuantityQuery = "UPDATE products SET StockQuantity = '$UpdateStockQuantity' WHERE productID = '$OrderInfo_ProductID'";

            $UpdateResult = $connection -> query($UpdateQuantityQuery);
        }else{
            $GetOrderDetailsInfo = "SELECT * FROM order_details WHERE OrderID = '$OrderID'";
            $OD_Information = $connection -> query($GetOrderDetailsInfo);
            
            $OD_Info_Results = $OD_Information -> fetch_assoc();

            $OrderInfo_ProductID = $OD_Info_Results['productID'];

            $GetProductsInfo = "SELECT * FROM products WHERE productID ='$OrderInfo_ProductID'";
            $Products_Information = $connection -> query($GetProductsInfo);

            $Products_Info_Results = $Products_Information -> fetch_assoc();

            if ($Products_Info_Results['StockQuantity'] >= $OD_Info_Results['Quant']){
                $UpdateStockQuantity = $Products_Info_Results['StockQuantity'] - $OD_Info_Results['Quant'];

                // We Subtract the product if it returns to Other than cancelled Order
                $UpdateQuantityQuery = "UPDATE products SET StockQuantity = '$UpdateStockQuantity' WHERE productID = '$OrderInfo_ProductID'";

                $UpdateResult = $connection -> query($UpdateQuantityQuery);
            }else{
                echo  $Products_Info_Results['Name'] . " (Quantity left: " . $Products_Info_Results['StockQuantity'] . ")";

                return;
            }
        }

        $UpdateQuery = "UPDATE orders SET OrderStatus = '$OrderStatus' WHERE OrderID = '$OrderID'";
        $UpdateResult = mysqli_query($connection, $UpdateQuery);

        if ($UpdateResult){
            
        } else {
            echo "Error: " . $UpdateQuery . "<br>" . mysqli_error($connection);
        }
    }
?>