<?php
require 'include/phpcode.php';

header('Content-Type: application/json');



// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($session_id)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized - Not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['job_id'])) {
    $jobId = (int)$_POST['job_id'];
    $employerId = (int)$session_id;
    
    // Debug: Log the values
    error_log("Job ID: $jobId, Employer ID: $employerId");
    
    // Get current status
    $query = "SELECT JOBSTATUS FROM tbljob WHERE JOBID = ? AND EMPLOYERID = ?";
    $stmt = mysqli_prepare($con, $query);
    
    if (!$stmt) {
        echo json_encode([
            'success' => false, 
            'message' => 'Database prepare error: ' . mysqli_error($con)
        ]);
        exit();
    }
    
    mysqli_stmt_bind_param($stmt, "ii", $jobId, $employerId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $job = mysqli_fetch_assoc($result);
    
    if ($job) {
        // Toggle status
        $currentStatus = $job['JOBSTATUS'];
        $newStatus = ($currentStatus == 'Active') ? 'Inactive' : 'Active';
        
        // Debug: Log status change
        error_log("Changing status from '$currentStatus' to '$newStatus'");
        
        // Update the status
        $updateQuery = "UPDATE tbljob SET JOBSTATUS = ? WHERE JOBID = ? AND EMPLOYERID = ?";
        $stmtUpdate = mysqli_prepare($con, $updateQuery);
        
        if (!$stmtUpdate) {
            echo json_encode([
                'success' => false, 
                'message' => 'Update prepare error: ' . mysqli_error($con)
            ]);
            exit();
        }
        
        mysqli_stmt_bind_param($stmtUpdate, "sii", $newStatus, $jobId, $employerId);
        
        if (mysqli_stmt_execute($stmtUpdate)) {
            $affectedRows = mysqli_stmt_affected_rows($stmtUpdate);
            
            // Debug: Check if rows were actually updated
            error_log("Affected rows: $affectedRows");
            
            if ($affectedRows > 0) {
                echo json_encode([
                    'success' => true, 
                    'new_status' => $newStatus,
                    'message' => "Job is now $newStatus",
                    'debug' => [
                        'job_id' => $jobId,
                        'old_status' => $currentStatus,
                        'new_status' => $newStatus,
                        'affected_rows' => $affectedRows
                    ]
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'No rows updated - status may already be ' . $newStatus,
                    'debug' => [
                        'job_id' => $jobId,
                        'current_status' => $currentStatus,
                        'attempted_status' => $newStatus
                    ]
                ]);
            }
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Execute error: ' . mysqli_stmt_error($stmtUpdate)
            ]);
        }
        
        mysqli_stmt_close($stmtUpdate);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Job not found or access denied',
            'debug' => [
                'job_id' => $jobId,
                'employer_id' => $employerId
            ]
        ]);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid request - Missing job_id',
        'received' => $_POST
    ]);
}
?>