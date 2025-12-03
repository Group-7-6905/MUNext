<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';
require '../include/toast.php';

// ==================== DATE RANGE FILTER ====================
$date_from = isset($_GET['date_from']) ? mysqli_real_escape_string($con, $_GET['date_from']) : date('Y-m-01');
$date_to = isset($_GET['date_to']) ? mysqli_real_escape_string($con, $_GET['date_to']) : date('Y-m-d');

// ==================== OVERALL STATISTICS ====================
// Total companies
$totalCompaniesQuery = "SELECT COUNT(*) as total FROM tblcompany";
$totalCompanies = mysqli_fetch_assoc(mysqli_query($con, $totalCompaniesQuery))['total'];

// Active companies
$activeCompaniesQuery = "SELECT COUNT(*) as total FROM tblcompany WHERE COMPANYSTATUS = 'Active'";
$activeCompanies = mysqli_fetch_assoc(mysqli_query($con, $activeCompaniesQuery))['total'];

// Pending companies
$pendingCompaniesQuery = "SELECT COUNT(*) as total FROM tblcompany WHERE COMPANYSTATUS = 'Pending'";
$pendingCompanies = mysqli_fetch_assoc(mysqli_query($con, $pendingCompaniesQuery))['total'];

// Suspended companies
$suspendedCompaniesQuery = "SELECT COUNT(*) as total FROM tblcompany WHERE COMPANYSTATUS = 'Suspended'";
$suspendedCompanies = mysqli_fetch_assoc(mysqli_query($con, $suspendedCompaniesQuery))['total'];

