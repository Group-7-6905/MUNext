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
<!-- Mirrored from themezhub.net/live-workplex/workplex/job-detail.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Feb 2022 12:07:20 GMT -->

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
                                            <h4 class="mb-0 ft-medium fs-md"><?php echo $JOBTITLE ?></h4>
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
                                            <span
                                                class="px-2 py-1 ft-medium medium rounded theme-cl theme-bg-light mr-2"><?php echo $JOBSTATUS ?>
                                            </span>
                                            <span
                                                class="px-2 py-1 ft-medium medium rounded text-danger bg-light-danger mr-2"><?php echo $COMPANYINDUSTRY ?></span>
                                            <span
                                                class="px-2 py-1 ft-medium medium rounded text-purple bg-light-purple"><?php echo $COMPANYSPECIALISM ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="jbd-01-right text-right hide-1023">
                                    <div class="jbl_button mb-2">

                                        <?php if (empty($session_id)) { ?>
                                        <a href="#" data-toggle="modal" data-target="#login"
                                            class="btn rounded theme-bg-light theme-cl fs-sm ft-medium">Apply This
                                            Job</a>
                                        <?php } else { ?>
                                        <a href="#apply"
                                            class="btn rounded theme-bg-light theme-cl fs-sm ft-medium">Apply This
                                            Job</a>
                                        <?php } ?>

                                    </div>
                                    <div class="jbl_button"><a
                                            href="employer-detail.php?companyid=<?php echo $COMPANYID ?> "
                                            class="btn rounded bg-white border fs-sm ft-medium">View Company</a></div>
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
                                            <p class="extra">Not a member?<a href="#et-register-wrap" class="text-dark">
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
                                    <p class="extra">Not a member?<a href="#et-register-wrap" class="text-dark">
                                            Register</a></p>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!---Sidebar--->
                    <?php } elseif (((empty($APPLICANTPHOTO)) || (empty($DEGREE)) || (empty($CITY)) || (empty($ADDRESS)) || (empty($SKILLS)) || (empty($LinkedIn_link)))) {
					?>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                        <div class="jb-apply-form bg-white rounded py-3 px-4 box-static" id="apply">
                            <h4 class="ft-medium fs-md mb-3">Interested in this job?</h4>
                            <a href="./dashboard/dashboard-add-profile.php"
                                class="btn btn-md rounded theme-bg  text-light ft-medium fs-sm full-width">Complete
                                Profile</a>
                        </div>
                    </div>
                    <?php } else { ?>
                    <!-- Sidebar -->
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                        <form method="post" enctype="multipart/form-data">
                            <div class="jb-apply-form bg-white rounded py-3 px-4 box-static" id="apply">
                                <h4 class="ft-medium fs-md mb-3">Interested in this job?</h4>

                                <div class="_apply_form_form">

                                    <div class="form-group">
                                        <label class="text-dark mb-1 ft-medium medium">Upload Resume:<font>pdf, doc,
                                                docx</font></label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="customFile"
                                                name="fileToUpload" required onchange="set_btn()"
                                                accept=".doc, .docx,.pdf">
                                            <label class="custom-file-label" for="customFile">Choose file</label>
                                        </div>
                                    </div>

                                    <style>
                                    #enabled_btn {
                                        display: none;
                                    }
                                    </style>
                                    <script>
                                    function set_btn() {
                                        // alert('worked');
                                        var customFile = $('#customFile')
                                            .val(); //document.getElementById("title_select").innerHTML

                                        if (customFile != "") {
                                            document.getElementById("enabled_btn").style.display = "block";
                                            document.getElementById("disabled_btn").style.display = "none";
                                        } else {
                                            document.getElementById("disabled_btn").style.display = "none";
                                            document.getElementById("enabled_btn").style.display = "block";
                                        }
                                        // document.getElementById("set_wallet").innerHTML = set_wallet;
                                    }
                                    </script>

                                    <input type="hidden" name="JOBID" value="<?php echo $JOBID ?>">
                                    <input type="hidden" name="APPLICANTID" value="<?php echo $session_id ?>">

                                    <div class="form-group">
                                        <div class="terms_con">
                                            <input id="aa3" class="checkbox-custom" name="Coffee" type="checkbox"
                                                required checked>
                                            <label for="aa3" class="checkbox-custom-label">I agree to pirvacy
                                                policy</label>
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <?php
											$queryapply = "SELECT * from tbljobapplication where APPLICANTID = '$session_id' and JOBID = '$JOBID'";
											$resultapply = mysqli_query($con, $queryapply);
											$countapply = mysqli_num_rows($resultapply);

											if ($countapply < 1) {
											?>
                                        <button type="button" disabled id="disabled_btn"
                                            class="btn btn-md rounded theme-bg  text-light ft-medium fs-sm full-width">Apply
                                            For This Job</button>

                                        <button type="button" data-toggle="modal" data-target=".bd-example-modal-lg"
                                            name="apply_job" id="enabled_btn"
                                            class="btn btn-md rounded theme-bg text-light ft-medium fs-sm full-width">Apply
                                            For This Job</button>
                                        <?php } else { ?>
                                        <button type="button" disabled
                                            class="btn btn-md rounded theme-bg  text-light ft-medium fs-sm full-width">Job
                                            Already Applied!</button>
                                        <?php } ?>
                                    </div>






                                </div>
                            </div>


                            <!-- Large modal -->

                            <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog"
                                aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-headers">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span class="ti-close"></span>
                                            </button>
                                        </div>

                                        <div class="modal-body p-5">
                                            <div class="text-center mb-4">
                                                <!-- <h4 class="ft-medium fs-md mb-3">Appy to <?php echo $JOBTITLE ?> </h4> -->
                                            </div>

                                            <div class="col-12 col-md-12 col-12 text-center miliods">

                                                <div class="d-block border rounded mfliud-bot mb-4">
                                                    <h4
                                                        class="ft-medium fs-md mt-2 mb-0 mr-2 mb-2 d-inline-flex px-2 py-1 rounded theme-cl theme-bg-light">
                                                        Application Review </h4>
                                                    <div class="cdt_author px-2 pt-5 pb-4">
                                                        <div
                                                            class="dash_auth_thumb circle p-1 border d-inline-flex mx-auto mb-2">
                                                            <img src="./<?php echo $APPLICANTPHOTO ?>"
                                                                class="img-fluid circle" width="100" alt="" />
                                                        </div>
                                                        <div class="dash_caption mb-3">
                                                            <h4 class="fs-lg ft-medium mb-0 lh-1">
                                                                <?php echo $FULLNAME ?></h4>
                                                            <p class="m-0 p-0"><?php echo $JOBTITLE ?></p>
                                                            <span class="text-muted smalls"><i
                                                                    class="lni lni-map-marker mr-1"></i><?php echo $CITY ?>,<?php echo $COUNTRY ?></span>
                                                        </div>
                                                        <div class="jb-list-01-title px-2">
                                                            <span
                                                                class="px-2 mb-2 d-inline-flex py-1 rounded text-purple bg-light-purple">0<?php echo $APPLICANTID ?></span>
                                                            <span
                                                                class="mr-2 mb-2 d-inline-flex px-2 py-1 rounded theme-cl theme-bg-light">Active</span>
                                                            <span
                                                                class="mr-2 mb-2 d-inline-flex px-2 py-1 rounded text-warning bg-light-warning"><?php echo $SUBCATEGORY ?></span>
                                                            <!-- <span class="mr-2 mb-2 d-inline-flex px-2 py-1 rounded text-danger bg-light-danger">Magento</span> -->
                                                            <br>
                                                            <span
                                                                class="mr-2 mb-2 d-inline-flex px-2 py-1 rounded text-info bg-light-info"><?php echo $SKILLS ?></span>

                                                        </div>
                                                    </div>

                                                </div>

                                            </div>



                                            <!-- <form method="post"> -->
                                            <?php
												// $sql = "SELECT * from tbljobscreening_ques where job_id ='$JOBID'  ORDER BY id DESC";
												// $runjob = mysqli_query($con, $sql);
												// $c = mysqli_num_rows($runjob);
												// if ($c > 0) {
												?>
                                            <div class="jbd-details mb-3">
                                                <h5>Screening Questions:</h5>
                                                <div class="position-relative row">
                                                    <div class="col-lg-12 col-md-12 col-12">

                                                        <?php
															// $sql = "SELECT * from tbljobscreening_ques where job_id ='$JOBID'  ORDER BY id DESC";
															// $runjob = mysqli_query($con, $sql);
															// while ($rowjob = mysqli_fetch_array($runjob)) {
															// 	$question_id = $rowjob['question_id'];

															$query = "SELECT * from tblscreening ORDER BY RAND() LIMIT 10" or die(mysqli_error($con));
															$run = mysqli_query($con, $query);
															$count = 0;
															while ($row = mysqli_fetch_array($run)) {
																$count++;
																// $jobque_id[] = $row['id'];
															?> <br>
                                                        <div class="form-group">
                                                            <div class="mb-2 mr-4 ml-lg-0 mr-lg-4">
                                                                <div class="d-flex align-items-center">
                                                                    <div
                                                                        class="rounded-circle bg-light-danger theme-cl p-1 small d-flex align-items-center justify-content-center">
                                                                        <i
                                                                            class="fas fa-question small text-danger"></i>
                                                                    </div>
                                                                    <h6 class="mb-0 ml-3 text-muted fs-sm">
                                                                        <?php echo $count ?>.<i> Question
                                                                            <strong>(<?php echo $row['q_title'] ?>)</strong></i>
                                                                    </h6>
                                                                </div>


                                                                <label
                                                                    class="checkbox-custom-label text-dark ft-medium"><b><?php echo $row['question'] ?></b><span>?</span></label>

                                                                <!-- <div class="col-xl-12 col-lg-12 col-md-12">
																			<label class="muted-text ft-medium" for="<?php echo $row['id'] ?>">Choose Correct Option </label><br>
																		</div> -->

                                                                <input type="hidden" name="id[]"
                                                                    value="<?php echo $row['id'] ?>">


                                                                <input type="radio" id="A<?php echo $row['id'] ?>"
                                                                    class="checkbox-custom"
                                                                    name="ans[<?php echo $row['id'] ?>]" value="A"
                                                                    required>
                                                                <label for="A<?php echo $row['id'] ?>"
                                                                    class="checkbox-custom-label">A.
                                                                    <?php echo $row['opt_A'] ?></label>



                                                                <input type="radio" id="B<?php echo $row['id'] ?>"
                                                                    class="checkbox-custom"
                                                                    name="ans[<?php echo $row['id'] ?>]" value="B"
                                                                    required>

                                                                <label for="B<?php echo $row['id'] ?>"
                                                                    class="checkbox-custom-label">B.
                                                                    <?php echo $row['opt_B'] ?></label>


                                                                <input type="radio" id="C<?php echo $row['id'] ?>"
                                                                    class="checkbox-custom"
                                                                    name="ans[<?php echo $row['id'] ?>]" value="C"
                                                                    required>

                                                                <label for="C<?php echo $row['id'] ?>"
                                                                    class="checkbox-custom-label">C.
                                                                    <?php echo $row['opt_C'] ?></label>


                                                                <input type="radio" id="D<?php echo $row['id'] ?>"
                                                                    class="checkbox-custom"
                                                                    name="ans[<?php echo $row['id'] ?>]" value="D"
                                                                    required>

                                                                <label for="D<?php echo $row['id'] ?>"
                                                                    class="checkbox-custom-label">D.
                                                                    <?php echo $row['opt_D'] ?></label>


                                                                <input type="radio" id="E<?php echo $row['id'] ?>"
                                                                    class="checkbox-custom"
                                                                    name="ans[<?php echo $row['id'] ?>]" value="E"
                                                                    required>

                                                                <label for="E<?php echo $row['id'] ?>"
                                                                    class="checkbox-custom-label">E.
                                                                    <?php echo $row['opt_E'] ?></label>




                                                                <hr>
                                                            </div>
                                                        </div>




                                                        <?php //}
															} ?>


                                                    </div>
                                                </div>
                                            </div>
                                            <?php //} 
												?>


                                            <!-- <button type="submit" name="job" class="btn btn-md rounded theme-bg text-light ft-medium fs-sm full-width">Submit</button> -->

                                            <button type="submit" name="apply_job"
                                                class="btn btn-md rounded theme-bg text-light ft-medium fs-sm full-width">Submit
                                                Now</button>

                                            <!-- </form> -->

                                            <?php
												if (isset($_POST['job'])) {

													$ques = $_POST['id'];
													$ans = $_POST['ans'];


													for ($i = 0; $i < sizeof($ques); $i++) {
														// '" . $ques[$i] . "'
														if ($ans[$ques[$i]] == 1) {
															$say = 'Yes';
														} else {
															$say = 'No';
														}

														echo $ques[$i] . '. ' . $say . ' ' . $ans[$ques[$i]] . '<br>';
													}
												}
												?>
                                        </div>
                                    </div>
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
					$query = "SELECT * from tbljob where JOBTITLE LIKE '%$JOBTITLE%' or JOBTYPE LIKE '%$JOBTYPE%'  ORDER BY JOBID DESC" or die(mysqli_error($con));

					$run = mysqli_query($con, $query);
					while ($row = mysqli_fetch_array($run)) {
						$COMPANYID = $row['COMPANYID'];
						$JOBID = $row['JOBID'];
						$JOBSTATUS = $row['JOBSTATUS'];
						$DATEPOSTED = $row['DATEPOSTED'];
						$SALARY = $row['SALARY'];

						$querycomp = "SELECT * from tblcompany WHERE COMPANYID = '$COMPANYID'";
						$resultcomp = mysqli_query($con, $querycomp);
						$rowcomp = mysqli_fetch_array($resultcomp);

						$COMPANYNAME = $rowcomp['COMPANYNAME'];
						$COMPANYLOGO = $rowcomp['COMPANYLOGO'];
						$COMPANYADDRESS = $rowcomp['COMPANYADDRESS'];
						$COMPANYCOUNTRY = $rowcomp['COMPANYCOUNTRY'];
						$COMPANYCITY = $rowcomp['COMPANYCITY'];

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
                                <a href="job-detail.php?jobid=<?php echo $JOBID ?>"
                                    class="d-block text-center m-auto"><img src="./<?php echo $COMPANYLOGO ?>"
                                        class="img-fluid" width="70" alt="" /></a>
                            </div>
                            <div class="job_grid_caption text-center pb-5 px-3">
                                <h6 class="mb-0 lh-1 ft-medium medium"><a
                                        href="employer-detail.php?companyid=<?php echo $COMPANYID ?>"
                                        class="text-muted medium"><?php echo $COMPANYNAME; ?></a></h6>
                                <h4 class="mb-0 ft-medium medium"><a href="job-detail.php?jobid=<?php echo $JOBID ?>"
                                        class="text-dark fs-md"><?php echo $row['JOBTITLE']; ?></a></h4>
                                <div class="jbl_location"><i
                                        class="lni lni-map-marker mr-1"></i><span><?php echo $COMPANYCITY ?>,
                                        <?php echo $COMPANYCOUNTRY ?></span></div>
                            </div>
                            <div class="job_grid_footer pb-4 px-3 d-flex align-items-center justify-content-between">
                                <?php if ($SALARY > 0) { ?><div class="df-1 text-muted"><i
                                        class="lni lni-wallet mr-1"></i>: N<?php echo number_format($SALARY, 2) ?>
                                </div> <?php } ?>
                                <div class="df-1 text-muted"><i
                                        class="lni lni-timer mr-1"></i><?php echo timeago($DATEPOSTED); ?></div>
                            </div>
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

<!-- Mirrored from themezhub.net/live-workplex/workplex/job-detail.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Feb 2022 12:07:20 GMT -->

</html>