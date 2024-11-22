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
    if (isset($_SESSION['IsSideMenuOpen'])){
        $IsSideNavOpen = $_SESSION['IsSideMenuOpen'];
    }

    include '../mainFunctions/connection.php';

    $C_FirstName = null;
    $C_LastName = null;
    $C_CustomerID = null;

    if (isset($_SESSION['FirstName'])){
        $C_FirstName = $_SESSION['FirstName'];
    }else{
        if (isset($_SESSION['LastOrder_CustomerFirstName'])){
            $C_FirstName = $_SESSION['LastOrder_CustomerFirstName'];
        }else{
             // If not Found we Return to Order Details Page and Remove the Order Again Button
             $_SESSION['ErrorAddAgain'] = "Error Occurred Trying to get previous Order";
             header('Location: Order_DetailsPage.php');
             exit; 
        }
    }

    if (isset($_SESSION['LastName'])){
        $C_LastName = $_SESSION['LastName'];
    }else{
        if (isset($_SESSION['LastOrder_CustomerLastName'])){
            $C_LastName = $_SESSION['LastOrder_CustomerLastName'];
        }else{
             // If not Found we Return to Order Details Page and Remove the Order Again Button
             $_SESSION['ErrorAddAgain'] = "Error Occurred Trying to get previous Order";
             header('Location: Order_DetailsPage.php');
             exit; 
        }
    }

    if (isset($_SESSION['CustomerID'])){
        $C_CustomerID = $_SESSION['CustomerID'];
    }else{
        if (isset($_SESSION['LastOrder_CustomerID'])){
            $C_CustomerID = $_SESSION['LastOrder_CustomerID'];
        }else{
            // If not Found we Return to Order Details Page and Remove the Order Again Button
            $_SESSION['ErrorAddAgain'] = "Error Occurred Trying to get previous Order";
            header('Location: Order_DetailsPage.php');
            exit; 
        }
    }

    $_SESSION['CustomerID'] = $C_CustomerID;
    $_SESSION['FirstName'] = $C_FirstName;
    $_SESSION['LastName'] = $C_LastName;

    unset($_SESSION['LastOrder_CustomerID']);
    unset($_SESSION['LastOrder_CustomerFirstName']);
    unset($_SESSION['LastOrder_CustomerLastName']);
    unset($_SESSION['ErrorAddAgain']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Account</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="../Css/MainDesign.css">
    <style>
        .custom-warning-text{
            color:red;
        }

        .suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            border-radius: 14px;
            background-color: #121212;
            color: white;
            border: 0.5px solid #ced4da22;
            margin-top: 5px;
            z-index: 500;
            display: none;
            max-height: 180px;
            overflow-y: auto;
        }
        .suggestion-item {
            padding: 10px;
            cursor: pointer;
        }
        .suggestion-item:hover {
            background-color: #3c4043be;
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
                <li class = "NavigationLinks"> <i class="bi bi-people"></i>  <a href="CustomerPage.php" class="">Client Accounts</a></li>
                <li class = "NavigationLinks"> <i class="bi bi-box"></i>  <a href="ProductInfoPage.php" class="">Products</a></li>
                <li class = "NavigationLinks"> <i class="bi bi-clipboard"></i>  <a href="Order_DetailsPage.php" class="">Order Details</a></li>

                <hr>

                <li class = "NavigationLinks"> <i class="fas fa-eye"></i> <a href="../clientPages/clientHomePage.php" class="text-decoration-none">View Client Page</a></li>

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
        <h5 class="Content_title">Making Order For: <?php echo $_SESSION['LastName'] . ", " . $C_FirstName ?></h5>

        <div class="container text-center ">
            <form action="../mainFunctions/pageFunctions/MakeOrder.php" method="post" class="row g-4 needs-validation justify-content-md-center" id="MainForm" novalidate>
                <div class="row justify-content-md-center text-center">
                    <div class="col-md-4 position-relative">
                        <div class="input-container">
                            <label for="validationTooltip01" class="form-label">Choose Product</label>
                            <input type="text" class="form-control" id="SearchInputField" placeholder="Select a product" autocomplete="off" required>
                            <div class="invalid-tooltip" name = "SearchInputFeedback" id="SearchInputFeedback" ></div>

                            <div class="suggestions" id="SuggestionsContainer">
                                <?php
                                    $ProductsArray = array();
                                    $Query = "SELECT * FROM products";
                                    $QUERYRESULT = mysqli_query($connection, $Query);

                                    while ($Row = mysqli_fetch_assoc($QUERYRESULT)){
                                        if ($Row['StockQuantity'] > 0){
                                            echo '<div class="suggestion-item" data-value="' . $Row["productID"] . ',' . htmlspecialchars($Row['Name']) . '">' . htmlspecialchars($Row['Name']) . ' | ' . '{Qty.' . $Row['StockQuantity'] . '}' . ' ' . '{' . $Row['PackSize'] . ' ' . "P/S" . '}' .'</div>';
                                            array_push($ProductsArray, htmlspecialchars($Row['Name']));
                                        }
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-md-center text-center mt-2">
                    <div class="col-md-2 position-relative">
                        <label for="validationTooltip01" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="AddressInput" value="" name="Amount" placeholder="Enter Amount" required>
                        <div class="invalid-tooltip" name = "AmountInputFeedback"></div>
                    </div>
                </div>

                <div class="col-12">
                    <input type="hidden" name="ProductID" id="ProductID">
                    <button class="btn btn-primary" type="submit" id="submitButton">Submit</button>
                </div>
            </form>
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
        const SubmitButton = document.getElementById('submitButton');
        const AmountInput = document.getElementById('AddressInput');
        const ProductIDHiddenVal = document.getElementById('ProductID');
        const MainForm = document.getElementById('MainForm');

        SubmitButton.addEventListener('click', ValidateSubmit);

        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

        var IsOpen = "<?php echo $IsSideNavOpen; ?>";
        var ProductDatavalue = "empty";
        
        $(document).ready(function() {
            const SearchinputField = $('#SearchInputField');
            const suggestionsContainer = $('#SuggestionsContainer');

           

            if (IsOpen == "true"){
                $('#sidebar').toggleClass('show');
                $('#mainContent').toggleClass('shift');
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

            SearchinputField.on('focus', function() {
                suggestionsContainer.show();
            });

            // Search suggestions based on text input 
            SearchinputField.on('input', function() {
                const value = $(this).val().toLowerCase();

                suggestionsContainer.find('.suggestion-item').each(function() {
                    const text = $(this).text().toLowerCase();
                    $(this).toggle(text.includes(value)); 
                });

                if ($(this).is(':focus')) {
                    suggestionsContainer.show();
                }
                
                SearchinputField.removeClass('is-valid');
                SearchinputField.removeClass('is-invalid');

                ProductDatavalue = "empty";
            });

            // Handle suggestion click
            suggestionsContainer.on('click', '.suggestion-item', function(event) {
                event.stopPropagation()
                var compareValue = $(this).text().toLowerCase();
                SearchinputField.val($(this).data('value').split(',')[1]);
                
                const itemText = $(this).text().toLowerCase();
                if (itemText === compareValue){
                    ProductDatavalue = $(this).data('value').split(',');

                    SearchinputField.addClass('is-valid');
                    SearchinputField.removeClass('is-invalid');
                }else{
                    ProductDatavalue = "empty"
                }

                suggestionsContainer.hide();
            });

            $(document).on('click', function(event) {
                if (!$(event.target).closest('.input-container').length) {
                    suggestionsContainer.hide();
                    const SearchValue = SearchinputField.val().toLowerCase();

                    suggestionsContainer.find('.suggestion-item').each(function(){
                        const itemText = $(this).data('value').split(',')[1].toLowerCase();
                        if (itemText === SearchValue){
                            ProductDatavalue = $(this).data('value').split(',');
                            return false;
                        }

                        ProductDatavalue = "empty";
                    })
                }
            });

            SearchinputField.on('keydown', function(event){
                if (event.key === 'Tab') {
                    event.preventDefault();

                    const searchValue = $(this).val().toLowerCase();
                    var nearestMatch = null;
                    
                    // Find the nearest match in the suggestions
                    suggestionsContainer.find('.suggestion-item').each(function () {
                        const itemText = $(this).data('value').split(',')[1].toLowerCase();

                        if (itemText.startsWith(searchValue)) {
                            nearestMatch = $(this).data('value').split(',')[1];
                            return false;
                        }
                    });

                    if (nearestMatch) {
                        $(this).val(nearestMatch);
                        $(this).focus();
                    }
                }

                if (event.key === 'Enter'){
                    const SearchValue = $(this).val().toLowerCase();
                    
                    suggestionsContainer.find('.suggestion-item').each(function(){
                        const itemText = $(this).data('value').split(',')[1].toLowerCase();

                        if (itemText === SearchValue){
                            ProductDatavalue = $(this).data('value').split(',');
                            return false;
                        }
                        
                        ProductDatavalue = "empty";
                    })

                    if (ProductDatavalue !== "empty"){
                        SearchinputField.addClass('is-valid');
                        SearchinputField.removeClass('is-invalid');

                        suggestionsContainer.hide();
                    }else{
                        if (SearchValue === "" || SearchValue === null){
                            $('#SearchInputFeedback').text('Empty Field!');
                        }else{
                            $('#SearchInputFeedback').text('Invalid Product!');
                        }

                        SearchinputField.addClass('is-invalid');
                        SearchinputField.removeClass('is-valid');

                        ProductDatavalue = "empty";
                        suggestionsContainer.show();
                    }
                 
                    event.preventDefault();
                }
            })
        });


        function ValidateSubmit(){
            event.preventDefault();
            const productInput = document.getElementById('SearchInputField');

            var ValidAmount = true;
            var ValidProductDataValue = true;

            if (AmountInput.value <= 0){
                ValidAmount = false;

                document.getElementsByName('AmountInputFeedback')[0].textContent = "Amount cant be 0!";
                AmountInput.classList.add('is-invalid');
                AmountInput.classList.remove('is-valid');
            }else{
                ValidAmount = true;

                AmountInput.classList.add('is-valid');
                AmountInput.classList.remove('is-invalid');
            }

            if (ProductDatavalue === null || ProductDatavalue === "" || ProductDatavalue === "empty"){
                ValidProductDataValue = false;

                document.getElementsByName('SearchInputFeedback')[0].textContent = "Product Input Is Invalid!";
                productInput.classList.add('is-invalid');
                productInput.classList.remove('is-valid');
            }else{
                ValidProductDataValue = true;

                productInput.classList.add('is-valid');
                productInput.classList.remove('is-invalid');
            }

            if (ValidProductDataValue === true && ValidAmount === true){
                // Create TimeZone Value for DateTime in sql data
                const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

                const TimeZoneInput = document.createElement("input");
                TimeZoneInput.type = "hidden";
                TimeZoneInput.name = "TimeZone";
                TimeZoneInput.value = userTimezone;

                MainForm.appendChild(TimeZoneInput);

                ProductIDHiddenVal.value = ProductDatavalue[0];

                document.getElementById('MainForm').submit();
            }
        }
    </script>
</body>
</html>