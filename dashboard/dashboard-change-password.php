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

		<!-- ======================= dashboard Detail ======================== -->


		<div class="dashboard-wrap bg-light">
			<a class="mobNavigation" data-toggle="collapse" href="#MobNav" role="button" aria-expanded="false" aria-controls="MobNav">
				<i class="fas fa-bars mr-2"></i>Dashboard Navigation
			</a>

			<?php include 'sidenav.php' ?>

			<div class="dashboard-content">
				<div class="dashboard-tlbar d-block mb-5">
					<div class="row">
						<div class="colxl-12 col-lg-12 col-md-12">
							<h1 class="ft-medium">Change Password</h1>
							<nav aria-label="breadcrumb">
								<ol class="breadcrumb">
									<li class="breadcrumb-item text-muted"><a href="#">Home</a></li>
									<li class="breadcrumb-item text-muted"><a href="#">Dashboard</a></li>
									<li class="breadcrumb-item"><a href="#" class="theme-cl">Change Password</a></li>
								</ol>
							</nav>
						</div>
					</div>
				</div>

				<div class="dashboard-widg-bar d-block">
					<div class="row">
						<div class="col-xl-12 col-lg-12 col-md-12">
							<div class="_dashboard_content bg-white rounded mb-4">
								<div class="_dashboard_content_header br-bottom py-3 px-3">
									<div class="_dashboard__header_flex">
										<h4 class="mb-0 ft-medium fs-md"><i class="fa fa-lock mr-1 theme-cl fs-sm"></i>Change Password</h4>
									</div>
								</div>

								<div class="_dashboard_content_body py-3 px-3">
									<form class="row" method="post">
										<div class="col-xl-8 col-lg-9 col-md-12 col-sm-12">
											<div class="form-group">
												<label class="text-dark ft-medium">Old Password</label>
												<input type="password" class="form-control rounded" placeholder="" name="oldpassword">
											</div>
										</div>
										<div class="col-xl-8 col-lg-9 col-md-12 col-sm-12">
											<div class="form-group">
												<label class="text-dark ft-medium">New Password</label>
												<input type="password" class="form-control rounded" placeholder="" name="newpassword">
											</div>
										</div>
										<div class="col-xl-8 col-lg-9 col-md-12 col-sm-12">
											<div class="form-group">
												<label class="text-dark ft-medium">Confirm Password</label>
												<input type="password" class="form-control rounded" placeholder="" name="rnewpassword">
											</div>
										</div>
										<div class="col-xl-8 col-lg-9 col-md-12 col-sm-12">
											<div class="form-group">
												<label class="text-dark ft-medium"><?php echo $msg2?></label>
											</div>
										</div>
										<div class="col-xl-12 col-lg-12">
											<div class="form-group">
												<button type="submit" name="change_password" class="btn btn-md ft-medium text-light rounded theme-bg">Save Changes</button>
											</div>
										</div>

									</form>
								</div>
							</div>
						</div>
					</div>

				</div>

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

<!-- Mirrored from themezhub.net/live-workplex/workplex/dashboard-change-password.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Feb 2022 12:08:07 GMT -->

</html>