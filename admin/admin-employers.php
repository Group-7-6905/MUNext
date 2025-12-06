<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';
require '../include/toast.php';
require_once '../include/email-functions.php';

// ==================== HANDLE EMPLOYER ACTIONS ====================
if (isset($_POST['action'])) {
    // $action = $_POST['action'];
    $action = mysqli_real_escape_string($con, $_POST['action']);
    $userid = isset($_POST['userid']) ? (int)$_POST['userid'] : 0;
    
    if ($userid > 0) {
        switch ($action) {
            case 'approve':
                // Approve employer account
                $updateQuery = "UPDATE tblusers SET USERSTATUS = 'Active' WHERE USERID = ? AND ROLE = 'Employer'";
                $stmt = mysqli_prepare($con, $updateQuery);
                mysqli_stmt_bind_param($stmt, "i", $userid);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Get employer details for email
                    $detailsQuery = "SELECT u.EMAIL, u.FNAME, u.ONAME, c.COMPANYNAME 
                                    FROM tblusers u
                                    LEFT JOIN tblcompany c ON u.USERID = c.USERID
                                    WHERE u.USERID = ?";
                    $detailsStmt = mysqli_prepare($con, $detailsQuery);
                    mysqli_stmt_bind_param($detailsStmt, "i", $userid);
                    mysqli_stmt_execute($detailsStmt);
                    $detailsResult = mysqli_stmt_get_result($detailsStmt);
                    
                    if ($details = mysqli_fetch_assoc($detailsResult)) {
                        $employerName = $details['FNAME'] . ' ' . $details['ONAME'];
                        $companyName = $details['COMPANYNAME'] ?? 'Your Company';
                        
                        // Send approval email
                        sendCompanyApprovedEmail($con, $details['EMAIL'], $companyName, $employerName);
                    }
                    
                    // Log activity
                    $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                                VALUES (?, 'EMPLOYER_APPROVED', CONCAT('Admin approved employer account: ', ?), ?, NOW())";
                    $logStmt = mysqli_prepare($con, $logQuery);
                    $ip = getClientIP();
                    mysqli_stmt_bind_param($logStmt, "iis", $session_id, $userid, $ip);
                    mysqli_stmt_execute($logStmt);
                    
                    Toast::success('Employer approved successfully! Email notification sent.');
                } else {
                    Toast::error('Failed to approve employer.');
                }
                mysqli_stmt_close($stmt);
                break;
                
            case 'suspend':
                // Suspend employer account
                $updateQuery = "UPDATE tblusers SET USERSTATUS = 'Suspended' WHERE USERID = ? AND ROLE = 'Employer'";
                $stmt = mysqli_prepare($con, $updateQuery);
                mysqli_stmt_bind_param($stmt, "i", $userid);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Log activity
                    $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                                VALUES (?, 'EMPLOYER_SUSPENDED', CONCAT('Admin suspended employer account: ', ?), ?, NOW())";
                    $logStmt = mysqli_prepare($con, $logQuery);
                    $ip = getClientIP();
                    mysqli_stmt_bind_param($logStmt, "iis", $session_id, $userid, $ip);
                    mysqli_stmt_execute($logStmt);

                    // Get employer details for email
                    $detailsQuery = "SELECT u.EMAIL, u.FNAME, u.ONAME, c.COMPANYNAME 
                                    FROM tblusers u
                                    LEFT JOIN tblcompany c ON u.USERID = c.USERID
                                    WHERE u.USERID = ?";
                    $detailsStmt = mysqli_prepare($con, $detailsQuery);
                    mysqli_stmt_bind_param($detailsStmt, "i", $userid);
                    mysqli_stmt_execute($detailsStmt);
                    $detailsResult = mysqli_stmt_get_result($detailsStmt);
                    
                    if ($details = mysqli_fetch_assoc($detailsResult)) {
                        $employerName = $details['FNAME'] . ' ' . $details['ONAME'];
                        $companyName = $details['COMPANYNAME'] ?? 'Your Company';
                        
                        // Send approval email
                        sendCompanyRejectedEmail($con, $details['EMAIL'], $companyName, $employerName);
                    }
                    
                    Toast::warning('Employer suspended successfully!');
                } else {
                    Toast::error('Failed to suspend employer.');
                }
                mysqli_stmt_close($stmt);
                break;
                
            case 'activate':
                // Activate employer account (from suspended)
                $updateQuery = "UPDATE tblusers SET USERSTATUS = 'Active' WHERE USERID = ? AND ROLE = 'Employer'";
                $stmt = mysqli_prepare($con, $updateQuery);
                mysqli_stmt_bind_param($stmt, "i", $userid);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Log activity
                    $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                                VALUES (?, 'EMPLOYER_ACTIVATED', CONCAT('Admin activated employer account: ', ?), ?, NOW())";
                    $logStmt = mysqli_prepare($con, $logQuery);
                    $ip = getClientIP();
                    mysqli_stmt_bind_param($logStmt, "iis", $session_id, $userid, $ip);
                    mysqli_stmt_execute($logStmt);
                    
                    Toast::success('Employer activated successfully!');
                } else {
                    Toast::error('Failed to activate employer.');
                }
                mysqli_stmt_close($stmt);
                break;
                
            case 'delete':
                // Soft delete - mark as Inactive
                $deleteQuery = "UPDATE tblusers SET USERSTATUS = 'Inactive' WHERE USERID = ? AND ROLE = 'Employer'";
                $stmt = mysqli_prepare($con, $deleteQuery);
                mysqli_stmt_bind_param($stmt, "i", $userid);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Log activity
                    $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                                VALUES (?, 'EMPLOYER_DELETED', CONCAT('Admin deleted employer account: ', ?), ?, NOW())";
                    $logStmt = mysqli_prepare($con, $logQuery);
                    $ip = getClientIP();
                    mysqli_stmt_bind_param($logStmt, "iis", $session_id, $userid, $ip);
                    mysqli_stmt_execute($logStmt);
                    
                    Toast::success('Employer deleted successfully!');
                } else {
                    Toast::error('Failed to delete employer.');
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
$where_clauses = ["u.ROLE = 'Employer'"]; // Always filter by Employer role
$params = [];
$types = '';

if (!empty($filter_status)) {
    $where_clauses[] = "u.USERSTATUS = ?";
    $params[] = $filter_status;
    $types .= 's';
}

if (!empty($filter_date_from)) {
    $where_clauses[] = "DATE(u.DATECREATED) >= ?";
    $params[] = $filter_date_from;
    $types .= 's';
}

if (!empty($filter_date_to)) {
    $where_clauses[] = "DATE(u.DATECREATED) <= ?";
    $params[] = $filter_date_to;
    $types .= 's';
}

if (!empty($search)) {
    $where_clauses[] = "(u.FNAME LIKE ? OR u.ONAME LIKE ? OR u.EMAIL LIKE ? OR u.USERNAME LIKE ? OR c.COMPANYNAME LIKE ? OR c.COMPANYEMAIL LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= 'ssssss';
}

$where_sql = 'WHERE ' . implode(' AND ', $where_clauses);

// ==================== GET TOTAL RECORDS ====================
$count_query = "SELECT COUNT(*) as total 
                FROM tblusers u 
                LEFT JOIN tblcompany c ON u.USERID = c.USERID 
                $where_sql";
$count_stmt = mysqli_prepare($con, $count_query);

if (!empty($params)) {
    mysqli_stmt_bind_param($count_stmt, $types, ...$params);
}

mysqli_stmt_execute($count_stmt);
$total_records = mysqli_fetch_assoc(mysqli_stmt_get_result($count_stmt))['total'];
$total_pages = ceil($total_records / $records_per_page);
mysqli_stmt_close($count_stmt);

// ==================== GET EMPLOYERS ====================
$query = "SELECT u.USERID, u.FNAME, u.ONAME, u.EMAIL, u.USERNAME, u.USERSTATUS, u.DATECREATED,
          c.COMPANYID, c.COMPANYNAME, c.COMPANYEMAIL, c.COMPANYCONTACTNO, c.COMPANYLOGO, 
          c.COMPANYWEBSITE, c.COMPANYSTATUS, c.DATEREGISTERED,
          (SELECT COUNT(*) FROM tbljob WHERE EMPLOYERID = u.USERID AND JOBSTATUS = 'Active') as active_jobs,
          (SELECT COUNT(*) FROM tbljob WHERE EMPLOYERID = u.USERID) as total_jobs
          FROM tblusers u 
          LEFT JOIN tblcompany c ON u.USERID = c.USERID
          $where_sql 
          ORDER BY u.DATECREATED DESC 
          LIMIT ? OFFSET ?";

$params[] = $records_per_page;
$params[] = $offset;
$types .= 'ii';

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$employers = mysqli_stmt_get_result($stmt);

// ==================== GET STATISTICS ====================
$totalEmployersQuery = "SELECT COUNT(*) as total FROM tblusers WHERE ROLE = 'Employer'";
$totalEmployers = mysqli_fetch_assoc(mysqli_query($con, $totalEmployersQuery))['total'];

$activeEmployersQuery = "SELECT COUNT(*) as total FROM tblusers WHERE ROLE = 'Employer' AND USERSTATUS = 'Active'";
$activeEmployers = mysqli_fetch_assoc(mysqli_query($con, $activeEmployersQuery))['total'];

$pendingEmployersQuery = "SELECT COUNT(*) as total FROM tblusers WHERE ROLE = 'Employer' AND USERSTATUS = 'Pending'";
$pendingEmployers = mysqli_fetch_assoc(mysqli_query($con, $pendingEmployersQuery))['total'];

$suspendedEmployersQuery = "SELECT COUNT(*) as total FROM tblusers WHERE ROLE = 'Employer' AND USERSTATUS = 'Suspended'";
$suspendedEmployers = mysqli_fetch_assoc(mysqli_query($con, $suspendedEmployersQuery))['total'];

// Get count of employers with company profiles
$withCompanyQuery = "SELECT COUNT(DISTINCT u.USERID) as total 
                     FROM tblusers u 
                     INNER JOIN tblcompany c ON u.USERID = c.USERID 
                     WHERE u.ROLE = 'Employer'";
$withCompanyCount = mysqli_fetch_assoc(mysqli_query($con, $withCompanyQuery))['total'];
?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employers Management - Admin Dashboard | MUNext</title>
    <link href="assets/css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/custom-dashboard.css">

    <style>
    .profile-status-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 3px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-left: 5px;
    }

    .profile-complete {
        background: #d4edda;
        color: #155724;
    }

    .profile-incomplete {
        background: #fff3cd;
        color: #856404;
    }

    .company-info-extended {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    </style>
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
                        <i class="fa fa-users mr-2"></i>Employers Management
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">User Management</a></li>
                            <li class="breadcrumb-item active">Employers</li>
                        </ol>
                    </nav>
                </div>

                <div class="container-fluid">
                    <!-- Statistics -->
                    <div class="stats-grid">
                        <div class="stat-card total">
                            <span class="stat-number"><?php echo number_format($totalEmployers); ?></span>
                            <span class="stat-label">Total Employers</span>
                        </div>
                        <div class="stat-card active">
                            <span class="stat-number"><?php echo number_format($activeEmployers); ?></span>
                            <span class="stat-label">Active Accounts</span>
                        </div>
                        <div class="stat-card pending">
                            <span class="stat-number"><?php echo number_format($pendingEmployers); ?></span>
                            <span class="stat-label">Pending Approval</span>
                        </div>
                        <div class="stat-card suspended">
                            <span class="stat-number"><?php echo number_format($suspendedEmployers); ?></span>
                            <span class="stat-label">Suspended</span>
                        </div>
                        <div class="stat-card info" style="border-left-color: #17a2b8;">
                            <span class="stat-number"><?php echo number_format($withCompanyCount); ?></span>
                            <span class="stat-label">With Company Profile</span>
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
                                        placeholder="Name, email, company..."
                                        value="<?php echo htmlspecialchars($search); ?>">
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="Active"
                                            <?php echo $filter_status === 'Active' ? 'selected' : ''; ?>>Active
                                        </option>
                                        <option value="Pending"
                                            <?php echo $filter_status === 'Pending' ? 'selected' : ''; ?>>Pending
                                        </option>
                                        <option value="Suspended"
                                            <?php echo $filter_status === 'Suspended' ? 'selected' : ''; ?>>Suspended
                                        </option>
                                        <option value="Inactive"
                                            <?php echo $filter_status === 'Inactive' ? 'selected' : ''; ?>>Inactive
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

                    <!-- Employers Table -->
                    <div class="companies-table-card">
                        <div class="table-header">
                            <i class="fa fa-users mr-2"></i>Employers List
                            <span style="float: right; font-weight: normal;">
                                Total: <?php echo number_format($total_records); ?> employers
                            </span>
                        </div>

                        <?php if (mysqli_num_rows($employers) > 0): ?>
                        <div class="table-responsive">
                            <table class="companies-table">
                                <thead>
                                    <tr>
                                        <th>User ID</th>
                                        <th>Employer Name</th>
                                        <th>Company</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Jobs</th>
                                        <th>Status</th>
                                        <th>Registered</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($employer = mysqli_fetch_assoc($employers)): 
                                        $hasCompanyProfile = !empty($employer['COMPANYID']);
                                    ?>
                                    <tr>
                                        <td><?php echo $employer['USERID']; ?></td>
                                        <td>
                                            <div class="company-details">
                                                <span class="company-name">
                                                    <?php echo htmlspecialchars($employer['FNAME'] . ' ' . $employer['ONAME']); ?>
                                                </span>
                                                <span class="company-email">
                                                    @<?php echo htmlspecialchars($employer['USERNAME']); ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($hasCompanyProfile): ?>
                                            <div class="company-info-extended">
                                                <?php if (!empty($employer['COMPANYLOGO'])): ?>
                                                <img src="<?php echo $path.htmlspecialchars($employer['COMPANYLOGO']); ?>"
                                                    alt="Logo" class="company-logo-small"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <?php endif; ?>
                                                <div class="company-logo-placeholder"
                                                    style="<?php echo !empty($employer['COMPANYLOGO']) ? 'display:none;' : ''; ?>">
                                                    <?php echo strtoupper(substr($employer['COMPANYNAME'], 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <span class="company-name">
                                                        <?php echo htmlspecialchars($employer['COMPANYNAME']); ?>
                                                    </span>
                                                    <span class="profile-status-badge profile-complete">
                                                        <i class="lni lni-checkmark"></i> Complete
                                                    </span>
                                                </div>
                                            </div>
                                            <?php else: ?>
                                            <span class="profile-status-badge profile-incomplete">
                                                <i class="lni lni-close"></i> No Company Profile
                                            </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($employer['EMAIL']); ?>
                                            <?php if ($hasCompanyProfile && !empty($employer['COMPANYEMAIL'])): ?>
                                            <br><small class="text-muted">
                                                Co: <?php echo htmlspecialchars($employer['COMPANYEMAIL']); ?>
                                            </small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            if ($hasCompanyProfile && !empty($employer['COMPANYPHONE'])) {
                                                echo htmlspecialchars($employer['COMPANYPHONE']);
                                            } else {
                                                echo '<span class="text-muted">N/A</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($employer['total_jobs'] > 0): ?>
                                            <span class="job-count-badge">
                                                <?php echo $employer['active_jobs']; ?> /
                                                <?php echo $employer['total_jobs']; ?>
                                            </span>
                                            <?php else: ?>
                                            <span class="text-muted">None</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = 'status-active';
                                            $status = $employer['USERSTATUS'];
                                            if ($status == 'Pending') {
                                                $statusClass = 'status-pending';
                                            } elseif ($status == 'Suspended') {
                                                $statusClass = 'status-suspended';
                                            } elseif ($status == 'Inactive') {
                                                $statusClass = 'status-inactive';
                                            }
                                            ?>
                                            <span class="status-badge <?php echo $statusClass; ?>">
                                                <?php echo htmlspecialchars($status); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($employer['DATECREATED'])); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-action btn-view"
                                                    onclick="viewEmployer(<?php echo $employer['USERID']; ?>)"
                                                    title="View Details">
                                                    <i class="lni lni-eye"></i>
                                                </button>

                                                <?php if ($employer['USERSTATUS'] == 'Pending'): ?>
                                                <form method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Approve this employer account?')">
                                                    <input type="hidden" name="userid"
                                                        value="<?php echo $employer['USERID']; ?>">
                                                    <input type="hidden" name="action" value="approve">
                                                    <button type="submit" class="btn-action btn-verify" title="Approve">
                                                        <i class="lni lni-checkmark"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>

                                                <?php if ($employer['USERSTATUS'] == 'Active'): ?>
                                                <form method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Suspend this employer account?')">
                                                    <input type="hidden" name="userid"
                                                        value="<?php echo $employer['USERID']; ?>">
                                                    <input type="hidden" name="action" value="suspend">
                                                    <button type="submit" class="btn-action btn-suspend"
                                                        title="Suspend">
                                                        <i class="lni lni-ban"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>

                                                <?php if ($employer['USERSTATUS'] == 'Suspended'): ?>
                                                <form method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Activate this employer account?')">
                                                    <input type="hidden" name="userid"
                                                        value="<?php echo $employer['USERID']; ?>">
                                                    <input type="hidden" name="action" value="activate">
                                                    <button type="submit" class="btn-action btn-verify"
                                                        title="Activate">
                                                        <i class="lni lni-checkmark-circle"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>

                                                <form method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Delete this employer account permanently? This will also affect their company profile and jobs.')">
                                                    <input type="hidden" name="userid"
                                                        value="<?php echo $employer['USERID']; ?>">
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
                            <i class="fa fa-users empty-state-icon"></i>
                            <p>No employers found.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php include 'footer.php' ?>
            </div>
        </div>

        <a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>
    </div>

    <!-- Employer Details Modal -->
    <div class="modal fade" id="employerModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: var(--primary-color); color: white;">
                    <h5 class="modal-title text-white">Employer Details</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="employerModalContent">
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
    function viewEmployer(userid) {
        $('#employerModal').modal('show');
        $('#employerModalContent').html(
            '<div class="text-center py-5"><i class="lni lni-spinner-arrow rotating" style="font-size: 3rem;"></i><p>Loading...</p></div>'
        );

        // Load employer details via AJAX
        $.ajax({
            url: 'admin-get-employer-details.php',
            method: 'GET',
            data: {
                userid: userid
            },
            success: function(response) {
                $('#employerModalContent').html(response);
            },
            error: function() {
                $('#employerModalContent').html(
                    '<div class="alert alert-danger">Failed to load employer details.</div>');
            }
        });
    }
    </script>

    <?php echo Toast::render(); ?>
</body>

</html>