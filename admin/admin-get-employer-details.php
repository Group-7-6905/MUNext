<?php
require 'include/phpcode.php';

// ==================== GET USER ID ====================
$userid = isset($_GET['userid']) ? (int)$_GET['userid'] : 0;

if ($userid <= 0) {
    echo '<div class="alert alert-danger">Invalid user ID.</div>';
    exit();
}

// ==================== FETCH EMPLOYER DETAILS WITH COMPANY INFO ====================
$employerQuery = "SELECT u.*, 
                  c.COMPANYID, c.COMPANYNAME, c.COMPANYEMAIL, c.COMPANYCONTACTNO, 
                  c.COMPANYLOGO, c.COMPANYWEBSITE, c.COMPANYADDRESS, 
                  c.COMPANYABOUT, c.COMPANYSTATUS, c.DATEREGISTERED as COMPANY_REGISTERED
                  FROM tblusers u 
                  LEFT JOIN tblcompany c ON u.USERID = c.USERID
                  WHERE u.USERID = ? AND u.ROLE = 'Employer'";
$employerStmt = mysqli_prepare($con, $employerQuery);
mysqli_stmt_bind_param($employerStmt, "i", $userid);
mysqli_stmt_execute($employerStmt);
$employerResult = mysqli_stmt_get_result($employerStmt);

if (mysqli_num_rows($employerResult) == 0) {
    echo '<div class="alert alert-danger">Employer not found.</div>';
    exit();
}

$employer = mysqli_fetch_assoc($employerResult);
$hasCompanyProfile = !empty($employer['COMPANYID']);

// ==================== GET JOB STATISTICS ====================
$totalJobsQuery = "SELECT COUNT(*) as total FROM tbljob WHERE EMPLOYERID = ?";
$totalJobsStmt = mysqli_prepare($con, $totalJobsQuery);
mysqli_stmt_bind_param($totalJobsStmt, "i", $userid);
mysqli_stmt_execute($totalJobsStmt);
$totalJobs = mysqli_fetch_assoc(mysqli_stmt_get_result($totalJobsStmt))['total'];

$activeJobsQuery = "SELECT COUNT(*) as total FROM tbljob WHERE EMPLOYERID = ? AND JOBSTATUS = 'Active'";
$activeJobsStmt = mysqli_prepare($con, $activeJobsQuery);
mysqli_stmt_bind_param($activeJobsStmt, "i", $userid);
mysqli_stmt_execute($activeJobsStmt);
$activeJobs = mysqli_fetch_assoc(mysqli_stmt_get_result($activeJobsStmt))['total'];

$pendingJobsQuery = "SELECT COUNT(*) as total FROM tbljob WHERE EMPLOYERID = ? AND JOBSTATUS = 'Pending'";
$pendingJobsStmt = mysqli_prepare($con, $pendingJobsQuery);
mysqli_stmt_bind_param($pendingJobsStmt, "i", $userid);
mysqli_stmt_execute($pendingJobsStmt);
$pendingJobs = mysqli_fetch_assoc(mysqli_stmt_get_result($pendingJobsStmt))['total'];

// ==================== GET APPLICATION STATISTICS ====================
$totalApplicationsQuery = "SELECT COUNT(*) as total 
                          FROM tbljobapplication ja 
                          LEFT JOIN tbljob j ON ja.JOBID = j.JOBID 
                          WHERE j.EMPLOYERID = ?";
$totalApplicationsStmt = mysqli_prepare($con, $totalApplicationsQuery);
mysqli_stmt_bind_param($totalApplicationsStmt, "i", $userid);
mysqli_stmt_execute($totalApplicationsStmt);
$totalApplications = mysqli_fetch_assoc(mysqli_stmt_get_result($totalApplicationsStmt))['total'];

// ==================== GET RECENT JOBS ====================
$recentJobsQuery = "SELECT j.*, 
                    (SELECT COUNT(*) FROM tbljobapplication WHERE JOBID = j.JOBID) as application_count
                    FROM tbljob j 
                    WHERE j.EMPLOYERID = ? 
                    ORDER BY j.DATEPOSTED DESC 
                    LIMIT 5";
