<?php 
    session_start();
    include '../../connection.php';

    $UserID = $_SESSION['Login_UserID'];

    // Response Data Table
    $ResponseData = [
        'Status' => '',
        'StatusLabel' => '',
        'StatusMessage' => '',
        'CartList' => '',
        'OverallTotalPrice' => '',
    ];

    if ($_SERVER['REQUEST_METHOD'] === "POST"){
        // We Check if theres any Update
        if (isset($_POST['ReceivedActionType'])){
            if ($_POST['ReceivedActionType'] != null && $_POST['ReceivedActionType'] == "UpdateCartItem"){
                if (isset($_POST['ReceivedCartItemID'])){
                    if ($_POST['ReceivedCartItemID'] != null && $_POST['ReceivedCartItemID'] != ""){
                        if (isset($_POST['ReceivedNewQuantity']) && $_POST['ReceivedNewQuantity'] != "" && $_POST['ReceivedNewQuantity'] != null){
                            $Received_Cart_ItemID = $_POST['ReceivedCartItemID'];
                            $Received_NewQuantity = $_POST['ReceivedNewQuantity'];

                            // Get Cart Product ID
                            $GetCartItemProductID = "SELECT productID, Quantity FROM cart_items WHERE CustomerID = ? AND CartItemID = ?";
                            $GetCartItemProductID_Query = $connection -> prepare($GetCartItemProductID);
                            $GetCartItemProductID_Query -> bind_param('ii', $UserID, $Received_Cart_ItemID);
                            $GetCartItemProductID_Query -> execute();
                            $GetCartItemProductID_Query -> bind_result($Cart_ProductID, $Cart_ProductQuantity);
                            $GetCartItemProductID_Query -> fetch();
                            $GetCartItemProductID_Query -> close();

                            // Get Product Information
                            $GetProductInfo = "SELECT StockQuantity FROM products WHERE productID = ?";
                            $GetProductInfo_Query = $connection -> prepare($GetProductInfo);
                            $GetProductInfo_Query -> bind_param('i', $Cart_ProductID);
                            $GetProductInfo_Query -> execute();
                            $GetProductInfo_Query -> bind_result($Product_StockQuantity);
                            $GetProductInfo_Query -> fetch();
                            $GetProductInfo_Query -> close();

                            if ($Product_StockQuantity < $Received_NewQuantity){
                                // Not Enough Of Stock
                                // We set to the Maximum
                                $UpdateCartItem = "UPDATE cart_items SET Quantity = '$Product_StockQuantity' WHERE CartItemID ='$Received_Cart_ItemID' AND CustomerID ='$UserID'";
                                $UpdateCartItemQuery = $connection -> query($UpdateCartItem);
                            }else{
                                $FinalizedNewQuant = 0;
                                if ($Received_NewQuantity <= 0){
                                    $FinalizedNewQuant = 1;
                                }else{
                                    $FinalizedNewQuant = $Received_NewQuantity;
                                }
                                $UpdateCartItem = "UPDATE cart_items SET Quantity = '$FinalizedNewQuant' WHERE CartItemID ='$Received_Cart_ItemID' AND CustomerID ='$UserID'";
                                $UpdateCartItemQuery = $connection -> query($UpdateCartItem);
                            }
                        }
                    }
                }
            }
            if ($_POST['ReceivedActionType'] != null && $_POST['ReceivedActionType'] == "DeleteCartItem"){
                if (isset($_POST['ReceivedCartItemID'])){
                    if ($_POST['ReceivedCartItemID'] != null && $_POST['ReceivedCartItemID'] != ""){
                        $Received_Cart_ItemID = $_POST['ReceivedCartItemID'];
                        $DeleteCartItem = "DELETE FROM cart_items WHERE CartItemID ='$Received_Cart_ItemID' AND CustomerID = '$UserID'";
            
                        $DeleteCartItemQuery = $connection -> query($DeleteCartItem);
                        
                        if ($DeleteCartItemQuery){
                            $ResponseData['Status'] = 'True';
                            $ResponseData['StatusLabel'] = 'Successfully Removed!';
                            $ResponseData['StatusMessage'] = 'Item was successfully removed from your cart!';
                        }else{
                            $ResponseData['Status'] = 'False';
                            $ResponseData['StatusLabel'] = 'Error: Delete Failed';
                            $ResponseData['StatusMessage'] = 'Error Occurred while removing your item! Please Try Again.';
                        }
                    }
                }
            }
        }

        // GET the user cart items
        $GetUser_CartItems = "SELECT 
            ci.productID,
            ci.CustomerID, 
            ci.Quantity, 
            p.Name, 
            p.Price,
            p.picture_path,
            p.StockQuantity,
            ci.CartItemID,
            SUM(p.Price * ci.Quantity) AS TotalPrice
        FROM
            cart_items ci
        JOIN products p ON ci.productID = p.productID
        WHERE ci.CustomerID = ?
        GROUP BY
            ci.productID,
            ci.CustomerID, 
            ci.Quantity, 
            p.Name, 
            p.Price,
            p.picture_path,
            ci.CartItemID
        ";

        $GetCartItems = $connection -> prepare($GetUser_CartItems);
        $GetCartItems -> bind_param("i", $UserID);
        $GetCartItems -> execute();

        $User_CartItemsResult = $GetCartItems -> get_result();

        while ($Row = $User_CartItemsResult -> fetch_assoc()){
            if ($User_CartItemsResult -> num_rows > 0){
                if ($Row['Name'] != "" || $Row['productID'] != ""){
                    // Get Product Information
                    $GetProductInfo = "SELECT StockQuantity FROM products WHERE productID = ?";
                    $GetProductInfo_Query = $connection -> prepare($GetProductInfo);
                    $GetProductInfo_Query -> bind_param('i', $Row['productID']);
                    $GetProductInfo_Query -> execute();
                    $GetProductInfo_Query -> bind_result($Product_StockQuantity);
                    $GetProductInfo_Query -> fetch();
                    $GetProductInfo_Query -> close();

                    $ProductPngPath = $Row['picture_path'];
                    $FinalizedPngPath = null;
                    if ($ProductPngPath != null){
                        $FinalizedPngPath = "../productPicUploads/" . basename(htmlspecialchars($ProductPngPath));
                    }else{
                        $FinalizedPngPath = "";
                    }

                    // If Reached Passed by the Stocks we set the number in MaxQuantity
                    $FinalizedQuantity = "";
                    $Received_NewQuantity = $_POST['ReceivedNewQuantity'];
                    if ($Received_NewQuantity != ""){
                        if ($Product_StockQuantity <= $Received_NewQuantity){
                            $FinalizedQuantity = intval($Product_StockQuantity);
                        }else{
                            $FinalizedQuantity = intval($Row['Quantity']);
                        }
                    }else{
                        if ($Product_StockQuantity <= $Row['Quantity']){
                            $FinalizedQuantity = intval($Product_StockQuantity);
                        }else{
                            $FinalizedQuantity = intval($Row['Quantity']);
                        }
                    }

                    $Content = '<div class="cart-item d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <img src="'. htmlspecialchars($FinalizedPngPath) .'" alt="no img" class="img-fluid rounded" style="width: 80px; height: 80px; margin-right: 15px;">
                                                        <div>
                                                            <h5 class="mb-1">'. htmlspecialchars($Row['Name']) .'</h5>
                                                            <h6 class="mb-3"> Remaining Stock: '. htmlspecialchars($Row['StockQuantity']) .'</h5>
                                                            <p class="mb-0"> Unit Price: ₱'. htmlspecialchars($Row['Price']) .'</p>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <input type="number" id="ProductQuantity_Adjust" class="form-control" data-Cart-ItemID="'. intval($Row['CartItemID']) .'" value="'. $FinalizedQuantity .'" style="width: 80px;" min="1" step="1" max="999">
                                                    </div>
                                                    <p class="mb-0">Total: ₱'. htmlspecialchars($Row['TotalPrice']) .'</p>
                                                    <button type="submit" id="RemoveItemButton" class="btn btn-danger btn-sm ms-2" data-Cart-ItemID="'. intval($Row['CartItemID']) .'" >Remove</button>
                                                </div>';
                    $ResponseData['CartList'] .= $Content;
                }
            }
        }

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
            $FinalizedOverallPrice =  "₱" . htmlspecialchars($GetOverallTotalPriceResult['OVERALL_TOTAL_PRICE']);
        }

        $ResponseData['OverallTotalPrice'] = $FinalizedOverallPrice;

        echo json_encode($ResponseData);
    }
?>