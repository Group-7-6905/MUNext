<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';
require '../include/toast.php';


// ==================== HANDLE DELETE NOTIFICATION ====================
if (!empty($_GET['type']) && $_GET['type'] == 'delete') {
    $noticid = isset($_GET['noticid']) ? (int)$_GET['noticid'] : 0;
    
    if ($noticid > 0) {
        $delete_sql = "DELETE FROM tblnotification WHERE ID = ?";
        $stmt = mysqli_prepare($con, $delete_sql);
        mysqli_stmt_bind_param($stmt, "i", $noticid);
        
        if (mysqli_stmt_execute($stmt)) {
            Toast::success('Notification deleted successfully!');
        } else {
            Toast::error('Failed to delete notification.');
        }
        mysqli_stmt_close($stmt);
        
        header('location: index.php');
        exit();
    }
}

// ==================== FETCH ADMIN INFO ====================
$adminQuery = "SELECT * FROM tblusers WHERE USERID = ?";
$adminStmt = mysqli_prepare($con, $adminQuery);
mysqli_stmt_bind_param($adminStmt, "i", $session_id);
mysqli_stmt_execute($adminStmt);
$adminResult = mysqli_stmt_get_result($adminStmt);
$adminData = mysqli_fetch_assoc($adminResult);
$FULLNAME = $adminData['FNAME'] . ' ' . $adminData['ONAME'];

// ==================== GET DASHBOARD STATISTICS ====================
// Total Active Jobs
$queryjobs = "SELECT COUNT(*) as total FROM tbljob WHERE JOBSTATUS = 'Active'";
$resultjobs = mysqli_query($con, $queryjobs);
$jobs = mysqli_fetch_assoc($resultjobs)['total'];

// Total Applications
$queryapp = "SELECT COUNT(*) as total FROM tbljobapplication";
$resultapp = mysqli_query($con, $queryapp);
$apply = mysqli_fetch_assoc($resultapp)['total'];

// Total Users
$queryusers = "SELECT COUNT(*) as total FROM tblusers WHERE ROLE = 'Applicant'";
$resultusers = mysqli_query($con, $queryusers);
$totalUsers = mysqli_fetch_assoc($resultusers)['total'];

// Total Companies
$querycompanies = "SELECT COUNT(*) as total FROM tblcompany";
$resultcompanies = mysqli_query($con, $querycompanies);
$totalCompanies = mysqli_fetch_assoc($resultcompanies)['total'];

// Pending Jobs
$querypending = "SELECT COUNT(*) as total FROM tbljob WHERE JOBSTATUS = 'Pending'";
$resultpending = mysqli_query($con, $querypending);
$pendingJobs = mysqli_fetch_assoc($resultpending)['total'];

// Pending Applications
$querypendingapp = "SELECT COUNT(*) as total FROM tbljobapplication WHERE APPLICATIONSTATUS = 'Pending'";
$resultpendingapp = mysqli_query($con, $querypendingapp);
$pendingApplications = mysqli_fetch_assoc($resultpendingapp)['total'];

// Pending Companies
$querypendingcomp = "SELECT COUNT(*) as total FROM tblcompany WHERE COMPANYSTATUS = 'Pending'";
$resultpendingcomp = mysqli_query($con, $querypendingcomp);
$pendingCompanies = mysqli_fetch_assoc($resultpendingcomp)['total'];

// Categories
$querycategories = "SELECT COUNT(*) as total FROM tbljobcategory";
$resultcategories = mysqli_query($con, $querycategories);
$totalCategories = mysqli_fetch_assoc($resultcategories)['total'];
?>

<head>
    <meta charset="utf-8" />
    <meta name="author" content="MUNext" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - MUNext Job Portal</title>

    <!-- Custom CSS -->
    <link href="assets/css/styles.css" rel="stylesheet">


    <link rel="stylesheet" href="assets/css/custom-dashboard.css">
    <link rel="stylesheet" href="assets/css/plugins/theme-stats.css">

</head>

