<!DOCTYPE html>
<html lang="zxx">

<?php 
require 'include/phpcode.php';
unset($result); // Clear any $result from phpcode.php


$applicantId = $session_id;

// Get applicant's profile for job matching
$profileQuery = "SELECT JOBTITLE, EXJOBTITLE, JOBCATEGORYID FROM tblapplicants WHERE USERID = ?";
$stmtProfile = mysqli_prepare($con, $profileQuery);
mysqli_stmt_bind_param($stmtProfile, "i", $applicantId);
mysqli_stmt_execute($stmtProfile);
$profileResult = mysqli_stmt_get_result($stmtProfile);
$profile = mysqli_fetch_assoc($profileResult);

$JOBTITLE = $profile['JOBTITLE'] ?? '';
$EXJOBTITLE = $profile['EXJOBTITLE'] ?? '';
$JOBCATEGORYID = $profile['JOBCATEGORYID'] ?? 0;

$JOBTITLE = isset($JOBTITLE) ? trim($JOBTITLE) : '';
$EXJOBTITLE = isset($EXJOBTITLE) ? trim($EXJOBTITLE) : '';
$SKILLS = isset($SKILLS) ? trim($SKILLS) : '';
$JOBCATEGORYID = isset($JOBCATEGORYID) ? $JOBCATEGORYID : null;

// Pagination settings
$recordsPerPage = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

// Filter settings
$searchTerm = isset($_POST['search']) ? trim($_POST['search']) : (isset($_GET['search']) ? trim($_GET['search']) : '');
$categoryFilter = isset($_POST['category']) ? (int)$_POST['category'] : (isset($_GET['category']) ? (int)$_GET['category'] : 0);
$sortBy = isset($_POST['sort']) ? $_POST['sort'] : (isset($_GET['sort']) ? $_GET['sort'] : 'recent');

// Build query conditions
$whereConditions = [];
$params = [];
$types = "";

// Match jobs based on applicant's profile
if (!empty($JOBTITLE) || !empty($EXJOBTITLE) || !empty($JOBCATEGORYID)) {

    $matchConditions = [];
    
    if (!empty($JOBTITLE)) {
        $matchConditions[] = "j.JOBTITLE LIKE ?";
        $params[] = "%$JOBTITLE%";
        $types .= "s";
    }
    
    if (!empty($EXJOBTITLE)) {
        $matchConditions[] = "j.JOBTITLE LIKE ?";
        $params[] = "%$EXJOBTITLE%";
        $types .= "s";
    }

    if ($SKILLS !== '') {
        $matchConditions[] = 'JOBTITLE LIKE ?' ?? 'JOBDESCRIPTION LIKE ?';
        $params[] = "%{$SKILLS}%";
        $types .= 's';
    }
    
    if (!empty($JOBCATEGORYID)) {
        $matchConditions[] = "j.JOBCATEGORYID = ?";
        $params[] = $JOBCATEGORYID;
        $types .= "i";
    }
    
    if (!empty($matchConditions)) {
        $whereConditions[] = "(" . implode(" OR ", $matchConditions) . ")";
    }
}

// Additional filters
if (!empty($searchTerm)) {
    $whereConditions[] = "(j.JOBTITLE LIKE ? OR c.COMPANYNAME LIKE ? OR jsc.SUBCATEGORY LIKE ?)";
    $searchParam = "%$searchTerm%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= "sss";
}

if (!empty($categoryFilter)) {
    $whereConditions[] = "j.JOBCATEGORYID = ?";
    $params[] = $categoryFilter;
    $types .= "i";
}

$whereClause = implode(" OR ", $whereConditions);

// Check if applicant has already applied
$appliedJobsQuery = "SELECT JOBID FROM tbljobapplication WHERE APPLICANTID = ?";
$stmtApplied = mysqli_prepare($con, $appliedJobsQuery);
mysqli_stmt_bind_param($stmtApplied, "i", $applicantId);
mysqli_stmt_execute($stmtApplied);
$appliedResult = mysqli_stmt_get_result($stmtApplied);
$appliedJobs = [];
while ($row = mysqli_fetch_assoc($appliedResult)) {
    $appliedJobs[] = $row['JOBID'];
}

// Sorting
$orderBy = "ORDER BY j.DATEPOSTED DESC";
switch ($sortBy) {
    case 'oldest':
        $orderBy = "ORDER BY j.DATEPOSTED ASC";
        break;
    case 'salary_high':
        $orderBy = "ORDER BY CAST(SUBSTRING_INDEX(j.SALARY, '-', 1) AS UNSIGNED) DESC";
        break;
    case 'salary_low':
        $orderBy = "ORDER BY CAST(SUBSTRING_INDEX(j.SALARY, '-', 1) AS UNSIGNED) ASC";
        break;
    case 'title':
        $orderBy = "ORDER BY j.JOBTITLE ASC";
        break;
    default:
        $orderBy = "ORDER BY j.DATEPOSTED DESC";
}

