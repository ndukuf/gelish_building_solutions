<?php
    // Check if the 'status' field is set and not empty in the $_POST array
    if(isset($_POST['status']) && !empty($_POST['status'])) {
        // Establish a connection to the database
        require 'database_connection.php';

        // Include company-related data
        require 'company_data.php';

        // Get the current date and time
        require 'current_date_and_time.php';

        // Check if the 'status' field is set to 'active'
        if($_POST['status']=="active") {
            // Set the online status to 'online'
            $online = 'online';
            // SQL query to update the 'last_seen' field in the 'users' table
            $onlineUpdateSql = "UPDATE `users` SET `last_seen`=? WHERE `user_id`=?";
            // Prepare the SQL statement
            $onlineUpdateStmt = $dbConnection->prepare($onlineUpdateSql);
            // Bind parameters to the prepared statement
            $onlineUpdateStmt->bind_param('ss', $online, $_SESSION['user_id']);
            // Execute the prepared statement
            $onlineUpdateStmt->execute();
        }
        // Check if the 'status' field is set to 'inactive'
        elseif($_POST['status']=="inactive") {
            // SQL query to update the 'last_seen' field in the 'users' table
            $offlineUpdateSql = "UPDATE `users` SET `last_seen`=? WHERE `user_id`=?";
            // Prepare the SQL statement
            $offlineUpdateStmt = $dbConnection->prepare($offlineUpdateSql);
            // Bind parameters to the prepared statement
            $offlineUpdateStmt->bind_param('ss', $currentDateAndTime, $_SESSION['user_id']);
            // Execute the prepared statement
            $offlineUpdateStmt->execute();
        }
    }
?>