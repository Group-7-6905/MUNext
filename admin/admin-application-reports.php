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
    header('Content-Disposition: attachment; filename="application_report_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Export based on report type
    if ($report_type == 'detailed') {
        fputcsv($output, ['Application ID', 'Job Title', 'Company', 'Applicant Name', 'Applicant Email', 'Status', 'Application Date', 'Resume']);
        
        $exportQuery = "SELECT ja.*, j.JOBTITLE, c.COMPANYNAME, u.FNAME, u.ONAME, u.EMAIL
                        FROM tbljobapplication ja
                        INNER JOIN tbljob j ON ja.JOBID = j.JOBID
                        LEFT JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
                        INNER JOIN tblusers u ON ja.APPLICANTID = u.USERID
                        WHERE DATE(ja.DATEAPPLIED) BETWEEN ? AND ? 
                        ORDER BY ja.DATEAPPLIED DESC";
        $stmt = mysqli_prepare($con, $exportQuery);
        mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        while ($row = mysqli_fetch_assoc($result)) {
            fputcsv($output, [
                $row['ID'],
                $row['JOBTITLE'],
                $row['COMPANYNAME'],
                $row['FNAME'] . ' ' . $row['ONAME'],
                $row['EMAIL'],
                $row['APPLICATIONSTATUS'],
                date('Y-m-d', strtotime($row['DATEAPPLIED'])),
                $row['FILEDIR'] ?? 'N/A'
            ]);
        }
    } else {
        fputcsv($output, ['Metric', 'Value']);
        fputcsv($output, ['Total Applications', $totalApplications ?? 0]);
        fputcsv($output, ['Pending Applications', $pendingApplications ?? 0]);
        fputcsv($output, ['Approved Applications', $approvedApplications ?? 0]);
        fputcsv($output, ['Rejected Applications', $rejectedApplications ?? 0]);
    }
    
    fclose($output);
    exit();
}

