<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';
unset($result); // Clear any $result from phpcode.php


$employerId = $session_id;
$jobId = isset($_GET['jobid']) ? (int)$_GET['jobid'] : 0;

// Get job details if jobid is provided
$job = null;
if ($jobId > 0) {
    $jobQuery = "SELECT j.*, c.COMPANYNAME 
                 FROM tbljob j 
                 INNER JOIN tblcompany c ON j.COMPANYID = c.COMPANYID 
                 WHERE j.JOBID = ? AND j.EMPLOYERID = ?";
    $stmt = mysqli_prepare($con, $jobQuery);
    mysqli_stmt_bind_param($stmt, "ii", $jobId, $employerId);
    mysqli_stmt_execute($stmt);
    $jobResult = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($jobResult) > 0) {
        $job = mysqli_fetch_assoc($jobResult);
    } else {
        $_SESSION['error_msg'] = "Job not found or you don't have permission to view it.";
        header("Location: dashboard-manage-jobs.php");
        exit();
    }
}

// ==================== HANDLE STATUS UPDATE ====================
if (isset($_POST['applicationstatus']) && isset($_POST['jobapplicationid'])) {
    $newStatus = $_POST['applicationstatus'];
    $applicationId = (int)$_POST['jobapplicationid'];
    
    // Verify ownership
    $verifyQuery = "SELECT ja.ID FROM tbljobapplication ja
                    INNER JOIN tbljob j ON ja.JOBID = j.JOBID
                    WHERE ja.ID = ? AND j.EMPLOYERID = ?";
    $stmtVerify = mysqli_prepare($con, $verifyQuery);
    mysqli_stmt_bind_param($stmtVerify, "ii", $applicationId, $employerId);
    mysqli_stmt_execute($stmtVerify);
    
    if (mysqli_num_rows(mysqli_stmt_get_result($stmtVerify)) > 0) {
        $updateQuery = "UPDATE tbljobapplication SET APPLICATIONSTATUS = ? WHERE ID = ?";
        $stmtUpdate = mysqli_prepare($con, $updateQuery);
        mysqli_stmt_bind_param($stmtUpdate, "si", $newStatus, $applicationId);
        
        if (mysqli_stmt_execute($stmtUpdate)) {
            $_SESSION['success_msg'] = "Application status updated to $newStatus successfully!";
        } else {
            $_SESSION['error_msg'] = "Failed to update status.";
        }
    }
    
    header("Location: " . $_SERVER['PHP_SELF'] . ($jobId > 0 ? "?jobid=$jobId" : ""));
    exit();
}


// ==================== HANDLE BOOKMARK DELETE ====================
if (isset($_GET['type']) && $_GET['type'] == 'delete' && isset($_GET['bookmarkid'])) {
    $bookmarkId = (int)$_GET['bookmarkid'];
    
    $deleteQuery = "DELETE FROM tblbookmarkresume WHERE ID = ? AND USERID = ?";
    $stmtDel = mysqli_prepare($con, $deleteQuery);
    mysqli_stmt_bind_param($stmtDel, "ii", $bookmarkId, $employerId);
    
    if (mysqli_stmt_execute($stmtDel)) {
        $_SESSION['success_msg'] = "Bookmark removed successfully!";
    }
    
    $returnJobId = isset($_GET['jobid']) ? (int)$_GET['jobid'] : 0;
    header("Location: " . $_SERVER['PHP_SELF'] . ($returnJobId > 0 ? "?jobid=$returnJobId" : ""));
    exit();
}

// ==================== PAGINATION SETUP ====================
$recordsPerPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

// ==================== FILTER SETUP ====================
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'score'; // score, recent, older

// ==================== BUILD WHERE CLAUSE ====================
$whereConditions = ["j.EMPLOYERID = ?"];
$params = [$employerId];
$types = "i";

if ($jobId > 0) {
    $whereConditions[] = "ja.JOBID = ?";
    $params[] = $jobId;
    $types .= "i";
}

if (!empty($statusFilter)) {
    $whereConditions[] = "ja.APPLICATIONSTATUS = ?";
    $params[] = $statusFilter;
    $types .= "s";
}

