<?php
    // SQL statement with a parameter placeholder
    $fetchCurencySql = 'SELECT 	`one`,`many` FROM `currency_prefix` LIMIT 1';
    // Prepare the SQL statement
    $fetchCurencyStmt = $dbConnection->prepare($fetchCurencySql);
    // Execute the statement
    $fetchCurencyStmt->execute();
    // Retrieve the result set
    $fetchCurencyResult = $fetchCurencyStmt->get_result();
    // Fetch data
    $fetchCurencyAssoc= $fetchCurencyResult->fetch_assoc();
    // Close the $fetchCurencyStmt prepared statement
    $fetchCurencyStmt->close();
    // Set default timezone from the set region
    $currency_one = $fetchCurencyAssoc['one'];
    $currency_many = $fetchCurencyAssoc['many'];
?>