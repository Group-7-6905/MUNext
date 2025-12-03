<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';
require '../include/toast.php';

// ==================== DATE RANGE FILTER ====================
$date_from = isset($_GET['date_from']) ? mysqli_real_escape_string($con, $_GET['date_from']) : date('Y-m-01');
$date_to = isset($_GET['date_to']) ? mysqli_real_escape_string($con, $_GET['date_to']) : date('Y-m-d');
$report_type = isset($_GET['report_type']) ? mysqli_real_escape_string($con, $_GET['report_type']) : 'overview';

// ==================== EXPORT FUNCTIONALITY ====================
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="user_report_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Export based on report type
    if ($report_type == 'detailed') {
        fputcsv($output, ['User ID', 'Name', 'Email', 'Username', 'Status', 'City', 'Country', 'Education', 'Skills', 'Registration Date', 'Last Login']);
        
        $exportQuery = "SELECT u.*, a.CITY, a.COUNTRY, a.DEGREE, a.SKILLS, a.FNAME, a.OTHERNAMES 
                        FROM tblusers u 
                        LEFT JOIN tblapplicants a ON u.USERID = a.USERID 
                        WHERE u.ROLE = 'Applicant' AND DATE(u.DATECREATED) BETWEEN ? AND ? 
                        ORDER BY u.DATECREATED DESC";
        $stmt = mysqli_prepare($con, $exportQuery);
        mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        while ($row = mysqli_fetch_assoc($result)) {
            $name = !empty($row['FNAME']) ? $row['FNAME'] . ' ' . $row['OTHERNAMES'] : $row['FNAME'] . ' ' . $row['ONAME'];
            fputcsv($output, [
                $row['USERID'],
                $name,
                $row['EMAIL'],
                $row['USERNAME'],
                $row['USERSTATUS'],
                $row['CITY'] ?? 'N/A',
                $row['COUNTRY'] ?? 'N/A',
                $row['DEGREE'] ?? 'N/A',
                $row['SKILLS'] ?? 'N/A',
                date('Y-m-d', strtotime($row['DATECREATED'])),
                $row['LASTLOGIN'] ?? 'Never'
            ]);
        }
    } else {
        fputcsv($output, ['Metric', 'Value']);
        fputcsv($output, ['Total New Users', $totalNewUsers ?? 0]);
        fputcsv($output, ['Active Users', $activeUsers ?? 0]);
        fputcsv($output, ['Pending Users', $pendingUsers ?? 0]);
        fputcsv($output, ['Suspended Users', $suspendedUsers ?? 0]);
    }
    
    fclose($output);
    exit();
}

