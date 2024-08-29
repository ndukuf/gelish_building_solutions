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

    // Check if the 'tsk' variable is set in the GET request and if it is not empty
    if (isset($_GET['tsk']) == false || empty($_GET['tsk']) == true) {
        // If the 'tsk' variable is not set or is empty, redirect the user to the 'suppliers' page with a 'view_suppliers' query parameter
        header('Location: suppliers?tsk=view_suppliers');
        exit();
    }

    // Check if the 'tsk' parameter in the GET request is 'edit_supplier' and the 'id' parameter is empty
    if ($_GET['tsk'] == 'edit_supplier' && empty($_GET['id']) == true) {
        // If 'tsk' is 'edit_supplier' and 'id' is empty, redirect the user to the 'suppliers' page with a 'view_suppliers' task
        header('Location: suppliers?tsk=view_suppliers');
        exit();
    }

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'add_supplier_btn' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['add_supplier_btn']) == true) {
        // Sanitize and validate user input data to prevent SQL injection and cross-site scripting (XSS) attacks

        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the username input
        $add_supplier_name = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['add_supplier_name']), ENT_QUOTES, 'UTF-8')));

        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the phone number input
        $add_supplier_phone = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['add_supplier_phone']), ENT_QUOTES, 'UTF-8')));

        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the email input
        $add_supplier_email = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['add_supplier_email']), ENT_QUOTES, 'UTF-8')));

        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the password input
        $add_supplier_location = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['add_supplier_location']), ENT_QUOTES, 'UTF-8')));

        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the confirm password input
        $add_supplier_address = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['add_supplier_address']), ENT_QUOTES, 'UTF-8')));

        // Check if the supplier name is empty
        if (empty($add_supplier_name) == true) {
            // Redirect to the supliers page with an error message for empty supplier name
            header('Location: suppliers?tsk=add_supplier&addSupplierError=empty_supplier_name');
            // Exit the current script
            exit();
        }
        
        // Check if the phone number is empty
        if (empty($add_supplier_phone) == true) {
            // Redirect to the supliers page with an error message for empty phone number
            header('Location: suppliers?tsk=add_supplier&addSupplierError=empty_supplier_phone');
            // Exit the current script
            exit();
        }

        // Check if the email is empty
        if (empty($add_supplier_email) == true) {
            // Redirect to the supliers page with an error message for empty email
            header('Location: suppliers?tsk=add_supplier&addSupplierError=empty_supplier_email');
            // Exit the current script
            exit();
        }

        // Check if the supplier location is empty
        if (empty($add_supplier_location) == true) {
            // Redirect to the supliers page with an error message for empty supplier location
            header('Location: suppliers?tsk=add_supplier&addSupplierError=empty_supplier_location');
            // Exit the current script
            exit();
        }

        // Check if the supplier address is empty
        if (empty($add_supplier_address) == true) {
            // Redirect to the supliers page with an error message for empty supplier address
            header('Location: suppliers?tsk=add_supplier&addSupplierError=empty_supplier_address');
            // Exit the current script
            exit();
        }

        // Check if the length of the phone number is less than 10
        if (strlen($add_supplier_phone) < 10) {
            // Redirect the user to the add_supliers page with an error message indicating an invalid phone number
            header('Location: suppliers?tsk=add_supplier&addSupplierError=invalid_supplier_phone');
            // Stop the execution of the script
            exit();
        }

        // Check if the email provided is in a valid format
        if (!filter_var($add_supplier_email, FILTER_VALIDATE_EMAIL)) {
            // Redirect the user to the suppliers page with an error message indicating an invalid email
            header('Location: suppliers?tsk=add_supplier&addSupplierError=invalid_supplier_email');
            // Exit the current script
            exit();
        }

        // Prepare a SQL statement to select the supplier_name from the 'suppliers' table where the supplier_name matches the provided add_supplier_name
        $selectSupplierNameSql = 'SELECT `supplier_name` FROM `suppliers` WHERE `supplier_name` =?';
        
        // Prepare the SQL statement
        $selectSupplierNameStmt = $dbConnection->prepare($selectSupplierNameSql);

        // Bind the add_supplier_name variable to the prepared statement as a string
        $selectSupplierNameStmt->bind_param('s', $add_supplier_name);

        // Execute the prepared statement
        $selectSupplierNameStmt->execute();

        // Store the result of the executed statement
        $selectSupplierNameStmt->store_result();

        // Check if the number of rows returned by the executed statement is 1, indicating that the supplier's name already exists in the 'suppliers' table
        if ($selectSupplierNameStmt->num_rows===1) {
            // Redirect the user to the suppliers page with an error message indicating that the supplier's name already exists
            header('Location: suppliers?tsk=add_supplier&addSupplierError=supplier_name_exists');
            // Exit the current script to prevent further execution
            exit();
        }
        // Close the prepared statement to free up resources
        $selectSupplierNameStmt->close();

        // Prepare a SQL statement to select the supplier_phone_number from the 'suppliers' table where the supplier_phone_number matches the provided add_supplier_phone
        $selectSupplierPhoneNumberSql = 'SELECT `supplier_phone_number` FROM `suppliers` WHERE `supplier_phone_number` =?';
        
        // Prepare the SQL statement
        $selectSupplierPhoneNumberStmt = $dbConnection->prepare($selectSupplierPhoneNumberSql);

        // Bind the add_supplier_phone variable to the prepared statement as a string
        $selectSupplierPhoneNumberStmt->bind_param('s', $add_supplier_phone);

        // Execute the prepared statement
        $selectSupplierPhoneNumberStmt->execute();

        // Store the result of the executed statement
        $selectSupplierPhoneNumberStmt->store_result();

        // Check if the number of rows returned by the executed statement is 1, indicating that the phone already exists in the 'suppliers' table
        if ($selectSupplierPhoneNumberStmt->num_rows===1) {
            // Redirect the user to the suppliers page with an error message indicating that the phone already exists
            header('Location: suppliers?tsk=add_supplier&addSupplierError=supplier_phone_exists');
            // Exit the current script to prevent further execution
            exit();
        }
        // Close the prepared statement to free up resources
        $selectSupplierPhoneNumberStmt->close();

        // Prepare a SQL statement to select the supplier_email_address from the 'suppliers' table where the supplier_email_address matches the provided add_supplier_email
        $selectSupllierEmailAddressSql = 'SELECT `supplier_email_address` FROM `suppliers` WHERE `supplier_email_address` =?';
        
        // Prepare the SQL statement
        $selectSupllierEmailAddressStmt = $dbConnection->prepare($selectSupllierEmailAddressSql);

        // Bind the add_supplier_email variable to the prepared statement as a string
        $selectSupllierEmailAddressStmt->bind_param('s', $add_supplier_email);

        // Execute the prepared statement
        $selectSupllierEmailAddressStmt->execute();

        // Store the result of the executed statement
        $selectSupllierEmailAddressStmt->store_result();

        // Check if the number of rows returned by the executed statement is 1, indicating that the email address already exists in the 'suppliers' table
        if ($selectSupllierEmailAddressStmt->num_rows===1) {
            // Redirect the user to the suppliers page with an error message indicating that the email address already exists
            header('Location: suppliers?tsk=add_supplier&addSupplierError=supplier_email_exists');
            // Exit the current script to prevent further execution
            exit();
        }
        // Close the prepared statement to free up resources
        $selectSupllierEmailAddressStmt->close();

        // Generate a random salt for the user ID
        $randomCharactersSalt = 'GBS';

        // Generate two random characters, convert them to uppercase hexadecimal for uniqueness
        $randomCharacters01 = strtoupper(bin2hex(random_bytes(2)));
        $randomCharacters02 = strtoupper(bin2hex(random_bytes(2)));

        // Concatenate the random characters and the salt to create the user ID
        $supplier_id = $randomCharacters01.$randomCharactersSalt.$randomCharacters02;

        // Prepare a SQL query to fetch user_id from the database
        $fetchUserIdSql = 'SELECT `user_id` FROM `users` WHERE `user_id`=? LIMIT 1';

        // Prepare the SQL statement
        $fetchUserIdStmt = $dbConnection->prepare($fetchUserIdSql);

        // Bind parameters to the prepared statement
        $fetchUserIdStmt->bind_param('s',$supplier_id);

        // Execute the prepared statement
        $fetchUserIdStmt->execute();

        // Fetch the result of the prepared statement
        $fetchUserIdResult = $fetchUserIdStmt->get_result();

        // Prepare a SQL query to fetch supplier_id from the database
        $fetchSupplierIdSql = 'SELECT `supplier_id` FROM `suppliers` WHERE `supplier_id`=? LIMIT 1';

        // Prepare the SQL statement
        $fetchSupplierIdStmt = $dbConnection->prepare($fetchSupplierIdSql);

        // Bind parameters to the prepared statement
        $fetchSupplierIdStmt->bind_param('s',$supplier_id);

        // Execute the prepared statement
        $fetchSupplierIdStmt->execute();

        // Fetch the result of the prepared statement
        $fetchSupplierIdResult = $fetchSupplierIdStmt->get_result();

        do {
            // Generate a random salt for the user ID
            $randomCharactersSalt = 'GBS';
            
            // Generate two random characters, convert them to uppercase hexadecimal for uniqueness
            $randomCharacters01 = strtoupper(bin2hex(random_bytes(2)));
            $randomCharacters02 = strtoupper(bin2hex(random_bytes(2)));
        
            // Concatenate the random characters with the predefined salt to create a unique supplier ID
            $supplier_id = $randomCharacters01.$randomCharactersSalt.$randomCharacters02;
        } while ($fetchUserIdResult->num_rows > 0 || $fetchSupplierIdResult->num_rows > 0); // Loop until a unique supplier ID is generated, ensuring no duplicates

        $supplier_status = '5';
        // Record the current date and time as the date the user joined
        $date_created = $currentDateAndTime;

        // Define the SQL query to add a new supplier to the 'suppliers' table
        $addNewSupplierSql = 'INSERT INTO `suppliers`(`supplier_id`, `supplier_status`, `supplier_name`, `supplier_phone_number`, `date_created`, `supplier_email_address`, `supplier_location`, `supplier_address`, `created_by`) VALUES (?,?,?,?,?,?,?,?,?)';

        // Prepare the SQL query statement
        $addNewSupplierStmt = $dbConnection->prepare($addNewSupplierSql);

        // Bind the parameters to the prepared statement
        $addNewSupplierStmt->bind_param('sssssssss', $supplier_id,$supplier_status,$add_supplier_name,$add_supplier_phone,$date_created,$add_supplier_email,$add_supplier_location,$add_supplier_address,$_SESSION['user_id']);

        // Attempt to execute the statement for adding a new supplier
        if ($addNewSupplierStmt->execute()) {
            // Close the prepared statement to free up resources
            $addNewSupplierStmt->close();
            // If the supplier is successfully added, store the status in the session
            $_SESSION['addSupplierStatus'] = 'supplierAdded';
            // Redirect the user to the suppliers page with a success message
            header('Location: suppliers?tsk=add_supplier&addSupplier');
            // Stop further execution of the script
            exit();
        } else {
            // Close the prepared statement to free up resources
            $addNewSupplierStmt->close();
            // If the supplier is not added, store the status in the session
            $_SESSION['addSupplierStatus'] = 'supplierNotAdded';
            // Redirect the user to the suppliers page with a success message
            header('Location: suppliers?tsk=add_supplier&addSupplier');
            // Stop further execution of the script
            exit();
        }
    }

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'edit_supplier_btn' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['edit_supplier_btn']) == true) {
        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the inputs
        $get_edit_supplier_supplier_id = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_GET['id']), ENT_QUOTES, 'UTF-8')));
        $edit_supplier_supplier_id = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['edit_supplier_supplier_id']), ENT_QUOTES, 'UTF-8')));

        // Check if the supplier ID from the GET parameter matches the one in the database
        if ($get_edit_supplier_supplier_id!== $edit_supplier_supplier_id) {
            // If the IDs do not match, redirect the user to the suppliers page with an error message
            header('Location: suppliers?tsk=edit_supplier&id='.$get_edit_supplier_supplier_id.'&error=supplierIdNotMatch');
            exit();
        }

        // Prepare a SQL statement to fetch the initial details of a supplier from the database
        $fetchSupplierInitialDetailsSql = 'SELECT `supplier_status`,`supplier_name`,`supplier_phone_number`,`supplier_email_address`,`supplier_location`,`supplier_address` FROM `suppliers` WHERE `supplier_id` =? LIMIT 1';

        // Prepare the SQL statement for execution
        $fetchSupplierInitialDetailsStmt = $dbConnection->prepare($fetchSupplierInitialDetailsSql);

        // Bind the supplier_id parameter to the prepared statement
        $fetchSupplierInitialDetailsStmt->bind_param('s', $edit_supplier_supplier_id);

        // Execute the prepared statement
        $fetchSupplierInitialDetailsStmt->execute();

        // Store the result of the execution
        $fetchSupplierInitialDetailsResult = $fetchSupplierInitialDetailsStmt->get_result();

        // Close the prepared statement to free up resources
        $fetchSupplierInitialDetailsStmt->close();

        // Fetch the result as an associative array
        $fetchSupplierInitialDetailsAssoc= $fetchSupplierInitialDetailsResult->fetch_assoc();

        // Escape and sanitize the supplier name input to prevent SQL injection and ensure proper encoding
        $edit_supplier_name = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['edit_supplier_name']), ENT_QUOTES, 'UTF-8')));

        // Check if the $edit_supplier_name variable is not empty and if it is different from the current supplier name
        if (empty($edit_supplier_name)==false && $fetchSupplierInitialDetailsAssoc['supplier_name'] !== $edit_supplier_name) {
            // SQL statement to select the supplier name from the 'suppliers' table where the supplier_name matches the $edit_supplier_name
            $selectSupplierNameEditSql = 'SELECT `supplier_name` FROM `suppliers` WHERE `supplier_name` =?';

            // Prepare a SQL statement
            $selectSupplierNameEditStmt = $dbConnection->prepare($selectSupplierNameEditSql);
            
            // Bind the $edit_supplier_name variable to the prepared statement
            $selectSupplierNameEditStmt->bind_param('s', $edit_supplier_name);

            // Execute the prepared statement
            $selectSupplierNameEditStmt->execute();

            // Store the result of the executed statement
            $selectSupplierNameEditStmt->store_result();

            // Check if there is no row in the result
            if ($selectSupplierNameEditStmt->num_rows == 0) {
                // Prepare an SQL statement to update the supplier name in the 'suppliers' table
                $updateSupplierNameSql = 'UPDATE `suppliers` SET `supplier_name`=? WHERE `supplier_id`=? AND `supplier_name`=?';

                // Prepare a statement object using the database connection and the SQL statement
                $updateSupplierNameStmt = $dbConnection->prepare($updateSupplierNameSql);

                // Bind the parameters to the prepared statement
                $updateSupplierNameStmt->bind_param('sss', $edit_supplier_name,$edit_supplier_supplier_id,$fetchSupplierInitialDetailsAssoc['supplier_name']);

                // Execute the prepared statement to perform the update
                $updateSupplierNameStmt->execute();
            }
        }
        
        // Escape and sanitize the supplier phone input to prevent SQL injection and ensure proper encoding
        $edit_supplier_phone = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['edit_supplier_phone']), ENT_QUOTES, 'UTF-8')));

        // Check if the $edit_supplier_phone variable is not empty and its value is different from the current supplier phone number and also check if the length of $edit_supplier_phone is exactly 10 characters
        if (empty($edit_supplier_phone)==false && $fetchSupplierInitialDetailsAssoc['supplier_phone_number'] !== $edit_supplier_phone && strlen($edit_supplier_phone) == 10) {
            // SQL statement to select the phone number from the 'users' table where the user_phone_number matches the $edit_supplier_phone
            $selectSupplierPhoneEditSql00 = 'SELECT `user_phone_number` FROM `users` WHERE `user_phone_number` =?';

            // Prepare a SQL statement
            $selectSupplierPhoneEditStmt00 = $dbConnection->prepare($selectSupplierPhoneEditSql00);
            
            // Bind the $edit_supplier_phone variable to the prepared statement
            $selectSupplierPhoneEditStmt00->bind_param('s', $edit_supplier_phone);

            // Execute the prepared statement
            $selectSupplierPhoneEditStmt00->execute();

            // Store the result of the executed statement
            $selectSupplierPhoneEditStmt00->store_result();
            
            // SQL statement to select the phone number from the 'suppliers' table where the supplier_phone_number matches the $edit_supplier_phone
            $selectSupplierPhoneEditSql01 = 'SELECT `supplier_phone_number` FROM `suppliers` WHERE `supplier_phone_number` =?';

            // Prepare a SQL statement
            $selectSupplierPhoneEditStmt01 = $dbConnection->prepare($selectSupplierPhoneEditSql01);
            
            // Bind the $edit_supplier_phone variable to the prepared statement
            $selectSupplierPhoneEditStmt01->bind_param('s', $edit_supplier_phone);

            // Execute the prepared statement
            $selectSupplierPhoneEditStmt01->execute();

            // Store the result of the executed statement
            $selectSupplierPhoneEditStmt01->store_result();

            // Check if there is no row in the result
            if ($selectSupplierPhoneEditStmt00->num_rows == 0 && $selectSupplierPhoneEditStmt01->num_rows == 0) {
                // Prepare an SQL statement to update the supplier phone in the 'suppliers' table
                $updateSupplierPhoneEditSql = 'UPDATE `suppliers` SET `supplier_phone_number`=? WHERE `supplier_id`=? AND `supplier_phone_number`=?';

                // Prepare a statement object using the database connection and the SQL statement
                $updateSupplierPhoneEditStmt = $dbConnection->prepare($updateSupplierPhoneEditSql);

                // Bind the parameters to the prepared statement
                $updateSupplierPhoneEditStmt->bind_param('sss', $edit_supplier_phone,$edit_supplier_supplier_id,$fetchSupplierInitialDetailsAssoc['supplier_phone_number']);

                // Execute the prepared statement to perform the update
                $updateSupplierPhoneEditStmt->execute();
            }
        }
        
        // Escape and sanitize the supplier email input to prevent SQL injection and ensure proper encoding
        $edit_supplier_email = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['edit_supplier_email']), ENT_QUOTES, 'UTF-8')));

        // Check if $edit_supplier_email is not empty, and if it is different from the current supplier email address in the database, and if it is in a valid format
        if (empty($edit_supplier_email)==false && $fetchSupplierInitialDetailsAssoc['supplier_email_address'] !== $edit_supplier_email && filter_var($edit_supplier_email, FILTER_VALIDATE_EMAIL)) {
            // SQL statement to select the email address from the 'users' table where the email_address matches the $edit_supplier_email
            $selectSupplierEmailEditSql00 = 'SELECT `email_address` FROM `users` WHERE `email_address` =?';

            // Prepare a SQL statement
            $selectSupplierEmailEditStmt00 = $dbConnection->prepare($selectSupplierEmailEditSql00);
            
            // Bind the $edit_supplier_email variable to the prepared statement
            $selectSupplierEmailEditStmt00->bind_param('s', $edit_supplier_email);

            // Execute the prepared statement
            $selectSupplierEmailEditStmt00->execute();

            // Store the result of the executed statement
            $selectSupplierEmailEditStmt00->store_result();
            
            // SQL statement to select the email address from the 'suppliers' table where the supplier_email_address matches the $edit_supplier_email
            $selectSupplierEmailEditSql01 = 'SELECT `supplier_email_address` FROM `suppliers` WHERE `supplier_email_address` =?';

            // Prepare a SQL statement
            $selectSupplierEmailEditStmt01 = $dbConnection->prepare($selectSupplierEmailEditSql01);
            
            // Bind the $edit_supplier_email variable to the prepared statement
            $selectSupplierEmailEditStmt01->bind_param('s', $edit_supplier_email);

            // Execute the prepared statement
            $selectSupplierEmailEditStmt01->execute();

            // Store the result of the executed statement
            $selectSupplierEmailEditStmt01->store_result();

            // Check if there is no row in the result
            if ($selectSupplierEmailEditStmt00->num_rows == 0 && $selectSupplierEmailEditStmt01->num_rows == 0) {
                // Prepare an SQL statement to update the supplier email address in the 'suppliers' table
                $updateSupplierEmailEditSql = 'UPDATE `suppliers` SET `supplier_email_address`=? WHERE `supplier_id`=? AND `supplier_email_address`=?';

                // Prepare a statement object using the database connection and the SQL statement
                $updateSupplierEmailEditStmt = $dbConnection->prepare($updateSupplierEmailEditSql);

                // Bind the parameters to the prepared statement
                $updateSupplierEmailEditStmt->bind_param('sss', $edit_supplier_email,$edit_supplier_supplier_id,$fetchSupplierInitialDetailsAssoc['supplier_email_address']);

                // Execute the prepared statement to perform the update
                $updateSupplierEmailEditStmt->execute();
            }
        }
        
        // Escape and sanitize the supplier location input to prevent SQL injection and ensure proper encoding
        $edit_supplier_location = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['edit_supplier_location']), ENT_QUOTES, 'UTF-8')));

        // Check if $edit_supplier_location is not empty, and if it is different from the current supplier location in the database
        if (empty($edit_supplier_location)==false && $fetchSupplierInitialDetailsAssoc['supplier_location'] !== $edit_supplier_location) {
            // SQL statement to select the supplier's location from the 'suppliers' table where the supplier_location matches the $edit_supplier_location
            $selectSupplierLocationEditSql = 'SELECT `supplier_location` FROM `suppliers` WHERE `supplier_location` =?';

            // Prepare a SQL statement
            $selectSupplierLocationEditStmt = $dbConnection->prepare($selectSupplierLocationEditSql);
            
            // Bind the $edit_supplier_location variable to the prepared statement
            $selectSupplierLocationEditStmt->bind_param('s', $edit_supplier_location);

            // Execute the prepared statement
            $selectSupplierLocationEditStmt->execute();

            // Store the result of the executed statement
            $selectSupplierLocationEditStmt->store_result();

            // Check if there is no row in the result
            if ($selectSupplierLocationEditStmt->num_rows == 0) {
                // Prepare an SQL statement to update the supplier's lovation address in the 'suppliers' table
                $updateSupplierEmailEditSql = 'UPDATE `suppliers` SET `supplier_location`=? WHERE `supplier_id`=? AND `supplier_location`=?';

                // Prepare a statement object using the database connection and the SQL statement
                $updateSupplierEmailEditStmt = $dbConnection->prepare($updateSupplierEmailEditSql);

                // Bind the parameters to the prepared statement
                $updateSupplierEmailEditStmt->bind_param('sss', $edit_supplier_location,$edit_supplier_supplier_id,$fetchSupplierInitialDetailsAssoc['supplier_location']);

                // Execute the prepared statement to perform the update
                $updateSupplierEmailEditStmt->execute();
            }
        }
        
        // Escape and sanitize the supplier address input to prevent SQL injection and ensure proper encoding
        $edit_supplier_address = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['edit_supplier_address']), ENT_QUOTES, 'UTF-8')));

        // Check if the edited supplier address differs from the initial value
        if ($fetchSupplierInitialDetailsAssoc['supplier_address'] !== $edit_supplier_address) {
            // SQL statement to select the supplier's address from the 'suppliers' table where the supplier_address matches the $edit_supplier_address
            $selectSupplierAddressEditSql = 'SELECT `supplier_address` FROM `suppliers` WHERE `supplier_address` =?';

            // Prepare a SQL statement
            $selectSupplierAddressEditStmt = $dbConnection->prepare($selectSupplierAddressEditSql);
            
            // Bind the $edit_supplier_address variable to the prepared statement
            $selectSupplierAddressEditStmt->bind_param('s', $edit_supplier_address);

            // Execute the prepared statement
            $selectSupplierAddressEditStmt->execute();

            // Store the result of the executed statement
            $selectSupplierAddressEditStmt->store_result();

            // Check if there is no row in the result
            if ($selectSupplierAddressEditStmt->num_rows == 0) {
                // Prepare an SQL statement to update the supplier's lovation address in the 'suppliers' table
                $updateSupplierEmailEditSql = 'UPDATE `suppliers` SET `supplier_address`=? WHERE `supplier_id`=? AND `supplier_address`=?';

                // Prepare a statement object using the database connection and the SQL statement
                $updateSupplierEmailEditStmt = $dbConnection->prepare($updateSupplierEmailEditSql);

                // Bind the parameters to the prepared statement
                $updateSupplierEmailEditStmt->bind_param('sss', $edit_supplier_address,$edit_supplier_supplier_id,$fetchSupplierInitialDetailsAssoc['supplier_address']);

                // Execute the prepared statement to perform the update
                $updateSupplierEmailEditStmt->execute();
            }
        }

        // Redirect to the suppliers page with the edit_supplier task and the specific supplier ID
        header('Location: suppliers?tsk=edit_supplier&id='.$edit_supplier_supplier_id.'');
        exit();
    }
