<!DOCTYPE html>
<html lang="en">

<?php
require('./mysqli_connect.php');
include 'session_check.php';
include "include/helper.php";


// ==================== VALIDATION & DATA RETRIEVAL ====================
// Get and validate company ID from URL
$companyid = isset($_GET['companyid']) ? (int)$_GET['companyid'] : 0;


// Redirect if no company ID provided
if (empty($companyid)) {
    header('location: browse-employers.php');
    exit();
}

// ==================== FETCH COMPANY DATA ====================
// Fetch company details with prepared statement for security
$query = "SELECT * FROM tblcompany WHERE COMPANYID = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $companyid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Redirect if company not found
if (mysqli_num_rows($result) == 0) {
    header('location: browse-employers.php');
    exit();
}

$row = mysqli_fetch_assoc($result);

// ==================== EXTRACT COMPANY INFORMATION ====================
// Basic Information
$COMPANYID = $row['COMPANYID'];
$COMPANYNAME = $row['COMPANYNAME'];
$COMPANYLOGO = $row['COMPANYLOGO'] ?? '';

// Contact Information
$COMPANYEMAIL = $row['COMPANYEMAIL'] ?? '';
$COMPANYCONTACTNO = $row['COMPANYCONTACTNO'] ?? '';
$COMPANYADDRESS = $row['COMPANYADDRESS'] ?? '';
$COMPANYCITY = $row['COMPANYCITY'] ?? '';
$COMPANYCOUNTRY = $row['COMPANYCOUNTRY'] ?? '';
$COMPANYWEBSITE = $row['COMPANYWEBSITE'] ?? '';

// Company Details
$COMPANYABOUT = $row['COMPANYABOUT'] ?? '';
$COMPANYINDUSTRY = $row['COMPANYINDUSTRY'] ?? '';
$COMPANYSPECIALISM = $row['COMPANYSPECIALISM'] ?? '';
$COMPANYSTATUS = $row['COMPANYSTATUS'] ?? 'Active';
$COMPANYYEARFOUNDED = $row['COMPANYYEARFOUNDED'] ?? '';
$COMPANYSIZE = $row['COMPANYSIZE'] ?? '';

// Awards & Recognition
$COMPANYAWARD = $row['COMPANYAWARD'] ?? '';
$COMPANYYEAR = $row['COMPANYYEAR'] ?? '';
$COMPANYAWARDDESC = $row['COMPANYAWARDDESC'] ?? '';

// Social Media Links
$COMPANYLINKEDIN = $row['COMPANYLINKEDIN'] ?? '';
$COMPANYFACEBOOK = $row['COMPANYFACEBOOK'] ?? '';
$COMPANYTWITTER = $row['COMPANYTWITTER'] ?? '';

// ==================== GET JOB STATISTICS ====================
// Count active jobs for this company
$jobQuery = "SELECT COUNT(*) as job_count FROM tbljob WHERE COMPANYID = ? AND JOBSTATUS = 'Active'";
$jobStmt = mysqli_prepare($con, $jobQuery);
mysqli_stmt_bind_param($jobStmt, "i", $companyid);
mysqli_stmt_execute($jobStmt);
$jobResult = mysqli_stmt_get_result($jobStmt);
$jobRow = mysqli_fetch_assoc($jobResult);
$activeJobs = $jobRow['job_count'];

// ==================== FETCH RECENT JOBS ====================
// Get latest 5 jobs from this company
$recentJobsQuery = "SELECT * FROM tbljob WHERE COMPANYID = ? AND JOBSTATUS = 'Active' ORDER BY JOBID DESC LIMIT 5";
$recentStmt = mysqli_prepare($con, $recentJobsQuery);
mysqli_stmt_bind_param($recentStmt, "i", $companyid);
mysqli_stmt_execute($recentStmt);
$recentJobsResult = mysqli_stmt_get_result($recentStmt);
?>

<?php include 'include/head.php' ?>


