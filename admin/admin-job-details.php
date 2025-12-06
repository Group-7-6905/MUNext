<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';
unset($result); // Clear any $result from phpcode.php


$adminId = $session_id;

// Get job ID from URL
if (!isset($_GET['jobid']) || empty($_GET['jobid'])) {
    header("Location: admin-all-jobs.php");
    exit();
}

$jobId = (int)$_GET['jobid'];

// ==================== HANDLE JOB STATUS TOGGLE ====================
if (isset($_GET['action']) && $_GET['action'] == 'toggle_status') {
    $currentStatus = isset($_GET['current']) ? $_GET['current'] : '';
    $newStatus = ($currentStatus == 'Active') ? 'Inactive' : 'Active';
    
    $updateQuery = "UPDATE tbljob SET JOBSTATUS = ? WHERE JOBID = ?";
    $stmtUpdate = mysqli_prepare($con, $updateQuery);
    mysqli_stmt_bind_param($stmtUpdate, "si", $newStatus, $jobId);
    
    if (mysqli_stmt_execute($stmtUpdate)) {
        // Log activity
        $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                     VALUES (?, 'job_status_change', ?, ?, NOW())";
        $stmtLog = mysqli_prepare($con, $logQuery);
        $description = "Admin changed job #$jobId status to $newStatus";
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        mysqli_stmt_bind_param($stmtLog, "iss", $adminId, $description, $ipAddress);
        mysqli_stmt_execute($stmtLog);
        
        $_SESSION['success_msg'] = "Job status updated to $newStatus successfully!";
    } else {
        $_SESSION['error_msg'] = "Failed to update job status.";
    }
    
    header("Location: admin-job-details.php?jobid=$jobId");
    exit();
}

// ==================== HANDLE JOB DELETE ====================
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    // Delete related records first (cascading delete)
    $deleteAnswers = "DELETE FROM tblscreeninganswers WHERE APPLICATIONID IN (SELECT ID FROM tbljobapplication WHERE JOBID = ?)";
    $stmtDelAns = mysqli_prepare($con, $deleteAnswers);
    mysqli_stmt_bind_param($stmtDelAns, "i", $jobId);
    mysqli_stmt_execute($stmtDelAns);
    
    $deleteQuestions = "DELETE FROM tblscreeningquestions WHERE JOBID = ?";
    $stmtDelQues = mysqli_prepare($con, $deleteQuestions);
    mysqli_stmt_bind_param($stmtDelQues, "i", $jobId);
    mysqli_stmt_execute($stmtDelQues);
    
    $deleteBookmarks = "DELETE FROM tblbookmarkresume WHERE JOBID = ?";
    $stmtDelBk = mysqli_prepare($con, $deleteBookmarks);
    mysqli_stmt_bind_param($stmtDelBk, "i", $jobId);
    mysqli_stmt_execute($stmtDelBk);
    
    $deleteApps = "DELETE FROM tbljobapplication WHERE JOBID = ?";
    $stmtDelApps = mysqli_prepare($con, $deleteApps);
    mysqli_stmt_bind_param($stmtDelApps, "i", $jobId);
    mysqli_stmt_execute($stmtDelApps);
    
    // Delete job
    $deleteJob = "DELETE FROM tbljob WHERE JOBID = ?";
    $stmtDelJob = mysqli_prepare($con, $deleteJob);
    mysqli_stmt_bind_param($stmtDelJob, "i", $jobId);
    
    if (mysqli_stmt_execute($stmtDelJob)) {
        // Log activity
        $logQuery = "INSERT INTO tbl_activity_log (USER_ID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                     VALUES (?, 'job_delete', ?, ?, NOW())";
        $stmtLog = mysqli_prepare($con, $logQuery);
        $description = "Admin deleted job #$jobId";
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        mysqli_stmt_bind_param($stmtLog, "iss", $adminId, $description, $ipAddress);
        mysqli_stmt_execute($stmtLog);
        
        $_SESSION['success_msg'] = "Job deleted successfully!";
        header("Location: admin-all-jobs.php");
    } else {
        $_SESSION['error_msg'] = "Failed to delete job.";
        header("Location: admin-job-details.php?jobid=$jobId");
    }
    exit();
}

