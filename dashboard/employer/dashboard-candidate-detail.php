<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';

// Get and validate applicant ID
$applicantId = isset($_GET['applicantid']) ? (int)$_GET['applicantid'] : 0;
$jobApplicationId = isset($_GET['jobapplicationid']) ? (int)$_GET['jobapplicationid'] : 0;

if (empty($applicantId)) {
    header('Location: dashboard-manage-applications.php');
    exit();
}

// Fetch applicant data with prepared statement
$query = "SELECT 
    u.USERID, u.FNAME, u.ONAME, u.EMAIL, u.USERNAME,
    a.APPLICANTID, a.FNAME as APP_FNAME, a.OTHERNAMES as APP_ONAME, 
    a.FNAME, a.OTHERNAMES, a.APPLICANTPHOTO, a.JOBCATEGORYID, a.JOBTITLE, 
    a.EXCOMPANYNAME, a.EXJOBTITLE, a.ABOUTME, a.FULLADDRESS as ADDRESS, 
    a.COUNTRY, a.CITY, a.SEX, a.BIRTHDATE, a.CONTACTNO, 
    a.DEGREE, a.SCHOOLNAME, a.SKILLS, a.FB_link, a.LinkedIn_link,
    jsc.SUBCATEGORY
FROM tblusers u
LEFT JOIN tblapplicants a ON u.USERID = a.USERID
LEFT JOIN tbljobsubcategory jsc ON a.JOBCATEGORYID = jsc.ID
WHERE u.USERID = ?";

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $applicantId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header('Location: dashboard-manage-applications.php');
    exit();
}

$candidate = mysqli_fetch_assoc($result);

// Extract candidate data with defaults
$FULLNAME = $candidate['FNAME'].', '.$candidate['OTHERNAMES'] ?? trim(($candidate['APP_FNAME'] ?? '') . ' ' . ($candidate['APP_ONAME'] ?? ''));
$EMAIL = $candidate['EMAIL'] ?? '';
$USERNAME = $candidate['USERNAME'] ?? '';
$APPLICANTPHOTO = $candidate['APPLICANTPHOTO'] ?? 'assets/img/avatar-default.svg';
$JOBTITLE = $candidate['JOBTITLE'] ?? 'N/A';
$CITY = $candidate['CITY'] ?? 'N/A';
$COUNTRY = $candidate['COUNTRY'] ?? 'N/A';
$SUBCATEGORY = $candidate['SUBCATEGORY'] ?? 'N/A';
$SKILLS = $candidate['SKILLS'] ?? 'N/A';
$CONTACTNO = $candidate['CONTACTNO'] ?? 'N/A';
$DEGREE = $candidate['DEGREE'] ?? 'N/A';
$SEX = $candidate['SEX'] ?? 'N/A';
$BIRTHDATE = $candidate['BIRTHDATE'] ?? 'N/A';
$ABOUTME = $candidate['ABOUTME'] ?? 'No information provided';
$ADDRESS = $candidate['ADDRESS'] ?? 'N/A';
$SCHOOLNAME = $candidate['SCHOOLNAME'] ?? 'N/A';
$EXCOMPANYNAME = $candidate['EXCOMPANYNAME'] ?? 'N/A';
$EXJOBTITLE = $candidate['EXJOBTITLE'] ?? 'N/A';
$FB_link = $candidate['FB_link'] ?? '';
$LinkedIn_link = $candidate['LinkedIn_link'] ?? '';

// Fetch resume if job application ID is provided
$RESUME = '';
$COVERLETTER = '';
$APPLICATION_STATUS = '';
$DATE_APPLIED = '';

if (!empty($jobApplicationId)) {
    $appQuery = "SELECT RESUME, COVERLETTER, APPLICATIONSTATUS, DATEAPPLIED 
                 FROM tbljobapplication 
                 WHERE ID = ?";
    $stmtApp = mysqli_prepare($con, $appQuery);
    mysqli_stmt_bind_param($stmtApp, "i", $jobApplicationId);
    mysqli_stmt_execute($stmtApp);
    $appResult = mysqli_stmt_get_result($stmtApp);
    
    if ($appData = mysqli_fetch_assoc($appResult)) {
        $RESUME = $appData['RESUME'] ?? '';
        $COVERLETTER = $appData['COVERLETTER'] ?? '';
        $APPLICATION_STATUS = $appData['APPLICATIONSTATUS'] ?? '';
        $DATE_APPLIED = $appData['DATEAPPLIED'] ?? '';
    }
}

