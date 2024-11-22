<?php
    session_start();
    include '../connection.php';

    if ($_SERVER['REQUEST_METHOD'] === "POST"){
        $ProductName = $connection -> real_escape_string($_POST['ProductName']);
        $ProductPrice = $_POST['ProductPrice'];
        $ProductDescription = $connection -> real_escape_string($_POST['ProductDesc']);
        $Quantity = $_POST['ProductQuant'];

        // Used in {Add Another} Button
        $_SESSION['ErrorAddAgain'] = "Good"; //-- Good State

        if ($ProductDescription == null or $ProductName == null or $ProductPrice == null){
            $_SESSION['ErrorAdd'] = 'Data Received is empty! Please try Again.';
            header("Location: ../../Pages/ProductInfoPage.php");
            exit;
        }

        $AddedPng = false; 

        $Query = null;

        if ($Quantity == 0 or $Quantity == null){
            $Query = "INSERT INTO products (Name, Price, Description) Values ('$ProductName', '$ProductPrice', '$ProductDescription')";
        }else{
            if (isset($_POST['DisplayProduct'])){
                if (isset($_POST['FeaturedProduct'])){
                    $Query = "INSERT INTO products (Name, Price, Description, StockQuantity, Display, Featured) Values ('$ProductName', '$ProductPrice', '$ProductDescription', '$Quantity', 1, 1)";
                }else{
                    $Query = "INSERT INTO products (Name, Price, Description, StockQuantity, Display) Values ('$ProductName', '$ProductPrice', '$ProductDescription', '$Quantity', 1)";
                }
            }else{
                $Query = "INSERT INTO products (Name, Price, Description, StockQuantity, Display) Values ('$ProductName', '$ProductPrice', '$ProductDescription', '$Quantity', 0)";
            }
        }

        if (isset($_FILES['profile-photo'])){
            if ($_FILES['profile-photo']['error'] === UPLOAD_ERR_OK && $_FILES['profile-photo']['size'] > 0){
                $FileName = $_FILES['profile-photo']['name'];
                $FileType = $_FILES['profile-photo']['type'];
                $File_tmpName = $_FILES['profile-photo']['tmp_name'];
                $FileSize = $_FILES['profile-photo']['size'];

                $MAX_FILE_SIZE = 1 * 1024 * 1024;
                $AcceptedFileType = ['image/jpeg', 'image/jpg', 'image/png'];

                if (!in_array($FileType, $AcceptedFileType)){
                    $_SESSION['CustomNotifyMsg'] = "Product Profile type not valid!";
                    $_SESSION['CustomNotifyMsgHEADER'] = "Error: Update Failed";
                    header("Location: ../../Pages/ProductAdd.php");
                    exit;
                }elseif ($FileSize > $MAX_FILE_SIZE){
                    $_SESSION['CustomNotifyMsg'] = "Product Profile exceeds 1 mb!";
                    $_SESSION['CustomNotifyMsgHEADER'] = "Error: Update Failed";
                    header("Location: ../../Pages/ProductAdd.php");
                    exit;
                }else{

                    // After Deleting previous picture We Add new one
                    $Sanitized_FileName = uniqid(). "_" . basename($FileName);
                    $TARGET_UPLOAD_DIRECTORY ="/workspaces/Php-SQL-Activities/htdocs/PT5_Main/productPicUploads/";
                    $pngUpload_Path = $TARGET_UPLOAD_DIRECTORY . $Sanitized_FileName;

                    $stmt = null;
                    
                    $WillDisplay = null;
                    $WillFeatured = null;

                    if (isset($_POST['DisplayProduct'])){
                        if (isset($_POST['FeaturedProduct'])){
                            $WillDisplay = 1;
                            $WillFeatured = 1;

                            $stmt = $connection -> prepare("INSERT INTO products (Name, Price, Description, StockQuantity, picture_path, Display, Featured) Values (?, ?, ?, ?, ?, ?, ?)");
                            $stmt -> bind_param("sdsisii", $ProductName, $ProductPrice, $ProductDescription, $Quantity, $pngUpload_Path, $WillDisplay, $WillFeatured);
                        }else{
                            $WillDisplay = 1;

                            $stmt = $connection -> prepare("INSERT INTO products (Name, Price, Description, StockQuantity, picture_path, Display) Values (?, ?, ?, ?, ?, ?)");
                            $stmt -> bind_param("sdsisi", $ProductName, $ProductPrice, $ProductDescription, $Quantity, $pngUpload_Path, $WillDisplay);
                        }
                    }else{
                        $WillDisplay = 0;

                        $stmt = $connection -> prepare("INSERT INTO products (Name, Price, Description, StockQuantity, picture_path, Display) Values (?, ?, ?, ?, ? , ?)");
                        $stmt -> bind_param("sdsisi", $ProductName, $ProductPrice, $ProductDescription, $Quantity, $pngUpload_Path, $WillDisplay);
                    }

                    if ($stmt->execute()){
                        if (move_uploaded_file($File_tmpName, $pngUpload_Path)){
                            // Successfully move the file to Uploading Path
                            $_SESSION['SuccessAdd'] = "Product has been Successfully Added w/ Product Profile!";
                            header("Location: ../../Pages/ProductInfoPage.php");
                            exit;
                        }else{ 
                            // Failed to move the file to Uploading Path
                            $_SESSION['CustomNotifyMsg'] = "Profile picture upload failed! Please Try Again.";
                            $_SESSION['CustomNotifyMsgHEADER'] = "Error: File Upload Failed";
                            header("Location: ../../Pages/ProductAdd.php");
                            exit;
                        }
                    }else{
                        // Failed to save in Database
                        $_SESSION['CustomNotifyMsg'] = "Error Occurred while trying to update! Please Try Again.";
                        $_SESSION['CustomNotifyMsgHEADER'] = "Error: Update Failed";
                        header("Location: ../../Pages/ProductAdd.php");
                        exit;
                    }

                    $stmt -> close();
                }
            }else{
                $InsertResult = mysqli_query($connection, $Query);
            }
        }else{
            $InsertResult = mysqli_query($connection, $Query);
        }

        if ($InsertResult){
            $_SESSION['SuccessAdd'] = "Product has been Successfully Added!";
            header("Location: ../../Pages/ProductInfoPage.php");
            exit;
        }else{
            $_SESSION['ErrorAdd'] = 'Data Error: ' . mysqli_error($connection);
            header("Location: ../../Pages/ProductInfoPage.php");
            exit;
        }
    }
?>