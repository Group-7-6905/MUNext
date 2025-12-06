<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';
unset($result); // Clear any $result from phpcode.php


$applicantId = $session_id;

// ==================== HANDLE DELETE BOOKMARK ====================
if (isset($_GET['type']) && $_GET['type'] == 'delete' && isset($_GET['bookmarkedid'])) {
    $bookmarkId = (int)$_GET['bookmarkedid'];
    
    // Verify bookmark belongs to this applicant
    $verifyQuery = "SELECT ID FROM tblbookmarkjob WHERE ID = ? AND APPLICANTID = ?";
    $stmtVerify = mysqli_prepare($con, $verifyQuery);
    mysqli_stmt_bind_param($stmtVerify, "ii", $bookmarkId, $applicantId);
    mysqli_stmt_execute($stmtVerify);
    $verifyResult = mysqli_stmt_get_result($stmtVerify);
    
    if (mysqli_num_rows($verifyResult) > 0) {
        $deleteQuery = "DELETE FROM tblbookmarkjob WHERE ID = ?";
        $stmtDel = mysqli_prepare($con, $deleteQuery);
        mysqli_stmt_bind_param($stmtDel, "i", $bookmarkId);
        
        if (mysqli_stmt_execute($stmtDel)) {
            $_SESSION['success_msg'] = "Job removed from saved list successfully!";
        } else {
            $_SESSION['error_msg'] = "Failed to remove job from saved list.";
        }
    } else {
        $_SESSION['error_msg'] = "Invalid bookmark or unauthorized action.";
    }
    
    header("Location: dashboard-saved-jobs.php");
    exit();
}

// ==================== PAGINATION SETUP ====================
$recordsPerPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

// ==================== FILTER SETUP ====================
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$jobTypeFilter = isset($_GET['jobtype']) ? $_GET['jobtype'] : '';
$categoryFilter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'recent';

// ==================== BUILD WHERE CLAUSE ====================
$whereConditions = ["bj.APPLICANTID = ?"];
$params = [$applicantId];
$types = "i";

if (!empty($searchTerm)) {
    $whereConditions[] = "(j.JOBTITLE LIKE ? OR c.COMPANYNAME LIKE ? OR jsc.SUBCATEGORY LIKE ?)";
    $searchParam = "%$searchTerm%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= "sss";
}

if (!empty($jobTypeFilter)) {
    $whereConditions[] = "j.JOBTYPE = ?";
    $params[] = $jobTypeFilter;
    $types .= "s";
}

if ($categoryFilter > 0) {
    $whereConditions[] = "jsc.CATEGORYID = ?";
    $params[] = $categoryFilter;
    $types .= "i";
}

$whereClause = implode(" AND ", $whereConditions);

// ==================== BUILD ORDER BY ====================
switch($sortBy) {
    case 'oldest':
        $orderBy = "ORDER BY bj.DATETIME ASC";
        break;
    case 'title':
        $orderBy = "ORDER BY j.JOBTITLE ASC";
        break;
    case 'company':
        $orderBy = "ORDER BY c.COMPANYNAME ASC";
        break;
    case 'deadline':
        $orderBy = "ORDER BY j.DEADLINE ASC";
        break;
    default: // recent
        $orderBy = "ORDER BY bj.DATETIME DESC";
}

// ==================== GET APPLIED JOBS ====================
$appliedJobsQuery = "SELECT JOBID FROM tbljobapplication WHERE APPLICANTID = ?";
$stmtApplied = mysqli_prepare($con, $appliedJobsQuery);
mysqli_stmt_bind_param($stmtApplied, "i", $applicantId);
mysqli_stmt_execute($stmtApplied);
$appliedResult = mysqli_stmt_get_result($stmtApplied);
$appliedJobs = [];
while ($row = mysqli_fetch_assoc($appliedResult)) {
    $appliedJobs[] = $row['JOBID'];
}

// ==================== COUNT TOTAL RECORDS ====================
$countQuery = "SELECT COUNT(*) as total 
               FROM tblbookmarkjob bj
               INNER JOIN tbljob j ON bj.JOBID = j.JOBID
               INNER JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
               LEFT JOIN tbljobsubcategory jsc ON j.JOBCATEGORYID = jsc.ID
               WHERE $whereClause";

