<?php
    // Database connection
    include '../connection.php'; // Replace with your actual connection file


    $sql = "SELECT Name, StockQuantity from products where StockQuantity < 1000 LIMIT 10";


    $result = $connection -> query($sql);

    $ProductName = [];
    $Stocks = [];

    if ($result === false) {
        // Handle query error
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Database query error: ' . $connection->error]);
        exit();
    }

    if ($result -> num_rows > 0) {
        while ($row = $result -> fetch_assoc()) {
            $ProductName[] = $row['Name'];
            $Stocks[] = (int)$row['StockQuantity'];
        }
    }


    // Return JSON data using encode
    echo json_encode([
        'ProductName' => $ProductName,
        'Stocks' => $Stocks,
        'data' => $result
    ]);
?>