// ==================== FETCH JOB DETAILS ====================
$jobQuery = "SELECT 
    j.*,
    c.COMPANYNAME,
    c.COMPANYLOGO,
    c.COMPANYWEBSITE,
    c.COMPANYEMAIL,
    c.COMPANYCONTACTNO,
    c.COMPANYCITY,
    c.COMPANYCOUNTRY,
    u.FNAME as EMPLOYER_FNAME,
    u.ONAME as EMPLOYER_ONAME,
    u.EMAIL as EMPLOYER_EMAIL,
    jc.CATEGORY,
    jsc.SUBCATEGORY,
    jsc.CATEGORYID
FROM tbljob j
LEFT JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
LEFT JOIN tblusers u ON j.EMPLOYERID = u.USERID
LEFT JOIN tbljobsubcategory jsc ON j.JOBCATEGORYID = jsc.ID
LEFT JOIN tbljobcategory jc ON jsc.CATEGORYID = jc.ID
WHERE j.JOBID = ?
LIMIT 1";

$stmtJob = mysqli_prepare($con, $jobQuery);
mysqli_stmt_bind_param($stmtJob, "i", $jobId);
mysqli_stmt_execute($stmtJob);
$jobResult = mysqli_stmt_get_result($stmtJob);

if (mysqli_num_rows($jobResult) == 0) {
    $_SESSION['error_msg'] = "Job not found.";
    header("Location: admin-all-jobs.php");
    exit();
}

$job = mysqli_fetch_assoc($jobResult);

