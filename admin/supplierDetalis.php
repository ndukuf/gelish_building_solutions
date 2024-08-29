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
<!-- Declare the document type as HTML -->
<!DOCTYPE html>
<!-- Specify the language of the document as English -->
<html lang="en">
    <!-- Head section of the HTML document -->
    <head>
        <title><?php echo $fetchCompanyDataAssoc['company_name'] ?> | Supplier&apos;s Details</title>
        <?php require 'header.php'; ?>
    </head>
    <style>
        html {
            position:relative;
            min-height:100%;
        }
        body {
            margin-bottom:100px;
            min-height:100%;
        }
        footer {
            position:absolute;
            bottom:0;
            width:100%;
            height:100px;
        }
        img {
            object-fit:contain;
        }
    </style>
    <body>
        <nav class="navbar navbar-dark bg-dark">
            <a class="mx-auto" href="index">
                <!-- link to the brand logo -->
                <img src="../<?php echo $fetchCompanyDataAssoc['company_logo']; ?>" width="100%" height="30" class="d-inline-block align-top" alt="<?php echo $fetchCompanyDataAssoc['company_name']; ?>" loading="lazy">
                <!-- image of the brand logo with lazy loading -->
                <!-- Gelish Building Solutions -->
                <!-- brand name text -->
            </a>
        </nav>
        <section>
            <div class="container mt-2 border border-primary">
                <h5 class="text-center font-weight-bold">
                    Supplier&apos;s Details
                </h5>
                <div class="container">
                <?php
                    // initial number
                    $listNumber = 1;
                    // Prepare a SQL query to fetch user data from the database
                    $fetchCustomersSql = 'SELECT * FROM `suppliers` WHERE `supplier_id`=? LIMIT 1';

                    // Prepare the SQL statement
                    $fetchCustomersStmt = $dbConnection->prepare($fetchCustomersSql);

                    // Bind parameters to the prepared statement
                    $fetchCustomersStmt->bind_param('s',$_GET['id']);

                    // Execute the prepared statement
                    $fetchCustomersStmt->execute();

                    // Retrieve the result set
                    $fetchCustomersResult = $fetchCustomersStmt->get_result();

                    // Fetch data as an associative array
                    $fetchCustomersAssoc= $fetchCustomersResult->fetch_assoc();
                ?>
                <!-- <div class="text-center">
                    <?php if (empty($fetchCustomersAssoc['user_avatar']) == true || $fetchCustomersAssoc['user_avatar']=='notSet' || empty($fetchCustomersAssoc['user_avatar']) == false && is_file('../profile_pictures/'.$fetchCustomersAssoc['user_avatar']) == false){ ?>
                        <img src="../profile_pictures/gbs.png" alt="gbs.png" class="user-img rounded" style="width:70px;height:70px;">
                    <?php } else { ?>
                        <img src="../profile_pictures/<?php echo $fetchCustomersAssoc['user_avatar']; ?>" alt="" class="user-img border">
                    <?php } ?>
                </div> -->
                <hr style="border-top: 1px solid #000000;">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                            <p class="p-0 m-0">Id: <strong><?php echo $fetchCustomersAssoc['supplier_id']; ?></strong></p>
                            <p class="p-0 m-0">Supplier name: <strong><?php echo $fetchCustomersAssoc['supplier_name']; ?></strong></p>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                            <p class="p-0 m-0">Email: <strong><?php if (empty($fetchCustomersAssoc['supplier_email_address'])==true) { ?>Not set<?php } else { ?><a class="text-dark" href="mailto:<?php echo $fetchCustomersAssoc['supplier_email_address']; ?>"><?php echo $fetchCustomersAssoc['supplier_email_address']; ?></a><?php } ?></strong></p>
                            <p class="p-0 m-0">Phone: <strong><?php if (empty($fetchCustomersAssoc['supplier_phone_number'])==true) { ?>Not set<?php } else { ?><a class="text-dark" href="tel:<?php echo $fetchCustomersAssoc['supplier_phone_number']; ?>"><?php echo $fetchCustomersAssoc['supplier_phone_number']; ?></a><?php } ?></strong></p>
                        </div>
                    </div>
                    <hr style="border-top: 1px solid #000000;">
                    <div class="row text-center">
                        <div class="mx-auto">
                            <div class="col-12">
                                <p class="p-0 m-0">Date created: <strong><?php if (empty($fetchCustomersAssoc['date_created'])==true) { ?>Not set<?php } else { ?><?php echo date("l jS \of F Y h:i:s A", strtotime($fetchCustomersAssoc['date_created'])); ?><?php } ?></strong></p>
                            </div>
                        </div>
                    </div>
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