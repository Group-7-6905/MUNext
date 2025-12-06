<!DOCTYPE html>
<html lang="en">

<?php 
require 'include/phpcode.php';
unset($result); // Clear any $result from phpcode.php


// ==================== HANDLE NOTIFICATION DELETION ====================
if (!empty($_GET['type']) && $_GET['type'] == 'delete' && !empty($_GET['noticid'])) {
    $noticid = (int)$_GET['noticid'];
    
    // Delete notification (verify it belongs to this user)
    $deleteQuery = "DELETE FROM tblnotification WHERE ID = ? AND USERID = ?";
    $stmtDelete = mysqli_prepare($con, $deleteQuery);
    mysqli_stmt_bind_param($stmtDelete, "ii", $noticid, $session_id);
    mysqli_stmt_execute($stmtDelete);
    mysqli_stmt_close($stmtDelete);
    
    // Redirect to remove query parameters
    header("Location: dashboard.php");
    exit();
}

// ==================== GET APPLICANT STATISTICS ====================

// 1. Applied Jobs Count
$appliedJobsQuery = "SELECT COUNT(*) as count FROM tbljobapplication WHERE APPLICANTID = ?";
$stmtApplied = mysqli_prepare($con, $appliedJobsQuery);
mysqli_stmt_bind_param($stmtApplied, "i", $session_id);
mysqli_stmt_execute($stmtApplied);
$appliedResult = mysqli_stmt_get_result($stmtApplied);
$appliedJobsCount = mysqli_fetch_assoc($appliedResult)['count'];
mysqli_stmt_close($stmtApplied);

// 2. Pending Applications Count
$pendingQuery = "SELECT COUNT(*) as count FROM tbljobapplication 
                 WHERE APPLICANTID = ? AND APPLICATIONSTATUS = 'Pending'";
$stmtPending = mysqli_prepare($con, $pendingQuery);
mysqli_stmt_bind_param($stmtPending, "i", $session_id);
mysqli_stmt_execute($stmtPending);
$pendingResult = mysqli_stmt_get_result($stmtPending);
$pendingCount = mysqli_fetch_assoc($pendingResult)['count'];
mysqli_stmt_close($stmtPending);

// 3. Shortlisted Applications Count
$shortlistedQuery = "SELECT COUNT(*) as count FROM tbljobapplication 
                     WHERE APPLICANTID = ? AND APPLICATIONSTATUS = 'Shortlisted'";
$stmtShortlisted = mysqli_prepare($con, $shortlistedQuery);
mysqli_stmt_bind_param($stmtShortlisted, "i", $session_id);
mysqli_stmt_execute($stmtShortlisted);
$shortlistedResult = mysqli_stmt_get_result($stmtShortlisted);
$shortlistedCount = mysqli_fetch_assoc($shortlistedResult)['count'];
mysqli_stmt_close($stmtShortlisted);

// 4. Rejected Applications Count
$rejectedQuery = "SELECT COUNT(*) as count FROM tbljobapplication 
                  WHERE APPLICANTID = ? AND APPLICATIONSTATUS = 'Rejected'";
$stmtRejected = mysqli_prepare($con, $rejectedQuery);
mysqli_stmt_bind_param($stmtRejected, "i", $session_id);
mysqli_stmt_execute($stmtRejected);
$rejectedResult = mysqli_stmt_get_result($stmtRejected);
$rejectedCount = mysqli_fetch_assoc($rejectedResult)['count'];
mysqli_stmt_close($stmtRejected);

// 5. Unread Notifications Count
$notificationsQuery = "SELECT COUNT(*) as count FROM tblnotification 
                       WHERE USERID = ? AND STATUS = 'Unread'";
$stmtNotifications = mysqli_prepare($con, $notificationsQuery);
mysqli_stmt_bind_param($stmtNotifications, "i", $session_id);
mysqli_stmt_execute($stmtNotifications);
$notificationsResult = mysqli_stmt_get_result($stmtNotifications);
$unreadNotificationsCount = mysqli_fetch_assoc($notificationsResult)['count'];
mysqli_stmt_close($stmtNotifications);

