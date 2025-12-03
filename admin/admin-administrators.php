<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';
require '../include/toast.php';


// ==================== HANDLE ADMIN ACTIONS ====================
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $adminid = isset($_POST['adminid']) ? (int)$_POST['adminid'] : 0;
    
    // Prevent admin from modifying their own account via these actions
    if ($adminid == $session_id && in_array($action, ['suspend', 'delete'])) {
        Toast::error('You cannot perform this action on your own account!');
        header('location: admin-administrators.php?' . http_build_query($_GET));
        exit();
    }
    
    if ($adminid > 0) {
        switch ($action) {
            case 'activate':
                $updateQuery = "UPDATE tblusers SET USERSTATUS = 'Active' WHERE USERID = ? AND ROLE = 'Admin'";
                $stmt = mysqli_prepare($con, $updateQuery);
                mysqli_stmt_bind_param($stmt, "i", $adminid);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Log activity
                    $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                                VALUES (?, 'ADMIN_ACTIVATED', 'Admin activated another administrator account', ?, NOW())";
                    $logStmt = mysqli_prepare($con, $logQuery);
                    $ip = getClientIP();
                    mysqli_stmt_bind_param($logStmt, "is", $session_id, $ip);
                    mysqli_stmt_execute($logStmt);
                    
                    Toast::success('Administrator activated successfully!');
                } else {
                    Toast::error('Failed to activate administrator.');
                }
                mysqli_stmt_close($stmt);
                break;
                
            case 'suspend':
                $updateQuery = "UPDATE tblusers SET USERSTATUS = 'Suspended' WHERE USERID = ? AND ROLE = 'Admin'";
                $stmt = mysqli_prepare($con, $updateQuery);
                mysqli_stmt_bind_param($stmt, "i", $adminid);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Log activity
                    $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                                VALUES (?, 'ADMIN_SUSPENDED', 'Admin suspended another administrator account', ?, NOW())";
                    $logStmt = mysqli_prepare($con, $logQuery);
                    $ip = getClientIP();
                    mysqli_stmt_bind_param($logStmt, "is", $session_id, $ip);
                    mysqli_stmt_execute($logStmt);
                    
                    Toast::warning('Administrator suspended successfully!');
                } else {
                    Toast::error('Failed to suspend administrator.');
                }
                mysqli_stmt_close($stmt);
                break;
                
            case 'delete':
                // Soft delete - mark as deleted
                $deleteQuery = "UPDATE tblusers SET USERSTATUS = 'Deleted' WHERE USERID = ? AND ROLE = 'Admin'";
                $stmt = mysqli_prepare($con, $deleteQuery);
                mysqli_stmt_bind_param($stmt, "i", $adminid);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Log activity
                    $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                                VALUES (?, 'ADMIN_DELETED', 'Admin deleted another administrator account', ?, NOW())";
                    $logStmt = mysqli_prepare($con, $logQuery);
                    $ip = getClientIP();
                    mysqli_stmt_bind_param($logStmt, "is", $session_id, $ip);
                    mysqli_stmt_execute($logStmt);
                    
                    Toast::success('Administrator deleted successfully!');
                } else {
                    Toast::error('Failed to delete administrator.');
                }
                mysqli_stmt_close($stmt);
                break;
        }
        
        header('location: admin-administrators.php?' . http_build_query($_GET));
        exit();
    }
}

