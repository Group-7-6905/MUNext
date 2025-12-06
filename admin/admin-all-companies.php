<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';
require '../include/toast.php';

// ==================== HANDLE ACTIONS ====================
if (isset($_POST['action']) && isset($_POST['company_id'])) {
    $companyId = mysqli_real_escape_string($con, $_POST['company_id']);
    $action = mysqli_real_escape_string($con, $_POST['action']);
    
    switch ($action) {
        case 'activate':
            $query = "UPDATE tblcompany SET COMPANYSTATUS = 'Active' WHERE COMPANYID = ?";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "i", $companyId);
            if (mysqli_stmt_execute($stmt)) {
                Toast::success("Company activated successfully!");
            } else {
                Toast::error("Failed to activate company.");
            }
            break;
            
        case 'suspend':
            $query = "UPDATE tblcompany SET COMPANYSTATUS = 'Suspended' WHERE COMPANYID = ?";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "i", $companyId);
            if (mysqli_stmt_execute($stmt)) {
                Toast::success("Company suspended successfully!");
            } else {
                Toast::error("Failed to suspend company.");
            }
            break;
            
        case 'delete':
            // Check if company has jobs
            $checkQuery = "SELECT COUNT(*) as count FROM tbljob WHERE COMPANYID = ?";
            $stmt = mysqli_prepare($con, $checkQuery);
            mysqli_stmt_bind_param($stmt, "i", $companyId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            
            if ($result['count'] > 0) {
                Toast::error("Cannot delete company with active jobs. Please remove jobs first.");
            } else {
                $query = "DELETE FROM tblcompany WHERE COMPANYID = ?";
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, "i", $companyId);
                if (mysqli_stmt_execute($stmt)) {
                    Toast::success("Company deleted successfully!");
                } else {
                    Toast::error("Failed to delete company.");
                }
            }
            break;
    }
}

// ==================== PAGINATION & FILTERING ====================
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 15;
$offset = ($page - 1) * $limit;

// Search and filters
$search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($con, $_GET['status']) : '';
$country_filter = isset($_GET['country']) ? mysqli_real_escape_string($con, $_GET['country']) : '';

// Build query
$whereConditions = [];
$params = [];
$types = '';

// Default to Pending status for verification page
if (!empty($status_filter)) {
    $whereConditions[] = "COMPANYSTATUS = ?";
    $params[] = $status_filter;
    $types .= 's';
} else {
    $whereConditions[] = "COMPANYSTATUS != 'Pending'";
}


if (!empty($search)) {
    $whereConditions[] = "(COMPANYNAME LIKE ? OR COMPANYEMAIL LIKE ? OR COMPANYCITY LIKE ?)";
    $searchParam = "%$search%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= 'sss';
}

if (!empty($status_filter)) {
    $whereConditions[] = "COMPANYSTATUS = ?";
    $params[] = $status_filter;
    $types .= 's';
}

if (!empty($country_filter)) {
    $whereConditions[] = "COMPANYCOUNTRY = ?";
    $params[] = $country_filter;
    $types .= 's';
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM tblcompany $whereClause";
if (!empty($params)) {
    $stmt = mysqli_prepare($con, $countQuery);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $totalCompanies = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];
} else {
    $totalCompanies = mysqli_fetch_assoc(mysqli_query($con, $countQuery))['total'];
}

$totalPages = ceil($totalCompanies / $limit);

// Get companies with job count
$companiesQuery = "SELECT c.*, 
                   (SELECT COUNT(*) FROM tbljob WHERE COMPANYID = c.COMPANYID) as job_count,
                   (SELECT COUNT(*) FROM tbljob WHERE COMPANYID = c.COMPANYID AND JOBSTATUS = 'Active') as active_jobs
                   FROM tblcompany c 
                   $whereClause 
                   ORDER BY c.DATEREGISTERED DESC 
                   LIMIT ? OFFSET ?";

if (!empty($params)) {
    $stmt = mysqli_prepare($con, $companiesQuery);
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $companiesResult = mysqli_stmt_get_result($stmt);
} else {
    $stmt = mysqli_prepare($con, $companiesQuery);
    mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
    mysqli_stmt_execute($stmt);
    $companiesResult = mysqli_stmt_get_result($stmt);
}

// Get countries for filter
$countriesQuery = "SELECT DISTINCT COMPANYCOUNTRY FROM tblcompany WHERE COMPANYCOUNTRY IS NOT NULL AND COMPANYCOUNTRY != '' ORDER BY COMPANYCOUNTRY";
$countriesResult = mysqli_query($con, $countriesQuery);

// ==================== STATISTICS ====================
$totalCompaniesCount = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM tblcompany"))['total'];
$activeCompaniesCount = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM tblcompany WHERE COMPANYSTATUS = 'Active'"))['total'];
$pendingCompaniesCount = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM tblcompany WHERE COMPANYSTATUS = 'Pending'"))['total'];
$suspendedCompaniesCount = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM tblcompany WHERE COMPANYSTATUS = 'Suspended'"))['total'];
?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>All Companies - Admin Dashboard | MUNext</title>
    <link href="assets/css/styles.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/custom-dashboard.css">
