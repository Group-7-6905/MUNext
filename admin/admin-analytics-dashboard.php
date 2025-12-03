<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';
require '../include/toast.php';


// ==================== DATE RANGE FILTER ====================
$date_range = isset($_GET['range']) ? $_GET['range'] : '30';
$custom_from = isset($_GET['custom_from']) ? $_GET['custom_from'] : '';
$custom_to = isset($_GET['custom_to']) ? $_GET['custom_to'] : '';

// Calculate date range
switch ($date_range) {
    case '7':
        $from_date = date('Y-m-d', strtotime('-7 days'));
        $to_date = date('Y-m-d');
        break;
    case '30':
        $from_date = date('Y-m-d', strtotime('-30 days'));
        $to_date = date('Y-m-d');
        break;
    case '90':
        $from_date = date('Y-m-d', strtotime('-90 days'));
        $to_date = date('Y-m-d');
        break;
    case 'custom':
        $from_date = !empty($custom_from) ? $custom_from : date('Y-m-d', strtotime('-30 days'));
        $to_date = !empty($custom_to) ? $custom_to : date('Y-m-d');
        break;
    default:
        $from_date = date('Y-m-d', strtotime('-30 days'));
        $to_date = date('Y-m-d');
}

// ==================== OVERVIEW STATISTICS ====================
// Total Users
$totalUsersQuery = "SELECT COUNT(*) as total FROM tblusers WHERE ROLE != 'Admin'";
$totalUsers = mysqli_fetch_assoc(mysqli_query($con, $totalUsersQuery))['total'];

// Total Jobs
$totalJobsQuery = "SELECT COUNT(*) as total FROM tbljob";
$totalJobs = mysqli_fetch_assoc(mysqli_query($con, $totalJobsQuery))['total'];

// Total Applications
$totalApplicationsQuery = "SELECT COUNT(*) as total FROM tbljobapplication";
$totalApplications = mysqli_fetch_assoc(mysqli_query($con, $totalApplicationsQuery))['total'];

// Active Jobs
$activeJobsQuery = "SELECT COUNT(*) as total FROM tbljob WHERE JOBSTATUS = 'Active'";
$activeJobs = mysqli_fetch_assoc(mysqli_query($con, $activeJobsQuery))['total'];

// Total Companies
$totalCompaniesQuery = "SELECT COUNT(*) as total FROM tblcompany";
$totalCompanies = mysqli_fetch_assoc(mysqli_query($con, $totalCompaniesQuery))['total'];

// Total Applicants
$totalApplicantsQuery = "SELECT COUNT(*) as total FROM tblapplicants";
$totalApplicants = mysqli_fetch_assoc(mysqli_query($con, $totalApplicantsQuery))['total'];

// Pending Applications
$pendingApplicationsQuery = "SELECT COUNT(*) as total FROM tbljobapplication WHERE APPLICATIONSTATUS = 'Pending'";
$pendingApplications = mysqli_fetch_assoc(mysqli_query($con, $pendingApplicationsQuery))['total'];

// Success Rate
$approvedApplicationsQuery = "SELECT COUNT(*) as total FROM tbljobapplication WHERE APPLICATIONSTATUS = 'Approved'";
$approvedApplications = mysqli_fetch_assoc(mysqli_query($con, $approvedApplicationsQuery))['total'];
$successRate = $totalApplications > 0 ? round(($approvedApplications / $totalApplications) * 100, 1) : 0;

// ==================== APPLICATIONS OVER TIME ====================
$applicationsTimeQuery = "SELECT DATE(DATEAPPLIED) as date, COUNT(*) as count 
                          FROM tbljobapplication 
                          WHERE DATE(DATEAPPLIED) BETWEEN ? AND ?
                          GROUP BY DATE(DATEAPPLIED)
                          ORDER BY date ASC";
$applicationsTimeStmt = mysqli_prepare($con, $applicationsTimeQuery);
mysqli_stmt_bind_param($applicationsTimeStmt, "ss", $from_date, $to_date);
mysqli_stmt_execute($applicationsTimeStmt);
$applicationsTimeResult = mysqli_stmt_get_result($applicationsTimeStmt);

$applicationsDates = [];
$applicationsCounts = [];
while ($row = mysqli_fetch_assoc($applicationsTimeResult)) {
    $applicationsDates[] = date('M d', strtotime($row['date']));
    $applicationsCounts[] = $row['count'];
}

