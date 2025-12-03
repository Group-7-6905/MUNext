<!DOCTYPE html>
<html lang="zxx">

<?php
require('./mysqli_connect.php');

include "session_check.php";


require_once "include/email-functions.php";

// include "include/toast.php";


$UserRole = $_GET['role'] ?? '';

// Get any URL parameter for redirect after login
$requestedUrl = isset($_GET['redirect']) ? htmlspecialchars($_GET['redirect']) : '';

$Lerror = '';
$error = '';
$alertText = '';
$chk = "";

if (!empty($_GET['chk'])) {
    $chk = $_GET['chk'];
}

// ==================== GET REGISTRATION SETTINGS ====================
// Check if user registration is allowed
$userRegQuery = "SELECT setting_value FROM tbl_settings WHERE setting_key = 'user_registration'";
$userRegResult = mysqli_query($con, $userRegQuery);
$userRegAllowed = false;
if ($userRegResult && mysqli_num_rows($userRegResult) > 0) {
    $userRegRow = mysqli_fetch_assoc($userRegResult);
    $userRegAllowed = ($userRegRow['setting_value'] == '1');
}

// Check if company registration is allowed
$compRegQuery = "SELECT setting_value FROM tbl_settings WHERE setting_key = 'company_registration'";
$compRegResult = mysqli_query($con, $compRegQuery);
$compRegAllowed = false;
if ($compRegResult && mysqli_num_rows($compRegResult) > 0) {
    $compRegRow = mysqli_fetch_assoc($compRegResult);
    $compRegAllowed = ($compRegRow['setting_value'] == '1');
}

// Check auto-approve companies setting
$autoApproveQuery = "SELECT setting_value FROM tbl_settings WHERE setting_key = 'auto_approve_companies'";
$autoApproveResult = mysqli_query($con, $autoApproveQuery);
$autoApproveCompanies = false;
if ($autoApproveResult && mysqli_num_rows($autoApproveResult) > 0) {
    $autoApproveRow = mysqli_fetch_assoc($autoApproveResult);
    $autoApproveCompanies = ($autoApproveRow['setting_value'] == '1');
}

include "include/helper.php";


?>

<?php include 'include/head.php' ?>

