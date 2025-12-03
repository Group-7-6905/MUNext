<?php
/**
 * EMAIL SYSTEM - USAGE EXAMPLES
 * 
 * This file demonstrates how to use the MUNext email system
 * in various scenarios throughout the application.
 */

require_once 'mysqli_connect.php';
require_once 'include/email-functions.php';

// ==================== EXAMPLE 1: PASSWORD RESET ====================
// Location: forgot-password.php

function example_sendPasswordReset() {
    global $con;
    
    $email = 'user@example.com';
    $userName = 'John Doe';
    $token = bin2hex(random_bytes(32));
    $resetLink = "https://munext.com/forgot-password.php?token={$token}";
    
    $sent = sendPasswordResetEmail($con, $email, $userName, $resetLink);
    
    if ($sent) {
        echo "✓ Password reset email sent!";
    } else {
        echo "✗ Failed to send email";
        error_log("Password reset email failed for: {$email}");
    }
}

// ==================== EXAMPLE 2: USER REGISTRATION ====================
// Location: login.php (after successful registration)

function example_sendWelcomeEmail() {
    global $con;
    
    // After user registration
    $email = $_POST['email'];
    $firstName = $_POST['Fname'];
    $lastName = $_POST['Lname'];
    $fullName = $firstName . ' ' . $lastName;
    $userType = $_POST['role']; // 'Applicant' or 'Employer'
    
    // Send welcome email
    $sent = sendWelcomeEmail($con, $email, $fullName, $userType);
    
    if ($sent) {
        Toast::success("Welcome email sent! Please check your inbox.");
    }
}

// ==================== EXAMPLE 3: JOB APPLICATION ====================
// Location: job-apply.php (after successful application)

function example_sendApplicationConfirmation() {
    global $con;
    
    // After job application is submitted
    $applicantEmail = 'applicant@example.com';
    $applicantName = 'Jane Smith';
    $jobTitle = 'Software Developer';
    $companyName = 'TechCorp Inc.';
    
    // Send confirmation to applicant
    sendApplicationConfirmationEmail(
        $con, 
        $applicantEmail, 
        $applicantName, 
        $jobTitle, 
        $companyName
    );
    
    // Also notify employer
    $employerEmail = 'employer@techcorp.com';
    $employerName = 'HR Manager';
    $applicationId = 123; // From database insert
    
    sendNewApplicationEmail(
        $con,
        $employerEmail,
        $employerName,
        $applicantName,
        $jobTitle,
        $applicationId
    );
    
    Toast::success("Application submitted! Check your email for confirmation.");
}

// ==================== EXAMPLE 4: APPLICATION STATUS UPDATE ====================
// Location: dashboard-manage-applications.php (when employer updates status)

function example_sendStatusUpdate() {
    global $con;
    
    // When employer changes application status
    $applicationId = $_POST['application_id'];
    $newStatus = $_POST['status']; // 'Shortlisted' or 'Rejected'
    
    // Get applicant details from database
    $query = "SELECT u.EMAIL, u.FNAME, u.ONAME, j.JOBTITLE 
              FROM tbljobapplication ja
              INNER JOIN tblusers u ON ja.APPLICANTID = u.USERID
              INNER JOIN tbljob j ON ja.JOBID = j.JOBID
              WHERE ja.ID = ?";
    
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $applicationId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    
    $applicantEmail = $data['EMAIL'];
    $applicantName = $data['FNAME'] . ' ' . $data['ONAME'];
    $jobTitle = $data['JOBTITLE'];
    
    // Send status update email
    $sent = sendApplicationStatusEmail(
        $con,
        $applicantEmail,
        $applicantName,
        $jobTitle,
        $newStatus
    );
    
    if ($sent) {
        Toast::success("Status updated and applicant notified!");
    }
    
    mysqli_stmt_close($stmt);
}

// ==================== EXAMPLE 5: JOB POSTING ====================
// Location: dashboard-post-job.php (after job is posted)

