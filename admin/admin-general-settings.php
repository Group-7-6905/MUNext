<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';
require '../include/toast.php';

// ==================== HANDLE SETTINGS UPDATE ====================
if (isset($_POST['update_settings'])) {
    $settings_type = $_POST['settings_type'];
    
    switch ($settings_type) {
        case 'site_info':
            $site_name = mysqli_real_escape_string($con, $_POST['site_name']);
            $site_tagline = mysqli_real_escape_string($con, $_POST['site_tagline']);
            $site_email = mysqli_real_escape_string($con, $_POST['site_email']);
            $site_phone = mysqli_real_escape_string($con, $_POST['site_phone']);
            $site_address = mysqli_real_escape_string($con, $_POST['site_address']);
            
            // Update or insert settings
            $updateQuery = "INSERT INTO tbl_settings (setting_key, setting_value) VALUES 
                            ('site_name', ?),
                            ('site_tagline', ?),
                            ('site_email', ?),
                            ('site_phone', ?),
                            ('site_address', ?)
                            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
            
            $stmt = mysqli_prepare($con, $updateQuery);
            mysqli_stmt_bind_param($stmt, "sssss", $site_name, $site_tagline, $site_email, $site_phone, $site_address);
            
            if (mysqli_stmt_execute($stmt)) {
                // Log activity
                $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                            VALUES (?, 'SETTINGS_UPDATED', 'Admin updated site information settings', ?, NOW())";
                $logStmt = mysqli_prepare($con, $logQuery);
                $ip = getClientIP();
                mysqli_stmt_bind_param($logStmt, "is", $session_id, $ip);
                mysqli_stmt_execute($logStmt);
                
                Toast::success('Site information updated successfully!');
                $_SESSION['success_msg'] = 'Site information updated successfully!';
            } else {
                Toast::error('Failed to update site information.');
                $_SESSION['error_msg'] = 'Failed to update site information.';
            }
            mysqli_stmt_close($stmt);
            break;
            
        case 'email_config':
            $smtp_host = mysqli_real_escape_string($con, $_POST['smtp_host']);
            $smtp_port = mysqli_real_escape_string($con, $_POST['smtp_port']);
            $smtp_username = mysqli_real_escape_string($con, $_POST['smtp_username']);
            $smtp_password = mysqli_real_escape_string($con, $_POST['smtp_password']);
            $smtp_from_email = mysqli_real_escape_string($con, $_POST['smtp_from_email']);
            $smtp_from_name = mysqli_real_escape_string($con, $_POST['smtp_from_name']);
            
            $updateQuery = "INSERT INTO tbl_settings (setting_key, setting_value) VALUES 
                            ('smtp_host', ?),
                            ('smtp_port', ?),
                            ('smtp_username', ?),
                            ('smtp_password', ?),
                            ('smtp_from_email', ?),
                            ('smtp_from_name', ?)
                            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
            
            $stmt = mysqli_prepare($con, $updateQuery);
            mysqli_stmt_bind_param($stmt, "ssssss", $smtp_host, $smtp_port, $smtp_username, $smtp_password, $smtp_from_email, $smtp_from_name);
            
            if (mysqli_stmt_execute($stmt)) {
                $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                            VALUES (?, 'SETTINGS_UPDATED', 'Admin updated email configuration', ?, NOW())";
                $logStmt = mysqli_prepare($con, $logQuery);
                $ip = getClientIP();
                mysqli_stmt_bind_param($logStmt, "is", $session_id, $ip);
                mysqli_stmt_execute($logStmt);
                
                Toast::success('Email configuration updated successfully!');
                $_SESSION['success_msg'] = 'Email configuration updated successfully!';
            } else {
                Toast::error('Failed to update email configuration.');
                $_SESSION['error_msg'] = 'Failed to update email configuration.';
            }
            mysqli_stmt_close($stmt);
            break;
            
        case 'system_config':
            $items_per_page = (int)$_POST['items_per_page'];
            $maintenance_mode = isset($_POST['maintenance_mode']) ? 1 : 0;
            $user_registration = isset($_POST['user_registration']) ? 1 : 0;
            $company_registration = isset($_POST['company_registration']) ? 1 : 0;
            $auto_approve_jobs = isset($_POST['auto_approve_jobs']) ? 1 : 0;
            $auto_approve_companies = isset($_POST['auto_approve_companies']) ? 1 : 0;
            
            $updateQuery = "INSERT INTO tbl_settings (setting_key, setting_value) VALUES 
                            ('items_per_page', ?),
                            ('maintenance_mode', ?),
                            ('user_registration', ?),
                            ('company_registration', ?),
                            ('auto_approve_jobs', ?),
                            ('auto_approve_companies', ?)
                            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
            
            $stmt = mysqli_prepare($con, $updateQuery);
            mysqli_stmt_bind_param($stmt, "iiiiii", $items_per_page, $maintenance_mode, $user_registration, $company_registration, $auto_approve_jobs, $auto_approve_companies);
            
            if (mysqli_stmt_execute($stmt)) {
                $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                            VALUES (?, 'SETTINGS_UPDATED', 'Admin updated system configuration', ?, NOW())";
                $logStmt = mysqli_prepare($con, $logQuery);
                $ip = getClientIP();
                mysqli_stmt_bind_param($logStmt, "is", $session_id, $ip);
                mysqli_stmt_execute($logStmt);
                
                Toast::success('System configuration updated successfully!');
                $_SESSION['success_msg'] = 'System configuration updated successfully!';
            } else {
                Toast::error('Failed to update system configuration.');
                $_SESSION['error_msg'] = 'Failed to update system configuration.';
            }
            mysqli_stmt_close($stmt);
            break;
    }
}

