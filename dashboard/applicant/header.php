<!-- Start Navigation -->
<div class="header header-light dark-text">
    <div class="container">
        <nav id="navigation" class="navigation navigation-landscape">
            <div class="nav-header">
                <a class="nav-brand" href="./">
                    <!-- <img src="assets/img/logo.png" class="logo" alt="" /> -->
                    MUNext
                </a>
                <div class="nav-toggle"></div>
                <div class="mobile_nav">
                    <ul>
                        <li>
                            <a href="#" data-toggle="modal" data-target="#login" class="theme-cl fs-lg">
                                <i class="lni lni-user"></i>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" data-toggle="modal" data-target="#login"
                                class="crs_yuo12 w-auto text-white theme-bg">
                                <span class="embos_45"><i class="fas fa-plus-circle mr-1 mr-1"></i>Post Job</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="nav-menus-wrapper" style="transition-property: none;">
                <ul class="nav-menu">

                    <li><a href="../../">Home</a>

                    </li>

                    <li>
                        <a href="../../job-list-v1.php">Find Job</a>

                    </li>

                    <li>
                        <a href="../../browse-employers.php">Employers</a>

                    </li>

                    <li>
                        <a href="../../about-us.php">About Us</a>
                    </li>

                    <li>
                        <a href="./../contact.php">Contact</a>
                    </li>

                </ul>



                <ul class="nav-menu nav-menu-social align-to-right">
                    <!-- User Role Indicator -->
                    <li>
                        <span class="role-section">
                            <div class="role-icon">
                                <?php echo isset($USERNAME) ? strtoupper(substr($USERNAME, 0, 1)) : 'A'; ?>
                            </div>
                            <div class="role-profile-icon">
                                <span class="role-profile">
                                    <?php echo isset($USERNAME) ? htmlspecialchars($USERNAME) : 'Employer'; ?>
                                </span>
                                <span
                                    class="role-title"><?php echo isset($ROLE) ? htmlspecialchars($ROLE) : ''; ?></span>
                            </div>
                        </span>
                    </li>

                    <li class="add-listing gray">
                        <a href="logout.php">
                            <i class="lni lni-power-switch mr-1"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>
<!-- End Navigation -->