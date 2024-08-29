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
    
    // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters
    $expenseId = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_GET['id']), ENT_QUOTES, 'UTF-8')));;

    if (!isset($expenseId)) {
        // start session
        session_start();
        // unset session
        session_unset();
        // destroy session
        session_destroy();  
        echo '<script>window.close()</script>';
    }

    // SQL SELECT query with expense_id placeholder
    $selectExpenseIdSql = 'SELECT * FROM `expenses` WHERE `expense_id`=?';
    // Prepare the SQL SELECT statement
    $selectExpenseIdStmt = $dbConnection->prepare($selectExpenseIdSql);
    // Bind parameters to the prepared statement
    $selectExpenseIdStmt->bind_param('s',$expenseId);
    // Execute the prepared statement
    $selectExpenseIdStmt->execute();
    // Retrieve the result set
    $selectExpenseIdStmtResult = $selectExpenseIdStmt->get_result();
    // Close the $selectExpenseIdStmt prepared statement to free up resources
    $selectExpenseIdStmt->close();
    // Fetch data as an associative array
    $selectExpenseIdStmtAssoc= $selectExpenseIdStmtResult->fetch_assoc();
    if (strval($selectExpenseIdStmtResult->num_rows) !== '1') {
        echo '<script>window.close()</script>';
    }

    // SQL SELECT query with expense_id placeholder
    $selectExpenseIdDataSql = 'SELECT * FROM `received_invoice_products` WHERE `expense_id`=?';
    // Prepare the SQL SELECT statement
    $selectExpenseIdDataStmt = $dbConnection->prepare($selectExpenseIdDataSql);
    // Bind parameters to the prepared statement
    $selectExpenseIdDataStmt->bind_param('s',$expenseId);
    // Execute the prepared statement
    $selectExpenseIdDataStmt->execute();
    // Retrieve the result set
    $selectExpenseIdDataStmtResult = $selectExpenseIdDataStmt->get_result();
    // Close the $selectExpenseIdDataStmt prepared statement to free up resources
    $selectExpenseIdDataStmt->close();
    // Fetch data as an associative array
    $selectExpenseIdDataStmtAssoc= $selectExpenseIdDataStmtResult->fetch_assoc();
    if (strval($selectExpenseIdDataStmtResult->num_rows) !== '1') {
        echo '<script>window.close()</script>';
    }

    // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters
    $supplierId = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($selectExpenseIdDataStmtAssoc['invoice_supplier']), ENT_QUOTES, 'UTF-8')));;
    // SQL SELECT query with supplier_id placeholder
    $selectSupplierDetailsIdSql = 'SELECT * FROM `suppliers` WHERE `supplier_id`=?';
    // Prepare the SQL SELECT statement
    $selectSupplierDetailsIdStmt = $dbConnection->prepare($selectSupplierDetailsIdSql);
    // Bind parameters to the prepared statement
    $selectSupplierDetailsIdStmt->bind_param('s',$supplierId);
    // Execute the prepared statement
    $selectSupplierDetailsIdStmt->execute();
    // Retrieve the result set
    $selectSupplierDetailsIdStmtResult = $selectSupplierDetailsIdStmt->get_result();
    // Close the $selectSupplierDetailsIdStmt prepared statement to free up resources
    $selectSupplierDetailsIdStmt->close();
    // Fetch data as an associative array
    $selectSupplierDetailsIdStmtAssoc= $selectSupplierDetailsIdStmtResult->fetch_assoc();

    // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters
    $receivedById = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($selectExpenseIdDataStmtAssoc['invoice_recipient']), ENT_QUOTES, 'UTF-8')));
    // SQL SELECT query with receivedBy_id placeholder
    $selectreceivedByDetailsIdSql = 'SELECT `user_id`,`user_first_name`,`user_middle_name`,`user_last_name` FROM `users` WHERE `user_id`=?';
    // Prepare the SQL SELECT statement
    $selectreceivedByDetailsIdStmt = $dbConnection->prepare($selectreceivedByDetailsIdSql);
    // Bind parameters to the prepared statement
    $selectreceivedByDetailsIdStmt->bind_param('s',$receivedById);
    // Execute the prepared statement
    $selectreceivedByDetailsIdStmt->execute();
    // Retrieve the result set
    $selectreceivedByDetailsIdStmtResult = $selectreceivedByDetailsIdStmt->get_result();
    // Close the $selectreceivedByDetailsIdStmt prepared statement to free up resources
    $selectreceivedByDetailsIdStmt->close();
    // Fetch data as an associative array
    $selectreceivedByDetailsIdStmtAssoc= $selectreceivedByDetailsIdStmtResult->fetch_assoc();
    
