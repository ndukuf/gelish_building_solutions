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
    
    $user_id = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_SESSION['user_id']),ENT_QUOTES,'UTF-8')));

    // array of the allowed tasks in the account page
    $accountPageTasks = array('profile','change_password');

    if (!isset($_GET['tsk']) || !in_array($_GET['tsk'],$accountPageTasks)) {
        // Redirect to the index page
        header('Location: index');
        exit();
    }

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'changePassword' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['changePassword']) == true) {
        // Sanitize and validate user input data to prevent SQL injection and cross-site scripting (XSS) attacks
        $currentPassword = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_POST['currentPassword']),ENT_QUOTES,'UTF-8')));
        $newPassword = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_POST['newPassword']),ENT_QUOTES,'UTF-8')));
        $confirmNewPassword = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_POST['confirmNewPassword']),ENT_QUOTES,'UTF-8')));

        if (empty($currentPassword)) {
            // Redirect to the account?tsk=change_password page
            header('Location: account?tsk=change_password&status=1');
            exit();
        }

        if (empty($newPassword)) {
            // Redirect to the account?tsk=change_password page
            header('Location: account?tsk=change_password&status=2');
            exit();
        }

        if (empty($confirmNewPassword)) {
            // Redirect to the account?tsk=change_password page
            header('Location: account?tsk=change_password&status=3');
            exit();
        }

        if (strval($newPassword) !== strval($confirmNewPassword)) {
            // Redirect to the account?tsk=change_password page
            header('Location: account?tsk=change_password&status=4');
            exit();
        }

        // Prepare a SQL query to fetch user password from the database
        $fetchUserPasswordSql = 'SELECT `user_password` FROM `users` WHERE `user_id`=? LIMIT 1';
        // Prepare the SQL statement
        $fetchUserPasswordStmt = $dbConnection->prepare($fetchUserPasswordSql);
        // Bind parameters to the prepared statement
        $fetchUserPasswordStmt->bind_param('s',$user_id);
        // Execute the prepared statement
        $fetchUserPasswordStmt->execute();
        // Retrieve the result set
        $fetchUserPasswordResult = $fetchUserPasswordStmt->get_result();
        // Close the $fetchUserPasswordStmt prepared statement to free up resources
        $fetchUserPasswordStmt->close();
        // Fetch data as an associative array
        $fetchUserPasswordAssoc= $fetchUserPasswordResult->fetch_assoc();

        if (!password_verify($currentPassword,$fetchUserPasswordAssoc['user_password'])) {
            // Redirect to the account?tsk=change_password page
            header('Location: account?tsk=change_password&status=5');
            exit();
        } else {
            // Hash the current password using the default password hashing algorithm
            $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Prepare a SQL query to update user password in the database
            $updateUserPasswordSql = 'UPDATE `users` SET `user_password`=? WHERE `user_id`=?';
            // Prepare the SQL statement
            $updateUserPasswordStmt = $dbConnection->prepare($updateUserPasswordSql);
            // Bind parameters to the prepared statement
            $updateUserPasswordStmt->bind_param('ss',$hashedNewPassword,$user_id);
            // Execute the prepared statement
            if ($updateUserPasswordStmt->execute()) {
                // Close the $updateUserPasswordStmt prepared statement to free up resources
                $updateUserPasswordStmt->close();
                // Redirect to the account?tsk=change_password page
                header('Location: account?tsk=change_password&status=6');
                exit();
            } else {
                // Redirect to the account?tsk=change_password page
                header('Location: account?tsk=change_password&status=7');
                exit();
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $fetchCompanyDataAssoc['company_name']; ?> | Account center</title>
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
                <?php if (isset($_GET['tsk']) && $_GET['tsk'] =='profile') { ?>
                    <div class="container mx-auto col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                        <div class="card">
                            <div class="card-header text-center">
                                <strong title="<?php echo $user_first_name.' '.$user_last_name; ?>"><?php echo $user_first_name.' '.$user_last_name; ?></strong>
                            </div>
                            <div class="card-body">
                                <?php
                                    // Prepare a SQL query to fetch user data from the database
                                    $fetchUserDataSql = 'SELECT `user_first_name`,`user_middle_name`,`user_last_name`,`user_phone_number`,`email_address`,`username`,`user_avatar` FROM `users` WHERE `user_id`=? LIMIT 1';
                                    // Prepare the SQL statement
                                    $fetchUserDataStmt = $dbConnection->prepare($fetchUserDataSql);
                                    // Bind parameters to the prepared statement
                                    $fetchUserDataStmt->bind_param('s',$user_id);
                                    // Execute the prepared statement
                                    $fetchUserDataStmt->execute();
                                    // Retrieve the result set
                                    $fetchUserDataResult = $fetchUserDataStmt->get_result();
                                    // Close the $fetchUserDataStmt prepared statement to free up resources
                                    $fetchUserDataStmt->close();
                                    // Fetch data as an associative array
                                    $fetchUserDataAssoc= $fetchUserDataResult->fetch_assoc();
                                ?>
                                <form action="" method="post">
                                    <div class="row">
                                        <div class="mb-2 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                            <label for="firstName" class="control-label">First name<span class="text-danger">*</span></label>
                                            <span id="firstNameStatus" class="d-block"></span>
                                            <input type="text" name="firstName" id="firstName" placeholder="First name" class="form-control" autocomplete="off" autofocus>
                                        </div>
                                        <div class="mb-2 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                            <label for="lastName" class="control-label">Last name<span class="text-danger">*</span></label>
                                            <span id="lastNameStatus" class="d-block"></span>
                                            <input type="text" name="lastName" id="lastName" placeholder="Last name" class="form-control" autocomplete="off">
                                        </div>
                                    </div>
                                    <div>
                                        <label for="username" class="control-label">Username<span class="text-danger">*</span></label>
                                        <span id="usernameStatus" class="d-block"></span>
                                        <div class="input-group mb-3">
                                            <input type="text" name="username" id="username" placeholder="Username" class="form-control" autocomplete="off">
                                            
                                        </div>
                                    </div>
                                    <div>
                                        <label for="email" class="control-label">Email<span class="text-danger">*</span></label>
                                        <span id="emailStatus" class="d-block"></span>
                                        <div class="input-group mb-3">
                                            <input type="text" name="email" id="email" placeholder="Email" class="form-control" autocomplete="off">
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-envelope"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="phone" class="control-label">Phone number<span class="text-danger">*</span></label>
                                        <span id="phoneStatus" class="d-block"></span>
                                        <div class="input-group mb-3">
                                            <input type="tel" name="phone" id="phone" placeholder="Phone number" class="form-control" autocomplete="off">
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-phone"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <button type="submit" name="updateProfilebtn" id="updateProfilebtn" class="btn btn-primary btn-block">Save</button>
                                            <span  name="updateProfileloader" id="updateProfileloader" class="d-none btn btn-primary btn-block">
                                                <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                            </span>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php } elseif (isset($_GET['tsk']) && $_GET['tsk'] =='change_password') { ?>
                    <div class="container mx-auto col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                        <div class="card">
                            <form action="" method="post" onsubmit="return changePasswordJsValidation();">
                                <div class="card-header text-center">
                                    <strong>Change password</strong>
                                    <div name="statusDivision" id="statusDivision">
                                        <?php if (isset($_GET['status']) && strval($_GET['status']) === '1') { ?>
                                            <h6 class="mt-1" style="color: #dc3545 !important;">Please enter your current password</h6>
                                        <?php } elseif (isset($_GET['status']) && strval($_GET['status']) === '2') { ?>
                                            <h6 class="mt-1" style="color: #dc3545 !important;">Please enter your new password</h6>
                                        <?php } elseif (isset($_GET['status']) && strval($_GET['status']) === '3') { ?>
                                            <h6 class="mt-1" style="color: #dc3545 !important;">Please confirm your new password</h6>
                                        <?php } elseif (isset($_GET['status']) && strval($_GET['status']) === '4') { ?>
                                            <h6 class="mt-1" style="color: #dc3545 !important;">Please ensure your new passwords match</h6>
                                        <?php } elseif (isset($_GET['status']) && strval($_GET['status']) === '5') { ?>
                                            <h6 class="mt-1" style="color: #dc3545 !important;">Invalid current password</h6>
                                        <?php } elseif (isset($_GET['status']) && strval($_GET['status']) === '6') { ?>
                                            <h6 class="mt-1" style="color: #28a745 !important;">Password changed successfully</h6>
                                        <?php } elseif (isset($_GET['status']) && strval($_GET['status']) === '7') { ?>
                                            <h6 class="mt-1" style="color: #dc3545 !important;">There was an status updating your password!<br>Please try again</h6>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div>
                                    <div class="card-body">
                                        <label for="currentPassword" class="control-label">Current password<span class="text-danger">*</span></label>
                                        <span id="currentPasswordStatus" class="d-block"></span>
                                        <div class="input-group mb-3">
                                            <input type="password" name="currentPassword" id="currentPassword" placeholder="Current password" class="form-control" autocomplete="new-password" autofocus>
                                        </div>

                                        <label for="newPassword" class="control-label">New password<span class="text-danger">*</span></label>
                                        <span id="newPasswordStatus" class="d-block"></span>
                                        <div class="input-group mb-3">
                                            <input type="password" name="newPassword" id="newPassword" placeholder="New password" class="form-control" autocomplete="new-password">
                                        </div>

                                        <label for="confirmNewPassword" class="control-label">Confirm new password<span class="text-danger">*</span></label>
                                        <span id="confirmNewPasswordStatus" class="d-block"></span>
                                        <div class="input-group mb-3">
                                            <input type="password" name="confirmNewPassword" id="confirmNewPassword" placeholder="Confirm new password " class="form-control" autocomplete="new-password">
                                        </div>

                                        <div class="row">
                                            <div class="col">
                                                <button type="submit" name="changePassword" id="changePassword" class="d-block btn btn-primary btn-block">Change password</button>
                                                <span name="changePasswordLoader" id="changePasswordLoader" class="d-none btn btn-primary btn-block">
                                                    <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <script>
                                function changePasswordJsValidation() {
                                    // Set the value of is_input_valid to be true by default
                                    var is_input_valid = true;

                                    document.getElementById('changePassword').className = "d-none";
                                    document.getElementById('changePasswordLoader').className = "d-block btn btn-primary btn-block";

                                    // If the value currentPassword is empty show the status message
                                    if (document.getElementById('currentPassword').value === "") {
                                        is_input_valid = false;

                                        document.getElementById('changePasswordLoader').className = "d-none";
                                        document.getElementById('changePassword').className = "d-block btn btn-primary btn-block";

                                        document.getElementById('currentPassword').focus();
                                        document.getElementById('currentPassword').style.border = "1px solid #dc3545";
                                        document.getElementById('currentPasswordStatus').style.color = "#dc3545";
                                        document.getElementById('currentPasswordStatus').innerHTML = "Please enter your current password";
                                    } else {
                                        document.getElementById('currentPassword').style.border = "1px solid #28a745";
                                        document.getElementById('currentPasswordStatus').innerHTML = "";

                                        // If the value newPassword is empty show the status message
                                        if (document.getElementById('newPassword').value === "") {
                                            is_input_valid = false;

                                            document.getElementById('changePasswordLoader').className = "d-none";
                                            document.getElementById('changePassword').className = "d-block btn btn-primary btn-block";

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

                                                document.getElementById('changePasswordLoader').className = "d-none";
                                                document.getElementById('changePassword').className = "d-block btn btn-primary btn-block";

                                                document.getElementById('confirmNewPassword').focus();
                                                document.getElementById('confirmNewPassword').style.border = "1px solid #dc3545";
                                                document.getElementById('confirmNewPasswordStatus').style.color = "#dc3545";
                                                document.getElementById('confirmNewPasswordStatus').innerHTML = "Please enter your new password";
                                            } else {
                                                if (String(document.getElementById('newPassword').value) !== String(document.getElementById('confirmNewPassword').value)) {
                                                    is_input_valid = false;

                                                    document.getElementById('changePasswordLoader').className = "d-none";
                                                    document.getElementById('changePassword').className = "d-block btn btn-primary btn-block";

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
                                    }

                                    // Return the value of is_input_valid
                                    return is_input_valid;
                                }
                            </script>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <!-- require mainfooter.php php file -->
            <?php require 'mainfooter.php'; ?>
        </div>
        <!-- require footer.php php file -->
        <?php require 'footer.php'; ?>
    </body>
</html>
<?php $dbConnection->close(); // Close the database connection ?>