<body>
    <!-- ==================== PRELOADER ==================== -->
    <!-- <div class="preloader"></div> -->

    <div id="main-wrapper">
        <!-- ==================== HEADER ==================== -->
        <?php include 'header.php' ?>
        <div class="clearfix"></div>

        <!-- ==================== DASHBOARD WRAPPER ==================== -->
        <div class="dashboard-wrap bg-light">
            <a class="mobNavigation" data-toggle="collapse" href="#MobNav" role="button" aria-expanded="false"
                aria-controls="MobNav">
                <i class="fas fa-bars mr-2"></i>Dashboard Navigation
            </a>

            <?php include 'sidenav.php' ?>

            <div class="dashboard-content">
                <!-- ==================== PAGE HEADER ==================== -->
                <div class="dashboard-tlbar d-block mb-5">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12">
                            <h1 class="ft-medium">Hello, <?php echo htmlspecialchars($FULLNAME); ?>! 👋</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item text-muted"><a href="index.php">Home</a></li>
                                    <li class="breadcrumb-item"><a href="#" class="theme-cl">Admin Dashboard</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="stats-grid row">
                    <!-- Active Jobs -->
                    <!-- col-xl-3 col-lg-3 col-md-6 col-sm-6 -->
                    <div class="stat-card success">
                        <div class="stat-card-content ">
                            <div class="stat-info">
                                <div class="stat-number"><?php echo number_format($jobs); ?></div>
                                <div class="stat-label">Active Jobs</div>
                            </div>
                            <?php if ($pendingJobs > 0): ?>
                            <span class="stat-badge bg-success text-light">
                                <?php echo $pendingJobs; ?> Pending
                            </span>
                            <?php endif; ?>
                            <div class="stat-icon">
                                <i class="lni lni-briefcase"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Applications -->
                    <div class="stat-card purple">
                        <div class="stat-card-content">
                            <div class="stat-info">
                                <div class="stat-number"><?php echo number_format($apply); ?></div>
                                <div class="stat-label">Job Applications</div>
                            </div>
                            <?php if ($pendingApplications > 0): ?>
                            <span class="stat-badge bg-purple text-light">
                                <?php echo $pendingApplications; ?> Pending
                            </span>
                            <?php endif; ?>
                            <div class="stat-icon">
                                <i class="lni lni-files"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Job seekers -->
                    <div class="stat-card danger">
                        <div class="stat-card-content">
                            <div class="stat-info">
                                <div class="stat-number"><?php echo number_format($totalUsers); ?></div>
                                <div class="stat-label">Job Seekers</div>
                            </div>
                            <div class="stat-icon">
                                <i class="lni lni-users"></i>
                            </div>
                        </div>
                    </div>


                    <!-- Companies -->
                    <div class="stat-card primary">
                        <div class="stat-card-content">
                            <div class="stat-info">
                                <div class="stat-number"><?php echo number_format($totalCompanies); ?></div>
                                <div class="stat-label">Companies</div>
                            </div>
                            <?php if ($pendingCompanies > 0): ?>
                            <span class="stat-badge text-light bg-blue">
                                <?php echo $pendingCompanies; ?> Pending
                            </span>
                            <?php endif; ?>
                            <div class="stat-icon">
                                <i class="fa fa-building"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Jobs -->
                    <!-- <div class="stat-card warning">
                        <div class="stat-card-content">
                            <div class="stat-info">
                                <div class="stat-number"><?php echo number_format($pendingJobs); ?></div>
                                <div class="stat-label">Pending Jobs</div>
                            </div>
                            <div class="stat-icon">
                                <i class="lni lni-alarm-clock"></i>
                            </div>
                        </div>
                    </div> -->
                </div>

                <!-- ==================== MAIN STATISTICS ==================== -->
                <div class="dashboard-widg-bar d-block">


                    <!-- ==================== SECONDARY STATISTICS ==================== -->
                    <div class="row stats-secondary">
                        <!-- Pending Jobs -->
                        <div class="stat-card warning col-xl-3 col-lg-3 col-md-6 col-sm-6">
                            <div class="stat-card-content">
                                <div class="stat-info">
                                    <div class="ft-medium mb-1 fs-xl text-muted">
                                        <?php echo number_format($pendingJobs); ?></div>
                                    <div class="p-0 m-0 text-muted fs-md">Pending Jobs</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="lni lni-alarm-clock"></i>
                                </div>
                            </div>
                        </div>
                        <!-- Pending Applications -->
                        <div class="stat-card info col-xl-3 col-lg-3 col-md-6 col-sm-6">
                            <div class="stat-card-content">
                                <div class="stat-info">
                                    <div class="ft-medium mb-1 fs-xl text-muted">
                                        <?php echo number_format($pendingApplications); ?></div>
                                    <div class="p-0 m-0 text-muted fs-md">Pending Applications</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="lni lni-hourglass"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Companies -->
                        <div class="stat-card dark col-xl-3 col-lg-3 col-md-6 col-sm-6">
                            <div class="stat-card-content">
                                <div class="stat-info">
                                    <div class="ft-medium mb-1 fs-xl text-muted">
                                        <?php echo number_format($pendingCompanies); ?></div>
                                    <div class="p-0 m-0 text-muted fs-md">Pending Companies</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="lni lni-checkmark"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Categories -->
                        <div class="stat-card secondary col-xl-3 col-lg-3 col-md-6 col-sm-6">
                            <div class="stat-card-content">
                                <div class="stat-info">
                                    <div class="ft-medium mb-1 fs-xl text-muted">
                                        <?php echo number_format($totalCategories); ?></div>
                                    <div class="p-0 m-0 text-muted fs-md">Job Categories</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="lni lni-layers"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== QUICK ACTIONS ==================== -->
                    <div class="quick-actions-section">
                        <h5 class="section-title">
                            <i class="lni lni-bolt mr-2"></i>Quick Actions
                        </h5>
                        <div class="quick-actions-grid mt-4">
                            <a href="admin-pending-jobs.php" class="quick-action-btn">
                                <div class="quick-action-icon">
                                    <i class="lni lni-alarm-clock"></i>
                                </div>
                                <div class="quick-action-text">
                                    Approve Jobs
                                    <?php if ($pendingJobs > 0): ?>
                                    <br><small style="color: #dc3545;">(<?php echo $pendingJobs; ?>)</small>
                                    <?php endif; ?>
                                </div>
                            </a>

                            <a href="admin-verify-companies.php" class="quick-action-btn">
                                <div class="quick-action-icon">
                                    <i class="lni lni-checkmark"></i>
                                </div>
                                <div class="quick-action-text">
                                    Verify Companies
                                    <?php if ($pendingCompanies > 0): ?>
                                    <br><small style="color: #dc3545;">(<?php echo $pendingCompanies; ?>)</small>
                                    <?php endif; ?>
                                </div>
                            </a>

                            <a href="admin-all-applications.php" class="quick-action-btn">
                                <div class="quick-action-icon">
                                    <i class="lni lni-files"></i>
                                </div>
                                <div class="quick-action-text">View Applications</div>
                            </a>

                            <a href="admin-job-category.php" class="quick-action-btn">
                                <div class="quick-action-icon">
                                    <i class="lni lni-layers"></i>
                                </div>
                                <div class="quick-action-text">Manage Categories</div>
                            </a>

                            <a href="admin-job-seekers.php" class="quick-action-btn">
                                <div class="quick-action-icon">
                                    <i class="lni lni-users"></i>
                                </div>
                                <div class="quick-action-text">Manage Users</div>
                            </a>

                            <a href="admin-activity-logs.php" class="quick-action-btn">
                                <div class="quick-action-icon">
                                    <i class="lni lni-list"></i>
                                </div>
                                <div class="quick-action-text">Activity Logs</div>
                            </a>

                            <a href="admin-analytics-dashboard.php" class="quick-action-btn">
                                <div class="quick-action-icon">
                                    <i class="lni lni-bar-chart"></i>
                                </div>
                                <div class="quick-action-text">View Reports</div>
                            </a>

                            <a href="admin-general-settings.php" class="quick-action-btn">
                                <div class="quick-action-icon">
                                    <i class="lni lni-cog"></i>
                                </div>
                                <div class="quick-action-text">Settings</div>
                            </a>
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

    <!-- Toast Rendering -->
    <?php echo Toast::render(); ?>
</body>

</html>