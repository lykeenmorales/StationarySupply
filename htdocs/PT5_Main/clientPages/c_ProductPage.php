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
    unset($_SESSION['p_productStocksQuantity']);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Stationary Supplies</title>
    <!-- MDBootstrap and Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="client_css/c_productPage.css">
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
                    <li class="nav-item"><a class="nav-link nav-link-FOCUS" href="#"><i class="fas fa-box"></i> Products</a></li>
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
        <h1>Our Products</h1>
        <p>Explore our wide range of high-quality stationary supplies.</p>
    </section>

    <!-- Main Content -->
    <div class="container my-5">
        <div class="row">
            <!-- Filter Section -->
            <aside class="col-lg-3 mb-4">
                <div class="filter-section">
                    <h5>Filter by Category</h5>
                    <button class="btn filter-btn" id="filterbtns" name="AllProducts" value="0">All Products</button>
                    <button class="btn filter-btn" id="filterbtns" name="WritingInstruments" value="1">Writing Instruments</button>
                    <button class="btn filter-btn" id="filterbtns" name="WritingTools" value="2">Writing Tools</button>
                    <button class="btn filter-btn" id="filterbtns" name="Organizers" value="3">Organizers</button>
                    <button class="btn filter-btn" id="filterbtns" name="Miscellaneous" value="4">Miscellaneous</button>
                </div>
            </aside>


            <!-- Product Grid -->
            <div class="col-lg-9">
                <div class="mb-4">
                    <div class="input-group">
                        <input type="text" id="searchBar" class="form-control" placeholder="Search for products..." aria-label="Search for products">
                        <button class="btn btn-outline-secondary" type="button" id="searchButton">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="row" id="displayFilterContent">
                   
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024 Stationary Supplies. All rights reserved.</p>
    </footer>

    <!-- Notify Modal -->
    <div class="modal fade" id="NotifyModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-custom">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Confirm Changes</h5>
                    <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Warning: Saving these changes will update your profile information. Proceed with <span style="color:red">caution</span>.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="ConfirmationModal">Confirm</button>
                    <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
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

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <!-- MDBootstrap and Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const navigationBar_btn = document.getElementById('navigationButton');

        $(document).ready(function(){
            // Filter/Categorized Functionalities
            function GetFilters(filterID, NewPage, TypeChanged){
                // We Get Search Input to make it dynamically search if theres
                var SearchFilter = $('#searchBar').val().toLowerCase();

                var FinalizedFilter = "";
                var FinalizedPage = NewPage;
                if (filterID == "0"){
                    FinalizedFilter = "all";
                }else if (filterID == "1"){
                    FinalizedFilter = "1";
                }else if (filterID == "2"){
                    FinalizedFilter = "2";
                }else if (filterID == "3"){
                    FinalizedFilter = "5";
                }else if (filterID == "4"){
                    FinalizedFilter = "misc";
                }

                if (FinalizedPage == "" || FinalizedPage == null){
                    FinalizedPage = 1
                }
                
                $.ajax({
                    url: '../mainFunctions/pageFunctions/clientFunctions/client_productPage.php',
                    method: 'Post',
                    data: {
                        FilterID: FinalizedFilter, 
                        SearchInputs: SearchFilter,
                        page:FinalizedPage, 
                        ChangedType:TypeChanged
                    },
                    success: function(response){
                        $('#displayFilterContent').html(response);
                    },
                    error: function(){
                        console.log('Failed to fetch new filter list.');
                    }
                })
            }

            document.querySelectorAll('#filterbtns').forEach(FilterButtons => {
                FilterButtons.addEventListener('click', () => {
                    if (FilterButtons.getAttribute('name') == "AllProducts"){
                        GetFilters("0", null, "Filter");
                    }else if (FilterButtons.getAttribute('name') == "WritingInstruments"){
                        GetFilters("1", null, "Filter");
                    }else if (FilterButtons.getAttribute('name') == "WritingTools"){
                        GetFilters("2", null, "Filter");
                    }else if (FilterButtons.getAttribute('name') == "Organizers"){
                        GetFilters("3", null, "Filter");
                    }else if (FilterButtons.getAttribute('name') == "Miscellaneous"){
                        GetFilters("4", null, "Filter");
                    }
                });
            });

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

            // Load all products on page load
            GetFilters("0", 1, "AllType");

            // Event handler for pagination links
            $(document).on('click', '.page-link', function(event) {
                event.preventDefault();
                var page = $(this).data('page'); // Get the page number from data attribute
                const activeFilter = $('#filterbtns.active').val() || "0";
                GetFilters("0", page, "Page"); // Call GetFilters with selected filter ID and page number
            });

            // This Handles The Add To Cart Button  
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
            
            $('#searchBar').on('keyup', debounceTime(function(){
                var Filter = $(this).val().toLowerCase();
                var Page = 1;

                $.ajax({
                    url: '../mainFunctions/pageFunctions/clientFunctions/client_productPage.php',
                    method: 'Post',
                    data: {SearchInputs: Filter, page:Page},
                    success: function(response){
                        $('#displayFilterContent').html(response);
                    },
                    error: function(){
                        console.log('Failed to fetch new filter list.');
                    }
                })
            }, 25));
        });

        navigationBar_btn.addEventListener('click', function(event){
            if (navigationBar_btn.getAttribute('aria-expanded') == "true"){
                navigationBar_btn.focus();
            }else{
                navigationBar_btn.blur();
            }
        });
    
    </script>
</body>
</html>