function example_sendJobPostedConfirmation() {
    global $con;
    
    // After employer posts a job
    $employerId = $_SESSION['USERID'];
    $jobId = 456; // From database insert
    
    // Get employer details
    $query = "SELECT u.EMAIL, u.FNAME, u.ONAME, j.JOBTITLE 
              FROM tblusers u
              INNER JOIN tbljob j ON j.EMPLOYERID = u.USERID
              WHERE j.JOBID = ?";
    
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $jobId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    
    $employerEmail = $data['EMAIL'];
    $employerName = $data['FNAME'] . ' ' . $data['ONAME'];
    $jobTitle = $data['JOBTITLE'];
    
    // Send confirmation email
    sendJobPostedEmail(
        $con,
        $employerEmail,
        $employerName,
        $jobTitle,
        $jobId
    );
    
    Toast::success("Job posted successfully! Confirmation email sent.");
    
    mysqli_stmt_close($stmt);
}

// ==================== EXAMPLE 6: COMPANY APPROVAL/REJECTION ====================
// Location: admin-companies.php (when admin approves/rejects company)

function example_sendCompanyDecision() {
    global $con;
    
    $companyId = $_POST['company_id'];
    $action = $_POST['action']; // 'approve' or 'reject'
    
    // Get company and employer details
    $query = "SELECT c.COMPANYNAME, u.EMAIL, u.FNAME, u.ONAME 
              FROM tblcompany c
              INNER JOIN tblusers u ON c.EMPLOYERID = u.USERID
              WHERE c.COMPANYID = ?";
    
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $companyId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    
    $companyName = $data['COMPANYNAME'];
    $employerEmail = $data['EMAIL'];
    $employerName = $data['FNAME'] . ' ' . $data['ONAME'];
    
    if ($action === 'approve') {
        // Update database
        $updateQuery = "UPDATE tblcompany SET COMPANYSTATUS = 'Active' WHERE COMPANYID = ?";
        $stmtUpdate = mysqli_prepare($con, $updateQuery);
        mysqli_stmt_bind_param($stmtUpdate, "i", $companyId);
        mysqli_stmt_execute($stmtUpdate);
        
        // Send approval email
        sendCompanyApprovedEmail(
            $con,
            $employerEmail,
            $companyName,
            $employerName
        );
        
        Toast::success("Company approved and notification sent!");
        
    } else {
        // Update database
        $rejectionReason = $_POST['reason'] ?? 'Does not meet registration requirements';
        
        $updateQuery = "UPDATE tblcompany SET COMPANYSTATUS = 'Rejected' WHERE COMPANYID = ?";
        $stmtUpdate = mysqli_prepare($con, $updateQuery);
        mysqli_stmt_bind_param($stmtUpdate, "i", $companyId);
        mysqli_stmt_execute($stmtUpdate);
        
        // Send rejection email
        sendCompanyRejectedEmail(
            $con,
            $employerEmail,
            $companyName,
            $employerName,
            $rejectionReason
        );
        
        Toast::info("Company rejected and notification sent.");
    }
    
    mysqli_stmt_close($stmt);
}

// ==================== EXAMPLE 7: NEWSLETTER SUBSCRIPTION ====================
// Location: newsletter-signup.php

function example_sendNewsletterConfirmation() {
    global $con;
    
    $email = $_POST['email'];
    
    // Generate confirmation token
    $token = bin2hex(random_bytes(32));
    
    // Store in database
    $query = "INSERT INTO tbl_newsletter (email, token, status) VALUES (?, ?, 'pending')";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ss", $email, $token);
    mysqli_stmt_execute($stmt);
    
    // Send confirmation email
    $sent = sendSubscriptionEmail($con, $email, $token);
    
    if ($sent) {
        Toast::success("Please check your email to confirm subscription!");
    }
    
    mysqli_stmt_close($stmt);
}

// ==================== EXAMPLE 8: CUSTOM EMAIL WITH TEMPLATE ====================
// For scenarios not covered by predefined functions