$stmtCount = mysqli_prepare($con, $countQuery);
mysqli_stmt_bind_param($stmtCount, $types, ...$params);
mysqli_stmt_execute($stmtCount);
$countResult = mysqli_stmt_get_result($stmtCount);
$totalRecords = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

// ==================== GET STATISTICS ====================
$statsQuery = "SELECT 
    COUNT(*) as total_saved,
    SUM(CASE WHEN j.JOBSTATUS = 'Active' THEN 1 ELSE 0 END) as active_jobs,
    SUM(CASE WHEN j.JOBID IN (SELECT JOBID FROM tbljobapplication WHERE APPLICANTID = ?) THEN 1 ELSE 0 END) as applied_jobs,
    SUM(CASE WHEN j.DEADLINE < CURDATE() THEN 1 ELSE 0 END) as expired_jobs
FROM tblbookmarkjob bj
INNER JOIN tbljob j ON bj.JOBID = j.JOBID
WHERE bj.APPLICANTID = ?";

$stmtStats = mysqli_prepare($con, $statsQuery);
mysqli_stmt_bind_param($stmtStats, "ii", $applicantId, $applicantId);
mysqli_stmt_execute($stmtStats);
$statsResult = mysqli_stmt_get_result($stmtStats);
$stats = mysqli_fetch_assoc($statsResult);

// ==================== FETCH SAVED JOBS ====================
$savedJobsQuery = "SELECT 
    bj.ID as BOOKMARK_ID, 
    bj.DATETIME as SAVED_DATE,
    j.JOBID, 
    j.JOBTITLE, 
    j.JOBTYPE, 
    j.SALARY, 
    j.DATEPOSTED, 
    j.DEADLINE,
    j.WORKEXPERIENCE, 
    j.WORKPLACE_POLICY, 
    j.JOBSTATUS,
    c.COMPANYID, 
    c.COMPANYNAME, 
    c.COMPANYLOGO, 
    c.COMPANYCITY, 
    c.COMPANYCOUNTRY,
    jsc.SUBCATEGORY,
    jsc.CATEGORYID,
    jcat.CATEGORY
FROM tblbookmarkjob bj
INNER JOIN tbljob j ON bj.JOBID = j.JOBID
INNER JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
LEFT JOIN tbljobsubcategory jsc ON j.JOBCATEGORYID = jsc.ID
LEFT JOIN tbljobcategory jcat ON jsc.CATEGORYID = jcat.ID
WHERE $whereClause
$orderBy
LIMIT ? OFFSET ?";

$params[] = $recordsPerPage;
$params[] = $offset;
$types .= "ii";

$stmt = mysqli_prepare($con, $savedJobsQuery);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$savedJobs = mysqli_stmt_get_result($stmt);

// ==================== GET CATEGORIES FOR FILTER ====================
$categoriesQuery = "SELECT DISTINCT jcat.ID, jcat.CATEGORY 
                    FROM tblbookmarkjob bj
                    INNER JOIN tbljob j ON bj.JOBID = j.JOBID
                    LEFT JOIN tbljobsubcategory jsc ON j.JOBCATEGORYID = jsc.ID
                    LEFT JOIN tbljobcategory jcat ON jsc.CATEGORYID = jcat.ID
                    WHERE bj.APPLICANTID = ? AND jcat.ID IS NOT NULL
                    ORDER BY jcat.CATEGORY ASC";
