<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';

// ==================== FETCH COMPANY DATA ====================
// Fetch company details with prepared statement for security
$query = "SELECT * FROM tblcompany WHERE USERID = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $session_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Redirect if no company found
if (mysqli_num_rows($result) == 0) {
    header('location: dashboard-company-add.php');
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
?>

<head>
    <meta charset="utf-8" />
    <meta name="author" content="MUNext" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Company Profile - MUNext Dashboard</title>
    <link href="assets/css/styles.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/custom-dashboard.css">
</head>

<body>
    <div id="main-wrapper">
        <!-- ==================== HEADER ==================== -->
        <?php include 'header.php' ?>
        <div class="clearfix"></div>

        <!-- ==================== DASHBOARD WRAPPER ==================== -->
        <div class="dashboard-wrap bg-light">
            <!-- Mobile Navigation Toggle -->
            <a class="mobNavigation" data-toggle="collapse" href="#MobNav" role="button" aria-expanded="false"
                aria-controls="MobNav">
                <i class="fas fa-bars mr-2"></i>Dashboard Navigation
            </a>

            <!-- ==================== SIDEBAR NAVIGATION ==================== -->
            <?php include 'sidenav.php' ?>

            <!-- ==================== MAIN CONTENT ==================== -->
            <div class="dashboard-content">
                <!-- Page Header -->
                <div class="page-header">
                    <h1 class="page-title">Company Profile</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Company Profile</li>
                        </ol>
                    </nav>
                </div>

                <div class="container-fluid">
                    <div class="row">
                        <!-- ==================== SIDEBAR COLUMN ==================== -->
                        <div class="col-lg-4 col-md-12 mb-4">
                            <!-- Company Profile Card -->
                            <div class="company-card">
                                <!-- Profile Header -->
                                <div class="profile-header">
                                    <div class="logo-container">
                                        <img src="<?php echo !empty($COMPANYLOGO) ? $path . $COMPANYLOGO : 'assets/img/company-default.png'; ?>"
                                            alt="<?php echo htmlspecialchars($COMPANYNAME); ?>">
                                    </div>
                                    <h4 class="company-name"><?php echo htmlspecialchars($COMPANYNAME); ?></h4>



                                    <p class="company-tagline">
                                        <i class="lni lni-map-marker mr-1"></i>
                                        <?php echo htmlspecialchars($COMPANYCITY . ', ' . $COMPANYCOUNTRY); ?>
                                    </p>

                                    <span
                                        class=" badge <?php echo $COMPANYSTATUS == 'Active' ? 'badge-success' : 'badge-warning'; ?>">
                                        <i
                                            class="lni lni-<?php echo $COMPANYSTATUS == 'Active' ? 'checkmark-circle' : 'warning'; ?>"></i>
                                        <?php echo htmlspecialchars($COMPANYSTATUS); ?>
                                    </span>
                                </div>

                                <!-- Contact Information -->
                                <div class="card-body-section">
                                    <div class="section-header">
                                        <div class="section-icon">
                                            <i class="lni lni-phone"></i>
                                        </div>
                                        <h5 class="section-title">Contact Information</h5>
                                    </div>

                                    <div class="info-grid">
                                        <!-- Company ID -->
                                        <div class="info-row">
                                            <div class="info-icon">
                                                <i class="lni lni-key"></i>
                                            </div>
                                            <div class="info-content">
                                                <span class="info-label">Company ID</span>
                                                <div class="info-value">#<?php echo $COMPANYID; ?></div>
                                            </div>
                                        </div>

                                        <!-- Email -->
                                        <?php if (!empty($COMPANYEMAIL)): ?>
                                        <div class="info-row">
                                            <div class="info-icon">
                                                <i class="lni lni-envelope"></i>
                                            </div>
                                            <div class="info-content">
                                                <span class="info-label">Email Address</span>
                                                <div class="info-value">
                                                    <a href="mailto:<?php echo htmlspecialchars($COMPANYEMAIL); ?>">
                                                        <?php echo htmlspecialchars($COMPANYEMAIL); ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Phone -->
                                        <?php if (!empty($COMPANYCONTACTNO)): ?>
                                        <div class="info-row">
                                            <div class="info-icon">
                                                <i class="lni lni-phone"></i>
                                            </div>
                                            <div class="info-content">
                                                <span class="info-label">Phone Number</span>
                                                <div class="info-value">
                                                    <a href="tel:<?php echo htmlspecialchars($COMPANYCONTACTNO); ?>">
                                                        <?php echo htmlspecialchars($COMPANYCONTACTNO); ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Website -->
                                        <?php if (!empty($COMPANYWEBSITE)): ?>
                                        <div class="info-row">
                                            <div class="info-icon">
                                                <i class="lni lni-world"></i>
                                            </div>
                                            <div class="info-content">
                                                <span class="info-label">Website</span>
                                                <div class="info-value">
                                                    <a href="<?php echo htmlspecialchars($COMPANYWEBSITE); ?>"
                                                        target="_blank">
                                                        <?php echo htmlspecialchars($COMPANYWEBSITE); ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Address -->
                                        <?php if (!empty($COMPANYADDRESS)): ?>
                                        <div class="info-row">
                                            <div class="info-icon">
                                                <i class="lni lni-map-marker"></i>
                                            </div>
                                            <div class="info-content">
                                                <span class="info-label">Address</span>
                                                <div class="info-value"><?php echo htmlspecialchars($COMPANYADDRESS); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Industry -->
                                        <?php if (!empty($COMPANYINDUSTRY)): ?>
                                        <div class="info-row">
                                            <div class="info-icon">
                                                <i class="lni lni-briefcase"></i>
                                            </div>
                                            <div class="info-content">
                                                <span class="info-label">Industry</span>
                                                <div class="info-value">
                                                    <?php echo htmlspecialchars($COMPANYINDUSTRY); ?></div>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Company Size -->
                                        <?php if (!empty($COMPANYSIZE)): ?>
                                        <div class="info-row">
                                            <div class="info-icon">
                                                <i class="lni lni-users"></i>
                                            </div>
                                            <div class="info-content">
                                                <span class="info-label">Company Size</span>
                                                <div class="info-value"><?php echo htmlspecialchars($COMPANYSIZE); ?>
                                                    employees</div>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Year Founded -->
                                        <?php if (!empty($COMPANYYEARFOUNDED)): ?>
                                        <div class="info-row">
                                            <div class="info-icon">
                                                <i class="lni lni-calendar"></i>
                                            </div>
                                            <div class="info-content">
                                                <span class="info-label">Year Founded</span>
                                                <div class="info-value">
                                                    <?php echo htmlspecialchars($COMPANYYEARFOUNDED); ?></div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Social Media Links -->
                                    <?php if (!empty($COMPANYLINKEDIN) || !empty($COMPANYFACEBOOK) || !empty($COMPANYTWITTER)): ?>
                                    <div class="section-header mt-4">
                                        <div class="section-icon">
                                            <i class="lni lni-share"></i>
                                        </div>
                                        <h5 class="section-title">Social Media</h5>
                                    </div>
                                    <div class="social-links">
                                        <?php if (!empty($COMPANYLINKEDIN)): ?>
                                        <a href="<?php echo htmlspecialchars($COMPANYLINKEDIN); ?>" target="_blank"
                                            class="social-btn" title="LinkedIn">
                                            <i class="lni lni-linkedin-original"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if (!empty($COMPANYFACEBOOK)): ?>
                                        <a href="<?php echo htmlspecialchars($COMPANYFACEBOOK); ?>" target="_blank"
                                            class="social-btn" title="Facebook">
                                            <i class="lni lni-facebook-filled"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if (!empty($COMPANYTWITTER)): ?>
                                        <a href="<?php echo htmlspecialchars($COMPANYTWITTER); ?>" target="_blank"
                                            class="social-btn" title="Twitter">
                                            <i class="lni lni-twitter-original"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Action Buttons -->
                                <div class="action-buttons">
                                    <a href="dashboard-company-edit.php?companyid=<?php echo $COMPANYID; ?>"
                                        class="btn-primary-action">
                                        <i class="lni lni-pencil"></i>
                                        <span>Edit Company Profile</span>
                                    </a>
                                    <a href="<?php echo $path?>employer-detail.php?companyid=<?php echo $COMPANYID; ?>"
                                        target="_blank" class="btn-secondary-action">
                                        <i class="lni lni-eye"></i>
                                        <span>View Public Profile</span>
                                    </a>
                                </div>
                            </div>

                            <!-- Quick Actions Panel -->
                            <div class="quick-actions-panel">
                                <h5 class="quick-actions-title">Quick Actions</h5>
                                <a href="dashboard-post-job.php" class="quick-action-link">
                                    <div class="quick-action-icon">
                                        <i class="lni lni-plus"></i>
                                    </div>
                                    <span>Post New Job</span>
                                </a>
                                <a href="dashboard-manage-jobs.php" class="quick-action-link">
                                    <div class="quick-action-icon">
                                        <i class="lni lni-briefcase"></i>
                                    </div>
                                    <span>Manage Jobs</span>
                                </a>
                                <a href="dashboard-manage-applications.php" class="quick-action-link">
                                    <div class="quick-action-icon">
                                        <i class="lni lni-users"></i>
                                    </div>
                                    <span>View Applications</span>
                                </a>
                            </div>
                        </div>

                        <!-- ==================== MAIN CONTENT COLUMN ==================== -->
                        <div class="col-lg-8 col-md-12">
                            <!-- About Company Section -->
                            <div class="company-card">
                                <div class="card-body-section">
                                    <div class="section-header">
                                        <div class="section-icon">
                                            <i class="lni lni-information"></i>
                                        </div>
                                        <h5 class="section-title">About <?php echo htmlspecialchars($COMPANYNAME); ?>
                                        </h5>
                                    </div>
                                    <?php if (!empty($COMPANYABOUT)): ?>
                                    <p class="about-text"><?php echo nl2br(htmlspecialchars($COMPANYABOUT)); ?></p>
                                    <?php else: ?>
                                    <div class="empty-state">
                                        <i class="lni lni-write"></i>
                                        <p class="empty-state-text">No company description added yet.</p>
                                        <a href="dashboard-company-edit.php?companyid=<?php echo $COMPANYID; ?>"
                                            class="btn-primary-action d-inline-flex">
                                            <i class="lni lni-plus"></i>
                                            <span>Add Description</span>
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Company Specialisms Section -->
                            <?php if (!empty($COMPANYSPECIALISM)): ?>
                            <div class="company-card">
                                <div class="card-body-section">
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
                                            <div class="specialism-text"><?php echo htmlspecialchars($specialism); ?>
                                            </div>
                                        </div>
                                        <?php 
                                            endif;
                                        endforeach; 
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Awards & Recognition Section -->
                            <?php if (!empty($COMPANYAWARD)): ?>
                            <div class="company-card">
                                <div class="card-body-section">
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
                                        <p class="award-description"><?php echo htmlspecialchars($COMPANYAWARDDESC); ?>
                                        </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- ==================== FOOTER ==================== -->
                <?php include 'footer.php' ?>
            </div>
        </div>

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