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

    //connect to currency_prefix.
    require '../currency_prefix.php';

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'submitSupplier' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submitSupplier']) == true) {
        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the selectedSupplier input
        $selectedSupplier = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['selectedSupplier']), ENT_QUOTES, 'UTF-8')));
        
        // If the $selectedSupplier variable is empty
        if (empty($selectedSupplier) == true) {
            // Redirect the user to the invoice input page if the supplier id is empty
            header('Location: receive_invoice?tsk=input_invoice&err=empty_supplier');
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        // SQL SELECT statement to select the supplier_id from `suppliers` table where the supplier_id mathces the selected supplier id
        $validateSelectedSupplierSql = 'SELECT `supplier_id` FROM `suppliers` WHERE `supplier_id`=?';
        
        // Prepare the SQL SELECT statement
        $validateSelectedSupplierStmt = $dbConnection->prepare($validateSelectedSupplierSql);

        // Bind the $selectedSupplier variable to the SQL SELECT statement
        $validateSelectedSupplierStmt->bind_param('s', $selectedSupplier);

        // Execute the SQL SELECT statement
        $validateSelectedSupplierStmt->execute();

        // Fetch the result of the SQL SELECT statement
        $validateSelectedSupplierResult = $validateSelectedSupplierStmt->get_result();

        // Close the $validateSelectedSupplierStmt prepared statement to free up resources
        $validateSelectedSupplierStmt->close();

        // Check if the $selectedSupplier variable does not exists in the `suppliers` table
        if ($validateSelectedSupplierResult->num_rows !== 1) {
            // Redirect the user to the invoice input page if the supplier does not exist in the `suppliers` table
            header('Location: receive_invoice?tsk=input_invoice&err=invalid_supplier');
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        // SQL SELECT statement to select the reception_invoice_supplier from `invoice_reception_details` table where the reception_invoice_supplier mathces the selected supplier id
        $validateInvoiceSupplierSql = 'SELECT `reception_invoice_supplier` FROM `invoice_reception_details` WHERE `reception_invoice_supplier`=?';
        
        // Prepare the SQL SELECT statement to validate the supplier id
        $validateInvoiceSupplierStmt = $dbConnection->prepare($validateInvoiceSupplierSql);

        // Bind the $selectedSupplier variable to the SQL SELECT statement
        $validateInvoiceSupplierStmt->bind_param('s', $selectedSupplier);

        // Execute the SQL SELECT statement
        $validateInvoiceSupplierStmt->execute();

        // Fetch the result of the SQL SELECT statement
        $validateInvoiceSupplierResult = $validateInvoiceSupplierStmt->get_result();

        // Close the $validateInvoiceSupplierStmt prepared statement to free up resources
        $validateInvoiceSupplierStmt->close();

        // Check if the $selectedSupplier variable does not exists in the `invoice_reception_details` table
        if ($validateInvoiceSupplierResult->num_rows !== 0) {
            // Redirect the user to the invoice input page with an error message
            header('Location: receive_invoice?tsk=input_invoice&err=supplierExists');
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        // SQL SELECT statement to select the reception_invoice_supplier from the invoice_reception_products table where the reception_invoice_supplier mathces the selected supplier id
        $validateInvoiceSupplierPrdctSql = 'SELECT `reception_invoice_supplier` FROM `invoice_reception_products` WHERE `reception_invoice_supplier`=?';
        
        // Prepare the SQL SELECT statement
        $validateInvoiceSupplierPrdctStmt = $dbConnection->prepare($validateInvoiceSupplierPrdctSql);

        // Bind the $selectedSupplier variable to the SQL SELECT statement
        $validateInvoiceSupplierPrdctStmt->bind_param('s', $selectedSupplier);

        // Execute the SQL SELECT statement
        $validateInvoiceSupplierPrdctStmt->execute();

        // Fetch the result of the SQL SELECT statement
        $validateInvoiceSupplierPrdctResult = $validateInvoiceSupplierPrdctStmt->get_result();

        // Close the $validateInvoiceSupplierPrdctStmt prepared statement to free up resources
        $validateInvoiceSupplierPrdctStmt->close();

        // Check if the $selectedSupplier variable does not exists in the `invoice_reception_products` table
        if ($validateInvoiceSupplierPrdctResult->num_rows !== 0) {
            // Redirect the user to the invoice input page with an error message
            header('Location: receive_invoice?tsk=input_invoice&err=supplierExists01');
            // Exit the script immediately to prevent any further execution.
            exit();
        }
                
        // SQL SELECT statement to select the reception_invoice_recipient from the invoice_reception_details table where the reception_invoice_recipient mathces the current user's id
        $validateRecepientSql = 'SELECT `reception_invoice_recipient` FROM `invoice_reception_details` WHERE `reception_invoice_recipient`=?';
    
        // Prepare the SQL SELECT statement
        $validateRecepientStmt = $dbConnection->prepare($validateRecepientSql);

        // Bind the current user's user_id session variable to the SQL SELECT statement
        $validateRecepientStmt->bind_param('s', $_SESSION['user_id']);

        // Execute the SQL SELECT statement
        $validateRecepientStmt->execute();

        // Fetch the result of the SQL SELECT statement
        $validateRecepientStmtResult = $validateRecepientStmt->get_result();

        // Close the $validateRecepientStmt prepared statement to free up resources
        $validateRecepientStmt->close();

        // Check if the current user's user_id session variable does not exists in the `invoice_reception_details` table
        if ($validateRecepientStmtResult->num_rows !== 0) {
            // Redirect the user to the invoice input page with an error message
            header('Location: receive_invoice?tsk=input_invoice&err=recipientExists');
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        // SQL SELECT statement to select the reception_invoice_recipient from the invoice_reception_products table where the reception_invoice_recipient mathces the current user's id
        $validateRecepientPrdctSql = 'SELECT `reception_invoice_recipient` FROM `invoice_reception_products` WHERE `reception_invoice_recipient`=?';
    
        // Prepare the SQL SELECT statement
        $validateRecepientPrdctStmt = $dbConnection->prepare($validateRecepientPrdctSql);

        // Bind the current user's user_id session variable to the SQL SELECT statement
        $validateRecepientPrdctStmt->bind_param('s', $_SESSION['user_id']);

        // Execute the SQL SELECT statement
        $validateRecepientPrdctStmt->execute();

        // Fetch the result of the SQL SELECT statement
        $validateRecepientPrdctResult = $validateRecepientPrdctStmt->get_result();

        // Close the $validateRecepientPrdctStmt prepared statement to free up resources
        $validateRecepientPrdctStmt->close();

        // Check if the current user's user_id session variable does not exists in the `invoice_reception_products` table
        if ($validateRecepientPrdctResult->num_rows !== 0) {
            // Redirect the user to the invoice input page if the recepient exists
            header('Location: receive_invoice?tsk=input_invoice&err=recipientExists01');
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        // SQL INSERT statement to insert the reception invoice supplier and reception invoice recipient into the `invoice_reception_details` table
        $insertSupplerIdSql = 'INSERT INTO `invoice_reception_details`(`reception_invoice_supplier`,`reception_invoice_recipient`) VALUES (?,?)';

        // Prepare the SQL INSERT statement for execution
        $insertSupplerIdStmt = $dbConnection->prepare($insertSupplerIdSql);

        // Bind the parameters to the prepared statement
        // $selectedSupplier is the ID of the selected supplier
        // $_SESSION['user_id'] is the ID of the current user (the recipient of the invoice)
        $insertSupplerIdStmt->bind_param('ss', $selectedSupplier,$_SESSION['user_id']);

        // If the execution of the prepared statement is successful
        if ($insertSupplerIdStmt->execute()) {
            // If the insertion is successful, redirect the user to the invoice reception page
            header('Location: receive_invoice?tsk=input_invoice&scs=rcrd01_added');
            // Exit the script immediately to prevent any further execution.
            exit();
        } else {
            // Redirect the user to the invoice input page if the recepient and supplier has noe been saved
            header('Location: receive_invoice?tsk=input_invoice&err=rcrd01_not_added');
            // Exit the script immediately to prevent any further execution.
            exit();
        }
    }

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'submitDate' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submitDate']) == true) {
        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the invoiceReceiptDate input
        $invoiceReceiptDate = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['invoiceReceiptDate']), ENT_QUOTES, 'UTF-8')));

        // If the $invoiceReceiptDate variable is empty
        if (empty($invoiceReceiptDate) == true) {
            // Redirect the user to the invoice input page with an error message indicating invoice date is empty
            header('Location: receive_invoice?tsk=input_invoice&err=empty_invoice_date');
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        // The date and time are formatted using the 'Y-m-d' format.
        $dateToday = date('Y-m-d', strtotime($currentDateAndTime));

        // If the invoice receipt date is greater than the current date
        if ($invoiceReceiptDate > $dateToday) {
            // Redirect the user to the invoice input page with an error message indicating the invoice date cannot be greater than the current date
            header('Location: receive_invoice?tsk=input_invoice&err=greatnvoiceDate');
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        // Extract the month from the invoice receipt date
        $checkReceiptMonth = date('m', strtotime($invoiceReceiptDate));

        // Extract the day from the invoice receipt date
        $checkReceiptDate = date('d', strtotime($invoiceReceiptDate));

        // Extract the year from the invoice receipt date
        $checkReceiptYear = date('Y', strtotime($invoiceReceiptDate));

        // Validate the extracted date components using the checkdate function
        $isInvoiceReceiptDateValid = checkdate($checkReceiptMonth,$checkReceiptDate,$checkReceiptYear);

        // If invoice receipt date is invalid
        if ($isInvoiceReceiptDateValid !== true) {
            // Redirect the user to the invoice input page with an error message indicating an invalid invoice date
            header('Location: receive_invoice?tsk=input_invoice&err=invalid_invoice_date');
            exit();
        }

        // Initialize an empty variable to store the reception invoice date
        $empty_reception_invoice_date = '';

        // SQL SELECT statement to select the reception_invoice_date from the invoice_reception_details table where the reception_invoice_date is empty and the reception_invoice_recipient mathces the current user's id
        $validateInvoiceDateSql = 'SELECT `reception_invoice_date` FROM `invoice_reception_details` WHERE `reception_invoice_date`=? AND `reception_invoice_recipient`=?';
        
        // Prepare the SQL SELECT statement
        $validateInvoiceDateStmt = $dbConnection->prepare($validateInvoiceDateSql);

        // Bind the $empty_reception_invoice_date variable and current user's user_id session variable to the SQL SELECT statement
        $validateInvoiceDateStmt->bind_param('ss', $empty_reception_invoice_date,$_SESSION['user_id']);

        // Execute the SQL SELECT statement
        $validateInvoiceDateStmt->execute();

        // Fetch the result of the SQL SELECT statement
        $validateInvoiceDateResult = $validateInvoiceDateStmt->get_result();

        // Close the $validateInvoiceDateStmt prepared statement to free up resources
        $validateInvoiceDateStmt->close();

        // If the number of rows is zero
        if ($validateInvoiceDateResult->num_rows === 0) {
            // Redirect the user to the invoice input page with an error message indicating the invoice date is already set
            header('Location: receive_invoice?tsk=input_invoice&err=invoice_date_already_set');
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        // SQL UPDATE statement to update the reception_invoice_date in the invoice_reception_details table where the reception_invoice_date is empty and the reception_invoice_recipient mathces the current user's id
        $updateInvoiceDateSql = 'UPDATE `invoice_reception_details` SET `reception_invoice_date`=? WHERE `reception_invoice_date`=? AND `reception_invoice_recipient`=?';

        // Prepare the SQL UPDATE statement
        $updateInvoiceDateStmt = $dbConnection->prepare($updateInvoiceDateSql);

        // Bind the $invoiceReceiptDate, $empty_reception_invoice_date variables and current user's user_id session variable to the SQL UPDATE statement
        $updateInvoiceDateStmt->bind_param('sss', $invoiceReceiptDate,$empty_reception_invoice_date,$_SESSION['user_id']);

        // If the SQL UPDATE statement is executed
        if ($updateInvoiceDateStmt->execute()){
            // Redirect the user to the invoice input page with a success message indicating the invoice date has been set
            header('Location: receive_invoice?tsk=input_invoice&scs=invoice_date_set');
            // Exit the script immediately to prevent any further execution.
            exit();
        }
    }

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'submitInvoiceNumber' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submitInvoiceNumber']) == true) {
        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the invoiceNumber input
        $invoiceNumber = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['invoiceNumber']), ENT_QUOTES, 'UTF-8')));

        // If the $invoiceNumber variable is empty
        if (empty($invoiceNumber) == true) {
            // Redirect the user to the invoice input page with an error message indicating invoice number is empty
            header('Location: receive_invoice?tsk=input_invoice&err=empty_invoice_number');
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        // Initialize an empty variable to store the reception invoice number
        $empty_reception_invoice_number = '';

        // SQL SELECT statement to select the reception_invoice_number from the invoice_reception_details table where the reception_invoice_number is empty and the reception_invoice_recipient mathces the current user's id
        $validateInvoiceNumberSql = 'SELECT `reception_invoice_number` FROM `invoice_reception_details` WHERE `reception_invoice_number`=? AND `reception_invoice_recipient`=?';
        
        // Prepare the SQL SELECT statement
        $validateInvoiceNumberStmt = $dbConnection->prepare($validateInvoiceNumberSql);

        // Bind the $empty_reception_invoice_date variable and current user's user_id session variable to the SQL SELECT statement
        $validateInvoiceNumberStmt->bind_param('ss', $empty_reception_invoice_number,$_SESSION['user_id']);

        // Execute the SQL SELECT statement
        $validateInvoiceNumberStmt->execute();

        // Fetch the result of the SQL SELECT statement
        $validateInvoiceDateResult = $validateInvoiceNumberStmt->get_result();

        // Close the $validateInvoiceNumberStmt prepared statement to free up resources
        $validateInvoiceNumberStmt->close();

        // If the number of rows is zero
        if ($validateInvoiceDateResult->num_rows === 0) {
            // Redirect the user to the invoice input page showing invalid invoice number session error message
            header('Location: receive_invoice?tsk=input_invoice&err=invalidInvoiceNumberSession');
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        // SQL UPDATE statement to update the reception_invoice_number in the invoice_reception_details table where the reception_invoice_number is empty and the reception_invoice_recipient mathces the current user's id
        $updateInvoiceNumberSql = 'UPDATE `invoice_reception_details` SET `reception_invoice_number`=? WHERE `reception_invoice_number`=? AND `reception_invoice_recipient`=?';
        // Prepare the SQL UPDATE statement
        $updateInvoiceNumberStmt = $dbConnection->prepare($updateInvoiceNumberSql);
        // Bind the $invoiceReceiptDate, $empty_reception_invoice_number variables and current user's user_id session variable to the SQL UPDATE statement
        $updateInvoiceNumberStmt->bind_param('sss', $invoiceNumber,$empty_reception_invoice_number,$_SESSION['user_id']);
        // If the SQL UPDATE statement is executed
        if ($updateInvoiceNumberStmt->execute()){
            // Redirect the user to the invoice input page a success message indicating the invoice number has been set
            // header('Location: receive_invoice?tsk=input_invoice&scs=invoice_number_set');
            header('Location: receive_invoice?tsk=input_invoice');
            // Exit the script immediately to prevent any further execution.
            exit();
        }
    }

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'submitClearInvoice' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submitClearInvoice']) == true) {
        // Prepare a SQL statement to delete data from the `invoice_reception_details` table
        // where the `reception_invoice_recipient` matches the current user's ID
        $deleteReceptionDataSql = 'DELETE FROM `invoice_reception_details` WHERE `reception_invoice_recipient`=?';

        // Establish a prepared statement using the SQL statement and the database connection
        $deleteReceptionDataStmt = $dbConnection->prepare($deleteReceptionDataSql);

        // Bind the current user's ID to the prepared statement as a string
        $deleteReceptionDataStmt->bind_param('s', $_SESSION['user_id']);

        // Prepare a SQL statement to delete data from the `invoice_reception_products` table
        // where the `reception_invoice_recipient` matches the current user's ID
        $deleteProductsUnderReceptionSql = 'DELETE FROM `invoice_reception_products` WHERE `reception_invoice_recipient`=?';

        // Establish a prepared statement using the SQL statement and the database connection
        $deleteProductsUnderReceptionStmt = $dbConnection->prepare($deleteProductsUnderReceptionSql);

        // Bind the current user's ID to the prepared statement as a string
        $deleteProductsUnderReceptionStmt->bind_param('s', $_SESSION['user_id']);

        if ($deleteReceptionDataStmt->execute() && $deleteProductsUnderReceptionStmt->execute()) {
            // Close the $deleteReceptionDataStmt prepared statement to free up resources
            $deleteReceptionDataStmt->close();
            // Close the $deleteProductsUnderReceptionStmt prepared statement to free up resources
            $deleteProductsUnderReceptionStmt->close();
            // If the statement executes successfully, redirect the user to the invoice input page
            header('Location: receive_invoice?tsk=input_invoice&scs=invoiceCleared');
            // Exit the script immediately to prevent any further execution
            exit();
        } else {
            // If the statement fails to execute, redirect the user to the invoice input page
            // with an error message indicating that the record could not be deleted
            header('Location: receive_invoice?tsk=input_invoice&err=unableToClearInvoice');
            // Exit the script immediately to prevent any further execution
            exit();
        }
    }

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'submiteditProduct' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submiteditProduct']) == true) {
        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the editQuantity input
        $editQuantity = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['editQuantity']), ENT_QUOTES, 'UTF-8')));

        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the editBuyingPrice input
        $editBuyingPrice = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['editBuyingPrice']), ENT_QUOTES, 'UTF-8')));

        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the productCodeEditing input
        $productCodeEditing = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['productCodeEditing']), ENT_QUOTES, 'UTF-8')));

        // If the $editQuantity variable is empty or is less than one
        if (empty($editQuantity) == true || $editQuantity < '1') {
            // Redirect the user to the invoice input page if the editQuantity is empty
            header('Location: receive_invoice?tsk=input_invoice&err=empty_editQuantity');
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        // If the $editBuyingPrice variable is empty or is less than one
        if (empty($editBuyingPrice) == true || $editBuyingPrice < '1') {
            // Redirect the user to the invoice input page if the editBuyingPrice is empty
            header('Location: receive_invoice?tsk=input_invoice&err=empty_editBuyingPrice');
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        // If the $productCodeEditing variable is empty
        if (empty($productCodeEditing) == true) {
            // Redirect the user to the invoice input page if the productCodeEditing is empty
            header('Location: receive_invoice?tsk=input_invoice&err=empty_productCodeEditing');
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        // Define a regular expression pattern (optional minus sign, followed by one or more digits, optionally followed by a decimal point and one or more digits)
        $numberRegex = '/^-?\d+(\.\d+)?$/';

        if (!preg_match($numberRegex, $productCodeEditing)) {
            // Redirect the user to the invoice input page showing invalid product code error messagge
            header('Location: receive_invoice?tsk=input_invoice&err=invalid_product_code');
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        if (!preg_match($numberRegex, $editQuantity)) {
            // Redirect the user to the invoice input page showing invalid quantity error messagge
            header('Location: receive_invoice?tsk=input_invoice&err=invalid_Quantity');
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        if (!preg_match($numberRegex, $editBuyingPrice)) {
            // Redirect the user to the invoice input page showing invalid buying price error messagge
            header('Location: receive_invoice?tsk=input_invoice&err=invalid_BuyingPrice');
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        // SQL SELECT statement to select the product name and product code from the products table where product_code matches the product_code provided
        $fetchProductCodeSql = 'SELECT `product_code` FROM `products` WHERE `product_code`=?';

        // Prepare the SQL SELECT statement
        $fetchProductCodeStmt = $dbConnection->prepare($fetchProductCodeSql);

        // Bind the $productCodeEditing variable as a parameter to the SQL SELECT statement
        $fetchProductCodeStmt->bind_param('s', $productCodeEditing);

        // Execute the SQL SELECT statement
        $fetchProductCodeStmt->execute();

        // Fetch the result of the SQL SELECT statement
        $fetchProductCodeResults = $fetchProductCodeStmt->get_result();

        // Close the $fetchProductCodeStmt prepared statement to free up resources
        $fetchProductCodeStmt->close();

        // Fetch data as an associative array
        $fetchProductCodeAssoc= $fetchProductCodeResults->fetch_assoc();

        // Check if the $productCodeEditing variable does not exists in the `products` table
        if ($fetchProductCodeResults->num_rows === 0) {
            // Redirect the user to the invoice input page if the product code does not exist
            header('Location: receive_invoice?tsk=input_invoice&err=invalid_product');
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        // Convert the input values to floating point numbers and calculate the total buying price
        $product_total_buying_price = floatval($editQuantity) * floatval($editBuyingPrice);
        
        // SQL UPDATE statement to update the product_quantity,product_buying_price,product_total_buying_price in the invoice_reception_products table where the reception_invoice_recipient mathces the current user's id
        $updateProductValuesSql = 'UPDATE `invoice_reception_products` SET `product_quantity`=?,`product_buying_price`=?,`product_total_buying_price`=? WHERE `reception_invoice_recipient`=?';

        // Prepare the SQL UPDATE statement
        $updateProductValuesStmt = $dbConnection->prepare($updateProductValuesSql);

        // Bind the $editQuantity, $fetchInitialvaluesAssoc['product_quantity'] variables and current user's user_id session variable to the SQL UPDATE statement
        $updateProductValuesStmt->bind_param('ssss', $editQuantity,$editBuyingPrice,$product_total_buying_price,$_SESSION['user_id']);

        // Execute the SQL UPDATE statement
        if ($updateProductValuesStmt->execute()) {
            // Redirect the user to the invoice input page
            header('Location: receive_invoice?tsk=input_invoice');
            // Exit the script immediately to prevent any further execution.
            exit();
        }
    }

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'submitremoveProduct' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submitremoveProduct']) == true) {
        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the productCodeRemove input
        $productCodeRemove = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['productCodeRemove']), ENT_QUOTES, 'UTF-8')));

        // If the $productCodeRemove variable is empty
        if (empty($productCodeRemove) == true) {
            // Redirect the user to the invoice input page if the productCodeRemove is empty
            header('Location: receive_invoice?tsk=input_invoice&err=empty_productCodeRemove');
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        // SQL DELETE statement to delete the product from the invoice_reception_products table where the product_code matches the set value and reception_invoice_recipient mathces the current user's id
        $deleteProductSql = 'DELETE FROM `invoice_reception_products` WHERE `product_code`=? AND `reception_invoice_recipient`=?';

        // Prepare the SQL DELETE statement
        $deleteProductStmt = $dbConnection->prepare($deleteProductSql);

        // Bind the $productCodeRemove and current user's user_id session variable to the SQL DELETE statement
        $deleteProductStmt->bind_param('ss', $productCodeRemove,$_SESSION['user_id']);

        // Execute the SQL DELETE statement
        if ($deleteProductStmt->execute()) {
            // Redirect the user to the invoice input page
            header('Location: receive_invoice?tsk=input_invoice');
            // Exit the script immediately to prevent any further execution.
            exit();
        }
    }

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'submitSaveInvoice' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submitSaveInvoice']) == true) {
        // SQL SELECT statement to select the product_code from the invoice_reception_products table where the reception_invoice_recipient mathces the current user's id
        $countInvoiceItemsSql = 'SELECT `product_code` FROM `invoice_reception_products` WHERE `reception_invoice_recipient`=?';
        
        // Prepare the SQL SELECT statement
        $countInvoiceItemsStmt = $dbConnection->prepare($countInvoiceItemsSql);

        // Bind the current user's user_id session variable to the SQL SELECT statement
        $countInvoiceItemsStmt->bind_param('s', $_SESSION['user_id']);

        // Execute the SQL SELECT statement
        $countInvoiceItemsStmt->execute();

        // Fetch the result of the SQL SELECT statement
        $countInvoiceItemsResult = $countInvoiceItemsStmt->get_result();

        // Close the $countInvoiceItemsStmt prepared statement to free up resources
        $countInvoiceItemsStmt->close();

        // If the number of rows is zero
        if ($countInvoiceItemsResult->num_rows === 0) {
            // Redirect the user to the invoice input page with an error message indicating the there are no invoice items
            header('Location: receive_invoice?tsk=input_invoice&err=noInvoiceItems');
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        // Generate a random salt for the expense id
        $randomCharactersSalt = 'XPNS';

        // Generate two random characters, convert them to uppercase hexadecimal for uniqueness
        $randomCharacters01 = strtoupper(bin2hex(random_bytes(2)));
        $randomCharacters02 = strtoupper(bin2hex(random_bytes(2)));
    
        // Concatenate the random characters with the predefined salt to create a unique expense id
        $expense_id = $randomCharacters01.$randomCharactersSalt.$randomCharacters02;

        // Prepare a SQL query to fetch expense_id from `invoice_reception_products` table where `expense_id` matches the generated expense_id variable
        $fetchExpenseIds00Sql = 'SELECT `expense_id` FROM `invoice_reception_products` WHERE `expense_id`=? LIMIT 1';

        // Prepare the SQL statement
        $fetchExpenseIds00Stmt = $dbConnection->prepare($fetchExpenseIds00Sql);

        // Bind parameters to the prepared statement
        $fetchExpenseIds00Stmt->bind_param('s',$expense_id);

        // Execute the prepared statement
        $fetchExpenseIds00Stmt->execute();

        // Fetch the result of the prepared statement
        $fetchExpenseIds00Result = $fetchExpenseIds00Stmt->get_result();

        // Prepare a SQL query to fetch expense_id from `received_invoice_products` table where `expense_id` matches the generated expense_id variable
        $fetchExpenseIds01Sql = 'SELECT `expense_id` FROM `received_invoice_products` WHERE `expense_id`=? LIMIT 1';

        // Prepare the SQL statement
        $fetchExpenseIds01Stmt = $dbConnection->prepare($fetchExpenseIds01Sql);

        // Bind parameters to the prepared statement
        $fetchExpenseIds01Stmt->bind_param('s',$expense_id);

        // Execute the prepared statement
        $fetchExpenseIds01Stmt->execute();

        // Fetch the result of the prepared statement
        $fetchExpenseIds01Result = $fetchExpenseIds01Stmt->get_result();

        do {
            // Generate a random salt for the expense id
            $randomCharactersSalt = 'XPNS';

            // Generate two random characters, convert them to uppercase hexadecimal for uniqueness
            $randomCharacters01 = strtoupper(bin2hex(random_bytes(2)));
            $randomCharacters02 = strtoupper(bin2hex(random_bytes(2)));
        
            // Concatenate the random characters with the predefined salt to create a unique expense id
            $expense_id = $randomCharacters01.$randomCharactersSalt.$randomCharacters02;
        } while ($fetchExpenseIds00Result->num_rows > 0 || $fetchExpenseIds01Result->num_rows > 0); // Loop until a unique expense id is generated, ensuring no duplicates

        // SQL UPDATE statement to update the expense_id in the invoice_reception_products table where the reception_invoice_recipient mathces the current user's id
        $updateExpenseIdSql = 'UPDATE `invoice_reception_products` SET `expense_id`=? WHERE `reception_invoice_recipient`=?';

        // Prepare the SQL UPDATE statement
        $updateExpenseIdStmt = $dbConnection->prepare($updateExpenseIdSql);

        // Bind the $editQuantity, $fetchInitialvaluesAssoc['product_quantity'] variables and current user's user_id session variable to the SQL UPDATE statement
        $updateExpenseIdStmt->bind_param('ss', $expense_id,$_SESSION['user_id']);

        // Execute the SQL UPDATE statement
        if ($updateExpenseIdStmt->execute()) {
            // Prepare a SQL query to fetch reception_invoice_date,reception_invoice_date,reception_invoice_supplier and reception_invoice_number from `invoice_reception_details` table where `reception_invoice_recipient` matches the current user's id
            $fetchExpenseDetailsSql = 'SELECT `reception_invoice_supplier`,`reception_invoice_date`,`reception_invoice_number` FROM `invoice_reception_details` WHERE `reception_invoice_recipient`=? LIMIT 1';

            // Prepare the SQL statement
            $fetchExpenseDetailsStmt = $dbConnection->prepare($fetchExpenseDetailsSql);

            // Bind parameters to the prepared statement
            $fetchExpenseDetailsStmt->bind_param('s', $_SESSION['user_id']);

            // Execute the prepared statement
            if ($fetchExpenseDetailsStmt->execute()) {
                // Fetch the result of the prepared statement
                $fetchExpenseDetailsResult = $fetchExpenseDetailsStmt->get_result();

                // Close the prepared statement to free up resources
                $fetchExpenseDetailsStmt->close();

                // Fetch the result as an associative array
                $fetchExpenseDetailsAssoc= $fetchExpenseDetailsResult->fetch_assoc();

                $selectTotalExpenseSql = 'SELECT SUM(`product_total_buying_price`) AS `product_total_buying_price` FROM `invoice_reception_products` WHERE `reception_invoice_recipient` =? AND `expense_id`=?';
                // Prepare the SQL statement
                $selectTotalExpenseStmt = $dbConnection->prepare($selectTotalExpenseSql);
                
                // Bind the current user's user_id session variable to the SQL SELECT statement
                $selectTotalExpenseStmt->bind_param('ss', $_SESSION['user_id'],$expense_id);

                // Execute the prepared statement
                $selectTotalExpenseStmt->execute();

                // Retrieve the result set
                $selectTotalExpenseResult = $selectTotalExpenseStmt->get_result();
                
                // Close the prepared statement to free up resources
                $selectTotalExpenseStmt->close();

                // Fetch data as an associative array
                $selectTotalExpenseAssoc= $selectTotalExpenseResult->fetch_assoc();
                
                // Define the expense status as 'pending'
                $expense_status = 'pending';

                // Set the expense description to include the invoice number from the fetched expense details associative array
                $expense_description = 'Invoice number: '. $fetchExpenseDetailsAssoc['reception_invoice_number'];

                // SQL INSERT statement
                $addExpenseSql = 'INSERT INTO `expenses`(`expense_id`,`expense_amount`,`paid_to`,`paid_by`,`expense_description`,`expense_date`,`expense_status`) VALUES (?,?,?,?,?,?,?)';

                // Prepare the SQL INSERT statement
                $addExpenseStmt = $dbConnection->prepare($addExpenseSql);

                // Bind the current user's user_id session variable to the SQL SELECT statement
                $addExpenseStmt->bind_param('sssssss', $expense_id,$selectTotalExpenseAssoc['product_total_buying_price'],$fetchExpenseDetailsAssoc['reception_invoice_supplier'],$_SESSION['user_id'],$expense_description,$fetchExpenseDetailsAssoc['reception_invoice_date'],$expense_status);
                // Execute the prepared statement
                if ($addExpenseStmt->execute()) {
                    // SQL INSERT statement to add the products from `invoice_reception_products` table to the `received_invoice_products` table
                    $addToReceivedInvoiceProductsSql = 'INSERT INTO received_invoice_products (invoice_supplier, invoice_number,`product_code`,`product_name`,`product_quantity`,`product_buying_price`,`product_total_buying_price`,`time_added`,`invoice_recipient`,`expense_id`)
                    SELECT `reception_invoice_supplier`,`reception_invoice_number`,`product_code`,`product_name`,`product_quantity`,`product_buying_price`,`product_total_buying_price`,`time_added`,`reception_invoice_recipient`,`expense_id` FROM invoice_reception_products WHERE `reception_invoice_recipient`=? AND `expense_id`=?';
                    
                    // Prepare the SQL Insert statement
                    $addToReceivedInvoiceProductsStmt = $dbConnection->prepare($addToReceivedInvoiceProductsSql);

                    // Bind $expense_id as a parameter to the prepared statement
                    $addToReceivedInvoiceProductsStmt->bind_param('ss', $_SESSION['user_id'],$expense_id);

                    // If the statement is executed
                    if ($addToReceivedInvoiceProductsStmt->execute()) {
                        // Retrieve the product details from the received invoice products table
                        $addToReceivedProductsDetailsSql = 'SELECT `product_code`,`product_quantity`,`product_buying_price` FROM `received_invoice_products` WHERE `invoice_recipient`=? AND `expense_id`=?';
                        $addToReceivedProductsDetailsStmt = $dbConnection->prepare($addToReceivedProductsDetailsSql);
                        $addToReceivedProductsDetailsStmt->bind_param('ss', $_SESSION['user_id'],$expense_id);
                        
                        // If the statement is executed
                        if ($addToReceivedProductsDetailsStmt->execute()) {
                            // Fetch the result set
                            $addToReceivedProductsDetailsStmtResult = $addToReceivedProductsDetailsStmt->get_result();
                            
                            while ($addToReceivedProductsDetailAssoc = $addToReceivedProductsDetailsStmtResult->fetch_assoc()) {
                                // Extract the product details
                                $received_product_code = $addToReceivedProductsDetailAssoc['product_code'];
                                $received_product_quantity = $addToReceivedProductsDetailAssoc['product_quantity'];
                                $received_product_buying_price = $addToReceivedProductsDetailAssoc['product_buying_price'];
                                
                                // Retrieve the initial product details from the products table
                                $selectInitialProductsDetailsSql = 'SELECT `product_buying_price`,`product_count` FROM `products` WHERE `product_code`=?';
                                $selectInitialProductsDetailsStmt = $dbConnection->prepare($selectInitialProductsDetailsSql);
                                $selectInitialProductsDetailsStmt->bind_param('s', $received_product_code);
                                
                                // If the statement is executed
                                if ($selectInitialProductsDetailsStmt->execute()) {
                                    // Fetch the result set
                                    $selectInitialProductsDetailsStmtResult = $selectInitialProductsDetailsStmt->get_result();
                                    
                                    while ($selectInitialProductsDetailsAssoc = $selectInitialProductsDetailsStmtResult->fetch_assoc()) {
                                        // Calculate the new product count
                                        $newProductCount = floatval($received_product_quantity) + floatval($selectInitialProductsDetailsAssoc['product_count']);
                                        
                                        // Update the product details in the products table
                                        $updateProductDetailsSql = 'UPDATE `products` SET `product_buying_price`=?,`product_count`=? WHERE `product_code`=?';
                                        $updateProductDetailsStmt = $dbConnection->prepare($updateProductDetailsSql);
                                        $updateProductDetailsStmt->bind_param('sss', $received_product_buying_price,$newProductCount,$received_product_code);
                                        
                                        // If the statement is executed
                                        if ($updateProductDetailsStmt->execute()) {
                                            // Delete the invoice reception products
                                            $deleteInvoiceReceptionProductsSql = 'DELETE FROM `invoice_reception_products` WHERE `reception_invoice_recipient`=? AND `expense_id`=?';
                                            $deleteInvoiceReceptionProductsStmt = $dbConnection->prepare($deleteInvoiceReceptionProductsSql);
                                            $deleteInvoiceReceptionProductsStmt->bind_param('ss', $_SESSION['user_id'],$expense_id);
                                            
                                            // If the statement is executed
                                            if ($deleteInvoiceReceptionProductsStmt->execute()) {
                                                // Delete the invoice reception details
                                                $deleteInvoiceReceptionDetailsql = 'DELETE FROM `invoice_reception_details` WHERE `reception_invoice_recipient`=?';
                                                $deleteInvoiceReceptionDetailstmt = $dbConnection->prepare($deleteInvoiceReceptionDetailsql);
                                                $deleteInvoiceReceptionDetailstmt->bind_param('s', $_SESSION['user_id']);
                                                $deleteInvoiceReceptionDetailstmt->execute();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        // Redirect the user to the invoice input page
        header('Location: receive_invoice?tsk=input_invoice');
        // Exit the script immediately to prevent any further execution.
        exit();
    }
?>
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title><?php echo $fetchCompanyDataAssoc['company_name']; ?> | Add invoice</title>
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
                                <?php if (isset($_GET['tsk'])==true && $_GET['tsk']=='input_invoice') { ?>
                                    <div class="container text-center mb-2">
                                        <?php if (isset($_GET['err'])==true && $_GET['err']=='empty_supplier') { ?>
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <strong>Error!</strong> Please select the supplier.
                                                <span type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </span>
                                            </div>
                                        <?php } elseif (isset($_GET['err'])==true && $_GET['err']=='invalid_supplier') { ?>
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <strong>Error!</strong> Please select a supplier that exists in the system.
                                                <span type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </span>
                                            </div>
                                        <?php } elseif (isset($_GET['err'])==true && $_GET['err']=='supplierExists' || isset($_GET['err'])==true && $_GET['err']=='supplierExists01') { ?>
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <strong>Error!</strong> The supplier&apos;s invoice is already being added to the system.
                                                <span type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </span>
                                            </div>
                                        <?php } elseif (isset($_GET['err'])==true && $_GET['err']=='recipientExists' || isset($_GET['err'])==true && $_GET['err']=='recipientExists01') { ?>
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <strong>Error!</strong> You already are receiving another Invoice.<br>Please complete saving the previous invoice so as to receive another invoice.
                                                <span type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </span>
                                            </div>
                                        <?php } elseif (isset($_GET['err'])==true && $_GET['err']=='rcrd01_not_added') { ?>
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <strong>Error!</strong> Invoice supplier has not been saved.<br>Please try again.
                                                <span type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </span>
                                            </div>
                                        <?php } elseif (isset($_GET['scs'])==true && $_GET['scs']=='rcrd01_added') { ?>
                                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                <strong>Success!</strong> Invoice supplier has been saved.
                                                <span type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </span>
                                            </div>
                                        <?php } elseif (isset($_GET['err'])==true && $_GET['err']=='empty_invoice_date') { ?>
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <strong>Error!</strong> Please enter the invoice date.
                                                <span type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </span>
                                            </div>
                                        <?php } elseif (isset($_GET['err'])==true && $_GET['err']=='greatnvoiceDate') { ?>
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <strong>Error!</strong> Please enter a valid invoice date.<br>Invoice date cannot be greater than the current date
                                                <span type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </span>
                                            </div>
                                        <?php } elseif (isset($_GET['err'])==true && $_GET['err']=='invalid_invoice_date') { ?>
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <strong>Error!</strong> Please enter a valid invoice date.
                                                <span type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </span>
                                            </div>
                                        <?php } elseif (isset($_GET['err'])==true && $_GET['err']=='invoice_date_already_set') { ?>
                                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                                <strong>Error!</strong> The invoice date has already been set.
                                                <span type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </span>
                                            </div>
                                        <?php } elseif (isset($_GET['scs'])==true && $_GET['scs']=='invoice_date_set') { ?>
                                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                <strong>Success!</strong> Invoice supplier has been saved.
                                                <span type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </span>
                                            </div>
                                        <?php } elseif (isset($_GET['err'])==true && $_GET['err']=='empty_invoice_number') { ?>
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <strong>Error!</strong> Please enter the invoice number.
                                                <span type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </span>
                                            </div>
                                        <?php } elseif (isset($_GET['err'])==true && $_GET['err']=='empty_invoice_number') { ?>
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <strong>Error!</strong> Please enter the invoice number.
                                                <span type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </span>
                                            </div>
                                        <?php } elseif (isset($_GET['scs'])==true && $_GET['scs']=='invoice_number_set') { ?>
                                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                <strong>Success!</strong> Invoice number has been saved.
                                                <span type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </span>
                                            </div>
                                        <?php } elseif (isset($_GET['scs'])==true && $_GET['scs']=='invoiceCleared') { ?>
                                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                <strong>Success!</strong> Invoice details have been cleared.
                                                <span type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </span>
                                            </div>
                                        <?php } elseif (isset($_GET['err'])==true && $_GET['err']=='unableToClearInvoice') { ?>
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <strong>Error!</strong> while clearing invoice details.<br>Please try again
                                                <span type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </span>
                                            </div>
                                        <?php } elseif (isset($_SESSION['InvoiceItemAdded'])==true && empty($_SESSION['InvoiceItemAdded'])==false && $_SESSION['InvoiceItemAdded']=='added') { ?>
                                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                <strong>Success!</strong> <?php echo $_SESSION['InvoiceItemAddedDetails']; ?>
                                                <?php unset($_SESSION['InvoiceItemAdded']); unset($_SESSION['InvoiceItemAddedDetails']); ?>
                                                <span type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </span>
                                            </div>
                                        <?php } elseif (isset($_SESSION['InvoiceItemNotAdded'])==true && empty($_SESSION['InvoiceItemNotAdded'])==false && $_SESSION['InvoiceItemNotAdded']=='notAdded') { ?>
                                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                <strong>Success!</strong> <?php echo $_SESSION['InvoiceItemNotAddedDetails']; ?>
                                                <?php unset($_SESSION['InvoiceItemNotAdded']); unset($_SESSION['InvoiceItemNotAddedDetails']); ?>
                                                <span type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </span>
                                            </div>
                                        <?php } elseif (isset($_GET['err'])==true && $_GET['err']=='noInvoiceItems') { ?>
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <strong>Error!</strong> Ensure you have entered all the invoice items
                                                <span type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </span>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="card card-outline card-dark" style="border-bottom: 3px solid #343a40;">
                                        <div class="card-header">
                                            <h6 class="text-dark font-weight-bold">Add invoice</h6>
                                        </div>
                                        <div class="card-body">
                                            <div>
                                                <div class="form-group col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                                    <?php
                                                        // Define the SQL query to select supplier_id and supplier_name from the suppliers table, 
                                                        // ordered by supplier_name in ascending order
                                                        $selectSuppliersSql ='SELECT `supplier_id`,`supplier_name` FROM `suppliers` ORDER BY `supplier_name` ASC';

                                                        // Prepare the SQL query using the database connection
                                                        $selectSuppliersStmt = $dbConnection->prepare($selectSuppliersSql);

                                                        // Execute the prepared SQL query
                                                        $selectSuppliersStmt->execute();

                                                        // Get the result of the executed query
                                                        $selectSuppliersResult = $selectSuppliersStmt->get_result();

                                                        // Check if the result contains more than 0 rows
                                                        if ($selectSuppliersResult->num_rows > 0) {
                                                            // Fetch all the rows from the result as an associative array
                                                            $supplier= mysqli_fetch_all($selectSuppliersResult, MYSQLI_ASSOC);
                                                        }
                                                    ?>
                                                    <?php
                                                        // Prepare a SQL query to select invoice reception details from the database
                                                        // where the reception invoice recipient matches the user's ID, limited to 1 result
                                                        $selectInvoiceReceptionDetailsSql ='SELECT `reception_invoice_number`, `reception_invoice_date`, `reception_invoice_supplier`,`reception_invoice_recipient` FROM `invoice_reception_details` WHERE `reception_invoice_recipient`=? LIMIT 1';

                                                        // Prepare the SQL statement for execution
                                                        $selectInvoiceReceptionDetailsStmt = $dbConnection->prepare($selectInvoiceReceptionDetailsSql);

                                                        // Bind the user's ID to the prepared statement as a string
                                                        $selectInvoiceReceptionDetailsStmt->bind_param('s', $_SESSION['user_id']);

                                                        // Execute the prepared statement
                                                        $selectInvoiceReceptionDetailsStmt->execute();

                                                        // Get the result from the executed statement
                                                        $selectInvoiceReceptionDetailsResult = $selectInvoiceReceptionDetailsStmt->get_result();

                                                        // Close the prepared statement to free up resources
                                                        $selectInvoiceReceptionDetailsStmt->close();

                                                        // Fetch the result as an associative array
                                                        $selectInvoiceReceptionDetailsAssoc= $selectInvoiceReceptionDetailsResult->fetch_assoc();
                                                    ?>
                                                    <?php if (empty($selectInvoiceReceptionDetailsAssoc['reception_invoice_supplier'])==true) { ?>
                                                        <form action="" method="post" class="card-body" onsubmit="return invoiceSupplierValidation();">
                                                            <label for="selectedSupplier" class="control-label">Select supplier<span class="ml-1 text-danger">*</span></label>
                                                            <span id="selectedSupplierStatus" class="d-block"></span>
                                                            <select name="selectedSupplier" id="selectedSupplier" class="form-control form-control-sm">
                                                                <option value="">Select supplier</option>
                                                                <!-- Loop through the $supplier array and create an option element for each supplier -->
                                                                <?php foreach ($supplier as $supplier) { ?>
                                                                    <option class="font-weight-bold" value="<?php echo $supplier['supplier_id']; ?>">
                                                                        <!-- Set the value of the option element to the supplier_id of the current supplier -->
                                                                        <?php echo $supplier['supplier_name']; ?>
                                                                        <!-- Display the supplier_name of the current supplier as the text of the option element -->
                                                                    </option>
                                                                <?php } ?>
                                                                <!-- End the foreach loop -->
                                                            </select>
                                                            <button type="submit" name="submitSupplier" id="submitSupplier" class="mt-1 btn btn-sm btn-primary" style="width: 115px;">Submit supplier</button>
                                                            <span name="submitSupplierLoader" id="submitSupplierLoader" class="mt-1 btn btn-sm btn-primary d-none" style="width: 115px;">
                                                                <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                                            </span>
                                                        </form>
                                                    <?php } elseif (empty($selectInvoiceReceptionDetailsAssoc['reception_invoice_supplier'])==false) { ?>
                                                        <?php
                                                            // Prepare a SQL statement to select the supplier name from the 'suppliers' table
                                                            // where the 'supplier_id' matches the 'reception_invoice_supplier' value from the
                                                            // $selectInvoiceReceptionDetailsAssoc array. The LIMIT 1 clause is used to ensure
                                                            // that only one result is returned.
                                                            $selectsupplierNameSql ='SELECT `supplier_name` FROM `suppliers` WHERE `supplier_id`=? LIMIT 1';

                                                            // Bind the 'reception_invoice_supplier' value from the $selectInvoiceReceptionDetailsAssoc
                                                            // array to the prepared statement as a string
                                                            $selectsupplierNameStmt = $dbConnection->prepare($selectsupplierNameSql);
                                                            $selectsupplierNameStmt->bind_param('s', $selectInvoiceReceptionDetailsAssoc['reception_invoice_supplier']);

                                                            // Execute the prepared statement
                                                            $selectsupplierNameStmt->execute();

                                                            // Get the result from the executed statement
                                                            $selectsupplierNameResult = $selectsupplierNameStmt->get_result();

                                                            // Close the prepared statement to free up resources
                                                            $selectsupplierNameStmt->close();

                                                            // Fetch the result as an associative array
                                                            $selectsupplierNameAssoc= $selectsupplierNameResult->fetch_assoc();
                                                        ?>
                                                        <div class="row">
                                                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-4 mt-2">
                                                                <span class="font-weight-bold">Supplier: <?php echo $selectsupplierNameAssoc['supplier_name']; ?></span>
                                                            </div>
                                                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-4 mt-2">
                                                                <?php if (empty($selectInvoiceReceptionDetailsAssoc['reception_invoice_date'])==true) { ?>
                                                                    <form action="" method="post" class="card-body" onsubmit="return invoiceDateValidation();">
                                                                        <label for="invoiceReceiptDate" class="control-label">Invoice date<span class="ml-1 text-danger">*</span></label>
                                                                        <span id="invoiceReceiptDateStatus" class="d-block"></span>
                                                                        <input type="date" name="invoiceReceiptDate" id="invoiceReceiptDate" class="form-control form-control-sm" placeholder="Invoice date" autocomplete="off">
                                                                        <button type="submit" name="submitDate" id="submitDate" class="mt-1 btn btn-sm btn-primary" style="width: 115px;">Submit date</button>
                                                                        <span name="submitDateLoader" id="submitDateLoader" class="mt-1 btn btn-sm btn-primary d-none" style="width: 115px;">
                                                                            <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                                                        </span>
                                                                    </form>
                                                                <?php } else { ?>
                                                                    <span class="font-weight-bold">Invoice date: <?php echo date('jS F Y', strtotime($selectInvoiceReceptionDetailsAssoc['reception_invoice_date'])); ?></span>
                                                            </div>
                                                                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-4 mt-2">
                                                                    <?php if (empty($selectInvoiceReceptionDetailsAssoc['reception_invoice_number'])==true) { ?>
                                                                        <form action="" method="post" class="card-body" onsubmit="return invoiceNumberValidation();">
                                                                            <label for="invoiceNumber" class="control-label">Invoice number<span class="ml-1 text-danger">*</span></label>
                                                                            <span id="invoiceNumberStatus" class="d-block"></span>
                                                                            <input type="text" name="invoiceNumber" id="invoiceNumber" class="form-control form-control-sm" placeholder="Invoice number" autocomplete="off">
                                                                            <button type="submit" name="submitInvoiceNumber" id="submitInvoiceNumber" class="mt-1 btn btn-sm btn-primary" style="width: 115px;">Submit</button>
                                                                            <span name="submitInvoiceNumberLoader" id="submitInvoiceNumberLoader" class="mt-1 btn btn-sm btn-primary d-none" style="width: 115px;">
                                                                                <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                                                            </span>
                                                                        </form>
                                                                    <?php } else { ?>
                                                                        <span class="font-weight-bold">Invoice number: <?php echo $selectInvoiceReceptionDetailsAssoc['reception_invoice_number']; ?></span>
                                                                    <?php } ?>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                        <?php if(empty($selectInvoiceReceptionDetailsAssoc['reception_invoice_number'])==false && empty($selectInvoiceReceptionDetailsAssoc['reception_invoice_date'])==false && empty($selectInvoiceReceptionDetailsAssoc['reception_invoice_supplier'])==false && empty($selectInvoiceReceptionDetailsAssoc['reception_invoice_recipient'])==false) { ?>
                                                            <button onclick="window.open('addToReception','popup','width=900,height=900'); return false;" class="btn btn-sm btn-primary text-nowrap text-light mt-2">Search product</button>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <script>
                                                function invoiceSupplierValidation() {
                                                    // Set a flag to track the validity of the input, default to true
                                                    var is_input_valid = true;

                                                    // Check if the selected supplier is empty
                                                    if (document.getElementById('selectedSupplier').value === "") {
                                                        // If empty, set the flag to false and display error messages
                                                        is_input_valid = false;
                                                        document.getElementById('selectedSupplier').style.border = "1px solid #dc3545";
                                                        document.getElementById('selectedSupplierStatus').style.color = "#dc3545";
                                                        document.getElementById('selectedSupplierStatus').innerHTML = "Please select the supplier";
                                                        
                                                        // Hide the loader and show the submit button
                                                        document.getElementById('submitSupplierLoader').className = "d-none";
                                                        document.getElementById('submitSupplier').className = "d-block mt-1 btn btn-sm btn-primary";
                                                        document.getElementById('submitSupplier').style.width = "115px";
                                                    } else {
                                                        // If not empty, set the border color to green and update the status message
                                                        document.getElementById('selectedSupplier').style.border = "1px solid #28a745";
                                                        document.getElementById('selectedSupplierStatus').innerHTML = "";
                                                        
                                                        // Hide the submit button and show the loader
                                                        document.getElementById('submitSupplier').className = "d-none";
                                                        document.getElementById('submitSupplierLoader').className = "d-block mt-1 btn btn-sm btn-primary";
                                                        document.getElementById('submitSupplierLoader').style.width = "115px";
                                                    }
                                                    
                                                    // Return the validity of the input
                                                    return is_input_valid;
                                                }

                                                // Function to validate the invoice date input
                                                function invoiceDateValidation() {
                                                    // Initialize a variable 'is_input_valid' with a default value of true
                                                    var is_input_valid = true;

                                                    // Check if the value of the 'invoiceReceiptDate' input field is empty
                                                    if (document.getElementById('invoiceReceiptDate').value === "") {
                                                        // If the input is empty, set 'is_input_valid' to false
                                                        is_input_valid = false;

                                                        // Display an error message and style the input field and status element
                                                        document.getElementById('invoiceReceiptDate').style.border = "1px solid #dc3545";
                                                        document.getElementById('invoiceReceiptDateStatus').style.color = "#dc3545";
                                                        document.getElementById('invoiceReceiptDateStatus').innerHTML = "Please enter the invoice date";

                                                        // Hide the submit button loader and show the submit button
                                                        document.getElementById('submitDateLoader').className = "d-none";
                                                        document.getElementById('submitDate').className = "d-block mt-1 btn btn-sm btn-primary";
                                                        document.getElementById('submitDate').style.width = "115px";
                                                    } else {
                                                        // If the input is not empty, style the input field and status element
                                                        document.getElementById('invoiceReceiptDate').style.border = "1px solid #28a745";
                                                        document.getElementById('invoiceReceiptDateStatus').innerHTML = "";

                                                        // Hide the submit button and show the submit button loader
                                                        document.getElementById('submitDate').className = "d-none";
                                                        document.getElementById('submitDateLoader').className = "d-block mt-1 btn btn-sm btn-primary";
                                                        document.getElementById('submitDateLoader').style.width = "115px";
                                                    }

                                                    // Return the value of 'is_input_valid'
                                                    return is_input_valid;
                                                }

                                                function invoiceNumberValidation() {
                                                    // Initialize a flag to track the validity of the input
                                                    var is_input_valid = true;

                                                    // Check if the invoice number field is empty
                                                    if (document.getElementById('invoiceNumber').value === "") {
                                                        // If empty, set the flag to false and display an error message
                                                        is_input_valid = false;
                                                        document.getElementById('invoiceNumber').style.border = "1px solid #dc3545";
                                                        document.getElementById('invoiceNumberStatus').style.color = "#dc3545";
                                                        document.getElementById('invoiceNumberStatus').innerHTML = "Please enter the invoice date";
                                                        
                                                        // Hide the loader and show the submit button
                                                        document.getElementById('submitInvoiceNumberLoader').className = "d-none";
                                                        document.getElementById('submitInvoiceNumber').className = "d-block mt-1 btn btn-sm btn-primary";
                                                        document.getElementById('submitInvoiceNumber').style.width = "115px";
                                                    } else {
                                                        // If not empty, set the border color to green and update the status message
                                                        document.getElementById('invoiceNumber').style.border = "1px solid #28a745";
                                                        document.getElementById('invoiceNumberStatus').innerHTML = "";
                                                        
                                                        // Hide the submit button and show the loader
                                                        document.getElementById('submitInvoiceNumber').className = "d-none";
                                                        document.getElementById('submitInvoiceNumberLoader').className = "d-block mt-1 btn btn-sm btn-primary";
                                                        document.getElementById('submitInvoiceNumberLoader').style.width = "115px";
                                                    }
                                                    
                                                    // Return the validity of the input
                                                    return is_input_valid;
                                                }
                                            </script>
                                            <div class="card-body table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl">
                                                <table class="text-nowrap table table-bordered table-hover table-striped" id="invoice_list_table">
                                                    <thead class="thead-dark text-light">
                                                        <th>Product code</th>
                                                        <th>Product name</th>
                                                        <th>Quantity</th>
                                                        <th>Buying price</th>
                                                        <th>Total price</th>
                                                        <th class="col-1">Actions</th>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                            // Prepare a SQL query to fetch product data from the database
                                                            $fetchProductsInReceptionSql = 'SELECT `product_code`, `product_name`, `product_quantity`, `product_buying_price`, `product_total_buying_price` FROM `invoice_reception_products` WHERE `reception_invoice_recipient`=? ORDER BY `time_added` ASC';

                                                            // Prepare the SQL statement
                                                            $fetchProductsInReceptionStmt = $dbConnection->prepare($fetchProductsInReceptionSql);
                                                            
                                                            // Bind the current user's user_id session variable to the SQL SELECT statement
                                                            $fetchProductsInReceptionStmt->bind_param('s', $_SESSION['user_id']);

                                                            // Execute the prepared statement
                                                            $fetchProductsInReceptionStmt->execute();

                                                            // Retrieve the result set
                                                            $fetchProductsInReceptionResult = $fetchProductsInReceptionStmt->get_result();
                                                            
                                                            // Close the prepared statement to free up resources
                                                            $fetchProductsInReceptionStmt->close();

                                                            // Fetch data as an associative array
                                                            while ($fetchProductsInReceptionAssoc= $fetchProductsInReceptionResult->fetch_assoc()) {
                                                        ?>
                                                            <tr>
                                                                <td class="font-weight-bold"><?php echo $fetchProductsInReceptionAssoc['product_code']; ?></td>
                                                                <td>
                                                                    <?php if (empty($fetchProductsInReceptionAssoc['product_name'])==true) { ?>
                                                                        Not set
                                                                    <?php }else{ ?>
                                                                        <?php echo $fetchProductsInReceptionAssoc['product_name']; ?>
                                                                    <?php } ?>
                                                                </td>
                                                                <td>
                                                                    <?php if (empty($fetchProductsInReceptionAssoc['product_quantity'])==true) { ?>
                                                                        Not set
                                                                    <?php }else{ ?>
                                                                        <?php echo number_format($fetchProductsInReceptionAssoc['product_quantity'], 2); ?>
                                                                    <?php } ?>
                                                                </td>
                                                                <td>
                                                                    <?php if (empty($fetchProductsInReceptionAssoc['product_buying_price'])==true) { ?>
                                                                        Not set
                                                                    <?php }else{ ?>
                                                                        <?php echo number_format($fetchProductsInReceptionAssoc['product_buying_price'], 2); ?>
                                                                    <?php } ?>
                                                                </td>
                                                                <td>
                                                                    <?php if (empty($fetchProductsInReceptionAssoc['product_total_buying_price'])==true) { ?>
                                                                        Not set
                                                                    <?php }else{ ?>
                                                                        <?php echo number_format($fetchProductsInReceptionAssoc['product_total_buying_price'], 2); ?>
                                                                    <?php } ?>
                                                                </td>
                                                                <td>
                                                                    <div class="btn-group" role="group">
                                                                        <span data-toggle="modal" data-target="#editProduct<?php echo $fetchProductsInReceptionAssoc['product_code']; ?>" name="editProduct<?php echo $fetchProductsInReceptionAssoc['product_code']; ?>" id="editProduct<?php echo $fetchProductsInReceptionAssoc['product_code']; ?>" class="mr-1 btn btn-md btn-secondary text-nowrap text-light" style="width: 100px;"><i class="fas fa-edit mr-2"></i>Edit</a></span>
                                                                        <span data-toggle="modal" data-target="#removeProduct<?php echo $fetchProductsInReceptionAssoc['product_code']; ?>" name="removeProduct<?php echo $fetchProductsInReceptionAssoc['product_code']; ?>" id="removeProduct<?php echo $fetchProductsInReceptionAssoc['product_code']; ?>" class="btn btn-md btn-danger text-nowrap text-light" style="width: 100px;"><i class="fas fa-trash-alt mr-2"></i>Remove</span>
                                                                    </div>
                                                                </td>

                                                                <div class="modal fade" id="editProduct<?php echo $fetchProductsInReceptionAssoc['product_code']; ?>" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="editProduct<?php echo $fetchProductsInReceptionAssoc['product_code']; ?>Label" aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title" id="editProduct<?php echo $fetchProductsInReceptionAssoc['product_code']; ?>Label">Edit <?php echo $fetchProductsInReceptionAssoc['product_name']; ?></h5>
                                                                                <span type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                </button>
                                                                            </div>
                                                                            <form action="" method="post" onsubmit="return editProductValidation<?php echo $fetchProductsInReceptionAssoc['product_code']; ?>();">
                                                                                <div class="modal-body">
                                                                                    <div class="form-group">
                                                                                        <label for="editQuantity" class="font-weight-bold">Quantity<span class="text-danger ml-1">*</span></label>
                                                                                        <input type="tel" autocomplete="off" name="editQuantity" id="editQuantity" class="mt-1 form-control form-contol-sm" placeholder="Quantity" value="<?php echo number_format($fetchProductsInReceptionAssoc['product_quantity'], 2); ?>">
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label for="editBuyingPrice" class="font-weight-bold">Buying price<span class="text-danger ml-1">*</span></label>
                                                                                        <input type="tel" autocomplete="off" name="editBuyingPrice" id="editBuyingPrice" class="mt-1 form-control form-contol-sm" placeholder="Buying price" value="<?php echo number_format($fetchProductsInReceptionAssoc['product_buying_price'], 2); ?>">
                                                                                    </div>
                                                                                    <input type="hidden" name="productCodeEditing" id="productCodeEditing" class="mt-1 form-control form-contol-sm" placeholder="Quantity" value="<?php echo $fetchProductsInReceptionAssoc['product_code']; ?>">
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <div class="row container">
                                                                                        <div class="col"><button type="button" name="dismisseditProductModal" id="dismisseditProductModal" class="float-left text-light btn btn-secondary" style="width: 100px;" data-dismiss="modal">No</button></div>
                                                                                        <div class="col">
                                                                                            <button type="submit" name="submiteditProduct" id="submiteditProduct<?php echo $fetchProductsInReceptionAssoc['product_code']; ?>" class="float-right text-light btn btn-primary" style="width: 100px;">Yes</button>
                                                                                            <span name="editProductLoader" id="editProductLoader<?php echo $fetchProductsInReceptionAssoc['product_code']; ?>" class="d-none float-right text-light btn btn-primary" style="width: 100px;">
                                                                                                <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                                                                            </span>
                                                                                        </div>
                                                                                        <script>
                                                                                            // Function to validate and clear an invoice
                                                                                            function editProductValidation<?php echo $fetchProductsInReceptionAssoc['product_code']; ?>() {
                                                                                                // Initialize a variable to store the input validation status
                                                                                                var is_input_valid = true;

                                                                                                // Hide the submit button and display the loading indicator
                                                                                                document.getElementById('submiteditProduct<?php echo $fetchProductsInReceptionAssoc['product_code']; ?>').className = "d-none";
                                                                                                document.getElementById('editProductLoader<?php echo $fetchProductsInReceptionAssoc['product_code']; ?>').className = "d-block float-right text-light btn btn-primary";

                                                                                                // Set the width of the loading indicator
                                                                                                document.getElementById('editProductLoader<?php echo $fetchProductsInReceptionAssoc['product_code']; ?>').style="width: 100px";

                                                                                                // Return the input validation status
                                                                                                return is_input_valid;
                                                                                            }
                                                                                        </script>
                                                                                    </div>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="modal fade" id="removeProduct<?php echo $fetchProductsInReceptionAssoc['product_code']; ?>" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="removeProduct<?php echo $fetchProductsInReceptionAssoc['product_code']; ?>Label" aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title" id="removeProduct<?php echo $fetchProductsInReceptionAssoc['product_code']; ?>Label">Edit <?php echo $fetchProductsInReceptionAssoc['product_name']; ?></h5>
                                                                                <span type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                </button>
                                                                            </div>
                                                                            <form action="" method="post" onsubmit="return removeProductValidation<?php echo $fetchProductsInReceptionAssoc['product_code']; ?>();">
                                                                                <div class="modal-body">
                                                                                    Are you sure you want remove <?php echo $fetchProductsInReceptionAssoc['product_name']; ?>
                                                                                    <input type="hidden" name="productCodeRemove" id="productCodeRemove" class="mt-1 form-control form-contol-sm" placeholder="Quantity" value="<?php echo $fetchProductsInReceptionAssoc['product_code']; ?>">
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <div class="row container">
                                                                                        <div class="col"><button type="button" name="dismissremoveProductModal" id="dismissremoveProductModal" class="float-left text-light btn btn-secondary" style="width: 100px;" data-dismiss="modal">No</button></div>
                                                                                        <div class="col">
                                                                                            <button type="submit" name="submitremoveProduct" id="submitremoveProduct<?php echo $fetchProductsInReceptionAssoc['product_code']; ?>" class="float-right text-light btn btn-primary" style="width: 100px;">Yes</button>
                                                                                            <span name="removeProductLoader" id="removeProductLoader<?php echo $fetchProductsInReceptionAssoc['product_code']; ?>" class="d-none float-right text-light btn btn-primary" style="width: 100px;">
                                                                                                <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                                                                            </span>
                                                                                        </div>
                                                                                        <script>
                                                                                            // Function to validate and clear an invoice
                                                                                            function removeProductValidation<?php echo $fetchProductsInReceptionAssoc['product_code']; ?>() {
                                                                                                // Initialize a variable to store the input validation status
                                                                                                var is_input_valid = true;

                                                                                                // Hide the submit button and display the loading indicator
                                                                                                document.getElementById('submitremoveProduct<?php echo $fetchProductsInReceptionAssoc['product_code']; ?>').className = "d-none";
                                                                                                document.getElementById('removeProductLoader<?php echo $fetchProductsInReceptionAssoc['product_code']; ?>').className = "d-block float-right text-light btn btn-primary";

                                                                                                // Set the width of the loading indicator
                                                                                                document.getElementById('removeProductLoader<?php echo $fetchProductsInReceptionAssoc['product_code']; ?>').style="width: 100px";

                                                                                                // Return the input validation status
                                                                                                return is_input_valid;
                                                                                            }
                                                                                        </script>
                                                                                    </div>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </tr>
                                                        <?php } ?>
                                                        <tr>
                                                            <td colspan="4"></td>
                                                            <?php
                                                                $selectTotalPriceSql = 'SELECT SUM(`product_total_buying_price`) AS `product_total_buying_price` FROM `invoice_reception_products` WHERE `reception_invoice_recipient` =?';
                                                                // Prepare the SQL statement
                                                                $selectTotalPriceStmt = $dbConnection->prepare($selectTotalPriceSql);
                                                                
                                                                // Bind the current user's user_id session variable to the SQL SELECT statement
                                                                $selectTotalPriceStmt->bind_param('s', $_SESSION['user_id']);

                                                                // Execute the prepared statement
                                                                $selectTotalPriceStmt->execute();

                                                                // Retrieve the result set
                                                                $selectTotalPriceResult = $selectTotalPriceStmt->get_result();
                                                                
                                                                // Close the prepared statement to free up resources
                                                                $selectTotalPriceStmt->close();

                                                                // Fetch data as an associative array
                                                                $selectTotalPriceAssoc= $selectTotalPriceResult->fetch_assoc();
                                                                
                                                                if (empty($selectTotalPriceAssoc['product_total_buying_price'])==true) {
                                                            ?>
                                                            <td class="font-weight-bold"><?php echo $currency_one.'. 0.00' ?></td>
                                                            <?php } elseif ($selectTotalPriceAssoc['product_total_buying_price'] <= '1') { ?>
                                                                <td class="font-weight-bold"><?php echo $currency_one.'. '.number_format($selectTotalPriceAssoc['product_total_buying_price'], 2); ?></td>
                                                            <?php } elseif ($selectTotalPriceAssoc['product_total_buying_price'] > '1') { ?>
                                                                <td class="font-weight-bold"><?php echo $currency_many.'. '.number_format($selectTotalPriceAssoc['product_total_buying_price'], 2); ?></td>
                                                            <?php } ?>
                                                            <td>
                                                                <button type="button" data-toggle="modal" data-target="#submitInvoiceModal" name="submitInvoiceModalTrigger" id="submitInvoiceModalTrigger" class="btn text-nowrap text-light btn-block btn-primary float-right ">
                                                                    Submit
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="row">
                                                <?php if(empty($selectInvoiceReceptionDetailsAssoc['reception_invoice_number'])==false || empty($selectInvoiceReceptionDetailsAssoc['reception_invoice_date'])==false || empty($selectInvoiceReceptionDetailsAssoc['reception_invoice_supplier'])==false || empty($selectInvoiceReceptionDetailsAssoc['reception_invoice_recipient'])==false) { ?>
                                                    <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 mt-1"><button type="button" data-toggle="modal" data-target="#clearInvoiceModal" name="clearInvoiceModalTrigger" id="clearInvoiceModalTrigger" class="btn text-nowrap text-light btn-sm btn-danger">Clear invoice</button></div>
                                                <?php } else { ?>
                                                    <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 mt-1"></div>
                                                <?php } ?>

                                                <div class="modal fade" id="clearInvoiceModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="clearInvoiceModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-scrollable">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="clearInvoiceModalLabel">Clear Invoice modal</h5>
                                                                <span type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </span>
                                                            </div>
                                                            <div class="modal-body">
                                                                Are you sure you want to clear the invoice?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <div class="row container">
                                                                    <form action="" method="post" class="card-body" onsubmit="return clearInVoiceValidation();">
                                                                        <div class="col"><button type="button" name="dismissClearInvoiceModal" id="dismissClearInvoiceModal" class="float-left text-light btn btn-secondary" style="width: 100px;" data-dismiss="modal">No</button></div>
                                                                        <div class="col">
                                                                            <button type="submit" name="submitClearInvoice" id="submitClearInvoice" class="float-right text-light btn btn-primary" style="width: 100px;">Yes</button>
                                                                            <span name="clearInvoiceLoader" id="clearInvoiceLoader" class="d-none float-right text-light btn btn-primary" style="width: 100px;">
                                                                                <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                                                            </span>
                                                                        </div>
                                                                    </form>
                                                                    <script>
                                                                        // Function to validate and clear an invoice
                                                                        function clearInVoiceValidation() {
                                                                            // Initialize a variable to store the input validation status
                                                                            var is_input_valid = true;

                                                                            // Hide the submit button and display the loading indicator
                                                                            document.getElementById('submitClearInvoice').className = "d-none";
                                                                            document.getElementById('clearInvoiceLoader').className = "d-block float-right text-light btn btn-primary";

                                                                            // Set the width of the loading indicator
                                                                            document.getElementById('clearInvoiceLoader').style.width = "100px";

                                                                            // Return the input validation status
                                                                            return is_input_valid;
                                                                        }
                                                                    </script>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal fade" id="submitInvoiceModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="submitInvoiceModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-scrollable">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="submitInvoiceModalLabel">Save Invoice</h5>
                                                                <span type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </span>
                                                            </div>
                                                            <div class="modal-body">
                                                                Are you sure you want to save the invoice?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <div class="row container">
                                                                    <form action="" method="post" class="card-body" onsubmit="return saveInVoiceValidation();">
                                                                        <div class="col"><button type="button" name="dismisssubmitInvoiceModal" id="dismisssubmitInvoiceModal" class="float-left text-light btn btn-secondary" style="width: 100px;" data-dismiss="modal">No</button></div>
                                                                        <div class="col">
                                                                            <button type="submit" name="submitSaveInvoice" id="submitSaveInvoice" class="float-right text-light btn btn-primary" style="width: 100px;">Yes</button>
                                                                            <span name="saveInvoiceLoader" id="saveInvoiceLoader" class="d-none float-right text-light btn btn-primary" style="width: 100px;">
                                                                                <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                                                            </span>
                                                                        </div>
                                                                    </form>
                                                                    <script>
                                                                        // Function to validate and save an invoice
                                                                        function saveInVoiceValidation() {
                                                                            // Initialize a variable to store the input validation status
                                                                            var is_input_valid = true;

                                                                            // Hide the submit button and display the loading indicator
                                                                            document.getElementById('submitSaveInvoice').className = "d-none";
                                                                            document.getElementById('saveInvoiceLoader').className = "d-block float-right text-light btn btn-primary";

                                                                            // Set the width of the loading indicator
                                                                            document.getElementById('saveInvoiceLoader').style.width = "100px";

                                                                            // Return the input validation status
                                                                            return is_input_valid;
                                                                        }
                                                                    </script>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
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
                <!-- <script>
                    $(document).ready( function (){$('#invoice_list_table').DataTable();});
                </script> -->
            </body>
        </html>
<?php $dbConnection->close(); // Close the database connection ?>