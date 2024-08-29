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
                <title><?php echo $fetchCompanyDataAssoc['company_name']; ?> | Customers</title>
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
                                <?php if (isset($_GET['tsk'])==true && $_GET['tsk']=='view_customers') {?>
                                    <div class="card card-outline card-dark" style="border-bottom: 3px solid #343a40;">
                                        <div class="card-header">
                                            <h6 class="text-dark font-weight-bold">Customers</h6>
                                        </div>
                                        <div class="card-body">
                                            <table class="text-nowrap table table-hover table-striped table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl" id="customers_list_table">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th class="text-center">#</th>
                                                        <th>Username</th>
                                                        <th>Phone</th>
                                                        <th>Email</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        // initial number
                                                        $listNumber = 1;
                                                        $tbl_user_type = '7';
                                                        // Prepare a SQL query to fetch user data from the database
                                                        $fetchCustomersSql = 'SELECT `user_id`,`username`,`user_phone_number`,`email_address` FROM `users`  WHERE `user_type`=?  ORDER BY `date_joined` ASC';

                                                        // Prepare the SQL statement
                                                        $fetchCustomersStmt = $dbConnection->prepare($fetchCustomersSql);

                                                        // Bind parameters to the prepared statement
                                                        $fetchCustomersStmt->bind_param('s',$tbl_user_type);

                                                        // Execute the prepared statement
                                                        $fetchCustomersStmt->execute();

                                                        // Retrieve the result set
                                                        $fetchCustomersResult = $fetchCustomersStmt->get_result();

                                                        // Fetch data as an associative array
                                                        while ($fetchCustomersAssoc= $fetchCustomersResult->fetch_assoc()) {
                                                    ?>
                                                        <tr>
                                                            <th class="text-center">
                                                                <?php
                                                                    $count=$listNumber++;
                                                                    if ($count<10){echo '0'.$count;}
                                                                    else{echo $count;}
                                                                ?>
                                                            </th>
                                                            <td><?php echo $fetchCustomersAssoc['username']; ?></td>
                                                            <td>
                                                                <?php if (empty($fetchCustomersAssoc['user_phone_number'])==true) { ?>
                                                                    Not set
                                                                <?php }else{ ?>
                                                                    <a class="text-dark" href="tel:+<?php echo $fetchCustomersAssoc['user_phone_number']; ?>"><?php echo $fetchCustomersAssoc['user_phone_number']; ?></a>
                                                                <?php } ?>
                                                            </td>
                                                            <td><a class="text-dark" href="mailto:<?php echo $fetchCustomersAssoc['email_address']; ?>"><?php echo $fetchCustomersAssoc['email_address']; ?></a></td>
                                                            <td><span class="btn btn-sm btn-block btn-primary" onclick="window.open('customerDetalis?id=<?php echo $fetchCustomersAssoc['user_id']; ?>','popup','width=900,height=600'); return false;">View</span></td>
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
                    $(document).ready( function (){$('#customers_list_table').DataTable();});
                </script>
            </body>
        </html>
<?php $dbConnection->close(); // Close the database connection ?>