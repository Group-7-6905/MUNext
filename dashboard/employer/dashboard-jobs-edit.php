<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';
unset($result); // Clear any $result from phpcode.php


// Check if job ID is provided
if (!empty($_GET['id'])) {
    $jobId = (int)$_GET['id'];
} else {
    header('Location: dashboard-manage-jobs.php');
    exit();
}

// ==================== FETCH JOB DETAILS ====================
$jobQuery = "SELECT * FROM tbljob WHERE JOBID = ?";
$stmtJob = mysqli_prepare($con, $jobQuery);
mysqli_stmt_bind_param($stmtJob, "i", $jobId);
mysqli_stmt_execute($stmtJob);
$jobResult = mysqli_stmt_get_result($stmtJob);

if ($jobRow = mysqli_fetch_array($jobResult)) {
    // Verify job belongs to this employer
    if ($jobRow['EMPLOYERID'] != $session_id) {
        $_SESSION['error_msg'] = "Unauthorized access to this job.";
        header('Location: dashboard-manage-jobs.php');
        exit();
    }
    
    $COMPANYID = $jobRow['COMPANYID'];
    $WORKPLACE_POLICY = $jobRow['WORKPLACE_POLICY'];
    $JOBTITLE = $jobRow['JOBTITLE'];
    $JOBCATEGORYID = $jobRow['JOBCATEGORYID'];
    $SALARY = $jobRow['SALARY'];
    $JOBTYPE = $jobRow['JOBTYPE'];
    $QUALIFICATION = $jobRow['QUALIFICATION'];
    $JOBDESCRIPTION = $jobRow['JOBDESCRIPTION'];
    $PREFEREDSEX = $jobRow['PREFEREDSEX'];
    $CAREERLEVEL = $jobRow['CAREERLEVEL'];
    $WORKEXPERIENCE = $jobRow['WORKEXPERIENCE'];
    $DEADLINE = $jobRow['DEADLINE'];
} else {
    $_SESSION['error_msg'] = "Job not found.";
    header('Location: dashboard-manage-jobs.php');
    exit();
}

// Get job category name
$categoryQuery = "SELECT SUBCATEGORY FROM tbljobsubcategory WHERE ID = ?";
$stmtCategory = mysqli_prepare($con, $categoryQuery);
mysqli_stmt_bind_param($stmtCategory, "i", $JOBCATEGORYID);
mysqli_stmt_execute($stmtCategory);
$categoryResult = mysqli_stmt_get_result($stmtCategory);
$categoryRow = mysqli_fetch_array($categoryResult);
$SUBCATEGORY = $categoryRow['SUBCATEGORY'];

// ==================== CHECK COMPANY STATUS ====================
$companyQuery = "SELECT * FROM tblcompany WHERE USERID = ?";
$stmtCompany = mysqli_prepare($con, $companyQuery);
mysqli_stmt_bind_param($stmtCompany, "i", $session_id);
mysqli_stmt_execute($stmtCompany);
$companyResult = mysqli_stmt_get_result($stmtCompany);

if ($companyRow = mysqli_fetch_array($companyResult)) {
    $COMPANYNAME = $companyRow['COMPANYNAME'];
    $COMPANYADDRESS = $companyRow['COMPANYADDRESS'];
    $COMPANYCOUNTRY = $companyRow['COMPANYCOUNTRY'];
    $COMPANYCITY = $companyRow['COMPANYCITY'];
    $COMPANYSTATUS = $companyRow['COMPANYSTATUS'];
    $COMPANYLOGO = $companyRow['COMPANYLOGO'];
} else {
    $_SESSION['error_msg'] = "Company profile not found.";
    header("Location: dashboard-company-add.php");
    exit();
}

// Check if company status is Active
$isCompanyActive = ($COMPANYSTATUS === 'Active');

