<!DOCTYPE html>
<html lang="zxx">

<?php require 'include/phpcode.php'; ?>

<!-- Mirrored from themezhub.net/live-workplex/workplex/candidate-dashboard.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Feb 2022 12:07:05 GMT -->

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
		<?php include 'header.php' ?>

		<div class="clearfix"></div>
		<!-- ============================================================== -->
		<!-- Top header  -->
		<!-- ============================================================== -->


		<!-- ======================= Top Breadcrubms ======================== -->
		<div class="bg-light py-5">
			<div class="ht-30"></div>
			<div class="container">
				<div class="row">
					<div class="colxl-12 col-lg-12 col-md-12">
						<h1 class="ft-medium">Candidate Detail</h1>
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">Home</a></li>
								<li class="breadcrumb-item"><a href="#">Candidates</a></li>
								<li class="breadcrumb-item active theme-cl" aria-current="page">Candidates Detail</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
			<div class="ht-30"></div>
		</div>
		<!-- ======================= Top Breadcrubms ======================== -->

		<!-- ======================= Dashboard Detail ======================== -->
		<section class="middle">
			<div class="container">
				<div class="row align-items-start justify-content-between">

					<div class="col-12 col-md-12 col-lg-4 col-xl-4 text-center miliods">
						<div class="d-block border rounded mfliud-bot mb-4">
							<div class="cdt_author px-2 pt-5 pb-4">
								<div class="dash_auth_thumb circle p-1 border d-inline-flex mx-auto mb-2">
									<img src="../<?php echo $APPLICANTPHOTO ?>" class="img-fluid circle" width="100" alt="" />
								</div>
								<div class="dash_caption mb-3">
									<h4 class="fs-lg ft-medium mb-0 lh-1"><?php echo $FULLNAME ?></h4>
									<p class="m-0 p-0"><?php echo $JOBTITLE ?></p>
									<span class="text-muted smalls"><i class="lni lni-map-marker mr-1"></i><?php echo $CITY ?>,<?php echo $COUNTRY ?></span>
								</div>
								<div class="jb-list-01-title px-2">
									<span class="px-2 mb-2 d-inline-flex py-1 rounded text-purple bg-light-purple">0<?php echo $APPLICANTID ?></span>
									<span class="mr-2 mb-2 d-inline-flex px-2 py-1 rounded theme-cl theme-bg-light">Active</span>
									<span class="mr-2 mb-2 d-inline-flex px-2 py-1 rounded text-warning bg-light-warning"><?php echo $SUBCATEGORY ?></span>
									<!-- <span class="mr-2 mb-2 d-inline-flex px-2 py-1 rounded text-danger bg-light-danger">Magento</span> -->
									<br>
									<span class="mr-2 mb-2 d-inline-flex px-2 py-1 rounded text-info bg-light-info"><?php echo $SKILLS ?></span>

								</div>
							</div>

							<div class="cdt_caps">
								<div class="job_grid_footer pb-3 px-3 d-flex align-items-center justify-content-between">
									<div class="df-1 text-muted"><i class="lni lni-phone mr-1"></i><?php echo $CONTACTNO ?></div>
									<div class="df-1 text-muted"><i class="lni lni-laptop-phone mr-1"></i><?php echo $JOBTITLE ?></div>
								</div>
								<div class="job_grid_footer pb-3 px-3 d-flex align-items-center justify-content-between">
									<div class="df-1 text-muted"><i class="lni lni-envelope mr-1"></i><?php echo $EMAIL ?></div>
									<div class="df-1 text-muted"><i class="lni lni-graduation mr-1"></i><?php echo $DEGREE ?></div>
								</div>
								<div class="job_grid_footer px-3 d-flex align-items-center justify-content-between">
									<div class="df-1 text-muted"><i class="lni lni-user mr-1"></i><?php echo $USERNAME ?> (<?php echo $SEX ?>)</div>
									<div class="df-1 text-muted"><i class="lni lni-calendar mr-1"></i><?php echo $BIRTHDATE ?></div>
								</div>
							</div>

							<div class="cdt_caps py-5 px-3">
								<a href="dashboard-my-profile.php" class="btn btn-md theme-bg text-light rounded full-width">Edit Profile</a>
							</div>

						</div>
						<div class="cdt_caps py-5 px-3">
							<a href="./" class="btn btn-md text-dark rounded "> << BACK TO DASHBOARD</a>
						</div>
					</div>

					<div class="col-12 col-md-12 col-lg-8 col-xl-8">

						<!-- row -->
						<div class="row align-items-start">

							<!-- About -->
							<div class="abt-cdt d-block full-width mb-4">
								<h4 class="ft-medium mb-1 fs-md">About <?php echo $FULLNAME ?></h4>
								<p><?php echo $ABOUTME ?></p>
							</div>

							<!-- Hobbies -->
							<div class="abt-cdt d-block full-width mb-4">
								<h4 class="ft-medium mb-1 fs-md"><?php echo $FULLNAME ?> Contact</h4>
								<div class="position-relative row">
									<div class="col-lg-12 col-md-12 col-12">
										<div class="mb-2 mr-4 ml-lg-0 mr-lg-4">
											<div class="d-flex align-items-center">
												<div class="rounded-circle bg-light-success theme-cl p-1 small d-flex align-items-center justify-content-center">
													<i class="fas fa-check small"></i>
												</div>
												<h6 class="mb-0 ml-3 text-muted fs-sm">Country: <?php echo $COUNTRY ?></h6>
											</div>
										</div>
										<div class="mb-2 mr-4 ml-lg-0 mr-lg-4">
											<div class="d-flex align-items-center">
												<div class="rounded-circle bg-light-success theme-cl p-1 small d-flex align-items-center justify-content-center">
													<i class="fas fa-check small"></i>
												</div>
												<h6 class="mb-0 ml-3 text-muted fs-sm">City: <?php echo $CITY ?></h6>
											</div>
										</div>
										<div class="mb-2 mr-4 ml-lg-0 mr-lg-4">
											<div class="d-flex align-items-center">
												<div class="rounded-circle bg-light-success theme-cl p-1 small d-flex align-items-center justify-content-center">
													<i class="fas fa-check small"></i>
												</div>
												<h6 class="mb-0 ml-3 text-muted fs-sm">Address: <?php echo $ADDRESS ?></h6>
											</div>
										</div>
										<div class="mb-2 mr-4 ml-lg-0 mr-lg-4">
											<div class="d-flex align-items-center">
												<div class="rounded-circle bg-light-success theme-cl p-1 small d-flex align-items-center justify-content-center">
													<i class="fas fa-check small"></i>
												</div>
												<h6 class="mb-0 ml-3 text-muted fs-sm">Phone: <?php echo $CONTACTNO ?></h6>
											</div>
										</div>
										<div class="mb-2 mr-4 ml-lg-0 mr-lg-4">
											<div class="d-flex align-items-center">
												<div class="rounded-circle bg-light-success theme-cl p-1 small d-flex align-items-center justify-content-center">
													<i class="fas fa-check small"></i>
												</div>
												<h6 class="mb-0 ml-3 text-muted fs-sm">Email: <?php echo $EMAIL ?></h6>
											</div>
										</div>
										<div class="mb-2 mr-4 ml-lg-0 mr-lg-4">
											<div class="d-flex align-items-center">
												<div class="rounded-circle bg-light-success theme-cl p-1 small d-flex align-items-center justify-content-center">
													<i class="fas fa-check small"></i>
												</div>
												<h6 class="mb-0 ml-3 text-muted fs-sm">Facebook: <?php echo $FB_link ?></h6>
											</div>
										</div>
										<div class="mb-2 mr-4 ml-lg-0 mr-lg-4">
											<div class="d-flex align-items-center">
												<div class="rounded-circle bg-light-success theme-cl p-1 small d-flex align-items-center justify-content-center">
													<i class="fas fa-check small"></i>
												</div>
												<h6 class="mb-0 ml-3 text-muted fs-sm">LinkedIn: <?php echo $LinkedIn_link ?></h6>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- Qualification -->
							<div class="abt-cdt d-block full-width mb-4">
								<h4 class="ft-medium mb-1 fs-md">Qualification</h4>
								<div class="exslio-list mt-3">
									<ul>
										<li>
											<div class="esclio-110 bg-light rounded px-3 py-3">
												<h4 class="mb-0 ft-medium fs-md"><?php echo $DEGREE ?></h4>
												<div class="esclio-110-info full-width mb-2">
													<span class="text-muted mr-2"><i class="lni lni-graduation mr-1"></i><?php echo $SCHOOLNAME ?></span>
													<!-- <span class="text-muted mr-2"><i class="lni lni-calendar mr-1"></i>2010</span> -->
												</div>
												<div class="esclio-110-decs full-width">
													<p><i>Download Resume/CV to view more <a href="javascript:void(0);" class="theme-cl">...</a></i>
													</p>
												</div>
											</div>
										</li>

										<!-- <li>
											<div class="esclio-110 bg-light rounded px-3 py-3">
												<h4 class="mb-0 ft-medium fs-md">Inter Medium</h4>
												<div class="esclio-110-info full-width mb-2">
													<span class="text-muted mr-2"><i class="lni lni-graduation mr-1"></i>International Inter College</span>
													<span class="text-muted mr-2"><i class="lni lni-calendar mr-1"></i>2012</span>
												</div>
												<div class="esclio-110-decs full-width">
													<p>Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur,
														adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et
														dolore magnam <a href="javascript:void(0);" class="theme-cl">Read More..</a>
													</p>
												</div>
											</div>
										</li>

										<li>
											<div class="esclio-110 bg-light rounded px-3 py-3">
												<h4 class="mb-0 ft-medium fs-md">Gradutation</h4>
												<div class="esclio-110-info full-width mb-2">
													<span class="text-muted mr-2"><i class="lni lni-graduation mr-1"></i>Nandani
														College</span>
													<span class="text-muted mr-2"><i class="lni lni-calendar mr-1"></i>2015</span>
												</div>
												<div class="esclio-110-decs full-width">
													<p>Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur,
														adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et
														dolore magnam <a href="javascript:void(0);" class="theme-cl">Read More..</a>
													</p>
												</div>
											</div>
										</li> -->
									</ul>
								</div>
							</div>

							<!-- Experience -->
							<div class="abt-cdt d-block full-width mb-4">
								<h4 class="ft-medium mb-1 fs-md">Experience</h4>
								<div class="exslio-list mt-3">
									<ul>
										<li>
											<div class="esclio-110 bg-light rounded px-3 py-3">
												<h4 class="mb-0 ft-medium fs-md"><?php echo $EXCOMPANYNAME ?></h4>
												<div class="esclio-110-info full-width mb-2">
													<!-- <span class="text-muted mr-2"><i class="lni lni-map-marker mr-1"></i>Liverpool,
														UK</span> -->
													<span class="text-muted mr-2"><i class="lni lni-laptop-phone mr-1"></i><?php echo $EXJOBTITLE ?></span>

													<!-- <span class="text-muted mr-2"><i class="lni lni-calendar mr-1"></i>2010 -
														2012</span> -->
												</div>
												<div class="esclio-110-decs full-width">
													<p><i>Download Resume/CV to view more <a href="javascript:void(0);" class="theme-cl">...</a></i>
													</p>
												</div>
											</div>
										</li>

										<!-- <li>
											<div class="esclio-110 bg-light rounded px-3 py-3">
												<h4 class="mb-0 ft-medium fs-md">Waft Technology</h4>
												<div class="esclio-110-info full-width mb-2">
													<span class="text-muted mr-2"><i class="lni lni-map-marker mr-1"></i>Liverpool,
														UK</span>
													<span class="text-muted mr-2"><i class="lni lni-laptop-phone mr-1"></i>Product
														Designer</span>
													<span class="text-muted mr-2"><i class="lni lni-calendar mr-1"></i>2012 -
														2014</span>
												</div>
												<div class="esclio-110-decs full-width">
													<p>Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur,
														adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et
														dolore magnam <a href="javascript:void(0);" class="theme-cl">Read More..</a>
													</p>
												</div>
											</div>
										</li>

										<li>
											<div class="esclio-110 bg-light rounded px-3 py-3">
												<h4 class="mb-0 ft-medium fs-md">Swap Designd</h4>
												<div class="esclio-110-info full-width mb-2">
													<span class="text-muted mr-2"><i class="lni lni-map-marker mr-1"></i>Liverpool,
														UK</span>
													<span class="text-muted mr-2"><i class="lni lni-laptop-phone mr-1"></i>UI/UX
														Designer</span>
													<span class="text-muted mr-2"><i class="lni lni-calendar mr-1"></i>2015 -
														2021</span>
												</div>
												<div class="esclio-110-decs full-width">
													<p>Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur,
														adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et
														dolore magnam <a href="javascript:void(0);" class="theme-cl">Read More..</a>
													</p>
												</div>
											</div>
										</li> -->
									</ul>
								</div>
							</div>

							<!-- Skills -->
							<div class="abt-cdt d-block full-width">
								<h4 class="ft-medium mb-1 fs-md">Skills</h4>
								<ul class="p-0 skills_tag text-left">
									<li><span class="px-2 py-1 medium skill-bg rounded text-dark"><?php echo $SKILLS ?></span></li>
									<!-- <li><span class="px-2 py-1 medium skill-bg rounded text-dark">WordPress</span></li>
									<li><span class="px-2 py-1 medium skill-bg rounded text-dark">Javascript</span></li>
									<li><span class="px-2 py-1 medium skill-bg rounded text-dark">PHP</span></li>
									<li><span class="px-2 py-1 medium skill-bg rounded text-dark">HTML5</span></li>
									<li><span class="px-2 py-1 medium skill-bg rounded text-dark">MS SQL</span></li>
									<li><span class="px-2 py-1 medium skill-bg rounded text-dark">SQL Development</span></li>
									<li><span class="px-2 py-1 medium skill-bg rounded text-dark">Dynamod</span></li>
									<li><span class="px-2 py-1 medium skill-bg rounded text-dark">Database</span></li> -->
								</ul>
							</div>

						</div>
						<!-- row -->

					</div>

				</div>
			</div>
		</section>
		<!-- ======================= Dashboard Detail End ======================== -->
		<?php include 'footer.php'?>

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

<!-- Mirrored from themezhub.net/live-workplex/workplex/candidate-detail.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Feb 2022 12:07:05 GMT -->

</html>