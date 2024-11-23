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

    $GetAllFeaturedProducts = "SELECT * FROM products WHERE Featured >= 1 AND StockQuantity > 0";

    $Results = $connection -> query($GetAllFeaturedProducts);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Stationary Supplies</title>
    <!-- MDBootstrap and Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="client_css/c_HomePage.css">
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
                        <li class="nav-item"><a class="nav-link nav-link-FOCUS" href="#"><i class="fas fa-home"></i> Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="c_ProductPage.php"><i class="fas fa-box"></i> Products</a></li>
                        <?php
                            if ($_SESSION['Login_UserID']){
                                if ($_SESSION['Login_UserType'] == "Admin"){
                                    echo '<li class="nav-item"><a class="nav-link" href="../homepage.php"><i class="fas fa-user-shield"></i> Admin View</a></li>';
                                }else{
                                    echo '<li class="nav-item"><a class="nav-link" href="c_CartListPage.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>';
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

        <!-- Page Header -->
        <section class="page-header d-flex flex-column justify-content-center align-items-center">
            <h1>Welcome to Stationary Supplies</h1>
            <p>Your one-stop shop for all your stationary needs.</p>
        </section>

        <!-- Main Content -->
        <div class="container my-5">
            <h2 class="text-center mb-5">Featured Products</h2>

            <div class="row">
                <!-- Product Card Example -->
                <?php
                    while ($row = $Results -> fetch_assoc()){
                        $ProductPngPath = $row['picture_path'];
                        $FinalizedPngPath = null;
                        if ($ProductPngPath != null){
                            $FinalizedPngPath = "../productPicUploads/" . basename(htmlspecialchars($ProductPngPath));
                        }else{
                            $FinalizedPngPath = "";
                        }

                        echo '<div class="col-md-3 mb-5">
                                <div class="card product-card">
                                    <img src="'. $FinalizedPngPath .'" class="card-img-top img-fluid" alt="Book Image" style="object-fit: contain; max-height: 250px;" alt="Error: No Image">
                                        <div class="card-body">
                                            <form action="c_ViewDetails.php" method="post" id="MainForm">
                                                <h5 class="card-title">'. $row['Name'] .'</h5>
                                                <p class="card-text">₱'. $row['Price'] .'</p>
                                                <div class="d-flex justify-content-between">
                                                <input type="hidden" name="ProductID" value="' . $row['productID'] . '">
                                                <button class="btn btn-cta me-3">View Details</button>
                                            </form>';
                                        if ($_SESSION['Login_UserType'] != "Admin"){ 
                                            echo '<button type="submit" id="AddToCartBtn" name="AddToCartBtn" data-product-ID = "'. $row['productID'] .'" class="btn btn-cta me-3">Add to Cart</button>';
                                        }   

                                echo '  </div>
                                    </div>
                                </div>
                            </div>';
                    }
                ?>

            </div>
        </div>

        <!-- Add To Cart Notify Modal -->
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
                        <button type="button" class="btn btn-secondary" id="CheckCartButton">Check Cart</button>
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

            // This Handles The Add To Cart Button  
            $(document).ready(function(){
                $(document).on('click', '#AddToCartBtn', function(event){
                    event.preventDefault();
                    var ProductID = $(this).data('productId');

                    // Create TimeZone Value for DateTime in sql data
                    const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
                    
                    $.ajax({
                        url: '../mainFunctions/pageFunctions/clientFunctions/client_AddToCart.php',
                        method: 'Post',
                        data: {OrderedProduct: ProductID, ClientTimeZone:userTimezone},
                        success: function(response){
                            if (response){
                                console.log(response);
                                var response_parse = JSON.parse(response);

                                if (response_parse.Status != ""){
                                    var NotifyModal2 = new mdb.Modal(document.getElementById('NotifyModal2'));

                                    document.getElementById('mainModalLabel').innerHTML = response_parse.StatusLabel;
                                    document.getElementsByClassName('mainmodalbody')[0].textContent = response_parse.StatusMessage;
                                    document.getElementById('CheckCartButton').addEventListener('click', function(event){
                                        window.location.href = './c_CartListPage.php';
                                    })

                                    NotifyModal2.show();
                                }
                            }
                        },
                        error: function(){
                            console.log('Failed to fetch new filter list.');
                        }
                    })
                });
            });
        </script>
    </body>
</html>
