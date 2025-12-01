<?php
/**
 * ==================== EMAIL FUNCTIONS ====================
 * MUNext Email System
 * Uses templates from tbl_email_templates and settings from tbl_settings
 * Supports SMTP and standard PHP mail()
 */

// Include PHPMailer (make sure you have PHPMailer installed)
// Download from: https://github.com/PHPMailer/PHPMailer
// Or install via composer: composer require phpmailer/phpmailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Require PHPMailer files
require_once 'phpmailer/src/Exception.php';
require_once 'phpmailer/src/PHPMailer.php';
require_once 'phpmailer/src/SMTP.php';

/**
 * Get email settings from database
 * 
 * @param mysqli $con Database connection
 * @return array Email settings
 */
function getEmailSettings($con) {
    $settings = array();
    
    $query = "SELECT setting_key, setting_value 
              FROM tbl_settings 
              WHERE setting_key IN (
                  'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password',
                  'smtp_encryption', 'smtp_from_email', 'smtp_from_name',
                  'use_smtp', 'site_name', 'site_email'
              )";
    
    $result = mysqli_query($con, $query);
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    // Set defaults if not found
    $defaults = array(
        'smtp_host' => 'smtp.gmail.com',
        'smtp_port' => '587',
        'smtp_username' => '',
        'smtp_password' => '',
        'smtp_encryption' => 'tls',
        'smtp_from_email' => 'noreply@munext.com',
        'smtp_from_name' => 'MUNext',
        'use_smtp' => '1',
        'site_name' => 'MUNext',
        'site_email' => 'support@munext.com'
    );
    
    foreach ($defaults as $key => $value) {
        if (!isset($settings[$key]) || empty($settings[$key])) {
            $settings[$key] = $value;
        }
    }
    
    return $settings;
}

/**
 * Get email template from database
 * 
 * @param mysqli $con Database connection
 * @param string $templateSlug Template identifier (e.g., 'password_reset', 'welcome_email')
 * @return array|null Template data or null if not found
 */
function getEmailTemplate($con, $templateSlug) {
    $query = "SELECT template_name, template_subject, template_body 
              FROM tbl_email_templates 
              WHERE template_slug = ? AND is_active = 1 
              LIMIT 1";
    
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "s", $templateSlug);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        mysqli_stmt_close($stmt);
        return $row;
    }
    
    mysqli_stmt_close($stmt);
    return null;
}

/**
 * Replace placeholders in email template
 * 
 * @param string $content Template content with placeholders
 * @param array $variables Associative array of variable => value pairs
 * @return string Content with replaced placeholders
 */
function replacePlaceholders($content, $variables) {
    foreach ($variables as $key => $value) {
        // Replace {{variable}} and {variable} formats
        $content = str_replace('{{' . $key . '}}', $value, $content);
        $content = str_replace('{' . $key . '}', $value, $content);
    }
    return $content;
}

/**
 * Send email using PHPMailer with SMTP or standard mail()
 * 
 * @param mysqli $con Database connection
 * @param string $to Recipient email address
 * @param string $templateSlug Email template slug
 * @param array $variables Template variables to replace
 * @param string $recipientName Recipient's name (optional)
 * @param array $attachments Array of file paths to attach (optional)
 * @return bool True on success, false on failure
 */