$stmtCat = mysqli_prepare($con, $categoriesQuery);
mysqli_stmt_bind_param($stmtCat, "i", $applicantId);
mysqli_stmt_execute($stmtCat);
$categoriesResult = mysqli_stmt_get_result($stmtCat);

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
    <title>Saved Jobs - MUNext</title>
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
                                <i class="lni lni-heart-filled"></i> Saved Jobs
                            </h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item text-muted"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item text-muted"><a href="index.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="#" class="theme-cl">Saved Jobs</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="dashboard-widg-bar d-block">
                    <!-- Success/Error Messages -->
                    <?php if (!empty($_SESSION['success_msg'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="lni lni-checkmark-circle"></i> <?php echo $_SESSION['success_msg']; ?>
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                    <?php unset($_SESSION['success_msg']); endif; ?>

                    <?php if (!empty($_SESSION['error_msg'])): ?>
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
                            <span class="stat-number"><?php echo number_format($stats['total_saved']); ?></span>
                            <span class="stat-label">Total Saved</span>
                        </div>
                        <div class="stat-card active">
                            <span class="stat-number"><?php echo number_format($stats['active_jobs']); ?></span>
                            <span class="stat-label">Active Jobs</span>
                        </div>
                        <div class="stat-card applied">
                            <span class="stat-number"><?php echo number_format($stats['applied_jobs']); ?></span>
                            <span class="stat-label">Already Applied</span>
                        </div>
                        <div class="stat-card expired">
                            <span class="stat-number"><?php echo number_format($stats['expired_jobs']); ?></span>
                            <span class="stat-label">Expired</span>
                        </div>
                    </div>

                    <!-- Filter Section -->
                    <div class="filter-section">
                        <form method="GET" action="">
                            <div class="row align-items-center">
                                <div class="col-lg-4 col-md-6 mb-3 mb-lg-0">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search by job title, company, or category..."
                                        value="<?php echo htmlspecialchars($searchTerm); ?>">
                                </div>

                                <div class="col-lg-2 col-md-6 mb-3 mb-lg-0">
                                    <select name="category" class="form-control">
                                        <option value="">All Categories</option>
                                        <?php while ($cat = mysqli_fetch_assoc($categoriesResult)): ?>
                                        <option value="<?php echo $cat['ID']; ?>"
                                            <?php echo $categoryFilter == $cat['ID'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['CATEGORY']); ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="col-lg-2 col-md-6 mb-3 mb-lg-0">
                                    <select name="jobtype" class="form-control">
                                        <option value="">All Job Types</option>
                                        <option value="Full Time"
                                            <?php echo $jobTypeFilter == 'Full Time' ? 'selected' : ''; ?>>Full Time
                                        </option>
                                        <option value="Part Time"
                                            <?php echo $jobTypeFilter == 'Part Time' ? 'selected' : ''; ?>>Part Time
                                        </option>
                                        <option value="Contract"
                                            <?php echo $jobTypeFilter == 'Contract' ? 'selected' : ''; ?>>Contract
                                        </option>
                                        <option value="Internship"
                                            <?php echo $jobTypeFilter == 'Internship' ? 'selected' : ''; ?>>Internship
                                        </option>
                                        <option value="Freelance"
                                            <?php echo $jobTypeFilter == 'Freelance' ? 'selected' : ''; ?>>Freelance
                                        </option>
                                    </select>
                                </div>

                                <div class="col-lg-2 col-md-6 mb-3 mb-lg-0">
                                    <select name="sort" class="form-control">
                                        <option value="recent" <?php echo $sortBy == 'recent' ? 'selected' : ''; ?>>
                                            Recently Saved</option>
                                        <option value="oldest" <?php echo $sortBy == 'oldest' ? 'selected' : ''; ?>>
                                            Oldest First</option>
                                        <option value="title" <?php echo $sortBy == 'title' ? 'selected' : ''; ?>>Job
                                            Title A-Z</option>
                                        <option value="company" <?php echo $sortBy == 'company' ? 'selected' : ''; ?>>
                                            Company A-Z</option>
                                        <option value="deadline" <?php echo $sortBy == 'deadline' ? 'selected' : ''; ?>>
                                            Deadline Soon</option>
                                    </select>
                                </div>

                                <div class="col-lg-2 col-md-6">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn theme-bg text-white flex-fill">
                                            <i class="lni lni-search"></i> Filter
                                        </button>
                                        <a href="dashboard-saved-jobs.php" class="btn btn-secondary">
                                            <i class="lni lni-reload"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Saved Jobs List -->
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12">
                            <?php if (mysqli_num_rows($savedJobs) > 0): ?>
                            <?php while ($job = mysqli_fetch_assoc($savedJobs)): 
                                    $jobId = $job['JOBID'];
                                    $bookmarkId = $job['BOOKMARK_ID'];
                                    $isApplied = in_array($jobId, $appliedJobs);
                                    $isActive = ($job['JOBSTATUS'] == 'Active');
                                    $isExpired = strtotime($job['DEADLINE']) < time();
                                    $daysUntilDeadline = ceil((strtotime($job['DEADLINE']) - time()) / 86400);

                                    $SAVED_DATE = $job['SAVED_DATE']??'';

                                    
                                ?>

                            <div class="job-card">
                                <span class="saved-badge mb-4">
                                    <i class="lni lni-heart-filled"></i> Saved
                                </span>

                                <div class="row align-items-center">
                                    <!-- Company Logo -->
                                    <div class="col-lg-1 col-md-2 col-3 text-center mb-3 mb-lg-0">
                                        <?php if (!empty($job['COMPANYLOGO'])): ?>
                                        <img src="<?php echo $path.htmlspecialchars($job['COMPANYLOGO']); ?>"
                                            alt="Company Logo" class="company-logo"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <?php endif; ?>
                                        <div class="company-logo-placeholder"
                                            style="<?php echo !empty($job['COMPANYLOGO']) ? 'display:none;' : ''; ?>">
                                            <?php echo strtoupper(substr($job['COMPANYNAME'], 0, 1)); ?>
                                        </div>
                                    </div>

                                    <!-- Job Info -->
                                    <div class="col-lg-7 col-md-6 col-9 mb-3 mb-lg-0">
                                        <div class="d-flex align-items-start mb-2 flex-wrap">
                                            <h5 class="mb-0 ft-medium mr-2">
                                                <a href="<?php echo $path?>job-detail.php?jobid=<?php echo $jobId; ?>"
                                                    class="text-dark">
                                                    <?php echo htmlspecialchars($job['JOBTITLE']); ?>
                                                </a>
                                            </h5>
                                            <span
                                                class="job-badge <?php echo $isActive ? 'badge-active' : 'badge-closed'; ?>">
                                                <i
                                                    class="lni lni-<?php echo $isActive ? 'checkmark-circle' : 'cross-circle'; ?>"></i>
                                                <?php echo htmlspecialchars($job['JOBSTATUS']); ?>
                                            </span>
                                            <?php if ($isApplied): ?>
                                            <span class="job-badge badge-applied">
                                                <i class="lni lni-checkmark"></i> Applied
                                            </span>
                                            <?php endif; ?>
                                            <?php if ($isExpired): ?>
                                            <span class="job-badge badge-expired">
                                                <i class="lni lni-alarm"></i> Expired
                                            </span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="mb-2">
                                            <span class="text-muted">
                                                <i class="lni lni-building mr-1"></i>
                                                <?php echo htmlspecialchars($job['COMPANYNAME']); ?>
                                            </span>
                                        </div>

                                        <div class="d-flex flex-wrap">
                                            <?php if (!empty($job['COMPANYCITY']) || !empty($job['COMPANYCOUNTRY'])): ?>
                                            <span class="text-muted small mr-3">
                                                <i class="lni lni-map-marker mr-1"></i>
                                                <?php echo htmlspecialchars(trim(($job['COMPANYCITY'] ?? '') . ', ' . ($job['COMPANYCOUNTRY'] ?? ''), ', ')); ?>
                                            </span>
                                            <?php endif; ?>

                                            <?php if ($job['JOBTYPE']): ?>
                                            <span class="text-muted small mr-3">
                                                <i class="lni lni-briefcase mr-1"></i>
                                                <?php echo htmlspecialchars($job['JOBTYPE']); ?>
                                            </span>
                                            <?php endif; ?>

                                            <?php if ($job['CATEGORY']): ?>
                                            <span class="text-muted small mr-3">
                                                <i class="lni lni-folder mr-1"></i>
                                                <?php echo htmlspecialchars($job['CATEGORY']); ?>
                                            </span>
                                            <?php endif; ?>

                                            <?php if ($job['SALARY']): ?>
                                            <span class="text-success small mr-3">
                                                <i class="lni lni-dollar mr-1"></i>
                                                <?php echo htmlspecialchars($job['SALARY']); ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Deadline Warning -->
                                        <?php if (!$isExpired && $daysUntilDeadline <= 7): ?>
                                        <div class="mt-2">
                                            <span
                                                class="deadline-alert <?php echo $daysUntilDeadline <= 3 ? 'urgent' : ''; ?>">
                                                <i class="lni lni-alarm"></i>
                                                Deadline in <?php echo $daysUntilDeadline; ?>
                                                day<?php echo $daysUntilDeadline != 1 ? 's' : ''; ?>
                                            </span>
                                        </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Dates -->
                                    <div class="col-lg-2 col-md-2 col-6 text-center mb-3 mb-lg-0">
                                        <div class="text-muted small mb-1">Saved</div>
                                        <strong><?php echo timeago($SAVED_DATE); ?></strong>

                                        <div class="text-muted small mt-1">
                                            Posted: <?php echo date('M d', strtotime($job['DATEPOSTED'])); ?>
                                        </div>
                                        <?php if (!$isExpired): ?>
                                        <div class="text-muted small">
                                            Deadline: <?php echo date('M d', strtotime($job['DEADLINE'])); ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Actions -->
                                    <div class="col-lg-2 col-md-2 col-6 text-center mt-4">


                                        <?php if ($isActive && !$isApplied && !$isExpired): ?>
                                        <a href="<?php echo $path?>job-detail.php?jobid=<?php echo $jobId; ?>#apply"
                                            class="btn btn-outline text-white rounded small theme-bg btn-sm btn-block">
                                            <i class="lni lni-telegram mr-1"></i> Apply Now
                                        </a>
                                        <?php elseif ($isApplied): ?>
                                        <a href="dashboard-applied-jobs.php"
                                            class="btn btn-outline-secondary btn-block">
                                            View Application
                                        </a>
                                        <?php elseif ($isExpired): ?>
                                        <button class="btn btn-outline-secondary btn-block" disabled>
                                            Deadline Passed
                                        </button>
                                        <?php endif; ?>

                                        <div class="d-flex justify-content-center mt-2">

                                            <a href="?type=delete&bookmarkedid=<?php echo $bookmarkId; ?>"
                                                class="btn btn-outline-danger rounded small btn-sm btn-block"
                                                title="Remove from Saved"
                                                onclick="return confirm('Remove this job from your saved list?');">
                                                <i class="lni lni-trash-can"></i> Remove
                                            </a>
                                        </div>
                                    </div>
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
                                                href="?page=<?php echo $page - 1; ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?><?php echo !empty($jobTypeFilter) ? '&jobtype=' . urlencode($jobTypeFilter) : ''; ?><?php echo $categoryFilter > 0 ? '&category=' . $categoryFilter : ''; ?><?php echo !empty($sortBy) ? '&sort=' . $sortBy : ''; ?>">
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
                                                href="?page=<?php echo $i; ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?><?php echo !empty($jobTypeFilter) ? '&jobtype=' . urlencode($jobTypeFilter) : ''; ?><?php echo $categoryFilter > 0 ? '&category=' . $categoryFilter : ''; ?><?php echo !empty($sortBy) ? '&sort=' . $sortBy : ''; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                        <?php endfor; ?>

                                        <?php if ($page < $totalPages): ?>
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="?page=<?php echo $page + 1; ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?><?php echo !empty($jobTypeFilter) ? '&jobtype=' . urlencode($jobTypeFilter) : ''; ?><?php echo $categoryFilter > 0 ? '&category=' . $categoryFilter : ''; ?><?php echo !empty($sortBy) ? '&sort=' . $sortBy : ''; ?>">
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
                                <i class="lni lni-heart"></i>
                                <h4 class="mt-3">No Saved Jobs</h4>
                                <p class="text-muted">
                                    <?php if (!empty($searchTerm) || !empty($jobTypeFilter) || $categoryFilter > 0): ?>
                                    No saved jobs match your search criteria. Try adjusting your filters.
                                    <?php else: ?>
                                    You haven't saved any jobs yet. Start browsing and save jobs you're interested in!
                                    <?php endif; ?>
                                </p>
                                <?php if (!empty($searchTerm) || !empty($jobTypeFilter) || $categoryFilter > 0): ?>
                                <a href="dashboard-saved-jobs.php" class="btn btn-outline-secondary mt-3 pt-4">
                                    <i class="lni lni-reload"></i> Clear Filters
                                </a>
                                <?php else: ?>
                                <a href="<?php echo $path?>browse-jobs.php" class="btn btn-outline-secondary mt-3 pt-4">
                                    <i class="lni lni-search"></i> Browse Jobs
                                </a>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="h-150"></div>

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