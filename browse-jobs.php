<!DOCTYPE html>
<html lang="en">

<?php
require('./mysqli_connect.php');
include 'session_check.php';
include "include/helper.php";

// Pagination settings
$results_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $results_per_page;

// Initialize variables
$search = '';
$search_by = '';
$category = '';
$jtype = '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'DESC';

// Checkboxes state
$all = 'checked';
$ft = $pt = $cb = $in = $tp = '';

// Handle search from index page or filter form
if (isset($_POST['search_btn']) || isset($_GET['search'])) {
    // Get search parameters
    if (isset($_POST['search'])) {
        $search = mysqli_real_escape_string($con, trim($_POST['search']));
    } elseif (isset($_GET['search'])) {
        $search = mysqli_real_escape_string($con, trim($_GET['search']));
    }
    
    if (isset($_POST['search_by'])) {
        $search_by = mysqli_real_escape_string($con, trim($_POST['search_by']));
    } elseif (isset($_GET['search_by'])) {
        $search_by = mysqli_real_escape_string($con, trim($_GET['search_by']));
    }
    
    if (isset($_POST['category'])) {
        $category = mysqli_real_escape_string($con, trim($_POST['category']));
    } elseif (isset($_GET['category'])) {
        $category = mysqli_real_escape_string($con, trim($_GET['category']));
    }
    
    if (isset($_POST['jtype'])) {
        $jtype = mysqli_real_escape_string($con, trim($_POST['jtype']));
    } elseif (isset($_GET['jtype'])) {
        $jtype = mysqli_real_escape_string($con, trim($_GET['jtype']));
    }
    
    // Set checkbox states
    if ($jtype == 'Full time') $ft = 'checked';
    elseif ($jtype == 'Part Time') $pt = 'checked';
    elseif ($jtype == 'Contract') $cb = 'checked';
    elseif ($jtype == 'Internship') $in = 'checked';
    elseif ($jtype == 'Temporary') $tp = 'checked';
    else $all = 'checked';
}

// Build the query
$where_conditions = [];
$where_conditions[] = "j.JOBSTATUS = 'Active'"; // Only show active jobs

// Search conditions
if (!empty($search)) {
    if ($search_by == 'name') {
        $where_conditions[] = "(j.JOBTITLE LIKE '%$search%' OR j.JOBDESCRIPTION LIKE '%$search%')";
    } elseif ($search_by == 'company') {
        $where_conditions[] = "c.COMPANYNAME LIKE '%$search%'";
    } elseif ($search_by == 'location') {
        $where_conditions[] = "(c.COMPANYCITY LIKE '%$search%' OR c.COMPANYCOUNTRY LIKE '%$search%' OR c.COMPANYADDRESS LIKE '%$search%')";
    } else {
        // Search all fields if no specific search_by
        $where_conditions[] = "(j.JOBTITLE LIKE '%$search%' OR j.JOBDESCRIPTION LIKE '%$search%' OR c.COMPANYNAME LIKE '%$search%' OR c.COMPANYCITY LIKE '%$search%' OR c.COMPANYCOUNTRY LIKE '%$search%')";
    }
}

// Category filter
if (!empty($category)) {
    $where_conditions[] = "(j.JOBTITLE LIKE '%$category%' OR jsc.SUBCATEGORY LIKE '%$category%')";
}

// Job type filter
if (!empty($jtype)) {
    $where_conditions[] = "j.JOBTYPE = '$jtype'";
}

// Build WHERE clause
$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Count total results
$count_query = "SELECT COUNT(*) as total 
                FROM tbljob j 
                LEFT JOIN tblcompany c ON j.COMPANYID = c.COMPANYID 
                LEFT JOIN tbljobsubcategory jsc ON j.JOBCATEGORYID = jsc.ID 
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
          LEFT JOIN tbljobsubcategory jsc ON j.JOBCATEGORYID = jsc.ID 
          $where_clause
          ORDER BY j.JOBID $sort
          LIMIT $results_per_page OFFSET $offset";

// Time ago function
function timeago($date) {
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
}

