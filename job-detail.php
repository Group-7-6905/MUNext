<!DOCTYPE html>
<html lang="zxx">

<?php
require('./mysqli_connect.php');
// header.php

include 'session_check.php';

include "include/helper.php";




//////////////////////Get Company Details////////////////////////
if (!empty($_GET['jobid'])) {

	$JOBID = $_GET['jobid'];

	$query = "SELECT * from tbljob WHERE JOBID = '$JOBID'";
	$result = mysqli_query($con, $query);
	$row = mysqli_fetch_array($result);

	$COMPANYID = $row['COMPANYID'];
	$WORKPLACE_POLICY = $row['WORKPLACE_POLICY'];
	$JOBTITLE = $row['JOBTITLE'];
	$SALARY = $row['SALARY'];
	$JOBTYPE = $row['JOBTYPE'];
	$QUALIFICATION = $row['QUALIFICATION'];
	$JOBDESCRIPTION = $row['JOBDESCRIPTION'];
	$PREFEREDSEX = $row['PREFEREDSEX'];
	$CAREERLEVEL = $row['CAREERLEVEL'];
	$WORKEXPERIENCE = $row['WORKEXPERIENCE'];
	$DEADLINE = $row['DEADLINE'];
	$JOBSTATUS = $row['JOBSTATUS'];

	$isActive = ($row['JOBSTATUS'] == 'Active');
	$isFilled = ($row['JOBSTATUS'] == 'Filled');

	$DATEPOSTED = $row['DATEPOSTED'];


	$querycomp = "SELECT * from tblcompany WHERE COMPANYID = '$COMPANYID'";
	$resultcomp = mysqli_query($con, $querycomp);
	$rowcomp = mysqli_fetch_array($resultcomp);

	$COMPANYNAME = $rowcomp['COMPANYNAME'];
	$COMPANYADDRESS = $rowcomp['COMPANYADDRESS'];
	$COMPANYCOUNTRY = $rowcomp['COMPANYCOUNTRY'];
	$COMPANYCITY = $rowcomp['COMPANYCITY'];
	$COMPANYEMAIL = $rowcomp['COMPANYEMAIL'];
	$COMPANYCONTACTNO = $rowcomp['COMPANYCONTACTNO'];
	$COMPANYINDUSTRY = $rowcomp['COMPANYINDUSTRY'];
	$COMPANYSPECIALISM = $rowcomp['COMPANYSPECIALISM'];
	$COMPANYLOGO = $rowcomp['COMPANYLOGO'];
} else {
	header('location: job-search-v1.php');
}
//////////////////////Get Company Details Ends//////////////////////

if (!empty($_GET['type'])) {
	$type = $_GET['type'];

	$jobid = $_GET['jobid'];

	if (($type == "delete")) {
		$bookmarkedid = $_GET['bookmarkedid'];
		$delete_sql = "DELETE from tblbookmarkjob where ID='$bookmarkedid'";
		mysqli_query($con, $delete_sql);

		// $delete_sql = "DELETE from tblscreening_qa where ID='$bookmarkedid'";
		// mysqli_query($con, $delete_sql); 
?>

<script>
// alert("Bookmarked Job Removed");
setTimeout(function() {
    window.location.href = 'job-detail.php?jobid=<?php echo $jobid ?>';
}, 3000);
</script>
<?php
	}
}
?>

<?php include 'include/head.php' ?>

