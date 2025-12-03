<?php
require 'include/phpcode.php';


// ==================== GET JOB ID ====================
$jobid = isset($_GET['jobid']) ? (int)$_GET['jobid'] : 0;

if ($jobid <= 0) {
    echo '<div class="alert alert-danger">Invalid job ID.</div>';
    exit();
}

// ==================== FETCH JOB DETAILS ====================
$jobQuery = "SELECT j.*, c.COMPANYNAME, c.COMPANYLOGO, c.COMPANYADDRESS, 
             c.COMPANYEMAIL, c.COMPANYCONTACTNO, c.COMPANYWEBSITE
             FROM tbljob j 
             LEFT JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
             WHERE j.JOBID = ?";
$jobStmt = mysqli_prepare($con, $jobQuery);
mysqli_stmt_bind_param($jobStmt, "i", $jobid);
mysqli_stmt_execute($jobStmt);
$jobResult = mysqli_stmt_get_result($jobStmt);

if (mysqli_num_rows($jobResult) == 0) {
    echo '<div class="alert alert-danger">Job not found.</div>';
    exit();
}

$job = mysqli_fetch_assoc($jobResult);

// ==================== GET APPLICATION STATISTICS ====================
$totalAppsQuery = "SELECT COUNT(*) as total FROM tbljobapplication WHERE JOBID = ?";
$totalAppsStmt = mysqli_prepare($con, $totalAppsQuery);
mysqli_stmt_bind_param($totalAppsStmt, "i", $jobid);
mysqli_stmt_execute($totalAppsStmt);
$totalApplications = mysqli_fetch_assoc(mysqli_stmt_get_result($totalAppsStmt))['total'];

$pendingAppsQuery = "SELECT COUNT(*) as total FROM tbljobapplication WHERE JOBID = ? AND APPLICATIONSTATUS = 'Pending'";
$pendingAppsStmt = mysqli_prepare($con, $pendingAppsQuery);
mysqli_stmt_bind_param($pendingAppsStmt, "i", $jobid);
mysqli_stmt_execute($pendingAppsStmt);
$pendingApplications = mysqli_fetch_assoc(mysqli_stmt_get_result($pendingAppsStmt))['total'];

$approvedAppsQuery = "SELECT COUNT(*) as total FROM tbljobapplication WHERE JOBID = ? AND APPLICATIONSTATUS = 'Approved'";
$approvedAppsStmt = mysqli_prepare($con, $approvedAppsQuery);
mysqli_stmt_bind_param($approvedAppsStmt, "i", $jobid);
mysqli_stmt_execute($approvedAppsStmt);
$approvedApplications = mysqli_fetch_assoc(mysqli_stmt_get_result($approvedAppsStmt))['total'];

$rejectedAppsQuery = "SELECT COUNT(*) as total FROM tbljobapplication WHERE JOBID = ? AND APPLICATIONSTATUS = 'Rejected'";
$rejectedAppsStmt = mysqli_prepare($con, $rejectedAppsQuery);
mysqli_stmt_bind_param($rejectedAppsStmt, "i", $jobid);
mysqli_stmt_execute($rejectedAppsStmt);
$rejectedApplications = mysqli_fetch_assoc(mysqli_stmt_get_result($rejectedAppsStmt))['total'];

// ==================== GET BOOKMARKS COUNT ====================
$bookmarksQuery = "SELECT COUNT(*) as total FROM tblbookmarkjob WHERE JOBID = ?";
$bookmarksStmt = mysqli_prepare($con, $bookmarksQuery);
mysqli_stmt_bind_param($bookmarksStmt, "i", $jobid);
mysqli_stmt_execute($bookmarksStmt);
$totalBookmarks = mysqli_fetch_assoc(mysqli_stmt_get_result($bookmarksStmt))['total'];

// ==================== GET RECENT APPLICATIONS ====================
$recentAppsQuery = "SELECT ja.*, u.FNAME, u.ONAME, u.EMAIL, a.APPLICANTPHOTO
                    FROM tbljobapplication ja
                    LEFT JOIN tblusers u ON ja.APPLICANTID = u.USERID
                    LEFT JOIN tblapplicants a ON ja.APPLICANTID = a.USERID
                    WHERE ja.JOBID = ?
                    ORDER BY ja.DATEAPPLIED DESC
                    LIMIT 5";
$recentAppsStmt = mysqli_prepare($con, $recentAppsQuery);
mysqli_stmt_bind_param($recentAppsStmt, "i", $jobid);
mysqli_stmt_execute($recentAppsStmt);
$recentApplications = mysqli_stmt_get_result($recentAppsStmt);
?>

