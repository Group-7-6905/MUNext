<!DOCTYPE html>
<html lang="zxx">


<?php require 'include/phpcode.php'; ?>
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
                     <h1 class="ft-medium">Companys</h1>
                     <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                           <li class="breadcrumb-item text-muted"><a href="#">Home</a></li>
                           <li class="breadcrumb-item text-muted"><a href="#">Dashboard</a></li>
                           <li class="breadcrumb-item"><a href="dashboard-companys.php" class="theme-cl">Create a Company</a></li>
                        </ol>
                     </nav>
                     <a href="dashboard-company-add.php" class="theme-cl btn btn-success text-white"> <i class="fas fa-plus"></i> Add Company</a>
                  </div>
               </div>
            </div>

            <!-- ============================ Main Section Start ================================== -->

            <div class="container">

               <!-- row -->
               <div class="row align-items-center">
                  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">

                     <?php
                     $query = "SELECT * from tblcompany ORDER BY COMPANYID DESC" or die(mysqli_error($con));
                     $run = mysqli_query($con, $query);
                     $count = 0;
                     while ($row = mysqli_fetch_array($run)) {

                     ?>
                        <!-- Single -->
                        <div class="job_grid d-block border rounded px-3 pt-3 pb-2">
                           <div class="jb-list01-flex d-flex align-items-start justify-content-start">
                             <a href="company-detail.php?companyid=<?php echo $row['COMPANYID'] ?>">
                                 <div class="jb-list01-thumb">
                                    <img src="../<?php echo $row['COMPANYLOGO'] ?>" class="img-fluid" width="90" alt="" />
                                 </div>
                             </a>

                              <div class="jb-list01 pl-3">
                                 <div class="jb-list-01-title">
                                    <h5 class="ft-medium mb-1"><a href="company-detail.php?companyid=<?php echo $row['COMPANYID'] ?>"><?php echo $row['COMPANYNAME'] ?><img src="assets/img/verify.svg" class="ml-1" width="12" alt=""></a></h5>
                                 </div>
                                 <div class="jb-list-01-info d-block mb-3">
                                    <span class="text-muted mr-2"><i class="lni lni-map-marker mr-1"></i><?php echo $row['COMPANYCITY'] ?>, <?php echo $row['COMPANYCOUNTRY'] ?></span>
                                    <span class="text-muted mr-2"><i class="lni lni-home mr-1"></i><?php echo $row['COMPANYADDRESS'] ?></span>
                                    <span class="text-muted mr-2"><i class="lni lni-phone mr-1"></i><?php echo $row['COMPANYCONTACTNO'] ?></span>
                                    <span class="text-muted mr-2"><i class="lni lni-users mr-1"></i><?php echo $row['COMPANYINDUSTRY'] ?></span>
                                 </div>
                                 <div class="jb-list-01-title d-inline">
                                    <span class="mr-2 mb-2 d-inline-flex px-2 py-1 rounded theme-cl theme-bg-light">Full
                                       Time</span>
                                    <span class="mr-2 mb-2 d-inline-flex px-2 py-1 rounded text-warning bg-light-warning"><?php echo $row['COMPANYSPECIALISM'] ?></span>
                                 </div>
                              </div>
                              
                           </div>
                        </div>
                     <?php
                     }
                     ?>

                  </div>
                  <!-- row -->

                  <!-- <div class="row">
                     <div class="col-lg-12 col-md-12 col-sm-12">
                        <ul class="pagination">
                           <li class="page-item">
                              <a class="page-link" href="#" aria-label="Psrevious">
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