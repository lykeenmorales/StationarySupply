<?php
    session_start();

    // Check any Bypasses or Unauthorized Access
    if (isset($_SESSION['Login_UserID'])){
        if (isset($_SESSION['Login_UserType'])){
            if ($_SESSION['Login_UserType'] != "Admin"){
                header("Location: ../clientPages/clientHomePage.php");
            }
        }
    }else{
        header("Location: ../LoginPage.php");
    }

    $IsSideNavOpen = null;
    $VisibleAllProducts = null;
    if (isset($_SESSION['IsSideMenuOpen'])){
        $IsSideNavOpen = $_SESSION['IsSideMenuOpen'];
    }
    if (isset($_SESSION['VisibleAllProducts'])){
        $VisibleAllProducts = $_SESSION['VisibleAllProducts'];
    }else{
        $VisibleAllProducts = "false";
    }
    
    $SessionKeysToUnset = [
        'CustomerID', 
        'FirstName', 
        'LastName', 
        'PhoneNumber', 
        'Address', 
        'ProductPrice', 
        'ProductName', 
        'ProductDescription', 
        'StockQuantity', 
        'Display', 
        'ProductStockQuantity'
    ];
    foreach($SessionKeysToUnset as $keys){
        if (isset($_SESSION[$keys])){
            unset($_SESSION[$keys]);
        }
    }
    
    include '../mainFunctions/connection.php';

    $QUERY = "SELECT * From products";
    $QUERYRESULT = mysqli_query($connection, $QUERY);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="../Css/MainDesign.css">

    <style>
        .custom-search-bar{
            position: absolute;
            top: 215px;
            margin-left: 15px;
            width: 25%;
            max-width: 20%;
            min-width: 20%;
            height: 25px;
        }
        .custom-search-size{
            height: 25px;
        }
        .ScrollingTable-height{
            height: 633px;
        }
        .custom-search-bar .form-check-input {
            margin: 0; 
        }

        .custom-search-bar .form-check-label {
            white-space: nowrap; 
            margin-bottom: 0; 
        }
    </style>
