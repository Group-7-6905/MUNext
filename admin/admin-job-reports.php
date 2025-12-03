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
    header('Content-Disposition: attachment; filename="job_report_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Export based on report type
    if ($report_type == 'detailed') {
        fputcsv($output, ['Job ID', 'Title', 'Company', 'Category', 'Type', 'Location', 'Status', 'Applications', 'Views', 'Posted Date', 'Deadline']);
        
        $exportQuery = "SELECT j.*, c.COMPANYNAME, sbc.SUBCATEGORY 	
                        (SELECT COUNT(*) FROM tbljobapplication WHERE JOBID = j.JOBID) as application_count,
                        (SELECT COUNT(*) FROM tbljob_views WHERE JOBID = j.JOBID) as view_count
                        FROM tbljob j 
                        LEFT JOIN tblcompany c ON j.COMPANYID = c.COMPANYID 
                        LEFT JOIN tbljobsubcategory sbc ON j.JOBCATEGORYID = sbc.ID 
                        WHERE DATE(j.DATEPOSTED) BETWEEN ? AND ? 
                        ORDER BY j.DATEPOSTED DESC";
        $stmt = mysqli_prepare($con, $exportQuery);
        mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        while ($row = mysqli_fetch_assoc($result)) {
            fputcsv($output, [
                $row['JOBID'],
                $row['JOBTITLE'],
                $row['COMPANYNAME'],
                $row['SUBCATEGORY'] ?? 'N/A',
                $row['JOBTYPE'] ?? 'N/A',
                $row['COMPANYCITY'] ?? 'N/A',
                $row['JOBSTATUS'],
                $row['application_count'] ?? 0,
                $row['view_count'] ?? 0,
                date('Y-m-d', strtotime($row['DATEPOSTED'])),
                $row['JOBDEADLINE'] ?? 'N/A'
            ]);
        }
    } else {
        fputcsv($output, ['Metric', 'Value']);
        fputcsv($output, ['Total Jobs Posted', $totalJobsPosted ?? 0]);
        fputcsv($output, ['Active Jobs', $activeJobs ?? 0]);
        fputcsv($output, ['Pending Jobs', $pendingJobs ?? 0]);
        fputcsv($output, ['Closed Jobs', $closedJobs ?? 0]);
        fputcsv($output, ['Total Applications', $totalApplications ?? 0]);
        fputcsv($output, ['Average Applications per Job', $avgApplicationsPerJob ?? 0]);
    }
    
    fclose($output);
    exit();
}