if (!empty($searchTerm)) {
    $whereConditions[] = "(u.FNAME LIKE ? OR u.ONAME LIKE ? OR j.JOBTITLE LIKE ?)";
    $searchParam = "%$searchTerm%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= "sss";
}

$whereClause = implode(" AND ", $whereConditions);

// ==================== BUILD ORDER BY ====================
switch($sortBy) {
    case 'recent':
        $orderBy = "ORDER BY ja.DATEAPPLIED DESC";
        break;
    case 'older':
        $orderBy = "ORDER BY ja.DATEAPPLIED ASC";
        break;
    default: // score
        $orderBy = "ORDER BY ja.SCREENINGSCORE DESC, ja.DATEAPPLIED DESC";
}

// ==================== COUNT TOTAL RECORDS ====================
$countQuery = "SELECT COUNT(*) as total 
               FROM tbljobapplication ja
               INNER JOIN tbljob j ON ja.JOBID = j.JOBID
               INNER JOIN tblusers u ON ja.APPLICANTID = u.USERID
               WHERE $whereClause";
$stmtCount = mysqli_prepare($con, $countQuery);
mysqli_stmt_bind_param($stmtCount, $types, ...$params);
mysqli_stmt_execute($stmtCount);
$countResult = mysqli_stmt_get_result($stmtCount);
$totalRecords = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

// ==================== FETCH APPLICATIONS ====================
$applicationsQuery = "SELECT 
    ja.*,
    u.FNAME, u.ONAME, u.EMAIL,
    ap.COUNTRY, ap.CITY, ap.CONTACTNO, ap.APPLICANTPHOTO, ap.SKILLS, ap.DEGREE,
    j.JOBTITLE, j.JOBID
FROM tbljobapplication ja
INNER JOIN tblapplicants ap ON ja.APPLICANTID = ap.USERID
INNER JOIN tblusers u ON ja.APPLICANTID = u.USERID
INNER JOIN tbljob j ON ja.JOBID = j.JOBID
WHERE $whereClause
$orderBy
LIMIT ? OFFSET ?";

$params[] = $recordsPerPage;
$params[] = $offset;
$types .= "ii";

$stmt = mysqli_prepare($con, $applicationsQuery);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$applicationsResult = mysqli_stmt_get_result($stmt);

