<?php
    $selectedCompanyRegion = '6';
    // SQL statement with a parameter placeholder
    $fetchCompanyRegionSql = 'SELECT `timezone_name` FROM `company_timezone` WHERE `selected_company_timezone`=? LIMIT 1';
    // Prepare the SQL statement
    $fetchCompanyRegionStmt = $dbConnection->prepare($fetchCompanyRegionSql);
    // Bind parameters
    $fetchCompanyRegionStmt->bind_param('s',$selectedCompanyRegion);
    // Execute the statement
    $fetchCompanyRegionStmt->execute();
    // Retrieve the result set
    $fetchCompanyRegionResult = $fetchCompanyRegionStmt->get_result();
    // Fetch data
    $fetchCompanyRegionAssoc= $fetchCompanyRegionResult->fetch_assoc();
    // Close the $fetchCompanyRegionStmt prepared statement
    $fetchCompanyRegionStmt->close();
    // Set default timezone from the set region
    $currentDateAndTime = date_default_timezone_set($fetchCompanyRegionAssoc['timezone_name']);
    $currentDateAndTime = date('Y-m-d H:i:s', time());
?>