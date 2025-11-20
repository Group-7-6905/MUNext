<!DOCTYPE html>
<html lang="zxx">


<?php require 'include/phpcode.php';

//////////////////////Get Company Details////////////////////////
if (!empty($_GET['id'])) {

   $JOBID = $_GET['id'];

   $query = "SELECT * from tbljob WHERE JOBID = '$JOBID'";
   $result = mysqli_query($con, $query);
   $row = mysqli_fetch_array($result);

   $COMPANYID = $row['COMPANYID'];
   $WORKPLACE_POLICY = $row['WORKPLACE_POLICY'];
   $JOBTITLE = $row['JOBTITLE'];
   $SALARY = $row['SALARY'];
   $JOBTYPE = $row['JOBTYPE'];
   $QUALIFICATION = $row['QUALIFICATION'];
   $JOBDESCRIPTION = $row['JOBDESCRIPTION'];
   $PREFEREDSEX = $row['PREFEREDSEX'];
   $CAREERLEVEL = $row['CAREERLEVEL'];
   $WORKEXPERIENCE = $row['WORKEXPERIENCE'];
   $DEADLINE = $row['DEADLINE'];
   $JOBSTATUS = $row['JOBSTATUS'];


   $querycomp = "SELECT * from tblcompany WHERE COMPANYID = '$COMPANYID'";
   $resultcomp = mysqli_query($con, $querycomp);
   $rowcomp = mysqli_fetch_array($resultcomp);

   $COMPANYNAME = $rowcomp['COMPANYNAME'];
   $COMPANYADDRESS = $rowcomp['COMPANYADDRESS'];
   $COMPANYCOUNTRY = $rowcomp['COMPANYCOUNTRY'];
   $COMPANYCITY = $rowcomp['COMPANYCITY'];
   $COMPANYEMAIL = $rowcomp['COMPANYEMAIL'];
   $COMPANYCONTACTNO = $rowcomp['COMPANYCONTACTNO'];
   $COMPANYINDUSTRY = $rowcomp['COMPANYINDUSTRY'];
   $COMPANYSPECIALISM = $rowcomp['COMPANYSPECIALISM'];
   $COMPANYLOGO = $rowcomp['COMPANYLOGO'];
} else {
   header('location: dashboard-companys.php');
}
//////////////////////Get Company Details Ends//////////////////////


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
                     <h1 class="ft-medium">Job Datails</h1>
                     <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                           <li class="breadcrumb-item text-muted"><a href="#">Home</a></li>
                           <li class="breadcrumb-item text-muted"><a href="#">Dashboard</a></li>
                           <li class="breadcrumb-item"><a href="#" class="theme-cl">View Job Details</a></li>
                        </ol>
                     </nav>
                  </div>
               </div>
            </div>



            <div class="container">
               <div class="row">

                  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">

                     <div class="bg-white rounded px-3 py-4 mb-4">
                        <div class="jbd-01 d-flex align-items-center justify-content-between">
                           <div class="jbd-flex d-flex align-items-center justify-content-start">
                              <div class="jbd-01-thumb">
                                 <img src="../<?php echo $COMPANYLOGO ?>" class="img-fluid" width="90" alt="" />
                              </div>
                              <div class="jbd-01-caption pl-3">
                                 <div class="tbd-title">
                                    <h4 class="mb-0 ft-medium fs-md"><?php echo $JOBTITLE ?></h4>
                                 </div>
                                 <div class="jbl_location mb-3">
                                    <span><i class="lni lni-map-marker mr-1"></i><?php echo $COMPANYCITY ?>, <?php echo $COMPANYCOUNTRY ?></span>
                                    <span class="text-muted mr-2"><i class="lni lni-home mr-1"></i><?php echo $COMPANYADDRESS ?></span>
                                       <span class="medium ft-medium text-warning ml-3"><?php echo $JOBTYPE ?></span>
                                 </div>
                                 <div class="jbl_info01">
                                    <span class="px-2 py-1 ft-medium medium rounded theme-cl theme-bg-light mr-2"><?php echo $JOBSTATUS ?> </span>
                                    <span class="px-2 py-1 ft-medium medium rounded text-danger bg-light-danger mr-2"><?php echo $COMPANYINDUSTRY ?></span>
                                    <span class="px-2 py-1 ft-medium medium rounded text-purple bg-light-purple"><?php echo $COMPANYSPECIALISM ?></span>
                                 </div>
                              </div>
                           </div>
                           <div class="jbd-01-right text-right hide-1023">
                              <div class="jbl_button mb-2"><a href="dashboard-jobs-edit.php?id=<?php echo $JOBID ?> " class="btn rounded theme-bg-light theme-cl fs-sm ft-medium">Edit Job</a></div>
                              <div class="jbl_button"><a href="company-detail.php?companyid=<?php echo $COMPANYID ?>" class="btn rounded bg-white border fs-sm ft-medium">View Company</a></div>
                           </div>
                        </div>
                     </div>

                     <div class="bg-white rounded mb-4">
                        <div class="jbd-01 px-3 py-4">
                           <div class="jbd-details mb-4">
                              <h5 class="ft-medium fs-md">Job description</h5>
                              <p><?php echo $JOBDESCRIPTION ?></p>
                           </div>

                           <div class="jbd-details mb-3">
                              <h5>Screening Questions:</h5>
                              <div class="position-relative row">
                                 <div class="col-lg-12 col-md-12 col-12">

                                    <?php
                                    $sql = "SELECT * from tbljobscreening_ques where job_id ='$JOBID'  ORDER BY id DESC";
                                    $runjob = mysqli_query($con, $sql);
                                    while ($rowjob = mysqli_fetch_array($runjob)) {
                                       $question_id = $rowjob['question_id'];

                                       $query = "SELECT * from tblscreening where id ='$question_id'  ORDER BY id DESC" or die(mysqli_error($con));
                                       $run = mysqli_query($con, $query);
                                       $count = 0;
                                       while ($row = mysqli_fetch_array($run)) {
                                          $jobque_id[] = $row['id'];

                                    ?> <br>
                                          <div class="mb-2 mr-4 ml-lg-0 mr-lg-4">
                                             <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-light-success theme-cl p-1 small d-flex align-items-center justify-content-center">
                                                   <i class="fas fa-check small"></i>
                                                </div>
                                                <h6 class="mb-0 ml-3 text-muted fs-sm"><i>Question <strong>(<?php echo $row['q_title'] ?>)</strong></i></h6>
                                             </div>


                                             <label for="<?php echo $row['id'] ?>" class="checkbox-custom-label"><?php echo $row['question'] ?><span>?</span></label>
                                             <label for="<?php echo $row['id'] ?>" class="checkbox-custom-label"><i>Ideal Answer</i></label>
                                             <label for="<?php echo $row['id'] ?>" class="checkbox-custom-label"><?php echo $row['ideal_ans'] ?></label>
                                             <hr>
                                          </div>




                                    <?php }
                                    } ?>


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





                           <div class="jbd-details mb-4">
                              <h5 class="ft-medium fs-md">Job Information</h5>
                              <div class="other-details">
                                 <div class="details ft-medium"><label class="text-muted">Workplace Policy</label><span class="text-dark"><?php echo $WORKPLACE_POLICY ?></span></div>
                                 <div class="details ft-medium"><label class="text-muted">Industry Type</label><span class="text-dark"><?php echo $COMPANYINDUSTRY ?></span></div>
                                 <div class="details ft-medium"><label class="text-muted">Functional Area</label><span class="text-dark"><?php echo $COMPANYSPECIALISM ?></span></div>
                                 <div class="details ft-medium"><label class="text-muted">Employment Type</label><span class="text-dark"><?php echo $JOBTYPE ?></span></div>
                                 <div class="details ft-medium"><label class="text-muted">Qualification</label><span class="text-dark"><?php echo $QUALIFICATION ?></span></div>
                                 <div class="details ft-medium"><label class="text-muted">Prefered Gender</label><span class="text-dark"><?php echo $PREFEREDSEX ?></span></div>
                                 <div class="details ft-medium"><label class="text-muted">Experience</label><span class="text-dark"><?php echo $WORKEXPERIENCE ?></span></div>
                                 <div class="details ft-medium"><label class="text-muted">Career Level</label><span class="text-dark"><?php echo $CAREERLEVEL ?></span></div>
                                 <div class="details ft-medium"><label class="text-muted">Salary</label><span class="text-dark"><?php if ($SALARY > 0) {
                                                                                                                                    echo $SALARY; # code...
                                                                                                                                 } else {
                                                                                                                                    echo 'Not Specified';
                                                                                                                                 }  ?></span></div>
                                 <div class="details ft-medium"><label class="text-muted">Deadline</label><span class="text-dark"><?php echo $DEADLINE ?></span></div>
                              </div>
                           </div>

                           <!-- <div class="jbd-details mb-1">
                              <h5 class="ft-medium fs-md">Key Skills</h5>
                              <ul class="p-0 skills_tag text-left">
                                 <li><span class="px-2 py-1 medium skill-bg rounded text-dark">Joomla</span></li>
                                 <li><span class="px-2 py-1 medium skill-bg rounded text-dark">WordPress</span></li>
                              </ul>
                           </div> -->

                        </div>

                        <div class="jbd-02 px-3 py-3 br-top">
                           <div class="jbd-02-flex d-flex align-items-center justify-content-between">
                              <div class="jbd-02-social">
                                 <!-- <ul class="jbd-social">
                                    <li><i class="ti-sharethis"></i></li>
                                    <li><a href="javascript:void(0);"><i class="ti-facebook"></i></a></li>
                                    <li><a href="javascript:void(0);"><i class="ti-twitter"></i></a></li>
                                    <li><a href="javascript:void(0);"><i class="ti-linkedin"></i></a></li>
                                    <li><a href="javascript:void(0);"><i class="ti-instagram"></i></a></li>
                                 </ul> -->
                              </div>
                              <div class="jbd-02-aply">
                                 <div class="jbl_button mb-2">
                                    <a href="company-detail.php?companyid=<?php echo $COMPANYID ?>" class="btn btn-md rounded gray fs-sm ft-medium mr-2">View Company</a>
                                    <a href="dashboard-jobs-edit.php?id=<?php echo $JOBID ?>" class="btn btn-md rounded theme-bg text-light fs-sm ft-medium">Edit Job</a>
                                 </div>
                              </div>
                           </div>
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