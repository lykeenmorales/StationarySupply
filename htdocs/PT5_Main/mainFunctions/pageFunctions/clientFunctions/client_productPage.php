<?php
    session_start();
    include '../../connection.php';

    if ($_SERVER['REQUEST_METHOD'] === "POST"){
        $GetProductsQuery = "";
        $ActiveTotalProducts = 0;

        // Pagination Variables (Settings)
        $limit = 9; // Number of products per page
        $page = isset($_POST['page']) ? (int)$_POST['page'] : 1; // Current page
        $offset = ($page - 1) * $limit; // Calculate offset

        // Search Variable
        $SearchQuery = "";
        if (isset($_POST['SearchInputs'])){
            $SearchInput = $_POST['SearchInputs'];
            $SearchQuery = "AND (Name LIKE '%$SearchInput%' OR Price LIKE '%$SearchInput%')";
        }
        
        if (isset($_POST['ChangedType'])){
            if ($_POST['ChangedType'] == "Filter" || $_POST['ChangedType'] == "AllType"){
                if (isset($_POST['FilterID'])) {
                    $TempID = $_POST['FilterID'];
            
                    if ($TempID == "all") {
                        $GetProductsQuery = "SELECT * FROM products WHERE CategoryID != 0 AND Display >= 1 AND StockQuantity > 0 $SearchQuery LIMIT $limit OFFSET $offset";
                    } elseif ($TempID == "1") {
                        $GetProductsQuery = "SELECT * FROM products WHERE CategoryID = 1 AND Display >= 1 AND StockQuantity > 0  $SearchQuery LIMIT $limit OFFSET $offset";
                    } elseif ($TempID == "2") {
                        $GetProductsQuery = "SELECT * FROM products WHERE CategoryID = 2 AND Display >= 1 AND StockQuantity > 0 $SearchQuery LIMIT $limit OFFSET $offset";
                    } elseif ($TempID == "5") {
                        $GetProductsQuery = "SELECT * FROM products WHERE CategoryID = 5 AND Display >= 1 AND StockQuantity > 0 $SearchQuery LIMIT $limit OFFSET $offset";
                    } elseif ($TempID == "misc") {
                        $GetProductsQuery = "SELECT * FROM products WHERE CategoryID IN (3,4) AND Display >= 1 AND StockQuantity > 0 $SearchQuery LIMIT $limit OFFSET $offset";
                    }
                }

                unset($_SESSION['Current_ProductFilter']);
                $_SESSION['Current_ProductFilter'] = $TempID;
            }
        }

        if ($GetProductsQuery == null || $GetProductsQuery == ""){
            $TempID = $_SESSION['Current_ProductFilter'];
            
            if ($TempID == "all") {
                $GetProductsQuery = "SELECT * FROM products WHERE CategoryID != 0 AND Display >= 1 AND StockQuantity > 0 $SearchQuery LIMIT $limit OFFSET $offset";
            } elseif ($TempID == "1") {
                $GetProductsQuery = "SELECT * FROM products WHERE CategoryID = 1 AND Display >= 1 AND StockQuantity > 0 $SearchQuery LIMIT $limit OFFSET $offset";
            } elseif ($TempID == "2") {
                $GetProductsQuery = "SELECT * FROM products WHERE CategoryID = 2 AND Display >= 1 AND StockQuantity > 0 $SearchQuery LIMIT $limit OFFSET $offset";
            } elseif ($TempID == "5") {
                $GetProductsQuery = "SELECT * FROM products WHERE CategoryID = 5 AND Display >= 1 AND StockQuantity > 0 $SearchQuery LIMIT $limit OFFSET $offset";
            } elseif ($TempID == "misc") {
                $GetProductsQuery = "SELECT * FROM products WHERE CategoryID IN (3,4) AND Display >= 1 AND StockQuantity > 0 $SearchQuery LIMIT $limit OFFSET $offset";
            }
        }

        // Put in function to run
        function ShowPageLinks($PositionType){
            global $GetProductsQuery;
            global $connection;
            global $limit;
            global $page;

            // Calculate total products for pagination without LIMIT and OFFSET
            $TotalCountQuery = str_replace("SELECT *", "SELECT COUNT(*) as total", $GetProductsQuery);
            // Remove LIMIT and OFFSET from TotalCountQuery
            $TotalCountQuery = preg_replace('/LIMIT \d+ OFFSET \d+/', '', $TotalCountQuery);
            $TotalCountResult = $connection->query($TotalCountQuery);

            if ($PositionType == "Top"){
                if ($TotalCountResult && $TotalCountRow = $TotalCountResult->fetch_assoc()) {
                    $totalProducts = $TotalCountRow['total'];
                    $totalPages = ceil($totalProducts / $limit); // Calculate total pages

                    if ($totalPages > 1) {
                        echo '<div class="pagination-container pagination-container-CustomTop">';
                            echo '<nav aria-label="Page navigation">';
                                echo '<ul class="pagination justify-content-center">';
        
                                    // Previous Button
                                    if ($page > 1) {
                                        echo '<li class="page-item">';
                                        echo '<a class="page-link page-arrow" href="#" data-page="' . ($page - 1) . '">';
                                        echo '<i class="fas fa-chevron-left"></i> Previous';
                                        echo '</a>';
                                        echo '</li>';
                                    }

                                    for ($i = 1; $i <= $totalPages; $i++) {
                                        if ($i == $page) {
                                            echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
                                        } else {
                                            echo '<li class="page-item"><a class="page-link" href="#" data-page="' . $i . '">' . $i . '</a></li>';
                                        }
                                    }

                                    // Next button
                                    if ($page < $totalPages) {
                                        echo '<li class="page-item">';
                                        echo '<a class="page-link page-arrow" href="#" data-page="' . ($page + 1) . '">';
                                        echo 'Next <i class="fas fa-chevron-right"></i>';
                                        echo '</a>';
                                        echo '</li>';
                                    }                        
    
                                echo '</ul>';
                            echo '</nav>';
                        echo '</div>';
                    }
                } else {
                    echo 'Error fetching total product count.';
                }
            }elseif ($PositionType == "Bottom"){
                if ($TotalCountResult && $TotalCountRow = $TotalCountResult->fetch_assoc()) {
                    $totalProducts = $TotalCountRow['total'];
                    $totalPages = ceil($totalProducts / $limit); // Calculate total pages
                    if ($totalPages > 1) {
                        echo '<div class="pagination-container pagination-container-CustomBottom">';
                            echo '<nav aria-label="Page navigation">';
                                echo '<ul class="pagination justify-content-center">';

                                    // Previous Button
                                    if ($page > 1) {
                                        echo '<li class="page-item">';
                                        echo '<a class="page-link page-arrow" href="#" data-page="' . ($page - 1) . '">';
                                        echo '<i class="fas fa-chevron-left"></i> Previous';
                                        echo '</a>';
                                        echo '</li>';
                                    }
    
                                    for ($i = 1; $i <= $totalPages; $i++) {
                                        if ($i == $page) {
                                            echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
                                        } else {
                                            echo '<li class="page-item"><a class="page-link" href="#" data-page="' . $i . '">' . $i . '</a></li>';
                                        }
                                    }

                                    // Next button
                                    if ($page < $totalPages) {
                                        echo '<li class="page-item">';
                                        echo '<a class="page-link page-arrow" href="#" data-page="' . ($page + 1) . '">';
                                        echo 'Next <i class="fas fa-chevron-right"></i>';
                                        echo '</a>';
                                        echo '</li>';
                                    }   
    
                                echo '</ul>';
                            echo '</nav>';
                        echo '</div>';
                    }
                } else {
                    echo 'Error fetching total product count.';
                }
            }
        }

        $Results = $connection -> query($GetProductsQuery);

        ShowPageLinks("Top");

        while ($row = $Results -> fetch_assoc()){
            $ProductPngPath = $row['picture_path'];
            $FinalizedPngPath = null;
            if ($ProductPngPath != null){
                $FinalizedPngPath = "../productPicUploads/" . basename(htmlspecialchars($ProductPngPath));
            }else{
                $FinalizedPngPath = "";
            }

            $ActiveTotalProducts += 1;

            echo '<div class="col-md-4 mb-5" id="MaincardContents">
                    <div class="card product-card">
                        <img src="'. $FinalizedPngPath .'" class="card-img-top img-fluid rounded" alt="Book Image" style="object-fit: contain; max-height: 250px;" alt="Error: No Image">
                        <div class="card-body">
                        <form action="../clientPages/c_ViewDetails.php" method="post" id="MainForm">
                            <h5 class="card-title">'. htmlspecialchars($row['Name']) .'</h5>
                            <p class="card-text">'. "₱" . htmlspecialchars($row['Price']) .'</p>
                            <div class="d-flex justify-content-between">
                            <input type="hidden" name="ProductID" value="' . $row['productID'] . '">
                            <button class="btn btn-cta me-3">View Details</button>';

                        echo '</form>';
                        
            if ($_SESSION['Login_UserType'] != "Admin"){ 
                echo '<button type="submit" id="AddToCartBtn" name="AddToCartBtn" data-product-ID = "'. $row['productID'] .'" class="btn btn-cta me-3">Add to Cart</button>';
            }

            echo '   </div>
                        </div>
                    </div>
                </div>';
        }
        
        if ($ActiveTotalProducts > 6){
            ShowPageLinks("Bottom");
        }
    }
?>
