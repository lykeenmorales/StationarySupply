<?php
    session_start();
    include '../connection.php';

    function ConvertToPhoneNumber($PhoneNumber){
        $ReceivedPhoneNumber = (string)$PhoneNumber;

        if (substr($ReceivedPhoneNumber, 0,1) == '0'){
            $ReceivedPhoneNumber = substr($ReceivedPhoneNumber, 1);
        }

        $PhoneNumberStringEdit1 = '+63' . $ReceivedPhoneNumber;    
        $FinalizePhoneNumber = preg_replace('/(\+63)(\d{3})(\d{3})(\d{4})/', '$1 $2 $3 $4', $PhoneNumberStringEdit1);

        return $FinalizePhoneNumber;
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST['TypeOfUpdate'])){
            if ($_POST['TypeOfUpdate'] == "CustomerUpdate"){
                // Used in {Add Another} Button
                $_SESSION['ErrorAddAgain'] = "Good"; //-- Good State
                $_SESSION['RemoveAddAnother'] = "true"; //-- Set to true to remove

                $Current_UsedID = null;
                if (isset($_SESSION['Login_UserID'])){
                    if ($_SESSION['Login_UserID'] != ""){
                        $Current_UsedID = $_SESSION['Login_UserID'];
                    }
                }else{
                    if (isset($_SESSION['CustomerID'])){
                        if ($_SESSION['CustomerID'] != ""){
                            $Current_UsedID = $_SESSION['CustomerID'];
                        }
                    }
                }

                // Checking if IsDelete is set
                if (isset($_POST['IsDelete'])){
                    if ($_POST['IsDelete'] != null and $_POST['IsDelete'] == "Delete"){
                        $Query = "DELETE FROM customeraccount WHERE CustomerID = " . $_SESSION['CustomerID'];
                
                        $DeleteQuery = mysqli_query($connection, $Query);
                        
                        if ($DeleteQuery){
                            $_SESSION['SuccessAdd'] = "Account has been Deleted Successfully!";
                            header("Location: ../../Pages/CustomerPage.php");
                            exit;
                        }else{
                            $_SESSION['ErrorAdd'] = 'Data Error while trying to Delete: ' . mysqli_error($connection);
                            header("Location: ../../Pages/CustomerPage.php");
                            exit;
                        }
                    }
                }
           
                $AddedPng = null;

                if (isset($_FILES['profile-photo'])){
                    if ($_FILES['profile-photo']['size'] != 0){
                        $FileName = $_FILES['profile-photo']['name'];
                        $FileType = $_FILES['profile-photo']['type'];
                        $File_tmpName = $_FILES['profile-photo']['tmp_name'];
                        $FileSize = $_FILES['profile-photo']['size'];

                        $MAX_FILE_SIZE = 1 * 1024 * 1024;
                        $AcceptedFileType = ['image/jpeg', 'image/jpg', 'image/png'];

                        if (!in_array($FileType, $AcceptedFileType)){
                            $_SESSION['CustomNotifyMsg'] = "Profile picture type not valid!";
                            $_SESSION['CustomNotifyMsgHEADER'] = "Error: Update Failed";
                            header("Location: ../../clientPages/c_Account.php");
                            exit;
                        }elseif ($FileSize > $MAX_FILE_SIZE){
                            $_SESSION['CustomNotifyMsg'] = "Profile picture exceeds 1 mb!";
                            $_SESSION['CustomNotifyMsgHEADER'] = "Error: Update Failed";
                            header("Location: ../../clientPages/c_Account.php");
                            exit;
                        }else{
                            // We Delete the previous picture First (If Exist Only)
                            $GetUserInfo = "SELECT * FROM customeraccount WHERE CustomerID = '$Current_UsedID'";
                            $GetUserInfoQuery = $connection -> query($GetUserInfo);
                            $GetUserInfo_Results = $GetUserInfoQuery -> fetch_assoc();

                            $UserInfo_Picture_Path = $GetUserInfo_Results['profile_picture_path'];

                            if ($UserInfo_Picture_Path != null || $UserInfo_Picture_Path != ""){
                                if (file_exists($UserInfo_Picture_Path)){
                                    unlink($UserInfo_Picture_Path);
                                }
                            }

                            // After Deleting previous picture We Add new one
                            $Sanitized_FileName = uniqid(). "_" . basename($FileName);
                            $TARGET_UPLOAD_DIRECTORY ="/workspaces/Php-SQL-Activities/htdocs/PT5_Main/profilePicUploads/";
                            $pngUpload_Path = $TARGET_UPLOAD_DIRECTORY . $Sanitized_FileName;

                            $stmt = $connection -> prepare("UPDATE customeraccount SET profile_picture_path = ? WHERE CustomerID = '$Current_UsedID' ");
                            $stmt -> bind_param("s", $pngUpload_Path);

                            if ($stmt->execute()){
                                if (move_uploaded_file($File_tmpName, $pngUpload_Path)){
                                    // Successfully move the file to Uploading Path
                                    $AddedPng = true;
                                }else{ 
                                    // Failed to move the file to Uploading Path
                                    $_SESSION['CustomNotifyMsg'] = "Profile picture upload failed! Please Try Again.";
                                    $_SESSION['CustomNotifyMsgHEADER'] = "Error: File Upload Failed";
                                    header("Location: ../../clientPages/c_Account.php");
                                    exit;
                                }
                            }else{
                                // Failed to save in Database
                                $_SESSION['CustomNotifyMsg'] = "Error Occurred while trying to update! Please Try Again.";
                                $_SESSION['CustomNotifyMsgHEADER'] = "Error: Update Failed";
                                header("Location: ../../clientPages/c_Account.php");
                                exit;
                            }

                            $stmt -> close();
                        }
                    }
                }

                // If not Delete we proceed to Update
                $FirstName = $connection -> real_escape_string($_POST['FirstName']);
                $LastName = $connection -> real_escape_string($_POST['LastName']);
                $verifyPhoneNumber = $connection -> real_escape_string($_POST['PhoneNumber']);
                $Phone = ConvertToPhoneNumber($verifyPhoneNumber);
                $Address = $connection -> real_escape_string($_POST['Address']);
                $finalizedEmail = null;
                if (isset($_POST['Email'])){
                    if ($_POST['Email'] != ""){
                        $finalizedEmail = $_POST['Email'];
                    }
                }elseif (isset($_POST['EmailInput'])){
                    if ($_POST['EmailInput']){
                        $finalizedEmail = $_POST['EmailInput'];
                    }
                }
                $Email = $connection -> real_escape_string($finalizedEmail);
            
                if ($FirstName == null or $LastName == null or $Phone == null or $Address == null){
                    $_SESSION['ErrorAdd'] = 'Data Received is empty {Error while receiving Data}! Please try Again.';
                    header("Location: ../../Pages/CustomerPage.php");
                    return;
                }
        
            
                $Query = null;
            
                if ($Email != ""){
                    $Query = "UPDATE customeraccount SET first_name = '$FirstName', last_name = '$LastName', Phone = '$Phone', Address = '$Address', Email = '$Email' WHERE CustomerID = " . $Current_UsedID;
                }else{
                    $Query = "UPDATE customeraccount SET first_name = '$FirstName', last_name = '$LastName', Phone = '$Phone', Address = '$Address', Email = null WHERE CustomerID = " . $Current_UsedID;
                }
            
                $UpdateResult = mysqli_query($connection, $Query);

                if (isset($_POST['client_ActivePage'])){
                    if ($_POST['client_ActivePage'] == "true"){
                        if ($UpdateResult){
                            if ($AddedPng == true){
                                $_SESSION['CustomNotifyMsg'] = "Account Information Has Been Changed w/ Profile Picture!";
                                $_SESSION['CustomNotifyMsgHEADER'] = "Account Information Updated!";
                                header("Location: ../../clientPages/c_Account.php");
                                exit;
                            }else{
                                $_SESSION['CustomNotifyMsg'] = "Account Information Has Been Changed!";
                                $_SESSION['CustomNotifyMsgHEADER'] = "Account Information Updated!";
                                header("Location: ../../clientPages/c_Account.php");
                                exit;
                            }
                        }else{
                            $_SESSION['CustomNotifyMsg'] = "Something went wrong while trying to Update Information! Please try again.";
                            $_SESSION['CustomNotifyMsgHEADER'] = "Error!";
                            header("Location: ../../clientPages/c_Account.php");
                            exit;
                        }
                    }
                }else{
                    if ($UpdateResult){
                        $_SESSION['SuccessAdd'] = $FirstName . " " . $LastName . " Account has been Updated.";
                        header("Location: ../../Pages/CustomerPage.php");
                        exit;
                    }else{
                        $_SESSION['ErrorAdd'] = 'Data Error: ' . mysqli_error($connection);
                        header("Location: ../../Pages/CustomerPage.php");
                        exit;
                    }
                }
            }
        
            if ($_POST['TypeOfUpdate'] == "ProductUpdate"){
                // Used in {Add Another} Button
                $_SESSION['ErrorAddAgain'] = "Good"; //-- Good State
                $_SESSION['RemoveAddAnother'] = "true"; //-- Set to true to remove
                
                $DisplayProduct = null;
                $IsFeaturedProduct = null;

                if (isset($_POST['DisplayProduct'])){
                    $DisplayProduct = 1;
                }else{
                    $DisplayProduct = 0;
                }

                if (isset($_POST['FeaturedProduct'])){
                    $IsFeaturedProduct = 1;
                }else{
                    $IsFeaturedProduct = 0;
                }

                // Checking if IsDelete is set
                if (isset($_POST['IsDelete'])){
                    if ($_POST['IsDelete'] != null and $_POST['IsDelete'] == "Delete"){
                        $Query = "UPDATE products SET Display = 0 WHERE productID = " . $_SESSION['ProductId'];
            
                        $DeleteQuery = mysqli_query($connection, $Query);
            
                        if ($DeleteQuery){
                            $_SESSION['SuccessAdd'] = 'Automatically Set to Visible False Only! (Wont appear unless "Display all" is Unchecked)';
                            header("Location: ../../Pages/ProductInfoPage.php");
                            exit;
                        }else{
                            $_SESSION['ErrorAdd'] = 'Data Error while trying to Delete: ' . mysqli_error($connection);
                            header("Location: ../../Pages/ProductInfoPage.php");
                            exit;
                        }
                    }
                }
        
                $AddedPng = false;

                if (isset($_FILES['profile-photo'])){
                    if ($_FILES['profile-photo'] != null || $_FILES['profile-photo'] != ""){
                        $FileName = $_FILES['profile-photo']['name'];
                        $FileType = $_FILES['profile-photo']['type'];
                        $File_tmpName = $_FILES['profile-photo']['tmp_name'];
                        $FileSize = $_FILES['profile-photo']['size'];

                        $UsedProductID = $_SESSION['ProductId'];

                        $MAX_FILE_SIZE = 2 * 1024 * 1024;
                        $AcceptedFileType = ['image/jpeg', 'image/jpg', 'image/png'];

                        if (!in_array($FileType, $AcceptedFileType)){
                            $_SESSION['CustomNotifyMsg'] = "Profile picture type not valid!";
                            $_SESSION['CustomNotifyMsgHEADER'] = "Error: Update Failed";
                            header("Location: ../../Pages/ProductEdit.php");
                            exit;
                        }elseif ($FileSize > $MAX_FILE_SIZE){
                            $_SESSION['CustomNotifyMsg'] = "Profile picture exceeds 15 mb!";
                            $_SESSION['CustomNotifyMsgHEADER'] = "Error: Update Failed";
                            header("Location: ../../Pages/ProductEdit.php");
                            exit;
                        }else{
                            // We Delete the previous picture First (If Exist Only)
                            $GetUserInfo = "SELECT * FROM products WHERE productID = '$UsedProductID'";
                            $GetUserInfoQuery = $connection -> query($GetUserInfo);
                            $GetUserInfo_Results = $GetUserInfoQuery -> fetch_assoc();

                            $UserInfo_Picture_Path = $GetUserInfo_Results['picture_path'];

                            if ($UserInfo_Picture_Path != null || $UserInfo_Picture_Path != ""){
                                if (file_exists($UserInfo_Picture_Path)){
                                    unlink($UserInfo_Picture_Path);
                                }
                            }

                            // After Deleting previous picture We Add new one
                            $Sanitized_FileName = uniqid(). "_" . basename($FileName);
                            $TARGET_UPLOAD_DIRECTORY ="/workspaces/Php-SQL-Activities/htdocs/PT5_Main/productPicUploads/";
                            $pngUpload_Path = $TARGET_UPLOAD_DIRECTORY . $Sanitized_FileName;

                            $stmt = $connection -> prepare("UPDATE products SET picture_path = ? WHERE productID = '$UsedProductID' ");
                            $stmt -> bind_param("s", $pngUpload_Path);

                            if ($stmt->execute()){
                                if (move_uploaded_file($File_tmpName, $pngUpload_Path)){
                                    // Successfully move the file to Uploading Path
                                    $AddedPng = true;
                                }else{ 
                                    // Failed to move the file to Uploading Path
                                    $_SESSION['CustomNotifyMsg'] = "Profile picture upload failed! Please Try Again.";
                                    $_SESSION['CustomNotifyMsgHEADER'] = "Error: File Upload Failed";
                                    header("Location: ../../Pages/ProductEdit.php");
                                    exit;
                                }
                            }else{
                                // Failed to save in Database
                                $_SESSION['CustomNotifyMsg'] = "Error Occurred while trying to update! Please Try Again.";
                                $_SESSION['CustomNotifyMsgHEADER'] = "Error: Update Failed";
                                header("Location: ../../Pages/ProductEdit.php");
                                exit;
                            }

                            $stmt -> close();
                        }
                    }
                }

                $ProductName = $connection -> real_escape_string($_POST['ProductName']);
                $ProductPrice = $_POST['ProductPrice'];
                $ProductDescription = $connection -> real_escape_string($_POST['ProductDesc']);
                $ProductQuantity = $_POST['ProductQuant'];
            
                if ($ProductDescription == null or $ProductName == null or $ProductPrice == null){
                    $_SESSION['ErrorProductAdd'] = 'Data Received is empty {Error while receiving Data}! Please try Again.';
                    header("Location: ../../Pages/ProductInfoPage.php");
                    exit;
                }
            
                $Query = null;
        
                if ($ProductQuantity != null or $ProductQuantity != 0){
                    $Query = "UPDATE products SET Name = '$ProductName', Price = '$ProductPrice', Description = '$ProductDescription', StockQuantity = '$ProductQuantity', Display = '$DisplayProduct', Featured = '$IsFeaturedProduct', version = version + 1 WHERE productID = " . $_SESSION['ProductId'];
                }else{
                    $Query = "UPDATE products SET Name = '$ProductName', Price = '$ProductPrice', Description = '$ProductDescription', Display = '$DisplayProduct', Featured = '$IsFeaturedProduct', version = version + 1 WHERE productID = " . $_SESSION['ProductId'];
                }
            
                $UpdateResult = mysqli_query($connection, $Query);

                if ($UpdateResult){
                    if ($AddedPng == true){
                        $_SESSION['SuccessAdd'] = $ProductName . " Information has been Updated w/ Product Profile Added!";
                        header("Location: ../../Pages/ProductInfoPage.php");
                        exit;
                    }else{
                        $_SESSION['SuccessAdd'] = $ProductName . " Information has been Updated!";
                        header("Location: ../../Pages/ProductInfoPage.php");
                        exit;
                    }
                }else{
                    $_SESSION['ErrorAdd'] = 'Data Error: ' . mysqli_error($connection);
                    header("Location: ../../Pages/ProductInfoPage.php");
                    exit;
                }
                
            }
        }
    }

?>