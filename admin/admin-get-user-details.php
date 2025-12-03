<?php
require 'include/phpcode.php';

// ==================== GET USER ID ====================
$userid = isset($_GET['userid']) ? (int)$_GET['userid'] : 0;

if ($userid <= 0) {
    echo '<div class="alert alert-danger">Invalid user ID.</div>';
    exit();
}

// ==================== FETCH USER DETAILS WITH APPLICANT INFO ====================
$userQuery = "SELECT u.*, 
              a.APPLICANTID, a.JOBCATEGORYID, a.JOBTITLE, a.EXCOMPANYNAME, a.EXJOBTITLE,
              a.FNAME as APPLICANT_FNAME, a.OTHERNAMES, a.FULLADDRESS, a.CITY, a.COUNTRY,
              a.SEX, a.BIRTHDATE, a.ABOUTME, a.USERNAME as APPLICANT_USERNAME, 
              a.EMAILADDRESS, a.CONTACTNO, a.DEGREE, a.SCHOOLNAME, a.SKILLS, 
              a.APPLICANTPHOTO, a.FB_link, a.LinkedIn_link
              FROM tblusers u 
              LEFT JOIN tblapplicants a ON u.USERID = a.USERID
              WHERE u.USERID = ?";
$userStmt = mysqli_prepare($con, $userQuery);
mysqli_stmt_bind_param($userStmt, "i", $userid);
mysqli_stmt_execute($userStmt);
$userResult = mysqli_stmt_get_result($userStmt);

if (mysqli_num_rows($userResult) == 0) {
    echo '<div class="alert alert-danger">User not found.</div>';
    exit();
}

$user = mysqli_fetch_assoc($userResult);

// ==================== GET APPLICATION STATISTICS ====================
$applicationsQuery = "SELECT COUNT(*) as total FROM tbljobapplication WHERE APPLICANTID = ?";
$applicationsStmt = mysqli_prepare($con, $applicationsQuery);
mysqli_stmt_bind_param($applicationsStmt, "i", $userid);
mysqli_stmt_execute($applicationsStmt);
$totalApplications = mysqli_fetch_assoc(mysqli_stmt_get_result($applicationsStmt))['total'];

// ==================== GET PENDING APPLICATIONS ====================
$pendingAppsQuery = "SELECT COUNT(*) as total FROM tbljobapplication WHERE APPLICANTID = ? AND APPLICATIONSTATUS = 'Pending'";
$pendingAppsStmt = mysqli_prepare($con, $pendingAppsQuery);
mysqli_stmt_bind_param($pendingAppsStmt, "i", $userid);
mysqli_stmt_execute($pendingAppsStmt);
$pendingApplications = mysqli_fetch_assoc(mysqli_stmt_get_result($pendingAppsStmt))['total'];

// ==================== GET APPROVED APPLICATIONS ====================
$approvedAppsQuery = "SELECT COUNT(*) as total FROM tbljobapplication WHERE APPLICANTID = ? AND APPLICATIONSTATUS = 'Approved'";
$approvedAppsStmt = mysqli_prepare($con, $approvedAppsQuery);
mysqli_stmt_bind_param($approvedAppsStmt, "i", $userid);
mysqli_stmt_execute($approvedAppsStmt);
$approvedApplications = mysqli_fetch_assoc(mysqli_stmt_get_result($approvedAppsStmt))['total'];

// ==================== GET BOOKMARKED JOBS ====================
$bookmarksQuery = "SELECT COUNT(*) as total FROM tblbookmarkjob WHERE APPLICANTID = ?";
$bookmarksStmt = mysqli_prepare($con, $bookmarksQuery);
mysqli_stmt_bind_param($bookmarksStmt, "i", $userid);
mysqli_stmt_execute($bookmarksStmt);
$totalBookmarks = mysqli_fetch_assoc(mysqli_stmt_get_result($bookmarksStmt))['total'];

