<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';


// ==================== FETCH ADMIN PROFILE DATA ====================
$query = "SELECT * FROM tblusers WHERE USERID = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $session_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header('location: logout.php');
    exit();
}

$adminData = mysqli_fetch_assoc($result);

// Extract admin information
$USERID = $adminData['USERID'];
$FIRSTNAME = $adminData['FNAME'] ?? '';
$LASTNAME = $adminData['ONAME'] ?? '';
$EMAIL = $adminData['EMAIL'] ?? '';
$PHONE = $adminData['PHONE'] ?? '';
$USERNAME = $adminData['USERNAME'] ?? '';
$USERTYPE = $adminData['role'] ?? 'Admin';
$USERSTATUS = $adminData['USERSTATUS'] ?? 'Active';
$DATECREATED = $adminData['DATECREATED'] ?? '';
$DATEUPDATED = $adminData['DATEUPDATED'] ?? '';

// ==================== GET ACTIVITY STATISTICS ====================
// Recent login history
$loginHistoryQuery = "SELECT * FROM tbl_login_history WHERE USERID = ? ORDER BY LOGIN_TIME DESC LIMIT 5";
$loginStmt = mysqli_prepare($con, $loginHistoryQuery);
mysqli_stmt_bind_param($loginStmt, "i", $session_id);
mysqli_stmt_execute($loginStmt);
$loginHistory = mysqli_stmt_get_result($loginStmt);

// Count admin activities
$activityCountQuery = "SELECT COUNT(*) as count FROM tbl_activity_log WHERE USERID = ?";
$activityStmt = mysqli_prepare($con, $activityCountQuery);
mysqli_stmt_bind_param($activityStmt, "i", $session_id);
mysqli_stmt_execute($activityStmt);
$activityCount = mysqli_fetch_assoc(mysqli_stmt_get_result($activityStmt))['count'];

// Get platform statistics managed by admin
$platformStats = [
    'total_users' => mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM tblusers"))['count'],
    'total_companies' => mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM tblcompany"))['count'],
    'total_jobs' => mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM tbljob"))['count'],
    'total_applications' => mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM tbljobapplication"))['count']
];

// Initialize message
$msg = '';
$msgType = '';

// ==================== HANDLE PROFILE UPDATE ====================
if (isset($_POST['update_profile'])) {
    $firstname = mysqli_real_escape_string($con, trim($_POST['firstname']));
    $lastname = mysqli_real_escape_string($con, trim($_POST['lastname']));
    $phone = mysqli_real_escape_string($con, trim($_POST['phone']));
    
    // Validation
    if (empty($firstname) || empty($lastname)) {
        $msg = 'First name and last name are required.';
        $msgType = 'error';
    } else {
        // Update profile
        $updateQuery = "UPDATE tblusers SET FNAME = ?, ONAME = ?, PHONE = ?, DATEUPDATED = NOW() WHERE USERID = ?";
        $updateStmt = mysqli_prepare($con, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, "sssi", $firstname, $lastname, $phone, $session_id);
        
        if (mysqli_stmt_execute($updateStmt)) {
            // Log activity
            $activityQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, CREATED_AT) 
                             VALUES (?, 'PROFILE_UPDATE', 'Admin updated profile information', NOW())";
            $activityLogStmt = mysqli_prepare($con, $activityQuery);
            mysqli_stmt_bind_param($activityLogStmt, "i", $session_id);
            mysqli_stmt_execute($activityLogStmt);
            
            $msg = 'Profile updated successfully!';
            $msgType = 'success';
            
            // Refresh data
            $FIRSTNAME = $firstname;
            $LASTNAME = $lastname;
            $PHONE = $phone;
        } else {
            $msg = 'Failed to update profile. Please try again.';
            $msgType = 'error';
        }
    }
}
?>

