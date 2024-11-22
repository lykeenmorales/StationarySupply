<?php
    session_start();

    if (isset($_SESSION['Login_UserID'])){
        if (isset($_SESSION['Login_UserType'])){
            if ($_SESSION['Login_UserType'] == "Admin"){
                header("Location: homepage.php");
            }else{
                header("Location: clientPages/clientHomePage.php");
            }
        }
    }

    include './mainFunctions/connection.php';

    unset($_SESSION['ReceivedEmail']);
    unset($_SESSION['Email']);
    unset($_SESSION['IsRememberEnabled']);
    if (isset($_SESSION['ReceivedEmail'])){
        unset($_SESSION['ReceivedEmail']);
    }

    $RememberedEmailInput = null;

    if (isset($_SESSION['RememberedEmail'])){
        if ($_SESSION['RememberedEmail'] != "" or $_SESSION['RememberedEmail'] != null){
            $RememberedEmailInput = $_SESSION['RememberedEmail'];
            // Uncomment out if want to reset when user refreshes {Means if they logout can only be remembered one time when refresh it reset}
            //unset($_SESSION['RememberedEmail']);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Account</title>

    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/8.0.0/mdb.min.css"
        rel="stylesheet"
    />
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

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

    <!-- Google Identity Service Library-->
    <script src="https://accounts.google.com/gsi/client" async defer></script>

    <link rel="stylesheet" href="Css/MainDesign.css">

    <style>
        .login-content {
            margin-top: 250px;
            margin-left: 740px;
            margin-right: 740px;
            transition: margin-left 0.3s ease; /* Smooth transition for content */
        }
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
            color: white !important;
        }

        /* Change the background when focused */
        input:focus {
            background-color: #121212 !important;  /* Change this as needed */
            color: white !important;
        }

        /* For filled inputs */
        input:not(:placeholder-shown) {
            background-color: #121212 !important;  /* Keep consistent when filled */
        }

        .modal-content-custom {
            background-color: #121212 !important; /* Pure black background */
            color: #ffffff !important; /* White text */
        }

        .btn-custom-hover:hover{
            background-color: rgba(255, 255, 255, 0.2); 
            transition: background-color 0.2s ease;
        }
    </style>
</head>
<body>
    <!-- Main Content Area -->
    <div class="container maincontent-area">
        <div class="d-flex justify-content-center">
            <p class="title">Stationary Supplies</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4 col-10 text-center">
                <form action="./mainFunctions/pageFunctions/loginaccount.php" method="post" id="MainForm">
                    <!-- Email input -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="email" id="EmailInput" name="EmailInput" class="form-control" autocomplete="new-email-input" value='<?php echo $RememberedEmailInput; ?>' required />
                        <label class="form-label" for="form2Example1">Email address</label>
                        <div class="invalid-feedback" name="invalid-Email-feedback"> </div>
                    </div>

                    <!-- Password input -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="password" id="PasswordInput" name="PasswordInput" class="form-control" required />
                        <label class="form-label" for="form2Example2">Password</label>
                        <div class="invalid-feedback" name="invalid-Password-feedback"> </div>
                    </div>

                    <!-- Checkbox and link -->
                    <div class="row mb-4">
                        <div class="col d-flex align-items-center justify-content-start">
                            <input class="form-check-input me-2" type="checkbox" value="" id="RememberMeButton" />
                            <label class="form-check-label" for="form2Example31"> Remember me </label>
                        </div>
                        <div class="col text-end">
                            <a href="clientPages/changePassPage.php">Forgot password?</a>
                        </div>
                    </div>

                    <!-- Submit button -->
                    <button type="Submit" id="SubmitButton" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-block mb-4">Sign in</button>

                    <!-- Register buttons -->
                    <p>Don't have an account?
                    <a href="clientPages/RegisterPage.php">Register</a></p>
                    <p>or sign up with:</p>

                    <div class="d-flex justify-content-center">
                        <button type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-custom-hover btn-link btn-floating mx-2">
                            <i class="fab fa-facebook-f"></i>
                        </button>
                        <button type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-custom-hover btn-link btn-floating mx-1">
                            <i class="fab fa-twitter"></i>
                        </button>
                        <button id="google-signin-btn" type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-custom-hover btn-link btn-floating mx-2">
                            <i class="fab fa-google"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Notify Modal -->
    <div class="modal fade" id="NotifyModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-custom">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Error: Trying to log in</h5>
                    <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script
        type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/8.0.0/mdb.umd.min.js"
    ></script>

    <script>
        const PasswordInput = document.getElementById('PasswordInput');
        const EmailInput = document.getElementById('EmailInput');
        const SubmitButton = document.getElementById('SubmitButton');
        const MainForm = document.getElementById('MainForm');
        const RememberMeBtn = document.getElementById('RememberMeButton');

        const EmailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

        var Email_EXIST_ERROR = '<?php
            if (isset($_SESSION['Email_EXIST_ERROR'])){
                echo $_SESSION['Email_EXIST_ERROR'];
                unset($_SESSION['Email_EXIST_ERROR']);
                unset($_SESSION['ReceivedEmail']);
            }
        ?>'

        var LoginError = '<?php
            if (isset($_SESSION['LoginError'])){
                echo $_SESSION['LoginError'];
                unset($_SESSION['LoginError']);
                unset($_SESSION['ReceivedEmail']);
            }
        ?>'

        var CustomNotifyMsgHEADER = '<?php
            if (isset($_SESSION['CustomNotifyMsgHEADER'])){
                echo $_SESSION['CustomNotifyMsgHEADER'];
                unset($_SESSION['CustomNotifyMsgHEADER']);
                unset($_SESSION['ReceivedEmail']);
            }
        ?>'

        var CustomNotifyMsg = '<?php
            if (isset($_SESSION['CustomNotifyMsg'])){
                echo $_SESSION['CustomNotifyMsg'];
                unset($_SESSION['CustomNotifyMsg']);
                unset($_SESSION['ReceivedEmail']);
            }
        ?>'

        if (Email_EXIST_ERROR != ""){
            var NotifyModal = new mdb.Modal(document.getElementById('NotifyModal'));

            document.getElementsByClassName('modal-body')[0].textContent = Email_EXIST_ERROR + " Try logging in with that email.";

            NotifyModal.show();
        }

        if (CustomNotifyMsg != ""){
            var NotifyModal = new mdb.Modal(document.getElementById('NotifyModal'));

            document.getElementById('ModalLabel').innerHTML = CustomNotifyMsgHEADER;
            document.getElementsByClassName('modal-body')[0].textContent = CustomNotifyMsg;

            NotifyModal.show();
        }

        if (LoginError != ""){
            document.getElementsByName("invalid-Email-feedback")[0].textContent = LoginError;

            PasswordInput.classList.add('is-invalid'); 
            PasswordInput.classList.remove('is-valid'); 
            EmailInput.classList.add('is-invalid'); 
            EmailInput.classList.remove('is-valid'); 
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.form-outline').forEach((formOutline) => {
            new mdb.Input(formOutline).init();
            });
            document.querySelectorAll('.form-outline').forEach((formOutline) => {
                new mdb.Input(formOutline).update();
            });
        });

        RememberMeBtn.addEventListener('click', function(){
            $.ajax({
                url: './mainFunctions/pageFunctions/Rememberme.php',
                type: 'POST',
                data: { IsEnabled: RememberMeBtn.checked },
                success: function(response) {
                    console.log('Response from server:', response);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                }
            });
        })

        function handleCredentialResponse(response) {
            const id_token = response.credential;
            console.log("ID Token: " + id_token);
        }

        function initializeGoogleSignIn() {
            google.accounts.id.initialize({
                client_id: '335141065600-1fd37eljhpn5hba011einlgukj4fb8hk.apps.googleusercontent.com',
                callback: handleCredentialResponse,
                ux_mode: 'popup',
                context: 'signin'
            });

             document.getElementById("google-signin-btn").onclick = function() {
                google.accounts.id.prompt(); 
            };
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

        // Function to check URL for id_token after redirect
        function checkForToken() {
            const hash = window.location.hash;
            if (hash) {
                const params = new URLSearchParams(hash.substring(1)); // Remove the '#' and parse params
                const idToken = params.get('id_token');

                if (idToken) {
                    $.ajax({
                        url: './clientPages/RegisterPage.php',
                        type: 'POST',
                        data: { id_token: idToken },
                        success: function(response) {
                            window.location.href = './clientPages/RegisterPage.php';
                            console.log('Response from server:', response);
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', error);
                        }
                    });
                } else {
                    console.log("No ID token found in the URL.");
                }
            }else{
                document.getElementById('google-signin-btn').onclick = function() {
                    function generateNonce() {
                        return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
                    }

                    const nonce = generateNonce();
                    const clientId = '335141065600-1fd37eljhpn5hba011einlgukj4fb8hk.apps.googleusercontent.com'; // Client ID
                    // Change the redirectUri Based on your {LoginPage.php} Web Link (Get the link when you open the {LoginPage.php} Page and replace here)
                    const redirectUri = 'https://fluffy-yodel-97qv5qgrwxv5hr6v-8000.app.github.dev/htdocs/PT5_Main/LoginPage.php';
                    const scope = 'openid email profile';
                    const responseType = 'id_token';
                    const prompt = 'select_account';

                    const authUrl = `https://accounts.google.com/o/oauth2/v2/auth?client_id=${clientId}&scope=${scope}&response_type=${responseType}&redirect_uri=${encodeURIComponent(redirectUri)}&prompt=${prompt}&nonce=${nonce}`;

                    window.location.href = authUrl;
                };
            }
        }

        // Call this function on page load
        checkForToken();

        // Validation Variables
        var ValidPassword = true;
        var ValidEmail = true;

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
                ValidEmail = true;
                this.classList.add('is-valid'); 
                this.classList.remove('is-invalid'); 
            }
        }, 250)
        // Password Input Detect
        const PasswordInputDetect = debounceTime(function(event){
            // Password Validation
            if (this.value == "" || this.value == null){
                ValidPassword = false;
                event.stopPropagation();

                this.classList.add('is-invalid'); 
                this.classList.remove('is-valid'); 

                document.getElementsByName("invalid-Password-feedback")[0].textContent = "Input field is Empty!";
            }else{
                ValidPassword = true;
                this.classList.add('is-valid'); 
                this.classList.remove('is-invalid'); 
            }
        }, 250)


        EmailInput.addEventListener('input', EmailInputDetect);
        PasswordInput.addEventListener('input', PasswordInputDetect);

        SubmitButton.addEventListener('click', function(event){
            event.preventDefault();

            // Email Validation
            if (EmailInput.value == "" || EmailInput.value == null){
                ValidEmail = false;
                event.stopPropagation();

                EmailInput.classList.add('is-invalid'); 
                EmailInput.classList.remove('is-valid'); 

                document.getElementsByName("invalid-Email-feedback")[0].textContent = "Input field is Empty!";
            }else{
                ValidEmail = true;
                EmailInput.classList.add('is-valid'); 
                EmailInput.classList.remove('is-invalid'); 
            }

            // Password Validation 
            if (PasswordInput.value == "" || PasswordInput.value == null){
                ValidPassword = false;
                event.stopPropagation();

                PasswordInput.classList.add('is-invalid'); 
                PasswordInput.classList.remove('is-valid'); 

                document.getElementsByName("invalid-Password-feedback")[0].textContent = "Input field is Empty!";
            }else{
                ValidPassword = true;
                PasswordInput.classList.add('is-valid'); 
                PasswordInput.classList.remove('is-invalid'); 
            }


            if (ValidPassword != false && ValidEmail != false){
                document.getElementById('MainForm').submit();
            }
        })
        
        
    </script>
    
</body>
</html>