<?php
    session_start();
    include '../connection.php';

    $PasswordInput = null;
    $EmailInput = null;

    if (isset($_POST['EmailReceived'])){
        $EmailInput = $connection -> real_escape_string(trim($_POST['EmailReceived']));
    }else{
        if (isset($_SESSION['Email'])){
            $EmailInput = $_SESSION['Email'];
        }
    }

    if (isset($_POST['PasswordReceived'])){
        $PasswordInput = $connection -> real_escape_string(trim($_POST['PasswordReceived']));
    }

    if ($EmailInput != null && $PasswordInput != null){
        $query = "SELECT * FROM customeraccount WHERE Email = '". $EmailInput ."' AND password = '".$PasswordInput."'";

        $result = $connection -> query($query);
        
        if ($result -> num_rows > 0){
            echo "Password is Same as Previous One!";
        }
    }

    return;
?>