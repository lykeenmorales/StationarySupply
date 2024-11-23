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

    function ConvertToPhoneNumber($PhoneNumber){
        $ReceivedPhoneNumber = (string)$PhoneNumber;
    
        if (substr($ReceivedPhoneNumber, 0,1) == '0'){
            $ReceivedPhoneNumber = substr($ReceivedPhoneNumber, 1);
        }
    
        $PhoneNumberStringEdit1 = '+63' . $ReceivedPhoneNumber;    
        $FinalizePhoneNumber = preg_replace('/(\+63)(\d{3})(\d{3})(\d{4})/', '$1 $2 $3 $4', $PhoneNumberStringEdit1);
    
        return $FinalizePhoneNumber;
    }
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
    </style>
</head>
<body>       

    <!-- Sidebar Navigation -->
    <div class="sidebar" id="sidebar">
        <div class="SideBarObjects">
            <ul class="list-unstyled p-3">
                <li class = "NavigationLinks"> <i class="bi-box-arrow-right"></i>  <a href="#" class="text-decoration-none">Logout</a></li>

                <hr>

                <li class = "NavigationLinks"> <i class="bi bi-window"></i>  <a href="../homepage.php" class="text-decoration-none">Dashboard</a></li>
                <li class = "NavigationLinks"> <i class="bi bi-people"></i>  <a href="CustomerPage.php" class="">Client Accounts</a></li>
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
        </div>
    </nav>

    <!-- Main Content Area -->
    <div class="main-content" id="mainContent">
        <h5 class="Content_title">Adding Client Account</h5>

        <div class="container text-center ">
            <form action="../mainFunctions/pageFunctions/customerInsert.php" method="post" class="row g-4 needs-validation justify-content-md-center" id="MainForm" novalidate>
                <div class="row justify-content-md-center text-center">
                    <div class="col-md-4 position-relative">
                        <label for="validationTooltip01" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="FirstNameInput" value="" name="FirstName" placeholder="Enter first name" required>
                        <div class="invalid-tooltip" name = "FirstNameInputFeedback"></div>
                    </div>
                    <div class="col-md-4 position-relative">
                        <label for="validationTooltip01" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="LastNameInput" value="" name="LastName" placeholder="Enter last name" required>
                        <div class="invalid-tooltip" name = "LastNameInputFeedback">
                           
                        </div>
                    </div>
                </div>

                <div class="row justify-content-md-center text-center">
                    <div class="col-md-3 position-relative mt-5">
                        <label for="validationTooltip02" class="form-label">Phone</label>
                        <input type="number" class="form-control" id="PhoneInput" value="" name="PhoneNumber" placeholder="Enter phone number" min="0" maxlength="11" required>
                        <div class="invalid-tooltip" name="PhoneInputFeedback"></div>
                    </div>
                    <div class="col-md-3 position-relative mt-5">
                        <label for="validationTooltip01" class="form-label">Email</label>
                        <input type="text" class="form-control" id="EmailInput" value="" name="Email" placeholder="Enter email" autocomplete="on" required>
                        <div class="invalid-tooltip" name = "EmailInputFeedback">
                           
                        </div>
                    </div>
                </div>

                <div class="row justify-content-md-center text-center">
                    <div class="col-md-4 position-relative mt-5">
                        <label for="validationTooltip01" class="form-label">Address</label>
                        <input type="text" class="form-control" id="AddressInput" value="" name="Address" placeholder="Enter Address" autocomplete="on" required>
                        <div class="invalid-tooltip" name = "AddressInputFeedback">
                           
                        </div>
                    </div>
                </div>

                <div class="col-12">
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
        const SubmitButton = document.getElementById('submitButton');
        const PhoneNumberInput = document.getElementById("PhoneInput");
        const FirstNameInput = document.getElementById('FirstNameInput');
        const LastNameInput = document.getElementById('LastNameInput');
        const EmailInput = document.getElementById('EmailInput');
        const AddressInput = document.getElementById('AddressInput')

        // Email Pattern to Check
        const EmailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

        SubmitButton.addEventListener('click', ValidateSubmit);

        PhoneNumberInput.addEventListener('input', function(){
            // Remove any non-numeric characters
            this.value = this.value.replace(/[^0-9]/g, '');

            // Limit to 11 characters
            if (this.value.length > 11) {
                this.value = this.value.slice(0, 11);
            }
        })

        function ValidateSubmit(){
            event.preventDefault();

            var ValidFirstName = true;
            var ValidLastName = true;
            var ValidPhoneNumber = true;
            var ValidAddress = true;
            var ValidEmail = true;

            if (FirstNameInput.value === ""){
                ValidFirstName = false;
                event.stopPropagation();

                FirstNameInput.classList.add('is-invalid'); 
                FirstNameInput.classList.remove('is-valid'); 

                document.getElementsByName("FirstNameInputFeedback")[0].textContent = "Input field is Empty!";
            }else{
                FirstNameInput.classList.add('is-valid'); 
                FirstNameInput.classList.remove('is-invalid'); 
            }

            if (LastNameInput.value === ""){
                ValidLastName = false;
                event.stopPropagation();

                LastNameInput.classList.add('is-invalid'); 
                LastNameInput.classList.remove('is-valid'); 

                document.getElementsByName("LastNameInputFeedback")[0].textContent = "Input field is Empty!";
            }else{
                LastNameInput.classList.add('is-valid'); 
                LastNameInput.classList.remove('is-invalid'); 
            }

            if (AddressInput.value === ""){
                ValidAddress = false;
                event.stopPropagation();

                AddressInput.classList.add('is-invalid'); 
                AddressInput.classList.remove('is-valid'); 

                document.getElementsByName("AddressInputFeedback")[0].textContent = "Input field is Empty!";
            }else{
                AddressInput.classList.add('is-valid'); 
                AddressInput.classList.remove('is-invalid'); 
            }

            if (EmailInput.value == "" || EmailInput.value == null){
                ValidEmail = false;
                event.stopPropagation();

                EmailInput.classList.add('is-invalid'); 
                EmailInput.classList.remove('is-valid'); 

                document.getElementsByName("EmailInputFeedback")[0].textContent = "Input field is Empty!";
            }else{
                if (!EmailPattern.test(EmailInput.value)){
                    ValidEmail = false;
                    event.stopPropagation();

                    EmailInput.classList.add('is-invalid'); 
                    EmailInput.classList.remove('is-valid'); 

                    document.getElementsByName("EmailInputFeedback")[0].textContent = "Invalid Email!";
                }else{
                    EmailInput.classList.add('is-valid'); 
                    EmailInput.classList.remove('is-invalid'); 
                }
            }

            if (PhoneNumberInput.value === 0 || PhoneNumberInput.value === null || PhoneNumberInput.value === ""){
                ValidPhoneNumber = false;
                event.stopPropagation();

                PhoneNumberInput.classList.add('is-invalid'); 
                PhoneNumberInput.classList.remove('is-valid'); 

                document.getElementsByName("PhoneInputFeedback")[0].textContent = "Input field is Empty!";
            }else if(PhoneNumberInput.value.length < 11){
                ValidPhoneNumber = false;
                event.stopPropagation();

                PhoneNumberInput.classList.add('is-invalid'); 
                PhoneNumberInput.classList.remove('is-valid'); 

                document.getElementsByName("PhoneInputFeedback")[0].textContent =  "Phone number must be 11 numbers";
            }else if (PhoneNumberInput.value.length > 11){
                ValidPhoneNumber = false;
                event.stopPropagation();

                PhoneNumberInput.classList.add('is-invalid'); 
                PhoneNumberInput.classList.remove('is-valid'); 

                document.getElementsByName("PhoneInputFeedback")[0].textContent = "Phone number must be 11 numbers";
            }else if (PhoneNumberInput.value.charAt(0) != 0 || PhoneNumberInput.value.charAt(1) != 9){
                ValidPhoneNumber = false;
                event.stopPropagation();

                PhoneNumberInput.classList.add('is-invalid'); 
                PhoneNumberInput.classList.remove('is-valid'); 

                document.getElementsByName("PhoneInputFeedback")[0].textContent = "Phone Number must start with '09'";
            }else{
                PhoneNumberInput.classList.add('is-valid'); 
                PhoneNumberInput.classList.remove('is-invalid'); 
            }

            if (ValidFirstName != false && ValidLastName != false && ValidPhoneNumber != false && ValidAddress != false && ValidEmail != false){
                document.getElementById('MainForm').submit();
            }
        }
    </script>
</body>
</html>