<?php
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
        echo 'The access token is not set. Please try again. <strong>Error source: <i>(accessToken.php)</i></strong><br>';
    } else {
        // The value of access token
        $access_token = $result->access_token;
        echo 'The access token is: <strong>'.$access_token.'</strong><br>';
    }
    
    // Close the cURL session
    curl_close($curl);
?>