</head>
<body>       
    <!-- Sidebar Navigation -->
    <div class="sidebar" id="sidebar">
        <div class="SideBarObjects">
            <ul class="list-unstyled p-3">
                <li class = "NavigationLinks"> Account: <div class="userCustomNameTEXT"><?php echo $_SESSION['Login_UserName']; ?></div> </li>

                <hr>

                <li class = "NavigationLinks"> <i class="bi bi-window"></i>  <a href="../homepage.php" class="text-decoration-none">Dashboard</a></li>
                <li class = "NavigationLinks"> <i class="bi bi-people"></i>  <a href="CustomerPage.php" class="text-decoration-none">Client Accounts</a></li>
                <li class = "NavigationLinks"> <i class="bi bi-box"></i>  <a href="#" class="text-decoration-none custom-glow-Current-Page">Products</a></li>
                <li class = "NavigationLinks"> <i class="bi bi-clipboard"></i>  <a href="Order_DetailsPage.php" class="text-decoration-none">Order Details</a></li>

                <hr>

                <li class = "NavigationLinks"> <i class="fas fa-eye"></i>  <a href="../clientPages/clientHomePage.php" class="text-decoration-none">View Client Page</a></li>

                <hr>

                <li class = "NavigationLinks"> <i class="bi-box-arrow-right"></i>  <a href="../mainFunctions/pageFunctions/Logout.php" class="text-decoration-none">Logout</a></li>
            </ul>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg Topbar-custom fixed-top">
        <div class="container-fluid">
            <button class="btn btn-primary customButtonPos" id="menuToggle">
                <i class="bi bi-list"></i>
            </button>    
            <h4 class="text-center fs-4 custom-title-text">Stationary Supplies</h4>
            <a class="text-center custom-Add-link" href="ProductAdd.php"> Add Product </a>
        </div>
    </nav>

    <!-- Main Content Area -->
    <div class="main-content" id="mainContent">
        <h5 class="Content_title">Products Information</h5>

        <div class="container">
            <!-- Search Bar -->
            <div class="input-group mb-3 custom-search-bar">
                <span class="input-group-text custom-search-size"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control custom-search-size" id="myInput" type="text" placeholder="Search.." aria-label="Search">

                <div class="row align-items-center">
                    <div class="col-md-4 d-flex align-items-center"> 
                        <input data-bs-toggle="tooltip" data-bs-title="If checked will display all Hidden Products." class="form-check-input me-2" type="checkbox" id="ProductsVisibility" name="DisplayProduct">
                        <label class="form-check-label" for="check2">Display All</label>
                    </div>
                </div>
            </div>

            <div class="table-responsive-xxl overflow-auto ScrollingTable ScrollingTable-height">
                <table class="table caption-top align-middle table-hover table-dark table-bordered table-sm">
                    <thead>
                        <tr>
                            <th class="col-2">Product Name</th>
                            <th class="col-1">Price</th>
                            <th class="col-3">Description</th>
                            <th class="col-1">Stocks</th>
                            <th class="col-1">Pack Size</th>
                            <th class="col-1">Featured</th>
                        </tr>
                    </thead>

                    <tbody id="displayTable" class="table-group-divider">

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Success Notify Modal -->
    <div class="modal fade" id="SuccessfullModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" class="AnotherAdd" id="AnotherAdd">Add Another</button>
                    <button type="button" class="btn btn-secondary" id="Confirm">Continue</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

    <!-- Navigation Link Jscript -->
    <script type="module">        
        // Import some functions
        import * as jsFunctions from '../mainFunctions/Functions.js';

        var IsOpen = "<?php echo $IsSideNavOpen; ?>";
        var IsVisibleAllProducts = "<?php echo $VisibleAllProducts; ?>";
    
        //console.log(IsVisibleAllProducts);

        $(document).ready(function() {
            if (IsOpen == "true"){
                $('#sidebar').toggleClass('show');
                $('#mainContent').toggleClass('shift');
            }
            if (IsVisibleAllProducts == "true"){
                $('#ProductsVisibility').prop('checked', true);
            }else{
                $('#ProductsVisibility').prop('checked', false);
            }

            function fetchNewUpdatedProducts(IsVisible){
                $.ajax({
                    url: '../mainFunctions/pageFunctions/DisplayProduct.php',
                    method: 'Post',
                    data: {isVisible: IsVisible},
                    success: function(response){
                        $('#displayTable').html(response);
                    },
                    error: function(){
                        console.log('Failed to fetch new product list.');
                    }
                })
            }

            $('#menuToggle').click(function() {
                $('#sidebar').toggleClass('show');
                $('#mainContent').toggleClass('shift');

                if (IsOpen == "true" || IsOpen == true ){
                    jsFunctions.SendAJXCallback({CallBack:'SideNavBarOpen', Data:{IsSideNavOpenValue:"false"}})
                    IsOpen = "false";
                }else{
                    jsFunctions.SendAJXCallback({CallBack:'SideNavBarOpen', Data:{IsSideNavOpenValue:"true"}})
                    IsOpen = "true";
                }
            })

            $('#ProductsVisibility').click(function() {
                if ($('#ProductsVisibility').is(':checked')){
                    jsFunctions.SendAJXCallback({CallBack:'VisibleAllHideProducts', Data:{VisibleAllProductsValue:"true"}})
                    IsVisibleAllProducts = "true";
                }else{
                    jsFunctions.SendAJXCallback({CallBack:'VisibleAllHideProducts', Data:{VisibleAllProductsValue:""}})
                    IsVisibleAllProducts = "false"
                }
                // UnFocus after Clicking and Refresh
                $('#ProductsVisibility').blur();

                fetchNewUpdatedProducts(IsVisibleAllProducts);
            })

            fetchNewUpdatedProducts(IsVisibleAllProducts);
        });
    </script>

    <script>
         // This will activate the tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

        // Check if returned by errored
        var ErrorSession = '<?php 
            if(isset($_SESSION['ErrorAdd'])){
                echo $_SESSION['ErrorAdd'];
                unset($_SESSION['ErrorAdd']);
            }
        ?>'

        var SuccessSession = '<?php
            if (isset($_SESSION['SuccessAdd'])){
                echo $_SESSION['SuccessAdd'];
                unset($_SESSION['SuccessAdd']);
            }
        ?>'

        var ErrorAddAgain = '<?php
            if (isset($_SESSION['ErrorAddAgain'])){
                echo $_SESSION['ErrorAddAgain'];
                unset($_SESSION['ErrorAddAgain']);
            }
        ?>'

        var RemoveAddAnother = '<?php
            if (isset($_SESSION['RemoveAddAnother'])){
                echo $_SESSION['RemoveAddAnother'];
                unset($_SESSION['RemoveAddAnother']);
            }
        ?>'

        const NotifyModal = new bootstrap.Modal(document.getElementById('SuccessfullModal'));

        if (SuccessSession != ""){
            document.getElementsByClassName('modal-body')[0].textContent = SuccessSession;
            NotifyModal.show();
        }

        if (ErrorSession != ""){
            document.getElementsByClassName('modal-body')[0].textContent = ErrorSession;
            NotifyModal.show();
        }

        document.getElementById('Confirm').addEventListener('click', function(){
            NotifyModal.hide();
        });

        if (ErrorAddAgain !== ""){
            if (ErrorAddAgain == "ErroredWhileAdding"){
                document.getElementsByClassName('modal-body')[0].textContent = ErrorAddAgain;
                document.getElementsByClassName('AnotherAdd')[0].textContent = ""
                document.getElementById('AnotherAdd').remove();

            }else{
                if (RemoveAddAnother !== "true"){
                    document.getElementById('AnotherAdd').addEventListener('click', function(){
                        window.location.href = 'ProductAdd.php';
                        NotifyModal.hide();
                    })
                }else{
                    document.getElementById('AnotherAdd').remove();
                }
            }
        }

        document.getElementById('myInput').addEventListener('keyup', function() {
            var input = document.getElementById('myInput');
            var filter = input.value.toLowerCase();
            var table = document.querySelector('table');
            var tr = table.getElementsByTagName('tr');

            for (var i = 1; i < tr.length; i++) {
                var td = tr[i].getElementsByTagName('td');
                var found = false;
                for (var j = 0; j < td.length; j++) {
                    if (td[j]) {
                        if (td[j].textContent.toLowerCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                if (found) {
                    tr[i].style.display = '';
                } else {
                    tr[i].style.display = 'none';
                }
            }
        });
    </script>
</body>
</html>