<head>
    <meta charset="utf-8" />
    <meta name="author" content="MUNext" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Profile - Admin Dashboard | MUNext</title>

    <!-- Custom CSS -->
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
                        <i class="lni lni-user mr-2"></i>My Profile
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">My Profile</li>
                        </ol>
                    </nav>
                </div>

                <div class="container-fluid">
                    <div class="row">
                        <!-- ==================== PROFILE OVERVIEW ==================== -->
                        <div class="col-lg-4 col-md-12">
                            <div class="profile-card">
                                <div class="profile-header">

                                    <h4 class="profile-name">
                                        <?php echo htmlspecialchars($FIRSTNAME . ' ' . $LASTNAME); ?></h4>
                                    <p class="profile-role">
                                        <i class="lni lni-shield mr-1"></i><?php echo htmlspecialchars($USERTYPE); ?>
                                    </p>
                                    <span class="profile-status-badge">
                                        <i class="lni lni-checkmark-circle"></i>
                                        <?php echo htmlspecialchars($USERSTATUS); ?>
                                    </span>
                                </div>

                                <div class="profile-body">
                                    <!-- Statistics -->
                                    <div class="profile-stats">
                                        <div class="stat-item">
                                            <span
                                                class="stat-number"><?php echo number_format($platformStats['total_users']); ?></span>
                                            <span class="stat-label">Total Users</span>
                                        </div>
                                        <div class="stat-item">
                                            <span
                                                class="stat-number"><?php echo number_format($platformStats['total_jobs']); ?></span>
                                            <span class="stat-label">Total Jobs</span>
                                        </div>
                                        <div class="stat-item">
                                            <span
                                                class="stat-number"><?php echo number_format($activityCount); ?></span>
                                            <span class="stat-label">My Actions</span>
                                        </div>
                                        <div class="stat-item">
                                            <span
                                                class="stat-number"><?php echo number_format($platformStats['total_companies']); ?></span>
                                            <span class="stat-label">Companies</span>
                                        </div>
                                    </div>

                                    <!-- Quick Actions -->
                                    <div class="section-header">
                                        <h6 class="section-title">
                                            <div class="section-icon">
                                                <i class="lni lni-bolt"></i>
                                            </div>
                                            Quick Actions
                                        </h6>
                                    </div>

                                    <div class="quick-actions-grid">
                                        <a href="admin-change-password.php" class="quick-action-card">
                                            <div class="quick-action-icon">
                                                <i class="lni lni-lock-alt"></i>
                                            </div>
                                            <div class="quick-action-title">Change Password</div>
                                        </a>

                                        <a href="admin-general-settings.php" class="quick-action-card">
                                            <div class="quick-action-icon">
                                                <i class="lni lni-cog"></i>
                                            </div>
                                            <div class="quick-action-title">General Settings</div>
                                        </a>

                                        <a href="admin-activity-logs.php" class="quick-action-card">
                                            <div class="quick-action-icon">
                                                <i class="lni lni-list"></i>
                                            </div>
                                            <div class="quick-action-title">Activity Logs</div>
                                        </a>

                                        <a href="admin-help-support.php" class="quick-action-card">
                                            <div class="quick-action-icon">
                                                <i class="lni lni-help"></i>
                                            </div>
                                            <div class="quick-action-title">Help & Support</div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ==================== PROFILE DETAILS ==================== -->
                        <div class="col-lg-8 col-md-12">
                            <!-- Personal Information -->
                            <div class="info-section-details">
                                <div class="section-header">
                                    <h6 class="section-title">
                                        <div class="section-icon">
                                            <i class="lni lni-user"></i>
                                        </div>
                                        Personal Information
                                    </h6>
                                    <button type="button" class="btn-outline-custom" onclick="toggleEditMode()">
                                        <i class="lni lni-pencil"></i>
                                        <span id="editBtnText">Edit Profile</span>
                                    </button>
                                </div>

                                <!-- Alert Messages -->
                                <?php if (!empty($msg)): ?>
                                <div class="alert-message alert-<?php echo $msgType; ?>">
                                    <i
                                        class="lni lni-<?php echo $msgType === 'success' ? 'checkmark-circle' : 'close-circle'; ?>"></i>
                                    <span><?php echo htmlspecialchars($msg); ?></span>
                                </div>
                                <?php endif; ?>

                                <form method="post" id="profileForm">
                                    <div class="info-grid">
                                        <div class="form-group">
                                            <label class="form-label">First Name</label>
                                            <input type="text" name="firstname" class="form-control-custom"
                                                value="<?php echo htmlspecialchars($FIRSTNAME); ?>" disabled>
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">Last Name</label>
                                            <input type="text" name="lastname" class="form-control-custom"
                                                value="<?php echo htmlspecialchars($LASTNAME); ?>" disabled>
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">Email Address</label>
                                            <input type="email" class="form-control-custom"
                                                value="<?php echo htmlspecialchars($EMAIL); ?>" disabled>
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">Phone Number</label>
                                            <input type="text" name="phone" class="form-control-custom"
                                                value="<?php echo htmlspecialchars($PHONE); ?>" disabled>
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">Username</label>
                                            <input type="text" class="form-control-custom"
                                                value="<?php echo htmlspecialchars($USERNAME); ?>" disabled>
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">User Type</label>
                                            <input type="text" class="form-control-custom"
                                                value="<?php echo htmlspecialchars($USERTYPE); ?>" disabled>
                                        </div>
                                    </div>

                                    <div id="formActions" style="display: none; margin-top: 20px;">
                                        <button type="submit" name="update_profile" class="btn-primary-custom">
                                            <i class="lni lni-checkmark-circle"></i>
                                            <span>Save Changes</span>
                                        </button>
                                        <button type="button" class="btn-outline-custom" onclick="cancelEdit()">
                                            <i class="lni lni-close"></i>
                                            <span>Cancel</span>
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Account Information -->
                            <div class="info-section-details">
                                <div class="section-header">
                                    <h6 class="section-title">
                                        <div class="section-icon">
                                            <i class="lni lni-cog"></i>
                                        </div>
                                        Account Information
                                    </h6>
                                </div>

                                <div class="info-grid">
                                    <div class="info-item">
                                        <span class="info-label">User ID</span>
                                        <span class="info-value">#<?php echo $USERID; ?></span>
                                    </div>

                                    <div class="info-item">
                                        <span class="info-label">Account Status</span>
                                        <span class="info-value">
                                            <span style="color: var(--success-color); font-weight: 600;">
                                                <i class="lni lni-checkmark-circle"></i>
                                                <?php echo htmlspecialchars($USERSTATUS); ?>
                                            </span>
                                        </span>
                                    </div>

                                    <div class="info-item">
                                        <span class="info-label">Account Created</span>
                                        <span
                                            class="info-value"><?php echo date('M d, Y', strtotime($DATECREATED)); ?></span>
                                    </div>

                                    <div class="info-item">
                                        <span class="info-label">Last Updated</span>
                                        <span
                                            class="info-value"><?php echo !empty($DATEUPDATED) ? date('M d, Y', strtotime($DATEUPDATED)) : 'Never'; ?></span>
                                    </div>

                                    <div class="info-item">
                                        <span class="info-label">Last Login</span>
                                        <span
                                            class="info-value"><?php echo !empty($LASTLOGIN) ? date('M d, Y H:i', strtotime($LASTLOGIN)) : 'N/A'; ?></span>
                                    </div>

                                    <div class="info-item">
                                        <span class="info-label">Total Activities</span>
                                        <span class="info-value"><?php echo number_format($activityCount); ?>
                                            actions</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Login History -->
                            <div class="info-section-details">
                                <div class="section-header">
                                    <h6 class="section-title">
                                        <div class="section-icon">
                                            <i class="lni lni-timer"></i>
                                        </div>
                                        Recent Login Activity
                                    </h6>
                                </div>

                                <ul class="activity-list">
                                    <?php 
                                    if (mysqli_num_rows($loginHistory) > 0):
                                        while ($login = mysqli_fetch_assoc($loginHistory)):
                                    ?>
                                    <li class="activity-item">
                                        <div class="activity-icon">
                                            <i class="lni lni-enter"></i>
                                        </div>
                                        <div class="activity-content">
                                            <div class="activity-title">Logged in successfully</div>
                                            <div class="activity-meta">
                                                <i class="lni lni-calendar mr-1"></i>
                                                <?php echo date('M d, Y - h:i A', strtotime($login['LOGIN_TIME'])); ?>
                                                <?php if (!empty($login['IP_ADDRESS'])): ?>
                                                | <i
                                                    class="lni lni-map-marker ml-2 mr-1"></i><?php echo htmlspecialchars($login['IP_ADDRESS']); ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </li>
                                    <?php 
                                        endwhile;
                                    else:
                                    ?>
                                    <li class="activity-item">
                                        <div class="activity-icon">
                                            <i class="lni lni-information"></i>
                                        </div>
                                        <div class="activity-content">
                                            <div class="activity-title">No login history available</div>
                                        </div>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== FOOTER ==================== -->
                <?php include 'footer.php' ?>
            </div>
        </div>

        <!-- Back to Top -->
        <a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>
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
    // ==================== TOGGLE EDIT MODE ====================
    function toggleEditMode() {
        const formInputs = document.querySelectorAll(
            '#profileForm input[name="firstname"], #profileForm input[name="lastname"], #profileForm input[name="phone"]'
        );
        const formActions = document.getElementById('formActions');
        const editBtn = document.getElementById('editBtnText');

        formInputs.forEach(input => {
            input.disabled = !input.disabled;
        });

        if (formActions.style.display === 'none') {
            formActions.style.display = 'block';
            editBtn.textContent = 'Cancel Edit';
        } else {
            formActions.style.display = 'none';
            editBtn.textContent = 'Edit Profile';
        }
    }

    // ==================== CANCEL EDIT ====================
    function cancelEdit() {
        location.reload();
    }

    // ==================== AVATAR UPLOAD ====================
    document.getElementById('avatar-upload')?.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector('.profile-avatar').src = e.target.result;
            };
            reader.readAsDataURL(e.target.files[0]);

            // Here you would typically upload to server
            // uploadAvatar(e.target.files[0]);
        }
    });

    // ==================== AUTO-HIDE SUCCESS MESSAGE ====================
    <?php if ($msgType === 'success'): ?>
    setTimeout(function() {
        const alert = document.querySelector('.alert-message');
        if (alert) {
            alert.style.opacity = '0';
            setTimeout(() => alert.style.display = 'none', 300);
        }
    }, 5000);
    <?php endif; ?>
    </script>
</body>

</html>