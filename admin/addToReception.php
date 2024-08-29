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

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'addToReceptionSubmit' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['addToReceptionSubmit']) == true) {
        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the addToReceptionProductCode input
        $addToReceptionProductCode = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['addToReceptionProductCode']), ENT_QUOTES, 'UTF-8')));

        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the addToReceptionQuantity input
        $addToReceptionQuantity = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['addToReceptionQuantity']), ENT_QUOTES, 'UTF-8')));

        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the addToReceptioBuyingPrice input
        $addToReceptioBuyingPrice = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['addToReceptioBuyingPrice']), ENT_QUOTES, 'UTF-8')));

        // Define a regular expression pattern to match edited quantity (one or more digits, optionally followed by a decimal point and one or more digits)
        $addToReceptionQuantityRegex = '/^\d+(\.\d+)?$/';

        // Define a regular expression pattern to match edited buying price (one or more digits, optionally followed by a decimal point and one or more digits)
        $addToReceptioBuyingPriceRegex = '/^\d+(\.\d+)?$/';

        if (empty($addToReceptionProductCode)==true) {
            // // Redirect the user to the invoice add to reception page showing empty product code error message
            // header('Location: addToReception?error=empty_product_code');
            // // Exit the script immediately to prevent any further execution.
            // exit();
            // Display an alert message to the user
            echo '<script language="javascript">';
            echo 'alert("Empty product code")';
            echo '</script>';
            echo "<script>window.close();</script>";
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        if (empty($addToReceptionQuantity)==true || $addToReceptionQuantity < '1') {
            // // Redirect the user to the invoice add to reception page showing empty quantity error message
            // header('Location: addToReception?error=empty_quantity');
            // // Exit the script immediately to prevent any further execution.
            // exit();
            // Display an alert message to the user
            echo '<script language="javascript">';
            echo 'alert("Empty quantity")';
            echo '</script>';
            echo "<script>window.close();</script>";
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        if (empty($addToReceptioBuyingPrice)==true) {
            // // Redirect the user to the invoice add to reception page showing empty buying price error message
            // header('Location: addToReception?error=empty_buying_price');
            // // Exit the script immediately to prevent any further execution.
            // exit();
            // Display an alert message to the user
            echo '<script language="javascript">';
            echo 'alert("Empty buying price")';
            echo '</script>';
            echo "<script>window.close();</script>";
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        if (!preg_match($addToReceptionQuantityRegex, $addToReceptionQuantity)) {
            // // Redirect the user to the invoice add to reception page showing invalid quantity error message
            // header('Location: addToReception?error=invalid_quantity');
            // // Exit the script immediately to prevent any further execution.
            // exit();
            // Display an alert message to the user
            echo '<script language="javascript">';
            echo 'alert("Invalid quantity")';
            echo '</script>';
            echo "<script>window.close();</script>";
            // Exit the script immediately to prevent any further execution.
            exit();
        }
        
        if (!preg_match($addToReceptioBuyingPriceRegex, $addToReceptioBuyingPrice)) {
            // // Redirect the user to the invoice add to reception page showing invalid buying price error message
            // header('Location: addToReception?error=invalid_buying_price');
            // // Exit the script immediately to prevent any further execution.
            // exit();
            // Display an alert message to the user
            echo '<script language="javascript">';
            echo 'alert("Invalid buying price")';
            echo '</script>';
            echo "<script>window.close();</script>";
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        // Convert the input values to floating point numbers and calculate the total buying price
        $total_buying_price = floatval($addToReceptionQuantity) * floatval($addToReceptioBuyingPrice);

        // Define a regular expression pattern to match the total buying price (one or more digits, optionally followed by a decimal point and one or more digits)
        $totalBuyingPriceRegex = '/^\d+(\.\d+)?$/';

        if (!preg_match($totalBuyingPriceRegex, $total_buying_price)) {
            // Redirect the user to the invoice add to reception page showing invalid buying price error message
            header('Location: addToReception?error=invalid_total_buying_price');
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        // SQL SELECT statement to select the reception_invoice_supplier from `invoice_reception_details` table where the reception_invoice_recipient mathces the  current user's id
        $fetchInvoiceReceptionDetailSql = 'SELECT `reception_invoice_number`,`reception_invoice_supplier` FROM `invoice_reception_details` WHERE `reception_invoice_recipient`=?';
        
        // Prepare the SQL SELECT statement to validate the supplier id
        $fetchInvoiceReceptionDetailStmt = $dbConnection->prepare($fetchInvoiceReceptionDetailSql);

        // Bind the current user's user_id session variable to the SQL SELECT statement
        $fetchInvoiceReceptionDetailStmt->bind_param('s', $_SESSION['user_id']);

        // Execute the SQL SELECT statement
        $fetchInvoiceReceptionDetailStmt->execute();

        // Fetch the result of the SQL SELECT statement
        $fetchInvoiceReceptionDetailResult = $fetchInvoiceReceptionDetailStmt->get_result();

        // Close the $fetchInvoiceReceptionDetailStmt prepared statement to free up resources
        $fetchInvoiceReceptionDetailStmt->close();

        // Fetch the result as an associative array
        $fetchInvoiceReceptionDetailAssoc= $fetchInvoiceReceptionDetailResult->fetch_assoc();

        // Check if the $selectedSupplier variable does not exists in the `invoice_reception_details` table
        if ($fetchInvoiceReceptionDetailResult->num_rows === 0) {
            // Display an alert message to the user
            echo '<script language="javascript">';
            echo 'alert("The supplier is not set")';
            echo '</script>';
            echo "<script>window.close();</script>";
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        // SQL SELECT statement to select the supplier_id from `suppliers` table where the supplier_id mathces the selected supplier id
        $validateSelectedSupplierSql = 'SELECT `supplier_id` FROM `suppliers` WHERE `supplier_id`=?';
        
        // Prepare the SQL SELECT statement
        $validateSelectedSupplierStmt = $dbConnection->prepare($validateSelectedSupplierSql);

        // Bind the $selectedSupplier variable to the SQL SELECT statement
        $validateSelectedSupplierStmt->bind_param('s', $fetchInvoiceReceptionDetailAssoc['reception_invoice_supplier']);

        // Execute the SQL SELECT statement
        $validateSelectedSupplierStmt->execute();

        // Fetch the result of the SQL SELECT statement
        $validateSelectedSupplierResult = $validateSelectedSupplierStmt->get_result();

        // Close the $validateSelectedSupplierStmt prepared statement to free up resources
        $validateSelectedSupplierStmt->close();

        // Check if the $selectedSupplier variable does not exists in the `suppliers` table
        if ($validateSelectedSupplierResult->num_rows !== 1) {
            // Display an alert message to the user
            echo '<script language="javascript">';
            echo 'alert("Invalid supplier set")';
            echo '</script>';
            echo "<script>window.close();</script>";
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        // SQL SELECT statement to select the product name and product code from the products table where product_code matches the product_code provided
        $fetchProductDetailsSql = 'SELECT `product_code`,`product_name` FROM `products` WHERE `product_code`=?';

        // Prepare the SQL SELECT statement
        $fetchProductDetailsStmt = $dbConnection->prepare($fetchProductDetailsSql);

        // Bind the $addToReceptionProductCode variable as a parameter to the SQL SELECT statement
        $fetchProductDetailsStmt->bind_param('s', $addToReceptionProductCode);

        // Execute the SQL SELECT statement
        $fetchProductDetailsStmt->execute();

        // Fetch the result of the SQL SELECT statement
        $fetchProductDetailsResults = $fetchProductDetailsStmt->get_result();

        // Close the $fetchProductDetailsStmt prepared statement to free up resources
        $fetchProductDetailsStmt->close();

        // Fetch data as an associative array
        $fetchProductDetailsAssoc= $fetchProductDetailsResults->fetch_assoc();

        // Check if the $addToReceptionProductCode variable does not exists in the `products` table
        if ($fetchProductDetailsResults->num_rows === 0) {
            // Display an alert message to the user
            echo '<script language="javascript">';
            echo 'alert("The supplier is not set")';
            echo '</script>';
            echo "<script>window.close();</script>";
            // Exit the script immediately to prevent any further execution.
            exit();
        }
        
        // SQL statement to select the product code in the invoice_reception_products table where product_code matches the product_code provided
        $validateProductCodeSql = 'SELECT `product_code` FROM `invoice_reception_products` WHERE `product_code`=?';

        // Prepare the SQL statement
        $validateProductCodeStmt = $dbConnection->prepare($validateProductCodeSql);

        // Bind the product code parameter to the SQL statement
        $validateProductCodeStmt->bind_param('s', $addToReceptionProductCode);

        // Execute the SQL statement
        $validateProductCodeStmt->execute();

        // Store the result of the SQL statement
        $validateProductCodeStmt->store_result();

        // Check if the product code already exists in the reception
        if ($validateProductCodeStmt->num_rows !== 0) {
            // Display an alert message to the user
            echo '<script language="javascript">';
            echo 'alert("'.$fetchProductDetailsAssoc['product_name'].' already exists in the reception.")';
            echo '</script>';
            echo "<script>window.close();</script>";
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        // This function gets the current date and time and stores it in the $time_added variable.
        // The date and time are formatted using the 'Y-m-d Y-m-d H:i:s' format.
        $time_added = date('Y-m-d H:i:s', strtotime($currentDateAndTime));

        // SQL INSERT statement for inserting THE product into the `invoice_reception_products` table
        $insertInvoiceProductSql = 'INSERT INTO `invoice_reception_products`(`reception_invoice_supplier`,`reception_invoice_number`,`product_code`,`product_name`,`product_quantity`,`product_buying_price`,`product_total_buying_price`,`time_added`,`reception_invoice_recipient`) VALUES (?,?,?,?,?,?,?,?,?)';

        // Prepare the SQL INSERT statement
        $insertInvoiceProductStmt = $dbConnection->prepare($insertInvoiceProductSql);

        // Bind the parameters for the SQL INSERT statement
        $insertInvoiceProductStmt->bind_param('sssssssss', $fetchInvoiceReceptionDetailAssoc['reception_invoice_supplier'],$fetchInvoiceReceptionDetailAssoc['reception_invoice_number'],$fetchProductDetailsAssoc['product_code'],$fetchProductDetailsAssoc['product_name'],$addToReceptionQuantity,$addToReceptioBuyingPrice,$total_buying_price,$time_added,$_SESSION['user_id']);

        // Execute the SQL statement to insert the new product
        if ($insertInvoiceProductStmt->execute()) {
            $_SESSION['InvoiceItemAdded'] = 'added';
            $_SESSION['InvoiceItemAddedDetails'] = $fetchProductDetailsAssoc['product_name'].' has been added!';
            // Display an alert message to the user
            echo '<script language="javascript">';
            echo 'alert("'.$fetchProductDetailsAssoc['product_name'].' has been added to reception.")';
            echo '</script>';
            echo "<script>window.close();</script>";
            // Exit the script immediately to prevent any further execution.
            exit();
        } else {
            $_SESSION['InvoiceItemNotAdded'] = 'notAdded';
            $_SESSION['InvoiceItemNotAddedDetails'] = $fetchProductDetailsAssoc['product_name'].' has not been added!<br>Please try again.';
            // Display an alert message to the user
            echo '<script language="javascript">';
            echo 'alert("Problem with database or something! Please try again")';
            echo '</script>';
            // Exit the script immediately to prevent any further execution.
            exit();
        }
    }
?>
<!-- Declare the document type as HTML -->
<!DOCTYPE html>
<!-- Specify the language of the document as English -->
<html lang="en">
    <!-- Head section of the HTML document -->
    <head>
        <title><?php echo $fetchCompanyDataAssoc['company_name'] ?> | Add to reception</title>
        <?php require 'header.php'; ?>
    </head>
    <script>
        //JavaScript code to prevent the form from being resubmitted when the user refreshes the page.
        if ( window.history.replaceState ){window.history.replaceState( null, null, window.location.href );}
    </script>
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
        <section class="mt-4">
            <div class="card card-outline card-dark" style="border-bottom: 3px solid #343a40;">
                <div class="card-header text-center">
                    <h6 class="text-dark font-weight-bold">Add product to reception</h6>
                </div>
                <div class="card-body table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl">
                    <table class="text-nowrap table table-bordered table-hover table-striped" id="add_to_reception">
                        <thead class="thead-dark">
                            <tr>
                                <th>Product code</th>
                                <th>Product name</th>
                                <th>Quantity</th>
                                <th>Buying price</th>
                                <th class="col-1">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                // Prepare a SQL query to fetch product data from the database
                                $addToReceptionSql = 'SELECT `product_code`,`product_name`,`product_description`,`product_selling_price` FROM `products` ORDER BY `product_date_created` ASC';

                                // Prepare the SQL statement
                                $addToReceptionStmt = $dbConnection->prepare($addToReceptionSql);

                                // Execute the prepared statement
                                $addToReceptionStmt->execute();

                                // Retrieve the result set
                                $addToReceptionResult = $addToReceptionStmt->get_result();
                                
                                // Close the prepared statement to free up resources
                                $addToReceptionStmt->close();

                                // Fetch data as an associative array
                                while ($addToReceptionAssoc= $addToReceptionResult->fetch_assoc()) {
                            ?>
                                <tr>
                                    <form action="" method="post">
                                        <td class="font-weight-bold"><?php echo $addToReceptionAssoc['product_code']; ?></td>
                                        <td><?php echo $addToReceptionAssoc['product_name']; ?></td>
                                        <td>
                                            <input type="tel" name="addToReceptionQuantity" id="addToReceptionQuantity" class="form-control form-control-sm" placeholder="Quantity" autocomplete="off">
                                        </td>
                                        <td>
                                            <input type="hidden" name="addToReceptionProductCode" id="addToReceptionProductCode" class="form-control form-control-sm" readonly value="<?php echo $addToReceptionAssoc['product_code']; ?>" autocomplete="off">
                                            <input type="tel" name="addToReceptioBuyingPrice" id="addToReceptioBuyingPrice" class="form-control form-control-sm" placeholder="Buying price" autocomplete="off">
                                        </td>
                                        <td>
                                            <button type="submit" name="addToReceptionSubmit" id="addToReceptionSubmit" class="btn btn-md btn-block btn-secondary">Add</button>
                                        </td>
                                    </form>
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
        <script>
            $(document).ready( function (){$('#add_to_reception').DataTable();});
        </script>
        <?php require 'footer.php'; ?>
    </body>
</html>
<?php $dbConnection->close(); // Close the database connection ?>