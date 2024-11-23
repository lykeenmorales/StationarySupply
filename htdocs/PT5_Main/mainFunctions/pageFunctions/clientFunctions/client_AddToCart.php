<?php
    session_start();
    include '../../connection.php';

    $UserID = $_SESSION['Login_UserID'];

    if ($_SERVER['REQUEST_METHOD'] === "POST"){
        $OrderedQuant = 1; // Default Since Adding to Cart is a Single Item 
        $RequestedProductID = $_POST['OrderedProduct'];
        // Response Data Table
        $ResponseData = [
            'Status' => 'True',
            'StatusLabel' => 'An Item was Added Successfully',
            'StatusMessage' => '',
        ];

        $UserTimezone = $_POST['ClientTimeZone'];

        date_default_timezone_set($UserTimezone);

        $currentDateTime = new DateTime();
        $formattedDateTime = $currentDateTime->format('Y-m-d H:i:s');

        // Before we Insert we Check if already exist we just update the quantity
        $GetCartInformation = "SELECT * FROM cart_items WHERE CustomerID = '$UserID' AND productID = '$RequestedProductID'";
        $GetCartInformationQuery = $connection -> query($GetCartInformation);
        $CartInformationQuery = $GetCartInformationQuery -> fetch_assoc();
        if ($GetCartInformationQuery -> num_rows > 0){
            if ($CartInformationQuery['CartItemID'] != 0 || $CartInformationQuery['CustomerID'] != 0 || $CartInformationQuery['productID'] != 0){
                $NewQuantity = $CartInformationQuery['Quantity'] + 1;

                // We Get Product Info
                $GetProductInfo = "SELECT StockQuantity FROM products WHERE productID = '$RequestedProductID'";
                $GetProductInfo_Query = $connection -> query($GetProductInfo);
                $GetProductInfo_Result = $GetProductInfo_Query -> fetch_assoc();

                if ($GetProductInfo_Result['StockQuantity'] > 0){
                    // Get Product Information
                    $GetProductInfo = "SELECT StockQuantity FROM products WHERE productID = ?";
                    $GetProductInfo_Query = $connection -> prepare($GetProductInfo);
                    $GetProductInfo_Query -> bind_param('i', $RequestedProductID);
                    $GetProductInfo_Query -> execute();
                    $GetProductInfo_Query -> bind_result($Product_StockQuantity);
                    $GetProductInfo_Query -> fetch();
                    $GetProductInfo_Query -> close();

                    if ($Product_StockQuantity < $NewQuantity){
                        $ResponseData['Status'] = 'False';
                        $ResponseData['StatusLabel'] = 'Error Occurred While Updating';
                        $ResponseData['StatusMessage'] = 'Product doesnt have enough stocks!';
                    }else{
                        $UpdateProductItem = "UPDATE cart_items SET Quantity = '$NewQuantity' WHERE productID = '$RequestedProductID' AND CustomerID = '$UserID'";
                        $UpdateProductQuery = $connection -> query($UpdateProductItem);
                        
                        if ($UpdateProductQuery){
                            $ResponseData['Status'] = 'True';
                            $ResponseData['StatusLabel'] = 'Item was successfully Updated';
                            $ResponseData['StatusMessage'] = 'You have added one more quantity to same product! Check Your Cart';
                        }else{
                            $ResponseData['Status'] = 'False';
                            $ResponseData['StatusLabel'] = 'Error Occurred While Updating';
                            $ResponseData['StatusMessage'] = 'Something went wrong trying to Update! Please Try Again';
                        }
                    }
                }else{
                    $ResponseData['Status'] = 'False';
                    $ResponseData['StatusLabel'] = 'Out of Stock!';
                    $ResponseData['StatusMessage'] = 'Sorry this item is Out of Stocks';
                }

                echo json_encode($ResponseData);
                return;
            }
        }

        // We Get Product Info
        $GetProductInfo = "SELECT * FROM products WHERE productID = '$RequestedProductID'";
        $GetProductInfo_Query = $connection -> query($GetProductInfo);
        $GetProductInfo_Result = $GetProductInfo_Query -> fetch_assoc();
        $ProductVersion = $GetProductInfo_Result['version'];

        if ($GetProductInfo_Result['StockQuantity'] > 0){ 
            $AddToCartQuery = "INSERT INTO cart_items (CustomerID, productID, Quantity, DateAdded, version) VALUES (?,?,?,?,?)";
            $stmt = $connection -> prepare($AddToCartQuery);
            $stmt -> bind_param("iiisi", $UserID, $RequestedProductID, $OrderedQuant, $formattedDateTime, $ProductVersion);

            $GetProductInformation = "SELECT * FROM products WHERE productID ='$RequestedProductID'";
            $ProductInformationQuery = $connection -> query($GetProductInformation);
            $ProductInformationResult = $ProductInformationQuery -> fetch_assoc();
        
            if ($stmt -> execute()){
                $ResponseData['StatusMessage'] = 'A ' . $ProductInformationResult['Name'] . ' was successfully added to your cart!';
            }else{
                $ResponseData['Status'] = 'False';
                $ResponseData['StatusLabel'] = 'Error Occurred While Adding';
                $ResponseData['StatusMessage'] = 'Something went wrong trying to Add! Please Try Again';
            }
        }else{
            $ResponseData['Status'] = 'False';
            $ResponseData['StatusLabel'] = 'Out of Stock!';
            $ResponseData['StatusMessage'] = 'Sorry this item is Out of Stocks';
        }

        echo json_encode($ResponseData);
    }
?>