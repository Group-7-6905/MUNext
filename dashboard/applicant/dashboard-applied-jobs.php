<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';
unset($result); // Clear any $result from phpcode.php

// Security: Ensure user is logged in as applicant
if (!isset($session_id) || empty($session_id)) {
    header("Location: login.php");
    exit();
}

$applicantId = $session_id;

// ==================== HANDLE DELETE APPLICATION ====================
if (isset($_GET['type']) && $_GET['type'] == 'delete' && isset($_GET['appid'])) {
    $appId = (int)$_GET['appid'];
    
    // Verify application belongs to this applicant
    $verifyQuery = "SELECT ID FROM tbljobapplication WHERE ID = ? AND APPLICANTID = ?";
    $stmtVerify = mysqli_prepare($con, $verifyQuery);
    mysqli_stmt_bind_param($stmtVerify, "ii", $appId, $applicantId);
    mysqli_stmt_execute($stmtVerify);
    $verifyResult = mysqli_stmt_get_result($stmtVerify);
    
    if (mysqli_num_rows($verifyResult) > 0) {
        // Delete screening answers first (cascading delete)
        $deleteAnswers = "DELETE FROM tblscreeninganswers WHERE APPLICATIONID = ?";
        $stmtDelAns = mysqli_prepare($con, $deleteAnswers);
        mysqli_stmt_bind_param($stmtDelAns, "i", $appId);
        mysqli_stmt_execute($stmtDelAns);
        
        // Delete application
        $deleteApp = "DELETE FROM tbljobapplication WHERE ID = ?";
        $stmtDelApp = mysqli_prepare($con, $deleteApp);
        mysqli_stmt_bind_param($stmtDelApp, "i", $appId);
        
        if (mysqli_stmt_execute($stmtDelApp)) {
            $_SESSION['success_msg'] = "Application withdrawn successfully!";
        } else {
            $_SESSION['error_msg'] = "Failed to withdraw application.";
        }
    } else {
        $_SESSION['error_msg'] = "Invalid application or unauthorized action.";
    }
    
    header("Location: dashboard-applied-jobs.php");
    exit();
}

// ==================== PAGINATION SETUP ====================
$recordsPerPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

// ==================== FILTER SETUP ====================
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'recent';

// ==================== BUILD WHERE CLAUSE ====================
$whereConditions = ["ja.APPLICANTID = ?"];
$params = [$applicantId];
$types = "i";

if (!empty($statusFilter)) {
    $whereConditions[] = "ja.APPLICATIONSTATUS = ?";
    $params[] = $statusFilter;
    $types .= "s";
}

if (!empty($searchTerm)) {
    $whereConditions[] = "(j.JOBTITLE LIKE ? OR c.COMPANYNAME LIKE ?)";
    $searchParam = "%$searchTerm%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= "ss";
}

$whereClause = implode(" AND ", $whereConditions);

// ==================== BUILD ORDER BY ====================
switch($sortBy) {
    case 'oldest':
        $orderBy = "ORDER BY ja.DATEAPPLIED ASC";
        break;
    case 'company':
        $orderBy = "ORDER BY c.COMPANYNAME ASC";
        break;
    case 'status':
        $orderBy = "ORDER BY ja.APPLICATIONSTATUS ASC, ja.DATEAPPLIED DESC";
        break;
    default: // recent
        $orderBy = "ORDER BY ja.DATEAPPLIED DESC";
}

// ==================== COUNT TOTAL RECORDS ====================
$countQuery = "SELECT COUNT(*) as total 
               FROM tbljobapplication ja
               INNER JOIN tbljob j ON ja.JOBID = j.JOBID
               INNER JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
               WHERE $whereClause";

$stmtCount = mysqli_prepare($con, $countQuery);
mysqli_stmt_bind_param($stmtCount, $types, ...$params);
mysqli_stmt_execute($stmtCount);
$countResult = mysqli_stmt_get_result($stmtCount);
$totalRecords = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

