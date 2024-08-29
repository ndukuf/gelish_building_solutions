<?php
    /* Require necessary files for the application to function properly. */
    //establishes a connection to the database.
    require 'database_connection.php';

    //contains company-related data.
    require 'company_data.php';

    //provides the current date and time.
    require 'current_date_and_time.php';

    $status = 'awaiting';
    $selectSumAwaitingPaymentSql = 'SELECT SUM(`product_total_selling_price`) AS `product_total_selling_price` FROM `cash_on_delivery` WHERE `user_id`=? AND `status`=?';
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
        echo $total = '0.00';
    } else {
        echo $total = $selectSumAwaitingPaymentAssoc['product_total_selling_price'];
    }
    
    // Close the prepared statement to free up resources
    $selectSumAwaitingPaymentStmt->close();
?>