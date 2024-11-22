<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");

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
    if (isset($_SESSION['IsSideMenuOpen'])){
        $IsSideNavOpen = $_SESSION['IsSideMenuOpen'];
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

    $GetOrderHistoryQuery = " SELECT 
        o.OrderID, 
        ca.first_name, 
        ca.last_name,
        ca.Address, 
        ca.CustomerID,
        p.Name, 
        p.Price,
        o.TotalPrice,
        o.OrderStatus, 
        od.Quant, 
        o.OrderDate,

        GROUP_CONCAT(p.Name SEPARATOR ', ') AS Products,
        GROUP_CONCAT(od.Quant SEPARATOR ', ') AS Quantities,
        GROUP_CONCAT(p.Price SEPARATOR ', ') AS Prices,
        SUM(od.Quant) AS TotalQuantity,
        SUM(p.Price * od.Quant) AS TOTAL_PRICE,
        o.TotalPrice
    FROM
        customeraccount ca
    JOIN 
        orders o ON o.CustomerID = ca.CustomerID
    JOIN
        order_details od ON o.OrderID = od.OrderID
    JOIN
        products p ON od.productID = p.productID
    GROUP BY
        o.OrderID, o.OrderDate
    ORDER BY
        o.OrderDate Desc
    ";

    $Details = mysqli_query($connection, $GetOrderHistoryQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="../Css/MainDesign.css">

    <style>
        .modal-content-custom {
            background-color: #121212 !important; /* Pure black background */
            color: #ffffff !important; /* White text */
        }
        
        .custom-select-sizeform{
            width: 122px;
            height: 34px;

            font-size: 13px;
            font-weight: bold;
            text-align: center;
            display: flex;
            justify-content: center; 
            align-items: center;
        }

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
            height: 643px;
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
                <li class = "NavigationLinks"> <i class="bi bi-box"></i>  <a href="ProductInfoPage.php" class="text-decoration-none">Products</a></li>
                <li class = "NavigationLinks"> <i class="bi bi-clipboard"></i>  <a href="#" class="text-decoration-none custom-glow-Current-Page">Order Details</a></li>

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
        </div>
    </nav>

    <!-- Main Content Area -->
    <div class="main-content" id="mainContent">
        <h5 class="Content_title">Customer Order Details</h5>

        <div class="container container-sm container-md container-lg">
            <!-- Search Bar -->
            <div class="input-group mb-3 custom-search-bar">
                <span class="input-group-text custom-search-size"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control custom-search-size" id="myInput" type="text" placeholder="Search.." aria-label="Search">
            </div>

            <div class="table-responsive overflow-auto ScrollingTable ScrollingTable-height">
                <table class="table caption-top align-middle table-hover table-dark table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th class="col-md-1">Customer Name</th>
                            <th class="col-md-4">Product</th>
                            <th class="col-md-3">Unit Price</th>
                            <th >Quantity</th>
                            <th class="col-md-1">Order Date</th>
                            <th class="col-md-1">Total Price</th>
                            <th >Status</th>
                            <th class="col-md-3">Address</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        <?php
                            while ($Row = mysqli_fetch_assoc($Details)){
                            $FormattedTime = date("Y-m-d h:i A", strtotime($Row['OrderDate']));
                            // Arrange Products
                            $product = htmlspecialchars($Row['Products']);
                            $quantity = htmlspecialchars($Row['Quantities']);
                            $price = htmlspecialchars($Row['Prices']);
                            $Products = explode(", ", $product);
                            $Quantities = explode(", ", $quantity);
                            $Prices = explode(", ", $price);

                            $ProductDetails = [];
                            $ProductQuantities = [];
                            $ProductPrices = [];

                            for ($i = 0; $i < count($Products); $i++){
                                $ProductDetails[] = $Products[$i];
                                $ProductQuantities[] = $Quantities[$i];
                                $ProductPrices[] = $Prices[$i];
                            }

                            $ProductDetailsString = implode(", ", $ProductDetails);
                            $QuantitiesDetailsString = implode(", ", $ProductQuantities);
                            $PricesString = implode(", ₱", $ProductPrices)
                        ?>

                        <tr>
                        <input type="hidden" name="OrderID" value='<?php echo $Row['OrderID']; ?>'>
                            <td id="OrderIdColumn"> <?php echo htmlspecialchars($Row['OrderID']);?></td>
                            <td id="CustomerNameColumn"> <?php echo htmlspecialchars($Row['last_name']) . ", " . htmlspecialchars($Row['first_name']);?></td>
                            <td id="ProductNameColumn"> <?php echo $ProductDetailsString;?></td>
                            <td id="ProductPriceColumn"> <?php echo '₱' . $PricesString;?></td>
                            <td id="QuantityColumn"> <?php echo $QuantitiesDetailsString;?></td>
                            <td id="OrderDateColumn"> <?php echo $FormattedTime;?></td>
                            <td id="TotalPriceColumn"> <?php echo '₱' . htmlspecialchars($Row['TOTAL_PRICE']);?></td>
                            <td id="OrderStatusColumn">
                                <select class="form-select SelectStatus custom-select-sizeform mySelect" name="OrderStatus" data-order-id='<?php echo $Row['OrderID']; ?>'>
                                    <option value="Pending" <?php if(htmlspecialchars($Row['OrderStatus']) == 'Pending') echo 'selected'; ?>>Pending</option>
                                    <option value="Processing" <?php if(htmlspecialchars($Row['OrderStatus']) == 'Processing') echo 'selected'; ?>>Processing</option>
                                    <option value="Shipped" <?php if(htmlspecialchars($Row['OrderStatus']) == 'Shipped') echo 'selected'; ?>>Shipped</option>
                                    <option value="Delivered" <?php if(htmlspecialchars($Row['OrderStatus']) == 'Delivered') echo 'selected'; ?>>Delivered</option>
                                    <option value="Cancelled" <?php if(htmlspecialchars($Row['OrderStatus']) == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                                </select>
                            </td>
                            <td id="AddressColumn"> <?php echo $Row['Address'];?></td>
                        </tr>

                        <?php
                            ;}
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- Success Notify Modal -->
    <div class="modal fade" id="SuccessfullModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" class="AnotherOrder" id="AnotherOrder">Another Order</button>
                    <button type="button" class="btn btn-secondary" id="Confirm">Continue</button>
                </div>

            </div>
        </div>
    </div>
    <!-- Notify Modal 2 -->
    <div class="modal fade" id="NotifyModal2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-custom">
                <div class="modal-header">
                    <h5 class="modal-title" id="mainModalLabel">Error: Trying to log in</h5>
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

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/8.0.0/mdb.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>


    <!-- Navigation Link Jscript -->
    <script type="module">        
        // Import some functions
        import * as jsFunctions from '../mainFunctions/Functions.js';

        var IsOpen = "<?php echo $IsSideNavOpen; ?>";

        $(document).ready(function() {
            if (IsOpen == "true"){
                $('#sidebar').toggleClass('show');
                $('#mainContent').toggleClass('shift');
            }

            $('#menuToggle').click(function() {
                $('#sidebar').toggleClass('show');
                $('#mainContent').toggleClass('shift');
                
                //console.log(IsOpen);

                if (IsOpen == "true" || IsOpen == true ){
                    jsFunctions.SendAJXCallback({CallBack:'SideNavBarOpen', Data:{IsSideNavOpenValue:"false"}})
                    IsOpen = "false";
                }else{
                    jsFunctions.SendAJXCallback({CallBack:'SideNavBarOpen', Data:{IsSideNavOpenValue:"true"}})
                    IsOpen = "true";
                }
            })
        });
    </script>

    <script>
        const SubmitButton = document.getElementById('SubmitButton');
        const orderStatusSelect = document.getElementsByName('OrderStatus');
        const SelectStatus = document.querySelectorAll('.SelectStatus')
        var Clicktimes = 0;
        
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

        var RemoveOrderAgainButton = '<?php
            if (isset($_SESSION['ErrorAddAgain'])){
                echo $_SESSION['ErrorAddAgain'];
                unset($_SESSION['ErrorAddAgain']);
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

        if (RemoveOrderAgainButton !== ""){
            if (RemoveOrderAgainButton == "ErroredWhileOrdering"){
                document.getElementsByClassName('modal-body')[0].textContent = RemoveOrderAgainButton;
                document.getElementsByClassName('AnotherOrder')[0].textContent = ""
                document.getElementById('AnotherOrder').remove();

            }else{
                document.getElementById('AnotherOrder').addEventListener('click', function(){
                    window.location.href = 'MakeOrderPage.php';
                    NotifyModal.hide();
                })
            }
        }

        // Search Bar Function
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

        // Removes the Focus
        orderStatusSelect[0].addEventListener('change', function() {
            this.blur(); // Remove focus from the select element
        });

        function SetStatusColor(selectedStatus, IsQuery, IsDefault){
            if (IsQuery != null){
                if (IsDefault != null){
                    selectedStatus.css({
                        'background-color': '#ffff',
                        'color': '#000'
                    });
                }else{
                    var selectval = selectedStatus.val();
                    if (selectval === "Pending") {
                        selectedStatus.css({
                            'background-color': '#f0ad4e',
                            'color': '#000'
                        });
                    } else if (selectval === "Processing") {
                        selectedStatus.css({
                            'background-color': '#5bc0de',
                            'color': '#000'
                        });
                    } else if (selectval === "Shipped") {
                        selectedStatus.css({
                            'background-color': '#66cdaa',
                            'color': '#000'
                        });
                    } else if (selectval === "Delivered") {
                        selectedStatus.css({
                            'background-color': '#5cb85c',
                            'color': '#000'
                        });
                    } else if (selectval === "Cancelled") {
                        selectedStatus.css({
                            'background-color': '#d9534f',
                            'color': '#000'
                        });
                    }
                }
            }else{
                var selectval = selectedStatus.value

                if (selectval === "Pending") {
                selectedStatus.style.backgroundColor = "#f0ad4e";
                } else if (selectval === "Processing") {
                    selectedStatus.style.backgroundColor = "#5bc0de";
                } else if (selectval === "Shipped") {
                    selectedStatus.style.backgroundColor = "#66cdaa";
                } else if (selectval === "Delivered") {
                    selectedStatus.style.backgroundColor = "#5cb85c";
                } else if (selectval === "Cancelled") {
                    selectedStatus.style.backgroundColor = "#d9534f";
                }
            }
        }

        const orderbuttonSelect = document.querySelectorAll('select[name="OrderStatus"]');

        orderbuttonSelect.forEach(element => {
            SetStatusColor(element);
            element.addEventListener('change', function() {
                SetStatusColor(this);
            });
        })

        $(document).ready(function() {
            let isOpen = false;
            var isScrolling;

            // Add mousewheel and DOMMouseScroll event handlers
            $('.mySelect').on('show.bs.select', function() {
                // When dropdown is shown, add event listeners to prevent scrolling
                $(this).siblings('.dropdown-menu').on('wheel', function(e) {
                    e.preventDefault();
                });
            });

            $('.mySelect').on('hide.bs.select', function() {
                // Remove the event listener when the dropdown is hidden
                $(this).siblings('.dropdown-menu').off('mousewheel DOMMouseScroll');
            });
            
            document.addEventListener('wheel', function(event) {
                // Clear the timeout if it's still running
                clearTimeout(isScrolling);

                // Set a timeout to run after scrolling ends
                isScrolling = setTimeout(function() {
                    $('.mySelect').blur();
                    isScrolling = null;
                }, 100); // Set delay to 100ms or your desired duration
            });

            $('.mySelect').on('focus', function() {
                isOpen = true;
            });

            $('.mySelect').on('blur', function() {
                isOpen = false;
                Clicktimes = 0;
                SetStatusColor($(this), true);
            });

            $('.mySelect').on('click', function(eventobject){
                if (isOpen) {
                    SetStatusColor($(this), true, "Default");
                    Clicktimes += 1;
                    if (Clicktimes > 1){
                        $('.mySelect').blur();
                    }
                }else {
                    isOpen = true; 
                }
            });

            $(document).on('click', function(event) {
                if (!$(event.target).closest('.mySelect').length) {
                    isOpen = false;
                    Clicktimes = 0;
                }
            });
        });

        var SelectedStatusOldValue;

        SelectStatus.forEach(element => {
            SelectedStatusOldValue = element.value
            element.addEventListener('change', function() {
                const selectedStatus = this.value; // Get the selected value
                const orderId = this.getAttribute('data-order-id'); // Get the Order ID from data attribute

                // Make AJAX request to update the order status
                fetch('../mainFunctions/pageFunctions/UpdateOrderDetails.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `OrderID=${orderId}&OrderStatus=${selectedStatus}`
                })
                .then(response => {
                    if (response.ok) {
                        return response.text();
                    }
                    throw new Error('Network response was not ok.');
                })
                .then(data => {
                    if (data != ""){
                        var NotifyModal2 = new mdb.Modal(document.getElementById('NotifyModal2'));

                        document.getElementById('mainModalLabel').innerHTML = "Error: Trying to Update Details!";
                        document.getElementsByClassName('mainmodalbody')[0].innerHTML = "Not Enough Stocks" + '<br>' + "Product: " + data;

                        NotifyModal2.show();

                        this.value = SelectedStatusOldValue;

                        SetStatusColor(this)
                    }else{
                        SelectedStatusOldValue = this.value;
                    }

                    console.log(data); // If Success this will print
                    this.blur();
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });
            });
        });
    </script>

</body>
</html>