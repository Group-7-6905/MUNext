<!DOCTYPE html>
<html lang="zxx">

<?php require 'include/phpcode.php';


?>


<head>
    <meta charset="utf-8" />
    <meta name="author" content="Themezhub" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>MUNext - Creative Job Board HTML</title>

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
            <a class="mobNavigation" data-toggle="collapse" href="#MobNav" role="button" aria-expanded="false"
                aria-controls="MobNav">
                <i class="fas fa-bars mr-2"></i>Dashboard Navigation
            </a>

            <?php include 'sidenav.php' ?>


            <div class="dashboard-content">
                <div class="dashboard-tlbar d-block mb-5">
                    <div class="row">
                        <div class="colxl-12 col-lg-12 col-md-12">
                            <h1 class="ft-medium">My Alert Jobs</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item text-muted"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item text-muted"><a href="#">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="#" class="theme-cl">Alert Jobs</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="dashboard-widg-bar d-block">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12">
                            <div class="mb-4 tbl-lg rounded overflow-hidden">
                                <div class="table-responsive bg-white">
                                    <table class="table">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th scope="col">Title</th>
                                                <th scope="col">Designation</th>
                                                <th scope="col">Posted Date</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>


                                            <?php
											$count = 0;
											if ((!empty($JOBTITLE)) or (!empty($EXJOBTITLE))) {
											
											$queryJOB = "SELECT * from tbljob WHERE JOBTITLE LIKE '%$JOBTITLE%' or JOBCATEGORYID = '$JOBCATEGORYID' ORDER BY JOBID DESC" or die(mysqli_error($con));

											if (!empty($EXJOBTITLE)) {
												$queryJOB = "SELECT * from tbljob WHERE JOBTITLE LIKE '%$JOBTITLE%' or  JOBTITLE LIKE '%$EXJOBTITLE%' or JOBCATEGORYID = '$JOBCATEGORYID' ORDER BY JOBID DESC" or die(mysqli_error($con));
											}
											$resultJOB = mysqli_query($con, $queryJOB);
											
											while ($rowJOB = mysqli_fetch_array($resultJOB)) {
												$count++;


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
                                                    <div class="dash-title">
                                                        <h4 class="mb-0 ft-medium fs-sm"><?php echo $JOBTITLE ?></h4>
                                                        <div class="jbl_location"><i
                                                                class="lni lni-map-marker mr-1"></i><span><?php echo $COMPANYCITY ?>,<?php echo $COMPANYCOUNTRY ?></span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo $SUBCATEGORY ?></td>
                                                <td><?php echo $DATEPOSTED ?></td>
                                                <td>
                                                    <div class="dash-action">

                                                        <a href="../job-detail.php?jobid=<?php echo $JOBID ?>"
                                                            class="p-2 circle text-info bg-light-info d-inline-flex align-items-center justify-content-center mr-1"><i
                                                                class="lni lni-eye"></i></a>

                                                        <!-- <a href="javascript:void(0);" class="p-2 circle text-danger bg-light-danger d-inline-flex align-items-center justify-content-center ml-1"><i class="lni lni-trash-can"></i></a> -->

                                                    </div>
                                                </td>
                                            </tr>
                                            <?php } }
											if ($count < 1) { ?>
                                            <tr>
                                                <td></td>
                                                <!-- <td></td> -->
                                                <td><i>No Job Alert Yet...</i></td>
                                                <td></td>

                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
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


</html>