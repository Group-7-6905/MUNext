<?php
/**
 * ==================== EMAIL FUNCTIONS ====================
 * Functions for sending emails using database templates and settings
 * 
 * Uses:
 * - tbl_email_templates for email content
 * - tbl_settings for SMTP/email configuration
 * 
 * Author: MUNext Development Team
 * Date: 2025
 */

// ==================== GLOBAL EMAIL CONFIGURATION ====================

/**
 * Get email settings from database
 * Loads SMTP configuration from tbl_settings
 * 
 * @param mysqli $con Database connection
 * @return array Email settings
 */
function getEmailSettings($con) {
    static $emailSettings = null;
    
    // Cache settings to avoid multiple database queries
    if ($emailSettings !== null) {
        return $emailSettings;
    }
    
    $emailSettings = array(
        'smtp_host' => '',
        'smtp_port' => 587,
        'smtp_username' => '',
        'smtp_password' => '',
        'smtp_encryption' => 'tls',
        'from_email' => 'noreply@munext.ca',
        'from_name' => 'MUNext',
        'site_name' => 'MUNext',
        'site_url' => '',
        'use_smtp' => false
    );
    
    // Get email settings from database
    $query = "SELECT setting_key, setting_value FROM tbl_settings 
              WHERE setting_key IN ('smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 
                                   'smtp_encryption', 'smtp_from_email', 'smtp_from_name', 
                                   'site_name', 'site_url', 'use_smtp')";
    
    $result = mysqli_query($con, $query);
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $key = $row['setting_key'];
            $value = $row['setting_value'];
            
            // Map database keys to array keys
            switch ($key) {
                case 'smtp_from_email':
                    $emailSettings['from_email'] = $value;
                    break;
                case 'smtp_from_name':
                    $emailSettings['from_name'] = $value;
                    break;
                case 'use_smtp':
                    $emailSettings['use_smtp'] = ($value === '1' || $value === 'true');
                    break;
                case 'smtp_port':
                    $emailSettings['smtp_port'] = (int)$value;
                    break;
                default:
                    if (isset($emailSettings[$key])) {
                        $emailSettings[$key] = $value;
                    }
            }
        }
    }
    
    // Set site URL if not in database
    if (empty($emailSettings['site_url'])) {
        $emailSettings['site_url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") 
                                    . "://" . $_SERVER['HTTP_HOST'];
    }
    
    return $emailSettings;
}

/**
 * Get global email headers
 * 
 * @param mysqli $con Database connection
 * @return string Email headers
 */
function getEmailHeaders($con) {
    $settings = getEmailSettings($con);
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: {$settings['from_name']} <{$settings['from_email']}>" . "\r\n";
    $headers .= "Reply-To: {$settings['from_email']}" . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    return $headers;
}

// ==================== EMAIL TEMPLATE FUNCTIONS ====================

/**
 * Get email template from database
 * 
 * @param mysqli $con Database connection
 * @param string $templateName Template name/slug
 * @return array|null Template data or null if not found
 */
function getEmailTemplate($con, $templateName) {
    // $query = "SELECT * FROM tbl_email_templates WHERE template_name = ? AND is_active = 1 LIMIT 1";
    $query = "SELECT * FROM tbl_email_templates WHERE template_key = ? AND is_active = 1 LIMIT 1";
    $stmt = mysqli_prepare($con, $query);
    
    if (!$stmt) {
        error_log("Failed to prepare statement: " . mysqli_error($con));
        return null;
    }
    
    mysqli_stmt_bind_param($stmt, "s", $templateName);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $template = null;
    if ($row = mysqli_fetch_assoc($result)) {
        $template = $row;
    }
    
    mysqli_stmt_close($stmt);
    return $template;
}

/**
 * Replace placeholders in email template
 * 
 * @param string $content Template content with placeholders
 * @param array $variables Associative array of placeholder => value
 * @return string Content with replaced placeholders
 */