<body>
    <div id="toast"></div>

    <div id="main-wrapper">
        <!-- Header -->
        <?php include 'include/header.php' ?>
        <div class="clearfix"></div>

        <!-- Breadcrumb -->
        <div class="gray py-3">
            <div class="container">
                <div class="row">
                    <div class="colxl-12 col-lg-12 col-md-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="#">Pages</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Login</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Login and Registration Section -->
        <section class="middle">
            <div class="container">
                <div class="row">
                    <!-- Login Section -->
                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mfliud">
                        <div class="form-group">
                            <?php if ($chk == "successful") { ?>
                            <h5 class="text-bold">Registration Successful!</h5>
                            <hr>
                            <div>Thank You!!!</div>
                            <span>Please Sign In to your account.</span>
                            <?php } else { ?>
                            <h5>Welcome Back!</h5>
                            <span class="t">Access Account</span>
                            <?php } ?>
                        </div>

                        <?php if (!empty($Lerror)): ?>
                        <div class="form-group">
                            <div class="alert alert-danger">
                                <?php echo $Lerror; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <form class="border p-3 rounded" method="post">
                            <div class="form-group">
                                <label>User Name *</label>
                                <input required type="text" class="form-control" placeholder="Username*"
                                    name="username">
                            </div>

                            <div class="form-group">
                                <label>Password *</label>
                                <input required type="password" class="form-control" placeholder="Password*"
                                    name="password">
                            </div>

                            <div class="form-group">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="flex-1">
                                        <input id="dd" class="checkbox-custom" name="dd" type="checkbox">
                                        <label for="dd" class="checkbox-custom-label">Remember Me</label>
                                    </div>
                                    <div class="eltio_k2">
                                        <a href="forgot-password.php">Lost Your Password?</a>
                                    </div>
                                </div>
                            </div>



                            <div class="form-group">
                                <button type="submit" name="login_btn"
                                    class="btn btn-md full-width theme-bg text-light fs-md ft-medium">
                                    Login
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Registration Section -->
                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mfliud">
                        <div class="form-group">
                            <h5>Create Account!</h5>
                            <span class="t">Signup Account</span>
                            <?php if (!empty($alertText)) { echo '<div class="pt-2">' . $alertText . '</div>'; } ?>
                        </div>

                        <form class="border p-3 rounded" method="post" id="registrationForm">
                            <!-- Role Selection -->
                            <div class="form-group">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="flex-1">
                                        <input required id="applicant" name="role" type="radio" value="Applicant"
                                            <?php echo ($UserRole == 'applicants' || $UserRole == '') ? 'checked' : ''; ?>
                                            <?php echo !$userRegAllowed ? 'disabled' : ''; ?>>
                                        <label for="applicant" class="checkbox-custom-label">
                                            Register as Job Applicant
                                            <?php if (!$userRegAllowed): ?>
                                            <small class="text-danger">(Disabled)</small>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                    <div class="flex-1">
                                        <input required id="employer" name="role" type="radio" value="Employer"
                                            <?php echo ($UserRole == 'employer') ? 'checked' : ''; ?>
                                            <?php echo !$compRegAllowed ? 'disabled' : ''; ?>>
                                        <label for="employer" class="checkbox-custom-label">
                                            Register as Employer
                                            <?php if (!$compRegAllowed): ?>
                                            <small class="text-danger">(Disabled)</small>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>First Name *</label>
                                    <input required type="text" class="form-control" placeholder="First Name"
                                        name="Fname">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Last Name *</label>
                                    <input required type="text" class="form-control" placeholder="Last Name"
                                        name="Lname">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Email *</label>
                                <input required type="email" class="form-control" placeholder="Email*" name="email">
                            </div>

                            <div class="form-group">
                                <label>Username *</label>
                                <input required type="text" class="form-control" placeholder="Username*"
                                    name="username">
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Password *</label>
                                    <input required type="password" class="form-control" placeholder="Password*"
                                        name="password" minlength="6" id="regPassword">
                                    <small class="form-text text-muted">Minimum 6 characters</small>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Confirm Password *</label>
                                    <input required type="password" class="form-control" placeholder="Confirm Password*"
                                        name="cpassword" id="regConfirmPassword">
                                </div>
                            </div>

                            <div class="form-group">
                                <p>By registering your details, you agree with our Terms & Conditions, and Privacy and
                                    Cookie Policy.</p>
                            </div>

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
                                <button type="submit" name="register_btn"
                                    class="btn btn-md full-width theme-bg text-light fs-md ft-medium">
                                    Create An Account
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <?php include 'include/footer.php'; ?>
    </div>

    <!-- Scripts -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/slick.js"></script>
    <script src="assets/js/slider-bg.js"></script>
    <script src="assets/js/smoothproducts.js"></script>
    <script src="assets/js/snackbar.min.js"></script>
    <script src="assets/js/jQuery.style.switcher.js"></script>
    <script src="assets/js/custom.js"></script>

    <!-- Toast Notification Script -->
    <script>
    function showToast(message, type = 'info') {
        const toast = document.getElementById('toast');
        if (!toast) {
            console.error('Toast element not found');
            return;
        }

        toast.className = '';

        let icon = '';
        switch (type) {
            case 'success':
                icon = '✓';
                break;
            case 'error':
                icon = '✕';
                break;
            case 'warning':
                icon = '⚠';
                break;
            case 'info':
                icon = 'ℹ';
                break;
        }

        toast.innerHTML = '<span style="font-size: 1.2rem;">' + icon + '</span><span>' + message + '</span>';
        toast.className = 'show ' + type;

        setTimeout(() => {
            toast.className = toast.className.replace('show', '');
        }, 3000);
    }

    // Password validation for registration
    document.addEventListener('DOMContentLoaded', function() {
        const regForm = document.getElementById('registrationForm');
        if (regForm) {
            regForm.addEventListener('submit', function(e) {
                const password = document.getElementById('regPassword').value;
                const confirmPassword = document.getElementById('regConfirmPassword').value;

                if (password !== confirmPassword) {
                    e.preventDefault();
                    showToast('Passwords do not match!', 'error');
                    return false;
                }

                if (password.length < 6) {
                    e.preventDefault();
                    showToast('Password must be at least 6 characters long!', 'error');
                    return false;
                }
            });
        }
    });
    </script>

    <!-- Render Toast from Session -->
    <?php echo Toast::render(); ?>

</body>

</html>