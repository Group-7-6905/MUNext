<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';
require '../include/toast.php';



// ==================== HANDLE USER ACTIONS ====================
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $userid = isset($_POST['userid']) ? (int)$_POST['userid'] : 0;
    
    if ($userid > 0) {
        switch ($action) {
            case 'activate':
                $updateQuery = "UPDATE tblusers SET USERSTATUS = 'Active', DATEUPDATED = NOW() WHERE USERID = ?";
                $stmt = mysqli_prepare($con, $updateQuery);
                mysqli_stmt_bind_param($stmt, "i", $userid);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Log activity
                    $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                                VALUES (?, 'USER_ACTIVATED', 'Admin activated a job seeker account', ?, NOW())";
                    $logStmt = mysqli_prepare($con, $logQuery);
                    $ip = getClientIP();
                    mysqli_stmt_bind_param($logStmt, "is", $session_id, $ip);
                    mysqli_stmt_execute($logStmt);
                    
                    Toast::success('User activated successfully!');
                } else {
                    Toast::error('Failed to activate user.');
                }
                mysqli_stmt_close($stmt);
                break;
                
            case 'suspend':
                $updateQuery = "UPDATE tblusers SET USERSTATUS = 'Suspended', DATEUPDATED = NOW() WHERE USERID = ?";
                $stmt = mysqli_prepare($con, $updateQuery);
                mysqli_stmt_bind_param($stmt, "i", $userid);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Log activity
                    $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                                VALUES (?, 'USER_SUSPENDED', 'Admin suspended a job seeker account', ?, NOW())";
                    $logStmt = mysqli_prepare($con, $logQuery);
                    $ip = getClientIP();
                    mysqli_stmt_bind_param($logStmt, "is", $session_id, $ip);
                    mysqli_stmt_execute($logStmt);
                    
                    Toast::warning('User suspended successfully!');
                } else {
                    Toast::error('Failed to suspend user.');
                }
                mysqli_stmt_close($stmt);
                break;
                
            case 'delete':
                // Soft delete - just mark as deleted
                $deleteQuery = "UPDATE tblusers SET USERSTATUS = 'Deleted', DATEUPDATED = NOW() WHERE USERID = ?";
                $stmt = mysqli_prepare($con, $deleteQuery);
                mysqli_stmt_bind_param($stmt, "i", $userid);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Log activity
                    $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                                VALUES (?, 'USER_DELETED', 'Admin deleted a job seeker account', ?, NOW())";
                    $logStmt = mysqli_prepare($con, $logQuery);
                    $ip = getClientIP();
                    mysqli_stmt_bind_param($logStmt, "is", $session_id, $ip);
                    mysqli_stmt_execute($logStmt);
                    
                    Toast::success('User deleted successfully!');
                } else {
                    Toast::error('Failed to delete user.');
                }
                mysqli_stmt_close($stmt);
                break;
        }
        
        header('location: admin-job-seekers.php?' . http_build_query($_GET));
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
$where_clauses = ["u.ROLE = 'Applicant'"];
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
    $where_clauses[] = "(u.FNAME LIKE ? OR u.ONAME LIKE ? OR u.EMAIL LIKE ? OR u.USERNAME LIKE ? OR a.CITY LIKE ? OR a.COUNTRY LIKE ? OR a.SKILLS LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= 'sssssss';
}

$where_sql = 'WHERE ' . implode(' AND ', $where_clauses);

// ==================== GET TOTAL RECORDS ====================
$count_query = "SELECT COUNT(*) as total 
                FROM tblusers u 
                LEFT JOIN tblapplicants a ON u.USERID = a.USERID 
                $where_sql";
$count_stmt = mysqli_prepare($con, $count_query);

if (!empty($params)) {
    mysqli_stmt_bind_param($count_stmt, $types, ...$params);
}

mysqli_stmt_execute($count_stmt);
$total_records = mysqli_fetch_assoc(mysqli_stmt_get_result($count_stmt))['total'];
$total_pages = ceil($total_records / $records_per_page);
mysqli_stmt_close($count_stmt);

// ==================== GET JOB SEEKERS WITH APPLICANT DETAILS ====================
$query = "SELECT u.*, 
          a.APPLICANTID, a.JOBCATEGORYID, a.JOBTITLE, a.EXCOMPANYNAME, a.EXJOBTITLE,
          a.FNAME as APPLICANT_FNAME, a.OTHERNAMES, a.FULLADDRESS, a.CITY, a.COUNTRY,
          a.SEX, a.BIRTHDATE, a.ABOUTME, a.USERNAME as APPLICANT_USERNAME, 
          a.EMAILADDRESS, a.CONTACTNO, a.DEGREE, a.SCHOOLNAME, a.SKILLS, 
          a.APPLICANTPHOTO, a.FB_link, a.LinkedIn_link
          FROM tblusers u 
          LEFT JOIN tblapplicants a ON u.USERID = a.USERID
          $where_sql 
          ORDER BY u.DATECREATED DESC 
          LIMIT ? OFFSET ?";

