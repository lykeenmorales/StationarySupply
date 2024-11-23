<?php
// Database connection
include '../connection.php'; // Replace with your actual connection file


$sql = "SELECT p.Name, SUM(pu.Quant) AS total_purchases
        FROM order_details pu
        JOIN products p ON pu.productID = p.productID
        GROUP BY p.productID
        ORDER BY total_purchases DESC
        LIMIT 5";


$result = $connection -> query($sql);

$products = [];
$purchases = [];

if ($result === false) {
    // Handle query error
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Database query error: ' . $connection->error]);
    exit();
}

if ($result -> num_rows > 0) {
    while ($row = $result -> fetch_assoc()) {
        $products[] = $row['Name'];
        $purchases[] = (int)$row['total_purchases'];
    }
}


// Return JSON data using encode
echo json_encode([
    'products' => $products,
    'purchases' => $purchases
]);
?>