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

    $user_id = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_SESSION['user_id']),ENT_QUOTES,'UTF-8')));

    // If the 'user_id' session variable is set and has a length greater than 0
    if (isset($user_id) == true && strlen($user_id) > 0) {
        // Redirect the user to the 'index' page and stop further execution
        header('Location: index');
        exit();
    }

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'submitEmail' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submitEmail']) == true) {
        // Sanitize and validate user input data to prevent SQL injection and cross-site scripting (XSS) attacks
        $email = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_POST['email']),ENT_QUOTES,'UTF-8')));

        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        // If the user identity is empty, redirect to forgot-password page with an error message
        if (empty($email) == true) {
            header('Location:forgot-password?status=empty_email');
            exit();
        }

        // If the email is invalid, redirect to forgot-password page with an error message
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location:forgot-password?status=invalid_email');
            exit();
        }

        // Prepare a SQL query to fetch user data from the database
        $fetchUserSql = 'SELECT `email_address` FROM `users` WHERE `email_address`=? LIMIT 1';

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
            // Generate a 4 character password
            $randomPassword = strtoupper(bin2hex(random_bytes(2)));
            
            // Prepare a SQL query to fetch user data from the database
            $fetchOTPSql = 'SELECT `opt` FROM `forgot_password_accounts`';

            // Prepare the SQL statement
            $fetchOTPStmt = $dbConnection->prepare($fetchOTPSql);

            // Execute the prepared statement
            $fetchOTPStmt->execute();

            // Retrieve the result set
            $fetchOTPStmtResult = $fetchOTPStmt->get_result();

            // Fetch data as an associative array
            $fetchOTPStmtAssoc= $fetchOTPStmtResult->fetch_assoc();

            // Check if the user does not exist, redirect to sign_in page with an error message if not
            if ($fetchOTPStmtResult->num_rows === 0) {
                $password = $randomPassword;
            } else {
                while (password_verify($password,$fetchOTPStmtAssoc['opt'])) {
                    // Generate a 4 character password
                    $randomPassword = strtoupper(bin2hex(random_bytes(2)));
                }
            }

            // Encrypt the $randomPassword variable
            $opt = password_hash($randomPassword, PASSWORD_DEFAULT);
            // Encrypt the $email variable
            $token = password_hash($email.'#\@/#', PASSWORD_DEFAULT);
            // Change the number format of the cuttent date
            $date_and_time = date('Y-m-d H:i:s', strtotime($currentDateAndTime));

            // Prepare a SQL query to update user password
            $insertOTPSql = 'INSERT INTO `forgot_password_accounts`(`email`,`opt`,`date_and_time`,`token`) VALUES (?,?,?,?)';

            // Prepare the SQL statement
            $insertOTPStmt = $dbConnection->prepare($insertOTPSql);

            // Bind parameters to the prepared statement
            $insertOTPStmt->bind_param('ssss',$email,$opt,$date_and_time,$token);

            // If the $insertOTPStmt prepared statement is executed successfully
            if ($insertOTPStmt->execute()) {
                try {
                    //Send using SMTP
                    $mail->isSMTP();
                    //Enable SMTP authentication
                    $mail->SMTPAuth = true;
                    
                    //Set the SMTP server to send through
                    $mail->Host = 'smtp.gmail.com';
                    //SMTP username
                    $mail->Username = 'kiokofaith252@gmail.com';
                    //SMTP password
                    $mail->Password = 'lodftbgnkvxneruk';
    
                    //ENCRYPTION_SMTPS 465 - Enable implicit TLS encryption
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
                    $mail->Port = 587;
    
                    //Email sender
                    $mail->setFrom('kiokofaith252@gmail.com', 'Gelish Building Solutions');
                    //Email recipient
                    $mail->addAddress($email);
                    //Set email format to HTML
                    $mail->isHTML(true);
    
                    // Subject of the email
                    $mail->Subject = 'Password reset';
    
                    // Body of the email
                    $bodyContent = '<div> Your OTP is '.$randomPassword.'<br>It is valid for <strong>15 minutes</strong></div>';
    
                    // Declare the body of the email
                    $mail->Body = $bodyContent;
    
                    if (!$mail->send()) {
                        $_SESSION['forgotPasswordTitle'] = 'Error';
                        $_SESSION['forgotPasswordText'] = 'There was an error sending the email - Try again';
                        $_SESSION['forgotPasswordIcon'] = 'error';
                        header('Location:forgot-password?sts=not_sent_00');
                        exit();
                    } else {
                        $system_access = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim('7'),ENT_QUOTES,'UTF-8')));

                        // Prepare a SQL query to update user password
                        $updateSystemAccessSql = 'UPDATE `users` SET `system_access` = ? WHERE `email_address` = ?';

                        // Prepare the SQL statement
                        $updateSystemAccessStmt = $dbConnection->prepare($updateSystemAccessSql);

                        // Bind parameters to the prepared statement
                        $updateSystemAccessStmt->bind_param('ss',$system_access,$email);
                        // If the $updateSystemAccessStmt prepared statement is ececuted successfully
                        if (!$updateSystemAccessStmt->execute()) {
                            $_SESSION['forgotPasswordTitle'] = 'Error';
                            $_SESSION['forgotPasswordText'] = 'There was a system error - Try again';
                            $_SESSION['forgotPasswordIcon'] = 'error';
                            header('Location:forgot-password?status=try_again');
                            exit();
                        } else {
                            $_SESSION['forgotPasswordTitle'] = 'Success';
                            $_SESSION['forgotPasswordText'] = 'Check your email';
                            $_SESSION['forgotPasswordIcon'] = 'success';
                            header('Location:forgot-password?sts=email_sent');
                            exit();
                        }
                    }
                } catch (Exception $e) {
                    $_SESSION['forgotPasswordTitle'] = 'Error';
                    $_SESSION['forgotPasswordText'] = 'There was an error sending the email - Try again';
                    $_SESSION['forgotPasswordIcon'] = 'error';
                    $_SESSION['status'] = 'Gelish Building Solutions '.date('Y').'';
                    header('Location:forgot-password?sts=not_sent_01');
                    exit();
                }
            } else {
                $_SESSION['forgotPasswordTitle'] = 'Error';
                $_SESSION['forgotPasswordText'] = 'There was an error sending the email - Try again';
                $_SESSION['forgotPasswordIcon'] = 'error';
                header('Location:forgot-password?status=try_again');
                exit();
            }
        } else {
            $_SESSION['forgotPasswordTitle'] = 'Success';
            $_SESSION['forgotPasswordText'] = 'Check your email';
            $_SESSION['forgotPasswordIcon'] = 'success';
            header('Location:forgot-password?sts=no_email');
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
        <title><?php echo $fetchCompanyDataAssoc['company_name']; ?> | Forgot password</title>
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
                    Forgot password<br>
                    <?php if (isset($_GET['status'])==true && $_GET['status'] === 'empty_email') { ?>
                        <h6 class="mt-1" style="color: #dc3545 !important;">Please enter your login id</h6>
                    <?php }elseif (isset($_GET['status'])==true && $_GET['status'] === 'invalid_email') { ?>
                        <h6 class="mt-1" style="color: #dc3545 !important;">Please enter your password</h6>
                    <?php } ?>
                </div>
                <form action="" method="post" id="login-form" onsubmit="return forgotPasswordJsValidation();">
                    <div class="card-body">
                        <div>
                            <label for="email" class="control-label">Email<span class="text-danger">*</span></label>
                            <span id="emailStatus" class="d-block"></span>
                            <div class="input-group mb-3">
                                <input type="email" name="email" id="email" placeholder="Email" class="form-control" autofocus autocomplete="on">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-envelope"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <button type="submit" name="submitEmail" id="submitEmail" class="btn btn-primary btn-block">Submit</button>
                                <button type="button" name="submitEmailLoader" id="submitEmailLoader" class="d-none btn btn-primary btn-block">
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
                            <a class="text-decotation-none" style="color:#12487d !important;" href="forgot_account">I forgot my account</a>
                        </div>
                        <h6 class="d-block text-muted mt-2">&copy; <?php echo date('Y'); ?> <?php echo $fetchCompanyDataAssoc['company_name'] ?></h6>
                    </div>
                </form>
                <script>
                    function forgotPasswordJsValidation() {
                        // Set the value of is_input_valid to be true by default
                        var is_input_valid = true;

                        document.getElementById('submitEmail').className = 'd-none';
                        document.getElementById('submitEmailLoader').className = 'd-block btn btn-primary btn-block';

                        // If the value email is empty show the error message
                        if (document.getElementById('email').value === '') {
                            is_input_valid = false;
                            document.getElementById('submitEmailLoader').className = 'd-none';
                            document.getElementById('submitEmail').className = 'd-block btn btn-primary btn-block';

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
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
           var forgotPasswordTitle = "<?php if (empty($_SESSION['forgotPasswordTitle'])) {echo $_SESSION['forgotPasswordTitle'] = '';} else {echo $_SESSION['forgotPasswordTitle'];} ?>";
           var forgotPasswordText = "<?php if (empty($_SESSION['forgotPasswordText'])) {echo $_SESSION['forgotPasswordText'] = '';} else {echo $_SESSION['forgotPasswordText'];} ?>";
           var forgotPasswordIcon = "<?php if (empty($_SESSION['forgotPasswordIcon'])) {echo $_SESSION['forgotPasswordIcon'] = '';} else {echo $_SESSION['forgotPasswordIcon'];} ?>";

           if (forgotPasswordTitle != '' && forgotPasswordText != '' && forgotPasswordIcon != '') {
                Swal.fire({title: forgotPasswordTitle,text: forgotPasswordText,icon: forgotPasswordIcon});
           }

           <?php unset($_SESSION['forgotPasswordTitle']); unset($_SESSION['forgotPasswordText']); unset($_SESSION['forgotPasswordIcon']); ?>
        </script>
    </body>
</html>
<?php $dbConnection->close(); // Close the database connection ?>