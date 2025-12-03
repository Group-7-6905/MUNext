<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';
require '../include/toast.php';

// ==================== HANDLE APPLICATION ACTIONS ====================
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $applicationId = isset($_POST['application_id']) ? (int)$_POST['application_id'] : 0;
    
    if ($applicationId > 0) {
        switch ($action) {
            case 'approve':
                $updateQuery = "UPDATE tbljobapplication SET APPLICATIONSTATUS = 'Approved' WHERE ID = ?";
                $stmt = mysqli_prepare($con, $updateQuery);
                mysqli_stmt_bind_param($stmt, "i", $applicationId);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Log activity
                    $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                                VALUES (?, 'APPLICATION_APPROVED', 'Admin approved a job application', ?, NOW())";
                    $logStmt = mysqli_prepare($con, $logQuery);
                    $ip = getClientIP();
                    mysqli_stmt_bind_param($logStmt, "is", $session_id, $ip);
                    mysqli_stmt_execute($logStmt);
                    
                    Toast::success('Application approved successfully!');
                } else {
                    Toast::error('Failed to approve application.');
                }
                mysqli_stmt_close($stmt);
                break;
                
            case 'reject':
                $updateQuery = "UPDATE tbljobapplication SET APPLICATIONSTATUS = 'Rejected' WHERE ID = ?";
                $stmt = mysqli_prepare($con, $updateQuery);
                mysqli_stmt_bind_param($stmt, "i", $applicationId);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Log activity
                    $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                                VALUES (?, 'APPLICATION_REJECTED', 'Admin rejected a job application', ?, NOW())";
                    $logStmt = mysqli_prepare($con, $logQuery);
                    $ip = getClientIP();
                    mysqli_stmt_bind_param($logStmt, "is", $session_id, $ip);
                    mysqli_stmt_execute($logStmt);
                    
                    Toast::warning('Application rejected!');
                } else {
                    Toast::error('Failed to reject application.');
                }
                mysqli_stmt_close($stmt);
                break;
                
            case 'shortlist':
                $updateQuery = "UPDATE tbljobapplication SET APPLICATIONSTATUS = 'Shortlisted' WHERE ID = ?";
                $stmt = mysqli_prepare($con, $updateQuery);
                mysqli_stmt_bind_param($stmt, "i", $applicationId);
                
                if (mysqli_stmt_execute($stmt)) {
                    Toast::info('Application shortlisted!');
                } else {
                    Toast::error('Failed to shortlist application.');
                }
                mysqli_stmt_close($stmt);
                break;
                
            case 'delete':
                $deleteQuery = "DELETE FROM tbljobapplication WHERE ID = ?";
                $stmt = mysqli_prepare($con, $deleteQuery);
                mysqli_stmt_bind_param($stmt, "i", $applicationId);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Log activity
                    $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                                VALUES (?, 'APPLICATION_DELETED', 'Admin deleted a job application', ?, NOW())";
                    $logStmt = mysqli_prepare($con, $logQuery);
                    $ip = getClientIP();
                    mysqli_stmt_bind_param($logStmt, "is", $session_id, $ip);
                    mysqli_stmt_execute($logStmt);
                    
                    Toast::success('Application deleted successfully!');
                } else {
                    Toast::error('Failed to delete application.');
                }
                mysqli_stmt_close($stmt);
                break;
        }
        
        header('location: admin-all-applications.php?' . http_build_query($_GET));
        exit();
    }
}

// ==================== PAGINATION SETUP ====================
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$records_per_page = 20;
$offset = ($page - 1) * $records_per_page;

// ==================== FILTER PARAMETERS ====================
$jobIdFilter = isset($_GET['jobid']) ? (int)$_GET['jobid'] : 0;
$filter_status = isset($_GET['status']) ? mysqli_real_escape_string($con, $_GET['status']) : '';
$filter_job = isset($_GET['job']) ? (int)$_GET['job'] : 0;
$filter_company = isset($_GET['company']) ? (int)$_GET['company'] : 0;
$filter_date_from = isset($_GET['date_from']) ? mysqli_real_escape_string($con, $_GET['date_from']) : '';
$filter_date_to = isset($_GET['date_to']) ? mysqli_real_escape_string($con, $_GET['date_to']) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';

// ==================== BUILD QUERY ====================
$where_clauses = ["1=1"];
$params = [];
$types = '';


if ($jobIdFilter > 0) {
    $where_clauses[] = "ja.JOBID = ?";
    $params[] = $jobIdFilter;
    $types .= 'i';
}