function replacePlaceholders($content, $variables) {
    foreach ($variables as $key => $value) {
        // Replace {{placeholder}} or {placeholder} format
        $content = str_replace('{{' . $key . '}}', $value, $content);
        $content = str_replace('{' . $key . '}', $value, $content);
    }
    return $content;
}

/**
 * Wrap content in email layout
 * 
 * @param mysqli $con Database connection
 * @param string $content Email content
 * @param string $preheader Optional preheader text
 * @return string Complete HTML email
 */
function wrapEmailLayout($con, $content, $preheader = '') {
    $settings = getEmailSettings($con);
    $siteName = htmlspecialchars($settings['site_name']);
    $siteUrl = $settings['site_url'];
    $currentYear = date('Y');
    
    return "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>{$siteName}</title>
        <style>
            body { 
                font-family: Arial, sans-serif; 
                line-height: 1.6; 
                color: #333; 
                margin: 0; 
                padding: 0; 
                background-color: #f4f4f4;
            }
            .email-container { 
                max-width: 600px; 
                margin: 20px auto; 
                background: #ffffff; 
                border-radius: 10px; 
                overflow: hidden;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            .header { 
                background: linear-gradient(135deg, #862633 0%, #6b1e28 100%); 
                color: white; 
                padding: 30px; 
                text-align: center; 
            }
            .header h1 {
                margin: 0;
                font-size: 28px;
                font-weight: 700;
            }
            .preheader {
                display: none;
                max-height: 0;
                overflow: hidden;
            }
            .content { 
                padding: 30px; 
                background: #ffffff;
            }
            .content h2 {
                color: #862633;
                font-size: 24px;
                margin-top: 0;
            }
            .content p {
                margin: 15px 0;
            }
            .button { 
                display: inline-block; 
                padding: 15px 30px; 
                background: linear-gradient(135deg, #862633, #6b1e28);
                color: white !important; 
                text-decoration: none; 
                border-radius: 25px; 
                font-weight: 600; 
                margin: 20px 0;
                text-align: center;
            }
            .button:hover {
                background: linear-gradient(135deg, #6b1e28, #862633);
            }
            .footer { 
                background: #2c3e50; 
                color: white; 
                padding: 20px; 
                text-align: center; 
                font-size: 12px; 
            }
            .footer a {
                color: #ffffff;
                text-decoration: underline;
            }
            .footer p {
                margin: 5px 0;
            }
            @media only screen and (max-width: 600px) {
                .email-container {
                    margin: 10px;
                }
                .content {
                    padding: 20px;
                }
            }
        </style>
    </head>
    <body>
        <span class='preheader'>{$preheader}</span>
        <div class='email-container'>
            <div class='header'>
                <h1>{$siteName}</h1>
            </div>
            <div class='content'>
                {$content}
            </div>
            <div class='footer'>
                <p>&copy; {$currentYear} {$siteName} - Memorial University Job Board</p>
                <p>St. John's, Newfoundland and Labrador, Canada</p>
                <p><a href='{$siteUrl}'>Visit our website</a></p>
            </div>
        </div>
    </body>
    </html>
    ";
}

// ==================== MAIN SEND EMAIL FUNCTION ====================

/**
 * Send email using database template
 * 
 * @param mysqli $con Database connection
 * @param string $to Recipient email address
 * @param string $templateName Template name from tbl_email_templates
 * @param array $variables Variables to replace in template
 * @param array $options Optional settings (cc, bcc, attachments)
 * @return bool Success status
 */
function sendTemplateEmail($con, $to, $templateName, $variables = array(), $options = array()) {
    // Validate email address
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        error_log("Invalid email address: {$to}");
        return false;
    }
    
    // Get email template
    $template = getEmailTemplate($con, $templateName);
    
    if (!$template) {
        error_log("Email template not found: {$templateName}");
        return false;
    }
    
    // Get email settings
    $settings = getEmailSettings($con);
    
    // Add default variables
    $defaultVariables = array(
        'site_name' => $settings['site_name'],
        'site_url' => $settings['site_url'],
        'current_year' => date('Y'),
        'recipient_email' => $to
    );
    
    $variables = array_merge($defaultVariables, $variables);
    
    // Replace placeholders in subject and body
    $subject = replacePlaceholders($template['subject'], $variables);
    $body = replacePlaceholders($template['body'], $variables);
    
    // Wrap in email layout
    $preheader = isset($template['preheader']) ? replacePlaceholders($template['preheader'], $variables) : '';
    $htmlBody = wrapEmailLayout($con, $body, $preheader);
    
    // Get headers
    $headers = getEmailHeaders($con);
    
    // Add CC if provided
    if (isset($options['cc']) && !empty($options['cc'])) {
        $headers .= "\r\nCc: " . $options['cc'];
    }
    
    // Add BCC if provided
    if (isset($options['bcc']) && !empty($options['bcc'])) {
        $headers .= "\r\nBcc: " . $options['bcc'];
    }
    
    // Send email
    $result = mail($to, $subject, $htmlBody, $headers);
    
    // Log email
    if ($result) {
        logEmail($con, $to, $templateName, $subject, 'sent');
    } else {
        logEmail($con, $to, $templateName, $subject, 'failed');
        error_log("Failed to send email to {$to} using template {$templateName}");
    }
    
    return $result;
}

/**
 * Log email sending attempt
 * 
 * @param mysqli $con Database connection
 * @param string $to Recipient email
 * @param string $templateName Template used
 * @param string $subject Email subject
 * @param string $status Status (sent/failed)
 */
function logEmail($con, $to, $templateName, $subject, $status) {
    // Check if tbl_email_log table exists
    $tableCheck = mysqli_query($con, "SHOW TABLES LIKE 'tbl_email_log'");
    
    if (mysqli_num_rows($tableCheck) > 0) {
        $query = "INSERT INTO tbl_email_log (recipient, template_name, subject, status, sent_at) 
                  VALUES (?, ?, ?, ?, NOW())";
        
        $stmt = mysqli_prepare($con, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssss", $to, $templateName, $subject, $status);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    } else {
        // Log to file if table doesn't exist
        error_log("Email {$status}: To: {$to}, Template: {$templateName}, Subject: {$subject}");
    }
}

// ==================== SPECIFIC EMAIL FUNCTIONS ====================

/**
 * Send password reset email
 * 
 * @param mysqli $con Database connection
 * @param string $email Recipient email
 * @param string $name Recipient name
 * @param string $resetLink Password reset link
 * @return bool Success status
 */
function sendPasswordResetEmail($con, $email, $name, $resetLink) {
    $variables = array(
        'user_name' => $name,
        'reset_link' => $resetLink,
        'expiry_time' => '1 hour'
    );
    
    return sendTemplateEmail($con, $email, 'password_reset', $variables);
}

/**
 * Send welcome email to new user
 * 
 * @param mysqli $con Database connection
 * @param string $email Recipient email
 * @param string $name Recipient name
 * @param string $userType User type (Applicant/Employer)
 * @return bool Success status
 */
// function sendWelcomeEmail($con, $email, $name, $userType) {
//     $variables = array(
//         'user_name' => $name,
//         'user_type' => $userType,
//         'login_url' => getEmailSettings($con)['site_url'] . '/login.php'
//     );
    
//     $templateName = ($userType === 'Applicant') ? 'welcome_applicant' : 'welcome_employer';
    
//     return sendTemplateEmail($con, $email, $templateName, $variables);
// }

/**
 * Send job application confirmation to Applicant
 * 
 * @param mysqli $con Database connection
 * @param string $email Applicant email
 * @param string $applicantName Applicant name
 * @param string $jobTitle Job title
 * @param string $companyName Company name
 * @return bool Success status
 */
// function sendApplicationConfirmationEmail($con, $email, $applicantName, $jobTitle, $companyName) {
//     $variables = array(
//         'applicant_name' => $applicantName,
//         'job_title' => $jobTitle,
//         'company_name' => $companyName,
//         'application_date' => date('F j, Y'),
//         'dashboard_url' => getEmailSettings($con)['site_url'] . '/dashboard/applicant/dashboard-applied-jobs.php'
//     );
    
//     return sendTemplateEmail($con, $email, 'application_confirmation', $variables);
// }

/**
 * Send application status update
 * 
 * @param mysqli $con Database connection
 * @param string $email Applicant email
 * @param string $applicantName Applicant name
 * @param string $jobTitle Job title
 * @param string $status New status (Shortlisted/Rejected)
 * @return bool Success status
 */
// function sendApplicationStatusEmail($con, $email, $applicantName, $jobTitle, $status) {
//     $variables = array(
//         'applicant_name' => $applicantName,
//         'job_title' => $jobTitle,
//         'status' => $status,
//         'status_date' => date('F j, Y'),
//         'dashboard_url' => getEmailSettings($con)['site_url'] . '/dashboard-applied-jobs.php'
//     );
    
//     $templateName = ($status === 'Shortlisted') ? 'application_shortlisted' : 'application_rejected';
    
//     return sendTemplateEmail($con, $email, $templateName, $variables);
// }

/**
 * Send newsletter subscription confirmation
 * 
 * @param mysqli $con Database connection
 * @param string $email Subscriber email
 * @param string $token Confirmation token
 * @return bool Success status
 */
function sendSubscriptionEmail($con, $email, $token) {
    $siteUrl = getEmailSettings($con)['site_url'];
    $confirmLink = $siteUrl . "/newsletter-confirm.php?token=" . $token;
    
    $variables = array(
        'confirm_link' => $confirmLink,
        'token' => $token
    );
    
    return sendTemplateEmail($con, $email, 'newsletter_subscription', $variables);
}

/**
 * Send newsletter unsubscription confirmation
 * 
 * @param mysqli $con Database connection
 * @param string $email Subscriber email
 * @return bool Success status
 */
function sendUnsubscriptionEmail($con, $email) {
    $variables = array(
        'unsubscribe_date' => date('F j, Y')
    );
    
    return sendTemplateEmail($con, $email, 'newsletter_unsubscribe', $variables);
}

/**
 * Send new job notification to employer
 * 
 * @param mysqli $con Database connection
 * @param string $email Employer email
 * @param string $employerName Employer name
 * @param string $jobTitle Job title
 * @param string $jobId Job ID
 * @return bool Success status
 */
// function sendJobPostedEmail($con, $email, $employerName, $jobTitle, $jobId) {
//     $siteUrl = getEmailSettings($con)['site_url'];
    
//     $variables = array(
//         'employer_name' => $employerName,
//         'job_title' => $jobTitle,
//         'job_url' => $siteUrl . '/job-detail.php?jobid=' . $jobId,
//         'manage_url' => $siteUrl . '/dashboard/employer/dashboard-manage-jobs.php',
//         'post_date' => date('F j, Y')
//     );
    
//     return sendTemplateEmail($con, $email, 'job_posted', $variables);
// }

/**
 * Send new application notification to employer
 * 
 * @param mysqli $con Database connection
 * @param string $email Employer email
 * @param string $employerName Employer name
 * @param string $applicantName Applicant name
 * @param string $jobTitle Job title
 * @param string $applicationId Application ID
 * @return bool Success status
 */
function sendNewApplicationEmail($con, $email, $employerName, $applicantName, $jobTitle, $applicationId) {
    $siteUrl = getEmailSettings($con)['site_url'];
    
    $variables = array(
        'employer_name' => $employerName,
        'applicant_name' => $applicantName,
        'job_title' => $jobTitle,
        'application_url' => $siteUrl . '/dashboard-manage-applications.php?applicationid=' . $applicationId,
        'application_date' => date('F j, Y')
    );
    
    return sendTemplateEmail($con, $email, 'new_application', $variables);
}

/**
 * Send company account approval notification
 * 
 * @param mysqli $con Database connection
 * @param string $email Employer email
 * @param string $companyName Company name
 * @param string $employerName Employer name
 * @return bool Success status
 */
// function sendCompanyApprovedEmail($con, $email, $companyName, $employerName) {
//     $siteUrl = getEmailSettings($con)['site_url'];
    
//     $variables = array(
//         'employer_name' => $employerName,
//         'company_name' => $companyName,
//         'approval_date' => date('F j, Y'),
//         'login_url' => $siteUrl . '/employer-login.php',
//         'dashboard_url' => $siteUrl . '/employer-dashboard.php'
//     );
    
//     return sendTemplateEmail($con, $email, 'company_approved', $variables);
// }

/**
 * Send company account rejection notification
 * 
 * @param mysqli $con Database connection
 * @param string $email Employer email
 * @param string $companyName Company name
 * @param string $employerName Employer name
 * @param string $reason Rejection reason
 * @return bool Success status
 */
function sendCompanyRejectedEmail($con, $email, $companyName, $employerName, $reason = '') {
    $variables = array(
        'employer_name' => $employerName,
        'company_name' => $companyName,
        'rejection_date' => date('F j, Y'),
        'rejection_reason' => $reason,
        'support_email' => getEmailSettings($con)['from_email']
    );
    
    return sendTemplateEmail($con, $email, 'company_rejected', $variables);
}












function sendWelcomeEmail($con, $email, $name, $userType) {
    $variables = array(
        'user_name' => $name,
        'user_type' => $userType,
        'user_email' => $email,
        'login_url' => getEmailSettings($con)['site_url'] . '/login.php'
    );
    
    // Use 'welcome_email' which exists in your database
    return sendTemplateEmail($con, $email, 'welcome_email', $variables);
}

function sendApplicationConfirmationEmail($con, $email, $applicantName, $jobTitle, $companyName) {
    $variables = array(
        'applicant_name' => $applicantName,
        'job_title' => $jobTitle,
        'company_name' => $companyName,
        'application_id' => '', // Add if available
        'application_date' => date('F j, Y')
    );
    
    // Use 'job_application_received' which exists in your database
    return sendTemplateEmail($con, $email, 'job_application_received', $variables);
}

function sendApplicationStatusEmail($con, $email, $applicantName, $jobTitle, $status, $companyName, $applicationId) {
    $variables = array(
        'applicant_name' => $applicantName,
        'job_title' => $jobTitle,
        'company_name' => $companyName,
        'application_status' => $status,
        'application_id' => $applicationId,
        'status_date' => date('F j, Y')
    );
    
    // Use 'application_status_update' which exists in your database
    return sendTemplateEmail($con, $email, 'application_status_update', $variables);
}

function sendJobPostedEmail($con, $email, $companyName, $jobTitle, $jobId) {
    $siteUrl = getEmailSettings($con)['site_url'];
    
    $variables = array(
        'company_name' => $companyName,
        'job_title' => $jobTitle,
        'job_url' => $siteUrl . '/job-detail.php?jobid=' . $jobId,
        'post_date' => date('F j, Y')
    );
    
    // Use 'job_approved' which exists in your database
    return sendTemplateEmail($con, $email, 'job_approved', $variables);
}

function sendCompanyApprovedEmail($con, $email, $companyName, $employerName = '') {
    $variables = array(
        'company_name' => $companyName,
        'approval_date' => date('F j, Y')
    );
    
    // Use 'company_approved' which exists in your database
    return sendTemplateEmail($con, $email, 'company_approved', $variables);
}




// ==================== BACKWARD COMPATIBILITY ====================
// Set global variables for legacy code

if (isset($con)) {
    $headers = getEmailHeaders($con);
    $siteUrl = getEmailSettings($con)['site_url'];
    
    $GLOBALS['headers'] = $headers;
    $GLOBALS['siteUrl'] = $siteUrl;
}