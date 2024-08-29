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
    }

     // If the current HTTP request method is POST and if a form element with the name attribute set to 'checkoutPayment' is submitted as part of the POST data in an HTTP request
     if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['checkoutPayment']) == true) {
        // Define an array of available payment methods
        $paymentMethodArray = ['cash','mpesa'];

        // Use mysqli_real_escape_string to prevent SQL injection attacks by escaping special characters 
        // in the paymentMethod input, and also apply stripslashes and htmlspecialchars with ENT_QUOTES 
        // and 'UTF-8' encoding to trim and sanitize the user input from the $_POST['paymentMethod'] variable
        $paymentMethod = mysqli_real_escape_string($dbConnection, stripslashes(htmlspecialchars(trim($_POST['paymentMethod']), ENT_QUOTES, 'UTF-8')));

        // Check if the payment method is not set
        if (!isset($paymentMethod)) {
            // Redirect the user to the checkout page with an error message indicating that the payment method is not set
            header('Location: checkout?error=paymentMethodNotSet');
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        // Check if the payment method is empty
        if (empty($paymentMethod)) {
            // Redirect the user to the checkout page with an error message indicating that the payment method is empty
            header('Location: checkout?error=emptyPaymentMethod');
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        // Check if the payment method is not in the array of allowed payment methods
        if (!in_array($paymentMethod,$paymentMethodArray)) {
            // Redirect the user to the checkout page with an error message indicating that the payment method is not allowed
            header('Location: checkout?error=invalidPaymentMethod');
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        if ($paymentMethod === 'cash') {
            // Redirect the user to the checkout page with a pop-up if the $paymentMethod variable value is 'cash'
            header('Location: checkoutPayment?pay=cashOnDelivery');
            // Exit the script immediately to prevent any further execution.
            exit();
        }

        if ($paymentMethod === 'mpesa') {
            // Redirect the user to the checkoutPayment page with a pop-up if the $paymentMethod variable value is 'mpesa'
            header('Location: checkoutPayment?pay=mpesa');
            // Exit the script immediately to prevent any further execution.
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
        <title><?php echo $fetchCompanyDataAssoc['company_name']; ?> | Checkout</title>
        <?php require 'head.php'; ?>
    </head>
    <body style="margin-bottom: 160px; background-color: #dee2e6;">
        <?php require 'navbar.php'; ?>
        <section class="mt-2 container">
            <div class="bg-light p-2 rounded">
                <div class="text-left">
                    <strong>Order confirmation &amp; payment</strong>
                </div>
                <div class="mt-2">
                    Please select the mode of payment for your order.
                    <form action="" method="post" class="mt-2" onsubmit="return paymentMethodValidation()">
                        <div>
                            <?php if (isset($_GET['error'])) { ?>
                                <?php if ($_GET['error'] == 'paymentMethodNotSet') { ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        Please select a payment method.
                                        <span class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true" style="color: #721c24; cursor: pointer;">&times;</span>
                                        </span>
                                    </div>
                                <?php } elseif ($_GET['error'] == 'emptyPaymentMethod') { ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        Please select a payment method.
                                        <span class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true" style="color: #721c24; cursor: pointer;">&times;</span>
                                        </span>
                                    </div>
                                <?php } elseif ($_GET['error'] == 'invalidPaymentMethod') { ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        Please select a payment method.
                                        <span class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true" style="color: #721c24; cursor: pointer;">&times;</span>
                                        </span>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                     <span id="paymentMethodStatus" class="d-block"></span>
                        <div class="form-check-inline">
                            <input class="form-check-input" type="radio" name="paymentMethod" id="paymentMethod01" value="cash">
                            <label class="form-check-label" for="paymentMethod01">Cash on delivery</label>
                        </div>
                        <div class="form-check-inline">
                            <input class="form-check-input" type="radio" name="paymentMethod" id="paymentMethod02" value="mpesa">
                            <label class="form-check-label" for="paymentMethod02">Mpesa</label>
                        </div>
                        <button name="checkoutPayment" id="checkoutPayment" class="mt-3 mb-3 d-block text-light btn btn-sm btn-primary" style="width:115px;">Submit</button>
                        <span name="checkoutPaymentLoader" id="checkoutPaymentLoader" class="mt-3 mb-3 d-none text-light btn btn-sm btn-primary" style="width:115px;">
                            <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                        </span>
                     </form>
                     <script>
                        function paymentMethodValidation() {
                            // Set the value of is_input_valid to be true by default
                            var is_input_valid = true;

                            // Hide the checkout button and show the loader
                            document.getElementById('checkoutPayment').className = "d-none";
                            document.getElementById('checkoutPaymentLoader').style = "width:115px;";
                            document.getElementById('checkoutPaymentLoader').className = "mt-3 mb-3 d-block text-light btn btn-sm btn-primary";

                            // Set the value of checkedStatus to be false by default
                            var checkedStatus = false;
                            var radioButton = document.getElementsByName('paymentMethod');

                            // Loop through each radio button to check if any is checked
                            for (var i = 0; i < radioButton.length; i++) {
                                if (radioButton[i].checked) {
                                    var checkedValue = radioButton[i].value;
                                    checkedStatus = true;
                                    break;
                                }
                            }

                            // If the checkedStatus is false, it means no payment method is selected
                            if (checkedStatus == false) {
                                // Set is_input_valid to false to indicate invalid input
                                is_input_valid = false;
                                
                                // Display an error message to the user
                                document.getElementById('paymentMethodStatus').className = "d-block";
                                document.getElementById('paymentMethodStatus').style = "color:#dc3545";
                                document.getElementById('paymentMethodStatus').innerHTML = "Please select one payment method";
                                
                                // Hide the loader button and show the checkout button
                                document.getElementById('checkoutPayment').style = "width:115px;";
                                document.getElementById('checkoutPaymentLoader').className = "d-none";
                                document.getElementById('checkoutPayment').className = "mt-3 mb-3 d-block text-light btn btn-sm btn-primary";
                            }
                            
                            // Return the validation result
                            return is_input_valid;
                        }
                     </script>
                </div>
            </div>
        </section>
        <?php require 'footer.php'; ?>
        <?php require 'foot.php'; ?>
    </body>
</html>
<?php $dbConnection->close(); // Close the database connection ?>