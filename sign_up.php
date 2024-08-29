<?php
    /* Require necessary files for the application to function properly. */
    //establishes a connection to the database.
    require 'database_connection.php';

    //contains company-related data.
    require 'company_data.php';

    //provides the current date and time.
    require 'current_date_and_time.php';

    // Check if the request method is POST and if the sign_up_btn is set
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['sign_up_btn']) == true) {

        // Escape and sanitize the sign_up_first_name input
        $sign_up_first_name = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['sign_up_first_name']), ENT_QUOTES, 'UTF-8')));

        // Escape and sanitize the sign_up_last_name input
        $sign_up_last_name = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['sign_up_last_name']), ENT_QUOTES, 'UTF-8')));

        // Escape and sanitize the sign_up_email input
        $sign_up_username = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['sign_up_username']), ENT_QUOTES, 'UTF-8')));
        
        // Escape and sanitize the sign_up_email input
        $sign_up_email = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['sign_up_email']), ENT_QUOTES, 'UTF-8')));

        // Escape and sanitize the sign_up_password input
        $sign_up_password = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['sign_up_password']), ENT_QUOTES, 'UTF-8')));

        // Escape and sanitize the sign_up_confirm_password input
        $sign_up_confirm_password = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['sign_up_confirm_password']), ENT_QUOTES, 'UTF-8')));


        // Check if the sign_up_first_name input is empty
        if (empty($sign_up_first_name) == true) {
            // Redirect to the sign_up page with an error message if the sign_up_first_name input is empty
            header('Location:sign_up?signUpError=empty_first_name');
            exit();
        }

        // Check if the sign_up_last_name input is empty
        if (empty($sign_up_last_name) == true) {
            // Redirect to the sign_up page with an error message if the sign_up_last_name input is empty
            header('Location:sign_up?signUpError=empty_last_name');
            exit();
        }

        // Check if the sign_up_username input is empty
        if (empty($sign_up_username) == true) {
            // Redirect to the sign_up page with an error message if the sign_up_username input is empty
            header('Location:sign_up?signUpError=empty_username');
            exit();
        }
        
        // Check if the sign_up_email input is empty
        if (empty($sign_up_email) == true) {
            // Redirect to the sign_up page with an error message if the sign_up_email input is empty
            header('Location:sign_up?signUpError=empty_email');
            exit();
        }

        // Check if the sign_up_password input is empty
        if (empty($sign_up_password) == true) {
            // Redirect to the sign_up page with an error message if the sign_up_password input is empty
            header('Location:sign_up?signUpError=empty_password');
            exit();
        }

        // Check if the sign_up_confirm_password input is empty
        if (empty($sign_up_confirm_password) == true) {
            // Redirect to the sign_up page with an error message if the sign_up_confirm_password input is empty
            header('Location:sign_up?signUpError=empty_confirm_password');
            exit();
        }

        // Validate the sign_up_email input using PHP's built-in filter_var function and the FILTER_VALIDATE_EMAIL filter.
        if (!filter_var($sign_up_email, FILTER_VALIDATE_EMAIL)) {
            // If the sign_up_email input is invalid, redirect the user to the sign_up page with an error message.
            header('Location:sign_up?signUpError=invalid_email');
            exit();
        }

        // Check if the entered password and confirm password match
        if ($sign_up_password!== $sign_up_confirm_password) {
            // Redirect to the sign-up page with an error message indicating that the passwords do not match
            header('Location:sign_up?signUpError=pwd_match');
            exit();
        }

        // Prepare a SQL statement to select the username from the 'users' table where the username matches the provided sign_up_username
        $selectUsernameSql = 'SELECT `username` FROM `users` WHERE `username` =?';
        
        // Prepare the SQL statement
        $selectUsernameStmt = $dbConnection->prepare($selectUsernameSql);

        // Bind the sign_up_username variable to the prepared statement as a string
        $selectUsernameStmt->bind_param('s', $sign_up_username);

        // Execute the prepared statement
        $selectUsernameStmt->execute();

        // Store the result of the executed statement
        $selectUsernameStmt->store_result();

        // Check if the number of rows returned by the executed statement is 1, indicating that the username already exists in the 'users' table
        if ($selectUsernameStmt->num_rows===1) {
            // Redirect the user to the sign_up page with an error message indicating that the username already exists
            header('Location:sign_up?signUpError=username_exists');
            // Exit the current script to prevent further execution
            exit();
        }

        // Prepare a SQL statement to select the email address from the 'users' table where the email address matches the provided sign_up_email
        $selectEmailSql = 'SELECT `email_address` FROM `users` WHERE `email_address` =?';
        
        // Prepare the SQL statement
        $selectEmailStmt = $dbConnection->prepare($selectEmailSql);

        // Bind the sign_up_email variable to the prepared statement as a string
        $selectEmailStmt->bind_param('s', $sign_up_email);

        // Execute the prepared statement
        $selectEmailStmt->execute();

        // Store the result of the executed statement
        $selectEmailStmt->store_result();

        // Check if the number of rows returned by the executed statement is 1, indicating that the email address already exists in the 'users' table
        if ($selectEmailStmt->num_rows===1) {
            // Redirect the user to the sign_up page with an error message indicating that the email address already exists
            header('Location:sign_up?signUpError=email_exists');
            // Exit the current script to prevent further execution
            exit();
        }

        // Hash the sign-up password using the default password hashing algorithm
        $hashPass = password_hash($sign_up_password, PASSWORD_DEFAULT);

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
        
        // Define the user type as '7'
        $user_type = '7';

        // Define the value of allowed system access
        $system_access = '5';

        // Set the last login to 'n_usr' indicating no user has logged in yet
        $last_login = 'n_usr';

        // Set the last_login_type to 'n_usr' indicating no user has logged in yet
        $last_login_type = 'n_usr';

        // Set the last seen to 'n_usr' indicating no user has been seen yet
        $last_seen = 'n_usr';

        // Define the SQL query to add a new user to the 'users' table
        $addNewUserSql = 'INSERT INTO `users`(`user_id`,`user_type`,`user_first_name`,`user_last_name`,`email_address`,`username`,`user_password`,`date_joined`,`system_access`,`last_login`,`last_login_type`,`last_seen`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)';

        // Prepare the SQL query statement
        $addNewUserStmt = $dbConnection->prepare($addNewUserSql);

        // Bind the parameters to the prepared statement
        $addNewUserStmt->bind_param('ssssssssssss', $user_id,$user_type,$sign_up_first_name,$sign_up_last_name,$sign_up_email,$sign_up_username,$hashPass,$date_joined,$system_access,$last_login,$last_login_type,$last_seen);

        // Attempt to execute the $addNewUserStmt statement
        if ($addNewUserStmt->execute()) {
            // If the statement is executed successfully, redirect the user to the sign-in page with a status query parameter set to 'rdy' (ready)
            header('Location:sign_in?sts=rdy');
            // Stop the execution of the script to prevent any further actions
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
        <title><?php echo $fetchCompanyDataAssoc['company_name']; ?> | Sign up</title>
        <?php require_once 'head.php'; ?>
    </head>
    <script>
        //JavaScript code to prevent the form from being resubmitted when the user refreshes the page.
        if ( window.history.replaceState ){window.history.replaceState( null, null, window.location.href );}
    </script>
    <body class="bg-secondary">
        <?php require_once 'navbar.php'; ?>
        <div class="authentication_form">
            <div class="card p-0 col-sm-6 col-md-6 col-lg-4 col-xl-4">
                <form action="" method="post" id="login-form" onsubmit="return signUpJsValidation();">
                <!-- <form action="" method="post" id="login-form"> -->
                <div class="card-header bg-transparent text-center font-weight-bold">
                    Sign up<br>
                    <?php if (isset($_GET['signUpError'])==true && $_GET['signUpError']==='empty_first_name') { ?>
                        <h6 class="mt-1" style="color: #dc3545 !important;">Please enter your first name</h6>
                    <?php }elseif (isset($_GET['signUpError'])==true && $_GET['signUpError']==='empty_last_name') { ?>
                        <h6 class="mt-1" style="color: #dc3545 !important;">Please enter your last name</h6>
                    <?php }elseif (isset($_GET['signUpError'])==true && $_GET['signUpError']==='empty_username') { ?>
                        <h6 class="mt-1" style="color: #dc3545 !important;">Please enter your username</h6>
                    <?php }elseif (isset($_GET['signUpError'])==true && $_GET['signUpError']==='empty_email') { ?>
                        <h6 class="mt-1" style="color: #dc3545 !important;">Please enter your email</h6>
                    <?php }elseif (isset($_GET['signUpError'])==true && $_GET['signUpError']==='empty_password') { ?>
                        <h6 class="mt-1" style="color: #dc3545 !important;">Please enter your password</h6>
                    <?php }elseif (isset($_GET['signUpError'])==true && $_GET['signUpError']==='empty_confirm_password') { ?>
                        <h6 class="mt-1" style="color: #dc3545 !important;">Please confirm your password</h6>
                    <?php }elseif (isset($_GET['signUpError'])==true && $_GET['signUpError']==='invalid_email') { ?>
                        <h6 class="mt-1" style="color: #dc3545 !important;">Please enter a valid email</h6>
                    <?php }elseif (isset($_GET['signUpError'])==true && $_GET['signUpError']==='pwd_match') { ?>
                        <h6 class="mt-1" style="color: #dc3545 !important;">Passwords do not match</h6>
                        <?php }elseif (isset($_GET['signUpError'])==true && $_GET['signUpError']==='username_exists') { ?>
                            <h6 class="mt-1" style="color: #dc3545 !important;">The username already exists</h6>
                        <?php }elseif (isset($_GET['signUpError'])==true && $_GET['signUpError']==='email_exists') { ?>
                            <h6 class="mt-1" style="color: #dc3545 !important;">The email already exists</h6>
                    <?php } ?>
                </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="mb-2 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                <label for="sign_up_first_name" class="control-label">First name<span class="text-danger">*</span></label>
                                <span id="sign_up_first_name_status" class="d-block"></span>
                                <input type="text" name="sign_up_first_name" id="sign_up_first_name" placeholder="First name" class="form-control" autocomplete="new-password" autofocus>
                            </div>
                            <div class="mb-2 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                <label for="sign_up_last_name" class="control-label">Last name<span class="text-danger">*</span></label>
                                <span id="sign_up_last_name_status" class="d-block"></span>
                                <input type="text" name="sign_up_last_name" id="sign_up_last_name" placeholder="Last name" class="form-control" autocomplete="new-password">
                            </div>
                        </div>

                        <div>
                            <label for="sign_up_username" class="control-label">Username<span class="text-danger">*</span></label>
                            <span id="sign_up_username_status" class="d-block"></span>
                            <div class="input-group mb-3">
                                <input type="text" name="sign_up_username" id="sign_up_username" placeholder="Username" class="form-control" autocomplete="new-password">
                                
                            </div>
                        </div>

                        <div>
                            <label for="sign_up_email" class="control-label">Email<span class="text-danger">*</span></label>
                            <span id="sign_up_email_status" class="d-block"></span>
                            <div class="input-group mb-3">
                                <input type="text" name="sign_up_email" id="sign_up_email" placeholder="Email" class="form-control" autocomplete="new-password">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-user"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="sign_up_password" class="control-label">Password <span class="text-danger">*</span></label>
                            <span id="sign_up_password_status" class="d-block"></span>
                            <div class="input-group mb-3">
                                <input type="password" name="sign_up_password" id="sign_up_password" placeholder="Password" class="form-control" autocomplete="new-password">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-lock"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="sign_up_confirm_password" class="control-label">Confirm password <span class="text-danger">*</span></label>
                            <span id="sign_up_confirm_password_status" class="d-block"></span>
                            <div class="input-group mb-3">
                                <input type="password" name="sign_up_confirm_password" id="sign_up_confirm_password" placeholder="Confirm password" class="form-control" autocomplete="new-password">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-lock"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <button type="submit" name="sign_up_btn" id="sign_up_btn" class="btn btn-primary btn-block">Sign up</button>
                                <button type="button" name="sign_up_loader" id="sign_up_loader" class="d-none btn btn-primary btn-block">
                                    <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center font-weight-bold">
                        <div class="row">
                            <a class="col" style="color:#12487d !important;" href="sign_in">I have an account</a>
                        </div>
                        <h6 class="d-block text-muted mt-2">&copy; <?php echo date('Y'); ?> <?php echo $fetchCompanyDataAssoc['company_name'] ?></h6>
                    </div>
                </form>
                <script>
                    // Function to validate sign-up form using JavaScript
                    function signUpJsValidation() {
                        // Initialize a variable to check if input is valid
                        var is_input_valid = true;

                        // Hide the sign-up button and show the loading spinner
                        document.getElementById('sign_up_btn').className = "d-none";
                        document.getElementById('sign_up_loader').className = "d-block btn btn-primary btn-block";

                        // Check if the first name input is empty
                        if (document.getElementById('sign_up_first_name').value === "") {
                            // Set the input validity to false
                            is_input_valid = false;

                            // Add a red border to the first name input and display an error message
                            document.getElementById('sign_up_first_name').style = "border: 1px solid #dc3545;";
                            document.getElementById('sign_up_first_name_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                            document.getElementById('sign_up_first_name_status').innerHTML = "Required";

                            // Hide the loading spinner and show the sign-up button
                            document.getElementById('sign_up_loader').className = "d-none";
                            document.getElementById('sign_up_btn').className = "d-block btn btn-primary btn-block";
                        } else {
                            // Add a green border to the first name input
                            document.getElementById('sign_up_first_name').style.border = "1px solid #28a745";
                            document.getElementById('sign_up_first_name_status').innerHTML = "";
                        }

                        // Check if the last name input is empty
                        if (document.getElementById('sign_up_last_name').value === "") {
                            // Set the input validity to false
                            is_input_valid = false;

                            // Add a red border to the last name input and display an error message
                            document.getElementById('sign_up_last_name').style = "border: 1px solid #dc3545;";
                            document.getElementById('sign_up_last_name_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                            document.getElementById('sign_up_last_name_status').innerHTML = "Required";

                            // Hide the loading spinner and show the sign-up button
                            document.getElementById('sign_up_loader').className = "d-none";
                            document.getElementById('sign_up_btn').className = "d-block btn btn-primary btn-block";
                        } else {
                            // Add a green border to the last name input
                            document.getElementById('sign_up_last_name').style.border = "1px solid #28a745";
                            document.getElementById('sign_up_last_name_status').innerHTML = "";
                        }

                        // Check if the username input is empty
                        if (document.getElementById('sign_up_username').value === "") {
                            // Set the input validity to false
                            is_input_valid = false;

                            // Add a red border to the username input and display an error message
                            document.getElementById('sign_up_username').style = "border: 1px solid #dc3545;";
                            document.getElementById('sign_up_username_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                            document.getElementById('sign_up_username_status').innerHTML = "Required";

                            // Hide the loading spinner and show the sign-up button
                            document.getElementById('sign_up_loader').className = "d-none";
                            document.getElementById('sign_up_btn').className = "d-block btn btn-primary btn-block";
                        } else {
                            // Add a green border to the username input
                            document.getElementById('sign_up_username').style.border = "1px solid #28a745";
                            document.getElementById('sign_up_username_status').innerHTML = "";
                        }

                        // Check if the email input is empty
                        if (document.getElementById('sign_up_email').value === "") {
                            // Set the input validity to false
                            is_input_valid = false;

                            // Add a red border to the email input and display an error message
                            document.getElementById('sign_up_email').style = "border: 1px solid #dc3545;";
                            document.getElementById('sign_up_email_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                            document.getElementById('sign_up_email_status').innerHTML = "Required";

                            // Hide the loading spinner and show the sign-up button
                            document.getElementById('sign_up_loader').className = "d-none";
                            document.getElementById('sign_up_btn').className = "d-block btn btn-primary btn-block";
                        } else {
                            // Add a green border to the email input
                            document.getElementById('sign_up_email').style.border = "1px solid #28a745";
                            document.getElementById('sign_up_email_status').innerHTML = "";
                        }

                        // Check if the password input is empty
                        if (document.getElementById('sign_up_password').value === "") {
                            // Set the input validity to false
                            is_input_valid = false;

                            // Add a red border to the password input and display an error message
                            document.getElementById('sign_up_password').style = "border: 1px solid #dc3545;";
                            document.getElementById('sign_up_password_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                            document.getElementById('sign_up_password_status').innerHTML = "Required";

                            // Hide the loading spinner and show the sign-up button
                            document.getElementById('sign_up_loader').className = "d-none";
                            document.getElementById('sign_up_btn').className = "d-block btn btn-primary btn-block";
                        } else {
                            // Add a green border to the password input
                            document.getElementById('sign_up_password').style.border = "1px solid #28a745";
                            document.getElementById('sign_up_password_status').innerHTML = "";

                            // Check if the confirm password input is empty
                            if (document.getElementById('sign_up_confirm_password').value === "") {
                                // Set the input validity to false
                                is_input_valid = false;

                                // Add a red border to the confirm password input and display an error message
                                document.getElementById('sign_up_confirm_password').style = "border: 1px solid #dc3545;";
                                document.getElementById('sign_up_confirm_password_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                document.getElementById('sign_up_confirm_password_status').innerHTML = "Required";

                                // Hide the loading spinner and show the sign-up button
                                document.getElementById('sign_up_loader').className = "d-none";
                                document.getElementById('sign_up_btn').className = "d-block btn btn-primary btn-block";
                            } else {
                                if (document.getElementById('sign_up_password').value !== document.getElementById('sign_up_confirm_password').value) {
                                    // Set the input validity to false
                                    is_input_valid = false;

                                    // Add a red border to the confirm password input and display an error message
                                    document.getElementById('sign_up_confirm_password').style = "border: 1px solid #dc3545;";
                                    document.getElementById('sign_up_confirm_password_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                    document.getElementById('sign_up_confirm_password_status').innerHTML = "Passwords do not match";

                                    // Hide the loading spinner and show the sign-up button
                                    document.getElementById('sign_up_loader').className = "d-none";
                                    document.getElementById('sign_up_btn').className = "d-block btn btn-primary btn-block";
                                } else {
                                    // Add a green border to the confirm password input
                                    document.getElementById('sign_up_confirm_password').style.border = "1px solid #28a745";
                                    document.getElementById('sign_up_confirm_password_status').innerHTML = "";
                                }
                            }
                        }

                        // Return the validity of the input
                        return is_input_valid;
                    }
                </script>
            </div>
        </div>
        <?php require_once 'footer.php'; ?>
        <?php require_once 'foot.php'; ?>
    </body>
</html>
<?php $dbConnection->close(); // Close the database connection ?>