// ==================== JOB POSTINGS BY CATEGORY ====================
$jobsCategoryQuery = "SELECT JOBCATEGORYID, COUNT(*) as count 
                      FROM tbljob 
                      WHERE JOBCATEGORYID IS NOT NULL AND JOBCATEGORYID != ''
                      GROUP BY JOBCATEGORYID 
                      ORDER BY count DESC 
                      LIMIT 10";
$jobsCategoryResult = mysqli_query($con, $jobsCategoryQuery);

$jobCategories = [];
$jobCategoryCounts = [];
while ($row = mysqli_fetch_assoc($jobsCategoryResult)) {
    $jobCategories[] = $row['JOBCATEGORYID'];
    $jobCategoryCounts[] = $row['count'];
}

// ==================== USER REGISTRATIONS TREND ====================
$userRegistrationsQuery = "SELECT DATE(DATECREATED) as date, COUNT(*) as count 
                           FROM tblusers 
                           WHERE DATE(DATECREATED) BETWEEN ? AND ? AND ROLE != 'Admin'
                           GROUP BY DATE(DATECREATED)
                           ORDER BY date ASC";
$userRegistrationsStmt = mysqli_prepare($con, $userRegistrationsQuery);
mysqli_stmt_bind_param($userRegistrationsStmt, "ss", $from_date, $to_date);
mysqli_stmt_execute($userRegistrationsStmt);
$userRegistrationsResult = mysqli_stmt_get_result($userRegistrationsStmt);

$userRegDates = [];
$userRegCounts = [];
while ($row = mysqli_fetch_assoc($userRegistrationsResult)) {
    $userRegDates[] = date('M d', strtotime($row['date']));
    $userRegCounts[] = $row['count'];
}

// ==================== APPLICATION STATUS DISTRIBUTION ====================
$appStatusQuery = "SELECT APPLICATIONSTATUS, COUNT(*) as count 
                   FROM tbljobapplication 
                   GROUP BY APPLICATIONSTATUS";
$appStatusResult = mysqli_query($con, $appStatusQuery);

$appStatuses = [];
$appStatusCounts = [];
while ($row = mysqli_fetch_assoc($appStatusResult)) {
    $appStatuses[] = $row['APPLICATIONSTATUS'];
    $appStatusCounts[] = $row['count'];
}

// ==================== TOP COMPANIES BY APPLICATIONS ====================
$topCompaniesQuery = "SELECT c.COMPANYNAME, COUNT(ja.ID) as app_count 
                      FROM tblcompany c
                      LEFT JOIN tbljob j ON c.COMPANYID = j.COMPANYID
                      LEFT JOIN tbljobapplication ja ON j.JOBID = ja.JOBID
                      GROUP BY c.COMPANYID, c.COMPANYNAME
                      ORDER BY app_count DESC
                      LIMIT 10";
$topCompaniesResult = mysqli_query($con, $topCompaniesQuery);

// ==================== TOP JOBS BY APPLICATIONS ====================
$topJobsQuery = "SELECT j.JOBTITLE, c.COMPANYNAME, COUNT(ja.ID) as app_count 
                 FROM tbljob j
                 LEFT JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
                 LEFT JOIN tbljobapplication ja ON j.JOBID = ja.JOBID
                 GROUP BY j.JOBID, j.JOBTITLE, c.COMPANYNAME
                 ORDER BY app_count DESC
                 LIMIT 10";
$topJobsResult = mysqli_query($con, $topJobsQuery);

// ==================== RECENT ACTIVITIES ====================
$recentActivitiesQuery = "SELECT * FROM tbl_activity_log 
                          ORDER BY CREATED_AT DESC 
                          LIMIT 15";
$recentActivitiesResult = mysqli_query($con, $recentActivitiesQuery);

// ==================== JOB STATUS DISTRIBUTION ====================
$jobStatusQuery = "SELECT JOBSTATUS, COUNT(*) as count 
                   FROM tbljob 
                   GROUP BY JOBSTATUS";
$jobStatusResult = mysqli_query($con, $jobStatusQuery);

$jobStatuses = [];
$jobStatusCounts = [];
while ($row = mysqli_fetch_assoc($jobStatusResult)) {
    $jobStatuses[] = $row['JOBSTATUS'];
    $jobStatusCounts[] = $row['count'];
}

// ==================== EMPLOYMENT TYPE DISTRIBUTION ====================
$employmentTypeQuery = "SELECT JOBTYPE, COUNT(*) as count 
                        FROM tbljob 
                        WHERE JOBTYPE IS NOT NULL AND JOBTYPE != ''
                        GROUP BY JOBTYPE";
