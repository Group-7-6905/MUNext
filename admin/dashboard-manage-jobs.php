<!DOCTYPE html>
<html lang="zxx">


<?php require 'include/phpcode.php';

if (!empty($_GET['type'])) {
	$type = $_GET['type'];

	if (($type == "delete")) {
		$Qid = $_GET['id'];
		$delete_sql = "DELETE from tbljob where JOBID='$Qid'";
		mysqli_query($con, $delete_sql); ?>

		<script>
			alert("Job was deleted");
			setTimeout(function() {
				window.location.href = 'dashboard-manage-jobs.php';
			}, 3000);
		</script>
<?php

	}
}
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
							<h1 class="ft-medium">Manage Jobs</h1>
							<nav aria-label="breadcrumb">
								<ol class="breadcrumb">
									<li class="breadcrumb-item text-muted"><a href="#">Home</a></li>
									<li class="breadcrumb-item text-muted"><a href="#">Dashboard</a></li>
									<li class="breadcrumb-item"><a href="#" class="theme-cl">Manage Jobs</a></li>
								</ol>
							</nav>
						</div>
					</div>
				</div>

				<div class="dashboard-widg-bar d-block">
					<div class="row">
						<div class="col-xl-12 col-lg-12 col-md-12">
							<div class="d-flex align-items-center p-3 alert alert-danger">
								Your listings will be automatically removed after the deadline date.
							</div>
							<div class="mb-4 tbl-lg rounded overflow-hidden">
								<div class="table-responsive bg-white">
									<table class="table">
										<thead class="thead-dark">
											<tr>
												<th scope="col">Title</th>
												<th scope="col">Filled</th>
												<th scope="col">Posted Date</th>
												<th scope="col">Expired</th>
												<th scope="col">Applications</th>
												<th scope="col">Action</th>
											</tr>
										</thead>
										<tbody>
											<?php
											$query = "SELECT * from tbljob ORDER BY JOBID DESC" or die(mysqli_error($con));
											$run = mysqli_query($con, $query);
											while ($row = mysqli_fetch_array($run)) {
												$COMPANYID = $row['COMPANYID'];
												$JOBID = $row['JOBID'];

												$queryapp = "SELECT * from tbljobapplication WHERE JOBID = '$JOBID'" or die(mysqli_error($con));
												$runapp = mysqli_query($con, $queryapp);
												$count = 0;
												while ($rowapp = mysqli_fetch_array($runapp)) {
													$count++;
												}

											?>
												<tr>
													<td>
														<div class="dash-title">
															<h4 class="mb-0 ft-medium fs-sm"><?php echo $row['JOBTITLE']; ?>

																<span class="medium theme-cl rounded text-success bg-light-success ml-1 py-1 px-2"><?php echo $row['JOBSTATUS']; ?></span>

															</h4>
														</div>
													</td>
													<td>
														<div class="dash-filled">
															<?php if ($row['JOBSTATUS'] == "Filled") { ?>
																<div class="dash-filled"><span class="p-2 circle text-light bg-success d-inline-flex align-items-center justify-content-center"><i class="lni lni-checkmark"></i></span></div>
															<?php } else { ?>
																<span class="p-2 circle gray d-inline-flex align-items-center justify-content-center"><i class="lni lni-minus"></i></span>
															<?php } ?>

														</div>
													</td>

													<td><?php echo $row['DATEPOSTED']; ?></td>
													<td><?php echo $row['DEADLINE']; ?></td>


													<td>
														<?php if ($count > 0) { ?>
															<a href="dashboard-manage-applications.php?jobid=<?php echo $JOBID ?>" class="theme-bg text-light rounded px-3 py-2 ft-medium">Total <?php echo $count ?></a>
														<?php } else { ?>
															<a href="javascript:void(0);" class="gray rounded px-3 py-2 ft-medium">----</a>
														<?php } ?>

													</td>

													<td>
														<div class="dash-action">
															<a href="dashboard-job-details.php?id=<?php echo $row['JOBID']; ?>" class="p-2 circle text-info bg-light-info d-inline-flex align-items-center justify-content-center mr-1"><i class="lni lni-eye"></i></a>
															<a href="dashboard-jobs-edit.php?id=<?php echo $row['JOBID']; ?>" class="p-2 circle text-success bg-light-success d-inline-flex align-items-center justify-content-center"><i class="lni lni-pencil"></i></a>

															<a href="?type=delete&id=<?php echo $row['JOBID'] ?>" onclick="return confirm('Are you sure you want to delete?');" class="p-2 circle text-danger bg-light-danger d-inline-flex align-items-center justify-content-center ml-1"><i class="lni lni-trash-can"></i></a>
														</div>
													</td>
												</tr>
											<?php
											}
											if ($count < 1) { ?>
												<tr>
													<td></td>
													<td></td>
													<td><i>No Job Yet...</i></td>
													<td></td>
													<td></td>
													<td></td>

												</tr>
											<?php } ?>
											<!-- <tr>
												<td>
													<div class="dash-title">
														<h4 class="mb-0 ft-medium fs-sm">WordPress Developer & Database Management System</h4>
													</div>
												</td>
												<td>
													<span class="p-2 circle gray d-inline-flex align-items-center justify-content-center"><i class="lni lni-minus"></i></span>
												</td>
												<td>21 Sep 2021</td>
												<td>20 Dec 2021</td>
												<td><a href="dashboard-manage-applications.html" class="theme-bg text-light rounded px-3 py-2 ft-medium">Total 12</a></td>
												<td>
													<div class="dash-action">
														<a href="javascript:void(0);" class="p-2 circle text-info bg-light-info d-inline-flex align-items-center justify-content-center mr-1"><i class="lni lni-eye"></i></a>
														<a href="javascript:void(0);" class="p-2 circle text-success bg-light-success d-inline-flex align-items-center justify-content-center"><i class="lni lni-pencil"></i></a>
														<a href="javascript:void(0);" class="p-2 circle text-danger bg-light-danger d-inline-flex align-items-center justify-content-center ml-1"><i class="lni lni-trash-can"></i></a>
													</div>
												</td>
											</tr> -->


										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>

				</div>


				<!-- ============================ Main Section End ================================== -->

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