<style>
.job-detail-section {
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

.status-closed {
    background: #d1ecf1;
    color: #0c5460;
}

.job-header {
    text-align: center;
    padding: 25px;
    background: linear-gradient(135deg, #862633 0%, #6b1e28 100%);
    color: white;
    border-radius: 8px;
    margin-bottom: 25px;
}

.company-logo-large {
    width: 80px;
    height: 80px;
    border-radius: 12px;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: 700;
    margin: 0 auto 15px;
    border: 4px solid rgba(255, 255, 255, 0.3);
    object-fit: cover;
    color: #862633;
}

.job-header-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.job-header-company {
    opacity: 0.9;
    font-size: 1rem;
    margin-bottom: 10px;
}

.job-header-location {
    opacity: 0.8;
    font-size: 0.9rem;
}

.applications-table {
    width: 100%;
    font-size: 0.85rem;
}

.applications-table th {
    background: #f8f9fa;
    padding: 8px;
    font-weight: 600;
    border-bottom: 2px solid #e9ecef;
}

.applications-table td {
    padding: 8px;
    border-bottom: 1px solid #f8f9fa;
}

.no-data {
    text-align: center;
    padding: 20px;
    color: #6c757d;
    font-style: italic;
}

.applicant-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.applicant-avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: #862633;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.9rem;
    object-fit: cover;
}

.applicant-name {
    font-weight: 600;
    color: #2c3e50;
}

.requirements-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.requirements-list li {
    padding: 5px 0 5px 20px;
    position: relative;
}

.requirements-list li:before {
    content: "✓";
    position: absolute;
    left: 0;
    color: #28a745;
    font-weight: bold;
}

.salary-badge {
    background: #28a745;
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-weight: 500;
    font-size: 0.7rem !important;
}

.category-badge {
    background: #e9ecef;
    color: #495057;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
    display: inline-block;
}

.job-description {
    line-height: 1.8;
    color: #2c3e50;
}
</style>

<!-- Job Header -->
<div class="job-header">
    <?php if (!empty($job['COMPANYLOGO'])): ?>
    <img src="<?php echo $path.htmlspecialchars($job['COMPANYLOGO']); ?>" alt="Company Logo" class="company-logo-large"
        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
    <?php endif; ?>
    <div class="company-logo-large" style="<?php echo !empty($job['COMPANYLOGO']) ? 'display:none;' : ''; ?>">
        <?php echo strtoupper(substr($job['COMPANYNAME'], 0, 1)); ?>
    </div>





    <div class="job-header-title">
        <?php echo htmlspecialchars($job['JOBTITLE']); ?>
    </div>
    <div class="job-header-company">
        <?php echo htmlspecialchars($job['COMPANYNAME']); ?>
    </div>
    <?php if (!empty($job['JOBLOCATION'])): ?>
    <div class="job-header-location">
        <i class="lni lni-map-marker mr-1"></i>
        <?php echo htmlspecialchars($job['JOBLOCATION']); ?>
    </div>
    <?php endif; ?>
    <div style="margin-top: 15px;">
        <?php
        $statusClass = 'status-active';
        $status = $job['JOBSTATUS'];
        if ($status == 'Pending') {
            $statusClass = 'status-pending';
        } elseif ($status == 'Suspended') {
            $statusClass = 'status-suspended';
        } elseif ($status == 'Inactive') {
            $statusClass = 'status-pending';
        } elseif ($status == 'Closed') {
            $statusClass = 'status-closed';
        }
        ?>
        <span class="status-badge <?php echo $statusClass; ?>">
            <?php echo htmlspecialchars($status); ?>
        </span>
    </div>
</div>

<!-- Application Statistics -->
<div class="job-detail-section">
    <h6 class="section-title">
        <i class="lni lni-bar-chart mr-2"></i>Application Statistics
    </h6>
    <div class="stats-mini-grid">
        <div class="stat-mini-card">
            <span class="stat-mini-number"><?php echo number_format($totalApplications); ?></span>
            <span class="stat-mini-label">Total Applications</span>
        </div>
        <div class="stat-mini-card">
            <span class="stat-mini-number"><?php echo number_format($pendingApplications); ?></span>
            <span class="stat-mini-label">Pending</span>
        </div>
        <div class="stat-mini-card">
            <span class="stat-mini-number"><?php echo number_format($approvedApplications); ?></span>
            <span class="stat-mini-label">Approved</span>
        </div>
        <div class="stat-mini-card">
            <span class="stat-mini-number"><?php echo number_format($rejectedApplications); ?></span>
            <span class="stat-mini-label">Rejected</span>
        </div>
    </div>
</div>

<!-- Job Information -->
<div class="job-detail-section">
    <h6 class="section-title">
        <i class="lni lni-briefcase mr-2"></i>Job Information
    </h6>

    <div class="detail-row">
        <div class="detail-label">Job ID:</div>
        <div class="detail-value"><?php echo $job['JOBID']; ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Job Title:</div>
        <div class="detail-value">
            <strong><?php echo htmlspecialchars($job['JOBTITLE']); ?></strong>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Category:</div>
        <div class="detail-value">
            <span class="category-badge">
                <?php echo htmlspecialchars($job['CATEGORY'] ?? 'Not specified'); ?>
            </span>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Employment Type:</div>
        <div class="detail-value">
            <?php echo htmlspecialchars($job['EMPLOYMENTTYPE'] ?? 'Not specified'); ?>
        </div>
    </div>

    <?php if (!empty($job['SALARY'])): ?>
    <div class="detail-row">
        <div class="detail-label">Salary:</div>
        <div class="detail-value">
            <span class="salary-badge">
                <?php echo htmlspecialchars($job['SALARY']); ?>
            </span>
        </div>
    </div>
    <?php endif; ?>

    <div class="detail-row">
        <div class="detail-label">Location:</div>
        <div class="detail-value">
            <?php echo htmlspecialchars($job['JOBLOCATION'] ?? 'Not specified'); ?>
        </div>
    </div>

    <?php if (!empty($job['EXPERIENCEREQUIRED'])): ?>
    <div class="detail-row">
        <div class="detail-label">Experience Required:</div>
        <div class="detail-value">
            <?php echo htmlspecialchars($job['EXPERIENCEREQUIRED']); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($job['EDUCATIONREQUIRED'])): ?>
    <div class="detail-row">
        <div class="detail-label">Education Required:</div>
        <div class="detail-value">
            <?php echo htmlspecialchars($job['EDUCATIONREQUIRED']); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($job['DEADLINE'])): ?>
    <div class="detail-row">
        <div class="detail-label">Application Deadline:</div>
        <div class="detail-value">
            <?php 
            $deadline = strtotime($job['DEADLINE']);
            $today = strtotime(date('Y-m-d'));
            $daysLeft = floor(($deadline - $today) / 86400);
            
            echo date('F d, Y', $deadline);
            
            if ($daysLeft > 0) {
                echo ' <span style="color: #28a745;">(' . $daysLeft . ' days left)</span>';
            } elseif ($daysLeft == 0) {
                echo ' <span style="color: #ffc107;">(Expires today)</span>';
            } else {
                echo ' <span style="color: #dc3545;">(Expired)</span>';
            }
            ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="detail-row">
        <div class="detail-label">Bookmarks:</div>
        <div class="detail-value">
            <?php echo number_format($totalBookmarks); ?>
            <?php echo $totalBookmarks == 1 ? 'user' : 'users'; ?> bookmarked this job
        </div>
    </div>
