<!DOCTYPE html>
<html lang="en">

<?php
require('./mysqli_connect.php');

include "session_check.php";

include "include/helper.php";
require_once "include/email-functions.php";

// include "include/toast.php";


$error = '';
$success = '';
$step = 'email'; // email, verify, reset

// Handle email submission
if (isset($_POST['send_reset_link'])) {
    $email = mysqli_real_escape_string($con, trim($_POST['email']));
    
    // Check if email exists in database
    $checkQuery = "SELECT USERID, USERNAME, FNAME, ONAME FROM tblusers WHERE EMAIL = ?";
    $stmt = mysqli_prepare($con, $checkQuery);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $userId = $user['USERID'];
        $username = $user['USERNAME'];
        $fullName = trim($user['FNAME'] . ' ' . $user['ONAME']);
        
        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+5 hour'));
        
        // Store token in database
        $insertToken = "INSERT INTO tblpasswordreset (USERID, TOKEN, EXPIRY, EMAIL) 
                       VALUES (?, ?, ?, ?)
                       ON DUPLICATE KEY UPDATE TOKEN = ?, EXPIRY = ?";
        $stmtToken = mysqli_prepare($con, $insertToken);
        mysqli_stmt_bind_param($stmtToken, "isssss", $userId, $token, $expiry, $email, $token, $expiry);
        
        if (mysqli_stmt_execute($stmtToken)) {
            // Generate reset link
            $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/forgot-password.php?token=" . $token;
            
            // Send password reset email using template from database
            $emailSent = sendPasswordResetEmail($con, $email, $fullName, $resetLink);
            
            if ($emailSent) {
                Toast::success("Password reset link has been sent to your email!");
                $success = "We've sent a password reset link to <strong>" . htmlspecialchars($email) . "</strong>. Please check your email (and spam folder).";
                $step = 'verify';
            } else {
                Toast::warning("Reset link generated but email failed to send. Please contact support.");
                error_log("Failed to send password reset email to: " . $email);
                
                // Still show success to user for security, but log the error
                $success = "If an account exists with <strong>" . htmlspecialchars($email) . "</strong>, you will receive a password reset link.";
            }
        } else {
            Toast::error("Failed to generate reset link. Please try again.");
            $error = "Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmtToken);
    } else {
        // Don't reveal if email exists or not (security best practice)
        Toast::success("If that email exists, you'll receive a reset link shortly.");
        $success = "If an account exists with <strong>" . htmlspecialchars($email) . "</strong>, you will receive a password reset link.";
    }
    mysqli_stmt_close($stmt);
}

// Handle password reset
if (isset($_POST['reset_password'])) {
    $token = mysqli_real_escape_string($con, $_POST['token']);
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if ($newPassword !== $confirmPassword) {
        Toast::error("Passwords do not match!");
        $error = "Passwords do not match. Please try again.";
    } elseif (strlen($newPassword) < 6) {
        Toast::error("Password must be at least 6 characters long!");
        $error = "Password must be at least 6 characters long.";
    } else {
        // Verify token
        $verifyQuery = "SELECT USERID, EMAIL FROM tblpasswordreset 
                       WHERE TOKEN = ? AND EXPIRY > NOW()";
        $stmtVerify = mysqli_prepare($con, $verifyQuery);
        mysqli_stmt_bind_param($stmtVerify, "s", $token);
        mysqli_stmt_execute($stmtVerify);
        $verifyResult = mysqli_stmt_get_result($stmtVerify);
        
        if (mysqli_num_rows($verifyResult) > 0) {
            $resetData = mysqli_fetch_assoc($verifyResult);
            $userId = $resetData['USERID'];
            
            // Hash new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // Update password
            $updateQuery = "UPDATE tblusers SET PASS = ? WHERE USERID = ?";
            $stmtUpdate = mysqli_prepare($con, $updateQuery);
            mysqli_stmt_bind_param($stmtUpdate, "si", $hashedPassword, $userId);
            
            if (mysqli_stmt_execute($stmtUpdate)) {
                // Delete used token
                $deleteToken = "DELETE FROM tblpasswordreset WHERE TOKEN = ?";
                $stmtDelete = mysqli_prepare($con, $deleteToken);
                mysqli_stmt_bind_param($stmtDelete, "s", $token);
                mysqli_stmt_execute($stmtDelete);
                mysqli_stmt_close($stmtDelete);
                
                Toast::success("Password reset successful! You can now login.");
                $_SESSION['password_reset_success'] = true;
                header("Location: login.php");
                exit();
            } else {
                Toast::error("Failed to reset password. Please try again.");
                $error = "Failed to reset password. Please try again.";
            }
            mysqli_stmt_close($stmtUpdate);
        } else {
            Toast::error("Invalid or expired reset link!");
            $error = "This password reset link is invalid or has expired. Please request a new one.";
        }
        mysqli_stmt_close($stmtVerify);
    }
}
?>



<head>
    <?php include 'include/head.php' ?>