// Count total records
$countQuery = "SELECT COUNT(DISTINCT j.JOBID) as total 
               FROM tbljob j
               INNER JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
               LEFT JOIN tbljobsubcategory jsc ON j.JOBCATEGORYID = jsc.ID
               WHERE $whereClause";

$stmtCount = mysqli_prepare($con, $countQuery);
if (!empty($types)) {
    mysqli_stmt_bind_param($stmtCount, $types, ...$params);
}
mysqli_stmt_execute($stmtCount);
$countResult = mysqli_stmt_get_result($stmtCount);
$totalRecords = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

// Fetch job alerts
$jobsQuery = "SELECT 
    j.JOBID, j.JOBTITLE, j.JOBTYPE, j.SALARY, j.DATEPOSTED, j.WORKEXPERIENCE, j.WORKPLACE_POLICY,
    c.COMPANYID, c.COMPANYNAME, c.COMPANYLOGO, c.COMPANYCITY, c.COMPANYCOUNTRY,
    jsc.SUBCATEGORY
FROM tbljob j
INNER JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
LEFT JOIN tbljobsubcategory jsc ON j.JOBCATEGORYID = jsc.ID
WHERE $whereClause
$orderBy
LIMIT ? OFFSET ?";

$params[] = $recordsPerPage;
$params[] = $offset;
$types .= "ii";

