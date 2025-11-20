<!DOCTYPE html>
<html lang="zxx">

<?php require 'include/phpcode.php';
?>
<!-- Mirrored from themezhub.net/live-workplex/workplex/dashboard-post-job.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Feb 2022 12:07:20 GMT -->

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
			<a class="mobNavigation" data-toggle="collapse" href="#MobNav" role="button" aria-expanded="false" aria-controls="MobNav">
				<i class="fas fa-bars mr-2"></i>Dashboard Navigation
			</a>

			<?php include 'sidenav.php' ?>
			<div class="dashboard-content">
				<div class="dashboard-tlbar d-block mb-5">
					<div class="row">
						<div class="colxl-12 col-lg-12 col-md-12">
							<h1 class="ft-medium">Post A New Job</h1>
							<nav aria-label="breadcrumb">
								<ol class="breadcrumb">
									<li class="breadcrumb-item text-muted"><a href="#">Home</a></li>
									<li class="breadcrumb-item text-muted"><a href="#">Dashboard</a></li>
									<li class="breadcrumb-item"><a href="#" class="theme-cl">Post Job</a></li>
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
										<h4 class="mb-0 ft-medium fs-md"><i class="fa fa-file mr-1 theme-cl fs-sm"></i>Post Job</h4>
									</div>
								</div>

								<div class="_dashboard_content_body py-3 px-3">
									<form class="row" method="post">
										<div class="col-xl-12 col-lg-12 col-md-12">
											<div class="row">

												<div class="col-xl-12 col-lg-12 col-md-12">
													<div class="form-group">
														<label class="text-dark ft-medium">Job Title</label>
														<select class="custom-select rounded" name="job_title_select" id="title_select" onchange="set_job()" required>
															<option hidden>Select Job Title</option>
															<option>Assistant</option>
															<option>Associate</option>
															<option>Administrative Assistant</option>
															<option>Account Manager</option>
															<option>Assistant Manager</option>
															<option>Commission Sales Associate</option>
															<option>Sales Attendant</option>
															<option>Sales Associate</option>
															<option>Accountant</option>
															<option>Sales Advocate</option>
															<option>Analyst</option>
															<option>Research Assistant</option>
															<option>Sales And Marketing Specialist</option>
															<option>Administrative Aide</option>
															<option>Administrative Clerk</option>
															<option>Account Executive</option>
															<option>Executive Assistant</option>
															<option>Administrator</option>
															<option>Account Relationship Manager</option>
															<option>Web Developer</option>
															<option>Senior Software Developer</option>
															<option>Others (Please specify)</option>
														</select>

													</div>
												</div>
												<style>
													#title_other {
														display: none;
													}
												</style>
												<div class="col-xl-12 col-lg-12 col-md-12">
													<div class="form-group">
														<input type="text" class="form-control rounded" name="job_title_specify" placeholder="Job Title" id="title_other">
													</div>
												</div>

												<script>
													function set_job() {
														var title_select = $('#title_select').val(); //document.getElementById("title_select").innerHTML

														if (title_select == "Others (Please specify)") {
															document.getElementById("title_other").style.display = "block";
														} else {
															document.getElementById("title_other").style.display = "none";
														}
														// document.getElementById("set_wallet").innerHTML = set_wallet;
													}
												</script>


												<div class="col-xl-6 col-lg-6 col-md-6">
													<div class="form-group">
														<label class="text-dark ft-medium">Job Category*</label>
														<select class="custom-select rounded" name="job_categoryid" required>
															<option hidden>Choose Job Category</option>
															<?php
															$query = "SELECT * from tbljobsubcategory ORDER BY ID DESC" or die(mysqli_error($con));

															$run = mysqli_query($con, $query);

															while ($row = mysqli_fetch_array($run)) {
															?>
																<option value="<?php echo $row['ID'] ?>"><?php echo $row['SUBCATEGORY'] ?></option>

															<?php } ?>

														</select>
													</div>
												</div>


												<div class="col-xl-6 col-lg-6 col-md-6">
													<div class="form-group">
														<label class="text-dark ft-medium">Workplace policy*</label>
														<select class="custom-select rounded" name="workplace_policy" required>
															<option hidden>Choose the workplace policy</option>
															<option value="On-site">On-site (Employees come to work in-person)</option>
															<option value="Hybrid">Hybrid (Employees work on-site and off-site)</option>
															<option value="Remote">Remote (Employees work off-site)</option>

														</select>
													</div>
												</div>


												<div class="col-xl-12 col-lg-12 col-md-12">
													<div class="form-group">
														<label class="text-dark ft-medium">Job Description</label>
														<textarea class="form-control rounded" name="job_desc" placeholder="Add Skills and requirements you're looking for." required></textarea>
													</div>
												</div>



												<!-- <div class="col-xl-6 col-lg-6 col-md-6">
													<div class="form-group">
														<label class="text-dark ft-medium">Skills (seperate with a comma)</label>
														<input type="text" class="form-control rounded" placeholder="Skills">
														<code>e.g PHP, JavaScript</code>
													</div>
												</div> -->



												<div class="col-xl-6 col-lg-6 col-md-6">
													<div class="form-group">
														<label class="text-dark ft-medium">Company*</label>
														<select class="custom-select rounded" required name="company" id="companyid" onChange="getcompany(this.value);">

															<option value="">Select Company</option>
															<?php $sql = "SELECT * from tblcompany ORDER BY COMPANYNAME ASC";
															$run = mysqli_query($con, $sql);
															while ($row = mysqli_fetch_array($run)) { ?>
																<option value="<?php echo ($row['COMPANYID']); ?>">
																	<?php echo ($row['COMPANYNAME']); ?>
																</option>
															<?php
															} ?>
														</select>
													</div>
												</div>

												<script>
													function getcompany(val) {
														$.ajax({
															type: "POST",
															url: "get_company.php",
															data: 'companyid=' + val,
															success: function(data) {
																$("#location").html(data);
															}
														});

														// $.ajax({
														// 	type: "POST",
														// 	url: "get_company.php",
														// 	data: 'companyid=' + val,
														// 	success: function(data) {
														// 		$("#subject").html(data);

														// 	}
														// });
													}
												</script>

												<div class="col-xl-6 col-lg-6 col-md-6">
													<div class="form-group">
														<label class="text-dark ft-medium">Job Location*</label>
														<div id="location"></div>


													</div>
												</div>

												<!-- <div class="col-xl-6 col-lg-6 col-md-6">
													<div class="form-group">
														<label class="text-dark ft-medium">Country</label>
														<input type="text" class="form-control" placeholder="Country" />
													</div>
												</div>

												<div class="col-xl-6 col-lg-6 col-md-6">
													<div class="form-group">
														<label class="text-dark ft-medium">City</label>
														<input type="text" class="form-control" placeholder="City" />
													</div>
												</div>

												<div class="col-xl-12 col-lg-12 col-md-12">
													<div class="form-group">
														<label class="text-dark ft-medium">Full Address</label>
														<input type="text" class="form-control" placeholder="#10 Marke Juger, SBI Road" />
													</div>
												</div> -->



												<div class="col-xl-6 col-lg-6 col-md-6">
													<div class="form-group">
														<label class="text-dark ft-medium">Job Type</label>
														<select class="custom-select rounded" name="job_type" required>
															<option>Full Time</option>
															<option>Part Time</option>
															<option>Internship</option>
															<option>Contract</option>
															<option>Freelancing</option>
															<option>Temporary</option>
															<option>Volunteer</option>
														</select>
													</div>
												</div>

												<div class="col-xl-6 col-lg-6 col-md-6">
													<div class="form-group">
														<label class="text-dark ft-medium" required>Career Level</label>
														<select class="custom-select rounded" name="career_level">
															<option>Begginer</option>
															<option>Junior</option>
															<option>Manager</option>
															<option>Team leader</option>
															<option>Not Necessary</option>
														</select>
													</div>
												</div>


												<div class="col-xl-6 col-lg-6 col-md-6">
													<div class="form-group">
														<label class="text-dark ft-medium">Experience</label>
														<select class="custom-select rounded" name="experience" required>
															<option>Begginer</option>
															<option>0 To 6 Month</option>
															<option>1 Years</option>
															<option>2 Years</option>
															<option>3 Years</option>
															<option>4 Years</option>
															<option>5+ Years</option>
															<option>No Restriction</option>
														</select>
													</div>
												</div>

												<div class="col-xl-6 col-lg-6 col-md-6">
													<div class="form-group">
														<label class="text-dark ft-medium">Qualification</label>
														<select class="custom-select rounded" name="qualification" required>
															<option>B.Sc</option>
															<option>Master Degree</option>
															<option>BPharma</option>
															<option>P.H.D.</option>
															<option>Other</option>
														</select>
													</div>
												</div>

												<div class="col-xl-6 col-lg-6 col-md-6">
													<div class="form-group">
														<label class="text-dark ft-medium">Gender</label>
														<select class="custom-select rounded" name="gender" required>
															<option>Male</option>
															<option>Female</option>
															<option>Both</option>
														</select>
													</div>
												</div>
												<div class="col-xl-6 col-lg-6 col-md-6">
													<div class="form-group">
														<label class="text-dark ft-medium">Salary (optional) <code>Leave blank if not necessary to specfy</code></label>
														<input type="number" class="form-control rounded" name="salary" placeholder="Salary Amount" id="">
													</div>
												</div>

												<div class="col-xl-6 col-lg-6 col-md-6">
													<div class="form-group">
														<label class="text-dark ft-medium">Application Deadline</label>
														<input type="date" class="form-control rounded" placeholder="dd-mm-yyyy" name="deadline" required>
													</div>
												</div>


												<div class="col-xl-12 col-lg-12 col-md-12">
													<div class="form-group"><br>
														<hr>
														<h4>Screening Questions </h4>
														<label class="text-dark ft-medium">Add questions to screen for qualified applicants</label>
														<div class="row">
															<div class="col-xl-12 col-lg-12 col-md-12">
																<ul class="no-ul-list">
																	<br>
																	<?php
																	$query = "SELECT * from tblscreening ORDER BY id DESC" or die(mysqli_error($con));
																	$run = mysqli_query($con, $query);
																	$count = 0;
																	while ($row = mysqli_fetch_array($run)) {

																	?>
																		<li>
																			<label for="<?php echo $row['id'] ?>" class="checkbox-custom-label"><i>Question <strong>(<?php echo $row['q_title'] ?>)</strong></i></label>
																			<input id="<?php echo $row['id'] ?>" value="<?php echo $row['id'] ?>" class="checkbox-custom" name="question[]" type="checkbox">
																			<label for="<?php echo $row['id'] ?>" class="checkbox-custom-label"><?php echo $row['question'] ?><span>?</span></label>
																			<label for="<?php echo $row['id'] ?>" class="checkbox-custom-label"><i>Ideal Answer</i></label>
																			<label for="<?php echo $row['id'] ?>" class="checkbox-custom-label"><?php echo $row['ideal_ans'] ?></label>
																		</li>
																		<hr>
																	<?php } ?>
																	<!-- <li>
																		<label for="q2" class="checkbox-custom-label"><i>Question <strong>(Background Check)</strong></i></label>
																		<input id="q2" class="checkbox-custom" name="q2" type="checkbox">
																		<label for="q2" class="checkbox-custom-label">Are you willing to undergo a background check, in accordance with local law/regulations<span>?</span></label>
																		<label for="q2" class="checkbox-custom-label"><i>Ideal Answer</i></label>
																		<label for="q2" class="checkbox-custom-label">Yes</label>
																	</li>
																	<br> -->

																</ul>
															</div>

														</div>
													</div>





													<div class="col-12">
														<div class="form-group">
															<button type="submit" class="btn btn-md ft-medium text-light rounded theme-bg" name="add_job">Publish Job</button>
														</div>
													</div>

												</div>
											</div>
									</form>
								</div>
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

<!-- Mirrored from themezhub.net/live-workplex/workplex/dashboard-post-job.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Feb 2022 12:07:20 GMT -->

</html>