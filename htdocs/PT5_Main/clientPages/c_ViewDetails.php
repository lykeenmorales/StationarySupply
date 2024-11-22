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

    if ($_SERVER['REQUEST_METHOD'] == "POST"){
        if (isset($_POST['ProductID'])){
            if ($_POST['ProductID'] != ""){
                $RequestedProductID = $_POST['ProductID'];

                $GetProduct = "SELECT * FROM products WHERE productID = $RequestedProductID";

                $query = $connection -> query($GetProduct);

                $Result = $query -> fetch_assoc();

                $_SESSION['p_Description'] = $Result['Description'];
                $_SESSION['p_Price'] = $Result['Price'];
                $_SESSION['p_productName'] = $Result['Name'];
                $_SESSION['p_productPackSize'] = $Result['PackSize'];
                $_SESSION['p_productStocksQuantity'] = $Result['StockQuantity'];
                $_SESSION['p_picture_path'] = $Result['picture_path'];
            }
        }else{
            header('clientHomePage.php');
            return;
        }
    }

    $ProductPngPath = $_SESSION['p_picture_path'];
    $FinalizedPngPath = null;
    if ($ProductPngPath != null){
        $FinalizedPngPath = "../productPicUploads/" . basename(htmlspecialchars($ProductPngPath));
    }else{
        $FinalizedPngPath = "";
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details - Stationary Supplies</title>
    <!-- MDBootstrap and Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="stylesheet" href="client_css/c_viewdetails.css">
    <style>
        .modal-content-custom {
            background-color: #121212 !important; /* Pure black background */
            color: #ffffff !important; /* White text */
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

    <!-- Product Details Header -->
    <section class="product-details-header mb-5">
        <h1><?php echo htmlspecialchars($_SESSION['p_productName']); ?></h1>
    </section>

    <!-- Main Content -->
    <div class="container my-5">
        <div class="row">
            <!-- Product Image -->
            <div class="col-md-6">
                <img src="<?php echo $FinalizedPngPath; ?>" alt="Premium Notebook" class="card-img-top img-fluid rounded" alt="Book Image" style="object-fit: contain; max-height: 250px;">
            </div>
            <div class="col-md-6">
                <div class="product-info">
                    <h2 class="mb-2"><?php echo "₱" . htmlspecialchars($_SESSION['p_Price']); ?></h2>
                    <h5 class="mb-3"><?php echo "Remaining Stock: " . htmlspecialchars($_SESSION['p_productStocksQuantity']) ?></h4>
                    <p><?php echo htmlspecialchars($_SESSION['p_Description']); ?></p>
                    <hr>
                    <p style="font-weight: bold;"><?php echo "PackSize:" . " " . htmlspecialchars($_SESSION['p_productPackSize']) . "."; ?></p>
                    <div class="d-flex mt-5">
                        <?php
                            if ($_SESSION['Login_UserID']){
                                if ($_SESSION['Login_UserType'] != "Admin"){
                                    echo '<button type="submit" id="AddToCartBtn" name="AddToCartBtn" data-product-ID = "'. $_POST['ProductID'] .'" class="btn btn-cta me-3">Add to Cart</button>';
                                }
                            }
                        ?>
                        <a href="c_ProductPage.php" class="btn btn-outline-secondary">Back to Products</a>
                    </div>
                </div>
            </div>
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
