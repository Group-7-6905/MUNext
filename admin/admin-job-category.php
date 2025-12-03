<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';
unset($result); // Clear any $result from phpcode.php

$adminId = $session_id;

// Initialize variables
$categoryid = isset($_GET['categoryid']) ? (int)$_GET['categoryid'] : 0;
$subcategoryid = isset($_GET['subcategoryid']) ? (int)$_GET['subcategoryid'] : 0;

// ==================== HANDLE CATEGORY DELETE ====================
if (isset($_GET['categorytype']) && $_GET['categorytype'] == 'delete' && isset($_GET['catid'])) {
    $catid = (int)$_GET['catid'];
    
    // Check if category has subcategories
    $checkQuery = "SELECT COUNT(*) as count FROM tbljobsubcategory WHERE CATEGORYID = ?";
    $stmtCheck = mysqli_prepare($con, $checkQuery);
    mysqli_stmt_bind_param($stmtCheck, "i", $catid);
    mysqli_stmt_execute($stmtCheck);
    $checkResult = mysqli_stmt_get_result($stmtCheck);
    $checkData = mysqli_fetch_assoc($checkResult);
    
    if ($checkData['count'] > 0) {
        $_SESSION['error_msg'] = "Cannot delete category with existing subcategories. Delete subcategories first.";
    } else {
        $deleteQuery = "DELETE FROM tbljobcategory WHERE ID = ?";
        $stmtDelete = mysqli_prepare($con, $deleteQuery);
        mysqli_stmt_bind_param($stmtDelete, "i", $catid);
        
        if (mysqli_stmt_execute($stmtDelete)) {
            $_SESSION['success_msg'] = "Category deleted successfully!";
        } else {
            $_SESSION['error_msg'] = "Failed to delete category.";
        }
    }
    
    header("Location: admin-job-category.php");
    exit();
}

// ==================== HANDLE SUBCATEGORY DELETE ====================
if (isset($_GET['subcategorytype']) && $_GET['subcategorytype'] == 'delete' && isset($_GET['subcatid'])) {
    $subcatid = (int)$_GET['subcatid'];
    
    // Check if subcategory is used in jobs
    $checkQuery = "SELECT COUNT(*) as count FROM tbljob WHERE JOBCATEGORYID = ?";
    $stmtCheck = mysqli_prepare($con, $checkQuery);
    mysqli_stmt_bind_param($stmtCheck, "i", $subcatid);
    mysqli_stmt_execute($stmtCheck);
    $checkResult = mysqli_stmt_get_result($stmtCheck);
    $checkData = mysqli_fetch_assoc($checkResult);
    
    if ($checkData['count'] > 0) {
        $_SESSION['error_msg'] = "Cannot delete subcategory with existing job postings ({$checkData['count']} jobs).";
    } else {
        $deleteQuery = "DELETE FROM tbljobsubcategory WHERE ID = ?";
        $stmtDelete = mysqli_prepare($con, $deleteQuery);
        mysqli_stmt_bind_param($stmtDelete, "i", $subcatid);
        
        if (mysqli_stmt_execute($stmtDelete)) {
            $_SESSION['success_msg'] = "Subcategory deleted successfully!";
        } else {
            $_SESSION['error_msg'] = "Failed to delete subcategory.";
        }
    }
    
    header("Location: admin-job-category.php");
    exit();
}


// ==================== FETCH DATA FOR EDIT ====================
$categoryData = null;
$subcategoryData = null;

if ($categoryid > 0) {
    $catQuery = "SELECT * FROM tbljobcategory WHERE ID = ?";
    $stmtCat = mysqli_prepare($con, $catQuery);
    mysqli_stmt_bind_param($stmtCat, "i", $categoryid);
    mysqli_stmt_execute($stmtCat);
    $categoryData = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtCat));
}

if ($subcategoryid > 0) {
    $subQuery = "SELECT sc.*, c.CATEGORY 
                 FROM tbljobsubcategory sc 
                 INNER JOIN tbljobcategory c ON sc.CATEGORYID = c.ID 
                 WHERE sc.ID = ?";
    $stmtSub = mysqli_prepare($con, $subQuery);
    mysqli_stmt_bind_param($stmtSub, "i", $subcategoryid);
    mysqli_stmt_execute($stmtSub);
    $subcategoryData = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtSub));
}

