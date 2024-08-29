<?php
    // Import PHPMailer namespace and classes
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';

    /* Require necessary files for the application to function properly. */
    //establishes a connection to the database.
    require 'database_connection.php';

    //contains company-related data.
    require 'company_data.php';

    //provides the current date and time.
    require 'current_date_and_time.php';

    // If the 'user_id' session variable is set and has a length greater than 0
    if (isset($_SESSION['user_id']) == true && strlen($_SESSION['user_id']) > 0) {
        // Redirect the user to the 'index' page and stop further execution
        header('Location: index');
        exit();
    }

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'submitEmailIdentity' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submitEmailIdentity']) == true) {
        // Sanitize and validate user input data to prevent SQL injection and cross-site scripting (XSS) attacks
        $email = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_POST['email']),ENT_QUOTES,'UTF-8')));

        // If the user identity is empty, redirect to forgot_account page with an error message
        if (empty($email) == true) {
            header('Location:forgot_account?status=empty_email');
            exit();
        }

        // Prepare a SQL query to fetch user data from the database
        $fetchUserSql = 'SELECT `email_address`,`username`,`user_phone_number` FROM `users` WHERE `email_address`=? LIMIT 1';

        // Prepare the SQL statement
        $fetchUserStmt = $dbConnection->prepare($fetchUserSql);

        // Bind parameters to the prepared statement
        $fetchUserStmt->bind_param('s',$email);

        // Execute the prepared statement
        $fetchUserStmt->execute();

        // Store the result set
        $fetchUserStmt->store_result();

        // If the user identity is found in the database, generate a random password and send it to their email
        if ($fetchUserStmt->num_rows === 1) {
            header('Location:forgot_account?q='.$email.'');
            exit();
        } else {
            header('Location:forgot_account?status=email_not_found');
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
        <title><?php echo $fetchCompanyDataAssoc['company_name']; ?> | Forgot account</title>
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
                <div class="card-header bg-transparent text-center font-weight-bold">
                    Forgot account<br>
                    <?php if (isset($_GET['status'])==true && $_GET['status'] === 'empty_user') { ?>
                        <h6 class="mt-1" style="color: #dc3545 !important;">Please enter your email</h6>
                    <?php }elseif (isset($_GET['status'])==true && $_GET['status'] === 'email_not_found') { ?>
                        <h6 class="mt-1" style="color: #dc3545 !important;">Email not found</h6>
                    <?php } ?>
                </div>
                <form action="" method="post" id="login-form" onsubmit="return forgotAccountJsValidation();">
                <!-- <form action="" method="post" id="login-form"> -->
                    <div class="card-body">
                        <div>
                            <label for="email" class="control-label">Email<span class="text-danger">*</span></label>
                            <span id="emailStatus" class="d-block"></span>
                            <div class="input-group mb-3">
                                <input type="text" name="email" id="email" placeholder="Email" class="form-control" autofocus>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-envelope"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <button type="submit" name="submitEmailIdentity" id="submitEmailIdentity" class="btn btn-primary btn-block">Submit</button>
                                <button type="button" name="submitEmailIdentityLoader" id="submitEmailIdentityLoader" class="d-none btn btn-primary btn-block">
                                    <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center font-weight-bold">
                        <div class="row">
                            <a class="col" style="color:#12487d !important;" href="sign_up">Don&apos;t have an account?</a>
                            <a class="col" style="color:#12487d !important;" href="sign_in">Log in to my account</a>
                        </div>
                        <div class="mt-3 mb-3">
                            <a class="text-decotation-none" style="color:#12487d !important;" href="forgot-password">I forgot my password</a>
                        </div>
                        <h6 class="d-block text-muted mt-2">&copy; <?php echo date('Y'); ?> <?php echo $fetchCompanyDataAssoc['company_name'] ?></h6>
                    </div>
                </form>
                <script>
                    function forgotAccountJsValidation() {
                        // Set the value of is_input_valid to be true by default
                        var is_input_valid = true;

                        document.getElementById('submitEmailIdentity').className = 'd-none';
                        document.getElementById('submitEmailIdentityLoader').className = 'd-block btn btn-primary btn-block';

                        // If the value email is empty show the error message
                        if (document.getElementById('email').value === '') {
                            is_input_valid = false;
                            document.getElementById('submitEmailIdentityLoader').className = 'd-none';
                            document.getElementById('submitEmailIdentity').className = 'd-block btn btn-primary btn-block';

                            document.getElementById('email').style.border = '1px solid #dc3545';
                            document.getElementById('emailStatus').style.color = '#dc3545';
                            document.getElementById('emailStatus').innerHTML = 'Please enter your email';
                        } else {
                            document.getElementById('email').style.border = '1px solid #28a745';
                            document.getElementById('emailStatus').innerHTML = '';
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