<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';
require '../include/toast.php';

// ==================== CHECK AND CREATE EMAIL TEMPLATES TABLE IF NOT EXISTS ====================
$tableCheckQuery = "SHOW TABLES LIKE 'tbl_email_templates'";
$tableExists = mysqli_query($con, $tableCheckQuery);

if (mysqli_num_rows($tableExists) == 0) {
    // Create the email templates table
    $createTableQuery = "CREATE TABLE IF NOT EXISTS `tbl_email_templates` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `template_key` varchar(100) NOT NULL UNIQUE,
        `template_name` varchar(255) NOT NULL,
        `subject` varchar(255) NOT NULL,
        `body` text NOT NULL,
        `variables` text,
        `is_active` tinyint(1) DEFAULT 1,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `unique_template_key` (`template_key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if (mysqli_query($con, $createTableQuery)) {
        // Insert default email templates
        $defaultTemplates = [
            [
                'welcome_email',
                'Welcome Email',
                'Welcome to MUNext - {{site_name}}',
                '<h2>Welcome {{user_name}}!</h2><p>Thank you for joining {{site_name}}. We\'re excited to have you on board.</p><p>You can now browse and apply for jobs that match your skills and interests.</p><p>Best regards,<br>The {{site_name}} Team</p>',
                '{{user_name}}, {{site_name}}, {{user_email}}'
            ],
            [
                'job_application_received',
                'Job Application Received',
                'Application Received - {{job_title}}',
                '<h2>Application Received</h2><p>Dear {{applicant_name}},</p><p>Thank you for applying to the position of <strong>{{job_title}}</strong> at {{company_name}}.</p><p>Your application has been received and is currently under review. We will contact you if your profile matches our requirements.</p><p>Application ID: {{application_id}}</p><p>Best regards,<br>{{company_name}}</p>',
                '{{applicant_name}}, {{job_title}}, {{company_name}}, {{application_id}}'
            ],
            [
                'application_status_update',
                'Application Status Update',
                'Application Status Update - {{job_title}}',
                '<h2>Application Status Update</h2><p>Dear {{applicant_name}},</p><p>Your application for <strong>{{job_title}}</strong> at {{company_name}} has been updated.</p><p><strong>New Status:</strong> {{application_status}}</p><p>Application ID: {{application_id}}</p><p>Best regards,<br>{{company_name}}</p>',
                '{{applicant_name}}, {{job_title}}, {{company_name}}, {{application_status}}, {{application_id}}'
            ],
            [
                'new_application_alert',
                'New Application Alert (Employer)',
                'New Application - {{job_title}}',
                '<h2>New Application Received</h2><p>Dear {{company_name}},</p><p>A new application has been submitted for your job posting: <strong>{{job_title}}</strong></p><p><strong>Applicant:</strong> {{applicant_name}}<br><strong>Email:</strong> {{applicant_email}}<br><strong>Applied On:</strong> {{application_date}}</p><p>Login to your dashboard to review the application.</p><p>Best regards,<br>{{site_name}}</p>',
                '{{company_name}}, {{job_title}}, {{applicant_name}}, {{applicant_email}}, {{application_date}}, {{site_name}}'
            ],
            [
                'job_approved',
                'Job Posting Approved',
                'Your Job Posting Has Been Approved',
                '<h2>Job Posting Approved</h2><p>Dear {{company_name}},</p><p>Great news! Your job posting for <strong>{{job_title}}</strong> has been approved and is now live on our platform.</p><p>You can view your posting and manage applications from your dashboard.</p><p>Best regards,<br>{{site_name}}</p>',
                '{{company_name}}, {{job_title}}, {{site_name}}'
            ],
            [
                'company_approved',
                'Company Registration Approved',
                'Welcome to {{site_name}} - Company Approved',
                '<h2>Company Registration Approved</h2><p>Dear {{company_name}},</p><p>Congratulations! Your company registration has been approved.</p><p>You can now login to your dashboard and start posting jobs to find the best candidates.</p><p>Best regards,<br>{{site_name}}</p>',
                '{{company_name}}, {{site_name}}'
            ],
            [
                'password_reset',
                'Password Reset Request',
                'Password Reset Request - {{site_name}}',
                '<h2>Password Reset Request</h2><p>Dear {{user_name}},</p><p>We received a request to reset your password for your {{site_name}} account.</p><p>Click the link below to reset your password:<br><a href="{{reset_link}}">{{reset_link}}</a></p><p>This link will expire in 24 hours.</p><p>If you didn\'t request this, please ignore this email.</p><p>Best regards,<br>{{site_name}}</p>',
                '{{user_name}}, {{site_name}}, {{reset_link}}'
            ]
        ];
        
        foreach ($defaultTemplates as $template) {
            $insertQuery = "INSERT INTO tbl_email_templates (template_key, template_name, subject, body, variables) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($con, $insertQuery);
            mysqli_stmt_bind_param($stmt, "sssss", $template[0], $template[1], $template[2], $template[3], $template[4]);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        
        Toast::success('Email templates table created successfully with default templates!');
    }
}

// ==================== HANDLE TEMPLATE UPDATE ====================
if (isset($_POST['update_template'])) {
    $template_id = (int)$_POST['template_id'];
    $subject = mysqli_real_escape_string($con, $_POST['subject']);
    $body = mysqli_real_escape_string($con, $_POST['body']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $updateQuery = "UPDATE tbl_email_templates SET subject = ?, body = ?, is_active = ? WHERE id = ?";
    $stmt = mysqli_prepare($con, $updateQuery);
    mysqli_stmt_bind_param($stmt, "ssii", $subject, $body, $is_active, $template_id);
    
    if (mysqli_stmt_execute($stmt)) {
        // Log activity
        $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                    VALUES (?, 'EMAIL_TEMPLATE_UPDATED', 'Admin updated email template', ?, NOW())";
        $logStmt = mysqli_prepare($con, $logQuery);
        $ip = getClientIP();
        mysqli_stmt_bind_param($logStmt, "is", $session_id, $ip);
        mysqli_stmt_execute($logStmt);
        
        Toast::success('Email template updated successfully!');
        $_SESSION['success_msg'] = 'Email template updated successfully!';

    } else {
        Toast::error('Failed to update email template.');
        $_SESSION['error_msg'] = 'Failed to update email template.';
    }
    mysqli_stmt_close($stmt);
}

// ==================== HANDLE NOTIFICATION SETTINGS UPDATE ====================
if (isset($_POST['update_notifications'])) {
    $notify_new_application = isset($_POST['notify_new_application']) ? 1 : 0;
    $notify_application_status = isset($_POST['notify_application_status']) ? 1 : 0;
    $notify_job_approved = isset($_POST['notify_job_approved']) ? 1 : 0;
    $notify_company_approved = isset($_POST['notify_company_approved']) ? 1 : 0;
    $notify_new_user = isset($_POST['notify_new_user']) ? 1 : 0;
    
    // Check if tbl_settings exists, if not create it
    $settingsCheckQuery = "SHOW TABLES LIKE 'tbl_settings'";
    $settingsExists = mysqli_query($con, $settingsCheckQuery);
    
    if (mysqli_num_rows($settingsExists) == 0) {
        $createSettingsQuery = "CREATE TABLE IF NOT EXISTS `tbl_settings` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `setting_key` varchar(100) NOT NULL UNIQUE,
            `setting_value` text,
            `setting_description` varchar(255) DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `unique_setting_key` (`setting_key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        mysqli_query($con, $createSettingsQuery);
    }
    
    $updateQuery = "INSERT INTO tbl_settings (setting_key, setting_value) VALUES 
                    ('notify_new_application', ?),
                    ('notify_application_status', ?),
                    ('notify_job_approved', ?),
                    ('notify_company_approved', ?),
                    ('notify_new_user', ?)
                    ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
    
    $stmt = mysqli_prepare($con, $updateQuery);
    mysqli_stmt_bind_param($stmt, "iiiii", $notify_new_application, $notify_application_status, $notify_job_approved, $notify_company_approved, $notify_new_user);
    
    if (mysqli_stmt_execute($stmt)) {
        $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                    VALUES (?, 'NOTIFICATION_SETTINGS_UPDATED', 'Admin updated notification settings', ?, NOW())";
        $logStmt = mysqli_prepare($con, $logQuery);
        $ip = getClientIP();
        mysqli_stmt_bind_param($logStmt, "is", $session_id, $ip);
        mysqli_stmt_execute($logStmt);
        
        Toast::success('Notification settings updated successfully!');
        $_SESSION['success_msg'] = 'Notification settings updated successfully!';
                
    } else {
        Toast::error('Failed to update notification settings.');
        $_SESSION['error_msg'] = 'Failed to update notification settings.';
    }
    mysqli_stmt_close($stmt);
}

