<!DOCTYPE html>
<html lang="zxx">

<?php
require('./mysqli_connect.php');
		// header.php

		//////////////////////Get Company Details////////////////////////

		include 'session_check.php';

		include "include/helper.php";


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
                    <h1 class="ft-medium">Browse Employers</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#" class="text-light">Home</a></li>
                            <li class="breadcrumb-item"><a href="#" class="text-light">Job</a></li>
                            <li class="breadcrumb-item active theme-cl" aria-current="page">Browse Employers</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="ht-30"></div>
    </div>
    <!-- ======================= Top Breadcrubms ======================== -->

    <!-- ============================ Main Section Start ================================== -->
    <section class="bg-light">
        <div class="container">
            <div class="row">

                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="bg-white rounded mb-4">

                        <div
                            class="sidebar_header d-flex align-items-center justify-content-between px-4 py-3 br-bottom">
                            <h4 class="ft-medium fs-lg mb-0">Search Filter</h4>
                            <div class="ssh-header">
                                <a href="javascript:void(0);" class="clear_all ft-medium text-muted">Clear All</a>
                                <a href="#search_open" data-toggle="collapse" aria-expanded="false" role="button"
                                    class="collapsed _filter-ico ml-2"><i class="lni lni-text-align-right"></i></a>
                            </div>
                        </div>

                        <!-- Find New Property -->
                        <div class="sidebar-widgets collapse miz_show" id="search_open" data-parent="#search_open">

                            <div class="search-inner">
                                <form method="post">
                                    <?php
										$search = '';
										$search_by = '';
										if (isset($_POST['search_btn'])) {
											if (!empty($_POST['search'])) {
												$search = trim($_POST['search']);
											}

											if (!empty($_POST['search_by'])) {
												$search_by = trim($_POST['search_by']);
											}
										}

										?>
                                    <div class="filter-search-box px-4 pt-3 pb-0">
                                        <div class="form-group">
                                            <input type="text" name="search" class="form-control"
                                                placeholder="Search..." required>
                                        </div>
                                        <div class="form-group">
                                            <!-- <input type="text" class="form-control" name="search_by" placeholder="Location, City.."> -->
                                            <select class="custom-select simple" name="search_by" required>
                                                <option value="" selected="" hidden>Search By</option>
                                                <option value="name">Company Name</option>
                                                <option value="location">Location</option>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="form-group filter_button pt-2 pb-4 px-4">
                                        <button type="submit" name="search_btn"
                                            class="btn btn-md theme-bg text-light rounded full-width">Search</button>
                                    </div>
                                </form>

                            </div>
                        </div>
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
												echo "Search result for <i>" . trim($_POST['search']) ."</i>";
											} ?></h6>

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
                                            <a href="browse-employers.php" class="simple-button mr-1 active theme-cl"><i
                                                    class="ti-layout-grid2"></i></a>
                                            <!-- <a href="browse-employers-list.html" class="simple-button"><i class="ti-view-list"></i></a> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- row -->
                    <div class="row align-items-center">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">

                            <?php
								$query = "SELECT * from tblcompany ORDER BY COMPANYID DESC" or die(mysqli_error($con));

								if (!empty($sort)) {
									$query = "SELECT * from tblcompany ORDER BY COMPANYID $sort" or die(mysqli_error($con));
								}

								if (($search_by == "name")) {
									$query = "SELECT * from tblcompany where COMPANYNAME LIKE '%$search%' ORDER BY COMPANYID DESC" or die(mysqli_error($con));
								}
								if (($search_by == "location")) {
									$query = "SELECT * from tblcompany where COMPANYADDRESS LIKE '%$search%' or COMPANYCITY LIKE '%$search%' or COMPANYCOUNTRY LIKE '%$search%' ORDER BY COMPANYID DESC" or die(mysqli_error($con));
								}





								$run = mysqli_query($con, $query);
								$count = 0;
								while ($row = mysqli_fetch_array($run)) {
									$count++;
								?>

                            <!-- Single -->
                            <div class="job_grid d-block border rounded px-3 pt-3 pb-2">
                                <div class="jb-list01-flex d-flex align-items-start justify-content-start">
                                    <a href="employer-detail.php?companyid=<?php echo $row['COMPANYID'] ?>">
                                        <div class="jb-list01-thumb">
                                            <img src="./<?php echo $row['COMPANYLOGO'] ?>" class="img-fluid" width="90"
                                                alt="" />
                                        </div>
                                    </a>

                                    <div class="jb-list01 pl-3">
                                        <div class="jb-list-01-title">
                                            <h5 class="ft-medium mb-1"><a
                                                    href="job-detail.php?companyid=<?php echo $row['COMPANYID'] ?>"><?php echo $row['COMPANYNAME'] ?><img
                                                        src="assets/img/verify.svg" class="ml-1" width="12" alt=""></a>
                                            </h5>
                                        </div>
                                        <div class="jb-list-01-info d-block mb-3">
                                            <span class="text-muted mr-2"><i
                                                    class="lni lni-map-marker mr-1"></i><?php echo $row['COMPANYCITY'] ?>,
                                                <?php echo $row['COMPANYCOUNTRY'] ?></span>
                                            <span class="text-muted mr-2"><i
                                                    class="lni lni-home mr-1"></i><?php echo $row['COMPANYADDRESS'] ?></span>
                                            <span class="text-muted mr-2"><i
                                                    class="lni lni-phone mr-1"></i><?php echo $row['COMPANYCONTACTNO'] ?></span>
                                            <span class="text-muted mr-2"><i
                                                    class="lni lni-users mr-1"></i><?php echo $row['COMPANYINDUSTRY'] ?></span>
                                        </div>
                                        <div class="jb-list-01-title d-inline">
                                            <span
                                                class="mr-2 mb-2 d-inline-flex px-2 py-1 rounded theme-cl theme-bg-light">Full
                                                Time</span>
                                            <span
                                                class="mr-2 mb-2 d-inline-flex px-2 py-1 rounded text-warning bg-light-warning"><?php echo $row['COMPANYSPECIALISM'] ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
								}
								if ($count < 1) {
									echo "No Record Found!";
								}
								?>

                        </div>
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

<!-- Mirrored from themezhub.net/live-workplex/workplex/browse-employers.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Feb 2022 12:07:10 GMT -->

</html>