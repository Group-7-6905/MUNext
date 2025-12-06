<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';
require '../include/toast.php';
require_once '../include/email-functions.php';



// ==================== HANDLE JOB ACTIONS ====================
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $jobid = isset($_POST['jobid']) ? (int)$_POST['jobid'] : 0;
    
    if ($jobid > 0) {
        switch ($action) {
            case 'approve':
                $updateQuery = "UPDATE tbljob SET JOBSTATUS = 'Active' WHERE JOBID = ?";
                $stmt = mysqli_prepare($con, $updateQuery);
                mysqli_stmt_bind_param($stmt, "i", $jobid);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Log activity
                    $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                                VALUES (?, 'JOB_APPROVED', 'Admin approved a pending job posting', ?, NOW())";
                    $logStmt = mysqli_prepare($con, $logQuery);
                    $ip = getClientIP();
                    mysqli_stmt_bind_param($logStmt, "is", $session_id, $ip);
                    mysqli_stmt_execute($logStmt);


                     // Get employer details for email
                    $detailsQuery = "SELECT 
                                    j.*, 
                                    u.EMAIL, u.FNAME, u.ONAME, c.COMPANYNAME 
                                    FROM tbljob j
                                    LEFT JOIN tblusers u ON j.EMPLOYERID = u.USERID
                                    LEFT JOIN tblcompany c ON j.EMPLOYERID = c.USERID
                                    WHERE j.JOBID = ?";
                    $detailsStmt = mysqli_prepare($con, $detailsQuery);
                    mysqli_stmt_bind_param($detailsStmt, "i", $jobid);
                    mysqli_stmt_execute($detailsStmt);
                    $detailsResult = mysqli_stmt_get_result($detailsStmt);
                    
                    if ($details = mysqli_fetch_assoc($detailsResult)) {
                        $employerName = $details['FNAME'] . ' ' . $details['ONAME'];
                        $companyName = $details['COMPANYNAME'] ?? 'Your Company';
                        $companyEmail = $details['EMAIL'] ?? $details['COMPANYEMAIL'];
                        $jobTitle = $details['JOBTITLE'] ?? 'Job Title';
                        
                        // Send approval email
                         sendJobPostedEmail($con, $employerEmail, $employerName, $jobTitle, $jobId);
                    }
                   
                    Toast::success('Job approved successfully!');
                } else {
                    Toast::error('Failed to approve job.');
                }
                mysqli_stmt_close($stmt);
                break;
                
            case 'reject':
                $updateQuery = "UPDATE tbljob SET JOBSTATUS = 'Rejected' WHERE JOBID = ?";
                $stmt = mysqli_prepare($con, $updateQuery);
                mysqli_stmt_bind_param($stmt, "i", $jobid);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Log activity
                    $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                                VALUES (?, 'JOB_REJECTED', 'Admin rejected a pending job posting', ?, NOW())";
                    $logStmt = mysqli_prepare($con, $logQuery);
                    $ip = getClientIP();
                    mysqli_stmt_bind_param($logStmt, "is", $session_id, $ip);
                    mysqli_stmt_execute($logStmt);
                    
                    Toast::warning('Job rejected successfully!');
                } else {
                    Toast::error('Failed to reject job.');
                }
                mysqli_stmt_close($stmt);
                break;
                
            case 'suspend':
                $updateQuery = "UPDATE tbljob SET JOBSTATUS = 'Suspended' WHERE JOBID = ?";
                $stmt = mysqli_prepare($con, $updateQuery);
                mysqli_stmt_bind_param($stmt, "i", $jobid);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Log activity
                    $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                                VALUES (?, 'JOB_SUSPENDED', 'Admin suspended a pending job posting', ?, NOW())";
                    $logStmt = mysqli_prepare($con, $logQuery);
                    $ip = getClientIP();
                    mysqli_stmt_bind_param($logStmt, "is", $session_id, $ip);
                    mysqli_stmt_execute($logStmt);
                    
                    Toast::warning('Job suspended successfully!');
                } else {
                    Toast::error('Failed to suspend job.');
                }
                mysqli_stmt_close($stmt);
                break;
                
            case 'delete':
                // Soft delete - mark as deleted
                $deleteQuery = "UPDATE tbljob SET JOBSTATUS = 'Deleted' WHERE JOBID = ?";
                $stmt = mysqli_prepare($con, $deleteQuery);
                mysqli_stmt_bind_param($stmt, "i", $jobid);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Log activity
                    $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                                VALUES (?, 'JOB_DELETED', 'Admin deleted a pending job posting', ?, NOW())";
                    $logStmt = mysqli_prepare($con, $logQuery);
                    $ip = getClientIP();
                    mysqli_stmt_bind_param($logStmt, "is", $session_id, $ip);
                    mysqli_stmt_execute($logStmt);
                    
                    Toast::success('Job deleted successfully!');
                } else {
                    Toast::error('Failed to delete job.');
                }
                mysqli_stmt_close($stmt);
                break;
        }
        
        header('location: admin-pending-jobs.php?' . http_build_query($_GET));
        exit();
    }
}