$params[] = $records_per_page;
$params[] = $offset;
$types .= 'ii';

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$jobSeekers = mysqli_stmt_get_result($stmt);

// ==================== GET STATISTICS ====================
$totalUsersQuery = "SELECT COUNT(*) as total FROM tblusers WHERE ROLE = 'Applicant'";
$totalUsers = mysqli_fetch_assoc(mysqli_query($con, $totalUsersQuery))['total'];

$activeUsersQuery = "SELECT COUNT(*) as total FROM tblusers WHERE ROLE = 'Applicant' AND USERSTATUS = 'Active'";
$activeUsers = mysqli_fetch_assoc(mysqli_query($con, $activeUsersQuery))['total'];

$pendingUsersQuery = "SELECT COUNT(*) as total FROM tblusers WHERE ROLE = 'Applicant' AND USERSTATUS = 'Pending'";
$pendingUsers = mysqli_fetch_assoc(mysqli_query($con, $pendingUsersQuery))['total'];

$suspendedUsersQuery = "SELECT COUNT(*) as total FROM tblusers WHERE ROLE = 'Applicant' AND USERSTATUS = 'Suspended'";
$suspendedUsers = mysqli_fetch_assoc(mysqli_query($con, $suspendedUsersQuery))['total'];

// Get users with complete profiles (those who have applicant records with photo and skills)
$completeProfilesQuery = "SELECT COUNT(*) as total 
                         FROM tblusers u 
                         INNER JOIN tblapplicants a ON u.USERID = a.USERID 
                         WHERE u.ROLE = 'Applicant' 
                         AND a.APPLICANTPHOTO IS NOT NULL 
                         AND a.SKILLS IS NOT NULL";
