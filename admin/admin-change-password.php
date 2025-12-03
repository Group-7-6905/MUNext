<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';
?>

<head>
    <meta charset="utf-8" />
    <meta name="author" content="MUNext" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Change Password - Admin Dashboard | MUNext</title>

    <!-- Custom CSS -->
    <link href="assets/css/styles.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/custom-dashboard.css" />
</head>

<body>
    <div id="main-wrapper">
        <!-- ==================== HEADER ==================== -->
        <?php include 'header.php' ?>
        <div class="clearfix"></div>

        <!-- ==================== DASHBOARD WRAPPER ==================== -->
        <div class="dashboard-wrap bg-light">
            <!-- Mobile Navigation Toggle -->
            <a class="mobNavigation" data-toggle="collapse" href="#MobNav" role="button" aria-expanded="false"
                aria-controls="MobNav">
                <i class="fas fa-bars mr-2"></i>Dashboard Navigation
            </a>

            <!-- ==================== SIDEBAR ==================== -->
            <?php include 'sidenav.php' ?>

            <!-- ==================== MAIN CONTENT ==================== -->
            <div class="dashboard-content">
                <!-- Page Header -->
                <div class="page-header-section">
                    <h1 class="page-title">
                        <i class="lni lni-lock-alt mr-2"></i>Change Password
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="admin-profile.php">My Account</a></li>
                            <li class="breadcrumb-item active">Change Password</li>
                        </ol>
                    </nav>
                </div>

                <div class="container-fluid">
                    <div class="row">
                        <!-- ==================== PASSWORD CHANGE FORM ==================== -->
                        <div class="col-lg-8 col-md-12">
                            <div class="form-card">
                                <div class="form-card-header">
                                    <h5 class="form-card-title">
                                        <div class="title-icon">
                                            <i class="lni lni-lock-alt"></i>
                                        </div>
                                        Update Your Password
                                    </h5>
                                </div>

                                <div class="form-card-body">
                                    <!-- Alert Messages -->
                                    <?php if (!empty($msg)): ?>
                                    <div class="alert-message alert-<?php echo $msgType; ?>">
                                        <i
                                            class="lni lni-<?php echo $msgType === 'success' ? 'checkmark-circle' : 'close-circle'; ?>"></i>
                                        <span><?php echo htmlspecialchars($msg); ?></span>
                                    </div>
                                    <?php endif; ?>

                                    <form method="post" id="passwordChangeForm">
                                        <!-- Current Password -->
                                        <div class="form-group">
                                            <label class="form-label">
                                                Current Password<span class="required">*</span>
                                            </label>
                                            <div class="form-control-wrapper">
                                                <input type="password" name="oldpassword" id="oldPassword"
                                                    class="form-control-custom"
                                                    placeholder="Enter your current password" required>
                                                <i class="lni lni-eye password-toggle"
                                                    onclick="togglePassword('oldPassword')"></i>
                                            </div>
                                        </div>

                                        <!-- New Password -->
                                        <div class="form-group">
                                            <label class="form-label">
                                                New Password<span class="required">*</span>
                                            </label>
                                            <div class="form-control-wrapper">
                                                <input type="password" name="newpassword" id="newPassword"
                                                    class="form-control-custom" placeholder="Enter your new password"
                                                    minlength="8" required>
                                                <i class="lni lni-eye password-toggle"
                                                    onclick="togglePassword('newPassword')"></i>
                                            </div>
                                            <div class="password-strength" id="passwordStrength">
                                                <div class="strength-bar"></div>
                                            </div>
                                            <div class="strength-text" id="strengthText"></div>
                                        </div>

                                        <!-- Confirm Password -->
                                        <div class="form-group">
                                            <label class="form-label">
                                                Confirm New Password<span class="required">*</span>
                                            </label>
                                            <div class="form-control-wrapper">
                                                <input type="password" name="confirmpassword" id="confirmPassword"
                                                    class="form-control-custom" placeholder="Re-enter your new password"
                                                    required>
                                                <i class="lni lni-eye password-toggle"
                                                    onclick="togglePassword('confirmPassword')"></i>
                                            </div>
                                            <small id="passwordMatch" style="display: none; margin-top: 5px;"></small>
                                        </div>

                                        <!-- Password Requirements -->
                                        <div class="password-requirements">
                                            <div class="requirements-title">
                                                <i class="lni lni-information mr-1"></i>Password Requirements:
                                            </div>
                                            <div class="requirement-item" id="req-length">
                                                <i class="lni lni-close-circle"></i>
                                                <span>At least 8 characters long</span>
                                            </div>
                                            <div class="requirement-item" id="req-uppercase">
                                                <i class="lni lni-close-circle"></i>
                                                <span>Contains at least one uppercase letter</span>
                                            </div>
                                            <div class="requirement-item" id="req-lowercase">
                                                <i class="lni lni-close-circle"></i>
                                                <span>Contains at least one lowercase letter</span>
                                            </div>
                                            <div class="requirement-item" id="req-number">
                                                <i class="lni lni-close-circle"></i>
                                                <span>Contains at least one number</span>
                                            </div>
                                            <div class="requirement-item" id="req-special">
                                                <i class="lni lni-close-circle"></i>
                                                <span>Contains at least one special character (!@#$%^&*)</span>
                                            </div>
                                        </div>

                                        <!-- Form Actions -->
                                        <div class="form-group" style="margin-top: 30px;">
                                            <button type="submit" name="change_password" class="btn-primary-custom">
                                                <i class="lni lni-checkmark-circle"></i>
                                                <span>Update Password</span>
                                            </button>
                                            <a href="admin-profile.php" class="btn-secondary-custom">
                                                <i class="lni lni-close"></i>
                                                <span>Cancel</span>
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- ==================== SECURITY TIPS ==================== -->
                        <div class="col-lg-4 col-md-12">
                            <div class="security-tips">
                                <div class="tips-header">
                                    <div class="tips-icon">
                                        <i class="lni lni-shield"></i>
                                    </div>
                                    <h6 class="tips-title">Security Tips</h6>
                                </div>

                                <div class="tip-item">
                                    <i class="lni lni-checkmark-circle"></i>
                                    <span>Use a strong, unique password that you don't use elsewhere</span>
                                </div>

                                <div class="tip-item">
                                    <i class="lni lni-checkmark-circle"></i>
                                    <span>Avoid using personal information in your password</span>
                                </div>

                                <div class="tip-item">
                                    <i class="lni lni-checkmark-circle"></i>
                                    <span>Change your password regularly (every 3-6 months)</span>
                                </div>

                                <div class="tip-item">
                                    <i class="lni lni-checkmark-circle"></i>
                                    <span>Never share your password with anyone</span>
                                </div>

                                <div class="tip-item">
                                    <i class="lni lni-checkmark-circle"></i>
                                    <span>Use a password manager to securely store passwords</span>
                                </div>

                                <div class="tip-item">
                                    <i class="lni lni-checkmark-circle"></i>
                                    <span>Enable two-factor authentication when available</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== FOOTER ==================== -->
                <?php include 'footer.php' ?>
            </div>
        </div>

        <!-- Back to Top -->
        <a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>
    </div>

    <!-- ==================== SCRIPTS ==================== -->
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
    // ==================== PASSWORD VISIBILITY TOGGLE ====================
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = field.nextElementSibling;

        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('lni-eye');
            icon.classList.add('lni-eye-off');
        } else {
            field.type = 'password';
            icon.classList.remove('lni-eye-off');
            icon.classList.add('lni-eye');
        }
    }

    // ==================== PASSWORD STRENGTH CHECKER ====================
    document.getElementById('newPassword').addEventListener('input', function() {
        const password = this.value;
        const strengthBar = document.querySelector('.strength-bar');
        const strengthText = document.getElementById('strengthText');
        const strengthContainer = document.getElementById('passwordStrength');

        if (password.length === 0) {
            strengthContainer.classList.remove('active');
            strengthText.textContent = '';
            return;
        }

        strengthContainer.classList.add('active');

        let strength = 0;
        const requirements = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
        };

        // Update requirement indicators
        updateRequirement('req-length', requirements.length);
        updateRequirement('req-uppercase', requirements.uppercase);
        updateRequirement('req-lowercase', requirements.lowercase);
        updateRequirement('req-number', requirements.number);
        updateRequirement('req-special', requirements.special);

        // Calculate strength
        Object.values(requirements).forEach(met => {
            if (met) strength++;
        });

        // Update strength indicator
        strengthBar.className = 'strength-bar';
        strengthText.className = 'strength-text';

        if (strength <= 2) {
            strengthBar.classList.add('strength-weak');
            strengthText.classList.add('weak');
            strengthText.textContent = 'Weak Password';
        } else if (strength <= 4) {
            strengthBar.classList.add('strength-medium');
            strengthText.classList.add('medium');
            strengthText.textContent = 'Medium Password';
        } else {
            strengthBar.classList.add('strength-strong');
            strengthText.classList.add('strong');
            strengthText.textContent = 'Strong Password';
        }
    });

    // ==================== UPDATE REQUIREMENT INDICATOR ====================
    function updateRequirement(elementId, met) {
        const element = document.getElementById(elementId);
        const icon = element.querySelector('i');

        if (met) {
            element.classList.add('met');
            icon.classList.remove('lni-close-circle');
            icon.classList.add('lni-checkmark-circle');
        } else {
            element.classList.remove('met');
            icon.classList.remove('lni-checkmark-circle');
            icon.classList.add('lni-close-circle');
        }
    }

    // ==================== PASSWORD MATCH CHECKER ====================
    document.getElementById('confirmPassword').addEventListener('input', function() {
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = this.value;
        const matchIndicator = document.getElementById('passwordMatch');

        if (confirmPassword.length === 0) {
            matchIndicator.style.display = 'none';
            return;
        }

        matchIndicator.style.display = 'block';

        if (newPassword === confirmPassword) {
            matchIndicator.style.color = '#28a745';
            matchIndicator.innerHTML = '<i class="lni lni-checkmark-circle mr-1"></i>Passwords match';
        } else {
            matchIndicator.style.color = '#dc3545';
            matchIndicator.innerHTML = '<i class="lni lni-close-circle mr-1"></i>Passwords do not match';
        }
    });

    // ==================== FORM VALIDATION ====================
    document.getElementById('passwordChangeForm').addEventListener('submit', function(e) {
        const oldPassword = document.getElementById('oldPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        // Check if all fields are filled
        if (!oldPassword || !newPassword || !confirmPassword) {
            e.preventDefault();
            alert('Please fill in all fields.');
            return false;
        }

        // Check password length
        if (newPassword.length < 8) {
            e.preventDefault();
            alert('New password must be at least 8 characters long.');
            return false;
        }

        // Check if passwords match
        if (newPassword !== confirmPassword) {
            e.preventDefault();
            alert('New passwords do not match.');
            return false;
        }

        // Check if new password is different from old
        if (oldPassword === newPassword) {
            e.preventDefault();
            alert('New password must be different from current password.');
            return false;
        }

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="lni lni-spinner-arrow rotating"></i> Updating...';
    });

    // ==================== AUTO-HIDE SUCCESS MESSAGE ====================
    <?php if ($msgType === 'success'): ?>
    setTimeout(function() {
        document.querySelector('.alert-message').style.opacity = '0';
        setTimeout(function() {
            document.querySelector('.alert-message').style.display = 'none';
        }, 300);
    }, 5000);
    <?php endif; ?>
    </script>
</body>

</html>