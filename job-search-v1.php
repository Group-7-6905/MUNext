<!DOCTYPE html>
<html lang="zxx">

<!-- Mirrored from themezhub.net/live-workplex/workplex/job-search-v2.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Feb 2022 12:04:33 GMT -->

<?php
require('./mysqli_connect.php');
// header.php

include 'session_check.php';

include "include/helper.php";



if (!empty($_GET['type'])) {
	$type = $_GET['type'];

	// $companyid = $_GET['companyid'];

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
// 	window.location.href = 'job-search.php?companyid=<?php echo $jobid ?>';
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

        <!-- ======================= Searchbar Banner ======================== -->
        <!-- <div class="py-3 theme-bg searchingBar">
			<div class="container">
				<div class="row justify-content-between align-items-center">
					<div class="col-xl-7 col-lg-9 col-md-9 col-sm-12 col-12">
						<form class="bg-white rounded p-1 border">
							<div class="row no-gutters">
								<div class="col-xl-6 col-lg-5 col-md-5 col-sm-12 col-12">
									<div class="form-group mb-0 position-relative">
										<input type="text" class="form-control sm left-ico" placeholder="Job Title, Keyword or Company" />
										<i class="bnc-ico lni lni-search-alt"></i>
									</div>
								</div>
								<div class="col-xl-5 col-lg-5 col-md-5 col-sm-12 col-12">
									<div class="form-group mb-0 position-relative">
										<input type="text" class="form-control sm left-ico" placeholder="Location or Zip Code" />
										<i class="bnc-ico lni lni-target"></i>
									</div>
								</div>
								<div class="col-xl-1 col-lg-2 col-md-2 col-sm-12 col-12">
									<div class="form-group mb-0 position-relative">
										<button class="btn full-width custom-height sm rounded bg-dark text-white fs-md" type="button">Go</button>
									</div>
								</div>
							</div>
						</form>
					</div>

					<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12">
						<div class="d-block position-relative text-right">
							<a href="#" onclick="openSearch()" class="mlb-btn btn ft-medium rounded text-dark bg-light"><i class="ti-bell mr-1"></i>Job Alert</a>
						</div>
					</div>

				</div>
			</div>
		</div> -->
        <!-- ======================= Searchbar Banner ======================== -->
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

        <!-- ======================= All Product List ======================== -->
        <section class="bg-light middle">
            <div class="container">

                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-12">
                        <div class="row align-items-center justify-content-between mx-0 bg-white rounded py-2 mb-4">
                            <div class="col-xl-3 col-lg-4 col-md-5 col-sm-12">
                                <!-- <h6 class="mb-0 ft-medium fs-sm">302 New Jobs Found</h6> -->
                            </div>

                            <div class="col-xl-9 col-lg-8 col-md-7 col-sm-12">
                                <div class="filter_wraps elspo_wrap d-flex align-items-center justify-content-end">
                                    <div class="single_fitres mr-2 br-right">
                                        <form method="post">
                                            <?php
											$sort = '';
											if (isset($_POST['sort'])) {
												$sort = $_POST['sort'];
											}

											?>
                                            <select class="custom-select simple" onchange="submit()" name="sort">
                                                <?php
												if ($sort == "DESC") { ?>
                                                <option value="">Default Sorting</option>
                                                <option value="DESC" selected="">Sort By Recent</option>
                                                <option value="ASC">Sort By Older</option>
                                                <?php } elseif ($sort == "ASC") { ?>
                                                <option value="">Default Sorting</option>
                                                <option value="DESC">Sort By Recent</option>
                                                <option value="ASC" selected="">Sort By Older</option>
                                                <?php } else { ?>
                                                <option value="" selected="">Default Sorting</option>
                                                <option value="DESC">Sort By Recent</option>
                                                <option value="ASC">Sort By Older</option>
                                                <?php } ?>


                                            </select>

                                        </form>
                                    </div>
                                    <div class="single_fitres">
                                        <a href="job-search-v1.php" class="simple-button active theme-cl mr-1"><i
                                                class="ti-layout-grid2"></i></a>
                                        <a href="job-list-v1.php" class="simple-button"><i class="ti-view-list"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- row -->
                <div class="row align-items-center">

                    <?php


					if (!empty($_GET['jobcategoryid'])) {
						$jobcategoryid = $_GET['jobcategoryid'];
						$sql = "SELECT * from tbljob where JOBCATEGORYID = '$jobcategoryid'  ORDER BY JOBID DESC" or die(mysqli_error($con));
					}



					if (empty($sort)) {
						$query = "SELECT * from tbljob ORDER BY JOBID DESC" or die(mysqli_error($con));
						if (!empty($_GET['jobcategoryid'])) {
							$jobcategoryid = $_GET['jobcategoryid'];
							$query = "SELECT * from tbljob where JOBCATEGORYID = '$jobcategoryid'  ORDER BY JOBID DESC" or die(mysqli_error($con));
						}
						if (!empty($_GET['companyid'])) {
							$companyid = $_GET['companyid'];
							$query = "SELECT * from tbljob where COMPANYID = '$companyid'  ORDER BY JOBID DESC" or die(mysqli_error($con));
						}
					} else {
						$query = "SELECT * from tbljob ORDER BY JOBID $sort" or die(mysqli_error($con));
						if (!empty($_GET['jobcategoryid'])) {
							$jobcategoryid = $_GET['jobcategoryid'];
							$query = "SELECT * from tbljob where JOBCATEGORYID = '$jobcategoryid'  ORDER BY JOBID $sort" or die(mysqli_error($con));
						}
						if (!empty($_GET['companyid'])) {
							$companyid = $_GET['companyid'];
							$query = "SELECT * from tbljob where COMPANYID = '$companyid'  ORDER BY JOBID $sort" or die(mysqli_error($con));
						}
					}

					$run = mysqli_query($con, $query);
					while ($row = mysqli_fetch_array($run)) {
						$COMPANYID = $row['COMPANYID'];
						$JOBID = $row['JOBID'];
						$JOBSTATUS = $row['JOBSTATUS'];
						$DATEPOSTED = $row['DATEPOSTED'];
						$SALARY = $row['SALARY'];
						$DEADLINE = $row['DEADLINE'];


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
                                <div class="df-1 text-muted">
                                    <?php if ($SALARY > 0) { ?><i class="lni lni-wallet mr-1"></i>:
                                    N<?php echo number_format($SALARY, 2) ?> <?php } ?><br>
                                    <i class="lni lni-timer mr-1"></i><b>Posted:</b>
                                    <?php echo timeago($DATEPOSTED); ?><br>
                                    <i class="lni lni-calendar mr-1"></i><b>Deadline:</b> <?php echo date($DEADLINE) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php } ?>


                </div>
                <!-- row -->

                <!-- <div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12">
						<ul class="pagination">
							<li class="page-item">
								<a class="page-link" href="#" aria-label="Previous">
									<span class="fas fa-arrow-circle-right"></span>
									<span class="sr-only">Previous</span>
								</a>
							</li>
							<li class="page-item"><a class="page-link" href="#">1</a></li>
							<li class="page-item"><a class="page-link" href="#">2</a></li>
							<li class="page-item active"><a class="page-link" href="#">3</a></li>
							<li class="page-item"><a class="page-link" href="#">...</a></li>
							<li class="page-item"><a class="page-link" href="#">18</a></li>
							<li class="page-item">
								<a class="page-link" href="#" aria-label="Next">
									<span class="fas fa-arrow-circle-right"></span>
									<span class="sr-only">Next</span>
								</a>
							</li>
						</ul>
					</div> 
				</div>-->

            </div>
        </section>
        <!-- ======================= All Product List ======================== -->

        <?php include 'include/footer.php' ?>


        <!-- Job Alert -->
        <div class="w3-ch-sideBar w3-bar-block w3-card-2 w3-animate-right" style="display:none;right:0;" id="Search">
            <div class="rightMenu-scroll">
                <div class="d-flex align-items-center justify-content-between slide-head py-3 px-3">
                    <h4 class="cart_heading fs-md ft-medium mb-0">Make job Alert</h4>
                    <button onclick="closeSearch()" class="close_slide"><i class="ti-close"></i></button>
                </div>

                <div class="cart_action px-3 py-4">
                    <form class="form m-0 p-0">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Job Title, Keyword or Company" />
                        </div>

                        <div class="form-group">
                            <select class="custom-select">
                                <option value="1">Choose Categories</option>
                                <option value="2">Information Technology</option>
                                <option value="3">Cloud Computing</option>
                                <option value="4">Engineering Services</option>
                                <option value="5">Healthcare/Pharma</option>
                                <option value="6">Telecom/ Internet</option>
                                <option value="7">Finance/Insurance</option>
                            </select>
                        </div>

                        <div class="form-group mb-0">
                            <button type="button" class="btn d-block full-width theme-bg text-light">Save &
                                Update</button>
                        </div>
                    </form>
                </div>

                <div class="d-flex align-items-center justify-content-center br-top br-bottom py-2 px-3">
                    <h4 class="cart_heading fs-md mb-0">Featured Companies</h4>
                </div>

                <div class="cart_action px-3 py-3">
                    <div class="row justify-content-center">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="mb-2 text-left">
                                <a href="job-search-v1.html"
                                    class="cats-box rounded bg-light d-flex align-items-center px-2 py-3">
                                    <div class="text-center"><img src="assets/img/c-9.png" class="img-fluid" width="55"
                                            alt=""></div>
                                    <div class="cats-box-caption px-2">
                                        <h4 class="fs-sm mb-0 ft-medium">Web Designing</h4>
                                        <span class="text-muted">302 Jobs</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="mb-2 text-left">
                                <a href="job-search-v1.html"
                                    class="cats-box rounded bg-light d-flex align-items-center px-2 py-3">
                                    <div class="text-center"><img src="assets/img/c-4.png" class="img-fluid" width="55"
                                            alt=""></div>
                                    <div class="cats-box-caption px-2">
                                        <h4 class="fs-sm mb-0 ft-medium">Web Designing</h4>
                                        <span class="text-muted">302 Jobs</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="text-left">
                                <a href="job-search-v1.html"
                                    class="cats-box rounded bg-light d-flex align-items-center px-2 py-3">
                                    <div class="text-center"><img src="assets/img/c-2.png" class="img-fluid" width="55"
                                            alt=""></div>
                                    <div class="cats-box-caption px-2">
                                        <h4 class="fs-sm mb-0 ft-medium">Web Designing</h4>
                                        <span class="text-muted">302 Jobs</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

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

    <script>
    $(window).scroll(function() {
        var scroll = $(window).scrollTop();

        if (scroll >= 200) {
            $(".searchingBar").addClass("fixedSearching");
        } else {
            $(".searchingBar").removeClass("fixedSearching");
        }
    });
    </script>

    <script>
    function openSearch() {
        document.getElementById("Search").style.display = "block";
    }

    function closeSearch() {
        document.getElementById("Search").style.display = "none";
    }
    </script>

</body>

<!-- Mirrored from themezhub.net/live-workplex/workplex/job-search-v2.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Feb 2022 12:04:34 GMT -->

</html>