function example_sendCustomEmail() {
    global $con;
    
    // Define custom variables
    $variables = array(
        'user_name' => 'John Doe',
        'event_name' => 'Career Fair 2025',
        'event_date' => 'March 15, 2025',
        'event_location' => 'Memorial University',
        'registration_link' => 'https://munext.com/events/career-fair-2025'
    );
    
    // Send using custom template
    $result = sendTemplateEmail(
        $con,
        'user@example.com',
        'event_invitation', // Template name in database
        $variables,
        array('cc' => 'events@munext.com') // Optional CC
    );
    
    if ($result) {
        echo "✓ Event invitation sent!";
    }
}

// ==================== EXAMPLE 9: BATCH EMAIL SENDING ====================
// Send emails to multiple recipients (e.g., newsletter, announcements)

function example_sendBatchEmails() {
    global $con;
    
    // Get all active newsletter subscribers
    $query = "SELECT email FROM tbl_newsletter WHERE status = 'active'";
    $result = mysqli_query($con, $query);
    
    $sentCount = 0;
    $failedCount = 0;
    
    while ($row = mysqli_fetch_assoc($result)) {
        $email = $row['email'];
        
        $variables = array(
            'subscriber_email' => $email,
            'newsletter_date' => date('F j, Y'),
            'featured_jobs' => '<li>Job 1</li><li>Job 2</li><li>Job 3</li>'
        );
        
        $sent = sendTemplateEmail(
            $con,
            $email,
            'weekly_newsletter',
            $variables
        );
        
        if ($sent) {
            $sentCount++;
        } else {
            $failedCount++;
        }
        
        // Add small delay to prevent rate limiting
        usleep(100000); // 0.1 second
    }
    
    echo "Sent: {$sentCount}, Failed: {$failedCount}";
}

// ==================== EXAMPLE 10: ERROR HANDLING ====================
// Proper error handling when sending emails

function example_errorHandling() {
    global $con;
    
    try {
        $email = 'user@example.com';
        $userName = 'John Doe';
        $resetLink = 'https://munext.com/reset?token=abc';
        
        $sent = sendPasswordResetEmail($con, $email, $userName, $resetLink);
        
        if (!$sent) {
            // Log error
            error_log("Failed to send password reset email to: {$email}");
            
            // Check email logs for details
            $logQuery = "SELECT * FROM tbl_email_log 
                        WHERE recipient = ? 
                        ORDER BY sent_at DESC 
                        LIMIT 1";
            $stmt = mysqli_prepare($con, $logQuery);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if ($log = mysqli_fetch_assoc($result)) {
                error_log("Email error: " . $log['error_message']);
            }
            
            // Show user-friendly message
            Toast::error("We couldn't send the email. Please try again or contact support.");
            
            mysqli_stmt_close($stmt);
        } else {
            Toast::success("Email sent successfully!");
        }
        
    } catch (Exception $e) {
        error_log("Email system exception: " . $e->getMessage());
        Toast::error("An error occurred. Please try again later.");
    }
}

// ==================== TESTING FUNCTION ====================
// Use this to test if email system is working

function testEmailSystem() {
    global $con;
    
    echo "<h2>Email System Test</h2>";
    
    // Test 1: Check database connection
    echo "<p>1. Database connection: ";
    echo ($con) ? "✓ Connected" : "✗ Failed";
    echo "</p>";
    
    // Test 2: Check templates exist
    echo "<p>2. Email templates: ";
    $query = "SELECT COUNT(*) as count FROM tbl_email_templates WHERE is_active = 1";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    echo "✓ {$row['count']} active templates found";
    echo "</p>";
    
    // Test 3: Check email settings
    echo "<p>3. Email settings: ";
    $settings = getEmailSettings($con);
    echo "✓ From: {$settings['from_name']} &lt;{$settings['from_email']}&gt;";
    echo "</p>";
    
    // Test 4: Send test email (comment out in production)
    /*
    echo "<p>4. Sending test email: ";
    $sent = sendTemplateEmail(
        $con,
        'test@example.com',
        'password_reset',
        array(
            'user_name' => 'Test User',
            'reset_link' => 'https://munext.com/test',
            'expiry_time' => '1 hour'
        )
    );
    echo ($sent) ? "✓ Sent" : "✗ Failed";
    echo "</p>";
    */
    
    echo "<p><strong>Email system is operational!</strong></p>";
}

// Run test (uncomment to use)
// testEmailSystem();
?>
