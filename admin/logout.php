<?php
/**
 * ==================== LOGOUT HANDLER ====================
 * Handles user logout and session cleanup
 */

// session_start();
include "include/phpcode.php";
require_once '../include/toast.php';

// Record logout if user is logged in
if (isset($_SESSION['userID'])) {
    $userid = $_SESSION['userID'];
    
    // Record logout time in login history
    recordLogout($con, $userid);
    
    // Optional: Log activity
    $ip = getClientIP();
    $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                 VALUES (?, 'LOGOUT', 'User logged out', ?, NOW())";
    $logStmt = mysqli_prepare($con, $logQuery);
    if ($logStmt) {
        mysqli_stmt_bind_param($logStmt, "is", $userid, $ip);
        mysqli_stmt_execute($logStmt);
        mysqli_stmt_close($logStmt);
    }
}

// Clear all session variables
session_unset();

// Destroy the session
session_destroy();

// Start new session for toast message
session_start();

// Set logout success message
Toast::success('You have been successfully logged out.');

// Redirect to login page
header('Location: ../login.php?a=login');
exit();
?>