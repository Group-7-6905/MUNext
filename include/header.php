<style>
#toast {
    visibility: hidden;
    max-width: 220px;
    background-color: #333;
    color: #fff;
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 14px;
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    transition: opacity 0.3s ease, transform 0.3s ease;
    opacity: 0;
    transform: translateY(-20px);
}

#toast.show {
    visibility: visible;
    opacity: 1;
    transform: translateY(0);
}

#toast.success {
    background-color: #28a745;
}

#toast.error {
    background-color: #dc3545;
}
</style>

<div id="toast"></div>



<div class="header header-light dark-text">
    <div class="container">
        <nav id="navigation" class="navigation navigation-landscape">
            <div class="nav-header">
                <a class="nav-brand" href="./">
                    <!-- <img src="assets/img/logo.png" class="logo" alt="" /> -->
                    <strong>MUNext</strong>
                </a>
                <div class="nav-toggle"></div>

                <?php
				// session_start();

				if (isset($_SESSION["userID"]) and (trim($_SESSION["userID"] != ""))) {
					$session_id = $_SESSION["userID"]; ?>

                <div class="mobile_nav">
                    <ul>
                        <li>
                            <a href="dashboard/" class="theme-cl fs-lg">
                                <i class="lni lni-user"></i>
                            </a>
                        </li>
                        <li>
                            <a href="job-search-v1.php" class="crs_yuo12 w-auto text-white theme-bg">
                                <span class="embos_45"><i class="fas fa-plus-circle mr-1 mr-1"></i>Apply Job</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <?php } else { ?>
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
                                <span class="embos_45"><i class="fas fa-plus-circle mr-1 mr-1"></i>Apply Job</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <?php }
				?>

            </div>
            <div class="nav-menus-wrapper" style="transition-property: none;">
                <ul class="nav-menu">

                    <li><a href="./">Home</a>
                    </li>

                    <li>
                        <a href="job-list-v1.php">Find Job</a>
                    </li>



                    <li>
                        <a href="browse-employers.php">Employers</a>

                    </li>

                    <li>
                        <a href="about-us.php">About Us</a>
                    </li>

                    <li>
                        <a href="contact.php">Contact</a>
                    </li>

                </ul>

                <ul class="nav-menu nav-menu-social align-to-right">

                    <?php
					// session_start();

					if (isset($_SESSION["userID"]) and (trim($_SESSION["userID"] != ""))) {
						$session_id = $_SESSION["userID"]; ?>

                    <li>
                        <a href="./dashboard" class="ft-medium">
                            <i class="lni lni-dashboard mr-1"></i> Dashboard
                        </a>
                    </li>

                    <li class="add-listing gray">
                        <a href="logout.php">
                            <i class="lni lni-power-switch mr-1"></i> Logout
                        </a>
                    </li>
                    <li class="add-listing theme-bg">

                        <a href="./job-list-v1.php">
                            <i class="lni lni-circle-plus mr-1"></i> Apply a Job
                        </a>




                    </li>

                    <?php } else { ?>
                    <li>
                        <a href="#" data-toggle="modal" data-target="#login" class="ft-medium">
                            <i class="lni lni-user mr-2"></i>Sign In
                        </a>
                    </li>
                    <li class="add-listing theme-bg">

                        <a href="./login.php">
                            <i class="lni lni-circle-plus mr-1"></i> Apply a Job
                        </a>




                    </li>
                    <?php }
					?>



                </ul>
            </div>
        </nav>
    </div>
</div>