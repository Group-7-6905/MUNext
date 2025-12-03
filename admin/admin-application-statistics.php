<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';
require '../include/toast.php';

// ==================== DATE RANGE FILTER ====================
$date_from = isset($_GET['date_from']) ? mysqli_real_escape_string($con, $_GET['date_from']) : date('Y-m-01');
$date_to = isset($_GET['date_to']) ? mysqli_real_escape_string($con, $_GET['date_to']) : date('Y-m-d');

// ==================== OVERALL STATISTICS ====================
// Total applications
$totalApplicationsQuery = "SELECT COUNT(*) as total FROM tbljobapplication";
$totalApplications = mysqli_fetch_assoc(mysqli_query($con, $totalApplicationsQuery))['total'];

// Applications by status
$pendingApplicationsQuery = "SELECT COUNT(*) as total FROM tbljobapplication WHERE APPLICATIONSTATUS = 'Pending'";
$pendingApplications = mysqli_fetch_assoc(mysqli_query($con, $pendingApplicationsQuery))['total'];

$approvedApplicationsQuery = "SELECT COUNT(*) as total FROM tbljobapplication WHERE APPLICATIONSTATUS = 'Approved'";
$approvedApplications = mysqli_fetch_assoc(mysqli_query($con, $approvedApplicationsQuery))['total'];

$rejectedApplicationsQuery = "SELECT COUNT(*) as total FROM tbljobapplication WHERE APPLICATIONSTATUS = 'Rejected'";
$rejectedApplications = mysqli_fetch_assoc(mysqli_query($con, $rejectedApplicationsQuery))['total'];

$shortlistedApplicationsQuery = "SELECT COUNT(*) as total FROM tbljobapplication WHERE APPLICATIONSTATUS = 'Shortlisted'";
$shortlistedApplications = mysqli_fetch_assoc(mysqli_query($con, $shortlistedApplicationsQuery))['total'];

$interviewedApplicationsQuery = "SELECT COUNT(*) as total FROM tbljobapplication WHERE APPLICATIONSTATUS = 'Interviewed'";
$interviewedApplications = mysqli_fetch_assoc(mysqli_query($con, $interviewedApplicationsQuery))['total'];

