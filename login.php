<!DOCTYPE html>
<html lang="zxx">

<?php
require('./mysqli_connect.php');
		// session_start();

		// header.php
		// include "include/helper.php";


		include 'session_check.php';

		include "include/helper.php";


	
		
?>

<!-- Mirrored from themezhub.net/live-workplex/workplex/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Feb 2022 12:08:07 GMT -->

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
        <div class="gray py-3">
            <div class="container">
                <div class="row">
                    <div class="colxl-12 col-lg-12 col-md-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item"><a href="#">Pages</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Login</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- ======================= Top Breadcrubms ======================== -->

        <!-- ======================= Login Detail ======================== -->
        <section class="middle">
            <div class="container">
                <div class="row align-items-start justify-content-between">


                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">

                        <div class="form-group">
                            <?php if ($chk == "successful") { ?>
                            <h5 class="t">Registration Successful!</h5>

                            <hr>Thank You!!!<br>
                            <span>Please Sign In to your account.</span>
                            <!-- </h6> -->

                            <?php } else { ?>
                            <h5>Welcome Back!</h5>
                            <span class="t">Access Account</span>

                            <?php } ?>
                        </div>
                        <form class="border p-3 rounded" method="post">
                            <div class="form-group">
                                <label>User Name *</label>
                                <input type="text" class="form-control" placeholder="Username*" name="username">
                            </div>

                            <div class="form-group">
                                <label>Password *</label>
                                <input type="password" class="form-control" placeholder="Password*" name="password">
                            </div>

                            <div class="form-group">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="flex-1">
                                        <input id="dd" class="checkbox-custom" name="dd" type="checkbox">
                                        <label for="dd" class="checkbox-custom-label">Remember Me</label>
                                    </div>
                                    <div class="eltio_k2">
                                        <a href="#">Lost Your Password?</a>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="flex-1">
                                        <?php echo $Lerror;?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" name="login_btn"
                                    class="btn btn-md full-width theme-bg text-light fs-md ft-medium">Login</button>
                            </div>
                        </form>
                    </div>

                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mfliud">

                        <div class="form-group">

                            <h5>Create Account!</h5>
                            <span class="t">Signup Account</span>
                        </div>
                        <form class="border p-3 rounded" method="post">

                            <!-- Choose between registering as job applicant and employer -->
                            <div class="form-group">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="flex-1">
                                        <input id="applicant" class="" name="role" type="radio" value="Applicant"
                                            checked>
                                        <label for="applicant" class="checkbox-custom-label">Register as Job
                                            Applicant</label>
                                    </div>
                                    <div class="flex-1">
                                        <input id="employer" class="" name="role" type="radio" value="Employer">
                                        <label for="employer" class="checkbox-custom-label">Register as Employer</label>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>First Name *</label>
                                    <input type="text" class="form-control" placeholder="First Name" name="Fname">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Last Name</label>
                                    <input type="text" class="form-control" placeholder="Last Name" name="Lname">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Email *</label>
                                <input type="email" class="form-control" placeholder="Email*" name="email">
                            </div>

                            <div class="form-group">
                                <label>Username *</label>
                                <input type="text" class="form-control" placeholder="Username*" name="username">
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Password *</label>
                                    <input type="password" class="form-control" placeholder="Password*" name="password">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Confirm Password *</label>
                                    <input type="password" class="form-control" placeholder="Confirm Password*"
                                        name="cpassword">
                                </div>
                            </div>

                            <div class="form-group">
                                <p>By registering your details, you agree with our Terms & Conditions, and Privacy and
                                    Cookie Policy.</p>
                            </div>

                            <!-- <div class="form-group">
									<div class="d-flex align-items-center justify-content-between">
										<div class="flex-1">
											<input id="admin" class="checkbox-custom" name="admin" type="checkbox" value="Administrator">
											<label for="admin" class="checkbox-custom-label">For Administrator?</label>
										</div>		
									</div>
								</div>
								 -->
                            <div class="form-group">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="flex-1">
                                        <input id="ddd" class="checkbox-custom" name="ddd" type="checkbox">
                                        <label for="ddd" class="checkbox-custom-label">Sign me up for the
                                            Newsletter!</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="flex-1">
                                        <?php echo $error;?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" name="register_btn"
                                    class="btn btn-md full-width theme-bg text-light fs-md ft-medium">Create An
                                    Account</button>
                            </div>
                        </form>


                    </div>

                </div>
            </div>
        </section>
        <!-- ======================= Login End ======================== -->

        <?php include 'include/footer.php';
			session_destroy();
		?>


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

<!-- Mirrored from themezhub.net/live-workplex/workplex/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Feb 2022 12:08:07 GMT -->

</html>