?>
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title><?php echo $fetchCompanyDataAssoc['company_name']; ?> | View suppliers</title>
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
                                <?php if (isset($_GET['tsk'])==true && $_GET['tsk']=='add_supplier') { ?>
                                    <div class="container mx-auto">
                                        <div class="card">
                                            <form action="" method="post" onsubmit="return addSupplierJsValidation();">
                                            <div class="card-header bg-transparent text-center font-weight-bold">
                                                Add supplier<br>
                                                <?php if (isset($_GET['addSupplierError'])==true && $_GET['addSupplierError']==='empty_supplier_name') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">Please enter the supplier&apos;s name</h6>
                                                <?php }elseif (isset($_GET['addSupplierError'])==true && $_GET['addSupplierError']==='empty_supplier_phone') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">Please enter the supplier&apos;s phone</h6>
                                                <?php }elseif (isset($_GET['addSupplierError'])==true && $_GET['addSupplierError']==='empty_supplier_email') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">Please enter the supplier&apos;s email</h6>
                                                <?php }elseif (isset($_GET['addSupplierError'])==true && $_GET['addSupplierError']==='empty_supplier_location') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">Please enter the supplier&apos;s location</h6>
                                                <?php }elseif (isset($_GET['addSupplierError'])==true && $_GET['addSupplierError']==='empty_supplier_address') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">Please enter the supplier&apos;s address</h6>
                                                <?php }elseif (isset($_GET['addSupplierError'])==true && $_GET['addSupplierError']==='invalid_supplier_phone') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">Please enter a valid phone number</h6>
                                                <?php }elseif (isset($_GET['addSupplierError'])==true && $_GET['addSupplierError']==='invalid_supplier_email') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">Please enter a valid email address</h6>
                                                <?php }elseif (isset($_GET['addSupplierError'])==true && $_GET['addSupplierError']==='supplier_name_exists') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">The supplier&apos;s name already exists</h6>
                                                <?php }elseif (isset($_GET['addSupplierError'])==true && $_GET['addSupplierError']==='supplier_phone_exists') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">The supplier&apos;s phone number already exists</h6>
                                                <?php }elseif (isset($_GET['addSupplierError'])==true && $_GET['addSupplierError']==='supplier_email_exists') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">The supplier&apos;s email already exists</h6>
                                                <?php }elseif (isset($_SESSION['addSupplierStatus'])==true && $_SESSION['addSupplierStatus']==='supplierAdded') { ?>
                                                    <script>$(document).ready(function () {$("#supplierAdded").modal('show');});</script>
                                                    <?php unset($_SESSION['addSupplierStatus']); ?>
                                                <?php }elseif (isset($_SESSION['addSupplierStatus'])==true && $_SESSION['addSupplierStatus']==='supplierNotAdded') { ?>
                                                    <script>$(document).ready(function () {$("#supplierNotAdded").modal('show');});</script>
                                                    <?php unset($_SESSION['addSupplierStatus']); ?>
                                                <?php } ?>
                                                    <div class="modal fade" id="supplierAdded" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="supplierAddedLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="supplierAddedLabel">Staff added<i class="ml-1 fa-solid text-success fa-circle-check"></i></h5>
                                                                    <span type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </span>
                                                                </div>
                                                                <div class="modal-body">
                                                                    The staff has been added successfully!
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button name="okay" id="okay" type="button" data-dismiss="modal" class="btn btn-primary">Okay</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="modal fade" id="supplierNotAdded" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="supplierNotAddedLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="supplierNotAddedLabel">Staff not added<i class="ml-1 fa-solid text-danger fa-circle-xmark"></i></h5>
                                                                    <span type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </span>
                                                                </div>
                                                                <div class="modal-body">
                                                                    The staff has not been added!<br>Please try again.
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button name="okay" id="okay" type="button" data-dismiss="modal" class="btn btn-primary">Okay</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>
                                                <div class="card-body">
                                                    <div>
                                                        <label for="add_supplier_name" class="control-label">Supplier&apos;s name<span class="text-danger ml-1">*</span></label>
                                                        <span id="add_supplier_name_status" class="d-block"></span>
                                                        <div class="input-group mb-3">
                                                            <input type="text" name="add_supplier_name" id="add_supplier_name" placeholder="Supplier&apos;s name" class="form-control" autocomplete="new-password" autofocus>
                                                            
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label for="add_supplier_phone" class="control-label">Supplier&apos;s phone number<span class="text-danger ml-1">*</span></label>
                                                        <span id="add_supplier_phone_status" class="d-block"></span>
                                                        <div class="input-group mb-3">
                                                            <input type="tel" name="add_supplier_phone" id="add_supplier_phone" placeholder="Supplier&apos;s phone number" class="form-control" autocomplete="new-password">
                                                            <div class="input-group-append">
                                                                <div class="input-group-text">
                                                                    <span class="fas fa-phone"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label for="add_supplier_email" class="control-label">Supplier&apos;s email<span class="text-danger ml-1">*</span></label>
                                                        <span id="add_supplier_email_status" class="d-block"></span>
                                                        <div class="input-group mb-3">
                                                            <input type="text" name="add_supplier_email" id="add_supplier_email" placeholder="Supplier&apos;s email" class="form-control" autocomplete="new-password">
                                                            <div class="input-group-append">
                                                                <div class="input-group-text">
                                                                    <span class="fas fa-user"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label for="add_supplier_location" class="control-label">Supplier&apos;s location<span class="text-danger ml-1">*</span></label>
                                                        <span id="add_supplier_location_status" class="d-block"></span>
                                                        <div class="input-group mb-3">
                                                            <input type="text" name="add_supplier_location" id="add_supplier_location" placeholder="Supplier&apos;s location" class="form-control" autocomplete="new-password">
                                                            <div class="input-group-append">
                                                                <div class="input-group-text">
                                                                    <span class="fa-solid fa-location-dot"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label for="add_supplier_address" class="control-label">Supplier&apos;s address<span class="text-danger ml-1">*</span></label>
                                                        <span id="add_supplier_address_status" class="d-block"></span>
                                                        <input name="add_supplier_address" id="add_supplier_address" class="form-control" placeholder="Supplier&apos;s address">
                                                    </div>

                                                    <div class="row mt-2">
                                                        <div class="col">
                                                            <button type="submit" name="add_supplier_btn" id="add_supplier_btn" class="btn btn-primary btn-block">Add supplier</button>
                                                            <button type="button" name="add_supplier_loader" id="add_supplier_loader" class="d-none btn btn-primary btn-block">
                                                                <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer text-center font-weight-bold">
                                                    <h6 class="d-block text-muted mt-2">&copy; <?php echo date('Y'); ?> <?php echo $fetchCompanyDataAssoc['company_name'] ?></h6>
                                                </div>
                                            </form>
                                            <script>
                                                // Function to validate sign-up form using JavaScript
                                                function addSupplierJsValidation() {
                                                    // Initialize a variable to check if input is valid
                                                    var is_input_valid = true;

                                                    // Hide the sign-up button and show the loading spinner
                                                    document.getElementById('add_supplier_btn').className = "d-none";
                                                    document.getElementById('add_supplier_loader').className = "d-block btn btn-primary btn-block";

                                                    // Check if the last name input is empty
                                                    if (document.getElementById('add_supplier_phone').value === "") {
                                                        // Set the input validity to false
                                                        is_input_valid = false;

                                                        // Add a red border to the last name input and display an error message
                                                        document.getElementById('add_supplier_phone').style = "border: 1px solid #dc3545;";
                                                        document.getElementById('add_supplier_phone_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                        document.getElementById('add_supplier_phone_status').innerHTML = "Required";

                                                        // Hide the loading spinner and show the sign-up button
                                                        document.getElementById('add_supplier_loader').className = "d-none";
                                                        document.getElementById('add_supplier_btn').className = "d-block btn btn-primary btn-block";
                                                    } else {
                                                        const phoneNumberRegex = /^\d{10}$/;

                                                        if (!phoneNumberRegex.test(document.getElementById('add_supplier_phone').value)) {
                                                            // Set the input validity to false
                                                            is_input_valid = false;

                                                            // Add a red border to the phone input and display an error message
                                                            document.getElementById('add_supplier_phone').style = "border: 1px solid #dc3545;";
                                                            document.getElementById('add_supplier_phone_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                            document.getElementById('add_supplier_phone_status').innerHTML = "Enter a valid phone number";

                                                            // Hide the loading spinner and show the sign-up button
                                                            document.getElementById('add_supplier_loader').className = "d-none";
                                                            document.getElementById('add_supplier_btn').className = "d-block btn btn-primary btn-block";
                                                        } else {
                                                            // Add a green border to the phone input
                                                            document.getElementById('add_supplier_phone').style.border = "1px solid #28a745";
                                                            document.getElementById('add_supplier_phone_status').innerHTML = "";
                                                        }
                                                    }

                                                    // Check if the username input is empty
                                                    if (document.getElementById('add_supplier_name').value === "") {
                                                        // Set the input validity to false
                                                        is_input_valid = false;

                                                        // Add a red border to the username input and display an error message
                                                        document.getElementById('add_supplier_name').style = "border: 1px solid #dc3545;";
                                                        document.getElementById('add_supplier_name_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                        document.getElementById('add_supplier_name_status').innerHTML = "Required";

                                                        // Hide the loading spinner and show the sign-up button
                                                        document.getElementById('add_supplier_loader').className = "d-none";
                                                        document.getElementById('add_supplier_btn').className = "d-block btn btn-primary btn-block";
                                                    } else {
                                                        // Add a green border to the username input
                                                        document.getElementById('add_supplier_name').style.border = "1px solid #28a745";
                                                        document.getElementById('add_supplier_name_status').innerHTML = "";
                                                    }

                                                    // Check if the email input is empty
                                                    if (document.getElementById('add_supplier_email').value === "") {
                                                        // Set the input validity to false
                                                        is_input_valid = false;

                                                        // Add a red border to the email input and display an error message
                                                        document.getElementById('add_supplier_email').style = "border: 1px solid #dc3545;";
                                                        document.getElementById('add_supplier_email_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                        document.getElementById('add_supplier_email_status').innerHTML = "Required";

                                                        // Hide the loading spinner and show the sign-up button
                                                        document.getElementById('add_supplier_loader').className = "d-none";
                                                        document.getElementById('add_supplier_btn').className = "d-block btn btn-primary btn-block";
                                                    } else {
                                                        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                                                        // ^ asserts the start of the string.
                                                        // [a-zA-Z0-9._%+-]+ matches one or more alphanumeric characters, dots (.), underscores (_), percentage signs (%), plus signs (+), or hyphens (-).
                                                        // @ matches the @ symbol.
                                                        // [a-zA-Z0-9.-]+ matches one or more alphanumeric characters, dots (.), or hyphens (-).
                                                        // \. matches the dot (.) character.
                                                        // [a-zA-Z]{2,} matches two or more alphabetic characters.
                                                        // $ asserts the end of the string.

                                                        if (!emailRegex.test(document.getElementById('add_supplier_email').value)) {
                                                            // Set the input validity to false
                                                            is_input_valid = false;
                                                            
                                                            // Add a red border to the email input and display an error message
                                                            document.getElementById('add_supplier_email').style = "border: 1px solid #dc3545;";
                                                            document.getElementById('add_supplier_email_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                            document.getElementById('add_supplier_email_status').innerHTML = "Enter a valid email";

                                                            // Hide the loading spinner and show the sign-up button
                                                            document.getElementById('add_supplier_loader').className = "d-none";
                                                            document.getElementById('add_supplier_btn').className = "d-block btn btn-primary btn-block";
                                                        } else {
                                                            // Add a green border to the email input
                                                            document.getElementById('add_supplier_email').style.border = "1px solid #28a745";
                                                            document.getElementById('add_supplier_email_status').innerHTML = "";
                                                        }
                                                    }

                                                    // Check if the password input is empty
                                                    if (document.getElementById('add_supplier_location').value === "") {
                                                        // Set the input validity to false
                                                        is_input_valid = false;

                                                        // Add a red border to the password input and display an error message
                                                        document.getElementById('add_supplier_location').style = "border: 1px solid #dc3545;";
                                                        document.getElementById('add_supplier_location_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                        document.getElementById('add_supplier_location_status').innerHTML = "Required";

                                                        // Hide the loading spinner and show the sign-up button
                                                        document.getElementById('add_supplier_loader').className = "d-none";
                                                        document.getElementById('add_supplier_btn').className = "d-block btn btn-primary btn-block";
                                                    } else {
                                                        // Add a green border to the password input
                                                        document.getElementById('add_supplier_location').style.border = "1px solid #28a745";
                                                        document.getElementById('add_supplier_location_status').innerHTML = "";
                                                    }

                                                    // Check if the confirm password input is empty
                                                    if (document.getElementById('add_supplier_address').value === "") {
                                                        // Set the input validity to false
                                                        is_input_valid = false;

                                                        // Add a red border to the confirm password input and display an error message
                                                        document.getElementById('add_supplier_address').style = "border: 1px solid #dc3545;";
                                                        document.getElementById('add_supplier_address_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                        document.getElementById('add_supplier_address_status').innerHTML = "Required";

                                                        // Hide the loading spinner and show the sign-up button
                                                        document.getElementById('add_supplier_loader').className = "d-none";
                                                        document.getElementById('add_supplier_btn').className = "d-block btn btn-primary btn-block";
                                                    } else {
                                                        
                                                        // Add a green border to the confirm password input
                                                        document.getElementById('add_supplier_address').style.border = "1px solid #28a745";
                                                        document.getElementById('add_supplier_address_status').innerHTML = "";
                                                    }

                                                    // Return the validity of the input
                                                    return is_input_valid;
                                                }
                                            </script>
                                        </div>
                                    </div>
                                <?php }elseif (isset($_GET['tsk'])==true && $_GET['tsk']=='view_suppliers') { ?>
                                    <div class="card card-outline card-dark" style="border-bottom: 3px solid #343a40;">
                                        <div class="card-header">
                                            <h6 class="text-dark font-weight-bold">View suppliers</h6>
                                        </div>
                                        <div class="card-body">
                                            <table class="text-nowrap table table-bordered table-hover table-striped table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl" id="suppliers_list_table">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th class="text-center">#</th>
                                                        <th>Supplier&apos;s name</th>
                                                        <th>Phone</th>
                                                        <th>Email</th>
                                                        <th class="col-1">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        // initial number
                                                        $listNumber = 1;
                                                        // Prepare a SQL query to fetch user data from the database
                                                        $fetchSuppliersSql = 'SELECT `supplier_id`,`supplier_name`,`supplier_phone_number`,`supplier_email_address` FROM `suppliers` ORDER BY `date_created` ASC';

                                                        // Prepare the SQL statement
                                                        $fetchSuppliersStmt = $dbConnection->prepare($fetchSuppliersSql);

                                                        // Execute the prepared statement
                                                        $fetchSuppliersStmt->execute();

                                                        // Retrieve the result set
                                                        $fetchSuppliersResult = $fetchSuppliersStmt->get_result();
                                                        
                                                        // Close the prepared statement to free up resources
                                                        $fetchSuppliersStmt->close();

                                                        // Fetch data as an associative array
                                                        while ($fetchSuppliersAssoc= $fetchSuppliersResult->fetch_assoc()) {
                                                    ?>
                                                        <tr>
                                                            <th class="text-center">
                                                                <?php
                                                                    $count=$listNumber++;
                                                                    if ($count<10){echo '0'.$count;}
                                                                    else{echo $count;}
                                                                ?>
                                                            </th>
                                                            <td><?php echo $fetchSuppliersAssoc['supplier_name']; ?></td>
                                                            <td>
                                                                <?php if (empty($fetchSuppliersAssoc['supplier_phone_number'])==true) { ?>
                                                                    Not set
                                                                <?php }else{ ?>
                                                                    <a class="text-dark" href="tel:+<?php echo $fetchSuppliersAssoc['supplier_phone_number']; ?>"><?php echo $fetchSuppliersAssoc['supplier_phone_number']; ?></a>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <?php if (empty($fetchSuppliersAssoc['email_address'])==true) { ?>
                                                                    Not set
                                                                <?php }else{ ?>
                                                                    <a class="text-dark" href="mailto:+<?php echo $fetchSuppliersAssoc['email_address']; ?>"><?php echo $fetchSuppliersAssoc['email_address']; ?></a>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <div class="btn-group" role="group">
                                                                    <span class="btn btn-md btn-primary" onclick="window.open('supplierDetalis?id=<?php echo $fetchSuppliersAssoc['supplier_id']; ?>','popup','width=900,height=600'); return false;">View</span>
                                                                    <span class="btn btn-md btn-secondary ml-1"><a class="text-light text-decoration-none" href="suppliers?tsk=edit_supplier&id=<?php echo $fetchSuppliersAssoc['supplier_id']; ?>">Edit</a></span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <?php }elseif (isset($_GET['tsk'])==true && isset($_GET['id'])==true && empty($_GET['id'])==false && $_GET['tsk']=='edit_supplier') { ?>
                                    <?php
                                        // Prepare a SQL statement to select the email address from the 'suppliers' table where the email address matches the provided supplier_id
                                        $selectSupplierIdSql = 'SELECT `supplier_id` FROM `suppliers` WHERE `supplier_id` =?';
                                        // Prepare the SQL statement
                                        $selectSupplierIdStmt = $dbConnection->prepare($selectSupplierIdSql);
                                        // Bind the supplier_id variable to the prepared statement as a string
                                        $selectSupplierIdStmt->bind_param('s',$_GET['id']);
                                        // Execute the prepared statement
                                        $selectSupplierIdStmt->execute();
                                        // Store the result of the executed statement
                                        $selectSupplierIdStmt->store_result();
                                        // Check if the number of rows returned by the executed statement is not 1, indicating that the supplier_id does not exists in the 'suppliers' table
                                        if ($selectSupplierIdStmt->num_rows!==1) {$invalidSupplier="Yes";
                                    ?>
                                    <script>$(document).ready(function () {$("#invalidSupplier").modal('show');});</script>
                                    <div class="modal fade" id="invalidSupplier" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="invalidSupplierLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="invalidSupplierLabel">Invalid supplier<i class="ml-1 fa-solid text-danger fa-circle-xmark"></i></h5>
                                                </div>
                                                <div class="modal-body">
                                                    The supplier does not exist.<br>
                                                    Please select from the suppliers table.
                                                </div>
                                                <div class="modal-footer">
                                                    <a class="text-light btn btn-sm btn-primary text-decoration-none" href="suppliers?tsk=view_suppliers">Proceed to view suppliers</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php }else{$invalidSupplier='No';} ?>
                                    <?php if ($invalidSupplier=='Yes') { ?>
                                    <?php }elseif ($invalidSupplier=='No') { ?>
                                        <div class="container mx-auto">
                                            <?php
                                                // Prepare a SQL statement to select supplier details from the 'suppliers' table where the 'supplier_id' matches the provided ID.
                                                $selectSupplierDetailsSql = 'SELECT `supplier_id`,`supplier_status`,`supplier_name`,`supplier_phone_number`,`supplier_email_address`,`supplier_location`,`supplier_address` FROM `suppliers` WHERE `supplier_id` =?';

                                                // Prepare the SQL statement for execution
                                                $selectSupplierDetailsStmt = $dbConnection->prepare($selectSupplierDetailsSql);

                                                // Bind the provided ID to the prepared statement
                                                $selectSupplierDetailsStmt->bind_param('s',$_GET['id']);

                                                // Execute the prepared statement
                                                $selectSupplierDetailsStmt->execute();

                                                // Get the result of the executed statement
                                                $selectSupplierDetailsResult = $selectSupplierDetailsStmt->get_result();

                                                // Fetch the result as an associative array
                                                $selectSupplierDetailsAssoc= $selectSupplierDetailsResult->fetch_assoc();
                                            ?>
                                            <div class="card">
                                                <form action="" method="post" onsubmit="return editSupplierJsValidation()">
                                                <div class="card-header bg-transparent text-center font-weight-bold">
                                                    Edit supplier <input type="hidden" class="form-control form-control-sm" readonly name="edit_supplier_supplier_id" id="edit_supplier_supplier_id" value="<?php echo $selectSupplierDetailsAssoc['supplier_id']; ?>" placeholder="edit_supplier_supplier_id">
                                                    <br>
                                                    <?php if (isset($_GET['error']) && $_GET['error']=='supplierIdNotMatch') { ?>
                                                        <span class="text-danger">There was an error editing the supplier<br>Please try again.</span>
                                                    <?php }elseif (isset($_GET['editedSupplierError'])==true && $_GET['editedSupplierError']==='empty_supplier_name') { ?>
                                                        <h6 class="mt-1" style="color: #dc3545 !important;">Please enter the supplier&apos;s name</h6>
                                                    <?php }elseif (isset($_GET['editedSupplierError'])==true && $_GET['editedSupplierError']==='empty_supplier_phone') { ?>
                                                        <h6 class="mt-1" style="color: #dc3545 !important;">Please enter the supplier&apos;s phone</h6>
                                                    <?php }elseif (isset($_GET['editedSupplierError'])==true && $_GET['editedSupplierError']==='empty_supplier_email') { ?>
                                                        <h6 class="mt-1" style="color: #dc3545 !important;">Please enter the supplier&apos;s email</h6>
                                                    <?php }elseif (isset($_GET['editedSupplierError'])==true && $_GET['editedSupplierError']==='empty_supplier_location') { ?>
                                                        <h6 class="mt-1" style="color: #dc3545 !important;">Please enter the supplier&apos;s location</h6>
                                                    <?php }elseif (isset($_GET['editedSupplierError'])==true && $_GET['editedSupplierError']==='empty_supplier_address') { ?>
                                                        <h6 class="mt-1" style="color: #dc3545 !important;">Please enter the supplier&apos;s address</h6>
                                                    <?php }elseif (isset($_GET['editedSupplierError'])==true && $_GET['editedSupplierError']==='invalid_supplier_phone') { ?>
                                                        <h6 class="mt-1" style="color: #dc3545 !important;">Please enter a valid phone number</h6>
                                                    <?php }elseif (isset($_GET['editedSupplierError'])==true && $_GET['editedSupplierError']==='invalid_supplier_email') { ?>
                                                        <h6 class="mt-1" style="color: #dc3545 !important;">Please enter a valid email address</h6>
                                                    <?php }elseif (isset($_GET['editedSupplierError'])==true && $_GET['editedSupplierError']==='supplier_name_exists') { ?>
                                                        <h6 class="mt-1" style="color: #dc3545 !important;">The supplier&apos;s name already exists</h6>
                                                    <?php }elseif (isset($_GET['editedSupplierError'])==true && $_GET['editedSupplierError']==='supplier_phone_exists') { ?>
                                                        <h6 class="mt-1" style="color: #dc3545 !important;">The supplier&apos;s phone number already exists</h6>
                                                    <?php }elseif (isset($_GET['editedSupplierError'])==true && $_GET['editedSupplierError']==='supplier_email_exists') { ?>
                                                        <h6 class="mt-1" style="color: #dc3545 !important;">The supplier&apos;s email already exists</h6>
                                                    <?php }elseif (isset($_SESSION['addSupplierStatus'])==true && $_SESSION['addSupplierStatus']==='supplierEdited') { ?>
                                                        <script>$(document).ready(function () {$("#supplierEdited").modal('show');});</script>
                                                        <?php unset($_SESSION['addSupplierStatus']); ?>
                                                    <?php }elseif (isset($_SESSION['addSupplierStatus'])==true && $_SESSION['addSupplierStatus']==='supplierNotEdited') { ?>
                                                        <script>$(document).ready(function () {$("#supplierNotEdited").modal('show');});</script>
                                                        <?php unset($_SESSION['addSupplierStatus']); ?>
                                                    <?php } ?>
                                                        <div class="modal fade" id="supplierEdited" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="supplierEditedLabel" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="supplierEditedLabel">Staff edited<i class="ml-1 fa-solid text-success fa-circle-check"></i></h5>
                                                                        <span type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </span>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        The staff has been edited successfully!
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button name="okay" id="okay" type="button" data-dismiss="modal" class="btn btn-primary">Okay</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="modal fade" id="supplierNotEdited" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="supplierNotEditedLabel" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="supplierNotEditedLabel">Staff not edited<i class="ml-1 fa-solid text-danger fa-circle-xmark"></i></h5>
                                                                        <span type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </span>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        The staff has not been edited!<br>Please try again.
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button name="okay" id="okay" type="button" data-dismiss="modal" class="btn btn-primary">Okay</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                </div>
                                                    <div class="card-body">
                                                        <div>
                                                            <label for="edit_supplier_name" class="control-label">Supplier&apos;s name<span class="text-danger ml-1">*</span></label>
                                                            <span id="edit_supplier_name_status" class="d-block"></span>
                                                            <div class="input-group mb-3">
                                                                <input type="text" name="edit_supplier_name" id="edit_supplier_name" value="<?php echo $selectSupplierDetailsAssoc['supplier_name']; ?>" placeholder="Supplier&apos;s name" class="form-control" autocomplete="new-password">
                                                                
                                                            </div>
                                                        </div>

                                                        <div>
                                                            <label for="edit_supplier_phone" class="control-label">Supplier&apos;s phone number<span class="text-danger ml-1">*</span></label>
                                                            <span id="edit_supplier_phone_status" class="d-block"></span>
                                                            <div class="input-group mb-3">
                                                                <input type="tel" name="edit_supplier_phone" id="edit_supplier_phone" value="<?php echo $selectSupplierDetailsAssoc['supplier_phone_number']; ?>" placeholder="Supplier&apos;s phone number" class="form-control" autocomplete="new-password">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <span class="fas fa-phone"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div>
                                                            <label for="edit_supplier_email" class="control-label">Supplier&apos;s email<span class="text-danger ml-1">*</span></label>
                                                            <span id="edit_supplier_email_status" class="d-block"></span>
                                                            <div class="input-group mb-3">
                                                                <input type="text" name="edit_supplier_email" id="edit_supplier_email" value="<?php echo $selectSupplierDetailsAssoc['supplier_email_address']; ?>" placeholder="Supplier&apos;s email" class="form-control" autocomplete="new-password">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <span class="fas fa-user"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div>
                                                            <label for="edit_supplier_location" class="control-label">Supplier&apos;s location<span class="text-danger ml-1">*</span></label>
                                                            <span id="edit_supplier_location_status" class="d-block"></span>
                                                            <div class="input-group mb-3">
                                                                <input type="text" name="edit_supplier_location" id="edit_supplier_location" value="<?php echo $selectSupplierDetailsAssoc['supplier_location']; ?>" placeholder="Supplier&apos;s location" class="form-control" autocomplete="new-password">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <span class="fa-solid fa-location-dot"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div>
                                                            <label for="edit_supplier_address" class="control-label">Supplier&apos;s address<span class="text-danger ml-1">*</span></label>
                                                            <span id="edit_supplier_address_status" class="d-block"></span>
                                                            <input name="edit_supplier_address" id="edit_supplier_address" class="form-control" value="<?php echo $selectSupplierDetailsAssoc['supplier_address']; ?>" placeholder="Supplier&apos;s address">
                                                        </div>

                                                        <div class="row mt-2">
                                                            <div class="col">
                                                                <button type="submit" name="edit_supplier_btn" id="edit_supplier_btn" class="btn btn-primary btn-block">Edit supplier</button>
                                                                <button type="button" name="edit_supplier_loader" id="edit_supplier_loader" class="d-none btn btn-primary btn-block">
                                                                    <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-footer text-center font-weight-bold">
                                                        <h6 class="d-block text-muted mt-2">&copy; <?php echo date('Y'); ?> <?php echo $fetchCompanyDataAssoc['company_name'] ?></h6>
                                                    </div>
                                                </form>
                                                <script>
                                                    // Function to validate sign-up form using JavaScript
                                                    function editSupplierJsValidation() {
                                                        // Initialize a variable to check if input is valid
                                                        var is_input_valid = true;

                                                        // Hide the sign-up button and show the loading spinner
                                                        document.getElementById('edit_supplier_btn').className = "d-none";
                                                        document.getElementById('edit_supplier_loader').className = "d-block btn btn-primary btn-block";

                                                        // Check if the last name input is empty
                                                        if (document.getElementById('edit_supplier_phone').value === "") {
                                                            // Set the input validity to false
                                                            is_input_valid = false;

                                                            // Add a red border to the last name input and display an error message
                                                            document.getElementById('edit_supplier_phone').style = "border: 1px solid #dc3545;";
                                                            document.getElementById('edit_supplier_phone_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                            document.getElementById('edit_supplier_phone_status').innerHTML = "Required";

                                                            // Hide the loading spinner and show the sign-up button
                                                            document.getElementById('edit_supplier_loader').className = "d-none";
                                                            document.getElementById('edit_supplier_btn').className = "d-block btn btn-primary btn-block";
                                                        } else {
                                                            const phoneNumberRegex = /^\d{10}$/;

                                                            if (!phoneNumberRegex.test(document.getElementById('edit_supplier_phone').value)) {
                                                                // Set the input validity to false
                                                                is_input_valid = false;

                                                                // Add a red border to the phone input and display an error message
                                                                document.getElementById('edit_supplier_phone').style = "border: 1px solid #dc3545;";
                                                                document.getElementById('edit_supplier_phone_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                                document.getElementById('edit_supplier_phone_status').innerHTML = "Enter a valid phone number";

                                                                // Hide the loading spinner and show the sign-up button
                                                                document.getElementById('edit_supplier_loader').className = "d-none";
                                                                document.getElementById('edit_supplier_btn').className = "d-block btn btn-primary btn-block";
                                                            } else {
                                                                // Add a green border to the phone input
                                                                document.getElementById('edit_supplier_phone').style.border = "1px solid #28a745";
                                                                document.getElementById('edit_supplier_phone_status').innerHTML = "";
                                                            }
                                                        }

                                                        // Check if the username input is empty
                                                        if (document.getElementById('edit_supplier_name').value === "") {
                                                            // Set the input validity to false
                                                            is_input_valid = false;

                                                            // Add a red border to the username input and display an error message
                                                            document.getElementById('edit_supplier_name').style = "border: 1px solid #dc3545;";
                                                            document.getElementById('edit_supplier_name_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                            document.getElementById('edit_supplier_name_status').innerHTML = "Required";

                                                            // Hide the loading spinner and show the sign-up button
                                                            document.getElementById('edit_supplier_loader').className = "d-none";
                                                            document.getElementById('edit_supplier_btn').className = "d-block btn btn-primary btn-block";
                                                        } else {
                                                            // Add a green border to the username input
                                                            document.getElementById('edit_supplier_name').style.border = "1px solid #28a745";
                                                            document.getElementById('edit_supplier_name_status').innerHTML = "";
                                                        }

                                                        // Check if the email input is empty
                                                        if (document.getElementById('edit_supplier_email').value === "") {
                                                            // Set the input validity to false
                                                            is_input_valid = false;

                                                            // Add a red border to the email input and display an error message
                                                            document.getElementById('edit_supplier_email').style = "border: 1px solid #dc3545;";
                                                            document.getElementById('edit_supplier_email_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                            document.getElementById('edit_supplier_email_status').innerHTML = "Required";

                                                            // Hide the loading spinner and show the sign-up button
                                                            document.getElementById('edit_supplier_loader').className = "d-none";
                                                            document.getElementById('edit_supplier_btn').className = "d-block btn btn-primary btn-block";
                                                        } else {
                                                            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                                                            // ^ asserts the start of the string.
                                                            // [a-zA-Z0-9._%+-]+ matches one or more alphanumeric characters, dots (.), underscores (_), percentage signs (%), plus signs (+), or hyphens (-).
                                                            // @ matches the @ symbol.
                                                            // [a-zA-Z0-9.-]+ matches one or more alphanumeric characters, dots (.), or hyphens (-).
                                                            // \. matches the dot (.) character.
                                                            // [a-zA-Z]{2,} matches two or more alphabetic characters.
                                                            // $ asserts the end of the string.

                                                            if (!emailRegex.test(document.getElementById('edit_supplier_email').value)) {
                                                                // Set the input validity to false
                                                                is_input_valid = false;

                                                                // Add a red border to the email input and display an error message
                                                                document.getElementById('edit_supplier_email').style = "border: 1px solid #dc3545;";
                                                                document.getElementById('edit_supplier_email_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                                document.getElementById('edit_supplier_email_status').innerHTML = "Enter a valid email";

                                                                // Hide the loading spinner and show the sign-up button
                                                                document.getElementById('edit_supplier_loader').className = "d-none";
                                                                document.getElementById('edit_supplier_btn').className = "d-block btn btn-primary btn-block";
                                                            } else {
                                                                // Add a green border to the email input
                                                                document.getElementById('edit_supplier_email').style.border = "1px solid #28a745";
                                                                document.getElementById('edit_supplier_email_status').innerHTML = "";
                                                            }
                                                        }

                                                        // Check if the password input is empty
                                                        if (document.getElementById('edit_supplier_location').value === "") {
                                                            // Set the input validity to false
                                                            is_input_valid = false;

                                                            // Add a red border to the password input and display an error message
                                                            document.getElementById('edit_supplier_location').style = "border: 1px solid #dc3545;";
                                                            document.getElementById('edit_supplier_location_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                            document.getElementById('edit_supplier_location_status').innerHTML = "Required";

                                                            // Hide the loading spinner and show the sign-up button
                                                            document.getElementById('edit_supplier_loader').className = "d-none";
                                                            document.getElementById('edit_supplier_btn').className = "d-block btn btn-primary btn-block";
                                                        } else {
                                                            // Add a green border to the password input
                                                            document.getElementById('edit_supplier_location').style.border = "1px solid #28a745";
                                                            document.getElementById('edit_supplier_location_status').innerHTML = "";
                                                        }

                                                        // Check if the confirm password input is empty
                                                        if (document.getElementById('edit_supplier_address').value === "") {
                                                            // Set the input validity to false
                                                            is_input_valid = false;

                                                            // Add a red border to the confirm password input and display an error message
                                                            document.getElementById('edit_supplier_address').style = "border: 1px solid #dc3545;";
                                                            document.getElementById('edit_supplier_address_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                            document.getElementById('edit_supplier_address_status').innerHTML = "Required";

                                                            // Hide the loading spinner and show the sign-up button
                                                            document.getElementById('edit_supplier_loader').className = "d-none";
                                                            document.getElementById('edit_supplier_btn').className = "d-block btn btn-primary btn-block";
                                                        } else {
                                                            
                                                            // Add a green border to the confirm password input
                                                            document.getElementById('edit_supplier_address').style.border = "1px solid #28a745";
                                                            document.getElementById('edit_supplier_address_status').innerHTML = "";
                                                        }

                                                        // Return the validity of the input
                                                        return is_input_valid;
                                                    }
                                                </script>
                                            </div>
                                        </div>
                                    <?php } ?>
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
                    $(document).ready( function (){$('#suppliers_list_table').DataTable();});
                </script>
            </body>
        </html>
<?php $dbConnection->close(); // Close the database connection ?>