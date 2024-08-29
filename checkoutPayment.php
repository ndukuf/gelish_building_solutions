<?php
    /* Require necessary files for the application to function properly. */
    //establishes a connection to the database.
    require 'database_connection.php';

    //contains company-related data.
    require 'company_data.php';

    //provides the current date and time.
    require 'current_date_and_time.php';

    //contains currency_prefix data.
    require 'currency_prefix.php';

    // Check if the 'user_id' session variable is not set or empty
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        // If the condition is true, redirect the user to the 'index' page with a 'sign_in' parameter
        header('Location: index?sign_in');
        // Exit the script to prevent further execution
        exit();
    } else {
        // Retrieve the user ID from the session, and sanitize it to prevent SQL injection
        $user_id = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_SESSION['user_id']), ENT_QUOTES, 'UTF-8')));

        // Prepare a SQL query to fetch user data from the database
        $fetchUserSystemAccessSql = 'SELECT `system_access` FROM `users` WHERE `user_id`=? LIMIT 1';

        // Prepare the SQL statement
        $fetchUserSystemAccessStmt = $dbConnection->prepare($fetchUserSystemAccessSql);

        // Bind parameters to the prepared statement
        $fetchUserSystemAccessStmt->bind_param('s',$user_id);

        // Execute the prepared statement
        $fetchUserSystemAccessStmt->execute();

        // Retrieve the result set
        $fetchUserSystemAccessResult = $fetchUserSystemAccessStmt->get_result();

        // Close the $fetchUserSystemAccessStmt prepared statement to free up resources
        $fetchUserSystemAccessStmt->close();

        // Fetch data as an associative array
        $fetchUserSystemAccessAssoc= $fetchUserSystemAccessResult->fetch_assoc();

        // Check if the row does not exist or the fetched system_access is not identical to string 5
        if ($fetchUserSystemAccessResult->num_rows!==1 || strval($fetchUserSystemAccessAssoc['system_access']) !== '5') {
            //establishes a connection sign_out php page.
            require 'sign_out.php';
        }

        $selectTotalPriceSql = 'SELECT SUM(`product_total_selling_price`) AS `product_total_selling_price` FROM `cart` WHERE `user_id`=?';
        // Prepare the SQL statement
        $selectTotalPriceStmt = $dbConnection->prepare($selectTotalPriceSql);
        
        // Bind the current user's user_id session variable to the SQL SELECT statement
        $selectTotalPriceStmt->bind_param('s', $user_id);

        // Execute the prepared statement
        $selectTotalPriceStmt->execute();

        // Retrieve the result set
        $selectTotalPriceResult = $selectTotalPriceStmt->get_result();
        
        // Close the prepared statement to free up resources
        $selectTotalPriceStmt->close();

        // Fetch data as an associative array
        $selectTotalPriceAssoc= $selectTotalPriceResult->fetch_assoc();

        $price = strval(abs($selectTotalPriceAssoc['product_total_selling_price']));
    }

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'confirmCashOrder' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['confirmCashOrder']) == true) {
        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters 
        // in the phoneNumber input, and also apply stripslashes and htmlspecialchars with ENT_QUOTES 
        // and 'UTF-8' encoding to trim and sanitize the user input from the $_POST['phoneNumber'] variable
        $phoneNumber = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['phoneNumber']), ENT_QUOTES, 'UTF-8')));

        // Check if the phone number is not set
        if (!isset($phoneNumber)) {
            // Redirect the user to the checkout page with an error message indicating that the phone number is not set
            header('Location: checkoutPayment?pay=cashOnDelivery&error=phoneNumberNotSet');
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        // Check if the phone number is empty
        if (empty($phoneNumber)) {
            // Redirect the user to the checkout page with an error message indicating that the phone number is empty
            header('Location: checkoutPayment?pay=cashOnDelivery&error=emptyPhoneNumber');
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        // Check for invalid cash amount paid or phone number length
        if (!is_numeric($phoneNumber) || strlen($phoneNumber) < 10) {
            // If validation fails, redirect to checkout payment page with an error message
            header('Location: checkoutPayment?pay=cashOnDelivery&error=invalidPhoneNumber');
            // Exit the script immediately to prevent further execution
            exit();
        }

        $status = 'awaiting';
        $selectAwaitingPaymentSql = 'SELECT `user_id` FROM `cash_on_delivery` WHERE `user_id`=? AND `status`=?';
        // Prepare the SQL statement
        $selectAwaitingPaymentStmt = $dbConnection->prepare($selectAwaitingPaymentSql);
        
        // Bind the current user's user_id session variable to the SQL SELECT statement
        $selectAwaitingPaymentStmt->bind_param('ss', $_SESSION['user_id'],$status);

        // Execute the prepared statement
        $selectAwaitingPaymentStmt->execute();

        // Retrieve the result set
        $selectAwaitingPaymentResult = $selectAwaitingPaymentStmt->get_result();
        
        // Close the prepared statement to free up resources
        $selectAwaitingPaymentStmt->close();
        if ($selectAwaitingPaymentResult->num_rows > 0 ) {
            // Redirect to checkout payment page with an error message
            header('Location: checkoutPayment?pay=cashOnDelivery&error=completePayment');
            // Exit the current script to prevent further execution
            exit();
        }

        // Prepare a SQL statement to add the product to the 'cashOnDelivery' table where the user_id matches the provided user_id
        $addCashOnDeliverySql = 'INSERT INTO `cash_on_delivery`(`user_id`, `product_code`, `quantity`, `product_selling_price`, `product_total_selling_price`, `date`)
        SELECT `user_id`, `product_code`, `quantity`, `product_selling_price`, `product_total_selling_price`, `date` FROM `cart` WHERE `user_id`=?';
            
        // Prepare the SQL statement
        $addCashOnDeliveryStmt = $dbConnection->prepare($addCashOnDeliverySql);

        // Convert the date-time object to a string format of 'Y-m-d H:i:s'
        $date = date('Y-m-d H:i:s', strtotime($currentDateAndTime));

        // Bind the product_code variable to the prepared statement as a string
        $addCashOnDeliveryStmt->bind_param('s', $_SESSION['user_id']);

        // Execute the prepared statement
        if ($addCashOnDeliveryStmt->execute()) {
            $date = date('Y-m-d H:i:s', strtotime($currentDateAndTime));
            $status = 'awaiting';
            // SQL update query
            $updateDeliverySql = 'UPDATE `cash_on_delivery` SET `date`=?,`phone`=?,`status`=? WHERE `user_id`=?';

            // Prepare the SQL statement for execution
            $updateDeliveryStmt = $dbConnection->prepare($updateDeliverySql);

            // Bind the current user's id and other values parameter to the prepared statement
            $updateDeliveryStmt->bind_param('ssss', $date,$phoneNumber,$status,$_SESSION['user_id']);

            // Execute the prepared statement to perform the query on the database
            if ($updateDeliveryStmt->execute()) {
                // SQL update query
                $clearCartSql = 'DELETE FROM `cart` WHERE `user_id`=?';

                // Prepare the SQL statement for execution
                $clearCartStmt = $dbConnection->prepare($clearCartSql);

                // Bind the current user's id and other values parameter to the prepared statement
                $clearCartStmt->bind_param('s', $_SESSION['user_id']);

                // Execute the prepared statement to perform the query on the database
                if ($clearCartStmt->execute()) {
                    // redirect
                    header('Location: awaiting_cash_payment');
                    // Exit the current script to prevent further execution
                    exit();
                } else {
                    // redirect
                    header('Location: index');
                    // Exit the current script to prevent further execution
                    exit();
                }
            }
        } else {
            // redirect
            header('Location: index');
            // Exit the current script to prevent further execution
            exit();
        }
    }

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'submitMpesaNumber' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submitMpesaNumber']) == true) {
        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters 
        // in the phoneNumber input, and also apply stripslashes and htmlspecialchars with ENT_QUOTES 
        // and 'UTF-8' encoding to trim and sanitize the user input from the $_POST['phoneNumber'] variable
        $phoneNumber = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['phoneNumber']), ENT_QUOTES, 'UTF-8')));

        // Check if the phone number is not set
        if (!isset($phoneNumber)) {
            // Redirect the user to the checkout page with an error message indicating that the phone number is not set
            header('Location: checkoutPayment?pay=mpesa&error=phoneNumberNotSet');
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        // Check if the phone number is empty
        if (empty($phoneNumber)) {
            // Redirect the user to the checkout page with an error message indicating that the phone number is empty
            header('Location: checkoutPayment?pay=mpesa&error=emptyPhoneNumber');
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        // Check for invalid cash amount paid or phone number length
        if (!is_numeric($phoneNumber) || strlen($phoneNumber) < 10) {
            // If validation fails, redirect to checkout payment page with an error message
            header('Location: checkoutPayment?pay=mpesa&error=invalidPhoneNumber');
            // Exit the script immediately to prevent further execution
            exit();
        }
        // Initialize a variable $code with the value '254'
        $code = '254';

        // Concatenate the $code with a substring of $phoneNumber, starting from the second character (index 1)
        $phoneNumber = strval($code.substr($phoneNumber, 1));

        // Define the consumer key for the app
        $consumerKey = "rSa2tZpC1oq1lSmHDkTeJ0OvbjxF647tAgBn9vTA712xUxG0";
        
        // Define the consumer secret for the app
        $consumerSecret = "9d2fLRfXOq3EtXLjS4cbnXulg0wvSBA6uNrQ8cn1YmoJNV1WcGey7mvqMAtvGVWx";
        
        // Define the URL for generating an access token
        $access_token_url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        
        // Define the headers for the HTTP request
        $headers = ['Content-Type:application/json; charset=utf8'];
        
        // Initialize a new cURL session
        $curl = curl_init($access_token_url);
        
        // Set the HTTP headers for the request
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        
        // Set the option to return the response as a string
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        
        // Set the option to exclude the header from the response
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        
        // Set the option to use the consumer key and secret for authentication
        curl_setopt($curl, CURLOPT_USERPWD, $consumerKey . ':' . $consumerSecret);
        
        // Execute the cURL request
        $result = curl_exec($curl);
        
        // Get the HTTP status code of the response
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // echo $result;
    
        // Decode the JSON response
        $result = json_decode($result);
        
        // Check if the access token is set in the response
        if (isset($result->access_token)==false) {
            // If validation fails, redirect to checkout payment page with an error message
            header('Location: checkoutPayment?pay=mpesa&error=accessTkn');
            // Exit the script immediately to prevent further execution
            exit();
        } else {
            // The value of access token
            $access_token = $result->access_token;
            // echo 'The access token is: <strong>'.$access_token.'</strong><br>';
        }

        // Close the cURL session
        curl_close($curl);

        // Set the default timezone to Nairobi
        date_default_timezone_set('Africa/Nairobi');

        // Define the URL for processing the request
        $processrequestUrl = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

        // Define the callback URL
        $callbackurl = 'https://victormunandi.com/GelishBuildingSolutionsDarajaApi/callback.php';

        // Define the passkey
        $passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
        
        // Define the business short code
        $BusinessShortCode = '174379';
        
        // Generate the timestamp
        $Timestamp = date('YmdHis');
        
        // Encrypt data to get the password
        $Password = base64_encode($BusinessShortCode . $passkey . $Timestamp);
        
        // Define the phone number to receive the STK push
        $phone = $phoneNumber;
        
        // Define the amount of money to be transacted
        $money = $price;
        
        // Define the party A (the customer's phone number)
        $PartyA = $phone;
        
        // Define the party B (the business's phone number)
        $PartyB = '254708374149';
        
        // Define the account reference
        $AccountReference = 'Gelish Building Solutions';
        
        // Define the transaction description
        $TransactionDesc = 'Payment for invoice';
        
        // Define the amount to be transacted
        $Amount = $money;
        
        // Define the headers for the STK push request
        $stkpushheader = ['Content-Type:application/json', 'Authorization:Bearer ' . $access_token];
        
        // Initialize cURL
        $curl = curl_init();
        
        // Set the URL for the cURL request
        curl_setopt($curl, CURLOPT_URL, $processrequestUrl);
        
        // Set the headers for the cURL request
        curl_setopt($curl, CURLOPT_HTTPHEADER, $stkpushheader);
        
        // Define the data to be sent in the cURL request
        $curl_post_data = array(
            //Fill in the request parameters with valid values
            'BusinessShortCode' => $BusinessShortCode,
            'Password' => $Password,
            'Timestamp' => $Timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $Amount,
            'PartyA' => $PartyA,
            'PartyB' => $BusinessShortCode,
            'PhoneNumber' => $PartyA,
            'CallBackURL' => $callbackurl,
            'AccountReference' => $AccountReference,
            'TransactionDesc' => $TransactionDesc
        );
        
        // Encode the data as a JSON string
        $data_string = json_encode($curl_post_data);
        
        // Set the cURL options for the request
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        // Execute the cURL request
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $curl_response = curl_exec($curl);
        
        // Decode the response from the cURL request
        $data = json_decode($curl_response);
        
        // Check if the checkout request ID is set in the response
        if (isset($data->CheckoutRequestID)==false) {
            // If validation fails, redirect to checkout payment page with an error message
            header('Location: checkoutPayment?pay=mpesa&error=checkoutID');
            // Exit the script immediately to prevent further execution
            exit();
        }
        // Check if the response code is set in the response
        if (isset($data->ResponseCode)==false) {
            // If validation fails, redirect to checkout payment page with an error message
            header('Location: checkoutPayment?pay=mpesa&error=responseCode');
            // Exit the script immediately to prevent further execution
            exit();
        }
        
        // Get the CheckoutRequestID and ResponseCode from the response
        $CheckoutRequestID = $data->CheckoutRequestID;
        
        // Check if the response code is 0, indicating a successful request
        $ResponseCode = $data->ResponseCode;
        
        if ($ResponseCode == "0") {
            // 'The checkout request id for this transaction is : <strong>'.$CheckoutRequestID.'</strong>';
            // If validation fails, redirect to checkout payment page with an error message
            header('Location: checkoutPayment?pay=mpesa&success=sent&checkoutID='.$CheckoutRequestID);
            // Exit the script immediately to prevent further execution
            exit();
        }
    }
?>
<!-- Declare the document type as HTML -->
<!DOCTYPE html>
<!-- Specify the language of the document as English -->
<html lang="en">
    <!-- Head section of the HTML document -->
    <head>
        <title><?php echo $fetchCompanyDataAssoc['company_name']; ?> | Cart</title>
        <?php require_once 'head.php'; ?>
    </head>
    <body style="margin-bottom: 160px; background-color: #dee2e6;">
        <?php require_once 'navbar.php'; ?>
        <section class="mt-2 container">
            <div class="bg-light p-2 rounded mx-auto col-12-sm col-12-md col-6-lg col-6-xl">
                <div class="text-left">
                    <p class="text-dark">Please enter &amp; confirm your M-Pesa payment details</p>
                </div>
                <div class="mt-2">
                    <?php if (isset($_GET['pay']) && $_GET['pay'] == 'cashOnDelivery') { ?>
                        The cash amount to be paid at the shop is KES <?php echo number_format($selectTotalPriceAssoc['product_total_selling_price'],2); ?><br>
                        We hope to see soon.
                        Please confirm your order.
                        <form action="" method="post" onsubmit="return cashPaymentValidation()">
                            <label for="phoneNumber"></label>
                            <span id="phoneNumberStatus"></span>
                            <input type="tel" class="form-control form-control-sm" name="phoneNumber" id="phoneNumber" placeholder="e.g: 0712345678" autofocus autocomplete="off">
                            <div>
                                <button type="submit" name="confirmCashOrder" id="confirmCashOrder" class="mt-3 mb-3 d-block text-light btn btn-sm btn-primary">Confirm order</button>
                                <button type="button" name="confirmCashOrderLoader" id="confirmCashOrderLoader" class="mt-3 mb-3 d-none text-light btn btn-sm btn-primary" style="width:100px; height:30px;">
                                    <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                </button>
                            </div>
                        </form>
                        <script>
                            function cashPaymentValidation() {
                                // Initialize a flag to track if the input is valid
                                var is_input_valid = true;

                                document.getElementById('confirmCashOrder').className = 'd-none';
                                document.getElementById('confirmCashOrderLoader').style = 'width:100px; height:30px;';
                                document.getElementById('confirmCashOrderLoader').className = 'mt-3 mb-3 d-block text-light btn btn-sm btn-primary';

                                // Check if the phone number is empty
                                if (document.getElementById('phoneNumber').value === '') {
                                    // If empty, set the flag to false and display an error message
                                    is_input_valid = false;
                                    document.getElementById('phoneNumber').focus();
                                    document.getElementById('phoneNumberStatus').className = 'd-block';
                                    document.getElementById('phoneNumberStatus').style = 'color:#dc3545';
                                    document.getElementById('phoneNumberStatus').innerHTML = 'Please enter the phone number';
                                    document.getElementById('phoneNumber').style = 'border: 1px solid #dc3545;';
                                    document.getElementById('confirmCashOrderLoader').className = 'd-none';
                                    document.getElementById('confirmCashOrder').className = 'mt-3 mb-3 d-block text-light btn btn-sm btn-primary';
                                } else if (/^\d+(\.\d+)?$/.test(document.getElementById('phoneNumber').value) === false || document.getElementById('phoneNumber').value.length !== 10) {
                                    // If not a valid number, set the flag to false and display an error message
                                    is_input_valid = false;
                                    document.getElementById('phoneNumber').focus();
                                    document.getElementById('phoneNumberStatus').className = 'd-block';
                                    document.getElementById('phoneNumberStatus').style = 'color:#dc3545';
                                    document.getElementById('phoneNumberStatus').innerHTML = 'Please enter a valid phone number';
                                    document.getElementById('phoneNumber').style = 'border: 1px solid #dc3545;';
                                    document.getElementById('confirmCashOrderLoader').className = 'd-none';
                                    document.getElementById('confirmCashOrder').className = 'mt-3 mb-3 d-block text-light btn btn-sm btn-primary';
                                }

                                // Return the validation result
                                return is_input_valid;
                            }
                        </script>
                    <?php } elseif (isset($_GET['pay']) && $_GET['pay'] == 'mpesa') { ?>
                        <form action="" method="post" onsubmit="return mpesaPaymentValidation()">
                            <div>
                                <?php if (isset($_GET['error'])) { ?>
                                    <?php if ($_GET['error'] == 'phoneNumberNotSet') { ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            Please enter your phone number
                                            <span class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true" style="color: #721c24; cursor: pointer;">&times;</span>
                                            </span>
                                        </div>
                                    <?php } elseif ($_GET['error'] == 'emptyPhoneNumber') { ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            Please enter your phone number
                                            <span class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true" style="color: #721c24; cursor: pointer;">&times;</span>
                                            </span>
                                        </div>
                                    <?php } elseif ($_GET['error'] == 'invalidPhoneNumber') { ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            Please enter a valid phone number
                                            <span class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true" style="color: #721c24; cursor: pointer;">&times;</span>
                                            </span>
                                        </div>
                                    <?php } elseif ($_GET['error'] == 'accessTkn') { ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            The access token is not set. Please try again.
                                            <span class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true" style="color: #721c24; cursor: pointer;">&times;</span>
                                            </span>
                                        </div>
                                    <?php } elseif ($_GET['error'] == 'checkoutID') { ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            The checkout request id is not set. Please try again.
                                            <span class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true" style="color: #721c24; cursor: pointer;">&times;</span>
                                            </span>
                                        </div>
                                    <?php } elseif ($_GET['error'] == 'responseCode') { ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            The response code is not set. Please try again.
                                            <span class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true" style="color: #721c24; cursor: pointer;">&times;</span>
                                            </span>
                                        </div>
                                    <?php } ?>
                                <?php } elseif (isset($_GET['success'])) { ?>
                                        <?php if ($_GET['success'] == 'sent') { ?>
                                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                Request sent successfully. Please enter your M-PESA PIN in the prompt!
                                                <span class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true" style="color: #721c24; cursor: pointer;">&times;</span>
                                                </span>
                                            </div>
                                        <?php } ?>
                                <?php } ?>
                            </div>
                            <label class="mt-2 mb-0 text-dark text-sm font-weight-bold" for="phoneNumber">Phone number</label>
                            <span id="phoneNumberStatus"></span>
                            <input type="tel" class="form-control form-control-sm" name="phoneNumber" id="phoneNumber" placeholder="e.g: 0712345678" autofocus autocomplete="off">
                            <label class="mt-2 mb-0 text-dark text-sm font-weight-bold" for="amount">Amount</label>
                            <input type="tel" class="form-control form-control-sm" name="amount" id="amount" placeholder="Amount" value="<?php echo number_format($selectTotalPriceAssoc['product_total_selling_price'],2); ?>" disabled autocomplete="off">
                            <div>
                                <button type="submit" name="submitMpesaNumber" id="submitMpesaNumber" class="mt-3 mb-3 d-block text-light btn btn-block btn-primary">Submit</button>
                                <span name="submitMpesaNumberLoader" id="submitMpesaNumberLoader" class="mt-3 mb-3 d-none text-light btn btn-block btn-primary">
                                    <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                </span>
                            </div>
                        </form>
                        <script>
                            function mpesaPaymentValidation() {
                                // Initialize a flag to track if the input is valid
                                var is_input_valid = true;

                                document.getElementById('submitMpesaNumber').className = "d-none";
                                document.getElementById('submitMpesaNumberLoader').className = "mt-3 mb-3 d-block text-light btn btn-block btn-primary";

                                // Check if the phone number is empty
                                if (document.getElementById('phoneNumber').value === '') {
                                    // If empty, set the flag to false and display an error message
                                    is_input_valid = false;
                                    document.getElementById('phoneNumberStatus').className = "d-block";
                                    document.getElementById('phoneNumberStatus').style = "color:#dc3545";
                                    document.getElementById('phoneNumberStatus').innerHTML = 'Please enter the phone number';
                                    document.getElementById('phoneNumber').style = 'border: 1px solid #dc3545;';
                                    document.getElementById('submitMpesaNumber').className = "mt-3 mb-3 d-block text-light btn btn-block btn-primary";
                                    document.getElementById('submitMpesaNumberLoader').className = "d-none";
                                } else if (/^\d+(\.\d+)?$/.test(document.getElementById('phoneNumber').value) === false || document.getElementById('phoneNumber').value.length !== 10) {
                                    // If not a valid number, set the flag to false and display an error message
                                    is_input_valid = false;
                                    document.getElementById('phoneNumberStatus').className = "d-block";
                                    document.getElementById('phoneNumberStatus').style = "color:#dc3545";
                                    document.getElementById('phoneNumberStatus').innerHTML = 'Please enter a valid phone number';
                                    document.getElementById('phoneNumber').style = 'border: 1px solid #dc3545;';
                                    document.getElementById('submitMpesaNumber').className = "mt-3 mb-3 d-block text-light btn btn-block btn-primary";
                                    document.getElementById('submitMpesaNumberLoader').className = "d-none";
                                }

                                // Return the validation result
                                return is_input_valid;
                            }
                        </script>
                    <?php } ?>
                </div>
            </div>
        </section>
        <?php require_once 'footer.php'; ?>
        <?php require_once 'foot.php'; ?>
    </body>
</html>
<?php $dbConnection->close(); // Close the database connection ?>