// ==================== FETCH EMAIL TEMPLATES ====================
$templatesQuery = "SELECT * FROM tbl_email_templates ORDER BY template_name ASC";
$templatesResult = mysqli_query($con, $templatesQuery);

// ==================== FETCH NOTIFICATION SETTINGS ====================
$settingsQuery = "SELECT setting_key, setting_value FROM tbl_settings WHERE setting_key LIKE 'notify_%'";
$settingsResult = mysqli_query($con, $settingsQuery);
$notificationSettings = [];
while ($row = mysqli_fetch_assoc($settingsResult)) {
    $notificationSettings[$row['setting_key']] = $row['setting_value'];
}

function getNotificationSetting($settings, $key, $default = '1') {
    return isset($settings[$key]) ? $settings[$key] : $default;
}
?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Email Settings - Admin Dashboard | MUNext</title>
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
                        <i class="lni lni-envelope mr-2"></i>Email Settings
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Settings</a></li>
                            <li class="breadcrumb-item active">Email</li>
                        </ol>
                    </nav>
                </div>

                <div class="container-fluid">
                    <!-- Info Box -->
                    <div class="info-box">
                        <div class="info-box-content">
                            <i class="lni lni-information info-box-icon"></i>
                            <div class="info-box-text">
                                <div class="info-box-title">Email Templates & Notifications</div>
                                <div class="info-box-desc">
                                    Customize email templates and configure notification settings. Use variables like
                                    {{user_name}} in templates for dynamic content.
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

                    <!-- Notification Settings -->
                    <div class="settings-section">
                        <div class="section-header">
                            <div>
                                <h5 class="section-title">
                                    <div class="section-icon">
                                        <i class="lni lni-alarm"></i>
                                    </div>
                                    Notification Settings
                                </h5>
                                <p class="section-description">Configure when to send automated email notifications</p>
                            </div>
                        </div>

                        <form method="POST" action="">
                            <div class="form-group">
                                <label class="form-label">Email Notifications</label>

                                <div class="switch-container">
                                    <label class="switch">
                                        <input type="checkbox" name="notify_new_application"
                                            <?php echo getNotificationSetting($notificationSettings, 'notify_new_application', '1') == '1' ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <div class="switch-label">
                                        <span class="switch-label-title">New Application Notifications</span>
                                        <span class="switch-label-desc">Notify employers when they receive new
                                            applications</span>
                                    </div>
                                </div>

                                <div class="switch-container">
                                    <label class="switch">
                                        <input type="checkbox" name="notify_application_status"
                                            <?php echo getNotificationSetting($notificationSettings, 'notify_application_status', '1') == '1' ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <div class="switch-label">
                                        <span class="switch-label-title">Application Status Updates</span>
                                        <span class="switch-label-desc">Notify applicants when their application status
                                            changes</span>
                                    </div>
                                </div>

                                <div class="switch-container">
                                    <label class="switch">
                                        <input type="checkbox" name="notify_job_approved"
                                            <?php echo getNotificationSetting($notificationSettings, 'notify_job_approved', '1') == '1' ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <div class="switch-label">
                                        <span class="switch-label-title">Job Approval Notifications</span>
                                        <span class="switch-label-desc">Notify employers when their job postings are
                                            approved</span>
                                    </div>
                                </div>

                                <div class="switch-container">
                                    <label class="switch">
                                        <input type="checkbox" name="notify_company_approved"
                                            <?php echo getNotificationSetting($notificationSettings, 'notify_company_approved', '1') == '1' ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <div class="switch-label">
                                        <span class="switch-label-title">Company Approval Notifications</span>
                                        <span class="switch-label-desc">Notify companies when their registration is
                                            approved</span>
                                    </div>
                                </div>

                                <div class="switch-container">
                                    <label class="switch">
                                        <input type="checkbox" name="notify_new_user"
                                            <?php echo getNotificationSetting($notificationSettings, 'notify_new_user', '1') == '1' ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <div class="switch-label">
                                        <span class="switch-label-title">Welcome Email</span>
                                        <span class="switch-label-desc">Send welcome email to new users upon
                                            registration</span>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <button type="submit" name="update_notifications" class="btn-save">
                                    <i class="lni lni-save"></i>
                                    Save Notification Settings
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Email Templates -->
                    <div class="settings-section">
                        <div class="section-header">
                            <div>
                                <h5 class="section-title">
                                    <div class="section-icon">
                                        <i class="lni lni-write"></i>
                                    </div>
                                    Email Templates
                                </h5>
                                <p class="section-description">Manage and customize automated email templates</p>
                            </div>
                        </div>

                        <?php while ($template = mysqli_fetch_assoc($templatesResult)): ?>
                        <div class="template-card">
                            <div class="template-header">
                                <div>
                                    <h6 class="template-name">
                                        <?php echo htmlspecialchars($template['template_name']); ?>
                                    </h6>
                                    <span
                                        class="template-key"><?php echo htmlspecialchars($template['template_key']); ?></span>
                                </div>
                                <div class="template-status">
                                    <span
                                        class="<?php echo $template['is_active'] ? 'status-badge-active' : 'status-badge-inactive'; ?>">
                                        <?php echo $template['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                    <button class="btn-edit-template" data-toggle="modal"
                                        data-target="#editTemplateModal<?php echo $template['id']; ?>">
                                        <i class="lni lni-pencil"></i> Edit
                                    </button>
                                </div>
                            </div>

                            <div class="template-info">
                                <p style="margin: 5px 0; color: var(--text-dark);"><strong>Subject:</strong>
                                    <?php echo htmlspecialchars($template['subject']); ?></p>
                            </div>

                            <?php if (!empty($template['variables'])): ?>
                            <div class="template-variables">
                                <div class="template-variables-title">Available Variables:</div>
                                <div class="template-variables-list">
                                    <?php echo htmlspecialchars($template['variables']); ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Edit Template Modal -->
                        <div class="modal fade" id="editTemplateModal<?php echo $template['id']; ?>" tabindex="-1"
                            role="dialog" aria-labelledby="editTemplateModalLabel<?php echo $template['id']; ?>">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"
                                            id="editTemplateModalLabel<?php echo $template['id']; ?>">
                                            <i class="lni lni-write mr-2"></i>Edit Email Template:
                                            <?php echo htmlspecialchars($template['template_name']); ?>
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form method="POST" action="">
                                        <div class="modal-body">
                                            <input type="hidden" name="template_id"
                                                value="<?php echo $template['id']; ?>">

                                            <div class="form-group">
                                                <label class="form-label">Template Key</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($template['template_key']); ?>"
                                                    readonly style="background-color: #e9ecef; cursor: not-allowed;">
                                                <small class="form-text text-muted">Unique template identifier (cannot
                                                    be changed)</small>
                                            </div>

                                            <div class="form-group">
                                                <label class="form-label form-label-required">Email Subject</label>
                                                <input type="text" name="subject" class="form-control"
                                                    value="<?php echo htmlspecialchars($template['subject']); ?>"
                                                    placeholder="Enter email subject" required>
                                                <small class="form-text text-muted">
                                                    <i class="lni lni-information mr-1"></i>You can use variables like
                                                    {{user_name}} in the subject
                                                </small>
                                            </div>

                                            <div class="form-group">
                                                <label class="form-label form-label-required">Email Body (HTML)</label>
                                                <textarea name="body" class="form-control" rows="15"
                                                    placeholder="Enter email body with HTML tags" required
                                                    style="font-family: 'Courier New', monospace; font-size: 0.85rem; line-height: 1.5;"><?php echo htmlspecialchars($template['body']); ?></textarea>
                                                <small class="form-text text-muted">
                                                    <i class="lni lni-code mr-1"></i><strong>HTML tags are
                                                        supported.</strong><br>
                                                    <strong>Available variables:</strong> <code
                                                        style="font-size: 0.85rem;"><?php echo htmlspecialchars($template['variables']); ?></code>
                                                </small>
                                            </div>

                                            <div class="form-group mb-0">
                                                <label class="form-label">Template Status</label>
                                                <div class="switch-container"
                                                    style="margin-bottom: 0; margin-top: 10px;">
                                                    <label class="switch">
                                                        <input type="checkbox" name="is_active"
                                                            <?php echo $template['is_active'] ? 'checked' : ''; ?>>
                                                        <span class="slider"></span>
                                                    </label>
                                                    <div class="switch-label">
                                                        <span class="switch-label-title">Enable Template</span>
                                                        <span class="switch-label-desc">Turn this template on or off for
                                                            automated emails</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                <i class="lni lni-close mr-1"></i>Cancel
                                            </button>
                                            <button type="submit" name="update_template" class="btn-save">
                                                <i class="lni lni-save mr-1"></i>Save Changes
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
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

    <script>
    $(document).ready(function() {


        // Ensure modals display correctly
        $('.modal').on('show.bs.modal', function() {
            $('body').addClass('modal-open');
        });

        $('.modal').on('hidden.bs.modal', function() {
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
        });
    });
    </script>

    <?php echo Toast::render(); ?>

    <style>
    /* ==================== MODAL STYLING ==================== */
    /* Fix for dashboard layout interference */
    .dashboard-wrap,
    .dashboard-content {
        position: relative;
        z-index: auto !important;
    }

    body.modal-open {
        overflow: hidden;
        padding-right: 0 !important;
    }

    body.modal-open .dashboard-wrap {
        padding-right: 0 !important;
    }

    .modal {
        position: fixed !important;
        top: 0;
        left: 0;
        z-index: 9999 !important;
        display: none;
        width: 100%;
        height: 100%;
        overflow: hidden;
        outline: 0;
    }

    .modal.show {
        display: block !important;
    }

    .modal.fade .modal-dialog {
        transition: transform 0.3s ease-out;
        transform: translate(0, -50px);
    }

    .modal.show .modal-dialog {
        transform: none;
    }

    .modal-dialog {
        position: relative;
        width: auto;
        margin: 1.75rem auto;
        pointer-events: none;
        max-width: 800px;
    }

    .modal-dialog.modal-lg {
        max-width: 900px;
    }

    .modal.fade {
        opacity: 0;
        transition: opacity 0.15s linear;
    }

    .modal.show {
        opacity: 1;
    }

    .modal-backdrop {
        position: fixed !important;
        top: 0;
        left: 0;
        z-index: 9998 !important;
        width: 100vw;
        height: 100vh;
        background-color: #000;
    }

    .modal-backdrop.fade {
        opacity: 0;
    }

    .modal-backdrop.show {
        opacity: 0.5;
    }

    .modal-content {
        position: relative;
        display: flex;
        flex-direction: column;
        width: 100%;
        pointer-events: auto;
        background-color: #fff;
        background-clip: padding-box;
        border: none;
        border-radius: 8px;
        outline: 0;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
    }

    .modal-header {
        background: linear-gradient(135deg,
                var(--primary-color),
                var(--primary-dark));
        color: var(--white);
        border-radius: 8px 8px 0 0;
        border-bottom: none;
        padding: 20px 25px;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
    }

    .modal-title {
        font-weight: 700;
        color: var(--white);
        margin-bottom: 0;
        line-height: 1.5;
    }

    .modal-body {
        position: relative;
        flex: 1 1 auto;
        padding: 25px;
        max-height: calc(100vh - 250px);
        overflow-y: auto;
    }

    .modal-footer {
        padding: 15px 25px;
        border-top: 1px solid var(--border-light);
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
    }

    .close {
        color: var(--white);
        opacity: 1;
        text-shadow: none;
        font-size: 1.8rem;
        font-weight: 300;
        line-height: 1;
        background: transparent;
        border: 0;
        padding: 0;
        cursor: pointer;
    }

    .close:hover {
        color: var(--white);
        opacity: 0.8;
    }

    .close:focus {
        outline: none;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 600;
    }

    .btn-secondary:hover {
        background: #5a6268;
        color: white;
    }
    </style>
</body>

</html>