<?php
    $emptyValue = '';
    // SQL statement with a parameter placeholder
    $fetchCompanyDataSql = 'SELECT `company_name`,`company_logo`,`theme_color`,`system_version` FROM `company_details` WHERE `company_name`!=? AND `company_logo`!=? AND `company_address`!=? LIMIT 1';
    // Prepare the SQL statement
    $fetchCompanyDataStmt = $dbConnection->prepare($fetchCompanyDataSql);
    // Bind parameters
    $fetchCompanyDataStmt->bind_param('sss',$emptyValue,$emptyValue,$emptyValue);
    // Execute the statement
    $fetchCompanyDataStmt->execute();
    // Retrieve the result set
    $fetchCompanyDataResult = $fetchCompanyDataStmt->get_result();
    // Fetch data
    $fetchCompanyDataAssoc= $fetchCompanyDataResult->fetch_assoc();
    // Close the $fetchCompanyDataStmt prepared statement
    $fetchCompanyDataStmt->close();

    $setTimezone = '6';
    // SQL statement with a parameter placeholder
    $fetchCompanyTimezoneSql = 'SELECT `timezone_name` FROM `company_timezone` WHERE `selected_company_timezone`=? LIMIT 1';
    // Prepare the SQL statement
    $fetchCompanyTimezoneStmt = $dbConnection->prepare($fetchCompanyTimezoneSql);
    // Bind parameters
    $fetchCompanyTimezoneStmt->bind_param('s',$setTimezone);
    // Execute the statement
    $fetchCompanyTimezoneStmt->execute();
    // Retrieve the result set
    $fetchCompanyTimezoneResult = $fetchCompanyTimezoneStmt->get_result();
    // Fetch data
    $fetchCompanyTimezoneAssoc= $fetchCompanyTimezoneResult->fetch_assoc();
    // Close the $fetchCompanyTimezoneStmt prepared statement
    $fetchCompanyTimezoneStmt->close();
?>