// ==================== JOB STATISTICS ====================
// Total jobs posted in period
$totalJobsQuery = "SELECT COUNT(*) as total FROM tbljob WHERE DATE(DATEPOSTED) BETWEEN ? AND ?";
$stmt = mysqli_prepare($con, $totalJobsQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$totalJobsPosted = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

// Active jobs in period
$activeJobsQuery = "SELECT COUNT(*) as total FROM tbljob WHERE JOBSTATUS = 'Active' AND DATE(DATEPOSTED) BETWEEN ? AND ?";
$stmt = mysqli_prepare($con, $activeJobsQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$activeJobs = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

// Pending jobs in period
$pendingJobsQuery = "SELECT COUNT(*) as total FROM tbljob WHERE JOBSTATUS = 'Pending' AND DATE(DATEPOSTED) BETWEEN ? AND ?";
$stmt = mysqli_prepare($con, $pendingJobsQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$pendingJobs = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

// Closed jobs in period
$closedJobsQuery = "SELECT COUNT(*) as total FROM tbljob WHERE JOBSTATUS = 'Closed' AND DATE(DATEPOSTED) BETWEEN ? AND ?";
$stmt = mysqli_prepare($con, $closedJobsQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$closedJobs = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

// Suspended jobs
$suspendedJobsQuery = "SELECT COUNT(*) as total FROM tbljob WHERE JOBSTATUS = 'Suspended' AND DATE(DATEPOSTED) BETWEEN ? AND ?";
$stmt = mysqli_prepare($con, $suspendedJobsQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$suspendedJobs = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

// Total applications in period
$totalApplicationsQuery = "SELECT COUNT(*) as total 
                           FROM tbljobapplication ja
                           INNER JOIN tbljob j ON ja.JOBID = j.JOBID
                           WHERE DATE(j.DATEPOSTED) BETWEEN ? AND ?";
$stmt = mysqli_prepare($con, $totalApplicationsQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$totalApplications = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

// Average applications per job
$avgApplicationsPerJob = $totalJobsPosted > 0 ? $totalApplications / $totalJobsPosted : 0;

// ==================== JOB POSTING TREND ====================
$postingTrendQuery = "SELECT DATE(DATEPOSTED) as date, COUNT(*) as count 
                      FROM tbljob 
                      WHERE DATE(DATEPOSTED) BETWEEN ? AND ? 
                      GROUP BY DATE(DATEPOSTED) 
                      ORDER BY date ASC";
$stmt = mysqli_prepare($con, $postingTrendQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$postingTrendResult = mysqli_stmt_get_result($stmt);
$postingTrendData = [];
while ($row = mysqli_fetch_assoc($postingTrendResult)) {
    $postingTrendData[] = $row;
}

// ==================== JOBS BY CATEGORY ====================
$jobsByCategoryQuery = "SELECT JOBCATEGORYID, COUNT(*) as count 
                        FROM tbljob 
                        WHERE DATE(DATEPOSTED) BETWEEN ? AND ? 
                        AND JOBCATEGORYID IS NOT NULL 
                        GROUP BY JOBCATEGORYID 
                        ORDER BY count DESC 
                        LIMIT 10";
$stmt = mysqli_prepare($con, $jobsByCategoryQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$jobsByCategoryResult = mysqli_stmt_get_result($stmt);
$jobsByCategoryData = [];
while ($row = mysqli_fetch_assoc($jobsByCategoryResult)) {
    $jobsByCategoryData[$row['JOBCATEGORYID']] = $row['count'];
}

// ==================== JOBS BY EMPLOYMENT TYPE ====================
$jobsByTypeQuery = "SELECT JOBTYPE, COUNT(*) as count 
                    FROM tbljob 
                    WHERE DATE(DATEPOSTED) BETWEEN ? AND ? 
                    AND JOBTYPE IS NOT NULL 
                    GROUP BY JOBTYPE";
$stmt = mysqli_prepare($con, $jobsByTypeQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$jobsByTypeResult = mysqli_stmt_get_result($stmt);
$jobsByTypeData = [];
while ($row = mysqli_fetch_assoc($jobsByTypeResult)) {
    $jobsByTypeData[$row['JOBTYPE']] = $row['count'];
}

// ==================== JOB STATUS DISTRIBUTION ====================
$statusDistributionQuery = "SELECT JOBSTATUS, COUNT(*) as count 
                            FROM tbljob 
                            WHERE DATE(DATEPOSTED) BETWEEN ? AND ? 
                            GROUP BY JOBSTATUS";
$stmt = mysqli_prepare($con, $statusDistributionQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$statusDistributionResult = mysqli_stmt_get_result($stmt);
$statusDistributionData = [];
while ($row = mysqli_fetch_assoc($statusDistributionResult)) {
    $statusDistributionData[$row['JOBSTATUS']] = $row['count'];
}

// ==================== TOP COMPANIES BY JOBS ====================
$topCompaniesQuery = "SELECT c.COMPANYID, c.COMPANYNAME, c.COMPANYLOGO, COUNT(j.JOBID) as job_count 
                      FROM tblcompany c 
                      INNER JOIN tbljob j ON c.COMPANYID = j.COMPANYID 
                      WHERE DATE(j.DATEPOSTED) BETWEEN ? AND ?
                      GROUP BY c.COMPANYID 
                      ORDER BY job_count DESC 
                      LIMIT 10";
$stmt = mysqli_prepare($con, $topCompaniesQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$topCompaniesResult = mysqli_stmt_get_result($stmt);

// ==================== TOP JOBS BY APPLICATIONS ====================
$topJobsByAppsQuery = "SELECT j.JOBID, j.JOBTITLE, c.COMPANYNAME, 
                       COUNT(ja.ID) as app_count 
                       FROM tbljob j 
                       LEFT JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
                       LEFT JOIN tbljobapplication ja ON j.JOBID = ja.JOBID 
                       WHERE DATE(j.DATEPOSTED) BETWEEN ? AND ?
                       GROUP BY j.JOBID 
                       HAVING app_count > 0
                       ORDER BY app_count DESC 
                       LIMIT 10";
$stmt = mysqli_prepare($con, $topJobsByAppsQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$topJobsByAppsResult = mysqli_stmt_get_result($stmt);

// ==================== JOBS BY LOCATION ====================
$jobsByLocationQuery = "SELECT j.JOBID, c.COMPANYCITY, c.COMPANYID, COUNT(*) as count 
                        FROM tbljob j
                        LEFT JOIN tblcompany c ON j.COMPANYID = c.COMPANYID 
                        WHERE DATE(DATEPOSTED) BETWEEN ? AND ? 
                        AND COMPANYCITY IS NOT NULL 
                        GROUP BY COMPANYCITY 
                        ORDER BY count DESC 
                        LIMIT 10";
$stmt = mysqli_prepare($con, query: $jobsByLocationQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$jobsByLocationResult = mysqli_stmt_get_result($stmt);

// ==================== DETAILED JOB REPORT ====================
if ($report_type == 'detailed') {
    $detailedReportQuery = "SELECT j.*, c.COMPANYNAME, c.COMPANYLOGO,
                            (SELECT COUNT(*) FROM tbljobapplication WHERE JOBID = j.JOBID) as application_count
                            FROM tbljob j 
                            LEFT JOIN tblcompany c ON j.COMPANYID = c.COMPANYID 
                            WHERE DATE(j.DATEPOSTED) BETWEEN ? AND ? 
                            ORDER BY j.DATEPOSTED DESC 
                            LIMIT 100";
    $stmt = mysqli_prepare($con, $detailedReportQuery);
    mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
    mysqli_stmt_execute($stmt);
    $detailedReportResult = mysqli_stmt_get_result($stmt);
}

// ==================== APPLICATION RATE METRICS ====================
// Jobs with high application rate (>10 applications)
$highDemandJobsQuery = "SELECT COUNT(*) as count 
                        FROM (SELECT j.JOBID, COUNT(ja.ID) as app_count 
                              FROM tbljob j 
                              LEFT JOIN tbljobapplication ja ON j.JOBID = ja.JOBID 
                              WHERE DATE(j.DATEPOSTED) BETWEEN ? AND ?
                              GROUP BY j.JOBID 
                              HAVING app_count > 10) as subquery";
$stmt = mysqli_prepare($con, $highDemandJobsQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$highDemandJobs = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['count'];

// Jobs with no applications
$noApplicationJobsQuery = "SELECT COUNT(*) as count 
                           FROM tbljob j
                           LEFT JOIN tbljobapplication ja ON j.JOBID = ja.JOBID
                           WHERE DATE(j.DATEPOSTED) BETWEEN ? AND ?
                           GROUP BY j.JOBID
                           HAVING COUNT(ja.ID) = 0";
$stmt = mysqli_prepare($con, $noApplicationJobsQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$noApplicationJobs = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['count'] ?? 0;

// Calculate application rate
$applicationRate = $totalJobsPosted > 0 ? (($totalJobsPosted - $noApplicationJobs) / $totalJobsPosted) * 100 : 0;

// ==================== SYSTEM TOTALS ====================
$systemTotalJobs = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM tbljob"))['total'];
$systemActiveJobs = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM tbljob WHERE JOBSTATUS = 'Active'"))['total'];
$systemTotalApplications = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM tbljobapplication"))['total'];
?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Job Reports - Admin Dashboard | MUNext</title>
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
                            <i class="lni lni-briefcase mr-2"></i>Job Reports & Analytics
                        </h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="#">Reports</a></li>
                                <li class="breadcrumb-item active">Job Reports</li>
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
                                            Detailed Job List
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn-filter">
                                            <i class="lni lni-search mr-1"></i>Generate Report
                                        </button>
                                        <a href="admin-job-reports.php" class="btn-reset">Reset</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- System Overview -->
                    <div class="info-card">
                        <h5 class="text-white"><i class="lni lni-database mr-2"></i>System Overview (All Time)</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <p><strong><?php echo number_format($systemTotalJobs); ?></strong> Total Jobs Posted
                                </p>
                            </div>
                            <div class="col-md-4">
                                <p><strong><?php echo number_format($systemActiveJobs); ?></strong> Active Jobs</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong><?php echo number_format($systemTotalApplications); ?></strong> Total
                                    Applications</p>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="stats-grid">
                        <div class="stat-card total">
                            <i class="lni lni-briefcase stat-icon"></i>
                            <span class="stat-number"><?php echo number_format($totalJobsPosted); ?></span>
                            <span class="stat-label">Jobs Posted</span>
                        </div>
                        <div class="stat-card active">
                            <i class="lni lni-checkmark-circle stat-icon"></i>
                            <span class="stat-number"><?php echo number_format($activeJobs); ?></span>
                            <span class="stat-label">Active Jobs</span>
                        </div>
                        <div class="stat-card pending">
                            <i class="lni lni-hourglass stat-icon"></i>
                            <span class="stat-number"><?php echo number_format($pendingJobs); ?></span>
                            <span class="stat-label">Pending Approval</span>
                        </div>
                        <div class="stat-card closed">
                            <i class="lni lni-lock stat-icon"></i>
                            <span class="stat-number"><?php echo number_format($closedJobs); ?></span>
                            <span class="stat-label">Closed Jobs</span>
                        </div>
                        <div class="stat-card suspended">
                            <i class="lni lni-ban stat-icon"></i>
                            <span class="stat-number"><?php echo number_format($suspendedJobs); ?></span>
                            <span class="stat-label">Suspended</span>
                        </div>
                        <div class="stat-card applications">
                            <i class="lni lni-files stat-icon"></i>
                            <span class="stat-number"><?php echo number_format($totalApplications); ?></span>
                            <span class="stat-label">Applications Received</span>
                        </div>
                    </div>

                    <!-- Key Metrics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="metric-card">
                                <span
                                    class="metric-value"><?php echo number_format($avgApplicationsPerJob, 1); ?></span>
                                <span class="metric-label">Avg Applications per Job</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-card">
                                <span class="metric-value"><?php echo number_format($applicationRate, 1); ?>%</span>
                                <span class="metric-label">Jobs with Applications</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-card">
                                <span class="metric-value"><?php echo number_format($highDemandJobs); ?></span>
                                <span class="metric-label">High Demand Jobs</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-card">
                                <span class="metric-value"><?php echo number_format($noApplicationJobs); ?></span>
                                <span class="metric-label">Jobs Without Applications</span>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row 1 -->
                    <div class="row">
                        <!-- Job Posting Trend -->
                        <div class="col-md-8 mb-4">
                            <div class="chart-section">
                                <div class="chart-header">
                                    <div>
                                        <h5 class="chart-title">
                                            <i class="lni lni-stats-up mr-2"></i>Job Posting Trend
                                        </h5>
                                        <p class="chart-subtitle">Daily job postings</p>
                                    </div>
                                </div>
                                <div class="chart-container">
                                    <canvas id="postingTrendChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Status Distribution -->
                        <div class="col-md-4 mb-4">
                            <div class="chart-section">
                                <div class="chart-header">
                                    <div>
                                        <h5 class="chart-title">
                                            <i class="lni lni-pie-chart mr-2"></i>Job Status
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
                        <!-- Jobs by Category -->
                        <div class="col-md-6 mb-4">
                            <div class="chart-section">
                                <div class="chart-header">
                                    <div>
                                        <h5 class="chart-title">
                                            <i class="lni lni-layers mr-2"></i>Top Categories
                                        </h5>
                                        <p class="chart-subtitle">Jobs by category</p>
                                    </div>
                                </div>
                                <div class="chart-container">
                                    <canvas id="categoryChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Employment Type -->
                        <div class="col-md-6 mb-4">
                            <div class="chart-section">
                                <div class="chart-header">
                                    <div>
                                        <h5 class="chart-title">
                                            <i class="lni lni-pie-chart mr-2"></i>Employment Type
                                        </h5>
                                        <p class="chart-subtitle">Distribution by type</p>
                                    </div>
                                </div>
                                <div class="chart-container">
                                    <canvas id="typeChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tables Row -->
                    <div class="row">
                        <!-- Top Companies -->
                        <div class="col-md-6 mb-4">
                            <div class="report-table-card">
                                <div class="table-header theme-bg">
                                    <i class="fa fa-building mr-2"></i>Top Companies by Jobs
                                </div>
                                <div class="table-responsive">
                                    <table class="report-table">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>Company</th>
                                                <th>Jobs Posted</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $rank = 1;
                                            while ($company = mysqli_fetch_assoc($topCompaniesResult)): 
                                            ?>
                                            <tr>
                                                <td><span class="rank-badge">#<?php echo $rank++; ?></span></td>
                                                <td>
                                                    <div class="job-info">

                                                        <?php if (!empty($company['COMPANYLOGO'])): ?>
                                                        <img src="<?php echo $path.htmlspecialchars($company['COMPANYLOGO']); ?>"
                                                            alt="Logo" class="company-logo-small"
                                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        <?php endif; ?>
                                                        <div class="company-logo-placeholder"
                                                            style="<?php echo !empty($company['COMPANYLOGO']) ? 'display:none;' : ''; ?>">
                                                            <?php echo strtoupper(substr($company['COMPANYNAME'], 0, 1)); ?>
                                                        </div>

                                                        <span><?php echo htmlspecialchars($company['COMPANYNAME']); ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="count-badge">
                                                        <?php echo number_format($company['job_count']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                            <?php if (mysqli_num_rows($topCompaniesResult) == 0): ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">No data available</td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Top Jobs by Applications -->
                        <div class="col-md-6 mb-4">
                            <div class="report-table-card">
                                <div class="table-header theme-bg">
                                    <i class="lni lni-star mr-2"></i>Most Applied Jobs
                                </div>
                                <div class="table-responsive">
                                    <table class="report-table">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>Job Title</th>
                                                <th>Company</th>
                                                <th>Applications</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $rank = 1;
                                            while ($job = mysqli_fetch_assoc($topJobsByAppsResult)): 
                                            ?>
                                            <tr>
                                                <td><span class="rank-badge">#<?php echo $rank++; ?></span></td>
                                                <td><?php echo htmlspecialchars($job['JOBTITLE']); ?></td>
                                                <td><?php echo htmlspecialchars($job['COMPANYNAME']); ?></td>
                                                <td>
                                                    <span class="count-badge">
                                                        <?php echo number_format($job['app_count']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                            <?php if (mysqli_num_rows($topJobsByAppsResult) == 0): ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No data available</td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Locations -->
                    <div class="report-table-card">
                        <div class="table-header theme-bg">
                            <i class="lni lni-map-marker mr-2"></i>Top Job Locations
                            <span style="float: right; font-weight: normal;">
                                Most popular job locations
                            </span>
                        </div>
                        <div class="table-responsive">
                            <table class="report-table">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Location</th>
                                        <th>Job Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $rank = 1;
                                    while ($location = mysqli_fetch_assoc($jobsByLocationResult)): 
                                    ?>
                                    <tr>
                                        <td><span class="rank-badge">#<?php echo $rank++; ?></span></td>
                                        <td>
                                            <i class="lni lni-map-marker text-muted mr-2"></i>
                                            <?php echo htmlspecialchars($location['COMPANYCITY']); ?>
                                        </td>
                                        <td>
                                            <span class="count-badge">
                                                <?php echo number_format($location['count']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php if (mysqli_num_rows($jobsByLocationResult) == 0): ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No data available</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Detailed Job Report -->
                    <?php if ($report_type == 'detailed' && isset($detailedReportResult)): ?>
                    <div class="report-table-card">
                        <div class="table-header theme-bg">
                            <i class="lni lni-list mr-2"></i>Detailed Job Report
                            <span style="float: right; font-weight: normal;">
                                Showing up to 100 jobs
                            </span>
                        </div>
                        <div class="table-responsive">
                            <table class="report-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Job Details</th>
                                        <th>Category</th>
                                        <th>Type</th>
                                        <th>Location</th>
                                        <th>Applications</th>
                                        <th>Status</th>
                                        <th>Posted</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($job = mysqli_fetch_assoc($detailedReportResult)): ?>
                                    <tr>
                                        <td><?php echo $job['JOBID']; ?></td>
                                        <td>
                                            <div class="job-info">
                                                <?php if (!empty($job['COMPANYLOGO'])): ?>
                                                <img src="<?php echo $path.htmlspecialchars($job['COMPANYLOGO']); ?>"
                                                    alt="Logo" class="company-logo-small">
                                                <?php else: ?>
                                                <div class="company-logo-placeholder">
                                                    <?php echo strtoupper(substr($job['COMPANYNAME'], 0, 1)); ?>
                                                </div>
                                                <?php endif; ?>
                                                <div>
                                                    <div><?php echo htmlspecialchars($job['JOBTITLE']); ?></div>
                                                    <small
                                                        class="text-muted"><?php echo htmlspecialchars($job['COMPANYNAME']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="type-badge">
                                                <?php echo htmlspecialchars($job['CATEGORY'] ?? 'N/A'); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($job['JOBTYPE'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($job['COMPANYCITY'] ?? 'N/A'); ?></td>
                                        <td>
                                            <span class="count-badge">
                                                <?php echo $job['application_count']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = 'status-active';
                                            $status = $job['JOBSTATUS'];
                                            if ($status == 'Pending') {
                                                $statusClass = 'status-pending';
                                            } elseif ($status == 'Suspended') {
                                                $statusClass = 'status-suspended';
                                            } elseif ($status == 'Closed') {
                                                $statusClass = 'status-closed';
                                            }
                                            ?>
                                            <span class="status-badge <?php echo $statusClass; ?>">
                                                <?php echo htmlspecialchars($status); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($job['DATEPOSTED'])); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php if (mysqli_num_rows($detailedReportResult) == 0): ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No jobs found in this period</td>
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

    // ==================== JOB POSTING TREND CHART ====================
    const postingTrendCtx = document.getElementById('postingTrendChart').getContext('2d');
    new Chart(postingTrendCtx, {
        type: 'line',
        data: {
            labels: [<?php 
                foreach ($postingTrendData as $data) {
                    echo "'" . date('M d', strtotime($data['date'])) . "',";
                }
            ?>],
            datasets: [{
                label: 'Jobs Posted',
                data: [<?php 
                    foreach ($postingTrendData as $data) {
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
                    '#17a2b8',
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

    // ==================== CATEGORY CHART ====================
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: [<?php 
                foreach ($jobsByCategoryData as $category => $count) {
                    echo "'" . addslashes($category) . "',";
                }
            ?>],
            datasets: [{
                label: 'Number of Jobs',
                data: [<?php 
                    foreach ($jobsByCategoryData as $count) {
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

    // ==================== EMPLOYMENT TYPE CHART ====================
    const typeCtx = document.getElementById('typeChart').getContext('2d');
    new Chart(typeCtx, {
        type: 'doughnut',
        data: {
            labels: [<?php 
                foreach ($jobsByTypeData as $type => $count) {
                    echo "'" . $type . "',";
                }
            ?>],
            datasets: [{
                data: [<?php 
                    foreach ($jobsByTypeData as $count) {
                        echo $count . ',';
                    }
                ?>],
                backgroundColor: [
                    '#17a2b8',
                    '#dc3545',
                    '#ffc107',
                    '#6c757d',
                    '#28a745'
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
    </script>

    <?php echo Toast::render(); ?>
</body>

</html>