// ==================== BULK ACTIONS ====================
if (isset($_POST['bulk_action']) && isset($_POST['selected_jobs'])) {
    $bulk_action = $_POST['bulk_action'];
    $selected_jobs = $_POST['selected_jobs'];
    
    if (!empty($selected_jobs) && is_array($selected_jobs)) {
        $success_count = 0;
        
        foreach ($selected_jobs as $jobid) {
            $jobid = (int)$jobid;
            
            if ($jobid > 0) {
                switch ($bulk_action) {
                    case 'approve':
                        $updateQuery = "UPDATE tbljob SET JOBSTATUS = 'Active' WHERE JOBID = ?";
                        break;
                    case 'reject':
                        $updateQuery = "UPDATE tbljob SET JOBSTATUS = 'Rejected' WHERE JOBID = ?";
                        break;
                    case 'delete':
                        $updateQuery = "UPDATE tbljob SET JOBSTATUS = 'Deleted' WHERE JOBID = ?";
                        break;
                    default:
                        continue 2;
                }
                
                $stmt = mysqli_prepare($con, $updateQuery);
                mysqli_stmt_bind_param($stmt, "i", $jobid);
                
                if (mysqli_stmt_execute($stmt)) {
                    $success_count++;
                }
                mysqli_stmt_close($stmt);
            }
        }
        
        if ($success_count > 0) {
            // Log bulk action
            $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                        VALUES (?, 'BULK_JOB_ACTION', 'Admin performed bulk action on $success_count pending jobs', ?, NOW())";
            $logStmt = mysqli_prepare($con, $logQuery);
            $ip = getClientIP();
            mysqli_stmt_bind_param($logStmt, "is", $session_id, $ip);
            mysqli_stmt_execute($logStmt);
            
            Toast::success("$success_count job(s) processed successfully!");
        }
        
        header('location: admin-pending-jobs.php?' . http_build_query($_GET));
        exit();
    }
}

// ==================== PAGINATION SETUP ====================
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$records_per_page = 20;
$offset = ($page - 1) * $records_per_page;

// ==================== FILTER PARAMETERS ====================
$filter_company = isset($_GET['company']) ? (int)$_GET['company'] : 0;
$filter_category = isset($_GET['category']) ? mysqli_real_escape_string($con, $_GET['category']) : '';
$filter_date_from = isset($_GET['date_from']) ? mysqli_real_escape_string($con, $_GET['date_from']) : '';
$filter_date_to = isset($_GET['date_to']) ? mysqli_real_escape_string($con, $_GET['date_to']) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';

// ==================== BUILD QUERY ====================
$where_clauses = ["j.JOBSTATUS = 'Pending'"];
$params = [];
$types = '';

if ($filter_company > 0) {
    $where_clauses[] = "j.COMPANYID = ?";
    $params[] = $filter_company;
    $types .= 'i';
}

if (!empty($filter_category)) {
    $where_clauses[] = "j.CATEGORY = ?";
    $params[] = $filter_category;
    $types .= 's';
}

if (!empty($filter_date_from)) {
    $where_clauses[] = "DATE(j.DATEPOSTED) >= ?";
    $params[] = $filter_date_from;
    $types .= 's';
}