?>
<!-- Declare the document type as HTML -->
<!DOCTYPE html>
<!-- Specify the language of the document as English -->
<html lang="en">
    <!-- Head section of the HTML document -->
    <head>
        <script>document.addEventListener("keydown", function(event) {if (event.key === "Escape") {window.close()}});</script>
        <title><?php echo $fetchCompanyDataAssoc['company_name'] ?> | Expense <?php echo htmlspecialchars_decode($selectExpenseIdStmtAssoc['expense_id']); ?></title>
        <?php require 'header.php'; ?>
    </head>
    <style>
        html {position:relative;min-height:100%;}
        body {margin-bottom:100px;min-height:100%;}
        img {object-fit:contain;}
        #expenseID{font-size:20px;}
        footer {position:absolute;bottom:0;width:100%;height:100px;}
    </style>
    <body>
        <nav class="navbar navbar-dark bg-dark">
            <div class="mx-auto" onclick="window.location.reload();">
                <!-- link to the brand logo -->
                <img src="../<?php echo $fetchCompanyDataAssoc['company_logo']; ?>" width="100%" height="30" class="d-inline-block align-top" alt="<?php echo $fetchCompanyDataAssoc['company_name']; ?>" loading="lazy">
                <!-- image of the brand logo with lazy loading -->
                <!-- Gelish Building Solutions -->
                <!-- brand name text -->
            </div>
        </nav>
        <section>
            <div class="container mt-2">
                <h5 class="text-center font-weight-bold">
                    Expense <code id="expenseID"><?php echo htmlspecialchars_decode($selectExpenseIdStmtAssoc['expense_id']); ?></code> details
                </h5>
                <hr style="border-top: 1px solid #000000;">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                        <p class="p-0 m-0">Id: <strong><?php if (empty($selectExpenseIdStmtAssoc['expense_id'])==true) { ?>Not set<?php }else{ ?><?php echo htmlspecialchars_decode($selectExpenseIdStmtAssoc['expense_id']); ?><?php } ?></strong></p>
                        <p class="p-0 m-0">Date: <strong><?php if (empty($selectExpenseIdStmtAssoc['expense_date'])==true) { ?>Not set<?php }else{ ?><?php echo htmlspecialchars_decode(date('l jS F Y',strtotime($selectExpenseIdStmtAssoc['expense_date']))); ?><?php } ?></strong></p>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                        <p class="p-0 m-0">Supplier: <strong><?php if (empty($selectSupplierDetailsIdStmtAssoc['supplier_name'])==true) { ?>Not set<?php }else{ ?><?php echo htmlspecialchars_decode($selectSupplierDetailsIdStmtAssoc['supplier_name']); ?><?php } ?></strong></p>
                        <p class="p-0 m-0">Received by: <strong><?php if (empty($selectreceivedByDetailsIdStmtAssoc['user_first_name'])==true) { ?>Not set<?php }else{ ?><?php echo htmlspecialchars_decode($selectreceivedByDetailsIdStmtAssoc['user_first_name'].'('.$selectreceivedByDetailsIdStmtAssoc['user_id'].')'); ?><?php } ?></strong></p>
                    </div>
                </div>
                <hr style="border-top: 1px solid #000000;">
                <div class="card-body table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl">
                    <table class="text-nowrap table table-bordered table-hover table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>Product code</th>
                                <th>Product name</th>
                                <th>Quantity</th>
                                <th>Unit buying price</th>
                                <th>Total buying price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                // SQL SELECT query with expense_id placeholder
                                $selectExpenseIdProductsSql = 'SELECT * FROM `received_invoice_products` WHERE `expense_id`=?';
                                // Prepare the SQL SELECT statement
                                $selectExpenseIdProductsStmt = $dbConnection->prepare($selectExpenseIdProductsSql);
                                // Bind parameters to the prepared statement
                                $selectExpenseIdProductsStmt->bind_param('s',$expenseId);
                                // Execute the prepared statement
                                $selectExpenseIdProductsStmt->execute();
                                // Retrieve the result set
                                $selectExpenseIdProductsStmtResult = $selectExpenseIdProductsStmt->get_result();
                                // Close the $selectExpenseIdProductsStmt prepared statement to free up resources
                                $selectExpenseIdProductsStmt->close();
                                while ($selectExpenseIdProductsStmtAssoc= $selectExpenseIdProductsStmtResult->fetch_assoc()) {
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars_decode($selectExpenseIdProductsStmtAssoc['product_code']); ?></td>
                                    <td><?php echo htmlspecialchars_decode($selectExpenseIdProductsStmtAssoc['product_name']); ?></td>
                                    <td><?php echo htmlspecialchars_decode($selectExpenseIdProductsStmtAssoc['product_quantity']); ?></td>
                                    <td><?php echo htmlspecialchars_decode(number_format($selectExpenseIdProductsStmtAssoc['product_buying_price'],2)); ?></td>
                                    <td><?php echo htmlspecialchars_decode(number_format($selectExpenseIdProductsStmtAssoc['product_total_buying_price'],2)); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        <footer class="bg-dark bd-footer bg-body-tertiary d-flex flex-wrap justify-content-between align-items-center">
            <p class="text-light col-md-4 mb-0 text-body-secondary">&copy; <?php echo date('Y'); ?>  <?php echo $fetchCompanyDataAssoc['company_name']; ?></p>
            <ul class="nav col-md-4 justify-content-end">
                <li class="nav-item">
                    <span class="nav-link px-2 text-body-secondary">
                        <img src="../<?php echo $fetchCompanyDataAssoc['company_logo']; ?>" width="100%" height="30" class="d-inline-block align-top" alt="<?php echo $fetchCompanyDataAssoc['company_name']; ?>" loading="lazy">
                    </span>
                </li>
            </ul>
        </footer>
        <?php require 'footer.php'; ?>
    </body>
</html>
<?php $dbConnection->close(); // Close the database connection ?>