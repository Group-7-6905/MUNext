<!DOCTYPE html>
<html lang="en">

<?php
/**
 * ==================== BROWSE JOB CATEGORIES PAGE ====================
 * Displays all job categories with their subcategories and active job counts
 */

// ==================== INITIALIZE ====================
require('./mysqli_connect.php');
include 'session_check.php';
include 'include/helper.php';

// ==================== FETCH ALL CATEGORIES ====================
function getAllCategories($con) {
    $query = "SELECT * FROM tbljobcategory ORDER BY CATEGORY ASC";
    $stmt = mysqli_prepare($con, $query);
    
    if (!$stmt) {
        return [];
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    return $categories;
}

// ==================== FETCH SUBCATEGORIES FOR A CATEGORY ====================
function getSubcategoriesByCategory($con, $categoryId) {
    $query = "SELECT s.*, COUNT(j.JOBID) as job_count 
              FROM tbljobsubcategory s 
              LEFT JOIN tbljob j ON s.ID = j.JOBCATEGORYID AND j.JOBSTATUS = 'Active'
              WHERE s.CATEGORYID = ? 
              GROUP BY s.ID 
              HAVING job_count > 0 
              ORDER BY s.SUBCATEGORY ASC";
    
    $stmt = mysqli_prepare($con, $query);
    
    if (!$stmt) {
        return [];
    }
    
    mysqli_stmt_bind_param($stmt, "i", $categoryId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $subcategories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $subcategories[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    return $subcategories;
}

// ==================== GET DATA ====================
$categories = getAllCategories($con);
$totalCategories = count($categories);

// ==================== GET CATEGORY ICONS ====================
$categoryIcons = [
    'Technology' => 'lni-code',
    'Healthcare' => 'lni-heart',
    'Finance' => 'lni-wallet',
    'Education' => 'lni-graduation',
    'Marketing' => 'lni-bullhorn',
    'Sales' => 'lni-stats-up',
    'Engineering' => 'lni-cog',
    'Design' => 'lni-pencil',
    'Customer Service' => 'lni-users',
    'Human Resources' => 'lni-briefcase'
];
?>

<?php include 'include/head.php' ?>

<head>
    <title>Browse Job Categories - MUNext Job Portal</title>

    <style>
    /* ==================== COLOR VARIABLES ==================== */
    :root {
        --primary-color: #862633;
        --primary-dark: #6b1e28;
        --text-dark: #2c3e50;
        --text-muted: #6c757d;
        --border-light: #e9ecef;
        --background-light: #f8f9fa;
        --white: #ffffff;
    }

    /* ==================== PAGE HEADER ==================== */
    .page-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        padding: 50px 0 70px;
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        opacity: 0.3;
    }

    .page-header-content {
        position: relative;
        z-index: 1;
    }

    .page-title {
        color: var(--white);
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .page-subtitle {
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.95rem;
        margin-bottom: 15px;
    }

    .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
        font-size: 0.85rem;
    }

    .breadcrumb-item a {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: var(--white);
    }

    /* ==================== STATS SECTION ==================== */
    .stats-section {
        margin-top: -40px;
        position: relative;
        z-index: 10;
    }

    .stats-card {
        background: var(--white);
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        text-align: center;
        transition: all 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 25px rgba(134, 38, 51, 0.12);
    }

    .stats-number {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary-color);
        display: block;
        margin-bottom: 5px;
    }

    .stats-label {
        color: var(--text-muted);
        font-weight: 600;
        font-size: 0.85rem;
    }

    /* ==================== CATEGORY SECTION ==================== */
    .category-section {
        margin-bottom: 35px;
        background: var(--white);
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--border-light);
    }

    .category-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--border-light);
    }

    .category-icon-wrapper {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        flex-shrink: 0;
    }

    .category-icon {
        color: var(--white);
        font-size: 1.3rem;
    }

    .category-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0;
    }

    .category-count {
        color: var(--text-muted);
        font-size: 0.8rem;
        margin-top: 3px;
    }

    /* ==================== SUBCATEGORY CARDS ==================== */
    .subcategories-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 15px;
    }

    .subcategory-card {
        background: var(--white);
        border: 2px solid var(--border-light);
        border-radius: 10px;
        padding: 18px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .subcategory-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 3px;
        height: 0;
        background: var(--primary-color);
        transition: height 0.3s ease;
    }

    .subcategory-card:hover {
        border-color: var(--primary-color);
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(134, 38, 51, 0.1);
    }

    .subcategory-card:hover::before {
        height: 100%;
    }

    .subcategory-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: var(--background-light);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
    }

    .subcategory-icon i {
        color: var(--primary-color);
        font-size: 1.1rem;
    }

    .subcategory-name {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 6px;
        line-height: 1.3;
    }

    .subcategory-jobs {
        color: var(--text-muted);
        font-size: 0.8rem;
        margin-bottom: 12px;
    }

    .subcategory-link {
        color: var(--primary-color);
        font-weight: 600;
        font-size: 0.85rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.3s ease;
    }

    .subcategory-link i {
        font-size: 0.9rem;
    }

    .subcategory-link:hover {
        gap: 8px;
        text-decoration: none;
    }

    /* ==================== EMPTY STATE ==================== */
    .empty-state {
        text-align: center;
        padding: 50px 20px;
        background: var(--background-light);
        border-radius: 12px;
        border: 2px dashed var(--border-light);
    }

    .empty-state-icon {
        font-size: 3rem;
        color: var(--border-light);
        margin-bottom: 15px;
    }

    .empty-state-text {
        color: var(--text-muted);
        font-size: 0.95rem;
    }

    /* ==================== SEARCH SECTION ==================== */
    .search-section {
        margin: 35px 0;
    }

    .search-box {
        max-width: 550px;
        margin: 0 auto;
        position: relative;
    }

    .search-input {
        width: 100%;
        padding: 12px 45px 12px 18px;
        border: 2px solid var(--border-light);
        border-radius: 50px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(134, 38, 51, 0.1);
    }

    .search-btn {
        position: absolute;
        right: 4px;
        top: 50%;
        transform: translateY(-50%);
        background: var(--primary-color);
        color: var(--white);
        border: none;
        border-radius: 50%;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .search-btn:hover {
        background: var(--primary-dark);
    }

    /* ==================== RESPONSIVE DESIGN ==================== */
    @media (max-width: 991px) {
        .page-title {
            font-size: 1.7rem;
        }

        .category-section {
            padding: 20px;
        }

        .subcategories-grid {
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        }

        .stats-number {
            font-size: 1.7rem;
        }
    }

    @media (max-width: 767px) {
        .page-header {
            padding: 35px 0 55px;
        }

        .page-title {
            font-size: 1.4rem;
        }

        .page-subtitle {
            font-size: 0.85rem;
        }

        .category-header {
            flex-direction: column;
            text-align: center;
        }

        .category-icon-wrapper {
            margin-right: 0;
            margin-bottom: 12px;
        }

        .category-title {
            font-size: 1.1rem;
        }

        .subcategories-grid {
            grid-template-columns: 1fr;
        }

        .stats-section {
            margin-top: -30px;
        }

        .stats-card {
            padding: 15px;
        }

        .stats-number {
            font-size: 1.5rem;
        }

        .stats-label {
            font-size: 0.8rem;
        }
    }

    @media (max-width: 576px) {
        .category-section {
            padding: 15px;
        }

        .subcategory-card {
            padding: 15px;
        }
    }
    </style>
</head>

<body>
    <div id="main-wrapper">
        <!-- ==================== HEADER ==================== -->
        <?php include 'include/header.php' ?>
        <div class="clearfix"></div>

        <!-- ==================== PAGE HEADER ==================== -->
        <div class="page-header">
            <div class="container">
                <div class="page-header-content">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                            <li class="breadcrumb-item"><a href="job-list-v1.php">Jobs</a></li>
                            <li class="breadcrumb-item active">Browse Categories</li>
                        </ol>
                    </nav>
                    <h1 class="page-title">Browse Job Categories</h1>
                    <p class="page-subtitle">Explore thousands of opportunities across <?php echo $totalCategories; ?>
                        categories</p>
                </div>
            </div>
        </div>

        <!-- ==================== STATS SECTION ==================== -->
        <section class="stats-section">
            <div class="container">
                <div class="row g-3">
                    <div class="col-lg-4 col-md-6">
                        <div class="stats-card">
                            <span class="stats-number"><?php echo $totalCategories; ?>+</span>
                            <span class="stats-label">Job Categories</span>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="stats-card">
                            <?php
                            $totalJobsQuery = "SELECT COUNT(*) as total FROM tbljob WHERE JOBSTATUS = 'Active'";
                            $totalJobsResult = mysqli_query($con, $totalJobsQuery);
                            $totalJobs = mysqli_fetch_assoc($totalJobsResult)['total'];
                            ?>
                            <span class="stats-number"><?php echo number_format($totalJobs); ?>+</span>
                            <span class="stats-label">Active Jobs</span>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="stats-card">
                            <?php
                            $totalCompaniesQuery = "SELECT COUNT(DISTINCT COMPANYID) as total FROM tbljob WHERE JOBSTATUS = 'Active'";
                            $totalCompaniesResult = mysqli_query($con, $totalCompaniesQuery);
                            $totalCompanies = mysqli_fetch_assoc($totalCompaniesResult)['total'];
                            ?>
                            <span class="stats-number"><?php echo number_format($totalCompanies); ?>+</span>
                            <span class="stats-label">Companies Hiring</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================== SEARCH SECTION ==================== -->
        <section class="search-section">
            <div class="container">
                <div class="search-box">
                    <form action="job-list-v1.php" method="GET">
                        <input type="text" name="search" class="search-input"
                            placeholder="Search for categories or jobs..." autocomplete="off">
                        <button type="submit" class="search-btn">
                            <i class="lni lni-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- ==================== CATEGORIES SECTION ==================== -->
        <section class="middle bg-light py-5">
            <div class="container">
                <?php if (empty($categories)): ?>
                <!-- Empty State -->
                <div class="empty-state">
                    <i class="lni lni-layers empty-state-icon"></i>
                    <p class="empty-state-text">No job categories available at the moment.</p>
                </div>
                <?php else: ?>
                <?php 
                    $displayedCategories = 0;
                    foreach ($categories as $category): 
                        $categoryId = $category['ID'];
                        $categoryName = $category['CATEGORY'];
                        $subcategories = getSubcategoriesByCategory($con, $categoryId);
                        
                        // Skip category if no subcategories with active jobs
                        if (empty($subcategories)) {
                            continue;
                        }
                        
                        $displayedCategories++;
                        
                        // Get icon for category
                        $iconClass = 'lni-briefcase'; // default icon
                        foreach ($categoryIcons as $key => $icon) {
                            if (stripos($categoryName, $key) !== false) {
                                $iconClass = $icon;
                                break;
                            }
                        }
                        
                        // Count total jobs in category
                        $totalCategoryJobs = array_sum(array_column($subcategories, 'job_count'));
                    ?>

                <!-- Single Category Section -->
                <div class="category-section">
                    <!-- Category Header -->
                    <div class="category-header">
                        <div class="category-icon-wrapper">
                            <i class="lni <?php echo $iconClass; ?> category-icon"></i>
                        </div>
                        <div>
                            <h3 class="category-title"><?php echo htmlspecialchars($categoryName); ?></h3>
                            <p class="category-count">
                                <?php echo count($subcategories); ?> Subcategories •
                                <?php echo number_format($totalCategoryJobs); ?> Active Jobs
                            </p>
                        </div>
                    </div>

                    <!-- Subcategories Grid -->
                    <div class="subcategories-grid">
                        <?php foreach ($subcategories as $subcategory): ?>
                        <?php
                                    $subcategoryId = $subcategory['ID'];
                                    $subcategoryName = $subcategory['SUBCATEGORY'];
                                    $jobCount = $subcategory['job_count'];
                                    ?>

                        <div class="subcategory-card">
                            <div class="subcategory-icon">
                                <i class="lni lni-briefcase"></i>
                            </div>
                            <h4 class="subcategory-name">
                                <?php echo htmlspecialchars($subcategoryName); ?>
                            </h4>
                            <p class="subcategory-jobs">
                                <?php echo number_format($jobCount); ?> Job<?php echo $jobCount != 1 ? 's' : ''; ?>
                                Available
                            </p>
                            <a href="job-list-v1.php?jobcategoryid=<?php echo $subcategoryId; ?>"
                                class="subcategory-link">
                                Explore Jobs
                                <i class="lni lni-arrow-right-circle"></i>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php if ($displayedCategories === 0): ?>
                <!-- No Active Jobs State -->
                <div class="empty-state">
                    <i class="lni lni-briefcase empty-state-icon"></i>
                    <p class="empty-state-text">No active jobs available at the moment. Please check back later.</p>
                </div>
                <?php endif; ?>
                <?php endif; ?>
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

    <script>
    // ==================== CATEGORY SEARCH FILTER ====================
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('.search-input');

        if (searchInput) {
            // Auto-focus search on key press (Ctrl+K or Cmd+K)
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    searchInput.focus();
                }
            });
        }
    });
    </script>
</body>

</html>