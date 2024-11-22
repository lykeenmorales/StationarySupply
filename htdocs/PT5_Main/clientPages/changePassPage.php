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

    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Pass Account</title>

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
            margin-top: 150px;
        }
        .title {
            font-size: 32px;
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
            <p class="title">Change Password</p>
        </div>

        <form action="../mainFunctions/pageFunctions/ChangePass.php" method="post" id="MainForm" novalidate>
            <div class="row g-3 justify-content-center text-center">
                <!-- Email input -->  
                <?php
                    if (isset($_SESSION['Email']) == null || $_SESSION['Email'] == ""){
                        echo '<div class="col-md-7 col-lg-3 col-10 position-relative">
                                <div data-mdb-input-init class="form-outline mb-3">
                                    <input type="text" id="EmailInput" name="EmailInput" class="form-control" autocomplete="new-Password-off" style="color: white;" required/>
                                    <label class="form-label" for="form2Example2">Email</label>
                                    <div class="invalid-feedback" name="invalid-Email-feedback"> </div>
                                </div>
                            </div>
                            
                            <div class="w-100"></div>

                            ';
                    }
                ?>

                <!-- Password input -->  
                <div class="col-md-7 col-lg-3 col-10 position-relative">
                    <div data-mdb-input-init class="form-outline mb-3">
                        <input type="password" id="PasswordInput" name="NewPassword" class="form-control" autocomplete='new-Password-off' style="color: white;" required/>
                        <label class="form-label" for="form2Example2">New Password</label>
                        <div class="invalid-feedback" name="invalid-Password-feedback"> </div>
                        <div class="valid-feedback" name="valid-Password-feedback"> </div>
                    </div>
                </div>
                <div class="w-100"></div>
                <!-- Re-Check Password input -->  
                <div class="col-md-7 col-lg-3 col-10 position-relative">
                    <div data-mdb-input-init class="form-outline mb-3">
                        <input type="password" id="ReCheckPasswordInput" name="ReCheckPassword" class="form-control" autocomplete='new-Password-off' style="color: white;" required/>
                        <label class="form-label" for="form2Example2">Confirm Password</label>
                        <div class="invalid-feedback" name="invalid-ReCheckPassword-feedback"> </div>
                        <div class="valid-feedback" name="valid-ReCheckPassword-feedback"> </div>
                    </div>
                </div>

                <div class="w-100"></div>
                
                <!-- Submit button -->
                <div class="col-md-7 col-lg-2 col-10 position-relative">
                    <button type="submit" id="SubmitButton" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-block mb-4">Submit</button>
                </div>

                <!-- Register buttons -->
                <p>Already have account? <a href="../LoginPage.php"> Sign In</a></p>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/8.0.0/mdb.umd.min.js"></script>

    <script>
        const SubmitButton = document.getElementById('SubmitButton');
        const NewPasswordInput = document.getElementById('PasswordInput');
        const ReCheckPassInput = document.getElementById('ReCheckPasswordInput');

        var EmailInput;
        if (document.getElementById('EmailInput')){
            EmailInput = document.getElementById('EmailInput');
        }else{
            EmailInput = null;
        }
        const MainForm = document.getElementById('MainForm')

        var DefaultEmailInput = '<?php 
            if (isset($_SESSION['Email'])){
                echo $_SESSION['Email'];
            }else{
                echo "";
            }
        ?>';

        var FinalEmaiLValue;

        if (EmailInput != null){
            EmailInput.addEventListener('input', debounceTime(function(){
                if (EmailInput.value != ""){
                    FinalEmaiLValue = EmailInput.value;
                }
            }, 50));
        }else{
            FinalEmaiLValue = DefaultEmailInput;
        }
        
        // Validation Variables
        var ValidPassword = true;
        var ValidEmail = true;
        var RecheckPass = true;

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

        ReCheckPassInput.addEventListener('input', debounceTime(function(){
            if (ReCheckPassInput.value != ""){
                if (ReCheckPassInput.value != NewPasswordInput.value){
                    RecheckPass = false;
                    ReCheckPassInput.classList.add('is-invalid');
                    ReCheckPassInput.classList.remove('is-valid');

                    document.getElementsByName("invalid-ReCheckPassword-feedback")[0].textContent = "Password does not match!";
                }else{
                    RecheckPass = true;
                    ReCheckPassInput.classList.add('is-valid');
                    ReCheckPassInput.classList.remove('is-invalid');
                }
            }
        }, 125));
        NewPasswordInput.addEventListener('input', debounceTime(function(){
            if (ReCheckPassInput.value != ""){
                if (ReCheckPassInput.value != NewPasswordInput.value){
                    RecheckPass = false;
                    ReCheckPassInput.classList.add('is-invalid');
                    ReCheckPassInput.classList.remove('is-valid');

                    document.getElementsByName("invalid-ReCheckPassword-feedback")[0].textContent = "Password does not match!";
                }else{
                    RecheckPass = true;
                    ReCheckPassInput.classList.add('is-valid');
                    ReCheckPassInput.classList.remove('is-invalid');
                }
            }
        }, 125));

        SubmitButton.addEventListener('click', (event) => {
            event.preventDefault();

            // Password Validation
            if (NewPasswordInput.value == null || NewPasswordInput.value == ""){
                ValidPassword = false;
                NewPasswordInput.classList.add('is-invalid');
                NewPasswordInput.classList.remove('is-valid');

                document.getElementsByName("invalid-Password-feedback")[0].textContent = "Input field is Empty!";
            }else{
                $.ajax({
                    url: '../mainFunctions/pageFunctions/CheckPass.php',
                    type: 'POST',
                    data: {EmailReceived:FinalEmaiLValue, PasswordReceived: NewPasswordInput.value },
                    success: function(response) {
                        if (response == ""){
                            if (checkPasswordStrength(NewPasswordInput.value) == 'Weak'){
                                NewPasswordInput.classList.add('is-invalid'); 
                                NewPasswordInput.classList.remove('is-valid'); 

                                document.getElementsByName("invalid-Password-feedback")[0].textContent = "Weak";
                                ValidPassword = false;
                            }else if (checkPasswordStrength(NewPasswordInput.value) == 'Medium'){
                                NewPasswordInput.classList.remove('is-invalid'); 
                                NewPasswordInput.classList.add('is-valid'); 

                                document.getElementsByName("valid-Password-feedback")[0].textContent = "Medium";
                                ValidPassword = true;
                            }else if (checkPasswordStrength(NewPasswordInput.value) == 'Strong'){
                                NewPasswordInput.classList.remove('is-invalid'); 
                                NewPasswordInput.classList.add('is-valid'); 

                                document.getElementsByName("valid-Password-feedback")[0].textContent = "Strong";
                                ValidPassword = true;
                            }
                        }else if (response != ""){
                            var ValidPassword = false;
                            NewPasswordInput.classList.add('is-invalid');
                            NewPasswordInput.classList.remove('is-valid');

                            document.getElementsByName("invalid-Password-feedback")[0].textContent = response;
                        }

                        console.log('Response from server:', response);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                    }
                });
            }

            // Email Validation
            if (FinalEmaiLValue != null){
                if (FinalEmaiLValue != null || FinalEmaiLValue != ""){
                    if (FinalEmaiLValue == null || FinalEmaiLValue == ""){
                        ValidEmail = false;
                        EmailInput.classList.add('is-invalid');
                        EmailInput.classList.remove('is-valid');

                        document.getElementsByName("invalid-Email-feedback")[0].textContent = "Input field is Empty!";
                    }else{
                        $.ajax({
                            url: '../mainFunctions/pageFunctions/CheckEmail.php',
                            type: 'POST',
                            data: { EmailReceived: FinalEmaiLValue },
                            success: function(response) {
                                if (response == "true"){
                                    ValidEmail = true;
                                    EmailInput.classList.add('is-valid');
                                    EmailInput.classList.remove('is-invalid');
                                }else if (response == "false"){
                                    ValidEmail = false;
                                    EmailInput.classList.add('is-invalid');
                                    EmailInput.classList.remove('is-valid');

                                    document.getElementsByName("invalid-Email-feedback")[0].textContent = "Email is Invalid!";
                                }

                                console.log('Response from server:', response);
                            },
                            error: function(xhr, status, error) {
                                console.error('AJAX Error:', error);
                            }
                        });
                    }
                }
            }

            // Recheck Password Validation
            if (ReCheckPassInput.value == ""){
                RecheckPass = false;
                ReCheckPassInput.classList.add('is-invalid');
                ReCheckPassInput.classList.remove('is-valid');

                document.getElementsByName("invalid-ReCheckPassword-feedback")[0].textContent = "Empty Field";
            }else{
                if (ReCheckPassInput.value != NewPasswordInput.value){
                    RecheckPass = false;
                    ReCheckPassInput.classList.add('is-invalid');
                    ReCheckPassInput.classList.remove('is-valid');

                    document.getElementsByName("invalid-ReCheckPassword-feedback")[0].textContent = "Password does not match!";
                }else{
                    RecheckPass = true;
                    ReCheckPassInput.classList.add('is-valid');
                    ReCheckPassInput.classList.remove('is-invalid');
                }
            }

            if (ValidEmail == true && ValidPassword == true && RecheckPass == true){
                document.getElementById('MainForm').submit();
            }
        });

    </script>
</body>
</html>