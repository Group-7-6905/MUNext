<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';
require '../include/toast.php';



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
                                VALUES (?, 'JOB_APPROVED', 'Admin approved a job posting', ?, NOW())";
                    $logStmt = mysqli_prepare($con, $logQuery);
                    $ip = getClientIP();
                    mysqli_stmt_bind_param($logStmt, "is", $session_id, $ip);
                    mysqli_stmt_execute($logStmt);
                    
                    Toast::success('Job approved successfully!');
                } else {
                    Toast::error('Failed to approve job.');
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
                                VALUES (?, 'JOB_SUSPENDED', 'Admin suspended a job posting', ?, NOW())";
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
                                VALUES (?, 'JOB_DELETED', 'Admin deleted a job posting', ?, NOW())";
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
                
            case 'close':
                $updateQuery = "UPDATE tbljob SET JOBSTATUS = 'Closed' WHERE JOBID = ?";
                $stmt = mysqli_prepare($con, $updateQuery);
                mysqli_stmt_bind_param($stmt, "i", $jobid);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Log activity
                    $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                                VALUES (?, 'JOB_CLOSED', 'Admin closed a job posting', ?, NOW())";
                    $logStmt = mysqli_prepare($con, $logQuery);
                    $ip = getClientIP();
                    mysqli_stmt_bind_param($logStmt, "is", $session_id, $ip);
                    mysqli_stmt_execute($logStmt);
                    
                    Toast::info('Job closed successfully!');
                } else {
                    Toast::error('Failed to close job.');
                }
                mysqli_stmt_close($stmt);
                break;
        }
        
        header('location: admin-all-jobs.php?' . http_build_query($_GET));
        exit();
    }
}

// ==================== PAGINATION SETUP ====================
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$records_per_page = 20;
$offset = ($page - 1) * $records_per_page;

// ==================== FILTER PARAMETERS ====================
$filter_status = isset($_GET['status']) ? mysqli_real_escape_string($con, $_GET['status']) : '';
$filter_company = isset($_GET['company']) ? (int)$_GET['company'] : 0;
$filter_category = isset($_GET['category']) ? mysqli_real_escape_string($con, $_GET['category']) : '';
$filter_date_from = isset($_GET['date_from']) ? mysqli_real_escape_string($con, $_GET['date_from']) : '';
$filter_date_to = isset($_GET['date_to']) ? mysqli_real_escape_string($con, $_GET['date_to']) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';

// ==================== BUILD QUERY ====================
$where_clauses = ["1=1"];
$params = [];
$types = '';

// Default to Pending status for verification page
if (!empty($filter_status)) {
    $where_clauses[] = "JOBSTATUS = ?";
    $params[] = $filter_status;
    $types .= 's';
} else {
    $where_clauses[] = "JOBSTATUS != 'Pending'";
}

if (!empty($filter_status)) {
    $where_clauses[] = "j.JOBSTATUS = ?";
    $params[] = $filter_status;
    $types .= 's';
}

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

// ==================== GET JOBS ====================
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
$totalJobsQuery = "SELECT COUNT(*) as total FROM tbljob";
$totalJobs = mysqli_fetch_assoc(mysqli_query($con, $totalJobsQuery))['total'];

$activeJobsQuery = "SELECT COUNT(*) as total FROM tbljob WHERE JOBSTATUS = 'Active'";
$activeJobs = mysqli_fetch_assoc(mysqli_query($con, $activeJobsQuery))['total'];

$pendingJobsQuery = "SELECT COUNT(*) as total FROM tbljob WHERE JOBSTATUS = 'Pending'";
$pendingJobs = mysqli_fetch_assoc(mysqli_query($con, $pendingJobsQuery))['total'];

$closedJobsQuery = "SELECT COUNT(*) as total FROM tbljob WHERE JOBSTATUS = 'Closed'";
$closedJobs = mysqli_fetch_assoc(mysqli_query($con, $closedJobsQuery))['total'];

$suspendedJobsQuery = "SELECT COUNT(*) as total FROM tbljob WHERE JOBSTATUS = 'Suspended'";
$suspendedJobs = mysqli_fetch_assoc(mysqli_query($con, $suspendedJobsQuery))['total'];