// ==================== FETCH APPLICATION STATISTICS ====================
$statsQuery = "SELECT 
    COUNT(*) as total_applications,
    SUM(CASE WHEN APPLICATIONSTATUS = 'Pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN APPLICATIONSTATUS = 'Reviewed' THEN 1 ELSE 0 END) as reviewed,
    SUM(CASE WHEN APPLICATIONSTATUS = 'Approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN APPLICATIONSTATUS = 'Rejected' THEN 1 ELSE 0 END) as rejected,
    SUM(CASE WHEN APPLICATIONSTATUS = 'Hire' THEN 1 ELSE 0 END) as hired
FROM tbljobapplication 
WHERE JOBID = ?";

$stmtStats = mysqli_prepare($con, $statsQuery);
mysqli_stmt_bind_param($stmtStats, "i", $jobId);
mysqli_stmt_execute($stmtStats);
$statsResult = mysqli_stmt_get_result($stmtStats);
$appStats = mysqli_fetch_assoc($statsResult);

// ==================== FETCH SCREENING QUESTIONS ====================
$questionsQuery = "SELECT COUNT(*) as question_count FROM tblscreeningquestions WHERE JOBID = ?";
$stmtQuestions = mysqli_prepare($con, $questionsQuery);
mysqli_stmt_bind_param($stmtQuestions, "i", $jobId);
mysqli_stmt_execute($stmtQuestions);
$questionsResult = mysqli_stmt_get_result($stmtQuestions);
$questionData = mysqli_fetch_assoc($questionsResult);
$questionCount = $questionData['question_count'];

// ==================== FETCH RECENT APPLICATIONS ====================
$recentAppsQuery = "SELECT 
    ja.ID,
    ja.DATEAPPLIED,
    ja.APPLICATIONSTATUS,
    ja.SCREENINGSCORE,
    u.FNAME,
    u.ONAME,
    a.EMAILADDRESS
FROM tbljobapplication ja
INNER JOIN tblusers u ON ja.APPLICANTID = u.USERID
INNER JOIN tblapplicants a ON ja.APPLICANTID = a.USERID
WHERE ja.JOBID = ?
ORDER BY ja.DATEAPPLIED DESC
LIMIT 5";

$stmtRecentApps = mysqli_prepare($con, $recentAppsQuery);
mysqli_stmt_bind_param($stmtRecentApps, "i", $jobId);
mysqli_stmt_execute($stmtRecentApps);
$recentAppsResult = mysqli_stmt_get_result($stmtRecentApps);

// Helper function
function timeago($date) {
    $timestamp = strtotime($date);
    $difference = time() - $timestamp;
    
    if ($difference < 3600) {
        $minutes = floor($difference / 60);
        return $minutes > 0 ? $minutes . " min ago" : "Just now";
    } elseif ($difference < 86400) {
        $hours = floor($difference / 3600);
        return $hours . " hour" . ($hours > 1 ? 's' : '') . " ago";
    } elseif ($difference < 604800) {
        $days = floor($difference / 86400);
        return $days . " day" . ($days > 1 ? 's' : '') . " ago";
    } else {
        return date('M d, Y', $timestamp);
    }
}

// Check if expired
$isExpired = strtotime($job['DEADLINE']) < time();
$daysUntilDeadline = ceil((strtotime($job['DEADLINE']) - time()) / 86400);


$JOBLOCATION = ($job['COMPANYCITY'] . ', ' . $job['COMPANYCOUNTRY'])?? '';
$EMPLOYER_NAME = trim($job['EMPLOYER_FNAME'] . ' ' . $job['EMPLOYER_ONAME'])?? '';
?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($job['JOBTITLE']); ?> - Job Details | Admin - MUNext</title>
    <link href="assets/css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.lineicons.com/3.0/lineicons.css">

    <link rel="stylesheet" href="assets/css/custom-style.css">
</head>

<body>
    <div id="main-wrapper">
        <?php include 'header.php' ?>
        <div class="clearfix"></div>

        <div class="dashboard-wrap bg-light">
            <a class="mobNavigation" data-toggle="collapse" href="#MobNav" role="button" aria-expanded="false">
                <i class="fas fa-bars mr-2"></i>Dashboard Navigation
            </a>

            <?php include 'sidenav.php' ?>

            <div class="dashboard-content">
                <!-- Page Header -->
                <div class="dashboard-tlbar">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12">
                            <h1>
                                <i class="lni lni-briefcase"></i> Job Details (Admin View)
                            </h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item text-muted"><a href="admin-dashboard.php">Home</a></li>
                                    <li class="breadcrumb-item text-muted"><a href="admin-all-jobs.php">Manage
                                            Jobs</a></li>
                                    <li class="breadcrumb-item"><a href="#" class="theme-cl">Job Details</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="dashboard-widg-bar d-block">
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

                    <!-- Job Header Card -->
                    <div class="job-header-card position-relative">
                        <span class="admin-badge">
                            <i class="lni lni-shield"></i> ADMIN
                        </span>

                        <h2 class="text-white"><?php echo htmlspecialchars($job['JOBTITLE']); ?></h2>
                        <div class="d-flex align-items-center gap-3">
                            <span
                                class="badge <?php echo $job['JOBSTATUS'] == 'Active' ? 'badge-active' : ($job['JOBSTATUS'] == 'Filled' ? 'badge-filled' : 'badge-inactive'); ?>">
                                <i
                                    class="lni lni-<?php echo $job['JOBSTATUS'] == 'Active' ? 'checkmark-circle' : 'eye-off'; ?>"></i>
                                <?php echo htmlspecialchars($job['JOBSTATUS']); ?>
                            </span>
                            <?php if ($isExpired): ?>
                            <span class="ml-2 badge badge-expired">
                                <i class="lni lni-alarm"></i> Expired
                            </span>
                            <?php endif; ?>
                        </div>

                        <div class="job-meta-info">
                            <div class="job-meta-item">
                                <i class="lni lni-building"></i>
                                <span><?php echo htmlspecialchars($job['COMPANYNAME']); ?></span>
                            </div>
                            <?php if ($JOBLOCATION): ?>
                            <div class="job-meta-item">
                                <i class="lni lni-map-marker"></i>
                                <span><?php echo htmlspecialchars($JOBLOCATION); ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($job['JOBTYPE']): ?>
                            <div class="job-meta-item">
                                <i class="lni lni-briefcase"></i>
                                <span><?php echo htmlspecialchars($job['JOBTYPE']); ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="job-meta-item">
                                <i class="lni lni-calendar"></i>
                                <span>Posted: <?php echo date('M d, Y', strtotime($job['DATEPOSTED'])); ?></span>
                            </div>
                            <div class="job-meta-item">
                                <i class="lni lni-user"></i>
                                <span>Employer: <?php echo htmlspecialchars($EMPLOYER_NAME); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Deadline Alert -->
                    <?php if ($isExpired): ?>
                    <div class="deadline-alert expired">
                        <i class="lni lni-alarm"></i>
                        <div>
                            <strong>This job posting has expired!</strong><br>
                            <small>Deadline was <?php echo date('M d, Y', strtotime($job['DEADLINE'])); ?>.</small>
                        </div>
                    </div>
                    <?php elseif ($daysUntilDeadline <= 7): ?>
                    <div class="deadline-alert soon">
                        <i class="lni lni-alarm"></i>
                        <div>
                            <strong>Deadline approaching!</strong><br>
                            <small>This job will expire in <?php echo $daysUntilDeadline; ?> day(s) on
                                <?php echo date('M d, Y', strtotime($job['DEADLINE'])); ?></small>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Application Statistics -->
                    <div class="stats-grid">
                        <div class="stat-card total">
                            <span
                                class="stat-number"><?php echo number_format($appStats['total_applications']); ?></span>
                            <span class="stat-label">Total Applications</span>
                        </div>
                        <div class="stat-card pending">
                            <span class="stat-number"><?php echo number_format($appStats['pending']); ?></span>
                            <span class="stat-label">Pending</span>
                        </div>
                        <div class="stat-card reviewed">
                            <span class="stat-number"><?php echo number_format($appStats['reviewed']); ?></span>
                            <span class="stat-label">Reviewed</span>
                        </div>
                        <div class="stat-card approved">
                            <span class="stat-number"><?php echo number_format($appStats['approved']); ?></span>
                            <span class="stat-label">Approved</span>
                        </div>
                        <div class="stat-card rejected">
                            <span class="stat-number"><?php echo number_format($appStats['rejected']); ?></span>
                            <span class="stat-label">Rejected</span>
                        </div>
                        <div class="stat-card hired">
                            <span class="stat-number"><?php echo number_format($appStats['hired']); ?></span>
                            <span class="stat-label">Hired</span>
                        </div>
                        <div class="stat-card questions">
                            <span class="stat-number"><?php echo number_format($questionCount); ?></span>
                            <span class="stat-label">Screening Questions</span>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-lg-8">
                            <!-- Job Information -->
                            <div class="info-card-details">
                                <div class="info-card-header">
                                    <div class="info-card-icon">
                                        <i class="lni lni-information"></i>
                                    </div>
                                    <h3 class="info-card-title">Job Information</h3>
                                </div>

                                <div class="detail-row">
                                    <div class="detail-label">Job ID:</div>
                                    <div class="detail-value">#<?php echo $job['JOBID']; ?></div>
                                </div>

                                <div class="detail-row">
                                    <div class="detail-label">Job Title:</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($job['JOBTITLE']); ?></div>
                                </div>

                                <?php if ($job['SUBCATEGORY']): ?>
                                <div class="detail-row">
                                    <div class="detail-label">Category:</div>
                                    <div class="detail-value">
                                        <?php echo htmlspecialchars($job['CATEGORY'] . ' - ' . $job['SUBCATEGORY']); ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if ($job['JOBTYPE']): ?>
                                <div class="detail-row">
                                    <div class="detail-label">Job Type:</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($job['JOBTYPE']); ?></div>
                                </div>
                                <?php endif; ?>

                                <?php if ($job['WORKPLACE_POLICY']): ?>
                                <div class="detail-row">
                                    <div class="detail-label">Workplace:</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($job['WORKPLACE_POLICY']); ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if ($job['SALARY']): ?>
                                <div class="detail-row">
                                    <div class="detail-label">Salary:</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($job['SALARY']); ?></div>
                                </div>
                                <?php endif; ?>

                                <?php if ($JOBLOCATION): ?>
                                <div class="detail-row">
                                    <div class="detail-label">Location:</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($JOBLOCATION); ?></div>
                                </div>
                                <?php endif; ?>

                                <div class="detail-row">
                                    <div class="detail-label">Posted Date:</div>
                                    <div class="detail-value">
                                        <?php echo date('F d, Y', strtotime($job['DATEPOSTED'])); ?></div>
                                </div>

                                <div class="detail-row">
                                    <div class="detail-label">Deadline:</div>
                                    <div
                                        class="detail-value <?php echo $isExpired ? 'text-danger font-weight-bold' : ''; ?>">
                                        <?php echo date('F d, Y', strtotime($job['DEADLINE'])); ?>
                                        <?php if ($isExpired): ?>
                                        <span class="badge badge-danger ml-2">Expired</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="detail-row">
                                    <div class="detail-label">Status:</div>
                                    <div class="detail-value">
                                        <span
                                            class="badge <?php echo $job['JOBSTATUS'] == 'Active' ? 'badge-active' : 'badge-inactive'; ?>">
                                            <?php echo htmlspecialchars($job['JOBSTATUS']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Job Description -->
                            <div class="info-card-details">
                                <div class="info-card-header">
                                    <div class="info-card-icon">
                                        <i class="lni lni-text-format"></i>
                                    </div>
                                    <h3 class="info-card-title">Job Description</h3>
                                </div>
                                <div style="line-height: 1.8;">
                                    <?php echo nl2br(htmlspecialchars($job['JOBDESCRIPTION'])); ?>
                                </div>
                            </div>

                            <!-- Qualifications -->
                            <?php if (!empty($job['QUALIFICATIONS'])): ?>
                            <div class="info-card-details">
                                <div class="info-card-header">
                                    <div class="info-card-icon">
                                        <i class="lni lni-graduation"></i>
                                    </div>
                                    <h3 class="info-card-title">Qualifications</h3>
                                </div>
                                <div style="line-height: 1.8;">
                                    <?php echo nl2br(htmlspecialchars($job['QUALIFICATIONS'])); ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Right Column -->
                        <div class="col-lg-4">
                            <!-- Admin Actions -->
                            <div class="info-card-details">
                                <div class="info-card-header">
                                    <div class="info-card-icon">
                                        <i class="lni lni-cog"></i>
                                    </div>
                                    <h3 class="info-card-title">Admin Actions</h3>
                                </div>

                                <div class="action-buttons">
                                    <a href="?action=toggle_status&jobid=<?php echo $jobId; ?>&current=<?php echo $job['JOBSTATUS']; ?>"
                                        class="btn-action <?php echo $job['JOBSTATUS'] == 'Active' ? 'btn-warning' : 'btn-success'; ?>">
                                        <i
                                            class="lni lni-<?php echo $job['JOBSTATUS'] == 'Active' ? 'eye-off' : 'checkmark-circle'; ?>"></i>
                                        <?php echo $job['JOBSTATUS'] == 'Active' ? 'Deactivate Job' : 'Activate Job'; ?>
                                    </a>

                                    <a href="admin-all-applications.php?jobid=<?php echo $jobId; ?>"
                                        class="btn-action btn-admin">
                                        <i class="lni lni-users"></i> View Applications
                                        (<?php echo $appStats['total_applications']; ?>)
                                    </a>

                                    <a href="<?php echo $path?>job-detail.php?jobid=<?php echo $jobId; ?>"
                                        target="_blank" class="btn-action btn-info">
                                        <i class="lni lni-eye"></i> View Public Page
                                    </a>

                                    <a href="?action=delete&jobid=<?php echo $jobId; ?>" class="btn-action btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this job? This will also delete all applications and screening questions. This action cannot be undone!');">
                                        <i class="lni lni-trash-can"></i> Delete Job
                                    </a>
                                </div>
                            </div>

                            <!-- Recent Applications -->
                            <div class="info-card-details">
                                <div class="info-card-header">
                                    <div class="info-card-icon">
                                        <i class="lni lni-users"></i>
                                    </div>
                                    <h3 class="info-card-title">Recent Applications</h3>
                                </div>

                                <?php if (mysqli_num_rows($recentAppsResult) > 0): ?>
                                <?php while ($app = mysqli_fetch_assoc($recentAppsResult)): ?>
                                <div class="application-item">
                                    <div class="applicant-name">
                                        <?php echo htmlspecialchars($app['FNAME'] . ' ' . $app['ONAME']); ?>
                                    </div>
                                    <div class="applicant-info">
                                        <span><i class="lni lni-envelope"></i>
                                            <?php echo htmlspecialchars($app['EMAILADDRESS']); ?></span>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="lni lni-calendar"></i> <?php echo timeago($app['DATEAPPLIED']); ?>
                                        </small>
                                        <span
                                            class="badge badge-<?php echo $app['APPLICATIONSTATUS'] == 'Pending' ? 'warning' : 'info'; ?> ml-2">
                                            <?php echo htmlspecialchars($app['APPLICATIONSTATUS']); ?>
                                        </span>
                                    </div>
                                </div>
                                <?php endwhile; ?>

                                <?php if ($appStats['total_applications'] > 5): ?>
                                <div class="text-center mt-3">
                                    <a href="admin-all-applications.php?jobid=<?php echo $jobId; ?>"
                                        class="btn btn-sm btn-outline-primary">
                                        View All <?php echo $appStats['total_applications']; ?> Applications
                                    </a>
                                </div>
                                <?php endif; ?>
                                <?php else: ?>
                                <div class="empty-state">
                                    <i class="lni lni-users"></i>
                                    <p>No applications received yet.</p>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Company Info -->
                            <div class="info-card-details">
                                <div class="info-card-header">
                                    <div class="info-card-icon">
                                        <i class="fa fa-building"></i>
                                    </div>
                                    <h3 class="info-card-title">Company Information</h3>
                                </div>

                                <div class="detail-row">
                                    <div class="detail-label">Company:</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($job['COMPANYNAME']); ?></div>
                                </div>

                                <?php if ($job['COMPANYCITY'] || $job['COMPANYCOUNTRY']): ?>
                                <div class="detail-row">
                                    <div class="detail-label">Location:</div>
                                    <div class="detail-value">
                                        <?php 
                                        $location = [];
                                        if ($job['COMPANYCITY']) $location[] = $job['COMPANYCITY'];
                                        if ($job['COMPANYCOUNTRY']) $location[] = $job['COMPANYCOUNTRY'];
                                        echo htmlspecialchars(implode(', ', $location));
                                        ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if ($job['COMPANYEMAIL']): ?>
                                <div class="detail-row">
                                    <div class="detail-label">Email:</div>
                                    <div class="detail-value">
                                        <a href="mailto:<?php echo htmlspecialchars($job['COMPANYEMAIL']); ?>">
                                            <?php echo htmlspecialchars($job['COMPANYEMAIL']); ?>
                                        </a>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if ($job['COMPANYCONTACTNO']): ?>
                                <div class="detail-row">
                                    <div class="detail-label">Phone:</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($job['COMPANYCONTACTNO']); ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if ($job['COMPANYWEBSITE']): ?>
                                <div class="detail-row">
                                    <div class="detail-label">Website:</div>
                                    <div class="detail-value">
                                        <a href="<?php echo htmlspecialchars($job['COMPANYWEBSITE']); ?>"
                                            target="_blank">
                                            <?php echo htmlspecialchars($job['COMPANYWEBSITE']); ?>
                                        </a>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Employer Info -->
                            <div class="info-card-details">
                                <div class="info-card-header">
                                    <div class="info-card-icon">
                                        <i class="lni lni-user"></i>
                                    </div>
                                    <h3 class="info-card-title">Employer Information</h3>
                                </div>

                                <div class="detail-row">
                                    <div class="detail-label">Name:</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($EMPLOYER_NAME); ?>
                                    </div>
                                </div>

                                <?php if ($job['EMPLOYER_EMAIL']): ?>
                                <div class="detail-row">
                                    <div class="detail-label">Email:</div>
                                    <div class="detail-value">
                                        <a href="mailto:<?php echo htmlspecialchars($job['EMPLOYER_EMAIL']); ?>">
                                            <?php echo htmlspecialchars($job['EMPLOYER_EMAIL']); ?>
                                        </a>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div class="detail-row">
                                    <div class="detail-label">Employer ID:</div>
                                    <div class="detail-value">#<?php echo $job['EMPLOYERID']; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php include 'footer.php' ?>
            </div>
        </div>

        <a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>
    </div>

    <!-- Scripts -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/slick.js"></script>
    <script src="assets/js/slider-bg.js"></script>
    <script src="assets/js/smoothproducts.js"></script>
    <script src="assets/js/snackbar.min.js"></script>
    <script src="assets/js/jQuery.style.switcher.js"></script>
    <script src="assets/js/custom.js"></script>

    <script>
    $(document).ready(function() {
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
    </script>
</body>

</html>