<!DOCTYPE html>
<html lang="zxx">

<?php
require('./mysqli_connect.php');
	// header.php
	include 'session_check.php';

	include "include/helper.php";


//////////////////////Get Company Details////////////////////////
$sort = '';
if (!empty($_GET['sort'])) {
	$sort = $_GET['sort'];
}




$all = '';
$ft = '';
$pt = '';
$cb = '';
$in = '';
$tp = '';


$query = "SELECT * from tbljob ORDER BY JOBID $sort" or die(mysqli_error($con));

$search = '';
$search_by = '';
if (isset($_POST['search_btn'])) {
	if (!empty($_POST['search'])) {
		$search = trim($_POST['search']);
	}

	if (!empty($_POST['search_by'])) {
		$search_by = trim($_POST['search_by']);
	}

	if (!empty($_POST['category'])) {
		$category = trim($_POST['category']);
	}

	if (!empty($_POST['jtype'])) {
		$jtype = trim($_POST['jtype']);
		if ($jtype == '') {
			$all = "checked";
		} elseif ($jtype == 'Full time') {
			$ft = "checked";
		} elseif ($jtype == 'Part Time') {
			$pt = "checked";
		} elseif ($jtype == 'Contract') {
			$cb = "checked";
		} elseif ($jtype == 'Internship') {
			$in = "checked";
		} elseif ($jtype == 'Temporary') {
			$tp = "checked";
		}
	}


	if (($search_by == "name")) {
		// $query = "SELECT * from tbljob where JOBTITLE LIKE '%$search%' ORDER BY JOBID $sort" or die(mysqli_error($con));

		if ((!empty($category)) and (!empty($jtype))) {
			$query = "SELECT * from tbljob where JOBTITLE LIKE '%$category%' or JOBTITLE LIKE '%$search%' or JOBTYPE LIKE '%$jtype%' ORDER BY JOBID $sort" or die(mysqli_error($con));
		} elseif (!empty($jtype)) {
			$query = "SELECT * from tbljob where JOBTYPE LIKE '%$jtype%' or JOBTITLE LIKE '%$search%' ORDER BY JOBID $sort" or die(mysqli_error($con));
		} elseif (!empty($category)) {
			$query = "SELECT * from tbljob where JOBTITLE LIKE '%$category%' or JOBTITLE LIKE '%$search%' ORDER BY JOBID $sort" or die(mysqli_error($con));
		}
	} elseif (($search_by == "company")) {

		$querycomp = "SELECT * from tblcompany WHERE COMPANYNAME LIKE '%$search%'";
		$resultcomp = mysqli_query($con, $querycomp);
		$rowcomp = mysqli_fetch_array($resultcomp);
		$COMPANYID = $rowcomp['COMPANYID'];
		$query = "SELECT * from tbljob where COMPANYID ='$COMPANYID' ORDER BY JOBID $sort" or die(mysqli_error($con));

		if ((!empty($category)) and (!empty($jtype))) {
			$query = "SELECT * from tbljob where JOBTITLE LIKE '%$category%' or COMPANYID ='$COMPANYID' or JOBTYPE LIKE '%$jtype%' ORDER BY JOBID $sort" or die(mysqli_error($con));
		} elseif (!empty($jtype)) {
			$query = "SELECT * from tbljob where JOBTYPE LIKE '%$jtype%' or COMPANYID ='$COMPANYID' ORDER BY JOBID $sort" or die(mysqli_error($con));
		} elseif (!empty($category)) {
			$query = "SELECT * from tbljob where JOBTITLE LIKE '%$category%' or COMPANYID ='$COMPANYID' ORDER BY JOBID $sort" or die(mysqli_error($con));
		}
	} elseif (($search_by == "location")) {


		$querycomp = "SELECT * from tblcompany WHERE COMPANYADDRESS LIKE '%$search%' or COMPANYCITY LIKE '%$search%' or COMPANYCOUNTRY LIKE '%$search%'";
		$resultcomp = mysqli_query($con, $querycomp);
		$rowcomp = mysqli_fetch_array($resultcomp);
		$COMPANYID = $rowcomp['COMPANYID'];
		$query = "SELECT * from tbljob where COMPANYID ='$COMPANYID' ORDER BY JOBID $sort" or die(mysqli_error($con));

		if ((!empty($category)) and (!empty($jtype))) {
			$query = "SELECT * from tbljob where JOBTITLE LIKE '%$category%' or COMPANYID ='$COMPANYID' or JOBTYPE LIKE '%$jtype%' ORDER BY JOBID $sort" or die(mysqli_error($con));
		} elseif (!empty($jtype)) {
			$query = "SELECT * from tbljob where JOBTYPE LIKE '%$jtype%' or COMPANYID ='$COMPANYID' ORDER BY JOBID $sort" or die(mysqli_error($con));
		} elseif (!empty($category)) {
			$query = "SELECT * from tbljob where JOBTITLE LIKE '%$category%' or COMPANYID ='$COMPANYID' ORDER BY JOBID $sort" or die(mysqli_error($con));
		}
	} elseif ((!empty($category)) || (!empty($jtype))) {
		// $query = "SELECT * from tbljob where JOBTYPE LIKE '%$jtype%' ORDER BY JOBID $sort" or die(mysqli_error($con));

		if ((!empty($category)) and (!empty($jtype))) {
			$query = "SELECT * from tbljob where JOBTYPE LIKE '%$jtype%' or JOBTITLE LIKE '%$category%' ORDER BY JOBID $sort" or die(mysqli_error($con));
		} elseif (!empty($jtype)) {
			$query = "SELECT * from tbljob where JOBTYPE LIKE '%$jtype%' ORDER BY JOBID $sort" or die(mysqli_error($con));
		} elseif (!empty($category)) {
			$query = "SELECT * from tbljob where JOBTITLE LIKE '%$category%' ORDER BY JOBID $sort" or die(mysqli_error($con));
		}
	}
}

