<?php
    // Database host
    $servername = "localhost";    
    // Database username
    $username = "root";    
    // Database password
    $password = "";    
    // Database name
    $database = "gelish_building_solutions";
    
    // Create database connection
    $dbConnection = mysqli_connect($servername, $username, $password, $database);
    
    // Check database connection
    if ($dbConnection->connect_error){
        // Die and display the connection error if the connection fails
        die("Connection failed: ". $dbConnection->connect_error);
    }

    // start a new session
    session_start();
?>