<!DOCTYPE html>
<html lang="en">

<?php
require('./mysqli_connect.php');
include 'session_check.php';
include "include/helper.php";

// Pagination settings
$results_per_page = 12;
$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($current_page - 1) * $results_per_page;

// Initialize variables
$search = isset($_GET['search']) ? mysqli_real_escape_string($con, trim($_GET['search'])) : '';
$search_by = isset($_GET['search_by']) ? mysqli_real_escape_string($con, trim($_GET['search_by'])) : '';
$category = isset($_GET['category']) ? mysqli_real_escape_string($con, trim($_GET['category'])) : '';
$jtype = isset($_GET['jtype']) ? mysqli_real_escape_string($con, trim($_GET['jtype'])) : '';
$location = isset($_GET['location']) ? mysqli_real_escape_string($con, trim($_GET['location'])) : '';
$min_salary = isset($_GET['min_salary']) && $_GET['min_salary'] !== '' ? (int)$_GET['min_salary'] : 0;
$max_salary = isset($_GET['max_salary']) && $_GET['max_salary'] !== '' ? (int)$_GET['max_salary'] : 0;
$sort = isset($_GET['sort']) && in_array($_GET['sort'], ['ASC', 'DESC']) ? $_GET['sort'] : 'DESC';
$jobcategoryid = isset($_GET['jobcategoryid']) ? (int)$_GET['jobcategoryid'] : 0;

// Checkboxes state
$all = empty($jtype) ? 'checked' : '';
$ft = ($jtype == 'Full time') ? 'checked' : '';
$pt = ($jtype == 'Part Time') ? 'checked' : '';
$cb = ($jtype == 'Contract') ? 'checked' : '';
$in = ($jtype == 'Internship') ? 'checked' : '';
$tp = ($jtype == 'Temporary') ? 'checked' : '';

// Build WHERE conditions
$where_conditions = [];
$where_conditions[] = "j.JOBSTATUS = 'Active'"; // Only show active jobs

// Category from URL
if (!empty($jobcategoryid)) {
    $where_conditions[] = "j.JOBCATEGORYID = $jobcategoryid";
}

// Search conditions
if (!empty($search)) {
    if ($search_by == 'name') {
        $where_conditions[] = "j.JOBTITLE LIKE '%$search%'";
    } elseif ($search_by == 'company') {
        $where_conditions[] = "c.COMPANYNAME LIKE '%$search%'";
    } elseif ($search_by == 'location') {
        $where_conditions[] = "(c.COMPANYCITY LIKE '%$search%' OR c.COMPANYCOUNTRY LIKE '%$search%')";
    } else {
        $where_conditions[] = "(j.JOBTITLE LIKE '%$search%' OR c.COMPANYNAME LIKE '%$search%' OR c.COMPANYCITY LIKE '%$search%')";
    }
}

// Category filter
if (!empty($category)) {
    $where_conditions[] = "j.JOBTITLE LIKE '%$category%'";
}

// Job type filter
if (!empty($jtype)) {
    $where_conditions[] = "j.JOBTYPE = '$jtype'";
}

// Location filter
if (!empty($location)) {
    $where_conditions[] = "(c.COMPANYCITY LIKE '%$location%' OR c.COMPANYCOUNTRY LIKE '%$location%')";
}

// Salary filter
if ($min_salary > 0) {
    $where_conditions[] = "j.SALARY >= $min_salary";
}
if ($max_salary > 0) {
    $where_conditions[] = "j.SALARY <= $max_salary";
}

// Build WHERE clause
$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Count total results
$count_query = "SELECT COUNT(*) as total 
                FROM tbljob j 
                LEFT JOIN tblcompany c ON j.COMPANYID = c.COMPANYID 
                $where_clause";