// ==================== GET LAST LOGIN INFO ====================
$lastLoginQuery = "SELECT * FROM tbl_login_history WHERE USERID = ? ORDER BY LOGIN_TIME DESC LIMIT 1";
$lastLoginStmt = mysqli_prepare($con, $lastLoginQuery);
mysqli_stmt_bind_param($lastLoginStmt, "i", $userid);
mysqli_stmt_execute($lastLoginStmt);
$lastLoginResult = mysqli_stmt_get_result($lastLoginStmt);
$lastLogin = mysqli_fetch_assoc($lastLoginResult);

// ==================== GET RECENT APPLICATIONS ====================
$recentAppsQuery = "SELECT ja.*, j.JOBTITLE, c.COMPANYNAME, ja.DATEAPPLIED, ja.APPLICATIONSTATUS
                    FROM tbljobapplication ja
                    LEFT JOIN tbljob j ON ja.JOBID = j.JOBID
                    LEFT JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
                    WHERE ja.APPLICANTID = ?
                    ORDER BY ja.DATEAPPLIED DESC
                    LIMIT 5";
$recentAppsStmt = mysqli_prepare($con, $recentAppsQuery);
mysqli_stmt_bind_param($recentAppsStmt, "i", $userid);
mysqli_stmt_execute($recentAppsStmt);
$recentApplications = mysqli_stmt_get_result($recentAppsStmt);

// Determine display name and email
$displayName = !empty($user['APPLICANT_FNAME']) 
    ? $user['APPLICANT_FNAME'] . ' ' . $user['OTHERNAMES']
    : $user['FNAME'] . ' ' . $user['ONAME'];

$displayEmail = !empty($user['EMAILADDRESS']) 
    ? $user['EMAILADDRESS'] 
    : $user['EMAIL'];
?>