$employmentTypeResult = mysqli_query($con, $employmentTypeQuery);

$employmentTypes = [];
$employmentTypeCounts = [];
while ($row = mysqli_fetch_assoc($employmentTypeResult)) {
    $employmentTypes[] = $row['JOBTYPE'];
    $employmentTypeCounts[] = $row['count'];
}
?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Analytics Dashboard - Admin | MUNext</title>
    <link href="assets/css/styles.css" rel="stylesheet">
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
                        <i class="lni lni-bar-chart mr-2"></i>Analytics Dashboard
                    </h1>
                    <p class="page-subtitle text-dark">
                        Comprehensive platform analytics and insights
                    </p>
                </div>

                <div class="container-fluid">
                    <!-- Date Filter -->
                    <div class="date-filter-section">
                        <form method="GET" action="" id="dateFilterForm">
                            <div class="filter-buttons">
                                <button type="submit" name="range" value="7"
                                    class="filter-btn <?php echo $date_range == '7' ? 'active' : ''; ?>">
                                    Last 7 Days
                                </button>
                                <button type="submit" name="range" value="30"
                                    class="filter-btn <?php echo $date_range == '30' ? 'active' : ''; ?>">
                                    Last 30 Days
                                </button>
                                <button type="submit" name="range" value="90"
                                    class="filter-btn <?php echo $date_range == '90' ? 'active' : ''; ?>">
                                    Last 90 Days
                                </button>

                                <div class="custom-date-inputs">
                                    <input type="date" name="custom_from" value="<?php echo $custom_from; ?>"
                                        placeholder="From">
                                    <input type="date" name="custom_to" value="<?php echo $custom_to; ?>"
                                        placeholder="To">
                                    <button type="submit" name="range" value="custom" class="filter-btn">
                                        Apply Custom
                                    </button>
                                </div>

                                <button type="button" class="export-btn" onclick="exportData()">
                                    <i class="lni lni-download mr-1"></i>Export Report
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Overview Statistics -->
                    <div class="stats-grid">
                        <div class="stat-card users">
                            <div class="stat-icon">
                                <i class="lni lni-users"></i>
                            </div>
                            <span class="stat-number"><?php echo number_format($totalUsers); ?></span>
                            <span class="stat-label">Total Users</span>
                        </div>

                        <div class="stat-card jobs">
                            <div class="stat-icon">
                                <i class="lni lni-briefcase"></i>
                            </div>
                            <span class="stat-number"><?php echo number_format($totalJobs); ?></span>
                            <span class="stat-label">Total Jobs</span>
                        </div>

                        <div class="stat-card applications">
                            <div class="stat-icon">
                                <i class="lni lni-clipboard"></i>
                            </div>
                            <span class="stat-number"><?php echo number_format($totalApplications); ?></span>
                            <span class="stat-label">Total Applications</span>
                        </div>

                        <div class="stat-card active">
                            <div class="stat-icon">
                                <i class="lni lni-checkmark-circle"></i>
                            </div>
                            <span class="stat-number"><?php echo number_format($activeJobs); ?></span>
                            <span class="stat-label">Active Jobs</span>
                        </div>

                        <div class="stat-card companies">
                            <div class="stat-icon">
                                <i class="lni lni-apartment"></i>
                            </div>
                            <span class="stat-number"><?php echo number_format($totalCompanies); ?></span>
                            <span class="stat-label">Total Companies</span>
                        </div>

                        <div class="stat-card applicants">
                            <div class="stat-icon">
                                <i class="lni lni-user"></i>
                            </div>
                            <span class="stat-number"><?php echo number_format($totalApplicants); ?></span>
                            <span class="stat-label">Job Seekers</span>
                        </div>

                        <div class="stat-card pending">
                            <div class="stat-icon">
                                <i class="lni lni-hourglass"></i>
                            </div>
                            <span class="stat-number"><?php echo number_format($pendingApplications); ?></span>
                            <span class="stat-label">Pending Review</span>
                        </div>

                        <div class="stat-card success">
                            <div class="stat-icon">
                                <i class="lni lni-graph"></i>
                            </div>
                            <span class="stat-number"><?php echo $successRate; ?>%</span>
                            <span class="stat-label">Success Rate</span>
                        </div>
                    </div>

                    <!-- Charts Grid -->
                    <div class="chart-grid">
                        <!-- Applications Over Time -->
                        <div class="chart-card full-width">
                            <div class="chart-header">
                                <h6 class="chart-title">Applications Over Time</h6>
                                <p class="chart-subtitle">
                                    Daily application submissions from
                                    <?php echo date('M d, Y', strtotime($from_date)); ?>
                                    to <?php echo date('M d, Y', strtotime($to_date)); ?>
                                </p>
                            </div>
                            <div class="chart-container">
                                <canvas id="applicationsChart"></canvas>
                            </div>
                        </div>

                        <!-- Job Postings by Category -->
                        <div class="chart-card">
                            <div class="chart-header">
                                <h6 class="chart-title">Jobs by Category</h6>
                                <p class="chart-subtitle">Top 10 job categories</p>
                            </div>
                            <div class="chart-container">
                                <canvas id="categoriesChart"></canvas>
                            </div>
                        </div>

                        <!-- Application Status Distribution -->
                        <div class="chart-card">
                            <div class="chart-header">
                                <h6 class="chart-title">Application Status</h6>
                                <p class="chart-subtitle">Distribution by status</p>
                            </div>
                            <div class="chart-container">
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>

                        <!-- User Registrations Trend -->
                        <div class="chart-card">
                            <div class="chart-header">
                                <h6 class="chart-title">User Registrations</h6>
                                <p class="chart-subtitle">New user sign-ups trend</p>
                            </div>
                            <div class="chart-container">
                                <canvas id="registrationsChart"></canvas>
                            </div>
                        </div>

                        <!-- Job Status Distribution -->
                        <div class="chart-card">
                            <div class="chart-header">
                                <h6 class="chart-title">Job Status Distribution</h6>
                                <p class="chart-subtitle">Jobs by status</p>
                            </div>
                            <div class="chart-container">
                                <canvas id="jobStatusChart"></canvas>
                            </div>
                        </div>

                        <!-- Employment Type Distribution -->
                        <div class="chart-card">
                            <div class="chart-header">
                                <h6 class="chart-title">Employment Types</h6>
                                <p class="chart-subtitle">Jobs by employment type</p>
                            </div>
                            <div class="chart-container small">
                                <canvas id="employmentTypeChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Tables Section -->
                    <div class="row">
                        <!-- Top Companies -->
                        <div class="col-md-6">
                            <div class="table-card">
                                <div class="table-header">
                                    <h6 class="table-title text-white">
                                        <i class="lni lni-apartment mr-2"></i>Top Companies by Applications
                                    </h6>
                                </div>
                                <div class="table-responsive">
                                    <table class="analytics-table">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>Company Name</th>
                                                <th>Applications</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $rank = 1;
                                            mysqli_data_seek($topCompaniesResult, 0);
                                            while ($company = mysqli_fetch_assoc($topCompaniesResult)): 
                                                $rankClass = $rank <= 3 ? "rank-$rank" : "rank-other";
                                            ?>
                                            <tr>
                                                <td>
                                                    <span class="rank-badge <?php echo $rankClass; ?>">
                                                        <?php echo $rank; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo htmlspecialchars($company['COMPANYNAME']); ?></td>
                                                <td>
                                                    <strong><?php echo number_format($company['app_count']); ?></strong>
                                                </td>
                                            </tr>
                                            <?php 
                                            $rank++;
                                            endwhile; 
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Top Jobs -->
                        <div class="col-md-6">
                            <div class="table-card">
                                <div class="table-header">
                                    <h6 class="table-title text-white">
                                        <i class="lni lni-briefcase mr-2"></i>Top Jobs by Applications
                                    </h6>
                                </div>
                                <div class="table-responsive">
                                    <table class="analytics-table">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>Job Title</th>
                                                <th>Applications</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $rank = 1;
                                            mysqli_data_seek($topJobsResult, 0);
                                            while ($job = mysqli_fetch_assoc($topJobsResult)): 
                                                $rankClass = $rank <= 3 ? "rank-$rank" : "rank-other";
                                            ?>
                                            <tr>
                                                <td>
                                                    <span class="rank-badge <?php echo $rankClass; ?>">
                                                        <?php echo $rank; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($job['JOBTITLE']); ?></strong>
                                                    <br>
                                                    <small style="color: #6c757d;">
                                                        <?php echo htmlspecialchars($job['COMPANYNAME']); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <strong><?php echo number_format($job['app_count']); ?></strong>
                                                </td>
                                            </tr>
                                            <?php 
                                            $rank++;
                                            endwhile; 
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="table-card">
                        <div class="table-header">
                            <h6 class="table-title text-white">
                                <i class="lni lni-pulse mr-2"></i>Recent Admin Activity
                            </h6>
                        </div>
                        <div class="activity-feed">
                            <?php 
                            mysqli_data_seek($recentActivitiesResult, 0);
                            while ($activity = mysqli_fetch_assoc($recentActivitiesResult)): 
                            ?>
                            <div class="activity-item">
                                <div class="activity-action">
                                    <?php echo htmlspecialchars($activity['ACTION']); ?>
                                </div>
                                <div class="activity-description">
                                    <?php echo htmlspecialchars($activity['DESCRIPTION']); ?>
                                </div>
                                <div class="activity-meta">
                                    <span class="activity-time">
                                        <i class="lni lni-timer"></i>
                                        <?php 
                                        $timestamp = strtotime($activity['CREATED_AT']);
                                        $now = time();
                                        $diff = $now - $timestamp;
                                        
                                        if ($diff < 60) {
                                            echo 'Just now';
                                        } elseif ($diff < 3600) {
                                            echo floor($diff / 60) . ' minutes ago';
                                        } elseif ($diff < 86400) {
                                            echo floor($diff / 3600) . ' hours ago';
                                        } else {
                                            echo date('M d, Y h:i A', $timestamp);
                                        }
                                        ?>
                                    </span>
                                    <span>
                                        <i class="lni lni-map-marker"></i>
                                        <?php echo htmlspecialchars($activity['IP_ADDRESS']); ?>
                                    </span>
                                </div>
                            </div>
                            <?php endwhile; ?>
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
    // Chart.js default configuration
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#6c757d';

    // Applications Over Time Chart
    const applicationsCtx = document.getElementById('applicationsChart').getContext('2d');
    const applicationsChart = new Chart(applicationsCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($applicationsDates); ?>,
            datasets: [{
                label: 'Applications',
                data: <?php echo json_encode($applicationsCounts); ?>,
                borderColor: '#862633',
                backgroundColor: 'rgba(134, 38, 51, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#862633',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#862633',
                    borderWidth: 1
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        precision: 0
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Job Categories Chart
    const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
    const categoriesChart = new Chart(categoriesCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($jobCategories); ?>,
            datasets: [{
                label: 'Jobs',
                data: <?php echo json_encode($jobCategoryCounts); ?>,
                backgroundColor: [
                    '#862633', '#3498db', '#e74c3c', '#f39c12',
                    '#9b59b6', '#1abc9c', '#34495e', '#16a085',
                    '#d35400', '#c0392b'
                ],
                borderRadius: 8,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        precision: 0
                    }
                },
                y: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Application Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($appStatuses); ?>,
            datasets: [{
                data: <?php echo json_encode($appStatusCounts); ?>,
                backgroundColor: [
                    '#ffc107', '#28a745', '#dc3545', '#6c757d'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                }
            }
        }
    });

    // User Registrations Chart
    const registrationsCtx = document.getElementById('registrationsChart').getContext('2d');
    const registrationsChart = new Chart(registrationsCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($userRegDates); ?>,
            datasets: [{
                label: 'New Users',
                data: <?php echo json_encode($userRegCounts); ?>,
                borderColor: '#3498db',
                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#3498db',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
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
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        precision: 0
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Job Status Chart
    const jobStatusCtx = document.getElementById('jobStatusChart').getContext('2d');
    const jobStatusChart = new Chart(jobStatusCtx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($jobStatuses); ?>,
            datasets: [{
                data: <?php echo json_encode($jobStatusCounts); ?>,
                backgroundColor: [
                    '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6c757d'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                }
            }
        }
    });

    // Employment Type Chart
    const employmentTypeCtx = document.getElementById('employmentTypeChart').getContext('2d');
    const employmentTypeChart = new Chart(employmentTypeCtx, {
        type: 'polarArea',
        data: {
            labels: <?php echo json_encode($employmentTypes); ?>,
            datasets: [{
                data: <?php echo json_encode($employmentTypeCounts); ?>,
                backgroundColor: [
                    'rgba(134, 38, 51, 0.7)',
                    'rgba(52, 152, 219, 0.7)',
                    'rgba(231, 76, 60, 0.7)',
                    'rgba(243, 156, 18, 0.7)'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                }
            }
        }
    });

    // Export Data Function
    function exportData() {
        alert('Export functionality would generate a PDF/Excel report with all analytics data.');
        // In production, this would call a backend endpoint to generate the report
    }
    </script>

    <?php echo Toast::render(); ?>
</body>

</html>