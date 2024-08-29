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
?>
<!-- Declare the document type as HTML -->
<!DOCTYPE html>
<!-- Specify the language of the document as English -->
<html lang="en">
    <!-- Head section of the HTML document -->
    <head>
        <title><?php echo $fetchCompanyDataAssoc['company_name']; ?> | Awaiting Cash Payment</title>
        <?php require_once 'head.php'; ?>
    </head>
    <body style="margin-bottom: 160px; background-color: #dee2e6;">
        <?php require_once 'navbar.php'; ?>
        <section class="mt-2 container">
            <div class="card">
                <div class="card-body">
                    <?php
                        $status = 'awaiting';
                        $selectAwaitingPaymentSql = 'SELECT `user_id`, `product_code`, `quantity`, `product_selling_price`, `product_total_selling_price`, `date`, `phone`, `status` FROM `cash_on_delivery` WHERE `user_id`=? AND `status`=? GROUP BY `user_id`';
                        // Prepare the SQL statement
                        $selectAwaitingPaymentStmt = $dbConnection->prepare($selectAwaitingPaymentSql);
                        
                        // Bind the current user's user_id session variable to the SQL SELECT statement
                        $selectAwaitingPaymentStmt->bind_param('ss', $_SESSION['user_id'],$status);
                
                        // Execute the prepared statement
                        $selectAwaitingPaymentStmt->execute();
                
                        // Retrieve the result set
                        $selectAwaitingPaymentResult = $selectAwaitingPaymentStmt->get_result();

                        // Close the prepared statement to free up resources
                        $selectAwaitingPaymentStmt->close();

                        $status = 'awaiting';
                        $selectSumAwaitingPaymentSql = 'SELECT SUM(`product_total_selling_price`) AS `product_total_selling_price` FROM `cash_on_delivery` WHERE `user_id`=? AND `status`=? GROUP BY `user_id`';
                        // Prepare the SQL statement
                        $selectSumAwaitingPaymentStmt = $dbConnection->prepare($selectSumAwaitingPaymentSql);
                        
                        // Bind the current user's user_id session variable to the SQL SELECT statement
                        $selectSumAwaitingPaymentStmt->bind_param('ss', $_SESSION['user_id'],$status);

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
                    <div class="table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl">
                        <table class="text-nowrap table table-hover table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($selectAwaitingPaymentResult->num_rows <= 0 ) { ?>
                                    <tr class="text-center">
                                        <td colspan="3" class="text-center">There is no order waiting payment</td>
                                    </tr>
                                <?php } else { while ($selectAwaitingPaymentAssoc= $selectAwaitingPaymentResult->fetch_assoc()) { ?>
                                    <tr>
                                        <td class="text-center"><?php echo $total; ?></td>
                                        <td class="text-center"><?php echo $selectAwaitingPaymentAssoc['date']; ?></td>
                                        <td class="text-center">
                                            <?php
                                                if (strval($selectAwaitingPaymentAssoc['status']) === 'awaitin') {
                                                    echo 'Awaiting cash payment';
                                                } else {
                                                    echo strval($selectAwaitingPaymentAssoc['status']);
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                <?php }} ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
        <?php require_once 'footer.php'; ?>
        <?php require_once 'foot.php'; ?>
    </body>
</html>
<?php $dbConnection->close(); // Close the database connection ?>