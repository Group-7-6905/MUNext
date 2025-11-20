<!DOCTYPE html>
<html lang="zxx">

<?php require 'include/phpcode.php';



if (!empty($_GET['type'])) {
	$type = $_GET['type'];



	if (($type == "delete")) {
		$appid = $_GET['appid'];
		$delete_sql = "DELETE from tbljobapplication where ID='$appid'";
		mysqli_query($con, $delete_sql);

		// $delete_sql = "DELETE from tblscreening_qa where ID='$appid'";
		// mysqli_query($con, $delete_sql); 
		?>

		<script>
			alert("Category was deleted");
			// setTimeout(function() {
			//    window.location.href = 'dashboard-job-category.php';
			// }, 3000);
		</script>
<?php
	}
}
?>


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
							<h1 class="ft-medium">My Applied Jobs</h1>
							<nav aria-label="breadcrumb">
								<ol class="breadcrumb">
									<li class="breadcrumb-item text-muted"><a href="#">Home</a></li>
									<li class="breadcrumb-item text-muted"><a href="#">Dashboard</a></li>
									<li class="breadcrumb-item"><a href="#" class="theme-cl">Applied Jobs</a></li>
								</ol>
							</nav>
						</div>
					</div>
				</div>

				<div class="dashboard-widg-bar d-block">
					<div class="row">
						<div class="col-xl-12 col-md-12 col-sm-12">
							<div class="cl-justify">

								<div class="cl-justify-first">
									<?php
									$query = "SELECT * from tbljobapplication where APPLICANTID = '$session_id' ORDER BY ID DESC" or die(mysqli_error($con));
									$run = mysqli_query($con, $query);
									$cn = mysqli_num_rows($run);

									?>
									<p class="m-0 p-0 ft-sm">You have applied <span class="text-dark ft-medium"><?php echo $cn ?></span> job(s)</p>
								</div>

								<div class="cl-justify-last">
									<div class="d-flex align-items-center justify-content-left">
										<div class="dlc-list">
											<!-- <select class="form-control sm rounded">
												<option>All Jobs</option>
												<option>Full Time</option>
												<option>Part Time</option>
												<option>Freelancing</option>
												<option>Internship</option>
												<option>Contract</option>
											</select> -->
										</div>
										<!-- <div class="dlc-list ml-2">
												<select class="form-control sm rounded">
													<option>Show 20</option>
													<option>Show 30</option>
													<option>Show 40</option>
													<option>Show 50</option>
													<option>Show 100</option>
													<option>Show 250</option>
												</select>
											</div> -->
									</div>
								</div>

							</div>
						</div>
						<div class="col-xl-12 col-lg-12 col-md-12">
							<div class="mb-4 tbl-lg rounded overflow-hidden">
								<div class="table-responsive bg-white">
									<table class="table">
										<thead class="thead-dark">
											<tr>
												<th scope="col">Job Title</th>
												<th scope="col">Status</th>
												<th scope="col">Applied Date</th>
												<th scope="col">Action</th>
											</tr>
										</thead>
										<tbody>

											<?php
											$query = "SELECT * from tbljobapplication where APPLICANTID = '$session_id' ORDER BY ID DESC" or die(mysqli_error($con));
											$run = mysqli_query($con, $query);
											$count = 0;
											while ($row = mysqli_fetch_array($run)) {
												$count++;
												$JOBID = $row['JOBID'];
												$ID = $row['ID'];
												$APPLICATIONSTATUS = $row['APPLICATIONSTATUS'];
												$APPLICATIONDATE = $row['APPLICATIONDATE'];

												$queryJOB = "SELECT * from tbljob WHERE JOBID = '$JOBID'";
												$resultJOB = mysqli_query($con, $queryJOB);
												$rowJOB = mysqli_fetch_array($resultJOB);

												$WORKEXPERIENCE = $rowJOB['WORKEXPERIENCE'];

												$COMPANYID = $rowJOB['COMPANYID'];
												$JOBID = $rowJOB['JOBID'];
												$JOBSTATUS = $rowJOB['JOBSTATUS'];
												$DATEPOSTED = $rowJOB['DATEPOSTED'];
												$SALARY = $rowJOB['SALARY'];
												$JOBTITLE = $rowJOB['JOBTITLE'];
												$JOBCATEGORYID = $rowJOB['JOBCATEGORYID'];

												$queryjobsub = "SELECT * from tbljobsubcategory WHERE ID = '$JOBCATEGORYID'";
												$resultjobsub = mysqli_query($con, $queryjobsub);
												$rowjobsub = mysqli_fetch_array($resultjobsub);
												$SUBCATEGORY = $rowjobsub['SUBCATEGORY'];

												$querycomp = "SELECT * from tblcompany WHERE COMPANYID = '$COMPANYID'";
												$resultcomp = mysqli_query($con, $querycomp);
												$rowcomp = mysqli_fetch_array($resultcomp);

												$COMPANYNAME = $rowcomp['COMPANYNAME'];
												$COMPANYLOGO = $rowcomp['COMPANYLOGO'];
												$COMPANYADDRESS = $rowcomp['COMPANYADDRESS'];
												$COMPANYCOUNTRY = $rowcomp['COMPANYCOUNTRY'];
												$COMPANYCITY = $rowcomp['COMPANYCITY'];

											?>
												<tr>
													<td>
														<div class="cats-box rounded bg-white d-flex align-items-center">
															<div class="text-center"><img src="../<?php echo $COMPANYLOGO?>" class="img-fluid" width="55" alt=""></div>
															<div class="cats-box-caption px-2">
																<h4 class="fs-md mb-0 ft-medium"><?php echo $JOBTITLE ?> <?php if ($WORKEXPERIENCE != "No Restriction") {
																																				echo "($WORKEXPERIENCE Exp.)";
																																			} ?></h4>
																<div class="d-block mb-2 position-relative">
																	<span class="text-muted medium"><i class="lni lni-map-marker mr-1"></i><?php echo $COMPANYCITY ?>, <?php echo $COMPANYCOUNTRY ?></span>
																	<span class="muted medium ml-2 theme-cl"><i class="lni lni-briefcase mr-1"></i> <?php echo $SUBCATEGORY ?></span>
																</div>
															</div>
														</div>
													</td>
													<td><span class="text-info"><?php echo $APPLICATIONSTATUS ?></span></td>
													<td><?php echo $APPLICATIONDATE ?></td>
													<td>
														<div class="dash-action">

															<a href="../job-detail.php?jobid=<?php echo $JOBID ?>" class="p-2 circle text-info bg-light-info d-inline-flex align-items-center justify-content-center mr-1"><i class="lni lni-eye"></i></a>

															<!-- <a href="?type=delete&appid=<?php echo $ID ?>" onclick="return confirm('Are you sure you want to delete?');" class="p-2 circle text-danger bg-light-danger d-inline-flex align-items-center justify-content-center ml-1"><i class="lni lni-trash-can"></i></a> -->
														</div>
													</td>
												</tr>
											<?php }
											if ($count < 1) { ?>
												<tr>
													<td></td>
													<!-- <td></td> -->
													<td><i>No Applied Jobs Yet...</i></td>
													<td></td>

												</tr>
											<?php } ?>

											<!-- <tr>
													<td>
														<div class="cats-box rounded bg-white d-flex align-items-center">
															<div class="text-center"><img src="assets/img/c-6.png" class="img-fluid" width="55" alt=""></div>
															<div class="cats-box-caption px-2">
																<h4 class="fs-md mb-0 ft-medium">Advance Magento Developer</h4>
																<div class="d-block mb-2 position-relative">
																	<span class="text-muted medium"><i class="lni lni-map-marker mr-1"></i>Liverpool, London</span>
																	<span class="muted medium ml-2 text-warning"><i class="lni lni-briefcase mr-1"></i>Part Time</span>
																</div>
															</div>
														</div>
													</td>
													<td><span class="text-info">Active</span></td>
													<td>18 Sep 2021</td>
													<td>
														<div class="dash-action">
															<a href="javascript:void(0);" class="p-2 circle text-info bg-light-info d-inline-flex align-items-center justify-content-center mr-1"><i class="lni lni-eye"></i></a>
															<a href="javascript:void(0);" class="p-2 circle text-danger bg-light-danger d-inline-flex align-items-center justify-content-center ml-1"><i class="lni lni-trash-can"></i></a>
														</div>
													</td>
												</tr>
												<tr>
													<td>
														<div class="cats-box rounded bg-white d-flex align-items-center">
															<div class="text-center"><img src="assets/img/c-5.png" class="img-fluid" width="55" alt=""></div>
															<div class="cats-box-caption px-2">
																<h4 class="fs-md mb-0 ft-medium">Senior IOS App Developer</h4>
																<div class="d-block mb-2 position-relative">
																	<span class="text-muted medium"><i class="lni lni-map-marker mr-1"></i>Liverpool, London</span>
																	<span class="muted medium ml-2 theme-cl"><i class="lni lni-briefcase mr-1"></i>Full Time</span>
																</div>
															</div>
														</div>
													</td>
													<td><span class="text-info">Active</span></td>
													<td>07 Sep 2021</td>
													<td>
														<div class="dash-action">
															<a href="javascript:void(0);" class="p-2 circle text-info bg-light-info d-inline-flex align-items-center justify-content-center mr-1"><i class="lni lni-eye"></i></a>
															<a href="javascript:void(0);" class="p-2 circle text-danger bg-light-danger d-inline-flex align-items-center justify-content-center ml-1"><i class="lni lni-trash-can"></i></a>
														</div>
													</td>
												</tr>
												<tr>
													<td>
														<div class="cats-box rounded bg-white d-flex align-items-center">
															<div class="text-center"><img src="assets/img/c-7.png" class="img-fluid" width="55" alt=""></div>
															<div class="cats-box-caption px-2">
																<h4 class="fs-md mb-0 ft-medium">Senior Product Designer</h4>
																<div class="d-block mb-2 position-relative">
																	<span class="text-muted medium"><i class="lni lni-map-marker mr-1"></i>Liverpool, London</span>
																	<span class="muted medium ml-2 text-warning"><i class="lni lni-briefcase mr-1"></i>Part Time</span>
																</div>
															</div>
														</div>
													</td>
													<td><span class="text-info">Active</span></td>
													<td>21 Sep 2021</td>
													<td>
														<div class="dash-action">
															<a href="javascript:void(0);" class="p-2 circle text-info bg-light-info d-inline-flex align-items-center justify-content-center mr-1"><i class="lni lni-eye"></i></a>
															<a href="javascript:void(0);" class="p-2 circle text-danger bg-light-danger d-inline-flex align-items-center justify-content-center ml-1"><i class="lni lni-trash-can"></i></a>
														</div>
													</td>
												</tr>
												<tr>
													<td>
														<div class="cats-box rounded bg-white d-flex align-items-center">
															<div class="text-center"><img src="assets/img/c-8.png" class="img-fluid" width="55" alt=""></div>
															<div class="cats-box-caption px-2">
																<h4 class="fs-md mb-0 ft-medium">Basic WordPress Developer</h4>
																<div class="d-block mb-2 position-relative">
																	<span class="text-muted medium"><i class="lni lni-map-marker mr-1"></i>Liverpool, London</span>
																	<span class="muted medium ml-2 theme-cl"><i class="lni lni-briefcase mr-1"></i>Full Time</span>
																</div>
															</div>
														</div>
													</td>
													<td><span class="text-info">Active</span></td>
													<td>10 Oct 2021</td>
													<td>
														<div class="dash-action">
															<a href="javascript:void(0);" class="p-2 circle text-info bg-light-info d-inline-flex align-items-center justify-content-center mr-1"><i class="lni lni-eye"></i></a>
															<a href="javascript:void(0);" class="p-2 circle text-danger bg-light-danger d-inline-flex align-items-center justify-content-center ml-1"><i class="lni lni-trash-can"></i></a>
														</div>
													</td>
												</tr>
												<tr>
													<td>
														<div class="cats-box rounded bg-white d-flex align-items-center">
															<div class="text-center"><img src="assets/img/c-9.png" class="img-fluid" width="55" alt=""></div>
															<div class="cats-box-caption px-2">
																<h4 class="fs-md mb-0 ft-medium">Technical Content Writer</h4>
																<div class="d-block mb-2 position-relative">
																	<span class="text-muted medium"><i class="lni lni-map-marker mr-1"></i>Liverpool, London</span>
																	<span class="muted medium ml-2 text-danger"><i class="lni lni-briefcase mr-1"></i>Internship</span>
																</div>
															</div>
														</div>
													</td>
													<td><span class="text-info">Active</span></td>
													<td>15 Oct 2021</td>
													<td>
														<div class="dash-action">
															<a href="javascript:void(0);" class="p-2 circle text-info bg-light-info d-inline-flex align-items-center justify-content-center mr-1"><i class="lni lni-eye"></i></a>
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

<!-- Mirrored from themezhub.net/live-workplex/workplex/dashboard-applied-jobs.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Feb 2022 12:08:06 GMT -->

</html>