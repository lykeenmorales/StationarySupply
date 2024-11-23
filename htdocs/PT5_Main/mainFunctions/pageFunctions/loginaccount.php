<?php
    session_start();
    include '../connection.php';

    if ($_POST['EmailInput'] == null or $_POST['PasswordInput'] == null){
        $_SESSION['CustomNotifyMsg'] = "Something went wrong while trying to login! Please try again.";
        $_SESSION['CustomNotifyMsgHEADER'] = "Error: Login Error!";
        header('Location: ../../../PT5_Main/LoginPage.php');
        exit();
    }

    $UserPass = $connection -> real_escape_string(trim($_POST['PasswordInput']));
    $UserEmail = $connection -> real_escape_string(trim($_POST['EmailInput']));

    $Client_account_Query = "SELECT * FROM customeraccount WHERE Email = '".$UserEmail."' AND password = '".$UserPass."'";
    $Admin_account_Query = "SELECT * FROM Administrator WHERE adminName = '".$UserEmail."' AND adminPassword = '".$UserPass."'";

    $result_ClientAccount = $connection -> query($Client_account_Query);

    $result_AdminAccount = $connection -> query($Admin_account_Query);

    $Client_Acc_resultArray = $result_ClientAccount -> fetch_assoc();
    $Admin_Acc_Result = $result_AdminAccount -> fetch_assoc();

    if (isset($Admin_Acc_Result)){
        if ($Admin_Acc_Result != "" or $Admin_Acc_Result != null){
            $_SESSION['Login_UserType'] = "Admin";
            $_SESSION['Login_UserID'] = $Admin_Acc_Result['adminID'];
            $_SESSION['Login_UserName'] = $Admin_Acc_Result['adminName'];

            header('Location: ../../homepage.php');
            exit();
        }
    }

    if (isset($Client_Acc_resultArray)){
        if ($Client_Acc_resultArray != "" or $Client_Acc_resultArray != null){
            // This will allow the user to Change their password if the administrator created their account
            if ($Client_Acc_resultArray['password'] == $Client_Acc_resultArray['first_name'] . " " . $Client_Acc_resultArray['last_name'] . "." . $Client_Acc_resultArray['Phone'] || $Client_Acc_resultArray['password'] == "testpass"){
                $_SESSION['Email'] = $Client_Acc_resultArray['Email'];
                
                header('Location: ../../clientPages/changePassPage.php');
                exit();
            }

            // We proceed to login if above condition is not met
            $_SESSION['Login_UserType'] = "Client";
            $_SESSION['Login_UserID'] = $Client_Acc_resultArray['CustomerID'];
            $_SESSION['Login_UserName'] = $Client_Acc_resultArray['first_name'] . " " . $Client_Acc_resultArray['last_name'];

            // If Remembered Button is True we set the email automatically when logout on the login form
            if (isset($_SESSION['IsRememberEnabled'])){
                if ($_SESSION['IsRememberEnabled'] == true){
                    $_SESSION['RememberedEmail'] = $Client_Acc_resultArray['Email'];
                }
            }else{
                unset($_SESSION['RememberedEmail']);
            }

            header('Location: ../../clientPages/clientHomePage.php');
            exit();
        }else{
            $_SESSION['LoginError'] = "Invalid Email or Password";
            header('Location: ../../../PT5_Main/LoginPage.php');
            exit();
        }
    }else{
        $_SESSION['LoginError'] = "Invalid Email or Password";
        header('Location: ../../../PT5_Main/LoginPage.php');
        exit();
    }
?>