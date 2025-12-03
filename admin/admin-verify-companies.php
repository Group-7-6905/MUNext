<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';
require '../include/toast.php';

// ==================== HANDLE VERIFICATION ACTIONS ====================
if (isset($_POST['action']) && isset($_POST['company_id'])) {
    $companyId = mysqli_real_escape_string($con, $_POST['company_id']);
    $action = mysqli_real_escape_string($con, $_POST['action']);
    
    switch ($action) {
        case 'approve':
            $query = "UPDATE tblcompany SET COMPANYSTATUS = 'Active' WHERE COMPANYID = ?";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "i", $companyId);
            if (mysqli_stmt_execute($stmt)) {
                // Log activity
                $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                            VALUES (?, 'COMPANY_APPROVED', 'Admin approved a company registration', ?, NOW())";
                $logStmt = mysqli_prepare($con, $logQuery);
                $ip = getClientIP();
                mysqli_stmt_bind_param($logStmt, "is", $session_id, $ip);
                mysqli_stmt_execute($logStmt);
                
                Toast::success("Company approved successfully!");
            } else {
                Toast::error("Failed to approve company.");
            }
            break;
            
        case 'reject':
            $query = "UPDATE tblcompany SET COMPANYSTATUS = 'Rejected' WHERE COMPANYID = ?";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "i", $companyId);
            if (mysqli_stmt_execute($stmt)) {
                // Log activity
                $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                            VALUES (?, 'COMPANY_REJECTED', 'Admin rejected a company registration', ?, NOW())";
                $logStmt = mysqli_prepare($con, $logQuery);
                $ip = getClientIP();
                mysqli_stmt_bind_param($logStmt, "is", $session_id, $ip);
                mysqli_stmt_execute($logStmt);
                
                Toast::warning("Company rejected!");
            } else {
                Toast::error("Failed to reject company.");
            }
            break;
            
        case 'request_info':
            // Update status to request more information
            $query = "UPDATE tblcompany SET COMPANYSTATUS = 'Info Required' WHERE COMPANYID = ?";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "i", $companyId);
            if (mysqli_stmt_execute($stmt)) {
                Toast::info("Company marked for additional information!");
            } else {
                Toast::error("Failed to update company status.");
            }
            break;
    }
}

// ==================== PAGINATION SETUP ====================
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$records_per_page = 20;
$offset = ($page - 1) * $records_per_page;

// ==================== FILTER PARAMETERS ====================
$filter_status = isset($_GET['status']) ? mysqli_real_escape_string($con, $_GET['status']) : 'Pending';
$search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';
$filter_country = isset($_GET['country']) ? mysqli_real_escape_string($con, $_GET['country']) : '';

// ==================== BUILD QUERY ====================
$where_clauses = [];
$params = [];
$types = '';

// Default to Pending status for verification page
if (!empty($filter_status)) {
    $where_clauses[] = "c.COMPANYSTATUS = ?";
    $params[] = $filter_status;
    $types .= 's';
} else {
    $where_clauses[] = "c.COMPANYSTATUS = 'Pending'";
}

if (!empty($search)) {
    $where_clauses[] = "(c.COMPANYNAME LIKE ? OR c.COMPANYEMAIL LIKE ? OR c.COMPANYCITY LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= 'sss';
}

if (!empty($filter_country)) {
    $where_clauses[] = "c.COMPANYCOUNTRY = ?";
    $params[] = $filter_country;
    $types .= 's';
}

$where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

// ==================== GET TOTAL RECORDS ====================
$count_query = "SELECT COUNT(*) as total FROM tblcompany c $where_sql";
$count_stmt = mysqli_prepare($con, $count_query);

if (!empty($params)) {
    mysqli_stmt_bind_param($count_stmt, $types, ...$params);
}

mysqli_stmt_execute($count_stmt);
$total_records = mysqli_fetch_assoc(mysqli_stmt_get_result($count_stmt))['total'];
$total_pages = ceil($total_records / $records_per_page);
mysqli_stmt_close($count_stmt);

// ==================== GET COMPANIES ====================
$query = "SELECT c.*, u.FNAME, u.ONAME, u.EMAIL as USER_EMAIL, u.PHONE,
          (SELECT COUNT(*) FROM tbljob WHERE COMPANYID = c.COMPANYID) as job_count
          FROM tblcompany c
          LEFT JOIN tblusers u ON c.USERID = u.USERID
          $where_sql 
          ORDER BY c.DATEREGISTERED DESC 
          LIMIT ? OFFSET ?";

$params[] = $records_per_page;
$params[] = $offset;
$types .= 'ii';

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$companies = mysqli_stmt_get_result($stmt);

// ==================== GET STATISTICS ====================
$pendingCompaniesQuery = "SELECT COUNT(*) as total FROM tblcompany WHERE COMPANYSTATUS = 'Pending'";
$pendingCompanies = mysqli_fetch_assoc(mysqli_query($con, $pendingCompaniesQuery))['total'];

