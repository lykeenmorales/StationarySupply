<?php
    session_start();
    include '../mainFunctions/connection.php';

    if (isset($_SESSION['Login_UserID'])){
        if (isset($_SESSION['Login_UserType'])){
            if ($_SESSION['Login_UserType'] == "Admin"){
                //header("Location: ../homepage.php");
            }
        }
    }else{
        header("Location: ../LoginPage.php");
    }

    // Unset Some Variables
    unset($_SESSION['p_Description']);
    unset($_SESSION['p_Price']);
    unset($_SESSION['p_productName']);
    unset($_SESSION['p_productPackSize']);
    unset($_SESSION['p_picture_path']);
    unset($_SESSION['Current_ProductFilter']);
    unset($_SESSION['p_productStocksQuantity']);
    
    $UserID = $_SESSION['Login_UserID'];

    // We get the OVERALL total price of all the products inside cart
    $GetUser_CartItems_OVERALLTotalPrice = "SELECT
        SUM(p.Price * ci.Quantity) AS OVERALL_TOTAL_PRICE
    FROM
        cart_items ci
    JOIN products p ON ci.productID = p.productID
    WHERE ci.CustomerID = $UserID";

    $GetOverallTotalPrice = $connection -> query($GetUser_CartItems_OVERALLTotalPrice);
    $GetOverallTotalPriceResult = $GetOverallTotalPrice -> fetch_assoc();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Stationary Supplies</title>
    <!-- MDBootstrap and Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="stylesheet" href="client_css/c_cartListPage.css">
    <style>
        .modal-content-custom {
            background-color: #121212 !important; /* Pure black background */
            color: #ffffff !important; /* White text */
        }
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="clientHomePage.php">Stationary Supplies</a>
            <button class="navbar-toggler" id="navigationButton" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="clientHomePage.php"><i class="fas fa-home"></i> Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="c_ProductPage.php"><i class="fas fa-box"></i> Products</a></li>
                    <?php
                        if ($_SESSION['Login_UserID']){
                            if ($_SESSION['Login_UserType'] == "Admin"){
                                echo '<li class="nav-item"><a class="nav-link" href="../homepage.php"><i class="fas fa-user-shield"></i> Admin View</a></li>';
                            }else{
                                echo '<li class="nav-item"><a class="nav-link nav-link-FOCUS" href="c_CartListPage.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>';
                                echo '<li class="nav-item"><a class="nav-link" href="c_Account.php"><i class="fas fa-user"></i> Account</a></li>';
                            }
                        }
                    ?>
                    
                    <!-- Logout Button -->
                    <li class="nav-item">
                        <a class="nav-link" href="../mainFunctions/pageFunctions/Logout.php" style="color: white;">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Cart Header -->
    <section class="cart-header">
        <h1>Your Shopping Cart</h1>
        <p>Review your selected items before checkout.</p>
    </section>

    <!-- Cart Content -->
    <div class="container my-5">
        <div class="row">
            <div class="col-lg-7" id="ProductCartContents">
                <!-- Contents -->

            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="cart-item p-3">
                    <h4>Order Summary</h4>
                    <h5 class="cart-total">Total Cart Value:<span id="OverallTotalPrice" class="span-overall-price"> </span></h5>
                    <button class="btn btn-cta mt-3" id="CheckOutOrderBtn">Proceed to Checkout</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Notify Modal -->
    <div class="modal fade" id="NotifyModal2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-custom">
                <div class="modal-header">
                    <h5 class="modal-title" id="mainModalLabel"> </h5>
                    <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body mainmodalbody">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024 Stationary Supplies. All rights reserved.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <!-- MDBootstrap and Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const navigationBar_btn = document.getElementById('navigationButton');

        navigationBar_btn.addEventListener('click', function(event){
            if (navigationBar_btn.getAttribute('aria-expanded') == "true"){
                navigationBar_btn.focus();
            }else{
                navigationBar_btn.blur();
            }
        });

        $(document).ready(function(){
            function debounceTime(func, delay){
                let timeout;
                return function(...args){
                    if (timeout){
                        clearTimeout(timeout);
                    }
                    timeout = setTimeout(() => {
                        func.apply(this, args);
                    }, delay);
                };
            }

            function GetCartItems(ActionType, CartItemID, NewQuantity){
                var FinalizedActionType = "";
                var FinalizedCartItemID = "";
                var FinalizedNewQuantity = "";

                if (ActionType != null && ActionType != ""){
                    FinalizedActionType = ActionType;
                }
                if (CartItemID != null && CartItemID != ""){
                    FinalizedCartItemID = CartItemID;
                }   
                if (NewQuantity != null && NewQuantity != ""){
                    FinalizedNewQuantity = NewQuantity;
                }

                $.ajax({
                    url: '../mainFunctions/pageFunctions/clientFunctions/client_CartItem_Display.php',
                    method: 'Post',
                    data: {ReceivedActionType: FinalizedActionType, ReceivedCartItemID:FinalizedCartItemID, ReceivedNewQuantity:FinalizedNewQuantity},
                    success: function(response){
                        var Response_Parse = JSON.parse(response);
                        console.log(Response_Parse);

                        $('#ProductCartContents').html(Response_Parse.CartList);
                        $('#OverallTotalPrice').html(Response_Parse.OverallTotalPrice);

                        if (Response_Parse.Status != ""){
                            var NotifyModal2 = new mdb.Modal(document.getElementById('NotifyModal2'));

                            document.getElementById('mainModalLabel').innerHTML = Response_Parse.StatusLabel;
                            document.getElementsByClassName('mainmodalbody')[0].textContent = Response_Parse.StatusMessage;

                            NotifyModal2.show();

                            var Received_Cart_P_Quantity = Response_Parse.Cart_P_Quantity;

                            if (Received_Cart_P_Quantity != "" && Received_Cart_P_Quantity != 0){
                                $('#ProductQuantity_Adjust').each(function(){
                                    $(this).val($Received_Cart_P_Quantity);
                                });
                            }

                        }
                    },
                    error: function(){
                        console.log('Failed to fetch new filter list.');
                    }
                });
            }

            function CheckoutOrder(){
                // Create TimeZone Value for DateTime in sql data
                const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

                $.ajax({
                    url: '../mainFunctions/pageFunctions/clientFunctions/client_CartCheckout.php',
                    method: 'Post',
                    data: {ClientTimeZone:userTimezone},
                    success: function(response){
                        console.log(response);
                        var Response_Parse = JSON.parse(response);
                        console.log(Response_Parse);

                        if (Response_Parse.Status != ""){
                            var NotifyModal2 = new mdb.Modal(document.getElementById('NotifyModal2'));

                            document.getElementById('mainModalLabel').innerHTML = Response_Parse.StatusLabel;
                            document.getElementsByClassName('mainmodalbody')[0].textContent = Response_Parse.StatusMessage;

                            NotifyModal2.show();

                            // We Refresh
                            GetCartItems();
                        }
                    },
                    error: function(){
                        console.log('Failed to fetch new filter list.');
                    }
                });
            }

            // We First Run GetCartItems Function
            GetCartItems();

            $(document).on('keydown', '#ProductQuantity_Adjust', function(event){
                if (event.key === 'Enter' || event.keyCode === 13){
                    var ReceivedCartItemID = $(this).data('cartItemid');
                    var FinalValue = $(this).val();

                    GetCartItems('UpdateCartItem', ReceivedCartItemID, FinalValue)

                    event.preventDefault();
                }
            })

            $(document).on('blur', '#ProductQuantity_Adjust', function(event){
                var ReceivedCartItemID = $(this).data('cartItemid');
                var FinalValue = $(this).val();

                GetCartItems('UpdateCartItem', ReceivedCartItemID, FinalValue)
            })
            
            $(document).on('click', '#RemoveItemButton', function(event){
                event.preventDefault();
                var ReceivedCartItemID = $(this).data('cartItemid');
               
                GetCartItems("DeleteCartItem", ReceivedCartItemID);
            })

            $('#CheckOutOrderBtn').on('click', function(event){
                event.preventDefault();

                CheckoutOrder();
            })
        });
    </script>
</body>
</html>
