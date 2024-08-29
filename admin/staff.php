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
        // If 'tsk' is not set or is empty, redirect the user to the 'staff' page with a 'view_staff' query parameter
        header('Location: staff?tsk=view_staff');
        // Stop the execution of the script to prevent any further actions
        exit();
    }

    // Check if the task 'tsk' is set to 'edit_staff' and if the 'id' parameter is not provided
    if ($_GET['tsk'] == 'edit_staff' && empty($_GET['id']) == true) {
        // Redirect the user to the 'taff' page with the 'edit_staff' task
        header('Location: staff?tsk=view_staff');
        // Stop the execution of the script
        exit();
    }

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'add_staff_btn' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['add_staff_btn']) == true) {
        // Sanitize and validate user input data to prevent SQL injection and cross-site scripting (XSS) attacks

        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the first name input
        $add_staff_first_name = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['add_staff_first_name']), ENT_QUOTES, 'UTF-8')));

        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the last name input
        $add_staff_last_name = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['add_staff_last_name']), ENT_QUOTES, 'UTF-8')));

        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the username input
        $add_staff_username = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['add_staff_username']), ENT_QUOTES, 'UTF-8')));

        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the phone number input
        $add_staff_phone = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['add_staff_phone']), ENT_QUOTES, 'UTF-8')));

        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the email input
        $add_staff_email = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['add_staff_email']), ENT_QUOTES, 'UTF-8')));

        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the password input
        $add_staff_password = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['add_staff_password']), ENT_QUOTES, 'UTF-8')));

        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the confirm password input
        $add_staff_confirm_password = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['add_staff_confirm_password']), ENT_QUOTES, 'UTF-8')));
        

        // Check if the first name is empty
        if (empty($add_staff_first_name) == true) {
            // Redirect to the staff page with an error message for empty first name
            header('Location: staff?tsk=add_staff&addStaffError=empty_first_name');
            exit();
        }

        // Check if the last name is empty
        if (empty($add_staff_last_name) == true) {
            // Redirect to the staff page with an error message for empty last name
            header('Location: staff?tsk=add_staff&addStaffError=empty_last_name');
            exit();
        }

        // Check if the username is empty
        if (empty($add_staff_username) == true) {
            // Redirect to the staff page with an error message for empty username
            header('Location: staff?tsk=add_staff&addStaffError=empty_username');
            exit();
        }

        // Check if the phone number is empty
        if (empty($add_staff_phone) == true) {
            // Redirect to the staff page with an error message for empty phone number
            header('Location: staff?tsk=add_staff&addStaffError=empty_phone');
            exit();
        }

        // Check if the email is empty
        if (empty($add_staff_email) == true) {
            // Redirect to the staff page with an error message for empty email
            header('Location: staff?tsk=add_staff&addStaffError=empty_email');
            exit();
        }

        // Check if the password is empty
        if (empty($add_staff_password) == true) {
            // Redirect to the staff page with an error message for empty password
            header('Location: staff?tsk=add_staff&addStaffError=empty_password');
            exit();
        }

        // Check if the confirm password is empty
        if (empty($add_staff_confirm_password) == true) {
            // Redirect to the staff page with an error message for empty confirm password
            header('Location: staff?tsk=add_staff&addStaffError=empty_confirm_password');
            exit();
        }

        // Check if the phone number is less than 10 digits
        if (strlen($add_staff_phone) < 10) {
            // Redirect to the staff page with an error message for invalid phone number
            header('Location: staff?tsk=add_staff&addStaffError=invalid_phone');
            exit();
        }

        // Check if the email is valid
        if (!filter_var($add_staff_email, FILTER_VALIDATE_EMAIL)) {
            // Redirect to the staff page with an error message for invalid email
            header('Location: staff?tsk=add_staff&addStaffError=invalid_email');
            exit();
        }

        // Check if the password and confirm password match
        if ($add_staff_password!== $add_staff_confirm_password) {
            // Redirect to the staff page with an error message for password mismatch
            header('Location: staff?tsk=add_staff&addStaffError=pwd_match');
            exit();
        }

        // Prepare a SQL statement to select the username from the 'users' table where the username matches the provided add_staff_username
        $selectUsernameSql = 'SELECT `username` FROM `users` WHERE `username` =?';
        
        // Prepare the SQL statement
        $selectUsernameStmt = $dbConnection->prepare($selectUsernameSql);

        // Bind the add_staff_username variable to the prepared statement as a string
        $selectUsernameStmt->bind_param('s', $add_staff_username);

        // Execute the prepared statement
        $selectUsernameStmt->execute();

        // Store the result of the executed statement
        $selectUsernameStmt->store_result();

        // Check if the number of rows returned by the executed statement is 1, indicating that the username already exists in the 'users' table
        if ($selectUsernameStmt->num_rows===1) {
            // Redirect the user to the staff page with an error message indicating that the username already exists
            header('Location: staff?tsk=add_staff&addStaffError=username_exists');
            // Exit the current script to prevent further execution
            exit();
        }

        // Prepare a SQL statement to select the user_phone_number from the 'users' table where the user_phone_number matches the provided add_staff_phone
        $selectPhoneSql = 'SELECT `user_phone_number` FROM `users` WHERE `user_phone_number` =?';
        
        // Prepare the SQL statement
        $selectPhoneStmt = $dbConnection->prepare($selectPhoneSql);

        // Bind the add_staff_phone variable to the prepared statement as a string
        $selectPhoneStmt->bind_param('s', $add_staff_phone);

        // Execute the prepared statement
        $selectPhoneStmt->execute();

        // Store the result of the executed statement
        $selectPhoneStmt->store_result();

        // Check if the number of rows returned by the executed statement is 1, indicating that the phone already exists in the 'users' table
        if ($selectPhoneStmt->num_rows===1) {
            // Redirect the user to the staff page with an error message indicating that the phone already exists
            header('Location: staff?tsk=add_staff&addStaffError=phone_exists');
            // Exit the current script to prevent further execution
            exit();
        }

        // Prepare a SQL statement to select the email address from the 'users' table where the email address matches the provided add_staff_email
        $selectEmailSql = 'SELECT `email_address` FROM `users` WHERE `email_address` =?';
        
        // Prepare the SQL statement
        $selectEmailStmt = $dbConnection->prepare($selectEmailSql);

        // Bind the add_staff_email variable to the prepared statement as a string
        $selectEmailStmt->bind_param('s', $add_staff_email);

        // Execute the prepared statement
        $selectEmailStmt->execute();

        // Store the result of the executed statement
        $selectEmailStmt->store_result();

        // Check if the number of rows returned by the executed statement is 1, indicating that the email address already exists in the 'users' table
        if ($selectEmailStmt->num_rows===1) {
            // Redirect the user to the staff page with an error message indicating that the email address already exists
            header('Location: staff?tsk=add_staff&addStaffError=email_exists');
            // Exit the current script to prevent further execution
            exit();
        }

        // Hash the add_staff_password using the default password hashing algorithm
        $hashPass = password_hash($add_staff_password, PASSWORD_DEFAULT);

        // Record the current date and time as the date the user joined
        $date_joined = $currentDateAndTime;

        // Generate a random salt for the user ID
        $randomCharactersSalt = 'GBS';

        // Generate two random characters, convert them to uppercase hexadecimal for uniqueness
        $randomCharacters01 = strtoupper(bin2hex(random_bytes(2)));
        $randomCharacters02 = strtoupper(bin2hex(random_bytes(2)));

        // Concatenate the random characters and the salt to create the user ID
        $user_id = $randomCharacters01.$randomCharactersSalt.$randomCharacters02;

        // Prepare a SQL query to fetch user_id from the database
        $fetchUserIdSql = 'SELECT `user_id` FROM `users` WHERE `user_id`=? LIMIT 1';

        // Prepare the SQL statement
        $fetchUserIdStmt = $dbConnection->prepare($fetchUserIdSql);

        // Bind parameters to the prepared statement
        $fetchUserIdStmt->bind_param('s',$user_id);

        // Execute the prepared statement
        $fetchUserIdStmt->execute();

        // Store the result set
        $fetchUserIdResult = $fetchUserIdStmt->store_result();

        do {
            // Generate two random characters, convert them to uppercase hexadecimal for uniqueness
            $randomCharacters01 = strtoupper(bin2hex(random_bytes(2)));
            $randomCharacters02 = strtoupper(bin2hex(random_bytes(2)));
        
            // Concatenate the random characters with the predefined salt to create a unique user ID
            $user_id = $randomCharacters01.$randomCharactersSalt.$randomCharacters02;
        } while ($fetchUserIdResult->num_rows > 0); // Loop until a unique user ID is generated, ensuring no duplicates
        
        // Define the user type as '8'
        $user_type = '8';

        // Define the value of allowed system access
        $system_access = '5';

        // Set the last login to 'n_usr' indicating no user has logged in yet
        $last_login = 'n_usr';

        // Set the last_login_type to 'n_usr' indicating no user has logged in yet
        $last_login_type = 'n_usr';

        // Set the last seen to 'n_usr' indicating no user has been seen yet
        $last_seen = 'n_usr';

        // Define the SQL query to add a new user to the 'users' table
        $addNewUserSql = 'INSERT INTO `users`(`user_id`,`user_type`,`user_first_name`,`user_last_name`,`user_phone_number`,`email_address`,`username`,`user_password`,`date_joined`,`system_access`,`last_login`,`last_login_type`,`last_seen`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)';

        // Prepare the SQL query statement
        $addNewUserStmt = $dbConnection->prepare($addNewUserSql);

        // Bind the parameters to the prepared statement
        $addNewUserStmt->bind_param('sssssssssssss', $user_id,$user_type,$add_staff_first_name,$add_staff_last_name,$add_staff_phone,$add_staff_email,$add_staff_username,$hashPass,$date_joined,$system_access,$last_login,$last_login_type,$last_seen);

        // Attempt to execute the statement for adding a new user
        if ($addNewUserStmt->execute()) {
            // If the user is successfully added, store the status in the session
            $_SESSION['addStaffStatus'] = 'staffAdded';
            // Redirect the user to the staff page with a success message
            header('Location: staff?tsk=add_staff&addStaff');
            // Stop further execution of the script
            exit();
        }
    }
