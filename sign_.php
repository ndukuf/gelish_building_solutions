<?php
    /* Require necessary files for the application to function properly. */
    //establishes a connection to the database.
    require 'database_connection.php';

    //contains company-related data.
    require 'company_data.php';

    //provides the current date and time.
    require 'current_date_and_time.php';

    // Check if the 'user_id' session variable is set and has a length greater than 0
    if (isset($_SESSION['user_id']) == true && strlen($_SESSION['user_id']) > 0) {
        // Redirect the user to the 'index' page and stop further execution
        header('Location: index');
        exit();
    }

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'sign_in' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['sign_in']) == true) {
        // Remove unnecessary code - echo $currentDateAndTime;

        // Sanitize and validate user input data to prevent SQL injection and cross-site scripting (XSS) attacks
        $userIdentity = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_POST['userIdentity']),ENT_QUOTES,'UTF-8')));
        $loginPassword = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_POST['loginPassword']),ENT_QUOTES,'UTF-8')));

        // Check if the user identity is empty, redirect to sign_in page with an error message
        if (empty($userIdentity)==true) {
            header('Location:sign_in?signInError=empty_user');
            exit();
        }

        // Check if the login password is empty, redirect to sign_in page with an error message
        if (empty($loginPassword)==true) {
            header('Location:sign_in?signInError=empty_password');
            exit();
        }

        // Prepare a SQL query to fetch user data from the users table
        $fetchUserSql = 'SELECT `user_id`,`user_type`,`user_phone_number`,`email_address`,`username`,`user_password`,`system_access` FROM `users` WHERE `user_phone_number`=? OR `email_address`=? OR `username`=? LIMIT 1';

        // Prepare the SQL statement
        $fetchUserStmt = $dbConnection->prepare($fetchUserSql);

        // Bind parameters to the prepared statement
        $fetchUserStmt->bind_param('sss',$userIdentity,$userIdentity,$userIdentity);

        // Execute the prepared statement
        $fetchUserStmt->execute();

        // Retrieve the result set
        $fetchUserResult = $fetchUserStmt->get_result();

        // Fetch data as an associative array
        $fetchUserAssoc= $fetchUserResult->fetch_assoc();

        // Check if the user does not exist, redirect to sign_in page with an error message
        if ($fetchUserResult->num_rows !== 1) {
            header('Location:sign_in?signInError=invalid_credentials');
            exit();
        }

        // If the user has system access level 5
        if (strval($fetchUserAssoc['system_access']) === '5') {
            // Verify the provided login password against the stored password for the fetched user
            if (password_verify($loginPassword,$fetchUserAssoc['user_password']) == true) {
                // If the user has system access level 5, set the session variable 'user_id' value to be the user's id
                $_SESSION['user_id'] = $fetchUserAssoc['user_id'];
                $_SESSION['user_type'] = $fetchUserAssoc['user_type'];

                // Check if the user_id session variable is set and not empty
                if (empty($_SESSION['user_id']) == false && strlen($_SESSION['user_id']) > 0) {

                    // Update the user's last login information in the database
                    $last_login = $currentDateAndTime;
                    $last_login_type = 'password';
                    $last_seen = 'online';
                    $log_user_id = $_SESSION['user_id'];

                    // SQL query to update the user's last login information
                    $updateLogInSql = 'UPDATE `users` SET `last_login`=?, `last_login_type`=?, `last_seen`=? WHERE `user_id`=?';

                    // Prepare the SQL query for execution
                    $updateLogInStmt = $dbConnection->prepare($updateLogInSql);

                    // Bind the parameters to the SQL query
                    $updateLogInStmt->bind_param('ssss', $last_login, $last_login_type, $last_seen, $log_user_id);

                    // Execute the SQL query
                    if ($updateLogInStmt->execute()) {
                        if ($_SESSION['user_type'] == '7') {
                            // Redirect the user to the index page if they are already logged in
                            header('Location: index');
                            // Stop further execution of the script
                            exit();
                        }elseif ($_SESSION['user_type'] == '8') {
                            // Redirect the user to the care folder if they are already logged in
                            header('Location: care/');
                            // Stop further execution of the script
                            exit();
                        }elseif ($_SESSION['user_type'] == '9') {
                            // Redirect the user to the admin folder if they are already logged in
                            header('Location: admin/');
                            // Stop further execution of the script
                            exit();
                        }
                    }
                }
            } else {
                // If the passwords do not match, redirect to sign_in page with an error message
                header('Location:sign_in?signInError=invalid_credentials');
                exit();
            }
        }

        // If the user has system access level 6, redirect them to the sign-in page with an error message indicating an issue with their account
        if (strval($fetchUserAssoc['system_access']) === '6') {
            // If the passwords do not match, redirect to sign_in page with an error message
            if (!password_verify($loginPassword,$fetchUserAssoc['user_password'])) {
                header('Location:sign_in?signInError=acc');
                exit();
            }
        }

        // If the user has system access level 7
        if (strval($fetchUserAssoc['system_access']) === '7') {
            // Prepare a SQL query to fetch otp from forgot_password_accounts table
            $fetchAccountOTPDataSql = 'SELECT `opt`,`date_and_time` FROM `forgot_password_accounts` WHERE `email` = ? ORDER BY  `date_and_time` DESC LIMIT 1';

            // Prepare the SQL statement
            $fetchAccountOTPDataStmt = $dbConnection->prepare($fetchAccountOTPDataSql);

            // Bind parameters to the prepared statement
            $fetchAccountOTPDataStmt->bind_param('s',$fetchUserAssoc['email_address']);

            // Execute the prepared statement
            $fetchAccountOTPDataStmt->execute();

            // Retrieve the result set
            $fetchAccountOTPDataResult = $fetchAccountOTPDataStmt->get_result();

            // Fetch data as an associative array
            $fetchAccountOTPDataAssoc= $fetchAccountOTPDataResult->fetch_assoc();

            // If the passwords do not match, redirect to sign_in page with an error message
            if (!password_verify($loginPassword,$fetchAccountOTPDataAssoc['opt'])) {
                header('Location:sign_in?signInError=invalid_credential');
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
                } else {
                    // Redirect to the reset_password page
                    header('Location: reset_password?email='.$fetchUserAssoc['email_address'].'&token='.password_hash($fetchUserAssoc['email_address'].'#\@/#', PASSWORD_DEFAULT).'');
                    exit();
                }
            }
        }
    }
