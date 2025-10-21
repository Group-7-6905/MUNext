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

        <!-- ======================= Top Breadcrubms ======================== -->
        <div class="bg-title py-5" data-overlay="0">
            <div class="ht-30"></div>
            <div class="container">
                <div class="row">
                    <div class="colxl-12 col-lg-12 col-md-12">
                        <h1 class="ft-medium">Browse Categories</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#" class="text-light">Home</a></li>
                                <li class="breadcrumb-item"><a href="#" class="text-light">Job</a></li>
                                <li class="breadcrumb-item active theme-cl" aria-current="page">Browse Categories</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="ht-30"></div>
        </div>
        <!-- ======================= Top Breadcrubms ======================== -->

        <!-- ======================= Browse Category Detail ======================== -->
        <section class="middle bg-light">
            <div class="container">

                <?php


				 $cn =0;
				
				$query = "SELECT * from tbljobcategory ORDER BY ID DESC" or die(mysqli_error($con));

				$run = mysqli_query($con, $query);
				
				while ($row = mysqli_fetch_array($run)) {
					$cn ++;

					$CATEGORY = $row['CATEGORY'];
					$CATEGORYID = $row['ID'];

				// 	$querysubcat = "SELECT * from tbljobsubcategory where CATEGORYID = '$CATEGORYID'  ORDER BY ID DESC" or die(mysqli_error($con));

				// 	$runrowsubcat = mysqli_query($con, $querysubcat);

				// 	while ($rowsubcat = mysqli_fetch_array($runrowsubcat)) {

				// 		$SUBCATEGORY = $rowsubcat['SUBCATEGORY'];
				// 		$SUBCATEGORYID = $rowsubcat['ID'];

				// 		$queryjbs = "SELECT * from tbljob where JOBCATEGORYID = '$SUBCATEGORYID' ORDER BY JOBID DESC" or die(mysqli_error($con));

				// 		$resultjbs = mysqli_query($con, $queryjbs);
				// 		$rowjbs = mysqli_fetch_array($resultjbs);

				// 		$count = mysqli_num_rows($resultjbs);
				// 	}

				// 		if ($count > 0) {

					

					
				?>

                <!-- Single Category -->
                <div class="row align-items-start mb-5">
                    <div class="col-xl-3 col-lg-4 col-md-4 col-sm-12 col-12">
                        <div class="d-block full-width mt-2">
                            <h3 class="ft-medium mb-0"><?php echo $CATEGORY ?></h3>
                        </div>
                    </div>
                    <div class="col-xl-9 col-lg-8 col-md-8 col-sm-12 col-12">
                        <div class="row">

                            <?php

						$query = "SELECT * from tbljobsubcategory  ORDER BY ID DESC" or die(mysqli_error($con));

						$run = mysqli_query($con, $query);

						while ($row = mysqli_fetch_array($run)) {

							$SUBCATEGORY = $row['SUBCATEGORY'];
							$SUBCATEGORYID = $row['ID'];

							$queryjbs = "SELECT * from tbljob where JOBCATEGORYID = '$SUBCATEGORYID' ORDER BY JOBID DESC" or die(mysqli_error($con));

							$resultjbs = mysqli_query($con, $queryjbs);
							$rowjbs = mysqli_fetch_array($resultjbs);

							$count = mysqli_num_rows($resultjbs);

							if ($count > 0) {


					?>

                            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                                <div class="d-block full-width px-4 py-4 bg-white rounded mb-3">
                                    <div class="d-block full-width mb-1"><img src="assets/img/icons/1.png"
                                            class="img-fluid" width="45" alt="" /></div>
                                    <h4 class="ft-medium mb-0 fs-md"><?php echo $SUBCATEGORY; ?></h4>
                                    <p class="mb-3 p-0 lh-1"><?php echo $count ?> Jobs </p>
                                    <a href="job-list-v1.php?jobcategoryid=<?php echo $SUBCATEGORYID; ?>"
                                        class="theme-cl ft-medium">Explore Jobs<i
                                            class="lni lni-arrow-right-circle ml-1"></i></a>
                                </div>
                            </div>
                            <?php } 
										}
									 ?>

                            <!-- <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
										<div class="d-block full-width px-4 py-4 bg-white rounded mb-3">
											<div class="d-block full-width mb-1"><img src="assets/img/icons/2.png" class="img-fluid" width="45" alt="" /></div>
											<h4 class="ft-medium mb-0 fs-md">Web Designing</h4>
											<p class="mb-3 p-0 lh-1">412 Jobs</p>
											<a href="job-search-v1.html" class="theme-cl ft-medium">Explore Jobs<i class="lni lni-arrow-right-circle ml-1"></i></a>
										</div>
									</div>

									<div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
										<div class="d-block full-width px-4 py-4 bg-white rounded mb-3">
											<div class="d-block full-width mb-1"><img src="assets/img/icons/3.png" class="img-fluid" width="45" alt="" /></div>
											<h4 class="ft-medium mb-0 fs-md">WordPress Developer</h4>
											<p class="mb-3 p-0 lh-1">620 Jobs</p>
											<a href="job-search-v1.html" class="theme-cl ft-medium">Explore Jobs<i class="lni lni-arrow-right-circle ml-1"></i></a>
										</div>
									</div>

									<div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
										<div class="d-block full-width px-4 py-4 bg-white rounded mb-3">
											<div class="d-block full-width mb-1"><img src="assets/img/icons/4.png" class="img-fluid" width="45" alt="" /></div>
											<h4 class="ft-medium mb-0 fs-md">PHP Developer</h4>
											<p class="mb-3 p-0 lh-1">470 Jobs</p>
											<a href="job-search-v1.html" class="theme-cl ft-medium">Explore Jobs<i class="lni lni-arrow-right-circle ml-1"></i></a>
										</div>
									</div>

									<div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
										<div class="d-block full-width px-4 py-4 bg-white rounded mb-3">
											<div class="d-block full-width mb-1"><img src="assets/img/icons/5.png" class="img-fluid" width="45" alt="" /></div>
											<h4 class="ft-medium mb-0 fs-md">UI/UX Designer</h4>
											<p class="mb-3 p-0 lh-1">513 Jobs</p>
											<a href="job-search-v1.html" class="theme-cl ft-medium">Explore Jobs<i class="lni lni-arrow-right-circle ml-1"></i></a>
										</div>
									</div> -->

                        </div>
                    </div>
                </div>
                <!-- /row -->

                <?php 
					} 
				// } 
				if ($cn < 1){
					echo "<p class='text-center'>No Record..</p>";
				}?>

                <!-- Single Category -->
                <!-- <div class="row align-items-start mb-5">
					<div class="col-xl-3 col-lg-4 col-md-4 col-sm-12 col-12">
						<div class="d-block full-width mt-2">
							<h3 class="ft-medium mb-0">Financial Services & Software</h3>
						</div>
					</div>
					<div class="col-xl-9 col-lg-8 col-md-8 col-sm-12 col-12">
						<div class="row">

							<div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
								<div class="d-block full-width px-4 py-4 bg-white rounded mb-3">
									<div class="d-block full-width mb-1"><img src="assets/img/icons/6.png" class="img-fluid" width="45" alt="" /></div>
									<h4 class="ft-medium mb-0 fs-md">Pride Technologies</h4>
									<p class="mb-3 p-0 lh-1">389 Jobs</p>
									<a href="job-search-v1.html" class="theme-cl ft-medium">Explore Jobs<i class="lni lni-arrow-right-circle ml-1"></i></a>
								</div>
							</div>

							<div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
								<div class="d-block full-width px-4 py-4 bg-white rounded mb-3">
									<div class="d-block full-width mb-1"><img src="assets/img/icons/7.png" class="img-fluid" width="45" alt="" /></div>
									<h4 class="ft-medium mb-0 fs-md">Petroleum/Energy</h4>
									<p class="mb-3 p-0 lh-1">400 Jobs</p>
									<a href="job-search-v1.html" class="theme-cl ft-medium">Explore Jobs<i class="lni lni-arrow-right-circle ml-1"></i></a>
								</div>
							</div>

							<div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
								<div class="d-block full-width px-4 py-4 bg-white rounded mb-3">
									<div class="d-block full-width mb-1"><img src="assets/img/icons/8.png" class="img-fluid" width="45" alt="" /></div>
									<h4 class="ft-medium mb-0 fs-md">Magento Developer</h4>
									<p class="mb-3 p-0 lh-1">978 Jobs</p>
									<a href="job-search-v1.html" class="theme-cl ft-medium">Explore Jobs<i class="lni lni-arrow-right-circle ml-1"></i></a>
								</div>
							</div>

							<div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
								<div class="d-block full-width px-4 py-4 bg-white rounded mb-3">
									<div class="d-block full-width mb-1"><img src="assets/img/icons/9.png" class="img-fluid" width="45" alt="" /></div>
									<h4 class="ft-medium mb-0 fs-md">Product Designer</h4>
									<p class="mb-3 p-0 lh-1">854 Jobs</p>
									<a href="job-search-v1.html" class="theme-cl ft-medium">Explore Jobs<i class="lni lni-arrow-right-circle ml-1"></i></a>
								</div>
							</div>

							<div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
								<div class="d-block full-width px-4 py-4 bg-white rounded mb-3">
									<div class="d-block full-width mb-1"><img src="assets/img/icons/10.png" class="img-fluid" width="45" alt="" /></div>
									<h4 class="ft-medium mb-0 fs-md">Team Leader</h4>
									<p class="mb-3 p-0 lh-1">632 Jobs</p>
									<a href="job-search-v1.html" class="theme-cl ft-medium">Explore Jobs<i class="lni lni-arrow-right-circle ml-1"></i></a>
								</div>
							</div>

						</div>
					</div>
				</div> -->
                <!-- /row -->

                <!-- Single Category -->
                <!-- <div class="row align-items-start mb-5">
					<div class="col-xl-3 col-lg-4 col-md-4 col-sm-12 col-12">
						<div class="d-block full-width mt-2">
							<h3 class="ft-medium mb-0">Transmission & Distribution</h3>
						</div>
					</div>
					<div class="col-xl-9 col-lg-8 col-md-8 col-sm-12 col-12">
						<div class="row">

							<div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
								<div class="d-block full-width px-4 py-4 bg-white rounded mb-3">
									<div class="d-block full-width mb-1"><img src="assets/img/icons/11.png" class="img-fluid" width="45" alt="" /></div>
									<h4 class="ft-medium mb-0 fs-md">Chat Manager</h4>
									<p class="mb-3 p-0 lh-1">730 Jobs</p>
									<a href="job-search-v1.html" class="theme-cl ft-medium">Explore Jobs<i class="lni lni-arrow-right-circle ml-1"></i></a>
								</div>
							</div>

							<div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
								<div class="d-block full-width px-4 py-4 bg-white rounded mb-3">
									<div class="d-block full-width mb-1"><img src="assets/img/icons/12.png" class="img-fluid" width="45" alt="" /></div>
									<h4 class="ft-medium mb-0 fs-md">Sales Manager</h4>
									<p class="mb-3 p-0 lh-1">941 Jobs</p>
									<a href="job-search-v1.html" class="theme-cl ft-medium">Explore Jobs<i class="lni lni-arrow-right-circle ml-1"></i></a>
								</div>
							</div>

							<div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
								<div class="d-block full-width px-4 py-4 bg-white rounded mb-3">
									<div class="d-block full-width mb-1"><img src="assets/img/icons/13.png" class="img-fluid" width="45" alt="" /></div>
									<h4 class="ft-medium mb-0 fs-md">Font Developer</h4>
									<p class="mb-3 p-0 lh-1">320 Jobs</p>
									<a href="job-search-v1.html" class="theme-cl ft-medium">Explore Jobs<i class="lni lni-arrow-right-circle ml-1"></i></a>
								</div>
							</div>

							<div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
								<div class="d-block full-width px-4 py-4 bg-white rounded mb-3">
									<div class="d-block full-width mb-1"><img src="assets/img/icons/14.png" class="img-fluid" width="45" alt="" /></div>
									<h4 class="ft-medium mb-0 fs-md">Laravel Developer</h4>
									<p class="mb-3 p-0 lh-1">756 Jobs</p>
									<a href="job-search-v1.html" class="theme-cl ft-medium">Explore Jobs<i class="lni lni-arrow-right-circle ml-1"></i></a>
								</div>
							</div>

							<div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
								<div class="d-block full-width px-4 py-4 bg-white rounded mb-3">
									<div class="d-block full-width mb-1"><img src="assets/img/icons/15.png" class="img-fluid" width="45" alt="" /></div>
									<h4 class="ft-medium mb-0 fs-md">Web Master</h4>
									<p class="mb-3 p-0 lh-1">695 Jobs</p>
									<a href="job-search-v1.html" class="theme-cl ft-medium">Explore Jobs<i class="lni lni-arrow-right-circle ml-1"></i></a>
								</div>
							</div>

						</div>
					</div>
				</div> -->
                <!-- /row -->

                <!-- Single Category -->
                <!-- <div class="row align-items-start">
					<div class="col-xl-3 col-lg-4 col-md-4 col-sm-12 col-12">
						<div class="d-block full-width mt-2">
							<h3 class="ft-medium mb-0">Switchgear/ Electrical Engineering</h3>
						</div>
					</div>
					<div class="col-xl-9 col-lg-8 col-md-8 col-sm-12 col-12">
						<div class="row">

							<div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
								<div class="d-block full-width px-4 py-4 bg-white rounded mb-3">
									<div class="d-block full-width mb-1"><img src="assets/img/icons/16.png" class="img-fluid" width="45" alt="" /></div>
									<h4 class="ft-medium mb-0 fs-md">Web Designing</h4>
									<p class="mb-3 p-0 lh-1">720 Jobs</p>
									<a href="job-search-v1.html" class="theme-cl ft-medium">Explore Jobs<i class="lni lni-arrow-right-circle ml-1"></i></a>
								</div>
							</div>

							<div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
								<div class="d-block full-width px-4 py-4 bg-white rounded mb-3">
									<div class="d-block full-width mb-1"><img src="assets/img/icons/17.png" class="img-fluid" width="45" alt="" /></div>
									<h4 class="ft-medium mb-0 fs-md">Web Designing</h4>
									<p class="mb-3 p-0 lh-1">945 Jobs</p>
									<a href="job-search-v1.html" class="theme-cl ft-medium">Explore Jobs<i class="lni lni-arrow-right-circle ml-1"></i></a>
								</div>
							</div>

							<div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
								<div class="d-block full-width px-4 py-4 bg-white rounded mb-3">
									<div class="d-block full-width mb-1"><img src="assets/img/icons/18.png" class="img-fluid" width="45" alt="" /></div>
									<h4 class="ft-medium mb-0 fs-md">Web Designing</h4>
									<p class="mb-3 p-0 lh-1">320 Jobs</p>
									<a href="job-search-v1.html" class="theme-cl ft-medium">Explore Jobs<i class="lni lni-arrow-right-circle ml-1"></i></a>
								</div>
							</div>

							<div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
								<div class="d-block full-width px-4 py-4 bg-white rounded mb-3">
									<div class="d-block full-width mb-1"><img src="assets/img/icons/19.png" class="img-fluid" width="45" alt="" /></div>
									<h4 class="ft-medium mb-0 fs-md">Web Designing</h4>
									<p class="mb-3 p-0 lh-1">945 Jobs</p>
									<a href="job-search-v1.html" class="theme-cl ft-medium">Explore Jobs<i class="lni lni-arrow-right-circle ml-1"></i></a>
								</div>
							</div>

							<div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
								<div class="d-block full-width px-4 py-4 bg-white rounded mb-3">
									<div class="d-block full-width mb-1"><img src="assets/img/icons/20.png" class="img-fluid" width="45" alt="" /></div>
									<h4 class="ft-medium mb-0 fs-md">Web Designing</h4>
									<p class="mb-3 p-0 lh-1">625 Jobs</p>
									<a href="job-search-v1.html" class="theme-cl ft-medium">Explore Jobs<i class="lni lni-arrow-right-circle ml-1"></i></a>
								</div>
							</div>

						</div>
					</div>
				</div> -->
                <!-- /row -->

            </div>
        </section>
        <!-- ======================= Browse Category End ======================== -->

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

<!-- Mirrored from themezhub.net/live-workplex/workplex/browse-category.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Feb 2022 12:07:05 GMT -->

</html>