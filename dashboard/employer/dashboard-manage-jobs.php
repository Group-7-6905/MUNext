<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';
unset($result); // Clear any $result from phpcode.php


$employerID = $session_id;

// ==================== HANDLE JOB DELETE ====================
if (isset($_GET['type']) && $_GET['type'] == 'delete' && isset($_GET['id'])) {
    $jobId = (int)$_GET['id'];
    
    // Verify job belongs to this employer
    $verifyQuery = "SELECT JOBID FROM tbljob WHERE JOBID = ? AND EMPLOYERID = ?";
    $stmtVerify = mysqli_prepare($con, $verifyQuery);
    mysqli_stmt_bind_param($stmtVerify, "ii", $jobId, $employerID);
    mysqli_stmt_execute($stmtVerify);
    
    if (mysqli_num_rows(mysqli_stmt_get_result($stmtVerify)) > 0) {
        // Delete related records first (cascading delete)
        $deleteAnswers = "DELETE FROM tblscreeninganswers WHERE APPLICATIONID IN (SELECT ID FROM tbljobapplication WHERE JOBID = ?)";
        $stmtDelAns = mysqli_prepare($con, $deleteAnswers);
        mysqli_stmt_bind_param($stmtDelAns, "i", $jobId);
        mysqli_stmt_execute($stmtDelAns);
        
        $deleteQuestions = "DELETE FROM tblscreeningquestions WHERE JOBID = ?";
        $stmtDelQues = mysqli_prepare($con, $deleteQuestions);
        mysqli_stmt_bind_param($stmtDelQues, "i", $jobId);
        mysqli_stmt_execute($stmtDelQues);
        
        $deleteBookmarks = "DELETE FROM tblbookmarkresume WHERE JOBAPPLICATIONID IN (SELECT ID FROM tbljobapplication WHERE JOBID = ?)";
        $stmtDelBm = mysqli_prepare($con, $deleteBookmarks);
        mysqli_stmt_bind_param($stmtDelBm, "i", $jobId);
        mysqli_stmt_execute($stmtDelBm);
        
        $deleteApps = "DELETE FROM tbljobapplication WHERE JOBID = ?";
        $stmtDelApps = mysqli_prepare($con, $deleteApps);
        mysqli_stmt_bind_param($stmtDelApps, "i", $jobId);
        mysqli_stmt_execute($stmtDelApps);
        
        // Delete job
        $deleteJob = "DELETE FROM tbljob WHERE JOBID = ? AND EMPLOYERID = ?";
        $stmtDelJob = mysqli_prepare($con, $deleteJob);
        mysqli_stmt_bind_param($stmtDelJob, "ii", $jobId, $employerID);
        
        if (mysqli_stmt_execute($stmtDelJob)) {
            $_SESSION['success_msg'] = "Job deleted successfully!";
        } else {
            $_SESSION['error_msg'] = "Failed to delete job.";
        }
    } else {
        $_SESSION['error_msg'] = "Invalid job or unauthorized action.";
    }
    
    header("Location: dashboard-manage-jobs.php");
    exit();
}