$approvedTodayQuery = "SELECT COUNT(*) as total FROM tblcompany WHERE COMPANYSTATUS = 'Active' AND DATE(DATEREGISTERED) = CURDATE()";
$approvedToday = mysqli_fetch_assoc(mysqli_query($con, $approvedTodayQuery))['total'];

$rejectedCompaniesQuery = "SELECT COUNT(*) as total FROM tblcompany WHERE COMPANYSTATUS = 'Rejected'";
$rejectedCompanies = mysqli_fetch_assoc(mysqli_query($con, $rejectedCompaniesQuery))['total'];

$infoRequiredQuery = "SELECT COUNT(*) as total FROM tblcompany WHERE COMPANYSTATUS = 'Info Required'";
$infoRequired = mysqli_fetch_assoc(mysqli_query($con, $infoRequiredQuery))['total'];

// ==================== GET COUNTRIES FOR FILTER ====================
$countriesQuery = "SELECT DISTINCT COMPANYCOUNTRY FROM tblcompany WHERE COMPANYCOUNTRY IS NOT NULL AND COMPANYCOUNTRY != '' ORDER BY COMPANYCOUNTRY";
$countriesResult = mysqli_query($con, $countriesQuery);
?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Companies - Admin Dashboard | MUNext</title>
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
                        <i class="lni lni-checkmark-circle mr-2"></i>Verify Companies
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Companies</a></li>
                            <li class="breadcrumb-item active">Verify Companies</li>
                        </ol>
                    </nav>
                </div>

                <div class="container-fluid">
                    <!-- Statistics -->
                    <div class="stats-grid">
                        <div class="stat-card pending">
                            <span class="stat-number"><?php echo number_format($pendingCompanies); ?></span>
                            <span class="stat-label">Pending Verification</span>
                        </div>
                        <div class="stat-card approved">
                            <span class="stat-number"><?php echo number_format($approvedToday); ?></span>
                            <span class="stat-label">Approved Today</span>
                        </div>
                        <div class="stat-card rejected">
                            <span class="stat-number"><?php echo number_format($rejectedCompanies); ?></span>
                            <span class="stat-label">Rejected</span>
                        </div>
                        <div class="stat-card info">
                            <span class="stat-number"><?php echo number_format($infoRequired); ?></span>
                            <span class="stat-label">Info Required</span>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="filter-section">
                        <div class="filter-header">
                            <h6 class="filter-title">
                                <i class="lni lni-funnel mr-2"></i>Filter Companies
                            </h6>
                        </div>

                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Search</label>
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Company name, email, city..."
                                        value="<?php echo htmlspecialchars($search); ?>">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="Pending"
                                            <?php echo $filter_status === 'Pending' ? 'selected' : ''; ?>>Pending
                                            Verification</option>
                                        <option value="Info Required"
                                            <?php echo $filter_status === 'Info Required' ? 'selected' : ''; ?>>Info
                                            Required</option>
                                        <option value="Active"
                                            <?php echo $filter_status === 'Active' ? 'selected' : ''; ?>>Approved
                                        </option>
                                        <option value="Rejected"
                                            <?php echo $filter_status === 'Rejected' ? 'selected' : ''; ?>>Rejected
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Country</label>
                                    <select name="country" class="form-control">
                                        <option value="">All Countries</option>
                                        <?php 
                                        mysqli_data_seek($countriesResult, 0);
                                        while ($country = mysqli_fetch_assoc($countriesResult)): 
                                        ?>
                                        <option value="<?php echo htmlspecialchars($country['COMPANYCOUNTRY']); ?>"
                                            <?php echo $filter_country === $country['COMPANYCOUNTRY'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($country['COMPANYCOUNTRY']); ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="col-md-2 mb-3">
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
                                    <a href="admin-verify-companies.php" class="btn-reset">
                                        <i class="lni lni-reload mr-1"></i>Reset Filters
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Companies Table -->
                    <div class="companies-table-card">
                        <div class="table-header">
                            <i class="fa fa-building mr-2"></i>Companies for Verification
                            <span style="float: right; font-weight: normal;">
                                Total: <?php echo number_format($total_records); ?> companies
                            </span>
                        </div>

                        <?php if (mysqli_num_rows($companies) > 0): ?>
                        <div class="table-responsive">
                            <table class="companies-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Company Details</th>
                                        <th>Contact Person</th>
                                        <th>Location</th>
                                        <th>Registered</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($company = mysqli_fetch_assoc($companies)): ?>
                                    <tr>
                                        <td><?php echo $company['COMPANYID']; ?></td>
                                        <td>
                                            <div class="company-info">
                                                <?php if (!empty($company['COMPANYLOGO'])): ?>
                                                <img src="<?php echo $path.htmlspecialchars($company['COMPANYLOGO']); ?>"
                                                    alt="Logo" class="company-logo-small"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <?php endif; ?>
                                                <div class="company-logo-placeholder"
                                                    style="<?php echo !empty($company['COMPANYLOGO']) ? 'display:none;' : ''; ?>">
                                                    <?php echo strtoupper(substr($company['COMPANYNAME'], 0, 1)); ?>
                                                </div>

                                                <div class="company-details">
                                                    <h6><?php echo htmlspecialchars($company['COMPANYNAME']); ?></h6>
                                                    <small><?php echo htmlspecialchars($company['COMPANYEMAIL'] ?? 'No email'); ?></small>
                                                    <?php if (!empty($company['COMPANYCONTACTNO'])): ?>
                                                    <br><small><i class="lni lni-phone"></i>
                                                        <?php echo htmlspecialchars($company['COMPANYCONTACTNO']); ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($company['FNAME'] . ' ' . $company['ONAME']); ?></strong><br>
                                            <small class="text-muted">
                                                <i class="lni lni-envelope"></i>
                                                <?php echo htmlspecialchars($company['USER_EMAIL']); ?>
                                            </small>
                                            <?php if (!empty($company['PHONE'])): ?>
                                            <br><small class="text-muted">
                                                <i class="lni lni-phone"></i>
                                                <?php echo htmlspecialchars($company['PHONE']); ?>
                                            </small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $location = [];
                                            if (!empty($company['COMPANYCITY'])) {
                                                $location[] = $company['COMPANYCITY'];
                                            }
                                            if (!empty($company['COMPANYCOUNTRY'])) {
                                                $location[] = $company['COMPANYCOUNTRY'];
                                            }
                                            echo !empty($location) ? htmlspecialchars(implode(', ', $location)) : '<span class="text-muted">N/A</span>';
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $registered = strtotime($company['DATEREGISTERED']);
                                            $now = time();
                                            $diff = $now - $registered;
                                            $days = floor($diff / (60 * 60 * 24));
                                            ?>
                                            <?php echo date('M d, Y', $registered); ?>
                                            <br>
                                            <span class="time-badge">
                                                <?php 
                                                if ($days == 0) {
                                                    echo 'Today';
                                                } elseif ($days == 1) {
                                                    echo 'Yesterday';
                                                } else {
                                                    echo $days . ' days ago';
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = 'status-pending';
                                            $status = $company['COMPANYSTATUS'];
                                            if ($status == 'Active') {
                                                $statusClass = 'status-active';
                                            } elseif ($status == 'Rejected') {
                                                $statusClass = 'status-rejected';
                                            } elseif ($status == 'Info Required') {
                                                $statusClass = 'status-info-required';
                                            }
                                            ?>
                                            <span class="status-badge <?php echo $statusClass; ?>">
                                                <?php echo htmlspecialchars($status); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-action btn-view"
                                                    onclick="viewCompany(<?php echo $company['COMPANYID']; ?>)"
                                                    title="View">
                                                    <i class="lni lni-eye"></i>
                                                </button>

                                                <?php if ($status == 'Pending' || $status == 'Info Required'): ?>
                                                <form method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Approve this company?')">
                                                    <input type="hidden" name="company_id"
                                                        value="<?php echo $company['COMPANYID']; ?>">
                                                    <input type="hidden" name="action" value="approve">
                                                    <button type="submit" class="btn-action btn-approve"
                                                        title="Approve">
                                                        <i class="lni lni-checkmark"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>

                                                <?php if ($status == 'Pending'): ?>
                                                <form method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Mark as requiring more information?')">
                                                    <input type="hidden" name="company_id"
                                                        value="<?php echo $company['COMPANYID']; ?>">
                                                    <input type="hidden" name="action" value="request_info">
                                                    <button type="submit" class="btn-action btn-info"
                                                        title="Request More info">
                                                        <i class="lni lni-question-circle"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>

                                                <?php if ($status == 'Pending' || $status == 'Info Required'): ?>
                                                <form method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Reject this company registration?')">
                                                    <input type="hidden" name="company_id"
                                                        value="<?php echo $company['COMPANYID']; ?>">
                                                    <input type="hidden" name="action" value="reject">
                                                    <button type="submit" class="btn-action btn-reject" title="Reject">
                                                        <i class="lni lni-close"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>
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
                            <i class="fa fa-building empty-state-icon"></i>
                            <p>No companies found for verification.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php include 'footer.php' ?>
            </div>
        </div>

        <a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>
    </div>

    <!-- Company Details Modal -->
    <div class="modal fade" id="companyModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Company Details</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="companyModalContent">
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
    function viewCompany(companyid) {
        $('#companyModal').modal('show');
        $('#companyModalContent').html(
            '<div class="text-center py-5"><i class="lni lni-spinner-arrow rotating" style="font-size: 3rem;"></i><p>Loading...</p></div>'
        );

        // Load company details via AJAX
        $.ajax({
            url: 'admin-get-company-details.php',
            method: 'GET',
            data: {
                companyid: companyid
            },
            success: function(response) {
                $('#companyModalContent').html(response);
            },
            error: function() {
                $('#companyModalContent').html(
                    '<div class="alert alert-danger">Failed to load company details.</div>');
            }
        });
    }
    </script>

    <?php echo Toast::render(); ?>
</body>

</html>