// ==================== GET STATISTICS ====================
$statsQuery = "SELECT 
    COUNT(*) as total_applications,
    SUM(CASE WHEN ja.APPLICATIONSTATUS = 'Pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN ja.APPLICATIONSTATUS = 'Reviewed' THEN 1 ELSE 0 END) as reviewed,
    SUM(CASE WHEN ja.APPLICATIONSTATUS = 'Approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN ja.APPLICATIONSTATUS = 'Rejected' THEN 1 ELSE 0 END) as rejected,
    SUM(CASE WHEN ja.APPLICATIONSTATUS = 'Ineligible' THEN 1 ELSE 0 END) as ineligible,
    SUM(CASE WHEN ja.APPLICATIONSTATUS = 'Hire' THEN 1 ELSE 0 END) as hired
FROM tbljobapplication ja
WHERE ja.APPLICANTID = ?";

$stmtStats = mysqli_prepare($con, $statsQuery);
mysqli_stmt_bind_param($stmtStats, "i", $applicantId);
mysqli_stmt_execute($stmtStats);
$statsResult = mysqli_stmt_get_result($stmtStats);
$stats = mysqli_fetch_assoc($statsResult);

// ==================== FETCH APPLICATIONS ====================
$applicationsQuery = "SELECT 
    ja.ID,
    ja.JOBID,
    ja.APPLICANTID,
    ja.DATEAPPLIED,
    ja.APPLICATIONSTATUS,
    ja.SCREENINGSCORE,
    ja.SCREENPASSED,
    ja.COVERLETTER,
    ja.RESUME,
    j.JOBTITLE, 
    j.JOBTYPE, 
    j.SALARY, 
    j.WORKPLACE_POLICY, 
    j.COMPANYID,
    c.COMPANYNAME, 
    c.COMPANYLOGO, 
    c.USERID, 
    c.COMPANYCITY, 
    c.COMPANYCOUNTRY,
    jsc.SUBCATEGORY
FROM tbljobapplication ja
INNER JOIN tbljob j ON ja.JOBID = j.JOBID
INNER JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
LEFT JOIN tbljobsubcategory jsc ON j.JOBCATEGORYID = jsc.ID
WHERE $whereClause
$orderBy
LIMIT ? OFFSET ?";

$params[] = $recordsPerPage;
$params[] = $offset;
$types .= "ii";

$stmt = mysqli_prepare($con, $applicationsQuery);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$applications = mysqli_stmt_get_result($stmt);

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
    <title>My Applied Jobs - MUNext</title>
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
                                <i class="lni lni-briefcase"></i> My Applied Jobs
                            </h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item text-muted"><a href="dashboard.php">Home</a></li>
                                    <li class="breadcrumb-item text-muted"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="#" class="theme-cl">Applied Jobs</a></li>
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
                            <span class="stat-number"><?php echo number_format($stats['total_applications']); ?></span>
                            <span class="stat-label">Total Applied</span>
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
                        <div class="stat-card hired">
                            <span class="stat-number"><?php echo number_format($stats['hired']); ?></span>
                            <span class="stat-label">Hired</span>
                        </div>
                        <div class="stat-card rejected">
                            <span
                                class="stat-number"><?php echo number_format($stats['rejected'] + $stats['ineligible']); ?></span>
                            <span class="stat-label">Rejected</span>
                        </div>
                    </div>

                    <!-- Filter Section -->
                    <div class="filter-section">
                        <form method="GET" action="">
                            <div class="row align-items-center">
                                <div class="col-lg-4 col-md-6 mb-3 mb-lg-0">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search by job title or company..."
                                        value="<?php echo htmlspecialchars($searchTerm); ?>">
                                </div>

                                <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                                    <select name="status" class="form-control">
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
                                            Hired</option>
                                        <option value="Rejected"
                                            <?php echo $statusFilter == 'Rejected' ? 'selected' : ''; ?>>Rejected
                                        </option>
                                        <option value="Ineligible"
                                            <?php echo $statusFilter == 'Ineligible' ? 'selected' : ''; ?>>Ineligible
                                        </option>
                                    </select>
                                </div>

                                <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                                    <select name="sort" class="form-control">
                                        <option value="recent" <?php echo $sortBy == 'recent' ? 'selected' : ''; ?>>
                                            Recently Applied</option>
                                        <option value="oldest" <?php echo $sortBy == 'oldest' ? 'selected' : ''; ?>>
                                            Oldest First</option>
                                        <option value="company" <?php echo $sortBy == 'company' ? 'selected' : ''; ?>>
                                            Company A-Z</option>
                                        <option value="status" <?php echo $sortBy == 'status' ? 'selected' : ''; ?>>By
                                            Status</option>
                                    </select>
                                </div>

                                <div class="col-lg-2 col-md-6">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn theme-bg text-white flex-fill">
                                            <i class="lni lni-search"></i> Filter
                                        </button>
                                        <a href="dashboard-applied-jobs.php" class="btn btn-secondary">
                                            <i class="lni lni-reload"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Applications Table -->
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12">
                            <?php if (mysqli_num_rows($applications) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="width: 40%;">Job Details</th>
                                            <th scope="col" class="text-center">Status</th>
                                            <th scope="col" class="text-center">Screening</th>
                                            <th scope="col" class="text-center">Applied</th>
                                            <th scope="col" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($app = mysqli_fetch_assoc($applications)): 
                                            $applicationId = $app['ID'];
                                            $jobId = $app['JOBID'];
                                            $status = $app['APPLICATIONSTATUS'];
                                            $dateApplied = $app['DATEAPPLIED'];
                                            $screeningScore = $app['SCREENINGSCORE'] ?? 0;
                                            $employerId = $app['USERID'];

                                            // Get max score
                                            $maxScoreQuery = "SELECT SUM(POINTS) as max_score 
                                                             FROM tblscreeningquestions WHERE JOBID = ?";
                                            $stmtMax = mysqli_prepare($con, $maxScoreQuery);
                                            mysqli_stmt_bind_param($stmtMax, "i", $jobId);
                                            mysqli_stmt_execute($stmtMax);
                                            $maxResult = mysqli_stmt_get_result($stmtMax);
                                            $maxData = mysqli_fetch_assoc($maxResult);
                                            $maxScore = $maxData['max_score'] ?? 0;

                                            // Status badge class
                                            $statusClass = 'status-pending';
                                            switch(strtolower($status)) {
                                                case 'reviewed': $statusClass = 'status-reviewed'; break;
                                                case 'approved': $statusClass = 'status-approved'; break;
                                                case 'hire': $statusClass = 'status-hired'; break;
                                                case 'rejected':
                                                case 'ineligible': $statusClass = 'status-rejected'; break;
                                            }

                                            // Screening score class
                                            $scoreClass = 'score-medium';
                                            if ($maxScore > 0) {
                                                $percentage = ($screeningScore / $maxScore) * 100;
                                                if ($percentage >= 70) $scoreClass = 'score-high';
                                                elseif ($percentage < 50) $scoreClass = 'score-low';
                                            }
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="text-center mr-3">
                                                        <?php if (!empty($app['COMPANYLOGO'])): ?>
                                                        <img src="<?php echo $path.htmlspecialchars($app['COMPANYLOGO']); ?>"
                                                            alt="Company Logo" class="company-logo"
                                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        <?php endif; ?>
                                                        <div class="company-logo-placeholder"
                                                            style="<?php echo !empty($app['COMPANYLOGO']) ? 'display:none;' : ''; ?>">
                                                            <?php echo strtoupper(substr($app['COMPANYNAME'], 0, 1)); ?>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1 ft-medium">
                                                            <a href="<?php echo $path?>job-detail.php?jobid=<?php echo $jobId; ?>"
                                                                class="text-dark">
                                                                <?php echo htmlspecialchars($app['JOBTITLE']); ?>
                                                            </a>
                                                        </h6>
                                                        <div class="mb-1">
                                                            <span class="text-muted small">
                                                                <i class="lni lni-building mr-1"></i>
                                                                <?php echo htmlspecialchars($app['COMPANYNAME']); ?>
                                                            </span>
                                                        </div>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            <?php if (!empty($app['COMPANYCITY']) || !empty($app['COMPANYCOUNTRY'])): ?>
                                                            <span class="text-muted small">
                                                                <i class="lni lni-map-marker mr-1"></i>
                                                                <?php echo htmlspecialchars(trim(($app['COMPANYCITY'] ?? '') . ', ' . ($app['COMPANYCOUNTRY'] ?? ''), ', ')); ?>
                                                            </span>
                                                            <?php endif; ?>
                                                            <?php if ($app['JOBTYPE']): ?>
                                                            <span class="job-type-badge bg-light text-dark">
                                                                <i class="lni lni-briefcase"></i>
                                                                <?php echo htmlspecialchars($app['JOBTYPE']); ?>
                                                            </span>
                                                            <?php endif; ?>
                                                            <?php if ($app['SALARY']): ?>
                                                            <span class="job-type-badge bg-light text-success">
                                                                <i class="lni lni-dollar"></i>
                                                                <?php echo htmlspecialchars($app['SALARY']); ?>
                                                            </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="text-center">
                                                <span class="status-badge <?php echo $statusClass; ?>">
                                                    <?php echo htmlspecialchars($status); ?>
                                                </span>
                                            </td>

                                            <td class="text-center">
                                                <?php if ($maxScore > 0): ?>
                                                <span class="screening-score <?php echo $scoreClass; ?>">
                                                    <i class="lni lni-star"></i>
                                                    <?php echo $screeningScore . '/' . $maxScore; ?>
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    <?php echo round(($screeningScore / $maxScore) * 100); ?>%
                                                </small>
                                                <?php else: ?>
                                                <span class="screening-score score-none">N/A</span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="text-center">
                                                <div class="mb-1">
                                                    <strong><?php echo date('M d, Y', strtotime($dateApplied)); ?></strong>
                                                </div>
                                                <small class="text-muted">
                                                    <?php echo timeago($dateApplied); ?>
                                                </small>
                                            </td>

                                            <td class="text-center">
                                                <div class="d-flex justify-content-center flex-wrap">
                                                    <a href="<?php echo $path?>job-detail.php?jobid=<?php echo $jobId; ?>"
                                                        class="action-btn text-info bg-light-info" title="View Job">
                                                        <i class="lni lni-eye"></i>
                                                    </a>

                                                    <?php if ($app['COVERLETTER']): ?>
                                                    <button type="button"
                                                        class="action-btn text-primary bg-light border-0"
                                                        title="View Cover Letter"
                                                        data-cover-letter="<?php echo htmlspecialchars($app['COVERLETTER'], ENT_QUOTES, 'UTF-8'); ?>"
                                                        onclick="viewCoverLetter(this)">
                                                        <i class="lni lni-files"></i>
                                                    </button>
                                                    <?php endif; ?>

                                                    <a href="dashboard-messages.php?empid=<?php echo $employerId; ?>"
                                                        class="action-btn text-warning bg-light-warning"
                                                        title="Message Employer">
                                                        <i class="lni lni-comments"></i>
                                                    </a>

                                                    <?php if (in_array(strtolower($status), ['pending', 'reviewed'])): ?>
                                                    <a href="?type=delete&appid=<?php echo $applicationId; ?>"
                                                        class="action-btn text-danger bg-light-danger"
                                                        title="Withdraw Application"
                                                        onclick="return confirm('Are you sure you want to withdraw this application?');">
                                                        <i class="lni lni-trash-can"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if ($totalPages > 1): ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="?page=<?php echo $page - 1; ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?><?php echo !empty($statusFilter) ? '&status=' . $statusFilter : ''; ?><?php echo !empty($sortBy) ? '&sort=' . $sortBy : ''; ?>">
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
                                                href="?page=<?php echo $i; ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?><?php echo !empty($statusFilter) ? '&status=' . $statusFilter : ''; ?><?php echo !empty($sortBy) ? '&sort=' . $sortBy : ''; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                        <?php endfor; ?>

                                        <?php if ($page < $totalPages): ?>
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="?page=<?php echo $page + 1; ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?><?php echo !empty($statusFilter) ? '&status=' . $statusFilter : ''; ?><?php echo !empty($sortBy) ? '&sort=' . $sortBy : ''; ?>">
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
                                <h4 class="mt-3">No Applications Found</h4>
                                <p class="text-muted">
                                    <?php if (!empty($searchTerm) || !empty($statusFilter)): ?>
                                    No applications match your search criteria. Try adjusting your filters.
                                    <?php else: ?>
                                    You haven't applied to any jobs yet. Start browsing and apply to jobs that match
                                    your skills!
                                    <?php endif; ?>
                                </p>
                                <?php if (!empty($searchTerm) || !empty($statusFilter)): ?>
                                <a href="dashboard-applied-jobs.php" class="btn btn-outline-secondary mt-4 pt-4">
                                    <i class="lni lni-reload"></i> Clear Filters
                                </a>
                                <?php else: ?>
                                <a href="browse-jobs.php" class="btn btn-outline-secondary mt-4 pt-4">
                                    <i class="lni lni-search"></i> Browse Jobs
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
        <div class="modal fade" id="coverLetterModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header theme-bg">
                        <h5 class="modal-title text-white">
                            <i class="lni lni-files"></i> Cover Letter
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
                        <div id="coverLetterContent" style="white-space: pre-wrap; line-height: 1.8;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
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
    function viewCoverLetter(element) {
        // Get the cover letter content from data attribute
        var content = $(element).data('cover-letter');

        // Set the content and show modal
        $('#coverLetterContent').text(content);
        $('#coverLetterModal').modal('show');
    }

    $(document).ready(function() {
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Ensure Bootstrap modal is properly initialized
        $('#coverLetterModal').modal({
            show: false
        });
    });
    </script>
</body>

</html>