<!DOCTYPE html>
<html lang="en">
<head>
    <title>Network Connection Test</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.3/css/bootstrap.min.css" />

    <script>
    $(document).ready(function($) {
        
        //--->option > 1 > start
        /**/
        $(window).bind('offline', function(event) {
            event.preventDefault();            
            $(document).find('.container_connection').html('<span class="text-danger">OFFLINE</span>');
        });

        $(window).bind('online', function(event) {
            event.preventDefault(); 
            $(document).find('.container_connection').html('<span class="text-success">ONLINE</span>');
        });        
        //--->option > 1 > end

        //--->option > 2 > start
        function check_internet_connection() {
            $.ajax({
                type: "GET",
                url: "https://dummyimage.com/300", 
                success: function(data, status, xhr){console.log('status', xhr.status);},
                error: function(output) {
                    //console.log('OFFLINE');   
                    $(document).find('.container_connection').html('<span class="text-danger">AJAX > OFFLINE</span>');                
                }
            });
        };
 

        //run every 3 seconds
        setInterval(check_internet_connection, 3000);//ever 3 seconds 

        //--->option > 2 > end

    });
    </script>

</head>
<body style="background: black;">
    <div class="p-5"></div>
    <div class="container text-center online container_connection border"   style="font-size: 30px; width: 300px;border-radius: 25px;padding:15px; " >
        <span class="text-success">ONLINE</span>
    </div>   
</body>
</html>