// New applications in period
$newApplicationsQuery = "SELECT COUNT(*) as total FROM tbljobapplication WHERE DATE(DATEAPPLIED) BETWEEN ? AND ?";
$stmt = mysqli_prepare($con, $newApplicationsQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$newApplications = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

// ==================== APPLICATION TREND ====================
$trendQuery = "SELECT DATE(DATEAPPLIED) as date, COUNT(*) as count 
               FROM tbljobapplication 
               WHERE DATE(DATEAPPLIED) BETWEEN ? AND ? 
               GROUP BY DATE(DATEAPPLIED) 
               ORDER BY date ASC";
$stmt = mysqli_prepare($con, $trendQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$trendResult = mysqli_stmt_get_result($stmt);
$trendData = [];
while ($row = mysqli_fetch_assoc($trendResult)) {
    $trendData[] = $row;
}

// ==================== STATUS DISTRIBUTION ====================
$statusDistributionQuery = "SELECT APPLICATIONSTATUS, COUNT(*) as count 
                            FROM tbljobapplication 
                            GROUP BY APPLICATIONSTATUS";
$statusDistributionResult = mysqli_query($con, $statusDistributionQuery);
$statusDistributionData = [];
while ($row = mysqli_fetch_assoc($statusDistributionResult)) {
    $statusDistributionData[$row['APPLICATIONSTATUS']] = $row['count'];
}

// ==================== TOP JOBS BY APPLICATIONS ====================
$topJobsQuery = "SELECT j.JOBID, j.JOBTITLE, c.COMPANYNAME, c.COMPANYLOGO, COUNT(ja.ID) as app_count
                 FROM tbljob j
                 LEFT JOIN tbljobapplication ja ON j.JOBID = ja.JOBID
                 LEFT JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
                 GROUP BY j.JOBID
                 ORDER BY app_count DESC
                 LIMIT 10";
$topJobsResult = mysqli_query($con, $topJobsQuery);

// ==================== TOP APPLICANTS ====================
$topApplicantsQuery = "SELECT a.USERID, a.FNAME, a.OTHERNAMES, a.EMAILADDRESS, a.APPLICANTPHOTO, 
                       COUNT(ja.ID) as app_count,
                       SUM(CASE WHEN ja.APPLICATIONSTATUS = 'Approved' THEN 1 ELSE 0 END) as approved_count
                       FROM tblapplicants a
                       LEFT JOIN tbljobapplication ja ON a.USERID = ja.APPLICANTID
                       GROUP BY a.USERID
                       HAVING app_count > 0
                       ORDER BY app_count DESC
                       LIMIT 10";
$topApplicantsResult = mysqli_query($con, $topApplicantsQuery);

// ==================== APPLICATIONS BY COMPANY ====================
$applicationsByCompanyQuery = "SELECT c.COMPANYNAME, COUNT(ja.ID) as count 
                               FROM tblcompany c
                               LEFT JOIN tbljob j ON c.COMPANYID = j.COMPANYID
                               LEFT JOIN tbljobapplication ja ON j.JOBID = ja.JOBID
                               WHERE ja.ID IS NOT NULL
                               GROUP BY c.COMPANYID
                               ORDER BY count DESC
                               LIMIT 10";
$applicationsByCompanyResult = mysqli_query($con, $applicationsByCompanyQuery);

// ==================== KEY METRICS ====================
// Average applications per day
$avgAppsPerDay = 0;
if (count($trendData) > 0) {
    $totalDays = count($trendData);
    $totalCount = array_sum(array_column($trendData, 'count'));
    $avgAppsPerDay = $totalDays > 0 ? $totalCount / $totalDays : 0;
}

// Success rate (approved + shortlisted + interviewed / total)
$successRate = $totalApplications > 0 ? (($approvedApplications + $shortlistedApplications + $interviewedApplications) / $totalApplications) * 100 : 0;

// Response rate (non-pending / total)
$responseRate = $totalApplications > 0 ? (($totalApplications - $pendingApplications) / $totalApplications) * 100 : 0;

// Rejection rate
$rejectionRate = $totalApplications > 0 ? ($rejectedApplications / $totalApplications) * 100 : 0;

// ==================== RECENT ACTIVITIES ====================
$recentActivitiesQuery = "SELECT ja.ID, ja.DATEAPPLIED, ja.APPLICATIONSTATUS,
                          j.JOBTITLE, c.COMPANYNAME, c.COMPANYLOGO,
                          a.FNAME, a.OTHERNAMES, a.APPLICANTPHOTO
                          FROM tbljobapplication ja
                          INNER JOIN tbljob j ON ja.JOBID = j.JOBID
                          INNER JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
                          LEFT JOIN tblapplicants a ON ja.APPLICANTID = a.USERID
                          WHERE DATE(ja.DATEAPPLIED) BETWEEN ? AND ?
                          ORDER BY ja.DATEAPPLIED DESC
                          LIMIT 10";
$stmt = mysqli_prepare($con, $recentActivitiesQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$recentActivitiesResult = mysqli_stmt_get_result($stmt);

// ==================== APPLICATIONS BY JOB TYPE ====================
$applicationsByTypeQuery = "SELECT j.JOBTYPE, COUNT(ja.ID) as count
                            FROM tbljob j
                            LEFT JOIN tbljobapplication ja ON j.JOBID = ja.JOBID
                            WHERE ja.ID IS NOT NULL
                            GROUP BY j.JOBTYPE
                            ORDER BY count DESC";
$applicationsByTypeResult = mysqli_query($con, $applicationsByTypeQuery);

// ==================== CONVERSION FUNNEL ====================
$funnelTotal = $totalApplications;
$funnelReviewed = $totalApplications - $pendingApplications;
$funnelShortlisted = $shortlistedApplications + $interviewedApplications;
$funnelApproved = $approvedApplications;

// ==================== SYSTEM TOTALS ====================
$systemTotalJobs = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM tbljob"))['total'];
$systemTotalApplicants = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(DISTINCT APPLICANTID) as total FROM tbljobapplication"))['total'];
$systemTotalCompanies = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM tblcompany"))['total'];
?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Application Statistics - Admin Dashboard | MUNext</title>
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
                    <h1 class="page-title">
                        <i class="lni lni-bar-chart mr-2"></i>Application Statistics & Analytics
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Applications</a></li>
                            <li class="breadcrumb-item active">Statistics</li>
                        </ol>
                    </nav>
                </div>

                <div class="container-fluid">
                    <!-- Date Filter -->
                    <div class="filter-section">
                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">From Date</label>
                                    <input type="date" name="date_from" class="form-control"
                                        value="<?php echo htmlspecialchars($date_from); ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">To Date</label>
                                    <input type="date" name="date_to" class="form-control"
                                        value="<?php echo htmlspecialchars($date_to); ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn-filter">
                                            <i class="lni lni-search mr-1"></i>Generate Report
                                        </button>
                                        <a href="admin-application-statistics.php" class="btn-reset">Reset</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- System Overview -->
                    <div class="info-card">
                        <h5 class="text-white"><i class="lni lni-database mr-2"></i>System Overview (All Time)</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <p><strong><?php echo number_format($systemTotalJobs); ?></strong> Total Jobs</p>
                            </div>
                            <div class="col-md-3">
                                <p><strong><?php echo number_format($systemTotalApplicants); ?></strong> Unique
                                    Applicants</p>
                            </div>
                            <div class="col-md-3">
                                <p><strong><?php echo number_format($systemTotalCompanies); ?></strong> Companies</p>
                            </div>
                            <div class="col-md-3">
                                <p><strong><?php echo number_format($totalApplications); ?></strong> Total Applications
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
                        <div class="stat-card shortlisted">
                            <i class="lni lni-star stat-icon"></i>
                            <span class="stat-number"><?php echo number_format($shortlistedApplications); ?></span>
                            <span class="stat-label">Shortlisted</span>
                        </div>
                        <div class="stat-card rejected">
                            <i class="lni lni-ban stat-icon"></i>
                            <span class="stat-number"><?php echo number_format($rejectedApplications); ?></span>
                            <span class="stat-label">Rejected</span>
                        </div>
                        <div class="stat-card new">
                            <i class="lni lni-calendar stat-icon"></i>
                            <span class="stat-number"><?php echo number_format($newApplications); ?></span>
                            <span class="stat-label">New (Period)</span>
                        </div>
                    </div>

                    <!-- Key Metrics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="metric-card">
                                <span class="metric-value"><?php echo number_format($avgAppsPerDay, 1); ?></span>
                                <span class="metric-label">Avg Applications per Day</span>
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
                                    <canvas id="trendChart"></canvas>
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

                    <!-- Charts Row 2 -->
                    <div class="row">
                        <!-- Applications by Job Type -->
                        <div class="col-md-6 mb-4">
                            <div class="chart-section">
                                <div class="chart-header">
                                    <div>
                                        <h5 class="chart-title">
                                            <i class="lni lni-briefcase mr-2"></i>Applications by Job Type
                                        </h5>
                                        <p class="chart-subtitle">Distribution across job types</p>
                                    </div>
                                </div>
                                <div class="chart-container-small">
                                    <canvas id="jobTypeChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Conversion Funnel -->
                        <div class="col-md-6 mb-4">
                            <div class="chart-section">
                                <div class="chart-header">
                                    <div>
                                        <h5 class="chart-title">
                                            <i class="lni lni-funnel mr-2"></i>Application Funnel
                                        </h5>
                                        <p class="chart-subtitle">Conversion through stages</p>
                                    </div>
                                </div>
                                <div class="funnel-container">
                                    <div class="funnel-stage">
                                        <div class="funnel-label">
                                            <span>Total Applications</span>
                                            <span><?php echo number_format($funnelTotal); ?></span>
                                        </div>
                                        <div class="funnel-bar">
                                            <div class="funnel-fill" style="width: 100%;">100%</div>
                                        </div>
                                    </div>

                                    <div class="funnel-stage">
                                        <div class="funnel-label">
                                            <span>Reviewed</span>
                                            <span><?php echo number_format($funnelReviewed); ?></span>
                                        </div>
                                        <div class="funnel-bar">
                                            <div class="funnel-fill"
                                                style="width: <?php echo $funnelTotal > 0 ? ($funnelReviewed / $funnelTotal) * 100 : 0; ?>%;">
                                                <?php echo $funnelTotal > 0 ? number_format(($funnelReviewed / $funnelTotal) * 100, 1) : 0; ?>%
                                            </div>
                                        </div>
                                    </div>

                                    <div class="funnel-stage">
                                        <div class="funnel-label">
                                            <span>Shortlisted/Interviewed</span>
                                            <span><?php echo number_format($funnelShortlisted); ?></span>
                                        </div>
                                        <div class="funnel-bar">
                                            <div class="funnel-fill"
                                                style="width: <?php echo $funnelTotal > 0 ? ($funnelShortlisted / $funnelTotal) * 100 : 0; ?>%;">
                                                <?php echo $funnelTotal > 0 ? number_format(($funnelShortlisted / $funnelTotal) * 100, 1) : 0; ?>%
                                            </div>
                                        </div>
                                    </div>

                                    <div class="funnel-stage">
                                        <div class="funnel-label">
                                            <span>Approved</span>
                                            <span><?php echo number_format($funnelApproved); ?></span>
                                        </div>
                                        <div class="funnel-bar">
                                            <div class="funnel-fill"
                                                style="width: <?php echo $funnelTotal > 0 ? ($funnelApproved / $funnelTotal) * 100 : 0; ?>%;">
                                                <?php echo $funnelTotal > 0 ? number_format(($funnelApproved / $funnelTotal) * 100, 1) : 0; ?>%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tables Row -->
                    <div class="row">
                        <!-- Top Jobs by Applications -->
                        <div class="col-md-6 mb-4">
                            <div class="report-table-card">
                                <div class="table-header">
                                    <i class="lni lni-briefcase mr-2"></i>Top Jobs by Applications
                                </div>
                                <div class="table-responsive">
                                    <table class="report-table">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>Job / Company</th>
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
                                                <td>
                                                    <div class="item-info">

                                                        <?php if (!empty($job['COMPANYLOGO'])): ?>
                                                        <img src="<?php echo $path.htmlspecialchars($job['COMPANYLOGO']); ?>"
                                                            alt="Logo" class="company-logo-small"
                                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        <?php endif; ?>
                                                        <div class="company-logo-placeholder"
                                                            style="<?php echo !empty($job['COMPANYLOGO']) ? 'display:none;' : ''; ?>">
                                                            <?php echo strtoupper(substr($job['COMPANYNAME'], 0, 1)); ?>
                                                        </div>


                                                        <div>
                                                            <strong><?php echo htmlspecialchars($job['JOBTITLE']); ?></strong><br>
                                                            <small
                                                                class="text-muted"><?php echo htmlspecialchars($job['COMPANYNAME']); ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="count-badge">
                                                        <?php echo number_format($job['app_count']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Top Applicants -->
                        <div class="col-md-6 mb-4">
                            <div class="report-table-card">
                                <div class="table-header">
                                    <i class="lni lni-users mr-2"></i>Most Active Applicants
                                </div>
                                <div class="table-responsive">
                                    <table class="report-table">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>Applicant</th>
                                                <th>Applications</th>
                                                <th>Approved</th>
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
                                                    <div class="item-info">

                                                        <?php if (!empty($applicant['APPLICANTPHOTO'])): ?>
                                                        <img src="<?php echo $path.htmlspecialchars($applicant['APPLICANTPHOTO']); ?>"
                                                            alt="Avatar" class="avatar-small"
                                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        <?php endif; ?>
                                                        <div class="avatar-placeholder"
                                                            style="<?php echo !empty($applicant['APPLICANTPHOTO']) ? 'display:none;' : ''; ?>">
                                                            <?php echo strtoupper(substr($applicant['FNAME'], 0, 1)); ?>
                                                        </div>



                                                        <div>
                                                            <strong><?php echo htmlspecialchars($applicant['FNAME'] . ' ' . $applicant['OTHERNAMES']); ?></strong><br>
                                                            <small
                                                                class="text-muted"><?php echo htmlspecialchars($applicant['EMAILADDRESS']); ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="count-badge">
                                                        <?php echo number_format($applicant['app_count']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <small class="text-success">
                                                        <?php echo number_format($applicant['approved_count']); ?>
                                                    </small>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="report-table-card">
                                <div class="table-header">
                                    <i class="lni lni-calendar mr-2"></i>Recent Applications (Period)
                                </div>
                                <div class="table-responsive">
                                    <table class="report-table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Applicant</th>
                                                <th>Job / Company</th>
                                                <th>Date Applied</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($activity = mysqli_fetch_assoc($recentActivitiesResult)): ?>
                                            <tr>
                                                <td><?php echo $activity['ID']; ?></td>
                                                <td>
                                                    <div class="item-info">

                                                        <?php if (!empty($activity['APPLICANTPHOTO'])): ?>
                                                        <img src="<?php echo $path.htmlspecialchars($activity['APPLICANTPHOTO']); ?>"
                                                            alt="Avatar" class="avatar-small"
                                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        <?php endif; ?>
                                                        <div class="avatar-placeholder"
                                                            style="<?php echo !empty($activity['APPLICANTPHOTO']) ? 'display:none;' : ''; ?>">
                                                            <?php echo strtoupper(substr($activity['FNAME'], 0, 1)); ?>
                                                        </div>

                                                        <span><?php echo htmlspecialchars($activity['FNAME'] . ' ' . $activity['OTHERNAMES']); ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="item-info">
                                                        <?php if (!empty($activity['COMPANYLOGO'])): ?>
                                                        <img src="<?php echo $path.htmlspecialchars($activity['COMPANYLOGO']); ?>"
                                                            alt="Logo" class="company-logo-small"
                                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        <?php endif; ?>
                                                        <div class="company-logo-placeholder"
                                                            style="<?php echo !empty($activity['COMPANYLOGO']) ? 'display:none;' : ''; ?>">
                                                            <?php echo strtoupper(substr($activity['COMPANYNAME'], 0, 1)); ?>
                                                        </div>
                                                        <div>
                                                            <strong><?php echo htmlspecialchars($activity['JOBTITLE']); ?></strong><br>
                                                            <small
                                                                class="text-muted"><?php echo htmlspecialchars($activity['COMPANYNAME']); ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo date('M d, Y h:i A', strtotime($activity['DATEAPPLIED'])); ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $statusClass = 'status-pending';
                                                    $status = $activity['APPLICATIONSTATUS'];
                                                    if ($status == 'Approved') {
                                                        $statusClass = 'status-approved';
                                                    } elseif ($status == 'Rejected') {
                                                        $statusClass = 'status-rejected';
                                                    } elseif ($status == 'Shortlisted') {
                                                        $statusClass = 'status-shortlisted';
                                                    }
                                                    ?>
                                                    <span class="status-badge <?php echo $statusClass; ?>">
                                                        <?php echo htmlspecialchars($status); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                            <?php if (mysqli_num_rows($recentActivitiesResult) == 0): ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">No applications in this
                                                    period</td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Insights -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="chart-section">
                                <div class="chart-header">
                                    <div>
                                        <h5 class="chart-title">
                                            <i class="lni lni-information mr-2"></i>Key Insights
                                        </h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Success Rate:</strong> <?php echo number_format($successRate, 1); ?>%
                                            of applications are approved, shortlisted, or interviewed.</p>
                                        <p><strong>Average Response Time:</strong> Review and respond to applications
                                            promptly to maintain quality candidate engagement.</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Rejection Rate:</strong>
                                            <?php echo number_format($rejectionRate, 1); ?>% of total applications have
                                            been rejected.</p>
                                        <p><strong>Period Activity:</strong>
                                            <?php echo number_format($newApplications); ?>
                                            new applications received in the selected period.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: [<?php 
                foreach ($trendData as $data) {
                    echo "'" . date('M d', strtotime($data['date'])) . "',";
                }
            ?>],
            datasets: [{
                label: 'Applications',
                data: [<?php 
                    foreach ($trendData as $data) {
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
                    '#fd7e14'
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

    // ==================== JOB TYPE CHART ====================
    const jobTypeCtx = document.getElementById('jobTypeChart').getContext('2d');
    new Chart(jobTypeCtx, {
        type: 'bar',
        data: {
            labels: [<?php 
                mysqli_data_seek($applicationsByTypeResult, 0);
                while ($type = mysqli_fetch_assoc($applicationsByTypeResult)) {
                    echo "'" . addslashes($type['JOBTYPE']) . "',";
                }
            ?>],
            datasets: [{
                label: 'Applications',
                data: [<?php 
                    mysqli_data_seek($applicationsByTypeResult, 0);
                    while ($type = mysqli_fetch_assoc($applicationsByTypeResult)) {
                        echo $type['count'] . ',';
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