$completeProfiles = mysqli_fetch_assoc(mysqli_query($con, $completeProfilesQuery))['total'];
?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Job Seekers Management - Admin Dashboard | MUNext</title>
    <link href="assets/css/styles.css" rel="stylesheet">


    <link rel="stylesheet" href="assets/css/custom-dashboard.css" />
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
                        <i class="lni lni-users mr-2"></i>Job Seekers Management
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">User Management</a></li>
                            <li class="breadcrumb-item active">Job Seekers</li>
                        </ol>
                    </nav>
                </div>


                <div class="container-fluid">
                    <!-- Statistics -->
                    <div class="stats-grid">
                        <div class="stat-card total">
                            <span class="stat-number"><?php echo number_format($totalUsers); ?></span>
                            <span class="stat-label">Total Job Seekers</span>
                        </div>
                        <div class="stat-card active">
                            <span class="stat-number"><?php echo number_format($activeUsers); ?></span>
                            <span class="stat-label">Active Users</span>
                        </div>
                        <div class="stat-card pending">
                            <span class="stat-number"><?php echo number_format($pendingUsers); ?></span>
                            <span class="stat-label">Pending Approval</span>
                        </div>
                        <div class="stat-card suspended">
                            <span class="stat-number"><?php echo number_format($suspendedUsers); ?></span>
                            <span class="stat-label">Suspended</span>
                        </div>
                        <div class="stat-card complete">
                            <span class="stat-number"><?php echo number_format($completeProfiles); ?></span>
                            <span class="stat-label">Complete Profiles</span>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="filter-section">
                        <div class="filter-header">
                            <h6 class="filter-title">
                                <i class="lni lni-funnel mr-2"></i>Filter Job Seekers
                            </h6>
                        </div>

                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Search</label>
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Name, email, city, skills..."
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
                                        <a href="admin-job-seekers.php" class="btn-reset">Reset</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Users Table -->
                    <div class="users-table-card">
                        <div class="table-header">
                            <i class="lni lni-users mr-2"></i>Job Seekers List
                            <span style="float: right; font-weight: normal;">
                                Total: <?php echo number_format($total_records); ?> users
                            </span>
                        </div>

                        <?php if (mysqli_num_rows($jobSeekers) > 0): ?>
                        <div class="table-responsive">
                            <table class="users-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Username</th>
                                        <th>Location</th>
                                        <th>Education</th>
                                        <th>Profile</th>
                                        <th>Status</th>
                                        <th>Joined</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($user = mysqli_fetch_assoc($jobSeekers)): ?>
                                    <tr>
                                        <td><?php echo $user['USERID']; ?></td>
                                        <td>
                                            <div class="user-info">


                                                <?php if (!empty($user['APPLICANTPHOTO'])): ?>
                                                <img src="<?php echo $path.htmlspecialchars($user['APPLICANTPHOTO']); ?>"
                                                    alt="Avatar" class="user-avatar"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <?php endif; ?>
                                                <div class="user-avatar"
                                                    style="<?php echo !empty($user['APPLICANTPHOTO']) ? 'display:none;' : ''; ?>">
                                                    <?php echo strtoupper(substr($user['FNAME'], 0, 1)); ?>
                                                </div>





                                                <div class="user-details">
                                                    <span class="user-name">
                                                        <?php 
                                                        // Use applicant name if available, otherwise use user table name
                                                        $displayName = !empty($user['APPLICANT_FNAME']) 
                                                            ? $user['APPLICANT_FNAME'] . ' ' . $user['OTHERNAMES']
                                                            : $user['FNAME'] . ' ' . $user['ONAME'];
                                                        echo htmlspecialchars($displayName);
                                                        ?>
                                                    </span>
                                                    <span class="user-email">
                                                        <?php 
                                                        // Use applicant email if available, otherwise use user table email
                                                        $displayEmail = !empty($user['EMAILADDRESS']) 
                                                            ? $user['EMAILADDRESS'] 
                                                            : $user['EMAIL'];
                                                        echo htmlspecialchars($displayEmail); 
                                                        ?>
                                                    </span>
                                                    <?php if (!empty($user['CITY'])): ?>
                                                    <span class="user-location">
                                                        <i class="lni lni-map-marker"></i>
                                                        <?php echo htmlspecialchars($user['CITY']); ?>
                                                    </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($user['USERNAME']); ?></td>
                                        <td>
                                            <?php 
                                            if (!empty($user['CITY']) || !empty($user['COUNTRY'])) {
                                                $location = array_filter([
                                                    $user['CITY'],
                                                    $user['COUNTRY']
                                                ]);
                                                echo htmlspecialchars(implode(', ', $location));
                                            } else {
                                                echo 'N/A';
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($user['DEGREE'] ?? 'N/A'); ?></td>
                                        <td>
                                            <?php if (!empty($user['APPLICANTID']) && (!empty($user['APPLICANTPHOTO']) || !empty($user['SKILLS']))): ?>
                                            <span class="profile-badge profile-complete">
                                                <i class="lni lni-checkmark"></i> Complete
                                            </span>
                                            <?php else: ?>
                                            <span class="profile-badge profile-incomplete">
                                                <i class="lni lni-close"></i> Incomplete
                                            </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = 'status-active';
                                            $status = $user['USERSTATUS'];
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
                                        <td><?php echo date('M d, Y', strtotime($user['DATECREATED'])); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-action btn-view"
                                                    onclick="viewUser(<?php echo $user['USERID']; ?>)" title="View">
                                                    <i class="lni lni-eye"></i>
                                                </button>

                                                <?php if ($user['USERSTATUS'] != 'Active'): ?>
                                                <form method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Activate this user?')">
                                                    <input type="hidden" name="userid"
                                                        value="<?php echo $user['USERID']; ?>">
                                                    <input type="hidden" name="action" value="activate">
                                                    <button type="submit" class="btn-action btn-activate"
                                                        title="Activate">
                                                        <i class="lni lni-checkmark"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>

                                                <?php if ($user['USERSTATUS'] == 'Active'): ?>
                                                <form method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Suspend this user?')">
                                                    <input type="hidden" name="userid"
                                                        value="<?php echo $user['USERID']; ?>">
                                                    <input type="hidden" name="action" value="suspend">
                                                    <button type="submit" class="btn-action btn-suspend"
                                                        title="Suspend">
                                                        <i class="lni lni-ban"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>

                                                <form method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Delete this user permanently?')">
                                                    <input type="hidden" name="userid"
                                                        value="<?php echo $user['USERID']; ?>">
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
                            <i class="lni lni-users empty-state-icon"></i>
                            <p>No job seekers found.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php include 'footer.php' ?>
            </div>
        </div>

        <a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>
    </div>

    <!-- User Details Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">User Details</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="userModalContent">
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
    function viewUser(userid) {
        $('#userModal').modal('show');
        $('#userModalContent').html(
            '<div class="text-center py-5"><i class="lni lni-spinner-arrow rotating" style="font-size: 3rem;"></i><p>Loading...</p></div>'
        );

        // Load user details via AJAX
        $.ajax({
            url: 'admin-get-user-details.php',
            method: 'GET',
            data: {
                userid: userid
            },
            success: function(response) {
                $('#userModalContent').html(response);
            },
            error: function() {
                $('#userModalContent').html(
                    '<div class="alert alert-danger">Failed to load user details.</div>');
            }
        });
    }
    </script>

    <?php echo Toast::render(); ?>
</body>

</html>