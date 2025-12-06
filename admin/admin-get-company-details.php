<?php
require 'include/phpcode.php';

// ==================== GET COMPANY ID ====================
$companyid = isset($_GET['companyid']) ? (int)$_GET['companyid'] : 0;

if ($companyid <= 0) {
    echo '<div class="alert alert-danger">Invalid company ID.</div>';
    exit();
}

// ==================== FETCH COMPANY DETAILS WITH EMPLOYER INFO ====================
$companyQuery = "SELECT c.*, 
                 u.USERID, u.FNAME, u.ONAME, u.EMAIL as USER_EMAIL, 
                 u.USERNAME, u.PHONE as USER_PHONE, u.USERSTATUS
                 FROM tblcompany c 
                 LEFT JOIN tblusers u ON c.USERID = u.USERID
                 WHERE c.COMPANYID = ?";
$companyStmt = mysqli_prepare($con, $companyQuery);
mysqli_stmt_bind_param($companyStmt, "i", $companyid);
mysqli_stmt_execute($companyStmt);
$companyResult = mysqli_stmt_get_result($companyStmt);

if (mysqli_num_rows($companyResult) == 0) {
    echo '<div class="alert alert-danger">Company not found.</div>';
    exit();
}

$company = mysqli_fetch_assoc($companyResult);

// ==================== GET JOB STATISTICS ====================
$totalJobsQuery = "SELECT COUNT(*) as total FROM tbljob WHERE COMPANYID = ?";
$totalJobsStmt = mysqli_prepare($con, $totalJobsQuery);
mysqli_stmt_bind_param($totalJobsStmt, "i", $companyid);
mysqli_stmt_execute($totalJobsStmt);
$totalJobs = mysqli_fetch_assoc(mysqli_stmt_get_result($totalJobsStmt))['total'];

$activeJobsQuery = "SELECT COUNT(*) as total FROM tbljob WHERE COMPANYID = ? AND JOBSTATUS = 'Active'";
$activeJobsStmt = mysqli_prepare($con, $activeJobsQuery);
mysqli_stmt_bind_param($activeJobsStmt, "i", $companyid);
mysqli_stmt_execute($activeJobsStmt);
$activeJobs = mysqli_fetch_assoc(mysqli_stmt_get_result($activeJobsStmt))['total'];

$pendingJobsQuery = "SELECT COUNT(*) as total FROM tbljob WHERE COMPANYID = ? AND JOBSTATUS = 'Pending'";
$pendingJobsStmt = mysqli_prepare($con, $pendingJobsQuery);
mysqli_stmt_bind_param($pendingJobsStmt, "i", $companyid);
mysqli_stmt_execute($pendingJobsStmt);
$pendingJobs = mysqli_fetch_assoc(mysqli_stmt_get_result($pendingJobsStmt))['total'];

// ==================== GET APPLICATION STATISTICS ====================
$totalApplicationsQuery = "SELECT COUNT(*) as total 
                          FROM tbljobapplication ja 
                          LEFT JOIN tbljob j ON ja.JOBID = j.JOBID 
                          WHERE j.COMPANYID = ?";
$totalApplicationsStmt = mysqli_prepare($con, $totalApplicationsQuery);
mysqli_stmt_bind_param($totalApplicationsStmt, "i", $companyid);
mysqli_stmt_execute($totalApplicationsStmt);
$totalApplications = mysqli_fetch_assoc(mysqli_stmt_get_result($totalApplicationsStmt))['total'];

// ==================== GET RECENT JOBS ====================
$recentJobsQuery = "SELECT j.*, 
                    (SELECT COUNT(*) FROM tbljobapplication WHERE JOBID = j.JOBID) as application_count
                    FROM tbljob j 
                    WHERE j.COMPANYID = ? 
                    ORDER BY j.DATEPOSTED DESC 
                    LIMIT 5";
$recentJobsStmt = mysqli_prepare($con, $recentJobsQuery);
mysqli_stmt_bind_param($recentJobsStmt, "i", $companyid);
mysqli_stmt_execute($recentJobsStmt);
$recentJobs = mysqli_stmt_get_result($recentJobsStmt);
?>

<style>
.company-detail-section {
    margin-bottom: 25px;
}

.section-title {
    font-size: 1rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e9ecef;
}

.detail-row {
    display: flex;
    padding: 10px 0;
    border-bottom: 1px solid #f8f9fa;
}

.detail-label {
    font-weight: 600;
    color: #6c757d;
    width: 180px;
    flex-shrink: 0;
}

.detail-value {
    color: #2c3e50;
    flex: 1;
}

.stats-mini-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-bottom: 20px;
}

.stat-mini-card {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
    border-left: 3px solid #862633;
}

.stat-mini-number {
    font-size: 1.8rem;
    font-weight: 700;
    color: #862633;
    display: block;
}