// Calculate age if birthdate is available
$age = '';
if (!empty($BIRTHDATE) && $BIRTHDATE != 'N/A') {
    $birthDate = new DateTime($BIRTHDATE);
    $today = new DateTime();
    $ageYears = $today->diff($birthDate)->y;
    $age = $ageYears . ' years old';
}

// Parse skills into array
$skillsArray = [];
if (!empty($SKILLS) && $SKILLS != 'N/A') {
    $skillsArray = array_map('trim', explode(',', $SKILLS));
}

?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($FULLNAME); ?> - Candidate Profile | MUNext</title>

    <link href="assets/css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.lineicons.com/3.0/lineicons.css">
    <link href="assets/css/custom-style.css" rel="stylesheet">


</head>

<body>
    <!-- <div class="preloader"></div> -->

    <div id="main-wrapper">
        <?php include 'header.php' ?>
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
                                <i class="lni lni-briefcase"></i> Candidate Profile
                            </h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item text-muted"><a href="index.php">Dashboard</a></li>
                                    <li class="breadcrumb-item text-muted"><a
                                            href="dashboard-manage-applications.php">Applications</a></li>
                                    <li class="breadcrumb-item"><a href="#" class="theme-cl">Manage Jobs</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <section class="middle pb-5">
                    <div class="dashboard-widg-bar d-block">
                        <div class="container">
                            <div class="row">

                                <!-- Left Sidebar -->
                                <div class="col-lg-4 col-md-12 mb-4">
                                    <div class="profile-sidebar">
                                        <!-- Profile Header -->
                                        <div class="profile-header-card theme-bg">
                                            <img src="<?php echo htmlspecialchars($path . $APPLICANTPHOTO); ?>"
                                                class="profile-photo" alt="<?php echo htmlspecialchars($FULLNAME); ?>"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">

                                            <h4 class="profile-name"><?php echo htmlspecialchars($FULLNAME); ?></h4>
                                            <p class="profile-title"><?php echo htmlspecialchars($JOBTITLE); ?></p>
                                            <p class="profile-location">
                                                <i class="lni lni-map-marker mr-1"></i>
                                                <?php echo htmlspecialchars($CITY . ', ' . $COUNTRY); ?>
                                            </p>
                                        </div>

                                        <!-- Status Badges -->
                                        <div class="status-badges mt-4 p-3">
                                            <span class="badge-custom badge-active mr-2">
                                                <i class="lni lni-checkmark-circle"></i> Open To Work
                                            </span>
                                            <?php if (!empty($SUBCATEGORY) && $SUBCATEGORY != 'N/A'): ?>
                                            <span class="badge-custom badge-category">
                                                <?php echo htmlspecialchars($SUBCATEGORY); ?>
                                            </span>
                                            <?php endif; ?>
                                            <?php if (!empty($APPLICATION_STATUS)): ?>
                                            <!-- <span class="badge-custom badge-status">
                                                <?php echo htmlspecialchars($APPLICATION_STATUS); ?>
                                            </span> -->
                                            <?php endif; ?>
                                        </div>

                                        <!-- Quick Contact Info -->
                                        <div class="contact-info-card">
                                            <div class="contact-item">
                                                <i class="lni lni-phone"></i>
                                                <div class="contact-item-content">
                                                    <a href="tel:<?php echo htmlspecialchars($CONTACTNO); ?>">
                                                        <?php echo htmlspecialchars($CONTACTNO); ?>
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="contact-item">
                                                <i class="lni lni-envelope"></i>
                                                <div class="contact-item-content">
                                                    <a href="mailto:<?php echo htmlspecialchars($EMAIL); ?>">
                                                        <?php echo htmlspecialchars($EMAIL); ?>
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="contact-item">
                                                <i class="lni lni-graduation"></i>
                                                <div class="contact-item-content">
                                                    <?php echo htmlspecialchars($DEGREE); ?>
                                                </div>
                                            </div>

                                            <div class="contact-item">
                                                <i class="lni lni-user"></i>
                                                <div class="contact-item-content">
                                                    <?php echo htmlspecialchars($USERNAME . ' (' . $SEX . ')'); ?>
                                                </div>
                                            </div>

                                            <?php if (!empty($BIRTHDATE) && $BIRTHDATE != 'N/A'): ?>
                                            <div class="contact-item">
                                                <i class="lni lni-calendar"></i>
                                                <div class="contact-item-content">
                                                    <?php echo date('M d, Y', strtotime($BIRTHDATE)); ?>
                                                    <?php if (!empty($age)): ?>
                                                    <br><small class="text-muted"><?php echo $age; ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                            <?php if (!empty($DATE_APPLIED)): ?>
                                            <div class="contact">
                                                <i class="lni lni-calendar-alt"></i>
                                                <div class="contact-item-content">
                                                    <strong>Applied:</strong>
                                                    <?php echo date('M d, Y', strtotime($DATE_APPLIED)); ?>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Social Links -->
                                        <?php if (!empty($FB_link) || !empty($LinkedIn_link)): ?>
                                        <div class="action-buttons" style="border-top: none; padding-top: 10px;">
                                            <div class="social-links justify-content-center">
                                                <?php if (!empty($FB_link)): ?>
                                                <a href="<?php echo htmlspecialchars($FB_link); ?>" target="_blank"
                                                    class="social-link theme-bg" title="Facebook">
                                                    <i class="lni lni-facebook-filled"></i>
                                                </a>
                                                <?php endif; ?>

                                                <?php if (!empty($LinkedIn_link)): ?>
                                                <a href="<?php echo htmlspecialchars($LinkedIn_link); ?>"
                                                    target="_blank" class="social-link theme-bg" title="LinkedIn">
                                                    <i class="lni lni-linkedin-original"></i>
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Action Buttons -->
                                        <div class="action-buttons">
                                            <?php if (!empty($RESUME)): ?>
                                            <form method="post" class="w-100">
                                                <input type="hidden" name="resume_url"
                                                    value="<?php echo htmlspecialchars($RESUME); ?>">
                                                <button type="submit" name="download_resume"
                                                    class="btn btn-primary-custom">
                                                    <i class="lni lni-download mr-2"></i>Download Resume
                                                </button>
                                            </form>
                                            <?php endif; ?>

                                            <a href="mailto:<?php echo htmlspecialchars($EMAIL); ?>"
                                                class="btn btn-secondary-custom">
                                                <i class="lni lni-envelope mr-2"></i>Send Email
                                            </a>

                                            <a href="javascript:history.back()"
                                                class="btn btn-outline-secondary mt-2 w-100">
                                                <i class="lni lni-arrow-left mr-2"></i>Back
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Content -->
                                <div class="col-lg-8 col-md-12">

                                    <!-- About Section -->
                                    <?php if (!empty($ABOUTME) && $ABOUTME != 'No information provided'): ?>
                                    <div class="content-card">
                                        <h5 class="section-title">
                                            <i class="lni lni-user section-icon"></i>About
                                            <?php echo htmlspecialchars($FULLNAME); ?>
                                        </h5>
                                        <p style="line-height: 1.8; color: var(--text-dark);">
                                            <?php echo nl2br(htmlspecialchars($ABOUTME)); ?>
                                        </p>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Cover Letter -->
                                    <?php if (!empty($COVERLETTER)): ?>
                                    <div class="content-card">
                                        <h5 class="section-title">
                                            <i class="lni lni-files section-icon"></i>Cover Letter
                                        </h5>
                                        <div class="cover-letter-box">
                                            <?php echo (htmlspecialchars($COVERLETTER)); ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Contact Information -->
                                    <div class="content-card">
                                        <h5 class="section-title">
                                            <i class="lni lni-phone section-icon"></i>Contact Information
                                        </h5>
                                        <div class="contact-grid">
                                            <div class="contact-grid-item">
                                                <i class="lni lni-flag"></i>
                                                <span class="contact-label">Country</span>
                                                <span
                                                    class="contact-value"><?php echo htmlspecialchars($COUNTRY); ?></span>
                                            </div>

                                            <div class="contact-grid-item">
                                                <i class="lni lni-map-marker"></i>
                                                <span class="contact-label">City</span>
                                                <span
                                                    class="contact-value"><?php echo htmlspecialchars($CITY); ?></span>
                                            </div>

                                            <div class="contact-grid-item">
                                                <i class="lni lni-home"></i>
                                                <span class="contact-label">Address</span>
                                                <span
                                                    class="contact-value"><?php echo htmlspecialchars($ADDRESS); ?></span>
                                            </div>

                                            <div class="contact-grid-item">
                                                <i class="lni lni-phone"></i>
                                                <span class="contact-label">Phone</span>
                                                <span class="contact-value">
                                                    <a href="tel:<?php echo htmlspecialchars($CONTACTNO); ?>">
                                                        <?php echo htmlspecialchars($CONTACTNO); ?>
                                                    </a>
                                                </span>
                                            </div>

                                            <div class="contact-grid-item">
                                                <i class="lni lni-envelope"></i>
                                                <span class="contact-label">Email</span>
                                                <span class="contact-value">
                                                    <a href="mailto:<?php echo htmlspecialchars($EMAIL); ?>">
                                                        <?php echo htmlspecialchars($EMAIL); ?>
                                                    </a>
                                                </span>
                                            </div>

                                            <?php if (!empty($FB_link)): ?>
                                            <div class="contact-grid-item">
                                                <i class="lni lni-facebook"></i>
                                                <span class="contact-label">Facebook</span>
                                                <span class="contact-value">
                                                    <a href="<?php echo htmlspecialchars($FB_link); ?>" target="_blank">
                                                        View Profile
                                                    </a>
                                                </span>
                                            </div>
                                            <?php endif; ?>

                                            <?php if (!empty($LinkedIn_link)): ?>
                                            <div class="contact-grid-item">
                                                <i class="lni lni-linkedin"></i>
                                                <span class="contact-label">LinkedIn</span>
                                                <span class="contact-value">
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
                                    <?php if (!empty($DEGREE) && $DEGREE != 'N/A'): ?>
                                    <div class="content-card">
                                        <h5 class="section-title">
                                            <i class="lni lni-graduation section-icon"></i>Education
                                        </h5>
                                        <div class="education-item">
                                            <h6 class="item-title"><?php echo htmlspecialchars($DEGREE); ?></h6>
                                            <?php if (!empty($SCHOOLNAME) && $SCHOOLNAME != 'N/A'): ?>
                                            <p class="item-subtitle">
                                                <i class="lni lni-graduation"></i>
                                                <?php echo htmlspecialchars($SCHOOLNAME); ?>
                                            </p>
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
                                    <?php if (!empty($EXCOMPANYNAME) && $EXCOMPANYNAME != 'N/A'): ?>
                                    <div class="content-card">
                                        <h5 class="section-title">
                                            <i class="lni lni-briefcase section-icon"></i>Work Experience
                                        </h5>
                                        <div class="experience-item">
                                            <?php if (!empty($EXJOBTITLE) && $EXJOBTITLE != 'N/A'): ?>
                                            <h6 class="item-title"><?php echo htmlspecialchars($EXJOBTITLE); ?></h6>
                                            <?php endif; ?>
                                            <p class="item-subtitle">
                                                <i class="lni lni-apartment"></i>
                                                <?php echo htmlspecialchars($EXCOMPANYNAME); ?>
                                            </p>
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
                                    <div class="content-card">
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
                    </div>
                </section>
                <?php include 'footer.php' ?>
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
    // Print functionality
    function printProfile() {
        window.print();
    }

    // Smooth scroll
    $(document).ready(function() {
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