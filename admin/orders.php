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
?>
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title><?php echo $fetchCompanyDataAssoc['company_name']; ?> | Orders</title>
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
                                <?php if (isset($_GET['tsk'])==true && $_GET['tsk']=='awaiting_cash_payment') {?>
                                    <div class="card card-outline card-dark" style="border-bottom: 3px solid #343a40;">
                                        <div class="card-header">
                                            <h6 class="text-dark font-weight-bold">Orders awaiting cash payment</h6>
                                        </div>
                                        <div class="card-body">
                                        <?php
                                            $status = 'awaiting';
                                            $selectAwaitingPaymentSql = 'SELECT `user_id`, `product_code`, `quantity`, `product_selling_price`, `product_total_selling_price`, `date`, `phone`, `status` FROM `cash_on_delivery` WHERE `status`=? GROUP BY `user_id`';
                                            // Prepare the SQL statement
                                            $selectAwaitingPaymentStmt = $dbConnection->prepare($selectAwaitingPaymentSql);
                                            
                                            // Bind the current user's user_id session variable to the SQL SELECT statement
                                            $selectAwaitingPaymentStmt->bind_param('s',$status);
                                    
                                            // Execute the prepared statement
                                            $selectAwaitingPaymentStmt->execute();
                                    
                                            // Retrieve the result set
                                            $selectAwaitingPaymentResult = $selectAwaitingPaymentStmt->get_result();

                                            // Close the prepared statement to free up resources
                                            $selectAwaitingPaymentStmt->close();

                                            $status = 'awaiting';
                                            $selectSumAwaitingPaymentSql = 'SELECT SUM(`product_total_selling_price`) AS `product_total_selling_price` FROM `cash_on_delivery` WHERE `status`=? GROUP BY `user_id`';
                                            // Prepare the SQL statement
                                            $selectSumAwaitingPaymentStmt = $dbConnection->prepare($selectSumAwaitingPaymentSql);
                                            
                                            // Bind the current user's user_id session variable to the SQL SELECT statement
                                            $selectSumAwaitingPaymentStmt->bind_param('s',$status);

                                            // Execute the prepared statement
                                            $selectSumAwaitingPaymentStmt->execute();

                                            // Retrieve the result set
                                            $selectSumAwaitingPaymentResult = $selectSumAwaitingPaymentStmt->get_result();

                                            $selectSumAwaitingPaymentAssoc = $selectSumAwaitingPaymentResult->fetch_assoc();

                                            if (empty($selectSumAwaitingPaymentAssoc['product_total_selling_price'])) {
                                                $total = '0.00';
                                            } else {
                                                $total = $selectSumAwaitingPaymentAssoc['product_total_selling_price'];
                                            }
                                            
                                            // Close the prepared statement to free up resources
                                            $selectSumAwaitingPaymentStmt->close();
                                        ?>
                                            <table class="text-nowrap table table-hover table-striped table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl" id="customers_list_table">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th class="text-center">User</th>
                                                        <th class="text-center">Total</th>
                                                        <th class="text-center">Date</th>
                                                        <th class="text-center">Status</th>
                                                        <th class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if ($selectAwaitingPaymentResult->num_rows <= 0 ) { ?>
                                                        <tr class="text-center">
                                                            <td colspan="5" class="text-center">There is no order waiting payment</td>
                                                        </tr>
                                                    <?php } else { while ($selectAwaitingPaymentAssoc= $selectAwaitingPaymentResult->fetch_assoc()) { ?>
                                                        <tr>
                                                            <td class="text-center">
                                                                <?php
                                                                    // Prepare a SQL query to fetch user data from the database
                                                                    $fetchCustomersSql = 'SELECT `user_id`,`user_first_name`,`user_last_name` FROM `users`  WHERE `user_id`=?';

                                                                    // Prepare the SQL statement
                                                                    $fetchCustomersStmt = $dbConnection->prepare($fetchCustomersSql);

                                                                    // Bind parameters to the prepared statement
                                                                    $fetchCustomersStmt->bind_param('s',$selectAwaitingPaymentAssoc['user_id']);

                                                                    // Execute the prepared statement
                                                                    $fetchCustomersStmt->execute();

                                                                    // Retrieve the result set
                                                                    $fetchCustomersResult = $fetchCustomersStmt->get_result();

                                                                    // Fetch data as an associative array
                                                                    $fetchCustomersAssoc = $fetchCustomersResult->fetch_assoc();
                                                                    echo $fetchCustomersAssoc['user_first_name']. ' ' .$fetchCustomersAssoc['user_last_name'].' ('.$fetchCustomersAssoc['user_id'].')';
                                                                ?>
                                                            </td>
                                                            <td class="text-center"><?php echo $total; ?></td>
                                                            <td class="text-center"><?php echo $selectAwaitingPaymentAssoc['date']; ?></td>
                                                            <td class="text-center">
                                                                <?php
                                                                    if (strval($selectAwaitingPaymentAssoc['status']) === 'awaiting') {
                                                                        echo 'Awaiting cash payment';
                                                                    } else {
                                                                        echo strval($selectAwaitingPaymentAssoc['status']);
                                                                    }
                                                                ?>
                                                            </td>
                                                            <td class="text-center">
                                                                <a href="complete_order?user=<?php echo $selectAwaitingPaymentAssoc['user_id']; ?>" class="text-decoration-none btn btn-sm btn-primary">Complete order</a>
                                                            </td>
                                                        </tr>
                                                    <?php }} ?>
                                                </tbody>
                                            </table>
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