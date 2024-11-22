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

    if ($_SERVER['REQUEST_METHOD'] === "POST"){
        $FirstName = $connection -> real_escape_string(trim($_POST['FirstName']));
        $LastName = $connection -> real_escape_string(trim($_POST['LastName']));
        $PhoneNumber = ConvertToPhoneNumber($_POST['PhoneNumber']);
        $Address = $connection -> real_escape_string($_POST['Address']);
        $Email = $connection -> real_escape_string(trim($_POST['Email']));

        // Used in {Add Another} Button
        $_SESSION['ErrorAddAgain'] = "Good"; //-- Good State

        if ($FirstName == null or $LastName == null or $PhoneNumber == null or $Address == null){
            $_SESSION['ErrorAdd'] = 'Data Received is empty! Please try Again.';
            header("Location: ../../Pages/CustomerPage.php");
            return;
        }

        $Query = null;

        $CustomPass = $FirstName . $LastName . "@!" . uniqid();

        if ($Email == null){
            $Query = "INSERT INTO customeraccount (first_name, last_name, Phone, Address, Password) Values ('$FirstName', '$LastName', '$PhoneNumber', '$Address', '$CustomPass')";
        }else{
            $Query = "INSERT INTO customeraccount (first_name, last_name, Phone, Address, Email, Password) Values ('$FirstName', '$LastName', '$PhoneNumber', '$Address', '$Email', '$CustomPass')";
        }

      
        $InsertResult = mysqli_query($connection, $Query);

        if ($InsertResult){
            $_SESSION['SuccessAdd'] = "New Account has been Successfully Added";
            header("Location: ../../Pages/CustomerPage.php");
            exit;
        }else{
           $_SESSION['ErrorAdd'] = 'Data Error: ' . mysqli_error($connection);
           header("Location: ../../Pages/CustomerPage.php");
            exit;
        }
    }
?>