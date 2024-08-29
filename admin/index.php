<?php
    /* Require necessary files for the application to function properly. */
    //establishes a connection to the database.
    require '../database_connection.php';

    //contains company-related data.
    require '../company_data.php';

    //provides the current date and time.
    require '../current_date_and_time.php';

    //connect to 00_validate_admin.
    require '00_validate_admin.php';
?>
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title><?php echo $fetchCompanyDataAssoc['company_name']; ?> | Home</title>
                <!-- require header.php php file -->
                <?php require 'header.php'; ?>
            </head>
            <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
                <div class="wrapper">
                    <!-- require topbar.php php file -->
                    <?php require 'topbar.php'; ?>
                    <!-- require sidebar.php php file -->
                    <?php require 'sidebar.php'; ?>

                    <div class="content-wrapper mb-4">
                        <div class="content-header">
                            <div class="container-fluid">
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <h1 class="m-0"><?php echo $fetchCompanyDataAssoc['company_name']; ?></h1>
                                    </div>
                                </div>
                                <hr class="border-primary">
                            </div>
                        </div>
                        <div class="ml-4 font-italic font-weight-bold">
                            <span id="greeting">
                                <span class="spinner-border text-primary" style="width:20px; height:20px;"></span>
                            </span>
                        </div>
                        <script language="javascript" type="text/javascript">
                            var timerID = null;
                            var timerRunning = false;
                            function stopclock()
                            {
                                if(timerRunning)
                                clearTimeout(timerID);
                                timerRunning = false;
                            }
                            function showtime()
                            {
                                // Set the desired timezone
                                var timeZone = '<?php echo $fetchCompanyTimezoneAssoc['timezone_name']; ?>';
                                // Get the current time in the specified timezone
                                var currentTime = new Date().toLocaleTimeString('en-UK',{timeZone});
                                // Extract hour, minute, and second from the current time
                                var [hour, minute, second] = currentTime.split(/:| /);
                                // console.log(`Current time in ${timeZone}: ${hour}:${minute}:${second}`);
                                var currentTime = hour+':'+minute+':'+second;
                                
                                //“Good morning” is generally used from 5:00 a.m. to 11:59 p.m.
                                if (currentTime>="05:00:00" && currentTime<="11:59:59"){
                                    Greetings = "Good morning"+" <?php echo $user_first_name; ?>"+"";
                                }
                                //“Hello” time is from 12:00 p.m
                                if (currentTime>="12:00:00"){
                                    Greetings = "Hello"+" <?php echo $user_first_name; ?>"+"";
                                }
                                //“Good afternoon” time is from 12:00 p.m. to 6:00 p.m.
                                if (currentTime>="12:01:00" && currentTime<="18:00:00"){
                                    Greetings = "Good afternoon"+" <?php echo $user_first_name; ?>"+"";
                                }
                                //“Good evening” is often used after 6:01 p.m or when the sun goes down.
                                if (currentTime>="18:01:00"){
                                    Greetings = "Good evening"+" <?php echo $user_first_name; ?>"+"";
                                }

                                document.getElementById('liveTime').innerHTML = currentTime;
                                document.getElementById('greeting').innerHTML = Greetings;
                                timerID = setTimeout("showtime()",1000);
                                timerRunning = true;
                            }
                            function startTimezoneClock()
                            {
                                stopclock();
                                showtime();
                            }
                            window.onload=startTimezoneClock;
                        </script>
                        <!-- Main content -->
                        <section class="content">
                            <div class="container-fluid">
                                <div class="row">
                                    <a href="staff?tsk=view_staff" class="col-12 col-sm-6 col-md-4">
                                        <div class="small-box bg-light shadow border">    
                                            <div class="inner">
                                                <h3>Staff</h3>
                                                <p class="font-weight-bold" style="font-size: 20px;">
                                                    <?php
                                                        // Prepare a SQL statement to fetch user IDs from the 'users' table
                                                        $fetchUserSql = 'SELECT `user_id` FROM `users`';

                                                        // Prepare the SQL statement for execution
                                                        $fetchUserStmt = $dbConnection->prepare($fetchUserSql);

                                                        // Execute the prepared SQL statement
                                                        $fetchUserStmt->execute();

                                                        // Fetch the result set from the executed SQL statement
                                                        $fetchUserResult = $fetchUserStmt->get_result();

                                                        // Close the prepared statement to free up resources
                                                        $fetchUserStmt->close();

                                                        // Print the number of rows returned by the SQL query
                                                        echo $fetchUserResult->num_rows;
                                                    ?>
                                                </p>
                                            </div>
                                            <div class="icon">
                                                <i class="nav-icon fas fa-user-friends"></i>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="suppliers?tsk=view_suppliers" class="col-12 col-sm-6 col-md-4">
                                        <div class="small-box bg-light shadow border">    
                                            <div class="inner">
                                                <h3>Suppliers</h3>
                                                <p class="font-weight-bold" style="font-size: 20px;">
                                                    <?php
                                                        // Define a SQL query to fetch supplier IDs from the 'uppliers' table
                                                        $fetchSupplierSql = 'SELECT `supplier_id` FROM `suppliers`';

                                                        // Prepare a statement to execute the query using the database connection
                                                        $fetchSupplierStmt = $dbConnection->prepare($fetchSupplierSql);

                                                        // Execute the prepared statement
                                                        $fetchSupplierStmt->execute();

                                                        // Fetch the result of the query execution
                                                        $fetchSupplierResult = $fetchSupplierStmt->get_result();

                                                        // Close the prepared statement to free up resources
                                                        $fetchSupplierStmt->close();

                                                        // Print the number of rows returned by the query
                                                        echo $fetchSupplierResult->num_rows;
                                                    ?>
                                                </p>
                                            </div>
                                            <div class="icon">
                                                <i class="nav-icon fa-solid fa-truck-fast"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </section>
                    </div>

                    <!-- require mainfooter.php php file -->
                    <?php require 'mainfooter.php'; ?>
                </div>
                <!-- require footer.php php file -->
                <?php require 'footer.php'; ?>
            </body>
        </html>
<?php $dbConnection->close(); // Close the database connection ?>