// 6. Bookmarked Jobs Count
$bookmarksQuery = "SELECT COUNT(*) as count FROM tblbookmarkjob WHERE APPLICANTID = ?";
$stmtBookmarks = mysqli_prepare($con, $bookmarksQuery);
mysqli_stmt_bind_param($stmtBookmarks, "i", $session_id);
mysqli_stmt_execute($stmtBookmarks);
$bookmarksResult = mysqli_stmt_get_result($stmtBookmarks);
$bookmarksCount = mysqli_fetch_assoc($bookmarksResult)['count'];
mysqli_stmt_close($stmtBookmarks);
$alertJobsCount = 0;
// 7. Alert Jobs Count (based on user profile)
// $alertJobsCount = 0;
// if (!empty($JOBTITLE) || !empty($EXJOBTITLE) || !empty($JOBCATEGORYID)) {
//     $alertQuery = "SELECT COUNT(*) as count FROM tbljob 
//                    WHERE JOBSTATUS = 'Active' AND (JOBTITLE LIKE ? OR JOBCATEGORYID = ?)";
//     $stmtAlert = mysqli_prepare($con, $alertQuery);
//     $searchTitle = "%{$JOBTITLE}%";
//     mysqli_stmt_bind_param($stmtAlert, "si", $searchTitle, $JOBCATEGORYID);
//     mysqli_stmt_execute($stmtAlert);
//     $alertResult = mysqli_stmt_get_result($stmtAlert);
//     $alertJobsCount = mysqli_fetch_assoc($alertResult)['count'];
//     mysqli_stmt_close($stmtAlert);
// }

