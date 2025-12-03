<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';
unset($result); // Clear any $result from phpcode.php

$employerID = $session_id;

// Get job ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard-manage-jobs.php");
    exit();
}

$jobId = (int)$_GET['id'];

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
    jsc.SUBCATEGORY
FROM tbljob j
INNER JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
LEFT JOIN tbljobsubcategory jsc ON j.JOBCATEGORYID = jsc.ID
WHERE j.JOBID = ? AND j.EMPLOYERID = ?
LIMIT 1";

$stmtJob = mysqli_prepare($con, $jobQuery);
mysqli_stmt_bind_param($stmtJob, "ii", $jobId, $employerID);
mysqli_stmt_execute($stmtJob);
$jobResult = mysqli_stmt_get_result($stmtJob);

if (mysqli_num_rows($jobResult) == 0) {
    $_SESSION['error_msg'] = "Job not found or you don't have permission to view it.";
    header("Location: dashboard-manage-jobs.php");
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
$questionsQuery = "SELECT * FROM tblscreeningquestions WHERE JOBID = ? ORDER BY QUESTIONID ASC";
$stmtQuestions = mysqli_prepare($con, $questionsQuery);
mysqli_stmt_bind_param($stmtQuestions, "i", $jobId);
mysqli_stmt_execute($stmtQuestions);
$questionsResult = mysqli_stmt_get_result($stmtQuestions);

// ==================== FETCH RECENT APPLICATIONS ====================
$recentAppsQuery = "SELECT 
    ja.*,
    a.FNAME,
    a.OTHERNAMES,
    a.EMAILADDRESS,
    a.CONTACTNO,
    a.SKILLS,
    a.DEGREE
FROM tbljobapplication ja
INNER JOIN tblapplicants a ON ja.APPLICANTID = a.APPLICANTID
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
    
    if ($difference < 86400) {
        $hours = floor($difference / 3600);
        return $hours > 0 ? $hours . " hour" . ($hours > 1 ? 's' : '') . " ago" : "Just now";
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



$JOBLOCATION = $job['COMPANYCITY'] . ', ' . $job['COMPANYCOUNTRY'] ??'';
?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($job['JOBTITLE']); ?> - Job Details | MUNext</title>
    <link href="assets/css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.lineicons.com/3.0/lineicons.css">

    <link rel="stylesheet" href="assets/css/custom-dashboard.css">
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
                                <i class="lni lni-briefcase"></i> Job Details
                            </h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item text-muted"><a href="index.php">Home</a></li>
                                    <li class="breadcrumb-item text-muted"><a href="dashboard-manage-jobs.php">Manage
                                            Jobs</a></li>
                                    <li class="breadcrumb-item"><a href="#" class="theme-cl">Job Details</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="dashboard-widg-bar d-block">
                    <!-- Job Header Card -->
                    <div class="job-header-card">
                        <h2 class="text-white"><?php echo htmlspecialchars($job['JOBTITLE']); ?></h2>
                        <div class="d-flex align-items-center gap-3">
                            <span
                                class="badge <?php echo $job['JOBSTATUS'] == 'Active' ? 'badge-active' : ($job['JOBSTATUS'] == 'Filled' ? 'badge-filled' : 'badge-inactive'); ?>">
                                <i
                                    class="lni lni-<?php echo $job['JOBSTATUS'] == 'Active' ? 'checkmark-circle' : 'eye-off'; ?>"></i>
                                <?php echo htmlspecialchars($job['JOBSTATUS']); ?>
                            </span>
                            <?php if ($isExpired): ?>
                            <span class="badge badge-danger ml-2">
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
                        </div>
                    </div>

                    <!-- Deadline Alert -->
                    <?php if ($isExpired): ?>
                    <div class="deadline-alert expired">
                        <i class="lni lni-alarm"></i>
                        <div>
                            <strong>This job posting has expired!</strong><br>
                            <small>Deadline was <?php echo date('M d, Y', strtotime($job['DEADLINE'])); ?>. You can
                                extend the deadline or close this position.</small>
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
                    </div>

                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-lg-8">
                            <!-- Job Information -->
                            <div class="info-card">
                                <div class="info-card-header">
                                    <div class="info-card-icon">
                                        <i class="lni lni-information"></i>
                                    </div>
                                    <h3 class="info-card-title">Job Information</h3>
                                </div>

                                <div class="detail-row">
                                    <div class="detail-label">Job Title:</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($job['JOBTITLE']); ?></div>
                                </div>

                                <?php if ($job['SUBCATEGORY']): ?>
                                <div class="detail-row">
                                    <div class="detail-label">Category:</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($job['SUBCATEGORY']); ?></div>
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
                                        <?php echo date('F d, Y', strtotime($job['DATEPOSTED'])); ?>
                                    </div>
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
                                            class="badge <?php echo $job['JOBSTATUS'] == 'Active' ? 'badge-success' : 'badge-warning'; ?>">
                                            <?php echo htmlspecialchars($job['JOBSTATUS']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Job Description -->
                            <div class="info-card">
                                <div class="info-card-header">
                                    <div class="info-card-icon">
                                        <i class="lni lni-text-format"></i>
                                    </div>
                                    <h3 class="info-card-title">Job Description</h3>
                                </div>
                                <div style="line-height: 1.8;">
                                    <?php echo nl2br(htmlspecialchars($job['JOBDESCRIPTION']));?>
                                </div>
                            </div>

                            <!-- Qualifications -->
                            <?php if (!empty($job['QUALIFICATION'])): ?>
                            <div class="info-card">
                                <div class="info-card-header">
                                    <div class="info-card-icon">
                                        <i class="lni lni-graduation"></i>
                                    </div>
                                    <h3 class="info-card-title">Qualifications</h3>
                                </div>
                                <div style="line-height: 1.8;">
                                    <?php echo nl2br(htmlspecialchars($job['QUALIFICATION'])); ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Screening Questions -->
                            <div class="info-card">
                                <div class="info-card-header">
                                    <div class="info-card-icon">
                                        <i class="lni lni-question-circle"></i>
                                    </div>
                                    <h3 class="info-card-title">Screening Questions</h3>
                                </div>

                                <?php if (mysqli_num_rows($questionsResult) > 0): ?>
                                <?php while ($question = mysqli_fetch_assoc($questionsResult)): ?>
                                <div class="question-item">
                                    <div class="question-text">
                                        <?php echo htmlspecialchars($question['QUESTION']); ?>
                                    </div>
                                    <div class="question-meta">
                                        <span class="question-type">
                                            <?php echo ucwords(str_replace('_', ' ', $question['QUESTIONTYPE'])); ?>
                                        </span>
                                        <?php if ($question['ISREQUIRED']): ?>
                                        <span><i class="lni lni-checkmark"></i> Required</span>
                                        <?php endif; ?>
                                        <span><i class="lni lni-star"></i> <?php echo $question['POINTS']; ?>
                                            point(s)</span>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                                <?php else: ?>
                                <div class="empty-state">
                                    <i class="lni lni-question-circle"></i>
                                    <p>No screening questions set for this job.</p>
                                    <a href="dashboard-screening-questions.php?jobid=<?php echo $jobId; ?>"
                                        class="btn btn-sm btn-outline-secondary pt-3">
                                        <i class="lni lni-plus"></i> Add Screening Questions
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-lg-4">
                            <!-- Quick Actions -->
                            <div class="info-card">
                                <div class="info-card-header">
                                    <div class="info-card-icon">
                                        <i class="lni lni-cog"></i>
                                    </div>
                                    <h3 class="info-card-title">Quick Actions</h3>
                                </div>

                                <div class="action-buttons">
                                    <a href="dashboard-manage-applications.php?jobid=<?php echo $jobId; ?>"
                                        class="btn-action btn-primary">
                                        <i class="lni lni-users"></i> View Applications
                                        (<?php echo $appStats['total_applications']; ?>)
                                    </a>

                                    <a href="dashboard-jobs-edit.php?id=<?php echo $jobId; ?>"
                                        class="btn-action btn-success bg-success">
                                        <i class="lni lni-pencil"></i> Edit Job
                                    </a>

                                    <a href="dashboard-screening-questions.php?jobid=<?php echo $jobId; ?>"
                                        class="btn-action btn-warning bg-warning">
                                        <i class="lni lni-question-circle"></i> Manage Screening
                                    </a>

                                    <a href="<?php echo $path?>job-detail.php?jobid=<?php echo $jobId; ?>"
                                        target="_blank" class="btn-action btn-primary">
                                        <i class="lni lni-eye"></i> View Public Page
                                    </a>

                                    <a href="dashboard-manage-jobs.php?type=delete&id=<?php echo $jobId; ?>"
                                        class="btn-action btn-danger bg-danger"
                                        onclick="return confirm('Are you sure you want to delete this job? This will also delete all applications and screening questions.');">
                                        <i class="lni lni-trash-can"></i> Delete Job
                                    </a>
                                </div>
                            </div>

                            <!-- Recent Applications -->
                            <div class="info-card">
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
                                        <?php echo htmlspecialchars($app['FNAME'] . ' ' . $app['OTHERNAMES']); ?>
                                    </div>
                                    <div class="applicant-info">
                                        <span><i class="lni lni-envelope"></i>
                                            <?php echo htmlspecialchars($app['EMAILADDRESS']); ?></span>
                                        <?php if ($app['DEGREE']): ?>
                                        <span><i class="lni lni-graduation"></i>
                                            <?php echo htmlspecialchars($app['DEGREE']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="lni lni-calendar"></i>
                                            <?php echo timeago($app['DATEAPPLIED']); ?>
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
                                    <a href="dashboard-manage-applications.php?jobid=<?php echo $jobId; ?>"
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
                            <div class="info-card">
                                <div class="info-card-header">
                                    <div class="info-card-icon">
                                        <i class="fas fa-building"></i>
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
                                        <a
                                            href="mailto:<?php echo htmlspecialchars($job['COMPANYEMAIL']); ?>"><?php echo htmlspecialchars($job['COMPANYEMAIL']); ?></a>
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
                                            target="_blank"><?php echo htmlspecialchars($job['COMPANYWEBSITE']); ?></a>
                                    </div>
                                </div>
                                <?php endif; ?>
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
</body>

</html>