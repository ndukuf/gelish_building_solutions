<?php
    /* Require necessary files for the application to function properly. */
    //establishes a connection to the database.
    require 'database_connection.php';

    //contains company-related data.
    require 'company_data.php';

    //provides the current date and time.
    require 'current_date_and_time.php';

    // Check if the 'user_id' session variable is not set or is empty
    if (isset($_SESSION['user_id']) == false || strlen($_SESSION['user_id']) < 1) {
        // If the condition is true, redirect the user to the sign_in page
        header('Location: sign_in');
        // Exit the script to prevent further execution
        exit();
    }

    echo $_SESSION['user_id'];
?>