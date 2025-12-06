<?php
require 'include/phpcode.php';

// ==================== GET APPLICATION ID ====================
$applicationId = isset($_GET['application_id']) ? (int)$_GET['application_id'] : 0;

if ($applicationId <= 0) {
    echo '<div class="alert alert-danger">Invalid application ID.</div>';
    exit();
}

// ==================== FETCH APPLICATION DETAILS ====================
$appQuery = "SELECT ja.*, 
             j.JOBTITLE, j.JOBTYPE, j.SALARY, j.JOBDESCRIPTION, j.QUALIFICATION,
             c.COMPANYNAME, c.COMPANYLOGO, c.COMPANYEMAIL, c.COMPANYCONTACTNO, c.COMPANYCOUNTRY,
             a.FNAME, a.OTHERNAMES, a.EMAILADDRESS, a.CONTACTNO, a.APPLICANTPHOTO,
             a.FULLADDRESS, a.CITY, a.COUNTRY, a.SEX, a.BIRTHDATE, a.ABOUTME,
             a.DEGREE, a.SCHOOLNAME, a.SKILLS, a.EXJOBTITLE, a.EXCOMPANYNAME,
             a.FB_link, a.LinkedIn_link
             FROM tbljobapplication ja
             INNER JOIN tbljob j ON ja.JOBID = j.JOBID
             INNER JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
             LEFT JOIN tblapplicants a ON ja.APPLICANTID = a.USERID
             WHERE ja.ID = ?";
$appStmt = mysqli_prepare($con, $appQuery);
mysqli_stmt_bind_param($appStmt, "i", $applicationId);
mysqli_stmt_execute($appStmt);
$appResult = mysqli_stmt_get_result($appStmt);

if (mysqli_num_rows($appResult) == 0) {
    echo '<div class="alert alert-danger">Application not found.</div>';
    exit();
}

$app = mysqli_fetch_assoc($appResult);

// ==================== GET OTHER APPLICATIONS BY THIS APPLICANT ====================
$otherAppsQuery = "SELECT ja.*, j.JOBTITLE, c.COMPANYNAME, ja.DATEAPPLIED, ja.APPLICATIONSTATUS
                   FROM tbljobapplication ja
                   LEFT JOIN tbljob j ON ja.JOBID = j.JOBID
                   LEFT JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
                   WHERE ja.APPLICANTID = ? AND ja.ID != ?
                   ORDER BY ja.DATEAPPLIED DESC
                   LIMIT 5";
$otherAppsStmt = mysqli_prepare($con, $otherAppsQuery);
mysqli_stmt_bind_param($otherAppsStmt, "ii", $app['APPLICANTID'], $applicationId);
mysqli_stmt_execute($otherAppsStmt);
$otherApps = mysqli_stmt_get_result($otherAppsStmt);
?>