$recentJobsStmt = mysqli_prepare($con, $recentJobsQuery);
mysqli_stmt_bind_param($recentJobsStmt, "i", $userid);
mysqli_stmt_execute($recentJobsStmt);
$recentJobs = mysqli_stmt_get_result($recentJobsStmt);

// ==================== GET LOGIN HISTORY ====================
$loginHistoryQuery = "SELECT * FROM tbl_login_history 
                     WHERE USERID = ? 
                     ORDER BY LOGIN_TIME DESC 
                     LIMIT 5";
$loginHistoryStmt = mysqli_prepare($con, $loginHistoryQuery);
mysqli_stmt_bind_param($loginHistoryStmt, "i", $userid);
mysqli_stmt_execute($loginHistoryStmt);
$loginHistory = mysqli_stmt_get_result($loginHistoryStmt);
?>

<style>
.employer-detail-section {
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

.status-inactive {
    background: #e2e3e5;
    color: #383d41;
}

.employer-profile-header {
    text-align: center;
    padding: 20px;
    background: linear-gradient(135deg, #862633 0%, #6b1e28 100%);
    color: white;
    border-radius: 8px;
    margin-bottom: 25px;
}

.employer-profile-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: 700;
    margin: 0 auto 15px;
    border: 4px solid rgba(255, 255, 255, 0.3);
}

.employer-profile-name {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.employer-profile-email {
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

.company-info-box-details {
    background: #f8f9fa;
    border-left: 3px solid #862633;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.company-name-details {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 5px;
}

.company-details-details {
    color: #6c757d;
    font-size: 0.9rem;
}

.alert-info-custom-details {
    background: #e7f3ff;
    border-left: 4px solid #2196F3;
    padding: 15px;
    border-radius: 5px;
    color: #0c5460;
}

.company-logo-display-details {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    object-fit: cover;
    margin-right: 15px;
    border: 2px solid #e9ecef;
    float: left;
}

.company-logo-placeholder-display-details {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    background: #862633;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 700;
    margin-right: 15px;
    float: left;
}
</style>

<!-- Employer Profile Header -->
<div class="employer-profile-header">
    <div class="employer-profile-avatar">
        <?php echo strtoupper(substr($employer['FNAME'], 0, 1) . substr($employer['ONAME'], 0, 1)); ?>
    </div>
    <div class="employer-profile-name">
        <?php echo htmlspecialchars($employer['FNAME'] . ' ' . $employer['ONAME']); ?>
    </div>
    <div class="employer-profile-email">
        <i class="lni lni-envelope"></i> <?php echo htmlspecialchars($employer['EMAIL']); ?>
    </div>
    <div style="margin-top: 10px;">
        <?php
        $statusClass = 'status-active';
        $status = $employer['USERSTATUS'];
        if ($status == 'Pending') {
            $statusClass = 'status-pending';
        } elseif ($status == 'Suspended') {
            $statusClass = 'status-suspended';
        } elseif ($status == 'Inactive') {
            $statusClass = 'status-inactive';
        }
        ?>
        <span class="status-badge <?php echo $statusClass; ?>">
            <?php echo htmlspecialchars($status); ?>
        </span>
    </div>
</div>

<!-- Company Profile Information -->
<?php if ($hasCompanyProfile): ?>
<div class="employer-detail-section">
    <h6 class="section-title">
        <i class="fa fa-building mr-2"></i>Company Profile
    </h6>
    <div class="company-info-box-details">
        <?php if (!empty($employer['COMPANYLOGO'])): ?>
        <img src="<?php echo $path.htmlspecialchars($employer['COMPANYLOGO']); ?>" alt="Logo"
            class="company-logo-display-details"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <?php endif; ?>
        <div class="company-logo-placeholder-display-details"
            style="<?php echo !empty($employer['COMPANYLOGO']) ? 'display:none;' : ''; ?>">
            <?php echo strtoupper(substr($employer['COMPANYNAME'], 0, 1)); ?>
        </div>

        <div class="company-name-details">
            <?php echo htmlspecialchars($employer['COMPANYNAME']); ?>
            <?php
            $companyStatusClass = 'status-secondary';
            $companyStatus = $employer['COMPANYSTATUS'];
            if ($companyStatus == 'Pending') {
                $companyStatusClass = 'status-pending';
            } elseif ($companyStatus == 'Suspended') {
                $companyStatusClass = 'status-suspended';
            }elseif ($companyStatus == 'Active') {
                $companyStatusClass = 'status-active';
            } elseif ($companyStatus == 'Rejected') {
                $companyStatusClass = 'bg-danger text-light';
            }elseif ($companyStatus == 'Info Required') {
                $companyStatusClass = 'badge-info';
            }
            ?>
            <span class="status-badge <?php echo $companyStatusClass; ?>" style="margin-left: 10px;">
                <?php echo htmlspecialchars($companyStatus); ?>
            </span>
        </div>
        <div class="company-details-details">
            <?php if (!empty($employer['COMPANYEMAIL'])): ?>
            <i class="lni lni-envelope mr-1"></i>
            <?php echo htmlspecialchars($employer['COMPANYEMAIL']); ?>
            <br>
            <?php endif; ?>

            <?php if (!empty($employer['COMPANYCONTACTNO'])): ?>
            <i class="lni lni-phone mr-1"></i>
            <?php echo htmlspecialchars($employer['COMPANYCONTACTNO']); ?>
            <br>
            <?php endif; ?>

            <?php if (!empty($employer['COMPANYWEBSITE'])): ?>
            <i class="lni lni-world mr-1"></i>
            <a href="<?php echo htmlspecialchars($employer['COMPANYWEBSITE']); ?>" target="_blank">
                <?php echo htmlspecialchars($employer['COMPANYWEBSITE']); ?>
            </a>
            <br>
            <?php endif; ?>

            <?php if (!empty($employer['COMPANYADDRESS'])): ?>
            <i class="lni lni-map-marker mr-1"></i>
            <?php echo htmlspecialchars($employer['COMPANYADDRESS']); ?>
            <br>
            <?php endif; ?>

            <?php if (!empty($employer['COMPANY_REGISTERED'])): ?>
            <i class="lni lni-calendar mr-1"></i>
            Registered: <?php echo date('F d, Y', strtotime($employer['COMPANY_REGISTERED'])); ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($employer['COMPANYABOUT'])): ?>
    <div style="margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
        <strong>About Company:</strong>
        <p style="margin-top: 10px; margin-bottom: 0;">
            <?php echo nl2br(htmlspecialchars($employer['COMPANYABOUT'])); ?>
        </p>
    </div>
    <?php endif; ?>
</div>
<?php else: ?>
<div class="employer-detail-section">
    <div class="alert-info-custom-details">
        <i class="lni lni-information mr-2"></i>
        <strong>No Company Profile:</strong> This employer has not created a company profile yet.
    </div>
</div>
<?php endif; ?>

<!-- Job Statistics -->
<div class="employer-detail-section">
    <h6 class="section-title">
        <i class="lni lni-bar-chart mr-2"></i>Job Statistics
    </h6>
    <div class="stats-mini-grid">
        <div class="stat-mini-card">
            <span class="stat-mini-number"><?php echo number_format($totalJobs); ?></span>
            <span class="stat-mini-label">Total Jobs Posted</span>
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

<!-- Account Information -->
<div class="employer-detail-section">
    <h6 class="section-title">
        <i class="lni lni-user mr-2"></i>Account Information
    </h6>

    <div class="detail-row">
        <div class="detail-label">User ID:</div>
        <div class="detail-value"><?php echo $employer['USERID']; ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Full Name:</div>
        <div class="detail-value">
            <?php echo htmlspecialchars($employer['FNAME'] . ' ' . $employer['ONAME']); ?>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Username:</div>
        <div class="detail-value">@<?php echo htmlspecialchars($employer['USERNAME']); ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Email Address:</div>
        <div class="detail-value">
            <a href="mailto:<?php echo htmlspecialchars($employer['EMAIL']); ?>">
                <?php echo htmlspecialchars($employer['EMAIL']); ?>
            </a>
        </div>
    </div>

    <?php if (!empty($employer['PHONE'])): ?>
    <div class="detail-row">
        <div class="detail-label">Phone Number:</div>
        <div class="detail-value"><?php echo htmlspecialchars($employer['PHONE']); ?></div>
    </div>
    <?php endif; ?>

    <div class="detail-row">
        <div class="detail-label">Account Status:</div>
        <div class="detail-value">
            <span class="status-badge <?php echo $statusClass; ?>">
                <?php echo htmlspecialchars($employer['USERSTATUS']); ?>
            </span>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Role:</div>
        <div class="detail-value">
            <span class="badge badge-primary"><?php echo htmlspecialchars($employer['ROLE']); ?></span>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Account Created:</div>
        <div class="detail-value">
            <?php echo date('F d, Y h:i A', strtotime($employer['DATECREATED'])); ?>
        </div>
    </div>

    <?php if (!empty($employer['LASTLOGIN'])): ?>
    <div class="detail-row">
        <div class="detail-label">Last Login:</div>
        <div class="detail-value">
            <?php echo date('F d, Y h:i A', strtotime($employer['LASTLOGIN'])); ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Recent Jobs -->
<div class="employer-detail-section">
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
                        // $jobStatusClass = 'status-active';
                        // $jobStatus = $job['JOBSTATUS'];
                        // if ($jobStatus == 'Pending') {
                        //     $jobStatusClass = 'status-pending';
                        // } elseif ($jobStatus == 'Suspended' || $jobStatus == 'Inactive') {
                        //     $jobStatusClass = 'status-suspended';
                        // }

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

<!-- Login History -->
<?php if (mysqli_num_rows($loginHistory) > 0): ?>
<div class="employer-detail-section">
    <h6 class="section-title">
        <i class="lni lni-lock mr-2"></i>Recent Login History
    </h6>
    <div class="table-responsive">
        <table class="jobs-table">
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>IP Address</th>
                    <th>Status</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($login = mysqli_fetch_assoc($loginHistory)): ?>
                <tr>
                    <td><?php echo date('M d, Y h:i A', strtotime($login['LOGIN_TIME'])); ?></td>
                    <td><?php echo htmlspecialchars($login['IP_ADDRESS'] ?? 'N/A'); ?></td>
                    <td>
                        <?php
                        $loginStatusClass = 'status-active';
                        $loginStatus = $login['LOGIN_STATUS'];
                        if ($loginStatus == 'Failed') {
                            $loginStatusClass = 'status-suspended';
                        } elseif ($loginStatus == 'Blocked') {
                            $loginStatusClass = 'status-suspended';
                        } elseif ($loginStatus == 'Success') {
                            $loginStatusClass = 'status-active';
                        }
                        ?>
                        <span class="status-badge <?php echo $loginStatusClass; ?>">
                            <?php echo htmlspecialchars($loginStatus); ?>
                        </span>
                    </td>
                    <td>
                        <small class="text-muted">
                            <?php echo htmlspecialchars($login['NOTE'] ?? '-'); ?>
                        </small>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Action Buttons -->
<div class="employer-detail-section" style="border-top: 2px solid #e9ecef; padding-top: 20px;">
    <div class="row">
        <?php if (!empty($employer['COMPANYID'])):?>
        <div class="col-md-6 mb-2">
            <a href="admin-all-jobs.php?company=<?php echo $employer['COMPANYID']; ?>"
                class="btn btn-outline-secondary btn-block rounded">
                <i class="lni lni-briefcase mr-2"></i>View All Jobs
            </a>
        </div>

        <div class="col-md-6 mb-2">
            <a href="admin-all-applications.php?company=<?php echo $employer['COMPANYID']; ?>"
                class="btn btn-outline-info btn-block rounded">
                <i class="lni lni-files mr-2"></i>View All Applications
            </a>
        </div>
        <?php endif?>
    </div>
</div>