</head>

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
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="login.php">Login</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Forgot Password</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Forgot Password Section -->
        <section class="middle">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-7 col-lg-8 col-md-10 col-sm-12">
                        <div class="reset-container">

                            <!-- Header -->
                            <div class="reset-header">
                                <i class="ti-lock"></i>
                                <h2>Forgot Password?</h2>
                                <p>No worries, we'll send you reset instructions</p>
                            </div>

                            <?php if (!empty($success)): ?>
                            <!-- Success Message -->
                            <div class="success-message">
                                <div class="d-flex align-items-center">
                                    <i class="ti-check-box"></i>
                                    <div>
                                        <h5 class="mb-2">Email Sent!</h5>
                                        <p class="mb-0"><?php echo $success; ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="info-box">
                                <i class="ti-email"></i>
                                <h5>Check Your Email</h5>
                                <p class="text-muted mb-3">We've sent a password reset link to your email address.
                                    Click the link in the email to reset your password.</p>
                                <p class="text-muted small mb-0"><strong>Didn't receive the email?</strong> Check your
                                    spam folder or try again.</p>
                            </div>
                            <?php elseif (!empty($error)): ?>
                            <!-- Error Message -->
                            <div class="error-message">
                                <div class="d-flex align-items-center">
                                    <i class="ti-close"></i>
                                    <div>
                                        <h5 class="mb-2">Error</h5>
                                        <p class="mb-0"><?php echo $error; ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Password Reset Form -->
                            <?php if (isset($_GET['token']) && !isset($_POST['reset_password'])): ?>
                            <!-- Step 3: Reset Password -->
                            <form class="border p-4 rounded" method="post">
                                <input type="hidden" name="token"
                                    value="<?php echo htmlspecialchars($_GET['token']); ?>">

                                <div class="form-group">
                                    <label>New Password *</label>
                                    <input required type="password" class="form-control"
                                        placeholder="Enter new password" name="new_password" minlength="6">
                                    <small class="form-text text-muted">Password must be at least 6 characters
                                        long</small>
                                </div>

                                <div class="form-group">
                                    <label>Confirm New Password *</label>
                                    <input required type="password" class="form-control"
                                        placeholder="Confirm new password" name="confirm_password" minlength="6">
                                </div>

                                <div class="form-group">
                                    <button type="submit" name="reset_password"
                                        class="btn btn-md full-width theme-bg text-light fs-md ft-medium">
                                        <i class="ti-check mr-2"></i>Reset Password
                                    </button>
                                </div>
                            </form>

                            <?php elseif (empty($success)): ?>
                            <!-- Step 1: Enter Email -->
                            <form class="border p-4 rounded" method="post">
                                <div class="form-group">
                                    <label>Email Address *</label>
                                    <input required type="email" class="form-control"
                                        placeholder="Enter your registered email" name="email"
                                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                    <small class="form-text text-muted">Enter the email address associated with your
                                        account</small>
                                </div>

                                <div class="form-group">
                                    <button type="submit" name="send_reset_link"
                                        class="btn btn-md full-width theme-bg text-light fs-md ft-medium">
                                        <i class="ti-email mr-2"></i>Send Reset Link
                                    </button>
                                </div>

                                <div class="text-center">
                                    <a href="login.php" class="back-link">
                                        <i class="ti-arrow-left"></i>
                                        Back to Login
                                    </a>
                                </div>
                            </form>
                            <?php else: ?>
                            <!-- Success State -->
                            <div class="text-center">
                                <a href="login.php" class="btn btn-md theme-bg text-light fs-md ft-medium">
                                    <i class="ti-arrow-left mr-2"></i>Back to Login
                                </a>
                            </div>
                            <?php endif; ?>

                            <!-- Additional Help -->
                            <?php if (empty($success)): ?>
                            <div class="info-box mt-4">
                                <h6 class="mb-3">Need Help?</h6>
                                <p class="text-muted small mb-2">If you're having trouble resetting your password,
                                    please contact our support team.</p>
                                <p class="mb-0">
                                    <a href="/cdn-cgi/l/email-protection#ff8c8a8f8f908d8bbf928a919a878bd19c9092"
                                        class="text-primary font-weight-bold">
                                        <i class="ti-email mr-1"></i><span class="__cf_email__"
                                            data-cfemail="96e5e3e6e6f9e4e2d6fbe3f8f3eee2b8f5f9fb">[email&#160;protected]</span>
                                    </a>
                                </p>
                            </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
        </section>

        <?php include 'include/footer.php'; ?>
    </div>

    <!-- Scripts -->
    <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
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

    // Password confirmation validation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        if (form && form.querySelector('input[name="confirm_password"]')) {
            form.addEventListener('submit', function(e) {
                const newPassword = document.querySelector('input[name="new_password"]').value;
                const confirmPassword = document.querySelector('input[name="confirm_password"]').value;

                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    showToast('Passwords do not match!', 'error');
                    return false;
                }

                if (newPassword.length < 6) {
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