<script type="text/javascript">
    $(window).ready(function(){
        $.ajax({
            url: '01_last_seen_status_update.php',
            type: 'POST',
            data: {status: 'active'},
            // success: function(response){console.log('active');}
        });
    });
        
    $(window).focus(function(){
        $.ajax({
            url: '01_last_seen_status_update.php',
            type: 'POST',
            data: {status: 'active'},
            // success: function(response){console.log('active');}
        });
    });

    $(window).blur(function(){
        $.ajax({
            url: '01_last_seen_status_update.php',
            type: 'POST',
            data: {status: 'inactive'},
            // success: function(response){console.log('inactive');}
        });
    });
    
    window.addEventListener('beforeunload', function(){
       $.ajax({
            url: '01_last_seen_status_update.php',
            type: 'POST',
            data: {status: 'inactive'},
            // success: function(response){console.log('inactive');}
        });
    });
</script>