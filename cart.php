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

    // Check if the 'user_id' session variable is not set or empty
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        // If the condition is true, redirect the user to the 'index' page with a 'sign_in' parameter
        header('Location: index?sign_in');
        // Exit the script to prevent further execution
        exit();
    } else {
        // Retrieve the user ID from the session, and sanitize it to prevent SQL injection
        $user_id = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_SESSION['user_id']), ENT_QUOTES, 'UTF-8')));

        // Prepare a SQL query to fetch user data from the database
        $fetchUserSystemAccessSql = 'SELECT `system_access` FROM `users` WHERE `user_id`=? LIMIT 1';

        // Prepare the SQL statement
        $fetchUserSystemAccessStmt = $dbConnection->prepare($fetchUserSystemAccessSql);

        // Bind parameters to the prepared statement
        $fetchUserSystemAccessStmt->bind_param('s',$user_id);

        // Execute the prepared statement
        $fetchUserSystemAccessStmt->execute();

        // Retrieve the result set
        $fetchUserSystemAccessResult = $fetchUserSystemAccessStmt->get_result();

        // Close the $fetchUserSystemAccessStmt prepared statement to free up resources
        $fetchUserSystemAccessStmt->close();

        // Fetch data as an associative array
        $fetchUserSystemAccessAssoc= $fetchUserSystemAccessResult->fetch_assoc();

        // Check if the row does not exist or the fetched system_access is not identical to string 5
        if ($fetchUserSystemAccessResult->num_rows!==1 || strval($fetchUserSystemAccessAssoc['system_access']) !== '5') {
            //establishes a connection sign_out php page.
            require 'sign_out.php';
        }
    }

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'checkoutBtn' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['checkoutBtn']) == true) {
        // Sanitize and validate user input data to prevent SQL injection and cross-site scripting (XSS) attacks
        $user_id_session = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_SESSION['user_id']),ENT_QUOTES,'UTF-8')));

        // Prepare a SQL statement to fetch product_code from the 'cart' table
        $countProductsInCartSql = 'SELECT * FROM `cart` WHERE `user_id`=?';

        // Prepare the SQL statement for execution
        $countProductsInCartStmt = $dbConnection->prepare($countProductsInCartSql);

        // Bind the user ID to the prepared statement
        $countProductsInCartStmt->bind_param('s', $user_id_session);

        // Execute the prepared SQL statement
        $countProductsInCartStmt->execute();

        // Fetch the result set from the executed SQL statement
        $countProductsInCartResult = $countProductsInCartStmt->get_result();

        // Close the $countProductsInCartStmt prepared statement to free up resources
        $countProductsInCartStmt->close();

        $selectTotalPriceSql = 'SELECT SUM(`product_total_selling_price`) AS `product_total_selling_price` FROM `cart` WHERE `user_id`=?';
        // Prepare the SQL statement
        $selectTotalPriceStmt = $dbConnection->prepare($selectTotalPriceSql);
        
        // Bind the current user's user_id session variable to the SQL SELECT statement
        $selectTotalPriceStmt->bind_param('s', $user_id_session);

        // Execute the prepared statement
        $selectTotalPriceStmt->execute();

        // Retrieve the result set
        $selectTotalPriceResult = $selectTotalPriceStmt->get_result();
        
        // Close the prepared statement to free up resources
        $selectTotalPriceStmt->close();

        // Fetch data as an associative array
        $selectTotalPriceAssoc= $selectTotalPriceResult->fetch_assoc();
        
        if (intval($countProductsInCartResult->num_rows)>=1 && floatval($selectTotalPriceAssoc['product_total_selling_price'])>=1.00) {
            // redirect to the checkout page
            header('Location: checkout');
            // Exit the current script to prevent further execution
            exit();
        } else {
            // redirect to the cart page
            header('Location: cart');
            // Exit the current script to prevent further execution
            exit();
        }
    }

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'removeItem' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['removeItem']) == true) {
        // Sanitize and validate user input data to prevent SQL injection and cross-site scripting (XSS) attacks
        $user_id_session = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_SESSION['user_id']),ENT_QUOTES,'UTF-8')));

        // Sanitize and validate user input data to prevent SQL injection and cross-site scripting (XSS) attacks
        $itemID = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_POST['itemID']),ENT_QUOTES,'UTF-8')));

        // Prepare a SQL query to fetch product code from the database
        $fetchCart_product_code_Sql = 'SELECT `product_code` FROM `cart` WHERE `user_id`=? AND `product_code`=? LIMIT 1';

        // Prepare the SQL statement
        $fetchCart_product_code_Stmt = $dbConnection->prepare($fetchCart_product_code_Sql);

        // Bind parameters to the prepared statement
        $fetchCart_product_code_Stmt->bind_param('ss',$user_id,$itemID);

        // Execute the prepared statement
        $fetchCart_product_code_Stmt->execute();

        // Store the result set
        $fetchCart_product_code_Stmt->store_result();

        if ($fetchCart_product_code_Stmt->num_rows !== 0) {
            // Prepare a SQL query to delete data from the database
            $deleteCart_product_code_Sql = 'DELETE FROM `cart` WHERE `user_id`=? AND `product_code`=?';

            // Prepare the SQL statement
            $deleteCart_product_code_Stmt = $dbConnection->prepare($deleteCart_product_code_Sql);

            // Bind parameters to the prepared statement
            $deleteCart_product_code_Stmt->bind_param('ss',$user_id,$itemID);

            // Execute the prepared statement
            if ($deleteCart_product_code_Stmt->execute()) {
                // redirect to the cart page
                header('Location: cart');
                // Exit the current script to prevent further execution
                exit();
            }
        }

        // Close the $fetchCart_product_code_Stmt prepared statement to free up resources
        $fetchCart_product_code_Stmt->close();
    }

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'editItem' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['editItem']) == true) {
        // Sanitize and validate user input data to prevent SQL injection and cross-site scripting (XSS) attacks
        $user_id_session = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_SESSION['user_id']),ENT_QUOTES,'UTF-8')));

        // Sanitize and validate user input data to prevent SQL injection and cross-site scripting (XSS) attacks
        $itemID = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_POST['itemID']),ENT_QUOTES,'UTF-8')));

        // Sanitize and validate user input data to prevent SQL injection and cross-site scripting (XSS) attacks
        $cartQuantity = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_POST['cartQuantity']),ENT_QUOTES,'UTF-8')));

        // Prepare a SQL query to fetch product code from the database
        $fetchCart_product_code_Sql = 'SELECT `product_code` FROM `cart` WHERE `user_id`=? AND `product_code`=? LIMIT 1';

        // Prepare the SQL statement
        $fetchCart_product_code_Stmt = $dbConnection->prepare($fetchCart_product_code_Sql);

        // Bind parameters to the prepared statement
        $fetchCart_product_code_Stmt->bind_param('ss',$user_id,$itemID);

        // Execute the prepared statement
        $fetchCart_product_code_Stmt->execute();

        // Store the result set
        $fetchCart_product_code_Stmt->store_result();

        if ($fetchCart_product_code_Stmt->num_rows !== 0) {
            // Prepare a SQL query to fetch product code from the database
            $fetch_product_count_Sql = 'SELECT `product_selling_price`,`product_count` FROM `products` WHERE `product_code`=? LIMIT 1';

            // Prepare the SQL statement
            $fetch_product_count_Stmt = $dbConnection->prepare($fetch_product_count_Sql);

            // Bind parameters to the prepared statement
            $fetch_product_count_Stmt->bind_param('s',$itemID);

            // Execute the prepared statement
            $fetch_product_count_Stmt->execute();

            // Retrieve the result set
            $fetch_product_count_Stmt_Result = $fetch_product_count_Stmt->get_result();

            // Close the prepared statement to free up resources
            $fetch_product_count_Stmt->close();

            // Fetch data as an associative array
            $fetch_product_count_Stmt_ResultAssoc= $fetch_product_count_Stmt_Result->fetch_assoc();

            if (intval($cartQuantity) > intval($fetch_product_count_Stmt_ResultAssoc['product_count'])) {
                header('Location: cart?error=exessQty');
                // Exit the script to prevent further execution
                exit();
            }

            $product_total_selling_price = intval($cartQuantity) * intval($fetch_product_count_Stmt_ResultAssoc['product_selling_price']);

            // Prepare a SQL query to update quantity
            $updateCart_product_quantity_Sql = 'UPDATE `cart` SET `quantity`=?,`product_total_selling_price`=? WHERE `user_id`=? AND `product_code`=?';

            // Prepare the SQL statement
            $updateCart_product_quantity_Stmt = $dbConnection->prepare($updateCart_product_quantity_Sql);

            // Bind parameters to the prepared statement
            $updateCart_product_quantity_Stmt->bind_param('ssss',$cartQuantity,$product_total_selling_price,$user_id,$itemID);

            // Execute the prepared statement
            if ($updateCart_product_quantity_Stmt->execute()) {
                // redirect to the cart page
                header('Location: cart?sts=success');
                // Exit the current script to prevent further execution
                exit();
            } else {
                // redirect to the cart page
                header('Location: cart?sts=failed');
                // Exit the current script to prevent further execution
                exit();
            }
        }

        // Close the $fetchCart_product_code_Stmt prepared statement to free up resources
        $fetchCart_product_code_Stmt->close();
    }
