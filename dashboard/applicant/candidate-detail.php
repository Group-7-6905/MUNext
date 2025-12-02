<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';

$applicantId = $session_id;


// Get application statistics
$statsQuery = "SELECT 
    COUNT(*) as total_applications,
    SUM(CASE WHEN APPLICATIONSTATUS = 'Pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN APPLICATIONSTATUS = 'Reviewed' THEN 1 ELSE 0 END) as reviewed,
    SUM(CASE WHEN APPLICATIONSTATUS = 'Approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN APPLICATIONSTATUS = 'Hire' THEN 1 ELSE 0 END) as hired,
    SUM(CASE WHEN APPLICATIONSTATUS = 'Rejected' THEN 1 ELSE 0 END) as rejected
FROM tbljobapplication 
WHERE APPLICANTID = ?";

$stmtStats = mysqli_prepare($con, $statsQuery);
mysqli_stmt_bind_param($stmtStats, "i", $applicantId);
mysqli_stmt_execute($stmtStats);
$statsResult = mysqli_stmt_get_result($stmtStats);
$stats = mysqli_fetch_assoc($statsResult);

// Get saved jobs count
$savedQuery = "SELECT COUNT(*) as saved_count FROM tblbookmarkjob WHERE APPLICANTID = ?";
$stmtSaved = mysqli_prepare($con, $savedQuery);
mysqli_stmt_bind_param($stmtSaved, "i", $applicantId);
mysqli_stmt_execute($stmtSaved);
$savedResult = mysqli_stmt_get_result($stmtSaved);
$savedCount = mysqli_fetch_assoc($savedResult)['saved_count'];

// Calculate age
$age = '';
if (!empty($BIRTHDATE)) {
    $birthDate = new DateTime($BIRTHDATE);
    $today = new DateTime();
    $ageYears = $today->diff($birthDate)->y;
    $age = $ageYears . ' years old';
}

// Parse skills
$skillsArray = !empty($SKILLS) ? array_map('trim', explode(',', $SKILLS)) : [];

// Format date
$formattedBirthdate = !empty($BIRTHDATE) ? date('F d, Y', strtotime($BIRTHDATE)) : 'Not provided';
?>

<head>
    <meta charset="utf-8" />
    <meta name="author" content="MUNext" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php echo htmlspecialchars($FULLNAME); ?> - Professional Profile on MUNext">

    <title><?php echo htmlspecialchars($FULLNAME); ?> - My Profile | MUNext</title>

    <link href="assets/css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.lineicons.com/3.0/lineicons.css">

    <link rel="stylesheet" href="assets/css/custom-style.css">


</head>

<body>
    <!-- <div class="preloader"></div> -->

    <div id="main-wrapper">
        <?php include 'header.php' ?>
        <div class="clearfix"></div>
        <div class="clearfix"></div>

        <div class="dashboard-wrap bg-light">
            <a class="mobNavigation" data-toggle="collapse" href="#MobNav" role="button" aria-expanded="false">
                <i class="fas fa-bars mr-2"></i>Dashboard Navigation
            </a>
            <?php include 'sidenav.php' ?>

            <div class="dashboard-content">




                <div class="dashboard-tlbar">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12">
                            <h1>
                                <i class="lni lni-user mr-2"></i>Professional Profile
                            </h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item text-muted"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item text-muted"><a href="./">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="#" class="theme-cl">My Profile</a></li>
                                </ol>
                            </nav>
                            <p class="mb-0">View and manage your professional information</p>
                        </div>
                    </div>
                </div>




                <!-- Main Content -->
                <section class="middle pb-5">
                    <div class="container">
                        <div class="row">

                            <!-- Left Sidebar -->
                            <div class="col-lg-4 col-md-12 mb-4">
                                <div class="profile-card">
                                    <!-- Profile Photo Section -->
                                    <div class="profile-photo-section theme-bg">
                                        <div class="profile-photo-wrapper">
                                            <img src="<?php echo htmlspecialchars($path . $APPLICANTPHOTO); ?>"
                                                class="profile-photo" alt="<?php echo htmlspecialchars($FULLNAME); ?>"
                                                onerror="this.src='assets/img/avatar-default.svg'">
                                            <div class="profile-completion-badge"
                                                title="Profile <?php echo $profileCompletion; ?>% Complete">
                                                <?php echo $profileCompletion; ?>%
                                            </div>
                                        </div>

                                        <h4 class="fs-lg ft-medium mb-0 lh-1 p-2 text-white">
                                            <?php echo htmlspecialchars($FULLNAME); ?>
                                        </h4>
                                        <p class="m-0 p-0 text-white">
                                            <?php echo htmlspecialchars($JOBTITLE ?: 'Job Seeker'); ?></p>
                                        <p class="text-white smalls">
                                            <i class="lni lni-map-marker mr-1 text-white"></i>
                                            <?php echo htmlspecialchars(($CITY ? $CITY . ', ' : '') . $COUNTRY); ?>
                                        </p>
                                    </div>

                                    <!-- Status Badges -->
                                    <div class="status-badges">
                                        <span class="badge-custom badge-active">
                                            <i class="lni lni-checkmark-circle mr-1"></i>Active
                                        </span>
                                        <?php if (!empty($SUBCATEGORY)): ?>
                                        <span class="badge-custom badge-category">
                                            <?php echo htmlspecialchars($SUBCATEGORY); ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Quick Stats -->
                                    <div class="quick-stats">
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="stat-item">
                                                    <span
                                                        class="stat-number"><?php echo $stats['total_applications']; ?></span>
                                                    <span class="stat-label">Applied</span>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="stat-item">
                                                    <span class="stat-number"><?php echo $stats['hired']; ?></span>
                                                    <span class="stat-label">Hired</span>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="stat-item">
                                                    <span class="stat-number"><?php echo $savedCount; ?></span>
                                                    <span class="stat-label">Saved</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Contact Info -->
                                    <div class="contact-info">
                                        <?php if (!empty($CONTACTNO)): ?>
                                        <div class="contact-item">
                                            <i class="lni lni-phone"></i>
                                            <div class="contact-item-content">
                                                <span class="contact-item-label">Phone</span>
                                                <a href="tel:<?php echo htmlspecialchars($CONTACTNO); ?>">
                                                    <?php echo htmlspecialchars($CONTACTNO); ?>
                                                </a>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <?php if (!empty($EMAIL)): ?>
                                        <div class="contact-item">
                                            <i class="lni lni-envelope"></i>
                                            <div class="contact-item-content">
                                                <span class="contact-item-label">Email</span>
                                                <a href="mailto:<?php echo htmlspecialchars($EMAIL); ?>">
                                                    <?php echo htmlspecialchars($EMAIL); ?>
                                                </a>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <?php if (!empty($DEGREE)): ?>
                                        <div class="contact-item">
                                            <i class="lni lni-graduation"></i>
                                            <div class="contact-item-content">
                                                <span class="contact-item-label">Education</span>
                                                <?php echo htmlspecialchars($DEGREE); ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <?php if (!empty($USERNAME)): ?>
                                        <div class="contact-item">
                                            <i class="lni lni-user"></i>
                                            <div class="contact-item-content">
                                                <span class="contact-item-label">Username</span>
                                                <?php echo htmlspecialchars($USERNAME); ?>
                                                <?php if (!empty($SEX)): ?>
                                                (<?php echo htmlspecialchars($SEX); ?>)
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <?php if (!empty($BIRTHDATE)): ?>
                                        <div class="contact-item">
                                            <i class="lni lni-calendar"></i>
                                            <div class="contact-item-content">
                                                <span class="contact-item-label">Birth Date</span>
                                                <?php echo $formattedBirthdate; ?>
                                                <?php if (!empty($age)): ?>
                                                <br><small class="text-muted"><?php echo $age; ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Social Links -->
                                    <?php if (!empty($FB_link) || !empty($LinkedIn_link)): ?>
                                    <div class="social-links">
                                        <?php if (!empty($FB_link)): ?>
                                        <a href="<?php echo htmlspecialchars($FB_link); ?>" target="_blank"
                                            class="social-link" title="Facebook">
                                            <i class="lni lni-facebook-filled"></i>
                                        </a>
                                        <?php endif; ?>

                                        <?php if (!empty($LinkedIn_link)): ?>
                                        <a href="<?php echo htmlspecialchars($LinkedIn_link); ?>" target="_blank"
                                            class="social-link" title="LinkedIn">
                                            <i class="lni lni-linkedin-original"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Resume Download -->
                                    <?php if (!empty($RESUME)): ?>
                                    <div class="action-buttons">
                                        <a href="<?php echo htmlspecialchars($path . $RESUME); ?>"
                                            class="btn btn-outline-secondary btn-block mb-2" download>
                                            <i class="lni lni-download mr-2"></i>Download Resume
                                        </a>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Action Buttons -->
                                    <div class="action-buttons">
                                        <a href="dashboard-my-profile.php"
                                            class="btn btn-primary-custom btn-block mb-3">
                                            <i class="lni lni-pencil mr-2"></i>Edit Profile
                                        </a>
                                        <a href="index.php" class="btn btn-outline-secondary btn-block">
                                            <i class="lni lni-arrow-left mr-2"></i>Back
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Content -->
                            <div class="col-lg-8 col-md-12">

                                <!-- Profile Completion Alert -->
                                <?php if ($profileCompletion < 100): ?>
                                <div class="completion-alert">
                                    <div class="d-flex align-items-center">
                                        <i class="lni lni-information"></i>
                                        <div class="flex-grow-1">
                                            <strong>Your profile is <?php echo $profileCompletion; ?>% complete</strong>
                                            <p class="mb-0 mt-1">Complete your profile to increase your chances of
                                                getting hired
                                                by employers!</p>
                                        </div>
                                        <a href="dashboard-my-profile.php" class="btn btn-warning btn-sm ml-3">
                                            Complete Now
                                        </a>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <!-- Application Statistics -->
                                <div class="section-card">
                                    <h5 class="section-title">
                                        <i class="lni lni-bar-chart section-icon"></i>Application Statistics
                                    </h5>
                                    <div class="stats-grid">
                                        <div class="stat-card text-dark">
                                            <span class="stat-card-number"><?php echo $stats['pending']; ?></span>
                                            <span class="stat-card-label">Pending</span>
                                        </div>
                                        <div class="stat-card text-dark">
                                            <span class="stat-card-number"><?php echo $stats['reviewed']; ?></span>
                                            <span class="stat-card-label">Reviewed</span>
                                        </div>
                                        <div class="stat-card text-dark">
                                            <span class="stat-card-number"><?php echo $stats['approved']; ?></span>
                                            <span class="stat-card-label">Approved</span>
                                        </div>
                                        <div class="stat-card text-dark">
                                            <span class="stat-card-number"><?php echo $stats['rejected']; ?></span>
                                            <span class="stat-card-label">Rejected</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- About Section -->
                                <?php if (!empty($ABOUTME)): ?>
                                <div class="section-card">
                                    <h5 class="section-title">
                                        <i class="lni lni-user section-icon"></i>About Me
                                    </h5>
                                    <p class="about-text">
                                        <?php echo nl2br(htmlspecialchars($ABOUTME)); ?>
                                    </p>
                                </div>
                                <?php endif; ?>

                                <!-- Contact Information -->
                                <div class="section-card">
                                    <h5 class="section-title">
                                        <i class="lni lni-phone section-icon"></i>Contact Information
                                    </h5>
                                    <div class="info-grid">
                                        <?php if (!empty($COUNTRY)): ?>
                                        <div class="info-item">
                                            <i class="lni lni-flag"></i>
                                            <span class="info-label">Country</span>
                                            <span class="info-value"><?php echo htmlspecialchars($COUNTRY); ?></span>
                                        </div>
                                        <?php endif; ?>

                                        <?php if (!empty($CITY)): ?>
                                        <div class="info-item">
                                            <i class="lni lni-map-marker"></i>
                                            <span class="info-label">City</span>
                                            <span class="info-value"><?php echo htmlspecialchars($CITY); ?></span>
                                        </div>
                                        <?php endif; ?>

                                        <?php if (!empty($ADDRESS)): ?>
                                        <div class="info-item">
                                            <i class="lni lni-home"></i>
                                            <span class="info-label">Address</span>
                                            <span class="info-value"><?php echo htmlspecialchars($ADDRESS); ?></span>
                                        </div>
                                        <?php endif; ?>

                                        <?php if (!empty($CONTACTNO)): ?>
                                        <div class="info-item">
                                            <i class="lni lni-phone"></i>
                                            <span class="info-label">Phone</span>
                                            <span class="info-value">
                                                <a href="tel:<?php echo htmlspecialchars($CONTACTNO); ?>">
                                                    <?php echo htmlspecialchars($CONTACTNO); ?>
                                                </a>
                                            </span>
                                        </div>
                                        <?php endif; ?>

                                        <?php if (!empty($EMAIL)): ?>
                                        <div class="info-item">
                                            <i class="lni lni-envelope"></i>
                                            <span class="info-label">Email</span>
                                            <span class="info-value">
                                                <a href="mailto:<?php echo htmlspecialchars($EMAIL); ?>">
                                                    <?php echo htmlspecialchars($EMAIL); ?>
                                                </a>
                                            </span>
                                        </div>
                                        <?php endif; ?>

                                        <?php if (!empty($FB_link)): ?>
                                        <div class="info-item">
                                            <i class="lni lni-facebook"></i>
                                            <span class="info-label">Facebook</span>
                                            <span class="info-value">
                                                <a href="<?php echo htmlspecialchars($FB_link); ?>" target="_blank">
                                                    View Profile
                                                </a>
                                            </span>
                                        </div>
                                        <?php endif; ?>

                                        <?php if (!empty($LinkedIn_link)): ?>
                                        <div class="info-item">
                                            <i class="lni lni-linkedin"></i>
                                            <span class="info-label">LinkedIn</span>
                                            <span class="info-value">
                                                <a href="<?php echo htmlspecialchars($LinkedIn_link); ?>"
                                                    target="_blank">
                                                    View Profile
                                                </a>
                                            </span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Education -->
                                <?php if (!empty($DEGREE) || !empty($SCHOOLNAME)): ?>
                                <div class="section-card">
                                    <h5 class="section-title">
                                        <i class="lni lni-graduation section-icon"></i>Most Recent Education
                                    </h5>
                                    <div class="education-item">
                                        <?php if (!empty($DEGREE)): ?>
                                        <h6 class="item-title"><?php echo htmlspecialchars($DEGREE); ?></h6>
                                        <?php endif; ?>

                                        <?php if (!empty($SCHOOLNAME)): ?>
                                        <div class="item-subtitle">
                                            <i class="lni lni-graduation"></i>
                                            <span><?php echo htmlspecialchars($SCHOOLNAME); ?></span>
                                        </div>
                                        <?php endif; ?>

                                        <?php if (!empty($RESUME)): ?>
                                        <p class="mt-2 mb-0 text-muted">
                                            <small><i>Download resume/CV for complete education history</i></small>
                                        </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <!-- Work Experience -->
                                <?php if (!empty($EXCOMPANYNAME) || !empty($EXJOBTITLE)): ?>
                                <div class="section-card">
                                    <h5 class="section-title">
                                        <i class="lni lni-briefcase section-icon"></i>Most Recent Experience
                                    </h5>
                                    <div class="experience-item">
                                        <?php if (!empty($EXJOBTITLE)): ?>
                                        <h6 class="item-title"><?php echo htmlspecialchars($EXJOBTITLE); ?></h6>
                                        <?php endif; ?>

                                        <?php if (!empty($EXCOMPANYNAME)): ?>
                                        <div class="item-subtitle">
                                            <i class="lni lni-apartment"></i>
                                            <span><?php echo htmlspecialchars($EXCOMPANYNAME); ?></span>
                                        </div>
                                        <?php endif; ?>

                                        <?php if (!empty($RESUME)): ?>
                                        <p class="mt-2 mb-0 text-muted">
                                            <small><i>Download resume/CV for complete work history</i></small>
                                        </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <!-- Skills -->
                                <?php if (!empty($skillsArray)): ?>
                                <div class="section-card">
                                    <h5 class="section-title">
                                        <i class="lni lni-cog section-icon"></i>Skills & Expertise
                                    </h5>
                                    <div class="d-flex flex-wrap">
                                        <?php foreach ($skillsArray as $skill): ?>
                                        <span class="skill-tag"><?php echo htmlspecialchars($skill); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </section>

                <?php include 'footer.php'; ?>
            </div>
        </div>


        <a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>
    </div>

    <!-- Scripts -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/slick.js"></script>
    <script src="assets/js/slider-bg.js"></script>
    <script src="assets/js/smoothproducts.js"></script>
    <script src="assets/js/snackbar.min.js"></script>
    <script src="assets/js/jQuery.style.switcher.js"></script>
    <script src="assets/js/custom.js"></script>

    <script>
    $(document).ready(function() {
        // Smooth scroll to sections
        $('a[href^="#"]').on('click', function(e) {
            var target = $(this.getAttribute('href'));
            if (target.length) {
                e.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 100
                }, 800);
            }
        });
    });
    </script>
</body>

</html>