// ==================== HANDLE JOB STATUS TOGGLE ====================
if (isset($_GET['toggle_status']) && isset($_GET['jobid'])) {
    $jobId = (int)$_GET['jobid'];
    
    // Verify job belongs to this employer
    $verifyQuery = "SELECT JOBSTATUS FROM tbljob WHERE JOBID = ? AND EMPLOYERID = ?";
    $stmtVerify = mysqli_prepare($con, $verifyQuery);
    mysqli_stmt_bind_param($stmtVerify, "ii", $jobId, $employerID);
    mysqli_stmt_execute($stmtVerify);
    $verifyResult = mysqli_stmt_get_result($stmtVerify);
    $currentStatus = mysqli_fetch_assoc($verifyResult);
    
    if ($currentStatus) {
        // Toggle status
        $newStatus = ($currentStatus['JOBSTATUS'] == 'Active') ? 'Inactive' : 'Active';
        
        $updateQuery = "UPDATE tbljob SET JOBSTATUS = ? WHERE JOBID = ? AND EMPLOYERID = ?";
        $stmtUpdate = mysqli_prepare($con, $updateQuery);
        mysqli_stmt_bind_param($stmtUpdate, "sii", $newStatus, $jobId, $employerID);
        
        if (mysqli_stmt_execute($stmtUpdate)) {
            $_SESSION['success_msg'] = "Job status updated to $newStatus successfully!";
        } else {
            $_SESSION['error_msg'] = "Failed to update job status.";
        }
    } else {
        $_SESSION['error_msg'] = "Invalid job or unauthorized action.";
    }
    
    header("Location: dashboard-manage-jobs.php");
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
$whereConditions = ["j.EMPLOYERID = ?"];
$params = [$employerID];
$types = "i";

if (!empty($statusFilter)) {
    $whereConditions[] = "j.JOBSTATUS = ?";
    $params[] = $statusFilter;
    $types .= "s";
}

if (!empty($searchTerm)) {
    $whereConditions[] = "j.JOBTITLE LIKE ?";
    $searchParam = "%$searchTerm%";
    $params[] = $searchParam;
    $types .= "s";
}

$whereClause = implode(" AND ", $whereConditions);

// ==================== BUILD ORDER BY ====================
switch($sortBy) {
    case 'oldest':
        $orderBy = "ORDER BY j.DATEPOSTED ASC";
        break;
    case 'title':
        $orderBy = "ORDER BY j.JOBTITLE ASC";
        break;
    case 'deadline':
        $orderBy = "ORDER BY j.DEADLINE ASC";
        break;
    case 'applications':
        $orderBy = "ORDER BY application_count DESC";
        break;
    default: // recent
        $orderBy = "ORDER BY j.DATEPOSTED DESC";
}

// ==================== COUNT TOTAL RECORDS ====================
$countQuery = "SELECT COUNT(*) as total FROM tbljob j WHERE $whereClause";
$stmtCount = mysqli_prepare($con, $countQuery);
mysqli_stmt_bind_param($stmtCount, $types, ...$params);
mysqli_stmt_execute($stmtCount);
$countResult = mysqli_stmt_get_result($stmtCount);
$totalRecords = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

// ==================== FETCH JOBS ====================
$query = "SELECT 
    j.JOBID,
    j.JOBTITLE,
    j.JOBSTATUS,
    j.DATEPOSTED,
    j.DEADLINE,
    j.COMPANYID,
    c.COMPANYNAME,
    c.COMPANYLOGO,
    COUNT(DISTINCT ja.ID) as application_count,
    COUNT(DISTINCT sq.QUESTIONID) as screening_questions_count
FROM tbljob j
LEFT JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
LEFT JOIN tbljobapplication ja ON j.JOBID = ja.JOBID
LEFT JOIN tblscreeningquestions sq ON j.JOBID = sq.JOBID
WHERE $whereClause
GROUP BY j.JOBID, j.JOBTITLE, j.JOBSTATUS, j.DATEPOSTED, j.DEADLINE, j.COMPANYID, c.COMPANYNAME, c.COMPANYLOGO
$orderBy
LIMIT ? OFFSET ?";

$params[] = $recordsPerPage;
$params[] = $offset;
$types .= "ii";

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$resultManageJobs = mysqli_stmt_get_result($stmt);

// ==================== STATISTICS ====================
$statsQuery = "SELECT 
    COUNT(*) as total_jobs,
    SUM(CASE WHEN JOBSTATUS = 'Active' THEN 1 ELSE 0 END) as active_jobs,
    SUM(CASE WHEN JOBSTATUS = 'Inactive' THEN 1 ELSE 0 END) as inactive_jobs,
    SUM(CASE WHEN JOBSTATUS = 'Filled' THEN 1 ELSE 0 END) as filled_jobs,
    SUM(CASE WHEN DEADLINE < CURDATE() AND JOBSTATUS = 'Active' THEN 1 ELSE 0 END) as expired_jobs,
    (SELECT COUNT(DISTINCT ja.ID) FROM tbljob j2 LEFT JOIN tbljobapplication ja ON j2.JOBID = ja.JOBID WHERE j2.EMPLOYERID = ?) as total_applications
FROM tbljob WHERE EMPLOYERID = ?";

$stmtStats = mysqli_prepare($con, $statsQuery);
mysqli_stmt_bind_param($stmtStats, "ii", $employerID, $employerID);
mysqli_stmt_execute($stmtStats);
$statsResult = mysqli_stmt_get_result($stmtStats);
$stats = mysqli_fetch_assoc($statsResult);
?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Jobs - Employer Dashboard | MUNext</title>
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
                                <i class="lni lni-briefcase"></i> Manage Jobs
                            </h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item text-muted"><a href="index.php">Home</a></li>
                                    <li class="breadcrumb-item text-muted"><a href="index.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="#" class="theme-cl">Manage Jobs</a></li>
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
                            <span class="stat-number"><?php echo number_format($stats['total_jobs']); ?></span>
                            <span class="stat-label">Total Jobs</span>
                        </div>
                        <div class="stat-card active">
                            <span class="stat-number"><?php echo number_format($stats['active_jobs']); ?></span>
                            <span class="stat-label">Active Jobs</span>
                        </div>
                        <div class="stat-card inactive">
                            <span class="stat-number"><?php echo number_format($stats['inactive_jobs']); ?></span>
                            <span class="stat-label">Inactive Jobs</span>
                        </div>
                        <div class="stat-card filled">
                            <span class="stat-number"><?php echo number_format($stats['filled_jobs']); ?></span>
                            <span class="stat-label">Filled Jobs</span>
                        </div>
                        <div class="stat-card expired">
                            <span class="stat-number"><?php echo number_format($stats['expired_jobs']); ?></span>
                            <span class="stat-label">Expired Jobs</span>
                        </div>
                        <div class="stat-card applications">
                            <span class="stat-number"><?php echo number_format($stats['total_applications']); ?></span>
                            <span class="stat-label">Total Applications</span>
                        </div>
                    </div>

                    <!-- Info Alert -->
                    <div class="info-alert d-flex align-items-center">
                        <i class="lni lni-information"></i>
                        <div>
                            <strong>Note:</strong> Your listings will be automatically hidden from public view once
                            they reach their deadline. You can change the deadline date or republish a listing at any
                            time to make it visible again.
                        </div>
                    </div>

                    <!-- Filter Section -->
                    <div class="filter-section">
                        <form method="GET" action="">
                            <div class="row align-items-center">
                                <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search by job title..."
                                        value="<?php echo htmlspecialchars($searchTerm); ?>">
                                </div>

                                <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="Active"
                                            <?php echo $statusFilter == 'Active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="Inactive"
                                            <?php echo $statusFilter == 'Inactive' ? 'selected' : ''; ?>>Inactive
                                        </option>
                                        <option value="Filled"
                                            <?php echo $statusFilter == 'Filled' ? 'selected' : ''; ?>>Filled</option>
                                    </select>
                                </div>

                                <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                                    <select name="sort" class="form-control">
                                        <option value="recent" <?php echo $sortBy == 'recent' ? 'selected' : ''; ?>>
                                            Recently Posted</option>
                                        <option value="oldest" <?php echo $sortBy == 'oldest' ? 'selected' : ''; ?>>
                                            Oldest First</option>
                                        <option value="title" <?php echo $sortBy == 'title' ? 'selected' : ''; ?>>Job
                                            Title A-Z</option>
                                        <option value="deadline" <?php echo $sortBy == 'deadline' ? 'selected' : ''; ?>>
                                            Deadline Soon</option>
                                        <option value="applications"
                                            <?php echo $sortBy == 'applications' ? 'selected' : ''; ?>>Most Applications
                                        </option>
                                    </select>
                                </div>

                                <div class="col-lg-3 col-md-6">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn theme-bg text-white flex-fill">
                                            <i class="lni lni-search"></i> Filter
                                        </button>
                                        <a href="dashboard-manage-jobs.php" class="btn btn-secondary">
                                            <i class="lni lni-reload"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Jobs Table -->
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12">
                            <?php if (mysqli_num_rows($resultManageJobs) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="width: 35%;">Job Details</th>
                                            <th scope="col" class="text-center">Status</th>
                                            <th scope="col" class="text-center">Posted</th>
                                            <th scope="col" class="text-center">Deadline</th>
                                            <th scope="col" class="text-center">Applications</th>
                                            <th scope="col" class="text-center">Screening</th>
                                            <th scope="col" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($resultManageJobs)): 
                                            $jobId = $row['JOBID'];
                                            $appCount = $row['application_count'];
                                            $screeningCount = $row['screening_questions_count'];
                                            $isActive = ($row['JOBSTATUS'] == 'Active');
                                            $isFilled = ($row['JOBSTATUS'] == 'Filled');
                                            $isPending = ($row['JOBSTATUS'] == 'Pending');
                                            $isExpired = (strtotime($row['DEADLINE']) < time());
                                            $companyName = $row['COMPANYNAME'];
                                            $companyLogo = $row['COMPANYLOGO'];
                                        ?>
                                        <tr>
                                            <!-- Job Details with Company Logo -->
                                            <td>
                                                <div class="company-logo-cell">
                                                    <?php if (!empty($companyLogo)): ?>
                                                    <img src="<?php echo $path.htmlspecialchars($companyLogo); ?>"
                                                        alt="<?php echo htmlspecialchars($companyName); ?>"
                                                        class="company-logo"
                                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div class="company-logo-placeholder" style="display: none;">
                                                        <?php echo strtoupper(substr($companyName, 0, 1)); ?>
                                                    </div>
                                                    <?php else: ?>
                                                    <div class="company-logo-placeholder">
                                                        <?php echo strtoupper(substr($companyName, 0, 1)); ?>
                                                    </div>
                                                    <?php endif; ?>

                                                    <div class="job-info">
                                                        <h6 class="mb-0 ft-medium">
                                                            <?php echo htmlspecialchars($row['JOBTITLE']); ?>

                                                            <?php if ($isFilled): ?>
                                                            <span class="status-badge badge-filled">
                                                                <i class="lni lni-checkmark-circle"></i> Filled
                                                            </span>
                                                            <?php elseif ($isActive): ?>
                                                            <span class="status-badge badge-live">
                                                                <i class="lni lni-checkmark-circle"></i> Live
                                                            </span>
                                                            <?php elseif ($isPending): ?>
                                                            <span class="status-badge badge-warning">
                                                                <i class="lni lni-checkmark-circle"></i> Pending
                                                            </span>
                                                            <?php else: ?>
                                                            <span class="status-badge badge-inactive">
                                                                <i class="lni lni-eye-off"></i> Inactive
                                                            </span>
                                                            <?php endif; ?>

                                                            <?php if ($isExpired && !$isFilled): ?>
                                                            <span class="status-badge badge-expired">
                                                                <i class="lni lni-alarm"></i> Expired
                                                            </span>
                                                            <?php endif; ?>
                                                        </h6>
                                                        <span class="company-name-small">
                                                            <i class="lni lni-briefcase"></i>
                                                            <?php echo htmlspecialchars($companyName); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Status Toggle -->
                                            <td class="text-center">
                                                <label class="custom-switch">
                                                    <input type="checkbox" class="status-toggle"
                                                        data-jobid="<?php echo $jobId; ?>"
                                                        <?php echo $isActive ? 'checked' : ''; ?>
                                                        <?php echo $isFilled ? 'disabled' : ''; ?>
                                                        <?php echo $isPending ? 'disabled' : ''; ?>>
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </td>

                                            <!-- Posted Date -->
                                            <td class="text-center">
                                                <span class="text-muted small">
                                                    <i class="lni lni-calendar"></i><br>
                                                    <?php echo date('M d, Y', strtotime($row['DATEPOSTED'])); ?>
                                                </span>
                                            </td>

                                            <!-- Deadline -->
                                            <td class="text-center">
                                                <span
                                                    class="<?php echo $isExpired ? 'text-danger font-weight-bold' : 'text-muted'; ?> small">
                                                    <i class="lni lni-timer"></i><br>
                                                    <?php echo date('M d, Y', strtotime($row['DEADLINE'])); ?>
                                                </span>
                                            </td>

                                            <!-- Applications Count -->
                                            <td class="text-center">
                                                <?php if ($appCount > 0): ?>
                                                <a href="dashboard-manage-applications.php?jobid=<?php echo $jobId; ?>"
                                                    class="btn btn-sm theme-bg text-white rounded">
                                                    <i class="lni lni-users"></i>
                                                    <span class="badge badge-light ml-1"><?php echo $appCount; ?></span>
                                                </a>
                                                <?php else: ?>
                                                <span class="text-muted small">
                                                    <i class="lni lni-inbox"></i><br>No apps
                                                </span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Screening Questions -->
                                            <td class="text-center">
                                                <?php if ($screeningCount > 0): ?>
                                                <a href="dashboard-screening-questions.php?jobid=<?php echo $jobId; ?>"
                                                    class="btn btn-sm btn-info rounded">
                                                    <i class="lni lni-question-circle"></i>
                                                    <span
                                                        class="badge badge-light ml-1"><?php echo $screeningCount; ?></span>
                                                </a>
                                                <?php else: ?>
                                                <a href="dashboard-screening-questions.php?jobid=<?php echo $jobId; ?>"
                                                    class="btn btn-sm btn-outline-warning rounded">
                                                    <i class="lni lni-plus"></i> Add
                                                </a>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Actions -->
                                            <td class="text-center mt-4 pt-4">
                                                <div class="d-flex justify-content-center mt-4 pt-2">
                                                    <a href="dashboard-job-details.php?id=<?php echo $jobId; ?>"
                                                        class="action-btn bg-light-info" title="View Details">
                                                        <i class="lni lni-eye"></i>
                                                    </a>

                                                    <a href="dashboard-jobs-edit.php?id=<?php echo $jobId; ?>"
                                                        class="action-btn bg-light-success" title="Edit Job">
                                                        <i class="lni lni-pencil"></i>
                                                    </a>

                                                    <a href="?type=delete&id=<?php echo $jobId; ?>"
                                                        class="action-btn bg-light-danger" title="Delete Job"
                                                        onclick="return confirm('Are you sure you want to delete this job? This will also delete all applications and screening questions.');">
                                                        <i class="lni lni-trash-can"></i>
                                                    </a>
                                                </div>
                                                <div class="h-x1"></div>
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
                                <i class="lni lni-empty-file"></i>
                                <h4>No Jobs Found</h4>
                                <p class="text-muted">
                                    <?php if (!empty($searchTerm) || !empty($statusFilter)): ?>
                                    No jobs match your search criteria. Try adjusting your filters.
                                    <?php else: ?>
                                    You haven't posted any jobs yet. Start recruiting by posting your first job!
                                    <?php endif; ?>
                                </p>
                                <?php if (!empty($searchTerm) || !empty($statusFilter)): ?>
                                <a href="dashboard-manage-jobs.php" class="btn theme-bg text-white mt-3">
                                    <i class="lni lni-reload"></i> Clear Filters
                                </a>
                                <?php else: ?>
                                <a href="dashboard-post-job.php" class="btn btn-outline-secondary mt-3 pt-4">
                                    <i class="lni lni-plus"></i> Post Your First Job
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
        // Status Toggle Handler
        $('.status-toggle').change(function() {
            const toggle = $(this);
            const jobId = toggle.data('jobid');

            // Disable toggle during request
            toggle.prop('disabled', true);

            // Make request
            window.location.href = '?toggle_status=1&jobid=' + jobId;
        });

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
    </script>
</body>

</html>