// ==================== APPLICATION STATISTICS ====================
// Total applications in period
$totalApplicationsQuery = "SELECT COUNT(*) as total FROM tbljobapplication WHERE DATE(DATEAPPLIED) BETWEEN ? AND ?";
$stmt = mysqli_prepare($con, $totalApplicationsQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$totalApplications = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

// Pending applications
$pendingApplicationsQuery = "SELECT COUNT(*) as total FROM tbljobapplication WHERE APPLICATIONSTATUS = 'Pending' AND DATE(DATEAPPLIED) BETWEEN ? AND ?";
$stmt = mysqli_prepare($con, $pendingApplicationsQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$pendingApplications = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

// Approved applications
$approvedApplicationsQuery = "SELECT COUNT(*) as total FROM tbljobapplication WHERE APPLICATIONSTATUS = 'Approved' AND DATE(DATEAPPLIED) BETWEEN ? AND ?";
$stmt = mysqli_prepare($con, $approvedApplicationsQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$approvedApplications = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

// Rejected applications
$rejectedApplicationsQuery = "SELECT COUNT(*) as total FROM tbljobapplication WHERE APPLICATIONSTATUS = 'Rejected' AND DATE(DATEAPPLIED) BETWEEN ? AND ?";
$stmt = mysqli_prepare($con, $rejectedApplicationsQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$rejectedApplications = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

// Shortlisted applications
$shortlistedApplicationsQuery = "SELECT COUNT(*) as total FROM tbljobapplication WHERE APPLICATIONSTATUS = 'Shortlisted' AND DATE(DATEAPPLIED) BETWEEN ? AND ?";
$stmt = mysqli_prepare($con, $shortlistedApplicationsQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$shortlistedApplications = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

// Interviewed applications
$interviewedApplicationsQuery = "SELECT COUNT(*) as total FROM tbljobapplication WHERE APPLICATIONSTATUS = 'Interviewed' AND DATE(DATEAPPLIED) BETWEEN ? AND ?";
$stmt = mysqli_prepare($con, $interviewedApplicationsQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$interviewedApplications = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

// Average applications per day
$avgApplicationsPerDay = count($applicationTrendData ?? []) > 0 ? $totalApplications / count($applicationTrendData) : 0;

// ==================== APPLICATION TREND ====================
$applicationTrendQuery = "SELECT DATE(DATEAPPLIED) as date, COUNT(*) as count 
                          FROM tbljobapplication 
                          WHERE DATE(DATEAPPLIED) BETWEEN ? AND ? 
                          GROUP BY DATE(DATEAPPLIED) 
                          ORDER BY date ASC";
$stmt = mysqli_prepare($con, $applicationTrendQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$applicationTrendResult = mysqli_stmt_get_result($stmt);
$applicationTrendData = [];
while ($row = mysqli_fetch_assoc($applicationTrendResult)) {
    $applicationTrendData[] = $row;
}

// Recalculate average
$avgApplicationsPerDay = count($applicationTrendData) > 0 ? $totalApplications / count($applicationTrendData) : 0;

// ==================== STATUS DISTRIBUTION ====================
$statusDistributionQuery = "SELECT APPLICATIONSTATUS, COUNT(*) as count 
                            FROM tbljobapplication 
                            WHERE DATE(DATEAPPLIED) BETWEEN ? AND ? 
                            GROUP BY APPLICATIONSTATUS";
$stmt = mysqli_prepare($con, $statusDistributionQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$statusDistributionResult = mysqli_stmt_get_result($stmt);
$statusDistributionData = [];
while ($row = mysqli_fetch_assoc($statusDistributionResult)) {
    $statusDistributionData[$row['APPLICATIONSTATUS']] = $row['count'];
}

// ==================== TOP JOBS BY APPLICATIONS ====================
$topJobsQuery = "SELECT j.JOBID, j.JOBTITLE, c.COMPANYNAME, COUNT(ja.ID) as app_count 
                 FROM tbljobapplication ja
                 INNER JOIN tbljob j ON ja.JOBID = j.JOBID
                 LEFT JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
                 WHERE DATE(ja.DATEAPPLIED) BETWEEN ? AND ?
                 GROUP BY j.JOBID 
                 ORDER BY app_count DESC 
                 LIMIT 10";
$stmt = mysqli_prepare($con, $topJobsQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$topJobsResult = mysqli_stmt_get_result($stmt);

// ==================== TOP APPLICANTS ====================
$topApplicantsQuery = "SELECT u.USERID, u.FNAME, u.ONAME, u.EMAIL, 
                       a.APPLICANTPHOTO, COUNT(ja.ID) as app_count 
                       FROM tbljobapplication ja
                       INNER JOIN tblusers u ON ja.APPLICANTID = u.USERID
                       LEFT JOIN tblapplicants a ON u.USERID = a.USERID
                       WHERE DATE(ja.DATEAPPLIED) BETWEEN ? AND ?
                       GROUP BY u.USERID 
                       ORDER BY app_count DESC 
                       LIMIT 10";
$stmt = mysqli_prepare($con, $topApplicantsQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$topApplicantsResult = mysqli_stmt_get_result($stmt);

// ==================== TOP COMPANIES BY APPLICATIONS ====================
$topCompaniesQuery = "SELECT c.COMPANYID, c.COMPANYNAME, c.COMPANYLOGO, COUNT(ja.ID) as app_count 
                      FROM tbljobapplication ja
                      INNER JOIN tbljob j ON ja.JOBID = j.JOBID
                      INNER JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
                      WHERE DATE(ja.DATEAPPLIED) BETWEEN ? AND ?
                      GROUP BY c.COMPANYID 
                      ORDER BY app_count DESC 
                      LIMIT 10";
$stmt = mysqli_prepare($con, $topCompaniesQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$topCompaniesResult = mysqli_stmt_get_result($stmt);

// ==================== DETAILED APPLICATION REPORT ====================
if ($report_type == 'detailed') {
    $detailedReportQuery = "SELECT ja.*, j.JOBTITLE, c.COMPANYNAME, c.COMPANYLOGO, 
                            u.FNAME, u.ONAME, u.EMAIL, a.APPLICANTPHOTO
                            FROM tbljobapplication ja
                            INNER JOIN tbljob j ON ja.JOBID = j.JOBID
                            LEFT JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
                            INNER JOIN tblusers u ON ja.APPLICANTID = u.USERID
                            LEFT JOIN tblapplicants a ON u.USERID = a.USERID
                            WHERE DATE(ja.DATEAPPLIED) BETWEEN ? AND ? 
                            ORDER BY ja.DATEAPPLIED DESC 
                            LIMIT 100";
    $stmt = mysqli_prepare($con, $detailedReportQuery);
    mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
    mysqli_stmt_execute($stmt);
    $detailedReportResult = mysqli_stmt_get_result($stmt);
}

// ==================== APPLICATION METRICS ====================
// Success rate (approved + shortlisted + interviewed)
$successCount = $approvedApplications + $shortlistedApplications + $interviewedApplications;
$successRate = $totalApplications > 0 ? ($successCount / $totalApplications) * 100 : 0;

// Response rate (non-pending applications)
$respondedCount = $totalApplications - $pendingApplications;
$responseRate = $totalApplications > 0 ? ($respondedCount / $totalApplications) * 100 : 0;

// Rejection rate
$rejectionRate = $totalApplications > 0 ? ($rejectedApplications / $totalApplications) * 100 : 0;

// ==================== SYSTEM TOTALS ====================
$systemTotalApplications = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM tbljobapplication"))['total'];
$systemPendingApplications = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM tbljobapplication WHERE APPLICATIONSTATUS = 'Pending'"))['total'];
$systemApprovedApplications = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM tbljobapplication WHERE APPLICATIONSTATUS = 'Approved'"))['total'];
?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Application Reports - Admin Dashboard | MUNext</title>
    <link href="assets/css/styles.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

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
                            <i class="lni lni-files mr-2"></i>Application Reports & Analytics
                        </h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="#">Reports</a></li>
                                <li class="breadcrumb-item active">Application Reports</li>
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
                                            Detailed Application List
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn-filter">
                                            <i class="lni lni-search mr-1"></i>Generate Report
                                        </button>
                                        <a href="admin-application-reports.php" class="btn-reset">Reset</a>
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
                                <p><strong><?php echo number_format($systemTotalApplications); ?></strong> Total
                                    Applications</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong><?php echo number_format($systemPendingApplications); ?></strong> Pending
                                    Review</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong><?php echo number_format($systemApprovedApplications); ?></strong> Approved
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="stats-grid">
                        <div class="stat-card total">
                            <i class="lni lni-files stat-icon"></i>
                            <span class="stat-number"><?php echo number_format($totalApplications); ?></span>
                            <span class="stat-label">Total Applications</span>
                        </div>
                        <div class="stat-card pending">
                            <i class="lni lni-hourglass stat-icon"></i>
                            <span class="stat-number"><?php echo number_format($pendingApplications); ?></span>
                            <span class="stat-label">Pending Review</span>
                        </div>
                        <div class="stat-card approved">
                            <i class="lni lni-checkmark-circle stat-icon"></i>
                            <span class="stat-number"><?php echo number_format($approvedApplications); ?></span>
                            <span class="stat-label">Approved</span>
                        </div>
                        <div class="stat-card rejected">
                            <i class="lni lni-close stat-icon"></i>
                            <span class="stat-number"><?php echo number_format($rejectedApplications); ?></span>
                            <span class="stat-label">Rejected</span>
                        </div>
                        <div class="stat-card shortlisted">
                            <i class="lni lni-star stat-icon"></i>
                            <span class="stat-number"><?php echo number_format($shortlistedApplications); ?></span>
                            <span class="stat-label">Shortlisted</span>
                        </div>
                        <div class="stat-card interviewed">
                            <i class="lni lni-users stat-icon"></i>
                            <span class="stat-number"><?php echo number_format($interviewedApplications); ?></span>
                            <span class="stat-label">Interviewed</span>
                        </div>
                    </div>

                    <!-- Key Metrics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="metric-card">
                                <span
                                    class="metric-value"><?php echo number_format($avgApplicationsPerDay, 1); ?></span>
                                <span class="metric-label">Avg Applications/Day</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-card">
                                <span class="metric-value"><?php echo number_format($successRate, 1); ?>%</span>
                                <span class="metric-label">Success Rate</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-card">
                                <span class="metric-value"><?php echo number_format($responseRate, 1); ?>%</span>
                                <span class="metric-label">Response Rate</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-card">
                                <span class="metric-value"><?php echo number_format($rejectionRate, 1); ?>%</span>
                                <span class="metric-label">Rejection Rate</span>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row 1 -->
                    <div class="row">
                        <!-- Application Trend -->
                        <div class="col-md-8 mb-4">
                            <div class="chart-section">
                                <div class="chart-header">
                                    <div>
                                        <h5 class="chart-title">
                                            <i class="lni lni-stats-up mr-2"></i>Application Trend
                                        </h5>
                                        <p class="chart-subtitle">Daily application submissions</p>
                                    </div>
                                </div>
                                <div class="chart-container">
                                    <canvas id="applicationTrendChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Status Distribution -->
                        <div class="col-md-4 mb-4">
                            <div class="chart-section">
                                <div class="chart-header">
                                    <div>
                                        <h5 class="chart-title">
                                            <i class="lni lni-pie-chart mr-2"></i>Status Distribution
                                        </h5>
                                        <p class="chart-subtitle">Applications by status</p>
                                    </div>
                                </div>
                                <div class="chart-container-small">
                                    <canvas id="statusChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tables Row -->
                    <div class="row">
                        <!-- Top Jobs -->
                        <div class="col-md-6 mb-4">
                            <div class="report-table-card">
                                <div class="table-header">
                                    <i class="lni lni-briefcase mr-2"></i>Most Applied Jobs
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
                                            while ($job = mysqli_fetch_assoc($topJobsResult)): 
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
                                            <?php if (mysqli_num_rows($topJobsResult) == 0): ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No data available</td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Top Applicants -->
                        <div class="col-md-6 mb-4">
                            <div class="report-table-card">
                                <div class="table-header">
                                    <i class="lni lni-star mr-2"></i>Most Active Applicants
                                </div>
                                <div class="table-responsive">
                                    <table class="report-table">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>Applicant</th>
                                                <th>Email</th>
                                                <th>Applications</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $rank = 1;
                                            while ($applicant = mysqli_fetch_assoc($topApplicantsResult)): 
                                            ?>
                                            <tr>
                                                <td><span class="rank-badge">#<?php echo $rank++; ?></span></td>
                                                <td>
                                                    <div class="info-display">
                                                        <?php if (!empty($applicant['APPLICANTPHOTO'])): ?>
                                                        <img src="<?php echo $path.htmlspecialchars($applicant['APPLICANTPHOTO']); ?>"
                                                            alt="Avatar" class="avatar-small"
                                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        <?php endif; ?>
                                                        <div class="avatar-placeholder"
                                                            style="<?php echo !empty($applicant['APPLICANTPHOTO']) ? 'display:none;' : ''; ?>">
                                                            <?php echo strtoupper(substr($applicant['FNAME'], 0, 1)); ?>
                                                        </div>

                                                        <span><?php echo htmlspecialchars($applicant['FNAME'] . ' ' . $applicant['ONAME']); ?></span>
                                                    </div>
                                                </td>
                                                <td><?php echo htmlspecialchars($applicant['EMAIL']); ?></td>
                                                <td>
                                                    <span class="count-badge">
                                                        <?php echo number_format($applicant['app_count']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                            <?php if (mysqli_num_rows($topApplicantsResult) == 0): ?>
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

                    <!-- Top Companies -->
                    <div class="report-table-card">
                        <div class="table-header">
                            <i class="fa fa-building mr-2"></i>Top Companies by Applications Received
                            <span style="float: right; font-weight: normal;">
                                Companies receiving most applications
                            </span>
                        </div>
                        <div class="table-responsive">
                            <table class="report-table">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Company</th>
                                        <th>Applications Received</th>
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
                                            <div class="info-display">


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
                                                <?php echo number_format($company['app_count']); ?>
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

                    <!-- Detailed Application Report -->
                    <?php if ($report_type == 'detailed' && isset($detailedReportResult)): ?>
                    <div class="report-table-card">
                        <div class="table-header">
                            <i class="lni lni-list mr-2"></i>Detailed Application Report
                            <span style="float: right; font-weight: normal;">
                                Showing up to 100 applications
                            </span>
                        </div>
                        <div class="table-responsive">
                            <table class="report-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Applicant</th>
                                        <th>Job</th>
                                        <th>Company</th>
                                        <th>Status</th>
                                        <th>Applied Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($app = mysqli_fetch_assoc($detailedReportResult)): ?>
                                    <tr>
                                        <td><?php echo $app['ID']; ?></td>
                                        <td>
                                            <div class="info-display">
                                                <?php if (!empty($app['APPLICANTPHOTO'])): ?>
                                                <img src="<?php echo $path.htmlspecialchars($app['APPLICANTPHOTO']); ?>"
                                                    alt="Avatar" class="avatar-small">
                                                <?php else: ?>
                                                <div class="avatar-placeholder">
                                                    <?php echo strtoupper(substr($app['FNAME'], 0, 1)); ?>
                                                </div>
                                                <?php endif; ?>
                                                <div>
                                                    <div>
                                                        <?php echo htmlspecialchars($app['FNAME'] . ' ' . $app['ONAME']); ?>
                                                    </div>
                                                    <small
                                                        class="text-muted"><?php echo htmlspecialchars($app['EMAIL']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($app['JOBTITLE']); ?></td>
                                        <td>
                                            <div class="info-display">
                                                <?php if (!empty($app['COMPANYLOGO'])): ?>
                                                <img src="<?php echo $path.htmlspecialchars($app['COMPANYLOGO']); ?>"
                                                    alt="Logo" class="company-logo-small">
                                                <?php else: ?>
                                                <div class="company-logo-placeholder">
                                                    <?php echo strtoupper(substr($app['COMPANYNAME'], 0, 1)); ?>
                                                </div>
                                                <?php endif; ?>
                                                <span><?php echo htmlspecialchars($app['COMPANYNAME']); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = 'status-pending';
                                            $status = $app['APPLICATIONSTATUS'];
                                            if ($status == 'Approved') {
                                                $statusClass = 'status-approved';
                                            } elseif ($status == 'Rejected') {
                                                $statusClass = 'status-rejected';
                                            } elseif ($status == 'Shortlisted') {
                                                $statusClass = 'status-shortlisted';
                                            } elseif ($status == 'Interviewed') {
                                                $statusClass = 'status-interviewed';
                                            }
                                            ?>
                                            <span class="status-badge <?php echo $statusClass; ?>">
                                                <?php echo htmlspecialchars($status); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($app['DATEAPPLIED'])); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php if (mysqli_num_rows($detailedReportResult) == 0): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No applications found in this
                                            period</td>
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

    // ==================== APPLICATION TREND CHART ====================
    const applicationTrendCtx = document.getElementById('applicationTrendChart').getContext('2d');
    new Chart(applicationTrendCtx, {
        type: 'line',
        data: {
            labels: [<?php 
                foreach ($applicationTrendData as $data) {
                    echo "'" . date('M d', strtotime($data['date'])) . "',";
                }
            ?>],
            datasets: [{
                label: 'Applications',
                data: [<?php 
                    foreach ($applicationTrendData as $data) {
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
                    '#ffc107',
                    '#28a745',
                    '#dc3545',
                    '#17a2b8',
                    '#6f42c1',
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
    </script>

    <?php echo Toast::render(); ?>
</body>

</html>