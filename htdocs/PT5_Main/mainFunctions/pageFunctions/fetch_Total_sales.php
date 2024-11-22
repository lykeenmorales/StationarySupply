<?php
    // Database connection
    include '../connection.php'; // Replace with your actual connection file

    $sql = "SELECT DATE_FORMAT(OrderDate, '%Y-%m-%d') AS orderMonth,
                SUM(TotalPrice) as TotalSales
            FROM 
                orders
            Group BY
                orderMonth
            Order By
                orderMonth;
            ";

    $result = $connection -> query($sql);

    $months = [];
    $TotalSales = [];

    if ($result === false) {
        // Handle query error
        http_response_code(500);
        echo json_encode(['error' => 'Database query error: ' . $connection->error]);
        exit();
    }

    while ($row = $result -> fetch_assoc()) {
        if ($row['orderMonth'] == null || $row['TotalSales'] == null){
            continue;
        }
        $months[] = $row['orderMonth'];
        $TotalSales[] = (float)$row['TotalSales'];
    }

    if (empty($months) || empty($TotalSales)){
        http_response_code(404);
        echo json_encode(['error' => 'No data found']);
        exit();
    }

    echo json_encode([
        'months' => $months,
        'TotalSales' => $TotalSales
    ]);
?>