?>
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title><?php echo $fetchCompanyDataAssoc['company_name']; ?> | View staff</title>
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
                                <?php if (isset($_GET['tsk'])==true && $_GET['tsk']=='add_staff') {?>
                                    <div class="container mx-auto">
                                        <div class="card">
                                            <form action="" method="post" onsubmit="return addStaffJsValidation();">
                                            <!-- <form action="" method="post"> -->
                                            <div class="card-header bg-transparent text-center font-weight-bold">
                                                Add staff<br>
                                                <?php if (isset($_GET['addStaffError'])==true && $_GET['addStaffError']==='empty_first_name') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">Please enter the first name</h6>
                                                <?php }elseif (isset($_GET['addStaffError'])==true && $_GET['addStaffError']==='empty_last_name') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">Please enter the last name</h6>
                                                <?php }elseif (isset($_GET['addStaffError'])==true && $_GET['addStaffError']==='empty_username') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">Please enter the username</h6>
                                                <?php }elseif (isset($_GET['addStaffError'])==true && $_GET['addStaffError']==='empty_phone') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">Please enter the phone</h6>
                                                <?php }elseif (isset($_GET['addStaffError'])==true && $_GET['addStaffError']==='empty_email') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">Please enter the email</h6>
                                                <?php }elseif (isset($_GET['addStaffError'])==true && $_GET['addStaffError']==='empty_password') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">Please enter the password</h6>
                                                <?php }elseif (isset($_GET['addStaffError'])==true && $_GET['addStaffError']==='empty_confirm_password') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">Please confirm the password</h6>
                                                <?php }elseif (isset($_GET['addStaffError'])==true && $_GET['addStaffError']==='invalid_phone') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">Please enter a valid phone number</h6>
                                                <?php }elseif (isset($_GET['addStaffError'])==true && $_GET['addStaffError']==='invalid_email') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">Please enter a valid email</h6>
                                                <?php }elseif (isset($_GET['addStaffError'])==true && $_GET['addStaffError']==='pwd_match') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">Passwords do not match</h6>
                                                <?php }elseif (isset($_GET['addStaffError'])==true && $_GET['addStaffError']==='username_exists') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">The username already exists</h6>
                                                <?php }elseif (isset($_GET['addStaffError'])==true && $_GET['addStaffError']==='phone_exists') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">The phone already exists</h6>
                                                <?php }elseif (isset($_GET['addStaffError'])==true && $_GET['addStaffError']==='email_exists') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">The email already exists</h6>
                                                <?php }elseif (isset($_SESSION['addStaffStatus'])==true && $_SESSION['addStaffStatus']==='staffAdded') { ?>
                                                    <script>$(document).ready(function () {$("#staffAdded").modal('show');});</script>
                                                    <?php unset($_SESSION['addStaffStatus']); ?>
                                                <?php } ?>
                                                    <div class="modal fade" id="staffAdded" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staffAddedLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="staffAddedLabel">Staff added<i class="ml-1 fa-solid text-success fa-circle-check"></i></h5>
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
                                            </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="mb-2 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                                            <label for="add_staff_first_name" class="control-label">First name<span class="text-danger">*</span></label>
                                                            <span id="add_staff_first_name_status" class="d-block"></span>
                                                            <input type="text" name="add_staff_first_name" id="add_staff_first_name" placeholder="First name" class="form-control" autocomplete="new-password" autofocus>
                                                        </div>
                                                        <div class="mb-2 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                                            <label for="add_staff_last_name" class="control-label">Last name<span class="text-danger">*</span></label>
                                                            <span id="add_staff_last_name_status" class="d-block"></span>
                                                            <input type="text" name="add_staff_last_name" id="add_staff_last_name" placeholder="Last name" class="form-control" autocomplete="new-password">
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label for="add_staff_username" class="control-label">Username<span class="text-danger">*</span></label>
                                                        <span id="add_staff_username_status" class="d-block"></span>
                                                        <div class="input-group mb-3">
                                                            <input type="text" name="add_staff_username" id="add_staff_username" placeholder="Username" class="form-control" autocomplete="new-password">
                                                            
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label for="add_staff_phone" class="control-label">Phone<span class="text-danger">*</span></label>
                                                        <span id="add_staff_phone_status" class="d-block"></span>
                                                        <div class="input-group mb-3">
                                                            <input type="tel" name="add_staff_phone" id="add_staff_phone" placeholder="Phone" class="form-control" autocomplete="new-password">
                                                            <div class="input-group-append">
                                                                <div class="input-group-text">
                                                                    <span class="fas fa-phone"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label for="add_staff_email" class="control-label">Email<span class="text-danger">*</span></label>
                                                        <span id="add_staff_email_status" class="d-block"></span>
                                                        <div class="input-group mb-3">
                                                            <input type="text" name="add_staff_email" id="add_staff_email" placeholder="Email" class="form-control" autocomplete="new-password">
                                                            <div class="input-group-append">
                                                                <div class="input-group-text">
                                                                    <span class="fas fa-user"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label for="add_staff_password" class="control-label">Password <span class="text-danger">*</span></label>
                                                        <span id="add_staff_password_status" class="d-block"></span>
                                                        <div class="input-group mb-3">
                                                            <input type="password" name="add_staff_password" id="add_staff_password" placeholder="Password" class="form-control" autocomplete="new-password">
                                                            <div class="input-group-append">
                                                                <div class="input-group-text">
                                                                    <span class="fas fa-lock"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label for="add_staff_confirm_password" class="control-label">Confirm password <span class="text-danger">*</span></label>
                                                        <span id="add_staff_confirm_password_status" class="d-block"></span>
                                                        <div class="input-group mb-3">
                                                            <input type="password" name="add_staff_confirm_password" id="add_staff_confirm_password" placeholder="Confirm password" class="form-control" autocomplete="new-password">
                                                            <div class="input-group-append">
                                                                <div class="input-group-text">
                                                                    <span class="fas fa-lock"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col">
                                                            <button type="submit" name="add_staff_btn" id="add_staff_btn" class="btn btn-primary btn-block">Add staff</button>
                                                            <button type="button" name="add_staff_loader" id="add_staff_loader" class="d-none btn btn-primary btn-block">
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
                                                function addStaffJsValidation() {
                                                    // Initialize a variable to check if input is valid
                                                    var is_input_valid = true;

                                                    // Hide the sign-up button and show the loading spinner
                                                    document.getElementById('add_staff_btn').className = "d-none";
                                                    document.getElementById('add_staff_loader').className = "d-block btn btn-primary btn-block";

                                                    // Check if the first name input is empty
                                                    if (document.getElementById('add_staff_first_name').value === "") {
                                                        // Set the input validity to false
                                                        is_input_valid = false;

                                                        // Add a red border to the first name input and display an error message
                                                        document.getElementById('add_staff_first_name').style = "border: 1px solid #dc3545;";
                                                        document.getElementById('add_staff_first_name_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                        document.getElementById('add_staff_first_name_status').innerHTML = "Required";

                                                        // Hide the loading spinner and show the sign-up button
                                                        document.getElementById('add_staff_loader').className = "d-none";
                                                        document.getElementById('add_staff_btn').className = "d-block btn btn-primary btn-block";
                                                    } else {
                                                        const firstNameRegex = /^[A-Za-z][A-Za-z'-]{0,49}$/;
                                                        /*
                                                            ^: Asserts the position at the start of the string.
                                                            [A-Za-z]: Ensures the first character is a letter (either uppercase or lowercase).
                                                            [A-Za-z'-]{0,49}: Matches 0 to 49 characters that can be uppercase letters, lowercase letters, hyphens (-), or apostrophes (').
                                                            $: Asserts the position at the end of the string.
                                                        */

                                                        if (!firstNameRegex.test(document.getElementById('add_staff_first_name').value)) {
                                                            // Add a red border to the first name input and display an error message
                                                            document.getElementById('add_staff_first_name').style = "border: 1px solid #dc3545;";
                                                            document.getElementById('add_staff_first_name_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                            document.getElementById('add_staff_first_name_status').innerHTML = "Enter a valid name";

                                                            // Hide the loading spinner and show the sign-up button
                                                            document.getElementById('add_staff_loader').className = "d-none";
                                                            document.getElementById('add_staff_btn').className = "d-block btn btn-primary btn-block";
                                                        } else {
                                                            // Add a green border to the first name input
                                                            document.getElementById('add_staff_first_name').style.border = "1px solid #28a745";
                                                            document.getElementById('add_staff_first_name_status').innerHTML = "";
                                                        }
                                                    }

                                                    // Check if the last name input is empty
                                                    if (document.getElementById('add_staff_last_name').value === "") {
                                                        // Set the input validity to false
                                                        is_input_valid = false;

                                                        // Add a red border to the last name input and display an error message
                                                        document.getElementById('add_staff_last_name').style = "border: 1px solid #dc3545;";
                                                        document.getElementById('add_staff_last_name_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                        document.getElementById('add_staff_last_name_status').innerHTML = "Required";

                                                        // Hide the loading spinner and show the sign-up button
                                                        document.getElementById('add_staff_loader').className = "d-none";
                                                        document.getElementById('add_staff_btn').className = "d-block btn btn-primary btn-block";
                                                    } else {
                                                        const lastNameRegex = /^[A-Za-z][A-Za-z'-]{0,49}$/;
                                                        /*
                                                            ^: Asserts the position at the start of the string.
                                                            [A-Za-z]: Ensures the first character is a letter (either uppercase or lowercase).
                                                            [A-Za-z'-]{0,49}: Matches 0 to 49 characters that can be uppercase letters, lowercase letters, hyphens (-), or apostrophes (').
                                                            $: Asserts the position at the end of the string.
                                                        */

                                                        if (!lastNameRegex.test(document.getElementById('add_staff_last_name').value)) {
                                                            // Add a red border to the last name input and display an error message
                                                            document.getElementById('add_staff_last_name').style = "border: 1px solid #dc3545;";
                                                            document.getElementById('add_staff_last_name_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                            document.getElementById('add_staff_last_name_status').innerHTML = "Enter a valid name";

                                                            // Hide the loading spinner and show the sign-up button
                                                            document.getElementById('add_staff_loader').className = "d-none";
                                                            document.getElementById('add_staff_btn').className = "d-block btn btn-primary btn-block";
                                                        } else {
                                                            // Add a green border to the last name input
                                                            document.getElementById('add_staff_last_name').style.border = "1px solid #28a745";
                                                            document.getElementById('add_staff_last_name_status').innerHTML = "";
                                                        }
                                                    }

                                                    // Check if the last name input is empty
                                                    if (document.getElementById('add_staff_phone').value === "") {
                                                        // Set the input validity to false
                                                        is_input_valid = false;

                                                        // Add a red border to the last name input and display an error message
                                                        document.getElementById('add_staff_phone').style = "border: 1px solid #dc3545;";
                                                        document.getElementById('add_staff_phone_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                        document.getElementById('add_staff_phone_status').innerHTML = "Required";

                                                        // Hide the loading spinner and show the sign-up button
                                                        document.getElementById('add_staff_loader').className = "d-none";
                                                        document.getElementById('add_staff_btn').className = "d-block btn btn-primary btn-block";
                                                    } else {
                                                        const phoneNumberRegex = /^\d{10}$/;

                                                        if (!phoneNumberRegex.test(document.getElementById('add_staff_phone').value)) {
                                                            // Add a red border to the phone input and display an error message
                                                            document.getElementById('add_staff_phone').style = "border: 1px solid #dc3545;";
                                                            document.getElementById('add_staff_phone_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                            document.getElementById('add_staff_phone_status').innerHTML = "Enter a valid phone number";

                                                            // Hide the loading spinner and show the sign-up button
                                                            document.getElementById('add_staff_loader').className = "d-none";
                                                            document.getElementById('add_staff_btn').className = "d-block btn btn-primary btn-block";
                                                        } else {
                                                            // Add a green border to the phone input
                                                            document.getElementById('add_staff_phone').style.border = "1px solid #28a745";
                                                            document.getElementById('add_staff_phone_status').innerHTML = "";
                                                        }
                                                    }

                                                    // Check if the username input is empty
                                                    if (document.getElementById('add_staff_username').value === "") {
                                                        // Set the input validity to false
                                                        is_input_valid = false;

                                                        // Add a red border to the username input and display an error message
                                                        document.getElementById('add_staff_username').style = "border: 1px solid #dc3545;";
                                                        document.getElementById('add_staff_username_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                        document.getElementById('add_staff_username_status').innerHTML = "Required";

                                                        // Hide the loading spinner and show the sign-up button
                                                        document.getElementById('add_staff_loader').className = "d-none";
                                                        document.getElementById('add_staff_btn').className = "d-block btn btn-primary btn-block";
                                                    } else {
                                                        // Add a green border to the username input
                                                        document.getElementById('add_staff_username').style.border = "1px solid #28a745";
                                                        document.getElementById('add_staff_username_status').innerHTML = "";
                                                    }

                                                    // Check if the email input is empty
                                                    if (document.getElementById('add_staff_email').value === "") {
                                                        // Set the input validity to false
                                                        is_input_valid = false;

                                                        // Add a red border to the email input and display an error message
                                                        document.getElementById('add_staff_email').style = "border: 1px solid #dc3545;";
                                                        document.getElementById('add_staff_email_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                        document.getElementById('add_staff_email_status').innerHTML = "Required";

                                                        // Hide the loading spinner and show the sign-up button
                                                        document.getElementById('add_staff_loader').className = "d-none";
                                                        document.getElementById('add_staff_btn').className = "d-block btn btn-primary btn-block";
                                                    } else {
                                                        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                                                        // ^ asserts the start of the string.
                                                        // [a-zA-Z0-9._%+-]+ matches one or more alphanumeric characters, dots (.), underscores (_), percentage signs (%), plus signs (+), or hyphens (-).
                                                        // @ matches the @ symbol.
                                                        // [a-zA-Z0-9.-]+ matches one or more alphanumeric characters, dots (.), or hyphens (-).
                                                        // \. matches the dot (.) character.
                                                        // [a-zA-Z]{2,} matches two or more alphabetic characters.
                                                        // $ asserts the end of the string.

                                                        if (!emailRegex.test(document.getElementById('add_staff_email').value)) {
                                                            // Add a red border to the email input and display an error message
                                                            document.getElementById('add_staff_email').style = "border: 1px solid #dc3545;";
                                                            document.getElementById('add_staff_email_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                            document.getElementById('add_staff_email_status').innerHTML = "Enter a valid email";

                                                            // Hide the loading spinner and show the sign-up button
                                                            document.getElementById('add_staff_loader').className = "d-none";
                                                            document.getElementById('add_staff_btn').className = "d-block btn btn-primary btn-block";
                                                        } else {
                                                            // Add a green border to the email input
                                                            document.getElementById('add_staff_email').style.border = "1px solid #28a745";
                                                            document.getElementById('add_staff_email_status').innerHTML = "";
                                                        }
                                                    }

                                                    // Check if the password input is empty
                                                    if (document.getElementById('add_staff_password').value === "") {
                                                        // Set the input validity to false
                                                        is_input_valid = false;

                                                        // Add a red border to the password input and display an error message
                                                        document.getElementById('add_staff_password').style = "border: 1px solid #dc3545;";
                                                        document.getElementById('add_staff_password_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                        document.getElementById('add_staff_password_status').innerHTML = "Required";

                                                        // Hide the loading spinner and show the sign-up button
                                                        document.getElementById('add_staff_loader').className = "d-none";
                                                        document.getElementById('add_staff_btn').className = "d-block btn btn-primary btn-block";
                                                    } else {
                                                        // Add a green border to the password input
                                                        document.getElementById('add_staff_password').style.border = "1px solid #28a745";
                                                        document.getElementById('add_staff_password_status').innerHTML = "";

                                                        // Check if the confirm password input is empty
                                                        if (document.getElementById('add_staff_confirm_password').value === "") {
                                                            // Set the input validity to false
                                                            is_input_valid = false;

                                                            // Add a red border to the confirm password input and display an error message
                                                            document.getElementById('add_staff_confirm_password').style = "border: 1px solid #dc3545;";
                                                            document.getElementById('add_staff_confirm_password_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                            document.getElementById('add_staff_confirm_password_status').innerHTML = "Required";

                                                            // Hide the loading spinner and show the sign-up button
                                                            document.getElementById('add_staff_loader').className = "d-none";
                                                            document.getElementById('add_staff_btn').className = "d-block btn btn-primary btn-block";
                                                        } else {
                                                            if (document.getElementById('add_staff_password').value !== document.getElementById('add_staff_confirm_password').value) {
                                                                // Set the input validity to false
                                                                is_input_valid = false;

                                                                // Add a red border to the confirm password input and display an error message
                                                                document.getElementById('add_staff_confirm_password').style = "border: 1px solid #dc3545;";
                                                                document.getElementById('add_staff_confirm_password_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                                document.getElementById('add_staff_confirm_password_status').innerHTML = "Passwords do not match";

                                                                // Hide the loading spinner and show the sign-up button
                                                                document.getElementById('add_staff_loader').className = "d-none";
                                                                document.getElementById('add_staff_btn').className = "d-block btn btn-primary btn-block";
                                                            } else {
                                                                // Add a green border to the confirm password input
                                                                document.getElementById('add_staff_confirm_password').style.border = "1px solid #28a745";
                                                                document.getElementById('add_staff_confirm_password_status').innerHTML = "";
                                                            }
                                                        }
                                                    }

                                                    // Return the validity of the input
                                                    return is_input_valid;
                                                }
                                            </script>
                                        </div>
                                    </div>
                                <?php }elseif (isset($_GET['tsk'])==true && $_GET['tsk']=='view_staff') {?>
                                    <div class="card card-outline card-dark" style="border-bottom: 3px solid #343a40;">
                                        <div class="card-header">
                                            <h6 class="text-dark font-weight-bold">View staff</h6>
                                        </div>
                                        <div class="card-body">
                                            <table class="text-nowrap table table-hover table-striped table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl" id="staff_list_table">
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
                                                        $tbl_user_type = '8';
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
                                                            <td><span class="btn btn-sm btn-block btn-primary" onclick="window.open('staffDetalis?id=<?php echo $fetchCustomersAssoc['user_id']; ?>','popup','width=900,height=600'); return false;">View</span></td>
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
                    $(document).ready( function (){$('#staff_list_table').DataTable();});
                </script>
            </body>
        </html>
<?php $dbConnection->close(); // Close the database connection ?>