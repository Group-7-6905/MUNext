<!DOCTYPE html>
<html lang="zxx">

<?php require 'include/phpcode.php'; ?>

<!-- Mirrored from themezhub.net/live-workplex/workplex/candidate-dashboard.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Feb 2022 12:07:05 GMT -->

<head>
    <meta charset="utf-8" />
    <meta name="author" content="Themezhub" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Workplex - Creative Job Board HTML</title>

    <!-- Custom CSS -->
    <link href="assets/css/styles.css" rel="stylesheet">

</head>

<body>

    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader"></div>

    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper">

        <!-- ============================================================== -->
        <!-- Top header  -->
        <!-- ============================================================== -->
        <?php include 'header.php' ?>

        <div class="clearfix"></div>
        <!-- ============================================================== -->
        <!-- Top header  -->
        <!-- ============================================================== -->

        <!-- ======================= dashboard Detail ======================== -->


        <div class="dashboard-wrap bg-light">
            <a class="mobNavigation" data-toggle="collapse" href="#MobNav" role="button" aria-expanded="false"
                aria-controls="MobNav">
                <i class="fas fa-bars mr-2"></i>Dashboard Navigation
            </a>

            <?php include 'sidenav.php' ?>

            <div class="dashboard-content">
                <div class="dashboard-tlbar d-block mb-5">
                    <div class="row">
                        <div class="colxl-12 col-lg-12 col-md-12">
                            <h1 class="ft-medium">Hello, <?php echo $FULLNAME ?></h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item text-muted"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item"><a href="#" class="theme-cl">Dashboard</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="dashboard-widg-bar d-block">
                    <div class="row">
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                            <div class="dash-widgets py-5 px-4 bg-info rounded">
                                <?php
								$query = "SELECT * from tbljobapplication where APPLICANTID = '$session_id' ORDER BY ID DESC" or die(mysqli_error($con));
								$run = mysqli_query($con, $query);
								$appliedjobs = mysqli_num_rows($run);
								?>
                                <h2 class="ft-medium mb-1 fs-xl text-light"><?php echo $appliedjobs ?></h2>
                                <p class="p-0 m-0 text-light fs-md">Applied Jobs</p>
                                <i class="lni lni-empty-file"></i>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                            <div class="dash-widgets py-5 px-4 bg-dark rounded">
                                <?php
								$querynotice = "SELECT * from tblnotification where USERID = '$session_id' ORDER BY ID DESC" or die(mysqli_error($con));
								$resultnotice = mysqli_query($con, $querynotice);
								$notice = mysqli_num_rows($resultnotice);
								?>
                                <h2 class="ft-medium mb-1 fs-xl text-light"><?php echo $notice ?></h2>
                                <p class="p-0 m-0 text-light fs-md">Notifications</p>
                                <i class="lni lni-users"></i>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                            <div class="dash-widgets py-5 px-4 bg-warning rounded">
                                <h2 class="ft-medium mb-1 fs-xl text-light"><?php echo $Alert ?></h2>
                                <p class="p-0 m-0 text-light fs-md">Alert Jobs</p>
                                <i class="lni lni-bar-chart"></i>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                            <div class="dash-widgets py-5 px-4 bg-purple rounded">
                                <?php
								$querybook = "SELECT * from tblbookmarkjob where APPLICANTID = '$session_id'ORDER BY ID DESC" or die(mysqli_error($con));
								$resultbook = mysqli_query($con, $querybook);
								$bookmark = mysqli_num_rows($resultbook);
								?>
                                <h2 class="ft-medium mb-1 fs-xl text-light"><?php echo $bookmark ?></h2>
                                <p class="p-0 m-0 text-light fs-md">Bookmark jobs</p>
                                <i class="lni lni-heart"></i>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 col-md-12">
                            <div class="dashboard-gravity-list with-icons">
                                <h4 class="mb-0 ft-medium">Notifications</h4>
                                <ul>

                                    <?php
									$query = "SELECT * from tblnotification where USERID ='$session_id' ORDER BY ID DESC" or die(mysqli_error($con));
									$run = mysqli_query($con, $query);
									$count = 0;
									while ($row = mysqli_fetch_array($run)) {
										$count++;
										$ID = $row['ID'];
										$USERID = $row['USERID'];
										$NOTE = $row['NOTE'];
										$DATETIME = $row['DATETIME'];
										$STATUS = $row['STATUS'];


										$TYPE = $row['TYPE'];
										$TYPEID = $row['TYPEID'];
										if ($TYPE == "Job Application") {
											$queryapp = "SELECT * from tbljobapplication WHERE ID = '$TYPEID'";
											$resultapp = mysqli_query($con, $queryapp);
											$rowapp = mysqli_fetch_array($resultapp);
											// $JOBID = $rowapp['JOBID'];
										 	$JOBID = isset($rowapp['JOBID']) ? $rowapp['JOBID'] : '';
                                        }

										if ($STATUS == "Unread") {
									?>
                                    <li>
                                        <i class="dash-icon-box ti-bell text-success bg-light-purple"></i>
                                        <strong class="ft-medium text-dark">
                                            <?php
													if ($TYPE == "Job Application") { ?>
                                            <a href="dashboard-applied-jobs.php"><?php echo $NOTE ?></a>
                                            <?php } else { ?>
                                            <a href="dashboard-messages.php">You Have a <?php echo $NOTE ?>...</a>
                                            <?php } ?>
                                        </strong>
                                        <!-- <a href="#" class="close-list-item"><i class="fas fa-times"></i></a> -->
                                        <p class="text"><?php echo timeago($DATETIME) ?></p>
                                    </li>

                                    <?php }
									}
									if ($count < 1) { ?>
                                    <p class="text-center"><i>No Notification Yet...</i></p>
                                    <?php } ?>

                                    <!-- <li>
										<i class="dash-icon-box ti-star text-success bg-light-success"></i> Jodie Farrell left a review <div class="numerical-rating high" data-rating="5.0"></div> for<strong class="ft-medium text-dark"><a href="#"> Real Estate Logo</a></strong>
										<a href="#" class="close-list-item"><i class="fas fa-times"></i></a>
									</li>

									<li>
										<i class="dash-icon-box ti-heart text-warning bg-light-warning"></i> Someone bookmarked your <strong class="ft-medium text-dark"><a href="#">SEO Expert Job</a></strong> listing!
										<a href="#" class="close-list-item"><i class="fas fa-times"></i></a>
									</li>

									<li>
										<i class="dash-icon-box ti-star text-info bg-light-info"></i> Gracie Mahmood left a review <div class="numerical-rating mid" data-rating="3.8"></div> on <strong class="ft-medium text-dark"><a href="#">Product Design</a></strong>
										<a href="#" class="close-list-item"><i class="fas fa-times"></i></a>
									</li>

									<li>
										<i class="dash-icon-box ti-heart text-danger bg-light-danger"></i> Your Magento Developer job expire<strong class="ft-medium text-dark"><a href="#">Renew</a></strong> now it!
										<a href="#" class="close-list-item"><i class="fas fa-times"></i></a>
									</li>

									<li>
										<i class="dash-icon-box ti-star text-success bg-light-success"></i> Ethan Barrett left a review <div class="numerical-rating high" data-rating="4.7"></div> on <strong class="ft-medium text-dark"><a href="#">New Logo</a></strong>
										<a href="#" class="close-list-item"><i class="fas fa-times"></i></a>
									</li>

									<li>
										<i class="dash-icon-box ti-star text-purple bg-light-purple"></i> Your Magento Developer job expire <strong class="ft-medium text-dark"><a href="#">Renew</a></strong> now it.
										<a href="#" class="close-list-item"><i class="fas fa-times"></i></a>
									</li> -->
                                </ul>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-12">
                            <div class="dashboard-gravity-list invoices with-icons">
                                <h4 class="mb-0 ft-medium">Available Jobs for you</h4>
                                <ul>
                                    <?php

									function timeago($date)
									{
										$timestamp = strtotime($date);

										$strTime = array("second", "minute", "hour", "day", "month", "year");
										$length = array("60", "60", "24", "30", "12", "10");

										$currentTime = time();
										if ($currentTime >= $timestamp) {
											$diff     = time() - $timestamp;
											for ($i = 0; $diff >= $length[$i] && $i < count($length) - 1; $i++) {
												$diff = $diff / $length[$i];
											}

											$diff = round($diff);
											return $diff . " " . $strTime[$i] . "(s) ago ";
										}
									}

									// $strTimeAgo = timeago($row["trans_date"]);

									?>

                                    <?php
									$count = 0;
									if ((!empty($JOBTITLE)) or (!empty($EXJOBTITLE))) {

										$queryJOB = "SELECT * from tbljob WHERE JOBTITLE LIKE '%$JOBTITLE%' or JOBCATEGORYID = '$JOBCATEGORYID' ORDER BY JOBID DESC" or die(mysqli_error($con));

										if (!empty($EXJOBTITLE)) {
											$queryJOB = "SELECT * from tbljob WHERE JOBTITLE LIKE '%$JOBTITLE%' or  JOBTITLE LIKE '%$EXJOBTITLE%' or JOBCATEGORYID = '$JOBCATEGORYID' ORDER BY JOBID DESC" or die(mysqli_error($con));
										}
										$resultJOB = mysqli_query($con, $queryJOB);

										while ($rowJOB = mysqli_fetch_array($resultJOB)) {
											$count++;

											$WORKEXPERIENCE = $rowJOB['WORKEXPERIENCE'];

											$COMPANYID = $rowJOB['COMPANYID'];
											$JOBID = $rowJOB['JOBID'];
											$JOBSTATUS = $rowJOB['JOBSTATUS'];
											$DATEPOSTED = $rowJOB['DATEPOSTED'];
											$SALARY = $rowJOB['SALARY'];
											$JOBTITLE = $rowJOB['JOBTITLE'];
											$JOBCATEGORYID = $rowJOB['JOBCATEGORYID'];
											$DEADLINE = $rowJOB['DEADLINE'];

											$queryjobsub = "SELECT * from tbljobsubcategory WHERE ID = '$JOBCATEGORYID'";
											$resultjobsub = mysqli_query($con, $queryjobsub);
											$rowjobsub = mysqli_fetch_array($resultjobsub);
											$SUBCATEGORY = $rowjobsub['SUBCATEGORY'];

											$querycomp = "SELECT * from tblcompany WHERE COMPANYID = '$COMPANYID'";
											$resultcomp = mysqli_query($con, $querycomp);
											$rowcomp = mysqli_fetch_array($resultcomp);

											$COMPANYNAME = $rowcomp['COMPANYNAME'];
											$COMPANYLOGO = $rowcomp['COMPANYLOGO'];
											$COMPANYADDRESS = $rowcomp['COMPANYADDRESS'];
											$COMPANYCOUNTRY = $rowcomp['COMPANYCOUNTRY'];
											$COMPANYCITY = $rowcomp['COMPANYCITY'];

									?>

                                    <li><i class="dash-icon-box ti-briefcase text-success bg-light-warning"></i>
                                        <strong class="ft-medium text-dark"><?php echo $JOBTITLE ?></strong>
                                        <ul>
                                            <li class="unpaid"><?php echo $COMPANYNAME ?></li>
                                            <li><?php echo $SUBCATEGORY ?></li>
                                            <li><b>Posted:</b> <?php echo timeago($DATEPOSTED); ?></li>
                                            <li><b>Deadline:</b> <?php echo date($DEADLINE) ?></li>
                                        </ul>
                                        <div class="buttons-to-right">
                                            <a href="../job-detail.php?jobid=<?php echo $JOBID ?>"
                                                class="button text-light bg-success">Apply Job</a>
                                        </div>
                                    </li>

                                    <?php }
									}
									if ($count < 1) { ?>
                                    <p class="text-center"><i>No Job Alert Yet...<br>Complete Profile to see avaliable
                                            jobs for you.</i></p>
                                    <?php } ?>
                                    <!-- <li><i class="dash-icon-box ti-files text-success bg-light-success"></i>
										<strong class="ft-medium text-dark">Basic Plan</strong>
										<ul>
											<li class="paid">Paid</li>
											<li>Order: #20550</li>
											<li>Date: 01/07/2019</li>
										</ul>
										<div class="buttons-to-right">
											<a href="javascript:void(0);" class="button text-light bg-warning">View Invoice</a>
										</div>
									</li>

									<li><i class="dash-icon-box ti-files text-danger bg-light-danger"></i>
										<strong class="ft-medium text-dark">Extended Plan</strong>
										<ul>
											<li class="paid">Paid</li>
											<li>Order: #20549</li>
											<li>Date: 01/06/2019</li>
										</ul>
										<div class="buttons-to-right">
											<a href="javascript:void(0);" class="button text-light bg-warning">View Invoice</a>
										</div>
									</li>

									<li><i class="dash-icon-box ti-files text-success bg-light-success"></i>
										<strong class="ft-medium text-dark">Platinum Plan</strong>
										<ul>
											<li class="paid">Paid</li>
											<li>Order: #20548</li>
											<li>Date: 01/05/2019</li>
										</ul>
										<div class="buttons-to-right">
											<a href="javascript:void(0);" class="button text-light bg-warning">View Invoice</a>
										</div>
									</li>

									<li><i class="dash-icon-box ti-files text-warning bg-light-warning"></i>
										<strong class="ft-medium text-dark">Extended Plan</strong>
										<ul>
											<li class="paid">Paid</li>
											<li>Order: #20547</li>
											<li>Date: 01/04/2019</li>
										</ul>
										<div class="buttons-to-right">
											<a href="javascript:void(0);" class="button text-light bg-warning">View Invoice</a>
										</div>
									</li>

									<li><i class="dash-icon-box ti-files text-info bg-light-info"></i>
										<strong class="ft-medium text-dark">Platinum Plan</strong>
										<ul>
											<li class="paid">Paid</li>
											<li>Order: #20546</li>
											<li>Date: 01/03/2019</li>
										</ul>
										<div class="buttons-to-right">
											<a href="javascript:void(0);" class="button text-light bg-warning">View Invoice</a>
										</div>
									</li> -->

                                </ul>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- footer -->
                <?php include 'footer.php' ?>
            </div>

        </div>
        <!-- ======================= dashboard Detail End ======================== -->

        <a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>


    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->

    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/slick.js"></script>
    <script src="assets/js/slider-bg.js"></script>
    <script src="assets/js/smoothproducts.js"></script>
    <script src="assets/js/snackbar.min.js"></script>
    <script src="assets/js/jQuery.style.switcher.js"></script>
    <script src="assets/js/custom.js"></script>
    <!-- ============================================================== -->
    <!-- This page plugins -->
    <!-- ============================================================== -->

</body>

<!-- Mirrored from themezhub.net/live-workplex/workplex/candidate-dashboard.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Feb 2022 12:07:10 GMT -->

</html>