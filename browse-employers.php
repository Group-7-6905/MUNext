<!DOCTYPE html>
<html lang="en">

<?php
require('./mysqli_connect.php');
include 'session_check.php';
include "include/helper.php";

// Pagination settings
$results_per_page = 12;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $results_per_page;

// Initialize variables
$search = '';
$search_by = '';
$industry = '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'DESC';

// Handle search from form or URL parameters
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
    
    if (isset($_POST['industry'])) {
        $industry = mysqli_real_escape_string($con, trim($_POST['industry']));
    } elseif (isset($_GET['industry'])) {
        $industry = mysqli_real_escape_string($con, trim($_GET['industry']));
    }
}

// Build the query
$where_conditions = [];

// ALWAYS filter by active company status
$where_conditions[] = "COMPANYSTATUS = 'Active'";

// Search conditions
if (!empty($search)) {
    if ($search_by == 'name') {
        $where_conditions[] = "COMPANYNAME LIKE '%$search%'";
    } elseif ($search_by == 'location') {
        $where_conditions[] = "(COMPANYCITY LIKE '%$search%' OR COMPANYCOUNTRY LIKE '%$search%' OR COMPANYADDRESS LIKE '%$search%')";
    } else {
        // Search all fields if no specific search_by
        $where_conditions[] = "(COMPANYNAME LIKE '%$search%' OR COMPANYCITY LIKE '%$search%' OR COMPANYCOUNTRY LIKE '%$search%' OR COMPANYADDRESS LIKE '%$search%')";
    }
}

// Industry filter
if (!empty($industry)) {
    $where_conditions[] = "COMPANYINDUSTRY LIKE '%$industry%'";
}

