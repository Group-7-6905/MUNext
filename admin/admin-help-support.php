<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';

// ==================== SESSION VALIDATION ====================
// Check if user is admin


// ==================== FETCH ADMIN INFO ====================
$adminQuery = "SELECT * FROM tblusers WHERE USERID = ?";
$adminStmt = mysqli_prepare($con, $adminQuery);
mysqli_stmt_bind_param($adminStmt, "i", $session_id);
mysqli_stmt_execute($adminStmt);
$adminResult = mysqli_stmt_get_result($adminStmt);
$adminData = mysqli_fetch_assoc($adminResult);
$adminName = $adminData['FNAME'] . ' ' . $adminData['ONAME'];
$adminEmail = $adminData['EMAIL'];

// ==================== GET SYSTEM INFORMATION ====================
// Get database stats
$statsQuery = [
    'users' => "SELECT COUNT(*) as count FROM tblusers",
    'companies' => "SELECT COUNT(*) as count FROM tblcompany",
    'jobs' => "SELECT COUNT(*) as count FROM tbljob",
    'applications' => "SELECT COUNT(*) as count FROM tbljobapplication"
];

$systemStats = [];
foreach ($statsQuery as $key => $query) {
    $result = mysqli_query($con, $query);
    $systemStats[$key] = mysqli_fetch_assoc($result)['count'];
}

// Get PHP and MySQL versions
$phpVersion = phpversion();
$mysqlVersion = mysqli_get_server_info($con);
?>

<head>
    <meta charset="utf-8" />
    <meta name="author" content="MUNext" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Help & Support - Admin Dashboard | MUNext</title>

    <!-- Custom CSS -->
    <link href="assets/css/styles.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/custom-dashboard.css">
</head>