// ==================== HANDLE ADD NEW ADMIN ====================
if (isset($_POST['add_admin'])) {
    $firstname = mysqli_real_escape_string($con, trim($_POST['firstname']));
    $lastname = mysqli_real_escape_string($con, trim($_POST['lastname']));
    $email = mysqli_real_escape_string($con, trim($_POST['email']));
    $username = mysqli_real_escape_string($con, trim($_POST['username']));
    $password = $_POST['password'];
    $phone = mysqli_real_escape_string($con, trim($_POST['phone']));
    
    // Validate inputs
    $errors = [];
    
    if (empty($firstname) || empty($lastname) || empty($email) || empty($username) || empty($password)) {
        $errors[] = "All fields are required.";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }
    
    // Check if email already exists
    $checkEmailQuery = "SELECT USERID FROM tblusers WHERE EMAIL = ?";
    $checkStmt = mysqli_prepare($con, $checkEmailQuery);
    mysqli_stmt_bind_param($checkStmt, "s", $email);
    mysqli_stmt_execute($checkStmt);
    if (mysqli_num_rows(mysqli_stmt_get_result($checkStmt)) > 0) {
        $errors[] = "Email already exists.";
    }
    
    // Check if username already exists
    $checkUsernameQuery = "SELECT USERID FROM tblusers WHERE USERNAME = ?";
    $checkUsernameStmt = mysqli_prepare($con, $checkUsernameQuery);
    mysqli_stmt_bind_param($checkUsernameStmt, "s", $username);
    mysqli_stmt_execute($checkUsernameStmt);
    if (mysqli_num_rows(mysqli_stmt_get_result($checkUsernameStmt)) > 0) {
        $errors[] = "Username already exists.";
    }
    
    if (empty($errors)) {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new admin
        $insertQuery = "INSERT INTO tblusers 
                       (FNAME, ONAME, EMAIL, USERNAME, PASS, PHONE, ROLE, USERSTATUS, DATECREATED) 
                       VALUES (?, ?, ?, ?, ?, ?, 'Admin', 'Active', NOW())";
        $insertStmt = mysqli_prepare($con, $insertQuery);
        mysqli_stmt_bind_param($insertStmt, "ssssss", $firstname, $lastname, $email, $username, $hashedPassword, $phone);
        
        if (mysqli_stmt_execute($insertStmt)) {
            // Log activity
            $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                        VALUES (?, 'ADMIN_CREATED', 'New administrator account created', ?, NOW())";
            $logStmt = mysqli_prepare($con, $logQuery);
            $ip = getClientIP();
            mysqli_stmt_bind_param($logStmt, "is", $session_id, $ip);
            mysqli_stmt_execute($logStmt);
            
            Toast::success('New administrator added successfully!');
        } else {
            Toast::error('Failed to add new administrator.');
        }
    } else {
        foreach ($errors as $error) {
            Toast::error($error);
        }
    }
    
    header('location: admin-administrators.php');
    exit();
}

// ==================== PAGINATION SETUP ====================
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$records_per_page = 20;
$offset = ($page - 1) * $records_per_page;

// ==================== FILTER PARAMETERS ====================
$filter_status = isset($_GET['status']) ? mysqli_real_escape_string($con, $_GET['status']) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';

// ==================== BUILD QUERY ====================
$where_clauses = ["ROLE = 'Admin'"];
$params = [];
$types = '';

if (!empty($filter_status)) {
    $where_clauses[] = "USERSTATUS = ?";
    $params[] = $filter_status;
    $types .= 's';
}

if (!empty($search)) {
    $where_clauses[] = "(FNAME LIKE ? OR ONAME LIKE ? OR EMAIL LIKE ? OR USERNAME LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= 'ssss';
}

$where_sql = 'WHERE ' . implode(' AND ', $where_clauses);

// ==================== GET TOTAL RECORDS ====================
$count_query = "SELECT COUNT(*) as total FROM tblusers $where_sql";
$count_stmt = mysqli_prepare($con, $count_query);

if (!empty($params)) {
    mysqli_stmt_bind_param($count_stmt, $types, ...$params);
}

mysqli_stmt_execute($count_stmt);
$total_records = mysqli_fetch_assoc(mysqli_stmt_get_result($count_stmt))['total'];
$total_pages = ceil($total_records / $records_per_page);
mysqli_stmt_close($count_stmt);

// ==================== GET ADMINISTRATORS ====================
$query = "SELECT * FROM tblusers $where_sql ORDER BY DATECREATED DESC LIMIT ? OFFSET ?";
$params[] = $records_per_page;
$params[] = $offset;
$types .= 'ii';

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$administrators = mysqli_stmt_get_result($stmt);