$count_result = mysqli_query($con, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_results = $count_row['total'];
$total_pages = ceil($total_results / $results_per_page);

// Main query with pagination
$query = "SELECT j.*, c.COMPANYNAME, c.COMPANYLOGO, c.COMPANYADDRESS, 
          c.COMPANYCOUNTRY, c.COMPANYCITY, c.COMPANYSPECIALISM, c.COMPANYINDUSTRY
          FROM tbljob j
          LEFT JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
          $where_clause
          ORDER BY j.JOBID $sort
          LIMIT $results_per_page OFFSET $offset";

// Time ago function
function timeago($date) {
    if (empty($date)) return 'Just posted';
    $timestamp = strtotime($date);
    $strTime = array("second", "minute", "hour", "day", "month", "year");
    $length = array("60", "60", "24", "30", "12", "10");
    
    $currentTime = time();
    if ($currentTime >= $timestamp) {
        $diff = time() - $timestamp;
        for ($i = 0; $diff >= $length[$i] && $i < count($length) - 1; $i++) {
            $diff = $diff / $length[$i];
        }
        $diff = round($diff);
        return $diff . " " . $strTime[$i] . ($diff > 1 ? "s" : "") . " ago";
    }
    return 'Just now';
}

// Build URL parameters
function buildUrl($params) {
    $url_params = [];
    foreach ($params as $key => $value) {
        if ($value !== '' && $value !== null && $value !== 0) {
            $url_params[] = urlencode($key) . '=' . urlencode($value);
        }
    }
    return !empty($url_params) ? '?' . implode('&', $url_params) : '';
}

$base_params = [
    'search' => $search,
    'search_by' => $search_by,
    'category' => $category,
    'jtype' => $jtype,
    'location' => $location,
    'min_salary' => $min_salary,
    'max_salary' => $max_salary,
    'sort' => $sort,
    'jobcategoryid' => $jobcategoryid
];
?>

<?php include 'include/head.php' ?>

<body>
    <div id="main-wrapper">
        <?php include 'include/header.php' ?>
        <div class="clearfix"></div>

        <!-- Main Section -->
        <section class="main-section">
            <div class="container">
                <div class="row">
                    <!-- Sidebar Filters -->
                    <div class="col-lg-3 col-md-12">
                        <div class="sidebar-filter">
                            <form method="GET" action="job-list-v1.php" id="filterForm">
                                <input type="hidden" name="jobcategoryid" value="<?php echo $jobcategoryid; ?>">

                                <!-- Search Filter -->
                                <div class="filter-section">
                                    <h5 class="filter-title">
                                        <i class="lni lni-search-alt"></i>Search Jobs
                                    </h5>
                                    <div class="form-group">
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Job title, keyword..."
                                            value="<?php echo htmlspecialchars($search); ?>">
                                    </div>
                                    <div class="form-group mb-0">
                                        <select class="custom-select" name="search_by">
                                            <option value="">All Fields</option>
                                            <option value="name" <?php echo $search_by == 'name' ? 'selected' : ''; ?>>
                                                Job Title</option>
                                            <option value="company"
                                                <?php echo $search_by == 'company' ? 'selected' : ''; ?>>Company
                                            </option>
                                            <option value="location"
                                                <?php echo $search_by == 'location' ? 'selected' : ''; ?>>Location
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Job Type Filter -->
                                <div class="filter-section">
                                    <h5 class="filter-title">
                                        <i class="lni lni-briefcase"></i>Job Type
                                    </h5>
                                    <div class="form-group mb-0">
                                        <div class="custom-control custom-radio mb-2">
                                            <input type="radio" id="jtype_all" name="jtype" value=""
                                                class="custom-control-input" <?php echo $all; ?>>
                                            <label class="custom-control-label" for="jtype_all">All Types</label>
                                        </div>
                                        <div class="custom-control custom-radio mb-2">
                                            <input type="radio" id="jtype_ft" name="jtype" value="Full time"
                                                class="custom-control-input" <?php echo $ft; ?>>
                                            <label class="custom-control-label" for="jtype_ft">Full Time</label>
                                        </div>
                                        <div class="custom-control custom-radio mb-2">
                                            <input type="radio" id="jtype_pt" name="jtype" value="Part Time"
                                                class="custom-control-input" <?php echo $pt; ?>>
                                            <label class="custom-control-label" for="jtype_pt">Part Time</label>
                                        </div>
                                        <div class="custom-control custom-radio mb-2">
                                            <input type="radio" id="jtype_cb" name="jtype" value="Contract"
                                                class="custom-control-input" <?php echo $cb; ?>>
                                            <label class="custom-control-label" for="jtype_cb">Contract</label>
                                        </div>
                                        <div class="custom-control custom-radio mb-2">
                                            <input type="radio" id="jtype_in" name="jtype" value="Internship"
                                                class="custom-control-input" <?php echo $in; ?>>
                                            <label class="custom-control-label" for="jtype_in">Internship</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="jtype_tp" name="jtype" value="Temporary"
                                                class="custom-control-input" <?php echo $tp; ?>>
                                            <label class="custom-control-label" for="jtype_tp">Temporary</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Location Filter -->
                                <div class="filter-section">
                                    <h5 class="filter-title">
                                        <i class="lni lni-map-marker"></i>Location
                                    </h5>
                                    <div class="form-group mb-0">
                                        <input type="text" name="location" class="form-control"
                                            placeholder="City or Country"
                                            value="<?php echo htmlspecialchars($location); ?>">
                                    </div>
                                </div>

                                <!-- Salary Range Filter -->
                                <div class="filter-section">
                                    <h5 class="filter-title">
                                        <i class="lni lni-money-protection"></i>Salary Range
                                    </h5>
                                    <div class="salary-inputs">
                                        <input type="number" name="min_salary" class="form-control" placeholder="Min $"
                                            value="<?php echo $min_salary > 0 ? $min_salary : ''; ?>">
                                        <span>-</span>
                                        <input type="number" name="max_salary" class="form-control" placeholder="Max $"
                                            value="<?php echo $max_salary > 0 ? $max_salary : ''; ?>">
                                    </div>
                                </div>

                                <!-- Category Filter -->
                                <div class="filter-section">
                                    <h5 class="filter-title">
                                        <i class="lni lni-layers"></i>Categories
                                    </h5>
                                    <div class="form-group mb-0">
                                        <select class="custom-select" name="category">
                                            <option value="">All Categories</option>
                                            <?php
                                            $query_cat = "SELECT jsc.ID, jsc.SUBCATEGORY, COUNT(DISTINCT j.JOBID) as job_count 
                                                          FROM tbljobsubcategory jsc
                                                          INNER JOIN tbljob j ON j.JOBTITLE LIKE CONCAT('%', jsc.SUBCATEGORY, '%')
                                                          GROUP BY jsc.ID, jsc.SUBCATEGORY
                                                          HAVING job_count > 0
                                                          ORDER BY jsc.SUBCATEGORY ASC 
                                                          LIMIT 20";
                                            $run_cat = mysqli_query($con, $query_cat);
                                            if ($run_cat) {
                                                while ($row_cat = mysqli_fetch_array($run_cat)) {
                                                    $selected = ($category == $row_cat['SUBCATEGORY']) ? 'selected' : '';
                                            ?>
                                            <option value="<?php echo htmlspecialchars($row_cat['SUBCATEGORY']); ?>"
                                                <?php echo $selected; ?>>
                                                <?php echo htmlspecialchars($row_cat['SUBCATEGORY']); ?>
                                                (<?php echo $row_cat['job_count']; ?>)
                                            </option>
                                            <?php 
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="filter-section">
                                    <button type="submit" class="btn btn-primary-custom btn-block mb-2">
                                        <i class="lni lni-search-alt mr-2"></i>Apply Filters
                                    </button>
                                    <a href="job-list-v1.php<?php echo !empty($jobcategoryid) ? '?jobcategoryid=' . $jobcategoryid : ''; ?>"
                                        class="btn btn-outline-secondary btn-block">
                                        <i class="lni lni-reload mr-2"></i>Clear Filters
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Job Listings -->
                    <div class="col-lg-9 col-md-12">
                        <!-- Results Header -->
                        <div class="results-header">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div class="mb-2 mb-md-0">
                                    <h6 class="mb-0">
                                        <span class="result-count"><?php echo number_format($total_results); ?></span>
                                        Job<?php echo $total_results != 1 ? 's' : ''; ?> Found
                                        <?php if (!empty($search)): ?>
                                        <span class="text-muted"> for </span>
                                        "<span class="search-highlight"><?php echo htmlspecialchars($search); ?></span>"
                                        <?php endif; ?>
                                    </h6>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <form method="GET" action="" class="d-inline">
                                        <?php foreach ($base_params as $key => $value): ?>
                                        <?php if ($key != 'sort' && $value !== '' && $value !== null): ?>
                                        <input type="hidden" name="<?php echo $key; ?>"
                                            value="<?php echo htmlspecialchars($value); ?>">
                                        <?php endif; ?>
                                        <?php endforeach; ?>
                                        <select class="custom-select" name="sort" onchange="this.form.submit()"
                                            style="width: auto;">
                                            <option value="DESC" <?php echo $sort == 'DESC' ? 'selected' : ''; ?>>Newest
                                                First</option>
                                            <option value="ASC" <?php echo $sort == 'ASC' ? 'selected' : ''; ?>>Oldest
                                                First</option>
                                        </select>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Job Cards Grid -->
                        <div class="row">
                            <?php
                            $run = mysqli_query($con, $query);
                            
                            if ($run && mysqli_num_rows($run) > 0) {
                                while ($row = mysqli_fetch_array($run)) {
                                    $COMPANYID = $row['COMPANYID'] ?? 0;
                                    $JOBID = $row['JOBID'];
                                    $DATEPOSTED = $row['DATEPOSTED'] ?? '';
                                    $SALARY = isset($row['SALARY']) ? $row['SALARY'] : 0;
                                    $JOBTYPE = $row['JOBTYPE'] ?? 'Full time';
                                    $JOBTITLE = $row['JOBTITLE'];
                                    $WORKEXPERIENCE = $row['WORKEXPERIENCE'] ?? 'No Restriction';
                                    $DEADLINE = $row['DEADLINE'] ?? '';
                                    $COMPANYNAME = $row['COMPANYNAME'] ?? 'Company';
                                    $COMPANYLOGO = $row['COMPANYLOGO'] ?? '';
                                    $COMPANYCITY = $row['COMPANYCITY'] ?? 'Location';
                                    $COMPANYCOUNTRY = $row['COMPANYCOUNTRY'] ?? '';
                                    
                                    // Badge class based on job type
                                    $badgeClass = 'badge-fulltime';
                                    if ($JOBTYPE == 'Part Time') $badgeClass = 'badge-parttime';
                                    elseif ($JOBTYPE == 'Contract') $badgeClass = 'badge-contract';
                                    elseif ($JOBTYPE == 'Internship') $badgeClass = 'badge-internship';
                                    elseif ($JOBTYPE == 'Temporary') $badgeClass = 'badge-temporary';

                                    
                                    $isActive = ($row['JOBSTATUS'] == 'Active');
                                    $isFilled = ($row['JOBSTATUS'] == 'Filled');

                                    // Check if deadline passed
									$isExpired = (strtotime($DEADLINE) < time());
                                    // if ($isExpired && !$isFilled):
                            ?>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <div class="job-card">
                                    <div class="job-card-header">
                                        <div class="company-logo">
                                            <img src="<?php echo !empty($COMPANYLOGO) ? './' . $COMPANYLOGO : 'assets/img/company-default.png'; ?>"
                                                alt="<?php echo htmlspecialchars($COMPANYNAME); ?>">
                                        </div>
                                        <div class="job-card-info">
                                            <h5 class="job-title">
                                                <a href="job-detail.php?jobid=<?php echo $JOBID; ?>">
                                                    <?php echo htmlspecialchars($JOBTITLE); ?>
                                                </a>
                                            </h5>
                                            <div class="company-name">
                                                <a href="employer-detail.php?companyid=<?php echo $COMPANYID; ?>">
                                                    <?php echo htmlspecialchars($COMPANYNAME); ?>
                                                </a>
                                            </div>
                                            <?php if ($WORKEXPERIENCE != "No Restriction"): ?>
                                            <small class="text-muted"><?php echo htmlspecialchars($WORKEXPERIENCE); ?>
                                                Experience</small>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="job-meta">
                                        <div class="job-meta-item">
                                            <i class="lni lni-map-marker"></i>
                                            <span><?php echo htmlspecialchars($COMPANYCITY . ', ' . $COMPANYCOUNTRY); ?></span>
                                        </div>
                                        <?php if ($SALARY > 0): ?>
                                        <div class="job-meta-item">
                                            <i class="lni lni-wallet"></i>
                                            <span>$<?php echo number_format($SALARY, 0); ?>/hr</span>
                                        </div>
                                        <?php endif; ?>
                                        <div class="job-meta-item">
                                            <i class="lni lni-timer"></i>
                                            <span><?php echo timeago($DATEPOSTED); ?></span>
                                        </div>
                                    </div>

                                    <!-- <div class="job-meta">
                                        <div class="job-meta-item">
                                            <span class="job-type-badge <?php echo $badgeClass; ?> ">
                                                <?php echo htmlspecialchars($JOBTYPE); ?>
                                            </span>
                                        </div>
                                    </div> -->


                                    <div class="job-meta">
                                        <div class="job-meta-item">

                                            <span class="job-type-badge <?php echo $badgeClass; ?> ">
                                                <?php echo htmlspecialchars($JOBTYPE); ?>
                                            </span>


                                            <?php if (!empty($DEADLINE) && $DEADLINE != '0000-00-00'): ?>
                                            <?php if ($isFilled): ?>
                                            <span class="job-type-badge theme-cl bg-light-danger mr-2">Position
                                                Filled</span>
                                            <?php elseif ($isActive): ?>
                                            <span class="job-type-badge text-success theme-bg-light mr-2">Hiring
                                            </span>
                                            <?php endif ?>



                                            <?php if ($isExpired && !$isFilled): ?>
                                            <span class="job-type-badge text-danger bg-light-danger">
                                                <i class="lni lni-alarm"></i> Expired
                                            </span>
                                            <?php endif ?>

                                            <?php endif; ?>


                                        </div>
                                    </div>




                                    <div class="job-footer">
                                        <span class="mb-2">
                                            <i class="lni lni-calendar"></i>
                                            <span>Deadline:
                                                <?php echo date('M d, Y', strtotime($DEADLINE)); ?></span></span>


                                        <a href="job-detail.php?jobid=<?php echo $JOBID; ?>" class="apply-btn">
                                            Apply Now
                                            <i class="lni lni-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php
                                }
                            } else {
                            ?>
                            <div class="col-12">
                                <div class="no-results">
                                    <i class="lni lni-search-alt"></i>
                                    <h4>No Jobs Found</h4>
                                    <p class="text-muted">
                                        <?php if (!empty($search) || !empty($jtype) || !empty($category) || !empty($location)): ?>
                                        No jobs match your search criteria. Try adjusting your filters.
                                        <?php else: ?>
                                        There are currently no job postings available. Please check back later.
                                        <?php endif; ?>
                                    </p>
                                    <a href="job-list-v1.php<?php echo !empty($jobcategoryid) ? '?jobcategoryid=' . $jobcategoryid : ''; ?>"
                                        class="btn btn-outline-secondary rounded pt-4 mt-3">
                                        <i class="lni lni-reload mr-2"></i>View All Jobs
                                    </a>
                                </div>
                            </div>
                            <?php } ?>
                        </div>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                        <div class="row">
                            <div class="col-12">
                                <ul class="pagination">
                                    <!-- Previous -->
                                    <li class="page-item <?php echo $current_page <= 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link"
                                            href="<?php echo $current_page > 1 ? 'job-list-v1.php' . buildUrl(array_merge($base_params, ['page' => $current_page - 1])) : '#'; ?>">
                                            <i class="lni lni-chevron-left"></i>
                                        </a>
                                    </li>

                                    <?php
                                    $start_page = max(1, $current_page - 2);
                                    $end_page = min($total_pages, $current_page + 2);

                                    if ($start_page > 1) {
                                        echo '<li class="page-item"><a class="page-link" href="job-list-v1.php' . buildUrl(array_merge($base_params, ['page' => 1])) . '">1</a></li>';
                                        if ($start_page > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }

                                    for ($i = $start_page; $i <= $end_page; $i++) {
                                        $active = ($i == $current_page) ? 'active' : '';
                                        echo '<li class="page-item ' . $active . '">';
                                        echo '<a class="page-link" href="job-list-v1.php' . buildUrl(array_merge($base_params, ['page' => $i])) . '">' . $i . '</a>';
                                        echo '</li>';
                                    }

                                    if ($end_page < $total_pages) {
                                        if ($end_page < $total_pages - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                        echo '<li class="page-item"><a class="page-link" href="job-list-v1.php' . buildUrl(array_merge($base_params, ['page' => $total_pages])) . '">' . $total_pages . '</a></li>';
                                    }
                                    ?>

                                    <!-- Next -->
                                    <li
                                        class="page-item <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">
                                        <a class="page-link"
                                            href="<?php echo $current_page < $total_pages ? 'job-list-v1.php' . buildUrl(array_merge($base_params, ['page' => $current_page + 1])) : '#'; ?>">
                                            <i class="lni lni-chevron-right"></i>
                                        </a>
                                    </li>
                                </ul>

                                <div class="text-center mt-3 text-muted">
                                    Showing <?php echo ($offset + 1); ?> to
                                    <?php echo min($offset + $results_per_page, $total_results); ?>
                                    of <?php echo number_format($total_results); ?> jobs
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <?php include 'include/footer.php' ?>
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
</body>

</html>