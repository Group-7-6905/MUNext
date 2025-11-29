<!DOCTYPE html>
<html lang="zxx">

<?php
require('./mysqli_connect.php');
// header.php

//////////////////////Get Company Details////////////////////////
include 'session_check.php';

include "include/helper.php";



if (!empty($_GET['type'])) {
	$type = $_GET['type'];

	// $jobid = $_GET['jobid'];

	if (($type == "delete")) {
		$bookmarkedid = $_GET['bookmarkedid'];
		$delete_sql = "DELETE from tblbookmarkjob where ID='$bookmarkedid'";
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

        <!-- ======================= Home Banner ======================== -->
        <div class="home-banner margin-bottom-0" style="background:#eff6f2 url(assets/img/banner-6.png) no-repeat;">
            <div class="container">
                <div class="row justify-content-start">
                    <div class="col-xl-6 col-lg-9 col-md-12 col-sm-12 col-12">
                        <div class="banner_caption text-left mb-4">
                            <h1 class="banner_title ft-bold mb-1">Where MUN <span class="theme-cl">Talent
                                    Meets</span> Opportunity</h1>
                            <p class="fs-md ft-regular">Your gateway to new-grad jobs, internships, and MUN-exclusive
                                opportunities.</p>
                        </div>
                    </div>
                    <div class="col-xl-8 col-lg-10 col-md-12 col-sm-12 col-12">

                        <form method="GET" action="browse-jobs.php" class="bg-white rounded p-1">
                            <div class="row no-gutters">
                                <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12 col-12">
                                    <div class="form-group mb-0 position-relative">
                                        <input type="text" name="search" class="form-control lg left-ico"
                                            placeholder="Job Title, Company or Location" required />
                                        <i class="bnc-ico lni lni-search-alt"></i>
                                    </div>
                                </div>
                                <div class="col-xl-5 col-lg-4 col-md-4 col-sm-12 col-12">
                                    <div class="form-group mb-0 position-relative">
                                        <select class="custom-select lg b-0" name="search_by">
                                            <option value="">All Fields</option>
                                            <option value="name">Job Title</option>
                                            <option value="company">Company Name</option>
                                            <option value="location">Location</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-2 col-lg-3 col-md-3 col-sm-12 col-12">
                                    <div class="form-group mb-0 position-relative">
                                        <button class="btn full-width custom-height-lg theme-bg text-white fs-md"
                                            type="submit">
                                            <i class="lni lni-search-alt mr-1"></i>Find Job
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="top-searches-key">
                            <ul class="p-0 mt-4 align-items-center d-flex">
                                <li><span class="text-dark ft-medium medium">Top Searches:</span></li>
                                <li><a href="javascript:void(0);" class="">Web Developer</a></li>
                                <li><a href="javascript:void(0);" class="">Dimconnect</a></li>
                                <li><a href="javascript:void(0);" class="">Senior Software Developer</a></li>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- ======================= Home Banner ======================== -->
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
        <!-- ======================= All category ======================== -->
        <section class="space">
            <div class="container">

                <div class="row justify-content-center">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                        <div class="sec_title position-relative text-center mb-5">
                            <h6 class="text-muted mb-0">Popular Categories</h6>
                            <h2 class="ft-bold">Browse Top <span class="theme-cl">Categories</span></h2>
                        </div>
                    </div>
                </div>

                <!-- row -->
                <div class="row align-items-center">

                    <?php
					$count = 0;
					$query = "SELECT * from tbljobsubcategory ORDER BY ID DESC " or die(mysqli_error($con));

					$run = mysqli_query($con, $query);

					while ($row = mysqli_fetch_array($run)) {

						$SUBCATEGORY = $row['SUBCATEGORY'];
						$SUBCATEGORYID = $row['ID'];

						$queryjbs = "SELECT * from tbljob where JOBCATEGORYID = '$SUBCATEGORYID' AND JOBSTATUS = 'Active' ORDER BY JOBID DESC" or die(mysqli_error($con));

						$resultjbs = mysqli_query($con, $queryjbs);
						$rowjbs = mysqli_fetch_array($resultjbs);

						$count = mysqli_num_rows($resultjbs);

						if ($count > 0) {


					?>
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                        <div class="cats-wrap text-center">
                            <a href="browse-jobs.php?jobcategoryid=<?php echo $SUBCATEGORYID ?>"
                                class="cats-box d-block rounded bg-white shadow px-2 py-4">
                                <div
                                    class="text-center mb-2 mx-auto position-relative d-inline-flex align-items-center justify-content-center p-3 theme-bg-light circle">
                                    <i class="lni lni-cloud laptop-phone fs-lg theme-cl"></i>
                                </div>
                                <div class="cats-box-caption">
                                    <h4 class="fs-md mb-0 ft-medium m-catrio"><?php echo $SUBCATEGORY ?></h4>
                                    <span class="text-muted"><?php echo $count ?> Jobs</span>
                                </div>
                            </a>
                        </div>
                    </div>
                    <?php }
					} ?>


                </div>
                <!-- /row -->

                <div class="row justify-content-center">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                        <div class="position-relative text-center">
                            <a href="browse-category.php"
                                class="btn btn-md bg-dark rounded text-light hover-theme">Browse All Categories<i
                                    class="lni lni-arrow-right-circle ml-2"></i></a>
                        </div>
                    </div>
                </div>

            </div>
        </section>
        <!-- ======================= All category ======================== -->

        <!-- ======================= Job List ======================== -->
        <section class="middle gray">
            <div class="container">

                <div class="row justify-content-center">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                        <div class="sec_title position-relative text-center mb-5">
                            <h6 class="text-muted mb-0">Trending Jobs</h6>
                            <h2 class="ft-bold">All Recent <span class="theme-cl">Listed jobs</span></h2>
                        </div>
                    </div>
                </div>

                <!-- row -->
                <div class="row align-items-center">

                    <?php
					$count = 0;
					$query = "SELECT * from tbljob WHERE JOBSTATUS = 
                    'Active' ORDER BY JOBID DESC LIMIT 12" or die(mysqli_error($con));

					$run = mysqli_query($con, $query);
					while ($row = mysqli_fetch_array($run)) {
						$COMPANYID = $row['COMPANYID'];
						$JOBID = $row['JOBID'];
						$JOBSTATUS = $row['JOBSTATUS'];
						$DATEPOSTED = $row['DATEPOSTED'];
						$SALARY = $row['SALARY'];

                        $isActive = ($row['JOBSTATUS'] == 'Active');
	                    $isFilled = ($row['JOBSTATUS'] == 'Filled');

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
                        <div class="job_grid rounded ">
                            <div class="position-absolute ab-left">

                                <form method="post">
                                    <input type="hidden" name="jobID" value="<?php echo $JOBID ?>">
                                    <input type="hidden" name="userID" value="<?php echo $session_id ?>">
                                    <?php
										$query_apply = "SELECT * from tblbookmarkjob where APPLICANTID = '$session_id' and JOBID = '$JOBID' ORDER BY ID DESC" or die(mysqli_error($con));
										$run_apply = mysqli_query($con, $query_apply);
										$row_apply = mysqli_fetch_array($run_apply);
										$cn_save = mysqli_num_rows($run_apply);

										if ($cn_save > 0) { ?>

                                    <a href="?type=delete&bookmarkedid=<?php echo $row_apply['ID'] ?>"
                                        class="p-3 border circle d-flex align-items-center justify-content-center bg-warning text-white">
                                        <i class="lni lni-heart-filled position-absolute"></i></a>

                                    <!-- <a href="?type=delete&bookmarkedid=<?php echo $row_apply['ID'] ?>&jobid=<?php echo $JOBID ?>" class="btn btn-md rounded gray fs-sm ft-medium mr-2 text-warning">Remove Saved Job</a> -->

                                    <?php } else { ?>
                                    <!-- <button type="submit" name="save_job" class="btn btn-md rounded gray fs-sm ft-medium mr-2">Save This Job</button> -->

                                    <button type="submit" name="save_job"
                                        class="p-3 border circle d-flex align-items-center justify-content-center bg-white text-gray">
                                        <i
                                            class="lni lni-heart-filled position-absolute snackbar-wishlist"></i></button>

                                    <?php } ?>
                                </form>



                            </div>


                            <div class="position-absolute ab-right"><span
                                    class="medium ft-medium text-warning mr-2 "><?php echo $row['JOBTYPE']; ?></span>
                                <!-- <span
                                    class="medium text-success theme-cl theme-bg-light px-2 py-1 rounded"><?php echo $JOBSTATUS ?></span> -->

                                <?php if ($isFilled): ?>
                                <span
                                    class="px-2 py-1 ft-medium text-danger medium rounded theme-cl bg-light-danger mr-2">Position
                                    Filled</span>
                                <?php elseif ($isActive): ?>
                                <span class="px-2 py-1 ft-medium medium rounded text-success theme-bg-light mr-2">Hiring
                                </span>
                                <?php endif ?>
                            </div>
                            <div class="job_grid_thumb mb-3 pt-5 px-3">
                                <a href="job-detail.php?jobid=<?php echo $JOBID ?>" class="d-block text-center m-auto">
                                    <!-- <img src="./<?php echo $COMPANYLOGO ?>"
                                        class="img-fluid" width="70" alt="" /> -->
                                    <?php if (!empty($COMPANYLOGO)): ?>
                                    <img src="<?php echo htmlspecialchars($COMPANYLOGO); ?>" alt="Company Logo"
                                        class="company-logo-col"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <?php endif; ?>
                                    <div class="company-logo-placeholder-col"
                                        style="<?php echo !empty($COMPANYLOGO) ? 'display:none;' : ''; ?>">
                                        <span><?php echo strtoupper(substr($COMPANYNAME, 0, 1)); ?></span>

                                    </div>
                                </a>
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
                                        class="lni lni-wallet mr-1"></i>: $<?php echo number_format($SALARY, 2) ?>
                                </div> <?php } ?>
                                <div class="df-1 text-muted"><i
                                        class="lni lni-timer mr-1"></i><?php echo timeago($DATEPOSTED); ?></div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>


                </div>
                <!-- row -->

                <div class="row justify-content-center">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                        <div class="position-relative text-center">
                            <a href="browse-jobs.php" class="btn btn-md theme-bg rounded text-light hover-theme">Explore
                                More Jobs<i class="lni lni-arrow-right-circle ml-2"></i></a>
                        </div>
                    </div>
                </div>

            </div>
        </section>
        <!-- ======================= Job List ======================== -->

        <!-- ============================ Our Partner Start ================================== -->
        <section class="bg-cover" style="background:#862633 url(assets/img/curve.svg)no-repeat">
            <div class="container">

                <div class="row justify-content-center">
                    <div class="col-xl-5 col-lg-7 col-md-9 col-sm-12">
                        <div class="sec_title position-relative text-center mb-5">
                            <h6 class="text-light mb-0">Current Openings</h6>
                            <h2 class="ft-bold text-light">We Have Worked with 10,000+ Trusted Companies</h2>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-xl-10 col-lg-11 col-md-12 col-sm-12">
                        <div class="row justify-content-center">

                            <?php
							$count = 0;
							$query = "SELECT * from tblcompany ORDER BY COMPANYID DESC" or die(mysqli_error($con));

							$run = mysqli_query($con, $query);
							while ($row = mysqli_fetch_array($run)) {
								$COMPANYID = $row['COMPANYID'];
								$COMPANYNAME = $row['COMPANYNAME'];
								$COMPANYLOGO = $row['COMPANYLOGO'];
								$COMPANYADDRESS = $row['COMPANYADDRESS'];
								$COMPANYCOUNTRY = $row['COMPANYCOUNTRY'];
								$COMPANYCITY = $row['COMPANYCITY'];

								$queryjbs = "SELECT * from tbljob WHERE COMPANYID = '$COMPANYID' AND JOBSTATUS ='Active' ORDER BY JOBID DESC" or die(mysqli_error($con));

								$resultjbs = mysqli_query($con, $queryjbs);
								$rowjbs = mysqli_fetch_array($resultjbs);

								$count = mysqli_num_rows($resultjbs);

								if ($count > 0) {


							?>
                            <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-12">
                                <div class="cats-wrap text-left">
                                    <a href="job-list-v1.php?companyid=<?php echo $COMPANYID ?>"
                                        class="cats-box rounded bg-white d-flex align-items-center px-2 py-3">
                                        <div class="text-center">
                                            <?php if (!empty($COMPANYLOGO)): ?>
                                            <img src="<?php echo htmlspecialchars($COMPANYLOGO); ?>" alt="Company Logo"
                                                class="company-logo"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <?php endif; ?>
                                            <div class="company-logo-placeholder"
                                                style="<?php echo !empty($COMPANYLOGO) ? 'display:none;' : ''; ?>">
                                                <?php echo strtoupper(substr($COMPANYNAME, 0, 1)); ?>
                                            </div>
                                        </div>
                                        <div class="cats-box-caption px-2">
                                            <h4 class="fs-md mb-0 ft-medium"><?php echo $COMPANYNAME ?></h4>
                                            <span class="text-muted"><?php echo $count ?> Jobs</span>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <?php }
							} ?>

                        </div>
                    </div>
                </div>

            </div>
            <div class="ht-50"></div>
        </section>
        <!-- ============================ Our Partner End ================================== -->

        <!-- ======================= Customer Review ======================== -->
        <section class="middle">
            <div class="container">

                <div class="row justify-content-center">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                        <div class="sec_title position-relative text-center mb-5">
                            <h6 class="text-muted mb-0">Our Reviews</h6>
                            <h2 class="ft-bold">What MUN Alumni Are <span class="theme-cl">Saying</span></h2>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-xl-9 col-lg-10 col-md-12 col-sm-12">
                        <div class="reviews-slide px-3">

                            <!-- single review -->
                            <div class="single_review">
                                <div class="sng_rev_thumb">
                                    <figure><img src="assets/img/team-1.jpg" class="img-fluid circle" alt="" /></figure>
                                </div>
                                <div class="sng_rev_caption text-center">
                                    <div class="rev_desc mb-4">
                                        <p class="fs-md">MUNext made my job search after graduation so much easier. I
                                            discovered internship openings I wouldn’t have found anywhere else, and
                                            within weeks, I landed a full-time software engineering role right here in
                                            Newfoundland</p>
                                    </div>
                                    <div class="rev_author">
                                        <h4 class="mb-0">Mark Jevenue</h4>
                                        <span class="fs-sm">B.Eng. Mechanical Engineering, Class of 2020</span>
                                    </div>
                                </div>
                            </div>

                            <!-- single review -->
                            <div class="single_review">
                                <div class="sng_rev_thumb">
                                    <figure><img src="assets/img/team-2.jpg" class="img-fluid circle" alt="" /></figure>
                                </div>
                                <div class="sng_rev_caption text-center">
                                    <div class="rev_desc mb-4">
                                        <p class="fs-md">As an international graduate, I struggled to find employers who
                                            valued my academic background. Through MUNext, I connected directly with
                                            companies that recruit MUN alumni. It felt like a community invested in my
                                            success.</p>
                                    </div>
                                    <div class="rev_author">
                                        <h4 class="mb-0">Henna Bajaj</h4>
                                        <span class="fs-sm">MSc Software Engineering, 2021</span>
                                    </div>
                                </div>
                            </div>

                            <!-- single review -->
                            <div class="single_review">
                                <div class="sng_rev_thumb">
                                    <figure><img src="assets/img/team-3.jpg" class="img-fluid circle" alt="" /></figure>
                                </div>
                                <div class="sng_rev_caption text-center">
                                    <div class="rev_desc mb-4">
                                        <p class="fs-md">I used MUNext to transition from student projects to a
                                            professional engineering career. The platform helped me find opportunities
                                            aligned with my interests, and the interface made applying simple and
                                            stress-free</p>
                                    </div>
                                    <div class="rev_author">
                                        <h4 class="mb-0">John Cenna</h4>
                                        <span class="fs-sm">B.Sc. Computer Science, Class of 2022</span>
                                    </div>
                                </div>
                            </div>

                            <!-- single review -->
                            <div class="single_review">
                                <div class="sng_rev_thumb">
                                    <figure><img src="assets/img/team-4.jpg" class="img-fluid circle" alt="" /></figure>
                                </div>
                                <div class="sng_rev_caption text-center">
                                    <div class="rev_desc mb-4">
                                        <p class="fs-md">As an international graduate, I struggled to find employers who
                                            valued my academic background. Through MUNext, I connected directly with
                                            companies that recruit MUN alumni. It felt like a community invested in my
                                            success.</p>
                                    </div>
                                    <div class="rev_author">
                                        <h4 class="mb-0">Madhu Sharma</h4>
                                        <span class="fs-sm">MBA, Class of 2021</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- ======================= Customer Review ======================== -->



        <?php include 'include/footer.php' ?>

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