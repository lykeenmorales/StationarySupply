<?php
    session_start();
    include '../connection.php';

    if ($_SERVER["REQUEST_METHOD"] === "POST"){
        if ($_POST['ProductID'] == null || $_POST['TimeZone'] == null || $_SESSION['CustomerID'] == null){
            $_SESSION['ErrorAdd'] = "'Data Received is empty! Please try Again.'";
            header("Location: ../../Pages/Order_DetailsPage.php");
            exit;
        }

        $UserTimezone = $_POST['TimeZone'];
        $CustomerID = $_SESSION['CustomerID'];
        $C_FirstName = $_SESSION['FirstName'];
        $C_LastName = $_SESSION['LastName'];

        // Set a Session ID for the current order {Added for the Button on Add Order}
        // Used to identify what was the CustomerID that ordered previous or current.
        $_SESSION['LastOrder_CustomerID'] = $CustomerID;
        $_SESSION['LastOrder_CustomerFirstName'] = $C_FirstName;
        $_SESSION['LastOrder_CustomerLastName'] = $C_LastName;

        $_SESSION['ErrorAddAgain'] = "Good";

        date_default_timezone_set($UserTimezone);

        $currentDateTime = new DateTime();
        $formattedDateTime = $currentDateTime->format('Y-m-d H:i:s');

        // We Got the Product Info First
        $Query = "SELECT * FROM products WHERE productID = " . $_POST['ProductID'];  

        $ProductsResult = mysqli_query($connection, $Query);

        while ($row = mysqli_fetch_assoc($ProductsResult)){
            $_SESSION['ProductName'] = $row['Name'];
            $_SESSION['ProductPrice'] = $row['Price'];
            $_SESSION['ProductDescription'] = $row['Description'];
            $_SESSION['ProductStockQuantity'] = $row['StockQuantity'];
        }

        function CalculateTotalPrice(){
            global $connection;

            $TotalPrice = 0;
            $ProductID = $_POST['ProductID'];
            $Quantity = $_POST['Amount'];
            $Price = $_SESSION['ProductPrice'];

            $TotalPrice = $Price * $Quantity;

            // We Check if the Quantity is more than the Stock Quantity
            if ($_SESSION['ProductStockQuantity'] < $Quantity){
                $_SESSION['ErrorAdd'] = "Product: " . $_SESSION['ProductName'] . " don't have enough Stock. ";
                return false;
            }

            $_SESSION['ProductStockQuantity'] -= $Quantity;

            // We Update the Stock Quantity
            mysqli_query($connection, "UPDATE products SET StockQuantity = StockQuantity - " . $Quantity . " WHERE ProductID = " . $ProductID);

            return $TotalPrice;
        }

        $TotalPrice = CalculateTotalPrice();

        if ($TotalPrice == false){
            header("Location: ../../Pages/Order_DetailsPage.php");
            exit;
        }

        // We Insert the Order
        $OrderQuery = mysqli_query($connection, "INSERT INTO orders (CustomerID, OrderDate, TotalPrice, OrderStatus) VALUES ('$CustomerID', '$formattedDateTime', '$TotalPrice', 'Processing')");
        // We Insert Data in order_details
        $OrderDetailsQuery = mysqli_query($connection, "INSERT INTO order_details (OrderID, ProductID, Quant) VALUES ((SELECT MAX(OrderID) FROM orders), " . $_POST['ProductID'] . ", " . $_POST['Amount'] . ")");

        if (!$OrderQuery){
            $_SESSION['ErrorAdd'] = 'Data Error: ' . mysqli_error($connection);
            header("Location: ../../Pages/Order_DetailsPage.php");
            exit;
        }
    
        if (!$OrderDetailsQuery){
            $_SESSION['ErrorAdd'] = 'Data Error: ' . mysqli_error($connection);
            header("Location: ../../Pages/Order_DetailsPage.php");
            exit;
        }

        $_SESSION['SuccessAdd'] = "An Order has been placed for: " . $_SESSION['LastName'] . ", " . $_SESSION['FirstName'];
        header("Location: ../../Pages/Order_DetailsPage.php");
    }
?>