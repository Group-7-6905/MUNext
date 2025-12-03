<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';
require '../include/toast.php';


// ==================== HANDLE COMPANY ACTIONS ====================
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $companyid = isset($_POST['companyid']) ? (int)$_POST['companyid'] : 0;
    
    if ($companyid > 0) {
        switch ($action) {
            case 'verify':
                $updateQuery = "UPDATE tblcompany SET COMPANYSTATUS = 'Active' WHERE COMPANYID = ?";
                $stmt = mysqli_prepare($con, $updateQuery);
                mysqli_stmt_bind_param($stmt, "i", $companyid);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Log activity
                    $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                                VALUES (?, 'COMPANY_VERIFIED', 'Admin verified a company account', ?, NOW())";
                    $logStmt = mysqli_prepare($con, $logQuery);
                    $ip = getClientIP();
                    mysqli_stmt_bind_param($logStmt, "is", $session_id, $ip);
                    mysqli_stmt_execute($logStmt);
                    
                    Toast::success('Company verified successfully!');
                } else {
                    Toast::error('Failed to verify company.');
                }
                mysqli_stmt_close($stmt);
                break;
                
            case 'suspend':
                $updateQuery = "UPDATE tblcompany SET COMPANYSTATUS = 'Suspended' WHERE COMPANYID = ?";
                $stmt = mysqli_prepare($con, $updateQuery);
                mysqli_stmt_bind_param($stmt, "i", $companyid);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Log activity
                    $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                                VALUES (?, 'COMPANY_SUSPENDED', 'Admin suspended a company account', ?, NOW())";
                    $logStmt = mysqli_prepare($con, $logQuery);
                    $ip = getClientIP();
                    mysqli_stmt_bind_param($logStmt, "is", $session_id, $ip);
                    mysqli_stmt_execute($logStmt);
                    
                    Toast::warning('Company suspended successfully!');
                } else {
                    Toast::error('Failed to suspend company.');
                }
                mysqli_stmt_close($stmt);
                break;
                
            case 'delete':
                // Soft delete - mark as deleted
                $deleteQuery = "UPDATE tblcompany SET COMPANYSTATUS = 'Deleted' WHERE COMPANYID = ?";
                $stmt = mysqli_prepare($con, $deleteQuery);
                mysqli_stmt_bind_param($stmt, "i", $companyid);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Log activity
                    $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                                VALUES (?, 'COMPANY_DELETED', 'Admin deleted a company account', ?, NOW())";
                    $logStmt = mysqli_prepare($con, $logQuery);
                    $ip = getClientIP();
                    mysqli_stmt_bind_param($logStmt, "is", $session_id, $ip);
                    mysqli_stmt_execute($logStmt);
                    
                    Toast::success('Company deleted successfully!');
                } else {
                    Toast::error('Failed to delete company.');
                }
                mysqli_stmt_close($stmt);
                break;
        }
        
        header('location: admin-employers.php?' . http_build_query($_GET));
        exit();
    }
}

// ==================== PAGINATION SETUP ====================
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$records_per_page = 20;
$offset = ($page - 1) * $records_per_page;

// ==================== FILTER PARAMETERS ====================
$filter_status = isset($_GET['status']) ? mysqli_real_escape_string($con, $_GET['status']) : '';
$filter_date_from = isset($_GET['date_from']) ? mysqli_real_escape_string($con, $_GET['date_from']) : '';
$filter_date_to = isset($_GET['date_to']) ? mysqli_real_escape_string($con, $_GET['date_to']) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';

// ==================== BUILD QUERY ====================
$where_clauses = [];
$params = [];
$types = '';

if (!empty($filter_status)) {
    $where_clauses[] = "c.COMPANYSTATUS = ?";
    $params[] = $filter_status;
    $types .= 's';
}

if (!empty($filter_date_from)) {
    $where_clauses[] = "DATE(c.DATEREGISTERED) >= ?";
    $params[] = $filter_date_from;
    $types .= 's';
}

