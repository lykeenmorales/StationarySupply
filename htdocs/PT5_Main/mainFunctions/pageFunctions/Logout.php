<?php
    session_start();
    include '../connection.php';

    // We get the Email First
    $RememberedEmailInput = null;
    $Login_userType = null;
    $UserID = $_SESSION['Login_UserID'];

    if (isset($_SESSION['RememberedEmail'])){
        if ($_SESSION['RememberedEmail'] != null || $_SESSION['RememberedEmail'] != ""){
            $RememberedEmailInput = $_SESSION['RememberedEmail'];
        }
    }
    if (isset($_SESSION['Login_UserType'])){
        if ($_SESSION['Login_UserType']){
            $Login_userType = $_SESSION['Login_UserType'];
        }
    }
    session_unset();


    $_SESSION['RememberedEmail'] = $RememberedEmailInput;
    $_SESSION['CustomNotifyMsgHEADER'] = "Successfully Logged Out!";
    
    if ($Login_userType == "Admin"){
        $_SESSION['CustomNotifyMsg'] = "Successfully logout from administration!";
    }else{
        $_SESSION['CustomNotifyMsg'] = "Successfully logout from Page!";
    }

    header("Location: ../../LoginPage.php");
    exit;
?>