// ==================== HANDLE JOB UPDATE ====================
if (isset($_POST['edit_job']) && $isCompanyActive) {
    // Get form data
    $job_title_select = mysqli_real_escape_string($con, $_POST['job_title_select']);
    $job_title_specify = mysqli_real_escape_string($con, $_POST['job_title_specify']);
    $job_categoryid = (int)$_POST['job_categoryid'];
    $workplace_policy = mysqli_real_escape_string($con, $_POST['workplace_policy']);
    $job_desc = mysqli_real_escape_string($con, $_POST['job_desc']);
    $job_type = mysqli_real_escape_string($con, $_POST['job_type']);
    $career_level = mysqli_real_escape_string($con, $_POST['career_level']);
    $experience = mysqli_real_escape_string($con, $_POST['experience']);
    $qualification = mysqli_real_escape_string($con, $_POST['qualification']);
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $salary = !empty($_POST['salary']) ? (int)$_POST['salary'] : "Not Specified";
    $deadline = mysqli_real_escape_string($con, $_POST['deadline']);
    
    // Determine final job title
    $job_title = ($job_title_select == "Others (Please specify)") ? $job_title_specify : $job_title_select;

    $jobStatus = autoApproveJobs() ? 'Active' : 'Pending';
    
    // Update job
    $updateQuery = "UPDATE tbljob SET 
        JOBTITLE = ?, 
        JOBCATEGORYID = ?, 
        WORKPLACE_POLICY = ?, 
        JOBDESCRIPTION = ?, 
        JOBTYPE = ?, 
        CAREERLEVEL = ?, 
        WORKEXPERIENCE = ?, 
        QUALIFICATION = ?, 
        PREFEREDSEX = ?, 
        SALARY = ?, 
        DEADLINE = ?,
        JOBSTATUS = ?
        WHERE JOBID = ? AND EMPLOYERID = ?";
    
    $stmt = mysqli_prepare($con, $updateQuery);
    mysqli_stmt_bind_param($stmt, "sisssssssissii", 
        $job_title, $job_categoryid, $workplace_policy, $job_desc,
        $job_type, $career_level, $experience, $qualification, $gender,
        $salary, $deadline, $jobStatus, $jobId, $session_id
    );
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success_msg'] = "Job updated successfully!";
        header("Location: dashboard-manage-jobs.php");
        exit();
    } else {
        $_SESSION['error_msg'] = "Failed to update job. Please try again.";
    }
    mysqli_stmt_close($stmt);
}
?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Job - Employer Dashboard | MUNext</title>
    <link href="assets/css/styles.css" rel="stylesheet">
    <link href="assets/css/custom-style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.lineicons.com/3.0/lineicons.css">
    <link rel="stylesheet" href="assets/css/plugins/theme.css">

</head>

