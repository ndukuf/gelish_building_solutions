<?php
    /* Require necessary files for the application to function properly. */
    //establishes a connection to the database.
    require '../database_connection.php';

    //contains company-related data.
    require '../company_data.php';

    //provides the current date and time.
    require '../current_date_and_time.php';

    //provides the currency_prefix.
    require '../currency_prefix.php';

    //connect to 00_validate_admin.
    require '00_validate_admin.php';

    // Prepare a SQL query to fetch product data from the database
    $fetchProductDetailsSql = 'SELECT * FROM `products` WHERE `product_code`=? LIMIT 1';

    // Prepare the SQL statement
    $fetchProductDetailsStmt = $dbConnection->prepare($fetchProductDetailsSql);

    // Bind parameters to the prepared statement
    $fetchProductDetailsStmt->bind_param('s',$_GET['id']);

    // Execute the prepared statement
    $fetchProductDetailsStmt->execute();

    // Retrieve the result set
    $fetchProductDetailsResult = $fetchProductDetailsStmt->get_result();

    // Fetch data as an associative array
    $fetchProductDetailsAssoc= $fetchProductDetailsResult->fetch_assoc();
    
?>
<!-- Declare the document type as HTML -->
<!DOCTYPE html>
<!-- Specify the language of the document as English -->
<html lang="en">
    <!-- Head section of the HTML document -->
    <head>
        <title><?php echo $fetchCompanyDataAssoc['company_name'] ?> | <?php echo $fetchProductDetailsAssoc['product_name']; ?> DETAILS</title>
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
                    <?php echo $fetchProductDetailsAssoc['product_name']; ?> DETAILS
                </h5>
                <div class="container">
                    <hr style="border-top: 1px solid #000000;">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                            <p class="p-0 m-0">Product code: <strong><?php if (empty($fetchProductDetailsAssoc['product_code'])==true) { ?>Not set<?php } else { ?><?php echo $fetchProductDetailsAssoc['product_code']; ?><?php } ?></strong></p>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                            <p class="p-0 m-0">Product name: <strong><?php if (empty($fetchProductDetailsAssoc['product_name'])==true) { ?>Not set<?php } else { ?><?php echo $fetchProductDetailsAssoc['product_name']; ?><?php } ?></strong></p>
                        </div>
                    </div>
                    <hr style="border-top: 1px solid #000000;">
                    <div class="row text-left">
                        <div class="mx-auto">
                            <div class="col-12">
                                <p class="p-0 m-0">Product description: <strong><?php if (empty($fetchProductDetailsAssoc['product_description'])==true) { ?>Not set<?php } else { ?><?php echo $fetchProductDetailsAssoc['product_description']; ?><?php } ?></strong></p>
                                <p class="p-0 m-0">Buying price: 
                                    <strong>
                                        <?php if (intval($fetchProductDetailsAssoc['product_buying_price']) < 2) { ?>
                                            <?php echo $currency_one.' '.number_format($fetchProductDetailsAssoc['product_buying_price'], 2); ?>
                                        <?php }elseif (intval($fetchProductDetailsAssoc['product_buying_price']) >= 2) { ?>
                                                <?php echo $currency_many.' '.number_format($fetchProductDetailsAssoc['product_buying_price'], 2); ?>
                                        <?php } ?>
                                    </strong>
                                </p>
                                <p class="p-0 m-0">Selling price: 
                                    <strong>
                                        <?php if (intval($fetchProductDetailsAssoc['product_selling_price']) < 2) { ?>
                                            <?php echo $currency_one.' '.number_format($fetchProductDetailsAssoc['product_selling_price'], 2); ?>
                                        <?php }elseif (intval($fetchProductDetailsAssoc['product_selling_price']) >= 2) { ?>
                                                <?php echo $currency_many.' '.number_format($fetchProductDetailsAssoc['product_selling_price'], 2); ?>
                                        <?php } ?>
                                    </strong>
                                </p>

                                <p class="p-0 m-0">$profit: 
                                    <strong>
                                        <?php $profit = intval($fetchProductDetailsAssoc['product_selling_price'])-intval($fetchProductDetailsAssoc['product_buying_price']) ?>
                                        <?php if (intval($profit) < 2) { ?>
                                            <?php echo $currency_one.' '.number_format($profit, 2); ?>
                                        <?php }elseif (intval($profit) >= 2) { ?>
                                                <?php echo $currency_many.' '.number_format($profit, 2); ?>
                                        <?php } ?>
                                    </strong>
                                </p>
                                <p class="p-0 m-0">Quantity in stock: 
                                    <strong>
                                        <?php echo number_format($fetchProductDetailsAssoc['product_count'], 2); ?>
                                    </strong>
                                </p>
                                <p class="p-0 m-0">Date created: <strong><?php if (empty($fetchProductDetailsAssoc['product_date_created'])==true) { ?>Not set<?php } else { ?><?php echo date("l jS \of F Y h:i:s A", strtotime($fetchProductDetailsAssoc['product_date_created'])); ?><?php } ?></strong></p>
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