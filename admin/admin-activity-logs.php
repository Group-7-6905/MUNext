<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';

// ==================== SESSION VALIDATION ====================


// ==================== PAGINATION SETUP ====================
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$records_per_page = 25;
$offset = ($page - 1) * $records_per_page;

// ==================== FILTER PARAMETERS ====================
$filter_action = isset($_GET['action']) ? mysqli_real_escape_string($con, $_GET['action']) : '';
$filter_userid = isset($_GET['userid']) ? (int)$_GET['userid'] : 0;
$filter_date_from = isset($_GET['date_from']) ? mysqli_real_escape_string($con, $_GET['date_from']) : '';
$filter_date_to = isset($_GET['date_to']) ? mysqli_real_escape_string($con, $_GET['date_to']) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';

// ==================== BUILD QUERY ====================
$where_clauses = [];
$params = [];
$types = '';

if (!empty($filter_action)) {
    $where_clauses[] = "ACTION = ?";
    $params[] = $filter_action;
    $types .= 's';
}

if ($filter_userid > 0) {
    $where_clauses[] = "USERID = ?";
    $params[] = $filter_userid;
    $types .= 'i';
}

if (!empty($filter_date_from)) {
    $where_clauses[] = "DATE(CREATED_AT) >= ?";
    $params[] = $filter_date_from;
    $types .= 's';
}

if (!empty($filter_date_to)) {
    $where_clauses[] = "DATE(CREATED_AT) <= ?";
    $params[] = $filter_date_to;
    $types .= 's';
}

if (!empty($search)) {
    $where_clauses[] = "(DESCRIPTION LIKE ? OR IP_ADDRESS LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= 'ss';
}

$where_sql = '';
if (!empty($where_clauses)) {
    $where_sql = 'WHERE ' . implode(' AND ', $where_clauses);
}

// ==================== GET TOTAL RECORDS ====================
$count_query = "SELECT COUNT(*) as total FROM tbl_activity_log $where_sql";
$count_stmt = mysqli_prepare($con, $count_query);

if (!empty($params)) {
    mysqli_stmt_bind_param($count_stmt, $types, ...$params);
}

mysqli_stmt_execute($count_stmt);
$total_records = mysqli_fetch_assoc(mysqli_stmt_get_result($count_stmt))['total'];
$total_pages = ceil($total_records / $records_per_page);
mysqli_stmt_close($count_stmt);

// ==================== GET ACTIVITY LOGS ====================
$query = "SELECT a.*, u.USERNAME, u.EMAIL, u.ROLE 
          FROM tbl_activity_log a 
          LEFT JOIN tblusers u ON a.USERID = u.USERID 
          $where_sql 
          ORDER BY a.CREATED_AT DESC 
          LIMIT ? OFFSET ?";

$params[] = $records_per_page;
$params[] = $offset;
$types .= 'ii';

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$activityLogs = mysqli_stmt_get_result($stmt);

// ==================== GET UNIQUE ACTIONS FOR FILTER ====================
$actionsQuery = "SELECT DISTINCT ACTION FROM tbl_activity_log ORDER BY ACTION ASC";
$actionsResult = mysqli_query($con, $actionsQuery);
$actions = [];
while ($row = mysqli_fetch_assoc($actionsResult)) {
    $actions[] = $row['ACTION'];
}

// ==================== GET ACTIVITY STATISTICS ====================
$statsQuery = "SELECT 
    COUNT(*) as total_activities,
    COUNT(DISTINCT USERID) as unique_users,
    COUNT(DISTINCT DATE(CREATED_AT)) as active_days,
    COUNT(DISTINCT IP_ADDRESS) as unique_ips
FROM tbl_activity_log 
WHERE CREATED_AT >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
$statsResult = mysqli_query($con, $statsQuery);
$stats = mysqli_fetch_assoc($statsResult);
?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Activity Logs - Admin Dashboard | MUNext</title>
    <link href="assets/css/styles.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/custom-dashboard.css">


</head>