// ==================== USER STATISTICS ====================
// Total new users in period
$totalUsersQuery = "SELECT COUNT(*) as total FROM tblusers WHERE ROLE = 'Applicant' AND DATE(DATECREATED) BETWEEN ? AND ?";
$stmt = mysqli_prepare($con, $totalUsersQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$totalNewUsers = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

// Active users in period
$activeUsersQuery = "SELECT COUNT(*) as total FROM tblusers WHERE ROLE = 'Applicant' AND USERSTATUS = 'Active' AND DATE(DATECREATED) BETWEEN ? AND ?";
$stmt = mysqli_prepare($con, $activeUsersQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$activeUsers = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

// Pending users in period
$pendingUsersQuery = "SELECT COUNT(*) as total FROM tblusers WHERE ROLE = 'Applicant' AND USERSTATUS = 'Pending' AND DATE(DATECREATED) BETWEEN ? AND ?";
$stmt = mysqli_prepare($con, $pendingUsersQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$pendingUsers = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

// Suspended users in period
$suspendedUsersQuery = "SELECT COUNT(*) as total FROM tblusers WHERE ROLE = 'Applicant' AND USERSTATUS = 'Suspended' AND DATE(DATECREATED) BETWEEN ? AND ?";
$stmt = mysqli_prepare($con, $suspendedUsersQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$suspendedUsers = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

// Users with complete profiles
$completeProfilesQuery = "SELECT COUNT(*) as total 
                          FROM tblusers u 
                          INNER JOIN tblapplicants a ON u.USERID = a.USERID 
                          WHERE u.ROLE = 'Applicant' 
                          AND a.APPLICANTPHOTO IS NOT NULL 
                          AND a.SKILLS IS NOT NULL
                          AND DATE(u.DATECREATED) BETWEEN ? AND ?";
$stmt = mysqli_prepare($con, $completeProfilesQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$completeProfiles = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

// Users with incomplete profiles
$incompleteProfiles = $totalNewUsers - $completeProfiles;

// ==================== USER GROWTH TREND ====================
$growthTrendQuery = "SELECT DATE(DATECREATED) as date, COUNT(*) as count 
                     FROM tblusers 
                     WHERE ROLE = 'Applicant' AND DATE(DATECREATED) BETWEEN ? AND ? 
                     GROUP BY DATE(DATECREATED) 
                     ORDER BY date ASC";
$stmt = mysqli_prepare($con, $growthTrendQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$growthTrendResult = mysqli_stmt_get_result($stmt);
$growthTrendData = [];
while ($row = mysqli_fetch_assoc($growthTrendResult)) {
    $growthTrendData[] = $row;
}

// ==================== USERS BY LOCATION ====================
$usersByLocationQuery = "SELECT a.COUNTRY, COUNT(*) as count 
                         FROM tblusers u 
                         INNER JOIN tblapplicants a ON u.USERID = a.USERID 
                         WHERE u.ROLE = 'Applicant' 
                         AND a.COUNTRY IS NOT NULL 
                         AND DATE(u.DATECREATED) BETWEEN ? AND ? 
                         GROUP BY a.COUNTRY 
                         ORDER BY count DESC 
                         LIMIT 10";
$stmt = mysqli_prepare($con, $usersByLocationQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$usersByLocationResult = mysqli_stmt_get_result($stmt);

// ==================== USERS BY EDUCATION LEVEL ====================
$usersByEducationQuery = "SELECT a.DEGREE, COUNT(*) as count 
                          FROM tblusers u 
                          INNER JOIN tblapplicants a ON u.USERID = a.USERID 
                          WHERE u.ROLE = 'Applicant' 
                          AND a.DEGREE IS NOT NULL 
                          AND DATE(u.DATECREATED) BETWEEN ? AND ? 
                          GROUP BY a.DEGREE 
                          ORDER BY count DESC";
$stmt = mysqli_prepare($con, $usersByEducationQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$usersByEducationResult = mysqli_stmt_get_result($stmt);
$usersByEducationData = [];
while ($row = mysqli_fetch_assoc($usersByEducationResult)) {
    $usersByEducationData[$row['DEGREE']] = $row['count'];
}

// ==================== USER STATUS DISTRIBUTION ====================
$statusDistributionQuery = "SELECT USERSTATUS, COUNT(*) as count 
                            FROM tblusers 
                            WHERE ROLE = 'Applicant' 
                            AND DATE(DATECREATED) BETWEEN ? AND ? 
                            GROUP BY USERSTATUS";
$stmt = mysqli_prepare($con, $statusDistributionQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$statusDistributionResult = mysqli_stmt_get_result($stmt);
$statusDistributionData = [];
while ($row = mysqli_fetch_assoc($statusDistributionResult)) {
    $statusDistributionData[$row['USERSTATUS']] = $row['count'];
}

// ==================== DETAILED USER REPORT ====================
if ($report_type == 'detailed') {
    $detailedReportQuery = "SELECT u.*, 
                            a.APPLICANTID, a.CITY, a.COUNTRY, a.DEGREE, a.SKILLS, 
                            a.APPLICANTPHOTO, a.FNAME, a.OTHERNAMES,
                            (SELECT COUNT(*) FROM tbljobapplication WHERE USERID = u.USERID) as application_count
                            FROM tblusers u 
                            LEFT JOIN tblapplicants a ON u.USERID = a.USERID 
                            WHERE u.ROLE = 'Applicant' 
                            AND DATE(u.DATECREATED) BETWEEN ? AND ? 
                            ORDER BY u.DATECREATED DESC 
                            LIMIT 100";
    $stmt = mysqli_prepare($con, $detailedReportQuery);
    mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
    mysqli_stmt_execute($stmt);
    $detailedReportResult = mysqli_stmt_get_result($stmt);
}

// ==================== TOP ACTIVE USERS ====================
$topActiveUsersQuery = "SELECT u.USERID, u.FNAME, u.ONAME, u.EMAIL, 
                        a.FNAME, a.OTHERNAMES, a.APPLICANTPHOTO,
                        COUNT(ja.ID) as app_count 
                        FROM tblusers u 
                        LEFT JOIN tblapplicants a ON u.USERID = a.USERID
                        LEFT JOIN tbljobapplication ja ON u.USERID = ja.APPLICANTID 
                        WHERE u.ROLE = 'Applicant' 
                        AND DATE(u.DATECREATED) BETWEEN ? AND ?
                        GROUP BY u.USERID 
                        HAVING app_count > 0
                        ORDER BY app_count DESC 
                        LIMIT 10";
$stmt = mysqli_prepare($con, $topActiveUsersQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$topActiveUsersResult = mysqli_stmt_get_result($stmt);

// ==================== ENGAGEMENT METRICS ====================
// Users who have applied for jobs
$engagedUsersQuery = "SELECT COUNT(DISTINCT u.USERID) as count 
                      FROM tblusers u 
                      INNER JOIN tbljobapplication ja ON u.USERID = ja.APPLICANTID 
                      WHERE u.ROLE = 'Applicant' 
                      AND DATE(u.DATECREATED) BETWEEN ? AND ?";
$stmt = mysqli_prepare($con, $engagedUsersQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$engagedUsers = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'] ?? 0;

// Calculate engagement rate
$engagementRate = $totalNewUsers > 0 ? ($engagedUsers / $totalNewUsers) * 100 : 0;

// Profile completion rate
$profileCompletionRate = $totalNewUsers > 0 ? ($completeProfiles / $totalNewUsers) * 100 : 0;

// ==================== SYSTEM TOTALS ====================
$systemTotalUsers = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM tblusers WHERE ROLE = 'Applicant'"))['total'];
$systemActiveUsers = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM tblusers WHERE ROLE = 'Applicant' AND USERSTATUS = 'Active'"))['total'];
?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Reports - Admin Dashboard | MUNext</title>
    <link href="assets/css/styles.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

    <link rel="stylesheet" href="assets/css/custom-style.css">

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
                            <i class="lni lni-users mr-2"></i>User Reports & Analytics
                        </h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="#">Reports</a></li>
                                <li class="breadcrumb-item active">User Reports</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['export' => 'csv'])); ?>"
                            class="btn-export">
                            <i class="lni lni-download"></i>
                            Export to CSV
                        </a>
                    </div>
                </div>

                <div class="container-fluid">
                    <!-- Filters -->
                    <div class="filter-section">
                        <div class="filter-header">
                            <h6 class="filter-title">
                                <i class="lni lni-funnel mr-2"></i>Report Filters
                            </h6>
                        </div>

                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">From Date</label>
                                    <input type="date" name="date_from" class="form-control"
                                        value="<?php echo htmlspecialchars($date_from); ?>" required>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">To Date</label>
                                    <input type="date" name="date_to" class="form-control"
                                        value="<?php echo htmlspecialchars($date_to); ?>" required>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Report Type</label>
                                    <select name="report_type" class="form-control">
                                        <option value="overview"
                                            <?php echo $report_type === 'overview' ? 'selected' : ''; ?>>
                                            Overview Summary
                                        </option>
                                        <option value="detailed"
                                            <?php echo $report_type === 'detailed' ? 'selected' : ''; ?>>
                                            Detailed User List
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn-filter">
                                            <i class="lni lni-search mr-1"></i>Generate Report
                                        </button>
                                        <a href="admin-user-reports.php" class="btn-reset">Reset</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- System Overview -->
                    <div class="info-card">
                        <h5 class="text-white"><i class="lni lni-database mr-2"></i>System Overview (All Time)</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong><?php echo number_format($systemTotalUsers); ?></strong> Total Registered
                                    Users</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong><?php echo number_format($systemActiveUsers); ?></strong> Active Users</p>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="stats-grid">
                        <div class="stat-card total">
                            <i class="lni lni-users stat-icon"></i>
                            <span class="stat-number"><?php echo number_format($totalNewUsers); ?></span>
                            <span class="stat-label">New Users</span>
                        </div>
                        <div class="stat-card active">
                            <i class="lni lni-checkmark-circle stat-icon"></i>
                            <span class="stat-number"><?php echo number_format($activeUsers); ?></span>
                            <span class="stat-label">Active Users</span>
                        </div>
                        <div class="stat-card pending">
                            <i class="lni lni-hourglass stat-icon"></i>
                            <span class="stat-number"><?php echo number_format($pendingUsers); ?></span>
                            <span class="stat-label">Pending Approval</span>
                        </div>
                        <div class="stat-card suspended">
                            <i class="lni lni-ban stat-icon"></i>
                            <span class="stat-number"><?php echo number_format($suspendedUsers); ?></span>
                            <span class="stat-label">Suspended</span>
                        </div>
                        <div class="stat-card complete">
                            <i class="lni lni-checkmark stat-icon"></i>
                            <span class="stat-number"><?php echo number_format($completeProfiles); ?></span>
                            <span class="stat-label">Complete Profiles</span>
                        </div>
                        <div class="stat-card incomplete">
                            <i class="lni lni-close stat-icon"></i>
                            <span class="stat-number"><?php echo number_format($incompleteProfiles); ?></span>
                            <span class="stat-label">Incomplete Profiles</span>
                        </div>
                    </div>

                    <!-- Key Metrics -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="metric-card">
                                <span
                                    class="metric-value"><?php echo number_format($profileCompletionRate, 1); ?>%</span>
                                <span class="metric-label">Profile Completion Rate</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="metric-card">
                                <span class="metric-value"><?php echo number_format($engagementRate, 1); ?>%</span>
                                <span class="metric-label">User Engagement Rate</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="metric-card">
                                <span class="metric-value"><?php echo number_format($engagedUsers); ?></span>
                                <span class="metric-label">Active Job Seekers</span>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row 1 -->
                    <div class="row">
                        <!-- User Growth Trend -->
                        <div class="col-md-8 mb-4">
                            <div class="chart-section">
                                <div class="chart-header">
                                    <div>
                                        <h5 class="chart-title">
                                            <i class="lni lni-stats-up mr-2"></i>User Registration Trend
                                        </h5>
                                        <p class="chart-subtitle">Daily new user registrations</p>
                                    </div>
                                </div>
                                <div class="chart-container">
                                    <canvas id="userGrowthChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Status Distribution -->
                        <div class="col-md-4 mb-4">
                            <div class="chart-section">
                                <div class="chart-header">
                                    <div>
                                        <h5 class="chart-title">
                                            <i class="lni lni-pie-chart mr-2"></i>User Status
                                        </h5>
                                        <p class="chart-subtitle">Distribution by status</p>
                                    </div>
                                </div>
                                <div class="chart-container-small">
                                    <canvas id="statusChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row 2 -->
                    <div class="row">
                        <!-- Education Level Distribution -->
                        <div class="col-md-6 mb-4">
                            <div class="chart-section">
                                <div class="chart-header">
                                    <div>
                                        <h5 class="chart-title">
                                            <i class="lni lni-graduation mr-2"></i>Education Levels
                                        </h5>
                                        <p class="chart-subtitle">Users by education qualification</p>
                                    </div>
                                </div>
                                <div class="chart-container">
                                    <canvas id="educationChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Top Locations -->
                        <div class="col-md-6 mb-4">
                            <div class="chart-section">
                                <div class="chart-header">
                                    <div>
                                        <h5 class="chart-title">
                                            <i class="lni lni-map-marker mr-2"></i>Top Locations
                                        </h5>
                                        <p class="chart-subtitle">Users by country</p>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="report-table">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>Country</th>
                                                <th>User Count</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $rank = 1;
                                            while ($location = mysqli_fetch_assoc($usersByLocationResult)): 
                                            ?>
                                            <tr>
                                                <td><span class="rank-badge">#<?php echo $rank++; ?></span></td>
                                                <td><?php echo htmlspecialchars($location['COUNTRY']); ?></td>
                                                <td>
                                                    <span class="count-badge">
                                                        <?php echo number_format($location['count']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                            <?php if (mysqli_num_rows($usersByLocationResult) == 0): ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">No data available</td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Active Users -->
                    <div class="report-table-card">
                        <div class="table-header theme-bg">
                            <i class="lni lni-star mr-2"></i>Most Active Users
                            <span style="float: right; font-weight: normal;">
                                Based on application activity
                            </span>
                        </div>
                        <div class="table-responsive">
                            <table class="report-table">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>User</th>
                                        <th>Email</th>
                                        <th>Applications</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $rank = 1;
                                    while ($user = mysqli_fetch_assoc($topActiveUsersResult)): 
                                        $displayName = !empty($user['FNAME']) 
                                            ? $user['FNAME'] . ' ' . $user['OTHERNAMES']
                                            : $user['FNAME'] . ' ' . $user['ONAME'];
                                    ?>
                                    <tr>
                                        <td><span class="rank-badge">#<?php echo $rank++; ?></span></td>
                                        <td>
                                            <div class="user-info">

                                                <?php if (!empty($user['APPLICANTPHOTO'])): ?>
                                                <img src="<?php echo $path.htmlspecialchars($user['APPLICANTPHOTO']); ?>"
                                                    alt="Avatar" class="user-avatar-small"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <?php endif; ?>
                                                <div class="user-avatar-placeholder"
                                                    style="<?php echo !empty($user['APPLICANTPHOTO']) ? 'display:none;' : ''; ?>">
                                                    <?php echo strtoupper(substr($displayName, 0, 1)); ?>
                                                </div>




                                                <span><?php echo htmlspecialchars($displayName); ?></span>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($user['EMAIL']); ?></td>
                                        <td>
                                            <span class="count-badge">
                                                <?php echo number_format($user['app_count']); ?> applications
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php if (mysqli_num_rows($topActiveUsersResult) == 0): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No active users in this period
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Detailed User Report -->
                    <?php if ($report_type == 'detailed' && isset($detailedReportResult)): ?>
                    <div class="report-table-card">
                        <div class="table-header">
                            <i class="lni lni-list mr-2"></i>Detailed User Report
                            <span style="float: right; font-weight: normal;">
                                Showing up to 100 users
                            </span>
                        </div>
                        <div class="table-responsive">
                            <table class="report-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Location</th>
                                        <th>Education</th>
                                        <th>Profile</th>
                                        <th>Applications</th>
                                        <th>Status</th>
                                        <th>Registered</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($user = mysqli_fetch_assoc($detailedReportResult)): 
                                        $displayName = !empty($user['FNAME']) 
                                            ? $user['FNAME'] . ' ' . $user['OTHERNAMES']
                                            : $user['FNAME'] . ' ' . $user['ONAME'];
                                    ?>
                                    <tr>
                                        <td><?php echo $user['USERID']; ?></td>
                                        <td>
                                            <div class="user-info">
                                                <?php if (!empty($user['APPLICANTPHOTO'])): ?>
                                                <img src="<?php echo $path.htmlspecialchars($user['APPLICANTPHOTO']); ?>"
                                                    alt="Avatar" class="user-avatar-small">
                                                <?php else: ?>
                                                <div class="user-avatar-placeholder">
                                                    <?php echo strtoupper(substr($displayName, 0, 1)); ?>
                                                </div>
                                                <?php endif; ?>
                                                <div>
                                                    <div><?php echo htmlspecialchars($displayName); ?></div>
                                                    <small
                                                        class="text-muted"><?php echo htmlspecialchars($user['EMAIL']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php 
                                            if (!empty($user['CITY']) || !empty($user['COUNTRY'])) {
                                                $location = array_filter([$user['CITY'], $user['COUNTRY']]);
                                                echo htmlspecialchars(implode(', ', $location));
                                            } else {
                                                echo '<span class="text-muted">N/A</span>';
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
                                            <span class="count-badge">
                                                <?php echo $user['application_count']; ?>
                                            </span>
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
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php if (mysqli_num_rows($detailedReportResult) == 0): ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No users found in this period
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <?php include 'footer.php' ?>
            </div>
        </div>

        <a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>
    </div>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/custom.js"></script>

    <script>
    // Chart.js Configuration
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#6c757d';

    // ==================== USER GROWTH CHART ====================
    const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
    new Chart(userGrowthCtx, {
        type: 'line',
        data: {
            labels: [<?php 
                foreach ($growthTrendData as $data) {
                    echo "'" . date('M d', strtotime($data['date'])) . "',";
                }
            ?>],
            datasets: [{
                label: 'New Users',
                data: [<?php 
                    foreach ($growthTrendData as $data) {
                        echo $data['count'] . ',';
                    }
                ?>],
                borderColor: '#862633',
                backgroundColor: 'rgba(134, 38, 51, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });

    // ==================== STATUS DISTRIBUTION CHART ====================
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: [<?php 
                foreach ($statusDistributionData as $status => $count) {
                    echo "'" . $status . "',";
                }
            ?>],
            datasets: [{
                data: [<?php 
                    foreach ($statusDistributionData as $count) {
                        echo $count . ',';
                    }
                ?>],
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#dc3545',
                    '#6c757d'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // ==================== EDUCATION CHART ====================
    const educationCtx = document.getElementById('educationChart').getContext('2d');
    new Chart(educationCtx, {
        type: 'bar',
        data: {
            labels: [<?php 
                foreach ($usersByEducationData as $degree => $count) {
                    echo "'" . addslashes($degree) . "',";
                }
            ?>],
            datasets: [{
                label: 'Number of Users',
                data: [<?php 
                    foreach ($usersByEducationData as $count) {
                        echo $count . ',';
                    }
                ?>],
                backgroundColor: '#862633'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
    </script>

    <?php echo Toast::render(); ?>
</body>

</html>