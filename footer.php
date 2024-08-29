<footer class="bg-dark bd-footer bg-body-tertiary d-flex flex-wrap justify-content-between align-items-center">
    <p class="text-light col-md-4 mb-0 text-body-secondary">&copy; <?php echo date('Y'); ?>  <?php echo $fetchCompanyDataAssoc['company_name']; ?></p>
    <a href="index" class="col-md-4 d-flex align-items-center justify-content-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
        <img src="<?php echo $fetchCompanyDataAssoc['company_logo']; ?>" width="100%" height="30" class="d-inline-block align-top" alt="<?php echo $fetchCompanyDataAssoc['company_name']; ?>" loading="lazy">
    </a>
    <ul class="nav col-md-4 justify-content-end">
    <li class="nav-item"><a href="index" class="nav-link px-2 text-body-secondary">Home</a></li>
    <!-- <li class="nav-item"><a href="services" class="nav-link px-2 text-body-secondary">Services</a></li> -->
    <li class="nav-item"><a href="about_us" class="nav-link px-2 text-body-secondary">About us</a></li>
    </ul>
</footer>