<?php
    session_start();
    include '../mainFunctions/connection.php';

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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['ProductID'])) {
            $_SESSION['ProductId'] = $_POST['ProductID'];

            $Query = "SELECT * FROM products WHERE productID = " . $_SESSION['ProductId'];
            $QUERYRESULT = mysqli_query($connection, $Query);

            $Row = mysqli_fetch_assoc($QUERYRESULT);

            $_SESSION['ProductPrice'] = $Row['Price'];
            $_SESSION['ProductName'] = $Row['Name'];
            $_SESSION['ProductDescription'] = $Row['Description'];
            $_SESSION['StockQuantity'] = $Row['StockQuantity'];
            $_SESSION['Display'] = $Row['Display'];
            $_SESSION['Featured'] = $Row['Featured'];
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>

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
        .no-resize {
            resize: none;
        }
        .btn-custom {
            background-color: #ffffffc4; /* Match background color with your submit buttons */
            color: #000000e7; /* Text color for contrast */
            border: 2px solid transparent; /* Border to match the design */
            border-radius: 8px; /* Rounded corners for consistency */
            padding: 0.5rem 1rem; /* Padding for a better appearance */
            font-size: 15px; /* Font size for readability */
            transition: background-color 0.2s ease, color 0.2s ease; /* Smooth transition for hover effects */
            cursor: pointer; /* Pointer cursor on hover */
        }

        .btn-custom:hover {
            background-color: #3c4043be; /* Darker background on hover */
            color: #ffffffd3; /* Change text color on hover */
            border-color: #3c4043; /* Border color on hover for emphasis */
        }

        #fileName {
            margin-top: 0.5rem; /* Space above the file name display */
            font-size: 0.9rem; /* Font size for the file name */
            color: #6c757d; /* Placeholder text color */
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
        <h5 class="Content_title">Updating Product </h5>
    
        <div class="container text-center ">
            <form action="../mainFunctions/pageFunctions/UpdateData.php" enctype="multipart/form-data" method="post" class="row g-4 needs-validation justify-content-md-center" id="MainForm" novalidate>
                <div class="row justify-content-md-center text-center">
                    <div class="col-md-3 position-relative">
                        <label for="validationTooltip01" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="productNameInput" value="<?php echo htmlspecialchars($_SESSION['ProductName']);?>" name="ProductName" placeholder="name" required>
                        <div class="invalid-tooltip" name = "ProductNameInputFeedback"></div>
                    </div>
                </div>

                <div class="row justify-content-md-center text-center">
                    <div class="col-md-3 position-relative mt-5">
                        <label for="validationTooltip02" class="form-label">Price</label>
                        <input type="number" class="form-control" id="productPriceInput" value="<?php echo htmlspecialchars($_SESSION['ProductPrice']);?>" name="ProductPrice" placeholder="price" min="1" step=".01" required>
                        <div class="invalid-tooltip" name="ProductPriceFeedback"></div>
                    </div>
                    <div class="col-md-3 position-relative mt-5">
                        <label for="validationTooltip03" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="productQuantityInput" value="<?php if (isset($_SESSION['StockQuantity'])){echo htmlspecialchars($_SESSION['StockQuantity']);}else{echo '0';} ?>" name="ProductQuant" placeholder="quantity" min="0" step="1" required>
                        <div class="invalid-tooltip" name="ProductQuantityFeedback">  </div>
                    </div>
                </div>

                <div class="col-md-4 position-relative mt-5">
                    <label for="validationTooltip04" class="form-label">Description</label>
                    <textarea class="form-control no-resize" id="productDescriptionInput" rows="4" name="ProductDesc" placeholder="description about the product..." required><?php echo htmlspecialchars($_SESSION['ProductDescription']);?></textarea>
                    <div class="invalid-tooltip" name="ProductDescFeedback"></div>
                </div>

                <div class="row justify-content-md-center text-center mt-3">
                    <div class="col-md-3 position-relative">
                        <input data-bs-toggle="tooltip" data-bs-title="If checked will display in Products Page." class="form-check-input me-2" type="checkbox" value="" id="check2" name="DisplayProduct">
                        <label class="form-check-label" for="check2"> Display Product </label>
                        &nbsp; &nbsp;
                        <input data-bs-toggle="tooltip" data-bs-title="If checked will be featured in Home Page." class="form-check-input me-2" type="checkbox" value="" id="check3" name="FeaturedProduct">
                        <label class="form-check-label" for="check3"> Featured </label>
                    </div>
                </div>

                <div class="row justify-content-md-center text-center mt-3">
                    <div class="col-md-3 position-relative">
                        <input type="file" id="fileInput" name="profile-photo" style="display: none;" accept=".png, .jpg, .jpeg">
                        <label for="fileInput" class="btn btn-custom btn-custom-profile mt-3">Upload Photo</label>

                        <div id="fileName" class="mt-3"></div>
                    </div>
                </div>

                <div class="col-12">
                    <button class="btn btn-primary" type="submit" id="submitButton" name="UpdateProduct">Submit</button>
                    <button class="btn btn-primary" type="submit" id="deleteButton" name="DeleteProduct">Delete</button>
                </div>
            </form>
        </div>
    </div>


    <!-- Confirmation Notify Modal -->
    <div class="modal fade" id="SuccessfullModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="Confirm">Delete</button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/8.0.0/mdb.umd.min.js"></script>
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

            // Turn on Validation bootstrap
            const forms = document.querySelectorAll('.needs-validation');
            
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
            
                form.classList.add('was-validated');
                }, false);
            });
        });
    </script>

    <script>
        const SubmitButton = document.getElementById('submitButton');
        const DeleteButton = document.getElementById('deleteButton');
        const ProductName = document.getElementById('productNameInput');
        const ProductQuantity = document.getElementById('productQuantityInput');
        const productUPrice = document.getElementById('productPriceInput');
        const productDescription = document.getElementById('productDescriptionInput');
        const CheckDisplayButton = document.getElementById('check2');
        const IsFeaturedBtn = document.getElementById('check3');

        const FileName_Element = document.getElementById('fileName');
        const UploadProductPic_btn = document.getElementById('fileInput');
        // Settings for Png Uploads
        const MAX_FILE_SIZE = 1 * 1024 * 1024;

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

        const NotifyModal = new bootstrap.Modal(document.getElementById('SuccessfullModal'));

        const IsDisplayed = '<?php
            if ($_SESSION['Display'] >= 1){
                echo "true";
            }else{
                echo "false";
            }
            unset($_SESSION['Display']);
        ?>';

        const IsFeatured = '<?php
            if ($_SESSION['Featured'] >= 1){
                echo "true";
            }else{
                echo "false";
            }
            unset($_SESSION['Featured']);
        ?>';

        if (IsDisplayed == "true"){
            CheckDisplayButton.checked = true;
        }else{
            CheckDisplayButton.checked = false;
        }

        if (IsFeatured == "true"){
            IsFeaturedBtn.checked = true;
        }else{
            IsFeaturedBtn.checked = false;
        }

        const MainForm = document.getElementById("MainForm");
        const TypeOfUpdate = document.createElement("input");
        TypeOfUpdate.type = "hidden";
        TypeOfUpdate.name = "TypeOfUpdate"
        TypeOfUpdate.value = "ProductUpdate"
        
        MainForm.appendChild(TypeOfUpdate);

        SubmitButton.addEventListener('click', ValidateSubmit);
        DeleteButton.addEventListener('click', DeleteProduct);

        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

        CheckDisplayButton.addEventListener('click', function(){
            CheckDisplayButton.blur();
        })

        UploadProductPic_btn.addEventListener('change', function(event){
            const file = event.target.files[0];

            if (file){
                if (file.size > MAX_FILE_SIZE){
                    // Notify Modal Here
                    var NotifyModal2 = new mdb.Modal(document.getElementById('NotifyModal2'));

                    document.getElementById('mainModalLabel').innerHTML = "Upload Error";
                    document.getElementsByClassName('mainmodalbody')[0].textContent = "File Size limit At: 1mb Only!";

                    NotifyModal2.show();

                    UploadProductPic_btn.value = "";
                    FileName_Element.textContent = ""
                }else{
                    FileName_Element.textContent = "Uploaded File: " + file.name;
                }
            }else{
                FileName_Element.textContent = "";
            }
        });

        // Custom Submit Validation
        function ValidateSubmit(event){
            event.preventDefault();

            var ValidName = true;
            var ValidQuantity = true;
            var ValidPrice = true;
            var ValidDescription = true;

            if (ProductName.value == "" || ProductName.value == null){
                ValidName = false;
                event.stopPropagation();

                ProductName.classList.add('is-invalid'); 
                ProductName.classList.remove('is-valid'); 

                document.getElementsByName("ProductNameInputFeedback")[0].textContent = "Input field is Empty!";
            }else{
                ProductName.classList.add('is-valid'); 
                ProductName.classList.remove('is-invalid'); 
            }

            if (ProductQuantity.value == null || ProductQuantity.value <= 0){
                ValidQuantity = false;
                event.stopPropagation();

                ProductQuantity.classList.add('is-invalid'); 
                ProductQuantity.classList.remove('is-valid'); 

                document.getElementsByName("ProductQuantityFeedback")[0].textContent = "Input field is Empty!";
            }else{
                ProductQuantity.classList.add('is-valid'); 
                ProductQuantity.classList.remove('is-invalid'); 
            }

            if (productUPrice.value == null || productUPrice.value <= 0){
                ValidPrice = false;
                event.stopPropagation();

                productUPrice.classList.add('is-invalid'); 
                productUPrice.classList.remove('is-valid'); 

                document.getElementsByName("ProductPriceFeedback")[0].textContent = "Input field is Empty!";
            }else{
                productUPrice.classList.add('is-valid'); 
                productUPrice.classList.remove('is-invalid'); 
            }

            if (productDescription.value == null || productDescription.value == ""){
                ValidDescription = false;
                event.stopPropagation();

                productDescription.classList.add('is-invalid'); 
                productDescription.classList.remove('is-valid'); 

                document.getElementsByName("ProductDescFeedback")[0].textContent = "Input field is Empty!";
            }else{
                productDescription.classList.add('is-valid'); 
                productDescription.classList.remove('is-invalid'); 
            }
           

            if (ValidDescription != false && ValidName != false && ValidPrice != false && ValidQuantity != false){
                document.getElementById('MainForm').submit();
            }
        } 

        function DeleteProduct(event){
            event.preventDefault();

            document.getElementsByClassName('modal-body')[0].textContent = "Are you sure you want to delete this Product? (cant be restored)";
            NotifyModal.show();

            document.getElementById('Confirm').addEventListener('click', function(){
                NotifyModal.hide();

                const IsDelete = document.createElement("input")
                IsDelete.type = "hidden"
                IsDelete.name = "IsDelete"
                IsDelete.value = "Delete"

                MainForm.appendChild(IsDelete);

                document.querySelector("form").submit();
            });
        }
    </script>
</body>
</html>