if (!empty($filter_date_to)) {
    $where_clauses[] = "DATE(c.DATEREGISTERED) <= ?";
    $params[] = $filter_date_to;
    $types .= 's';
}

if (!empty($search)) {
    $where_clauses[] = "(c.COMPANYNAME LIKE ? OR c.COMPANYEMAIL LIKE ? OR c.COMPANYWEBSITE LIKE ? OR CONCAT(u.FNAME, ' ', u.ONAME) LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= 'ssss';
}

$where_sql = '';
if (!empty($where_clauses)) {
    $where_sql = 'WHERE ' . implode(' AND ', $where_clauses);
}

// ==================== GET TOTAL RECORDS ====================
$count_query = "SELECT COUNT(*) as total 
                FROM tblcompany c 
                LEFT JOIN tblusers u ON c.USERID = u.USERID 
                $where_sql";
$count_stmt = mysqli_prepare($con, $count_query);

if (!empty($params)) {
    mysqli_stmt_bind_param($count_stmt, $types, ...$params);
}

mysqli_stmt_execute($count_stmt);
$total_records = mysqli_fetch_assoc(mysqli_stmt_get_result($count_stmt))['total'];
$total_pages = ceil($total_records / $records_per_page);
mysqli_stmt_close($count_stmt);

// ==================== GET COMPANIES ====================
$query = "SELECT c.*, 
          u.FNAME, u.ONAME, u.EMAIL as USER_EMAIL, u.USERNAME,
          (SELECT COUNT(*) FROM tbljob WHERE COMPANYID = c.COMPANYID AND JOBSTATUS = 'Active') as active_jobs,
          (SELECT COUNT(*) FROM tbljob WHERE COMPANYID = c.COMPANYID) as total_jobs
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
$totalCompaniesQuery = "SELECT COUNT(*) as total FROM tblcompany";
$totalCompanies = mysqli_fetch_assoc(mysqli_query($con, $totalCompaniesQuery))['total'];

$activeCompaniesQuery = "SELECT COUNT(*) as total FROM tblcompany WHERE COMPANYSTATUS = 'Active'";
$activeCompanies = mysqli_fetch_assoc(mysqli_query($con, $activeCompaniesQuery))['total'];

$pendingCompaniesQuery = "SELECT COUNT(*) as total FROM tblcompany WHERE COMPANYSTATUS = 'Pending'";
$pendingCompanies = mysqli_fetch_assoc(mysqli_query($con, $pendingCompaniesQuery))['total'];

