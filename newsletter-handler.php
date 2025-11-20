<?php
session_start();
require('./mysqli_connect.php');

// Set JSON header
header('Content-Type: application/json');

// Function to validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to send confirmation email
include 'PHPMail-Functions.php';

// Initialize response
$response = ['success' => false, 'message' => ''];

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get and sanitize email
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    
    // Validate email
    if (empty($email)) {
        $response['message'] = 'Please enter your email address.';
        echo json_encode($response);
        exit();
    }
    
    if (!validateEmail($email)) {
        $response['message'] = 'Please enter a valid email address.';
        echo json_encode($response);
        exit();
    }
    
    // Check if email already exists
    $checkQuery = "SELECT ID, STATUS, IS_VERIFIED FROM tblnewsletter WHERE EMAIL = ?";
    $stmt = mysqli_prepare($con, $checkQuery);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            
            if ($row['STATUS'] === 'Active' && $row['IS_VERIFIED'] == 1) {
                $response['message'] = 'This email is already subscribed to our newsletter.';
                echo json_encode($response);
                exit();
            } elseif ($row['STATUS'] === 'Unsubscribed') {
                // Reactivate subscription
                $token = bin2hex(random_bytes(32));
                $updateQuery = "UPDATE tblnewsletter SET STATUS = 'Active', VERIFICATION_TOKEN = ?, IS_VERIFIED = 0, SUBSCRIBE_DATE = NOW() WHERE EMAIL = ?";
                $stmtUpdate = mysqli_prepare($con, $updateQuery);
                mysqli_stmt_bind_param($stmtUpdate, "ss", $token, $email);
                
                if (mysqli_stmt_execute($stmtUpdate)) {
                    // Try to send email, but don't fail if it doesn't work
                    sendSubscriptionEmail($email, $token);
                    $response['success'] = true;
                    $response['message'] = 'Welcome back! Please check your email to confirm your subscription.';
                } else {
                    $response['message'] = 'An error occurred. Please try again.';
                }
                echo json_encode($response);
                exit();
            } elseif (!$row['IS_VERIFIED']) {
                $response['message'] = 'Please check your email to verify your subscription. Check spam folder if you don\'t see it.';
                echo json_encode($response);
                exit();
            }
        }
    }
    
    // Insert new subscription
    $token = bin2hex(random_bytes(32));
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $insertQuery = "INSERT INTO tblnewsletter (EMAIL, VERIFICATION_TOKEN, IP_ADDRESS, USER_AGENT) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $insertQuery);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssss", $email, $token, $ipAddress, $userAgent);
        
        if (mysqli_stmt_execute($stmt)) {
            // Try to send email
            sendSubscriptionEmail($email, $token);
            $response['success'] = true;
            $response['message'] = 'Thank you for subscribing! Please check your email to confirm your subscription.';
        } else {
            $response['message'] = 'An error occurred. Please try again later.';
        }
    } else {
        $response['message'] = 'Database error. Please try again later.';
    }
    
    echo json_encode($response);
    exit();
}

// If not POST request
http_response_code(405);
$response['message'] = 'Invalid request method.';
echo json_encode($response);