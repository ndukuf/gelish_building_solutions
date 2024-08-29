<?php
    /* Require necessary files for the application to function properly. */
    //establishes a connection to the database.
    require '../database_connection.php';

    //contains company-related data.
    require '../company_data.php';

    //provides the current date and time.
    require '../current_date_and_time.php';

    //connect to 00_validate_admin.
    require '00_validate_admin.php';

    if (!isset($_GET['user']) || empty($_GET['user'])) {
        // Redirect the user to the orders page
        header('Location: orders?tsk=awaiting_cash_payment');
        exit();
    }

    // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the user input
    $get_user_id = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_GET['user']), ENT_QUOTES, 'UTF-8')));

    // SQL SELECT statement
    $selectUserSql = 'SELECT `user_id` FROM `users` WHERE `user_id`=?';

    // Prepare the SQL statement
    $selectUserStmt = $dbConnection->prepare($selectUserSql);
    
    // Bind the current user's user_id session variable to the SQL SELECT statement
    $selectUserStmt->bind_param('s', $get_user_id);

    // Execute the prepared statement
    $selectUserStmt->execute();

    // Retrieve the result set
    $selectUserResult = $selectUserStmt->get_result();

    // Close the prepared statement to free up resources
    $selectUserStmt->close();

    // Fetch data as an associative array
    $selectUserAssoc = $selectUserResult->fetch_assoc();

    if (intval($selectUserResult->num_rows) !== 1) {
        // Redirect the user to the orders page
        header('Location: orders?tsk=awaiting_cash_payment');
        exit();
    }

    // SQL SELECT statement
    $select_COD_UserSql = 'SELECT `user_id` FROM `cash_on_delivery` WHERE `user_id`=?';

    // Prepare the SQL statement
    $select_COD_UserStmt = $dbConnection->prepare($select_COD_UserSql);
    
    // Bind the current user's user_id session variable to the SQL SELECT statement
    $select_COD_UserStmt->bind_param('s', $get_user_id);

    // Execute the prepared statement
    $select_COD_UserStmt->execute();

    // Retrieve the result set
    $select_COD_UserResult = $select_COD_UserStmt->get_result();

    // Close the prepared statement to free up resources
    $select_COD_UserStmt->close();

    // Fetch data as an associative array
    $select_COD_UserAssoc = $select_COD_UserResult->fetch_assoc();

    if (intval($select_COD_UserResult->num_rows) < 1) {
        // Redirect the user to the orders page
        header('Location: orders?tsk=awaiting_cash_payment');
        exit();
    }

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'competeTheCashOrder' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['competeTheCashOrder']) == true) {
        // SQL INSERT statement to add the products from `invoice_reception_products` table to the `paid_cash_on_delivery` table
        $addOrderPaymentSql = 'INSERT INTO `paid_cash_on_delivery`(`user_id`, `product_code`, `quantity`, `product_selling_price`, `product_total_selling_price`, `order_date`, `phone`, `status`)
        SELECT `user_id`, `product_code`, `quantity`, `product_selling_price`, `product_total_selling_price`, `date`, `phone`, `status` FROM `cash_on_delivery` WHERE `user_id`=?';
        
        // Prepare the SQL Insert statement
        $addOrderPaymentStmt = $dbConnection->prepare($addOrderPaymentSql);

        // Bind $get_user_id as a parameter to the prepared statement
        $addOrderPaymentStmt->bind_param('s',$get_user_id);

        // If the statement is executed
        if ($addOrderPaymentStmt->execute()) {
            // Retrieve the product details from the received invoice products table
            $selectDataSql = 'SELECT `product_code`,`quantity` FROM `paid_cash_on_delivery` WHERE `user_id`=?';
            $selectDataStmt = $dbConnection->prepare($selectDataSql);
            $selectDataStmt->bind_param('s',$get_user_id);
            // If the statement is executed
            if ($selectDataStmt->execute()) {
                // Fetch the result set
                $selectDataStmtResult = $selectDataStmt->get_result();
                while ($soldProductsDetailAssoc = $selectDataStmtResult->fetch_assoc()) {
                    // Extract the product details
                    $sold_product_code = $soldProductsDetailAssoc['product_code'];
                    $sold_quantity = $soldProductsDetailAssoc['quantity'];

                    // Retrieve the initial product details from the products table
                    $selectInitialProductsDetailsSql = 'SELECT `product_count` FROM `products` WHERE `product_code`=?';
                    $selectInitialProductsDetailsStmt = $dbConnection->prepare($selectInitialProductsDetailsSql);
                    $selectInitialProductsDetailsStmt->bind_param('s', $sold_product_code);
                    // If the statement is executed
                    if ($selectInitialProductsDetailsStmt->execute()) {
                        // Fetch the result set
                        $selectInitialProductsDetailsStmtResult = $selectInitialProductsDetailsStmt->get_result();

                        while ($selectInitialProductsDetailsAssoc = $selectInitialProductsDetailsStmtResult->fetch_assoc()) {
                            // Calculate the new product count
                            $newProductCount = floatval($selectInitialProductsDetailsAssoc['product_count']) - floatval($sold_quantity);

                            // Update the product details in the products table
                            $updateProductDetailsSql = 'UPDATE `products` SET `product_count`=? WHERE `product_code`=?';
                            $updateProductDetailsStmt = $dbConnection->prepare($updateProductDetailsSql);
                            $updateProductDetailsStmt->bind_param('ss',$newProductCount,$sold_product_code);
                            
                            // If the statement is executed
                            if ($updateProductDetailsStmt->execute()) {
                                // Delete the invoice reception products
                                $deleteInvoiceReceptionProductsSql = 'DELETE FROM `cash_on_delivery` WHERE `user_id`=?';
                                $deleteInvoiceReceptionProductsStmt = $dbConnection->prepare($deleteInvoiceReceptionProductsSql);
                                $deleteInvoiceReceptionProductsStmt->bind_param('s',$get_user_id);
                                $deleteInvoiceReceptionProductsStmt->execute();
                            }
                        }
                    }
                }
            }
        }
        // Redirect the user to the orders page
        header('Location: orders?tsk=awaiting_cash_payment');
        exit();
    }
