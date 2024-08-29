<?php
  // Prepare a SQL query to fetch user data from the database
  $fetchUserTypeSql = 'SELECT `user_type`,`system_access` FROM `users` WHERE `user_id`=? LIMIT 1';

  // Prepare the SQL statement
  $fetchUserTypeStmt = $dbConnection->prepare($fetchUserTypeSql);

  // Bind parameters to the prepared statement
  $fetchUserTypeStmt->bind_param('s',$_SESSION['user_id']);

  // Execute the prepared statement
  $fetchUserTypeStmt->execute();

  // Retrieve the result set
  $fetchUserTypeResult = $fetchUserTypeStmt->get_result();

  // Fetch data as an associative array
  $fetchUserTypeAssoc= $fetchUserTypeResult->fetch_assoc();

  // The user's user_type
  $user_type = strval($fetchUserTypeAssoc['user_type']);

  // The user's system_access
  $system_access = strval($fetchUserTypeAssoc['system_access']);

  // Check if the user_id session is not set or is empty
  if (isset($_SESSION['user_id']) == false && strlen($_SESSION['user_id']) <= 0) {
    // Redirect to the index page if the condition is true
    header('Location:../index');
    exit();
  }
  // If the user_type is not set to 9 or system_access is set to 7
  if ($user_type !== '9' || $system_access === '7') {
    // Redirect to the sign_out page if the condition is true
    header('Location:../sign_out');
    exit();
  }
?>