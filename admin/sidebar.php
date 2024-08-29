<style>
    .nav-pills .nav-link {color: #c2c7d0;}
    .main-sidebar {/* Safari */-webkit-user-select: none;/* IE 10 and IE 11 */-ms-user-select: none;/* Standard syntax */user-select: none;}
</style>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <div class="dropdown">
        <span class="brand-link">
            <h6 class="text-center p-0 m-0" style="cursor:pointer;text-transform: capitalize;">
                <strong title="<?php echo $user_first_name.' '.$user_last_name; ?>"><?php echo $user_first_name.' '.$user_last_name; ?></strong>
                <span style="font-size:16px;" class="font-weight-normal d-block">
                    <span id="liveTime" title="<?php echo $fetchCompanyTimezoneAssoc['timezone_name']; ?>">
                        <span class="spinner-border text-primary" style="width:20px; height:20px; border-width:3px;"></span>
                    </span>
                </span>
            </h6>
            <script language="javascript" type="text/javascript">
                var timerID = null;
                var timerRunning = false;

                function stopclock() {
                    if(timerRunning)
                    clearTimeout(timerID);
                    timerRunning = false;
                }

                function showtime() {
                    // Set the desired timezone
                    var timeZone = '<?php echo $fetchCompanyTimezoneAssoc['timezone_name']; ?>';
                    // Get the current time in the specified timezone
                    var currentTime = new Date().toLocaleTimeString('en-UK',{timeZone});
                    // Extract hour, minute, and second from the current time
                    var [hour, minute, second] = currentTime.split(/:| /);
                    // console.log(`Current time in ${timeZone}: ${hour}:${minute}:${second}`);
                    var currentTime = hour+':'+minute+':'+second;
                    
                    document.getElementById('liveTime').innerHTML = currentTime;
                    timerID = setTimeout("showtime()",1000);
                    timerRunning = true;
                }
                function startTimezoneClock() {
                    stopclock();
                    showtime();
                }
                window.onload=startTimezoneClock;
            </script>
        </span>
    </div>
    <div class="sidebar pb-4 mb-4">
        <nav class="mt-5">
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
                
                <li class="nav-item">
                    <a href="index" class="nav-link nav-home">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item" style="cursor:pointer;">
                    <span class="nav-link">
                        <i class="nav-icon fas fa-user-friends"></i>
                        <p>
                            Staff
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </span>
                    <ul class="nav nav-treeview">
                        <?php if (isset($_GET['tsk']) == true && $_GET['tsk'] == 'add_staff'){ ?>
                            <li class="nav-item bg-secondary">
                        <?php }else{ ?>
                            <li class="nav-item">
                        <?php } ?>
                            <a href="staff?tsk=add_staff" class="nav-link tree-item">
                                <i class="fa-regular fa-circle mr-1"></i>
                                <p>Add staff</p>
                            </a>
                        </li>
                        <?php if (isset($_GET['tsk']) == true && $_GET['tsk'] == 'view_staff'){ ?>
                            <li class="nav-item bg-secondary">
                        <?php }else{ ?>
                            <li class="nav-item">
                        <?php } ?>
                            <a href="staff?tsk=view_staff" class="nav-link tree-item">
                                <i class="fa-regular fa-circle mr-1"></i>
                                <p>View staff</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item" style="cursor:pointer;">
                    <span class="nav-link">
                        <i class="nav-icon fas fa-user-friends"></i>
                        <p>
                            Customers
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </span>
                    <ul class="nav nav-treeview">
                        <?php if (isset($_GET['tsk']) == true && $_GET['tsk'] == 'view_customers') { ?>
                            <li class="nav-item bg-secondary">
                        <?php }else{ ?>
                            <li class="nav-item">
                        <?php } ?>
                            <a href="customers?tsk=view_customers" class="nav-link tree-item">
                                <i class="fa-regular fa-circle mr-1"></i>
                                <p>View customers</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item" style="cursor:pointer;">
                    <span class="nav-link">
                        <i class="nav-icon fa-solid fa-truck-fast"></i>
                        <p>
                            Suppliers
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </span>
                    <ul class="nav nav-treeview">
                        <?php if (isset($_GET['tsk']) == true && $_GET['tsk'] == 'add_supplier'){ ?>
                            <li class="nav-item bg-secondary">
                        <?php }else{ ?>
                            <li class="nav-item">
                        <?php } ?>
                            <a href="suppliers?tsk=add_supplier" class="nav-link tree-item">
                                <i class="fa-regular fa-circle mr-1"></i>
                                <p>Add suppliers</p>
                            </a>
                        </li>
                        <?php if (isset($_GET['tsk']) == true && $_GET['tsk'] == 'view_suppliers'){ ?>
                            <li class="nav-item bg-secondary">
                        <?php }else{ ?>
                            <li class="nav-item">
                        <?php } ?>
                            <a href="suppliers?tsk=view_suppliers" class="nav-link tree-item">
                                <i class="fa-regular fa-circle mr-1"></i>
                                <p>View suppliers</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item" style="cursor:pointer;">
                    <span class="nav-link">
                        <i class="nav-icon fa-brands fa-product-hunt"></i>
                        <p>
                            Products
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </span>
                    <ul class="nav nav-treeview">
                        <?php if (isset($_GET['tsk']) == true && $_GET['tsk'] == 'add_product'){ ?>
                            <li class="nav-item bg-secondary">
                        <?php }else{ ?>
                            <li class="nav-item">
                        <?php } ?>
                            <a href="products?tsk=add_product" class="nav-link tree-item">
                                <i class="fa-regular fa-circle mr-1"></i>
                                <p>Add products</p>
                            </a>
                        </li>
                        <?php if (isset($_GET['tsk']) == true && $_GET['tsk'] == 'view_products'){ ?>
                            <li class="nav-item bg-secondary">
                        <?php }else{ ?>
                            <li class="nav-item">
                        <?php } ?>
                            <a href="products?tsk=view_products" class="nav-link tree-item">
                                <i class="fa-regular fa-circle mr-1"></i>
                                <p>View products</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item" style="cursor:pointer;">
                    <span class="nav-link">
                        <i class="nav-icon fas fa-cart-arrow-down"></i>
                        <p>
                            Receive products
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </span>
                    <ul class="nav nav-treeview">
                        <?php if (isset($_GET['tsk']) == true && $_GET['tsk'] == 'input_invoice') { ?>
                            <li class="nav-item bg-secondary">
                        <?php }else{ ?>
                            <li class="nav-item">
                        <?php } ?>
                            <a href="receive_invoice?tsk=input_invoice" class="nav-link tree-item">
                                <i class="fa-regular fa-circle mr-1"></i>
                                <p>Input invoice</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item" style="cursor:pointer;">
                    <span class="nav-link">
                        <i class="nav-icon fas fa-coins"></i>
                        <p>
                            Expenses
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </span>
                    <ul class="nav nav-treeview">
                        <!-- <?php if (isset($_GET['tsk']) == true && $_GET['tsk'] == 'add_expense') { ?>
                            <li class="nav-item bg-secondary">
                        <?php }else{ ?>
                            <li class="nav-item">
                        <?php } ?>
                            <a href="expense?tsk=add_expense" class="nav-link tree-item">
                                <i class="fa-regular fa-circle mr-1"></i>
                                <p>Add expense</p>
                            </a>
                        </li> -->

                        <?php if (isset($_GET['tsk']) == true && $_GET['tsk'] == 'view_expenses') { ?>
                            <li class="nav-item bg-secondary">
                        <?php }else{ ?>
                            <li class="nav-item">
                        <?php } ?>
                            <a href="expense?tsk=view_expenses" class="nav-link tree-item">
                                <i class="fa-regular fa-circle mr-1"></i>
                                <p>View expenses</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item" style="cursor:pointer;">
                    <span class="nav-link">
                        <i class="nav-icon fas fa-cart-arrow-down"></i>
                        <p>
                            Orders
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </span>
                    <ul class="nav nav-treeview">
                        <?php if (isset($_GET['tsk']) == true && $_GET['tsk'] == 'awaiting_cash_payment') { ?>
                            <li class="nav-item bg-secondary">
                        <?php }else{ ?>
                            <li class="nav-item">
                        <?php } ?>
                            <a href="orders?tsk=awaiting_cash_payment" class="nav-link tree-item">
                                <i class="fa-regular fa-circle mr-1"></i>
                                <p>Awaiting cash payment</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item" style="cursor:pointer;">
                    <span class="nav-link" onmouseover="rotateOn()" onmouseout="rotateOff()">
                        <i id="gearIcon" class="nav-icon fa-solid fa-gear"></i>
                        <p>
                            Settings
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </span>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="account?tsk=profile" class="nav-link tree-item">
                                <i class="fa-regular fa-circle mr-1"></i>
                                <p>View profile</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="account?tsk=change_password" class="nav-link tree-item">
                                <i class="fa-regular fa-circle mr-1"></i>
                                <p>Change password</p>
                            </a>
                        </li>
                        <li class="nav-item" onmouseover="powerOn()" onmouseout="powerOff()">
                            <span class="nav-link tree-item" data-toggle="modal" data-target="#logOutModal">
                                <i id="powerIcon" class="fa-solid fa-power-off mr-1 text-light"></i>
                                <p>Sign out</p>
                            </span>
                        </li>
                    </ul>
                </li>
            </ul>
            <script>
                // Function to rotate the gear icon 90 degrees clockwise
                function rotateOn() {
                    // Update the style of the gearIcon element to rotate 90 degrees
                    document.getElementById('gearIcon').style = "transform: rotate(90deg);animation-duration: 6s;";
                }

                // Function to rotate the gear icon 180 degrees counter-clockwise
                function rotateOff() {
                    // Update the style of the gearIcon element to rotate -180 degrees
                    document.getElementById('gearIcon').style = "transform: rotate(-180deg);animation-duration: 6s;";
                }
                
                // Function to change the appearance of the power icon when the device is turned on
                function powerOn() {
                    // Get the power icon element by its ID
                    // and update its class name to display the power-off icon in red
                    document.getElementById('powerIcon').className = "fa-solid fa-power-off mr-1 text-danger";
                }

                // Function to change the appearance of the power icon when the device is turned off
                function powerOff() {
                    // Get the power icon element by its ID
                    // and update its class name to display the power-off icon in light color
                    document.getElementById('powerIcon').className = "fa-solid fa-power-off mr-1 text-light";
                }
            </script>
        </nav>
    </div>
</aside>