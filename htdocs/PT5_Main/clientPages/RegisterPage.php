<?php
    session_start();

    if (isset($_SESSION['Login_UserID'])){
        if (isset($_SESSION['Login_UserType'])){
            if ($_SESSION['Login_UserType'] == "Admin"){
                header("Location: ../homepage.php");
            }else{
                header("Location: clientHomePage.php");
            }
        }
    }

    include '../mainFunctions/connection.php';

    $query = "SELECT * FROM customeraccount";

    $QueryResult = $connection -> query($query);

    // Function to validate and decode ID token
    function validateAndDecodeIdToken($idToken) {
        $clientId = '335141065600-1fd37eljhpn5hba011einlgukj4fb8hk.apps.googleusercontent.com'; // Your client ID
        $googleApiUrl = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . $idToken;

        $response = file_get_contents($googleApiUrl);
        $userInfo = json_decode($response, true);

        // Validate the client ID in the response
        if (isset($userInfo['aud']) && $userInfo['aud'] === $clientId) {
            return $userInfo; // Return user info if valid
        } else {
            return false; // Invalid token or client ID
        }
    }

    // Check if the ID token is received using POST
    if (isset($_POST['id_token'])) {
        $idToken = $_POST['id_token'];

        // Validate the ID token
        $userInfo = validateAndDecodeIdToken($idToken);

        if ($userInfo) {
            echo "User's email: " . $userInfo['email'];
            $_SESSION['ReceivedEmail'] = $userInfo['email'];
        } else {
            echo "Failed to decode ID token.";
        }

        return;
    }

    if (isset($_SESSION['ReceivedEmail'])){
        if ($_SESSION['ReceivedEmail'] != null or $_SESSION['ReceivedEmail'] != ""){
            while ($row = $QueryResult -> fetch_assoc()){
                if ($row['Email'] === $_SESSION['ReceivedEmail']){
                    $_SESSION['Email_EXIST_ERROR'] = "Email Already Exist!";
                    header("Location: ../LoginPage.php");
                    exit();
                }
            }
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Account</title>

    <!-- Font Awesome -->
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        rel="stylesheet"
    />
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap"
        rel="stylesheet"
    />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/8.0.0/mdb.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="../Css/MainDesign.css">

    <style>
        .btn-floating {
            width: 40px;
            height: 40px;
            border-radius: 50%; /* Makes the button circular */
            display: inline-flex;
            justify-content: center;
            align-items: center;
        }
        .maincontent-area{
            text-align: center;
            padding: 50px;
            margin-top: 100px;
        }
        .title {
            font-size: 34px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 50px;
        }

        input[type="text"], input[type="email"], input[type="password"], input[type="number"]{
            background-color: #121212 !important;
            color: white;
        }

        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 1000px #121212 inset !important; /* Set consistent dark background */
            background-color: #121212 !important; /* Ensures dark background */
            -webkit-text-fill-color: #ffffff !important; /* Sets text color to white */
        }
        input:-webkit-autofill {
            outline: none !important;
        }

        input:-webkit-autofill {
            background-color: #121212 !important;
            color: #ffffff !important;
        }

        input {
            background-color: #121212 !important; /* Default background color */
        }

        /* Change the background when focused */
        input:focus {
            background-color: #121212 !important;  /* Change this as needed */
        }

        /* For filled inputs */
        input:not(:placeholder-shown) {
            background-color: #121212 !important;  /* Keep consistent when filled */
        }
    </style>
</head>
<body>
    <!-- Main Content Area -->
    <div class="container maincontent-area">
        <div class="d-flex justify-content-center">
            <p class="title">Register</p>
        </div>

        <form action="../mainFunctions/pageFunctions/registeraccount.php" method="post" id="MainForm" novalidate>
            <div class="row g-3 justify-content-center text-center">
                <!-- First Name input -->  
                <div class="col-md-7 col-lg-3 col-10 position-relative">
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="text" id="FirstnameInput" name="FirstNameInput" class="form-control" autocomplete='new-First-Name-off' style="color: white;" required/>
                        <label class="form-label" for="form2Example2">First Name</label>
                        <div class="invalid-feedback" name="invalid-FirstName-feedback"> </div>
                    </div>
                </div>
                
                <!-- Last Name input -->
                <div class="col-md-7 col-lg-3 col-10 position-relative">  
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="text" id="LastnameInput" name="LastNameInput" class="form-control" autocomplete='new-Last-Name-off' style="color: white;" required />
                        <label class="form-label" for="form2Example3">Last Name</label>
                        <div class="invalid-feedback" name="invalid-LastName-feedback"> </div>
                    </div>
                </div>

                <div class="w-100"></div>

                <!-- Email input -->
                <?php
                    if (isset($_SESSION['ReceivedEmail'])){
                        echo '
                        <div class="col-md-7 col-lg-3 col-10 position-relative">
                            <div data-mdb-input-init class="form-outline mb-4">
                                <input type="email" id="emailInput" name="EmailInput" autocomplete="new-email-address-off" class="form-control" readonly value =\'' . $_SESSION['ReceivedEmail'] . '\' style="color: white;" />
                                <label class="form-label" for="emailInput">Email address (Read Only)</label>
                                <div class="invalid-feedback" name="invalid-Email-feedback"> </div>
                            </div>
                        </div>
                        ';
                    }else{
                        echo '
                        <div class="col-md-7 col-lg-3 col-10 position-relative">
                            <div data-mdb-input-init class="form-outline mb-4">
                                <input type="email" id="emailInput" name="EmailInput" autocomplete="new-email-address-off" class="form-control" style="color: white;" required />
                                <label class="form-label" for="emailInput">Email address</label>
                                <div class="invalid-feedback" name="invalid-Email-feedback"> </div>
                            </div>
                        </div>
                        
                        ';
                    }
                ?>

                <!-- Address input -->
                <div class="col-md-7 col-lg-3 col-10 position-relative">
                    <div data-mdb-input-init class="form-outline mb-4" autocomplete='new-address-Field'>
                        <input type="text" id="AddressInput" name="Location" class="form-control" style="color: white;" required />
                        <label class="form-label" for="form2Example3"> Location </label>
                        <div class="invalid-feedback" name="invalid-Address-feedback"> </div>
                    </div>    
                </div>
                
                <div class="w-100"></div>

                <!-- Phone Number input -->
                <div class="col-md-7 col-lg-3 col-10 position-relative">
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="number" id="PhoneNumberInput" name="PhoneNumber" class="form-control" autocomplete='new-Phone-Number-off' style="color: white;" required />
                        <label class="form-label" for="form2Example3">Phone Number</label>
                        <div class="invalid-feedback" name="invalid-PhoneNumber-feedback"> </div>
                    </div>
                </div>
                
                <!-- Password input -->
                <div class="col-md-7 col-lg-3 col-10 position-relative">
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="password" id="passwordInput" name="userPassword" class="form-control" autocomplete='new-Password-off' style="color: white;" required />
                        <label class="form-label" for="form2Example4">Password</label>
                        <div class="invalid-feedback" name="invalid-Password-feedback"> </div>
                        <div class="valid-feedback" name="valid-Password-feedback"> </div>
                    </div>
                </div>

                <div class="w-100"></div>
                
                <!-- Submit button -->
                <div class="col-md-7 col-lg-2 col-10 position-relative">
                    <button type="submit" id="RegisterSubmit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-block mb-4">Register</button>
                </div>

                <!-- Register buttons -->
                <p>Already have account? <a href="../LoginPage.php"> Sign In</a></p>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/8.0.0/mdb.umd.min.js"></script>

    <script>
        const FirstNameInput = document.getElementById('FirstnameInput');
        const LastNameInput = document.getElementById('LastnameInput');
        const EmailInput = document.getElementById('emailInput');
        const AddressInput = document.getElementById('AddressInput');
        const passwordInput = document.getElementById('passwordInput');
        const PhoneNumberInput = document.getElementById('PhoneNumberInput');

        const RegisterSubmit = document.getElementById('RegisterSubmit');

        const EmailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

        PhoneNumberInput.addEventListener('input', function(){
            const inputValue = this.value.replace(/\D/g, ''); // This keeps only digits
            if (inputValue.length > 11) {
                this.value = inputValue.slice(0, 11); // Limit to 11 digits
            } else {
                this.value = inputValue; // Allow valid input
            }
        })

        RegisterSubmit.addEventListener('click', Register);

        document.querySelectorAll('.form-outline').forEach((formOutline) => {
            new mdb.Input(formOutline).init();
        });
        document.querySelectorAll('.form-outline').forEach((formOutline) => {
            new mdb.Input(formOutline).update();
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

        function checkPasswordStrength(password){
            const regex = {
                // Strong: At least one lowercase, one uppercase, one digit, one special character, minimum length of 8
                strong: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W)[A-Za-z\d\W]{14,}$/,
                // Medium: At least one lowercase, one uppercase, one digit, minimum length of 6
                medium: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/
            };

            if (password == "" || password == null){
                return 'Empty';
            }

            if (regex.strong.test(password)){
                return 'Strong';
            }else if (regex.medium.test(password)){
                return 'Medium';
            }else{
                return 'Weak';
            }
        }

        // Select the input field
        const inputField = document.getElementById("LastnameInput");

        let isAutofillTriggered = false;

        // Event listener for focus
        inputField.addEventListener("focus", () => {
            // Reset autofill flag on focus
            isAutofillTriggered = false;
        });

        inputField.addEventListener("blur", () => {
            // Reset autofill flag on focus
            isAutofillTriggered = false;
        });

        // Event listener for input
        inputField.addEventListener("blur", () => {
            if (!isAutofillTriggered) {
                // Mark autofill as triggered
                isAutofillTriggered = true;
                console.log("Autofill data was applied!");
                // You can apply any specific styles or logic here
            document.querySelectorAll('.form-outline').forEach((formOutline) => {
            console.log("Before init:", formOutline);
                new mdb.Input(formOutline).init();
            console.log("After init:", formOutline);
        });
            }
        });

        // Validation Variables
        var ValidPassword = true;
        var ValidFirstName = true;
        var ValidLastName = true;
        var ValidPhoneNumber = true;
        var ValidAddress = true;
        var ValidEmail = true;

        // Password Input Detect
        const passwordInputDetect = debounceTime(function(event){
            if (checkPasswordStrength(this.value) == 'Weak'){
                passwordInput.classList.add('is-invalid'); 
                passwordInput.classList.remove('is-valid'); 

                document.getElementsByName("invalid-Password-feedback")[0].textContent = "Weak";
                ValidPassword = false;
            }else if (checkPasswordStrength(this.value) == 'Medium'){
                passwordInput.classList.remove('is-invalid'); 
                passwordInput.classList.add('is-valid'); 

                document.getElementsByName("valid-Password-feedback")[0].textContent = "Medium";
                ValidPassword = true;
            }else if (checkPasswordStrength(this.value) == 'Strong'){
                passwordInput.classList.remove('is-invalid'); 
                passwordInput.classList.add('is-valid'); 

                document.getElementsByName("valid-Password-feedback")[0].textContent = "Strong";
                ValidPassword = true;
            }else{
                passwordInput.classList.add('is-invalid'); 
                passwordInput.classList.remove('is-valid');

                document.getElementsByName("invalid-Password-feedback")[0].textContent = "Input field is Empty!";
                ValidPassword = false;
            }
        }, 100) 
        // FirstName Input Detect
        const FirstNameInputDetect = debounceTime(function(event){
            if (this.value === ""){
                ValidFirstName = false;

                this.classList.add('is-invalid'); 
                this.classList.remove('is-valid'); 

                document.getElementsByName("invalid-FirstName-feedback")[0].textContent = "Input field is Empty!";
            }else{
                ValidFirstName = true;
                this.classList.add('is-valid'); 
                this.classList.remove('is-invalid'); 
            }
        }, 200)
        //Last Name Input Detect
        const LastNameInputDetect = debounceTime(function(event){
            // Last Name Validation
            if (this.value === ""){
                ValidLastName = false;

                this.classList.add('is-invalid'); 
                this.classList.remove('is-valid'); 

                document.getElementsByName("invalid-LastName-feedback")[0].textContent = "Input field is Empty!";
            }else{
                ValidLastName = true;
                this.classList.add('is-valid'); 
                this.classList.remove('is-invalid'); 
            }
        }, 200)
        // Email Input Detect
        const EmailInputDetect = debounceTime(function(event){
            // Email Validation
            if (this.value == "" || this.value == null){
                ValidEmail = false;
                event.stopPropagation();

                this.classList.add('is-invalid'); 
                this.classList.remove('is-valid'); 

                document.getElementsByName("invalid-Email-feedback")[0].textContent = "Input field is Empty!";
            }else{
                if (!EmailPattern.test(this.value)){
                    ValidEmail = false;
                    event.stopPropagation();

                    this.classList.add('is-invalid'); 
                    this.classList.remove('is-valid'); 

                    document.getElementsByName("invalid-Email-feedback")[0].textContent = "Invalid Email!";
                }else{
                    ValidEmail = true;
                    this.classList.add('is-valid'); 
                    this.classList.remove('is-invalid'); 
                }
            }
        }, 200)
        // Address Input Detect
        const AddressInputDetect = debounceTime(function(event){
            // Address Validation
            if (this.value === ""){
                ValidAddress = false;

                this.classList.add('is-invalid'); 
                this.classList.remove('is-valid'); 

                document.getElementsByName("invalid-Address-feedback")[0].textContent = "Input field is Empty!";
            }else{
                ValidAddress = true;
                this.classList.add('is-valid'); 
                this.classList.remove('is-invalid'); 
            }

        }, 200)
        // Phone Number Input Detect
        const PhoneNumberInputDetect = debounceTime(function(event){
            // Phone Number Validation
            if (this.value === 0 || this.value === null || this.value === ""){
                ValidPhoneNumber = false;
                event.stopPropagation();

                this.classList.add('is-invalid'); 
                this.classList.remove('is-valid'); 

                document.getElementsByName("invalid-PhoneNumber-feedback")[0].textContent = "Input field is Empty!";
            }else if(this.value.length < 11){
                ValidPhoneNumber = false;
                event.stopPropagation();

                this.classList.add('is-invalid'); 
                this.classList.remove('is-valid'); 

                document.getElementsByName("invalid-PhoneNumber-feedback")[0].textContent =  "Phone number must be 11 numbers";
            }else if (this.value.length > 11){
                ValidPhoneNumber = false;
                event.stopPropagation();

                this.classList.add('is-invalid'); 
                this.classList.remove('is-valid'); 

                document.getElementsByName("invalid-PhoneNumber-feedback")[0].textContent = "Phone number must be 11 numbers";
            }else if (this.value.charAt(0) != 0 || this.value.charAt(1) != 9){
                ValidPhoneNumber = false;
                event.stopPropagation();

                this.classList.add('is-invalid'); 
                this.classList.remove('is-valid'); 

                document.getElementsByName("invalid-PhoneNumber-feedback")[0].textContent = "Phone Number must start with '09'";
            }else{
                ValidPhoneNumber = true;
                this.classList.add('is-valid'); 
                this.classList.remove('is-invalid'); 
            }
        }, 200)
        

        // Listener
        passwordInput.addEventListener('input', passwordInputDetect);
        FirstNameInput.addEventListener('input', FirstNameInputDetect);
        LastNameInput.addEventListener('input', LastNameInputDetect);
        EmailInput.addEventListener('input', EmailInputDetect);
        AddressInput.addEventListener('input', AddressInputDetect);
        PhoneNumberInput.addEventListener('input', PhoneNumberInputDetect);

        document.addEventListener("DOMContentLoaded", function () {
            const inputs = document.querySelectorAll("input");

            inputs.forEach(input => {
                input.addEventListener("animationstart", (e) => {
                    if (e.animationName === "onAutoFillStart") {
                        input.classList.add("filled"); // Custom class for styling consistency
                    }
                });

                input.addEventListener("input", () => {
                    if (input.value) {
                        input.classList.add("filled");
                    } else {
                        input.classList.remove("filled");
                    }
                });
            });
        });

        // Submit Handler
        function Register(event){
            event.preventDefault();

            // First Name Validation
            if (FirstNameInput.value === ""){
                ValidFirstName = false;
                event.stopPropagation();

                FirstNameInput.classList.add('is-invalid'); 
                FirstNameInput.classList.remove('is-valid'); 

                document.getElementsByName("invalid-FirstName-feedback")[0].textContent = "Input field is Empty!";
            }else{
                FirstNameInput.classList.add('is-valid'); 
                FirstNameInput.classList.remove('is-invalid'); 
            }

            // Last Name Validation
            if (LastNameInput.value === ""){
                ValidLastName = false;
                event.stopPropagation();

                LastNameInput.classList.add('is-invalid'); 
                LastNameInput.classList.remove('is-valid'); 

                document.getElementsByName("invalid-LastName-feedback")[0].textContent = "Input field is Empty!";
            }else{
                LastNameInput.classList.add('is-valid'); 
                LastNameInput.classList.remove('is-invalid'); 
            }

            // Address Validation
            if (AddressInput.value === ""){
                ValidAddress = false;
                event.stopPropagation();

                AddressInput.classList.add('is-invalid'); 
                AddressInput.classList.remove('is-valid'); 

                document.getElementsByName("invalid-Address-feedback")[0].textContent = "Input field is Empty!";
            }else{
                AddressInput.classList.add('is-valid'); 
                AddressInput.classList.remove('is-invalid'); 
            }

            // Password Validation
            if (checkPasswordStrength(passwordInput.value) == 'Weak'){
                passwordInput.classList.add('is-invalid'); 
                passwordInput.classList.remove('is-valid'); 

                document.getElementsByName("invalid-Password-feedback")[0].textContent = "Weak";
                ValidPassword = false;
            }else if (checkPasswordStrength(passwordInput.value) == 'Medium'){
                passwordInput.classList.remove('is-invalid'); 
                passwordInput.classList.add('is-valid'); 

                document.getElementsByName("valid-Password-feedback")[0].textContent = "Medium";
                ValidPassword = true;
            }else if (checkPasswordStrength(passwordInput.value) == 'Strong'){
                passwordInput.classList.remove('is-invalid'); 
                passwordInput.classList.add('is-valid'); 

                document.getElementsByName("valid-Password-feedback")[0].textContent = "Strong";
                ValidPassword = true;
            }else{
                passwordInput.classList.add('is-invalid'); 
                passwordInput.classList.remove('is-valid');

                document.getElementsByName("invalid-Password-feedback")[0].textContent = "Input field is Empty!";
                ValidPassword = false;
            }

            // Email Validation
            if (EmailInput.value == "" || EmailInput.value == null){
                ValidEmail = false;
                event.stopPropagation();

                EmailInput.classList.add('is-invalid'); 
                EmailInput.classList.remove('is-valid'); 

                document.getElementsByName("invalid-Email-feedback")[0].textContent = "Input field is Empty!";
            }else{
                if (!EmailPattern.test(EmailInput.value)){
                    ValidEmail = false;
                    event.stopPropagation();

                    EmailInput.classList.add('is-invalid'); 
                    EmailInput.classList.remove('is-valid'); 

                    document.getElementsByName("invalid-Email-feedback")[0].textContent = "Invalid Email!";
                }else{
                    EmailInput.classList.add('is-valid'); 
                    EmailInput.classList.remove('is-invalid'); 
                }
            }

            // Phone Number Validation
            if (PhoneNumberInput.value === 0 || PhoneNumberInput.value === null || PhoneNumberInput.value === ""){
                ValidPhoneNumber = false;
                event.stopPropagation();

                PhoneNumberInput.classList.add('is-invalid'); 
                PhoneNumberInput.classList.remove('is-valid'); 

                document.getElementsByName("invalid-PhoneNumber-feedback")[0].textContent = "Input field is Empty!";
            }else if(PhoneNumberInput.value.length < 11){
                ValidPhoneNumber = false;
                event.stopPropagation();

                PhoneNumberInput.classList.add('is-invalid'); 
                PhoneNumberInput.classList.remove('is-valid'); 

                document.getElementsByName("invalid-PhoneNumber-feedback")[0].textContent =  "Phone number must be 11 numbers";
            }else if (PhoneNumberInput.value.length > 11){
                ValidPhoneNumber = false;
                event.stopPropagation();

                PhoneNumberInput.classList.add('is-invalid'); 
                PhoneNumberInput.classList.remove('is-valid'); 

                document.getElementsByName("invalid-PhoneNumber-feedback")[0].textContent = "Phone number must be 11 numbers";
            }else if (PhoneNumberInput.value.charAt(0) != 0 || PhoneNumberInput.value.charAt(1) != 9){
                ValidPhoneNumber = false;
                event.stopPropagation();

                PhoneNumberInput.classList.add('is-invalid'); 
                PhoneNumberInput.classList.remove('is-valid'); 

                document.getElementsByName("invalid-PhoneNumber-feedback")[0].textContent = "Phone Number must start with '09'";
            }else{
                PhoneNumberInput.classList.add('is-valid'); 
                PhoneNumberInput.classList.remove('is-invalid'); 
            }
            
            if (ValidFirstName != false && ValidLastName != false && ValidPhoneNumber != false && ValidAddress != false && ValidEmail != false && ValidPassword != false){
                document.getElementById('MainForm').submit();
            }else{
                console.log("SOMETHING WRONG");
            }
        }
  
    </script>

</body>
</html>