<div class="collapse" id="MobNav">
    <div class="dashboard-nav">
        <div class="dashboard-inner">
            <!-- ==================== MAIN NAVIGATION ==================== -->
            <ul data-submenu-title="Main Navigation">
                <!-- Dashboard -->
                <li class="active">
                    <a href="./">
                        <i class="lni lni-dashboard mr-2"></i>Dashboard
                    </a>
                </li>

                <!-- User Management -->
                <li class="has-submenu">
                    <a href="#" class="dropdown-toggle">
                        <i class="lni lni-users mr-2"></i>User Management
                        <i class=" ml-auto"></i>
                    </a>
                    <ul class="submenu">
                        <?php
                        // Count pending job seekers
                        $queryPendingSeekersCount = "SELECT COUNT(*) as count FROM tblusers WHERE USERSTATUS = 'Pending'";
                        $resultPendingSeekersCount = mysqli_query($con, $queryPendingSeekersCount);
                        $pendingSeekersCount = mysqli_fetch_assoc($resultPendingSeekersCount)['count'];
                        ?>
                        <li>
                            <a href="admin-job-seekers.php">
                                <i class="lni lni-user mr-2"></i>Job Seekers
                                <?php if ($pendingSeekersCount > 0): ?>
                                <span class="count-tag bg-warning"><?php echo $pendingSeekersCount; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>

                        <?php
                        // Count pending employers
                        $queryPendingEmployersCount = "SELECT COUNT(*) as count FROM tblcompany WHERE COMPANYSTATUS = 'Pending'";
                        $resultPendingEmployersCount = mysqli_query($con, $queryPendingEmployersCount);
                        $pendingEmployersCount = mysqli_fetch_assoc($resultPendingEmployersCount)['count'];
                        ?>
                        <li>
                            <a href="admin-employers.php">
                                <i class="fa fa-building mr-2"></i>Employers
                                <?php if ($pendingEmployersCount > 0): ?>
                                <span class="count-tag bg-warning"><?php echo $pendingEmployersCount; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>

                        <li>
                            <a href="admin-administrators.php">
                                <i class="lni lni-shield mr-2"></i>Administrators
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Job Management -->
                <li class="has-submenu">
                    <a href="#" class="dropdown-toggle">
                        <i class="lni lni-briefcase mr-2"></i>Job Management
                        <i class=" ml-auto"></i>
                    </a>
                    <ul class="submenu">
                        <li>
                            <a href="admin-all-jobs.php">
                                <i class="lni lni-briefcase mr-2"></i>All Jobs
                            </a>
                        </li>

                        <?php
                        // Count pending jobs
                        $queryPendingJobs = "SELECT COUNT(*) as count FROM tbljob WHERE JOBSTATUS = 'Pending'";
                        $resultPendingJobs = mysqli_query($con, $queryPendingJobs);
                        $pendingJobsCount = mysqli_fetch_assoc($resultPendingJobs)['count'];
                        ?>
                        <li>
                            <a href="admin-pending-jobs.php">
                                <i class="lni lni-alarm-clock mr-2"></i>Pending Approval
                                <?php if ($pendingJobsCount > 0): ?>
                                <span class="count-tag bg-danger"><?php echo $pendingJobsCount; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>

                        <li>
                            <a href="admin-active-jobs.php">
                                <i class="lni lni-checkmark-circle mr-2"></i>Active Jobs
                            </a>
                        </li>

                        <li>
                            <a href="admin-expired-jobs.php">
                                <i class="lni lni-close mr-2"></i>Expired Jobs
                            </a>
                        </li>

                        <li>
                            <a href="admin-job-category.php">
                                <i class="lni lni-bookmark mr-2"></i>Job Category Management
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Company Management -->
                <li class="has-submenu">
                    <a href="#" class="dropdown-toggle">
                        <i class="fa fa-building mr-2"></i>Company Management
                        <i class=" ml-auto"></i>
                    </a>
                    <ul class="submenu">
                        <li>
                            <a href="admin-all-companies.php">
                                <i class="fa fa-building mr-2"></i>All Companies
                            </a>
                        </li>

                        <?php
                        // Count unverified companies
                        $queryUnverifiedCompanies = "SELECT COUNT(*) as count FROM tblcompany WHERE COMPANYSTATUS = 'Pending'";
                        $resultUnverifiedCompanies = mysqli_query($con, $queryUnverifiedCompanies);
                        $unverifiedCount = mysqli_fetch_assoc($resultUnverifiedCompanies)['count'];
                        ?>
                        <li>
                            <a href="admin-verify-companies.php">
                                <i class="lni lni-checkmark mr-2"></i>Pending Verification
                                <?php if ($unverifiedCount > 0): ?>
                                <span class="count-tag bg-warning"><?php echo $unverifiedCount; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>

                        <li>
                            <a href="admin-company-statistics.php">
                                <i class="lni lni-bar-chart mr-2"></i>Statistics
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Application Management -->
                <li class="has-submenu">
                    <a href="#" class="dropdown-toggle">
                        <i class="lni lni-files mr-2"></i>Applications
                        <i class=" ml-auto"></i>
                    </a>
                    <ul class="submenu">
                        <?php
                        // Count all applications
                        $queryAllApplications = "SELECT COUNT(*) as count FROM tbljobapplication";
                        $resultAllApplications = mysqli_query($con, $queryAllApplications);
                        $allApplicationsCount = mysqli_fetch_assoc($resultAllApplications)['count'];
                        ?>
                        <li>
                            <a href="admin-all-applications.php">
                                <i class="lni lni-files mr-2"></i>All Applications
                                <span class="count-tag bg-info"><?php echo $allApplicationsCount; ?></span>
                            </a>
                        </li>

                        <?php
                        // Count pending applications
                        $queryPendingApplications = "SELECT COUNT(*) as count FROM tbljobapplication WHERE APPLICATIONSTATUS = 'Pending'";
                        $resultPendingApplications = mysqli_query($con, $queryPendingApplications);
                        $pendingApplicationsCount = mysqli_fetch_assoc($resultPendingApplications)['count'];
                        ?>
                        <li>
                            <a href="admin-pending-applications.php">
                                <i class="lni lni-alarm-clock mr-2"></i>Pending
                                <?php if ($pendingApplicationsCount > 0): ?>
                                <span class="count-tag bg-warning"><?php echo $pendingApplicationsCount; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>

                        <li>
                            <a href="admin-application-statistics.php">
                                <i class="lni lni-bar-chart mr-2"></i>Statistics
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Reports & Analytics -->
                <li class="has-submenu">
                    <a href="#" class="dropdown-toggle">
                        <i class="lni lni-bar-chart mr-2"></i>Reports & Analytics
                        <i class=" ml-auto"></i>
                    </a>
                    <ul class="submenu">
                        <li>
                            <a href="admin-analytics-dashboard.php">
                                <i class="lni lni-dashboard mr-2"></i>Analytics Dashboard
                            </a>
                        </li>

                        <li>
                            <a href="admin-user-reports.php">
                                <i class="lni lni-users mr-2"></i>User Reports
                            </a>
                        </li>

                        <li>
                            <a href="admin-job-reports.php">
                                <i class="lni lni-briefcase mr-2"></i>Job Reports
                            </a>
                        </li>

                        <li>
                            <a href="admin-application-reports.php">
                                <i class="lni lni-files mr-2"></i>Application Reports
                            </a>
                        </li>

                        <li>
                            <a href="admin-activity-logs.php">
                                <i class="lni lni-list mr-2"></i>Activity Logs
                            </a>
                        </li>

                        <li>
                            <a href="admin-export-reports.php">
                                <i class="lni lni-download mr-2"></i>Export Reports
                            </a>
                        </li>
                    </ul>
                </li>


            </ul>

            <!-- ==================== SETTINGS & ACCOUNT ==================== -->
            <ul data-submenu-title="Settings & Account">
                <!-- Site Settings -->
                <li class="has-submenu">
                    <a href="#" class="dropdown-toggle">
                        <i class="lni lni-cog mr-2"></i>Settings
                        <i class=" ml-auto"></i>
                    </a>
                    <ul class="submenu">
                        <li>
                            <a href="admin-general-settings.php">
                                <i class="lni lni-cog mr-2"></i>General Settings
                            </a>
                        </li>

                        <li>
                            <a href="admin-email-settings.php">
                                <i class="lni lni-envelope mr-2"></i>Email Settings
                            </a>
                        </li>

                    </ul>
                </li>

                <!-- Help & Support -->
                <li>
                    <a href="admin-help-support.php">
                        <i class="lni lni-help mr-2"></i>Help & Support
                    </a>
                </li>

                <!-- My Account -->
                <li class="has-submenu">
                    <a href="#" class="dropdown-toggle">
                        <i class="lni lni-user mr-2"></i>My Account
                        <i class=" ml-auto"></i>
                    </a>
                    <ul class="submenu">
                        <li>
                            <a href="admin-profile.php">
                                <i class="lni lni-user mr-2"></i>My Profile
                            </a>
                        </li>

                        <li>
                            <a href="admin-change-password.php">
                                <i class="lni lni-lock-alt mr-2"></i>Change Password
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Logout -->
                <li>
                    <a href="logout.php" onclick="return confirm('Are you sure you want to logout?');">
                        <i class="lni lni-power-switch mr-2"></i>Log Out
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<style>
/* ==================== SUBMENU STYLES ==================== */
.has-submenu {
    position: relative;
}

