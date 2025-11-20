<!DOCTYPE html>
<html lang="zxx">

<?php 
 if (!empty($_GET['categoryid'])) {
   $categoryid = $_GET['categoryid'];
 }
if (!empty($_GET['subcategoryid'])) {
   $subcategoryid = $_GET['subcategoryid'];
}

require 'include/phpcode.php';

if (!empty($_GET['categorytype'])) {
   $categorytype = $_GET['categorytype'];



   if (($categorytype == "delete")) {
      $catid = $_GET['catid'];
      $delete_sql = "DELETE from tbljobcategory where id='$catid'";
      mysqli_query($con, $delete_sql); ?>

<script>
alert("Category was deleted");
// setTimeout(function() {
//    window.location.href = 'dashboard-job-category.php';
// }, 3000);
</script>
<?php
   }
}

if (!empty($_GET['subcategorytype'])) {
   $subcategorytype = $_GET['subcategorytype'];



   if (($categorysubcategorytypetype == "delete")) {
      $subcatid = $_GET['subcatid'];
      $delete_sql = "DELETE from tbljobsubcategory where id='$subcatid'";
      mysqli_query($con, $delete_sql); ?>

<script>
alert("Subcategory was deleted");
//setTimeout(function() {
// window.location.href = 'dashboard-job-category.php';
//}, 3000);
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
            <a class="mobNavigation" data-toggle="collapse" href="#MobNav" role="button" aria-expanded="false"
                aria-controls="MobNav">
                <i class="fas fa-bars mr-2"></i>Dashboard Navigation
            </a>

            <?php include 'sidenav.php' ?>
            <div class="dashboard-content">
                <div class="dashboard-tlbar d-block mb-5">
                    <div class="row">
                        <div class="colxl-12 col-lg-12 col-md-12">
                            <h1 class="ft-medium"> Manage Job Category/Sub Category</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item text-muted"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item text-muted"><a href="#">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="#" class="theme-cl">Manage Job Category</a>
                                    </li>
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
                                        <h4 class="mb-0 ft-medium fs-md"><i
                                                class="fa fa-file mr-1 theme-cl fs-sm"></i>Add Job Category</h4>
                                    </div>
                                </div>

                                <div class="_dashboard_content_body py-3 px-3">

                                    <div class="col-xl-12 col-lg-12 col-md-12">
                                        <?php
                              if ((empty($_GET['subcategoryid'])) and ((empty($_GET['categoryid'])))) { ?>
                                        <ul class="nav nav-tabs b-0 d-flex align-items-center justify-content-center simple_tab_links mb-4"
                                            id="myTab" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link" id="description-tab" href="#description"
                                                    data-toggle="tab" role="tab" aria-controls="description"
                                                    aria-selected="true" aria-expanded="true">Category List</a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link" href="#information" id="information-tab"
                                                    data-toggle="tab" role="tab" aria-controls="information"
                                                    aria-selected="false" aria-expanded="false">Add Category</a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link" id="description-tab" href="#description2"
                                                    data-toggle="tab" role="tab" aria-controls="description"
                                                    aria-selected="true" aria-expanded="true">SubCategory List</a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link" href="#information2" id="information-tab"
                                                    data-toggle="tab" role="tab" aria-controls="information"
                                                    aria-selected="false" aria-expanded="false">Add SubCategory</a>
                                            </li>

                                        </ul>
                                        <?php } ?>
                                        <div class="row">



                                            <div class="tab-content" id="myTabContent">



                                                <!-- ////////////////////////////////////////Category///////////////////////////////////// -->
                                                <!-- Description Content -->
                                                <div class="tab-pane fade" id="description" role="tabpanel"
                                                    aria-labelledby="description-tab" aria-expanded="true">
                                                    <div class="col-xl-12 col-lg-12 col-md-12">
                                                        <div class="mb-4 tbl-lg rounded overflow-hidden">
                                                            <div class="table-responsive bg-white">
                                                                <table class="table">
                                                                    <thead class="thead-dark">
                                                                        <tr>
                                                                            <th scope="col">SN</th>
                                                                            <th scope="col">Category</th>
                                                                            <!-- <th scope="col">Sub Category</th> -->
                                                                            <th scope="col">Action</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php
                                                      $cn = 0;
                                                      $query = "SELECT * from tbljobcategory ORDER BY ID DESC" or die(mysqli_error($con));

                                                      $run = mysqli_query($con, $query);
                                                      while ($row = mysqli_fetch_array($run)) {
                                                         $CATEGORY = $row['CATEGORY'];
                                                         $ID = $row['ID'];
                                                         $cn++;
                                                      ?>
                                                                        <tr>
                                                                            <td>

                                                                                <span><?php echo $cn ?></span>

                                                                            </td>
                                                                            <td><span
                                                                                    class="text-info"><?php echo $CATEGORY ?></span>
                                                                            </td>

                                                                            <td>
                                                                                <div class="dash-action">
                                                                                    <a href="dashboard-job-category.php?categoryid=<?php echo $ID ?>"
                                                                                        class="p-2 circle text-info bg-light-info d-inline-flex align-items-center justify-content-center mr-1"><i
                                                                                            class="fas fa-edit"></i></a>
                                                                                    <a href="?categorytype=delete&catid=<?php echo $ID ?>"
                                                                                        onclick="return confirm('Are you sure you want to delete?');"
                                                                                        class="p-2 circle text-danger bg-light-danger d-inline-flex align-items-center justify-content-center ml-1"><i
                                                                                            class="lni lni-trash-can"></i></a>
                                                                                </div>
                                                                            </td>
                                                                        </tr>

                                                                        <?php } ?>

                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                                <!-- Additional Content -->
                                                <div class="tab-pane fade" id="information" role="tabpanel"
                                                    aria-labelledby="information-tab" aria-expanded="false">
                                                    <form class="row" method="post">
                                                        <div class="additionals card-body">
                                                            <div class="row">

                                                                <div class="col-xl-12 col-lg-12 col-md-12">
                                                                    <div class="form-group">
                                                                        <label
                                                                            class="text-dark ft-medium">Category*</label>
                                                                        <input type="text" class="form-control rounded"
                                                                            placeholder="Category" name="category"
                                                                            required>
                                                                    </div>
                                                                </div>


                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        <button type="submit" name="add_category"
                                                                            class="btn btn-md ft-medium text-light rounded theme-bg">Save
                                                                            Category</button>
                                                                    </div>
                                                                </div>


                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>




















                                                <!-- ///////////////////////////////Sub Category/////////////////////////////// -->

                                                <!-- Description Content -->
                                                <div class="tab-pane fade" id="description2" role="tabpanel"
                                                    aria-labelledby="description-tab" aria-expanded="true">
                                                    <div class="col-xl-12 col-lg-12 col-md-12">
                                                        <div class="mb-4 tbl-lg rounded overflow-hidden">
                                                            <div class="table-responsive bg-white">
                                                                <table class="table">
                                                                    <thead class="thead-dark">
                                                                        <tr>
                                                                            <th scope="col">SN</th>
                                                                            <th scope="col">Sub Category</th>
                                                                            <th scope="col">Category</th>
                                                                            <th scope="col">Action</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php
                                                      $cn_sub = 0;
                                                      $query = "SELECT * from tbljobsubcategory ORDER BY ID DESC" or die(mysqli_error($con));

                                                      $run = mysqli_query($con, $query);
                                                      while ($row = mysqli_fetch_array($run)) {
                                                         $CATEGORYID = $row['CATEGORYID'];
                                                         $SUBCATEGORY = $row['SUBCATEGORY'];
                                                         $SUBCATEGORYID = $row['ID'];
                                                         $cn_sub++;

                                                         $querycat = "SELECT * from tbljobcategory WHERE ID = '$CATEGORYID'";
                                                         $resultcat = mysqli_query($con, $querycat);
                                                         $rowcat = mysqli_fetch_array($resultcat);
                                                         $CATEGORY = $rowcat['CATEGORY'];
                                                      ?>
                                                                        <tr>
                                                                            <td>

                                                                                <span
                                                                                    class="fs-md mb-0 "><?php echo $cn_sub ?></span>

                                                                            </td>
                                                                            <td><span
                                                                                    class="text-info"><?php echo $SUBCATEGORY ?></span>
                                                                            </td>
                                                                            <td><span
                                                                                    class="text-info"><?php echo $CATEGORY ?></span>
                                                                            </td>
                                                                            <td>
                                                                                <div class="dash-action">
                                                                                    <a href="dashboard-job-category.php?subcategoryid=<?php echo $SUBCATEGORYID ?>"
                                                                                        class="p-2 circle text-info bg-light-info d-inline-flex align-items-center justify-content-center mr-1"><i
                                                                                            class="fas fa-edit"></i></a>
                                                                                    <a href="?subcategorytype=delete&subcatid=<?php echo $SUBCATEGORYID ?>"
                                                                                        onclick="return confirm('Are you sure you want to delete?');"
                                                                                        class="p-2 circle text-danger bg-light-danger d-inline-flex align-items-center justify-content-center ml-1"><i
                                                                                            class="lni lni-trash-can"></i></a>
                                                                                </div>
                                                                            </td>
                                                                        </tr>

                                                                        <?php } ?>

                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                                <!-- Additional Content -->
                                                <div class="tab-pane fade" id="information2" role="tabpanel"
                                                    aria-labelledby="information-tab" aria-expanded="false">
                                                    <form class="row" method="post">
                                                        <div class="additionals card-body">
                                                            <div class="row">

                                                                <div class="col-xl-12 col-lg-12 col-md-12">
                                                                    <div class="form-group">
                                                                        <label
                                                                            class="text-dark ft-medium">Category</label>
                                                                        <select class="custom-select simple"
                                                                            name="categoryid" required>
                                                                            <option value="" hidden>Select Category
                                                                            </option>
                                                                            <?php
                                                         $query = "SELECT * from tbljobcategory ORDER BY ID DESC" or die(mysqli_error($con));

                                                         $run = mysqli_query($con, $query);

                                                         while ($row = mysqli_fetch_array($run)) {
                                                         ?>
                                                                            <option value="<?php echo $row['ID'] ?>">
                                                                                <?php echo $row['CATEGORY'] ?></option>

                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>


                                                                <div class="col-xl-12 col-lg-12 col-md-12">
                                                                    <div class="form-group">
                                                                        <label
                                                                            class="text-dark ft-medium">Subcategory*</label>
                                                                        <input type="text" class="form-control rounded"
                                                                            placeholder="Subcategory" name="subcategory"
                                                                            required>
                                                                    </div>
                                                                </div>


                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        <button type="submit"
                                                                            class="btn btn-md ft-medium text-light rounded theme-bg"
                                                                            name="add_subcategory">Save
                                                                            Subcategory</button>
                                                                    </div>
                                                                </div>


                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>




                                            <?php
                                 if (!empty($_GET['categoryid'])) {

                                    $categoryid = $_GET['categoryid'];
                                    $query = "SELECT * from tbljobcategory WHERE ID = '$categoryid'";
                                    $result = mysqli_query($con, $query);
                                    $row = mysqli_fetch_array($result);

                                    $CATEGORY = $row['CATEGORY'];

                                 ?>
                                            <form class="row" method="post">
                                                <div class="additionals card-body">
                                                    <div class="row">

                                                        <div class="col-xl-12 col-lg-12 col-md-12">
                                                            <div class="form-group">
                                                                <label class="text-dark ft-medium">Category*</label>
                                                                <input type="text" class="form-control rounded"
                                                                    placeholder="Category" name="category" required
                                                                    value="<?php echo $CATEGORY ?>">
                                                            </div>
                                                        </div>


                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <button type="submit" name="edit_category"
                                                                    class="btn btn-md ft-medium text-light rounded theme-bg">Update
                                                                    Category</button>
                                                            </div>
                                                        </div>


                                                    </div>
                                                </div>
                                            </form>
                                            <?php } ?>




                                            <?php
                                 if (!empty($_GET['subcategoryid'])) {
                                    $subcategoryid = $_GET['subcategoryid'];

                                    $query = "SELECT * from tbljobsubcategory WHERE ID = '$subcategoryid'";
                                    $result = mysqli_query($con, $query);
                                    $row = mysqli_fetch_array($result);

                                    $CATEGORYID = $row['CATEGORYID'];
                                    $SUBCATEGORY = $row['SUBCATEGORY'];
                                    $querycat = "SELECT * from tbljobcategory WHERE ID = '$CATEGORYID'";
                                    $resultcat = mysqli_query($con, $querycat);
                                    $rowcat = mysqli_fetch_array($resultcat);
                                    $CATEGORY = $rowcat['CATEGORY'];

                                 ?>
                                            <form class="row" method="post">
                                                <div class="additionals card-body">
                                                    <div class="row">

                                                        <div class="col-xl-12 col-lg-12 col-md-12">
                                                            <div class="form-group">
                                                                <label class="text-dark ft-medium">Category</label>
                                                                <select class="custom-select simple" name="categoryid"
                                                                    required>
                                                                    <option value="<?php echo $CATEGORYID ?>">
                                                                        <?php echo $CATEGORY ?></option>
                                                                    <?php
                                                      $query = "SELECT * from tbljobcategory ORDER BY ID DESC" or die(mysqli_error($con));

                                                      $run = mysqli_query($con, $query);

                                                      while ($row = mysqli_fetch_array($run)) {
                                                      ?>
                                                                    <option value="<?php echo $row['ID'] ?>">
                                                                        <?php echo $row['CATEGORY'] ?></option>

                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>


                                                        <div class="col-xl-12 col-lg-12 col-md-12">
                                                            <div class="form-group">
                                                                <label class="text-dark ft-medium">Subcategory*</label>
                                                                <input type="text" class="form-control rounded"
                                                                    placeholder="Subcategory" name="subcategory"
                                                                    required value="<?php echo $SUBCATEGORY?>">
                                                            </div>
                                                        </div>


                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <button type="submit"
                                                                    class="btn btn-md ft-medium text-light rounded theme-bg"
                                                                    name="edit_subcategory">Update Subcategory</button>
                                                            </div>
                                                        </div>


                                                    </div>
                                                </div>
                                            </form>
                                            <?php } ?>





                                        </div>

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