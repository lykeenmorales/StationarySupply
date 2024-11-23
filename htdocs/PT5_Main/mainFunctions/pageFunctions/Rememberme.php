<?php
    session_start();
    include '../connection.php';

    if (isset($_POST['IsEnabled'])){
        if ($_POST['IsEnabled'] == true){
            $_SESSION['IsRememberEnabled'] = $_POST['IsEnabled'];
        }
    }
?>