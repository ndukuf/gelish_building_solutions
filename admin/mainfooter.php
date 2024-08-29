<!-- <style>
  /* Smaller devices (575px and down) */
  @media (max-width: 575px) {
    #system_version{
        text-align: center !important;
    }
  }
  
  /* Small devices (landscape phones, 576px and up) */
  @media (min-width: 576px) {
    #system_version{
        text-align: center !important;
    }
  }

  /* Medium devices (tablets, 768px and up) */
  @media (min-width: 768px) {
    #system_version{
        text-align: center !important;
    }
  }

  /* Large devices (desktops, 992px and up) */
  @media (min-width: 992px) {
    #system_version{
        text-align: center !important;
    }
  }

  /* Extra large devices (large desktops, 1200px and up) */
  @media (min-width: 1200px) {
    #system_version{
        text-align: center !important;
    }
  }
</style> -->
<footer class="main-footer bg-dark text-light">
    &copy; 2024, <?php echo $fetchCompanyDataAssoc['company_name']; ?> All rights reserved.
    <div id="system_version" class="float-right d-block">
        <code class="text-light"><?php echo $fetchCompanyDataAssoc['system_version']; ?></code>
    </div>
</footer>