// ==================== FETCH CURRENT SETTINGS ====================
$settingsQuery = "SELECT setting_key, setting_value FROM tbl_settings";
$settingsResult = mysqli_query($con, $settingsQuery);
$settings = [];
while ($row = mysqli_fetch_assoc($settingsResult)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Helper function to get setting value with default
function getSetting($settings, $key, $default = '') {
    return isset($settings[$key]) ? $settings[$key] : $default;
}
?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>General Settings - Admin Dashboard | MUNext</title>
    <link href="assets/css/styles.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/custom-dashboard.css">
</head>

<body>
    <div id="main-wrapper">
        <?php include 'header.php' ?>
        <div class="clearfix"></div>

        <div class="dashboard-wrap bg-light">
            <a class="mobNavigation" data-toggle="collapse" href="#MobNav" role="button">
                <i class="fas fa-bars mr-2"></i>Dashboard Navigation
            </a>

            <?php include 'sidenav.php' ?>

            <div class="dashboard-content">
                <!-- Page Header -->
                <div class="page-header-section">
                    <h1 class="page-title">
                        <i class="lni lni-cog mr-2"></i>General Settings
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Settings</a></li>
                            <li class="breadcrumb-item active">General</li>
                        </ol>
                    </nav>
                </div>

                <div class="container-fluid">
                    <!-- Info Box -->
                    <div class="info-box">
                        <div class="info-box-content">
                            <i class="lni lni-information info-box-icon"></i>
                            <div class="info-box-text">
                                <div class="info-box-title">System Configuration</div>
                                <div class="info-box-desc">
                                    Configure your platform settings, email notifications, and system preferences.
                                    Changes take effect immediately after saving.
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Success/Error Messages -->
                    <?php if (isset($_SESSION['success_msg'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="lni lni-checkmark-circle"></i> <?php echo $_SESSION['success_msg']; ?>
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                    <?php unset($_SESSION['success_msg']); endif; ?>

                    <?php if (isset($_SESSION['error_msg'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="lni lni-cross-circle"></i> <?php echo $_SESSION['error_msg']; ?>
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                    <?php unset($_SESSION['error_msg']); endif; ?>

                    <!-- Site Information Settings -->
                    <div class="settings-section">
                        <div class="section-header">
                            <div>
                                <h5 class="section-title">
                                    <div class="section-icon">
                                        <i class="lni lni-world"></i>
                                    </div>
                                    Site Information
                                </h5>
                                <p class="section-description">Basic information about your job portal platform</p>
                            </div>
                        </div>

                        <form method="POST" action="">
                            <input type="hidden" name="settings_type" value="site_info">

                            <div class="form-group">
                                <label class="form-label form-label-required">Site Name</label>
                                <input type="text" name="site_name" class="form-control"
                                    value="<?php echo htmlspecialchars(getSetting($settings, 'site_name', 'MUNext')); ?>"
                                    required>
                                <small class="form-text">The name of your job portal (e.g., MUNext)</small>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Site Tagline</label>
                                <input type="text" name="site_tagline" class="form-control"
                                    value="<?php echo htmlspecialchars(getSetting($settings, 'site_tagline', 'Memorial University Job Portal')); ?>">
                                <small class="form-text">A brief description or slogan for your site</small>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6 col-6">
                                    <label class="form-label form-label-required">Contact Email</label>
                                    <input type="email" name="site_email" class="form-control"
                                        value="<?php echo htmlspecialchars(getSetting($settings, 'site_email', 'info@munext.com')); ?>"
                                        required>
                                    <small class="form-text">Primary contact email for the platform</small>
                                </div>

                                <div class="form-group col-md-6 col-6">
                                    <label class="form-label">Contact Phone</label>
                                    <input type="text" name="site_phone" class="form-control"
                                        value="<?php echo htmlspecialchars(getSetting($settings, 'site_phone', '')); ?>">
                                    <small class="form-text">Contact phone number (optional)</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Site Address</label>
                                <textarea name="site_address"
                                    class="form-control"><?php echo htmlspecialchars(getSetting($settings, 'site_address', '')); ?></textarea>
                                <small class="form-text">Physical address of your organization</small>
                            </div>

                            <div class="text-right">
                                <button type="submit" name="update_settings" class="btn-save">
                                    <i class="lni lni-save"></i>
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Email Configuration Settings -->
                    <div class="settings-section">
                        <div class="section-header">
                            <div>
                                <h5 class="section-title">
                                    <div class="section-icon">
                                        <i class="lni lni-envelope"></i>
                                    </div>
                                    Email Configuration
                                </h5>
                                <p class="section-description">SMTP settings for sending emails from the platform</p>
                            </div>
                        </div>

                        <div class="warning-box">
                            <div class="warning-box-content">
                                <i class="lni lni-warning warning-box-icon"></i>
                                <div class="warning-box-text">
                                    <strong>Important:</strong> Configure these settings carefully. Incorrect SMTP
                                    settings may prevent email notifications from being sent.
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="">
                            <input type="hidden" name="settings_type" value="email_config">

                            <div class="row">
                                <div class="form-group col-md-6 col-6">
                                    <label class="form-label form-label-required">SMTP Host</label>
                                    <input type="text" name="smtp_host" class="form-control"
                                        value="<?php echo htmlspecialchars(getSetting($settings, 'smtp_host', '')); ?>"
                                        required>
                                    <small class="form-text">e.g., smtp.gmail.com</small>
                                </div>

                                <div class="form-group col-md-6 col-6">
                                    <label class="form-label form-label-required">SMTP Port</label>
                                    <input type="number" name="smtp_port" class="form-control"
                                        value="<?php echo htmlspecialchars(getSetting($settings, 'smtp_port', '587')); ?>"
                                        required>
                                    <small class="form-text">Usually 587 or 465</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6 col-6">
                                    <label class="form-label form-label-required">SMTP Username</label>
                                    <input type="text" name="smtp_username" class="form-control"
                                        value="<?php echo htmlspecialchars(getSetting($settings, 'smtp_username', '')); ?>"
                                        required>
                                    <small class="form-text">Your SMTP account username</small>
                                </div>

                                <div class="form-group col-md-6 col-6">
                                    <label class="form-label form-label-required">SMTP Password</label>
                                    <input type="password" name="smtp_password" class="form-control"
                                        value="<?php echo htmlspecialchars(getSetting($settings, 'smtp_password', '')); ?>"
                                        required>
                                    <small class="form-text">Your SMTP account password</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6 col-6">
                                    <label class="form-label form-label-required">From Email</label>
                                    <input type="email" name="smtp_from_email" class="form-control"
                                        value="<?php echo htmlspecialchars(getSetting($settings, 'smtp_from_email', '')); ?>"
                                        required>
                                    <small class="form-text">Email address that appears as sender</small>
                                </div>

                                <div class="form-group col-md-6 col-6">
                                    <label class="form-label form-label-required">From Name</label>
                                    <input type="text" name="smtp_from_name" class="form-control"
                                        value="<?php echo htmlspecialchars(getSetting($settings, 'smtp_from_name', 'MUNext')); ?>"
                                        required>
                                    <small class="form-text">Name that appears as sender</small>
                                </div>
                            </div>

                            <div class="text-right">
                                <button type="submit" name="update_settings" class="btn-save">
                                    <i class="lni lni-save"></i>
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- System Configuration Settings -->
                    <div class="settings-section">
                        <div class="section-header">
                            <div>
                                <h5 class="section-title">
                                    <div class="section-icon">
                                        <i class="lni lni-cog"></i>
                                    </div>
                                    System Configuration
                                </h5>
                                <p class="section-description">Platform behavior and feature toggles</p>
                            </div>
                        </div>

                        <form method="POST" action="">
                            <input type="hidden" name="settings_type" value="system_config">

                            <!-- <div class="form-group">
                                <label class="form-label">Items Per Page</label>
                                <input type="number" name="items_per_page" class="form-control" min="5" max="100"
                                    value="<?php echo htmlspecialchars(getSetting($settings, 'items_per_page', '20')); ?>">
                                <small class="form-text">Number of items to display per page in listings</small>
                            </div> -->

                            <input type="hidden" name="items_per_page" class="form-control" min="5" max="100"
                                value="<?php echo htmlspecialchars(getSetting($settings, 'items_per_page', '20')); ?>">

                            <div class="form-group">
                                <label class="form-label">Platform Features</label>

                                <div class="switch-container">
                                    <label class="switch">
                                        <input type="checkbox" name="maintenance_mode"
                                            <?php echo getSetting($settings, 'maintenance_mode', '0') == '1' ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <div class="switch-label">
                                        <span class="switch-label-title">Maintenance Mode</span>
                                        <span class="switch-label-desc">Temporarily disable access to the platform for
                                            maintenance</span>
                                    </div>
                                </div>

                                <div class="switch-container">
                                    <label class="switch">
                                        <input type="checkbox" name="user_registration"
                                            <?php echo getSetting($settings, 'user_registration', '1') == '1' ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <div class="switch-label">
                                        <span class="switch-label-title">User Registration</span>
                                        <span class="switch-label-desc">Allow new users to register as job
                                            seekers</span>
                                    </div>
                                </div>

                                <div class="switch-container">
                                    <label class="switch">
                                        <input type="checkbox" name="company_registration"
                                            <?php echo getSetting($settings, 'company_registration', '1') == '1' ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <div class="switch-label">
                                        <span class="switch-label-title">Company Registration</span>
                                        <span class="switch-label-desc">Allow new companies to register and post
                                            jobs</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Auto-Approval Settings</label>

                                <div class="switch-container">
                                    <label class="switch">
                                        <input type="checkbox" name="auto_approve_jobs"
                                            <?php echo getSetting($settings, 'auto_approve_jobs', '0') == '1' ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <div class="switch-label">
                                        <span class="switch-label-title">Auto-Approve Jobs</span>
                                        <span class="switch-label-desc">Automatically approve new job postings without
                                            admin review</span>
                                    </div>
                                </div>

                                <div class="switch-container">
                                    <label class="switch">
                                        <input type="checkbox" name="auto_approve_companies"
                                            <?php echo getSetting($settings, 'auto_approve_companies', '0') == '1' ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <div class="switch-label">
                                        <span class="switch-label-title">Auto-Approve Companies</span>
                                        <span class="switch-label-desc">Automatically approve new company registrations
                                            without admin review</span>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <button type="submit" name="update_settings" class="btn-save">
                                    <i class="lni lni-save"></i>
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <?php include 'footer.php' ?>
            </div>
        </div>

        <a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>
    </div>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/custom.js"></script>

    <?php echo Toast::render(); ?>
</body>

</html>