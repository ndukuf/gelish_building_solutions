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
                <title><?php echo $fetchCompanyDataAssoc['company_name']; ?> | Expense</title>
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
                                <?php if (isset($_GET['tsk'])==true && $_GET['tsk']=='add_expense') { ?>
                                <?php }elseif (isset($_GET['tsk'])==true && $_GET['tsk']=='view_expenses') { ?>
                                    <div class="card card-outline card-dark" style="border-bottom: 3px solid #343a40;">
                                        <div class="card-header">
                                            <h6 class="text-dark font-weight-bold">Expense</h6>
                                        </div>
                                        <div class="card-body">
                                            <table class="text-nowrap table table-hover table-striped table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl" id="expenses_table">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th class="text-center">Expense id</th>
                                                        <th>Expense date</th>
                                                        <th>Expense amount</th>
                                                        <th>Paid to</th>
                                                        <th>Paid by</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        // Prepare a SQL query to fetch user data from the database
                                                        $fetchExpensesSql = 'SELECT `expense_id`,`expense_date`,`expense_amount`,`paid_to`,`paid_by` FROM `expenses`';

                                                        // Prepare the SQL statement
                                                        $fetchExpensesStmt = $dbConnection->prepare($fetchExpensesSql);

                                                        // Execute the prepared statement
                                                        $fetchExpensesStmt->execute();

                                                        // Retrieve the result set
                                                        $fetchExpensesResult = $fetchExpensesStmt->get_result();

                                                        // Close the prepared statement to free up resources
                                                        $fetchExpensesStmt->close();

                                                        // Fetch data as an associative array
                                                        while ($fetchExpensesAssoc= $fetchExpensesResult->fetch_assoc()) {
                                                    ?>
                                                        <tr>
                                                            <th>
                                                                <?php if (empty($fetchExpensesAssoc['expense_id'])==true) { ?> Not set <?php } else { ?> <?php echo $fetchExpensesAssoc['expense_id']; ?> <?php } ?>
                                                            </th>
                                                            <td>
                                                                <?php if (empty($fetchExpensesAssoc['expense_date'])==true) { ?> Not set <?php } else { ?> <?php echo date('jS F Y', strtotime($fetchExpensesAssoc['expense_date'])); ?> <?php } ?>
                                                            </td>
                                                            <td>
                                                                <?php if (empty($fetchExpensesAssoc['expense_amount'])==true) { ?> Not set <?php } else { ?> <?php echo number_format($fetchExpensesAssoc['expense_amount'],2); ?> <?php } ?>
                                                            </td>
                                                            <td>
                                                                <?php if (empty($fetchExpensesAssoc['paid_to'])==true) { ?> Not set <?php } else { ?> 
                                                                    <?php
                                                                        // Prepare a SQL query to fetch who was paid
                                                                        $fetchSupplierSql = 'SELECT `supplier_name` FROM `suppliers` WHERE `supplier_id`=?';

                                                                        // Prepare the SQL statement
                                                                        $fetchSupplierStmt = $dbConnection->prepare($fetchSupplierSql);
                                                                        
                                                                        // Bind the supplier is as the parameter for the SQL statement
                                                                        $fetchSupplierStmt ->bind_param('s', $fetchExpensesAssoc['paid_to']);

                                                                        // Execute the prepared statement
                                                                        $fetchSupplierStmt->execute();

                                                                        // Retrieve the result set
                                                                        $fetchSupplierResult = $fetchSupplierStmt->get_result();

                                                                        // Close the prepared statement to free up resources
                                                                        $fetchSupplierStmt->close();
                                                                        
                                                                        // Fetch data as an associative array
                                                                        $fetchSupplierAssoc= $fetchSupplierResult->fetch_assoc();
                                                                        
                                                                        if ($fetchSupplierResult->num_rows !== 1 || empty($fetchSupplierAssoc['supplier_name'])==true) { ?>
                                                                            Supplier not found
                                                                        <?php } else { ?>
                                                                            <?php echo $fetchSupplierAssoc['supplier_name']; ?>
                                                                        <?php } ?>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <?php if (empty($fetchExpensesAssoc['paid_by'])==true) { ?> Not set <?php } else { ?> 
                                                                    <?php
                                                                        // Prepare a SQL query to fetch who paid
                                                                        $fetchStaffSql = 'SELECT `user_id`,`user_first_name`,`user_middle_name`,`user_last_name` FROM `users` WHERE `user_id`=?';

                                                                        // Prepare the SQL statement
                                                                        $fetchStaffStmt = $dbConnection->prepare($fetchStaffSql);
                                                                        
                                                                        // Bind the supplier is as the parameter for the SQL statement
                                                                        $fetchStaffStmt ->bind_param('s', $fetchExpensesAssoc['paid_by']);

                                                                        // Execute the prepared statement
                                                                        $fetchStaffStmt->execute();

                                                                        // Retrieve the result set
                                                                        $fetchStaffResult = $fetchStaffStmt->get_result();

                                                                        // Close the prepared statement to free up resources
                                                                        $fetchStaffStmt->close();
                                                                        
                                                                        // Fetch data as an associative array
                                                                        $fetchStaffAssoc= $fetchStaffResult->fetch_assoc();
                                                                        
                                                                        if ($fetchStaffResult->num_rows !== 1) { ?>
                                                                            Staff not found
                                                                        <?php } else { ?>
                                                                            <?php echo $fetchStaffAssoc['user_first_name'].' '.$fetchStaffAssoc['user_middle_name'].' '.$fetchStaffAssoc['user_last_name']; ?>
                                                                        <?php } ?>
                                                                <?php } ?>
                                                            </td>
                                                            <td><span class="btn btn-sm btn-block btn-primary" onclick="window.open('expenseDetalis?id=<?php echo $fetchExpensesAssoc['expense_id']; ?>','popup','width=900,height=600'); return false;">View</span></td>
                                                        </tr>
                                                    <?php } ?>
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
                    $(document).ready( function (){$('#expenses_table').DataTable();});
                </script>
            </body>
        </html>
<?php $dbConnection->close(); // Close the database connection ?>