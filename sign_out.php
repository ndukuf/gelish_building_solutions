<?php
    function signOutUser() {
        /* Require necessary files for the application to function properly. */
        //establishes a connection to the database.
        require 'database_connection.php';

        //contains company-related data.
        require 'company_data.php';

        //provides the current date and time.
        require 'current_date_and_time.php';
        // Check if the 'user_id' session variable is set and has a length greater than 0
        if (isset($_SESSION['user_id']) == true && strlen($_SESSION['user_id']) > 0) {
            // Update the user's last login information in the database
            $last_seen = $currentDateAndTime;
            $log_user_id = $_SESSION['user_id'];

            // SQL query to update the user's last login information
            $updateLogOutSql = 'UPDATE `users` SET `last_seen`=? WHERE `user_id`=?';

            // Prepare the SQL query for execution
            $updateLogOutStmt = $dbConnection->prepare($updateLogOutSql);

            // Bind the parameters to the SQL query
            $updateLogOutStmt->bind_param('ss', $last_seen, $log_user_id);

            // Execute the SQL query
            if ($updateLogOutStmt->execute()) {

                // Close the database connection to free up resources
                $dbConnection->close();

                // start session
                session_start();

                // unset session
                session_unset();

                // destroy session
                session_destroy();  

                // Redirect the user to the index page if they are already logged in
                header('Location: index');

                // Stop further execution of the script to prevent any unintended actions
                exit();
            } else {
                // Close the database connection to free up resources
                $dbConnection->close();

                // start session
                session_start();

                // unset session
                session_unset();

                // destroy session
                session_destroy();  

                // Redirect the user to the index page if they are already logged in
                header('Location: index');
                // Stop further execution of the script to prevent any unintended actions
                exit();
            }
        } else {
            // Close the database connection to free up resources
            $dbConnection->close();

            // start session
            session_start();

            // unset session
            session_unset();

            // destroy session
            session_destroy();  

            // Redirect the user to the index page if they are already logged in
            header('Location: index');
            // Stop further execution of the script to prevent any unintended actions
            exit();
        }
    }
    signOutUser();
?>