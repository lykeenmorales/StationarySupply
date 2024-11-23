<?php
    session_start();
    include '../connection.php';

    $EmailInput = null;

    if (isset($_POST['EmailReceived'])){
        $EmailInput = $connection -> real_escape_string(trim($_POST['EmailReceived']));
    }else{
        if (isset($_SESSION['Email'])){
            $EmailInput = trim($_SESSION['Email']);
        }
    }

    if ($EmailInput != null){
        $query = "SELECT * FROM customeraccount WHERE Email = '".$EmailInput."'";

        $result = $connection -> query($query);
        if ($result -> num_rows > 0){
            echo "true";
        }else{
            echo "false";
        }
    }

    return;
?>