<style>
.user-detail-section {
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

.user-profile-header {
    text-align: center;
    padding: 20px;
    background: linear-gradient(135deg, #862633 0%, #6b1e28 100%);
    color: white;
    border-radius: 8px;
    margin-bottom: 25px;
}

.user-profile-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: white;
    color: #862633;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0 auto 15px;
    border: 4px solid rgba(255, 255, 255, 0.3);
    object-fit: cover;
}

.user-profile-name {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.user-profile-email {
    opacity: 0.9;
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

.skills-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.skill-badge {
    background: #e9ecef;
    color: #495057;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
}

.social-link {
    color: #862633;
    text-decoration: none;
    font-weight: 600;
}

.social-link:hover {
    text-decoration: underline;
}
</style>

<!-- User Profile Header -->
<div class="user-profile-header">
    <?php if (!empty($user['APPLICANTPHOTO'])): ?>
    <img src="<?php echo $path.htmlspecialchars($user['APPLICANTPHOTO']); ?>" alt="Profile" class="user-profile-avatar"
        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
    <?php endif; ?>
    <div class="user-profile-avatar" style="<?php echo !empty($user['APPLICANTPHOTO']) ? 'display:none;' : ''; ?>">
        <?php echo strtoupper(substr($user['FNAME'], 0, 1)); ?>
    </div>



    <div class="user-profile-name">
        <?php echo htmlspecialchars($displayName); ?>
    </div>
    <div class="user-profile-email">
        <?php echo htmlspecialchars($displayEmail); ?>
    </div>
    <div style="margin-top: 10px;">
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
    </div>
</div>

<!-- Activity Statistics -->
<div class="user-detail-section">
    <h6 class="section-title">
        <i class="lni lni-bar-chart mr-2"></i>Activity Statistics
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
            <span class="stat-mini-number"><?php echo number_format($totalBookmarks); ?></span>
            <span class="stat-mini-label">Bookmarked Jobs</span>
        </div>
    </div>
</div>

<!-- Personal Information -->
<div class="user-detail-section">
    <h6 class="section-title">
        <i class="lni lni-user mr-2"></i>Personal Information
    </h6>

    <div class="detail-row">
        <div class="detail-label">User ID:</div>
        <div class="detail-value"><?php echo $user['USERID']; ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Full Name:</div>
        <div class="detail-value">
            <?php echo htmlspecialchars($displayName); ?>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Username (System):</div>
        <div class="detail-value"><?php echo htmlspecialchars($user['USERNAME']); ?></div>
    </div>

    <?php if (!empty($user['APPLICANT_USERNAME']) && $user['APPLICANT_USERNAME'] != $user['USERNAME']): ?>
    <div class="detail-row">
        <div class="detail-label">Username (Profile):</div>
        <div class="detail-value"><?php echo htmlspecialchars($user['APPLICANT_USERNAME']); ?></div>
    </div>
    <?php endif; ?>

    <div class="detail-row">
        <div class="detail-label">Email:</div>
        <div class="detail-value">
            <a href="mailto:<?php echo htmlspecialchars($displayEmail); ?>">
                <?php echo htmlspecialchars($displayEmail); ?>
            </a>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Phone:</div>
        <div class="detail-value">
            <?php echo htmlspecialchars($user['CONTACTNO'] ?? $user['PHONE'] ?? 'Not provided'); ?>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Gender:</div>
        <div class="detail-value">
            <?php echo htmlspecialchars($user['SEX'] ?? 'Not specified'); ?>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Date of Birth:</div>
        <div class="detail-value">
            <?php 
            if (!empty($user['BIRTHDATE']) && $user['BIRTHDATE'] != '0000-00-00') {
                echo date('F d, Y', strtotime($user['BIRTHDATE']));
            } else {
                echo 'Not provided';
            }
            ?>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Full Address:</div>
        <div class="detail-value">
            <?php 
            if (!empty($user['FULLADDRESS'])) {
                echo htmlspecialchars($user['FULLADDRESS']);
            } else {
                echo 'Not provided';
            }
            ?>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">City:</div>
        <div class="detail-value">
            <?php echo htmlspecialchars($user['CITY'] ?? 'Not provided'); ?>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Country:</div>
        <div class="detail-value">
            <?php echo htmlspecialchars($user['COUNTRY'] ?? 'Not provided'); ?>
        </div>
    </div>

    <?php if (!empty($user['ABOUTME'])): ?>
    <div class="detail-row">
        <div class="detail-label">About Me:</div>
        <div class="detail-value">
            <?php echo nl2br(htmlspecialchars($user['ABOUTME'])); ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Professional Information -->
<?php if (!empty($user['APPLICANTID'])): ?>
<div class="user-detail-section">
    <h6 class="section-title">
        <i class="lni lni-briefcase mr-2"></i>Professional Information
    </h6>

    <?php if (!empty($user['JOBTITLE'])): ?>
    <div class="detail-row">
        <div class="detail-label">Current/Desired Job Title:</div>
        <div class="detail-value">
            <?php echo htmlspecialchars($user['JOBTITLE']); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($user['EXJOBTITLE'])): ?>
    <div class="detail-row">
        <div class="detail-label">Previous Job Title:</div>
        <div class="detail-value">
            <?php echo htmlspecialchars($user['EXJOBTITLE']); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($user['EXCOMPANYNAME'])): ?>
    <div class="detail-row">
        <div class="detail-label">Previous Company:</div>
        <div class="detail-value">
            <?php echo htmlspecialchars($user['EXCOMPANYNAME']); ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="detail-row">
        <div class="detail-label">Education (Degree):</div>
        <div class="detail-value">
            <?php echo htmlspecialchars($user['DEGREE'] ?? 'Not specified'); ?>
        </div>
    </div>

    <?php if (!empty($user['SCHOOLNAME'])): ?>
    <div class="detail-row">
        <div class="detail-label">School/University:</div>
        <div class="detail-value">
            <?php echo htmlspecialchars($user['SCHOOLNAME']); ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="detail-row">
        <div class="detail-label">Skills:</div>
        <div class="detail-value">
            <?php if (!empty($user['SKILLS'])): ?>
            <div class="skills-list">
                <?php 
                    $skills = explode(',', $user['SKILLS']);
                    foreach ($skills as $skill):
                    ?>
                <span class="skill-badge"><?php echo htmlspecialchars(trim($skill)); ?></span>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            Not specified
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Social Media Links -->
<?php if (!empty($user['FB_link']) || !empty($user['LinkedIn_link'])): ?>
<div class="user-detail-section">
    <h6 class="section-title">
        <i class="lni lni-share mr-2"></i>Social Media
    </h6>

    <?php if (!empty($user['FB_link'])): ?>
    <div class="detail-row">
        <div class="detail-label">Facebook:</div>
        <div class="detail-value">
            <a href="<?php echo htmlspecialchars($user['FB_link']); ?>" target="_blank" class="social-link">
                <i class="fab fa-facebook mr-1"></i><?php echo htmlspecialchars($user['FB_link']); ?>
            </a>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($user['LinkedIn_link'])): ?>
    <div class="detail-row">
        <div class="detail-label">LinkedIn:</div>
        <div class="detail-value">
            <a href="<?php echo htmlspecialchars($user['LinkedIn_link']); ?>" target="_blank" class="social-link">
                <i class="fab fa-linkedin mr-1"></i><?php echo htmlspecialchars($user['LinkedIn_link']); ?>
            </a>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Account Information -->
