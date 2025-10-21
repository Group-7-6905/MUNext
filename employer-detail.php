<!DOCTYPE html>
<html lang="zxx">

<?php
require('./mysqli_connect.php');
		// header.php
		include 'session_check.php';

		include "include/helper.php";


//////////////////////Get Company Details////////////////////////
if (!empty($_GET['companyid'])) {

	$companyid = $_GET['companyid'];

	$query = "SELECT * from tblcompany WHERE COMPANYID = '$companyid'";
	$result = mysqli_query($con, $query);
	$row = mysqli_fetch_array($result);

	$COMPANYID = $row['COMPANYID'];
	$COMPANYNAME = $row['COMPANYNAME'];
	$COMPANYADDRESS = $row['COMPANYADDRESS'];
	$COMPANYCONTACTNO = $row['COMPANYCONTACTNO'];
	$COMPANYSTATUS = $row['COMPANYSTATUS'];
	$COMPANYABOUT = $row['COMPANYABOUT'];
	$COMPANYEMAIL = $row['COMPANYEMAIL'];
	$COMPANYINDUSTRY = $row['COMPANYINDUSTRY'];
	$COMPANYSPECIALISM = $row['COMPANYSPECIALISM'];
	$COMPANYCOUNTRY = $row['COMPANYCOUNTRY'];
	$COMPANYCITY = $row['COMPANYCITY'];
	$COMPANYAWARD = $row['COMPANYAWARD'];
	$COMPANYYEAR = $row['COMPANYYEAR'];
	$COMPANYAWARDDESC  = $row['COMPANYAWARDDESC'];
	$COMPANYLOGO = $row['COMPANYLOGO'];
} else {
	header('location: job-search-v1.php');
}
//////////////////////Get Company Details Ends//////////////////////


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
        <div class="bg-light py-5">
            <div class="ht-30"></div>
            <div class="container">
                <div class="row">
                    <div class="colxl-12 col-lg-12 col-md-12">
                        <h1 class="ft-medium">Employer Detail</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item"><a href="#">Employer</a></li>
                                <li class="breadcrumb-item active theme-cl" aria-current="page">Employer Detail</li>
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
                                <div class="dash_auth_thumb rounded p-1 border d-inline-flex mx-auto mb-3">
                                    <img src="./<?php echo $COMPANYLOGO ?>" class="img-fluid" width="100" alt="" />
                                </div>
                                <div class="dash_caption mb-4">
                                    <h4 class="fs-lg ft-medium mb-0 lh-1"><?php echo $COMPANYNAME ?></h4>
                                    <span
                                        class="text-muted smalls mr-2 mb-2 d-inline-flex px-2 py-1 rounded theme-cl theme-bg-light"><i
                                            class="lni lni-key mr-1"></i><?php echo $COMPANYID ?></span>
                                    <span class="text-muted smalls"><i
                                            class="lni lni-map-marker mr-1"></i><?php echo $COMPANYCITY ?>,
                                        <?php echo $COMPANYCOUNTRY ?></span>

                                </div>
                                <div class="jb-list-01-title px-2">
                                    <span
                                        class="mr-2 mb-2 d-inline-flex px-2 py-1 rounded theme-cl theme-bg-light"><?php echo $COMPANYSTATUS ?></span>
                                    <span
                                        class="mr-2 mb-2 d-inline-flex px-2 py-1 rounded text-warning bg-light-warning"><?php echo $COMPANYSPECIALISM ?></span>
                                </div>
                            </div>

                            <div class="cdt_caps">
                                <div
                                    class="job_grid_footer pb-3 px-3 d-flex align-items-center justify-content-between">
                                    <div class="df-1 text-muted"><i
                                            class="lni lni-briefcase mr-1"></i><?php echo $COMPANYADDRESS ?></div>
                                    <div class="df-1 text-muted"><i
                                            class="lni lni-laptop-phone mr-1"></i><?php echo $COMPANYINDUSTRY ?></div>
                                </div>
                                <div class="job_grid_footer pb-3 px-3 align-items-center justify-content-between">
                                    <div class="df-1 text-muted"><i
                                            class="lni lni-envelope mr-1"></i><?php echo $COMPANYEMAIL ?></div>
                                    <div class="df-1 text-muted"><i
                                            class="lni lni-phone mr-1"></i><?php echo $COMPANYCONTACTNO ?></div>
                                </div>
                            </div>

                            <div class="cdt_caps py-5 px-3">
                                <a href="mailto:<?php echo $COMPANYEMAIL ?>"
                                    class="btn btn-md theme-bg text-light rounded full-width">Contact Company</a>
                            </div>

                        </div>
                    </div>

                    <div class="col-12 col-md-12 col-lg-8 col-xl-8">

                        <!-- row -->
                        <div class="row align-items-start">

                            <!-- About -->
                            <div class="abt-cdt d-block full-width mb-4">
                                <h4 class="ft-medium mb-1 fs-md">About <?php echo $COMPANYNAME ?></h4>
                                <p><?php echo $COMPANYABOUT ?></p>
                            </div>

                            <!-- Hobbies -->
                            <div class="abt-cdt d-block full-width mb-4">
                                <h4 class="ft-medium mb-1 fs-md">Specialisms</h4>
                                <div class="position-relative row">
                                    <div class="col-lg-12 col-md-12 col-12">
                                        <div class="mb-2 mr-4 ml-lg-0 mr-lg-4">
                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="rounded-circle bg-light-success theme-cl p-1 small d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-check small"></i>
                                                </div>
                                                <h6 class="mb-0 ml-3 text-muted fs-sm"><?php echo $COMPANYSPECIALISM ?>
                                                </h6>
                                            </div>
                                        </div>
                                        <!-- <div class="mb-2 mr-4 ml-lg-0 mr-lg-4">
                                       <div class="d-flex align-items-center">
                                          <div class="rounded-circle bg-light-success theme-cl p-1 small d-flex align-items-center justify-content-center">
                                             <i class="fas fa-check small"></i>
                                          </div>
                                          <h6 class="mb-0 ml-3 text-muted fs-sm">Strong Expertise in CodeIgniter Framework .</h6>
                                       </div>
                                    </div>
                                    <div class="mb-2 mr-4 ml-lg-0 mr-lg-4">
                                       <div class="d-flex align-items-center">
                                          <div class="rounded-circle bg-light-success theme-cl p-1 small d-flex align-items-center justify-content-center">
                                             <i class="fas fa-check small"></i>
                                          </div>
                                          <h6 class="mb-0 ml-3 text-muted fs-sm">Understanding of MVC design pattern.</h6>
                                       </div>
                                    </div>
                                    <div class="mb-2 mr-4 ml-lg-0 mr-lg-4">
                                       <div class="d-flex align-items-center">
                                          <div class="rounded-circle bg-light-success theme-cl p-1 small d-flex align-items-center justify-content-center">
                                             <i class="fas fa-check small"></i>
                                          </div>
                                          <h6 class="mb-0 ml-3 text-muted fs-sm">Expertise in PHP, MVC Frameworks and good technology exposure of Codeigniter .</h6>
                                       </div>
                                    </div>
                                    <div class="mb-2 mr-4 ml-lg-0 mr-lg-4">
                                       <div class="d-flex align-items-center">
                                          <div class="rounded-circle bg-light-success theme-cl p-1 small d-flex align-items-center justify-content-center">
                                             <i class="fas fa-check small"></i>
                                          </div>
                                          <h6 class="mb-0 ml-3 text-muted fs-sm">Basic understanding of front-end technologies, such as JavaScript, HTML5, and CSS3</h6>
                                       </div>
                                    </div>
                                    <div class="mb-2 mr-4 ml-lg-0 mr-lg-4">
                                       <div class="d-flex align-items-center">
                                          <div class="rounded-circle bg-light-success theme-cl p-1 small d-flex align-items-center justify-content-center">
                                             <i class="fas fa-check small"></i>
                                          </div>
                                          <h6 class="mb-0 ml-3 text-muted fs-sm">Good knowledge of relational databases, version control tools and of developing web services.</h6>
                                       </div>
                                    </div>
                                    <div class="mb-2 mr-4 ml-lg-0 mr-lg-4">
                                       <div class="d-flex align-items-center">
                                          <div class="rounded-circle bg-light-success theme-cl p-1 small d-flex align-items-center justify-content-center">
                                             <i class="fas fa-check small"></i>
                                          </div>
                                          <h6 class="mb-0 ml-3 text-muted fs-sm">Proficient understanding of code versioning tools, such as Git.</h6>
                                       </div>
                                    </div> -->
                                    </div>
                                </div>
                            </div>

                            <!-- Award -->
                            <div class="abt-cdt d-block full-width mb-4">
                                <h4 class="ft-medium mb-1 fs-md">Award</h4>
                                <div class="exslio-list mt-3">
                                    <ul>
                                        <li>
                                            <div class="esclio-110 bg-light rounded px-3 py-3">
                                                <h4 class="mb-0 ft-medium fs-md"><?php echo $COMPANYAWARD ?></h4>
                                                <div class="esclio-110-info full-width mb-2">
                                                    <span class="text-muted mr-2"><i
                                                            class="lni lni-calendar mr-1"></i><?php echo $COMPANYYEAR ?></span>
                                                </div>
                                                <div class="esclio-110-decs full-width">
                                                    <p><?php echo $COMPANYAWARDDESC ?>
                                                        <!-- <a href="javascript:void(0);" class="theme-cl">Read More..</a> -->
                                                    </p>
                                                </div>
                                            </div>
                                        </li>
                                        <!-- <li>
                                       <div class="esclio-110 bg-light rounded px-3 py-3">
                                          <h4 class="mb-0 ft-medium fs-md">CIMPLA Award</h4>
                                          <div class="esclio-110-info full-width mb-2">
                                             <span class="text-muted mr-2"><i class="lni lni-calendar mr-1"></i>2012</span>
                                          </div>
                                          <div class="esclio-110-decs full-width">
                                             <p>Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam <a href="javascript:void(0);" class="theme-cl">Read More..</a></p>
                                          </div>
                                       </div>
                                    </li>

                                    <li>
                                       <div class="esclio-110 bg-light rounded px-3 py-3">
                                          <h4 class="mb-0 ft-medium fs-md">Lisa Award</h4>
                                          <div class="esclio-110-info full-width mb-2">
                                             <span class="text-muted mr-2"><i class="lni lni-calendar mr-1"></i>2015</span>
                                          </div>
                                          <div class="esclio-110-decs full-width">
                                             <p>Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam <a href="javascript:void(0);" class="theme-cl">Read More..</a></p>
                                          </div>
                                       </div>
                                    </li> -->
                                    </ul>
                                </div>
                            </div>

                        </div>
                        <!-- row -->

                    </div>

                </div>
            </div>
        </section>
        <!-- ======================= Dashboard Detail End ======================== -->

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

<!-- Mirrored from themezhub.net/live-workplex/workplex/employer-detail.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Feb 2022 12:07:10 GMT -->

</html>