// ==================== GET STATISTICS ====================
$totalAdminsQuery = "SELECT COUNT(*) as total FROM tblusers WHERE ROLE = 'Admin'";
$totalAdmins = mysqli_fetch_assoc(mysqli_query($con, $totalAdminsQuery))['total'];

$activeAdminsQuery = "SELECT COUNT(*) as total FROM tblusers WHERE ROLE = 'Admin' AND USERSTATUS = 'Active'";
$activeAdmins = mysqli_fetch_assoc(mysqli_query($con, $activeAdminsQuery))['total'];

$suspendedAdminsQuery = "SELECT COUNT(*) as total FROM tblusers WHERE ROLE = 'Admin' AND USERSTATUS = 'Suspended'";
$suspendedAdmins = mysqli_fetch_assoc(mysqli_query($con, $suspendedAdminsQuery))['total'];
?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administrators Management - Admin Dashboard | MUNext</title>
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
                    <div>
                        <h1 class="page-title">
                            <i class="lni lni-shield mr-2"></i>Administrators Management
                        </h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="#">User Management</a></li>
                                <li class="breadcrumb-item active">Administrators</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <button class="btn-add-admin" data-toggle="modal" data-target="#addAdminModal">
                            <i class="lni lni-plus"></i>
                            Add New Admin
                        </button>
                    </div>
                </div>

                <div class="container-fluid">
                    <!-- Statistics -->
                    <div class="stats-grid">
                        <div class="stat-card total">
                            <span class="stat-number"><?php echo number_format($totalAdmins); ?></span>
                            <span class="stat-label">Total Administrators</span>
                        </div>
                        <div class="stat-card active">
                            <span class="stat-number"><?php echo number_format($activeAdmins); ?></span>
                            <span class="stat-label">Active Admins</span>
                        </div>
                        <div class="stat-card suspended">
                            <span class="stat-number"><?php echo number_format($suspendedAdmins); ?></span>
                            <span class="stat-label">Suspended</span>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="filter-section">
                        <div class="filter-header">
                            <h6 class="filter-title">
                                <i class="lni lni-funnel mr-2"></i>Filter Administrators
                            </h6>
                        </div>

                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-5 mb-3">
                                    <label class="form-label">Search</label>
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Name, email, username..."
                                        value="<?php echo htmlspecialchars($search); ?>">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="Active"
                                            <?php echo $filter_status === 'Active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="Suspended"
                                            <?php echo $filter_status === 'Suspended' ? 'selected' : ''; ?>>Suspended
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn-filter">
                                            <i class="lni lni-search mr-1"></i>Apply Filters
                                        </button>
                                        <a href="admin-administrators.php" class="btn-reset">Reset</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Administrators Table -->
                    <div class="admins-table-card">
                        <div class="table-header">
                            <i class="lni lni-shield mr-2"></i>Administrators List
                            <span style="float: right; font-weight: normal;">
                                Total: <?php echo number_format($total_records); ?> admins
                            </span>
                        </div>

                        <?php if (mysqli_num_rows($administrators) > 0): ?>
                        <div class="table-responsive">
                            <table class="admins-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Administrator</th>
                                        <th>Username</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th>Added Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($admin = mysqli_fetch_assoc($administrators)): ?>
                                    <tr <?php echo $admin['USERID'] == $session_id ? 'class="current-user"' : ''; ?>>
                                        <td><?php echo $admin['USERID']; ?></td>
                                        <td>
                                            <div class="user-info">
                                                <div class="user-avatar">
                                                    <?php echo strtoupper(substr($admin['FNAME'], 0, 1)); ?>
                                                </div>
                                                <div class="user-details">
                                                    <span class="user-name">
                                                        <?php echo htmlspecialchars($admin['FNAME'] . ' ' . $admin['ONAME']); ?>
                                                        <?php if ($admin['USERID'] == $session_id): ?>
                                                        <span class="badge-you">You</span>
                                                        <?php endif; ?>
                                                    </span>
                                                    <span class="user-email">
                                                        <?php echo htmlspecialchars($admin['EMAIL']); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($admin['USERNAME']); ?></td>
                                        <td><?php echo htmlspecialchars($admin['PHONE'] ?? 'N/A'); ?></td>
                                        <td>
                                            <?php
                                            $statusClass = 'status-active';
                                            $status = $admin['USERSTATUS'];
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
                                        <td><?php echo date('M d, Y', strtotime($admin['DATECREATED'])); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <!-- <button class="btn-action btn-view"
                                                    onclick="viewAdmin(<?php echo $admin['USERID']; ?>)">
                                                    <i class="lni lni-eye"></i>
                                                </button> -->

                                                <?php if ($admin['USERID'] != $session_id): ?>
                                                <?php if ($admin['USERSTATUS'] != 'Active'): ?>
                                                <form method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Activate this administrator?')">
                                                    <input type="hidden" name="adminid"
                                                        value="<?php echo $admin['USERID']; ?>">
                                                    <input type="hidden" name="action" value="activate">
                                                    <button type="submit" class="btn-action btn-activate">
                                                        <i class="lni lni-checkmark"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>

                                                <?php if ($admin['USERSTATUS'] == 'Active'): ?>
                                                <form method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Suspend this administrator?')">
                                                    <input type="hidden" name="adminid"
                                                        value="<?php echo $admin['USERID']; ?>">
                                                    <input type="hidden" name="action" value="suspend">
                                                    <button type="submit" class="btn-action btn-suspend">
                                                        <i class="lni lni-ban"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>

                                                <form method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Delete this administrator permanently?')">
                                                    <input type="hidden" name="adminid"
                                                        value="<?php echo $admin['USERID']; ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <button type="submit" class="btn-action btn-delete">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                                <?php else: ?>
                                                <button class="btn-action btn-suspend" disabled
                                                    title="Cannot modify your own account">
                                                    <i class="lni lni-lock"></i>
                                                </button>
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
                            <i class="lni lni-shield empty-state-icon"></i>
                            <p>No administrators found.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php include 'footer.php' ?>
            </div>
        </div>

        <a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>
    </div>

    <!-- Add Admin Modal -->
    <div class="modal fade" id="addAdminModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: var(--primary-color); color: white;">
                    <h5 class="modal-title text-white">
                        <i class="lni lni-plus mr-2"></i>Add New Administrator
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name *</label>
                                <input type="text" name="firstname" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name *</label>
                                <input type="text" name="lastname" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Username *</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password *</label>
                                <input type="password" name="password" class="form-control" required minlength="6">
                                <small class="text-muted">Minimum 6 characters</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                        </div>
                        <div class="alert alert-info mb-0">
                            <i class="lni lni-information mr-2"></i>
                            <strong>Note:</strong> The new administrator will be created with Active status and will
                            have full administrative privileges.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded" data-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_admin" class="btn theme-bg text-white rounded">
                            <i class="lni lni-checkmark mr-1"></i>Add Administrator
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Admin Details Modal -->
    <div class="modal fade" id="adminModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Administrator Details</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="adminModalContent">
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
    function viewAdmin(adminid) {
        $('#adminModal').modal('show');
        $('#adminModalContent').html(
            '<div class="text-center py-5"><i class="lni lni-spinner-arrow rotating" style="font-size: 3rem;"></i><p>Loading...</p></div>'
        );

        // Load admin details via AJAX
        $.ajax({
            url: 'admin-get-admin-details.php',
            method: 'GET',
            data: {
                adminid: adminid
            },
            success: function(response) {
                $('#adminModalContent').html(response);
            },
            error: function() {
                $('#adminModalContent').html(
                    '<div class="alert alert-danger">Failed to load administrator details.</div>');
            }
        });
    }
    </script>

    <?php echo Toast::render(); ?>
</body>

</html>