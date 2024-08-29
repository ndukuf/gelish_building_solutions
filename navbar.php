<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <!-- <a class="navbar-brand" href="#">Navbar</a> -->
    <a class="navbar-brand" href="index">
        <!-- link to the brand logo -->
        <img src="<?php echo $fetchCompanyDataAssoc['company_logo']; ?>" width="100%" height="30" class="d-inline-block align-top" alt="<?php echo $fetchCompanyDataAssoc['company_name']; ?>" loading="lazy">
        <!-- image of the brand logo with lazy loading -->
        <!-- Gelish Building Solutions -->
        <!-- brand name text -->
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarMenu">
        <div class="navbar-nav">
            <a class="nav-link text-light" href="index">Home</a>
            <?php if (isset($_SESSION['user_id']) == true && strlen($_SESSION['user_id']) > 0) { ?>
                <?php
                    // Prepare a SQL statement to fetch the sum of all quantities (total items) from the 'cart' table
                    // where the user_id matches the current session user_id
                    $countItemsInCartSql = 'SELECT SUM(`quantity`) AS `totalCartItemsCount` FROM `cart` WHERE `user_id` =?';

                    // Prepare the SQL statement for execution using the database connection
                    $countItemsInCartStmt = $dbConnection->prepare($countItemsInCartSql);

                    // Bind the user ID to the prepared statement as a string data type
                    $countItemsInCartStmt->bind_param('s', $_SESSION['user_id']);

                    // Execute the prepared SQL statement
                    $countItemsInCartStmt->execute();

                    // Fetch the result set from the executed SQL statement
                    $countItemsInCartResult = $countItemsInCartStmt->get_result();

                    // Close the prepared statement to free up resources
                    $countItemsInCartStmt->close();

                    // Fetch data as an associative array
                    $countItemsInCartStmtAssoc= $countItemsInCartResult->fetch_assoc();
                ?>
                <a class="nav-link text-light" href="cart">
                    <i class="fas fa-shopping-cart fa-flip-horizontal"></i>
                    <span class="badge badge-secondary">
                        <?php if (empty($countItemsInCartStmtAssoc['totalCartItemsCount'])==true) { ?>
                            0
                        <?php } else { ?>
                            <?php echo $countItemsInCartStmtAssoc['totalCartItemsCount']; ?>
                        <?php } ?>
                    </span>
                </a>
                <a class="nav-link text-light" href="sign_out">Sign out</a>
            <?php } else { ?>
                <a class="nav-link text-light" href="sign_in">Sign in</a>
            <?php } ?>
        </div>
    </div>
</nav>