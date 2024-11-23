<?php
    session_start();
    include '../connection.php';

    if (isset($_POST['NewPassword'])){
        $NewPassword = $connection -> real_escape_string(trim($_POST['NewPassword']));
        $EmailUsed = null;

        if (isset($_POST['EmailInput'])){
            $EmailUsed = $connection -> real_escape_string(trim($_POST['EmailInput']));
        }elseif (isset($_POST['Email'])){
            if ($_POST['Email'] != ""){
                $EmailUsed = $_POST["Email"];
            }
        }elseif (isset($_SESSION['Email'])){
            if ($_SESSION['Email'] != ""){
                $EmailUsed = $_SESSION["Email"];
            }
        }
        
        // This Here Works only in user c_Account.php Client Page
        if (isset($_POST['client_ActivePage'])){
            if ($_POST['client_ActivePage'] == "true"){
                $Login_Active_UserID = $_SESSION['Login_UserID'];
                $GetUserInformation = "SELECT * FROM customeraccount WHERE CustomerID = '$Login_Active_UserID'";
                $InformationQuery = $connection -> query($GetUserInformation);
                $InformationResult = $InformationQuery -> fetch_assoc();

                $Information_Email = $InformationResult['Email'];

                $Query = "UPDATE customeraccount SET Password = '$NewPassword' WHERE Email = '$Information_Email'";

                $UpdateResult = $connection -> query($Query);

                if ($UpdateResult){
                    $_SESSION['CustomNotifyMsg'] = "Password has been Successfully Changed!";
                    $_SESSION['CustomNotifyMsgHEADER'] = "Password Changed!";
                    header('Location: ../../clientPages/c_Account.php');
                    exit();
                }else{
                    $_SESSION['CustomNotifyMsg'] = "Something went wrong while trying to change your password! Please try again.";
                    $_SESSION['CustomNotifyMsgHEADER'] = "Error!";
                    header('Location: ../../clientPages/c_Account.php');
                    exit();
                }
                return;
            }
        }

        $Query = "UPDATE customeraccount SET Password = '$NewPassword' WHERE Email = '$EmailUsed'";

        $UpdateResult = $connection -> query($Query);

        if ($UpdateResult){
            $_SESSION['CustomNotifyMsg'] = "Password has been Successfully Changed! Please log in!";
            $_SESSION['CustomNotifyMsgHEADER'] = "Password Changed!";
            header('Location: ../../LoginPage.php');
            exit();
        }else{
            $_SESSION['CustomNotifyMsg'] = "Something went wrong while trying to change your password! Please try again.";
            $_SESSION['CustomNotifyMsgHEADER'] = "Error!";
            header('Location: ../../LoginPage.php');
            exit();
        }
    }
?>