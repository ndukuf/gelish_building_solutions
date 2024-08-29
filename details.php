<?php
    /* Require necessary files for the application to function properly. */
    //establishes a connection to the database.
    require 'database_connection.php';

    //contains company-related data.
    require 'company_data.php';

    //provides the current date and time.
    require 'current_date_and_time.php';

    //contains currency_prefix data.
    require 'currency_prefix.php';

    // Define the SQL query to select the product details name from the `products` table based on the provided product code
    $selectProductsSaleDetailsSql = 'SELECT `product_code`,`product_name`,`product_description`,`product_selling_price`,`product_count` FROM `products` WHERE `product_code`=? ';
                            
    // Prepare the SQL statement
    $selectProductsSaleDetailsStmt = $dbConnection->prepare($selectProductsSaleDetailsSql);

    // Bind parameters to the prepared statement
    $selectProductsSaleDetailsStmt->bind_param('s',$_GET['item']);

    // Execute the prepared statement
    $selectProductsSaleDetailsStmt->execute();

    // Retrieve the result set
    $selectProductsSaleDetailsResult = $selectProductsSaleDetailsStmt->get_result();

    // Close the $selectProductsSaleDetailsStmt prepared statement to free up resources
    $selectProductsSaleDetailsStmt->close();

    // Fetch data as an associative array
    $selectProductsSaleDetailsAssoc= $selectProductsSaleDetailsResult->fetch_assoc();

    // Define the SQL query to select the product image name from the `products_images` table based on the provided product code
    $selectProductImageSql = 'SELECT `product_image_name` FROM `products_images` WHERE `product_code`=?';

    // Prepare the SQL statement for execution with the database connection
    $selectProductImageStmt = $dbConnection->prepare($selectProductImageSql);

    // Bind the product code parameter to the prepared statement using the 's' data type (string)
    $selectProductImageStmt->bind_param('s', $_GET['item']);

    // Execute the prepared statement
    $selectProductImageStmt->execute();

    // Get the result set from the executed statement
    $selectProductImageResult = $selectProductImageStmt->get_result();

    // Close the $selectProductImageStmt prepared statement to free up resources
    $selectProductImageStmt->close();

    // Fetch data as an associative array
    $selectProductImageAssoc= $selectProductImageResult->fetch_assoc();

    // Define the SQL query to select the product code from the wishlist table where the user_id code matches the current user's id and product code matches the given product code, limiting the results to 1
    $checkProductWishlistAvailabilitySql = 'SELECT `product_code` FROM `wishlist` WHERE `user_id`=? AND `product_code`=? LIMIT 1';

    // Prepare the SQL statement for execution
    $checkProductWishlistAvailabilityStmt = $dbConnection->prepare($checkProductWishlistAvailabilitySql);

    // Bind the current user's id and product code parameter to the prepared statement
    // $selectProductsAssoc['product_code'] contains the product code value to be used in the query
    $checkProductWishlistAvailabilityStmt->bind_param('ss', $_SESSION['user_id'],$_GET['item']);

    // Execute the prepared statement to perform the query on the database
    $checkProductWishlistAvailabilityStmt->execute();

    // Retrieve the result set from the executed query
    $checkProductWishlistAvailabilityResult = $checkProductWishlistAvailabilityStmt->get_result();

    // Close the $checkProductWishlistAvailabilityStmt prepared statement to free up resources
    $checkProductWishlistAvailabilityStmt->close();

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'addNewReview' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['addNewReview']) == true) {
        // Sanitize and validate user input data to prevent SQL injection and cross-site scripting (XSS) attacks
        $newReview = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_POST['newReview']),ENT_QUOTES,'UTF-8')));
        if (empty($newReview)) {
            // redirect to the product details page
            header('Location: details?item='.$product_code);
            // Exit the current script to prevent further execution
            exit();
        }
        $date_and_time = date('Y-m-d H:i:s', strtotime($currentDateAndTime));

        // SQL INSERT query
        $addNewReviewSql = 'INSERT INTO `products_reviews`(`user_id`, `product_code`, `review`, `date_and_time`) VALUES (?,?,?,?)';

        // Prepare the SQL query statement
        $addNewReviewStmt = $dbConnection->prepare($addNewReviewSql);

        // Bind the parameters to the prepared statement
        $addNewReviewStmt->bind_param('ssss', $_SESSION['user_id'],$_GET['item'],$newReview,$date_and_time);

        // Attempt to execute the statement for adding a new user
        if ($addNewReviewStmt->execute()) {
            // redirect to the product details page
            header('Location: details?item='.$_GET['item']);
            // Exit the current script to prevent further execution
            exit();
        }
    }

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'updateWishList' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['updateWishList']) == true) {
        // Sanitize and validate user input data to prevent SQL injection and cross-site scripting (XSS) attacks
        $product_code = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_POST['product_code']),ENT_QUOTES,'UTF-8')));

        // Prepare a SQL statement to select the product_code from the 'wishlist' table where the product_code matches the provided product_code
        $selectProductCodeSql = 'SELECT `product_code` FROM `wishlist` WHERE `product_code` =?';
        
        // Prepare the SQL statement
        $selectProductCodeStmt = $dbConnection->prepare($selectProductCodeSql);

        // Bind the product_code variable to the prepared statement as a string
        $selectProductCodeStmt->bind_param('s', $product_code);

        // Execute the prepared statement
        $selectProductCodeStmt->execute();

        // Fetch the result of the executed statement
        $selectProductCodeResult = $selectProductCodeStmt->get_result();

        // Close the $selectProductCodeStmt prepared statement to free up resources
        $selectProductCodeStmt->close();

        // Check if the number of rows returned by the executed statement is 1, indicating that the supplier's name already exists in the 'wishlist' table
        if ($selectProductCodeResult->num_rows !==1 ) {
            // Prepare a SQL statement to add the product_code from the 'wishlist' table where the product_code matches the provided product_code
            $addProductCodeSql = 'INSERT INTO `wishlist`(`user_id`,`product_code`,`date`) VALUES (?,?,?)';
            
            // Prepare the SQL statement
            $addProductCodeStmt = $dbConnection->prepare($addProductCodeSql);

            // Convert the date-time object to a string format of 'Y-m-d H:i:s'
            $date = date('Y-m-d H:i:s', strtotime($currentDateAndTime));

            // Bind the product_code variable to the prepared statement as a string
            $addProductCodeStmt->bind_param('sss', $_SESSION['user_id'],$product_code,$date);

            // Execute the prepared statement
            if ($addProductCodeStmt->execute()) {
                // redirect to the product details page
                header('Location: details?item='.$product_code);
                // Exit the current script to prevent further execution
                exit();
            }
        } else {
            // Prepare a SQL statement to delete the product_code from the 'wishlist' table where the product_code matches the provided product_code
            $deleteProductCodeSql = 'DELETE FROM `wishlist` WHERE `user_id`=? AND `product_code`=?';
            
            // Prepare the SQL statement
            $deleteProductCodeStmt = $dbConnection->prepare($deleteProductCodeSql);

            // Bind the product_code variable to the prepared statement as a string
            $deleteProductCodeStmt->bind_param('ss', $_SESSION['user_id'],$product_code);

            // Execute the prepared statement
            if ($deleteProductCodeStmt->execute()) {
                // redirect to the product details page
                header('Location: details?item='.$product_code);
                // Exit the current script to prevent further execution
                exit();
            }
        }
    }

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'addToCart' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['addToCart']) == true) {
        // Sanitize and validate user input data to prevent SQL injection and cross-site scripting (XSS) attacks
        $product_code = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_POST['product_code']),ENT_QUOTES,'UTF-8')));
        $quantity = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_POST['quantity']),ENT_QUOTES,'UTF-8')));

        if (empty($product_code)) {
            // redirect to the product details page
            header('Location: details?item='.$product_code);
            // Exit the current script to prevent further execution
            exit();
        }

        if (empty($quantity)) {
            // redirect to the product details page
            header('Location: details?item='.$product_code);
            // Exit the current script to prevent further execution
            exit();
        }
        // Define a regular expression pattern to match product code (one or more digits, optionally followed by a decimal point and one or more digits)
        $productCodeRegex = '/^\d+(\.\d+)?$/';

        // Define a regular expression pattern to match quantity (one or more digits, optionally followed by a decimal point and one or more digits)
        $quantityRegex = '/^\d+(\.\d+)?$/';

        if (!preg_match($productCodeRegex, $product_code)) {
            // redirect to the product index page
            header('Location: index');
            // Exit the current script to prevent further execution
            exit();
        }

        if (!preg_match($quantityRegex, $quantity)) {
            // redirect to the product details page
            header('Location: details?item='.$product_code);
            // Exit the current script to prevent further execution
            exit();
        }

        // Define the SQL query to select the product code  from the `cart` table based on the provided current user's id and product code
        $checkProductExistenceInCartSql = 'SELECT `product_code` FROM `cart` WHERE `user_id`=? AND `product_code`=?';
                                
        // Prepare the SQL statement
        $checkProductExistenceInCartStmt = $dbConnection->prepare($checkProductExistenceInCartSql);

        // Bind parameters to the prepared statement
        $checkProductExistenceInCartStmt->bind_param('ss', $_SESSION['user_id'],$product_code);

        // Execute the prepared statement
        $checkProductExistenceInCartStmt->execute();

        // Retrieve the result set
        $checkProductExistenceInCartResult = $checkProductExistenceInCartStmt->get_result();

        // Close the $checkProductExistenceInCartStmt prepared statement to free up resources
        $checkProductExistenceInCartStmt->close();

        // Fetch data as an associative array
        $checkProductExistenceInCartAssoc= $checkProductExistenceInCartResult->fetch_assoc();

        // Check if the product code exists in the cart
        if ((intval($checkProductExistenceInCartAssoc['product_code'])) >= 1) {
            // If the product exists, redirect the user to the product details page
            // with the product code as a query parameter
            header('Location: details?item='.$product_code);
            // Exit the current script to prevent further execution
            exit();
        }


        // Define the SQL query to select the product details name from the `products` table based on the provided product code
        $selectProductCodeDetailsSql = 'SELECT `product_code`,`product_name`,`product_description`,`product_selling_price`,`product_count` FROM `products` WHERE `product_code`=? ';
                                
        // Prepare the SQL statement
        $selectProductCodeDetailsStmt = $dbConnection->prepare($selectProductCodeDetailsSql);

        // Bind parameters to the prepared statement
        $selectProductCodeDetailsStmt->bind_param('s', $product_code);

        // Execute the prepared statement
        $selectProductCodeDetailsStmt->execute();

        // Retrieve the result set
        $selectProductCodeDetailsResult = $selectProductCodeDetailsStmt->get_result();

        // Close the $selectProductCodeDetailsStmt prepared statement to free up resources
        $selectProductCodeDetailsStmt->close();

        // Fetch data as an associative array
        $selectProductCodeDetailsAssoc= $selectProductCodeDetailsResult->fetch_assoc();

        // if ((intval($selectProductCodeDetailsAssoc['product_count'])) >= 1) {
        if (intval($quantity) > intval($selectProductCodeDetailsAssoc['product_count'])) {
            header('Location: details?item='.$product_code);
            // Exit the current script to prevent further execution
            exit();
        } else {
            // Calculate the total selling price of the product by multiplying the product selling price with the quantity
            $product_total_selling_price = floatval($selectProductCodeDetailsAssoc['product_selling_price']) * floatval($quantity);

            // Define a regular expression pattern to match the product total selling price
            // The pattern matches one or more digits, optionally followed by a decimal point and one or more digits
            $productTotalSellingPriceRegex = '/^\d+(\.\d+)?$/';

            // Validate the product total selling price against the regular expression pattern
            // If the pattern does not match, redirect to the product details page
            if (!preg_match($productTotalSellingPriceRegex, $product_total_selling_price)) {
                // Redirect to the product details page with the product code as a query parameter
                header('Location: details?item='.$product_code);
                // Exit the current script to prevent further execution
                exit();
            }

            // Prepare a SQL statement to insert the product_code from the 'wishlist' table where the product_code matches the provided product_code
            $addProductCodeToCartSql = 'INSERT INTO `cart`(`user_id`,`product_code`,`quantity`,`product_selling_price`,`product_total_selling_price`,`date`) VALUES (?,?,?,?,?,?)';
            
            // Prepare the SQL statement
            $addProductCodeToCartStmt = $dbConnection->prepare($addProductCodeToCartSql);

            // Convert the date-time object to a string format of 'Y-m-d H:i:s'
            $date = date('Y-m-d H:i:s', strtotime($currentDateAndTime));

            // Bind the product_code variable to the prepared statement as a string
            $addProductCodeToCartStmt->bind_param('ssssss', $_SESSION['user_id'],$selectProductCodeDetailsAssoc['product_code'],$quantity,$selectProductCodeDetailsAssoc['product_selling_price'],$product_total_selling_price,$date);

            // Execute the prepared statement
            if ($addProductCodeToCartStmt->execute()) {
                // redirect to the product details page
                header('Location: details?item='.$product_code);
                // Exit the current script to prevent further execution
                exit();
            } else {
                // redirect to the product details page
                header('Location: details?item='.$product_code);
                // Exit the current script to prevent further execution
                exit();
            }
        }
    }