// Build URL parameters for pagination
function buildUrl($params) {
    $url_params = [];
    foreach ($params as $key => $value) {
        if (!empty($value)) {
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
    'sort' => $sort
];
?>

<?php include 'include/head.php' ?>


<body>
    <?php include 'include/header.php' ?>
    <div class="clearfix"></div>

    <!-- Breadcrumb -->
    <div class="bg-title py-5">
        <div class="ht-30"></div>
        <div class="container">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12">
                    <h1 class="ft-medium text-white">
                        <?php echo !empty($search) ? 'Search Results' : 'Explore All Jobs'; ?>
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent">
                            <li class="breadcrumb-item"><a href="index.php" class="text-light">Home</a></li>
                            <li class="breadcrumb-item"><a href="#" class="text-light">Jobs</a></li>
                            <li class="breadcrumb-item active text-warning">Browse Jobs</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="ht-30"></div>
    </div>

    <!-- Main Section -->
    <section class="bg-light py-5">
        <div class="container">
            <div class="row">
                <!-- Sidebar Filter -->
                <div class="col-lg-4 col-md-12 col-sm-12 mb-4">
                    <form method="GET" action="browse-jobs.php" id="filterForm">
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                        <input type="hidden" name="search_by" value="<?php echo htmlspecialchars($search_by); ?>">

                        <div class="bg-white rounded shadow-sm">
                            <div
                                class="sidebar_header d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                                <h4 class="ft-medium fs-lg mb-0">Search Filter</h4>
                                <div class="ssh-header">
                                    <a href="browse-jobs.php" class="clear_all ft-medium text-muted">Clear All</a>
                                    <a href="#search_open" data-toggle="collapse" aria-expanded="true" role="button"
                                        class="_filter-ico ml-2">
                                        <i class="lni lni-text-align-right"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="sidebar-widgets collapse show" id="search_open">
                                <div class="search-inner">
                                    <!-- Search Box -->
                                    <div class="filter-search-box px-4 pt-3 pb-0">
                                        <div class="form-group">
                                            <input type="text" name="search" class="form-control"
                                                placeholder="Search keywords..."
                                                value="<?php echo htmlspecialchars($search); ?>">
                                        </div>
                                        <div class="form-group">
                                            <select class="custom-select" name="search_by">
                                                <option value="">All Fields</option>
                                                <option value="name"
                                                    <?php echo $search_by == 'name' ? 'selected' : ''; ?>>Job Title
                                                </option>
                                                <option value="company"
                                                    <?php echo $search_by == 'company' ? 'selected' : ''; ?>>Company
                                                    Name</option>
                                                <option value="location"
                                                    <?php echo $search_by == 'location' ? 'selected' : ''; ?>>Location
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="filter_wraps">
                                        <!-- Job Categories -->
                                        <div class="single_search_boxed px-4 pt-0 border-bottom">
                                            <div class="widget-boxed-header py-3">
                                                <h4>
                                                    <a href="#categories" class="ft-medium fs-md" data-toggle="collapse"
                                                        aria-expanded="true">Job Categories</a>
                                                </h4>
                                            </div>
                                            <div class="widget-boxed-body collapse show" id="categories">
                                                <div class="side-list no-border">
                                                    <div class="single_filter_card">
                                                        <div class="card-body p-0">
                                                            <div class="inner_widget_link">
                                                                <ul class="no-ul-list filter-list">
                                                                    <?php
                                                                $query_cat = "SELECT jsc.ID, jsc.SUBCATEGORY, COUNT(DISTINCT j.JOBID) as job_count 
                                                                            FROM tbljobsubcategory jsc
                                                                            INNER JOIN tbljob j ON (
                                                                                j.JOBTITLE LIKE CONCAT('%', jsc.SUBCATEGORY, '%')
                                                                                OR j.JOBCATEGORYID LIKE CONCAT('%', jsc.ID, '%')
                                                                            )
                                                                            WHERE j.JOBSTATUS = 'Active'
                                                                            GROUP BY jsc.ID, jsc.SUBCATEGORY
                                                                            HAVING job_count > 0
                                                                            ORDER BY job_count DESC, jsc.SUBCATEGORY ASC 
                                                                            LIMIT 15";
                                                                $run_cat = mysqli_query($con, $query_cat);
                                                                
                                                                if ($run_cat && mysqli_num_rows($run_cat) > 0) {
                                                                    while ($row_cat = mysqli_fetch_array($run_cat)) {
                                                                        $checked = ($category == $row_cat['SUBCATEGORY']) ? 'checked' : '';
                                                                    ?>
                                                                    <li>
                                                                        <input id="cat_<?php echo $row_cat['ID']; ?>"
                                                                            class="checkbox" name="category"
                                                                            type="radio"
                                                                            value="<?php echo htmlspecialchars($row_cat['SUBCATEGORY']); ?>"
                                                                            <?php echo $checked; ?>
                                                                            onchange="this.form.submit()">
                                                                        <label for="cat_<?php echo $row_cat['ID']; ?>"
                                                                            class="checkbox-custom-label">
                                                                            <?php echo htmlspecialchars($row_cat['SUBCATEGORY']); ?>
                                                                            <span
                                                                                class="text-muted">(<?php echo $row_cat['job_count']; ?>)</span>
                                                                        </label>
                                                                    </li>
                                                                    <?php 
                                                                    }
                                                                } else { ?>
                                                                    <li class="text-center text-muted py-3">
                                                                        <i class="lni lni-inbox mr-2"></i>
                                                                        <small>No job categories with active
                                                                            listings</small>
                                                                    </li>
                                                                    <?php } ?>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Job Types -->
                                        <div class="single_search_boxed px-4 pt-0">
                                            <div class="widget-boxed-header py-3">
                                                <h4>
                                                    <a href="#jbtypes" data-toggle="collapse" aria-expanded="true"
                                                        class="ft-medium fs-md">Job Type</a>
                                                </h4>
                                            </div>
                                            <div class="widget-boxed-body collapse show" id="jbtypes">
                                                <div class="side-list no-border">
                                                    <div class="single_filter_card">
                                                        <div class="card-body p-0">
                                                            <div class="inner_widget_link">
                                                                <ul class="no-ul-list filter-list">
                                                                    <li>
                                                                        <input id="e1" class="radio-custom" name="jtype"
                                                                            type="radio" value="" <?php echo $all; ?>
                                                                            onchange="this.form.submit()">
                                                                        <label for="e1" class="radio-custom-label">All
                                                                            Types</label>
                                                                    </li>
                                                                    <li>
                                                                        <input id="e2" class="radio-custom" name="jtype"
                                                                            type="radio" value="Full time"
                                                                            <?php echo $ft; ?>
                                                                            onchange="this.form.submit()">
                                                                        <label for="e2" class="radio-custom-label">Full
                                                                            Time</label>
                                                                    </li>
                                                                    <li>
                                                                        <input id="e3" class="radio-custom" name="jtype"
                                                                            type="radio" value="Part Time"
                                                                            <?php echo $pt; ?>
                                                                            onchange="this.form.submit()">
                                                                        <label for="e3" class="radio-custom-label">Part
                                                                            Time</label>
                                                                    </li>
                                                                    <li>
                                                                        <input id="e4" class="radio-custom" name="jtype"
                                                                            type="radio" value="Contract"
                                                                            <?php echo $cb; ?>
                                                                            onchange="this.form.submit()">
                                                                        <label for="e4"
                                                                            class="radio-custom-label">Contract</label>
                                                                    </li>
                                                                    <li>
                                                                        <input id="e5" class="radio-custom" name="jtype"
                                                                            type="radio" value="Internship"
                                                                            <?php echo $in; ?>
                                                                            onchange="this.form.submit()">
                                                                        <label for="e5"
                                                                            class="radio-custom-label">Internship</label>
                                                                    </li>
                                                                    <li>
                                                                        <input id="e6" class="radio-custom" name="jtype"
                                                                            type="radio" value="Temporary"
                                                                            <?php echo $tp; ?>
                                                                            onchange="this.form.submit()">
                                                                        <label for="e6"
                                                                            class="radio-custom-label">Temporary</label>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group filter_button pt-2 pb-4 px-4">
                                        <button type="submit" class="btn btn-md theme-bg text-light rounded full-width">
                                            <i class="lni lni-search-alt mr-2"></i>Apply Filters
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Job Listings -->
                <div class="col-lg-8 col-md-12 col-sm-12">
                    <!-- Results Header -->
                    <div class="row">
                        <div class="col-12">
                            <div
                                class="row align-items-center justify-content-between mx-0 bg-white rounded py-3 mb-4 shadow-sm">
                                <div class="col-xl-6 col-lg-6 col-md-6">
                                    <h6 class="mb-0 ft-medium fs-sm">
                                        <span class="result-count"><?php echo number_format($total_results); ?></span>
                                        Job<?php echo $total_results != 1 ? 's' : ''; ?> Found
                                        <?php if (!empty($search)): ?>
                                        <span class="text-muted"> for </span>
                                        "<span class="search-highlight"><?php echo htmlspecialchars($search); ?></span>"
                                        <?php endif; ?>
                                    </h6>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <form method="GET" action="" class="d-inline">
                                            <?php foreach ($base_params as $key => $value): ?>
                                            <?php if ($key != 'sort' && !empty($value)): ?>
                                            <input type="hidden" name="<?php echo $key; ?>"
                                                value="<?php echo htmlspecialchars($value); ?>">
                                            <?php endif; ?>
                                            <?php endforeach; ?>
                                            <select class="custom-select" name="sort" onchange="this.form.submit()"
                                                style="max-width: 200px;">
                                                <option value="DESC" <?php echo $sort == 'DESC' ? 'selected' : ''; ?>>
                                                    Newest First</option>
                                                <option value="ASC" <?php echo $sort == 'ASC' ? 'selected' : ''; ?>>
                                                    Oldest First</option>
                                            </select>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Job Cards -->
                    <div class="row">
                        <div class="col-12">
                            <?php
                            $run = mysqli_query($con, $query);
                            if (mysqli_num_rows($run) > 0) {
                                while ($row = mysqli_fetch_array($run)) {
                                    $COMPANYID = $row['COMPANYID'];
                                    $JOBID = $row['JOBID'];
                                    $JOBSTATUS = $row['JOBSTATUS'];
                                    $DATEPOSTED = $row['DATEPOSTED'];
                                    $SALARY = $row['SALARY'];
                                    $JOBTYPE = $row['JOBTYPE'];
                                    $JOBTITLE = $row['JOBTITLE'];
                                    $COMPANYNAME = $row['COMPANYNAME'];
                                    $COMPANYLOGO = $row['COMPANYLOGO'];
                                    $COMPANYCITY = $row['COMPANYCITY'];
                                    $COMPANYCOUNTRY = $row['COMPANYCOUNTRY'];
                                    $COMPANYINDUSTRY = $row['COMPANYINDUSTRY'];
                                    $COMPANYSPECIALISM = $row['COMPANYSPECIALISM'];
                                    $DEADLINE = $row['DEADLINE'];

                                    
                                    $isActive = ($row['JOBSTATUS'] == 'Active');
                                    $isFilled = ($row['JOBSTATUS'] == 'Filled');

                                    // Check if deadline passed
									$isExpired = (strtotime($DEADLINE) < time());
                                    // if ($isExpired && !$isFilled): 
                            ?>
                            <div class="job_grid d-block border rounded px-3 pt-3 pb-2 mb-3 shadow-sm">
                                <div class="jb-list01-flex d-flex align-items-start justify-content-start">
                                    <div class="jb-list01-thumb">
                                        <a href="employer-detail.php?companyid=<?php echo $COMPANYID; ?>">
                                            <img src="<?php echo !empty($COMPANYLOGO) ? './' . $COMPANYLOGO : 'assets/img/company-default.png'; ?>"
                                                class="img-fluid" width="90"
                                                alt="<?php echo htmlspecialchars($COMPANYNAME); ?>">
                                        </a>
                                    </div>

                                    <div class="jb-list01 pl-3 flex-grow-1">
                                        <div class="jb-list-01-title">
                                            <h5 class="ft-medium mb-1">
                                                <a href="job-detail.php?jobid=<?php echo $JOBID; ?>" class="text-dark">
                                                    <?php echo htmlspecialchars($JOBTITLE); ?>
                                                </a>
                                            </h5>
                                            <p class="text-muted mb-2">
                                                <a href="employer-detail.php?companyid=<?php echo $COMPANYID; ?>"
                                                    class="text-muted">
                                                    <?php echo htmlspecialchars($COMPANYNAME); ?>
                                                </a>
                                            </p>
                                        </div>

                                        <div class="jb-list-01-info d-block mb-3">
                                            <span class="text-muted mr-3">
                                                <i class="lni lni-map-marker mr-1"></i>
                                                <?php echo htmlspecialchars($COMPANYCITY . ', ' . $COMPANYCOUNTRY); ?>
                                            </span>
                                            <span class="text-muted mr-3">
                                                <i class="lni lni-briefcase mr-1"></i>
                                                <?php echo htmlspecialchars($JOBTYPE); ?>
                                            </span>
                                            <span class="text-muted mr-3">
                                                <i class="lni lni-calendar mr-1"></i>
                                                <?php echo timeago($DATEPOSTED); ?>
                                            </span>

                                            <?php if ($SALARY > 0): ?>
                                            <span class="text-muted">
                                                <i class="lni lni-money-protection mr-1"></i>
                                                $<?php echo number_format($SALARY, 2); ?>+
                                            </span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="jb-list-01-title d-flex flex-wrap">
                                            <!-- <span class="mr-2 mb-2 px-2 py-1 rounded theme-cl theme-bg-light">
                                                <?php echo htmlspecialchars($JOBSTATUS); ?>
                                            </span> -->


                                            <?php if ($isExpired && !$isFilled): ?>
                                            <span class="mr-2 mb-2 px-2 py-1 rounded text-danger bg-light-danger">
                                                <i class="lni lni-alarm"></i> Expired
                                            </span>
                                            <?php endif ?>



                                            <?php if ($isFilled): ?>
                                            <span
                                                class="mr-2 mb-2 px-2 py-1 rounded rounded theme-cl bg-light-danger mr-2">Position
                                                Filled</span>
                                            <?php elseif ($isActive): ?>
                                            <span
                                                class="mr-2 mb-2 px-2 py-1 rounded text-success theme-bg-light mr-2">Hiring
                                            </span>
                                            <?php endif ?>



                                            <?php if (!empty($COMPANYINDUSTRY)): ?>
                                            <span class="mr-2 mb-2 px-2 py-1 rounded text-warning bg-light-warning">
                                                <?php echo htmlspecialchars($COMPANYINDUSTRY); ?>
                                            </span>
                                            <?php endif; ?>
                                            <?php if (!empty($COMPANYSPECIALISM)): ?>
                                            <span class="mr-2 mb-2 px-2 py-1 rounded text-info bg-light-info">
                                                <?php echo htmlspecialchars($COMPANYSPECIALISM); ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            //    endif; 
                                }
                            } else {
                            ?>
                            <div class="no-results bg-white rounded shadow-sm">
                                <i class="lni lni-search-alt"></i>
                                <h4 class="ft-medium">No Jobs Found</h4>
                                <p class="text-muted">
                                    <?php if (!empty($search)): ?>
                                    No jobs match your search criteria. Try adjusting your filters or search terms.
                                    <?php else: ?>
                                    There are currently no job postings available. Please check back later.
                                    <?php endif; ?>
                                </p>
                                <a href="browse-jobs.php" class="btn btn-outline-secondary mt-3">
                                    <i class="lni lni-reload mr-2 pt-4 rounded"></i>View All Jobs
                                </a>
                            </div>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <div class="row">
                        <div class="col-12">
                            <ul class="pagination justify-content-center">
                                <!-- Previous Button -->
                                <li class="page-item <?php echo $current_page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link"
                                        href="<?php echo $current_page > 1 ? 'browse-jobs.php' . buildUrl(array_merge($base_params, ['page' => $current_page - 1])) : '#'; ?>">
                                        <i class="lni lni-chevron-left"></i>
                                    </a>
                                </li>

                                <?php
                                // Show page numbers
                                $start_page = max(1, $current_page - 2);
                                $end_page = min($total_pages, $current_page + 2);

                                // First page
                                if ($start_page > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="browse-jobs.php' . buildUrl(array_merge($base_params, ['page' => 1])) . '">1</a></li>';
                                    if ($start_page > 2) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                }

                                // Page numbers
                                for ($i = $start_page; $i <= $end_page; $i++) {
                                    $active = ($i == $current_page) ? 'active' : '';
                                    echo '<li class="page-item ' . $active . '">';
                                    echo '<a class="page-link" href="browse-jobs.php' . buildUrl(array_merge($base_params, ['page' => $i])) . '">' . $i . '</a>';
                                    echo '</li>';
                                }

                                // Last page
                                if ($end_page < $total_pages) {
                                    if ($end_page < $total_pages - 1) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                    echo '<li class="page-item"><a class="page-link" href="browse-jobs.php' . buildUrl(array_merge($base_params, ['page' => $total_pages])) . '">' . $total_pages . '</a></li>';
                                }
                                ?>

                                <!-- Next Button -->
                                <li class="page-item <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">
                                    <a class="page-link"
                                        href="<?php echo $current_page < $total_pages ? 'browse-jobs.php' . buildUrl(array_merge($base_params, ['page' => $current_page + 1])) : '#'; ?>">
                                        <i class="lni lni-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>

                            <!-- Page Info -->
                            <div class="text-center mt-3 text-muted">
                                Showing <?php echo ($offset + 1); ?> to
                                <?php echo min($offset + $results_per_page, $total_results); ?> of
                                <?php echo number_format($total_results); ?> jobs
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