.stat-mini-label {
    color: #6c757d;
    font-size: 0.85rem;
    margin-top: 5px;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-suspended {
    background: #f8d7da;
    color: #721c24;
}

.company-profile-header {
    text-align: center;
    padding: 20px;
    background: linear-gradient(135deg, #862633 0%, #6b1e28 100%);
    color: white;
    border-radius: 8px;
    margin-bottom: 25px;
}

.company-profile-logo {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    object-fit: cover;
    margin: 0 auto 15px;
    border: 4px solid rgba(255, 255, 255, 0.3);
    background: white;
}

.company-profile-name {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.company-profile-email {
    opacity: 0.9;
    font-size: 0.9rem;
}

.jobs-table {
    width: 100%;
    font-size: 0.85rem;
}

.jobs-table th {
    background: #f8f9fa;
    padding: 8px;
    font-weight: 600;
    border-bottom: 2px solid #e9ecef;
}

.jobs-table td {
    padding: 8px;
    border-bottom: 1px solid #f8f9fa;
}

.no-data {
    text-align: center;
    padding: 20px;
    color: #6c757d;
    font-style: italic;
}

.employer-info-box {
    background: #f8f9fa;
    border-left: 3px solid #862633;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.employer-name {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 5px;
}

.employer-details {
    color: #6c757d;
    font-size: 0.9rem;
}
</style>

<!-- Company Profile Header -->
<div class="company-profile-header">

    <?php if (!empty($company['COMPANYLOGO'])): ?>
    <img src="<?php echo $path.htmlspecialchars($company['COMPANYLOGO']); ?>" alt="Logo" class="company-profile-logo"
        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
    <?php endif; ?>
    <div class="company-profile-logo"
        style="display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700; color: #862633; <?php echo !empty($company['COMPANYLOGO']) ? 'display:none;' : ''; ?>">
        <?php echo strtoupper(substr($company['COMPANYNAME'], 0, 1)); ?>
    </div>



    <div class="company-profile-name">
        <?php echo htmlspecialchars($company['COMPANYNAME']); ?>
    </div>
    <div class="company-profile-email">
        <?php echo htmlspecialchars($company['COMPANYEMAIL']); ?>
    </div>
    <div style="margin-top: 10px;">
        <?php
        $statusClass = 'status-secondary';
        $status = $company['COMPANYSTATUS'];
        if ($status == 'Pending') {
            $statusClass = 'status-pending';
        } elseif ($status == 'Suspended') {
            $statusClass = 'status-suspended';
        } elseif ($status == 'Active') {
            $statusClass = 'status-active';
        } elseif ($status == 'Rejected') {
            $statusClass = 'bg-danger text-light';
        }elseif ($status == 'Info Required') {
            $statusClass = 'badge-info';
        }
        ?>
        <span class="status-badge <?php echo $statusClass; ?>">
            <?php echo htmlspecialchars($status); ?>
        </span>
    </div>
</div>

<!-- Employer/Contact Person Information -->
<?php if (!empty($company['USERID'])): ?>
<div class="company-detail-section">
    <h6 class="section-title">
        <i class="lni lni-user mr-2"></i>Employer/Contact Person
    </h6>
    <div class="employer-info-box">
        <div class="employer-name">
            <?php echo htmlspecialchars($company['FNAME'] . ' ' . $company['ONAME']); ?>
        </div>
        <div class="employer-details">
            <i class="lni lni-envelope mr-1"></i>
            <?php echo htmlspecialchars($company['USER_EMAIL']); ?>
            <?php if (!empty($company['USER_PHONE'])): ?>
            <br>
            <i class="lni lni-phone mr-1"></i>
            <?php echo htmlspecialchars($company['USER_PHONE']); ?>
            <?php endif; ?>
            <br>
            <i class="lni lni-user mr-1"></i>
            Username: <?php echo htmlspecialchars($company['USERNAME']); ?>
            <br>
            <i class="lni lni-checkmark-circle mr-1"></i>
            Account Status:
            <?php
            $userStatusClass = 'status-active';
            $userStatus = $company['USERSTATUS'];
            if ($userStatus == 'Pending') {
                $userStatusClass = 'status-pending';
            } elseif ($userStatus == 'Suspended') {
                $userStatusClass = 'status-suspended';
            }
            ?>
            <span class="status-badge <?php echo $userStatusClass; ?>">
                <?php echo htmlspecialchars($userStatus); ?>
            </span>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Job Statistics -->
<div class="company-detail-section">
    <h6 class="section-title">
        <i class="lni lni-bar-chart mr-2"></i>Job Statistics
    </h6>
    <div class="stats-mini-grid">
        <div class="stat-mini-card">
            <span class="stat-mini-number"><?php echo number_format($totalJobs); ?></span>
            <span class="stat-mini-label">Total Jobs</span>
        </div>
        <div class="stat-mini-card">
            <span class="stat-mini-number"><?php echo number_format($activeJobs); ?></span>
            <span class="stat-mini-label">Active Jobs</span>
        </div>
        <div class="stat-mini-card">
            <span class="stat-mini-number"><?php echo number_format($pendingJobs); ?></span>
            <span class="stat-mini-label">Pending Jobs</span>
        </div>
        <div class="stat-mini-card">
            <span class="stat-mini-number"><?php echo number_format($totalApplications); ?></span>
            <span class="stat-mini-label">Total Applications</span>
        </div>
    </div>
</div>

<!-- Company Information -->
<div class="company-detail-section">
    <h6 class="section-title">
        <i class="fa fa-building mr-2"></i>Company Information
    </h6>

    <div class="detail-row">
        <div class="detail-label">Company ID:</div>
        <div class="detail-value"><?php echo $company['COMPANYID']; ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Company Name:</div>
        <div class="detail-value"><?php echo htmlspecialchars($company['COMPANYNAME']); ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Company Email:</div>
        <div class="detail-value">
            <a href="mailto:<?php echo htmlspecialchars($company['COMPANYEMAIL']); ?>">
                <?php echo htmlspecialchars($company['COMPANYEMAIL']); ?>
            </a>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Company Phone:</div>
        <div class="detail-value">
            <?php echo htmlspecialchars($company['COMPANYPHONE'] ?? 'Not provided'); ?>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Website:</div>
        <div class="detail-value">
            <?php if (!empty($company['COMPANYWEBSITE'])): ?>
            <a href="<?php echo htmlspecialchars($company['COMPANYWEBSITE']); ?>" target="_blank">
                <?php echo htmlspecialchars($company['COMPANYWEBSITE']); ?>
            </a>
            <?php else: ?>
            Not provided
            <?php endif; ?>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Address:</div>
        <div class="detail-value">
            <?php echo htmlspecialchars($company['COMPANYADDRESS'] ?? 'Not provided'); ?>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Company Status:</div>
        <div class="detail-value">
            <span class="status-badge <?php echo $statusClass; ?>">
                <?php echo htmlspecialchars($company['COMPANYSTATUS']); ?>
            </span>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Registration Date:</div>
        <div class="detail-value">
            <?php echo date('F d, Y h:i A', strtotime($company['DATEREGISTERED'])); ?>
        </div>
    </div>
</div>

<!-- Company Description -->
<?php if (!empty($company['COMPANYDESCRIPTION'])): ?>
<div class="company-detail-section">
    <h6 class="section-title">
        <i class="lni lni-text-format mr-2"></i>About Company
    </h6>
    <p><?php echo nl2br(htmlspecialchars($company['COMPANYDESCRIPTION'])); ?></p>
</div>
<?php endif; ?>

<!-- Recent Jobs -->
<div class="company-detail-section">
    <h6 class="section-title">
        <i class="lni lni-briefcase mr-2"></i>Recent Job Postings
    </h6>

    <?php if (mysqli_num_rows($recentJobs) > 0): ?>
    <div class="table-responsive">
        <table class="jobs-table">
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Location</th>
                    <th>Posted Date</th>
                    <th>Applications</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($job = mysqli_fetch_assoc($recentJobs)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($job['JOBTITLE']); ?></td>
                    <td><?php echo htmlspecialchars($job['JOBLOCATION'] ?? 'N/A'); ?></td>
                    <td><?php echo date('M d, Y', strtotime($job['DATEPOSTED'])); ?></td>
                    <td><?php echo number_format($job['application_count']); ?></td>
                    <td>
                        <?php
                        $jobStatusClass = 'status-secondary';
                        $jobStatus = $job['JOBSTATUS'];
                        if ($jobStatus == 'Pending') {
                            $statusClass = 'status-pending';
                        } elseif ($jobStatus == 'Active') {
                            $statusClass = 'status-active';
                        } elseif ($jobStatus == 'Suspended' || $jobStatus == 'Inactive') {
                            $jobStatusClass = 'status-suspended';
                        } elseif ($jobStatus == 'Rejected') {
                            $statusClass = 'bg-danger text-light';
                        }elseif ($jobStatus == 'Info Required') {
                            $statusClass = 'badge-info';
                        }
                        ?>
                        <span class="status-badge <?php echo $jobStatusClass; ?>">
                            <?php echo htmlspecialchars($jobStatus); ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="no-data">
        <i class="lni lni-inbox" style="font-size: 2rem; color: #e9ecef; display: block; margin-bottom: 10px;"></i>
        No jobs posted yet
    </div>
    <?php endif; ?>
</div>

<!-- Action Buttons -->
<div class="company-detail-section" style="border-top: 2px solid #e9ecef; padding-top: 20px;">
    <div class="row">
        <!-- <div class="col-md-6 mb-2">
            <a href="admin-company-jobs.php?companyid=<?php echo $companyid; ?>"
                class="btn btn-outline-secondary btn-block rounded">
                <i class="lni lni-briefcase mr-2"></i>View All Jobs
            </a>
        </div>
        <div class="col-md-6 mb-2">
            <a href="admin-all-applications.php?jobid=<?php echo $companyid; ?>"
                class="btn btn-outline-info btn-block rounded">
                <i class="lni lni-files mr-2"></i>View All Applications
            </a>
        </div> -->
    </div>
</div>