<?php
    session_start();
    include '../connection.php';

    $queryEmailCheck = "SELECT * FROM customeraccount";

    $resultQueryCheck = $connection -> query($queryEmailCheck);

    if (isset($_POST['EmailInput'])){
        if ($_POST['EmailInput'] != null or $_POST['EmailInput'] != ""){
            while ($row = $resultQueryCheck -> fetch_assoc()){
                if ($row['Email'] === $_POST['EmailInput']){
                    $_SESSION['Email_EXIST_ERROR'] = "Email is Already Registered!";
                    header("Location: ../../LoginPage.php");
                    exit();
                }
            }
        }
    }

    function ConvertToPhoneNumber($PhoneNumber){
        $ReceivedPhoneNumber = (string)$PhoneNumber;
    
        if (substr($ReceivedPhoneNumber, 0,1) == '0'){
            $ReceivedPhoneNumber = substr($ReceivedPhoneNumber, 1);
        }
    
        $PhoneNumberStringEdit1 = '+63' . $ReceivedPhoneNumber;    
        $FinalizePhoneNumber = preg_replace('/(\+63)(\d{3})(\d{3})(\d{4})/', '$1 $2 $3 $4', $PhoneNumberStringEdit1);
    
        return $FinalizePhoneNumber;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === "POST"){
        $FirstName = $connection -> real_escape_string(trim($_POST['FirstNameInput']));
        $LastName = $connection -> real_escape_string(trim($_POST['LastNameInput']));
        $PhoneNumber = ConvertToPhoneNumber($_POST['PhoneNumber']);
        $Address = $connection -> real_escape_string($_POST['Location']);
        $Email = $connection -> real_escape_string(trim($_POST['EmailInput']));
        $userPass = $connection -> real_escape_string(trim($_POST['userPassword']));

        if ($FirstName == null or $LastName == null or $PhoneNumber == null or $Address == null or $userPass == null){
            $_SESSION['ErrorAdd'] = 'Data Received is empty! Please try Again.';
            header("Location: ../../LoginPage.php");
            return;
        }

        $Query = null;

        if ($Email == null){
            $Query = "INSERT INTO customeraccount (first_name, last_name, Phone, Address, Password) Values ('$FirstName', '$LastName', '$PhoneNumber', '$Address', '$userPass')";
        }else{
            $Query = "INSERT INTO customeraccount (first_name, last_name, Phone, Address, Email, Password) Values ('$FirstName', '$LastName', '$PhoneNumber', '$Address', '$Email', '$userPass')";
        }

      
        $InsertResult = mysqli_query($connection, $Query);

        if ($InsertResult){
            $_SESSION['CustomNotifyMsg'] = "Account has been Successfully Registered! Please login.";
            $_SESSION['CustomNotifyMsgHEADER'] = "Registered Successfully!";
            header("Location: ../../LoginPage.php");
            exit;
        }else{
            $_SESSION['CustomNotifyMsg'] = "Something went wrong. Please try again.";
            $_SESSION['CustomNotifyMsgHEADER'] = "Register Error";
            header("Location: ../../LoginPage.php");
            exit;
        }
    }
?>