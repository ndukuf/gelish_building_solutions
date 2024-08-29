<?php
    /* Require necessary files for the application to function properly. */
    //establishes a connection to the database.
    require 'database_connection.php';

    //contains company-related data.
    require 'company_data.php';

    //provides the current date and time.
    require 'current_date_and_time.php';
    
    $email = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_GET['email']),ENT_QUOTES,'UTF-8')));
    $token = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_GET['token']),ENT_QUOTES,'UTF-8')));

    if (!isset($email) || empty($email)) {
        // Redirect the user to the 'reset_password' page
        header('Location: reset_password?email=invalid');
        exit();
    }
    if (!isset($token) || empty($token)) {
        // Redirect the user to the 'reset_password' page
        header('Location: reset_password?token=invalid');
        exit();
    }

    // Prepare a SQL query to fetch otp from forgot_password_accounts table
    $fetchAccountOTPDataSql = 'SELECT `email`,`opt`,`date_and_time`,`token` FROM `forgot_password_accounts` WHERE `email` = ? ORDER BY  `date_and_time` DESC LIMIT 1';

    // Prepare the SQL statement
    $fetchAccountOTPDataStmt = $dbConnection->prepare($fetchAccountOTPDataSql);

    // Bind parameters to the prepared statement
    $fetchAccountOTPDataStmt->bind_param('s',$email);

    // Execute the prepared statement
    $fetchAccountOTPDataStmt->execute();

    // Retrieve the result set
    $fetchAccountOTPDataResult = $fetchAccountOTPDataStmt->get_result();

    // Fetch data as an associative array
    $fetchAccountOTPDataAssoc= $fetchAccountOTPDataResult->fetch_assoc();


    // If the values do not verify, redirect to sign_in page with an error message
    if (!password_verify($fetchAccountOTPDataAssoc['email'].'#\@/#',$token)) {
        header('Location:sign_in?signInError=invalid_token');
        exit();
    } else {
        // Create DateTime object for OTP date and time
        $otpDateAndTime = new DateTime(date('Y-m-d H:i:s', strtotime($fetchAccountOTPDataAssoc['date_and_time'])));

        // Create DateTime object for current date and time
        $currentDateAndTime = new DateTime(date('Y-m-d H:i:s', strtotime($currentDateAndTime)));

        // Calculate the difference between current time and OTP time
        $timeDifference = $currentDateAndTime->diff($otpDateAndTime);

        // Convert days to minutes
        $minuteDifference = $timeDifference->days*24*60;

        // Add hours to minutes
        $minuteDifference += $timeDifference->h*60;

        // Add remaining minutes
        $minuteDifference += $timeDifference->i;

        if (strval($minuteDifference) > '15') {
            header('Location:sign_in?signInError=otp_expired');
            exit();
        }
    }

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'resetPassword' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['resetPassword']) == true) {
        // Sanitize and validate user input data to prevent SQL injection and cross-site scripting (XSS) attacks
        $newPassword = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_POST['newPassword']),ENT_QUOTES,'UTF-8')));
        $confirmNewPassword = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_POST['confirmNewPassword']),ENT_QUOTES,'UTF-8')));

        if (empty($newPassword)) {
            // Redirect to the reset_password page
            header('Location: reset_password&status=empty_newPassword');
            exit();
        }

        if (empty($confirmNewPassword)) {
            // Redirect to the reset_password page
            header('Location: reset_password&status=empty_confirmNewPassword');
            exit();
        }

        if (strval($newPassword) !== strval($confirmNewPassword)) {
            // Redirect to the reset_password page
            header('Location: reset_password&status=no_match');
            exit();
        }

        // Prepare a SQL query to fetch user data from the users table
        $fetchUserEmailSql = 'SELECT `email_address`,`system_access` FROM `users` WHERE `email_address`=? LIMIT 1';

        // Prepare the SQL statement
        $fetchUserEmailStmt = $dbConnection->prepare($fetchUserEmailSql);

        // Bind parameters to the prepared statement
        $fetchUserEmailStmt->bind_param('s',$email);

        // Execute the prepared statement
        $fetchUserEmailStmt->execute();

        // Retrieve the result set
        $fetchUserEmailResult = $fetchUserEmailStmt->get_result();

        // Fetch data as an associative array
        $fetchUserEmailAssoc= $fetchUserEmailResult->fetch_assoc();

        // Check if the user does not exist, redirect to sign_in page with an error message
        if ($fetchUserEmailResult->num_rows !== 1) {
            header('Location:sign_in?signInError=invalid_email');
            exit();
        }

        if (strval($fetchUserEmailAssoc['system_access']) !== '7') {
            // Redirect to the sign_in page
            header('Location: sign_in?sts=ctn');
            exit();
        }

        // Hash the current password using the default password hashing algorithm
        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // system_access value
        $system_access = '5';

        // Prepare a SQL query to update user password in the database
        $updateUserPasswordSql = 'UPDATE `users` SET `user_password`=?,`system_access`=? WHERE `email_address`=?';
        // Prepare the SQL statement
        $updateUserPasswordStmt = $dbConnection->prepare($updateUserPasswordSql);
        // Bind parameters to the prepared statement
        $updateUserPasswordStmt->bind_param('sss',$hashedNewPassword,$system_access,$email);
        // Execute the prepared statement
        if (!$updateUserPasswordStmt->execute()) {
            // Redirect to the reset_password page
            header('Location: reset_password&status=try_again');
            exit();
        } else {
            // Close the $updateUserPasswordStmt prepared statement to free up resources
            $updateUserPasswordStmt->close();
            // Redirect to the sign_in page
            header('Location: sign_in?sts=rdy');
            exit();
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $fetchCompanyDataAssoc['company_name']; ?> | Reset password</title>
    <!-- require head.php php file -->
    <?php require 'head.php'; ?>
    </head>
    <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
        <div class="wrapper">
            <?php require 'navbar.php'; ?>
            <div class="content-wrapper mb-4">
                <div class="mt-5 container mx-auto col-sm-6 col-md-6 col-lg-4 col-xl-4">
                    <div class="card">
                        <div class="card-header text-center">
                            <strong>Reset password</strong>
                            <?php if (isset($_GET['token'])==true && $_GET['token']==='invalid') { ?>
                                <h6 class="mt-1" style="color: #dc3545 !important;">Invalid token. Try <a href="sign_in" class="text-decoration-none">signing in</a></h6>
                            <?php } ?>
                        </div>
                        <div class="card-body">
                            <form action="" method="post" onsubmit="return resetPasswordJsValidation();">
                                <label for="newPassword" class="control-label">New password<span class="text-danger">*</span></label>
                                <span id="newPasswordStatus" class="d-block"></span>
                                <div class="input-group mb-3">
                                    <input type="password" name="newPassword" id="newPassword" placeholder="New password" class="form-control" autocomplete="new-password" autofocus>
                                </div>

                                <label for="confirmNewPassword" class="control-label">Confirm new password<span class="text-danger">*</span></label>
                                <span id="confirmNewPasswordStatus" class="d-block"></span>
                                <div class="input-group mb-3">
                                    <input type="password" name="confirmNewPassword" id="confirmNewPassword" placeholder="Confirm new password " class="form-control" autocomplete="new-password">
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <button type="submit" name="resetPassword" id="resetPassword" class="d-block btn btn-primary btn-block">Reset password</button>
                                        <span name="resetPasswordLoader" id="resetPasswordLoader" class="d-none btn btn-primary btn-block">
                                            <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                        </span>
                                    </div>
                                </div>
                            </form>
                            <script>
                                function resetPasswordJsValidation() {
                                    // Set the value of is_input_valid to be true by default
                                    var is_input_valid = true;

                                    // If the value newPassword is empty show the status message
                                    if (document.getElementById('newPassword').value === "") {
                                        is_input_valid = false;

                                        document.getElementById('resetPasswordLoader').className = "d-none";
                                        document.getElementById('resetPassword').className = "d-block btn btn-primary btn-block";

                                        document.getElementById('newPassword').focus();
                                        document.getElementById('newPassword').style.border = "1px solid #dc3545";
                                        document.getElementById('newPasswordStatus').style.color = "#dc3545";
                                        document.getElementById('newPasswordStatus').innerHTML = "Please enter your new password";
                                    } else {
                                        document.getElementById('newPassword').style.border = "1px solid #28a745";
                                        document.getElementById('newPasswordStatus').innerHTML = "";
                                        // If the value confirmNewPassword is empty show the status message
                                        if (document.getElementById('confirmNewPassword').value === "") {
                                            is_input_valid = false;

                                            document.getElementById('resetPasswordLoader').className = "d-none";
                                            document.getElementById('resetPassword').className = "d-block btn btn-primary btn-block";

                                            document.getElementById('confirmNewPassword').focus();
                                            document.getElementById('confirmNewPassword').style.border = "1px solid #dc3545";
                                            document.getElementById('confirmNewPasswordStatus').style.color = "#dc3545";
                                            document.getElementById('confirmNewPasswordStatus').innerHTML = "Please enter your new password";
                                        } else {
                                            if (String(document.getElementById('newPassword').value) !== String(document.getElementById('confirmNewPassword').value)) {
                                                is_input_valid = false;

                                                document.getElementById('resetPasswordLoader').className = "d-none";
                                                document.getElementById('resetPassword').className = "d-block btn btn-primary btn-block";

                                                document.getElementById('confirmNewPassword').focus();
                                                document.getElementById('confirmNewPassword').style.border = "1px solid #dc3545";
                                                document.getElementById('confirmNewPasswordStatus').style.color = "#dc3545";
                                                document.getElementById('confirmNewPasswordStatus').innerHTML = "Passwords do not match";
                                            } else {
                                                document.getElementById('confirmNewPassword').style.border = "1px solid #28a745";
                                                document.getElementById('confirmNewPasswordStatus').innerHTML = "";
                                            }
                                        }
                                    }
                                    // Return the value of is_input_valid
                                    return is_input_valid;
                                }
                            </script>
                        </div>
                    </div>
                </div>
            </div>
            <!-- require footer.php php file -->
            <?php require 'footer.php'; ?>
        </div>
        <!-- require foot.php php file -->
        <?php require 'foot.php'; ?>
    </body>
</html>
<?php $dbConnection->close(); // Close the database connection ?>