<style>
.app-detail-section {
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

.application-header {
    text-align: center;
    padding: 20px;
    background: linear-gradient(135deg, #862633 0%, #6b1e28 100%);
    color: white;
    border-radius: 8px;
    margin-bottom: 25px;
}

.applicant-photo-large {
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

.applicant-name-large {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.applicant-email-large {
    opacity: 0.9;
    font-size: 0.9rem;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-approved {
    background: #d4edda;
    color: #155724;
}

.status-rejected {
    background: #f8d7da;
    color: #721c24;
}

.status-shortlisted {
    background: #d1ecf1;
    color: #0c5460;
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

.job-description-box {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 15px;
}


.other-apps-table {
    width: 100%;
    font-size: 0.85rem;
}

.other-apps-table th {
    background: #f8f9fa;
    padding: 8px;
    font-weight: 600;
    border-bottom: 2px solid #e9ecef;
}

.other-apps-table td {
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
    padding: 15px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.company-logo-box-details {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    object-fit: cover;
    border: 2px solid #e9ecef;
}

.company-logo-placeholder-details {
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
}
</style>

<!-- Application Header -->
<div class="application-header">
    <?php if (!empty($app['APPLICANTPHOTO'])): ?>
    <img src="<?php echo $path.htmlspecialchars($app['APPLICANTPHOTO']); ?>" alt="Photo" class="applicant-photo-large"
        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
    <?php endif; ?>
    <div class="applicant-photo-large" style="<?php echo !empty($app['APPLICANTPHOTO']) ? 'display:none;' : ''; ?>">
        <?php echo strtoupper(substr($app['FNAME'], 0, 1)); ?>
    </div>


    <div class="applicant-name-large">
        <?php echo htmlspecialchars($app['FNAME'] . ' ' . $app['OTHERNAMES']); ?>
    </div>
    <div class="applicant-email-large">
        <?php echo htmlspecialchars($app['EMAILADDRESS']); ?>
    </div>
    <div style="margin-top: 10px;">
        <?php
        $statusClass = 'status-pending';
        $status = $app['APPLICATIONSTATUS'];
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
    </div>
</div>

<!-- Application Information -->
<div class="app-detail-section">
    <h6 class="section-title">
        <i class="lni lni-files mr-2"></i>Application Information
    </h6>

    <div class="detail-row">
        <div class="detail-label">Application ID:</div>
        <div class="detail-value"><?php echo $app['ID']; ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Date Applied:</div>
        <div class="detail-value">
            <?php echo date('F d, Y h:i A', strtotime($app['DATEAPPLIED'])); ?>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Application Status:</div>
        <div class="detail-value">
            <span class="status-badge <?php echo $statusClass; ?>">
                <?php echo htmlspecialchars($status); ?>
            </span>
        </div>
    </div>

    <?php if (!empty($app['REMARKS'])): ?>
    <div class="detail-row">
        <div class="detail-label">Remarks:</div>
        <div class="detail-value">
            <?php echo nl2br(htmlspecialchars($app['REMARKS'])); ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Job Information -->
<div class="app-detail-section">
    <h6 class="section-title">
        <i class="lni lni-briefcase mr-2"></i>Job Information
    </h6>

    <div class="company-info-box-details">

        <?php if (!empty($app['COMPANYLOGO'])): ?>
        <img src="<?php echo $path.htmlspecialchars($app['COMPANYLOGO']); ?>" alt="Logo" class="company-logo-small"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <?php endif; ?>
        <div class="company-logo-placeholder-details"
            style="<?php echo !empty($app['COMPANYLOGO']) ? 'display:none;' : ''; ?>">
            <?php echo strtoupper(substr($app['COMPANYNAME'], 0, 1)); ?>
        </div>



        <div>
            <h6 style="margin: 0; font-weight: 700;"><?php echo htmlspecialchars($app['JOBTITLE']); ?></h6>
            <p style="margin: 5px 0; color: #6c757d;"><?php echo htmlspecialchars($app['COMPANYNAME']); ?></p>
            <?php if (!empty($app['COMPANYCOUNTRY'])): ?>
            <p style="margin: 0; font-size: 0.85rem; color: #6c757d;">
                <i class="lni lni-map-marker"></i> <?php echo htmlspecialchars($app['COMPANYCOUNTRY']); ?>
            </p>
            <?php endif; ?>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Employment Type:</div>
        <div class="detail-value"><?php echo htmlspecialchars($app['JOBTYPE'] ?? 'N/A'); ?></div>
    </div>

    <?php if (!empty($app['SALARY'])): ?>
    <div class="detail-row">
        <div class="detail-label">Salary:</div>
        <div class="detail-value"><?php echo htmlspecialchars($app['SALARY']); ?></div>
    </div>
    <?php endif; ?>

    <?php if (!empty($app['JOBDESCRIPTION'])): ?>
    <div class="detail-row">
        <div class="detail-label mt-3">Job Description:</div>
        <div class="detail-value">
            <div class="job-description-box">
                <?php echo nl2br(htmlspecialchars($app['JOBDESCRIPTION'])); ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($app['QUALIFICATION'])): ?>
    <div class="detail-row">
        <div class="detail-label mt-3">Qualifications:</div>
        <div class="detail-value">
            <div class="job-description-box">
                <?php echo nl2br(htmlspecialchars($app['QUALIFICATION'])); ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Applicant Personal Information -->
<div class="app-detail-section">
    <h6 class="section-title">
        <i class="lni lni-user mr-2"></i>Personal Information
    </h6>

    <div class="detail-row">
        <div class="detail-label">Full Name:</div>
        <div class="detail-value">
            <?php echo htmlspecialchars($app['FNAME'] . ' ' . $app['OTHERNAMES']); ?>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Email:</div>
        <div class="detail-value">
            <a href="mailto:<?php echo htmlspecialchars($app['EMAILADDRESS']); ?>">
                <?php echo htmlspecialchars($app['EMAILADDRESS']); ?>
            </a>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Phone:</div>
        <div class="detail-value">
            <?php echo htmlspecialchars($app['CONTACTNO'] ?? 'Not provided'); ?>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Gender:</div>
        <div class="detail-value">
            <?php echo htmlspecialchars($app['SEX'] ?? 'Not specified'); ?>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Date of Birth:</div>
        <div class="detail-value">
            <?php 
            if (!empty($app['BIRTHDATE']) && $app['BIRTHDATE'] != '0000-00-00') {
                echo date('F d, Y', strtotime($app['BIRTHDATE']));
                $age = date_diff(date_create($app['BIRTHDATE']), date_create('today'))->y;
                echo ' (' . $age . ' years old)';
            } else {
                echo 'Not provided';
            }
            ?>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Full Address:</div>
        <div class="detail-value">
            <?php echo htmlspecialchars($app['FULLADDRESS'] ?? 'Not provided'); ?>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">City:</div>
        <div class="detail-value">
            <?php echo htmlspecialchars($app['CITY'] ?? 'Not provided'); ?>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Country:</div>
        <div class="detail-value">
            <?php echo htmlspecialchars($app['COUNTRY'] ?? 'Not provided'); ?>
        </div>
    </div>

    <?php if (!empty($app['ABOUTME'])): ?>
    <div class="detail-row">
        <div class="detail-label">About Me:</div>
        <div class="detail-value">
            <?php echo nl2br(htmlspecialchars($app['ABOUTME'])); ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Professional Information -->
<div class="app-detail-section">
    <h6 class="section-title">
        <i class="lni lni-graduation mr-2"></i>Professional Information
    </h6>

    <div class="detail-row">
        <div class="detail-label">Education (Degree):</div>
        <div class="detail-value">
            <?php echo htmlspecialchars($app['DEGREE'] ?? 'Not specified'); ?>
        </div>
    </div>

    <?php if (!empty($app['SCHOOLNAME'])): ?>
    <div class="detail-row">
        <div class="detail-label">School/University:</div>
        <div class="detail-value">
            <?php echo htmlspecialchars($app['SCHOOLNAME']); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($app['EXJOBTITLE'])): ?>
    <div class="detail-row">
        <div class="detail-label">Previous Job Title:</div>
        <div class="detail-value">
            <?php echo htmlspecialchars($app['EXJOBTITLE']); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($app['EXCOMPANYNAME'])): ?>
    <div class="detail-row">
        <div class="detail-label">Previous Company:</div>
        <div class="detail-value">
            <?php echo htmlspecialchars($app['EXCOMPANYNAME']); ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="detail-row">
        <div class="detail-label">Skills:</div>
        <div class="detail-value">
            <?php if (!empty($app['SKILLS'])): ?>
            <div class="skills-list">
                <?php 
                    $skills = explode(',', $app['SKILLS']);
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

<!-- Social Media Links -->
<?php if (!empty($app['FB_link']) || !empty($app['LinkedIn_link'])): ?>
<div class="app-detail-section">
    <h6 class="section-title">
        <i class="lni lni-share mr-2"></i>Social Media
    </h6>

    <?php if (!empty($app['FB_link'])): ?>
    <div class="detail-row">
        <div class="detail-label">Facebook:</div>
        <div class="detail-value">
            <a href="<?php echo htmlspecialchars($app['FB_link']); ?>" target="_blank" class="social-link">
                <i class="fab fa-facebook mr-1"></i><?php echo htmlspecialchars($app['FB_link']); ?>
            </a>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($app['LinkedIn_link'])): ?>
    <div class="detail-row">
        <div class="detail-label">LinkedIn:</div>
        <div class="detail-value">
            <a href="<?php echo htmlspecialchars($app['LinkedIn_link']); ?>" target="_blank" class="social-link">
                <i class="fab fa-linkedin mr-1"></i><?php echo htmlspecialchars($app['LinkedIn_link']); ?>
            </a>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Other Applications -->
<div class="app-detail-section">
    <h6 class="section-title">
        <i class="lni lni-files mr-2"></i>Other Applications by This Applicant
    </h6>

    <?php if (mysqli_num_rows($otherApps) > 0): ?>
    <div class="table-responsive">
        <table class="other-apps-table">
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Company</th>
                    <th>Date Applied</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($otherApp = mysqli_fetch_assoc($otherApps)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($otherApp['JOBTITLE']); ?></td>
                    <td><?php echo htmlspecialchars($otherApp['COMPANYNAME']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($otherApp['DATEAPPLIED'])); ?></td>
                    <td>
                        <?php
                        $otherStatusClass = 'status-pending';
                        $otherStatus = $otherApp['APPLICATIONSTATUS'];
                        if ($otherStatus == 'Approved') {
                            $otherStatusClass = 'status-approved';
                        } elseif ($otherStatus == 'Rejected') {
                            $otherStatusClass = 'status-rejected';
                        } elseif ($otherStatus == 'Shortlisted') {
                            $otherStatusClass = 'status-shortlisted';
                        }
                        ?>
                        <span class="status-badge <?php echo $otherStatusClass; ?>">
                            <?php echo htmlspecialchars($otherStatus); ?>
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
        No other applications
    </div>
    <?php endif; ?>
</div>

<!-- Action Buttons -->
<div class="app-detail-section" style="border-top: 2px solid #e9ecef; padding-top: 20px;">
    <div class="row">
        <!-- <div class="col-md-6 mb-2">
            <a href="admin-user-profile.php?userid=<?php echo $app['APPLICANTID']; ?>"
                class="btn btn-outline-secondary btn-block rounded">
                <i class="lni lni-user mr-2"></i>View Full Profile
            </a>
        </div> -->
        <div class="col-md-12 mb-2">
            <a href="admin-job-details.php?jobid=<?php echo $app['JOBID']; ?>"
                class="btn btn-outline-info btn-block rounded">
                <i class="lni lni-briefcase mr-2"></i>View Job Details
            </a>
        </div>
    </div>
</div>