?>
<!-- Mirrored from themezhub.net/live-workplex/workplex/job-detail.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Feb 2022 12:07:20 GMT -->

<?php include 'include/head.php' ?>

<body>


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

    <!-- ======================= Top Breadcrubms ======================== -->
    <div class="bg-title py-5" data-overlay="0">
        <div class="ht-30"></div>
        <div class="container">
            <div class="row">
                <div class="colxl-12 col-lg-12 col-md-12">
                    <h1 class="ft-medium">Explore All Jobs</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#" class="text-light">Home</a></li>
                            <li class="breadcrumb-item"><a href="#" class="text-light">Job</a></li>
                            <li class="breadcrumb-item active text-warning" aria-current="page">Job Search</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="ht-30"></div>
    </div>
    <!-- ======================= Top Breadcrubms ======================== -->
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
    <!-- ============================ Main Section Start ================================== -->
    <section class="bg-light">
        <div class="container">
            <div class="row">

                <div class="col-lg-4 col-md-12 col-sm-12">
                    <form method="post">
                        <div class="bg-white rounded">

                            <div
                                class="sidebar_header d-flex align-items-center justify-content-between px-4 py-3 br-bottom">
                                <h4 class="ft-medium fs-lg mb-0">Search Filter</h4>
                                <div class="ssh-header">
                                    <a href="javascript:void(0);" class="clear_all ft-medium text-muted">Clear
                                        All</a>
                                    <a href="#search_open" data-toggle="collapse" aria-expanded="false" role="button"
                                        class="collapsed _filter-ico ml-2"><i class="lni lni-text-align-right"></i></a>
                                </div>
                            </div>

                            <!-- Find New Property -->
                            <div class="sidebar-widgets collapse miz_show" id="search_open" data-parent="#search_open">

                                <div class="search-inner">

                                    <?php
										// $search = '';
										// $search_by = '';
										// if (isset($_POST['search_btn'])) {
										// 	if (!empty($_POST['search'])) {
										// 		$search = trim($_POST['search']);
										// 	}

										// 	if (!empty($_POST['search_by'])) {
										// 		$search_by = trim($_POST['search_by']);
										// 	}

										// 	if (!empty($_POST['category'])) {
										// 		$category = trim($_POST['category']);
										// 	}

										// 	if (!empty($_POST['jtype'])) {
										// 		$jtype = trim($_POST['jtype']);
										// 	}
										// }


										?>
                                    <div class="filter-search-box px-4 pt-3 pb-0">
                                        <div class="form-group">
                                            <input type="text" name="search" class="form-control"
                                                placeholder="Search...">
                                        </div>
                                        <div class="form-group">
                                            <!-- <input type="text" class="form-control" name="search_by" placeholder="Location, City.."> -->
                                            <select class="custom-select simple" name="search_by">
                                                <option value="" selected="" hidden>Search By</option>
                                                <option value="name">Job</option>
                                                <option value="company">Company Name</option>
                                                <option value="location">Location</option>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="filter_wraps">

                                        <!-- Job categories Search -->
                                        <div class="single_search_boxed px-4 pt-0 br-bottom">
                                            <div class="widget-boxed-header">
                                                <h4>
                                                    <a href="#categories" class="ft-medium fs-md pb-0"
                                                        data-toggle="collapse" aria-expanded="true" role="button">Job
                                                        Categories</a>
                                                </h4>

                                            </div>
                                            <div class="widget-boxed-body collapse show" id="categories"
                                                data-parent="#categories">
                                                <div class="side-list no-border">
                                                    <!-- Single Filter Card -->
                                                    <div class="single_filter_card">
                                                        <div class="card-body p-0">
                                                            <div class="inner_widget_link">
                                                                <ul class="no-ul-list filter-list">


                                                                    <?php
																		$cn = 0;
																		$query_job = "SELECT * from tbljobsubcategory ORDER BY ID DESC LIMIT 10" or die(mysqli_error($con));
																		$run = mysqli_query($con, $query_job);
																		while ($row = mysqli_fetch_array($run)) {
																			$CATEGORYID = $row['CATEGORYID'];
																		?>
                                                                    <li>
                                                                        <input id="<?php echo $row['ID'] ?>"
                                                                            class="checkbox-custom" name="category"
                                                                            type="checkbox"
                                                                            value="<?php echo $row['SUBCATEGORY'] ?>">

                                                                        <label for="<?php echo $row['ID'] ?>"
                                                                            class="checkbox-custom-label"><?php echo $row['SUBCATEGORY'] ?></label>
                                                                    </li>
                                                                    <?php $cn++;
																		}
																		if ($cn < 1) {
																			echo "No Record";
																		} ?>
                                                                    <!-- <li>
																			<input id="a5" class="checkbox-custom" name="Outdoor" type="checkbox">
																			<label for="a5" class="checkbox-custom-label">Apps Developements (17)</label>
																			<ul class="no-ul-list filter-list">
																				<li>
																					<input id="ab1" class="checkbox-custom" name="ADA" type="checkbox">
																					<label for="ab1" class="checkbox-custom-label">IOS Development (12)</label>
																				</li>
																				<li>
																					<input id="ab2" class="checkbox-custom" name="Parking" type="checkbox">
																					<label for="ab2" class="checkbox-custom-label">Android Development (04)</label>
																				</li>
																			</ul>
																		</li> -->

                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>




                                        <!-- Job types Search -->
                                        <div class="single_search_boxed px-4 pt-0 br-bottom">
                                            <div class="widget-boxed-header">
                                                <h4>
                                                    <a href="#jbtypes" data-toggle="collapse" aria-expanded="false"
                                                        role="button" class="ft-medium fs-md pb-0 collapsed">Job
                                                        Type</a>
                                                </h4>

                                            </div>
                                            <div class="widget-boxed-body collapse" id="jbtypes" data-parent="#jbtypes">
                                                <div class="side-list no-border">
                                                    <!-- Single Filter Card -->
                                                    <div class="single_filter_card">
                                                        <div class="card-body p-0">
                                                            <div class="inner_widget_link">
                                                                <ul class="no-ul-list filter-list">
                                                                    <li>
                                                                        <input id="e1" class="radio-custom" name="jtype"
                                                                            type="radio" value="" <?php echo $all ?>>
                                                                        <label for="e1"
                                                                            class="radio-custom-label">All</label>
                                                                    </li>
                                                                    <li>
                                                                        <input id="e2" class="radio-custom" name="jtype"
                                                                            type="radio" value="Full time"
                                                                            <?php echo $ft ?>>
                                                                        <label for="e2" class="radio-custom-label">Full
                                                                            Time</label>
                                                                    </li>
                                                                    <li>
                                                                        <input id="e3" class="radio-custom" name="jtype"
                                                                            type="radio" value="Part Time"
                                                                            <?php echo $pt ?>>
                                                                        <label for="e3" class="radio-custom-label">Part
                                                                            Time</label>
                                                                    </li>
                                                                    <li>
                                                                        <input id="e4" class="radio-custom" name="jtype"
                                                                            type="radio" value="Contract"
                                                                            <?php echo $cb ?>>
                                                                        <label for="e4"
                                                                            class="radio-custom-label">Contract
                                                                            Base</label>
                                                                    </li>
                                                                    <li>
                                                                        <input id="e5" class="radio-custom" name="jtype"
                                                                            type="radio" value="Internship"
                                                                            <?php echo $in ?>>
                                                                        <label for="e5"
                                                                            class="radio-custom-label">Internship</label>
                                                                    </li>
                                                                    <li>
                                                                        <input id="e6" class="radio-custom" name="jtype"
                                                                            type="radio" value="Temporary"
                                                                            <?php echo $tp ?>>
                                                                        <label for="e6"
                                                                            class="radio-custom-label">Temporary</label>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>





                                    </div>

                                    <div class="form-group filter_button pt-2 pb-4 px-4">
                                        <button type="submit" name="search_btn"
                                            class="btn btn-md theme-bg text-light rounded full-width">Show
                                            Result</button>
                                    </div>
                                </div>
                            </div>
                    </form>
                </div>
                <!-- Sidebar End -->

            </div>

            <!-- Item Wrap Start -->
            <div class="col-lg-8 col-md-12 col-sm-12">

                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-12">
                        <div class="row align-items-center justify-content-between mx-0 bg-white rounded py-2 mb-4">
                            <div class="col-xl-3 col-lg-4 col-md-5 col-sm-12">
                                <!-- <h6 class="mb-0 ft-medium fs-sm">302 New Jobs Found</h6> -->
                                <h6 class="mb-0 ft-medium fs-sm"><?php if (!empty($_POST['search'])) {
																					echo "Search result for <i>" . trim($_POST['search']) . "</i>";
																				} ?></h6>


                            </div>

                            <div class="col-xl-9 col-lg-8 col-md-7 col-sm-12">
                                <div class="filter_wraps elspo_wrap d-flex align-items-center justify-content-end">
                                    <div class="single_fitres mr-2 br-right">
                                        <form method="post" action="">
                                            <?php

												if (isset($_POST['sort'])) {
													$sort = $_POST['sort'];
												?>
                                            <script>
                                            window.location.href = '?sort=<?php echo $sort ?>';
                                            </script>
                                            <?php
													// header("location: ?sort=$sort");
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
                                        <a href="job-search-v1.php" class="simple-button mr-1"><i
                                                class="ti-layout-grid2"></i></a>
                                        <a href="job-list-v1.php" class="simple-button active theme-cl"><i
                                                class="ti-view-list"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- All jobs -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">

                        <?php



							// if (!empty($sort)) {
							// 	$query = "SELECT * from tbljob ORDER BY JOBID $sort" or die(mysqli_error($con));
							// }



							//////////////Submit Btn Was Here/////////////////////////////////////////////////////////////////////

							$count = 0;

							$run = mysqli_query($con, $query);
							while ($row = mysqli_fetch_array($run)) {
								$COMPANYID = $row['COMPANYID'];
								$JOBID = $row['JOBID'];
								$JOBSTATUS = $row['JOBSTATUS'];
								$DATEPOSTED = $row['DATEPOSTED'];
								$SALARY = $row['SALARY'];
								$JOBTYPE = $row['JOBTYPE'];
								$WORKEXPERIENCE = $row['WORKEXPERIENCE'];

								$querycomp = "SELECT * from tblcompany WHERE COMPANYID = '$COMPANYID'";
								$resultcomp = mysqli_query($con, $querycomp);
								$rowcomp = mysqli_fetch_array($resultcomp);

								$COMPANYNAME = $rowcomp['COMPANYNAME'];
								$COMPANYLOGO = $rowcomp['COMPANYLOGO'];
								$COMPANYADDRESS = $rowcomp['COMPANYADDRESS'];
								$COMPANYCOUNTRY = $rowcomp['COMPANYCOUNTRY'];
								$COMPANYCITY = $rowcomp['COMPANYCITY'];
								$COMPANYSPECIALISM = $rowcomp['COMPANYSPECIALISM'];
								$COMPANYINDUSTRY = $rowcomp['COMPANYINDUSTRY'];

								$count++;
							?>

                        <!-- Single job -->
                        <div class="job_grid d-block border rounded px-3 pt-3 pb-2">
                            <div class="jb-list01-flex d-flex align-items-start justify-content-start">
                                <div class="jb-list01-thumb">
                                    <a href="employer-detail.php?companyid=<?php echo $COMPANYID; ?>">
                                        <img src="./<?php echo $COMPANYLOGO ?>" class="img-fluid" width="90" alt="">
                                    </a>
                                    <br>

                                </div>

                                <div class="jb-list01 pl-3">
                                    <div class="jb-list-01-title">
                                        <h5 class="ft-medium mb-1"><a
                                                href="job-detail.php?jobid=<?php echo $JOBID ?>"><?php echo $row['JOBTITLE']; ?>
                                            </a></h5>
                                    </div>
                                    <div class="jb-list-01-info d-block mb-3">
                                        <span class="text-muted mr-2"><i
                                                class="lni lni-map-marker mr-1"></i><?php echo $COMPANYCITY ?>,
                                            <?php echo $COMPANYCOUNTRY ?></span>
                                        <span class="text-muted mr-2"><i
                                                class="lni lni-briefcase mr-1"></i><?php echo $JOBTYPE ?></span>
                                        <span class="text-muted mr-2"><i
                                                class="lni lni-check-box mr-1"></i><?php echo timeago($DATEPOSTED); ?></span>
                                        <?php if ($SALARY > 0) { ?><span class="text-muted mr-2"><i
                                                class="lni lni-money-protection mr-1"></i>:
                                            N<?php echo number_format($SALARY, 2) ?>+ </span><?php } ?>
                                    </div>
                                    <div class="jb-list-01-title d-inline">
                                        <span
                                            class="mr-2 mb-2 d-inline-flex px-2 py-1 rounded theme-cl theme-bg-light"><?php echo $JOBSTATUS ?></span>
                                        <span
                                            class="mr-2 mb-2 d-inline-flex px-2 py-1 rounded text-warning bg-light-warning"><?php echo $COMPANYINDUSTRY ?></span>
                                        <!-- <span class="mr-2 mb-2 d-inline-flex px-2 py-1 rounded text-danger bg-light-danger"><?php echo $COMPANYSPECIALISM ?></span> -->
                                        <span
                                            class="mr-2 mb-2 d-inline-flex px-2 py-1 rounded text-info bg-light-info"><?php echo $COMPANYSPECIALISM ?></span>
                                        <!--<span class="px-2 mb-2 d-inline-flex py-1 rounded text-purple bg-light-purple">HTML5</span> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php }
							if ($count < 1) {
								echo "No Record Found!";
							}
							?>


                    </div>
                </div>

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
							</div> -->

            </div>

        </div>
        </div>
    </section>
    <!-- ============================ Main Section End ================================== -->

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

<!-- Mirrored from themezhub.net/live-workplex/workplex/browse-jobs.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Feb 2022 12:04:41 GMT -->

</html>