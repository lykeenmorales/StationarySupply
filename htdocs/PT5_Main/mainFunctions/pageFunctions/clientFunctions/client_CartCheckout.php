<?php
    session_start();
    include '../../connection.php';

    $UserID = $_SESSION['Login_UserID'];

    $connection -> begin_transaction();

    try{
        if ($_SERVER['REQUEST_METHOD'] === "POST"){
            $DefaultOrderStatus = 'Processing';
    
            // Response Data Table
            $ResponseData = [
                'Status' => '',
                'StatusLabel' => '',
                'StatusMessage' => '',
                'test' => '',
            ];
    
            $UserTimezone = $_POST['ClientTimeZone'];
    
            date_default_timezone_set($UserTimezone);
    
            $currentDateTime = new DateTime();
            $formattedDateTime = $currentDateTime->format('Y-m-d H:i:s');
    
            // We Sanity Check first if a user has a item on cart to avoid checking out falsely
            $GetCartInformation = "SELECT * FROM cart_items WHERE CustomerID = '$UserID'";
            $GetCartInformationQuery = $connection -> query($GetCartInformation);
            $CartInformationResult = $GetCartInformationQuery -> fetch_assoc();
    
            // We get the OVERALL total price of all the products inside cart
            $GetUser_CartItems_OVERALLTotalPrice = "SELECT
                SUM(p.Price * ci.Quantity) AS OVERALL_TOTAL_PRICE
            FROM
                cart_items ci
            JOIN products p ON ci.productID = p.productID
            WHERE ci.CustomerID = $UserID";
    
            $GetOverallTotalPrice = $connection -> query($GetUser_CartItems_OVERALLTotalPrice);
            $GetOverallTotalPriceResult = $GetOverallTotalPrice -> fetch_assoc();
            
            $FinalizedOverallPrice = 0;
            if ($GetOverallTotalPriceResult['OVERALL_TOTAL_PRICE'] == null || $GetOverallTotalPriceResult['OVERALL_TOTAL_PRICE'] <= 0 || $GetOverallTotalPriceResult['OVERALL_TOTAL_PRICE'] == ""){
                $FinalizedOverallPrice = 0;
            }else{
                $FinalizedOverallPrice =  $GetOverallTotalPriceResult['OVERALL_TOTAL_PRICE'];
            }
    
            $TotalRows = $GetCartInformationQuery -> num_rows;

            if ($TotalRows > 0){
                if ($CartInformationResult['CustomerID'] != "" && $CartInformationResult['CustomerID'] != 0 && $CartInformationResult['productID'] != "" && $CartInformationResult['productID'] != 0){
                   if ($FinalizedOverallPrice != 0){
                    // Check Products Data
                    
                    $SetOrdersQuery = "INSERT INTO orders (CustomerID, OrderDate, TotalPrice, OrderStatus) VALUES (?,?,?,?)";
                    $SetOrders = $connection -> prepare($SetOrdersQuery);
                    $SetOrders -> bind_param("isis", $UserID, $formattedDateTime, $FinalizedOverallPrice, $DefaultOrderStatus);
                    $SetOrders -> execute();
                    
                    $LastInsert_OrderID = $connection -> insert_id;

                    $SetOrders -> close();
    
                    $Counts = 0;

                    foreach ($GetCartInformationQuery as $Items){
                        $GetProductVersion = "SELECT Name, productID, version, StockQuantity FROM products WHERE productID = ?";
                        $GetProductVersion_Query = $connection -> prepare($GetProductVersion);
                        $GetProductVersion_Query -> bind_param('i', $Items['productID']);
                        $GetProductVersion_Query -> execute();
                        $GetProductVersion_Query -> bind_result($MainproductName, $MainproductID, $productCurrentVersion, $ProductCurrentStock);
                        $GetProductVersion_Query -> fetch();
                        $GetProductVersion_Query -> close();

                        if ($Items['version'] !== $productCurrentVersion){
                            $DeleteCartItem = "DELETE FROM cart_items WHERE productID = ? AND CustomerID = ?";
                            $DeleteCartItem_Query = $connection -> prepare($DeleteCartItem);
                            $DeleteCartItem_Query -> bind_param('ii', $Items['productID'], $UserID);
                            $DeleteCartItem_Query -> execute();
                          
                            if ($DeleteCartItem_Query -> affected_rows > 0) {
                                // Working
                            } else {
                                throw new Exception("Failed to remove product from cart due to version mismatch.");
                            }
                        }

                        if ($ProductCurrentStock < $Items['Quantity']){
                            $DeleteCartItem = "DELETE FROM cart_items WHERE productID = ? AND CustomerID = ?";
                            $DeleteCartItem_Query = $connection -> prepare($DeleteCartItem);
                            $DeleteCartItem_Query -> bind_param('ii', $Items['productID'], $UserID);
                            $DeleteCartItem_Query -> execute();

                            if ($UpdateProductStocks_Query -> affected_rows === 0){
                               // Working
                            }else{
                                throw new Exception("Failed to remove product from cart due to insufficient stock.");
                            }
                        }

                        // Update Product Quantity
                        $UpdateProductStocks = "UPDATE products SET StockQuantity = StockQuantity - ?, version = version + 1 WHERE productID = ?";
                        $UpdateProductStocks_Query = $connection -> prepare($UpdateProductStocks);
                        $UpdateProductStocks_Query -> bind_param('ii', $Items['Quantity'], $Items['productID']);
                        $UpdateProductStocks_Query -> execute();

                        if ($UpdateProductStocks_Query -> affected_rows === 0){
                            throw new Exception("The product's stock has changed since you last viewed it. Please try again.");
                        }


                        $SetOrderDetails_Query = "INSERT INTO order_details (OrderID, productID, Quant) VALUES (?, ?, ?)";
                        $SetOrderDetails = $connection -> prepare($SetOrderDetails_Query);
                        $SetOrderDetails -> bind_param('iii', $LastInsert_OrderID , $Items['productID'], $Items['Quantity']);
                        $SetOrderDetails -> execute();
                        $SetOrderDetails -> close();
                        
                        $Counts += 1;
                    }

                    if ($Counts >= $TotalRows){
                        // Delete the User Cart Items 
                        $ClearCart_Query = "DELETE FROM cart_items WHERE CustomerID = ?";
                        $ClearCart = $connection -> prepare($ClearCart_Query);
                        $ClearCart -> bind_param('i', $UserID);
                        $ClearCart -> execute();
        
                        $DisplayFormattedTime = date("Y-m-d h:i A", strtotime($formattedDateTime));

                        $ResponseData['Status'] = 'True';
                        $ResponseData['StatusLabel'] = 'Thank you! Your order has been placed successfully.';
                        $ResponseData['StatusMessage'] = 'OrderId: ' . htmlspecialchars($LastInsert_OrderID) . ' Date: ' . htmlspecialchars($DisplayFormattedTime);

                        $connection -> commit();
                    }else{
                        throw new Exception("Error occurred while Checking Out, Please Try Again Later!");
                    }

                   }else{
                    throw new Exception("There are no items in your cart to check out. Please add products and try again.");
                   }
                }else{
                    throw new Exception("There are no items in your cart to check out. Please add products and try again.");
                }
            }else{
                throw new Exception("There are no items in your cart to check out. Please add products and try again.");
            }
        }
    }catch(Exception $error){
        $ResponseData['Status'] = 'False';
        $ResponseData['StatusLabel'] = 'Something went wrong when checking out!';
        $ResponseData['StatusMessage'] = $error->getMessage();

        $connection -> rollback();
    }

    echo json_encode($ResponseData);
?>