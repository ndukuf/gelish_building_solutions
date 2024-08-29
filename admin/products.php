<?php
    /* Require necessary files for the application to function properly. */
    //establishes a connection to the database.
    require '../database_connection.php';

    //contains company-related data.
    require '../company_data.php';

    //provides the current date and time.
    require '../current_date_and_time.php';

    //provides the currency_prefix.
    require '../currency_prefix.php';

    //connect to 00_validate_admin.
    require '00_validate_admin.php';

    // Check if the 'tsk' variable is set in the GET request and if it is not empty
    if (isset($_GET['tsk']) == false || empty($_GET['tsk']) == true) {
        // If the 'tsk' variable is not set or is empty, redirect the user to the 'products' page with a 'view_products' query parameter
        header('Location: products?tsk=view_products');
        exit();
    }

    // Check if the task is 'edit_product' and if the 'id' variable is empty
    if ($_GET['tsk'] == 'edit_product' && empty($_GET['id']) == true) {
        // Redirect the user to the 'products' page with the 'view_products' task
        header('Location: products?tsk=view_products');
        // Exit the current script
        exit();
    }

    // Check if the task is 'edit_more_details' and if the 'id' variable is empty
    if ($_GET['tsk'] == 'edit_more_details' && empty($_GET['id']) == true) {
        // Redirect the user to the 'products' page with the 'view_products' task
        header('Location: products?tsk=view_products');
        // Exit the current script
        exit();
    }

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'add_product_btn' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['add_product_btn']) == true) {
        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the add_product_name input
        $add_product_name = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['add_product_name']), ENT_QUOTES, 'UTF-8')));

        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the add_product_description input
        $add_product_description = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['add_product_description']), ENT_QUOTES, 'UTF-8')));
        
        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the add_product_buying_price input
        $add_product_buying_price = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['add_product_buying_price']), ENT_QUOTES, 'UTF-8')));

        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the add_product_selling_price input
        $add_product_selling_price = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['add_product_selling_price']), ENT_QUOTES, 'UTF-8')));
        
        // Check if the product name is empty
        if (empty($add_product_name) == true) {
            // Redirect the user to the products page with an error message indicating an empty product name
            header('Location: products?tsk=add_product&addProductError=empty_product_name');
            exit();
        }

        // Check if the product description is empty
        if (empty($add_product_description) == true) {
            // Redirect the user to the products page with an error message indicating an empty product description
            header('Location: products?tsk=add_product&addProductError=empty_product_description');
            exit();
        }

        // Check if the product buying price is empty
        if (empty($add_product_buying_price) == true) {
            // Redirect the user to the products page with an error message indicating an empty product buying price
            header('Location: products?tsk=add_product&addProductError=empty_product_buying_price');
            exit();
        }

        // Check if the product selling price is empty
        if (empty($add_product_selling_price) == true) {
            // Redirect the user to the products page with an error message indicating an empty product selling price
            header('Location: products?tsk=add_product&addProductError=empty_product_selling_price');
            exit();
        }

        // Define a regular expression pattern to match buying prices (one or more digits, optionally followed by a decimal point and one or more digits)
        $buying_price_regex = '/^\d+(\.\d+)?$/';

        // Define a regular expression pattern to match selling prices (one or more digits, optionally followed by a decimal point and one or more digits)
        $selling_price_regex = '/^\d+(\.\d+)?$/';

        // Validate if the buying price matches the defined pattern, if not redirect to the products page with an error message
        if (!preg_match($buying_price_regex, $add_product_buying_price)) {
            // Redirect the user to the products page with an error message indicating an invalid product buying price
            header('Location: products?tsk=add_product&addProductError=invalid_product_buying_price');
            exit();
        }

        // Validate if the selling price matches the defined pattern, if not redirect to the products page with an error message
        if (!preg_match($selling_price_regex, $add_product_selling_price)) {
            // Redirect the user to the products page with an error message indicating an invalid product selling price
            header('Location: products?tsk=add_product&addProductError=invalid_product_selling_price');
            exit();
        }

        // Check if the product's selling price is less than the buying price
        if ($add_product_selling_price < $add_product_buying_price) {
            // Redirect the user to the products page with an error message indicating that the selling price should be higher than the buying price
            header('Location: products?tsk=add_product&addProductError=less_selling_price');
            exit();
        }

        // Prepare a SQL query to fetch user data from the database
        $fetchProductCodeSql = 'SELECT MAX(product_code) AS max_product_code FROM products';

        // Prepare the SQL statement
        $fetchProductCodeStmt = $dbConnection->prepare($fetchProductCodeSql);

        // Execute the prepared statement
        $fetchProductCodeStmt->execute();

        // Retrieve the result set
        $fetchProductCodeResult = $fetchProductCodeStmt->get_result();

        // Close the prepared statement to free up resources
        $fetchProductCodeStmt->close();

        // Fetch data as an associative array
        $fetchProductCodeAssoc= $fetchProductCodeResult->fetch_assoc();
        
        // Check if the 'max_product_code' key exists and is not empty in the $fetchProductCodeAssoc array
        if (empty($fetchProductCodeAssoc['max_product_code'])) {
            // If 'max_product_code' is empty, set the $product_code variable to '1000'
            $product_code = '1000';
        } else {
            // If 'max_product_code' is not empty, convert its value to an integer and increment it by 1
            $product_code =  intval($fetchProductCodeAssoc['max_product_code']) + 1;
        }

        // Convert the product name to uppercase
        $add_product_name = strtoupper($add_product_name);

        // Store the current date and time as the date the product was created
        $product_date_created = $currentDateAndTime;

        // Set the initial product count to '0.00', indicating that there are currently no products.
        $product_count = '0.00';

        // Initialize the product status with a value of '5', which could represent 'inactive' status
        $product_status = '5';

        // Define the SQL query to add a new product to the 'products' table
        $addNewProductsSql = 'INSERT INTO `products`(`product_code`,`product_name`,`product_description`,`product_buying_price`,`product_selling_price`,`product_date_created`,`product_count`,`product_status`) VALUES (?,?,?,?,?,?,?,?)';

        // Prepare the SQL query statement
        $addNewProductsStmt = $dbConnection->prepare($addNewProductsSql);

        // Bind the parameters to the prepared statement
        $addNewProductsStmt->bind_param('ssssssss', $product_code,$add_product_name,$add_product_description,$add_product_buying_price,$add_product_selling_price,$product_date_created,$product_count,$product_status);

        // Check if the execution of $addNewProductsStmt is successful
        if ($addNewProductsStmt->execute()) {
            // Close the statement to release resources
            $addNewProductsStmt->close();
            // Set a session variable to indicate that the product was added successfully
            $_SESSION['addProductStatus'] = 'productAdded';
            // Redirect to the products page with a task parameter set to 'add_product'
            header('Location: products?tsk=add_product');
            // Exit the script to prevent further execution
            exit();
        } else {
            // Close the statement to release resources
            $addNewProductsStmt->close();
            // Set a session variable to indicate that the product was not added
            $_SESSION['addProductStatus'] = 'productNotAdded';
            // Redirect to the products page with a task parameter set to 'add_product'
            header('Location: products?tsk=add_product');
            // Exit the script to prevent further execution
            exit();
        }
    }

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'edit_product_btn' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['edit_product_btn']) == true) {
        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the inputs
        $get_edit_product_code = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_GET['id']), ENT_QUOTES, 'UTF-8')));
        $edit_product_code = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['edit_product_code']), ENT_QUOTES, 'UTF-8')));

        // Check if the id from the GET parameter matches the one in the database
        if ($get_edit_product_code!== $edit_product_code) {
            // If the IDs do not match, redirect the user to the products page with an editProductError message
            header('Location: products?tsk=edit_product&id='.$get_edit_product_code.'&editProductError=productCodeNotMatch');
            exit();
        }

        // Prepare a SQL statement to fetch the initial details of a product from the database wher the product_code mathes the set product_code
        $fetchProductCodeInitialDetailsSql = 'SELECT `product_name`,`product_description`,`product_buying_price`,`product_selling_price`,`product_date_created`,`product_count`,`product_status` FROM `products` WHERE `product_code` =? LIMIT 1';

        // Prepare the SQL statement for execution
        $fetchProductCodeInitialDetailsStmt = $dbConnection->prepare($fetchProductCodeInitialDetailsSql);

        // Bind the product_code parameter to the prepared statement
        $fetchProductCodeInitialDetailsStmt->bind_param('s', $edit_product_code);

        // Execute the prepared statement
        $fetchProductCodeInitialDetailsStmt->execute();

        // Store the result of the execution
        $fetchProductCodeInitialDetailsResult = $fetchProductCodeInitialDetailsStmt->get_result();

        // Close the prepared statement to free up resources
        $fetchProductCodeInitialDetailsStmt->close();

        // Fetch the result as an associative array
        $fetchProductCodeInitialDetailsAssoc= $fetchProductCodeInitialDetailsResult->fetch_assoc();

        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the edit_product_status input
        $edit_product_status = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['edit_product_status']), ENT_QUOTES, 'UTF-8')));

        // Check if the edited product name is different from the initial product name and the edited product name is not empty
        if ($fetchProductCodeInitialDetailsAssoc['product_status']!== $edit_product_status && empty($edit_product_status)==false && $edit_product_status=='5' || $edit_product_status=='6') {
            // Prepare an SQL statement to update the product name in the 'products' table
            $updateProductNameSql = 'UPDATE `products` SET `product_status`=? WHERE `product_code`=? AND `product_status`=?';

            // Prepare a statement object using the database connection and the SQL statement
            $updateProductNameStmt = $dbConnection->prepare($updateProductNameSql);

            // Bind the parameters to the prepared statement
            $updateProductNameStmt->bind_param('sss', $edit_product_status,$edit_product_code,$fetchProductCodeInitialDetailsAssoc['product_status']);

            // Execute the prepared statement to perform the update
            $updateProductNameStmt->execute();
        }

        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the edit_product_name input
        $edit_product_name = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['edit_product_name']), ENT_QUOTES, 'UTF-8')));

        // Convert the edited product name to uppercase using the strtoupper function.
        $edit_product_name = strtoupper($edit_product_name);

        // Check if the edited product name is different from the initial product name and the edited product name is not empty
        if ($fetchProductCodeInitialDetailsAssoc['product_name']!== $edit_product_name && empty($edit_product_name)==false) {
            // Prepare an SQL statement to update the product name in the 'products' table
            $updateProductNameSql = 'UPDATE `products` SET `product_name`=? WHERE `product_code`=? AND `product_name`=?';

            // Prepare a statement object using the database connection and the SQL statement
            $updateProductNameStmt = $dbConnection->prepare($updateProductNameSql);

            // Bind the parameters to the prepared statement
            $updateProductNameStmt->bind_param('sss', $edit_product_name,$edit_product_code,$fetchProductCodeInitialDetailsAssoc['product_name']);

            // Execute the prepared statement to perform the update
            $updateProductNameStmt->execute();
        }

        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the edit_product_description input
        $edit_product_description = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['edit_product_description']), ENT_QUOTES, 'UTF-8')));

        // Check if the edited product's description differs from the initial product description and the edited product description is not empty
        if ($fetchProductCodeInitialDetailsAssoc['product_description']!== $edit_product_description && empty($edit_product_description)==false) {
            // Prepare an SQL statement to update the product description in the 'products' table
            $updateProductDescriptionSql = 'UPDATE `products` SET `product_description`=? WHERE `product_code`=? AND `product_description`=?';

            // Prepare a statement object using the database connection and the SQL statement
            $updateProductDescriptionStmt = $dbConnection->prepare($updateProductDescriptionSql);

            // Bind the parameters to the prepared statement
            $updateProductDescriptionStmt->bind_param('sss', $edit_product_description,$edit_product_code,$fetchProductCodeInitialDetailsAssoc['product_description']);

            // Execute the prepared statement to perform the update
            $updateProductDescriptionStmt->execute();
        }

        // Define a regular expression pattern to match edited buying prices (one or more digits, optionally followed by a decimal point and one or more digits)
        $edit_buying_price_regex = '/^\d+(\.\d+)?$/';

        // Define a regular expression pattern to match edited selling prices (one or more digits, optionally followed by a decimal point and one or more digits)
        $edit_selling_price_regex = '/^\d+(\.\d+)?$/';

        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the edit_product_buying_price input
        $edit_product_buying_price = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['edit_product_buying_price']), ENT_QUOTES, 'UTF-8')));

        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the edit_product_selling_price input
        $edit_product_selling_price = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['edit_product_selling_price']), ENT_QUOTES, 'UTF-8')));

        if (empty($edit_product_buying_price)==false && empty($edit_product_selling_price)==false) {
            if (preg_match($edit_buying_price_regex, $edit_product_buying_price)==true && preg_match($edit_selling_price_regex, $edit_product_selling_price)==true) {
                // Check if the buying price is less than or equal the selling price, to ensure a valid pricing condition
                if ($fetchProductCodeInitialDetailsAssoc['product_buying_price'] !== $edit_product_buying_price && $edit_product_buying_price <= $edit_product_selling_price) {
                    // Prepare an SQL statement to update the product buying price in the 'products' table
                    $updateProductBuyingPriceSql = 'UPDATE `products` SET `product_buying_price`=? WHERE `product_code`=? AND `product_buying_price`=?';
                    
                    // Prepare a statement object using the database connection and the SQL statement
                    $updateProductBuyingPriceStmt = $dbConnection->prepare($updateProductBuyingPriceSql);
                    
                    // Bind the parameters to the prepared statement, using the buying price, product code, and initial buying price
                    $updateProductBuyingPriceStmt->bind_param('sss', $edit_product_buying_price,$edit_product_code,$fetchProductCodeInitialDetailsAssoc['product_buying_price']);
                    
                    // Execute the prepared statement to perform the update
                    $updateProductBuyingPriceStmt->execute();
                }
                // Check if the selling price is greater than or equal the buying price, to ensure a valid pricing condition
                if ($fetchProductCodeInitialDetailsAssoc['product_selling_price'] !== $edit_product_selling_price && $edit_product_selling_price >= $edit_product_buying_price) {
                    // Prepare an SQL statement to update the product selling price in the 'products' table
                    $updateProductSellingPriceSql = 'UPDATE `products` SET `product_selling_price`=? WHERE `product_code`=? AND `product_selling_price`=?';
                    
                    // Prepare a statement object using the database connection and the SQL statement
                    $updateProductSellingPriceStmt = $dbConnection->prepare($updateProductSellingPriceSql);
                    
                    // Bind the parameters to the prepared statement, using the selling price, product code, and initial selling price
                    $updateProductSellingPriceStmt->bind_param('sss', $edit_product_selling_price,$edit_product_code,$fetchProductCodeInitialDetailsAssoc['product_selling_price']);
                    
                    // Execute the prepared statement to perform the update
                    $updateProductSellingPriceStmt->execute();
                }
            }
        }

        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the edit_product_count input
        $edit_product_count = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['edit_product_count']), ENT_QUOTES, 'UTF-8')));

        // Define a regular expression pattern to match edited edit_product_count (one or more digits, optionally followed by a decimal point and one or more digits)
        $edit_product_count_regex = '/^\d+(\.\d+)?$/';
        
        // Check if the product count from the initial details and the edited product count are different and matches the regular expression pattern.
        if ($fetchProductCodeInitialDetailsAssoc['product_count']!== $edit_product_count && preg_match($edit_product_count_regex, $edit_product_count)) {
            // Prepare an SQL statement to update the product count in the 'products' table
            $updateProductCountSql = 'UPDATE `products` SET `product_count`=? WHERE `product_code`=? AND `product_count`=?';

            // Prepare a statement object using the database connection and the SQL statement
            $updateProductCountStmt = $dbConnection->prepare($updateProductCountSql);

            // Bind the parameters to the prepared statement
            $updateProductCountStmt->bind_param('sss', $edit_product_count,$edit_product_code,$fetchProductCodeInitialDetailsAssoc['product_count']);

            // Execute the prepared statement to perform the update
            $updateProductCountStmt->execute();
        }

        // // Redirect to the products page with the edit_product task and the specific supplier ID
        // header('Location: products?tsk=edit_product&id='.$edit_product_code.'');
        // exit();
    }

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'submitPhoto' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submitPhoto']) == true) {
        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the inputs
        $get_edit_product_code = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_GET['id']), ENT_QUOTES, 'UTF-8')));
        $upload_photo_product_code = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['upload_photo_product_code']), ENT_QUOTES, 'UTF-8')));

        // Check if the id from the GET parameter matches the one in the database
        if ($get_edit_product_code!== $upload_photo_product_code) {
            // If the IDs do not match, redirect the user to the products page with an error message
            header('Location: products?tsk=edit_more_details&id='.$get_edit_product_code.'&uploadError=productCodeNotMatch');
            exit();
        }

        // Prepare a SQL statement to select the product code from the 'products' table where the email address matches the provided products
        $validateProductCodeSql = 'SELECT `product_code` FROM `products` WHERE `product_code` =? AND `product_code` =?';
        // Prepare the SQL statement
        $validateProductCodeStmt = $dbConnection->prepare($validateProductCodeSql);
        // Bind the products variable to the prepared statement as a string
        $validateProductCodeStmt->bind_param('ss',$get_edit_product_code,$upload_photo_product_code);
        // Execute the prepared statement
        $validateProductCodeStmt->execute();
        // Store the result of the executed statement
        $validateProductCodeStmt->store_result();
        // Check if the number of rows returned by the executed statement is not 1, indicating that the products does not exists in the 'products' table
        if ($validateProductCodeStmt->num_rows!==1) {
            header('Location: products?tsk=edit_more_details&id='.$get_edit_product_code.'&uploadError=productCodeNotMatch');
            exit();
        }

        // The $name variable stores the original name of the uploaded file as provided by the user.
        $name = $_FILES['imageUpload']['name'];

        // The $tmp_name variable stores the temporary file name of the uploaded file which is used for processing.
        $tmp_name = $_FILES['imageUpload']['tmp_name'];

        // The $size variable stores the size of the uploaded file in bytes.
        $size = $_FILES['imageUpload']['size'];

        // The $error variable stores the error code associated with the uploaded file, if any.
        $error = $_FILES['imageUpload']['error'];

        // The $type variable stores the MIME type of the uploaded file as determined by the browser.
        $type = $_FILES['imageUpload']['type'];

       // Split the file name by '.' to get the file extension
        $fileExtension = explode('.',$name);

        // Get the actual file extension and convert it to lowercase
        $fileActualExtension = strtolower(end($fileExtension));

        // Define the allowed file extensions
        $allowedExtensions = array('png','jpg','jpeg');

        // Check if the file's actual extension is in the allowed extensions array
        // If not, the function in_array() will return false and the code block will be executed
        if (in_array($fileActualExtension, $allowedExtensions) == false) {
            // Redirect the user to the products page with an error message indicating that the file extension is not allowed
            header('Location: products?tsk=edit_more_details&id='.$get_edit_product_code.'&uploadError=invalidExtension');
            exit();
        }

        // Check if there was an error during the file upload
        // If the variable $error is not equal to 0, it means that an error occurred
        if ($error!== 0) {
            // Redirect the user to the products page with an error message indicating that there was an error uploading the file
            header('Location: products?tsk=edit_more_details&id='.$get_edit_product_code.'&uploadError=errorUploading');
            exit();
        }

        // Check if the size of the file exceeds 50 MB (50,000,000 bytes)
        if ($size > 50000000) {
            // Redirect the user to the products page with an error message indicating that the file size exceeds the specified limit
            header('Location: products?tsk=edit_more_details&id='.$get_edit_product_code.'&uploadError=uploadExceedsSize');
            exit();
        }

        // Generate two random characters, convert them to uppercase hexadecimal for uniqueness
        // This step ensures that the generated identifier is unique by using the bin2hex() function to convert binary data to hexadecimal,
        // and the random_bytes() function to generate cryptographically secure pseudo-random bytes.
        // The strtoupper() function is used to convert the resulting hexadecimal characters to uppercase.
        $randomCharacters01 = strtoupper(bin2hex(random_bytes(2)));
        $randomCharacters02 = strtoupper(bin2hex(random_bytes(2)));

        // finalImage variable is made by concatenating the random characters and actual file extension to create a unique identifier for the image
        $finalImage = $randomCharacters01.$randomCharacters02.'_'.$upload_photo_product_code.'.'.$fileActualExtension;

        // Prepare a SQL query to fetch products_images from products_images table where product_image_name match the given finalImage name
        $fetchProductImageSql = 'SELECT `product_image_name` FROM `products_images` WHERE `product_image_name`=? LIMIT 1';

        // Prepare the SQL statement
        $fetchProductImageStmt = $dbConnection->prepare($fetchProductImageSql);

        // Bind parameters to the prepared statement
        $fetchProductImageStmt->bind_param('s',$finalImage);

        // Execute the prepared statement
        $fetchProductImageStmt->execute();

        // Fetch the result of the prepared statement
        $fetchProductImageResult = $fetchProductImageStmt->get_result();
        // Close the prepared statement to free up resources
        $fetchProductImageStmt->close();

        do {
            // Generate two random characters, convert them to uppercase hexadecimal for uniqueness
            // This step ensures that the generated identifier is unique by using the bin2hex() function to convert binary data to hexadecimal,
            // and the random_bytes() function to generate cryptographically secure pseudo-random bytes.
            // The strtoupper() function is used to convert the resulting hexadecimal characters to uppercase.
            $randomCharacters01 = strtoupper(bin2hex(random_bytes(2)));
            $randomCharacters02 = strtoupper(bin2hex(random_bytes(2)));

            // finalImage variable is made by concatenating the random characters and actual file extension to create a unique identifier for the image
            $finalImage = $randomCharacters01.$randomCharacters02.'_'.$upload_photo_product_code.'.'.$fileActualExtension;
        } while ($fetchProductImageResult->num_rows > 0); // Loop until a unique finalImage is generated

        // Define the destination path for the uploaded file
        // The fileDestination variable stores the path where the uploaded file will be moved to.
        // The path is relative to the current file and includes the unique identifier generated in the previous step.
        $fileDestination = '../productsImages/'.$finalImage;

        // Attempt to move the uploaded file to the specified destination
        // The move_uploaded_file() function is used to move the uploaded file to the specified destination.
        // If the file upload fails, the user is redirected to the product edit page with an error message.
        if (move_uploaded_file($tmp_name,$fileDestination)==false) {
            header('Location: products?tsk=edit_more_details&id='.$get_edit_product_code.'&uploadError=not_uploaded');
            exit();
        } else {
            // Define the SQL query to add a new product image to the 'products_images' table
            $addNewImageSql = 'INSERT INTO `products_images`(`product_code`,`product_image_name`,`product_image_date_uploaded`,`product_image_uploaded_by`) VALUES (?,?,?,?)';
             // Prepare the SQL query statement
            $addNewImageStmt = $dbConnection->prepare($addNewImageSql);
            // Store the current date and time as the date the product image was uploaded
            $product_image_date_uploaded = $currentDateAndTime;
            // Bind the parameters to the prepared statement
            $addNewImageStmt->bind_param('ssss', $get_edit_product_code,$finalImage,$product_image_date_uploaded,$_SESSION['user_id']);
            // Attempt to execute the statement for adding a new product image
            if ($addNewImageStmt->execute()) {
                // If the file upload is successful, redirect the user to the product edit page with a success message
                // The user is redirected to the product edit page with a success message if the file upload is successful.
                header('Location: products?tsk=edit_more_details&id='.$get_edit_product_code.'&uploadSuccess=uploaded');
                exit();
            } else {
                // If product_code and product_image_name are not added to the products_images table, delete the uploaaded image from the productsImages folder
                // Define the path to the image file
                $imagePath = '../productsImages/'.$finalImage;
                // Check if the file exists
                if (file_exists($imagePath)==true) {
                    // Attempt to delete the file located at $imagePath
                    if (unlink($imagePath)) {
                        // If the file was successfully deleted, redirect the user to the products page with query parameters indicating that the image was not uploaded and providing the ID of the product being edited
                        header('Location: products?tsk=edit_more_details&id='.$get_edit_product_code.'&uploadError=not_uploaded');
                        exit();
                    }
                }
            }
        }
    }

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'removeImage' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['removeImage']) == true) {
        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters in the inputs
        $get_edit_product_code = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_GET['id']), ENT_QUOTES, 'UTF-8')));
        $imageNameCode = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['imageNameCode']), ENT_QUOTES, 'UTF-8')));
        $imageName = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['imageName']), ENT_QUOTES, 'UTF-8')));

        // Check if the id from the GET parameter matches the one in the database
        if ($imageNameCode!== $imageNameCode) {
            // If the IDs do not match, redirect the user to the products page with an error message
            header('Location: products?tsk=edit_more_details&id='.$get_edit_product_code.'&uploadError=productCodeNotMatch');
            exit();
        }

        // Prepare a SQL statement to select the product code from the 'products' table where the email address matches the provided products
        $validateProductCodeSql = 'SELECT `product_code` FROM `products` WHERE `product_code` =? AND `product_code` =?';
        // Prepare the SQL statement
        $validateProductCodeStmt = $dbConnection->prepare($validateProductCodeSql);
        // Bind the products variable to the prepared statement as a string
        $validateProductCodeStmt->bind_param('ss',$get_edit_product_code,$imageNameCode);
        // Execute the prepared statement
        $validateProductCodeStmt->execute();
        // Store the result of the executed statement
        $validateProductCodeStmt->store_result();
        // Check if the number of rows returned by the executed statement is not 1, indicating that the products does not exists in the 'products' table
        if ($validateProductCodeStmt->num_rows!==1) {
            header('Location: products?tsk=edit_more_details&id='.$get_edit_product_code.'&deleteError=productNotFound');
            exit();
        }

        // Prepare a SQL statement to select the product code from the 'products_images' table where the email address matches the provided products
        $validateImageSql = 'SELECT `product_code` FROM `products_images` WHERE `product_code` =? AND `product_code` =? AND `product_image_name` =?';
        // Prepare the SQL statement
        $validateImageStmt = $dbConnection->prepare($validateImageSql);
        // Bind the products variable to the prepared statement as a string
        $validateImageStmt->bind_param('sss',$get_edit_product_code,$imageNameCode,$imageName);
        // Execute the prepared statement
        $validateImageStmt->execute();
        // Store the result of the executed statement
        $validateImageStmt->store_result();
        // Check if the number of rows returned by the executed statement is not 1, indicating that the products does not exists in the 'products' table
        if ($validateImageStmt->num_rows!==1) {
            header('Location: products?tsk=edit_more_details&id='.$get_edit_product_code.'&deleteError=imageNotFound');
            exit();
        }

        // Define the path to the image file
        $imagePath = '../productsImages/'.$imageName;
        // Check if the file exists
        if (file_exists($imagePath)==true) {
            // Attempt to delete the file located at $imagePath
            if (unlink($imagePath)) {
                $deleteImageSql = 'DELETE FROM `products_images` WHERE `product_code` =? AND `product_code` =? AND `product_image_name` =?';
                // Prepare the SQL statement
                $deleteImageStmt = $dbConnection->prepare($deleteImageSql);
                // Bind the products variable to the prepared statement as a string
                $deleteImageStmt->bind_param('sss',$get_edit_product_code,$imageNameCode,$imageName);
                // Execute the prepared statement
                if ($deleteImageStmt->execute()) {
                    header('Location: products?tsk=edit_more_details&id='.$get_edit_product_code.'&deleteSuccess=deleted');
                    exit();
                }
            }
        } else {
            header('Location: products?tsk=edit_more_details&id='.$get_edit_product_code.'&deleteError=imageNotFound');
            exit();
        }
    }