// Build WHERE clause
$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Count total results
$count_query = "SELECT COUNT(*) as total FROM tblcompany $where_clause";
$count_result = mysqli_query($con, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_results = $count_row['total'];
$total_pages = ceil($total_results / $results_per_page);

// Main query with pagination
$query = "SELECT c.*, 
          (SELECT COUNT(*) FROM tbljob WHERE COMPANYID = c.COMPANYID AND JOBSTATUS = 'Active') as active_jobs
          FROM tblcompany c
          $where_clause
          ORDER BY c.COMPANYID $sort
          LIMIT $results_per_page OFFSET $offset";

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
    'industry' => $industry,
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
                        <?php echo !empty($search) ? 'Search Results' : 'Browse Employers'; ?>
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent">
                            <li class="breadcrumb-item"><a href="index.php" class="text-light">Home</a></li>
                            <li class="breadcrumb-item"><a href="#" class="text-light">Employers</a></li>
                            <li class="breadcrumb-item active text-warning">Browse Employers</li>
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
                    <form method="GET" action="browse-employers.php" id="filterForm">
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                        <input type="hidden" name="search_by" value="<?php echo htmlspecialchars($search_by); ?>">

                        <div class="bg-white rounded shadow-sm">
                            <div
                                class="sidebar_header d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                                <h4 class="ft-medium fs-lg mb-0">Search Filter</h4>
                                <div class="ssh-header">
                                    <a href="browse-employers.php" class="clear_all ft-medium text-muted">Clear All</a>
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
                                                placeholder="Search employers..."
                                                value="<?php echo htmlspecialchars($search); ?>">
                                        </div>
                                        <div class="form-group">
                                            <select class="custom-select" name="search_by">
                                                <option value="">All Fields</option>
                                                <option value="name"
                                                    <?php echo $search_by == 'name' ? 'selected' : ''; ?>>Company Name
                                                </option>
                                                <option value="location"
                                                    <?php echo $search_by == 'location' ? 'selected' : ''; ?>>Location
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="filter_wraps">
                                        <!-- Industry Filter -->
                                        <div class="single_search_boxed px-4 pt-0 border-bottom">
                                            <div class="widget-boxed-header py-3">
                                                <h4>
                                                    <a href="#industries" class="ft-medium fs-md" data-toggle="collapse"
                                                        aria-expanded="true">Industry</a>
                                                </h4>
                                            </div>
                                            <div class="widget-boxed-body collapse show" id="industries">
                                                <div class="side-list no-border">
                                                    <div class="single_filter_card">
                                                        <div class="card-body p-0">
                                                            <div class="inner_widget_link">
                                                                <ul class="no-ul-list filter-list">
                                                                    <?php
                                                                    $query_ind = "SELECT COMPANYINDUSTRY, COUNT(*) as count 
                                                                                  FROM tblcompany 
                                                                                  WHERE COMPANYINDUSTRY IS NOT NULL AND COMPANYINDUSTRY != '' AND COMPANYSTATUS = 'Active'
                                                                                  GROUP BY COMPANYINDUSTRY 
                                                                                  ORDER BY count DESC 
                                                                                  LIMIT 10";
                                                                    $run_ind = mysqli_query($con, $query_ind);
                                                                    
                                                                    // All industries option
                                                                    $all_checked = empty($industry) ? 'checked' : '';
                                                                    ?>
                                                                    <li>
                                                                        <input id="ind_all" class="radio-custom"
                                                                            name="industry" type="radio" value=""
                                                                            <?php echo $all_checked; ?>
                                                                            onchange="this.form.submit()">
                                                                        <label for="ind_all" class="radio-custom-label">
                                                                            All Industries
                                                                        </label>
                                                                    </li>
                                                                    <?php
                                                                    while ($row_ind = mysqli_fetch_array($run_ind)) {
                                                                        $checked = ($industry == $row_ind['COMPANYINDUSTRY']) ? 'checked' : '';
                                                                    ?>
                                                                    <li>
                                                                        <input
                                                                            id="ind_<?php echo $row_ind['COMPANYINDUSTRY']; ?>"
                                                                            class="radio-custom" name="industry"
                                                                            type="radio"
                                                                            value="<?php echo htmlspecialchars($row_ind['COMPANYINDUSTRY']); ?>"
                                                                            <?php echo $checked; ?>
                                                                            onchange="this.form.submit()">
                                                                        <label
                                                                            for="ind_<?php echo $row_ind['COMPANYINDUSTRY']; ?>"
                                                                            class="radio-custom-label">
                                                                            <?php echo htmlspecialchars($row_ind['COMPANYINDUSTRY']); ?>
                                                                            <span
                                                                                class="filter-count"><?php echo $row_ind['count']; ?></span>
                                                                        </label>
                                                                    </li>
                                                                    <?php } ?>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Popular Industries -->
                                        <div class="single_search_boxed px-4 pt-3 pb-3">
                                            <h5 class="ft-medium fs-md mb-3">Popular Industries</h5>
                                            <div class="d-flex flex-wrap">
                                                <?php
                                                $popular_ind = mysqli_query($con, "SELECT DISTINCT COMPANYINDUSTRY FROM tblcompany WHERE COMPANYINDUSTRY IS NOT NULL AND COMPANYINDUSTRY != '' AND COMPANYSTATUS = 'Active' LIMIT 6");
                                                while ($pop = mysqli_fetch_array($popular_ind)) {
                                                ?>
                                                <a href="?industry=<?php echo urlencode($pop['COMPANYINDUSTRY']); ?>"
                                                    class="industry-badge">
                                                    <?php echo htmlspecialchars($pop['COMPANYINDUSTRY']); ?>
                                                </a>
                                                <?php } ?>
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

                <!-- Employer Listings -->
                <div class="col-lg-8 col-md-12 col-sm-12">
                    <!-- Results Header -->
                    <div class="row">
                        <div class="col-12">
                            <div
                                class="row align-items-center justify-content-between mx-0 bg-white rounded py-3 mb-4 shadow-sm">
                                <div class="col-xl-6 col-lg-6 col-md-6">
                                    <h6 class="mb-0 ft-medium fs-sm">
                                        <span class="result-count"><?php echo number_format($total_results); ?></span>
                                        Employer<?php echo $total_results != 1 ? 's' : ''; ?> Found
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

                    <!-- Employer Cards -->
                    <div class="row">
                        <div class="col-12">
                            <?php
                            $run = mysqli_query($con, $query);
                            if (mysqli_num_rows($run) > 0) {
                                while ($row = mysqli_fetch_array($run)) {
                                    $COMPANYID = $row['COMPANYID'];
                                    $COMPANYNAME = $row['COMPANYNAME'];
                                    $COMPANYLOGO = $row['COMPANYLOGO'];
                                    $COMPANYCITY = $row['COMPANYCITY'];
                                    $COMPANYCOUNTRY = $row['COMPANYCOUNTRY'];
                                    $COMPANYADDRESS = $row['COMPANYADDRESS'];
                                    $COMPANYCONTACTNO = $row['COMPANYCONTACTNO'];
                                    $COMPANYINDUSTRY = $row['COMPANYINDUSTRY'];
                                    $COMPANYSPECIALISM = $row['COMPANYSPECIALISM'];
                                    $active_jobs = $row['active_jobs'];
                            ?>
                            <div class="job_grid d-block border rounded px-3 pt-3 pb-3 mb-3 shadow-sm">
                                <div class="jb-list01-flex d-flex align-items-start justify-content-start">
                                    <a href="employer-detail.php?companyid=<?php echo $COMPANYID; ?>">
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
                                    </a>

                                    <div class="jb-list01 pl-3 flex-grow-1">
                                        <div class="jb-list-01-title mb-2">
                                            <h5 class="ft-medium mb-1">
                                                <a href="employer-detail.php?companyid=<?php echo $COMPANYID; ?>"
                                                    class="text-dark">
                                                    <?php echo htmlspecialchars($COMPANYNAME); ?>
                                                </a>
                                                <!-- <span class="verified-badge">
                                                    <i class="lni lni-checkmark mr-1"></i>Verified
                                                </span> -->
                                                <img src="assets/img/verify.svg" class="ml-1" width="12" alt="">
                                            </h5>
                                            <?php if ($active_jobs > 0): ?>
                                            <span class="job-count-badge small">
                                                <?php echo $active_jobs; ?> Active
                                                Job<?php echo $active_jobs > 1 ? 's' : ''; ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="jb-list-01-info d-block mb-3">
                                            <span class="text-muted mr-3 d-inline-block mb-1">
                                                <i class="lni lni-map-marker mr-1"></i>
                                                <?php echo htmlspecialchars($COMPANYCITY . ', ' . $COMPANYCOUNTRY); ?>
                                            </span>
                                            <?php if (!empty($COMPANYADDRESS)): ?>
                                            <span class="text-muted mr-3 d-inline-block mb-1">
                                                <i class="lni lni-home mr-1"></i>
                                                <?php echo htmlspecialchars($COMPANYADDRESS); ?>
                                            </span>
                                            <?php endif; ?>
                                            <?php if (!empty($COMPANYCONTACTNO)): ?>
                                            <span class="text-muted mr-3 d-inline-block mb-1">
                                                <i class="lni lni-phone mr-1"></i>
                                                <?php echo htmlspecialchars($COMPANYCONTACTNO); ?>
                                            </span>
                                            <?php endif; ?>
                                            <?php if (!empty($COMPANYINDUSTRY)): ?>
                                            <span class="text-muted d-inline-block mb-1">
                                                <i class="lni lni-users mr-1"></i>
                                                <?php echo htmlspecialchars($COMPANYINDUSTRY); ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="jb-list-01-title d-flex flex-wrap align-items-center">
                                            <?php if (!empty($COMPANYINDUSTRY)): ?>
                                            <span class="mr-2 mb-2 px-2 py-1 rounded theme-cl theme-bg-light">
                                                <?php echo htmlspecialchars($COMPANYINDUSTRY); ?>
                                            </span>
                                            <?php endif; ?>
                                            <?php if (!empty($COMPANYSPECIALISM)): ?>
                                            <span class="mr-2 mb-2 px-2 py-1 rounded text-warning bg-light-warning">
                                                <?php echo htmlspecialchars($COMPANYSPECIALISM); ?>
                                            </span>
                                            <?php endif; ?>
                                            <!-- <a href="employer-detail.php?companyid=<?php echo $COMPANYID; ?>"
                                                class="ml-auto btn btn-sm theme-bg text-white">
                                                View Company
                                            </a> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                                }
                            } else {
                            ?>
                            <div class="no-results">
                                <i class="lni lni-apartment"></i>
                                <h4 class="ft-medium">No Employers Found</h4>
                                <p class="text-muted">
                                    <?php if (!empty($search)): ?>
                                    No employers match your search criteria. Try adjusting your filters or search terms.
                                    <?php else: ?>
                                    There are currently no employers registered. Please check back later.
                                    <?php endif; ?>
                                </p>
                                <a href="browse-employers.php" class="btn rounded btn-outline-secondary mt-3 pt-4">
                                    <i class="lni lni-reload mr-2"></i>View All Employers
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
                                        href="<?php echo $current_page > 1 ? 'browse-employers.php' . buildUrl(array_merge($base_params, ['page' => $current_page - 1])) : '#'; ?>">
                                        <i class="lni lni-chevron-left"></i>
                                    </a>
                                </li>

                                <?php
                                // Show page numbers
                                $start_page = max(1, $current_page - 2);
                                $end_page = min($total_pages, $current_page + 2);

                                // First page
                                if ($start_page > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="browse-employers.php' . buildUrl(array_merge($base_params, ['page' => 1])) . '">1</a></li>';
                                    if ($start_page > 2) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                }

                                // Page numbers
                                for ($i = $start_page; $i <= $end_page; $i++) {
                                    $active = ($i == $current_page) ? 'active' : '';
                                    echo '<li class="page-item ' . $active . '">';
                                    echo '<a class="page-link" href="browse-employers.php' . buildUrl(array_merge($base_params, ['page' => $i])) . '">' . $i . '</a>';
                                    echo '</li>';
                                }

                                // Last page
                                if ($end_page < $total_pages) {
                                    if ($end_page < $total_pages - 1) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                    echo '<li class="page-item"><a class="page-link" href="browse-employers.php' . buildUrl(array_merge($base_params, ['page' => $total_pages])) . '">' . $total_pages . '</a></li>';
                                }
                                ?>

                                <!-- Next Button -->
                                <li class="page-item <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">
                                    <a class="page-link"
                                        href="<?php echo $current_page < $total_pages ? 'browse-employers.php' . buildUrl(array_merge($base_params, ['page' => $current_page + 1])) : '#'; ?>">
                                        <i class="lni lni-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>

                            <!-- Page Info -->
                            <div class="text-center mt-3 text-muted">
                                Showing <?php echo ($offset + 1); ?> to
                                <?php echo min($offset + $results_per_page, $total_results); ?> of
                                <?php echo number_format($total_results); ?> employers
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

    <script>
    // Smooth scroll for back to top
    $(document).ready(function() {
        // Auto-submit form on filter change handled by onchange events

        // Highlight search terms
        <?php if (!empty($search)): ?>
        var searchTerm = "<?php echo addslashes($search); ?>";
        $(".job_grid").each(function() {
            var html = $(this).html();
            var regex = new RegExp(searchTerm, 'gi');
            html = html.replace(regex, '<span class="search-highlight">$&</span>');
            $(this).html(html);
        });
        <?php endif; ?>
    });
    </script>
</body>

</html>