?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Applicant Dashboard | MUNext</title>

    <!-- Custom CSS -->
    <link href="assets/css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/custom-dashboard.css">
    <link rel="stylesheet" href="https://cdn.lineicons.com/3.0/lineicons.css">
    <link rel="stylesheet" href="assets/css/custom-theme.css">
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
                <div class="dashboard-tlbar d-block mb-5">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12">
                            <h1 class="ft-medium">Hello, <?php echo htmlspecialchars($FULLNAME); ?></h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item text-muted"><a href="index.php">Home</a></li>
                                    <li class="breadcrumb-item"><a href="#" class="theme-cl">Dashboard</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="dashboard-widg-bar d-block">

                    <!-- Statistics Grid -->
                    <div class="stats-grid">
                        <!-- Applied Jobs -->
                        <div class="stat-card info">
                            <div class="stat-card-content">
                                <div class="stat-info">
                                    <div class="stat-number"><?php echo $appliedJobsCount; ?></div>
                                    <div class="stat-label">Applied Jobs</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="lni lni-briefcase"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Applications -->
                        <div class="stat-card warning">
                            <div class="stat-card-content">
                                <div class="stat-info">
                                    <div class="stat-number"><?php echo $pendingCount; ?></div>
                                    <div class="stat-label">Pending</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="lni lni-hourglass"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Shortlisted -->
                        <div class="stat-card success">
                            <div class="stat-card-content">
                                <div class="stat-info">
                                    <div class="stat-number"><?php echo $shortlistedCount; ?></div>
                                    <div class="stat-label">Shortlisted</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="lni lni-star-filled"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Rejected -->
                        <div class="stat-card danger">
                            <div class="stat-card-content">
                                <div class="stat-info">
                                    <div class="stat-number"><?php echo $rejectedCount; ?></div>
                                    <div class="stat-label">Rejected</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="lni lni-close"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Notifications -->
                        <div class="stat-card purple">
                            <div class="stat-card-content">
                                <div class="stat-info">
                                    <div class="stat-number"><?php echo $unreadNotificationsCount; ?></div>
                                    <div class="stat-label">Notifications</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="lni lni-alarm"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Saved Jobs -->
                        <div class="stat-card primary">
                            <div class="stat-card-content">
                                <div class="stat-info">
                                    <div class="stat-number"><?php echo $bookmarksCount; ?></div>
                                    <div class="stat-label">Saved Jobs</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="lni lni-heart-filled"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Alert Jobs -->
                        <div class="stat-card warning">
                            <div class="stat-card-content">
                                <div class="stat-info">
                                    <div class="stat-number"><?php echo $alertJobsCount; ?></div>
                                    <div class="stat-label">Job Alerts</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="lni lni-bullhorn"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Completion -->
                        <div class="stat-card success">
                            <div class="stat-card-content">
                                <div class="stat-info">
                                    <div class="stat-number"><?php echo $profileCompletion; ?>%</div>
                                    <div class="stat-label">Profile Complete</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="lni lni-user"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Completion Card -->
                    <?php if ($profileCompletion < 100): ?>
                    <div class="quick-actions" style="margin-bottom: 30px;">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="quick-actions-title mb-3">
                                    <i class="lni lni-user"></i>
                                    Complete Your Profile
                                </h4>
                                <div class="progress-bar-custom mb-3">
                                    <div class="progress-fill" style="width: <?php echo $profileCompletion; ?>%"></div>
                                </div>
                                <p class="text-muted mb-0">A complete profile increases your chances of getting hired by
                                    <strong><?php echo (100 - $profileCompletion); ?>%</strong>!
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="dashboard-add-profile.php" class="complete-profile-btn">
                                    <i class="lni lni-pencil"></i> Complete Profile Now
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Quick Actions -->
                    <div class="quick-actions">
                        <h4 class="quick-actions-title">
                            <i class="lni lni-rocket"></i>
                            Quick Actions
                        </h4>
                        <div class="quick-actions-grid">
                            <a href="<?php echo $path?>browse-jobs.php" class="quick-action-btn">
                                <div class="quick-action-icon">
                                    <i class="lni lni-search"></i>
                                </div>
                                <div class="quick-action-text">
                                    <div class="quick-action-title">Browse Jobs</div>
                                    <div class="quick-action-desc">Find your dream job</div>
                                </div>
                            </a>

                            <a href="dashboard-applied-jobs.php" class="quick-action-btn">
                                <div class="quick-action-icon">
                                    <i class="lni lni-briefcase"></i>
                                </div>
                                <div class="quick-action-text">
                                    <div class="quick-action-title">My Applications</div>
                                    <div class="quick-action-desc">View application status</div>
                                </div>
                            </a>

                            <a href="dashboard-saved-jobs.php" class="quick-action-btn">
                                <div class="quick-action-icon">
                                    <i class="lni lni-heart-filled"></i>
                                </div>
                                <div class="quick-action-text">
                                    <div class="quick-action-title">Saved Jobs</div>
                                    <div class="quick-action-desc">View bookmarked jobs</div>
                                </div>
                            </a>

                            <a href="dashboard-add-profile.php" class="quick-action-btn">
                                <div class="quick-action-icon">
                                    <i class="lni lni-user"></i>
                                </div>
                                <div class="quick-action-text">
                                    <div class="quick-action-title">My Profile</div>
                                    <div class="quick-action-desc">Update your information</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Notifications and Job Alerts Row -->
                    <div class="row">
                        <!-- Notifications Section -->
                        <div class="col-lg-6 col-md-12">
                            <div class="dashboard-gravity-list with-icons">
                                <h4 class="mb-0 ft-medium">Notifications</h4>
                                <ul>
                                    <?php
                                    // Get notifications
                                    $notifQuery = "SELECT ID, NOTE, DATETIME, TYPE, TYPEID 
                                                  FROM tblnotification 
                                                  WHERE USERID = ? AND STATUS = 'Unread' 
                                                  ORDER BY ID DESC LIMIT 5";
                                    $stmtNotif = mysqli_prepare($con, $notifQuery);
                                    mysqli_stmt_bind_param($stmtNotif, "i", $session_id);
                                    mysqli_stmt_execute($stmtNotif);
                                    $notifResult = mysqli_stmt_get_result($stmtNotif);
                                    
                                    $notifCount = 0;
                                    $processed_senders = array();
                                    
                                    while ($notifRow = mysqli_fetch_assoc($notifResult)):
                                        $ID = $notifRow['ID'];
                                        $NOTE = htmlspecialchars($notifRow['NOTE'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                                        $DATETIME = $notifRow['DATETIME'];
                                        $TYPE = $notifRow['TYPE'];
                                        $TYPEID = $notifRow['TYPEID'];
                                        
                                        $link_html = '<span>' . $NOTE . '</span>';
                                        
                                        if ($TYPE === "Job Application") {
                                            $link_html = '<a href="dashboard-applied-jobs.php"><b>Job Application:</b> &raquo; ' . $NOTE . '</a>';
                                            $notifCount++;
                                        } else {
                                            // Get sender info for messages
                                            $msgQuery = "SELECT SENDER_ID FROM tblmessages WHERE ID = ? LIMIT 1";
                                            $stmtMsg = mysqli_prepare($con, $msgQuery);
                                            if ($stmtMsg) {
                                                mysqli_stmt_bind_param($stmtMsg, "i", $TYPEID);
                                                mysqli_stmt_execute($stmtMsg);
                                                $msgResult = mysqli_stmt_get_result($stmtMsg);
                                                
                                                if ($msgRow = mysqli_fetch_assoc($msgResult)) {
                                                    $SENDER_ID = $msgRow['SENDER_ID'];
                                                    
                                                    if (!in_array($SENDER_ID, $processed_senders, true)) {
                                                        // Count unread messages
                                                        $countQuery = "SELECT COUNT(*) as cnt FROM tblmessages 
                                                                      WHERE SENDER_ID = ? AND RECIPIENT_ID = ? AND IS_READ = 0";
                                                        $stmtCount = mysqli_prepare($con, $countQuery);
                                                        if ($stmtCount) {
                                                            mysqli_stmt_bind_param($stmtCount, "ii", $SENDER_ID, $session_id);
                                                            mysqli_stmt_execute($stmtCount);
                                                            $countResult = mysqli_stmt_get_result($stmtCount);
                                                            $countRow = mysqli_fetch_assoc($countResult);
                                                            $msgCount = (int)($countRow['cnt'] ?? 0);
                                                            mysqli_stmt_close($stmtCount);
                                                        } else {
                                                            $msgCount = 1;
                                                        }
                                                        
                                                        // Get sender name
                                                        $userQuery = "SELECT FNAME, ONAME FROM tblusers WHERE USERID = ? LIMIT 1";
                                                        $stmtUser = mysqli_prepare($con, $userQuery);
                                                        if ($stmtUser) {
                                                            mysqli_stmt_bind_param($stmtUser, "i", $SENDER_ID);
                                                            mysqli_stmt_execute($stmtUser);
                                                            $userResult = mysqli_stmt_get_result($stmtUser);
                                                            
                                                            $senderName = 'Someone';
                                                            if ($userRow = mysqli_fetch_assoc($userResult)) {
                                                                $fname = trim($userRow['FNAME'] ?? '');
                                                                $oname = trim($userRow['ONAME'] ?? '');
                                                                $senderName = trim($fname . ' ' . $oname);
                                                                if ($senderName === '') $senderName = 'Someone';
                                                            }
                                                            mysqli_stmt_close($stmtUser);
                                                        }
                                                        
                                                        if ($msgCount > 1) {
                                                            $link_html = '<a href="dashboard-messages.php?empid=' . $SENDER_ID . '">You have ' . $msgCount . ' new messages from ' . htmlspecialchars($senderName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</a>';
                                                        } elseif ($msgCount === 1) {
                                                            $link_html = '<a href="dashboard-messages.php?empid=' . $SENDER_ID . '">You have a new message from ' . htmlspecialchars($senderName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</a>';
                                                        } else {
                                                            $link_html = '<a href="dashboard-messages.php?empid=' . $SENDER_ID . '">' . $NOTE . '</a>';
                                                        }
                                                        
                                                        $processed_senders[] = $SENDER_ID;
                                                        $notifCount++;
                                                    } else {
                                                        continue;
                                                    }
                                                }
                                                mysqli_stmt_close($stmtMsg);
                                            }
                                        }
                                        
                                        echo '<li>
                                                <i class="dash-icon-box ti-bell text-warning bg-light-warning"></i>' 
                                                . $link_html . '
                                                <a href="?type=delete&noticid=' . $ID . '" class="close-list-item" aria-label="Remove notification"><i class="fas fa-times"></i></a>
                                                <p class="text">' . htmlspecialchars(timeago($DATETIME), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</p>
                                            </li>';
                                    endwhile;
                                    mysqli_stmt_close($stmtNotif);
                                    
                                    if ($notifCount == 0): 
                                    ?>
                                    <li class="text-center">
                                        <div
                                            class="p-3 bg-white rounded shadow-sm d-flex align-items-center justify-content-center">
                                            <i class="dash-icon-box ti-bell text-muted" style="font-size:22px;"></i>
                                            <div class="text-left">
                                                <strong class="d-block">No notifications yet</strong>
                                                <small class="text-muted">You're all caught up. New notifications will
                                                    appear here.</small>
                                            </div>
                                        </div>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>

                        <!-- Job Alerts Section -->
                        <div class="col-lg-6 col-md-12">
                            <div class="dashboard-gravity-list invoices with-icons">
                                <h4 class="mb-0 ft-medium">Available Jobs for you</h4>
                                <ul>
                                    <?php
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
                                            return $diff . " " . $strTime[$i] . "(s) ago ";
                                        }
                                    }
                                    
                                    $jobCount = 0;

                                    $JOBTITLE = isset($JOBTITLE) ? trim($JOBTITLE) : '';
                                    $EXJOBTITLE = isset($EXJOBTITLE) ? trim($EXJOBTITLE) : '';
                                    $SKILLS = isset($SKILLS) ? trim($SKILLS) : '';
                                    $JOBCATEGORYID = isset($JOBCATEGORYID) ? $JOBCATEGORYID : null;


                                    $conditions = [];
                                    $types = '';
                                    $params = [];

                                    if ($JOBTITLE !== '') {
                                        $conditions[] = 'JOBTITLE LIKE ?';
                                        $types .= 's';
                                        $params[] = "%{$JOBTITLE}%";
                                    }
                                    if ($EXJOBTITLE !== '') {
                                        $conditions[] = 'JOBTITLE LIKE ?';
                                        $types .= 's';
                                        $params[] = "%{$EXJOBTITLE}%";
                                    }
                                    if ($SKILLS !== '') {
                                        $conditions[] = 'JOBTITLE LIKE ?' ?? 'JOBDESCRIPTION LIKE ?';
                                        $types .= 's';
                                        $params[] = "%{$SKILLS}%";
                                    }
                                    if ($JOBCATEGORYID !== null && $JOBCATEGORYID !== '' && is_numeric($JOBCATEGORYID)) {
                                        $conditions[] = 'JOBCATEGORYID = ?';
                                        $types .= 'i';
                                        $params[] = (int)$JOBCATEGORYID;
                                    }
                                    
                                                        
                                    if (!empty($JOBTITLE) || !empty($EXJOBTITLE) || !empty($JOBCATEGORYID)) {
                                        $where = implode(' OR ', $conditions);

                                        $jobQuery = "SELECT 
                                                    j.*,
                                                    c.COMPANYID, c.COMPANYNAME, c.COMPANYLOGO, c.COMPANYCITY, c.COMPANYCOUNTRY,
                                                    jsc.SUBCATEGORY
                                                    FROM tbljob j
                                                    INNER JOIN tblcompany c ON j.COMPANYID = c.COMPANYID
                                                    LEFT JOIN tbljobsubcategory jsc ON j.JOBCATEGORYID = jsc.ID
                                                    WHERE $where
                                                    ORDER BY j.DATEPOSTED DESC
                                                    LIMIT 5";
                                    
                                        
                                        if ($stmt = mysqli_prepare($con, $jobQuery)) {
                                            // Bind params dynamically when needed
                                            if ($types !== '') {
                                                $bind_params = [];
                                                // first element must be the types string (by reference)
                                                $bind_params[] = & $types;
                                                // then each param (by reference)
                                                for ($i = 0; $i < count($params); $i++) {
                                                    $bind_params[] = & $params[$i];
                                                }
                                                call_user_func_array([$stmt, 'bind_param'], $bind_params);
                                            }
                                        }

                                            mysqli_stmt_execute($stmt);
                                            $jobResult = mysqli_stmt_get_result($stmt);
                                          
                                        
                                        while ($jobRow = mysqli_fetch_assoc($jobResult)):
                                            if ($row['JOBSTATUS'] == 'Active'):
                                            $jobCount++;
                                            $JOBID = $jobRow['JOBID'];
                                            $JOBTITLE_DISPLAY = $jobRow['JOBTITLE'];
                                            $COMPANYNAME = $jobRow['COMPANYNAME'];
                                            $SUBCATEGORY = $jobRow['SUBCATEGORY'];
                                            $DATEPOSTED = $jobRow['DATEPOSTED'];
                                            $DEADLINE = $jobRow['DEADLINE'];

                                            
                                    ?>

                                    <li>
                                        <i class="dash-icon-box ti-briefcase text-success bg-light-warning"></i>
                                        <strong
                                            class="ft-medium text-dark"><?php echo htmlspecialchars($JOBTITLE_DISPLAY); ?></strong>
                                        <ul>
                                            <li class="unpaid"><?php echo htmlspecialchars($COMPANYNAME); ?></li>
                                            <li><?php echo htmlspecialchars($SUBCATEGORY); ?></li>
                                            <li><b>Posted:</b> <?php echo timeago($DATEPOSTED); ?></li>
                                            <li><b>Deadline:</b> <?php echo date('M d, Y', strtotime($DEADLINE)); ?>
                                            </li>
                                        </ul>
                                        <div class="buttons-to-right">
                                            <a href="<?php echo $path?>job-detail.php?jobid=<?php echo $JOBID; ?>"
                                                class="button text-light bg-success">Apply Job</a>
                                        </div>
                                    </li>

                                    <?php 
                                        endif;
                                        endwhile;
                                        mysqli_stmt_close($stmt);
                                    }
                                    
                                    if ($jobCount == 0): 
                                    ?>
                                    <li class="text-center">
                                        <div
                                            class="p-3 bg-white rounded shadow-sm d-flex align-items-center justify-content-center">
                                            <i class="dash-icon-box ti-briefcase text-muted"
                                                style="font-size:18px;"></i>
                                            <div class="text-left ml-3">
                                                <strong class="d-block">No Job Alerts Available</strong>
                                                <small class="text-muted">Please <b><a
                                                            href="dashboard-add-profile.php">complete your
                                                            profile</a></b> to receive personalized job recommendations.
                                                    There are no matching opportunities at this time; new job alerts
                                                    will appear here.</small>
                                            </div>
                                        </div>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- footer -->
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
</body>

</html>