?>
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title><?php echo $fetchCompanyDataAssoc['company_name']; ?> | Compele Order</title>
                <!-- require header.php php file -->
                <?php require 'header.php'; ?>
            </head>
            <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
                <div class="wrapper">
                    <!-- require topbar.php php file -->
                    <?php require 'topbar.php'; ?>
                    <!-- require sidebar.php php file -->
                    <?php require 'sidebar.php'; ?>

                    <div class="content-wrapper mb-4">
                        <div class="content-header">
                            <div class="container-fluid">
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <h1 class="m-0"><?php echo $fetchCompanyDataAssoc['company_name']; ?></h1>
                                    </div>
                                </div>
                                <hr class="border-primary">
                                <button type="button" name="previousPage" id="previousPage"class="btn btn-sm btn-primary" onclick="history.back()"><i class="fas fa-arrow-left mr-2"></i>Back</button>
                            </div>
                        </div>
                        <!-- Main content -->
                        <section class="content">
                            <div class="container-fluid">
                                <?php if (isset($_GET['user'])==true && !empty($_GET['user'])) {?>
                                    <div class="card card-outline card-dark" style="border-bottom: 3px solid #343a40;">
                                        <div class="card-header">
                                            <h6 class="text-dark font-weight-bold">Orders details</h6>
                                        </div>
                                        <div class="card-body">
                                            <?php
                                                $status = 'awaiting';
                                                $selectAwaitingPaymentSql = 'SELECT `user_id`, `product_code`, `quantity`, `product_selling_price`, `product_total_selling_price`, `date`, `phone`, `status` FROM `cash_on_delivery` WHERE `user_id`=? AND `status`=?';

                                                // Prepare the SQL statement
                                                $selectAwaitingPaymentStmt = $dbConnection->prepare($selectAwaitingPaymentSql);
                                                
                                                // Bind the current user's user_id session variable to the SQL SELECT statement
                                                $selectAwaitingPaymentStmt->bind_param('ss', $_GET['user'],$status);
                                        
                                                // Execute the prepared statement
                                                $selectAwaitingPaymentStmt->execute();
                                        
                                                // Retrieve the result set
                                                $selectAwaitingPaymentResult = $selectAwaitingPaymentStmt->get_result();

                                                // Close the prepared statement to free up resources
                                                $selectAwaitingPaymentStmt->close();
                                            ?>
                                            <table class="text-nowrap table table-bordered table-hover table-striped table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl" id="customers_lit_table">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>Product code</th>
                                                        <th>Product name</th>
                                                        <th>Quantity</th>
                                                        <th>Selling price</th>
                                                        <th>Total selling price</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (intval($selectAwaitingPaymentResult->num_rows) <= 0 ) { ?>
                                                        <tr><td colspan="4">No data available</td></tr>
                                                    <?php } else { while ($selectAwaitingPaymentAssoc= $selectAwaitingPaymentResult->fetch_assoc()) { ?>
                                                        <tr>
                                                            <td class="text-bold"><?php echo $selectAwaitingPaymentAssoc['product_code']; ?></td>
                                                            <td class="text-bold">
                                                                <?php
                                                                    // SQL SELECT statement
                                                                    $selectProductNameSql = 'SELECT `product_name` FROM `products` WHERE `product_code`=? LIMIT 1';

                                                                    // Prepare the SQL statement
                                                                    $selectProductNameStmt = $dbConnection->prepare($selectProductNameSql);
                                                                    
                                                                    // Bind the current user's user_id session variable to the SQL SELECT statement
                                                                    $selectProductNameStmt->bind_param('s', $selectAwaitingPaymentAssoc['product_code']);
                                                            
                                                                    // Execute the prepared statement
                                                                    $selectProductNameStmt->execute();
                                                            
                                                                    // Retrieve the result set
                                                                    $selectProductNameResult = $selectProductNameStmt->get_result();
                    
                                                                    // Close the prepared statement to free up resources
                                                                    $selectProductNameStmt->close();

                                                                    // Fetch data as an associative array
                                                                    $selectProductNameAssoc = $selectProductNameResult->fetch_assoc();
                                                                    if (empty($selectProductNameAssoc['product_name'])) {
                                                                ?>
                                                                Product not found
                                                                <?php } else { ?>
                                                                    <?php echo $selectProductNameAssoc['product_name']; ?>
                                                                <?php } ?>
                                                            </td>
                                                            <td class="text-bold"><?php echo $selectAwaitingPaymentAssoc['quantity']; ?></td>
                                                            <td class="text-bold"><?php echo number_format($selectAwaitingPaymentAssoc['product_selling_price'],2); ?></td>
                                                            <td class="text-bold"><?php echo number_format($selectAwaitingPaymentAssoc['product_total_selling_price'],2); ?></td>
                                                        </tr>
                                                    <?php }} ?>
                                                    <tr>
                                                        <td colspan="3"></td>
                                                        <td>Total</td>
                                                        <td class="text-bold">
                                                            <?php
                                                                $status = 'awaiting';
                                                                $selectSumAwaitingPaymentSql = 'SELECT SUM(`product_total_selling_price`) AS `product_total_selling_price` FROM `cash_on_delivery` WHERE `user_id`=? AND `status`=?';
                                                                // Prepare the SQL statement
                                                                $selectSumAwaitingPaymentStmt = $dbConnection->prepare($selectSumAwaitingPaymentSql);
                                                                
                                                                // Bind the current user's user_id session variable to the SQL SELECT statement
                                                                $selectSumAwaitingPaymentStmt->bind_param('ss',$_GET['user'],$status);
                    
                                                                // Execute the prepared statement
                                                                $selectSumAwaitingPaymentStmt->execute();
                    
                                                                // Retrieve the result set
                                                                $selectSumAwaitingPaymentResult = $selectSumAwaitingPaymentStmt->get_result();
                    
                                                                $selectSumAwaitingPaymentAssoc = $selectSumAwaitingPaymentResult->fetch_assoc();
                    
                                                                if (empty($selectSumAwaitingPaymentAssoc['product_total_selling_price'])) {
                                                                    $total = '0.00';
                                                                    echo number_format($total,2);
                                                                } else {
                                                                    $total = $selectSumAwaitingPaymentAssoc['product_total_selling_price'];
                                                                    echo number_format($total,2);
                                                                }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="card-footer">
                                            <div class="float-right">
                                                <form action="" method="post">
                                                    <button type="submit" name="competeTheCashOrder" id="competeTheCashOrder" class="btn btn-sm btn-primary text-light">Complete order</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </section>
                    </div>
                    <!-- require mainfooter.php php file -->
                    <?php require 'mainfooter.php'; ?>
                </div>
                <!-- require footer.php php file -->
                <?php require 'footer.php'; ?>
                <script>
                    $(document).ready( function (){$('#customers_list_table').DataTable();});
                </script>
            </body>
        </html>
<?php $dbConnection->close(); // Close the database connection ?>