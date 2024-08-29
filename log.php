// Verify the provided login password against the stored password for the fetched user
            if (password_verify($loginPassword,$fetchUserAssoc['user_password']) == true) {
                // If the passwords match, execute the code within this block

                // Check if the user has system access level 5
                if ($fetchUserAssoc['system_access']!== '5') {
                    // If the user does not have system access level 5, redirect them to the sign-in page with an error message indicating an issue with their account
                    header('Location:sign_in?signInError=acc');
                    exit();
                } else {
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
                }
            } else {
                // If the passwords do not match, redirect to sign_in page with an error message
                header('Location:sign_in?signInError=invalid_credentials');
                exit();
            }