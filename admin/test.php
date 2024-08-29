<?php require 'dbconnection.php'; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Web page description. -->
        <meta name="description" content="Gelish Building Solutions project mpesa payments">
        <!-- Keywords for search engines. -->
        <meta name="keywords" content="Gelish Building Solutions project mpesa payments">
        <!-- Author of web page. -->
        <meta name="author" content="Gelish Building Solutions project Victor Munandi Mulinge, victormunandi.com">
        <!-- Make web page look good on all devices. -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
        <title>Gelish Building Solutions | M-pesa payments</title>
    </head>
    <body>
        <?php
            // initial number
            $listNumber = 1;

            // Prepare a SQL query to fetch user data from the database
            $fetchMpesaPaymenysSql = 'SELECT * FROM `gelish_mpesa_payments` ORDER BY `TransactioDate` ASC';

            // Prepare the SQL statement
            $fetchMpesaPaymenysStmt = $dbconnection->prepare($fetchMpesaPaymenysSql);

            // Execute the prepared statement
            $fetchMpesaPaymenysStmt->execute();

            // Retrieve the result set
            $fetchMpesaPaymenysResult = $fetchMpesaPaymenysStmt->get_result();

            // Close the $fetchMpesaPaymenysStmt prepared statement to free up resources
            $fetchMpesaPaymenysStmt->close();

            if ($fetchMpesaPaymenysResult->num_rows < 1 ) {
        ?>
            No data            
        <?php } else { ?>
            <div style="margin-top: 10%;" class="container">
                <div class="p-2 card">
                    <div class="table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl">
                        <table class="table table-dark table-hover table-striped" id="GelishBuildingSolutionsMpesaPayments">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Mpesa receipt number</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Transaction date</th>
                                    <th scope="col">Phone number</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while (/*Fetch data as an associative array */ $fetchMpesaPaymenysAssoc= $fetchMpesaPaymenysResult->fetch_assoc()){ ?>
                                    <tr>
                                        <th class="text-center">
                                            <?php
                                                $count=$listNumber++;
                                                if ($count<10){echo '0'.$count;}
                                                else{echo $count;}
                                            ?>
                                        </th>
                                        <td>
                                            <?php if (empty($fetchMpesaPaymenysAssoc['MpesaReceiptNumber'])==true) { ?>
                                                Empty Mpesa receipt number
                                            <?php } else { ?>
                                                <?php echo $fetchMpesaPaymenysAssoc['MpesaReceiptNumber']; ?>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if (empty($fetchMpesaPaymenysAssoc['Amount'])==true) { ?>
                                                Empty Amount
                                            <?php } else { ?>
                                                <?php echo number_format($fetchMpesaPaymenysAssoc['Amount'],2); ?>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if (empty($fetchMpesaPaymenysAssoc['TransactioDate'])==true) { ?>
                                                Empty Transaction date
                                            <?php } else { ?>
                                                <?php echo $fetchMpesaPaymenysAssoc['TransactioDate']; ?>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if (empty($fetchMpesaPaymenysAssoc['PhoneNumber'])==true) { ?>
                                                Empty Phone number
                                            <?php } else { ?>
                                                <?php echo $fetchMpesaPaymenysAssoc['PhoneNumber']; ?>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php } ?>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
        <script>$(document).ready( function () {$('#GelishBuildingSolutionsMpesaPayments').DataTable();} );</script>
    </body>
</html>
<?php $dbConnection->close(); // Close the database connection ?>