?>
<!-- Declare the document type as HTML -->
<!DOCTYPE html>
<!-- Specify the language of the document as English -->
<html lang="en">
    <!-- Head section of the HTML document -->
    <head>
        <title><?php echo $fetchCompanyDataAssoc['company_name']; ?> | Cart</title>
        <?php require_once 'head.php'; ?>
    </head>
    <body style="margin-bottom: 160px; background-color: #dee2e6;">
        <?php require_once 'navbar.php'; ?>
        <section class="mt-2 container">
            <div class="row">
                <div class="bg-light p-2 border-right col-sm-12 col-md-6 col-lg-6 col-xl-6">
                    <?php
                        // Prepare a SQL statement to fetch product_code from the 'cart' table
                        $fetchProductsInCartSql = 'SELECT * FROM `cart` WHERE `user_id`=?';

                        // Prepare the SQL statement for execution
                        $fetchProductsInCartStmt = $dbConnection->prepare($fetchProductsInCartSql);

                        // Bind the user ID to the prepared statement
                        $fetchProductsInCartStmt->bind_param('s', $_SESSION['user_id']);

                        // Execute the prepared SQL statement
                        $fetchProductsInCartStmt->execute();

                        // Fetch the result set from the executed SQL statement
                        $fetchProductsInCartResult = $fetchProductsInCartStmt->get_result();

                        // Close the $fetchProductsInCartStmt prepared statement to free up resources
                        $fetchProductsInCartStmt->close();
                        if ($fetchProductsInCartResult->num_rows <= 0 ) {
                    ?>
                    There are no items in your cart
                    <div class="mt-2">
                    <a href="index" target="_self" class="text-dark text-decoration-underlined">Continue shopping</a>
                    </div>
                    <?php }else { while ($fetchProductsInCartStmtAssoc= $fetchProductsInCartResult->fetch_assoc()) { ?>
                        <div cLass="card m-2">
                            <div class="card-header">
                                <?php
                                    // Define the SQL query to select the product details name from the `products` table based on the provided product code
                                    $selectProductsSaleDetailsSql = 'SELECT `product_code`,`product_name`,`product_description`,`product_selling_price`,`product_count` FROM `products` WHERE `product_code`=? ';
                                                            
                                    // Prepare the SQL statement
                                    $selectProductsSaleDetailsStmt = $dbConnection->prepare($selectProductsSaleDetailsSql);

                                    // Bind parameters to the prepared statement
                                    $selectProductsSaleDetailsStmt->bind_param('s', $fetchProductsInCartStmtAssoc['product_code']);

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
                                    $selectProductImageStmt->bind_param('s', $fetchProductsInCartStmtAssoc['product_code']);

                                    // Execute the prepared statement
                                    $selectProductImageStmt->execute();

                                    // Get the result set from the executed statement
                                    $selectProductImageResult = $selectProductImageStmt->get_result();

                                    // Close the $selectProductImageStmt prepared statement to free up resources
                                    $selectProductImageStmt->close();

                                    // Fetch data as an associative array
                                    $selectProductImageAssoc= $selectProductImageResult->fetch_assoc();
                                ?>
                                <h6><?php echo $selectProductsSaleDetailsAssoc['product_name']; ?></h6>
                                <div class="row">
                                    <div class="col-3 col-12-sm col-6-md col-6-lg col-6-xl">
                                        <?php
                                            if (empty($selectProductImageAssoc['product_image_name'])) {
                                                $image = $fetchCompanyDataAssoc['company_logo']; 
                                                $imgClass = 'd-block bg-dark';
                                                $imgStyle = 'object-fit:contain;';
                                            } elseif (!file_exists('userProfilePictures/'.$selectProductImageAssoc['product_image_name'])) {
                                                $image = 'productsImages/'.$selectProductImageAssoc['product_image_name'];
                                                $imgClass = 'd-block bg-dark';
                                                $imgStyle = 'object-fit:cover;';
                                            }else {
                                                $image = $fetchCompanyDataAssoc['company_logo'];
                                                $imgClass = 'd-block bg-dark';
                                                $imgStyle = 'object-fit:contain;';
                                            }
                                        ?>
                                        <img src="<?php echo $image; ?>" height="100px" width="100px" style="<?php echo $imgStyle; ?>" class="<?php echo $imgClass; ?>" alt="<?php echo $image; ?>">
                                    </div>
                                    <div class="col-6 col-12-sm col-6-md col-6-lg col-6-xl">
                                        <?php echo $fetchProductsInCartStmtAssoc['product_selling_price'].' X '.$fetchProductsInCartStmtAssoc['quantity'];?>
                                        <br>
                                        <strong>
                                            <?php $totalSellingPrice = floatval($fetchProductsInCartStmtAssoc['product_selling_price'])*floatval($fetchProductsInCartStmtAssoc['quantity']);?>
                                            <?php if (empty($totalSellingPrice)==true) { ?>
                                                <?php echo $currency_one.'. 0.00'; ?>
                                            <?php } elseif ($totalSellingPrice <= '1') { ?>
                                                <?php echo $currency_one.'. '.number_format($totalSellingPrice, 2); ?>
                                            <?php } elseif ($totalSellingPrice > '1') { ?>
                                                <?php echo $currency_many.'. '.number_format($totalSellingPrice, 2); ?>
                                            <?php } ?>
                                        </strong>
                                        <br>
                                        <div class="btn-group mt-2">
                                            <button type="button" style="width: 100px !important;" data-toggle="modal" data-target="#remove<?php echo $fetchProductsInCartStmtAssoc['product_code']; ?>" class="text-nowrap text-light btn btn-sm ml-1 rounded btn-danger">Remove<i class="ml-1 fas fa-trash-alt"></i></button>
                                            <button type="button" style="width: 100px !important;" data-toggle="modal" data-target="#edit<?php echo $fetchProductsInCartStmtAssoc['product_code']; ?>" class="text-nowrap text-light btn btn-sm ml-5 rounded btn-primary">Edit<i class="ml-1 fas fa-edit"></i></button>
                                            <!-- remove item from cart modal -->
                                            <div class="modal fade" id="remove<?php echo $fetchProductsInCartStmtAssoc['product_code']; ?>" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="remove<?php echo $fetchProductsInCartStmtAssoc['product_code']; ?>Label" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-scrollable">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="remove<?php echo $fetchProductsInCartStmtAssoc['product_code']; ?>Label">
                                                                Remove item
                                                            </h5>
                                                        </div>
                                                        <form action="" method="post" onsubmit="return removeItemJSValidation()">
                                                            <div class="modal-body">
                                                                <input type="text" name="itemID" id="itemID" value="<?php echo $fetchProductsInCartStmtAssoc['product_code']; ?>" class="d-none" hidden>
                                                                Are you sure you want to remove <strong><?php echo $selectProductsSaleDetailsAssoc['product_name']; ?></strong> from your cart?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <div class="container">
                                                                    <button type="button" name="closeModal" id="closeModal" style="width: 100px !important;float: left !important;" class="d-block text-light btn btn-sm btn-primary" data-dismiss="modal">No</button>
                                                                    <span style="width: 100px !important;float: right !important;" name="removeItemLoader" id="removeItemLoader" class="d-none text-light btn btn-sm btn-danger">
                                                                        <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                                                    </span>
                                                                    <button type="submit" name="removeItem" id="removeItem" style="width: 100px !important;float: right !important;" class="d-block text-light btn btn-sm btn-danger">Yes</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <script>
                                                            function removeItemJSValidation() {
                                                                // Initialize a flag to track if the input is valid
                                                                var is_input_valid = true;

                                                                document.getElementById('removeItem').className = "d-none";
                                                                document.getElementById('removeItemLoader').style = "width: 100px !important;float: right !important;";
                                                                document.getElementById('removeItemLoader').className = "d-block text-light btn btn-sm btn-danger";

                                                                // Return the validation result
                                                                return is_input_valid;
                                                            }
                                                        </script>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- edit item in cart modal -->
                                            <div class="modal fade" id="edit<?php echo $fetchProductsInCartStmtAssoc['product_code']; ?>" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="edit<?php echo $fetchProductsInCartStmtAssoc['product_code']; ?>Label" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-scrollable">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="edit<?php echo $fetchProductsInCartStmtAssoc['product_code']; ?>Label">
                                                                Edit <?php echo $selectProductsSaleDetailsAssoc['product_name']; ?>
                                                            </h5>
                                                        </div>
                                                        <form action="" method="post" onsubmit="return editItemJSValidation()">
                                                            <div class="modal-body">
                                                                <input type="text" name="itemID" id="itemID" value="<?php echo $fetchProductsInCartStmtAssoc['product_code']; ?>" class="d-none" hidden>
                                                                <strong><?php echo $selectProductsSaleDetailsAssoc['product_name']; ?></strong>
                                                                <span id="cartQuantityStatus"></span>
                                                                <input type="tel" name="cartQuantity" id="cartQuantity" placeholder="Quantity" class="mt-2 mb-2 form-control form-control-sm" value="<?php echo $fetchProductsInCartStmtAssoc['quantity']; ?>" autocomplete="off">
                                                            </div>
                                                            <div class="modal-footer">
                                                                <div class="container">
                                                                    <button type="button" name="closeModal" id="closeModal" style="width: 100px !important;float: left !important;" class="d-block text-light btn btn-sm btn-secondary" data-dismiss="modal">No</button>
                                                                    <span style="width: 100px !important;float: right !important;" name="editItemLoader" id="editItemLoader" class="d-none text-light btn btn-sm btn-primary">
                                                                        <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                                                    </span>
                                                                    <button type="submit" name="editItem" id="editItem" style="width: 100px !important;float: right !important;" class="d-block text-light btn btn-sm btn-primary">Yes</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <script>
                                                            function editItemJSValidation() {
                                                                // Initialize a flag to track if the input is valid
                                                                var is_input_valid = true;

                                                                document.getElementById('editItem').className = "d-none";
                                                                document.getElementById('editItemLoader').style = "width: 100px !important;float: right !important;";
                                                                document.getElementById('editItemLoader').className = "d-block text-light btn btn-sm btn-primary";

                                                                if (document.getElementById('cartQuantity').value === '') {
                                                                    is_input_valid = false;
                                                                    document.getElementById('cartQuantity').style = "border: 1px solid #dc3545;";
                                                                    document.getElementById('cartQuantityStatus').style = "color: #dc3545; display: block;";
                                                                    document.getElementById('cartQuantityStatus').innerHTML = "Please enter the quantity"

                                                                    document.getElementById('editItemLoader').className = "d-none";
                                                                    document.getElementById('editItem').style = "width: 100px !important;float: right !important;";
                                                                    document.getElementById('editItem').className = "d-block text-light btn btn-sm btn-primary";
                                                                // } else if ( /^\d+$/.test(document.getElementById('cartQuantity').value) === false) {
                                                                //     is_input_valid = false;
                                                                //     document.getElementById('cartQuantity').style = "border: 1px solid #dc3545;";
                                                                //     document.getElementById('cartQuantityStatus').style = "color: #dc3545; display: block;";
                                                                //     document.getElementById('cartQuantityStatus').innerHTML = "Please enter a valid number"

                                                                //     document.getElementById('editItemLoader').className = "d-none";
                                                                //     document.getElementById('editItem').style = "width: 100px !important;float: right !important;";
                                                                //     document.getElementById('editItem').className = "d-block text-light btn btn-sm btn-primary";
                                                                } else if (parseInt(document.getElementById('cartQuantity').value) < 1) {
                                                                    is_input_valid = false;
                                                                    document.getElementById('cartQuantity').style = "border: 1px solid #dc3545;";
                                                                    document.getElementById('cartQuantityStatus').style = "color: #dc3545; display: block;";
                                                                    document.getElementById('cartQuantityStatus').innerHTML = "Quantity should be greater than 1"

                                                                    document.getElementById('editItemLoader').className = "d-none";
                                                                    document.getElementById('editItem').style = "width: 100px !important;float: right !important;";
                                                                    document.getElementById('editItem').className = "d-block text-light btn btn-sm btn-primary";
                                                                }

                                                                // Return the validation result
                                                                return is_input_valid;
                                                            }
                                                        </script>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } } ?>
                </div>
                <div class="pt-2 p-2 bg-light col-sm-12 col-md-6 col-lg-6 col-xl-6">
                    <?php
                        $selectTotalPriceSql = 'SELECT SUM(`product_total_selling_price`) AS `product_total_selling_price` FROM `cart` WHERE `user_id`=?';
                        // Prepare the SQL statement
                        $selectTotalPriceStmt = $dbConnection->prepare($selectTotalPriceSql);
                        
                        // Bind the current user's user_id session variable to the SQL SELECT statement
                        $selectTotalPriceStmt->bind_param('s', $_SESSION['user_id']);

                        // Execute the prepared statement
                        $selectTotalPriceStmt->execute();

                        // Retrieve the result set
                        $selectTotalPriceResult = $selectTotalPriceStmt->get_result();
                        
                        // Close the prepared statement to free up resources
                        $selectTotalPriceStmt->close();

                        // Fetch data as an associative array
                        $selectTotalPriceAssoc= $selectTotalPriceResult->fetch_assoc();
                    ?>
                    <div>
                        <h5>Order summary</h5>
                        <hr>
                        <?php if (empty($selectTotalPriceAssoc['product_total_selling_price'])==true) { ?>
                            Total:<strong class="ml-2 font-weight-bold"><?php echo $currency_one.'. 0.00'; ?></strong>
                        <?php } elseif ($selectTotalPriceAssoc['product_total_selling_price'] <= '1') { ?>
                            Total:<strong class="ml-2 font-weight-bold"><?php echo $currency_one.'. '.number_format($selectTotalPriceAssoc['product_total_selling_price'], 2); ?></strong>
                        <?php } elseif ($selectTotalPriceAssoc['product_total_selling_price'] > '1') { ?>
                            Total:<strong class="ml-2 font-weight-bold"><?php echo $currency_many.'. '.number_format($selectTotalPriceAssoc['product_total_selling_price'], 2); ?></strong>
                        <?php } ?>
                    </div>
                    <div class="text-center mt-5">
                       <form action="" method="post" onsubmit="return checkoutJsValidation();">
                        <?php if ($fetchProductsInCartResult->num_rows <= 0) { ?>
                            <span name="emptyCart" id="emptyCart" class="btn btn-block btn-dark text-light disabled" style="cursor:not-allowed">Checkout</span>
                        <?php } else { ?>
                            <button type="submit" name="checkoutBtn" id="checkoutBtn" class="btn btn-block btn-dark text-light">Checkout</button>
                        <?php } ?>
                            <span name="checkoutBtnLoader" id="checkoutBtnLoader" class="btn btn-block btn-dark text-light d-none">
                                <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                            </span>
                       </form>
                       <script>
                            function checkoutJsValidation() {
                                // Set the value of is_input_valid to be true by default
                                var is_input_valid = true;

                                document.getElementById('checkoutBtn').className = "d-none";
                                document.getElementById('checkoutBtnLoader').className = "d-block btn btn-dark btn-block";

                                // Return the value of is_input_valid
                                return is_input_valid;
                            }
                       </script>
                       <div class="mt-2">
                        <a href="index" target="_self" class="text-dark text-decoration-none"><i class="fas fa-angle-left mr-1"></i>Continue shopping</a>
                       </div>
                    </div>
                </div>
            </div>
            <div class="mt-2 p-4 container-fluid bg-secondary">
                <span class="text-light">You may also like</span>
                <div class="row">
                    <?php
                        $product_status = 6;

                        $selectProductsSql = 'SELECT `product_code`,`product_name`,`product_description`,`product_selling_price`,`product_count` FROM `products` WHERE `product_status`=? ORDER BY RAND() LIMIT 3';
                        
                        // Prepare the SQL statement
                        $selectProductsStmt = $dbConnection->prepare($selectProductsSql);

                        // Bind parameters to the prepared statement
                        $selectProductsStmt->bind_param('s',$product_status);

                        // Execute the prepared statement
                        $selectProductsStmt->execute();

                        // Retrieve the result set
                        $selectProductsResult = $selectProductsStmt->get_result();

                        // Close the $selectProductsStmt prepared statement to free up resources
                        $selectProductsStmt->close();

                    ?>
                    <!-- Fetch data as an associative array -->
                    <?php while ($selectProductsAssoc= $selectProductsResult->fetch_assoc()) { ?>
                        <a href="details?item=<?php echo $selectProductsAssoc['product_code']; ?>" class="text-decoration-none text-dark col-12 col-sm-6 col-md-6 col-lg-4 col-xl-4 mt-1">
                            <div class="card" style="width:250px;">
                                <?php
                                    // Define the SQL query to select the product image name from the products_images table
                                    // where the product code matches the given product code, limiting the results to 1
                                    $selectProductImageSql = 'SELECT `product_image_name` FROM `products_images` WHERE `product_code`=? LIMIT 1';

                                    // Prepare the SQL statement for execution
                                    $selectProductImageStmt = $dbConnection->prepare($selectProductImageSql);

                                    // Bind the product code parameter to the prepared statement
                                    // $selectProductsAssoc['product_code'] contains the product code value to be used in the query
                                    $selectProductImageStmt->bind_param('s', $selectProductsAssoc['product_code']);

                                    // Execute the prepared statement to perform the query on the database
                                    $selectProductImageStmt->execute();

                                    // Retrieve the result set from the executed query
                                    $selectProductImageResult = $selectProductImageStmt->get_result();

                                    // Close the $selectProductImageStmt prepared statement to free up resources
                                    $selectProductImageStmt->close();

                                    // Fetch the first row from the result set as an associative array
                                    // This will contain the product image name for the given product code
                                    $selectProductImageAssoc= $selectProductImageResult->fetch_assoc();

                                    // Define the SQL query to select the product code from the wishlist table
                                    // where the product code matches the given product code, limiting the results to 1
                                    $checkProductWishlistAvailabilitySql = 'SELECT `product_code` FROM `wishlist` WHERE `user_id`=? AND `product_code`=? LIMIT 1';

                                    // Prepare the SQL statement for execution
                                    $checkProductWishlistAvailabilityStmt = $dbConnection->prepare($checkProductWishlistAvailabilitySql);

                                    // Bind the product code parameter to the prepared statement
                                    // $selectProductsAssoc['product_code'] contains the product code value to be used in the query
                                    $checkProductWishlistAvailabilityStmt->bind_param('ss', $_SESSION['user_id'],$selectProductsAssoc['product_code']);

                                    // Execute the prepared statement to perform the query on the database
                                    $checkProductWishlistAvailabilityStmt->execute();

                                    // Retrieve the result set from the executed query
                                    $checkProductWishlistAvailabilityResult = $checkProductWishlistAvailabilityStmt->get_result();

                                    // Close the $checkProductWishlistAvailabilityStmt prepared statement to free up resources
                                    $checkProductWishlistAvailabilityStmt->close();

                                    if (empty($selectProductImageAssoc['product_image_name'])) {
                                        $image = $fetchCompanyDataAssoc['company_logo']; 
                                        $imgClass = 'card-img-top bg-dark';
                                    } elseif (!file_exists('userProfilePictures/'.$selectProductImageAssoc['product_image_name'])) {
                                        $image = 'productsImages/'.$selectProductImageAssoc['product_image_name'];
                                        $imgClass = 'card-img-top';
                                    }else {
                                        $image = $fetchCompanyDataAssoc['company_logo']; 
                                        $imgClass = 'card-img-top bg-dark';
                                    }
                                ?>
                                <img src="<?php echo $image; ?>" height="200px" width="100px" style="object-fit:contain;" class="<?php echo $imgClass; ?>" alt="<?php echo $image; ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $selectProductsAssoc['product_name']; ?></h5>
                                    <p class="card-text font-weight-bold"><?php echo $selectProductsAssoc['product_selling_price']; ?></p>
                                </div>
                                <div class="card-footer">
                                    <div>
                                        <?php if ($checkProductWishlistAvailabilityResult->num_rows !== 1) { ?>
                                            <button type="button" class="float-left btn btn-transparent"><i class="far fa-heart fa-lg"></i></button>
                                        <?php } else { ?>
                                            <button type="button" class="float-left btn btn-transparent"><i class="fas fa-heart fa-lg" style="color: #fd1d29;"></i></button>
                                        <?php } ?>
                                        <button type="button" class="float-right btn btn-transparent border border-dark">Add to cart <i class="fas fa-cart-plus fa-flip-horizontal"></i></button>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </section>
        <?php require_once 'footer.php'; ?>
        <?php require_once 'foot.php'; ?>
    </body>
</html>
<?php $dbConnection->close(); // Close the database connection ?>