// ==================== GET COMPANIES FOR FILTER ====================
$companiesQuery = "SELECT COMPANYID, COMPANYNAME FROM tblcompany ORDER BY COMPANYNAME ASC";
$companiesResult = mysqli_query($con, $companiesQuery);
?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>All Jobs Management - Admin Dashboard | MUNext</title>
    <link href="assets/css/styles.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/custom-dashboard.css">
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
                        <i class="lni lni-briefcase mr-2"></i>All Jobs Management
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Job Management</a></li>
                            <li class="breadcrumb-item active">All Jobs</li>
                        </ol>
                    </nav>
                </div>

                <div class="container-fluid">
                    <!-- Statistics -->
                    <div class="stats-grid">
                        <div class="stat-card total">
                            <span class="stat-number"><?php echo number_format($totalJobs); ?></span>
                            <span class="stat-label">Total Jobs</span>
                        </div>
                        <div class="stat-card active">
                            <span class="stat-number"><?php echo number_format($activeJobs); ?></span>
                            <span class="stat-label">Active Jobs</span>
                        </div>
                        <div class="stat-card pending">
                            <span class="stat-number"><?php echo number_format($pendingJobs); ?></span>
                            <span class="stat-label">Pending Approval</span>
                        </div>
                        <div class="stat-card closed">
                            <span class="stat-number"><?php echo number_format($closedJobs); ?></span>
                            <span class="stat-label">Closed</span>
                        </div>
                        <div class="stat-card suspended">
                            <span class="stat-number"><?php echo number_format($suspendedJobs); ?></span>
                            <span class="stat-label">Suspended</span>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="filter-section">
                        <div class="filter-header">
                            <h6 class="filter-title">
                                <i class="lni lni-funnel mr-2"></i>Filter Jobs
                            </h6>
                        </div>

                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Search</label>
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Job title, company, location..."
                                        value="<?php echo htmlspecialchars($search); ?>">
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="Active"
                                            <?php echo $filter_status === 'Active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="Pending"
                                            <?php echo $filter_status === 'Pending' ? 'selected' : ''; ?>>Pending
                                        </option>
                                        <option value="Closed"
                                            <?php echo $filter_status === 'Closed' ? 'selected' : ''; ?>>Closed
                                        </option>
                                        <option value="Suspended"
                                            <?php echo $filter_status === 'Suspended' ? 'selected' : ''; ?>>Suspended
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-2 mb-3">
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
                                    <a href="admin-all-jobs.php" class="btn-reset">
                                        <i class="lni lni-reload mr-1"></i>Reset Filters
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Jobs Table -->
                    <div class="jobs-table-card">
                        <div class="table-header">
                            <i class="lni lni-briefcase mr-2"></i>Jobs List
                            <span style="float: right; font-weight: normal;">
                                Total: <?php echo number_format($total_records); ?> jobs
                            </span>
                        </div>

                        <?php if (mysqli_num_rows($jobs) > 0): ?>
                        <div class="table-responsive">
                            <table class="jobs-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Job Details</th>
                                        <th>Category</th>
                                        <th>Type</th>
                                        <th>Applications</th>
                                        <th>Status</th>
                                        <th>Posted Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($job = mysqli_fetch_assoc($jobs)): ?>
                                    <tr>
                                        <td><?php echo $job['JOBID']; ?></td>
                                        <td>
                                            <div class="job-info">

                                                <?php if (!empty($job['COMPANYLOGO'])): ?>
                                                <img src="<?php echo $path.htmlspecialchars($job['COMPANYLOGO']); ?>"
                                                    alt="Logo" class="company-logo-small"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <?php endif; ?>
                                                <div class="company-logo-placeholder"
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
                                            <span class="application-count">
                                                <?php echo $job['APPLICATION_COUNT']; ?>
                                                <?php echo $job['APPLICATION_COUNT'] == 1 ? 'applicant' : 'applicants'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = 'status-active';
                                            $status = $job['JOBSTATUS'];
                                            if ($status == 'Pending') {
                                                $statusClass = 'status-pending';
                                            } elseif ($status == 'Suspended') {
                                                $statusClass = 'status-suspended';
                                            } elseif ($status == 'Inactive') {
                                                $statusClass = 'status-pending';
                                            } elseif ($status == 'Closed') {
                                                $statusClass = 'status-closed';
                                            }
                                            ?>
                                            <span class="status-badge <?php echo $statusClass; ?>">
                                                <?php echo htmlspecialchars($status); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($job['DATEPOSTED'])); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-action btn-view"
                                                    onclick="viewJob(<?php echo $job['JOBID']; ?>)" title="View">
                                                    <i class="lni lni-eye"></i>
                                                </button>

                                                <?php if ($job['JOBSTATUS'] == 'Pending'): ?>
                                                <form method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Approve this job?')">
                                                    <input type="hidden" name="jobid"
                                                        value="<?php echo $job['JOBID']; ?>">
                                                    <input type="hidden" name="action" value="approve">
                                                    <button type="submit" class="btn-action btn-approve"
                                                        title="Approve">
                                                        <i class="lni lni-checkmark"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>

                                                <?php if ($job['JOBSTATUS'] == 'Active' || $job['JOBSTATUS'] == 'Pending'): ?>
                                                <form method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Suspend this job?')">
                                                    <input type="hidden" name="jobid"
                                                        value="<?php echo $job['JOBID']; ?>">
                                                    <input type="hidden" name="action" value="suspend">
                                                    <button type="submit" class="btn-action btn-suspend"
                                                        title="Suspend">
                                                        <i class="lni lni-ban"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>

                                                <?php if ($job['JOBSTATUS'] == 'Active'): ?>
                                                <form method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Close this job?')">
                                                    <input type="hidden" name="jobid"
                                                        value="<?php echo $job['JOBID']; ?>">
                                                    <input type="hidden" name="action" value="close">
                                                    <button type="submit" class="btn-action btn-close" title="Close">
                                                        <i class="lni lni-close"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>

                                                <form method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Delete this job permanently?')">
                                                    <input type="hidden" name="jobid"
                                                        value="<?php echo $job['JOBID']; ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <button type="submit" class="btn-action btn-delete" title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
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

                        <?php else: ?>
                        <div class="empty-state">
                            <i class="lni lni-briefcase empty-state-icon"></i>
                            <p>No jobs found.</p>
                        </div>
                        <?php endif; ?>
                    </div>
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
                <div class="modal-header">
                    <h5 class="modal-title">Job Details</h5>
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

        // Load job details via AJAX
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
    </script>

    <?php echo Toast::render(); ?>
</body>

</html>