<body>
    <div id="main-wrapper">
        <?php include 'header.php' ?>
        <div class="clearfix"></div>

        <div class="dashboard-wrap bg-light">
            <a class="mobNavigation" data-toggle="collapse" href="#MobNav" role="button" aria-expanded="false">
                <i class="fas fa-bars mr-2"></i>Dashboard Navigation
            </a>

            <?php include 'sidenav.php' ?>

            <div class="dashboard-content">
                <!-- Page Header -->
                <div class="dashboard-tlbar">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12">
                            <h1>
                                <i class="lni lni-pencil"></i> Edit Job
                            </h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item text-muted"><a href="index.php">Home</a></li>
                                    <li class="breadcrumb-item text-muted"><a href="index.php">Dashboard</a></li>
                                    <li class="breadcrumb-item text-muted"><a href="dashboard-manage-jobs.php">Manage
                                            Jobs</a></li>
                                    <li class="breadcrumb-item"><a href="#" class="theme-cl">Edit Job</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="dashboard-widg-bar d-block">
                    <?php if (!$isCompanyActive): ?>
                    <!-- ACCESS DENIED MESSAGE -->
                    <div class="access-denied-card">
                        <div class="denied-icon">
                            <i class="lni lni-lock"></i>
                        </div>

                        <h2 class="denied-title">Access Restricted</h2>

                        <p class="denied-message">
                            You cannot edit jobs at this time because your company profile is not active.
                        </p>

                        <!-- Company Info Display -->
                        <div class="company-info-display">
                            <?php if (!empty($COMPANYLOGO)): ?>
                            <img src="<?php echo htmlspecialchars($COMPANYLOGO); ?>"
                                alt="<?php echo htmlspecialchars($COMPANYNAME); ?>" class="company-logo-small"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="company-logo-placeholder-small" style="display: none;">
                                <?php echo strtoupper(substr($COMPANYNAME, 0, 1)); ?>
                            </div>
                            <?php else: ?>
                            <div class="company-logo-placeholder-small">
                                <?php echo strtoupper(substr($COMPANYNAME, 0, 1)); ?>
                            </div>
                            <?php endif; ?>

                            <div class="company-details">
                                <div class="company-name-display"><?php echo htmlspecialchars($COMPANYNAME); ?></div>
                                <div class="company-address-display">
                                    <i class="lni lni-map-marker"></i>
                                    <?php echo htmlspecialchars($COMPANYADDRESS); ?>,
                                    <?php echo htmlspecialchars($COMPANYCITY); ?>,
                                    <?php echo htmlspecialchars($COMPANYCOUNTRY); ?>
                                </div>
                            </div>

                            <div>
                                <span class="status-badge-large <?php 
                                    if ($COMPANYSTATUS == 'Pending') {
                                        echo 'status-pending';
                                    } elseif ($COMPANYSTATUS == 'Suspended') {
                                        echo 'status-suspended';
                                    } else {
                                        echo 'status-inactive';
                                    }
                                ?>">
                                    <i class="lni lni-warning"></i>
                                    <?php echo htmlspecialchars($COMPANYSTATUS); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Status Info Box -->
                        <div class="status-info-box">
                            <div class="status-info-title">
                                <i class="lni lni-information"></i>
                                Why can't I edit jobs?
                            </div>
                            <p class="status-info-text">
                                <?php if ($COMPANYSTATUS == 'Pending'): ?>
                                Your company profile is currently under review by our admin team. You will be able to
                                edit jobs once your company is approved and activated.
                                <?php elseif ($COMPANYSTATUS == 'Suspended'): ?>
                                Your company account has been suspended. This may be due to a violation of our terms of
                                service or other policy issues. Please contact our support team at
                                <strong>support@munext.com</strong> for more information and to resolve this issue.
                                <?php else: ?>
                                Your company profile is currently inactive. Please contact support or ensure your
                                company profile meets all requirements to be activated.
                                <?php endif; ?>
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <a href="dashboard-manage-jobs.php" class="btn-secondary-custom">
                                <i class="lni lni-arrow-left"></i>
                                Back to Jobs
                            </a>
                            <?php if ($COMPANYSTATUS == 'Suspended'): ?>
                            <a href="mailto:support@munext.com?subject=Account Suspension - <?php echo urlencode($COMPANYNAME); ?>"
                                class="btn-primary-custom">
                                <i class="lni lni-envelope"></i>
                                Contact Support
                            </a>
                            <?php else: ?>
                            <a href="company-detail.php?companyid=<?php echo $COMPANYID; ?>" class="btn-primary-custom">
                                <i class="lni lni-eye"></i>
                                View Company Profile
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- JOB EDIT FORM (Only shown if company is active) -->
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12">
                            <div class="form-content-card">
                                <div class="form-section-title">
                                    <i class="lni lni-write"></i>
                                    Edit Job Details
                                </div>

                                <?php if (isset($_SESSION['error_msg'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="lni lni-cross-circle"></i> <?php echo $_SESSION['error_msg']; ?>
                                    <button type="button" class="close" data-dismiss="alert">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <?php unset($_SESSION['error_msg']); endif; ?>

                                <form class="row" method="post">
                                    <div class="col-xl-12 col-lg-12">
                                        <div class="row">

                                            <!-- Job Title Select -->
                                            <div class="col-xl-12 col-lg-12 col-md-12">
                                                <div class="form-group">
                                                    <label class="text-dark ft-medium">Job Title*</label>
                                                    <select class="custom-select rounded" name="job_title_select"
                                                        id="title_select" onchange="set_job()" required>
                                                        <option><?php echo htmlspecialchars($JOBTITLE); ?></option>
                                                        <option>Assistant</option>
                                                        <option>Associate</option>
                                                        <option>Administrative Assistant</option>
                                                        <option>Account Manager</option>
                                                        <option>Assistant Manager</option>
                                                        <option>Commission Sales Associate</option>
                                                        <option>Sales Attendant</option>
                                                        <option>Sales Associate</option>
                                                        <option>Accountant</option>
                                                        <option>Sales Advocate</option>
                                                        <option>Analyst</option>
                                                        <option>Research Assistant</option>
                                                        <option>Sales And Marketing Specialist</option>
                                                        <option>Administrative Aide</option>
                                                        <option>Administrative Clerk</option>
                                                        <option>Account Executive</option>
                                                        <option>Executive Assistant</option>
                                                        <option>Administrator</option>
                                                        <option>Account Relationship Manager</option>
                                                        <option>Web Developer</option>
                                                        <option>Senior Software Developer</option>
                                                        <option>Others (Please specify)</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Custom Job Title -->
                                            <div class="col-xl-12 col-lg-12 col-md-12">
                                                <div class="form-group">
                                                    <input type="text" class="form-control rounded"
                                                        name="job_title_specify" placeholder="Specify Job Title"
                                                        id="title_other">
                                                </div>
                                            </div>

                                            <!-- Job Category -->
                                            <div class="col-xl-6 col-lg-6 col-md-6">
                                                <div class="form-group">
                                                    <label class="text-dark ft-medium">Job Category*</label>
                                                    <select class="custom-select rounded" name="job_categoryid"
                                                        required>
                                                        <option value="<?php echo $JOBCATEGORYID; ?>">
                                                            <?php echo htmlspecialchars($SUBCATEGORY); ?>
                                                        </option>
                                                        <?php
                                                        $categoryQuery = "SELECT * FROM tbljobsubcategory ORDER BY ID DESC";
                                                        $categoryResult = mysqli_query($con, $categoryQuery);
                                                        while ($categoryRow = mysqli_fetch_array($categoryResult)):
                                                        ?>
                                                        <option value="<?php echo $categoryRow['ID']; ?>">
                                                            <?php echo htmlspecialchars($categoryRow['SUBCATEGORY']); ?>
                                                        </option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Workplace Policy -->
                                            <div class="col-xl-6 col-lg-6 col-md-6">
                                                <div class="form-group">
                                                    <label class="text-dark ft-medium">Workplace Policy*</label>
                                                    <select class="custom-select rounded" name="workplace_policy"
                                                        required>
                                                        <option><?php echo htmlspecialchars($WORKPLACE_POLICY); ?>
                                                        </option>
                                                        <option value="On-site">On-site (Employees come to work
                                                            in-person)</option>
                                                        <option value="Hybrid">Hybrid (Employees work on-site and
                                                            off-site)</option>
                                                        <option value="Remote">Remote (Employees work off-site)</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Job Description -->
                                            <div class="col-xl-12 col-lg-12 col-md-12">
                                                <div class="form-group">
                                                    <label class="text-dark ft-medium">Job Description*</label>
                                                    <textarea class="form-control rounded" name="job_desc" rows="6"
                                                        placeholder="Add skills and requirements you're looking for..."
                                                        required><?php echo nl2br(htmlspecialchars($JOBDESCRIPTION)); ?></textarea>
                                                </div>
                                            </div>

                                            <!-- Job Type -->
                                            <div class="col-xl-6 col-lg-6 col-md-6">
                                                <div class="form-group">
                                                    <label class="text-dark ft-medium">Job Type*</label>
                                                    <select class="custom-select rounded" name="job_type" required>
                                                        <option><?php echo htmlspecialchars($JOBTYPE); ?></option>
                                                        <option>Full Time</option>
                                                        <option>Part Time</option>
                                                        <option>Full Time or Part Time</option>
                                                        <option>Internship</option>
                                                        <option>Contract</option>
                                                        <option>Freelancing</option>
                                                        <option>Temporary</option>
                                                        <option>Volunteer</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Career Level -->
                                            <div class="col-xl-6 col-lg-6 col-md-6">
                                                <div class="form-group">
                                                    <label class="text-dark ft-medium">Career Level</label>
                                                    <select class="custom-select rounded" name="career_level">
                                                        <option><?php echo htmlspecialchars($CAREERLEVEL); ?></option>
                                                        <option>Beginner</option>
                                                        <option>Junior</option>
                                                        <option>Manager</option>
                                                        <option>Team leader</option>
                                                        <option>Not Necessary</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Experience -->
                                            <div class="col-xl-6 col-lg-6 col-md-6">
                                                <div class="form-group">
                                                    <label class="text-dark ft-medium">Experience*</label>
                                                    <select class="custom-select rounded" name="experience" required>
                                                        <option><?php echo htmlspecialchars($WORKEXPERIENCE); ?>
                                                        </option>
                                                        <option>0 To 6 Month</option>
                                                        <option>1 Years</option>
                                                        <option>2 Years</option>
                                                        <option>3 Years</option>
                                                        <option>4 Years</option>
                                                        <option>5+ Years</option>
                                                        <option>No Restriction</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Qualification -->
                                            <div class="col-xl-6 col-lg-6 col-md-6">
                                                <div class="form-group">
                                                    <label class="text-dark ft-medium">Qualification*</label>
                                                    <select class="custom-select rounded" name="qualification" required>
                                                        <option><?php echo htmlspecialchars($QUALIFICATION); ?></option>
                                                        <option>B.Sc & Above</option>
                                                        <option>M.Sc & Above</option>
                                                        <option>BPharma & Above</option>
                                                        <option>PH.D & Above</option>
                                                        <option>HND & Above</option>
                                                        <option>OND & Above</option>
                                                        <option>Basic Education</option>
                                                        <option>Other</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Gender -->
                                            <div class="col-xl-6 col-lg-6 col-md-6">
                                                <div class="form-group">
                                                    <label class="text-dark ft-medium">Gender*</label>
                                                    <select class="custom-select rounded" name="gender" required>
                                                        <option><?php echo htmlspecialchars($PREFEREDSEX); ?></option>
                                                        <option>Male</option>
                                                        <option>Female</option>
                                                        <option>Both</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Salary -->
                                            <div class="col-xl-6 col-lg-6 col-md-6">
                                                <div class="form-group">
                                                    <label class="text-dark ft-medium">Salary (Optional)</label>
                                                    <input type="number" class="form-control rounded" name="salary"
                                                        placeholder="Enter salary amount"
                                                        value="<?php echo $SALARY; ?>">
                                                    <small class="text-muted">Leave blank if not necessary to
                                                        specify</small>
                                                </div>
                                            </div>

                                            <!-- Deadline -->
                                            <div class="col-xl-6 col-lg-6 col-md-6">
                                                <div class="form-group">
                                                    <label class="text-dark ft-medium">Application Deadline*</label>
                                                    <input type="date" class="form-control rounded" name="deadline"
                                                        value="<?php echo $DEADLINE; ?>" required>
                                                </div>
                                            </div>

                                            <!-- Company Details Display -->
                                            <div class="col-xl-6 col-lg-6 col-md-6">
                                                <div class="form-group">
                                                    <label class="text-dark ft-medium">Company Details</label>
                                                    <div class="p-3 bg-light rounded">
                                                        <p class="mb-1 ft-medium">
                                                            <?php echo htmlspecialchars($COMPANYNAME); ?></p>
                                                        <p class="text-muted small mb-0">
                                                            <?php echo htmlspecialchars($COMPANYADDRESS); ?>,
                                                            <?php echo htmlspecialchars($COMPANYCITY); ?>,
                                                            <?php echo htmlspecialchars($COMPANYCOUNTRY); ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Submit Button -->
                                            <div class="col-xl-12 col-lg-12 col-md-12">
                                                <div class="form-group mt-3">
                                                    <button type="submit"
                                                        class="btn btn-md ft-medium text-light rounded theme-bg"
                                                        name="edit_job">
                                                        <i class="lni lni-save mr-2"></i>Update Job
                                                    </button>
                                                    <a href="dashboard-manage-jobs.php"
                                                        class="btn btn-md ft-medium text-dark rounded btn-light ml-2">
                                                        <i class="lni lni-close mr-2"></i>Cancel
                                                    </a>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <?php include 'footer.php' ?>
            </div>
        </div>

        <a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>
    </div>

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
    function set_job() {
        var title_select = $('#title_select').val();

        if (title_select == "Others (Please specify)") {
            document.getElementById("title_other").style.display = "block";
            document.getElementById("title_other").required = true;
        } else {
            document.getElementById("title_other").style.display = "none";
            document.getElementById("title_other").required = false;
        }
    }

    $(document).ready(function() {
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
    </script>
</body>

</html>