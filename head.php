<!-- Specify the character encoding of the document -->    
<meta charset="UTF-8">
<!-- Set the viewport settings for responsive design -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" type="image/png" href="Logo/Gelish Building Solutions sm.png" sizes="32x32">
<meta name="theme-color" content="<?php echo $fetchCompanyDataAssoc['theme_color']; ?>">
<!-- Link to the Bootstrap CSS file -->
<link rel="stylesheet" href="css/style.css">
<!-- Link to the Bootstrap CSS file -->
<link rel="stylesheet" href="bootstrap_4.5.3/css/bootstrap.css">
<!-- Link to the Font awesome CSS file -->
<link rel="stylesheet" href="fontawesome_free_5.15.4_web/css/all.css">
<script>
    //JavaScript code to prevent the form from being resubmitted when the user refreshes the page.
    if ( window.history.replaceState ){window.history.replaceState( null, null, window.location.href );}
</script>
<!-- Link to the Font awesome Javascript file -->
<script src="fontawesome_free_5.15.4_web/js/all.js"></script>
<script src="JavaScript/jquery/jquery.min.js"></script>
<script src="JavaScript/jquery-ui/jquery-ui.min.js"></script>
<!-- require 00_last_seen_status_update.php php file -->
<?php require '00_last_seen_status_update.php' ?>
<?php
  if (!empty($user_type) && !empty($system_access)) {
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
        header('Location: index');
        exit();
    }
    // If the user_type is not set to 9 or system_access is set to 7
    if ($user_type !== '9' || $system_access === '7') {
        // Redirect to the sign_out page if the condition is true
        header('Location: sign_out');
        exit();
    }
  }
?>