// New companies in period
$newCompaniesQuery = "SELECT COUNT(*) as total FROM tblcompany WHERE DATE(DATEREGISTERED) BETWEEN ? AND ?";
$stmt = mysqli_prepare($con, $newCompaniesQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$newCompanies = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

// Companies with jobs
$companiesWithJobsQuery = "SELECT COUNT(DISTINCT COMPANYID) as total FROM tbljob";
$companiesWithJobs = mysqli_fetch_assoc(mysqli_query($con, $companiesWithJobsQuery))['total'];

// ==================== COMPANY GROWTH TREND ====================
$growthTrendQuery = "SELECT DATE(DATEREGISTERED) as date, COUNT(*) as count 
                     FROM tblcompany 
                     WHERE DATE(DATEREGISTERED) BETWEEN ? AND ? 
                     GROUP BY DATE(DATEREGISTERED) 
                     ORDER BY date ASC";
$stmt = mysqli_prepare($con, $growthTrendQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$growthTrendResult = mysqli_stmt_get_result($stmt);
$growthTrendData = [];
while ($row = mysqli_fetch_assoc($growthTrendResult)) {
    $growthTrendData[] = $row;
}

// ==================== COMPANIES BY COUNTRY ====================
$companiesByCountryQuery = "SELECT COMPANYCOUNTRY, COUNT(*) as count 
                            FROM tblcompany 
                            WHERE COMPANYCOUNTRY IS NOT NULL AND COMPANYCOUNTRY != ''
                            GROUP BY COMPANYCOUNTRY 
                            ORDER BY count DESC 
                            LIMIT 10";
$companiesByCountryResult = mysqli_query($con, $companiesByCountryQuery);

// ==================== COMPANIES BY STATUS ====================
$statusDistributionQuery = "SELECT COMPANYSTATUS, COUNT(*) as count 
                            FROM tblcompany 
                            GROUP BY COMPANYSTATUS";
$statusDistributionResult = mysqli_query($con, $statusDistributionQuery);
$statusDistributionData = [];
while ($row = mysqli_fetch_assoc($statusDistributionResult)) {
    $statusDistributionData[$row['COMPANYSTATUS']] = $row['count'];
}

// ==================== TOP COMPANIES BY JOBS ====================
$topCompaniesByJobsQuery = "SELECT c.COMPANYID, c.COMPANYNAME, c.COMPANYLOGO, COUNT(j.JOBID) as job_count,
                             (SELECT COUNT(*) FROM tbljob WHERE COMPANYID = c.COMPANYID AND JOBSTATUS = 'Active') as active_jobs
                             FROM tblcompany c
                             INNER JOIN tbljob j ON c.COMPANYID = j.COMPANYID
                             GROUP BY c.COMPANYID
                             ORDER BY job_count DESC
                             LIMIT 10";
$topCompaniesByJobsResult = mysqli_query($con, $topCompaniesByJobsQuery);

// ==================== TOP COMPANIES BY APPLICATIONS ====================
$topCompaniesByAppsQuery = "SELECT c.COMPANYID, c.COMPANYNAME, c.COMPANYLOGO, COUNT(ja.ID) as app_count
                            FROM tblcompany c
                            INNER JOIN tbljob j ON c.COMPANYID = j.COMPANYID
                            INNER JOIN tbljobapplication ja ON j.JOBID = ja.JOBID
                            GROUP BY c.COMPANYID
                            ORDER BY app_count DESC
                            LIMIT 10";
$topCompaniesByAppsResult = mysqli_query($con, $topCompaniesByAppsQuery);

// ==================== COMPANY ENGAGEMENT METRICS ====================
// Average jobs per company
$avgJobsPerCompany = $totalCompanies > 0 ? mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM tbljob"))['total'] / $totalCompanies : 0;

// Average applications per company
$totalApplications = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM tbljobapplication"))['total'];
$avgAppsPerCompany = $companiesWithJobs > 0 ? $totalApplications / $companiesWithJobs : 0;

// Company activation rate
$activationRate = $totalCompanies > 0 ? ($activeCompanies / $totalCompanies) * 100 : 0;

// Company job posting rate
$jobPostingRate = $totalCompanies > 0 ? ($companiesWithJobs / $totalCompanies) * 100 : 0;

// ==================== RECENT COMPANY ACTIVITIES ====================
$recentActivitiesQuery = "SELECT c.COMPANYID, c.COMPANYNAME, c.COMPANYLOGO, c.DATEREGISTERED, c.COMPANYSTATUS,
                          (SELECT COUNT(*) FROM tbljob WHERE COMPANYID = c.COMPANYID) as job_count
                          FROM tblcompany c
                          WHERE DATE(c.DATEREGISTERED) BETWEEN ? AND ?
                          ORDER BY c.DATEREGISTERED DESC
                          LIMIT 10";
$stmt = mysqli_prepare($con, $recentActivitiesQuery);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$recentActivitiesResult = mysqli_stmt_get_result($stmt);

// ==================== COMPANIES WITHOUT JOBS ====================
$companiesWithoutJobsQuery = "SELECT COUNT(*) as total FROM tblcompany c 
                               WHERE NOT EXISTS (SELECT 1 FROM tbljob WHERE COMPANYID = c.COMPANYID)
                               AND c.COMPANYSTATUS = 'Active'";
$companiesWithoutJobs = mysqli_fetch_assoc(mysqli_query($con, $companiesWithoutJobsQuery))['total'];

// ==================== SYSTEM TOTALS ====================
$systemTotalJobs = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM tbljob"))['total'];
$systemActiveJobs = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM tbljob WHERE JOBSTATUS = 'Active'"))['total'];
?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Company Statistics - Admin Dashboard | MUNext</title>
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
                    <h1 class="page-title">
                        <i class="lni lni-bar-chart mr-2"></i>Company Statistics & Analytics
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Companies</a></li>
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
                                        <a href="admin-company-statistics.php" class="btn-reset">Reset</a>
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
                                <p><strong><?php echo number_format($totalApplications); ?></strong> Total Applications
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="stats-grid">
                        <div class="stat-card total">
                            <i class="fa fa-building stat-icon"></i>
                            <span class="stat-number"><?php echo number_format($totalCompanies); ?></span>
                            <span class="stat-label">Total Companies</span>
                        </div>
                        <div class="stat-card active">
                            <i class="lni lni-checkmark-circle stat-icon"></i>
                            <span class="stat-number"><?php echo number_format($activeCompanies); ?></span>
                            <span class="stat-label">Active Companies</span>
                        </div>
                        <div class="stat-card pending">
                            <i class="lni lni-hourglass stat-icon"></i>
                            <span class="stat-number"><?php echo number_format($pendingCompanies); ?></span>
                            <span class="stat-label">Pending Approval</span>
                        </div>
                        <div class="stat-card suspended">
                            <i class="lni lni-ban stat-icon"></i>
                            <span class="stat-number"><?php echo number_format($suspendedCompanies); ?></span>
                            <span class="stat-label">Suspended</span>
                        </div>
                        <div class="stat-card new">
                            <i class="lni lni-star stat-icon"></i>
                            <span class="stat-number"><?php echo number_format($newCompanies); ?></span>
                            <span class="stat-label">New Companies (Period)</span>
                        </div>
                        <div class="stat-card jobs">
                            <i class="lni lni-briefcase stat-icon"></i>
                            <span class="stat-number"><?php echo number_format($companiesWithJobs); ?></span>
                            <span class="stat-label">Companies with Jobs</span>
                        </div>
                    </div>

                    <!-- Key Metrics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="metric-card">
                                <span class="metric-value"><?php echo number_format($avgJobsPerCompany, 1); ?></span>
                                <span class="metric-label">Avg Jobs per Company</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-card">
                                <span class="metric-value"><?php echo number_format($avgAppsPerCompany, 1); ?></span>
                                <span class="metric-label">Avg Applications per Company</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-card">
                                <span class="metric-value"><?php echo number_format($activationRate, 1); ?>%</span>
                                <span class="metric-label">Company Activation Rate</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-card">
                                <span class="metric-value"><?php echo number_format($jobPostingRate, 1); ?>%</span>
                                <span class="metric-label">Job Posting Rate</span>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row 1 -->
                    <div class="row">
                        <!-- Company Growth Trend -->
                        <div class="col-md-8 mb-4">
                            <div class="chart-section">
                                <div class="chart-header">
                                    <div>
                                        <h5 class="chart-title">
                                            <i class="lni lni-stats-up mr-2"></i>Company Registration Trend
                                        </h5>
                                        <p class="chart-subtitle">Daily new company registrations</p>
                                    </div>
                                </div>
                                <div class="chart-container">
                                    <canvas id="growthTrendChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Status Distribution -->
                        <div class="col-md-4 mb-4">
                            <div class="chart-section">
                                <div class="chart-header">
                                    <div>
                                        <h5 class="chart-title">
                                            <i class="lni lni-pie-chart mr-2"></i>Company Status
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
                        <!-- Companies by Country -->
                        <div class="col-md-6 mb-4">
                            <div class="chart-section">
                                <div class="chart-header">
                                    <div>
                                        <h5 class="chart-title">
                                            <i class="lni lni-map-marker mr-2"></i>Top Countries
                                        </h5>
                                        <p class="chart-subtitle">Companies by country</p>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="report-table">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>Country</th>
                                                <th>Companies</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $rank = 1;
                                            mysqli_data_seek($companiesByCountryResult, 0);
                                            while ($location = mysqli_fetch_assoc($companiesByCountryResult)): 
                                            ?>
                                            <tr>
                                                <td><span class="rank-badge">#<?php echo $rank++; ?></span></td>
                                                <td>
                                                    <i class="lni lni-map-marker text-muted mr-2"></i>
                                                    <?php echo htmlspecialchars($location['COMPANYCOUNTRY']); ?>
                                                </td>
                                                <td>
                                                    <span class="count-badge">
                                                        <?php echo number_format($location['count']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Registrations -->
                        <div class="col-md-6 mb-4">
                            <div class="chart-section">
                                <div class="chart-header">
                                    <div>
                                        <h5 class="chart-title">
                                            <i class="lni lni-calendar mr-2"></i>Recent Registrations
                                        </h5>
                                        <p class="chart-subtitle">Latest company sign-ups</p>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="report-table">
                                        <thead>
                                            <tr>
                                                <th>Company</th>
                                                <th>Jobs</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($activity = mysqli_fetch_assoc($recentActivitiesResult)): ?>
                                            <tr>
                                                <td>
                                                    <div class="company-info">
                                                        <?php if (!empty($activity['COMPANYLOGO'])): ?>
                                                        <img src="<?php echo $path.htmlspecialchars($activity['COMPANYLOGO']); ?>"
                                                            alt="Logo" class="company-logo-small"
                                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        <?php endif; ?>
                                                        <div class="company-logo-placeholder"
                                                            style="<?php echo !empty($activity['COMPANYLOGO']) ? 'display:none;' : ''; ?>">
                                                            <?php echo strtoupper(substr($activity['COMPANYNAME'], 0, 1)); ?>
                                                        </div>


                                                        <span><?php echo htmlspecialchars($activity['COMPANYNAME']); ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="count-badge">
                                                        <?php echo number_format($activity['job_count']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $statusClass = 'status-active';
                                                    $status = $activity['COMPANYSTATUS'];
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
                                                <td><?php echo date('M d, Y', strtotime($activity['DATEREGISTERED'])); ?>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                            <?php if (mysqli_num_rows($recentActivitiesResult) == 0): ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No recent registrations
                                                </td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Companies Tables -->
                    <div class="row">
                        <!-- Top Companies by Jobs -->
                        <div class="col-md-6 mb-4">
                            <div class="report-table-card">
                                <div class="table-header theme-bg">
                                    <i class="lni lni-briefcase mr-2"></i>Top Companies by Jobs Posted
                                </div>
                                <div class="table-responsive">
                                    <table class="report-table">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>Company</th>
                                                <th>Total Jobs</th>
                                                <th>Active</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $rank = 1;
                                            while ($company = mysqli_fetch_assoc($topCompaniesByJobsResult)): 
                                            ?>
                                            <tr>
                                                <td><span class="rank-badge">#<?php echo $rank++; ?></span></td>
                                                <td>
                                                    <div class="company-info">

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
                                                <td>
                                                    <small class="text-success">
                                                        <?php echo number_format($company['active_jobs']); ?> active
                                                    </small>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                            <?php if (mysqli_num_rows($topCompaniesByJobsResult) == 0): ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No data available</td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Top Companies by Applications -->
                        <div class="col-md-6 mb-4">
                            <div class="report-table-card">
                                <div class="table-header theme-bg">
                                    <i class="lni lni-files mr-2"></i>Top Companies by Applications Received
                                </div>
                                <div class="table-responsive">
                                    <table class="report-table">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>Company</th>
                                                <th>Applications</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $rank = 1;
                                            while ($company = mysqli_fetch_assoc($topCompaniesByAppsResult)): 
                                            ?>
                                            <tr>
                                                <td><span class="rank-badge">#<?php echo $rank++; ?></span></td>
                                                <td>
                                                    <div class="company-info">
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
                                            <?php if (mysqli_num_rows($topCompaniesByAppsResult) == 0): ?>
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

                    <!-- Additional Insights -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="chart-section">
                                <div class="chart-header">
                                    <div>
                                        <h5 class="chart-title">
                                            <i class="lni lni-information mr-2"></i>Additional Insights
                                        </h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Companies Without Jobs:</strong>
                                            <?php echo number_format($companiesWithoutJobs); ?> active companies
                                            (<?php echo number_format($activeCompanies > 0 ? ($companiesWithoutJobs / $activeCompanies) * 100 : 0, 1); ?>%)
                                            have not posted any jobs yet.</p>
                                        <p><strong>Average Response:</strong> Companies receive an average of
                                            <?php echo number_format($avgAppsPerCompany, 1); ?> applications.</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Engagement Rate:</strong>
                                            <?php echo number_format($jobPostingRate, 1); ?>% of all companies have
                                            posted at least one job.</p>
                                        <p><strong>Growth Rate:</strong> <?php echo number_format($newCompanies); ?> new
                                            companies registered in the selected period.</p>
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

    // ==================== COMPANY GROWTH TREND CHART ====================
    const growthTrendCtx = document.getElementById('growthTrendChart').getContext('2d');
    new Chart(growthTrendCtx, {
        type: 'line',
        data: {
            labels: [<?php 
                foreach ($growthTrendData as $data) {
                    echo "'" . date('M d', strtotime($data['date'])) . "',";
                }
            ?>],
            datasets: [{
                label: 'New Companies',
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
                    '#6c757d',
                    '#17a2b8'
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