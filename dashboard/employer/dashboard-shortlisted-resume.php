<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';
unset($result); // Clear any $result from phpcode.php

$employerId = $session_id;


// ==================== HANDLE DELETE BOOKMARK ====================
if (isset($_GET['type']) && $_GET['type'] == 'delete' && isset($_GET['bookmarkedid'])) {
    $bookmarkId = (int)$_GET['bookmarkedid'];
    
    // Verify ownership
    $verifyQuery = "SELECT ID FROM tblbookmarkresume WHERE ID = ? AND USERID = ?";
    $stmtVerify = mysqli_prepare($con, $verifyQuery);
    mysqli_stmt_bind_param($stmtVerify, "ii", $bookmarkId, $employerId);
    mysqli_stmt_execute($stmtVerify);
    
    if (mysqli_num_rows(mysqli_stmt_get_result($stmtVerify)) > 0) {
        $deleteQuery = "DELETE FROM tblbookmarkresume WHERE ID = ? AND USERID = ?";
        $stmt = mysqli_prepare($con, $deleteQuery);
        mysqli_stmt_bind_param($stmt, "ii", $bookmarkId, $employerId);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_msg'] = "Resume removed from shortlist successfully!";
        } else {
            $_SESSION['error_msg'] = "Failed to remove resume.";
        }
    }
    
    header("Location: dashboard-shortlisted-resume.php");
    exit();
}

// ==================== HANDLE STATUS UPDATE ====================
if (isset($_POST['applicationstatus']) && isset($_POST['jobapplicationid'])) {
    $newStatus = $_POST['applicationstatus'];
    $applicationId = (int)$_POST['jobapplicationid'];
    
    // Verify ownership through bookmark
    $verifyQuery = "SELECT bm.ID FROM tblbookmarkresume bm
                    INNER JOIN tbljobapplication ja ON bm.JOBAPPLICATIONID = ja.ID
                    WHERE ja.ID = ? AND bm.USERID = ?";
    $stmtVerify = mysqli_prepare($con, $verifyQuery);
    mysqli_stmt_bind_param($stmtVerify, "ii", $applicationId, $employerId);
    mysqli_stmt_execute($stmtVerify);
    
    if (mysqli_num_rows(mysqli_stmt_get_result($stmtVerify)) > 0) {
        $updateQuery = "UPDATE tbljobapplication SET APPLICATIONSTATUS = ? WHERE ID = ?";
        $stmtUpdate = mysqli_prepare($con, $updateQuery);
        mysqli_stmt_bind_param($stmtUpdate, "si", $newStatus, $applicationId);
        
        if (mysqli_stmt_execute($stmtUpdate)) {

            

            // Get applicant details for email
            $detailsQuery = "SELECT ja.ID, ja.APPLICANTID,
                    u.FNAME, u.ONAME, u.EMAIL, j.JOBTITLE
                    FROM tbljobapplication ja
                    INNER JOIN tblusers u ON ja.APPLICANTID = u.USERID
                    INNER JOIN tbljob j ON ja.JOBID = j.JOBID
                    WHERE ja.ID = ?";
            $detailsStmt = mysqli_prepare($con, $detailsQuery);
            mysqli_stmt_bind_param($detailsStmt, "i", $applicationId);
            mysqli_stmt_execute($detailsStmt);
            $detailsResult = mysqli_stmt_get_result($detailsStmt);
            
            if ($details = mysqli_fetch_assoc($detailsResult)) {
                $applicantName = $details['FNAME'] . ' ' . $details['ONAME'];
                $applicantEmail = $details['EMAIL'] ?? '';
                $jobTitle = $details['JOBTITLE'] ?? '';
                


                 // Send job application confirmation to Applicant
                //  sendApplicationStatusEmail($con, $applicantEmail, $applicantName, $jobTitle, $newStatus);


                 sendApplicationStatusEmail($con, $applicantEmail, $applicantName, $jobTitle, $newStatus, $companyName??'Company', $applicationId);
            }
            
            $_SESSION['success_msg'] = "Application status updated to $newStatus successfully!";
        } else {
            $_SESSION['error_msg'] = "Failed to update status.";
        }
    }
    
    header("Location: dashboard-shortlisted-resume.php");
    exit();
}

// ==================== PAGINATION SETUP ====================
$recordsPerPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

// ==================== FILTER SETUP ====================
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'recent'; // recent, older, score