$stmt = mysqli_prepare($con, $jobsQuery);
if (!empty($types)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$jobs = mysqli_stmt_get_result($stmt);

// Get job categories for filter
$categoriesQuery = "SELECT DISTINCT jsc.ID, jsc.SUBCATEGORY 
                    FROM tbljobsubcategory jsc
                    INNER JOIN tbljob j ON jsc.ID = j.JOBCATEGORYID
                    WHERE j.JOBSTATUS = 'Active'
                    ORDER BY jsc.SUBCATEGORY ASC";
$categories = mysqli_query($con, $categoriesQuery);

// Time ago function
function timeago($date) {
    $timestamp = strtotime($date);
    $difference = time() - $timestamp;
    
    if ($difference < 86400) {
        return "Today";
    } elseif ($difference < 172800) {
        return "Yesterday";
    } elseif ($difference < 604800) {
        $days = floor($difference / 86400);
        return $days . " days ago";
    } else {
        return date('M d, Y', $timestamp);
    }
}
?>

<head>
    <meta charset="utf-8" />
    <meta name="author" content="Themezhub" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Job Alerts - MUNext</title>
    <link href="assets/css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.lineicons.com/3.0/lineicons.css">
    <link rel="stylesheet" href="assets/css/custom-style.css">
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
                <div class="dashboard-tlbar d-block mb-5">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12">
                            <h1 class="ft-medium">
                                <i class="lni lni-alarm"></i> Job Alerts
                            </h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item text-muted"><a href="dashboard.php">Home</a></li>
                                    <li class="breadcrumb-item text-muted"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="#" class="theme-cl">Job Alerts</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="dashboard-widg-bar d-block">

                    <!-- Alert Info Box -->
                    <div class="alert-info-box">
                        <div class="row align-items-center">
                            <div class="col-lg-8 col-md-7">
                                <h4><i class="lni lni-alarm mr-2"></i>Personalized Job Matches</h4>
                                <p class="mb-0">
                                    We're showing you jobs that match your profile and preferences.
                                    <?php if (!empty($JOBTITLE) || !empty($EXJOBTITLE)): ?>
                                    Looking for:
                                    <strong><?php echo htmlspecialchars($JOBTITLE . ($EXJOBTITLE ? ', ' . $SKILLS : '')); ?></strong>
                                    <?php else: ?>
                                    Update your profile to see better job matches!
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-lg-4 col-md-5 text-right">
                                <div class="text-white">
                                    <h2 class="text-white mb-1"><?php echo $totalRecords; ?></h2>
                                    <p class="mb-0">Matching Jobs</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Section -->
                    <div class="filter-section">
                        <form method="POST" action="" id="filterForm">
                            <div class="row align-items-center">
                                <div class="col-lg-5 col-md-6 mb-3 mb-lg-0">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search by job title, company, or category..."
                                        value="<?php echo htmlspecialchars($searchTerm); ?>">
                                </div>

                                <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                                    <select name="category" class="form-control">
                                        <option value="">All Categories</option>
                                        <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                                        <option value="<?php echo $cat['ID']; ?>"
                                            <?php echo $categoryFilter == $cat['ID'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['SUBCATEGORY']); ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                                    <select name="sort" class="form-control">
                                        <option value="recent" <?php echo $sortBy == 'recent' ? 'selected' : ''; ?>>Most
                                            Recent</option>
                                        <option value="oldest" <?php echo $sortBy == 'oldest' ? 'selected' : ''; ?>>
                                            Oldest First</option>
                                        <option value="salary_high"
                                            <?php echo $sortBy == 'salary_high' ? 'selected' : ''; ?>>Highest Salary
                                        </option>
                                        <option value="salary_low"
                                            <?php echo $sortBy == 'salary_low' ? 'selected' : ''; ?>>Lowest Salary
                                        </option>
                                        <option value="title" <?php echo $sortBy == 'title' ? 'selected' : ''; ?>>Job
                                            Title A-Z</option>
                                    </select>
                                </div>

                                <div class="col-lg-1 col-md-6">
                                    <button type="submit" class="btn btn-default text-white btn-sm theme-bg btn-block">
                                        <i class="lni lni-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Jobs List -->
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12">
                            <?php if (mysqli_num_rows($jobs) > 0): 
                            $jobsCount=0;?>


                            <?php while ($job = mysqli_fetch_assoc($jobs)): 
                                if ($row['JOBSTATUS'] == 'Active'):
                                    $jobsCount ++;
                                    
                                    $jobId = $job['JOBID'];
                                    $isApplied = in_array($jobId, $appliedJobs);
                                    $isNew = (time() - strtotime($job['DATEPOSTED'])) < 259200; // 3 days
                                ?>

                            <div class="job-card">
                                <div class="row align-items-center">
                                    <div class="col-lg-1 col-md-2 col-3 text-center mb-3 mb-lg-0">

                                        <?php if (!empty($job['COMPANYLOGO'])): ?>
                                        <img src="<?php echo $path.htmlspecialchars($job['COMPANYLOGO']); ?>"
                                            alt="Company Logo" class="company-logo"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <?php endif; ?>
                                        <div class="company-logo-placeholder ml-2"
                                            style="<?php echo !empty($job['COMPANYLOGO']) ? 'display:none;' : ''; ?>">
                                            <?php echo strtoupper(substr($job['COMPANYNAME'], 0, 1)); ?>
                                        </div>
                                    </div>


                                    <div class="col-lg-7 col-md-6 col-9 mb-3 mb-lg-0">
                                        <div class="d-flex align-items-start mb-2">
                                            <h5 class="mb-0 ft-medium mr-2">
                                                <a href="<?php echo $path?>job-detail.php?jobid=<?php echo $jobId; ?>"
                                                    class="text-dark">
                                                    <?php echo htmlspecialchars($job['JOBTITLE']); ?>
                                                </a>
                                            </h5>
                                            <?php if ($isNew): ?>
                                            <span class="job-badge badge-new">
                                                <i class="lni lni-star"></i> New
                                            </span>
                                            <?php endif; ?>
                                            <?php if ($isApplied): ?>
                                            <span class="job-badge badge-applied">
                                                <i class="lni lni-checkmark-circle"></i> Applied
                                            </span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="mb-2">
                                            <span class="text-muted">
                                                <i class="lni lni-building mr-1"></i>
                                                <?php echo htmlspecialchars($job['COMPANYNAME']); ?>
                                            </span>
                                        </div>

                                        <div class="d-flex flex-wrap">
                                            <span class="text-muted small mr-3">
                                                <i class="lni lni-map-marker mr-1"></i>
                                                <?php echo htmlspecialchars($job['COMPANYCITY'] . ', ' . $job['COMPANYCOUNTRY']); ?>
                                            </span>

                                            <?php if ($job['JOBTYPE']): ?>
                                            <span class="text-muted small mr-3">
                                                <i class="lni lni-briefcase mr-1"></i>
                                                <?php echo htmlspecialchars($job['JOBTYPE']); ?>
                                            </span>
                                            <?php endif; ?>

                                            <?php if ($job['SUBCATEGORY']): ?>
                                            <span class="text-muted small mr-3">
                                                <i class="lni lni-tag mr-1"></i>
                                                <?php echo htmlspecialchars($job['SUBCATEGORY']); ?>
                                            </span>
                                            <?php endif; ?>

                                            <?php if ($job['SALARY']): ?>
                                            <span class="text-success small mr-3">
                                                <i class="lni lni-dollar mr-1"></i>
                                                <?php echo htmlspecialchars($job['SALARY']); ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-lg-2 col-md-2 col-6 text-center mb-3 mb-lg-0">
                                        <div class="text-muted small mb-1">Posted</div>
                                        <strong><?php echo timeago($job['DATEPOSTED']); ?></strong>
                                    </div>

                                    <div class="col-lg-2 col-md-2 col-6 text-center">
                                        <!-- <a href="<?php echo $path?>job-detail.php?jobid=<?php echo $jobId; ?>"
                                            class="btn btn-outline-secondary btn-sm btn-block">
                                            <i class="lni lni-eye mr-1"></i> View Details
                                        </a> -->
                                        <?php if (!$isApplied): ?>
                                        <a href="<?php echo $path?>job-detail.php?jobid=<?php echo $jobId; ?>#apply"
                                            class="btn btn-outline text-white rounded small theme-bg btn-sm btn-block">
                                            <i class="lni lni-telegram mr-1"></i> Apply Now
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <?php 
                                endif;
                                endwhile; ?>

                            <!-- Pagination -->
                            <?php if ($totalPages > 1): ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <ul class="pagination justify-content-center">
                                        <!-- Previous -->
                                        <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="?page=<?php echo $page - 1; ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?><?php echo !empty($categoryFilter) ? '&category=' . $categoryFilter : ''; ?><?php echo !empty($sortBy) ? '&sort=' . $sortBy : ''; ?>">
                                                <i class="fas fa-arrow-left"></i>
                                            </a>
                                        </li>
                                        <?php endif; ?>

                                        <!-- Page Numbers -->
                                        <?php
                                            $startPage = max(1, $page - 2);
                                            $endPage = min($totalPages, $page + 2);
                                            
                                            if ($startPage > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="?page=1<?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?><?php echo !empty($categoryFilter) ? '&category=' . $categoryFilter : ''; ?><?php echo !empty($sortBy) ? '&sort=' . $sortBy : ''; ?>">1</a>
                                        </li>
                                        <?php if ($startPage > 2): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                        <?php endif; ?>
                                        <?php endif; ?>

                                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                            <a class="page-link"
                                                href="?page=<?php echo $i; ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?><?php echo !empty($categoryFilter) ? '&category=' . $categoryFilter : ''; ?><?php echo !empty($sortBy) ? '&sort=' . $sortBy : ''; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                        <?php endfor; ?>

                                        <?php if ($endPage < $totalPages): ?>
                                        <?php if ($endPage < $totalPages - 1): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                        <?php endif; ?>
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="?page=<?php echo $totalPages; ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?><?php echo !empty($categoryFilter) ? '&category=' . $categoryFilter : ''; ?><?php echo !empty($sortBy) ? '&sort=' . $sortBy : ''; ?>"><?php echo $totalPages; ?></a>
                                        </li>
                                        <?php endif; ?>

                                        <!-- Next -->
                                        <?php if ($page < $totalPages): ?>
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="?page=<?php echo $page + 1; ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?><?php echo !empty($categoryFilter) ? '&category=' . $categoryFilter : ''; ?><?php echo !empty($sortBy) ? '&sort=' . $sortBy : ''; ?>">
                                                <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                            <?php endif; ?>


                            <?php if ($jobsCount < 1):?>
                            <!-- Empty State -->
                            <div class="empty-state">
                                <i class="lni lni-alarm"></i>
                                <h4 class="mt-3">No Job Alerts Found</h4>
                                <p class="text-muted">
                                    <?php if (!empty($searchTerm) || !empty($categoryFilter)): ?>
                                    No jobs match your search criteria. Try adjusting your filters.
                                    <?php elseif (empty($JOBTITLE) && empty($EXJOBTITLE)): ?>
                                    Complete your profile to receive personalized job alerts!
                                    <?php else: ?>
                                    We couldn't find any jobs matching your profile. Check back later for new
                                    opportunities.
                                    <?php endif; ?>
                                </p>
                                <?php if (empty($JOBTITLE) && empty($EXJOBTITLE)): ?>
                                <a href="dashboard-my-profile.php" class="btn btn-primary mt-3">
                                    <i class="lni lni-user"></i> Complete Profile
                                </a>
                                <?php else: ?>
                                <a href="<?php echo $path?>browse-jobs.php" class="btn btn-outline-secondary mt-2 pt-4">
                                    <i class="lni lni-search"></i> Browse All Jobs
                                </a>
                                <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

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
</body>

</html>