<body>


    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper">

        <!-- ============================================================== -->
        <!-- Top header  -->
        <!-- ============================================================== -->
        <!-- Start Navigation -->
        <?php include 'include/header.php' ?>
        <!-- End Navigation -->
        <div class="clearfix"></div>
        <!-- ============================================================== -->
        <!-- Top header  -->
        <!-- ============================================================== -->
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

        <!-- ============================ Job Details Start ================================== -->
        <section class="bg-light py-5 position-relative">
            <div class="container">
                <div class="row">

                    <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12">

                        <div class="bg-white rounded px-3 py-4 mb-4">
                            <div class="jbd-01 d-flex align-items-center justify-content-between">
                                <div class="jbd-flex d-flex align-items-center justify-content-start">
                                    <div class="jbd-01-thumb">
                                        <img src="./<?php echo $COMPANYLOGO ?>" class="img-fluid" width="90" alt="" />
                                    </div>
                                    <div class="jbd-01-caption pl-3">
                                        <div class="tbd-title">
                                            <h4 class="mb-0 ft-medium fs-md">
                                                <a href="employer-detail.php?companyid=<?php echo $COMPANYID ?> ">
                                                    <?php echo $JOBTITLE ?></a>
                                            </h4>
                                        </div>
                                        <div class="jbl_location mb-3">
                                            <span><i class="lni lni-map-marker mr-1"></i><?php echo $COMPANYCITY ?>,
                                                <?php echo $COMPANYCOUNTRY ?></span>
                                            <span class="text-muted mr-2"><i
                                                    class="lni lni-home mr-1"></i><?php echo $COMPANYADDRESS ?></span>
                                            <span
                                                class="medium ft-medium text-warning ml-3"><?php echo $JOBTYPE ?></span>
                                        </div>
                                        <div class="jbl_info01">
                                            <?php if ($isFilled): ?>
                                            <span
                                                class="px-2 py-1 ft-medium text-danger medium rounded theme-cl bg-light-danger mr-2">Position
                                                Filled</span>
                                            <?php elseif ($isActive): ?>
                                            <span
                                                class="px-2 py-1 ft-medium medium rounded text-success theme-bg-light mr-2">Hiring
                                            </span>
                                            <?php endif ?>
                                            <span
                                                class="px-2 py-1 ft-medium medium rounded text-warning bg-light-danger mr-2"><?php echo $COMPANYINDUSTRY ?></span>
                                            <span
                                                class="px-2 py-1 ft-medium medium rounded text-purple bg-light-purple"><?php echo $COMPANYSPECIALISM ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="jbd-01-right text-right hide-1023">

                                    <div class="jbl_button"><a
                                            href="employer-detail.php?companyid=<?php echo $COMPANYID ?> "
                                            class="btn rounded bg-white border fs-sm ft-medium"> <i
                                                class="fa fa-eye"></i> View Company</a></div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded mb-4">
                            <div class="jbd-01 px-3 py-4">
                                <div class="jbd-details mb-4">
                                    <h5 class="ft-medium fs-md">Job description</h5>
                                    <p><?php echo $JOBDESCRIPTION ?></p>
                                </div>



                                <div class="jbd-details mb-4">
                                    <h5 class="ft-medium fs-md">Job Information</h5>
                                    <div class="other-details">
                                        <div class="details ft-medium"><label class="text-muted">Workplace
                                                Policy</label><span
                                                class="text-dark"><?php echo $WORKPLACE_POLICY ?></span></div>
                                        <div class="details ft-medium"><label class="text-muted">Industry
                                                Type</label><span
                                                class="text-dark"><?php echo $COMPANYINDUSTRY ?></span></div>
                                        <div class="details ft-medium"><label class="text-muted">Functional
                                                Area</label><span
                                                class="text-dark"><?php echo $COMPANYSPECIALISM ?></span></div>
                                        <div class="details ft-medium"><label class="text-muted">Employment
                                                Type</label><span class="text-dark"><?php echo $JOBTYPE ?></span></div>
                                        <div class="details ft-medium"><label
                                                class="text-muted">Qualification</label><span
                                                class="text-dark"><?php echo $QUALIFICATION ?></span></div>
                                        <div class="details ft-medium"><label class="text-muted">Prefered
                                                Gender</label><span class="text-dark"><?php echo $PREFEREDSEX ?></span>
                                        </div>
                                        <div class="details ft-medium"><label class="text-muted">Experience</label><span
                                                class="text-dark"><?php echo $WORKEXPERIENCE ?></span></div>
                                        <div class="details ft-medium"><label class="text-muted">Career
                                                Level</label><span class="text-dark"><?php echo $CAREERLEVEL ?></span>
                                        </div>
                                        <div class="details ft-medium"><label class="text-muted">Salary</label><span
                                                class="text-dark"><?php if ($SALARY > 0) {
																																											echo $SALARY; # code...
																																										} else {
																																											echo 'Not Specified';
																																										}  ?></span></div>
                                        <div class="details ft-medium"><label class="text-muted">Posted Day</label><span
                                                class="text-dark"><?php echo timeago($DATEPOSTED) ?></span></div>
                                        <div class="details ft-medium"><label class="text-muted">Deadline</label><span
                                                class="text-dark"><?php echo $DEADLINE ?></span></div>
                                    </div>
                                </div>


                                <!-- <div class="jbd-details mb-1">
									<h5 class="ft-medium fs-md">Key Skills</h5>
									<ul class="p-0 skills_tag text-left">
										<li><span class="px-2 py-1 medium skill-bg rounded text-dark">Joomla</span></li>
										<li><span class="px-2 py-1 medium skill-bg rounded text-dark">WordPress</span></li>
										<li><span class="px-2 py-1 medium skill-bg rounded text-dark">Javascript</span></li>
										<li><span class="px-2 py-1 medium skill-bg rounded text-dark">PHP</span></li>
										<li><span class="px-2 py-1 medium skill-bg rounded text-dark">HTML5</span></li>
										<li><span class="px-2 py-1 medium skill-bg rounded text-dark">MS SQL</span></li>
										<li><span class="px-2 py-1 medium skill-bg rounded text-dark">SQL Development</span></li>
										<li><span class="px-2 py-1 medium skill-bg rounded text-dark">Dynamod</span></li>
										<li><span class="px-2 py-1 medium skill-bg rounded text-dark">Database</span></li>
									</ul>
								</div> -->

                            </div>

                            <div class="jbd-02 px-3 py-3 br-top">
                                <div class="jbd-02-flex d-flex align-items-center justify-content-between">
                                    <!-- <div class="jbd-02-social">
										<ul class="jbd-social">
											<li><i class="ti-sharethis"></i></li>
											<li><a href="javascript:void(0);"><i class="ti-facebook"></i></a></li>
											<li><a href="javascript:void(0);"><i class="ti-twitter"></i></a></li>
											<li><a href="javascript:void(0);"><i class="ti-linkedin"></i></a></li>
											<li><a href="javascript:void(0);"><i class="ti-instagram"></i></a></li>
										</ul>
									</div> -->
                                    <div class="jbd-02-aply">
                                        <div class="jbl_button mb-2">


                                            <?php if (empty($session_id)) { ?>
                                            <a href="#" data-toggle="modal" data-target="#login"
                                                class="btn btn-md rounded gray fs-sm ft-medium mr-2">Save This Job</a>

                                            <a href="#" data-toggle="modal" data-target="#login"
                                                class="btn btn-md rounded theme-bg text-light fs-sm ft-medium">Apply
                                                Job</a>
                                            <?php } else { ?>
                                            <form method="post">
                                                <input type="hidden" name="jobID" value="<?php echo $JOBID ?>">
                                                <input type="hidden" name="userID" value="<?php echo $session_id ?>">
                                                <?php
													$query = "SELECT * from tblbookmarkjob where APPLICANTID = '$session_id' and JOBID = '$JOBID' ORDER BY ID DESC" or die(mysqli_error($con));
													$run = mysqli_query($con, $query);
													$row = mysqli_fetch_array($run);
													$cn_save = mysqli_num_rows($run);

													if ($cn_save > 0) { ?>
                                                <a href="?type=delete&bookmarkedid=<?php echo $row['ID'] ?>&jobid=<?php echo $JOBID ?>"
                                                    class="btn btn-md rounded gray fs-sm ft-medium mr-2 text-warning">Remove
                                                    Saved Job</a>
                                                <?php } else { ?>
                                                <button type="submit" name="save_job"
                                                    class="btn btn-md rounded gray fs-sm ft-medium mr-2">Save This
                                                    Job</button>
                                                <?php } ?>

                                                <a href="#apply"
                                                    class="btn btn-md rounded theme-bg text-light fs-sm ft-medium">Apply
                                                    Job</a>
                                            </form>

                                            <?php } ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>



                    <?php if (empty($session_id)) { ?>
                    <div class="modal fade" id="login" tabindex="-1" role="dialog" aria-labelledby="loginmodal"
                        aria-hidden="false">
                        <div class="modal-dialog modal-xl login-pop-form" role="document">
                            <div class="modal-content" id="loginmodal">
                                <div class="modal-headers">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span class="ti-close"></span>
                                    </button>
                                </div>

                                <div class="modal-body p-5">
                                    <div class="text-center mb-4">
                                        <h4 class="ft-medium fs-md mb-3">Interested in this job? Login to Apply</h4>
                                    </div>

                                    <form method="post">
                                        <div class="form-group">
                                            <label>User Name</label>
                                            <input type="text" class="form-control" placeholder="Username*"
                                                name="username">
                                        </div>

                                        <div class="form-group">
                                            <label>Password</label>
                                            <input type="password" class="form-control" placeholder="Password*"
                                                name="password">
                                        </div>

                                        <div class="form-group">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="flex-1">
                                                    <input id="dd" class="checkbox-custom" name="dd" type="checkbox">
                                                    <label for="dd" class="checkbox-custom-label">Remember Me</label>
                                                </div>
                                                <div class="eltio_k2">
                                                    <a href="#" class="theme-cl">Lost Your Password?</a>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="flex-1">
                                                    <?php echo $Lerror; ?>

                                                    <?php
														if (isset($_SERVER['HTTPS']) and ($_SERVER['HTTPS'])) {
															$url = "https://";
														} else {
															$url = '';
															// $url = "https://";

															// $url.= $_SERVER['HTTP_HOST'];

															// $url .= $_SERVER['REQUEST_URI'];

															$curPageName = substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1);
															$curPageName .= '?jobid=' . $JOBID;
														}
														?>
                                                    <input type="hidden" name="url" value="<?php echo $curPageName ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <button type="submit" name="login_btn"
                                                class="btn btn-md full-width theme-bg text-light fs-md ft-medium">Login</button>
                                        </div>

                                        <div class="form-group text-center mb-0">
                                            <p class="extra">Not a member?<a href="login.php" class="text-dark">
                                                    Register</a></p>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                        <div class="jb-apply-form bg-white rounded py-3 px-4 box-static" id="apply">
                            <h4 class="ft-medium fs-md mb-3">Interested in this job?</h4>

                            <form method="post">
                                <div class="form-group">
                                    <label>User Name</label>
                                    <input type="text" class="form-control" placeholder="Username*" name="username">
                                </div>

                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" class="form-control" placeholder="Password*" name="password">
                                </div>

                                <div class="form-group">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="flex-1">
                                            <input id="dd" class="checkbox-custom" name="dd" type="checkbox">
                                            <label for="dd" class="checkbox-custom-label">Remember Me</label>
                                        </div>
                                        <div class="eltio_k2">
                                            <a href="#" class="theme-cl">Lost Your Password?</a>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="flex-1">
                                            <?php echo $Lerror; ?>

                                            <?php
												if (isset($_SERVER['HTTPS']) and ($_SERVER['HTTPS'])) {
													$url = "https://";
												} else {
													$url = '';
													// $url = "https://";

													// $url.= $_SERVER['HTTP_HOST'];

													// $url .= $_SERVER['REQUEST_URI'];

													$curPageName = substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1);
													$curPageName .= '?jobid=' . $JOBID;
												}
												?>

                                            <input type="hidden" name="url" value="<?php echo $curPageName ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" name="login_btn"
                                        class="btn btn-md full-width theme-bg text-light fs-md ft-medium">Login</button>
                                </div>

                                <div class="form-group text-center mb-0">
                                    <p class="extra">Not a member?<a href="login.php" class="text-dark">
                                            Register</a></p>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!---Sidebar--->
                    <?php } elseif (((empty($APPLICANTPHOTO)) || (empty($DEGREE)) || (empty($CITY)) || (empty($ADDRESS)) || (empty($SKILLS)) || (empty($LinkedIn_link)))) {

                        if ($_SESSION['role'] == 'Applicant') {
					?>

                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                        <div class="jb-apply-form bg-white rounded py-3 px-4 box-static" id="apply">
                            <h4 class="ft-medium fs-md mb-3">Interested in this job?</h4>
                            <a href="./dashboard/applicant/dashboard-add-profile.php"
                                class="btn btn-md rounded theme-bg  text-light ft-medium fs-sm full-width">Complete
                                Profile</a>
                        </div>
                    </div>
                    <?php } else{?>
                    <!-- // For other roles, do not show anything -->
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                        <div class="jb-apply-form bg-white rounded py-3 px-4 box-static" id="apply">
                            <h4 class="ft-medium fs-md mb-3">Interested in this job?</h4>
                            <p class="text-muted mb-3">You are signed in with a non‑applicant account. To apply for
                                jobs, please switch to an Applicant account or register as an Applicant.</p>
                            <!-- <a href="./dashboard/applicant/dashboard-add-profile.php"
                                class="btn btn-md rounded theme-bg text-light ft-medium fs-sm full-width mb-2">Switch to
                                Applicant / Complete Profile</a> -->
                            <a href="login.php" class="btn btn-md rounded gray ft-medium fs-sm full-width">Register
                                as an Applicant</a>
                        </div>
                    </div>
                    <?php }
                
                } else { ?>

                    <!-- Sidebar -->
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                        <form method="post" enctype="multipart/form-data">
                            <div class="jb-apply-form bg-white rounded py-3 px-4 box-static" id="apply">
                                <h4 class="ft-medium fs-md mb-3">Interested in this job?</h4>

                                <div class="_apply_form_form">


                                    <?php if (isset($session_id)): ?>
                                    <?php
                                                    // Check if already applied
                                                    $checkQuery = "SELECT APPLICANTID FROM tbljobapplication WHERE JOBID = ? AND APPLICANTID = ?";
                                                    $stmtCheck = mysqli_prepare($con, $checkQuery);
                                                    mysqli_stmt_bind_param($stmtCheck, "ii", $JOBID, $session_id);
                                                    mysqli_stmt_execute($stmtCheck);
                                                    $hasApplied = mysqli_num_rows(mysqli_stmt_get_result($stmtCheck)) > 0;
                                                    ?>

                                    <?php if ($hasApplied): ?>
                                    <button class="btn btn-md rounded theme-bg  text-light ft-medium fs-sm full-width"
                                        disabled>
                                        <i class="lni lni-checkmark-circle"></i> Already Applied
                                    </button>
                                    <?php else: ?>
                                    <a href="apply-job.php?jobid=<?php echo $JOBID; ?>"
                                        class="btn btn-md rounded theme-bg text-light ft-medium fs-sm full-width">
                                        <i class="lni lni-briefcase"></i> Apply Now
                                    </a>
                                    <?php endif; ?>
                                    <?php else: ?>
                                    <a href="login.php?redirect=apply-job.php?jobid=<?php echo $JOBID; ?>"
                                        class="btn btn-md rounded theme-bg  text-light ft-medium fs-sm">
                                        <i class="lni lni-user"></i> Login to Apply
                                    </a>
                                    <?php endif; ?>



                                </div>
                            </div>



                        </form>
                    </div>
                    <!---Sidebar--->


                    <?php } ?>

                </div>
            </div>
        </section>
        <!-- ============================ Job Details End ================================== -->

        <!-- ======================= Related Jobs ======================== -->
        <section class="space min">
            <div class="container">

                <div class="row justify-content-center">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                        <div class="sec_title position-relative text-center mb-5">
                            <h6 class="text-muted mb-0">Related Jobs</h6>
                            <h2 class="ft-bold">All Related Listed jobs</h2>
                        </div>
                    </div>
                </div>

                <!-- row -->
                <div class="row align-items-center">

                    <?php
					$query = "SELECT * from tbljob where JOBTITLE LIKE '%$JOBTITLE%' AND JOBTYPE LIKE '%$JOBTYPE%'  ORDER BY JOBID DESC" or die(mysqli_error($con));

					$run = mysqli_query($con, $query);
					while ($row = mysqli_fetch_array($run)) {
						$COMPANYID = $row['COMPANYID'];
						$JOBID_NEW = $row['JOBID'];
						$JOBSTATUS = $row['JOBSTATUS'];
						$DATEPOSTED = $row['DATEPOSTED'];
						$SALARY = $row['SALARY'];
                        $count =0;

						$querycomp = "SELECT * from tblcompany WHERE COMPANYID = '$COMPANYID'";
						$resultcomp = mysqli_query($con, $querycomp);
						$rowcomp = mysqli_fetch_array($resultcomp);

						$COMPANYNAME = $rowcomp['COMPANYNAME'];
						$COMPANYLOGO = $rowcomp['COMPANYLOGO'];
						$COMPANYADDRESS = $rowcomp['COMPANYADDRESS'];
						$COMPANYCOUNTRY = $rowcomp['COMPANYCOUNTRY'];
						$COMPANYCITY = $rowcomp['COMPANYCITY'];
                        if ($JOBID != $JOBID_NEW){
                            $count++;
					?>

                    <!-- Single -->
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                        <div class="job_grid border rounded ">
                            <div class="position-absolute ab-left">
                                <form action="" class="cartCheckout" id="cartCheckout" method="post">

                                    <button type="button"
                                        class="p-3 border circle d-flex align-items-center justify-content-center bg-white text-gray"><i
                                            class="lni lni-heart-filled position-absolute snackbar-wishlist"></i></button>
                                </form>
                            </div>
                            <div class="position-absolute ab-right"><span
                                    class="medium theme-cl theme-bg-light px-2 py-1 rounded text-info"><?php echo $row['JOBTYPE']; ?></span>
                                <span
                                    class="medium theme-cl theme-bg-light px-2 py-1 rounded"><?php echo $JOBSTATUS ?></span>
                            </div>
                            <div class="job_grid_thumb mb-3 pt-5 px-3">
                                <a href="job-detail.php?jobid=<?php echo $JOBID_NEW ?>"
                                    class="d-block text-center m-auto"><img src="./<?php echo $COMPANYLOGO ?>"
                                        class="img-fluid" width="70" alt="" /></a>
                            </div>
                            <div class="job_grid_caption text-center pb-5 px-3">
                                <h6 class="mb-0 lh-1 ft-medium medium"><a
                                        href="employer-detail.php?companyid=<?php echo $COMPANYID ?>"
                                        class="text-muted medium"><?php echo $COMPANYNAME; ?></a></h6>
                                <h4 class="mb-0 ft-medium medium"><a
                                        href="job-detail.php?jobid=<?php echo $JOBID_NEW ?>"
                                        class="text-dark fs-md"><?php echo $row['JOBTITLE']; ?></a></h4>
                                <div class="jbl_location"><i
                                        class="lni lni-map-marker mr-1"></i><span><?php echo $COMPANYCITY ?>,
                                        <?php echo $COMPANYCOUNTRY ?></span></div>
                            </div>
                            <div class="job_grid_footer pb-4 px-3 d-flex align-items-center justify-content-between">
                                <?php if ($SALARY > 0) { ?><div class="df-1 text-muted"><i
                                        class="lni lni-wallet mr-1"></i>: $<?php echo number_format($SALARY, 2) ?>
                                </div> <?php } ?>
                                <div class="df-1 text-muted"><i
                                        class="lni lni-timer mr-1"></i><?php echo timeago($DATEPOSTED); ?></div>
                            </div>
                        </div>
                    </div>

                    <?php } } if ($count<1){ ?>

                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                        <div class="job_grid border rounded">
                            <div class="position-absolute ab-left">No Data Yet!</div>
                        </div>
                    </div>
                    <?php } ?>


                </div>
                <!-- row -->

            </div>
        </section>
        <!-- ======================= Related Jobs ======================== -->

        <?php include 'include/footer.php' ?>

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

</html>