// ==================== BUILD WHERE CLAUSE ====================
$whereConditions = ["bm.USERID = ?"];
$params = [$employerId];
$types = "i";

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
    case 'older':
        $orderBy = "ORDER BY bm.DATETIME ASC";
        break;
    case 'score':
        $orderBy = "ORDER BY ja.SCREENINGSCORE DESC, bm.DATETIME DESC";
        break;
    default: // recent
        $orderBy = "ORDER BY bm.DATETIME DESC";
}

// ==================== COUNT TOTAL RECORDS ====================
$countQuery = "SELECT COUNT(*) as total 
               FROM tblbookmarkresume bm
               INNER JOIN tbljobapplication ja ON bm.JOBAPPLICATIONID = ja.ID
               INNER JOIN tblusers u ON ja.APPLICANTID = u.USERID
               INNER JOIN tbljob j ON ja.JOBID = j.JOBID
               WHERE $whereClause";
$stmtCount = mysqli_prepare($con, $countQuery);
mysqli_stmt_bind_param($stmtCount, $types, ...$params);
mysqli_stmt_execute($stmtCount);
$countResult = mysqli_stmt_get_result($stmtCount);
$totalRecords = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

// ==================== FETCH BOOKMARKS ====================
$bookmarksQuery = "SELECT 
    bm.ID as BOOKMARK_ID,
    bm.DATETIME as BOOKMARKED_DATE,
    ja.*,
    u.FNAME, u.ONAME, u.EMAIL,
    ap.COUNTRY, ap.CITY, ap.CONTACTNO, ap.APPLICANTPHOTO, ap.SKILLS, ap.DEGREE,
    j.JOBTITLE, j.JOBID
FROM tblbookmarkresume bm
INNER JOIN tbljobapplication ja ON bm.JOBAPPLICATIONID = ja.ID
INNER JOIN tblapplicants ap ON ja.APPLICANTID = ap.USERID
INNER JOIN tblusers u ON ja.APPLICANTID = u.USERID
INNER JOIN tbljob j ON ja.JOBID = j.JOBID
WHERE $whereClause
$orderBy
LIMIT ? OFFSET ?";

$params[] = $recordsPerPage;
$params[] = $offset;
$types .= "ii";

$stmt = mysqli_prepare($con, $bookmarksQuery);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$bookmarksResult = mysqli_stmt_get_result($stmt);

