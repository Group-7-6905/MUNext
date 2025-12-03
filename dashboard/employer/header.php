<div class="header header-dark dark-text head-shadow">
    <div class="container">
        <nav id="navigation" class="navigation navigation-landscape">
            <div class="nav-header">
                <a class="nav-brand" href="./">
                    <!-- <img src="assets/img/logo-light.png" class="logo" alt="" /> -->
                    <span class="">MUNext</span>
                </a>
                <!-- <div class="nav-toggle"></div> -->
                <div class="mobile_nav">
                    <ul>
                        <li>
                            <a href="javascript:void(0);" data-toggle="modal" data-target="#login"
                                class="crs_yuo12 w-auto text-dark gray">
                                <span class="embos_45"><i class="lni lni-power-switch mr-1 mr-1"></i>Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="nav-menus-wrapper" style="transition-property: none;">


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

                    <li class="">
                        <a href="logout.php">
                            <i class="lni lni-power-switch mr-1"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>