$suspendedCompaniesQuery = "SELECT COUNT(*) as total FROM tblcompany WHERE COMPANYSTATUS = 'Suspended'";
$suspendedCompanies = mysqli_fetch_assoc(mysqli_query($con, $suspendedCompaniesQuery))['total'];
?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employers Management - Admin Dashboard | MUNext</title>
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
                        <i class="fa fa-building mr-2"></i>Employers Management
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Company Management</a></li>
                            <li class="breadcrumb-item active">Employers</li>
                        </ol>
                    </nav>
                </div>

                <div class="container-fluid">
                    <!-- Statistics -->
                    <div class="stats-grid">
                        <div class="stat-card total">
                            <span class="stat-number"><?php echo number_format($totalCompanies); ?></span>
                            <span class="stat-label">Total Companies</span>
                        </div>
                        <div class="stat-card active">
                            <span class="stat-number"><?php echo number_format($activeCompanies); ?></span>
                            <span class="stat-label">Verified Companies</span>
                        </div>
                        <div class="stat-card pending">
                            <span class="stat-number"><?php echo number_format($pendingCompanies); ?></span>
                            <span class="stat-label">Pending Verification</span>
                        </div>
                        <div class="stat-card suspended">
                            <span class="stat-number"><?php echo number_format($suspendedCompanies); ?></span>
                            <span class="stat-label">Suspended</span>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="filter-section">
                        <div class="filter-header">
                            <h6 class="filter-title">
                                <i class="lni lni-funnel mr-2"></i>Filter Employers
                            </h6>
                        </div>

                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Search</label>
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Company name, email, contact person..."
                                        value="<?php echo htmlspecialchars($search); ?>">
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="Active"
                                            <?php echo $filter_status === 'Active' ? 'selected' : ''; ?>>Verified
                                        </option>
                                        <option value="Pending"
                                            <?php echo $filter_status === 'Pending' ? 'selected' : ''; ?>>Pending
                                        </option>
                                        <option value="Suspended"
                                            <?php echo $filter_status === 'Suspended' ? 'selected' : ''; ?>>Suspended
                                        </option>
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

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn-filter">
                                            <i class="lni lni-search mr-1"></i>Apply Filters
                                        </button>
                                        <a href="admin-employers.php" class="btn-reset">Reset</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Companies Table -->
                    <div class="companies-table-card">
                        <div class="table-header">
                            <i class="fa fa-building mr-2"></i>Employers List
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
                                        <th>Company</th>
                                        <th>Contact Person</th>
                                        <th>Phone</th>
                                        <th>Jobs</th>
                                        <th>Status</th>
                                        <th>Registered</th>
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
                                                    <span class="company-name">
                                                        <?php echo htmlspecialchars($company['COMPANYNAME']); ?>
                                                    </span>
                                                    <span class="company-email">
                                                        <?php echo htmlspecialchars($company['COMPANYEMAIL']); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php 
                                            if (!empty($company['FNAME']) && !empty($company['ONAME'])) {
                                                echo htmlspecialchars($company['FNAME'] . ' ' . $company['ONAME']);
                                            } else {
                                                echo 'N/A';
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($company['COMPANYPHONE'] ?? 'N/A'); ?></td>
                                        <td>
                                            <span class="job-count-badge">
                                                <?php echo $company['active_jobs']; ?> /
                                                <?php echo $company['total_jobs']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = 'status-active';
                                            $status = $company['COMPANYSTATUS'];
                                            if ($status == 'Pending') {
                                                $statusClass = 'status-pending';
                                            } elseif ($status == 'Suspended') {
                                                $statusClass = 'status-suspended';
                                            }
                                            ?>
                                            <span class="status-badge <?php echo $statusClass; ?>">
                                                <?php echo htmlspecialchars($status); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($company['DATEREGISTERED'])); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-action btn-view"
                                                    onclick="viewCompany(<?php echo $company['COMPANYID']; ?>)"
                                                    title="View">
                                                    <i class="lni lni-eye"></i>
                                                </button>

                                                <?php if ($company['COMPANYSTATUS'] != 'Active'): ?>
                                                <form method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Verify this company?')">
                                                    <input type="hidden" name="companyid"
                                                        value="<?php echo $company['COMPANYID']; ?>">
                                                    <input type="hidden" name="action" value="verify">
                                                    <button type="submit" class="btn-action btn-verify" title="Approve">
                                                        <i class="lni lni-checkmark"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>

                                                <?php if ($company['COMPANYSTATUS'] == 'Active'): ?>
                                                <form method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Suspend this company?')">
                                                    <input type="hidden" name="companyid"
                                                        value="<?php echo $company['COMPANYID']; ?>">
                                                    <input type="hidden" name="action" value="suspend">
                                                    <button type="submit" class="btn-action btn-suspend"
                                                        title="Suspend">
                                                        <i class="lni lni-ban"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>

                                                <form method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Delete this company permanently?')">
                                                    <input type="hidden" name="companyid"
                                                        value="<?php echo $company['COMPANYID']; ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <button type="submit" class="btn-action btn-delete" title="Delete">
                                                        <i class="fas fa-trash"></i>
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
                            <i class="fa fa-building empty-state-icon"></i>
                            <p>No companies found.</p>
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
                <div class="modal-header" style="background: var(--primary-color); color: white;">
                    <h5 class="modal-title text-white">Company Details</h5>
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