</div>

<!-- Job Description -->
<?php if (!empty($job['JOBDESCRIPTION'])): ?>
<div class="job-detail-section">
    <h6 class="section-title">
        <i class="lni lni-text-format mr-2"></i>Job Description
    </h6>
    <div class="job-description">
        <?php echo nl2br(htmlspecialchars($job['JOBDESCRIPTION'])); ?>
    </div>
</div>
<?php endif; ?>

<!-- Job Requirements -->
<?php if (!empty($job['REQUIREMENTS'])): ?>
<div class="job-detail-section">
    <h6 class="section-title">
        <i class="lni lni-checkmark-circle mr-2"></i>Requirements
    </h6>
    <div class="job-description">
        <?php echo nl2br(htmlspecialchars($job['REQUIREMENTS'])); ?>
    </div>
</div>
<?php endif; ?>

<!-- Responsibilities -->
<?php if (!empty($job['RESPONSIBILITIES'])): ?>
<div class="job-detail-section">
    <h6 class="section-title">
        <i class="lni lni-list mr-2"></i>Responsibilities
    </h6>
    <div class="job-description">
        <?php echo nl2br(htmlspecialchars($job['RESPONSIBILITIES'])); ?>
    </div>
</div>
<?php endif; ?>

<!-- Benefits -->
<?php if (!empty($job['BENEFITS'])): ?>
<div class="job-detail-section">
    <h6 class="section-title">
        <i class="lni lni-heart mr-2"></i>Benefits
    </h6>
    <div class="job-description">
        <?php echo nl2br(htmlspecialchars($job['BENEFITS'])); ?>
    </div>
</div>
<?php endif; ?>