?>
<!-- Declare the document type as HTML -->
<!DOCTYPE html>
<!-- Specify the language of the document as English -->
<html lang="en">
    <!-- Head section of the HTML document -->
    <head>
        <title><?php echo $fetchCompanyDataAssoc['company_name']; ?> | Sign in</title>
        <?php require_once 'head.php'; ?>
    </head>
    <script>
        //JavaScript code to prevent the form from being resubmitted when the user refreshes the page.
        // if ( window.history.replaceState ){window.history.replaceState( null, null, window.location.href );}
    </script>
    <body class="bg-secondary">
        <?php require_once 'navbar.php'; ?>
        <div class="authentication_form">
            <div class="card p-0 col-sm-6 col-md-6 col-lg-4 col-xl-4">
                <div class="card-header bg-transparent text-center font-weight-bold">
                    Sign in<br>
                    <?php if (isset($_GET['signInError'])==true && $_GET['signInError']==='empty_user') { ?>
                        <h6 class="mt-1" style="color: #dc3545 !important;">Please enter your login id</h6>
                    <?php }elseif (isset($_GET['signInError'])==true && $_GET['signInError']==='empty_password') { ?>
                        <h6 class="mt-1" style="color: #dc3545 !important;">Please enter your password</h6>
                    <?php }elseif (isset($_GET['signInError'])==true && $_GET['signInError']==='invalid_credentials') { ?>
                        <h6 class="mt-1" style="color: #dc3545 !important;">Invalid login credentials<br>Please try again</h6>
                    <?php }elseif (isset($_GET['signInError'])==true && $_GET['signInError']==='acc') { ?>
                        <h6 class="mt-1" style="color: #007bff !important;">Your account has been suspended<br>Please contact customer care</h6>
                    <?php }elseif (isset($_GET['sts'])==true && $_GET['sts']==='rdy') { ?>
                        <h6 class="mt-1" style="color: #28a745 !important;">You're all set up<br>Sign in to continue</h6>
                    <?php }elseif (isset($_GET['sts'])==true && $_GET['sts']==='ctn') { ?>
                        <h6 class="mt-1" style="color: #28a745 !important;">Sign in to continue</h6>
                    <?php } ?>
                </div>
                <form action="" method="post" id="login-form" onsubmit="return signInJsValidation();">
                <!-- <form action="" method="post" id="login-form"> -->
                    <div class="card-body">
                        <div>
                            <label for="userIdentity" class="control-label">Email, phone or username<span class="text-danger">*</span></label>
                            <span id="userIdentityStatus" class="d-block"></span>
                            <div class="input-group mb-3">
                                <input type="text" name="userIdentity" id="userIdentity" placeholder="Email, phone or username" class="form-control" autofocus>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-user"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="loginPassword" class="control-label">Password <span class="text-danger">*</span></label>
                            <span id="loginPasswordStatus" class="d-block"></span>
                            <div class="input-group mb-3">
                                <input type="password" name="loginPassword" id="loginPassword" placeholder="Password" class="form-control">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-lock"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <button type="submit" name="sign_in" id="sign_in" class="btn btn-primary btn-block">Sign in</button>
                                <button type="button" name="sign_inLoader" id="sign_inLoader" class="d-none btn btn-primary btn-block">
                                    <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center font-weight-bold">
                        <div class="row">
                            <a class="col" style="color:#12487d !important;" href="sign_up">Don&apos;t have an account?</a>
                            <a class="col" style="color:#12487d !important;" href="forgot-password">I forgot my password</a>
                        </div>
                        <div class="mt-3 mb-3">
                            <a class="text-decotation-none" style="color:#12487d !important;" href="forgot_account">I forgot my account</a>
                        </div>
                        <h6 class="d-block text-muted mt-2">&copy; <?php echo date('Y'); ?> <?php echo $fetchCompanyDataAssoc['company_name'] ?></h6>
                    </div>
                </form>
                <script>
                    function signInJsValidation() {
                        // Set the value of is_input_valid to be true by default
                        var is_input_valid = true;

                        document.getElementById('sign_in').className = "d-none";
                        document.getElementById('sign_inLoader').className = "d-block btn btn-primary btn-block";
                        
                        // Check if the input fields are empty
                        if (document.getElementById('userIdentity').value === "" || document.getElementById('loginPassword').value === "") {
                            document.getElementById('sign_inLoader').className = "d-none";
                            document.getElementById('sign_in').className = "d-block btn btn-primary btn-block";
                        }

                        // If the value userIdentity is empty show the error message
                        if (document.getElementById('userIdentity').value === "") {
                            is_input_valid = false;
                            document.getElementById('userIdentity').style.border = "1px solid #dc3545";
                            document.getElementById('userIdentityStatus').style.color = "#dc3545";
                            document.getElementById('userIdentityStatus').innerHTML = "Please enter your email";
                        } else {
                            document.getElementById('userIdentity').style.border = "1px solid #28a745";
                            document.getElementById('userIdentityStatus').innerHTML = "";
                        }
                        
                        // If the value loginPassword is empty show the error message
                        if (document.getElementById('loginPassword').value === "") {
                            is_input_valid = false;
                            document.getElementById('loginPassword').style.border = "1px solid #dc3545";
                            document.getElementById('loginPasswordStatus').style.color = "#dc3545";
                            document.getElementById('loginPasswordStatus').innerHTML = "Please enter your password";
                        } else {
                            document.getElementById('loginPassword').style.border = "1px solid #28a745";
                            document.getElementById('loginPasswordStatus').innerHTML = "";
                        }
                        // Return the value of is_input_valid
                        return is_input_valid;
                    }
                </script>
            </div>
        </div>
        <?php require_once 'footer.php'; ?>
        <?php require_once 'foot.php'; ?>
        <script>
            document.onreadystatechange = function () {
                if (document.readyState === 'loading') {
                    // Page is still loading
                    console.log("Page is still loading");
                } else if (document.readyState === 'interactive') {
                    // DOM is fully parsed but resources are still loading
                    console.log("DOM is fully parsed but resources are still loading");
                } else if (document.readyState === 'complete') {
                    // Page and all resources are fully loaded
                    console.log("Page and all resources are fully loaded");
                }
            };
        </script>
    </body>
</html>
<?php $dbConnection->close(); // Close the database connection ?>