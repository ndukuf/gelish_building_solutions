<?php
  // Prepare a SQL query to fetch user data from the database
  $fetchUserSql = 'SELECT `user_first_name`,`user_last_name`,`user_avatar` FROM `users` WHERE `user_id`=? LIMIT 1';

  // Prepare the SQL statement
  $fetchUserStmt = $dbConnection->prepare($fetchUserSql);

  // Bind parameters to the prepared statement
  $fetchUserStmt->bind_param('s',$_SESSION['user_id']);

  // Execute the prepared statement
  $fetchUserStmt->execute();

  // Retrieve the result set
  $fetchUserResult = $fetchUserStmt->get_result();

  // Fetch data as an associative array
  $fetchUserAssoc= $fetchUserResult->fetch_assoc();

  // The user's avatar
  $userAvatar = $fetchUserAssoc['user_avatar'];
  $user_first_name = $fetchUserAssoc['user_first_name'];
  $user_last_name = $fetchUserAssoc['user_last_name'];

  // Check if the request method is POST and if the Yes is set
  if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['Yes']) == true) {
    header('Location: ../sign_out');
    exit();
  }
?>

<style>
  /* Smaller devices (575px and down) */
  @media (max-width: 575px) {
    #topbar_right{
      display: none !important;
    }
  }
  
  /* Small devices (landscape phones, 576px and up) */
  @media (min-width: 576px) {
    #topbar_right{
      display: none !important;
    }
  }

  /* Medium devices (tablets, 768px and up) */
  @media (min-width: 768px) {
    #topbar_right{
      display: none !important;
    }
  }

  /* Large devices (desktops, 992px and up) */
  @media (min-width: 992px) {
    #topbar_right{
      display: none !important;
    }
  }

  /* Extra large devices (large desktops, 1200px and up) */
  @media (min-width: 1200px) {
    #topbar_right{
      display: none !important;
    }
  }
</style>
<nav class="main-header navbar navbar-expand navbar-dark ">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <?php if (isset($_SESSION['user_id'])){?>
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="" role="button"><i class="fas fa-bars"></i></a>
            </li>
        <?php } ?>
      <li>
        <span class="nav-link text-white" role="button" onclick="window.location.reload();">
          <img src="../<?php echo $fetchCompanyDataAssoc['company_logo']; ?>" width="100%" height="30" class="d-inline-block align-top" alt="<?php echo $fetchCompanyDataAssoc['company_name']; ?>" loading="lazy">
        </span>
      </li>
    </ul>

    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <div class="nav-link">
          <span id="maximize" class="bg-transparent" onclick="fullscreenEntry();"><i id="maximize" class="fas fa-expand-arrows-alt"></i></span>
          <span id="minimize" class="bg-transparent d-none" onclick="fullscreenExit();"><i class="fas fa-compress-arrows-alt"></i></span>
        </div>
      </li>
      <li class="nav-link active">
        <div id="topbar_right" class="d-felx badge-pill">
          <span>
            <?php if (empty($userAvatar) == true || $userAvatar=='notSet' || empty($userAvatar) == false && is_file('../profile_pictures/'.$userAvatar) == false){ ?>
                <img src="../profile_pictures/gbs.png" alt="gbs.png" class="user-img border rounded" style="width:25px;height:25px;">
            <?php }else{ ?>
              <img src="../profile_pictures/<?php echo $userAvatar; ?>" alt="" class="user-img border">
            <?php } ?>
          </span>
          <strong><?php echo ucwords($user_first_name); ?></strong>
        </div>
      </li>
    </ul>
</nav>
<script>
    //JavaScript code to prevent the form from being resubmitted when the user refreshes the page.
    if ( window.history.replaceState ){window.history.replaceState( null, null, window.location.href );}
</script>
<div class="modal fade" id="logOutModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="logOutModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="logOutModalLabel">Log out</h5>
        <span type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </span>
      </div>
      <form action="" method="post" onsubmit="return signOutJsValidation();">
        <div class="modal-body">
          Are you sure you want to sign out?
        </div>
        <div class="modal-footer">
          <div class="container">
            <button name="Close" id="Close" type="button" class="d-block btn btn-secondary" style="width:100px;float:left!important;" data-dismiss="modal">Close</button>
            <button name="Yes" id="Yes" type="submit" class="d-block btn btn-primary" style="width:100px;float:right!important;">Yes</button>
            <span name="signOutLoader" id="signOutLoader" class="d-none btn btn-primary" style="width:100px;float:right!important;">
              <span class="spinner-border text-light" style="width:20px; height:20px; border-width:3px;"></span>
            </span>
          </div>
        </div>
      </form>
      <script>
          function signOutJsValidation() {
              // Set the value of is_input_valid to be true by default
              var is_input_valid = true;

              document.getElementById('Yes').className = "d-none";
              document.getElementById('signOutLoader').className = "d-block btn btn-primary";
              document.getElementById('signOutLoader').style = 'width:100px;float:right!important;';

              // Return the value of is_input_valid
              return is_input_valid;
          }
      </script>
    </div>
  </div>
</div>
<script>
   // Variable to store the HTML document's root element
  var elem = document.documentElement;

  // Open fullscreen function
  function fullscreenEntry()
  {
    // Change the 'maximize' element's class to 'd-none' and 'minimize' element's class to 'd-block'
    document.getElementById('maximize').className = 'd-none';
    document.getElementById('minimize').className = 'd-block';

    // Check for different browser implementations of requestFullscreen
    if (elem.requestFullscreen)
    {
        elem.requestFullscreen();
    }
    else if (elem.webkitRequestFullscreen) // Safari
    {
        elem.webkitRequestFullscreen();
    }
    else if (elem.msRequestFullscreen) // IE11
    {
        elem.msRequestFullscreen();
    }
  }

  // Close fullscreen function
  function fullscreenExit()
  {
    // Change the 'minimize' element's class to 'd-none' and 'maximize' element's class to 'd-block'
    document.getElementById('minimize').className = 'd-none';
    document.getElementById('maximize').className = 'd-block';

    // Check for different browser implementations of exitFullscreen
    if (document.exitFullscreen)
    {
        document.exitFullscreen();
    }
    else if (document.webkitExitFullscreen) // Safari
    {
        document.webkitExitFullscreen();
    }
    else if (document.msExitFullscreen) // IE11
    {
        document.msExitFullscreen();
    }
  }
</script>