function sendEmail($con, $to, $templateSlug, $variables = array(), $recipientName = '', $attachments = array()) {
    try {
        // Get email settings
        $settings = getEmailSettings($con);
        
        // Get email template
        $template = getEmailTemplate($con, $templateSlug);
        
        if (!$template) {
            error_log("Email template not found: " . $templateSlug);
            return false;
        }
        
        // Add site info to variables
        $variables['site_name'] = $settings['site_name'];
        $variables['site_email'] = $settings['site_email'];
        $variables['current_year'] = date('Y');
        $variables['site_url'] = 'http://' . $_SERVER['HTTP_HOST'];
        
        // Replace placeholders in subject and body
        $subject = replacePlaceholders($template['template_subject'], $variables);
        $body = replacePlaceholders($template['template_body'], $variables);
        
        // Check if we should use SMTP
        $useSMTP = ($settings['use_smtp'] == '1' || $settings['use_smtp'] === true);
        
        if ($useSMTP && !empty($settings['smtp_username']) && !empty($settings['smtp_password'])) {
            // Use PHPMailer with SMTP
            $mail = new PHPMailer(true);
            
            // Server settings
            $mail->isSMTP();
            $mail->Host = $settings['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $settings['smtp_username'];
            $mail->Password = $settings['smtp_password'];
            $mail->Port = (int)$settings['smtp_port'];
            
            // Set encryption
            if ($settings['smtp_encryption'] == 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($settings['smtp_encryption'] == 'tls') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }
            
            // Disable SSL verification for local development (remove in production)
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            
            // Recipients
            $mail->setFrom($settings['smtp_from_email'], $settings['smtp_from_name']);
            $mail->addAddress($to, $recipientName);
            $mail->addReplyTo($settings['site_email'], $settings['site_name']);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = strip_tags($body); // Plain text version
            
            // Attachments
            if (!empty($attachments)) {
                foreach ($attachments as $attachment) {
                    if (file_exists($attachment)) {
                        $mail->addAttachment($attachment);
                    }
                }
            }
            
            // Send email
            $result = $mail->send();
            
            // Log success
            logEmail($con, $to, $subject, $templateSlug, $result ? 'sent' : 'failed');
            
            return $result;
            
        } else {
            // Use standard PHP mail()
            $headers = array();
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-type: text/html; charset=UTF-8';
            $headers[] = 'From: ' . $settings['smtp_from_name'] . ' <' . $settings['smtp_from_email'] . '>';
            $headers[] = 'Reply-To: ' . $settings['site_email'];
            $headers[] = 'X-Mailer: PHP/' . phpversion();
            
            $result = mail($to, $subject, $body, implode("\r\n", $headers));
            
            // Log email
            logEmail($con, $to, $subject, $templateSlug, $result ? 'sent' : 'failed');
            
            return $result;
        }
        
    } catch (Exception $e) {
        error_log("Email send failed: " . $e->getMessage());
        logEmail($con, $to, $subject ?? '', $templateSlug, 'failed', $e->getMessage());
        return false;
    }
}

/**
 * Send password reset email
 * 
 * @param mysqli $con Database connection
 * @param string $email User's email address
 * @param string $name User's full name
 * @param string $resetLink Password reset link
 * @return bool True on success, false on failure
 */
function sendPasswordResetEmail($con, $email, $name, $resetLink) {
    $variables = array(
        'user_name' => $name,
        'reset_link' => $resetLink,
        'expiry_time' => '1 hour'
    );
    
    return sendEmail($con, $email, 'password_reset', $variables, $name);
}

/**
 * Send welcome email to new user
 * 
 * @param mysqli $con Database connection
 * @param string $email User's email address
 * @param string $name User's full name
 * @param string $username User's username
 * @param string $role User's role (Applicant/Employer)
 * @return bool True on success, false on failure
 */
function sendWelcomeEmail($con, $email, $name, $username, $role = 'Applicant') {
    $variables = array(
        'user_name' => $name,
        'username' => $username,
        'user_role' => $role,
        'login_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/login.php'
    );
    
    return sendEmail($con, $email, 'welcome_email', $variables, $name);
}

/**
 * Send job application confirmation email
 * 
 * @param mysqli $con Database connection
 * @param string $email Applicant's email
 * @param string $name Applicant's name
 * @param string $jobTitle Job title
 * @param string $companyName Company name
 * @return bool True on success, false on failure
 */
function sendApplicationConfirmationEmail($con, $email, $name, $jobTitle, $companyName) {
    $variables = array(
        'user_name' => $name,
        'job_title' => $jobTitle,
        'company_name' => $companyName,
        'application_date' => date('F d, Y')
    );
    
    return sendEmail($con, $email, 'application_confirmation', $variables, $name);
}

/**
 * Send job posting approval email to employer
 * 
 * @param mysqli $con Database connection
 * @param string $email Employer's email
 * @param string $name Employer's name
 * @param string $jobTitle Job title
 * @param string $jobId Job ID
 * @return bool True on success, false on failure
 */
function sendJobApprovalEmail($con, $email, $name, $jobTitle, $jobId) {
    $variables = array(
        'user_name' => $name,
        'job_title' => $jobTitle,
        'job_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/job-detail.php?jobid=' . $jobId
    );
    
    return sendEmail($con, $email, 'job_approval', $variables, $name);
}

/**
 * Send notification email for new job application to employer
 * 
 * @param mysqli $con Database connection
 * @param string $email Employer's email
 * @param string $name Employer's name
 * @param string $jobTitle Job title
 * @param string $applicantName Applicant's name
 * @param string $applicationId Application ID
 * @return bool True on success, false on failure
 */
function sendNewApplicationNotification($con, $email, $name, $jobTitle, $applicantName, $applicationId) {
    $variables = array(
        'user_name' => $name,
        'job_title' => $jobTitle,
        'applicant_name' => $applicantName,
        'application_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/dashboard-manage-applications.php?applicationid=' . $applicationId
    );
    
    return sendEmail($con, $email, 'new_application', $variables, $name);
}

/**
 * Send application status update email
 * 
 * @param mysqli $con Database connection
 * @param string $email Applicant's email
 * @param string $name Applicant's name
 * @param string $jobTitle Job title
 * @param string $status New status (Shortlisted/Rejected/Interview)
 * @return bool True on success, false on failure
 */
function sendApplicationStatusEmail($con, $email, $name, $jobTitle, $status) {
    $templateSlug = '';
    
    switch ($status) {
        case 'Shortlisted':
            $templateSlug = 'application_shortlisted';
            break;
        case 'Rejected':
            $templateSlug = 'application_rejected';
            break;
        case 'Interview':
            $templateSlug = 'interview_invitation';
            break;
        default:
            $templateSlug = 'application_status_update';
    }
    
    $variables = array(
        'user_name' => $name,
        'job_title' => $jobTitle,
        'status' => $status
    );
    
    return sendEmail($con, $email, $templateSlug, $variables, $name);
}

/**
 * Log sent emails (optional - create tbl_email_logs table)
 * 
 * @param mysqli $con Database connection
 * @param string $recipient Recipient email
 * @param string $subject Email subject
 * @param string $template Template slug
 * @param string $status Status (sent/failed)
 * @param string $error Error message if failed
 * @return bool True on success
 */
function logEmail($con, $recipient, $subject, $template, $status, $error = '') {
    $query = "INSERT INTO tbl_email_logs 
              (recipient_email, subject, template_slug, status, error_message, sent_at) 
              VALUES (?, ?, ?, ?, ?, NOW())";
    
    $stmt = mysqli_prepare($con, $query);
    
    // If table doesn't exist, just return true and don't log
    if (!$stmt) {
        return true;
    }
    
    mysqli_stmt_bind_param($stmt, "sssss", $recipient, $subject, $template, $status, $error);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    return $result;
}

/**
 * Test email configuration
 * 
 * @param mysqli $con Database connection
 * @param string $testEmail Email to send test to
 * @return array Result with success status and message
 */
function testEmailConfiguration($con, $testEmail) {
    $variables = array(
        'user_name' => 'Test User',
        'test_message' => 'This is a test email from MUNext. If you received this, your email configuration is working correctly!'
    );
    
    $result = sendEmail($con, $testEmail, 'test_email', $variables, 'Test User');
    
    return array(
        'success' => $result,
        'message' => $result ? 'Test email sent successfully!' : 'Failed to send test email. Please check your email configuration.'
    );
}

/**
 * Send custom email with manual content (without template)
 * 
 * @param mysqli $con Database connection
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $body Email body (HTML)
 * @param string $recipientName Recipient name
 * @return bool True on success
 */
function sendCustomEmail($con, $to, $subject, $body, $recipientName = '') {
    try {
        $settings = getEmailSettings($con);
        $useSMTP = ($settings['use_smtp'] == '1' || $settings['use_smtp'] === true);
        
        if ($useSMTP && !empty($settings['smtp_username']) && !empty($settings['smtp_password'])) {
            $mail = new PHPMailer(true);
            
            // Server settings
            $mail->isSMTP();
            $mail->Host = $settings['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $settings['smtp_username'];
            $mail->Password = $settings['smtp_password'];
            $mail->Port = (int)$settings['smtp_port'];
            
            if ($settings['smtp_encryption'] == 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($settings['smtp_encryption'] == 'tls') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }
            
            // Recipients
            $mail->setFrom($settings['smtp_from_email'], $settings['smtp_from_name']);
            $mail->addAddress($to, $recipientName);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = strip_tags($body);
            
            return $mail->send();
        } else {
            // Use standard mail()
            $headers = array();
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-type: text/html; charset=UTF-8';
            $headers[] = 'From: ' . $settings['smtp_from_name'] . ' <' . $settings['smtp_from_email'] . '>';
            
            return mail($to, $subject, $body, implode("\r\n", $headers));
        }
    } catch (Exception $e) {
        error_log("Custom email send failed: " . $e->getMessage());
        return false;
    }
}

?>