?>
<!-- Declare the document type as HTML -->
<!DOCTYPE html>
<!-- Specify the language of the document as English -->
<html lang="en">
    <!-- Head section of the HTML document -->
    <head>
        <title><?php echo $fetchCompanyDataAssoc['company_name'].' | '.$selectProductsSaleDetailsAssoc['product_name'] ?></title>
        <?php require_once 'head.php'; ?>
    </head>
    <body style="margin-bottom: 160px; background-color: #dee2e6;">
        <?php require_once 'navbar.php'; ?>
        <section class="mt-2 container">
        <div class="row">
            <div class="bg-light p-2 border-right col-sm-12 col-md-6 col-lg-6 col-xl-6">
                <div class="p-2">
                    <?php if (!empty($selectProductImageAssoc['product_image_name'])) { ?>
                        <div>
                            <img src="productsImages/<?php echo $selectProductImageAssoc['product_image_name']; ?>" height="450px" width="450px" style="object-fit:contain;" class="card-img-top" alt="admin/productsImages/<?php echo $selectProductsSaleDetailsAssoc['product_name']; ?>">
                        </div>
                    <?php } else { ?>
                        <div>
                            <img src="<?php echo $fetchCompanyDataAssoc['company_logo']; ?>" height="450px" width="450px" style="object-fit:contain;" class="bg-dark card-img-top" alt="<?php echo $fetchCompanyDataAssoc['company_name']; ?>">
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="pt-2 p-2 bg-light col-sm-12 col-md-6 col-lg-6 col-xl-6">
                <p class="text-dark font-weight-bold"><?php echo $selectProductsSaleDetailsAssoc['product_name']; ?></p>
                <p class="text-dark font-weight-bold"><?php echo $selectProductsSaleDetailsAssoc['product_description']; ?></p>
                <p class="text-dark font-weight-bold">
                    <?php if (empty($selectProductsSaleDetailsAssoc['product_selling_price'])==true) { ?>
                        Not found
                    <?php } elseif ($selectProductsSaleDetailsAssoc['product_selling_price'] < 1) { ?>
                        <?php echo $currency_one. ' '.number_format($selectProductsSaleDetailsAssoc['product_selling_price'], 2); ?>
                    <?php } elseif ($selectProductsSaleDetailsAssoc['product_selling_price'] > 1) { ?>
                        <?php echo $currency_many. ' '.number_format($selectProductsSaleDetailsAssoc['product_selling_price'], 2); ?>
                    <?php } ?>
                </p>
                <div class="row">
                    <div class="ml-2">
                        <form class="ml-2" action="" method="post">
                            <input type="tel" name="quantity" id="quantity" value="1.00" class="form-control form-control-sm mb-2 w-50">
                            <input type="hidden" name="product_code" id="product_code" value="<?php echo $selectProductsSaleDetailsAssoc['product_code']; ?>">
                            <?php if ($checkProductWishlistAvailabilityResult->num_rows !== 1) { ?>
                                <button type="submit" name="updateWishList" class="float-left btn rounded btn-transparent"><i class="far fa-heart fa-lg"></i></button>
                            <?php } else { ?>
                                <button type="submit" name="updateWishList" class="float-left btn rounded btn-transparent"><i class="fas fa-heart fa-lg" style="color: #fd1d29;"></i></button>
                            <?php } ?>

                            <?php if ($selectProductsSaleDetailsAssoc['product_count'] < '1') { ?>
                                <button type="button" name="outOfStock" id="outOfStock" class="btn btn-sm rounded btn-secondary">Out of stock</button>
                            <?php } elseif($selectProductsSaleDetailsAssoc['product_count'] >= '1') { ?>
                                <button type="submit" name="addToCart" id="addToCart" class="btn btn-sm btn-primary rounded">Add to cart<i class="ml-2 fas fa-cart-plus fa-flip-horizontal"></i></button>
                            <?php } ?>
                        </form>
                    </div>
                </div>
            </div>
            <div class="mt-2 pb-3 container bg-light rounded">
                Reviews
                <!-- Button trigger modal -->
                <button type="button" class=" mt-2 btn btn-sm btn-primary" data-toggle="modal" data-target="#exampleModal">
                    Add review
                </button>
                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="" method="post">
                                <div class="modal-body">
                                    <textarea name="newReview" id="newReview" class="form-control form-control-sm" placeholder="Add review"></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" name="addNewReview" id="addNewReview" class="btn btn-primary">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <?php
                    // SQL select query
                    $selectProductReviewsSql = 'SELECT `review` FROM `products_reviews` WHERE `product_code`=? ';
                                            
                    // Prepare the SQL statement
                    $selectProductReviewsStmt = $dbConnection->prepare($selectProductReviewsSql);

                    // Bind parameters to the prepared statement
                    $selectProductReviewsStmt->bind_param('s', $_GET['item']);

                    // Execute the prepared statement
                    $selectProductReviewsStmt->execute();

                    // Retrieve the result set
                    $selectProductReviewsResult = $selectProductReviewsStmt->get_result();

                    // Close the $selectProductReviewsStmt prepared statement to free up resources
                    $selectProductReviewsStmt->close();

                    // Fetch data as an associative array
                    // while ($selectProductReviewsAssoc= $selectProductReviewsResult->fetch_assoc()) {}
                    if (intval($selectProductReviewsResult->num_rows) < 1) {
                ?>
                <p class="text-center">No reviews yet</p>
                <?php } else { while ($selectProductReviwesAssoc = $selectProductReviewsResult->fetch_assoc()) { ?>
                    <div class="card">
                        <div class="card-body">
                            <?php echo $selectProductReviwesAssoc['review']; ?>
                        </div>
                    </div>
                <?php }} ?>
            </div>
        </div>
        </section>
        <?php require_once 'footer.php'; ?>
        <?php require_once 'foot.php'; ?>
    </body>
</html>
<?php $dbConnection->close(); // Close the database connection ?>