<div class="user-detail-section">
    <h6 class="section-title">
        <i class="lni lni-cog mr-2"></i>Account Information
    </h6>

    <div class="detail-row">
        <div class="detail-label">Account Status:</div>
        <div class="detail-value">
            <span class="status-badge <?php echo $statusClass; ?>">
                <?php echo htmlspecialchars($user['USERSTATUS']); ?>
            </span>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Registration Date:</div>
        <div class="detail-value">
            <?php echo date('F d, Y h:i A', strtotime($user['DATECREATED'])); ?>
        </div>
    </div>

    <?php if (!empty($user['DATEUPDATED'])): ?>
    <div class="detail-row">
        <div class="detail-label">Last Updated:</div>
        <div class="detail-value">
            <?php echo date('F d, Y h:i A', strtotime($user['DATEUPDATED'])); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($lastLogin): ?>
    <div class="detail-row">
        <div class="detail-label">Last Login:</div>
        <div class="detail-value">
            <?php echo date('F d, Y h:i A', strtotime($lastLogin['LOGIN_TIME'])); ?>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Last IP Address:</div>
        <div class="detail-value">
            <code><?php echo htmlspecialchars($lastLogin['IP_ADDRESS']); ?></code>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Recent Applications -->
<div class="user-detail-section">
    <h6 class="section-title">
        <i class="lni lni-files mr-2"></i>Recent Applications
    </h6>

    <?php if (mysqli_num_rows($recentApplications) > 0): ?>
    <div class="table-responsive">
        <table class="applications-table">
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Company</th>
                    <th>Date Applied</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($app = mysqli_fetch_assoc($recentApplications)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($app['JOBTITLE']); ?></td>
                    <td><?php echo htmlspecialchars($app['COMPANYNAME']); ?></td>
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
<div class="user-detail-section" style="border-top: 2px solid #e9ecef; padding-top: 20px;">
    <div class="row">
        <!-- <div class="col-md-6 mb-2">
            <a href="admin-applications.php?userid=<?php echo $userid; ?>"
                class="btn btn-outline-secondary btn-block rounded">
                <i class="lni lni-files mr-2"></i>View All Applications
            </a>
        </div>
        <div class="col-md-6 mb-2">
            <a href="admin-activity-logs.php?userid=<?php echo $userid; ?>"
                class="btn btn-outline-info btn-block rounded">
                <i class="lni lni-timer mr-2"></i>View Activity History
            </a>
        </div> -->
    </div>
</div>