if (!empty($filter_status)) {
    $where_clauses[] = "ja.APPLICATIONSTATUS = ?";
    $params[] = $filter_status;
    $types .= 's';
}

if ($filter_job > 0) {
    $where_clauses[] = "ja.JOBID = ?";
    $params[] = $filter_job;
    $types .= 'i';
}

if ($filter_company > 0) {
    $where_clauses[] = "j.COMPANYID = ?";
    $params[] = $filter_company;
    $types .= 'i';
}

if (!empty($filter_date_from)) {
    $where_clauses[] = "DATE(ja.DATEAPPLIED) >= ?";
    $params[] = $filter_date_from;
    $types .= 's';
}

if (!empty($filter_date_to)) {
    $where_clauses[] = "DATE(ja.DATEAPPLIED) <= ?";
    $params[] = $filter_date_to;
    $types .= 's';
}

if (!empty($search)) {
    $where_clauses[] = "(a.FNAME LIKE ? OR a.OTHERNAMES LIKE ? OR a.EMAILADDRESS LIKE ? OR j.JOBTITLE LIKE ? OR c.COMPANYNAME LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= 'sssss';
}

$where_sql = 'WHERE ' . implode(' AND ', $where_clauses);

// ==================== GET TOTAL RECORDS ====================
$count_query = "SELECT COUNT(*) as total 
                FROM tbljobapplication ja
                INNER JOIN tbljob j ON ja.JOBID = j.JOBID
                INNER JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
                LEFT JOIN tblapplicants a ON ja.APPLICANTID = a.APPLICANTID
                $where_sql";
$count_stmt = mysqli_prepare($con, $count_query);

if (!empty($params)) {
    mysqli_stmt_bind_param($count_stmt, $types, ...$params);
}

mysqli_stmt_execute($count_stmt);
$total_records = mysqli_fetch_assoc(mysqli_stmt_get_result($count_stmt))['total'];
$total_pages = ceil($total_records / $records_per_page);
mysqli_stmt_close($count_stmt);

// ==================== GET APPLICATIONS ====================
$query = "SELECT ja.*, 
          j.JOBTITLE, j.JOBTYPE,
          c.COMPANYNAME, c.COMPANYLOGO, c.COMPANYCOUNTRY, 
          a.FNAME, a.OTHERNAMES, a.EMAILADDRESS, a.CONTACTNO, a.APPLICANTPHOTO
          FROM tbljobapplication ja
          INNER JOIN tbljob j ON ja.JOBID = j.JOBID
          INNER JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
          LEFT JOIN tblapplicants a ON ja.APPLICANTID = a.USERID
          $where_sql 
          ORDER BY ja.DATEAPPLIED DESC 
          LIMIT ? OFFSET ?";

$params[] = $records_per_page;
$params[] = $offset;
$types .= 'ii';

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$applications = mysqli_stmt_get_result($stmt);

// ==================== GET STATISTICS ====================
$totalApplicationsQuery = "SELECT COUNT(*) as total FROM tbljobapplication";
$totalApplications = mysqli_fetch_assoc(mysqli_query($con, $totalApplicationsQuery))['total'];

$pendingApplicationsQuery = "SELECT COUNT(*) as total FROM tbljobapplication WHERE APPLICATIONSTATUS = 'Pending'";
$pendingApplications = mysqli_fetch_assoc(mysqli_query($con, $pendingApplicationsQuery))['total'];

$approvedApplicationsQuery = "SELECT COUNT(*) as total FROM tbljobapplication WHERE APPLICATIONSTATUS = 'Approved'";
$approvedApplications = mysqli_fetch_assoc(mysqli_query($con, $approvedApplicationsQuery))['total'];

$rejectedApplicationsQuery = "SELECT COUNT(*) as total FROM tbljobapplication WHERE APPLICATIONSTATUS = 'Rejected'";
$rejectedApplications = mysqli_fetch_assoc(mysqli_query($con, $rejectedApplicationsQuery))['total'];

$shortlistedApplicationsQuery = "SELECT COUNT(*) as total FROM tbljobapplication WHERE APPLICATIONSTATUS = 'Shortlisted'";
$shortlistedApplications = mysqli_fetch_assoc(mysqli_query($con, $shortlistedApplicationsQuery))['total'];

// ==================== GET COMPANIES FOR FILTER ====================
$companiesQuery = "SELECT COMPANYID, COMPANYNAME FROM tblcompany WHERE COMPANYSTATUS = 'Active' ORDER BY COMPANYNAME ASC";
$companiesResult = mysqli_query($con, $companiesQuery);

