<?php
require 'include/phpcode.php';


$employerId = $session_id; // Assuming session_id stores COMPANYID for employer

$applicationId = isset($_GET['appid']) ? (int)$_GET['appid'] : 0;

// Verify this application belongs to employer's job
$query = "SELECT ja.RESUME, j.EMPLOYERID 
          FROM tbljobapplication ja
          INNER JOIN tbljob j ON ja.JOBID = j.JOBID
          WHERE ja.ID = ?";

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $applicationId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$application = mysqli_fetch_assoc($result);

if (!$application) {
    die("Application not found");
}

// Verify employer owns this job
if ($application['EMPLOYERID'] != $session_id) {
    die("Access denied");
}

if (!$application['RESUME']) {
    die("No resume uploaded for this application");
}

$filePath = $path.'resumes_cv/' . $application['RESUME'];

if (!file_exists($filePath)) {
    die("Resume file not found on server");
}

// Get file extension
$extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

// Set appropriate content type
$contentTypes = [
    'pdf' => 'application/pdf',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'txt' => 'text/plain',
    'rtf' => 'application/rtf'
];

$contentType = $contentTypes[$extension] ?? 'application/octet-stream';

// Send file to browser for viewing
header('Content-Type: ' . $contentType);
header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: private, max-age=3600');
readfile($filePath);
exit();
?>