.has-submenu .dropdown-toggle {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.has-submenu .submenu {
    display: none;
    list-style: none;
    padding-left: 0;
    margin: 0;
    background: #f8f9fa;
}

.has-submenu.open .submenu {
    display: block;
}

.has-submenu .submenu li {
    border-left: 3px solid #862633;
    margin-left: 15px;
}

.has-submenu .submenu li a {
    padding: 10px 15px 10px 25px;
    font-size: 0.9rem;
    color: #6c757d;
}

.has-submenu .submenu li a:hover {
    color: #862633;
    background: #fff;
}


.has-submenu .submenu li a i {
    font-size: 0.85rem;
}

/* Count Tags */
.count-tag {
    /* display: inline-block;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    color: white;
    margin-left: auto; */
}

/* 
.count-tag.bg-danger {
    background: #dc3545;
}

.count-tag.bg-warning {
    background: #ffc107;
    color: #000;
} */

.count-tag.bg-info {
    background: #17a2b8;
}

/* Active State */
.dashboard-nav ul li.active>a {
    /* background: #862633; */
    color: #862633;
}

/* Submenu Title */
[data-submenu-title] {
    position: relative;
    padding-top: 20px;
}

[data-submenu-title]::before {
    /* content: attr(data-submenu-title);
    position: absolute;
    top: 0;
    left: 15px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #6c757d;
    letter-spacing: 0.5px; */
}
</style>

<script>
// ==================== SUBMENU TOGGLE SCRIPT ====================
document.addEventListener('DOMContentLoaded', function() {
    const submenuToggles = document.querySelectorAll('.has-submenu .dropdown-toggle');

    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const parent = this.closest('.has-submenu');

            // Close other open submenus
            document.querySelectorAll('.has-submenu.open').forEach(item => {
                if (item !== parent) {
                    item.classList.remove('open');
                }
            });

            // Toggle current submenu
            parent.classList.toggle('open');
        });
    });

    // Keep submenu open if child is active
    const activeSubmenuItem = document.querySelector('.submenu li.active');
    if (activeSubmenuItem) {
        const parentSubmenu = activeSubmenuItem.closest('.has-submenu');
        if (parentSubmenu) {
            parentSubmenu.classList.add('open');
        }
    }
});
</script>