</head>

<body>
    <div id="main-wrapper">
        <?php include 'header.php' ?>
        <div class="clearfix"></div>

        <div class="dashboard-wrap bg-light">
            <a class="mobNavigation" data-toggle="collapse" href="#MobNav" role="button">
                <i class="fas fa-bars mr-2"></i>Dashboard Navigation
            </a>

            <?php include 'sidenav.php' ?>

            <div class="dashboard-content">
                <!-- Page Header -->
                <div class="page-header-section">
                    <h1 class="page-title">
                        <i class="fa fa-building mr-2"></i>All Companies
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Companies</a></li>
                            <li class="breadcrumb-item active">All Companies</li>
                        </ol>
                    </nav>
                </div>

                <div class="container-fluid">
                    <!-- Statistics -->
                    <div class="stats-row">
                        <div class="stat-box total">
                            <h6>Total Companies</h6>
                            <h2><?php echo number_format($totalCompaniesCount); ?></h2>
                        </div>
                        <div class="stat-box active">
                            <h6>Active Companies</h6>
                            <h2><?php echo number_format($activeCompaniesCount); ?></h2>
                        </div>
                        <div class="stat-box pending">
                            <h6>Pending Approval</h6>
                            <h2><?php echo number_format($pendingCompaniesCount); ?></h2>
                        </div>
                        <div class="stat-box suspended">
                            <h6>Suspended</h6>
                            <h2><?php echo number_format($suspendedCompaniesCount); ?></h2>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="filter-section">
                        <form method="GET" action="">
                            <div class="row align-items-end">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Search Companies</label>
                                    <div class="search-box">
                                        <i class="lni lni-search-alt"></i>
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Search by name, email, or city..."
                                            value="<?php echo htmlspecialchars($search); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="Active"
                                            <?php echo $status_filter === 'Active' ? 'selected' : ''; ?>>Active
                                        </option>
                                        <option value="Pending"
                                            <?php echo $status_filter === 'Pending' ? 'selected' : ''; ?>>Pending
                                        </option>
                                        <option value="Suspended"
                                            <?php echo $status_filter === 'Suspended' ? 'selected' : ''; ?>>Suspended
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Country</label>
                                    <select name="country" class="form-control">
                                        <option value="">All Countries</option>
                                        <?php while ($country = mysqli_fetch_assoc($countriesResult)): ?>
                                        <option value="<?php echo htmlspecialchars($country['COMPANYCOUNTRY']); ?>"
                                            <?php echo $country_filter === $country['COMPANYCOUNTRY'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($country['COMPANYCOUNTRY']); ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <button type="submit" class="btn-filter w-100">
                                        <i class="lni lni-funnel mr-1"></i>Filter
                                    </button>
                                </div>
                            </div>
                            <?php if (!empty($search) || !empty($status_filter) || !empty($country_filter)): ?>
                            <div class="row">
                                <div class="col-12">
                                    <a href="admin-all-companies.php" class="btn-reset">
                                        <i class="lni lni-close mr-1"></i>Clear Filters
                                    </a>
                                </div>
                            </div>
                            <?php endif; ?>
                        </form>
                    </div>

                    <!-- Companies Table -->
                    <div class="table-section">
                        <div class="table-header">
                            <span>
                                <i class="fa fa-building mr-2"></i>Companies List
                            </span>
                            <span>
                                Showing <?php echo mysqli_num_rows($companiesResult); ?> of
                                <?php echo number_format($totalCompanies); ?> companies
                            </span>
                        </div>

                        <?php if (mysqli_num_rows($companiesResult) > 0): ?>
                        <div class="table-responsive">
                            <table class="companies-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Company</th>
                                        <th>Contact</th>
                                        <th>Location</th>
                                        <th>Jobs Posted</th>
                                        <th>Status</th>
                                        <th>Registered</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($company = mysqli_fetch_assoc($companiesResult)): ?>
                                    <tr>
                                        <td>#<?php echo $company['COMPANYID']; ?></td>
                                        <td>
                                            <div class="company-info">


                                                <?php if (!empty($company['COMPANYLOGO'])): ?>
                                                <img src="<?php echo $path.htmlspecialchars($company['COMPANYLOGO']); ?>"
                                                    alt="Logo" class="company-logo-small"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <?php endif; ?>
                                                <div class="company-logo-placeholder"
                                                    style="<?php echo !empty($company['COMPANYLOGO']) ? 'display:none;' : ''; ?>">
                                                    <?php echo strtoupper(substr($company['COMPANYNAME'], 0, 1)); ?>
                                                </div>




                                                <div class="company-details">
                                                    <h6><?php echo htmlspecialchars($company['COMPANYNAME']); ?></h6>
                                                    <small><?php echo htmlspecialchars($company['COMPANYEMAIL'] ?? 'No email'); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (!empty($company['COMPANYCONTACTNO'])): ?>
                                            <i
                                                class="lni lni-phone mr-1"></i><?php echo htmlspecialchars($company['COMPANYCONTACTNO']); ?>
                                            <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $location = [];
                                            if (!empty($company['COMPANYCITY'])) {
                                                $location[] = $company['COMPANYCITY'];
                                            }
                                            if (!empty($company['COMPANYCOUNTRY'])) {
                                                $location[] = $company['COMPANYCOUNTRY'];
                                            }
                                            echo !empty($location) ? htmlspecialchars(implode(', ', $location)) : '<span class="text-muted">N/A</span>';
                                            ?>
                                        </td>
                                        <td>
                                            <span class="job-badge">
                                                <?php echo $company['job_count']; ?> total
                                            </span>
                                            <?php if ($company['active_jobs'] > 0): ?>
                                            <br><small class="text-success"><?php echo $company['active_jobs']; ?>
                                                active</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = 'status-secondary';
                                            $status = $company['COMPANYSTATUS'];
                                            if ($status == 'Pending') {
                                                $statusClass = 'status-pending';
                                            } elseif ($status == 'Active') {
                                                $statusClass = 'status-active';
                                            } elseif ($status == 'Suspended') {
                                                $statusClass = 'status-suspended';
                                            } elseif ($status == 'Rejected') {
                                                $statusClass = 'bg-danger text-light';
                                            }elseif ($status == 'Info Required') {
                                                $statusClass = 'badge-info';
                                            }
                                            ?>
                                            <span class="status-badge <?php echo $statusClass; ?>">
                                                <?php echo htmlspecialchars($status); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($company['DATEREGISTERED'])); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-action btn-view"
                                                    onclick="viewCompany(<?php echo $company['COMPANYID']; ?>)"
                                                    title="View">
                                                    <i class="lni lni-eye"></i>
                                                </button>

                                                <?php if ($status == 'Pending' || $status == 'Suspended'): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="company_id"
                                                        value="<?php echo $company['COMPANYID']; ?>">
                                                    <input type="hidden" name="action" value="activate">
                                                    <button type="submit" class="btn-action btn-activate"
                                                        onclick="return confirm('Activate this company?')"
                                                        title="Activate">
                                                        <i class="lni lni-checkmark"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>

                                                <?php if ($status == 'Active'): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="company_id"
                                                        value="<?php echo $company['COMPANYID']; ?>">
                                                    <input type="hidden" name="action" value="suspend">
                                                    <button type="submit" class="btn-action btn-suspend"
                                                        onclick="return confirm('Suspend this company?')"
                                                        title="Suspend">
                                                        <i class="lni lni-ban"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>

                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="company_id"
                                                        value="<?php echo $company['COMPANYID']; ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <button type="submit" class="btn-action btn-delete"
                                                        onclick="return confirm('Are you sure you want to delete this company? This action cannot be undone.')"
                                                        title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                        <div class="pagination-section">
                            <div>
                                Showing page <?php echo $page; ?> of <?php echo $totalPages; ?>
                            </div>
                            <div class="pagination">
                                <?php if ($page > 1): ?>
                                <a
                                    href="?page=<?php echo ($page - 1); ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&country=<?php echo urlencode($country_filter); ?>">
                                    <i class="lni lni-chevron-left"></i>
                                </a>
                                <?php endif; ?>

                                <?php
                                $start = max(1, $page - 2);
                                $end = min($totalPages, $page + 2);
                                
                                for ($i = $start; $i <= $end; $i++):
                                ?>
                                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&country=<?php echo urlencode($country_filter); ?>"
                                    class="<?php echo $i == $page ? 'active' : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                                <?php endfor; ?>

                                <?php if ($page < $totalPages): ?>
                                <a
                                    href="?page=<?php echo ($page + 1); ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&country=<?php echo urlencode($country_filter); ?>">
                                    <i class="lni lni-chevron-right"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php else: ?>
                        <!-- Empty State -->
                        <div class="empty-state">
                            <i class="fa fa-building"></i>
                            <h5>No Companies Found</h5>
                            <p>No companies match your current filters. Try adjusting your search criteria.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php include 'footer.php' ?>
            </div>
        </div>

        <a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>
    </div>

    <!-- Company Details Modal -->
    <div class="modal fade" id="companyModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Company Details</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="companyModalContent">
                    <div class="text-center py-5">
                        <i class="lni lni-spinner-arrow rotating" style="font-size: 3rem;"></i>
                        <p>Loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/custom.js"></script>

    <script>
    function viewCompany(companyid) {
        $('#companyModal').modal('show');
        $('#companyModalContent').html(
            '<div class="text-center py-5"><i class="lni lni-spinner-arrow rotating" style="font-size: 3rem;"></i><p>Loading...</p></div>'
        );

        // Load company details via AJAX
        $.ajax({
            url: 'admin-get-company-details.php',
            method: 'GET',
            data: {
                companyid: companyid
            },
            success: function(response) {
                $('#companyModalContent').html(response);
            },
            error: function() {
                $('#companyModalContent').html(
                    '<div class="alert alert-danger">Failed to load company details.</div>');
            }
        });
    }
    </script>

    <?php echo Toast::render(); ?>
</body>

</html>