<body>
    <div id="main-wrapper">
        <!-- ==================== HEADER ==================== -->
        <?php include 'include/header.php' ?>
        <div class="clearfix"></div>

        <!-- ==================== PAGE HEADER ==================== -->
        <div class="company-header">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="browse-employers.php">Employers</a></li>
                        <li class="breadcrumb-item active">Company Profile</li>
                    </ol>
                </nav>
                <h1><?php echo htmlspecialchars($COMPANYNAME); ?></h1>
            </div>
        </div>

        <!-- ==================== MAIN CONTENT SECTION ==================== -->
        <section class="py-5 bg-light">
            <div class="container">
                <div class="row">
                    <!-- ==================== SIDEBAR COLUMN ==================== -->
                    <div class="col-lg-4 col-md-12 mb-4">
                        <div class="company-sidebar">
                            <!-- Profile Header -->
                            <div class="company-profile-header theme-bg">
                                <div class="company-logo-wrapper">
                                    <?php if (!empty($COMPANYLOGO)): ?>
                                    <img src="<?php echo htmlspecialchars($COMPANYLOGO); ?>" alt="Company Logo"
                                        class="company-logo"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <?php endif; ?>
                                    <div class="company-logo-placeholder"
                                        style="<?php echo !empty($COMPANYLOGO) ? 'display:none;' : ''; ?>">
                                        <?php echo strtoupper(substr($COMPANYNAME, 0, 1)); ?>
                                    </div>
                                </div>
                                <h4 class="company-name text-white"><?php echo htmlspecialchars($COMPANYNAME); ?></h4>
                                <p class="company-location text-white">
                                    <i class="lni lni-map-marker mr-1"></i>
                                    <?php echo htmlspecialchars($COMPANYCITY . ', ' . $COMPANYCOUNTRY); ?>
                                </p>

                                <!-- Company Badges -->
                                <div class="company-badges">
                                    <span class="badge-custom badge-active">
                                        <i class="lni lni-checkmark-circle"></i>
                                        <?php echo htmlspecialchars($COMPANYSTATUS); ?>
                                    </span>
                                    <?php if ($activeJobs > 0): ?>
                                    <span class="badge-custom badge-jobs">
                                        <?php echo $activeJobs; ?> Job<?php echo $activeJobs > 1 ? 's' : ''; ?>
                                    </span>
                                    <?php endif; ?>
                                    <?php if (!empty($COMPANYINDUSTRY)): ?>
                                    <span class="badge-custom badge-industry">
                                        <?php echo htmlspecialchars($COMPANYINDUSTRY); ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="contact-info-section">
                                <?php if (!empty($COMPANYADDRESS)): ?>
                                <div class="contact-item">
                                    <div class="contact-icon">
                                        <i class="lni lni-home"></i>
                                    </div>
                                    <div class="contact-details">
                                        <span class="contact-label">Address</span>
                                        <div class="contact-value"><?php echo htmlspecialchars($COMPANYADDRESS); ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($COMPANYEMAIL)): ?>
                                <div class="contact-item">
                                    <div class="contact-icon">
                                        <i class="lni lni-envelope"></i>
                                    </div>
                                    <div class="contact-details">
                                        <span class="contact-label">Email</span>
                                        <div class="contact-value">
                                            <a href="mailto:<?php echo htmlspecialchars($COMPANYEMAIL); ?>">
                                                <?php echo htmlspecialchars($COMPANYEMAIL); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($COMPANYWEBSITE)): ?>
                                <div class="contact-item">
                                    <div class="contact-icon">
                                        <i class="lni lni-world"></i>
                                    </div>
                                    <div class="contact-details">
                                        <span class="contact-label">Website</span>
                                        <div class="contact-value">
                                            <a href="<?php echo htmlspecialchars($COMPANYWEBSITE); ?>" target="_blank"
                                                rel="noopener">
                                                <?php echo htmlspecialchars($COMPANYWEBSITE); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($COMPANYCONTACTNO)): ?>
                                <div class="contact-item">
                                    <div class="contact-icon">
                                        <i class="lni lni-phone"></i>
                                    </div>
                                    <div class="contact-details">
                                        <span class="contact-label">Phone</span>
                                        <div class="contact-value">
                                            <a href="tel:<?php echo htmlspecialchars($COMPANYCONTACTNO); ?>">
                                                <?php echo htmlspecialchars($COMPANYCONTACTNO); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($COMPANYSIZE)): ?>
                                <div class="contact-item">
                                    <div class="contact-icon">
                                        <i class="lni lni-users"></i>
                                    </div>
                                    <div class="contact-details">
                                        <span class="contact-label">Company Size</span>
                                        <div class="contact-value"><?php echo htmlspecialchars($COMPANYSIZE); ?>
                                            employees</div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($COMPANYYEARFOUNDED)): ?>
                                <div class="contact-item">
                                    <div class="contact-icon">
                                        <i class="lni lni-calendar"></i>
                                    </div>
                                    <div class="contact-details">
                                        <span class="contact-label">Year Founded</span>
                                        <div class="contact-value"><?php echo htmlspecialchars($COMPANYYEARFOUNDED); ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Social Media Links -->
                            <?php if (!empty($COMPANYLINKEDIN) || !empty($COMPANYFACEBOOK) || !empty($COMPANYTWITTER)): ?>
                            <div class="social-section">
                                <h6 class="social-title">Connect With Us</h6>
                                <div class="social-links">
                                    <?php if (!empty($COMPANYLINKEDIN)): ?>
                                    <a href="<?php echo htmlspecialchars($COMPANYLINKEDIN); ?>" target="_blank"
                                        rel="noopener" class="social-btn" title="LinkedIn">
                                        <i class="lni lni-linkedin-original"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if (!empty($COMPANYFACEBOOK)): ?>
                                    <a href="<?php echo htmlspecialchars($COMPANYFACEBOOK); ?>" target="_blank"
                                        rel="noopener" class="social-btn" title="Facebook">
                                        <i class="lni lni-facebook-filled"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if (!empty($COMPANYTWITTER)): ?>
                                    <a href="<?php echo htmlspecialchars($COMPANYTWITTER); ?>" target="_blank"
                                        rel="noopener" class="social-btn" title="Twitter">
                                        <i class="lni lni-twitter-original"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Action Buttons -->
                            <div class="action-section">
                                <?php if (!empty($COMPANYEMAIL)): ?>
                                <a href="mailto:<?php echo htmlspecialchars($COMPANYEMAIL); ?>" class="btn-contact">
                                    <i class="lni lni-envelope"></i>
                                    <span>Contact Company</span>
                                </a>
                                <?php endif; ?>
                                <?php if ($activeJobs > 0): ?>
                                <a href="job-list-v1.php?search=<?php echo urlencode($COMPANYNAME); ?>&search_by=company"
                                    class="btn-view-jobs">
                                    <i class="lni lni-briefcase"></i>
                                    <span>View All Jobs (<?php echo $activeJobs; ?>)</span>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== MAIN CONTENT COLUMN ==================== -->
                    <div class="col-lg-8 col-md-12">
                        <!-- Statistics Cards -->
                        <?php if ($activeJobs > 0): ?>
                        <div class="stats-grid">
                            <div class="stat-card">
                                <span class="stat-number"><?php echo $activeJobs; ?></span>
                                <span class="stat-label">Active Job Openings</span>
                            </div>
                            <div class="stat-card">
                                <span class="stat-number">
                                    <i class="lni lni-checkmark-circle"></i>
                                </span>
                                <span class="stat-label">Verified Company</span>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- About Company Section -->
                        <?php if (!empty($COMPANYABOUT)): ?>
                        <div class="content-card">
                            <div class="section-header">
                                <div class="section-icon">
                                    <i class="lni lni-information"></i>
                                </div>
                                <h5 class="section-title">About <?php echo htmlspecialchars($COMPANYNAME); ?></h5>
                            </div>
                            <p class="about-text"><?php echo nl2br(htmlspecialchars($COMPANYABOUT)); ?></p>
                        </div>
                        <?php endif; ?>

                        <!-- Company Specialisms Section -->
                        <?php if (!empty($COMPANYSPECIALISM)): ?>
                        <div class="content-card">
                            <div class="section-header">
                                <div class="section-icon">
                                    <i class="lni lni-cog"></i>
                                </div>
                                <h5 class="section-title">Company Specialisms</h5>
                            </div>
                            <div class="specialisms-grid">
                                <?php 
                                $specialisms = explode(',', $COMPANYSPECIALISM);
                                foreach ($specialisms as $specialism): 
                                    $specialism = trim($specialism);
                                    if (!empty($specialism)):
                                ?>
                                <div class="specialism-item">
                                    <div class="check-icon">
                                        <i class="lni lni-checkmark"></i>
                                    </div>
                                    <div class="specialism-text"><?php echo htmlspecialchars($specialism); ?></div>
                                </div>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Awards & Recognition Section -->
                        <?php if (!empty($COMPANYAWARD)): ?>
                        <div class="content-card">
                            <div class="section-header">
                                <div class="section-icon">
                                    <i class="lni lni-star"></i>
                                </div>
                                <h5 class="section-title">Awards & Recognition</h5>
                            </div>
                            <div class="award-card">
                                <h6 class="award-title"><?php echo htmlspecialchars($COMPANYAWARD); ?></h6>
                                <?php if (!empty($COMPANYYEAR)): ?>
                                <div class="award-year">
                                    <i class="lni lni-calendar"></i>
                                    <span><?php echo htmlspecialchars($COMPANYYEAR); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($COMPANYAWARDDESC)): ?>
                                <p class="award-description"><?php echo htmlspecialchars($COMPANYAWARDDESC); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Recent Job Openings Section -->
                        <?php if (mysqli_num_rows($recentJobsResult) > 0): ?>
                        <div class="content-card">
                            <div class="section-header">
                                <div class="section-icon">
                                    <i class="lni lni-briefcase"></i>
                                </div>
                                <h5 class="section-title">Recent Job Openings</h5>
                            </div>
                            <?php while ($job = mysqli_fetch_assoc($recentJobsResult)): ?>
                            <div class="job-item">
                                <h6 class="job-item-title">
                                    <a href="job-detail.php?jobid=<?php echo $job['JOBID']; ?>">
                                        <?php echo htmlspecialchars($job['JOBTITLE']); ?>
                                    </a>
                                </h6>
                                <div class="job-item-meta">
                                    <span>
                                        <i class="lni lni-briefcase"></i>
                                        <?php echo htmlspecialchars($job['JOBTYPE']); ?>
                                    </span>
                                    <?php if (isset($job['SALARY']) && $job['SALARY'] > 0): ?>
                                    <span>
                                        <i class="lni lni-wallet"></i>
                                        $<?php echo number_format($job['SALARY'], 0); ?>/hr
                                    </span>
                                    <?php endif; ?>
                                    <span>
                                        <i class="lni lni-calendar"></i>
                                        <?php echo date('M d, Y', strtotime($job['DATEPOSTED'])); ?>
                                    </span>
                                </div>
                            </div>
                            <?php endwhile; ?>

                            <?php if ($activeJobs > 5): ?>
                            <div class="text-center mt-4">
                                <a href="job-list-v1.php?search=<?php echo urlencode($COMPANYNAME); ?>&search_by=company"
                                    class="btn btn-contact d-inline-flex">
                                    View All <?php echo $activeJobs; ?> Jobs
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php elseif ($activeJobs == 0): ?>
                        <div class="content-card">
                            <div class="section-header">
                                <div class="section-icon">
                                    <i class="lni lni-briefcase"></i>
                                </div>
                                <h5 class="section-title">Job Openings</h5>
                            </div>
                            <div class="empty-state">
                                <i class="lni lni-briefcase"></i>
                                <p class="empty-state-text">No active job openings at this time. Check back later!</p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================== FOOTER ==================== -->
        <?php include 'include/footer.php' ?>

        <!-- Back to Top Button -->
        <a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>
    </div>

    <!-- ==================== SCRIPTS ==================== -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/slick.js"></script>
    <script src="assets/js/slider-bg.js"></script>
    <script src="assets/js/smoothproducts.js"></script>
    <script src="assets/js/snackbar.min.js"></script>
    <script src="assets/js/jQuery.style.switcher.js"></script>
    <script src="assets/js/custom.js"></script>
</body>

</html>