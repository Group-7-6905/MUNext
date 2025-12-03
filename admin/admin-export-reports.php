<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';
require '../include/toast.php';

// ==================== HANDLE EXPORT REQUEST ====================
if (isset($_POST['export_data'])) {
    $export_type = mysqli_real_escape_string($con, $_POST['export_type']);
    $export_format = mysqli_real_escape_string($con, $_POST['export_format']);
    $date_from = mysqli_real_escape_string($con, $_POST['date_from']);
    $date_to = mysqli_real_escape_string($con, $_POST['date_to']);
    $status_filter = isset($_POST['status_filter']) ? mysqli_real_escape_string($con, $_POST['status_filter']) : '';
    
    // Set headers based on format
    if ($export_format == 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $export_type . '_export_' . date('Y-m-d') . '.csv"');
        $output = fopen('php://output', 'w');
    } elseif ($export_format == 'excel') {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $export_type . '_export_' . date('Y-m-d') . '.xls"');
        $output = fopen('php://output', 'w');
    }
    
    // Export based on type
    switch ($export_type) {
        case 'jobs':
            // Jobs Export
            fputcsv($output, ['Job ID', 'Title', 'Company', 'Category', 'Type', 'Location', 'Status', 'Salary Range', 'Applications', 'Posted Date', 'Deadline']);
            
            $query = "SELECT j.*, c.COMPANYNAME, c.COMPANYCOUNTRY,
                      (SELECT COUNT(*) FROM tbljobapplication WHERE JOBID = j.JOBID) as app_count
                      FROM tbljob j 
                      LEFT JOIN tblcompany c ON j.COMPANYID = c.COMPANYID 
                      WHERE DATE(j.DATEPOSTED) BETWEEN ? AND ?";
            
            if (!empty($status_filter)) {
                $query .= " AND j.JOBSTATUS = ?";
            }
            
            $query .= " ORDER BY j.DATEPOSTED DESC";
            
            $stmt = mysqli_prepare($con, $query);
            if (!empty($status_filter)) {
                mysqli_stmt_bind_param($stmt, "sss", $date_from, $date_to, $status_filter);
            } else {
                mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
            }
            
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            while ($row = mysqli_fetch_assoc($result)) {
                fputcsv($output, [
                    $row['JOBID'],
                    $row['JOBTITLE'],
                    $row['COMPANYNAME'],
                    $row['CATEGORY'] ?? 'N/A',
                    $row['JOBTYPE'] ?? 'N/A',
                    $row['COMPANYCOUNTRY'] ?? 'N/A',
                    $row['JOBSTATUS'],
                    ($row['SALARYRANGEFROM'] ?? '') . ' - ' . ($row['SALARYRANGETO'] ?? ''),
                    $row['app_count'],
                    date('Y-m-d', strtotime($row['DATEPOSTED'])),
                    $row['JOBDEADLINE'] ?? 'N/A'
                ]);
            }
            break;
            
        case 'applications':
            // Applications Export
            fputcsv($output, ['Application ID', 'Job Title', 'Company', 'Applicant Name', 'Email', 'Phone', 'Status', 'Applied Date', 'Resume']);
            
            $query = "SELECT ja.*, j.JOBTITLE, c.COMPANYNAME, u.FNAME, u.ONAME, u.EMAIL, u.PHONE
                      FROM tbljobapplication ja
                      INNER JOIN tbljob j ON ja.JOBID = j.JOBID
                      LEFT JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
                      INNER JOIN tblusers u ON ja.APPLICANTID = u.USERID
                      WHERE DATE(ja.DATEAPPLIED) BETWEEN ? AND ?";
            
            if (!empty($status_filter)) {
                $query .= " AND ja.STATUS = ?";
            }
            
            $query .= " ORDER BY ja.DATEAPPLIED DESC";
            
            $stmt = mysqli_prepare($con, $query);
            if (!empty($status_filter)) {
                mysqli_stmt_bind_param($stmt, "sss", $date_from, $date_to, $status_filter);
            } else {
                mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
            }
            
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            while ($row = mysqli_fetch_assoc($result)) {
                fputcsv($output, [
                    $row['ID'],
                    $row['JOBTITLE'],
                    $row['COMPANYNAME'],
                    $row['FNAME'] . ' ' . $row['ONAME'],
                    $row['EMAIL'],
                    $row['PHONE'] ?? 'N/A',
                    $row['STATUS'],
                    date('Y-m-d', strtotime($row['DATEAPPLIED'])),
                    $row['FILEDIR'] ?? 'N/A'
                ]);
            }
            break;
            
        case 'users':
            // Users Export
            fputcsv($output, ['User ID', 'Name', 'Email', 'Username', 'Phone', 'Status', 'Role', 'City', 'Country', 'Education', 'Skills', 'Registration Date']);
            
            $query = "SELECT u.*, a.CITY, a.COUNTRY, a.DEGREE, a.SKILLS, a.FNAME as APP_FNAME, a.OTHERNAMES
                      FROM tblusers u 
                      LEFT JOIN tblapplicants a ON u.USERID = a.USERID 
                      WHERE DATE(u.DATECREATED) BETWEEN ? AND ?";
            
            if (!empty($status_filter)) {
                $query .= " AND u.USERSTATUS = ?";
            }
            
            $query .= " ORDER BY u.DATECREATED DESC";
            
            $stmt = mysqli_prepare($con, $query);
            if (!empty($status_filter)) {
                mysqli_stmt_bind_param($stmt, "sss", $date_from, $date_to, $status_filter);
            } else {
                mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
            }
            
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            while ($row = mysqli_fetch_assoc($result)) {
                $name = !empty($row['APP_FNAME']) ? $row['APP_FNAME'] . ' ' . $row['OTHERNAMES'] : $row['FNAME'] . ' ' . $row['ONAME'];
                fputcsv($output, [
                    $row['USERID'],
                    $name,
                    $row['EMAIL'],
                    $row['USERNAME'],
                    $row['PHONE'] ?? 'N/A',
                    $row['USERSTATUS'],
                    $row['ROLE'],
                    $row['CITY'] ?? 'N/A',
                    $row['COUNTRY'] ?? 'N/A',
                    $row['DEGREE'] ?? 'N/A',
                    $row['SKILLS'] ?? 'N/A',
                    date('Y-m-d', strtotime($row['DATECREATED']))
                ]);
            }
            break;
            
        case 'companies':
            // Companies Export
            fputcsv($output, ['Company ID', 'Company Name', 'Email', 'Phone', 'Country', 'City', 'Address', 'Status', 'Jobs Posted', 'Registration Date']);
            
            $query = "SELECT c.*,
                      (SELECT COUNT(*) FROM tbljob WHERE COMPANYID = c.COMPANYID) as job_count
                      FROM tblcompany c 
                      WHERE DATE(c.DATECREATED) BETWEEN ? AND ?";
            
            if (!empty($status_filter)) {
                $query .= " AND c.COMPANYSTATUS = ?";
            }
            
            $query .= " ORDER BY c.DATECREATED DESC";
            
            $stmt = mysqli_prepare($con, $query);
            if (!empty($status_filter)) {
                mysqli_stmt_bind_param($stmt, "sss", $date_from, $date_to, $status_filter);
            } else {
                mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
            }
            
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            while ($row = mysqli_fetch_assoc($result)) {
                fputcsv($output, [
                    $row['COMPANYID'],
                    $row['COMPANYNAME'],
                    $row['COMPANYEMAIL'] ?? 'N/A',
                    $row['COMPANYCONTACTNO'] ?? 'N/A',
                    $row['COMPANYCOUNTRY'] ?? 'N/A',
                    $row['COMPANYCITY'] ?? 'N/A',
                    $row['COMPANYADDRESS'] ?? 'N/A',
                    $row['COMPANYSTATUS'],
                    $row['job_count'],
                    date('Y-m-d', strtotime($row['DATECREATED']))
                ]);
            }
            break;
            
        case 'analytics':
            // Analytics Summary Export
            fputcsv($output, ['Metric', 'Value']);
            
            // Get analytics data
            $totalJobs = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM tbljob WHERE DATE(DATEPOSTED) BETWEEN '$date_from' AND '$date_to'"))['total'];

            $totalApps = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM tbljobapplication WHERE DATE(DATEAPPLIED) BETWEEN '$date_from' AND '$date_to'"))['total'];

            $totalUsers = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM tblusers WHERE ROLE = 'Applicant' AND DATE(DATECREATED) BETWEEN '$date_from' AND '$date_to'"))['total'];

            $totalCompanies = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM tblcompany WHERE DATE(DATECREATED) BETWEEN '$date_from' AND '$date_to'"))['total'];

            $activeJobs = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM tbljob WHERE JOBSTATUS = 'Active' AND DATE(DATEPOSTED) BETWEEN '$date_from' AND '$date_to'"))['total'];
            
            $pendingApps = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM tbljobapplication WHERE STATUS = 'Pending' AND DATE(DATEAPPLIED) BETWEEN '$date_from' AND '$date_to'"))['total'];
            
            fputcsv($output, ['Report Period', "$date_from to $date_to"]);
            fputcsv($output, ['Total Jobs Posted', $totalJobs]);
            fputcsv($output, ['Active Jobs', $activeJobs]);
            fputcsv($output, ['Total Applications', $totalApps]);
            fputcsv($output, ['Pending Applications', $pendingApps]);
            fputcsv($output, ['New Users Registered', $totalUsers]);
            fputcsv($output, ['New Companies Registered', $totalCompanies]);
            fputcsv($output, ['Avg Applications per Job', $totalJobs > 0 ? round($totalApps / $totalJobs, 2) : 0]);
            break;
    }
    
    fclose($output);
    exit();
}
?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Export Reports - Admin Dashboard | MUNext</title>
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
                    <h1 class="page-title">
                        <i class="lni lni-download mr-2"></i>Export Reports & Data
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Reports</a></li>
                            <li class="breadcrumb-item active">Export Data</li>
                        </ol>
                    </nav>
                </div>

                <div class="container-fluid">
                    <!-- Info Section -->
                    <div class="info-section">
                        <h5 class="text-white"><i class="lni lni-information mr-2"></i>Export Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <ul>
                                    <li><i class="lni lni-checkmark"></i>Export data in CSV or Excel format</li>
                                    <li><i class="lni lni-checkmark"></i>Filter by date range and status</li>
                                    <li><i class="lni lni-checkmark"></i>Download comprehensive reports</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul>
                                    <li><i class="lni lni-checkmark"></i>Includes all relevant data fields</li>
                                    <li><i class="lni lni-checkmark"></i>Ready for analysis in spreadsheet tools</li>
                                    <li><i class="lni lni-checkmark"></i>Secure and encrypted downloads</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Export Cards -->
                    <div class="export-cards-grid">
                        <!-- Jobs Export -->
                        <div class="export-card jobs" onclick="showExportForm('jobs')">
                            <div class="export-card-icon">
                                <i class="lni lni-briefcase"></i>
                            </div>
                            <h3 class="export-card-title">Jobs Data</h3>
                            <p class="export-card-description">
                                Export all job postings with detailed information
                            </p>
                            <ul class="export-card-features">
                                <li><i class="lni lni-checkmark-circle"></i>Job details & descriptions</li>
                                <li><i class="lni lni-checkmark-circle"></i>Company information</li>
                                <li><i class="lni lni-checkmark-circle"></i>Application statistics</li>
                                <li><i class="lni lni-checkmark-circle"></i>Status & dates</li>
                            </ul>
                            <button class="export-card-button">
                                <i class="lni lni-download mr-2"></i>Export Jobs
                            </button>
                        </div>

                        <!-- Applications Export -->
                        <div class="export-card applications" onclick="showExportForm('applications')">
                            <div class="export-card-icon">
                                <i class="lni lni-files"></i>
                            </div>
                            <h3 class="export-card-title">Applications Data</h3>
                            <p class="export-card-description">
                                Export all job applications and applicant details
                            </p>
                            <ul class="export-card-features">
                                <li><i class="lni lni-checkmark-circle"></i>Applicant information</li>
                                <li><i class="lni lni-checkmark-circle"></i>Job & company details</li>
                                <li><i class="lni lni-checkmark-circle"></i>Application status</li>
                                <li><i class="lni lni-checkmark-circle"></i>Resume links</li>
                            </ul>
                            <button class="export-card-button">
                                <i class="lni lni-download mr-2"></i>Export Applications
                            </button>
                        </div>

                        <!-- Users Export -->
                        <div class="export-card users" onclick="showExportForm('users')">
                            <div class="export-card-icon">
                                <i class="lni lni-users"></i>
                            </div>
                            <h3 class="export-card-title">Users Data</h3>
                            <p class="export-card-description">
                                Export all registered users and their profiles
                            </p>
                            <ul class="export-card-features">
                                <li><i class="lni lni-checkmark-circle"></i>User credentials</li>
                                <li><i class="lni lni-checkmark-circle"></i>Profile information</li>
                                <li><i class="lni lni-checkmark-circle"></i>Skills & education</li>
                                <li><i class="lni lni-checkmark-circle"></i>Registration dates</li>
                            </ul>
                            <button class="export-card-button">
                                <i class="lni lni-download mr-2"></i>Export Users
                            </button>
                        </div>

                        <!-- Companies Export -->
                        <div class="export-card companies" onclick="showExportForm('companies')">
                            <div class="export-card-icon">
                                <i class="fa fa-building"></i>
                            </div>
                            <h3 class="export-card-title">Companies Data</h3>
                            <p class="export-card-description">
                                Export all registered companies and their details
                            </p>
                            <ul class="export-card-features">
                                <li><i class="lni lni-checkmark-circle"></i>Company profiles</li>
                                <li><i class="lni lni-checkmark-circle"></i>Contact information</li>
                                <li><i class="lni lni-checkmark-circle"></i>Job posting counts</li>
                                <li><i class="lni lni-checkmark-circle"></i>Verification status</li>
                            </ul>
                            <button class="export-card-button">
                                <i class="lni lni-download mr-2"></i>Export Companies
                            </button>
                        </div>

                        <!-- Analytics Export -->
                        <div class="export-card analytics" onclick="showExportForm('analytics')">
                            <div class="export-card-icon">
                                <i class="lni lni-bar-chart"></i>
                            </div>
                            <h3 class="export-card-title">Analytics Summary</h3>
                            <p class="export-card-description">
                                Export comprehensive analytics and statistics
                            </p>
                            <ul class="export-card-features">
                                <li><i class="lni lni-checkmark-circle"></i>Platform statistics</li>
                                <li><i class="lni lni-checkmark-circle"></i>Performance metrics</li>
                                <li><i class="lni lni-checkmark-circle"></i>Growth indicators</li>
                                <li><i class="lni lni-checkmark-circle"></i>Summary reports</li>
                            </ul>
                            <button class="export-card-button">
                                <i class="lni lni-download mr-2"></i>Export Analytics
                            </button>
                        </div>
                    </div>

                    <!-- Export Forms -->
                    <div id="jobsExportForm" class="export-form-section export-modal">
                        <div class="export-modal-header">
                            <h4 class="export-modal-title">
                                <i class="lni lni-briefcase mr-2"></i>Export Jobs Data
                            </h4>
                        </div>
                        <form method="POST" action="">
                            <input type="hidden" name="export_type" value="jobs">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">From Date *</label>
                                    <input type="date" name="date_from" class="form-control"
                                        value="<?php echo date('Y-m-01'); ?>" required>
                                    <small class="helper-text">Start date for data export</small>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">To Date *</label>
                                    <input type="date" name="date_to" class="form-control"
                                        value="<?php echo date('Y-m-d'); ?>" required>
                                    <small class="helper-text">End date for data export</small>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Status Filter</label>
                                    <select name="status_filter" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="Active">Active</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Closed">Closed</option>
                                        <option value="Suspended">Suspended</option>
                                    </select>
                                    <small class="helper-text">Optional status filter</small>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Export Format *</label>
                                    <select name="export_format" class="form-control" required>
                                        <option value="csv">CSV Format</option>
                                        <option value="excel">Excel Format (.xls)</option>
                                    </select>
                                    <small class="helper-text">Choose file format</small>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" name="export_data" class="btn-export-submit">
                                    <i class="lni lni-download mr-2"></i>Download Export
                                </button>
                                <button type="button" class="btn-cancel" onclick="hideExportForms()">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>

                    <div id="applicationsExportForm" class="export-form-section export-modal">
                        <div class="export-modal-header">
                            <h4 class="export-modal-title">
                                <i class="lni lni-files mr-2"></i>Export Applications Data
                            </h4>
                        </div>
                        <form method="POST" action="">
                            <input type="hidden" name="export_type" value="applications">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">From Date *</label>
                                    <input type="date" name="date_from" class="form-control"
                                        value="<?php echo date('Y-m-01'); ?>" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">To Date *</label>
                                    <input type="date" name="date_to" class="form-control"
                                        value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Status Filter</label>
                                    <select name="status_filter" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Approved">Approved</option>
                                        <option value="Rejected">Rejected</option>
                                        <option value="Shortlisted">Shortlisted</option>
                                        <option value="Interviewed">Interviewed</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Export Format *</label>
                                    <select name="export_format" class="form-control" required>
                                        <option value="csv">CSV Format</option>
                                        <option value="excel">Excel Format (.xls)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" name="export_data" class="btn-export-submit">
                                    <i class="lni lni-download mr-2"></i>Download Export
                                </button>
                                <button type="button" class="btn-cancel" onclick="hideExportForms()">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>

                    <div id="usersExportForm" class="export-form-section export-modal">
                        <div class="export-modal-header">
                            <h4 class="export-modal-title">
                                <i class="lni lni-users mr-2"></i>Export Users Data
                            </h4>
                        </div>
                        <form method="POST" action="">
                            <input type="hidden" name="export_type" value="users">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">From Date *</label>
                                    <input type="date" name="date_from" class="form-control"
                                        value="<?php echo date('Y-m-01'); ?>" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">To Date *</label>
                                    <input type="date" name="date_to" class="form-control"
                                        value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Status Filter</label>
                                    <select name="status_filter" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="Active">Active</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Suspended">Suspended</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Export Format *</label>
                                    <select name="export_format" class="form-control" required>
                                        <option value="csv">CSV Format</option>
                                        <option value="excel">Excel Format (.xls)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" name="export_data" class="btn-export-submit">
                                    <i class="lni lni-download mr-2"></i>Download Export
                                </button>
                                <button type="button" class="btn-cancel" onclick="hideExportForms()">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>

                    <div id="companiesExportForm" class="export-form-section export-modal">
                        <div class="export-modal-header">
                            <h4 class="export-modal-title">
                                <i class="fa fa-building mr-2"></i>Export Companies Data
                            </h4>
                        </div>
                        <form method="POST" action="">
                            <input type="hidden" name="export_type" value="companies">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">From Date *</label>
                                    <input type="date" name="date_from" class="form-control"
                                        value="<?php echo date('Y-m-01'); ?>" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">To Date *</label>
                                    <input type="date" name="date_to" class="form-control"
                                        value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Status Filter</label>
                                    <select name="status_filter" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="Active">Active</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Suspended">Suspended</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Export Format *</label>
                                    <select name="export_format" class="form-control" required>
                                        <option value="csv">CSV Format</option>
                                        <option value="excel">Excel Format (.xls)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" name="export_data" class="btn-export-submit">
                                    <i class="lni lni-download mr-2"></i>Download Export
                                </button>
                                <button type="button" class="btn-cancel" onclick="hideExportForms()">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>

                    <div id="analyticsExportForm" class="export-form-section export-modal">
                        <div class="export-modal-header">
                            <h4 class="export-modal-title">
                                <i class="lni lni-bar-chart mr-2"></i>Export Analytics Summary
                            </h4>
                        </div>
                        <form method="POST" action="">
                            <input type="hidden" name="export_type" value="analytics">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">From Date *</label>
                                    <input type="date" name="date_from" class="form-control"
                                        value="<?php echo date('Y-m-01'); ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">To Date *</label>
                                    <input type="date" name="date_to" class="form-control"
                                        value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Export Format *</label>
                                    <select name="export_format" class="form-control" required>
                                        <option value="csv">CSV Format</option>
                                        <option value="excel">Excel Format (.xls)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" name="export_data" class="btn-export-submit">
                                    <i class="lni lni-download mr-2"></i>Download Export
                                </button>
                                <button type="button" class="btn-cancel" onclick="hideExportForms()">
                                    Cancel
                                </button>
                            </div>
                        </form>
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
    function showExportForm(type) {
        // Hide all forms first
        hideExportForms();

        // Show the selected form
        document.getElementById(type + 'ExportForm').classList.add('active');

        // Scroll to form
        document.getElementById(type + 'ExportForm').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    function hideExportForms() {
        const forms = document.querySelectorAll('.export-form-section');
        forms.forEach(form => {
            form.classList.remove('active');
        });
    }
    </script>

    <?php echo Toast::render(); ?>
</body>

</html>