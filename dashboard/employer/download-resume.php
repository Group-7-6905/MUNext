<?php
require 'include/phpcode.php';


$applicationId = isset($_GET['appid']) ? (int)$_GET['appid'] : 0;

$query = "SELECT ja.RESUME, ja.APPLICANTID, u.FNAME, u.ONAME, j.EMPLOYERID 
          FROM tbljobapplication ja
          INNER JOIN tbljob j ON ja.JOBID = j.JOBID
          INNER JOIN tblusers u ON ja.APPLICANTID = u.USERID
          WHERE ja.ID = ?";

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $applicationId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$application = mysqli_fetch_assoc($result);

if (!$application) {
    die("Application not found");
}

if ($application['EMPLOYERID'] != $session_id) {
    die("Access denied");
}

if (!$application['RESUME']) {
    die("No resume uploaded");
}

$filePath = $path.'resumes_cv/' . $application['RESUME'];

if (!file_exists($filePath)) {
    die("Resume file not found");
}

$extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
$applicantName = $application['FNAME'] . '_' . ($application['ONAME']);
$downloadName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $applicantName) . '_Resume.' . $extension;

// Force download
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $downloadName . '"');
header('Content-Length: ' . filesize($filePath));
header('Pragma: no-cache');
header('Expires: 0');
readfile($filePath);
exit();
?>