// ==================== STATISTICS ====================
$statsQuery = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN ja.APPLICATIONSTATUS = 'Pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN ja.APPLICATIONSTATUS = 'Reviewed' THEN 1 ELSE 0 END) as reviewed,
    SUM(CASE WHEN ja.APPLICATIONSTATUS = 'Approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN ja.APPLICATIONSTATUS = 'Rejected' THEN 1 ELSE 0 END) as rejected,
    SUM(CASE WHEN ja.APPLICATIONSTATUS = 'Hire' THEN 1 ELSE 0 END) as hired
FROM tbljobapplication ja
INNER JOIN tbljob j ON ja.JOBID = j.JOBID
WHERE j.EMPLOYERID = ?" . ($jobId > 0 ? " AND ja.JOBID = $jobId" : "");

$stmtStats = mysqli_prepare($con, $statsQuery);
mysqli_stmt_bind_param($stmtStats, "i", $employerId);
mysqli_stmt_execute($stmtStats);
$statsResult = mysqli_stmt_get_result($stmtStats);
$stats = mysqli_fetch_assoc($statsResult);

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
?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Applications - Employer Dashboard | MUNext</title>
    <link href="assets/css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.lineicons.com/3.0/lineicons.css">

    <link href="assets/css/custom-style.css" rel="stylesheet">


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
                                <i class="lni lni-users"></i>
                                <?php if ($job): ?>
                                Applications for "<?php echo htmlspecialchars($job['JOBTITLE']); ?>"
                                <?php else: ?>
                                Manage Applications
                                <?php endif; ?>
                            </h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item text-muted"><a href="dashboard.php">Home</a></li>
                                    <?php if ($job): ?>
                                    <li class="breadcrumb-item text-muted"><a href="dashboard-manage-jobs.php">Manage
                                            Jobs</a></li>
                                    <?php endif; ?>
                                    <li class="breadcrumb-item"><a href="#" class="theme-cl">Applications</a></li>
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

                    <!-- Statistics Cards -->
                    <div class="stats-grid">
                        <div class="stat-card total">
                            <span class="stat-number"><?php echo number_format($stats['total']); ?></span>
                            <span class="stat-label">Total</span>
                        </div>
                        <div class="stat-card pending">
                            <span class="stat-number"><?php echo number_format($stats['pending']); ?></span>
                            <span class="stat-label">Pending</span>
                        </div>
                        <div class="stat-card reviewed">
                            <span class="stat-number"><?php echo number_format($stats['reviewed']); ?></span>
                            <span class="stat-label">Reviewed</span>
                        </div>
                        <div class="stat-card approved">
                            <span class="stat-number"><?php echo number_format($stats['approved']); ?></span>
                            <span class="stat-label">Approved</span>
                        </div>
                        <div class="stat-card rejected">
                            <span class="stat-number"><?php echo number_format($stats['rejected']); ?></span>
                            <span class="stat-label">Rejected</span>
                        </div>
                        <div class="stat-card hired">
                            <span class="stat-number"><?php echo number_format($stats['hired']); ?></span>
                            <span class="stat-label">Hired</span>
                        </div>
                    </div>

                    <!-- Filter Section -->
                    <div class="filter-section">
                        <form method="GET" action="">
                            <?php if ($jobId > 0): ?>
                            <input type="hidden" name="jobid" value="<?php echo $jobId; ?>">
                            <?php endif; ?>

                            <div class="row align-items-center">
                                <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search by name or job..."
                                        value="<?php echo htmlspecialchars($searchTerm); ?>">
                                </div>

                                <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                                    <select name="status" class="form-control custom-select">
                                        <option value="">All Status</option>
                                        <option value="Pending"
                                            <?php echo $statusFilter == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Reviewed"
                                            <?php echo $statusFilter == 'Reviewed' ? 'selected' : ''; ?>>Reviewed
                                        </option>
                                        <option value="Approved"
                                            <?php echo $statusFilter == 'Approved' ? 'selected' : ''; ?>>Approved
                                        </option>
                                        <option value="Hire" <?php echo $statusFilter == 'Hire' ? 'selected' : ''; ?>>
                                            Hire</option>
                                        <option value="Rejected"
                                            <?php echo $statusFilter == 'Rejected' ? 'selected' : ''; ?>>Rejected
                                        </option>
                                        <option value="Ineligible"
                                            <?php echo $statusFilter == 'Ineligible' ? 'selected' : ''; ?>>Ineligible
                                        </option>
                                    </select>
                                </div>

                                <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                                    <select name="sort" class="form-control custom-select">
                                        <option value="score" <?php echo $sortBy == 'score' ? 'selected' : ''; ?>>Sort
                                            by Score</option>
                                        <option value="recent" <?php echo $sortBy == 'recent' ? 'selected' : ''; ?>>Most
                                            Recent</option>
                                        <option value="older" <?php echo $sortBy == 'older' ? 'selected' : ''; ?>>Oldest
                                            First</option>
                                    </select>
                                </div>

                                <div class="col-lg-3 col-md-6">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary flex-fill">
                                            <i class="lni lni-search"></i> Filter
                                        </button>
                                        <a href="<?php echo $_SERVER['PHP_SELF'] . ($jobId > 0 ? '?jobid=' . $jobId : ''); ?>"
                                            class="btn btn-secondary">
                                            <i class="lni lni-reload"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Applications List -->
                    <div class="row">
                        <div class="col-xl-12">
                            <?php if (mysqli_num_rows($applicationsResult) > 0): ?>
                            <?php while ($app = mysqli_fetch_assoc($applicationsResult)): 
                                    $applicationId = $app['ID'];
                                    $applicantId = $app['APPLICANTID'];
                                    $appJobId = $app['JOBID'];
                                    $status = $app['APPLICATIONSTATUS'];
                                    $screeningScore = $app['SCREENINGSCORE'] ?? 0;
                                    
                                    // Get max score for this job
                                    $maxScoreQuery = "SELECT SUM(POINTS) as max_score FROM tblscreeningquestions WHERE JOBID = ?";
                                    $stmtMax = mysqli_prepare($con, $maxScoreQuery);
                                    mysqli_stmt_bind_param($stmtMax, "i", $appJobId);
                                    mysqli_stmt_execute($stmtMax);
                                    $maxResult = mysqli_stmt_get_result($stmtMax);
                                    $maxData = mysqli_fetch_assoc($maxResult);
                                    $maxScore = $maxData['max_score'] ?? 0;
                                    
                                    // Calculate score percentage
                                    $scorePercentage = 0;
                                    $scoreBadgeClass = 'score-none';
                                    $scoreBadgeText = 'No Score';
                                    
                                    if ($maxScore > 0) {
                                        $scorePercentage = ($screeningScore / $maxScore) * 100;
                                        $scoreBadgeText = $screeningScore . '/' . $maxScore . ' (' . round($scorePercentage) . '%)';
                                        
                                        if ($scorePercentage >= 70) {
                                            $scoreBadgeClass = 'score-high';
                                        } elseif ($scorePercentage >= 50) {
                                            $scoreBadgeClass = 'score-medium';
                                        } else {
                                            $scoreBadgeClass = 'score-low';
                                        }
                                    }
                                    
                                    // Check bookmark
                                    $bookmarkQuery = "SELECT ID FROM tblbookmarkresume WHERE USERID = ? AND JOBAPPLICATIONID = ?";
                                    $stmtBk = mysqli_prepare($con, $bookmarkQuery);
                                    mysqli_stmt_bind_param($stmtBk, "ii", $employerId, $applicationId);
                                    mysqli_stmt_execute($stmtBk);
                                    $bkResult = mysqli_stmt_get_result($stmtBk);
                                    $isBookmarked = mysqli_num_rows($bkResult) > 0;
                                    $bookmarkId = $isBookmarked ? mysqli_fetch_assoc($bkResult)['ID'] : 0;
                                    
                                    // Photo
                                    $applicantPhoto = !empty($app['APPLICANTPHOTO']) ? '../../' . $app['APPLICANTPHOTO'] : 'assets/img/avatar-default.svg';
                                    
                                    // Status badge class
                                    $statusBadgeClass = 'status-' . strtolower($status);
                                ?>

                            <!-- Application Card -->
                            <div class="application-card">
                                <div class="applicant-header">
                                    <img src="<?php echo $applicantPhoto; ?>" alt="Applicant Photo"
                                        class="applicant-photo" onerror="this.src='assets/img/avatar-default.svg'">

                                    <div class="applicant-info">
                                        <div class="applicant-name">
                                            <?php echo htmlspecialchars($app['FNAME'] . ' ' . ($app['ONAME'] ?? '')); ?>

                                            <!-- Screening Score -->
                                            <?php if ($maxScore > 0): ?>
                                            <span class="screening-score-badge <?php echo $scoreBadgeClass; ?>">
                                                <i class="lni lni-star"></i> <?php echo $scoreBadgeText; ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="applicant-meta">
                                            <?php if (!empty($app['CITY']) || !empty($app['COUNTRY'])): ?>
                                            <span>
                                                <i class="lni lni-map-marker"></i>
                                                <?php echo htmlspecialchars(trim(($app['CITY'] ?? '') . ', ' . ($app['COUNTRY'] ?? ''), ', ')); ?>
                                            </span>
                                            <?php endif; ?>

                                            <span>
                                                <i class="lni lni-briefcase"></i>
                                                <?php echo htmlspecialchars($app['JOBTITLE']); ?>
                                            </span>

                                            <?php if (!empty($app['DEGREE'])): ?>
                                            <span>
                                                <i class="lni lni-graduation"></i>
                                                <?php echo htmlspecialchars($app['DEGREE']); ?>
                                            </span>
                                            <?php endif; ?>

                                            <span>
                                                <i class="lni lni-calendar"></i>
                                                <?php echo timeago($app['DATEAPPLIED']); ?>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Status Badge -->
                                    <div>
                                        <span class="status-badge <?php echo $statusBadgeClass; ?>">
                                            <?php echo htmlspecialchars($status); ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="action-buttons">
                                    <!-- Change Status -->
                                    <form method="post" class="d-inline-block status-dropdown">
                                        <input type="hidden" name="jobapplicationid"
                                            value="<?php echo $applicationId; ?>">
                                        <select name="applicationstatus"
                                            onchange="if(confirm('Change status to ' + this.value + '?')) this.form.submit();">
                                            <option value="" disabled selected>Change Status</option>
                                            <option value="Pending">Pending</option>
                                            <option value="Reviewed">Reviewed</option>
                                            <option value="Approved">Approved</option>
                                            <option value="Hire">Hire</option>
                                            <option value="Rejected">Rejected</option>
                                            <option value="Ineligible">Ineligible</option>
                                        </select>
                                    </form>

                                    <!-- View Profile -->
                                    <a href="dashboard-candidate-detail.php?applicantid=<?php echo $applicantId; ?>&jobapplicationid=<?php echo $applicationId; ?>"
                                        class="btn-action btn-primary">
                                        <i class="lni lni-user"></i> View Profile
                                    </a>

                                    <!-- View Resume -->
                                    <?php if (!empty($app['RESUME'])): ?>
                                    <a href="view-resume.php?appid=<?php echo $applicationId; ?>" target="_blank"
                                        class="btn-action btn-info">
                                        <i class="lni lni-eye"></i> View Resume
                                    </a>
                                    <?php endif; ?>

                                    <!-- View Screening -->
                                    <?php if ($maxScore > 0): ?>
                                    <a href="view-screening-answers.php?appid=<?php echo $applicationId; ?>"
                                        class="btn-action btn-warning">
                                        <i class="lni lni-question-circle"></i> Screening
                                    </a>
                                    <?php endif; ?>

                                    <!-- Cover Letter -->
                                    <?php if (!empty($app['COVERLETTER'])): ?>
                                    <button type="button" class="btn-action btn-secondary open-cover-letter"
                                        data-appid="<?php echo $applicationId; ?>"
                                        data-name="<?php echo htmlspecialchars($app['FNAME'] . ' ' . ($app['ONAME'] ?? '')); ?>"
                                        data-email="<?php echo htmlspecialchars($app['EMAIL']); ?>"
                                        data-job="<?php echo htmlspecialchars($app['JOBTITLE']); ?>"
                                        data-letter="<?php echo htmlspecialchars($app['COVERLETTER']); ?>">
                                        <i class="lni lni-files"></i> Cover Letter
                                    </button>
                                    <?php endif; ?>

                                    <!-- Message -->
                                    <a href="dashboard-messages.php?userid=<?php echo $applicantId; ?>"
                                        class="btn-action btn-info">
                                        <i class="lni lni-comments"></i> Message
                                    </a>

                                    <!-- Bookmark -->
                                    <form method="post" class="d-inline-block">
                                        <input type="hidden" name="jobapplicationid"
                                            value="<?php echo $applicationId; ?>">
                                        <input type="hidden" name="jobID" value="<?php echo $appJobId; ?>">
                                        <input type="hidden" name="userID" value="<?php echo $applicantId; ?>">
                                        <input type="hidden" name="employer" value="<?php echo $session_id; ?>">


                                        <?php if ($isBookmarked): ?>
                                        <a href="?type=delete&bookmarkid=<?php echo $bookmarkId; ?>&jobid=<?php echo $appJobId; ?>"
                                            class="btn-action btn-warning">
                                            <i class="lni lni-heart-filled"></i> Saved
                                        </a>
                                        <?php else: ?>
                                        <button type="submit" name="save_resume" class="btn-action btn-outline">
                                            <i class="lni lni-heart"></i> Save
                                        </button>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            </div>

                            <?php endwhile; ?>

                            <!-- Pagination -->
                            <?php if ($totalPages > 1): ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="?page=<?php echo $page - 1; ?><?php echo $jobId > 0 ? '&jobid=' . $jobId : ''; ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?><?php echo !empty($statusFilter) ? '&status=' . $statusFilter : ''; ?><?php echo !empty($sortBy) ? '&sort=' . $sortBy : ''; ?>">
                                                <i class="lni lni-chevron-left"></i>
                                            </a>
                                        </li>
                                        <?php endif; ?>

                                        <?php
                                            $startPage = max(1, $page - 2);
                                            $endPage = min($totalPages, $page + 2);
                                            
                                            for ($i = $startPage; $i <= $endPage; $i++): ?>
                                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                            <a class="page-link"
                                                href="?page=<?php echo $i; ?><?php echo $jobId > 0 ? '&jobid=' . $jobId : ''; ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?><?php echo !empty($statusFilter) ? '&status=' . $statusFilter : ''; ?><?php echo !empty($sortBy) ? '&sort=' . $sortBy : ''; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                        <?php endfor; ?>

                                        <?php if ($page < $totalPages): ?>
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="?page=<?php echo $page + 1; ?><?php echo $jobId > 0 ? '&jobid=' . $jobId : ''; ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?><?php echo !empty($statusFilter) ? '&status=' . $statusFilter : ''; ?><?php echo !empty($sortBy) ? '&sort=' . $sortBy : ''; ?>">
                                                <i class="lni lni-chevron-right"></i>
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php else: ?>
                            <!-- Empty State -->
                            <div class="empty-state">
                                <i class="lni lni-inbox"></i>
                                <h4>No Applications Found</h4>
                                <p class="text-muted">
                                    <?php if (!empty($searchTerm) || !empty($statusFilter)): ?>
                                    No applications match your search criteria. Try adjusting your filters.
                                    <?php else: ?>
                                    Applications will appear here once candidates start applying.
                                    <?php endif; ?>
                                </p>
                                <?php if ($job && empty($searchTerm) && empty($statusFilter)): ?>
                                <a href="dashboard-screening-questions.php?jobid=<?php echo $jobId; ?>"
                                    class="btn btn-primary mt-3">
                                    <i class="lni lni-plus"></i> Add Screening Questions
                                </a>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php include 'footer.php' ?>
            </div>
        </div>

        <!-- Cover Letter Modal -->
        <div id="coverLetterModal" class="cover-letter-modal">
            <div class="cover-letter-content">
                <div class="cover-letter-header">
                    <h3><i class="lni lni-files"></i> Cover Letter</h3>
                    <span class="cover-letter-close">&times;</span>
                </div>
                <div class="cover-letter-body">
                    <div class="applicant-info-box">
                        <p><strong><i class="lni lni-user"></i> Applicant:</strong> <span
                                id="modal-applicant-name"></span></p>
                        <p><strong><i class="lni lni-envelope"></i> Email:</strong> <span
                                id="modal-applicant-email"></span></p>
                        <p><strong><i class="lni lni-briefcase"></i> Position:</strong> <span
                                id="modal-job-title"></span></p>
                    </div>
                    <div class="cover-letter-text" id="modal-cover-letter"></div>
                </div>
                <div class="cover-letter-footer">
                    <button class="btn btn-primary cover-letter-close small rounded">Close</button>
                </div>
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
        // Cover Letter Modal
        var modal = $('#coverLetterModal');

        // Open modal
        $('.open-cover-letter').click(function() {
            var name = $(this).data('name');
            var email = $(this).data('email');
            var job = $(this).data('job');
            var letter = $(this).data('letter');

            $('#modal-applicant-name').text(name);
            $('#modal-applicant-email').text(email);
            $('#modal-job-title').text(job);
            $('#modal-cover-letter').text(letter);

            modal.fadeIn(300);
        });

        // Close modal
        $('.cover-letter-close').click(function() {
            modal.fadeOut(300);
        });

        // Close when clicking outside
        $(window).click(function(event) {
            if (event.target.id === 'coverLetterModal') {
                modal.fadeOut(300);
            }
        });

        // Escape key closes modal
        $(document).keydown(function(e) {
            if (e.keyCode === 27 && modal.is(':visible')) {
                modal.fadeOut(300);
            }
        });

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
    </script>
</body>

</html>