// ==================== STATISTICS ====================
$statsQuery = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN ja.APPLICATIONSTATUS = 'Pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN ja.APPLICATIONSTATUS = 'Reviewed' THEN 1 ELSE 0 END) as reviewed,
    SUM(CASE WHEN ja.APPLICATIONSTATUS = 'Approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN ja.APPLICATIONSTATUS = 'Rejected' THEN 1 ELSE 0 END) as rejected,
    SUM(CASE WHEN ja.APPLICATIONSTATUS = 'Hire' THEN 1 ELSE 0 END) as hired
FROM tblbookmarkresume bm
INNER JOIN tbljobapplication ja ON bm.JOBAPPLICATIONID = ja.ID
WHERE bm.USERID = ?";

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
    <title>Shortlisted Resumes - Employer Dashboard | MUNext</title>
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
                                <i class="lni lni-heart-filled" style="color: var(--warning-color);"></i>
                                Shortlisted Resumes
                            </h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item text-muted"><a href="dashboard.php">Home</a></li>
                                    <li class="breadcrumb-item text-muted"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="#" class="theme-cl">Shortlisted</a></li>
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
                            <span class="stat-label">Total Shortlisted</span>
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
                                        <option value="recent" <?php echo $sortBy == 'recent' ? 'selected' : ''; ?>>Most
                                            Recent</option>
                                        <option value="older" <?php echo $sortBy == 'older' ? 'selected' : ''; ?>>Oldest
                                            First</option>
                                        <option value="score" <?php echo $sortBy == 'score' ? 'selected' : ''; ?>>
                                            Highest Score</option>
                                    </select>
                                </div>

                                <div class="col-lg-3 col-md-6">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-warning flex-fill text-white">
                                            <i class="lni lni-search"></i> Filter
                                        </button>
                                        <a href="dashboard-shortlisted-resume.php" class="btn btn-secondary">
                                            <i class="lni lni-reload"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Shortlisted Resumes List -->
                    <div class="row">
                        <div class="col-xl-12">
                            <?php if (mysqli_num_rows($bookmarksResult) > 0): ?>
                            <?php while ($bookmark = mysqli_fetch_assoc($bookmarksResult)): 
                                    $bookmarkId = $bookmark['BOOKMARK_ID'];
                                    $applicationId = $bookmark['ID'];
                                    $applicantId = $bookmark['APPLICANTID'];
                                    $jobId = $bookmark['JOBID'];
                                    $status = $bookmark['APPLICATIONSTATUS'];
                                    $screeningScore = $bookmark['SCREENINGSCORE'] ?? 0;
                                    $bookmarkedDate = $bookmark['BOOKMARKED_DATE'];
                                    
                                    // Get max score for this job
                                    $maxScoreQuery = "SELECT SUM(POINTS) as max_score FROM tblscreeningquestions WHERE JOBID = ?";
                                    $stmtMax = mysqli_prepare($con, $maxScoreQuery);
                                    mysqli_stmt_bind_param($stmtMax, "i", $jobId);
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
                                    
                                    // Photo
                                    $applicantPhoto = !empty($bookmark['APPLICANTPHOTO']) ? '../../' . $bookmark['APPLICANTPHOTO'] : 'assets/img/avatar-default.svg';
                                    
                                    // Status badge class
                                    $statusBadgeClass = 'status-' . strtolower($status);
                                ?>

                            <!-- Shortlist Card -->
                            <div class="shortlist-card">
                                <span class="bookmark-badge">
                                    <i class="lni lni-heart-filled"></i> Shortlisted
                                </span>

                                <div class="applicant-header">
                                    <img src="<?php echo $applicantPhoto; ?>" alt="Applicant Photo"
                                        class="applicant-photo" onerror="this.src='assets/img/avatar-default.svg'">

                                    <div class="applicant-info">
                                        <div class="applicant-name">
                                            <?php echo htmlspecialchars($bookmark['FNAME'] . ' ' . ($bookmark['ONAME'] ?? '')); ?>

                                            <!-- Screening Score -->
                                            <?php if ($maxScore > 0): ?>
                                            <span class="screening-score-badge <?php echo $scoreBadgeClass; ?>">
                                                <i class="lni lni-star"></i> <?php echo $scoreBadgeText; ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="applicant-meta">
                                            <?php if (!empty($bookmark['CITY']) || !empty($bookmark['COUNTRY'])): ?>
                                            <span>
                                                <i class="lni lni-map-marker"></i>
                                                <?php echo htmlspecialchars(trim(($bookmark['CITY'] ?? '') . ', ' . ($bookmark['COUNTRY'] ?? ''), ', ')); ?>
                                            </span>
                                            <?php endif; ?>

                                            <span>
                                                <i class="lni lni-briefcase"></i>
                                                <?php echo htmlspecialchars($bookmark['JOBTITLE']); ?>
                                            </span>

                                            <?php if (!empty($bookmark['DEGREE'])): ?>
                                            <span>
                                                <i class="lni lni-graduation"></i>
                                                <?php echo htmlspecialchars($bookmark['DEGREE']); ?>
                                            </span>
                                            <?php endif; ?>

                                            <span class="bookmarked-date">
                                                <i class="lni lni-heart-filled"
                                                    style="color: var(--warning-color);"></i>
                                                Shortlisted <?php echo timeago($bookmarkedDate); ?>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Status Badge -->
                                    <div class="mt-auto ">
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
                                    <?php if (!empty($bookmark['RESUME'])): ?>
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

                                    <!-- Message -->
                                    <a href="dashboard-messages.php?userid=<?php echo $applicantId; ?>"
                                        class="btn-action btn-info">
                                        <i class="lni lni-comments"></i> Message
                                    </a>

                                    <!-- View Application -->
                                    <a href="dashboard-manage-applications.php?jobid=<?php echo $jobId; ?>"
                                        class="btn-action btn-secondary">
                                        <i class="lni lni-eye"></i> View Application
                                    </a>

                                    <!-- Remove from Shortlist -->
                                    <a href="?type=delete&bookmarkedid=<?php echo $bookmarkId; ?>"
                                        class="btn-action btn-outline-danger"
                                        onclick="return confirm('Remove this resume from your shortlist?');">
                                        <i class="lni lni-trash-can"></i> Remove
                                    </a>
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
                                <i class="lni lni-heart"></i>
                                <h4>No Shortlisted Resumes</h4>
                                <p class="text-muted">
                                    <?php if (!empty($searchTerm) || !empty($statusFilter)): ?>
                                    No shortlisted resumes match your search criteria.
                                    <?php else: ?>
                                    Resumes you shortlist will appear here for quick access.
                                    <?php endif; ?>
                                </p>
                                <a href="dashboard-manage-applications.php" class="btn btn-outline-secondary mt-3 pt-4">
                                    <i class="lni lni-users"></i> View All Applications
                                </a>
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