if (!empty($filter_date_to)) {
    $where_clauses[] = "DATE(j.DATEPOSTED) <= ?";
    $params[] = $filter_date_to;
    $types .= 's';
}

if (!empty($search)) {
    $where_clauses[] = "(j.JOBTITLE LIKE ? OR j.JOBDESCRIPTION LIKE ? OR c.COMPANYNAME LIKE ? OR j.JOBLOCATION LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= 'ssss';
}

$where_sql = 'WHERE ' . implode(' AND ', $where_clauses);

// ==================== GET TOTAL RECORDS ====================
$count_query = "SELECT COUNT(*) as total 
                FROM tbljob j 
                LEFT JOIN tblcompany c ON j.COMPANYID = c.COMPANYID 
                $where_sql";
$count_stmt = mysqli_prepare($con, $count_query);

if (!empty($params)) {
    mysqli_stmt_bind_param($count_stmt, $types, ...$params);
}

mysqli_stmt_execute($count_stmt);
$total_records = mysqli_fetch_assoc(mysqli_stmt_get_result($count_stmt))['total'];
$total_pages = ceil($total_records / $records_per_page);
mysqli_stmt_close($count_stmt);

// ==================== GET PENDING JOBS ====================
$query = "SELECT j.*, c.COMPANYNAME, c.COMPANYLOGO,
          (SELECT COUNT(*) FROM tbljobapplication WHERE JOBID = j.JOBID) as APPLICATION_COUNT
          FROM tbljob j 
          LEFT JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
          $where_sql 
          ORDER BY j.DATEPOSTED DESC 
          LIMIT ? OFFSET ?";

$params[] = $records_per_page;
$params[] = $offset;
$types .= 'ii';

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$jobs = mysqli_stmt_get_result($stmt);

// ==================== GET STATISTICS ====================
$totalPendingQuery = "SELECT COUNT(*) as total FROM tbljob WHERE JOBSTATUS = 'Pending'";
$totalPending = mysqli_fetch_assoc(mysqli_query($con, $totalPendingQuery))['total'];

$todayPendingQuery = "SELECT COUNT(*) as total FROM tbljob WHERE JOBSTATUS = 'Pending' AND DATE(DATEPOSTED) = CURDATE()";
$todayPending = mysqli_fetch_assoc(mysqli_query($con, $todayPendingQuery))['total'];

$weekPendingQuery = "SELECT COUNT(*) as total FROM tbljob WHERE JOBSTATUS = 'Pending' AND DATEPOSTED >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
$weekPending = mysqli_fetch_assoc(mysqli_query($con, $weekPendingQuery))['total'];

$oldPendingQuery = "SELECT COUNT(*) as total FROM tbljob WHERE JOBSTATUS = 'Pending' AND DATEPOSTED < DATE_SUB(NOW(), INTERVAL 7 DAY)";
$oldPending = mysqli_fetch_assoc(mysqli_query($con, $oldPendingQuery))['total'];

// ==================== GET COMPANIES FOR FILTER ====================
$companiesQuery = "SELECT COMPANYID, COMPANYNAME FROM tblcompany ORDER BY COMPANYNAME ASC";
$companiesResult = mysqli_query($con, $companiesQuery);
?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pending Jobs Approval - Admin Dashboard | MUNext</title>
    <link href="assets/css/styles.css" rel="stylesheet">


    <link rel="stylesheet" href="assets/css/custom-pending.css">
</head>