<!-- Company Information -->
<div class="job-detail-section">
    <h6 class="section-title">
        <i class="lni lni-building mr-2"></i>Company Information
    </h6>

    <div class="detail-row">
        <div class="detail-label">Company Name:</div>
        <div class="detail-value">
            <strong><?php echo htmlspecialchars($job['COMPANYNAME']); ?></strong>
        </div>
    </div>

    <?php if (!empty($job['COMPANYADDRESS'])): ?>
    <div class="detail-row">
        <div class="detail-label">Address:</div>
        <div class="detail-value">
            <?php echo htmlspecialchars($job['COMPANYADDRESS']); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($job['COMPANYEMAIL'])): ?>
    <div class="detail-row">
        <div class="detail-label">Email:</div>
        <div class="detail-value">
            <a href="mailto:<?php echo htmlspecialchars($job['COMPANYEMAIL']); ?>">
                <?php echo htmlspecialchars($job['COMPANYEMAIL']); ?>
            </a>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($job['COMPANYCONTACTNO'])): ?>
    <div class="detail-row">
        <div class="detail-label">Phone:</div>
        <div class="detail-value">
            <?php echo htmlspecialchars($job['COMPANYCONTACTNO']); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($job['COMPANYWEBSITE'])): ?>
    <div class="detail-row">
        <div class="detail-label">Website:</div>
        <div class="detail-value">
            <a href="<?php echo htmlspecialchars($job['COMPANYWEBSITE']); ?>" target="_blank">
                <?php echo htmlspecialchars($job['COMPANYWEBSITE']); ?>
            </a>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Job Posting Details -->
<div class="job-detail-section">
    <h6 class="section-title">
        <i class="lni lni-calendar mr-2"></i>Posting Details
    </h6>

    <div class="detail-row">
        <div class="detail-label">Status:</div>
        <div class="detail-value">
            <span class="status-badge <?php echo $statusClass; ?>">
                <?php echo htmlspecialchars($job['JOBSTATUS']); ?>
            </span>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Date Posted:</div>
        <div class="detail-value">
            <?php echo date('F d, Y h:i A', strtotime($job['DATEPOSTED'])); ?>
        </div>
    </div>

    <?php if (!empty($job['DATEUPDATED'])): ?>
    <div class="detail-row">
        <div class="detail-label">Last Updated:</div>
        <div class="detail-value">
            <?php echo date('F d, Y h:i A', strtotime($job['DATEUPDATED'])); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($job['JOBOPENING'])): ?>
    <div class="detail-row">
        <div class="detail-label">Number of Openings:</div>
        <div class="detail-value">
            <strong><?php echo $job['JOBOPENING']; ?>
                <?php echo $job['JOBOPENING'] == 1 ? 'position' : 'positions'; ?></strong>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Recent Applications -->
<div class="job-detail-section">
    <h6 class="section-title">
        <i class="lni lni-users mr-2"></i>Recent Applications
    </h6>

    <?php if (mysqli_num_rows($recentApplications) > 0): ?>
    <div class="table-responsive">
        <table class="applications-table">
            <thead>
                <tr>
                    <th>Applicant</th>
                    <th>Email</th>
                    <th>Applied Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($app = mysqli_fetch_assoc($recentApplications)): ?>
                <tr>
                    <td>
                        <div class="applicant-info">
                            <?php if (!empty($app['APPLICANTPHOTO'])): ?>
                            <img src="<?php echo $path.htmlspecialchars($app['APPLICANTPHOTO']); ?>" alt="Avatar"
                                class="applicant-avatar"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <?php endif; ?>
                            <div class="applicant-avatar"
                                style="<?php echo !empty($app['APPLICANTPHOTO']) ? 'display:none;' : ''; ?>">
                                <?php echo strtoupper(substr($app['FNAME'], 0, 1)); ?>
                            </div>




                            <span class="applicant-name">
                                <?php echo htmlspecialchars($app['FNAME'] . ' ' . $app['ONAME']); ?>
                            </span>
                        </div>
                    </td>
                    <td><?php echo htmlspecialchars($app['EMAIL']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($app['DATEAPPLIED'])); ?></td>
                    <td>
                        <?php
                        $appStatusClass = 'status-pending';
                        $appStatus = $app['APPLICATIONSTATUS'];
                        if ($appStatus == 'Approved') {
                            $appStatusClass = 'status-active';
                        } elseif ($appStatus == 'Rejected') {
                            $appStatusClass = 'status-suspended';
                        }
                        ?>
                        <span class="status-badge <?php echo $appStatusClass; ?>">
                            <?php echo htmlspecialchars($appStatus); ?>
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
        No applications yet
    </div>
    <?php endif; ?>
</div>

<!-- Action Buttons -->
<div class="job-detail-section" style="border-top: 2px solid #e9ecef; padding-top: 20px;">
    <div class="row">
        <!-- <div class="col-md-6 mb-2">
            <a href="admin-job-applications.php?jobid=<?php echo $jobid; ?>" class="btn btn-primary btn-block">
                <i class="lni lni-users mr-2"></i>View All Applications
            </a>
        </div> -->
        <div class="col-md-12 mb-2">
            <a href="admin-job-details.php?jobid=<?php echo $jobid; ?>" class="btn btn-info btn-block">
                <i class="lni lni-eye mr-2"></i>View More Info
            </a>
        </div>
    </div>
</div>