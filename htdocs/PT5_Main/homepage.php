<?php
    session_start();
    include 'mainFunctions/connection.php';

    // Check any Bypasses or Unauthorized Access
    if (isset($_SESSION['Login_UserID'])){
        if (isset($_SESSION['Login_UserType'])){
            if ($_SESSION['Login_UserType'] != "Admin"){
                header("Location: clientPages/clientHomePage.php");
            }
        }
    }else{
        header("Location: LoginPage.php");
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
        'ProductStockQuantity',
        'p_Description',
        'p_Price',
        'p_productName',
        'p_productPackSize',
        'p_picture_path'
    ];

    foreach($SessionKeysToUnset as $keys){
        if (isset($_SESSION[$keys])){
            unset($_SESSION[$keys]);
        }
    }

    //-- CALCULATING Todays Price --\\
    $Todays_TotalPrice_Purchases_Query = "SELECT SUM(totalPrice) as total_price FROM orders WHERE CurrDate = CURDATE()";
    $Previous_TotalPrice_Query = "SELECT SUM(totalPrice) as total_price FROM orders WHERE CurrDate >= CURDATE() - INTERVAL 7 DAY";
    $Previous_TotalPrice_Result = $connection -> query($Previous_TotalPrice_Query);
    $Previous_Price_Result = $Previous_TotalPrice_Result -> fetch_assoc();
    // Calculations
    $Today_TotalPrice_Result = $connection -> query($Todays_TotalPrice_Purchases_Query);
    $TodaysPrice = $Today_TotalPrice_Result -> fetch_assoc()['total_price'];
    //
    $Previous_TotalPrice_Result = $connection -> query($Previous_TotalPrice_Query);
    $Previous_Price = $Previous_TotalPrice_Result -> fetch_assoc()['total_price'];

    $PreviousSales = 0;
    

    $percentage_change = null;
   
    if (!empty($Previous_Price)){
        $PreviousSales = (($TodaysPrice - $Previous_Price) / $Previous_Price) * 100;
    }else{
        $PreviousSales = 0;
    }

    // Formatting
    $FormattedTodaysPrice;
    $FinalPercentageValue;
    $IsNegativePercent_TodayValue = false;

    if ($PreviousSales > 0){
        $FinalPercentageValue = "+" . number_format($PreviousSales, 2) . "% ";
    }elseif ($PreviousSales <= 0){
        $IsNegativePercent_TodayValue = true;
        $FinalPercentageValue = "-" . number_format(abs($PreviousSales), 2) . "% ";
    }
    
    if ($TodaysPrice > 1000){
        $FormattedTodaysPrice = '₱' . number_format($TodaysPrice / 1000, 2) . 'k';
        // Million and Above Set up
        if ($TodaysPrice > 1000000){
            $FormattedTodaysPrice = '₱' . number_format($TodaysPrice / 1000000, 2) . 'm';
        }
    }else{
        if ($TodaysPrice == ""){
            $FormattedTodaysPrice = '₱0.00';
        }else{
            $FormattedTodaysPrice = '₱' . number_format($TodaysPrice, 2);
        }
    }
    
    if ($FinalPercentageValue == "" || $FinalPercentageValue >= 100){
        $FinalPercentageValue = "-100.00% ";
    }elseif ($PreviousSales == 0){
        $IsNegativePercent_TodayValue = false;
        $FinalPercentageValue = "0.00% ";
    }

    //-- CALCULATING Todays Orders --\\
    $GetOrderInfos = "SELECT Count(*) AS Total_Todays_Order FROM orders WHERE DATE(CurrDate) = CURDATE()";
    $GetOrderInfos_Query = $connection -> query($GetOrderInfos);
    $GetOrderInfos_Result = $GetOrderInfos_Query -> fetch_assoc();

    $GetLast_OrderInfos = "SELECT Count(*) AS Last_Month_Orders FROM orders WHERE DATE(CurrDate) != CURDATE() - INTERVAL 1 DAY AND DATE(CurrDate) >= CURDATE() - INTERVAL 1 MONTH";
    $GetLast_OrderInfos_Query = $connection -> query($GetLast_OrderInfos);
    $GetLast_OrderInfos_Result = $GetLast_OrderInfos_Query -> fetch_assoc();

    $FinalizedTotal_TodaysOrder = 0;
    $FinalizedTotal_TodaysOrder_Percent = 0;
    $Percentage_TotalOrders = 0;
    $TodaysOrder_IsPositive = false;

    $lastmonthOrder = 0;
    $todaysTotalOrder = 0;

    // Get Last Month Order
    if (isset($GetLast_OrderInfos_Result['Last_Month_Orders'])){
        if ($GetLast_OrderInfos_Result['Last_Month_Orders'] != ""){
            $lastmonthOrder = $GetLast_OrderInfos_Result['Last_Month_Orders'];
        }else{
            $lastmonthOrder = 0;
        }
    }

    // Get Todays Total Orders
    if (isset($GetOrderInfos_Result['Total_Todays_Order'])){
        if ($GetOrderInfos_Result['Total_Todays_Order'] != ""){
            $FinalizedTotal_TodaysOrder = $GetOrderInfos_Result['Total_Todays_Order'];
            $todaysTotalOrder = $GetOrderInfos_Result['Total_Todays_Order'];
        }else{
            $FinalizedTotal_TodaysOrder = "0";
        }
    }else{
        $FinalizedTotal_TodaysOrder = "0";
    }

    if ($todaysTotalOrder > 1000){
        $FinalizedTotal_TodaysOrder = ($todaysTotalOrder / 1000) . 'k';
        // Million and Above Set up
        if ($todaysTotalOrder > 1000000){
            $FinalizedTotal_TodaysOrder = ($todaysTotalOrder / 1000000) . 'm';
        }
    }else{
        if ($todaysTotalOrder == "" || $todaysTotalOrder == 0){
            $FinalizedTotal_TodaysOrder = '0.00';
        }else{
            $FinalizedTotal_TodaysOrder = $todaysTotalOrder;
        }
    }
    

    if ($lastmonthOrder > 0){
        $Percentage_TotalOrders = (($todaysTotalOrder - $lastmonthOrder) / $lastmonthOrder) * 100;
    }else{
        $Percentage_TotalOrders = 0;
    }

    if ($Percentage_TotalOrders > 0){
        $TodaysOrder_IsPositive = true;
        $FinalizedTotal_TodaysOrder_Percent = "+" . number_format($Percentage_TotalOrders, 2) . "% ";
    }elseif ($Percentage_TotalOrders <= 0){
        $FinalizedTotal_TodaysOrder_Percent = "-" . number_format(abs($Percentage_TotalOrders), 2) . "% ";
    }

    if ($Percentage_TotalOrders == "" || $Percentage_TotalOrders >= 100){
        $FinalizedTotal_TodaysOrder_Percent = "-100.00% ";
    }elseif ($PreviousSales == 0){
        $TodaysOrder_IsPositive = true;
        $FinalizedTotal_TodaysOrder_Percent = "0.00% ";
    }

    //-- TOTAL SALES CALCULATION --\\
    $CurrentWeek_TotalSales = "SELECT SUM(TotalPrice) AS Total_Sales_Week FROM orders WHERE CurrDate >= CURDATE() - INTERVAL DAYOFWEEK(CURDATE()) - 1 DAY AND CurrDate <= CURDATE()";
    $CurrentWeek_TotalSales_Query = $connection -> query($CurrentWeek_TotalSales);
    $CurrentWeek_TotalSales_Result = $CurrentWeek_TotalSales_Query -> fetch_assoc();
    $LastMonth_TotalSales = "SELECT SUM(TotalPrice) AS Last_Month_Total_Sales FROM orders WHERE CurrDate >= CURDATE() - INTERVAL 1 MONTH";
    $LastMonth_TotalSales_Query = $connection -> query($LastMonth_TotalSales);
    $LastMonth_TotalSales_Result = $LastMonth_TotalSales_Query -> fetch_assoc();

    $Finalized_TotalSales_Percent = 0;
    $Finalized_TotalSales = 0;
    $TotalSales_IsPositive = false;

    $Finalized_TotalLastMonthSale = 0;

    $LastMonthSale = 0;
    $CurrentWeekSale = 0;

    // Get Last Month Sale
    if (isset($LastMonth_TotalSales_Result['Last_Month_Total_Sales'])){
        if ($LastMonth_TotalSales_Result['Last_Month_Total_Sales'] != ""){
            $LastMonthSale = $LastMonth_TotalSales_Result['Last_Month_Total_Sales'];
        }else{
            $LastMonthSale = 0;
        }
    }

    // Get Current Week Sale
    if (isset($CurrentWeek_TotalSales_Result['Total_Sales_Week'])){
        if ($CurrentWeek_TotalSales_Result['Total_Sales_Week'] != ""){
            $CurrentWeekSale = $CurrentWeek_TotalSales_Result['Total_Sales_Week'];
        }else{
            $CurrentWeekSale = 0;
        }
    }else{
        $CurrentWeekSale = 0;
    }

    if ($CurrentWeekSale > 1000){
        $Finalized_TotalSales =  ($CurrentWeekSale / 1000) . 'k';
        // Million and Above Set up
        if ($CurrentWeekSale > 1000000){
            $Finalized_TotalSales = ($CurrentWeekSale / 1000000) . 'm';
        }
    }else{
        if ($CurrentWeekSale == "" || $CurrentWeekSale == 0){
            $Finalized_TotalSales = '0.00';
        }else{
            $Finalized_TotalSales = $CurrentWeekSale;
        }
    }
    

    if ($LastMonthSale > 0){
        $Finalized_TotalLastMonthSale = (($CurrentWeekSale - $LastMonthSale) / $LastMonthSale) * 100;
    }else{
        $Finalized_TotalLastMonthSale = 0;
    }

    if ($Finalized_TotalSales_Percent > 0){
        $TotalSales_IsPositive = true;
        $Finalized_TotalSales_Percent = "+" . number_format($Finalized_TotalLastMonthSale, 2) . "% ";
    }elseif ($Percentage_TotalOrders <= 0){
        $Finalized_TotalSales_Percent = "-" . number_format(abs($Finalized_TotalLastMonthSale), 2) . "% ";
    }

    if ($Percentage_TotalOrders == "" || $Percentage_TotalOrders >= 100){
        $Finalized_TotalSales_Percent = "-100.00% ";
    }elseif ($PreviousSales == 0){
        $TotalSales_IsPositive = true;
        $Finalized_TotalSales_Percent = "0.00% ";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    

    <link rel="stylesheet" href="Css/MainDesign.css">

    <style>
        /* Ensure the chart canvas takes full width */
        #purchaseChart {
            height: 250px !important; /* You can set this to whatever height you prefer */
            width: 100% !important;  /* Makes the chart take full width */
        }

        #TotalSalesChart{
            height: 200px !important; /* You can set this to whatever height you prefer */
            width: 100% !important;  /* Makes the chart take full width */
        }

        #DepletingStockChart{
            height: 250px !important; /* You can set this to whatever height you prefer */
            width: 100% !important;  /* Makes the chart take full width */
        }

        /* Responsive styles for larger screens */
        @media (min-width: 768px) {
            #purchaseChart {
                height: 300px; /* Adjust height for larger screens */
            }
            #TotalSalesChart {
                height: 300px; /* Adjust height for larger screens */
            }
            #DepletingStockChart {
                height: 350px; /* Adjust height for larger screens */
            }
        }

        .custom-text-color-Negative{
            color: red !important;
        }
        .custom-text-color-Positive{
            color: green !important;
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

                <li class = "NavigationLinks"> <i class="bi bi-window"></i>  <a href="#" class="text-decoration-none custom-glow-Current-Page">Dashboard</a></li>
                <li class = "NavigationLinks"> <i class="bi bi-people"></i>  <a href="Pages/CustomerPage.php" class="text-decoration-none">Client Accounts</a></li>
                <li class = "NavigationLinks"> <i class="bi bi-box"></i>  <a href="Pages/ProductInfoPage.php" class="text-decoration-none">Products</a></li>
                <li class = "NavigationLinks"> <i class="bi bi-clipboard"></i>  <a href="Pages/Order_DetailsPage.php" class="text-decoration-none">Order Details</a></li>

                <hr>

                <li class = "NavigationLinks"> <i class="fas fa-eye"></i>  <a href="clientPages/clientHomePage.php" class="text-decoration-none">View Client Page</a></li>

                <hr>

                <li class = "NavigationLinks"> <i class="bi-box-arrow-right"></i>  <a href="mainFunctions/pageFunctions/Logout.php" class="text-decoration-none">Logout</a></li>
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
        <div class="container justify-content-md-center text-center">

            <div class="row mb-5 justify-content-center">
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-2 ps-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-sm mb-0 text-capitalize">Today's Money</p>
                                    <h4 class="mb-0"><?php echo $FormattedTodaysPrice; ?></h4>
                                </div>
                                <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark text-center border-radius-lg">
                                    <i class="bi bi-cash opacity-10"></i> <!-- Bootstrap icon for cash -->
                                </div>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-2 ps-3">
                            <?php
                                if ($IsNegativePercent_TodayValue == true){
                                    echo '<p class="mb-0 text-sm"><span class="text-success custom-text-color-Negative font-weight-bolder">' . $FinalPercentageValue .'</span>than last week</p>';
                                }else{
                                    echo '<p class="mb-0 text-sm"><span class="text-success custom-text-color-Positive font-weight-bolder">'. $FinalPercentageValue .'</span>than last week</p>';
                                }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-2 ps-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-sm mb-0 text-capitalize">Today's Orders</p>
                                    <h4 class="mb-0"><?php echo $FinalizedTotal_TodaysOrder ?></h4>
                                </div>
                                <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark text-center border-radius-lg">
                                    <i class="bi bi-person-fill opacity-10"></i>
                                </div>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-2 ps-3">
                            <?php 
                                if ($TodaysOrder_IsPositive == false){
                                    echo "<p class='mb-0 text-sm'><span class='text-success custom-text-color-Negative font-weight-bolder'>". $FinalizedTotal_TodaysOrder_Percent ."</span>than last month</p>";
                                }else{
                                    echo "<p class='mb-0 text-sm'><span class='text-success custom-text-color-Positive font-weight-bolder'>". $FinalizedTotal_TodaysOrder_Percent ."</span>than last month</p>";
                                }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="card">
                        <div class="card-header p-2 ps-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-sm mb-0 text-capitalize">Sales</p>
                                    <h4 class="mb-0"><?php echo $Finalized_TotalSales; ?></h4>
                                </div>
                                <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark text-center border-radius-lg">
                                    <i class="bi bi-cash-stack opacity-10"></i>
                                </div>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-2 ps-3">
                            <?php 
                                if ($TotalSales_IsPositive == false){
                                    echo '<p class="mb-0 text-sm"><span class="text-success custom-text-color-Negative font-weight-bolder">'. $Finalized_TotalSales_Percent .'</span>than Last Month</p>';
                                }else{
                                    echo '<p class="mb-0 text-sm"><span class="text-success custom-text-color-Positive font-weight-bolder">'. $Finalized_TotalSales_Percent .'</span>than Last Month</p>';
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h4>Most Purchased Products</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="mostPurchaseChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mx-auto mt-5">
                    <div class="card">
                        <div class="card-header">
                            <h4>Total Sales</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="TotalSalesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script type="module">        
        // Import some functions
        import * as jsFunctions from './mainFunctions/Functions.js';

        var ctx = document.getElementById('mostPurchaseChart').getContext('2d');
        var ctx2 = document.getElementById('TotalSalesChart').getContext('2d');
        var chart;
        var chart2;

        const chartColors = [
            'rgba(54, 162, 235, 0.6)',  // Blue
            'rgba(255, 99, 132, 0.6)',  // Red
            'rgba(75, 192, 192, 0.6)',  // Teal
            'rgba(255, 206, 86, 0.6)',  // Yellow
            'rgba(153, 102, 255, 0.6)', // Purple
            'rgba(255, 159, 64, 0.6)',  // Orange
            'rgba(199, 199, 199, 0.6)',  // Grey
            'rgba(83, 102, 139, 0.6)',   // Dark Blue
            'rgba(186, 134, 33, 0.6)',   // Brown
            'rgba(124, 186, 43, 0.6)'    // Green
        ];

        var IsOpen = "<?php echo $IsSideNavOpen; ?>";

        $(document).ready(function() {
            if (IsOpen == "true"){
                $('#sidebar').toggleClass('show');
                $('#mainContent').toggleClass('shift');
            }

            $('#menuToggle').click(function() {
                $('#sidebar').toggleClass('show');
                $('#mainContent').toggleClass('shift');

                if (IsOpen == "true" || IsOpen == true ){
                    jsFunctions.SendMainAJXCallback({CallBack:'SideNavBarOpen', Data:{IsSideNavOpenValue:"false"}})
                    IsOpen = "false";
                }else{
                    jsFunctions.SendMainAJXCallback({CallBack:'SideNavBarOpen', Data:{IsSideNavOpenValue:"true"}})
                    IsOpen = "true";
                }
            })
        });

        // Using Json dataType
        function fetchAndUpdateChart() {
            $.ajax({
                url: './mainFunctions/pageFunctions/fetch_top_products.php', 
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log('Success:', data); 

                    if (data.products.length === 0) {
                        console.log('No products found.');
                        return;
                    }

                    var products = data.products;
                    var purchases = data.purchases;

                    if (chart) {
                        chart.destroy();  
                    }

                    chart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: products,
                            datasets: [{
                                label: 'Total Purchases',
                                data: purchases,
                                backgroundColor: chartColors,
                                borderColor: chartColors.map(color => color.replace('0.6', '1')),
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        },
                        
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error fetching data: ' + textStatus, errorThrown);
                    console.log('Response Text:', jqXHR.responseText); // testing log
                }
            });
        }

        function fetchSalesData(){
            $.ajax({
                url: './mainFunctions/pageFunctions/fetch_Total_sales.php', 
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log('Success:', data); 
                    
                    var monthDataLabel = data.months;
                    var salesData = data.TotalSales;

                    if (chart2) {
                        chart2.destroy();  
                    }

                    chart2 = new Chart(ctx2, {
                        type: 'line',
                        data: {
                            labels: monthDataLabel,
                            datasets: [{
                                label: 'Total Sales',
                                data: salesData,
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Date'
                                    }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: 'Total Sales'
                                    },
                                    beginAtZero: true
                                }
                            }
                        },
                        
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error fetching data: ' + textStatus, errorThrown);
                    console.log('Response Text:', jqXHR.responseText); // testing log
                }
            });
        }

        // First Load
        fetchAndUpdateChart();
        fetchSalesData();
    </script>
</body>
</html>