// ==================== GET JOBS FOR FILTER ====================
$jobsQuery = "SELECT JOBID, JOBTITLE FROM tbljob WHERE JOBSTATUS = 'Active' ORDER BY JOBTITLE ASC LIMIT 100";
$jobsResult = mysqli_query($con, $jobsQuery);
?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>All Applications - Admin Dashboard | MUNext</title>
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
                        <i class="lni lni-files mr-2"></i>All Job Applications
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Applications</a></li>
                            <li class="breadcrumb-item active">All Applications</li>
                        </ol>
                    </nav>
                </div>

                <div class="container-fluid">
                    <!-- Statistics -->
                    <div class="stats-grid">
                        <div class="stat-card total">
                            <span class="stat-number"><?php echo number_format($totalApplications); ?></span>
                            <span class="stat-label">Total Applications</span>
                        </div>
                        <div class="stat-card pending">
                            <span class="stat-number"><?php echo number_format($pendingApplications); ?></span>
                            <span class="stat-label">Pending Review</span>
                        </div>
                        <div class="stat-card approved">
                            <span class="stat-number"><?php echo number_format($approvedApplications); ?></span>
                            <span class="stat-label">Approved</span>
                        </div>
                        <div class="stat-card shortlisted">
                            <span class="stat-number"><?php echo number_format($shortlistedApplications); ?></span>
                            <span class="stat-label">Shortlisted</span>
                        </div>
                        <div class="stat-card rejected">
                            <span class="stat-number"><?php echo number_format($rejectedApplications); ?></span>
                            <span class="stat-label">Rejected</span>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="filter-section">
                        <div class="filter-header">
                            <h6 class="filter-title">
                                <i class="lni lni-funnel mr-2"></i>Filter Applications
                            </h6>
                        </div>

                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Search</label>
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Applicant name, email, job..."
                                        value="<?php echo htmlspecialchars($search); ?>">
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="Pending"
                                            <?php echo $filter_status === 'Pending' ? 'selected' : ''; ?>>Pending
                                        </option>
                                        <option value="Approved"
                                            <?php echo $filter_status === 'Approved' ? 'selected' : ''; ?>>Approved
                                        </option>
                                        <option value="Shortlisted"
                                            <?php echo $filter_status === 'Shortlisted' ? 'selected' : ''; ?>>
                                            Shortlisted</option>
                                        <option value="Rejected"
                                            <?php echo $filter_status === 'Rejected' ? 'selected' : ''; ?>>Rejected
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
                                    <a href="admin-all-applications.php" class="btn-reset">
                                        <i class="lni lni-reload mr-1"></i>Reset Filters
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Applications Table -->
                    <div class="applications-table-card">
                        <div class="table-header">
                            <i class="lni lni-files mr-2"></i>Applications List
                            <span style="float: right; font-weight: normal;">
                                Total: <?php echo number_format($total_records); ?> applications
                            </span>
                        </div>

                        <?php if (mysqli_num_rows($applications) > 0): ?>
                        <div class="table-responsive">
                            <table class="applications-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Applicant</th>
                                        <th>Job / Company</th>
                                        <th>Applied Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($app = mysqli_fetch_assoc($applications)): ?>
                                    <tr>
                                        <td><?php echo $app['ID']; ?></td>
                                        <td>
                                            <div class="applicant-info">
                                                <?php if (!empty($app['APPLICANTPHOTO'])): ?>
                                                <img src="<?php echo $path.htmlspecialchars($app['APPLICANTPHOTO']); ?>"
                                                    alt="Logo" class="applicant-photo"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <?php endif; ?>
                                                <div class="applicant-photo"
                                                    style="<?php echo !empty($app['APPLICANTPHOTO']) ? 'display:none;' : ''; ?>">
                                                    <?php echo strtoupper(substr($app['FNAME'], 0, 1)); ?>
                                                </div>
                                                <div class="applicant-details">
                                                    <span class="applicant-name">
                                                        <?php echo htmlspecialchars($app['FNAME'] . ' ' . $app['OTHERNAMES']); ?>
                                                    </span>
                                                    <span class="applicant-email">
                                                        <?php echo htmlspecialchars($app['EMAILADDRESS']); ?>
                                                    </span>
                                                    <?php if (!empty($app['CONTACTNO'])): ?>
                                                    <span class="applicant-email">
                                                        <i class="lni lni-phone"></i>
                                                        <?php echo htmlspecialchars($app['CONTACTNO']); ?>
                                                    </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="job-info">
                                                <?php if (!empty($app['COMPANYLOGO'])): ?>
                                                <img src="<?php echo $path.htmlspecialchars($app['COMPANYLOGO']); ?>"
                                                    alt="Logo" class="company-logo-small"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <?php endif; ?>
                                                <div class="company-logo-placeholder"
                                                    style="<?php echo !empty($app['COMPANYLOGO']) ? 'display:none;' : ''; ?>">
                                                    <?php echo strtoupper(substr($app['COMPANYNAME'], 0, 1)); ?>
                                                </div>
                                                <div class="job-details">
                                                    <span class="job-title">
                                                        <?php echo htmlspecialchars($app['JOBTITLE']); ?>
                                                    </span>
                                                    <span class="company-name">
                                                        <?php echo htmlspecialchars($app['COMPANYNAME']); ?>
                                                    </span>
                                                    <?php if (!empty($app['COMPANYCOUNTRY'])): ?>
                                                    <span class="company-name">
                                                        <i class="lni lni-map-marker"></i>
                                                        <?php echo htmlspecialchars($app['COMPANYCOUNTRY']); ?>
                                                    </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($app['DATEAPPLIED'])); ?></td>
                                        <td>
                                            <?php
                                            $statusClass = 'status-pending';
                                            $status = $app['APPLICATIONSTATUS'];
                                            if ($status == 'Approved') {
                                                $statusClass = 'status-approved';
                                            } elseif ($status == 'Rejected') {
                                                $statusClass = 'status-rejected';
                                            } elseif ($status == 'Shortlisted') {
                                                $statusClass = 'status-shortlisted';
                                            }
                                            ?>
                                            <span class="status-badge <?php echo $statusClass; ?>">
                                                <?php echo htmlspecialchars($status); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-action btn-view"
                                                    onclick="viewApplication(<?php echo $app['ID']; ?>)">
                                                    <i class="lni lni-eye"></i>
                                                </button>

                                                <?php if ($status == 'Pending' || $status == 'Shortlisted'): ?>
                                                <form method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Approve this application?')">
                                                    <input type="hidden" name="application_id"
                                                        value="<?php echo $app['ID']; ?>">
                                                    <input type="hidden" name="action" value="approve">
                                                    <button type="submit" class="btn-action btn-approve">
                                                        <i class="lni lni-checkmark"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>

                                                <?php if ($status == 'Pending'): ?>
                                                <!-- <form method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Shortlist this application?')">
                                                    <input type="hidden" name="application_id"
                                                        value="<?php echo $app['ID']; ?>">
                                                    <input type="hidden" name="action" value="shortlist">
                                                    <button type="submit" class="btn-action btn-shortlist">
                                                        <i class="lni lni-star"></i>
                                                    </button>
                                                </form> -->
                                                <?php endif; ?>

                                                <?php if ($status == 'Pending' || $status == 'Shortlisted'): ?>
                                                <form method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Reject this application?')">
                                                    <input type="hidden" name="application_id"
                                                        value="<?php echo $app['ID']; ?>">
                                                    <input type="hidden" name="action" value="reject">
                                                    <button type="submit" class="btn-action btn-reject">
                                                        <i class="lni lni-close"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>

                                                <form method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Delete this application permanently?')">
                                                    <input type="hidden" name="application_id"
                                                        value="<?php echo $app['ID']; ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <button type="submit" class="btn-action btn-delete">
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
                            <i class="lni lni-files empty-state-icon"></i>
                            <p>No applications found.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php include 'footer.php' ?>
            </div>
        </div>

        <a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>
    </div>

    <!-- Application Details Modal -->
    <div class="modal fade" id="applicationModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Application Details</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="applicationModalContent">
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
    function viewApplication(applicationId) {
        $('#applicationModal').modal('show');
        $('#applicationModalContent').html(
            '<div class="text-center py-5"><i class="lni lni-spinner-arrow rotating" style="font-size: 3rem;"></i><p>Loading...</p></div>'
        );

        // Load application details via AJAX
        $.ajax({
            url: 'admin-get-application-details.php',
            method: 'GET',
            data: {
                application_id: applicationId
            },
            success: function(response) {
                $('#applicationModalContent').html(response);
            },
            error: function() {
                $('#applicationModalContent').html(
                    '<div class="alert alert-danger">Failed to load application details.</div>');
            }
        });
    }
    </script>

    <?php echo Toast::render(); ?>
</body>

</html>