<body>
    <div id="main-wrapper">
        <!-- ==================== HEADER ==================== -->
        <?php include 'header.php' ?>
        <div class="clearfix"></div>

        <!-- ==================== DASHBOARD WRAPPER ==================== -->
        <div class="dashboard-wrap bg-light">
            <!-- Mobile Navigation Toggle -->
            <a class="mobNavigation" data-toggle="collapse" href="#MobNav" role="button" aria-expanded="false"
                aria-controls="MobNav">
                <i class="fas fa-bars mr-2"></i>Dashboard Navigation
            </a>

            <!-- ==================== SIDEBAR ==================== -->
            <?php include 'sidenav.php' ?>

            <!-- ==================== MAIN CONTENT ==================== -->
            <div class="dashboard-content">
                <!-- Page Header -->
                <div class="page-header-section">
                    <h1 class="page-title">
                        <i class="lni lni-list mr-2"></i>Activity Logs
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="admin-analytics-dashboard.php">Reports & Analytics</a>
                            </li>
                            <li class="breadcrumb-item active">Activity Logs</li>
                        </ol>
                    </nav>
                </div>

                <div class="container-fluid">
                    <!-- Statistics Cards -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <span class="stat-number"><?php echo number_format($stats['total_activities']); ?></span>
                            <span class="stat-label">Activities (30 Days)</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-number"><?php echo number_format($stats['unique_users']); ?></span>
                            <span class="stat-label">Active Users</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-number"><?php echo number_format($stats['active_days']); ?></span>
                            <span class="stat-label">Active Days</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-number"><?php echo number_format($stats['unique_ips']); ?></span>
                            <span class="stat-label">Unique IP Addresses</span>
                        </div>
                    </div>

                    <!-- Filter Section -->
                    <div class="filter-section">
                        <div class="filter-header">
                            <h6 class="filter-title">
                                <i class="lni lni-funnel"></i>
                                Filter Activities
                            </h6>
                            <a href="admin-export-logs.php?<?php echo http_build_query($_GET); ?>" class="btn-export">
                                <i class="lni lni-download"></i>
                                Export
                            </a>
                        </div>

                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <label class="form-label">Action Type</label>
                                    <select name="action" class="form-control">
                                        <option value="">All Actions</option>
                                        <?php foreach ($actions as $action): ?>
                                        <option value="<?php echo htmlspecialchars($action); ?>"
                                            <?php echo $filter_action === $action ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($action); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-3 col-sm-6 mb-3">
                                    <label class="form-label">Date From</label>
                                    <input type="date" name="date_from" class="form-control"
                                        value="<?php echo htmlspecialchars($filter_date_from); ?>">
                                </div>

                                <div class="col-md-3 col-sm-6 mb-3">
                                    <label class="form-label">Date To</label>
                                    <input type="date" name="date_to" class="form-control"
                                        value="<?php echo htmlspecialchars($filter_date_to); ?>">
                                </div>

                                <div class="col-md-3 col-sm-6 mb-3">
                                    <label class="form-label">Search</label>
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Description or IP..."
                                        value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn-filter">
                                        <i class="lni lni-search mr-1"></i>Apply Filters
                                    </button>
                                    <a href="admin-activity-logs.php" class="btn-reset">
                                        <i class="lni lni-reload mr-1"></i>Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Activity Logs Table -->
                    <div class="activity-table-card">
                        <div class="table-header">
                            <i class="lni lni-list mr-2"></i>Activity Logs
                            <span style="float: right; font-weight: normal; font-size: 0.9rem;">
                                Total: <?php echo number_format($total_records); ?> records
                            </span>
                        </div>

                        <?php if (mysqli_num_rows($activityLogs) > 0): ?>
                        <div class="table-responsive">
                            <table class="activity-table">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">ID</th>
                                        <th style="width: 20%;">User</th>
                                        <th style="width: 12%;">Action</th>
                                        <th style="width: 28%;">Description</th>
                                        <th style="width: 12%;">IP Address</th>
                                        <th style="width: 13%;">Date & Time</th>
                                        <th style="width: 10%;">Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($log = mysqli_fetch_assoc($activityLogs)): ?>
                                    <tr>
                                        <td><?php echo $log['ACTIVITY_ID']; ?></td>
                                        <td>
                                            <div class="user-info">
                                                <div class="user-avatar">
                                                    <?php 
                                                    if (!empty($log['USERNAME'])) {
                                                        echo strtoupper(substr($log['USERNAME'], 0, 1));
                                                    } else {
                                                        echo '?';
                                                    }
                                                    ?>
                                                </div>
                                                <div class="user-details">
                                                    <span class="user-name">
                                                        <?php echo htmlspecialchars($log['USERNAME'] ?? 'Unknown'); ?>
                                                    </span>
                                                    <span class="user-email">
                                                        <?php echo htmlspecialchars($log['EMAIL'] ?? 'N/A'); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            $action = strtolower($log['ACTION']);
                                            $badgeClass = 'action-default';
                                            
                                            if (strpos($action, 'login') !== false) {
                                                $badgeClass = 'action-login';
                                            } elseif (strpos($action, 'logout') !== false) {
                                                $badgeClass = 'action-logout';
                                            } elseif (strpos($action, 'create') !== false) {
                                                $badgeClass = 'action-create';
                                            } elseif (strpos($action, 'update') !== false || strpos($action, 'edit') !== false) {
                                                $badgeClass = 'action-update';
                                            } elseif (strpos($action, 'delete') !== false) {
                                                $badgeClass = 'action-delete';
                                            }
                                            ?>
                                            <span class="action-badge <?php echo $badgeClass; ?>">
                                                <?php echo htmlspecialchars($log['ACTION']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($log['DESCRIPTION'] ?? 'No description'); ?>
                                        </td>
                                        <td>
                                            <code><?php echo htmlspecialchars($log['IP_ADDRESS'] ?? 'N/A'); ?></code>
                                        </td>
                                        <td>
                                            <?php echo date('M d, Y', strtotime($log['CREATED_AT'])); ?><br>
                                            <small
                                                class="text-muted"><?php echo date('h:i A', strtotime($log['CREATED_AT'])); ?></small>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-danger"
                                                onclick="viewDetails(<?php echo htmlspecialchars(json_encode($log)); ?>)">
                                                <i class="lni lni-eye"></i>
                                            </button>
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
                            <i class="lni lni-inbox empty-state-icon"></i>
                            <p class="empty-state-text">No activity logs found matching your criteria.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ==================== FOOTER ==================== -->
                <?php include 'footer.php' ?>
            </div>
        </div>

        <!-- Back to Top -->
        <a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>
    </div>

    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: var(--primary-color); color: white;">
                    <h5 class="modal-title text-white">Activity Details</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalContent">
                    <!-- Content will be populated by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded"
                        data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== SCRIPTS ==================== -->
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
    // ==================== VIEW DETAILS MODAL ====================
    function viewDetails(log) {
        const content = `
            <div style="line-height: 1.8;">
                <p><strong>Activity ID:</strong> ${log.ACTIVITY_ID}</p>
                <p><strong>User:</strong> ${log.USERNAME || 'Unknown'} (${log.EMAIL || 'N/A'})</p>
                <p><strong>Role:</strong> ${log.ROLE || 'N/A'}</p>
                <p><strong>Action:</strong> <span class="badge badge-info">${log.ACTION}</span></p>
                <p><strong>Description:</strong> ${log.DESCRIPTION || 'No description'}</p>
                <p><strong>IP Address:</strong> <code>${log.IP_ADDRESS || 'N/A'}</code></p>
                <p><strong>User Agent:</strong><br><small>${log.USER_AGENT || 'N/A'}</small></p>
                <p><strong>Date & Time:</strong> ${new Date(log.CREATED_AT).toLocaleString()}</p>
            </div>
        `;

        document.getElementById('modalContent').innerHTML = content;
        $('#detailsModal').modal('show');
    }

    // ==================== AUTO-REFRESH OPTION ====================
    let autoRefresh = false;
    let refreshInterval;

    function toggleAutoRefresh() {
        autoRefresh = !autoRefresh;

        if (autoRefresh) {
            refreshInterval = setInterval(() => {
                location.reload();
            }, 30000); // Refresh every 30 seconds
            console.log('Auto-refresh enabled');
        } else {
            clearInterval(refreshInterval);
            console.log('Auto-refresh disabled');
        }
    }
    </script>
</body>

</html>