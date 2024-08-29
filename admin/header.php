<link rel="icon" type="image/png" href="../Logo/Gelish Building Solutions sm.png" sizes="32x32">
<meta name="theme-color" content="<?php echo $fetchCompanyDataAssoc['theme_color']; ?>">
<!-- fontawesome-free-6.5.1 Icons -->
<link rel="stylesheet" href="assets/fontawesome-free-6.5.1/css/all.css">
<!-- Theme style -->
<link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
<link rel="stylesheet" href="assets/dist/css/styles.css">
<!-- overlayScrollbars -->
<link rel="stylesheet" href="assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">

<!-- DataTables -->
<link rel="stylesheet" href="assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">

<script src="assets/plugins/jquery/jquery.min.js"></script>
<script src="assets/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- require 00_last_seen_status_update.php php file -->
<?php require '00_last_seen_status_update.php' ?>

<script>
    //JavaScript code to prevent the form from being resubmitted when the user refreshes the page.
    if ( window.history.replaceState ){window.history.replaceState( null, null, window.location.href );}
</script>

<style>
    body{/* Safari */-webkit-user-select: none;/* IE 10 and IE 11 */-ms-user-select: none;/* Standard syntax */user-select: none;}
</style>