<body>
    <div id="main-wrapper">
        <!-- ==================== HEADER ==================== -->
        <?php include 'header.php' ?>
        <div class="clearfix"></div>

        <!-- ==================== DASHBOARD WRAPPER ==================== -->
        <div class="dashboard-wrap bg-light">
            <!-- Mobile Navigation Toggle -->
            <a class="mobNavigation" data-toggle="collapse" href="#MobNav" role="button" aria-expanded="false"
                aria-controls="MobNav">
                <i class="fas fa-bars mr-2"></i>Dashboard Navigation
            </a>

            <!-- ==================== SIDEBAR ==================== -->
            <?php include 'sidenav.php' ?>

            <!-- ==================== MAIN CONTENT ==================== -->
            <div class="dashboard-content">
                <!-- Page Header -->
                <div class="page-header-section">
                    <h1 class="page-title">
                        <i class="lni lni-help mr-2"></i>Help & Support Center
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Help & Support</li>
                        </ol>
                    </nav>
                </div>

                <div class="container-fluid">
                    <div class="row">
                        <!-- ==================== QUICK ACCESS LINKS ==================== -->
                        <div class="col-12">
                            <div class="support-card">
                                <div class="card-header-custom">
                                    <div class="card-icon">
                                        <i class="lni lni-bolt"></i>
                                    </div>
                                    <h5 class="card-title">Quick Access</h5>
                                </div>

                                <div class="quick-links-grid">
                                    <a href="#documentation" class="quick-link-item"
                                        onclick="showSection('documentation')">
                                        <div class="quick-link-icon">
                                            <i class="lni lni-book"></i>
                                        </div>
                                        <h6 class="quick-link-title">Documentation</h6>
                                        <p class="quick-link-desc">User guides and manuals</p>
                                    </a>

                                    <a href="#faq" class="quick-link-item" onclick="showSection('faq')">
                                        <div class="quick-link-icon">
                                            <i class="lni lni-question-circle"></i>
                                        </div>
                                        <h6 class="quick-link-title">FAQs</h6>
                                        <p class="quick-link-desc">Frequently asked questions</p>
                                    </a>

                                    <a href="#system-info" class="quick-link-item" onclick="showSection('system-info')">
                                        <div class="quick-link-icon">
                                            <i class="lni lni-cog"></i>
                                        </div>
                                        <h6 class="quick-link-title">System Info</h6>
                                        <p class="quick-link-desc">Platform information</p>
                                    </a>

                                    <a href="#contact" class="quick-link-item" onclick="showSection('contact')">
                                        <div class="quick-link-icon">
                                            <i class="lni lni-envelope"></i>
                                        </div>
                                        <h6 class="quick-link-title">Contact Support</h6>
                                        <p class="quick-link-desc">Get in touch with us</p>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- ==================== DOCUMENTATION SECTION ==================== -->
                        <div class="col-lg-6 col-md-12">
                            <div class="support-card" id="documentation">
                                <div class="card-header-custom">
                                    <div class="card-icon">
                                        <i class="lni lni-book"></i>
                                    </div>
                                    <h5 class="card-title">Documentation & Resources</h5>
                                </div>

                                <ul class="resource-list">
                                    <li class="resource-item">
                                        <a href="admin-user-guide.php">
                                            <i class="lni lni-graduation"></i>
                                            <div class="resource-item-content">
                                                <div class="resource-title">Admin User Guide</div>
                                                <p class="resource-desc">Complete guide to using the admin dashboard</p>
                                            </div>
                                        </a>
                                    </li>

                                    <li class="resource-item">
                                        <a href="admin-getting-started.php">
                                            <i class="lni lni-rocket"></i>
                                            <div class="resource-item-content">
                                                <div class="resource-title">Getting Started</div>
                                                <p class="resource-desc">Quick start guide for new administrators</p>
                                            </div>
                                        </a>
                                    </li>

                                    <li class="resource-item">
                                        <a href="#">
                                            <i class="lni lni-video"></i>
                                            <div class="resource-item-content">
                                                <div class="resource-title">Video Tutorials</div>
                                                <p class="resource-desc">Step-by-step video guides</p>
                                            </div>
                                        </a>
                                    </li>


                                    <li class="resource-item">
                                        <a href="admin-best-practices.php">
                                            <i class="lni lni-thumbs-up"></i>
                                            <div class="resource-item-content">
                                                <div class="resource-title">Best Practices</div>
                                                <p class="resource-desc">Tips for optimal platform management</p>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- ==================== FAQ SECTION ==================== -->
                        <div class="col-lg-6 col-md-12">
                            <div class="support-card" id="faq">
                                <div class="card-header-custom">
                                    <div class="card-icon">
                                        <i class="lni lni-question-circle"></i>
                                    </div>
                                    <h5 class="card-title">Frequently Asked Questions</h5>
                                </div>

                                <div class="faq-accordion">
                                    <div class="faq-item">
                                        <div class="faq-question">
                                            <p class="faq-question-text">How do I approve pending job postings?</p>
                                            <i class="lni lni-chevron-down faq-icon"></i>
                                        </div>
                                        <div class="faq-answer">
                                            <p class="faq-answer-text">
                                                Navigate to Job Management → Pending Approval. Review the job details
                                                and click "Approve" or "Reject" based on the content quality and
                                                compliance with platform guidelines.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="faq-item">
                                        <div class="faq-question">
                                            <p class="faq-question-text">How do I verify employer accounts?</p>
                                            <i class="lni lni-chevron-down faq-icon"></i>
                                        </div>
                                        <div class="faq-answer">
                                            <p class="faq-answer-text">
                                                Go to Company Management → Pending Verification. Review company
                                                information, verify business details, and approve legitimate companies.
                                                You can also contact employers for additional verification documents.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="faq-item">
                                        <div class="faq-question">
                                            <p class="faq-question-text">How can I export platform reports?</p>
                                            <i class="lni lni-chevron-down faq-icon"></i>
                                        </div>
                                        <div class="faq-answer">
                                            <p class="faq-answer-text">
                                                Access Reports & Analytics → Export Reports. Select the report type,
                                                date range, and format (PDF or Excel), then click "Export". The report
                                                will be downloaded to your device.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="faq-item">
                                        <div class="faq-question">
                                            <p class="faq-question-text">How do I manage user accounts?</p>
                                            <i class="lni lni-chevron-down faq-icon"></i>
                                        </div>
                                        <div class="faq-answer">
                                            <p class="faq-answer-text">
                                                Navigate to User Management and select either Job Seekers or Employers.
                                                You can view, edit, activate, suspend, or delete user accounts as
                                                needed. Always document reasons for account actions.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="faq-item">
                                        <div class="faq-question">
                                            <p class="faq-question-text">How do I update email templates?</p>
                                            <i class="lni lni-chevron-down faq-icon"></i>
                                        </div>
                                        <div class="faq-answer">
                                            <p class="faq-answer-text">
                                                Go to Settings → Email Templates. Select the template you want to edit,
                                                make your changes using the available placeholders, preview the
                                                template, and save. Test emails are sent to your admin email.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ==================== SYSTEM INFORMATION ==================== -->
                        <div class="col-lg-6 col-md-12">
                            <div class="support-card" id="system-info">
                                <div class="card-header-custom">
                                    <div class="card-icon">
                                        <i class="lni lni-cog"></i>
                                    </div>
                                    <h5 class="card-title">System Information</h5>
                                </div>

                                <table class="system-info-table">
                                    <tbody>
                                        <tr>
                                            <td>Platform Version</td>
                                            <td>MUNext v1.0.0</td>
                                        </tr>
                                        <tr>
                                            <td>PHP Version</td>
                                            <td><?php echo $phpVersion; ?></td>
                                        </tr>
                                        <tr>
                                            <td>MySQL Version</td>
                                            <td><?php echo $mysqlVersion; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Total Users</td>
                                            <td><?php echo number_format($systemStats['users']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Total Companies</td>
                                            <td><?php echo number_format($systemStats['companies']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Total Jobs</td>
                                            <td><?php echo number_format($systemStats['jobs']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Total Applications</td>
                                            <td><?php echo number_format($systemStats['applications']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Server Time</td>
                                            <td><?php echo date('Y-m-d H:i:s'); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- ==================== CONTACT SUPPORT FORM ==================== -->
                        <div class="col-lg-6 col-md-12">
                            <div class="support-card" id="contact">
                                <div class="card-header-custom">
                                    <div class="card-icon">
                                        <i class="lni lni-envelope"></i>
                                    </div>
                                    <h5 class="card-title">Contact Support</h5>
                                </div>

                                <form class="contact-form" method="post">
                                    <div class="form-group">
                                        <label class="form-label">Your Name</label>
                                        <input type="text" name="name" class="form-control-custom"
                                            value="<?php echo htmlspecialchars($adminName); ?>" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Email Address</label>
                                        <input type="email" name="email" class="form-control-custom"
                                            value="<?php echo htmlspecialchars($adminEmail); ?>" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Subject</label>
                                        <input type="text" name="subject" class="form-control-custom"
                                            placeholder="Brief description of your issue" required>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Priority</label>
                                        <select name="priority" class="form-control-custom" required>
                                            <option value="">Select Priority</option>
                                            <option value="low">Low - General Question</option>
                                            <option value="medium">Medium - Feature Request</option>
                                            <option value="high">High - System Issue</option>
                                            <option value="urgent">Urgent - Critical Bug</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Message</label>
                                        <textarea name="message" class="form-control-custom"
                                            placeholder="Please provide detailed information about your inquiry..."
                                            required></textarea>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" name="submit_support" class="btn-primary-custom">
                                            <i class="lni lni-telegram-original mr-2"></i>Submit Request
                                        </button>
                                    </div>

                                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 15px;">
                                        <i class="lni lni-information mr-1"></i>
                                        Response time: Within 24-48 hours for standard inquiries
                                    </p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== FOOTER ==================== -->
                <?php include 'footer.php' ?>
            </div>
        </div>

        <!-- Back to Top -->
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
    // ==================== FAQ ACCORDION FUNCTIONALITY ====================
    document.addEventListener('DOMContentLoaded', function() {
        const faqItems = document.querySelectorAll('.faq-item');

        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');

            question.addEventListener('click', function() {
                // Close other open items
                faqItems.forEach(otherItem => {
                    if (otherItem !== item && otherItem.classList.contains('active')) {
                        otherItem.classList.remove('active');
                    }
                });

                // Toggle current item
                item.classList.toggle('active');
            });
        });
    });

    // ==================== SMOOTH SCROLL TO SECTIONS ====================
    function showSection(sectionId) {
        event.preventDefault();
        const section = document.getElementById(sectionId);
        if (section) {
            section.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }

    // ==================== FORM VALIDATION ====================
    document.querySelector('.contact-form')?.addEventListener('submit', function(e) {
        const subject = this.querySelector('[name="subject"]').value;
        const message = this.querySelector('[name="message"]').value;
        const priority = this.querySelector('[name="priority"]').value;

        if (!subject || !message || !priority) {
            e.preventDefault();
            alert('Please fill in all required fields.');
            return false;
        }

        if (message.length < 20) {
            e.preventDefault();
            alert('Please provide more details in your message (minimum 20 characters).');
            return false;
        }
    });
    </script>
</body>

</html>