<body>
    <div id="main-wrapper">
        <?php include 'header.php' ?>
        <div class="clearfix"></div>

        <div class="dashboard-wrap bg-light">
            <a class="mobNavigation" data-toggle="collapse" href="#MobNav" role="button">
                <i class="fas fa-bars mr-2"></i>Dashboard Navigation
            </a>

            <?php include 'sidenav.php' ?>

            <div class="dashboard-content">
                <!-- Page Header -->
                <div class="page-header-section">
                    <h1 class="page-title">
                        <i class="lni lni-hourglass mr-2"></i>Pending Jobs Approval
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Job Management</a></li>
                            <li class="breadcrumb-item active">Pending Jobs</li>
                        </ol>
                    </nav>
                </div>

                <div class="container-fluid">
                    <!-- Statistics -->
                    <div class="stats-grid">
                        <div class="stat-card total">
                            <span class="stat-number"><?php echo number_format($totalPending); ?></span>
                            <span class="stat-label">Total Pending</span>
                        </div>
                        <div class="stat-card today">
                            <span class="stat-number"><?php echo number_format($todayPending); ?></span>
                            <span class="stat-label">Today</span>
                        </div>
                        <div class="stat-card week">
                            <span class="stat-number"><?php echo number_format($weekPending); ?></span>
                            <span class="stat-label">This Week</span>
                        </div>
                        <div class="stat-card old">
                            <span class="stat-number"><?php echo number_format($oldPending); ?></span>
                            <span class="stat-label">Older than 7 Days</span>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="filter-section">
                        <div class="filter-header">
                            <h6 class="filter-title">
                                <i class="lni lni-funnel mr-2"></i>Filter Pending Jobs
                            </h6>
                        </div>

                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Search</label>
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Job title, company, location..."
                                        value="<?php echo htmlspecialchars($search); ?>">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Company</label>
                                    <select name="company" class="form-control">
                                        <option value="0">All Companies</option>
                                        <?php 
                                        mysqli_data_seek($companiesResult, 0);
                                        while ($company = mysqli_fetch_assoc($companiesResult)): 
                                        ?>
                                        <option value="<?php echo $company['COMPANYID']; ?>"
                                            <?php echo $filter_company == $company['COMPANYID'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($company['COMPANYNAME']); ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Date From</label>
                                    <input type="date" name="date_from" class="form-control"
                                        value="<?php echo htmlspecialchars($filter_date_from); ?>">
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Date To</label>
                                    <input type="date" name="date_to" class="form-control"
                                        value="<?php echo htmlspecialchars($filter_date_to); ?>">
                                </div>

                                <div class="col-md-1 mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn-filter" style="width: 100%;">
                                            <i class="lni lni-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <a href="admin-pending-jobs.php" class="btn-reset">
                                        <i class="lni lni-reload mr-1"></i>Reset Filters
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Bulk Actions -->
                    <?php if (mysqli_num_rows($jobs) > 0): ?>
                    <form method="POST" id="bulkForm">
                        <div class="bulk-actions-bar">
                            <input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)">
                            <label for="selectAll" style="margin: 0; font-weight: 600;">Select All</label>
                            <select name="bulk_action" class="form-control" style="width: auto;">
                                <option value="">Bulk Actions</option>
                                <option value="approve">Approve Selected</option>
                                <option value="reject">Reject Selected</option>
                                <option value="delete">Delete Selected</option>
                            </select>
                            <button type="submit" class="btn-bulk-apply" onclick="return confirmBulkAction()">
                                Apply
                            </button>
                            <span id="selectedCount" style="margin-left: auto; color: #6c757d;">
                                0 selected
                            </span>
                        </div>

                        <!-- Jobs Table -->
                        <div class="jobs-table-card">
                            <div class="table-header">
                                <i class="lni lni-hourglass mr-2"></i>Pending Jobs List
                                <span style="float: right; font-weight: normal;">
                                    Total: <?php echo number_format($total_records); ?> jobs awaiting approval
                                </span>
                            </div>

                            <div class="table-responsive">
                                <table class="jobs-table">
                                    <thead>
                                        <tr>
                                            <th width="30">
                                                <input type="checkbox" disabled style="visibility: hidden;">
                                            </th>
                                            <th>ID</th>
                                            <th>Job Details</th>
                                            <th>Category</th>
                                            <th>Type</th>
                                            <th>Submitted</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        mysqli_data_seek($jobs, 0);
                                        while ($job = mysqli_fetch_assoc($jobs)): 
                                            $daysSincePosted = floor((time() - strtotime($job['DATEPOSTED'])) / 86400);
                                        ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="selected_jobs[]"
                                                    value="<?php echo $job['JOBID']; ?>" class="job-checkbox"
                                                    onchange="updateSelectedCount()">
                                            </td>
                                            <td><?php echo $job['JOBID']; ?></td>
                                            <td>
                                                <div class="job-info">

                                                    <?php if (!empty($job['COMPANYLOGO'])): ?>
                                                    <img src="<?php echo $path.htmlspecialchars($job['COMPANYLOGO']); ?>"
                                                        alt="Logo" class="company-logo"
                                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <?php endif; ?>
                                                    <div class="company-logo"
                                                        style="<?php echo !empty($job['COMPANYLOGO']) ? 'display:none;' : ''; ?>">
                                                        <?php echo strtoupper(substr($job['COMPANYNAME'], 0, 1)); ?>
                                                    </div>

                                                    <div class="job-details">
                                                        <span class="job-title">
                                                            <?php echo htmlspecialchars($job['JOBTITLE']); ?>
                                                        </span>
                                                        <span class="company-name">
                                                            <?php echo htmlspecialchars($job['COMPANYNAME']); ?>
                                                        </span>
                                                        <?php if (!empty($job['JOBLOCATION'])): ?>
                                                        <span class="job-location">
                                                            <i class="lni lni-map-marker"></i>
                                                            <?php echo htmlspecialchars($job['JOBLOCATION']); ?>
                                                        </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="category-badge">
                                                    <?php echo htmlspecialchars($job['CATEGORY'] ?? 'N/A'); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($job['EMPLOYMENTTYPE'] ?? 'N/A'); ?></td>
                                            <td>
                                                <?php echo date('M d, Y', strtotime($job['DATEPOSTED'])); ?>
                                                <br>
                                                <span
                                                    class="time-badge <?php echo $daysSincePosted <= 1 ? 'time-new' : ($daysSincePosted > 7 ? 'time-old' : ''); ?>">
                                                    <?php 
                                                    if ($daysSincePosted == 0) {
                                                        echo 'Today';
                                                    } elseif ($daysSincePosted == 1) {
                                                        echo 'Yesterday';
                                                    } else {
                                                        echo $daysSincePosted . ' days ago';
                                                    }
                                                    ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="status-badge">
                                                    <i class="lni lni-hourglass"></i> Pending Review
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button type="button" title="View Job" class="btn-action btn-view"
                                                        onclick="viewJob(<?php echo $job['JOBID']; ?>)">
                                                        <i class="lni lni-eye"></i>
                                                    </button>

                                                    <button type="button" title="Approve Job"
                                                        class="btn-action btn-approve"
                                                        onclick="approveJob(<?php echo $job['JOBID']; ?>)">
                                                        <i class="lni lni-checkmark"></i>
                                                    </button>

                                                    <button type="button" title="Reject Job"
                                                        class="btn-action btn-reject"
                                                        onclick="rejectJob(<?php echo $job['JOBID']; ?>)">
                                                        <i class="lni lni-close"></i>
                                                    </button>

                                                    <button type="button" title="Delete Job"
                                                        class="btn-action btn-delete"
                                                        onclick="deleteJob(<?php echo $job['JOBID']; ?>)">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                            <div class="pagination-wrapper">
                                <div class="pagination-info">
                                    Showing <?php echo $offset + 1; ?> to
                                    <?php echo min($offset + $records_per_page, $total_records); ?>
                                    of <?php echo number_format($total_records); ?> entries
                                </div>
                                <nav>
                                    <ul class="pagination mb-0">
                                        <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="?page=<?php echo $page - 1; ?>&<?php echo http_build_query(array_diff_key($_GET, ['page' => ''])); ?>">
                                                Previous
                                            </a>
                                        </li>
                                        <?php endif; ?>

                                        <?php
                                        $start_page = max(1, $page - 2);
                                        $end_page = min($total_pages, $page + 2);
                                        
                                        for ($i = $start_page; $i <= $end_page; $i++):
                                        ?>
                                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                            <a class="page-link"
                                                href="?page=<?php echo $i; ?>&<?php echo http_build_query(array_diff_key($_GET, ['page' => ''])); ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                        <?php endfor; ?>

                                        <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="?page=<?php echo $page + 1; ?>&<?php echo http_build_query(array_diff_key($_GET, ['page' => ''])); ?>">
                                                Next
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            </div>
                            <?php endif; ?>
                        </div>
                    </form>

                    <?php else: ?>
                    <div class="jobs-table-card">
                        <div class="table-header">
                            <i class="lni lni-hourglass mr-2"></i>Pending Jobs List
                        </div>
                        <div class="empty-state">
                            <i class="lni lni-checkmark-circle empty-state-icon"></i>
                            <h5>No Pending Jobs!</h5>
                            <p>All jobs have been reviewed. Great job!</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <?php include 'footer.php' ?>
            </div>
        </div>

        <a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>
    </div>

    <!-- Job Details Modal -->
    <div class="modal fade" id="jobModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: var(--warning-color); color: var(--text-dark);">
                    <h5 class="modal-title">Job Details - Pending Approval</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="jobModalContent">
                    <div class="text-center py-5">
                        <i class="lni lni-spinner-arrow rotating" style="font-size: 3rem;"></i>
                        <p>Loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden forms for individual actions -->
    <form id="approveForm" method="POST" style="display: none;">
        <input type="hidden" name="jobid" id="approveJobId">
        <input type="hidden" name="action" value="approve">
    </form>

    <form id="rejectForm" method="POST" style="display: none;">
        <input type="hidden" name="jobid" id="rejectJobId">
        <input type="hidden" name="action" value="reject">
    </form>

    <form id="deleteForm" method="POST" style="display: none;">
        <input type="hidden" name="jobid" id="deleteJobId">
        <input type="hidden" name="action" value="delete">
    </form>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/custom.js"></script>

    <script>
    function viewJob(jobid) {
        $('#jobModal').modal('show');
        $('#jobModalContent').html(
            '<div class="text-center py-5"><i class="lni lni-spinner-arrow rotating" style="font-size: 3rem;"></i><p>Loading...</p></div>'
        );

        $.ajax({
            url: 'admin-get-job-details.php',
            method: 'GET',
            data: {
                jobid: jobid
            },
            success: function(response) {
                $('#jobModalContent').html(response);
            },
            error: function() {
                $('#jobModalContent').html(
                    '<div class="alert alert-danger">Failed to load job details.</div>');
            }
        });
    }

    function approveJob(jobid) {
        if (confirm('Are you sure you want to approve this job posting?')) {
            document.getElementById('approveJobId').value = jobid;
            document.getElementById('approveForm').submit();
        }
    }

    function rejectJob(jobid) {
        if (confirm('Are you sure you want to reject this job posting?')) {
            document.getElementById('rejectJobId').value = jobid;
            document.getElementById('rejectForm').submit();
        }
    }

    function deleteJob(jobid) {
        if (confirm('Are you sure you want to delete this job posting permanently?')) {
            document.getElementById('deleteJobId').value = jobid;
            document.getElementById('deleteForm').submit();
        }
    }

    function toggleSelectAll(source) {
        const checkboxes = document.querySelectorAll('.job-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = source.checked;
        });
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const checkboxes = document.querySelectorAll('.job-checkbox:checked');
        const count = checkboxes.length;
        document.getElementById('selectedCount').textContent = count + ' selected';

        // Update select all checkbox
        const allCheckboxes = document.querySelectorAll('.job-checkbox');
        document.getElementById('selectAll').checked = count === allCheckboxes.length && count > 0;
    }

    function confirmBulkAction() {
        const action = document.querySelector('select[name="bulk_action"]').value;
        const checkedCount = document.querySelectorAll('.job-checkbox:checked').length;

        if (!action) {
            alert('Please select a bulk action.');
            return false;
        }

        if (checkedCount === 0) {
            alert('Please select at least one job.');
            return false;
        }

        let message = '';
        if (action === 'approve') {
            message = `Are you sure you want to approve ${checkedCount} job(s)?`;
        } else if (action === 'reject') {
            message = `Are you sure you want to reject ${checkedCount} job(s)?`;
        } else if (action === 'delete') {
            message = `Are you sure you want to delete ${checkedCount} job(s) permanently?`;
        }

        return confirm(message);
    }
    </script>

    <?php echo Toast::render(); ?>
</body>

</html>