// ==================== COUNT STATISTICS ====================
$statsQuery = "SELECT 
    (SELECT COUNT(*) FROM tbljobcategory) as total_categories,
    (SELECT COUNT(*) FROM tbljobsubcategory) as total_subcategories";
$statsResult = mysqli_query($con, $statsQuery);
$stats = mysqli_fetch_assoc($statsResult);
?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Job Categories - Admin | MUNext</title>
    <link href="assets/css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.lineicons.com/3.0/lineicons.css">

    <link rel="stylesheet" href="assets/css/custom-dashboard.css">
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
                                <i class="lni lni-folder"></i> Manage Job Categories
                            </h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item text-muted"><a href="admin-dashboard.php">Home</a></li>
                                    <li class="breadcrumb-item text-muted"><a href="admin-dashboard.php">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#" class="theme-cl">Job Categories</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="dashboard-widg-bar d-block">

                    <!-- Success/Error Messages -->
                    <?php if (isset($_SESSION['success_msg'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="lni lni-checkmark-circle"></i> <?php echo $_SESSION['success_msg']; ?>
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                    <?php unset($_SESSION['success_msg']); endif; ?>

                    <?php if (isset($_SESSION['error_msg'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="lni lni-cross-circle"></i> <?php echo $_SESSION['error_msg']; ?>
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                    <?php unset($_SESSION['error_msg']); endif; ?>

                    <!-- Statistics -->
                    <div class="stats-row">
                        <div class="stat-card categories">
                            <span class="stat-number"><?php echo number_format($stats['total_categories']); ?></span>
                            <span class="stat-label">Total Categories</span>
                        </div>
                        <div class="stat-card subcategories">
                            <span class="stat-number"><?php echo number_format($stats['total_subcategories']); ?></span>
                            <span class="stat-label">Total Subcategories</span>
                        </div>
                    </div>

                    <!-- Main Content Card -->
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12">
                            <div class="_dashboard_content">
                                <div class="_dashboard_content_header bg-dark">
                                    <h4><i class="lni lni-folder mr-2"></i>Job Category Management
                                    </h4>
                                </div>

                                <div class="_dashboard_content_body py-3 px-3">
                                    <?php if (empty($categoryid) && empty($subcategoryid)): ?>
                                    <!-- Tabs Navigation -->
                                    <ul class="nav nav-tabs d-flex" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active" id="category-list-tab" data-toggle="tab"
                                                href="#category-list" role="tab">
                                                <i class="lni lni-list"></i> Category List
                                            </a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="category-add-tab" data-toggle="tab"
                                                href="#category-add" role="tab">
                                                <i class="lni lni-plus"></i> Add Category
                                            </a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="subcategory-list-tab" data-toggle="tab"
                                                href="#subcategory-list" role="tab">
                                                <i class="lni lni-list"></i> Subcategory List
                                            </a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="subcategory-add-tab" data-toggle="tab"
                                                href="#subcategory-add" role="tab">
                                                <i class="lni lni-plus"></i> Add Subcategory
                                            </a>
                                        </li>
                                    </ul>

                                    <!-- Tab Content -->
                                    <div class="tab-content mt-4" id="myTabContent">
                                        <!-- Category List -->
                                        <div class="tab-pane fade show active" id="category-list" role="tabpanel">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>SN</th>
                                                            <th>Category</th>
                                                            <th>Subcategories</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $cn = 0;
                                                        $catQuery = "SELECT c.*, 
                                                                     (SELECT COUNT(*) FROM tbljobsubcategory WHERE CATEGORYID = c.ID) as subcat_count 
                                                                     FROM tbljobcategory c ORDER BY c.ID DESC";
                                                        $catResult = mysqli_query($con, $catQuery);
                                                        
                                                        if (mysqli_num_rows($catResult) > 0):
                                                            while ($cat = mysqli_fetch_assoc($catResult)):
                                                                $cn++;
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $cn; ?></td>
                                                            <td><span
                                                                    class="font-weight-bold"><?php echo htmlspecialchars($cat['CATEGORY']); ?></span>
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="badge badge-info"><?php echo $cat['subcat_count']; ?>
                                                                    subcategories</span>
                                                            </td>
                                                            <td>
                                                                <div class="dash-action">
                                                                    <a href="?categoryid=<?php echo $cat['ID']; ?>"
                                                                        class="text-info bg-light-info"
                                                                        title="Edit Category">
                                                                        <i class="lni lni-pencil"></i>
                                                                    </a>
                                                                    <a href="?categorytype=delete&catid=<?php echo $cat['ID']; ?>"
                                                                        class="text-danger bg-light-danger"
                                                                        onclick="return confirm('Are you sure you want to delete this category?');"
                                                                        title="Delete Category">
                                                                        <i class="lni lni-trash-can"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php 
                                                            endwhile;
                                                        else:
                                                        ?>
                                                        <tr>
                                                            <td colspan="4">
                                                                <div class="empty-state">
                                                                    <i class="lni lni-folder"></i>
                                                                    <p>No categories found. Add your first category!</p>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Add Category -->
                                        <div class="tab-pane fade" id="category-add" role="tabpanel">
                                            <form method="post">
                                                <div class="row">
                                                    <div class="col-xl-8 col-lg-10 col-md-12">
                                                        <div class="form-group">
                                                            <label>Category Name <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" class="form-control"
                                                                placeholder="Enter category name" name="category"
                                                                required>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <button type="submit" name="add_category"
                                                            class="btn text-light theme-bg">
                                                            <i class="lni lni-save"></i> Save Category
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Subcategory List -->
                                        <div class="tab-pane fade" id="subcategory-list" role="tabpanel">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>SN</th>
                                                            <th>Subcategory</th>
                                                            <th>Category</th>
                                                            <th>Jobs</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $cn_sub = 0;
                                                        $subQuery = "SELECT sc.*, c.CATEGORY,
                                                                     (SELECT COUNT(*) FROM tbljob WHERE JOBCATEGORYID = sc.ID) as job_count
                                                                     FROM tbljobsubcategory sc 
                                                                     INNER JOIN tbljobcategory c ON sc.CATEGORYID = c.ID 
                                                                     ORDER BY sc.ID DESC";
                                                        $subResult = mysqli_query($con, $subQuery);
                                                        
                                                        if (mysqli_num_rows($subResult) > 0):
                                                            while ($sub = mysqli_fetch_assoc($subResult)):
                                                                $cn_sub++;
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $cn_sub; ?></td>
                                                            <td><span
                                                                    class="font-weight-bold"><?php echo htmlspecialchars($sub['SUBCATEGORY']); ?></span>
                                                            </td>
                                                            <td><span
                                                                    class="text-muted"><?php echo htmlspecialchars($sub['CATEGORY']); ?></span>
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="badge badge-primary"><?php echo $sub['job_count']; ?>
                                                                    jobs</span>
                                                            </td>
                                                            <td>
                                                                <div class="dash-action">
                                                                    <a href="?subcategoryid=<?php echo $sub['ID']; ?>"
                                                                        class="text-info bg-light-info"
                                                                        title="Edit Subcategory">
                                                                        <i class="lni lni-pencil"></i>
                                                                    </a>
                                                                    <a href="?subcategorytype=delete&subcatid=<?php echo $sub['ID']; ?>"
                                                                        class="text-danger bg-light-danger"
                                                                        onclick="return confirm('Are you sure you want to delete this subcategory?');"
                                                                        title="Delete Subcategory">
                                                                        <i class="lni lni-trash-can"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php 
                                                            endwhile;
                                                        else:
                                                        ?>
                                                        <tr>
                                                            <td colspan="5">
                                                                <div class="empty-state">
                                                                    <i class="lni lni-folder"></i>
                                                                    <p>No subcategories found. Add your first
                                                                        subcategory!
                                                                    </p>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Add Subcategory -->
                                        <div class="tab-pane fade" id="subcategory-add" role="tabpanel"
                                            style="min-height: 300px;">
                                            <form method="post">
                                                <div class="row">
                                                    <div class="col-xl-8 col-lg-10 col-md-12">
                                                        <div class="form-group">
                                                            <label>Category <span class="text-danger">*</span></label>
                                                            <select class="form-control custom-select" name="categoryid"
                                                                required>
                                                                <option value="" hidden>Select Category</option>
                                                                <?php
                                                                $catListQuery = "SELECT * FROM tbljobcategory ORDER BY CATEGORY ASC";
                                                                $catListResult = mysqli_query($con, $catListQuery);
                                                                while ($catItem = mysqli_fetch_assoc($catListResult)):
                                                                ?>
                                                                <option value="<?php echo $catItem['ID']; ?>">
                                                                    <?php echo htmlspecialchars($catItem['CATEGORY']); ?>
                                                                </option>
                                                                <?php endwhile; ?>
                                                            </select>
                                                        </div>

                                                        <div class="form-group">
                                                            <label>Subcategory Name <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" class="form-control"
                                                                placeholder="Enter subcategory name" name="subcategory"
                                                                required>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <button type="submit" name="add_subcategory"
                                                            class="btn text-light theme-bg">
                                                            <i class="lni lni-save"></i> Save Subcategory
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Edit Category Form -->
                                    <?php if ($categoryid > 0 && $categoryData): ?>
                                    <div class="edit-form-wrapper">
                                        <h4 class="edit-form-title">
                                            <i class="lni lni-pencil"></i> Edit Category
                                        </h4>
                                        <form method="post">
                                            <div class="row">
                                                <div class="col-xl-8 col-lg-10 col-md-12">
                                                    <div class="form-group">
                                                        <label>Category Name <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="category" required
                                                            value="<?php echo htmlspecialchars($categoryData['CATEGORY']); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <button type="submit" name="edit_category"
                                                        class="btn text-light theme-bg">
                                                        <i class="lni lni-save"></i> Update Category
                                                    </button>
                                                    <a href="admin-job-category.php" class="btn btn-secondary ml-2">
                                                        <i class="lni lni-close"></i> Cancel
                                                    </a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Edit Subcategory Form -->
                                    <?php if ($subcategoryid > 0 && $subcategoryData): ?>
                                    <div class="edit-form-wrapper" style="min-height: 350px;">
                                        <h4 class="edit-form-title">
                                            <i class="lni lni-pencil"></i> Edit Subcategory
                                        </h4>
                                        <form method="post">
                                            <div class="row">
                                                <div class="col-xl-8 col-lg-10 col-md-12">
                                                    <div class="form-group">
                                                        <label>Category <span class="text-danger">*</span></label>
                                                        <select class="form-control custom-select" name="categoryid"
                                                            required>
                                                            <option
                                                                value="<?php echo $subcategoryData['CATEGORYID']; ?>">
                                                                <?php echo htmlspecialchars($subcategoryData['CATEGORY']); ?>
                                                            </option>
                                                            <?php
                                                            $catListQuery = "SELECT * FROM tbljobcategory WHERE ID != ? ORDER BY CATEGORY ASC";
                                                            $stmtCatList = mysqli_prepare($con, $catListQuery);
                                                            mysqli_stmt_bind_param($stmtCatList, "i", $subcategoryData['CATEGORYID']);
                                                            mysqli_stmt_execute($stmtCatList);
                                                            $catListResult = mysqli_stmt_get_result($stmtCatList);
                                                            while ($catItem = mysqli_fetch_assoc($catListResult)):
                                                            ?>
                                                            <option value="<?php echo $catItem['ID']; ?>">
                                                                <?php echo htmlspecialchars($catItem['CATEGORY']); ?>
                                                            </option>
                                                            <?php endwhile; ?>
                                                        </select>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Subcategory Name <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="subcategory"
                                                            required
                                                            value="<?php echo htmlspecialchars($subcategoryData['SUBCATEGORY']); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <button type="submit" name="edit_subcategory"
                                                        class="btn text-light theme-bg">
                                                        <i class="lni lni-save"></i> Update Subcategory
                                                    </button>
                                                    <a href="admin-job-category.php" class="btn btn-secondary ml-2">
                                                        <i class="lni lni-close"></i> Cancel
                                                    </a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
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

    <script>
    $(document).ready(function() {
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
    </script>
</body>

</html>