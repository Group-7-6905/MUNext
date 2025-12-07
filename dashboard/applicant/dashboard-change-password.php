<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';
unset($result); // Clear any $result from phpcode.php


$userId = $session_id;
$errors = [];
$success = false;
?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Change Password - Dashboard | MUNext</title>
    <link href="assets/css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.lineicons.com/3.0/lineicons.css">

    <link rel="stylesheet" href="assets/css/custom-dashboard.css">
</head>

<body>
    <div id="main-wrapper">
        <?php include 'header.php' ?>
        <div class="clearfix"></div>

        <div class="dashboard-wrap bg-light">
            <a class="mobNavigation" data-toggle="collapse" href="#MobNav" role="button" aria-expanded="false">
                <i class="fas fa-bars mr-2"></i>Dashboard Navigation
            </a>

            <?php include 'sidenav.php' ?>

            <div class="dashboard-content">
                <!-- Page Header -->
                <div class="dashboard-tlbar d-block mb-5">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12">
                            <h1 class="ft-medium">
                                <i class="lni lni-lock"></i> Change Password
                            </h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item text-muted"><a href="dashboard.php">Home</a></li>
                                    <li class="breadcrumb-item text-muted"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="#" class="theme-cl">Change Password</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="dashboard-widg-bar d-block">
                    <div class="row">
                        <div class="col-xl-8 col-lg-10 col-md-12">
                            <!-- Success Message -->
                            <?php if ($success || isset($_SESSION['success_msg'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="lni lni-checkmark-circle"></i>
                                <strong>Success!</strong>
                                <?php echo isset($_SESSION['success_msg']) ? $_SESSION['success_msg'] : 'Your password has been changed successfully.'; ?>
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <?php unset($_SESSION['success_msg']); endif; ?>

                            <!-- Error Messages -->
                            <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="lni lni-cross-circle"></i>
                                <strong>Error!</strong> Please fix the following issues:
                                <ul>
                                    <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <?php endif; ?>

                            <!-- Password Change Card -->
                            <div class="password-card">
                                <div class="password-card-header">
                                    <i class="lni lni-lock"></i>
                                    <h4>Change Your Password</h4>
                                </div>

                                <div class="password-card-body">
                                    <form method="post" id="changePasswordForm">
                                        <!-- Current Password -->
                                        <div class="form-group">
                                            <label>Current Password <span class="required">*</span></label>
                                            <div class="password-input-wrapper">
                                                <input type="password" class="form-control" name="oldpassword"
                                                    id="oldPassword" required placeholder="Enter your current password">
                                                <i class="lni lni-eye toggle-password" data-target="oldPassword"></i>
                                            </div>
                                        </div>

                                        <!-- New Password -->
                                        <div class="form-group">
                                            <label>New Password <span class="required">*</span></label>
                                            <div class="password-input-wrapper">
                                                <input type="password" class="form-control" name="newpassword"
                                                    id="newPassword" required placeholder="Enter your new password">
                                                <i class="lni lni-eye toggle-password" data-target="newPassword"></i>
                                            </div>

                                            <!-- Password Strength Indicator -->
                                            <div class="password-strength" id="passwordStrength">
                                                <div class="password-strength-bar" id="strengthBar"></div>
                                            </div>
                                            <div class="password-strength-text" id="strengthText"></div>

                                            <!-- Password Requirements -->
                                            <div class="password-requirements">
                                                <h6>Password Requirements:</h6>
                                                <ul id="requirements">
                                                    <li id="length"><i class="lni lni-close"></i> At least 8 characters
                                                    </li>
                                                    <li id="uppercase"><i class="lni lni-close"></i> One uppercase
                                                        letter</li>
                                                    <li id="lowercase"><i class="lni lni-close"></i> One lowercase
                                                        letter</li>
                                                    <li id="number"><i class="lni lni-close"></i> One number</li>
                                                </ul>
                                            </div>
                                        </div>

                                        <!-- Confirm Password -->
                                        <div class="form-group">
                                            <label>Confirm New Password <span class="required">*</span></label>
                                            <div class="password-input-wrapper">
                                                <input type="password" class="form-control" name="rnewpassword"
                                                    id="confirmPassword" required
                                                    placeholder="Confirm your new password">
                                                <i class="lni lni-eye toggle-password"
                                                    data-target="confirmPassword"></i>
                                            </div>
                                            <small class="form-text text-muted" id="matchText"></small>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="form-group">
                                            <button type="submit" name="change_password" class="btn btn-primary">
                                                <i class="lni lni-checkmark-circle"></i> Change Password
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Security Tips -->
                            <div class="security-tips">
                                <h5>
                                    <i class="lni lni-shield"></i> Security Tips
                                </h5>
                                <ul>
                                    <li>
                                        <i class="lni lni-checkmark-circle"></i>
                                        Use a unique password that you don't use for other websites
                                    </li>
                                    <li>
                                        <i class="lni lni-checkmark-circle"></i>
                                        Avoid using personal information like your name or birthdate
                                    </li>
                                    <li>
                                        <i class="lni lni-checkmark-circle"></i>
                                        Consider using a password manager to generate and store strong passwords
                                    </li>
                                    <li>
                                        <i class="lni lni-checkmark-circle"></i>
                                        Change your password regularly (every 3-6 months)
                                    </li>
                                    <li>
                                        <i class="lni lni-checkmark-circle"></i>
                                        Never share your password with anyone
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <?php include 'footer.php' ?>
            </div>
        </div>

        <a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>
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

    <script>
    $(document).ready(function() {
        // Password visibility toggle
        $('.toggle-password').click(function() {
            const targetId = $(this).data('target');
            const input = $('#' + targetId);
            const icon = $(this);

            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('lni-eye').addClass('lni-eye-off');
            } else {
                input.attr('type', 'password');
                icon.removeClass('lni-eye-off').addClass('lni-eye');
            }
        });

        // Password strength checker
        $('#newPassword').on('input', function() {
            const password = $(this).val();
            const strength = checkPasswordStrength(password);

            $('#passwordStrength').addClass('show');
            $('#strengthBar').removeClass('strength-weak strength-medium strength-strong');
            $('#strengthText').removeClass('text-weak text-medium text-strong');

            if (password.length === 0) {
                $('#passwordStrength').removeClass('show');
                $('#strengthText').text('');
            } else if (strength.score < 3) {
                $('#strengthBar').addClass('strength-weak');
                $('#strengthText').addClass('text-weak').text('Weak');
            } else if (strength.score < 4) {
                $('#strengthBar').addClass('strength-medium');
                $('#strengthText').addClass('text-medium').text('Medium');
            } else {
                $('#strengthBar').addClass('strength-strong');
                $('#strengthText').addClass('text-strong').text('Strong');
            }

            // Update requirements
            updateRequirement('length', password.length >= 8);
            updateRequirement('uppercase', /[A-Z]/.test(password));
            updateRequirement('lowercase', /[a-z]/.test(password));
            updateRequirement('number', /[0-9]/.test(password));
        });

        // Password match checker
        $('#confirmPassword').on('input', function() {
            const password = $('#newPassword').val();
            const confirm = $(this).val();

            if (confirm.length === 0) {
                $('#matchText').text('').removeClass('text-success text-danger');
                $(this).removeClass('is-valid is-invalid');
            } else if (password === confirm) {
                $('#matchText').text('Passwords match').addClass('text-success').removeClass(
                    'text-danger');
                $(this).addClass('is-valid').removeClass('is-invalid');
            } else {
                $('#matchText').text('Passwords do not match').addClass('text-danger').removeClass(
                    'text-success');
                $(this).addClass('is-invalid').removeClass('is-valid');
            }
        });

        // Check password strength
        function checkPasswordStrength(password) {
            let score = 0;

            if (password.length >= 8) score++;
            if (password.length >= 12) score++;
            if (/[a-z]/.test(password)) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[^a-zA-Z0-9]/.test(password)) score++;

            return {
                score: Math.min(score, 5)
            };
        }

        // Update requirement indicator
        function updateRequirement(id, valid) {
            const element = $('#' + id);
            const icon = element.find('i');

            if (valid) {
                element.addClass('valid');
                icon.removeClass('lni-close').addClass('lni-checkmark-circle');
            } else {
                element.removeClass('valid');
                icon.removeClass('lni-checkmark-circle').addClass('lni-close');
            }
        }

        // Form validation
        $('#changePasswordForm').submit(function(e) {
            const newPassword = $('#newPassword').val();
            const confirmPassword = $('#confirmPassword').val();

            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }

            if (newPassword.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long!');
                return false;
            }
        });

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
    </script>
</body>

</html>