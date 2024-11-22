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
    
    $QUERY = "SELECT * From customeraccount ORDER BY last_name ASC";
    $QUERYRESULT = mysqli_query($connection, $QUERY);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Accounts</title>

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
                <li class = "NavigationLinks"> <i class="bi bi-people"></i>  <a href="#" class="custom-glow-Current-Page">Client Accounts</a></li>
                <li class = "NavigationLinks"> <i class="bi bi-box"></i>  <a href="ProductInfoPage.php" class="">Products</a></li>
                <li class = "NavigationLinks"> <i class="bi bi-clipboard"></i>  <a href="Order_DetailsPage.php" class="">Order Details</a></li>

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
            <a class="text-center custom-Add-link" href="CustomerAdd.php"> Add Client Account </a>
        </div>
    </nav>

    <!-- Main Content Area -->
    <div class="main-content" id="mainContent">
        <h5 class="Content_title">Client Accounts</h5>

        <div class="container">
             <!-- Search Bar -->
             <div class="input-group mb-3 custom-search-bar">
                <span class="input-group-text custom-search-size"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control custom-search-size" id="myInput" type="text" placeholder="Search.." aria-label="Search">
            </div>

            <div class="table-responsive-xxl overflow-auto ScrollingTable ScrollingTable-height">
                <table class="table caption-top align-middle table-hover table-dark table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Phone Number</th>
                            <th>Address</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        <?php
                            while ($Row = mysqli_fetch_assoc($QUERYRESULT)){
                        ?>

                            <tr>
                                <td id="FirstNameColumn" class="NameColumn"> 
                                <?php
                                // Show the Name in the Table
                                    echo htmlspecialchars($Row['last_name']) . ", " . htmlspecialchars($Row['first_name']); 
                                // If User Click Edit Button they will be redirect to Editing page with the productID
                                    echo '<form action="CustomerEdit.php" method="POST" id="MainForm"> 
                                        <input type="hidden" name="CustomerID" value="' . $Row['CustomerID'] . '">
                                        <input type="submit" value="Update" name="EditButton" id="EditButtonInput">
                                        <input type="submit" value="Add Order" name="MakeOrderButton" id="MakeOrderButtonInput"> 
                                    </form>';
                                ?>
                                </td> 
                                <td id="PhoneColumn"> <?php echo $Row['Phone'];?></td>
                                <td id="AddressColumn"> <?php echo htmlspecialchars($Row['Address']);?></td>
                                <td id="EmailColumn"> <?php  if ($Row['Email'] != null ){echo htmlspecialchars($Row['Email']);}; ?></td>
                            </tr>

                        <?php
                            }
                        ?>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

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
                document.getElementsByClassName('modal-body')[0].textContent = ""
                document.getElementById('AnotherAdd').remove();

            }else{
                if (RemoveAddAnother !== "true"){
                    document.getElementById('AnotherAdd').addEventListener('click', function(){
                        window.location.href = 'CustomerAdd.php';
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
                    //console.log(tr[i]);
                } else {
                    tr[i].style.display = 'none';
                }
            }
        });
    </script>
</body>
</html>