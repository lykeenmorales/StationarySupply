<?php
    session_start();
    include 'connection.php';
    
    $jsonData = file_get_contents('php://input');
    $Data = json_decode($jsonData, true);

    if ($Data === null && json_last_error() !== JSON_ERROR_NONE) {
        echo 'JSON Error: ' . json_last_error_msg();
    }

    if (isset($Data['CallBack'])){
        $Callback = $Data['CallBack'];
        $MainData = $Data['Data'];

        if ($Callback == "SideNavBarOpen"){
            $_SESSION['IsSideMenuOpen'] = $MainData['IsSideNavOpenValue'];
        }

        if ($Callback == "VisibleAllHideProducts"){
            $_SESSION['VisibleAllProducts'] = $MainData['VisibleAllProductsValue'];
        }
    }
?>