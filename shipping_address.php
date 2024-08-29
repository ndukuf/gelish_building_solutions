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

    // If the current HTTP request method is POST and if a form element with the name attribute set to 'addAddress' is submitted as part of the POST data in an HTTP request
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['addAddress']) == true) {
        // Sanitize and validate user input data to prevent SQL injection and cross-site scripting (XSS) attacks
        $user_id_session = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_SESSION['user_id']),ENT_QUOTES,'UTF-8')));
        $firstName = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_POST['firstName']),ENT_QUOTES,'UTF-8')));
        $lastName = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_POST['lastName']),ENT_QUOTES,'UTF-8')));
        $stateProvince = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_POST['stateProvince']),ENT_QUOTES,'UTF-8')));
        $city = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_POST['city']),ENT_QUOTES,'UTF-8')));
        $district = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_POST['district']),ENT_QUOTES,'UTF-8')));
        $streetAddress = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_POST['streetAddress']),ENT_QUOTES,'UTF-8')));
        $phoneNumber = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_POST['phoneNumber']),ENT_QUOTES,'UTF-8')));
        $whatsappNumber = mysqli_real_escape_string($dbConnection,stripslashes(htmlspecialchars(trim($_POST['whatsappNumber']),ENT_QUOTES,'UTF-8')));
        
        if (empty($user_id_session)==true) {
            // redirect to the shipping_address page with empty_user_id_session error message
            header('Location: shipping_address?err=empty_user_id_session');
            // Exit the current script to prevent further execution
            exit();
        }

        if (empty($firstName)==true) {
            // redirect to the shipping_address page with empty_firstName error message
            header('Location: shipping_address?err=empty_firstName');
            // Exit the current script to prevent further execution
            exit();
        }

        if (empty($lastName)==true) {
            // redirect to the shipping_address page with empty_lastName error message
            header('Location: shipping_address?err=empty_lastName');
            // Exit the current script to prevent further execution
            exit();
        }

        if (empty($stateProvince)==true) {
            // redirect to the shipping_address page with empty_stateProvince error message
            header('Location: shipping_address?err=empty_stateProvince');
            // Exit the current script to prevent further execution
            exit();
        }

        if (empty($city)==true) {
            // redirect to the shipping_address page with empty_city error message
            header('Location: shipping_address?err=empty_city');
            // Exit the current script to prevent further execution
            exit();
        }

        if (empty($district)==true) {
            // redirect to the shipping_address page with empty_district error message
            header('Location: shipping_address?err=empty_district');
            // Exit the current script to prevent further execution
            exit();
        }

        if (empty($streetAddress)==true) {
            // redirect to the shipping_address page with empty_streetAddress error message
            header('Location: shipping_address?err=empty_streetAddress');
            // Exit the current script to prevent further execution
            exit();
        }

        if (empty($phoneNumber)==true) {
            // redirect to the shipping_address page with empty_phoneNumber error message
            header('Location: shipping_address?err=empty_phoneNumber');
            // Exit the current script to prevent further execution
            exit();
        }

        if (empty($whatsappNumber)==true) {
            // redirect to the shipping_address page with empty_whatsappNumber error message
            header('Location: shipping_address?err=empty_whatsappNumber');
            // Exit the current script to prevent further execution
            exit();
        }

        if (strlen($phoneNumber) < 10) {
            // redirect to the shipping_address page with invalid_phoneNumber error message
            header('Location: shipping_address?err=invalid_phoneNumber');
            // Exit the current script to prevent further execution
            exit();
        }
        
        if (strlen($whatsappNumber) < 10) {
            // redirect to the shipping_address page with invalid_whatsappNumber error message
            header('Location: shipping_address?err=invalid_whatsappNumber');
            // Exit the current script to prevent further execution
            exit();
        }

        // Generate a random salt for the address_id
        $randomCharactersSalt = 'Adrs';
            
        // Generate random characters, convert them to uppercase hexadecimal for uniqueness
        $randomCharacters01 = strtoupper(bin2hex(random_bytes(2)));
    
        // Concatenate the random characters with the predefined salt to create a unique address_id
        $address_id = $randomCharacters01.$randomCharactersSalt;

        // Prepare a SQL query to fetch address_id from the database
        $fetchShippingAddressesIdSql = 'SELECT `address_id` FROM `shipping_addresses` WHERE `address_id`=? LIMIT 1';

        // Prepare the SQL statement
        $fetchShippingAddressesIdStmt = $dbConnection->prepare($fetchShippingAddressesIdSql);

        // Bind parameters to the prepared statement
        $fetchShippingAddressesIdStmt->bind_param('s',$address_id);

        // Execute the prepared statement
        $fetchShippingAddressesIdStmt->execute();

        // Fetch the result of the prepared statement
        $fetchShippingAddressesIdResult = $fetchShippingAddressesIdStmt->get_result();

        do {
            // Generate a random salt for the address_id
            $randomCharactersSalt = 'Adrs';
            
            // Generate random characters, convert them to uppercase hexadecimal for uniqueness
            $randomCharacters01 = strtoupper(bin2hex(random_bytes(2)));
        
            // Concatenate the random characters with the predefined salt to create a unique address_id
            $address_id = $randomCharacters01.$randomCharactersSalt;
        } while ($fetchShippingAddressesIdResult->num_rows > 0); // Loop until a unique address_id is generated, ensuring no duplicates


        // Prepare a SQL statement to add new adress to the 'shipping_addresses' table
        $addNewAddressSql = 'INSERT INTO `shipping_addresses`(`address_id`,`user_id`,`FirstName`,`LastName`,`State/Province`,`City`,`District`,`StreetAddress`,`PhoneNumber`,`WhatsappNumber`,`dateAdded`) VALUES (?,?,?,?,?,?,?,?,?,?,?)';

        // Prepare the SQL statement for execution
        $addNewAddressStmt = $dbConnection->prepare($addNewAddressSql);

        // Convert the date-time object to a string format of 'Y-m-d H:i:s'
        $dateAdded = date('Y-m-d H:i:s', strtotime($currentDateAndTime));

        // Bind the user ID to the prepared statement
        $addNewAddressStmt->bind_param('sssssssssss', $address_id,$user_id_session,$firstName,$lastName,$stateProvince,$city,$district,$streetAddress,$phoneNumber,$whatsappNumber,$dateAdded);

        // Execute the prepared SQL statement
        if ($addNewAddressStmt->execute()) {
            $_SESSION['addNewAddressStatus'] = 'addNewAddressAdded';
            // redirect to the shipping_address page
            header('Location: shipping_address');
            // Exit the current script to prevent further execution
            exit();
        } else {
            $_SESSION['addNewAddressStatus'] = 'addNewAddressNotAdded';
            // redirect to the shipping_address page
            header('Location: shipping_address');
            // Exit the current script to prevent further execution
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
        <title><?php echo htmlspecialchars_decode($fetchCompanyDataAssoc['company_name']); ?> | Cart</title>
        <?php require_once 'head.php'; ?>
    </head>
    <body style="margin-bottom: 160px; background-color: #dee2e6;">
        <?php require_once 'navbar.php'; ?>
        <section class="mt-2 container">
            <div class="bg-light p-2 rounded">
                <div class="text-center">
                    <strong>Shipping address</strong>
                </div>
                <?php
                    // Prepare a SQL statement to fetch adresses from the 'shipping_addresses' table
                    $fetchSippingAddressesSql = 'SELECT * FROM `shipping_addresses` WHERE `user_id`=?';

                    // Prepare the SQL statement for execution
                    $fetchSippingAddressesStmt = $dbConnection->prepare($fetchSippingAddressesSql);

                    // Bind the user ID to the prepared statement
                    $fetchSippingAddressesStmt->bind_param('s', $_SESSION['user_id']);

                    // Execute the prepared SQL statement
                    $fetchSippingAddressesStmt->execute();

                    // Fetch the result set from the executed SQL statement
                    $fetchSippingAddressesResult = $fetchSippingAddressesStmt->get_result();

                    // Close the $fetchSippingAddressesStmt prepared statement to free up resources
                    $fetchSippingAddressesStmt->close();
                ?>
                <div class="card">
                    <div class="card-header p-2">
                        <div>
                            <button type="button" class="text-nowrap btn btn-sm btn-primary text-nowrap font-weight-bold" data-toggle="modal" data-target="#addShippingAddress">
                                Add shipping address
                            </button>
                        </div>
                        <div class="modal fade" id="addShippingAddress" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="addShippingAddressLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addShippingAddressLabel">Shipping address</h5>
                                        <span class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </span>
                                    </div>
                                    <form action="" method="post" onsubmit="return addSippingAddressJsValidation();">
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 mb-3">
                                                    <label for="firstName" class="control-label">First name<span class="text-danger">*</span></label>
                                                    <span id="firstNameStatus" class="d-block"></span>
                                                    <div>
                                                        <input type="text" name="firstName" id="firstName" placeholder="First name" class="form-control" autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 mb-3">
                                                    <label for="lastName" class="control-label">Last name<span class="text-danger">*</span></label>
                                                    <span id="lastNameStatus" class="d-block"></span>
                                                    <div>
                                                        <input type="text" name="lastName" id="lastName" placeholder="Last name" class="form-control" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="stateProvince" class="control-label">State/Province<span class="text-danger">*</span></label>
                                                <span id="stateProvinceStatus" class="d-block"></span>
                                                <div>
                                                    <input type="text" name="stateProvince" id="stateProvince" placeholder="State/Province" class="form-control" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 mb-3">
                                                    <label for="city" class="control-label">City<span class="text-danger">*</span></label>
                                                    <span id="cityStatus" class="d-block"></span>
                                                    <div>
                                                        <input type="text" name="city" id="city" placeholder="City" class="form-control" autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 mb-3">
                                                    <label for="district" class="control-label">District<span class="text-danger">*</span></label>
                                                    <span id="districtStatus" class="d-block"></span>
                                                    <div>
                                                        <input type="text" name="district" id="district" placeholder="District" class="form-control" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="streetAddress" class="control-label">Street address<span class="text-danger">*</span></label>
                                                <span id="streetAddressStatus" class="d-block"></span>
                                                <div>
                                                    <input type="text" name="streetAddress" id="streetAddress" placeholder="Street address" class="form-control" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="phoneNumber" class="control-label">Phone number<span class="text-danger">*</span></label>
                                                <span id="phoneNumberStatus" class="d-block"></span>
                                                <div>
                                                    <input type="tel" name="phoneNumber" id="phoneNumber" placeholder="Phone number" class="form-control" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="whatsappNumber" class="control-label">Whatsapp number<span class="text-danger">*</span></label>
                                                <span id="whatsappNumberStatus" class="d-block"></span>
                                                <div>
                                                    <input type="tel" name="whatsappNumber" id="whatsappNumber" placeholder="Whatsapp number" class="form-control" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="">
                                                <div class="">
                                                    <button type="submit" name="addAddress" id="addAddress" style="width: 120px;" class="d-block text-nowrap btn btn-primary btn-block">Add address</button>
                                                    <button type="button" name="addAddressLoader" id="addAddressLoader" style="width: 120px;" class="d-none text-nowrap btn btn-primary btn-block">
                                                        <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <script>
                                        function addSippingAddressJsValidation() {
                                            // Set the value of is_input_valid to be true by default
                                            var is_input_valid = true;

                                            document.getElementById('addAddress').className = 'd-none';
                                            document.getElementById('addAddressLoader').style = 'width: 120px';
                                            document.getElementById('addAddressLoader').className = 'd-block text-nowrap btn btn-primary btn-block';

                                            // If the value firstName is empty show the error message
                                            if (document.getElementById('firstName').value === '') {
                                                is_input_valid = false;
                                                document.getElementById('firstName').style.border = '1px solid #dc3545';
                                                document.getElementById('firstNameStatus').style.color = '#dc3545';
                                                document.getElementById('firstNameStatus').innerHTML = 'The field is required';

                                                document.getElementById('addAddressLoader').className = 'd-none';
                                                document.getElementById('addAddress').className = 'd-block text-nowrap btn btn-primary btn-block';
                                            } else {
                                                document.getElementById('firstName').style.border = '1px solid #28a745';
                                                document.getElementById('firstNameStatus').innerHTML = '';
                                            }

                                             // If the value lastName is empty show the error message
                                             if (document.getElementById('lastName').value === '') {
                                                is_input_valid = false;
                                                document.getElementById('lastName').style.border = '1px solid #dc3545';
                                                document.getElementById('lastNameStatus').style.color = '#dc3545';
                                                document.getElementById('lastNameStatus').innerHTML = 'The field is required';

                                                document.getElementById('addAddressLoader').className = 'd-none';
                                                document.getElementById('addAddress').className = 'd-block text-nowrap btn btn-primary btn-block';
                                            } else {
                                                document.getElementById('lastName').style.border = '1px solid #28a745';
                                                document.getElementById('lastNameStatus').innerHTML = '';
                                            }

                                            // If the value stateProvince is empty show the error message
                                            if (document.getElementById('stateProvince').value === '') {
                                                is_input_valid = false;
                                                document.getElementById('stateProvince').style.border = '1px solid #dc3545';
                                                document.getElementById('stateProvinceStatus').style.color = '#dc3545';
                                                document.getElementById('stateProvinceStatus').innerHTML = 'The field is required';

                                                document.getElementById('addAddressLoader').className = 'd-none';
                                                document.getElementById('addAddress').className = 'd-block text-nowrap btn btn-primary btn-block';
                                            } else {
                                                document.getElementById('stateProvince').style.border = '1px solid #28a745';
                                                document.getElementById('stateProvinceStatus').innerHTML = '';
                                            }

                                            // If the value city is empty show the error message
                                            if (document.getElementById('city').value === '') {
                                                is_input_valid = false;
                                                document.getElementById('city').style.border = '1px solid #dc3545';
                                                document.getElementById('cityStatus').style.color = '#dc3545';
                                                document.getElementById('cityStatus').innerHTML = 'The field is required';

                                                document.getElementById('addAddressLoader').className = 'd-none';
                                                document.getElementById('addAddress').className = 'd-block text-nowrap btn btn-primary btn-block';
                                            } else {
                                                document.getElementById('city').style.border = '1px solid #28a745';
                                                document.getElementById('cityStatus').innerHTML = '';
                                            }

                                            // If the value district is empty show the error message
                                            if (document.getElementById('district').value === '') {
                                                is_input_valid = false;
                                                document.getElementById('district').style.border = '1px solid #dc3545';
                                                document.getElementById('districtStatus').style.color = '#dc3545';
                                                document.getElementById('districtStatus').innerHTML = 'The field is required';

                                                document.getElementById('addAddressLoader').className = 'd-none';
                                                document.getElementById('addAddress').className = 'd-block text-nowrap btn btn-primary btn-block';
                                            } else {
                                                document.getElementById('district').style.border = '1px solid #28a745';
                                                document.getElementById('districtStatus').innerHTML = '';
                                            }

                                            // If the value streetAddress is empty show the error message
                                            if (document.getElementById('streetAddress').value === '') {
                                                is_input_valid = false;
                                                document.getElementById('streetAddress').style.border = '1px solid #dc3545';
                                                document.getElementById('streetAddressStatus').style.color = '#dc3545';
                                                document.getElementById('streetAddressStatus').innerHTML = 'The field is required';

                                                document.getElementById('addAddressLoader').className = 'd-none';
                                                document.getElementById('addAddress').className = 'd-block text-nowrap btn btn-primary btn-block';
                                            } else {
                                                document.getElementById('streetAddress').style.border = '1px solid #28a745';
                                                document.getElementById('streetAddressStatus').innerHTML = '';
                                            }

                                            // If the value phoneNumber is empty show the error message
                                            if (document.getElementById('phoneNumber').value === '') {
                                                is_input_valid = false;
                                                document.getElementById('phoneNumber').style.border = '1px solid #dc3545';
                                                document.getElementById('phoneNumberStatus').style.color = '#dc3545';
                                                document.getElementById('phoneNumberStatus').innerHTML = 'The field is required';

                                                document.getElementById('addAddressLoader').className = 'd-none';
                                                document.getElementById('addAddress').className = 'd-block text-nowrap btn btn-primary btn-block';
                                            } else {
                                                document.getElementById('phoneNumber').style.border = '1px solid #28a745';
                                                document.getElementById('phoneNumberStatus').innerHTML = '';
                                            }

                                            // If the value whatsappNumber is empty show the error message
                                            if (document.getElementById('whatsappNumber').value === '') {
                                                is_input_valid = false;
                                                document.getElementById('whatsappNumber').style.border = '1px solid #dc3545';
                                                document.getElementById('whatsappNumberStatus').style.color = '#dc3545';
                                                document.getElementById('whatsappNumberStatus').innerHTML = 'The field is required';

                                                document.getElementById('addAddressLoader').className = 'd-none';
                                                document.getElementById('addAddress').className = 'd-block text-nowrap btn btn-primary btn-block';
                                            } else {
                                                document.getElementById('whatsappNumber').style.border = '1px solid #28a745';
                                                document.getElementById('whatsappNumberStatus').innerHTML = '';
                                            }

                                            // Return the value of is_input_valid
                                            return is_input_valid;
                                        }
                                    </script>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-2">
                        <?php if ($fetchSippingAddressesResult->num_rows>=1) { ?>
                            <div class="row">
                                <?php while ($fetchSippingAddressesAssoc= $fetchSippingAddressesResult->fetch_assoc()){ ?>
                                    <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6 mb-2">
                                        <div class="card p-2">
                                            <?php echo htmlspecialchars_decode($fetchSippingAddressesAssoc['District']); ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php }else{ ?>
                            No adresses set.
                        <?php } ?>
                    </div>
                </div>
            </div>
        </section>
        <?php require_once 'footer.php'; ?>
        <?php require_once 'foot.php'; ?>
    </body>
</html>
<?php $dbConnection->close(); // Close the database connection ?>