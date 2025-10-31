<!DOCTYPE html>
<html lang="zxx">


<?php require 'include/phpcode.php';

if (!empty($_GET['type'])) {
	$type = $_GET['type'];

	// $jobid = $_GET['jobid'];

	if (($type == "delete")) {
		$noticid = $_GET['noticid'];
		$delete_sql = "DELETE from tblnotification where ID='$noticid'";
		mysqli_query($con, $delete_sql);

		// $delete_sql = "DELETE from tblscreening_qa where ID='$bookmarkedid'";
		// mysqli_query($con, $delete_sql); 
?>

<script>
// alert("Bookmarked Job Removed");
// setTimeout(function() {
// 	window.location.href = 'job-detail.php?jobid=<?php echo $jobid ?>';
// }, 3000);
</script>
<?php
	}
} ?>

<!-- Mirrored from themezhub.net/live-workplex/workplex/employer-dashboard.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Feb 2022 12:07:10 GMT -->

<head>
    <meta charset="utf-8" />
    <meta name="author" content="Themezhub" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Workplex - Creative Job Board HTML Template</title>

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
        <!-- Start Navigation -->
        <?php include 'header.php' ?>
        <!-- End Navigation -->
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
                            <div class="dash-widgets py-5 px-4 bg-success rounded">
                                <?php
								$queryjobs = "SELECT * from tbljob ORDER BY JOBID DESC" or die(mysqli_error($con));
								$resultjobs = mysqli_query($con, $queryjobs);
								$jobs = mysqli_num_rows($resultjobs);
								?>
                                <h2 class="ft-medium mb-1 fs-xl text-light"><?php echo $jobs ?></h2>
                                <p class="p-0 m-0 text-light fs-md">Jobs</p>
                                <i class="lni lni-empty-file"></i>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                            <div class="dash-widgets py-5 px-4 bg-purple rounded">
                                <?php
								$query = "SELECT * from tbljobapplication ORDER BY ID DESC" or die(mysqli_error($con));
								$run = mysqli_query($con, $query);
								$apply = mysqli_num_rows($run);
								?>
                                <h2 class="ft-medium mb-1 fs-xl text-light"><?php echo $apply ?></h2>
                                <p class="p-0 m-0 text-light fs-md">Job Applications</p>
                                <i class="lni lni-users"></i>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                            <div class="dash-widgets py-5 px-4 bg-danger rounded">
                                <?php
								$querynotice = "SELECT * from tblnotification ORDER BY ID DESC" or die(mysqli_error($con));
								$resultnotice = mysqli_query($con, $querynotice);
								$notice = mysqli_num_rows($resultnotice);
								?>
                                <h2 class="ft-medium mb-1 fs-xl text-light"><?php echo $notice ?></h2>
                                <p class="p-0 m-0 text-light fs-md">Notifications</p>
                                <i class="lni lni-bar-chart"></i>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                            <div class="dash-widgets py-5 px-4 bg-blue rounded">
                                <?php
								$querybook = "SELECT * from tblbookmarkresume where USERID = '$session_id'ORDER BY ID DESC" or die(mysqli_error($con));
								$resultbook = mysqli_query($con, $querybook);
								$bookmark = mysqli_num_rows($resultbook);
								?>
                                <h2 class="ft-medium mb-1 fs-xl text-light"><?php echo $bookmark ?></h2>
                                <p class="p-0 m-0 text-light fs-md">Bookmark</p>
                                <i class="lni lni-heart"></i>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 col-md-12">
                            <div class="dashboard-gravity-list with-icons">
                                <h4 class="mb-0 ft-medium">Recent Activities</h4>
                                <ul>

                                    <?php
									$query = "SELECT * from tblnotification ORDER BY ID DESC" or die(mysqli_error($con));
									$run = mysqli_query($con, $query);
									$count = 0;
									while ($row = mysqli_fetch_array($run)) {
										$count++;
										$ID = $row['ID'];
										$USERID = $row['USERID'];
										$NOTE = $row['NOTE'];
										$DATETIME = $row['DATETIME'];


										$TYPE = $row['TYPE'];
										$TYPEID = $row['TYPEID'];
										// if ($TYPE == "Job Application") {
										// 	$queryapp = "SELECT * from tbljobapplication WHERE ID = '$TYPEID'";
										// 	$resultapp = mysqli_query($con, $queryapp);
										// 	$rowapp = mysqli_fetch_array($resultapp);
										// 	$JOBID = $rowapp['JOBID'];
										// }else {

										// }


									?>
                                    <li>
                                        <i class="dash-icon-box ti-bell text-warning bg-light-warning"></i>
                                        <?php //echo $NOTE 
											?>
                                        <?php
											if ($TYPE == "Job Application") { ?>
                                        <a href="dashboard-manage-applications.php">You sent a notice
                                            >><?php echo $NOTE ?></a>
                                        <?php } else {
												$queryapp = "SELECT * from tblfeedback where FEEDBACKID = '$TYPEID'";
												$resultapp = mysqli_query($con, $queryapp);
												$rowapp = mysqli_fetch_array($resultapp);
												$SENTBY = $rowapp['SENTBY'];
												if ($SENTBY == $session_id) { ?>
                                        <a href="dashboard-messages.php">You Sent a <?php echo $NOTE ?>...</a>
                                        <?php } else { ?>
                                        <a href="dashboard-messages.php">You have a <?php echo $NOTE ?>...</a>
                                        <?php }
											} ?>
                                        <a href="?type=delete&noticid=<?php echo $ID ?>" class="close-list-item"><i
                                                class="fas fa-times"></i></a>

                                        <p class="text"><?php echo timeago($DATETIME) ?></p>
                                    </li>
                                    <?php } ?>

                                </ul>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-12">
                            <div class="dashboard-gravity-list invoices with-icons">
                                <h4 class="mb-0 ft-medium">New Users</h4>
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
									$queryuser = "SELECT * from tblusers where ROLE='Applicant'  ORDER BY USERID DESC limit 5" or die(mysqli_error($con));
									$resultuser = mysqli_query($con, $queryuser);
									$count = 0;
									while ($rowuser = mysqli_fetch_array($resultuser)) {
										$count++;

										$USERID = $rowuser['USERID'];
										$USERNAME = $rowuser['USERNAME'];
										$FNAME = $rowuser['FNAME'];
										$OTHERNAMES = $rowuser['ONAME'];



										$query = "SELECT * from tblapplicants WHERE USERID = '$USERID'" or die(mysqli_error($con));
										$result = mysqli_query($con, $query);
										$row = mysqli_fetch_array($result);

										if ((!empty($row['FNAME'])) and (!empty($row['OTHERNAMES']))) {
											$FNAME = $row['FNAME']  ?? '';
											$OTHERNAMES = $row['OTHERNAMES']  ?? '';
										}


										$CITY = $row['CITY']  ?? '';
										$COUNTRY = $row['COUNTRY']  ?? '';

										$SEX = $row['SEX']  ?? '';
										$APPLICANTID = $row['APPLICANTID']  ?? '';
									?>

                                    <li><i class="dash-icon-box ti-user text-success bg-light-warning"></i>
                                        <strong class="ft-medium text-dark"><?php echo $FNAME ?>,
                                            <?php echo $OTHERNAMES ?></strong>
                                        <ul>
                                            <li><i class="fas fa-user"></i> <b><?php echo ($USERNAME); ?></b></li>
                                            <li class="unpaid"><?php echo $CITY ?>, <?php echo $COUNTRY ?></li>
                                            <li><?php echo $SEX ?></li>

                                            <!--<li><b>Deadline:</b> <?php echo date($DEADLINE) ?></li> -->
                                        </ul>
                                        <div class="buttons-to-right">
                                            <?php if (!empty($APPLICANTID)) { ?>
                                            <a href="dashboard-candidate-detail.php?applicantid=<?php echo $USERID ?>"
                                                class="button text-light bg-success">View</a>
                                            <?php } ?>
                                        </div>
                                    </li>

                                    <?php } ?>

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

<!-- Mirrored from themezhub.net/live-workplex/workplex/employer-dashboard.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Feb 2022 12:07:14 GMT -->

</html>