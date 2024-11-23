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
    unset($_SESSION['Current_ProductFilter']);
    unset($_SESSION['p_productStocksQuantity']);

    $UserID = $_SESSION['Login_UserID'];

    $GetUserProfile = "SELECT * FROM customeraccount WHERE CustomerID = '$UserID'";
    $Query = $connection -> query($GetUserProfile);

    $AccountResult = $Query -> fetch_assoc();
    $FinalizedPngPath = "";

    if ($AccountResult['profile_picture_path'] != null){
        $AccountPngPathFile = htmlspecialchars($AccountResult['profile_picture_path']);
        $FinalizedPngPath = "../profilePicUploads/" . basename($AccountPngPathFile);
    }

    function ConvertNumber(){
        global $AccountResult;
        
        $PhoneNumber = htmlspecialchars($AccountResult['Phone']);
        $PhoneNumberStringEdit = null;

        if (str_replace('+63', '09', $PhoneNumber)){
            $PhoneNumberStringEdit = str_replace('+63', '0', $PhoneNumber);
        }

        $PhoneNumberStringEdit2 = str_replace(' ', '', $PhoneNumberStringEdit);
        
        return $PhoneNumberStringEdit2;
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Information - Stationary Supplies</title>
    <!-- MDBootstrap and Bootstrap CSS -->
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="client_css/c_account.css">
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
                    <li class="nav-item"><a class="nav-link" href="c_ProductPage.php"><i class="fas fa-box"></i> Products</a></li>
                    <?php
                        if ($_SESSION['Login_UserID']){
                            if ($_SESSION['Login_UserType'] == "Admin"){
                                echo '<li class="nav-item"><a class="nav-link" href="../homepage.php"><i class="fas fa-user-shield"></i> Admin View</a></li>';
                            }else{
                                echo '<li class="nav-item"><a class="nav-link" href="c_CartListPage.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>';
                                echo '<li class="nav-item"><a class="nav-link nav-link-FOCUS" href="c_Account.php"><i class="fas fa-user"></i> Account</a></li>';
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
    <section class="page-header">
        <h1>Account Information</h1>
    </section>

    <!-- Main Content -->
    <div class="container my-5">
        <!-- Personal Information -->
        <div class="account-section">
            <form action="../mainFunctions/pageFunctions/UpdateData.php" method="post" id="AccountInformationForm" enctype="multipart/form-data">
                <h5>Personal Information</h5>
                <div class="profile-info-container">
                    <!-- Profile Picture Container -->
                    <div class="row">
                        <div class="col-lg-5 mb-4 text-center">
                            <div class="profile-picture-container">
                                <img src="<?php echo $FinalizedPngPath; ?>" onerror="this.src='https://www.w3schools.com/howto/img_avatar.png'" class="profile-picture">
                            </div>
                            <!-- File input styled as a button -->
                            <input type="file" id="fileInput" name="profile-photo" style="display: none;" accept=".png, .jpg, .jpeg">
                            <label for="fileInput" class="btn btn-custom btn-custom-profile mt-3">Change Photo</label>
                            <!-- Placeholder for the file name -->
                            <div id="fileName" class="mt-3"></div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <div class="mb-5">
                            <label for="firstname" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstName" name="FirstName" placeholder=" " value="<?php echo htmlspecialchars($AccountResult['first_name']); ?>" required>
                            <div class="invalid-feedback" name = "FirstNameInputFeedback"></div>
                        </div>
                        <div class="mb-5">
                            <label for="lastname" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastName" name="LastName" placeholder=" " value="<?php echo htmlspecialchars($AccountResult['last_name']); ?>" required>
                            <div class="invalid-feedback" name = "LastNameInputFeedback"></div>
                        </div>
                        <div class="mb-5">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="Email" placeholder=" " value="<?php echo htmlspecialchars($AccountResult['Email']); ?>" required>
                            <div class="invalid-feedback" name = "EmailInputFeedback"></div>
                        </div>
                        <div class="mb-5">
                            <label for="AddressInput" class="form-label">Address</label>
                            <input type="text" class="form-control" id="AddressInput" name="Address" placeholder=" " value="<?php echo htmlspecialchars($AccountResult['Address']); ?>" required>
                            <div class="invalid-feedback" name = "AddressInputFeedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="PhoneNumber" placeholder=" " value="<?php echo ConvertNumber(); ?>" required>
                            <div class="invalid-feedback" name = "PhoneInputFeedback"></div>
                        </div>

                        <input type="hidden" name="client_ActivePage" value="true">
                        <button type="submit" id="submitButton_UpdateInformation" class="btn btn-custom mt-2">Update Information</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Order History -->
        <div class="account-section">
            <h5>Order History</h5>
            <table class="table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Products</th>
                        <th>Status</th>
                        <th>Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $GetOrderHistoryQuery = " SELECT 
                            o.OrderID, 
                            o.OrderDate,
                            o.OrderStatus,
                            od.Quant,
                            GROUP_CONCAT(p.Name SEPARATOR ', ') AS Products,
                            GROUP_CONCAT(od.Quant SEPARATOR ', ') AS Quantities,
                            SUM(od.Quant) AS TotalQuantity,
                            SUM(p.Price * od.Quant) AS TOTAL_PRICE,
                            o.TotalPrice
                            FROM
                            orders o
                            JOIN
                            order_details od ON o.OrderID = od.OrderID
                            JOIN
                            products p ON od.productID = p.productID
                            WHERE
                            o.CustomerID = '$UserID'
                            GROUP BY
                            o.OrderID, o.OrderDate
                            ORDER BY
                            o.OrderDate Desc
                        ";

                        $HistoryQuery = $connection -> query($GetOrderHistoryQuery);

                        while ($HistoryRows = $HistoryQuery -> fetch_assoc()){
                            $FormattedTime = date("Y-m-d h:i A", strtotime($HistoryRows['OrderDate']));

                            // Arrange Products
                            $Products = explode(", ", $HistoryRows['Products']);
                            $Quantities = explode(", ", $HistoryRows['Quantities']);

                            $ProductDetails = [];
                       
                            for ($i = 0; $i < count($Products); $i++){
                                $ProductDetails[] = $Products[$i] . " (Quantity: " . $Quantities[$i] . ")";
                            }

                            $ProductDetailsString = implode(", ", $ProductDetails);

                            
                            echo '<tr>
                                    <td>'. htmlspecialchars($HistoryRows['OrderID']) .'</td>
                                    <td class="w-25">'. htmlspecialchars($FormattedTime) .'</td>
                                    <td>'. $ProductDetailsString .'</td>
                                    <td>'. htmlspecialchars($HistoryRows['OrderStatus']) .'</td>
                                    <td> ₱'. htmlspecialchars($HistoryRows['TOTAL_PRICE']) .'</td>
                                </tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Account Settings -->
        <div class="account-section">
            <form action="../mainFunctions/pageFunctions/ChangePass.php" method="post" id="AccountSettingsForm" novalidate>
                <h5>Account Settings</h5>
                <div class="mb-5">
                    <label for="password" class="form-label">Current Password</label>
                    <input type="password" name="CurrentPassword" class="form-control" id="Currentpassword" placeholder="" required>
                    <div class="invalid-feedback" name = "CurrentPassInputFeedBack"></div>
                </div>
                <div class="mb-5">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" name="NewPassword" class="form-control" id="Newpassword" placeholder="" required>
                    <div class="invalid-feedback" name = "NewPassInputFeedback"></div>
                </div>
                <div class="mb-5">
                    <label for="password" class="form-label">Verify Password</label>
                    <input type="password" name="VerifyPassword" class="form-control" id="Verifypassword" placeholder="" required>
                    <div class="invalid-feedback" name = "VerifyPassInputFeedback"></div>
                </div>
                
                <input type="hidden" name="client_ActivePage" value="true">
                <button type="submit" id="submitButton_UpdateSettings" class="btn btn-custom mt-2">Update Settings</button>
            </form>
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
                    Warning: Saving these changes will update your profile information/settings. Proceed with <span style="color:red">caution</span>.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="ConfirmationModal">Confirm</button>
                    <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
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

    <!-- MDBootstrap and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/8.0.0/mdb.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>

    <script>
        const navigationBar_btn = document.getElementById('navigationButton');
        const Button_UpdateInformation = document.getElementById('submitButton_UpdateInformation');
        const Button_UpdateAccountSettings = document.getElementById('submitButton_UpdateSettings');
        // Inputs
        const PhoneNumberInput = document.getElementById("phone");
        const FirstNameInput = document.getElementById('firstName');
        const LastNameInput = document.getElementById('lastName');
        const EmailInput = document.getElementById('email');
        const AddressInput = document.getElementById('AddressInput');

        // Upload Png Button
        const UploadProfilePic_btn = document.getElementById('fileInput');
        const FileName_Element = document.getElementById('fileName');
        // Settings for Png Uploads
        const MAX_FILE_SIZE = 2 * 1024 * 1024;

        // Password Inputs
        const CurrentPassInput = document.getElementById('Currentpassword');
        const NewPassInput = document.getElementById('Newpassword');
        const ConfirmPassInput = document.getElementById('Verifypassword');

        const AccountInformationForm = document.getElementById("AccountInformationForm");
        const AccountSettingsForm = document.getElementById("AccountSettingsForm");

        var NotifyModal = new mdb.Modal(document.getElementById('NotifyModal'));

        navigationBar_btn.addEventListener('click', function(event){
            if (navigationBar_btn.getAttribute('aria-expanded') == "true"){
                navigationBar_btn.focus();
            }else{
                navigationBar_btn.blur();
            }
        });

        // Update Notifies
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

        if (CustomNotifyMsg != ""){
            var NotifyModal2 = new mdb.Modal(document.getElementById('NotifyModal2'));

            document.getElementById('mainModalLabel').innerHTML = CustomNotifyMsgHEADER;
            document.getElementsByClassName('mainmodalbody')[0].textContent = CustomNotifyMsg;

            NotifyModal2.show();
        }

        const TypeOfUpdate = document.createElement("input");
        TypeOfUpdate.type = "hidden";
        TypeOfUpdate.name = "TypeOfUpdate";
        TypeOfUpdate.value = "CustomerUpdate";

        AccountInformationForm.appendChild(TypeOfUpdate);

        // Email Pattern to Check
        const EmailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

        Button_UpdateInformation.addEventListener('click', UpdateInformation_Submit);
        Button_UpdateAccountSettings.addEventListener('click', UpdateAccountSettings_Submit);

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

        // Validation Variables
        var ValidFirstName = true;
        var ValidLastName = true;
        var ValidPhoneNumber = true;
        var ValidAddress = true;
        var ValidEmail = true;
        var ValidCurrentPassword = true;
        var ValidNewPassword = true;
        var ValidConfirmPassword = true;

        const PhoneNumberInputValidation = debounceTime(function(event){
            if (this.value === 0 || this.value === null || this.value === ""){
                ValidPhoneNumber = false;
                event.stopPropagation();

                this.classList.add('is-invalid'); 
                this.classList.remove('is-valid'); 

                document.getElementsByName("PhoneInputFeedback")[0].textContent = "Input field is Empty!";
            }else if(this.value.length < 11){
                ValidPhoneNumber = false;
                event.stopPropagation();

                this.classList.add('is-invalid'); 
                this.classList.remove('is-valid'); 

                document.getElementsByName("PhoneInputFeedback")[0].textContent =  "Phone number must be 11 numbers";
            }else if (this.value.length > 11){
                ValidPhoneNumber = false;
                event.stopPropagation();

                this.classList.add('is-invalid'); 
                this.classList.remove('is-valid'); 

                document.getElementsByName("PhoneInputFeedback")[0].textContent = "Phone number must be 11 numbers";
            }else if (this.value.charAt(0) != 0 || this.value.charAt(1) != 9){
                ValidPhoneNumber = false;
                event.stopPropagation();

                this.classList.add('is-invalid'); 
                this.classList.remove('is-valid'); 

                document.getElementsByName("PhoneInputFeedback")[0].textContent = "Phone Number must start with '09'";
            }else{
                this.classList.add('is-valid'); 
                this.classList.remove('is-invalid'); 
            }
        }, 250);
        const FirstNameInputValidation = debounceTime(function(event){
            if (this.value === ""){
                ValidFirstName = false;
                event.stopPropagation();

                this.classList.add('is-invalid'); 
                this.classList.remove('is-valid'); 

                document.getElementsByName("FirstNameInputFeedback")[0].textContent = "Input field is Empty!";
            }else{
                this.classList.add('is-valid'); 
                this.classList.remove('is-invalid'); 
            }
        },250);
        const LastNameInputValidation = debounceTime(function(event){
            if (this.value === ""){
                ValidLastName = false;
                event.stopPropagation();

                this.classList.add('is-invalid'); 
                this.classList.remove('is-valid'); 

                document.getElementsByName("LastNameInputFeedback")[0].textContent = "Input field is Empty!";
            }else{
                this.classList.add('is-valid'); 
                this.classList.remove('is-invalid'); 
            }
        },250);
        const EmailInputValidation = debounceTime(function(event){
            if (this.value == "" || this.value == null){
                ValidEmail = false;
                event.stopPropagation();

                this.classList.add('is-invalid'); 
                this.classList.remove('is-valid'); 

                document.getElementsByName("EmailInputFeedback")[0].textContent = "Input field is Empty!";
            }else{
                if (!EmailPattern.test(this.value)){
                    ValidEmail = false;
                    event.stopPropagation();

                    this.classList.add('is-invalid'); 
                    this.classList.remove('is-valid'); 

                    document.getElementsByName("EmailInputFeedback")[0].textContent = "Email Inputted is Invalid!";
                }else{
                    this.classList.add('is-valid'); 
                    this.classList.remove('is-invalid'); 
                }
            }
        },250);
        const AddressInputValidation = debounceTime(function(event){
            if (this.value === ""){
                ValidAddress = false;
                event.stopPropagation();

                this.classList.add('is-invalid'); 
                this.classList.remove('is-valid'); 

                document.getElementsByName("AddressInputFeedback")[0].textContent = "Input field is Empty!";
            }else{
                ValidAddress = true;
                this.classList.add('is-valid'); 
                this.classList.remove('is-invalid'); 
            }
        },250);

        ConfirmPassInput.addEventListener('input', debounceTime(function(){
            if (ConfirmPassInput.value != ""){
                if (ConfirmPassInput.value != NewPassInput.value){
                    ValidConfirmPassword = false;
                    ConfirmPassInput.classList.add('is-invalid');
                    ConfirmPassInput.classList.remove('is-valid');

                    document.getElementsByName("VerifyPassInputFeedback")[0].textContent = "Password does not match!";
                }else{
                    ValidConfirmPassword = true;
                    ConfirmPassInput.classList.add('is-valid');
                    ConfirmPassInput.classList.remove('is-invalid');
                }
            }
        }, 125));
        NewPassInput.addEventListener('input', debounceTime(function(){
            if (NewPassInput.value != ""){
                if (ConfirmPassInput.value != NewPassInput.value){
                    ValidNewPassword = false;
                    ConfirmPassInput.classList.add('is-invalid');
                    ConfirmPassInput.classList.remove('is-valid');

                    document.getElementsByName("VerifyPassInputFeedback")[0].textContent = "Password does not match!";
                }
                if (checkPasswordStrength(NewPassInput.value) == 'Weak'){
                    NewPassInput.classList.add('is-invalid'); 
                    NewPassInput.classList.remove('is-valid'); 

                    document.getElementsByName("NewPassInputFeedback")[0].textContent = "Weak";
                    ValidNewPassword = false;
                }else if (checkPasswordStrength(NewPassInput.value) == 'Medium'){
                    NewPassInput.classList.remove('is-invalid'); 
                    NewPassInput.classList.add('is-valid'); 

                    document.getElementsByName("NewPassInputFeedback")[0].textContent = "Medium";
                }else if (checkPasswordStrength(NewPassInput.value) == 'Strong'){
                    NewPassInput.classList.remove('is-invalid'); 
                    NewPassInput.classList.add('is-valid'); 

                    document.getElementsByName("NewPassInputFeedback")[0].textContent = "Strong";
                }
            }else{
                ValidNewPassword = false;
                NewPassInput.classList.add('is-invalid');
                NewPassInput.classList.remove('is-valid');

                document.getElementsByName("NewPassInputFeedback")[0].textContent = "Input field is Empty!";
            }
        }, 125));
        FirstNameInput.addEventListener('input', FirstNameInputValidation);
        LastNameInput.addEventListener('input', LastNameInputValidation)
        PhoneNumberInput.addEventListener('input', PhoneNumberInputValidation);
        EmailInput.addEventListener('input', EmailInputValidation);

        PhoneNumberInput.addEventListener('input', function(){
            // Remove any non-numeric characters
            this.value = this.value.replace(/[^0-9]/g, '');

            // Limit to 11 characters
            if (this.value.length > 11) {
                this.value = this.value.slice(0, 11);
            }
        });
        UploadProfilePic_btn.addEventListener('change', function(event){
            const file = event.target.files[0];

            if (file){
                if (file.size > MAX_FILE_SIZE){
                    // Notify Modal Here
                    var NotifyModal2 = new mdb.Modal(document.getElementById('NotifyModal2'));

                    document.getElementById('mainModalLabel').innerHTML = "Upload Error";
                    document.getElementsByClassName('mainmodalbody')[0].textContent = "File Size limit At: 2mb Only!";

                    NotifyModal2.show();

                    UploadProfilePic_btn.value = "";
                    FileName_Element.textContent = ""
                }else{
                    FileName_Element.textContent = "Uploaded File: " + file.name;
                }
            }else{
                FileName_Element.textContent = "";
            }
        });

        function UpdateInformation_Submit(){
            event.preventDefault();
            
            // First Name Validation
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

            // Last Name Validation
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

            // Address Validation
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

            // Email Validation
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

                    document.getElementsByName("EmailInputFeedback")[0].textContent = "Email Input is Invalid!";
                }else{
                    ValidEmail = true;

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
                NotifyModal.show();
                document.getElementById('ConfirmationModal').addEventListener('click', function(){
                    document.getElementById('AccountInformationForm').submit();
                });
            }
        }

        function UpdateAccountSettings_Submit(){
            event.preventDefault();

            // Password Validation
            if (NewPassInput.value == null || NewPassInput.value == ""){
                ValidNewPassword = false;
                NewPassInput.classList.add('is-invalid');
                NewPassInput.classList.remove('is-valid');

                document.getElementsByName("NewPassInputFeedback")[0].textContent = "Input field is Empty!";
            }else{
                $.ajax({
                    url: '../mainFunctions/pageFunctions/CheckPass.php',
                    type: 'POST',
                    data: {EmailReceived:EmailInput.value, PasswordReceived: NewPassInput.value },
                    success: function(response) {
                        if (response == ""){
                            if (checkPasswordStrength(NewPassInput.value) == 'Weak'){
                                NewPassInput.classList.add('is-invalid'); 
                                NewPassInput.classList.remove('is-valid'); 

                                document.getElementsByName("NewPassInputFeedback")[0].textContent = "Weak";
                                ValidNewPassword = false;
                            }else if (checkPasswordStrength(NewPassInput.value) == 'Medium'){
                                NewPassInput.classList.remove('is-invalid'); 
                                NewPassInput.classList.add('is-valid'); 

                                document.getElementsByName("NewPassInputFeedback")[0].textContent = "Medium";
                                ValidNewPassword = true;
                            }else if (checkPasswordStrength(NewPassInput.value) == 'Strong'){
                                NewPassInput.classList.remove('is-invalid'); 
                                NewPassInput.classList.add('is-valid'); 

                                document.getElementsByName("NewPassInputFeedback")[0].textContent = "Strong";
                                ValidNewPassword = true;
                            }
                        }else if (response != ""){
                            ValidNewPassword = false;
                            NewPassInput.classList.add('is-invalid');
                            NewPassInput.classList.remove('is-valid');

                            document.getElementsByName("NewPassInputFeedback")[0].textContent = response;
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                    }
                });
            }

            if (CurrentPassInput.value == null || CurrentPassInput.value == ""){
                ValidCurrentPassword = false;
                CurrentPassInput.classList.add('is-invalid');
                CurrentPassInput.classList.remove('is-valid');

                document.getElementsByName("CurrentPassInputFeedBack")[0].textContent = "Current Password Input is Empty!";
            }else{
                $.ajax({
                    url: '../mainFunctions/pageFunctions/CheckPass.php',
                    type: 'POST',
                    data: {EmailReceived:EmailInput.value, PasswordReceived: CurrentPassInput.value },
                    success: function(response) {
                        if (response == ""){
                            ValidCurrentPassword = false;

                            CurrentPassInput.classList.add('is-invalid');
                            CurrentPassInput.classList.remove('is-valid');

                            document.getElementsByName("CurrentPassInputFeedBack")[0].textContent = "Current Password Invalid!";
                        }else if (response != ""){
                            ValidCurrentPassword = true;

                            CurrentPassInput.classList.add('is-valid');
                            CurrentPassInput.classList.remove('is-invalid');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                    }
                });
            }

            if (ValidNewPassword != false && ValidConfirmPassword != false && ValidCurrentPassword != false){
                NotifyModal.show();
                document.getElementById('ConfirmationModal').addEventListener('click', function(){
                    document.getElementById('AccountSettingsForm').submit();
                });
            }
        }
    </script>
</body>
</html>
