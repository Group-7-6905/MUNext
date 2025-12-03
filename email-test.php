<?php 
require('./mysqli_connect.php');

include 'session_check.php';
include "include/helper.php";
require_once "include/email-functions.php";

// Send password reset email
$emailSent = sendPasswordResetEmail(
    $con,                    // Database connection
    'user@example.com',      // Recipient email
    'John Doe',              // User name
    'https://munext.com/reset?token=abc123'  // Reset link
);

if ($emailSent) {
    echo "Email sent successfully!";
} else {
    echo "Failed to send email.";
}