?>
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title><?php echo $fetchCompanyDataAssoc['company_name']; ?> | Products</title>
                <!-- require header.php php file -->
                <?php require 'header.php'; ?>
                <style>
                    .drag-drop-area.drag-over {
                        background-color: #f8f9fa;
                    }
                    #preview {
                    max-height: 300px;
                    display: none;
                    }
                </style>
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
                                <button type="button" name="previousPage" id="previousPage"class="btn btn-sm btn-primary" onclick="history.back()"><i class="fas fa-arrow-left mr-2"></i>Back</button>
                            </div>
                        </div>
                        <!-- Main content -->
                        <section class="content">
                            <div class="container-fluid">
                                <?php if (isset($_GET['tsk'])==true && $_GET['tsk']=='add_product') { ?>
                                    <div class="container mx-auto">
                                        <div class="card">
                                            <form action="" method="post" onsubmit="return addProductJsValidation();">
                                            <div class="card-header bg-transparent text-center font-weight-bold">
                                                Add product<br>
                                                <?php if (isset($_GET['addProductError'])==true && $_GET['addProductError']==='empty_product_name') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">Please enter the product&apos;s name</h6>
                                                <?php }elseif (isset($_GET['addProductError'])==true && $_GET['addProductError']==='empty_product_description') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">Please enter the product&apos;s description</h6>
                                                <?php }elseif (isset($_GET['addProductError'])==true && $_GET['addProductError']==='empty_product_buying_price') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">Please enter the product&apos;s buying price</h6>
                                                <?php }elseif (isset($_GET['addProductError'])==true && $_GET['addProductError']==='empty_product_selling_price') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">Please enter the product&apos;s selling price</h6>
                                                <?php }elseif (isset($_GET['addProductError'])==true && $_GET['addProductError']==='invalid_product_buying_price') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">Please enter a valid buying price</h6>
                                                <?php }elseif (isset($_GET['addProductError'])==true && $_GET['addProductError']==='invalid_product_selling_price') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">Please enter a valid selling price</h6>
                                                <?php }elseif (isset($_GET['addProductError'])==true && $_GET['addProductError']==='less_selling_price') { ?>
                                                    <h6 class="mt-1" style="color: #dc3545 !important;">Selling price should be more than buying price</h6>
                                                <?php }elseif (isset($_SESSION['addProductStatus'])==true && $_SESSION['addProductStatus']==='productAdded') { ?>
                                                    <script>$(document).ready(function () {$("#productAdded").modal('show');});</script>
                                                    <?php unset($_SESSION['addProductStatus']); ?>
                                                <?php }elseif (isset($_SESSION['addProductStatus'])==true && $_SESSION['addProductStatus']==='productNotAdded') { ?>
                                                    <script>$(document).ready(function () {$("#productNotAdded").modal('show');});</script>
                                                    <?php unset($_SESSION['addProductStatus']); ?>
                                                <?php } ?>
                                                    <div class="modal fade" id="productAdded" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="productAddedLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="productAddedLabel">product added<i class="ml-1 fa-solid text-success fa-circle-check"></i></h5>
                                                                    <span type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </span>
                                                                </div>
                                                                <div class="modal-body">
                                                                    The product has been added successfully!
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button name="okay" id="okay" type="button" data-dismiss="modal" class="btn btn-primary">Okay</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal fade" id="productNotAdded" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="productNotAddedLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="productNotAddedLabel">product not added<i class="ml-1 fa-solid text-danger fa-circle-xmark"></i></h5>
                                                                    <span type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </span>
                                                                </div>
                                                                <div class="modal-body">
                                                                    The product has not been added!<br>Please try again.
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button name="okay" id="okay" type="button" data-dismiss="modal" class="btn btn-primary">Okay</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>
                                                <div class="card-body">
                                                    <div>
                                                        <label for="add_product_name" class="control-label">Product&apos;s name<span class="text-danger ml-1">*</span></label>
                                                        <span id="add_product_name_status" class="d-block"></span>
                                                        <div class="input-group mb-3">
                                                            <input type="text" name="add_product_name" id="add_product_name" placeholder="Product&apos;s name" class="form-control" autocomplete="off" autofocus>
                                                            
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label for="add_product_description" class="control-label">Product&apos;s description<span class="text-danger ml-1">*</span></label>
                                                        <span id="add_product_description_status" class="d-block"></span>
                                                        <div class="input-group mb-3">
                                                            <input type="text" name="add_product_description" id="add_product_description" placeholder="Product&apos;s description" class="form-control" autocomplete="off">
                                                            <div class="input-group-append">
                                                                <div class="input-group-text">
                                                                    <span class="fas fa-circle-info"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label for="add_product_buying_price" class="control-label">Product&apos;s buying price<span class="text-danger ml-1">*</span></label>
                                                        <span id="add_product_buying_price_status" class="d-block"></span>
                                                        <div class="input-group mb-3">
                                                            <input type="tel" name="add_product_buying_price" id="add_product_buying_price" placeholder="Product&apos;s buying price" class="form-control" autocomplete="off">
                                                            <div class="input-group-append">
                                                                <div class="input-group-text">
                                                                <span class="fa-solid fa-coins"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label for="add_product_selling_price" class="control-label">Product&apos;s selling price<span class="text-danger ml-1">*</span></label>
                                                        <span id="add_product_selling_price_status" class="d-block"></span>
                                                        <div class="input-group mb-3">
                                                            <input type="tel" name="add_product_selling_price" id="add_product_selling_price" placeholder="Product&apos;s selling price" class="form-control" autocomplete="off">
                                                            <div class="input-group-append">
                                                                <div class="input-group-text">
                                                                    <span class="fa-solid fa-coins"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row mt-2">
                                                        <div class="col">
                                                            <button type="submit" name="add_product_btn" id="add_product_btn" class="btn btn-primary btn-block">Add product</button>
                                                            <button type="button" name="add_product_loader" id="add_product_loader" class="d-none btn btn-primary btn-block">
                                                                <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer text-center font-weight-bold">
                                                    <h6 class="d-block text-muted mt-2">&copy; <?php echo date('Y'); ?> <?php echo $fetchCompanyDataAssoc['company_name'] ?></h6>
                                                </div>
                                            </form>
                                            <script>
                                                // Function to validate add product form using JavaScript
                                                function addProductJsValidation() {
                                                    // Initialize a variable to check if input is valid
                                                    var is_input_valid = true;

                                                    // Hide the add product button and show the loading spinner
                                                    document.getElementById('add_product_btn').className = "d-none";
                                                    document.getElementById('add_product_loader').className = "d-block btn btn-primary btn-block";

                                                    // Check if the product_name input is empty
                                                    if (document.getElementById('add_product_name').value === "") {
                                                        // Set the input validity to false
                                                        is_input_valid = false;

                                                        // Add a red border to the product_name input and display an error message
                                                        document.getElementById('add_product_name').style = "border: 1px solid #dc3545;";
                                                        document.getElementById('add_product_name_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                        document.getElementById('add_product_name_status').innerHTML = "Required";

                                                        // Hide the loading spinner and show the add product button
                                                        document.getElementById('add_product_loader').className = "d-none";
                                                        document.getElementById('add_product_btn').className = "d-block btn btn-primary btn-block";
                                                    } else {
                                                        // Add a green border to the product_name input
                                                        document.getElementById('add_product_name').style.border = "1px solid #28a745";
                                                        document.getElementById('add_product_name_status').innerHTML = "";
                                                    }

                                                    // Check if the product description input is empty
                                                    if (document.getElementById('add_product_description').value === "") {
                                                        // Set the input validity to false
                                                        is_input_valid = false;

                                                        // Add a red border to the product description input and display an error message
                                                        document.getElementById('add_product_description').style = "border: 1px solid #dc3545;";
                                                        document.getElementById('add_product_description_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                        document.getElementById('add_product_description_status').innerHTML = "Required";

                                                        // Hide the loading spinner and show the add product button
                                                        document.getElementById('add_product_loader').className = "d-none";
                                                        document.getElementById('add_product_btn').className = "d-block btn btn-primary btn-block";
                                                    } else {
                                                        // Add a green border to the phone input
                                                        document.getElementById('add_product_description').style.border = "1px solid #28a745";
                                                        document.getElementById('add_product_description_status').innerHTML = "";
                                                    }

                                                    // Check if the product_buying_price input is empty
                                                    if (document.getElementById('add_product_buying_price').value === "") {
                                                        // Set the input validity to false
                                                        is_input_valid = false;

                                                        // Add a red border to the product_buying_price input and display an error message
                                                        document.getElementById('add_product_buying_price').style = "border: 1px solid #dc3545;";
                                                        document.getElementById('add_product_buying_price_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                        document.getElementById('add_product_buying_price_status').innerHTML = "Required";

                                                        // Hide the loading spinner and show the add product button
                                                        document.getElementById('add_product_loader').className = "d-none";
                                                        document.getElementById('add_product_btn').className = "d-block btn btn-primary btn-block";
                                                    } else {
                                                        var buying_price_regex = /^\d+(\.\d+)?$/;
                                                        // ^ asserts the start of the string.
                                                        // \d+ matches one or more digits.
                                                        // (\.\d+)? optionally matches a period followed by one or more digits.
                                                        // $ asserts the end of the string.

                                                        if (!buying_price_regex.test(document.getElementById('add_product_buying_price').value)) {
                                                            // Set the input validity to false
                                                            is_input_valid = false;
                                                            
                                                            // Add a red border to the add_product_buying_price input and display an error message
                                                            document.getElementById('add_product_buying_price').style = "border: 1px solid #dc3545;";
                                                            document.getElementById('add_product_buying_price_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                            document.getElementById('add_product_buying_price_status').innerHTML = "Enter a valid buying price";

                                                            // Hide the loading spinner and show the add product button
                                                            document.getElementById('add_product_loader').className = "d-none";
                                                            document.getElementById('add_product_btn').className = "d-block btn btn-primary btn-block";
                                                        } else {
                                                            // Add a green border to the add_product_buying_price input
                                                            document.getElementById('add_product_buying_price').style.border = "1px solid #28a745";
                                                            document.getElementById('add_product_buying_price_status').innerHTML = "";
                                                        }
                                                    }

                                                    // Check if the add_product_selling_price input is empty
                                                    if (document.getElementById('add_product_selling_price').value === "") {
                                                        // Set the input validity to false
                                                        is_input_valid = false;

                                                        // Add a red border to the add_product_selling_price input and display an error message
                                                        document.getElementById('add_product_selling_price').style = "border: 1px solid #dc3545;";
                                                        document.getElementById('add_product_selling_price_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                        document.getElementById('add_product_selling_price_status').innerHTML = "Required";

                                                        // Hide the loading spinner and show the add product button
                                                        document.getElementById('add_product_loader').className = "d-none";
                                                        document.getElementById('add_product_btn').className = "d-block btn btn-primary btn-block";
                                                    } else {
                                                        var selling_price_regex = /^\d+(\.\d+)?$/;
                                                        // ^ asserts the start of the string.
                                                        // \d+ matches one or more digits.
                                                        // (\.\d+)? optionally matches a period followed by one or more digits.
                                                        // $ asserts the end of the string.

                                                        if (!selling_price_regex.test(document.getElementById('add_product_selling_price').value)) {
                                                            // Set the input validity to false
                                                            is_input_valid = false;
                                                            
                                                            // Add a red border to the add_product_selling_price input and display an error message
                                                            document.getElementById('add_product_selling_price').style = "border: 1px solid #dc3545;";
                                                            document.getElementById('add_product_selling_price_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                            document.getElementById('add_product_selling_price_status').innerHTML = "Enter a valid buying price";

                                                            // Hide the loading spinner and show the add product button
                                                            document.getElementById('add_product_loader').className = "d-none";
                                                            document.getElementById('add_product_btn').className = "d-block btn btn-primary btn-block";
                                                        } else {
                                                            if (parseFloat(document.getElementById('add_product_selling_price').value) < parseFloat(document.getElementById('add_product_buying_price').value)) {
                                                                // Set the input validity to false
                                                                is_input_valid = false;
                                                                // Add a red border to the add_product_selling_price input and display an error message
                                                                document.getElementById('add_product_selling_price').style = "border: 1px solid #dc3545;";
                                                                document.getElementById('add_product_selling_price_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                                document.getElementById('add_product_selling_price_status').innerHTML = "Selling price should be more than buying price";

                                                                // Hide the loading spinner and show the add product button
                                                                document.getElementById('add_product_loader').className = "d-none";
                                                                document.getElementById('add_product_btn').className = "d-block btn btn-primary btn-block";
                                                            } else {
                                                                // Add a green border to the add_product_selling_price input
                                                                document.getElementById('add_product_selling_price').style.border = "1px solid #28a745";
                                                                document.getElementById('add_product_selling_price_status').innerHTML = "";
                                                            }
                                                        }
                                                    }

                                                    // Return the validity of the input
                                                    return is_input_valid;
                                                }
                                            </script>
                                        </div>
                                    </div>
                                <?php }elseif (isset($_GET['tsk'])==true && $_GET['tsk']=='view_products') { ?>
                                    <div class="card card-outline card-dark" style="border-bottom: 3px solid #343a40;">
                                        <div class="card-header">
                                            <h6 class="text-dark font-weight-bold">View products</h6>
                                        </div>
                                        <div class="card-body">
                                            <table class="text-nowrap table table-bordered table-hover table-striped table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl" id="products_list_table">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>Product code</th>
                                                        <th>Product name</th>
                                                        <th>Selling price</th>
                                                        <th>Quantity</th>
                                                        <th class="col-1">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        // initial number
                                                        $listNumber = 1;
                                                        // Prepare a SQL query to fetch product data from the database
                                                        $fetchProductsSql = 'SELECT `product_code`,`product_name`,`product_description`,`product_selling_price`,`product_count` FROM `products` ORDER BY `product_date_created` ASC';

                                                        // Prepare the SQL statement
                                                        $fetchProductsStmt = $dbConnection->prepare($fetchProductsSql);

                                                        // Execute the prepared statement
                                                        $fetchProductsStmt->execute();

                                                        // Retrieve the result set
                                                        $fetchProductsResult = $fetchProductsStmt->get_result();
                                                        
                                                        // Close the prepared statement to free up resources
                                                        $fetchProductsStmt->close();

                                                        // Fetch data as an associative array
                                                        while ($fetchProductsAssoc= $fetchProductsResult->fetch_assoc()) {
                                                    ?>
                                                        <tr>
                                                            <td class="font-weight-bold"><?php echo $fetchProductsAssoc['product_code']; ?></td>
                                                            <td>
                                                                <?php if (empty($fetchProductsAssoc['product_name'])==true) { ?>
                                                                    Not set
                                                                <?php }else{ ?>
                                                                    <?php echo $fetchProductsAssoc['product_name']; ?>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <?php if (empty($fetchProductsAssoc['product_selling_price'])==true) { ?>
                                                                    Not set
                                                                <?php }else{ ?>
                                                                    <?php echo number_format($fetchProductsAssoc['product_selling_price'], 2); ?>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <?php if (empty($fetchProductsAssoc['product_count'])==true) { ?>
                                                                    Not set
                                                                <?php }else{ ?>
                                                                    <?php echo number_format($fetchProductsAssoc['product_count'], 2); ?>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <div class="btn-group" role="group">
                                                                    <span class="btn btn-md btn-primary" onclick="window.open('productDetalis?id=<?php echo $fetchProductsAssoc['product_code']; ?>','popup','width=900,height=600'); return false;">View</span>
                                                                    <span class="btn btn-md btn-secondary ml-1"><a class="text-light text-decoration-none" href="products?tsk=edit_product&id=<?php echo $fetchProductsAssoc['product_code']; ?>">Edit</a></span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <?php }elseif (isset($_GET['tsk'])==true && isset($_GET['id'])==true && empty($_GET['id'])==false && $_GET['tsk']=='edit_product') { ?>
                                    <?php
                                        // Prepare a SQL statement to select the product code from the 'products' table where the email address matches the provided products
                                        $selectProductCodeSql = 'SELECT `product_code` FROM `products` WHERE `product_code` =?';
                                        // Prepare the SQL statement
                                        $selectProductCodeStmt = $dbConnection->prepare($selectProductCodeSql);
                                        // Bind the products variable to the prepared statement as a string
                                        $selectProductCodeStmt->bind_param('s',$_GET['id']);
                                        // Execute the prepared statement
                                        $selectProductCodeStmt->execute();
                                        // Store the result of the executed statement
                                        $selectProductCodeStmt->store_result();
                                        // Check if the number of rows returned by the executed statement is not 1, indicating that the products does not exists in the 'products' table
                                        if ($selectProductCodeStmt->num_rows!==1) {
                                            $selectProductCodeStmt->close();// Close the prepared statement to free up resources
                                            $invalidProduct="Yes";// Set a flag to indicate that the product code is invalid
                                    ?>
                                        <script>$(document).ready(function () {$("#invalidProduct").modal('show');});</script>
                                        <?php }else{$invalidProduct='No';} ?>

                                        <?php if ($invalidProduct=='Yes') { ?>
                                            <div class="modal fade" id="invalidProduct" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="invalidProductLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="invalidProductLabel">Invalid product<i class="ml-1 fa-solid text-danger fa-circle-xmark"></i></h5>
                                                        </div>
                                                        <div class="modal-body">
                                                            The product does not exist.<br>Please select from the products table.
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a class="text-light btn btn-sm btn-primary text-decoration-none" href="products?tsk=view_products">Proceed to view products</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php }elseif ($invalidProduct=='No') { ?>
                                            <div class="container mx-auto">
                                                <?php
                                                    // Prepare a SQL statement to select products details from the 'products' table where the 'product_code' matches the provided product_code.
                                                    $selectProductDetailsSql = 'SELECT `product_code`,`product_name`,`product_description`,`product_buying_price`,`product_selling_price`,`product_count`,`product_status` FROM `products` WHERE `product_code` =?';

                                                    // Prepare the SQL statement for execution
                                                    $selectProductDetailsStmt = $dbConnection->prepare($selectProductDetailsSql);

                                                    // Bind the provided ID to the prepared statement
                                                    $selectProductDetailsStmt->bind_param('s',$_GET['id']);

                                                    // Execute the prepared statement
                                                    $selectProductDetailsStmt->execute();

                                                    // Get the result of the executed statement
                                                    $selectProductDetailsResult = $selectProductDetailsStmt->get_result();

                                                    // Close the prepared statement to free up resources
                                                    $selectProductDetailsStmt->close();

                                                    // Fetch the result as an associative array
                                                    $selectProductDetailsAssoc= $selectProductDetailsResult->fetch_assoc();
                                                ?>
                                                <div class="card">
                                                    <form action="" method="post" onsubmit="return editProductJsValidation();">
                                                        <div class="card-header bg-transparent text-center font-weight-bold">
                                                            Edit product<input type="hidden" class="form-control form-control-sm" readonly name="edit_product_code" id="edit_product_code" value="<?php echo $selectProductDetailsAssoc['product_code']; ?>" placeholder="edit_product_code">
                                                            <br>
                                                            <?php if (isset($_GET['editProductError']) && $_GET['editProductError']=='productCodeNotMatch') { ?>
                                                                <span class="text-danger">There was an error editing the product<br>Please try again.</span>
                                                            <?php }elseif (isset($_GET['editProductError'])==true && $_GET['editProductError']==='empty_product_name') { ?>
                                                                <h6 class="mt-1" style="color: #dc3545 !important;">Please enter the product&apos;s name</h6>
                                                            <?php }elseif (isset($_GET['editProductError'])==true && $_GET['editProductError']==='empty_product_description') { ?>
                                                                <h6 class="mt-1" style="color: #dc3545 !important;">Please enter the product&apos;s description</h6>
                                                            <?php }elseif (isset($_GET['editProductError'])==true && $_GET['editProductError']==='empty_product_buying_price') { ?>
                                                                <h6 class="mt-1" style="color: #dc3545 !important;">Please enter the product&apos;s buying price</h6>
                                                            <?php }elseif (isset($_GET['editProductError'])==true && $_GET['editProductError']==='empty_product_selling_price') { ?>
                                                                <h6 class="mt-1" style="color: #dc3545 !important;">Please enter the product&apos;s selling price</h6>
                                                            <?php }elseif (isset($_GET['editProductError'])==true && $_GET['editProductError']==='invalid_product_buying_price') { ?>
                                                                <h6 class="mt-1" style="color: #dc3545 !important;">Please enter a valid buying price</h6>
                                                            <?php }elseif (isset($_GET['editProductError'])==true && $_GET['editProductError']==='invalid_product_selling_price') { ?>
                                                                <h6 class="mt-1" style="color: #dc3545 !important;">Please enter a valid selling price</h6>
                                                            <?php }elseif (isset($_GET['editProductError'])==true && $_GET['editProductError']==='less_selling_price') { ?>
                                                                <h6 class="mt-1" style="color: #dc3545 !important;">Selling price should be more than buying price</h6>
                                                            <?php }elseif (isset($_SESSION['addProductStatus'])==true && $_SESSION['addProductStatus']==='productAdded') { ?>
                                                                <script>$(document).ready(function () {$("#productAdded").modal('show');});</script>
                                                                <?php unset($_SESSION['addProductStatus']); ?>
                                                            <?php }elseif (isset($_SESSION['addProductStatus'])==true && $_SESSION['addProductStatus']==='productNotAdded') { ?>
                                                                <script>$(document).ready(function () {$("#productNotAdded").modal('show');});</script>
                                                                <?php unset($_SESSION['addProductStatus']); ?>
                                                            <?php } ?>
                                                                <div class="modal fade" id="productAdded" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="productAddedLabel" aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title" id="productAddedLabel">product added<i class="ml-1 fa-solid text-success fa-circle-check"></i></h5>
                                                                                <span type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                </span>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                The product has been added successfully!
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button name="okay" id="okay" type="button" data-dismiss="modal" class="btn btn-primary">Okay</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal fade" id="productNotAdded" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="productNotAddedLabel" aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title" id="productNotAddedLabel">product not added<i class="ml-1 fa-solid text-danger fa-circle-xmark"></i></h5>
                                                                                <span type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                </span>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                The product has not been added!<br>Please try again.
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button name="okay" id="okay" type="button" data-dismiss="modal" class="btn btn-primary">Okay</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <div>
                                                                <?php if ($selectProductDetailsAssoc['product_status'] == '6') { ?>
                                                                <span class="d-block font-weight-bold">Product status<span class="text-danger ml-1">*</span></span>
                                                                <div class="row">
                                                                        <div class="col p-2 form-check form-check-inline">
                                                                        <input class="form-check-input" type="radio" name="edit_product_status" id="option01" value="6" checked>
                                                                        <label class="form-check-label" for="option01">Active</label>
                                                                    </div>
                                                                    <div class="col p-2 form-check form-check-inline">
                                                                        <input class="form-check-input" type="radio" name="edit_product_status" id="option02" value="5">
                                                                        <label class="form-check-label" for="option02">Inactive</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <?php } elseif ($selectProductDetailsAssoc['product_status'] == '5') { ?>
                                                                <span class="d-block font-weight-bold">Product status<span class="text-danger ml-1">*</span></span>
                                                                <div class="row">
                                                                        <div class="col p-2 form-check form-check-inline">
                                                                        <input class="form-check-input" type="radio" name="edit_product_status" id="option01" value="6">
                                                                        <label class="form-check-label" for="option01">Active</label>
                                                                    </div>
                                                                    <div class="col p-2 form-check form-check-inline">
                                                                        <input class="form-check-input" type="radio" name="edit_product_status" id="option02" value="5" checked>
                                                                        <label class="form-check-label" for="option02">Inactive</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <?php } elseif ($selectProductDetailsAssoc['product_status'] !== '6' || $selectProductDetailsAssoc['product_status'] !== '5') { ?>
                                                                    <span class="d-block font-weight-bold">
                                                                    <i class="fa-solid fa-triangle-exclamation fa-beat-fade text-danger mr-1"></i>
                                                                        Product status
                                                                        <span class="text-danger ml-1">*</span></span>
                                                                    <div class="row">
                                                                        <div class="col p-2 form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio" name="edit_product_status" id="option01" value="6">
                                                                            <label class="form-check-label" for="option01">Active</label>
                                                                        </div>
                                                                        <div class="col p-2 form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio" name="edit_product_status" id="option02" value="5">
                                                                            <label class="form-check-label" for="option02">Inactive</label>
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                            <div>
                                                                <label for="edit_product_name" class="control-label">Product&apos;s name<span class="text-danger ml-1">*</span></label>
                                                                <span id="edit_product_name_status" class="d-block"></span>
                                                                <div class="input-group mb-3">
                                                                    <input type="text" name="edit_product_name" id="edit_product_name" value="<?php echo $selectProductDetailsAssoc['product_name']; ?>" placeholder="Product&apos;s name" class="form-control" autocomplete="off">
                                                                    
                                                                </div>
                                                            </div>

                                                            <div>
                                                                <label for="edit_product_description" class="control-label">Product&apos;s description<span class="text-danger ml-1">*</span></label>
                                                                <span id="edit_product_description_status" class="d-block"></span>
                                                                <div class="input-group mb-3">
                                                                    <input type="text" name="edit_product_description" id="edit_product_description" value="<?php echo $selectProductDetailsAssoc['product_description']; ?>" placeholder="Product&apos;s description" class="form-control" autocomplete="off">
                                                                    <div class="input-group-append">
                                                                        <div class="input-group-text">
                                                                            <span class="fas fa-circle-info"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div>
                                                                <label for="edit_product_buying_price" class="control-label">Product&apos;s buying price<span class="text-danger ml-1">*</span></label>
                                                                <span id="edit_product_buying_price_status" class="d-block"></span>
                                                                <div class="input-group mb-3">
                                                                    <input type="tel" name="edit_product_buying_price" id="edit_product_buying_price" value="<?php echo $selectProductDetailsAssoc['product_buying_price']; ?>" placeholder="Product&apos;s buying price" class="form-control" autocomplete="off">
                                                                    <div class="input-group-append">
                                                                        <div class="input-group-text">
                                                                        <span class="fa-solid fa-coins"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div>
                                                                <label for="edit_product_selling_price" class="control-label">Product&apos;s selling price<span class="text-danger ml-1">*</span></label>
                                                                <span id="edit_product_selling_price_status" class="d-block"></span>
                                                                <div class="input-group mb-3">
                                                                    <input type="tel" name="edit_product_selling_price" id="edit_product_selling_price" value="<?php echo $selectProductDetailsAssoc['product_selling_price']; ?>" placeholder="Product&apos;s selling price" class="form-control" autocomplete="off">
                                                                    <div class="input-group-append">
                                                                        <div class="input-group-text">
                                                                            <span class="fa-solid fa-coins"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div>
                                                                <label for="edit_product_count" class="control-label">Product&apos;s quantity in stock<span class="text-danger ml-1">*</span></label>
                                                                <span id="edit_product_count_status" class="d-block"></span>
                                                                <div class="input-group mb-3">
                                                                    <input type="tel" name="edit_product_count" id="edit_product_count" value="<?php echo $selectProductDetailsAssoc['product_count']; ?>" placeholder="Product&apos;s quantity in stock" class="form-control" autocomplete="off">
                                                                    <div class="input-group-append">
                                                                        <div class="input-group-text">
                                                                            <span class="fa-solid fa-hashtag"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row mt-2">
                                                                <div class="mt-2 col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
                                                                    <a href="products?tsk=edit_more_details&id=<?php echo $_GET['id']; ?>" name="edit_more_details" id="edit_more_details" class="btn btn-secondary btn-block">Edit more details</a>
                                                                </div>
                                                                <div class="mt-2 col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4"></div>
                                                                <div class="mt-2 col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
                                                                    <button type="submit" name="edit_product_btn" id="edit_product_btn" class="btn btn-primary btn-block">Edit product</a>
                                                                    <button type="button" name="edit_product_loader" id="edit_product_loader" class="d-none btn btn-primary btn-block">
                                                                        <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-footer text-center font-weight-bold">
                                                            <h6 class="d-block text-muted mt-2">&copy; <?php echo date('Y'); ?> <?php echo $fetchCompanyDataAssoc['company_name'] ?></h6>
                                                        </div>
                                                    </form>
                                                    <script>
                                                        // Function to validate edit product form using JavaScript
                                                        function editProductJsValidation() {
                                                            // Initialize a variable to check if input is valid
                                                            var is_input_valid = true;

                                                            // Hide the edit product button and show the loading spinner
                                                            document.getElementById('edit_product_btn').className = "d-none";
                                                            document.getElementById('edit_product_loader').className = "d-block btn btn-primary btn-block";

                                                            // Check if the product_name input is empty
                                                            if (document.getElementById('edit_product_name').value === "") {
                                                                // Set the input validity to false
                                                                is_input_valid = false;

                                                                // Add a red border to the product_name input and display an error message
                                                                document.getElementById('edit_product_name').style = "border: 1px solid #dc3545;";
                                                                document.getElementById('edit_product_name_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                                document.getElementById('edit_product_name_status').innerHTML = "Required";

                                                                // Hide the loading spinner and show the edit product button
                                                                document.getElementById('edit_product_loader').className = "d-none";
                                                                document.getElementById('edit_product_btn').className = "d-block btn btn-primary btn-block";
                                                            } else {
                                                                // Add a green border to the product_name input
                                                                document.getElementById('edit_product_name').style.border = "1px solid #28a745";
                                                                document.getElementById('edit_product_name_status').innerHTML = "";
                                                            }

                                                            // Check if the product description input is empty
                                                            if (document.getElementById('edit_product_description').value === "") {
                                                                // Set the input validity to false
                                                                is_input_valid = false;

                                                                // Add a red border to the product description input and display an error message
                                                                document.getElementById('edit_product_description').style = "border: 1px solid #dc3545;";
                                                                document.getElementById('edit_product_description_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                                document.getElementById('edit_product_description_status').innerHTML = "Required";

                                                                // Hide the loading spinner and show the edit product button
                                                                document.getElementById('edit_product_loader').className = "d-none";
                                                                document.getElementById('edit_product_btn').className = "d-block btn btn-primary btn-block";
                                                            } else {
                                                                // Add a green border to the phone input
                                                                document.getElementById('edit_product_description').style.border = "1px solid #28a745";
                                                                document.getElementById('edit_product_description_status').innerHTML = "";
                                                            }

                                                            // Check if the product_buying_price input is empty
                                                            if (document.getElementById('edit_product_buying_price').value === "") {
                                                                // Set the input validity to false
                                                                is_input_valid = false;

                                                                // Add a red border to the product_buying_price input and display an error message
                                                                document.getElementById('edit_product_buying_price').style = "border: 1px solid #dc3545;";
                                                                document.getElementById('edit_product_buying_price_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                                document.getElementById('edit_product_buying_price_status').innerHTML = "Required";

                                                                // Hide the loading spinner and show the edit product button
                                                                document.getElementById('edit_product_loader').className = "d-none";
                                                                document.getElementById('edit_product_btn').className = "d-block btn btn-primary btn-block";
                                                            } else {
                                                                var buying_price_regex = /^\d+(\.\d+)?$/;
                                                                // ^ asserts the start of the string.
                                                                // \d+ matches one or more digits.
                                                                // (\.\d+)? optionally matches a period followed by one or more digits.
                                                                // $ asserts the end of the string.

                                                                if (!buying_price_regex.test(document.getElementById('edit_product_buying_price').value)) {
                                                                    // Set the input validity to false
                                                                    is_input_valid = false;
                                                                    
                                                                    // Add a red border to the edit_product_buying_price input and display an error message
                                                                    document.getElementById('edit_product_buying_price').style = "border: 1px solid #dc3545;";
                                                                    document.getElementById('edit_product_buying_price_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                                    document.getElementById('edit_product_buying_price_status').innerHTML = "Enter a valid buying price";

                                                                    // Hide the loading spinner and show the edit product button
                                                                    document.getElementById('edit_product_loader').className = "d-none";
                                                                    document.getElementById('edit_product_btn').className = "d-block btn btn-primary btn-block";
                                                                } else {
                                                                    // Add a green border to the edit_product_buying_price input
                                                                    document.getElementById('edit_product_buying_price').style.border = "1px solid #28a745";
                                                                    document.getElementById('edit_product_buying_price_status').innerHTML = "";
                                                                }
                                                            }

                                                            // Check if the edit_product_selling_price input is empty
                                                            if (document.getElementById('edit_product_selling_price').value === "") {
                                                                // Set the input validity to false
                                                                is_input_valid = false;

                                                                // Add a red border to the edit_product_selling_price input and display an error message
                                                                document.getElementById('edit_product_selling_price').style = "border: 1px solid #dc3545;";
                                                                document.getElementById('edit_product_selling_price_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                                document.getElementById('edit_product_selling_price_status').innerHTML = "Required";

                                                                // Hide the loading spinner and show the edit product button
                                                                document.getElementById('edit_product_loader').className = "d-none";
                                                                document.getElementById('edit_product_btn').className = "d-block btn btn-primary btn-block";
                                                            } else {
                                                                var selling_price_regex = /^\d+(\.\d+)?$/;
                                                                // ^ asserts the start of the string.
                                                                // \d+ matches one or more digits.
                                                                // (\.\d+)? optionally matches a period followed by one or more digits.
                                                                // $ asserts the end of the string.

                                                                if (!selling_price_regex.test(document.getElementById('edit_product_selling_price').value)) {
                                                                    // Set the input validity to false
                                                                    is_input_valid = false;
                                                                    
                                                                    // Add a red border to the edit_product_selling_price input and display an error message
                                                                    document.getElementById('edit_product_selling_price').style = "border: 1px solid #dc3545;";
                                                                    document.getElementById('edit_product_selling_price_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                                    document.getElementById('edit_product_selling_price_status').innerHTML = "Enter a valid buying price";

                                                                    // Hide the loading spinner and show the edit product button
                                                                    document.getElementById('edit_product_loader').className = "d-none";
                                                                    document.getElementById('edit_product_btn').className = "d-block btn btn-primary btn-block";
                                                                } else {
                                                                    if (parseFloat(document.getElementById('edit_product_selling_price').value) < parseFloat(document.getElementById('edit_product_buying_price').value)) {
                                                                        // Set the input validity to false
                                                                        is_input_valid = false;

                                                                        // Add a red border to the edit_product_selling_price input and display an error message
                                                                        document.getElementById('edit_product_selling_price').style = "border: 1px solid #dc3545;";
                                                                        document.getElementById('edit_product_selling_price_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                                        document.getElementById('edit_product_selling_price_status').innerHTML = "Selling price should be more than buying price";

                                                                        // Hide the loading spinner and show the edit product button
                                                                        document.getElementById('edit_product_loader').className = "d-none";
                                                                        document.getElementById('edit_product_btn').className = "d-block btn btn-primary btn-block";
                                                                    } else {
                                                                        // Add a green border to the edit_product_selling_price input
                                                                        document.getElementById('edit_product_selling_price').style.border = "1px solid #28a745";
                                                                        document.getElementById('edit_product_selling_price_status').innerHTML = "";
                                                                    }
                                                                }
                                                            }

                                                            // Check if the edit_product_count input is empty
                                                            if (document.getElementById('edit_product_count').value === "") {
                                                                // Set the input validity to false
                                                                is_input_valid = false;

                                                                // Add a red border to the product_buying_price input and display an error message
                                                                document.getElementById('edit_product_count').style = "border: 1px solid #dc3545;";
                                                                document.getElementById('edit_product_count_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                                document.getElementById('edit_product_count_status').innerHTML = "Required";

                                                                // Hide the loading spinner and show the edit product button
                                                                document.getElementById('edit_product_loader').className = "d-none";
                                                                document.getElementById('edit_product_btn').className = "d-block btn btn-primary btn-block";
                                                            } else {
                                                                var edit_product_count_regex = /^\d+(\.\d+)?$/;
                                                                // ^ asserts the start of the string.
                                                                // \d+ matches one or more digits.
                                                                // (\.\d+)? optionally matches a period followed by one or more digits.
                                                                // $ asserts the end of the string.

                                                                if (!edit_product_count_regex.test(document.getElementById('edit_product_count').value)) {
                                                                    // Set the input validity to false
                                                                    is_input_valid = false;
                                                                    
                                                                    // Add a red border to the edit_product_count input and display an error message
                                                                    document.getElementById('edit_product_count').style = "border: 1px solid #dc3545;";
                                                                    document.getElementById('edit_product_count_status').style = "color:#dc3545;font-size:15px;margin:0;padding:0;";
                                                                    document.getElementById('edit_product_count_status').innerHTML = "Enter a valid buying price";

                                                                    // Hide the loading spinner and show the edit product button
                                                                    document.getElementById('edit_product_loader').className = "d-none";
                                                                    document.getElementById('edit_product_btn').className = "d-block btn btn-primary btn-block";
                                                                } else {
                                                                    // Add a green border to the edit_product_count input
                                                                    document.getElementById('edit_product_count').style.border = "1px solid #28a745";
                                                                    document.getElementById('edit_product_count_status').innerHTML = "";
                                                                }
                                                            }

                                                            // Return the validity of the input
                                                            return is_input_valid;
                                                        }
                                                    </script>
                                                </div>
                                            </div>
                                        <?php } ?>
                                <?php }elseif (isset($_GET['tsk'])==true && isset($_GET['id'])==true && empty($_GET['id'])==false && $_GET['tsk']=='edit_more_details') { ?>
                                    <?php
                                        // Prepare a SQL statement to select the product code from the 'products' table where the email address matches the provided products
                                        $selectProductCodeSql = 'SELECT `product_code` FROM `products` WHERE `product_code` =?';
                                        // Prepare the SQL statement
                                        $selectProductCodeStmt = $dbConnection->prepare($selectProductCodeSql);
                                        // Bind the products variable to the prepared statement as a string
                                        $selectProductCodeStmt->bind_param('s',$_GET['id']);
                                        // Execute the prepared statement
                                        $selectProductCodeStmt->execute();
                                        // Store the result of the executed statement
                                        $selectProductCodeStmt->store_result();
                                        // Check if the number of rows returned by the executed statement is not 1, indicating that the products does not exists in the 'products' table
                                        if ($selectProductCodeStmt->num_rows!==1) {
                                            $selectProductCodeStmt->close();// Close the prepared statement to free up resources
                                            $invalidProduct="Yes";// Set a flag to indicate that the product code is invalid
                                        ?>
                                        <script>$(document).ready(function () {$("#invalidProduct").modal('show');});</script>
                                        <?php }else{$invalidProduct='No';}// Set a flag to indicate that the product code is invalid ?>

                                        <?php if ($invalidProduct=='Yes') { ?>
                                            <div class="modal fade" id="invalidProduct" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="invalidProductLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="invalidProductLabel">Invalid product<i class="ml-1 fa-solid text-danger fa-circle-xmark"></i></h5>
                                                        </div>
                                                        <div class="modal-body">
                                                            The product does not exist.<br>Please select from the products table.
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a class="text-light btn btn-sm btn-primary text-decoration-none" href="products?tsk=view_products">Proceed to view products</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php }elseif ($invalidProduct=='No') { ?>
                                            <?php
                                                // Prepare a SQL statement to select products code from the 'products' table where the 'product_code' matches the provided product_code.
                                                $selectProductDetailsSql = 'SELECT `product_code` FROM `products` WHERE `product_code` =?';

                                                // Prepare the SQL statement for execution
                                                $selectProductDetailsStmt = $dbConnection->prepare($selectProductDetailsSql);

                                                // Bind the provided ID to the prepared statement
                                                $selectProductDetailsStmt->bind_param('s',$_GET['id']);

                                                // Execute the prepared statement
                                                $selectProductDetailsStmt->execute();

                                                // Get the result of the executed statement
                                                $selectProductDetailsResult = $selectProductDetailsStmt->get_result();

                                                // Close the prepared statement to free up resources
                                                $selectProductDetailsStmt->close();

                                                // Fetch the result as an associative array
                                                $selectProductDetailsAssoc= $selectProductDetailsResult->fetch_assoc();
                                            ?>
                                            <div class="container mx-auto col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                            <?php if (isset($_GET['uploadError'])==true && $_GET['uploadError']==='productCodeNotMatch') { ?>
                                                <script>$(document).ready(function () {$("#productCodeNotMatch").modal('show');});</script>
                                                <div class="modal fade" id="productCodeNotMatch" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="productCodeNotMatchLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="productCodeNotMatchLabel">Invalid product<i class="ml-1 fa-solid text-danger fa-circle-xmark"></i></h5>
                                                            </div>
                                                            <div class="modal-body">
                                                                The product does not exist.<br>Please select from the products table.
                                                            </div>
                                                            <div class="modal-footer">
                                                                <a class="text-light btn btn-sm btn-primary text-decoration-none" href="products?tsk=view_products">Proceed to view products</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                                <div class="text-center">
                                                    <?php if (isset($_GET['uploadError'])==true && $_GET['uploadError']==='invalidExtension') { ?>
                                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                            <strong>Error!</strong> Only images with png, jpg and jpeg extensions are allowed.
                                                            <span type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </span>
                                                        </div>
                                                    <?php } elseif (isset($_GET['uploadError'])==true && $_GET['uploadError']==='uploadExceedsSize') { ?>
                                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                            <strong>Error!</strong> Only images with upto 50 MB are allowed.
                                                            <span type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </span>
                                                        </div>
                                                    <?php } elseif (isset($_GET['uploadError'])==true && $_GET['uploadError']==='not_uploaded') { ?>
                                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                            <strong>Error!</strong> The image couldn&apos;t be uploaded.
                                                            <span type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </span>
                                                        </div>
                                                    <?php } elseif (isset($_GET['deleteError'])==true && $_GET['deleteError']==='productNotFound') { ?>
                                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                            <strong>Error!</strong> The product couldn&apos;t be found.
                                                            <span type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </span>
                                                        </div>
                                                    <?php } elseif (isset($_GET['deleteError'])==true && $_GET['deleteError']==='imageNotFound') { ?>
                                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                            <strong>Error!</strong> The image couldn&apos;t be found.
                                                            <span type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </span>
                                                        </div>
                                                    <?php } elseif (isset($_GET['deleteSuccess'])==true && $_GET['deleteSuccess']==='deleted') { ?>
                                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                            <strong>Success!</strong> The image has been deleted successfully.
                                                            <span type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </span>
                                                        </div>
                                                    <?php } elseif (isset($_GET['uploadSuccess'])==true && $_GET['uploadSuccess']==='uploaded') { ?>
                                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                            <strong>Success!</strong> The image has been uploaded.
                                                            <span type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </span>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <div class="">
                                                    <div class="card p-2">
                                                        <div class="drag-drop-area">
                                                            <h6 class="mt-2"> Upload Your Photo</h6>
                                                            <form action="" method="post" enctype="multipart/form-data" onsubmit="return uploadImageJsValidation();">
                                                                <input type="hidden" class="form-control form-control-sm" readonly name="upload_photo_product_code" id="upload_photo_product_code" value="<?php echo $selectProductDetailsAssoc['product_code']; ?>" placeholder="edit_product_code">
                                                                <div class="mb-3 p-1 border-primary drag-drop-area" id="dragDropArea" style="border: 2px dashed #007bff;cursor: pointer;">
                                                                    <!-- <span style="font-size:16px;">Drag and drop your photo here or click to select</span> -->
                                                                    <span style="font-size:16px;">Click to select</span>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <img id="preview" src="" alt="Image Preview" class="img-fluid">
                                                                </div>
                                                                <input type="file" name="imageUpload" id="imageUpload" class="form-control" style="display: none;">
                                                                <button type="submit" name="submitPhoto" id="submitPhoto" class="btn btn-primary">Upload</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <script>
                                                    document.addEventListener('DOMContentLoaded', function() {
                                                        var dragDropArea = document.getElementById('dragDropArea');
                                                        var imageUpload = document.getElementById('imageUpload');
                                                        var preview = document.getElementById('preview');

                                                        // Prevent default drag behaviors
                                                        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                                                            dragDropArea.addEventListener(eventName, preventDefaults, false);
                                                            document.body.addEventListener(eventName, preventDefaults, false);
                                                        });

                                                        // Highlight drag area when item is dragged over it
                                                        ['dragenter', 'dragover'].forEach(eventName => {
                                                            dragDropArea.addEventListener(eventName, () => dragDropArea.classList.add('drag-over'), false);
                                                        });

                                                        ['dragleave', 'drop'].forEach(eventName => {
                                                            dragDropArea.addEventListener(eventName, () => dragDropArea.classList.remove('drag-over'), false);
                                                        });

                                                        function preventDefaults(e) {
                                                            e.preventDefault();
                                                            e.stopPropagation();
                                                        }

                                                        // Handle dropped files
                                                        dragDropArea.addEventListener('drop', handleDrop, false);
                                                        dragDropArea.addEventListener('click', () => imageUpload.click());
                                                        imageUpload.addEventListener('change', handleFiles);

                                                        function handleDrop(e) {
                                                            var dt = e.dataTransfer;
                                                            var files = dt.files;
                                                            handleFiles({ target: { files: files } });
                                                        }

                                                        function handleFiles(e) {
                                                            var files = e.target.files;
                                                            if (files.length) {
                                                            var file = files[0];
                                                            preview.src = URL.createObjectURL(file);
                                                            preview.style.display = 'block';
                                                            }
                                                        }
                                                    });
                                                </script>
                                            </div>
                                            <div class="container">
                                                <?php
                                                    // Define the SQL statement to fetch the product_name from the 'products' table
                                                    // using a prepared statement with a placeholder for the product_code.
                                                    $fetchProductNameSql = 'SELECT `product_name` FROM `products` WHERE `product_code`=? LIMIT 1';

                                                    // Prepare the SQL statement for execution using the database connection.
                                                    $fetchProductNameStmt = $dbConnection->prepare($fetchProductNameSql);

                                                    // Bind the provided ID to the prepared statement
                                                    $fetchProductNameStmt->bind_param('s',$_GET['id']);

                                                    // Execute the prepared SQL statement
                                                    $fetchProductNameStmt->execute();

                                                    // Fetch the result of the prepared statement
                                                    $fetchProductNameResult = $fetchProductNameStmt->get_result();

                                                    // Close the prepared statement to free up resources
                                                    $fetchProductNameStmt->close();

                                                    // Fetch the result set as an associative array
                                                    $fetchProductNameAssoc = $fetchProductNameResult->fetch_assoc();

                                                    // Prepare a SQL query to fetch product_code, products_images from products_images table where product_code match the given id
                                                    $selectProductImagesSql = 'SELECT `product_code`,`product_image_name` FROM `products_images` WHERE `product_code` =?';

                                                    // Prepare the SQL statement for execution
                                                    $selectProductImagesStmt = $dbConnection->prepare($selectProductImagesSql);

                                                    // Bind the provided ID to the prepared statement
                                                    $selectProductImagesStmt->bind_param('s',$_GET['id']);

                                                    // Execute the prepared statement
                                                    $selectProductImagesStmt->execute();

                                                    // Fetch the result of the prepared statement
                                                    $selectProductImagesResult = $selectProductImagesStmt->get_result();

                                                    // // Close the prepared statement to free up resources
                                                    // $selectProductImagesStmt->close();
                                                ?>
                                                <div class="row justify-content-left">
                                                    <!-- Fetch the result as an associative array -->
                                                    <?php $number = 0; while($selectProductImagesAssoc= $selectProductImagesResult->fetch_assoc()){ $newNumber = $number++; ?>
                                                        <div class="col-auto">
                                                            <!-- Check if the product image file exists in the '../productsImages/' directory -->
                                                            <?php if (is_file('../productsImages/'.$selectProductImagesAssoc['product_image_name'])==true) { ?>
                                                                <!-- Display product image if it exists -->
                                                                <div class="col-6 col-sm-6 col-md-6 col-lg-4 col-xl-4 mt-2">
                                                                    <div class="card" style="width:250px;">
                                                                        <img src="../productsImages/<?php echo $selectProductImagesAssoc['product_image_name']; ?>" alt="<?php echo $fetchProductNameAssoc['product_name']; ?>" height="200px" width="100px" style="object-fit:contain;" class="card-img-top mt-2">
                                                                        <div class="card-body">
                                                                            <hr style="border-top: 1px solid #1f2d3d;">
                                                                            <div class="text-center">
                                                                                <button type="button" style="width: 100px !important;" data-toggle="modal" data-target="#remove<?php echo $newNumber; ?>" class="text-nowrap text-light btn btn-sm ml-1 rounded btn-danger">
                                                                                    Delete
                                                                                    <i class="ml-1 fas fa-trash-alt"></i>
                                                                                </button>
                                                                                <!-- Modal -->
                                                                                <div class="text-left modal fade" id="remove<?php echo $newNumber; ?>" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="remove<?php echo $newNumber; ?>Label" aria-hidden="true">
                                                                                    <div class="modal-dialog modal-dialog-scrollable">
                                                                                        <div class="modal-content">
                                                                                            <div class="modal-header">
                                                                                                <h5 class="modal-title" id="remove<?php echo $newNumber; ?>Label">
                                                                                                    Remove item
                                                                                                </h5>
                                                                                            </div>
                                                                                            <form action="" method="post" onsubmit="return removeImageJSValidation()">
                                                                                                <div class="modal-body">
                                                                                                    <input type="text" name="imageNameCode" id="imageNameCode" value="<?php echo $selectProductImagesAssoc['product_code']; ?>" class="d-none" hidden>
                                                                                                    <input type="text" name="imageName" id="imageName" value="<?php echo $selectProductImagesAssoc['product_image_name']; ?>" class="d-none" hidden>
                                                                                                    Are you sure you want to delete the image below?
                                                                                                    <img src="../productsImages/<?php echo $selectProductImagesAssoc['product_image_name']; ?>" alt="<?php echo $fetchProductNameAssoc['product_name']; ?>" height="200px" width="100px" style="object-fit:contain;" class="card-img-top mt-2 mb-2">
                                                                                                </div>
                                                                                                <div class="modal-footer">
                                                                                                    <div class="container">
                                                                                                        <button type="button" name="closeModal" id="closeModal" style="width: 100px !important;float: left !important;" class="d-block text-light btn btn-sm btn-primary" data-dismiss="modal">No</button>
                                                                                                        <span style="width: 100px !important;float: right !important;" name="removeImageLoader" id="removeImageLoader" class="d-none text-light btn btn-sm btn-danger">
                                                                                                            <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                                                                                        </span>
                                                                                                        <button type="submit" name="removeImage" id="removeImage" style="width: 100px !important;float: right !important;" class="d-block text-light btn btn-sm btn-danger">Yes</button>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </form>
                                                                                            <script>
                                                                                                function removeImageJSValidation() {
                                                                                                    // Initialize a flag to track if the input is valid
                                                                                                    var is_input_valid = true;

                                                                                                    document.getElementById('removeImage').className = "d-none";
                                                                                                    document.getElementById('removeImageLoader').style = "width: 100px !important;float: right !important;";
                                                                                                    document.getElementById('removeImageLoader').className = "d-block text-light btn btn-sm btn-danger";

                                                                                                    // Return the validation result
                                                                                                    return is_input_valid;
                                                                                                }
                                                                                            </script>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                <?php } ?>
                            </div>
                        </section>
                    </div>
                    <!-- require mainfooter.php php file -->
                    <?php require 'mainfooter.php'; ?>
                </div>
                <!-- require footer.php php file -->
                <?php require 'footer.php'; ?>
                <script>
                    $(document).ready( function (){$('#products_list_table').DataTable();});
                </script>
            </body>
        </html>
<?php $dbConnection->close(); // Close the database connection ?>