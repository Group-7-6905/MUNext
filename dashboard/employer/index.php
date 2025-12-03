<!DOCTYPE html>
<html lang="zxx">


<?php require 'include/phpcode.php';

if (!empty($_GET['type'])) {
	$type = $_GET['type'];

	// $jobid = $_GET['jobid'];

	if (($type == "delete")) {
		$noticid = $_GET['noticid'];
		$delete_sql = "DELETE from tblnotification where ID='$noticid'";
		mysqli_query($con, $delete_sql);

		// $delete_sql = "DELETE from tblscreening_qa where ID='$bookmarkedid'";
		// mysqli_query($con, $delete_sql); 
?>

<script>
// alert("Bookmarked Job Removed");
// setTimeout(function() {
// 	window.location.href = 'job-detail.php?jobid=<?php echo $jobid ?>';
// }, 3000);
</script>
<?php
	}
} 

// ==================== GET EMPLOYER STATISTICS ====================

// 1. Active Jobs Count
$activeJobsQuery = "SELECT COUNT(*) as count FROM tbljob WHERE EMPLOYERID = ? AND JOBSTATUS = 'Active'";
$stmtActive = mysqli_prepare($con, $activeJobsQuery);
mysqli_stmt_bind_param($stmtActive, "i", $session_id);
mysqli_stmt_execute($stmtActive);
$activeJobsResult = mysqli_stmt_get_result($stmtActive);
$activeJobsCount = mysqli_fetch_assoc($activeJobsResult)['count'];
mysqli_stmt_close($stmtActive);

// 2. Pending Jobs Count
$pendingJobsQuery = "SELECT COUNT(*) as count FROM tbljob WHERE EMPLOYERID = ? AND JOBSTATUS = 'Pending'";
$stmtPending = mysqli_prepare($con, $pendingJobsQuery);
mysqli_stmt_bind_param($stmtPending, "i", $session_id);
mysqli_stmt_execute($stmtPending);
$pendingJobsResult = mysqli_stmt_get_result($stmtPending);
$pendingJobsCount = mysqli_fetch_assoc($pendingJobsResult)['count'];
mysqli_stmt_close($stmtPending);

// 3. Total Applications Count
$applicationsQuery = "SELECT COUNT(*) as count FROM tbljobapplication ja 
                      INNER JOIN tbljob j ON ja.JOBID = j.JOBID 
                      WHERE j.EMPLOYERID = ?";
$stmtApplications = mysqli_prepare($con, $applicationsQuery);
mysqli_stmt_bind_param($stmtApplications, "i", $session_id);
mysqli_stmt_execute($stmtApplications);
$applicationsResult = mysqli_stmt_get_result($stmtApplications);
$totalApplicationsCount = mysqli_fetch_assoc($applicationsResult)['count'];
mysqli_stmt_close($stmtApplications);

// 4. New Applications (Pending) Count
$newApplicationsQuery = "SELECT COUNT(*) as count FROM tbljobapplication ja 
                         INNER JOIN tbljob j ON ja.JOBID = j.JOBID 
                         WHERE j.EMPLOYERID = ? AND ja.APPLICATIONSTATUS = 'Pending'";
$stmtNewApps = mysqli_prepare($con, $newApplicationsQuery);
mysqli_stmt_bind_param($stmtNewApps, "i", $session_id);
mysqli_stmt_execute($stmtNewApps);
$newApplicationsResult = mysqli_stmt_get_result($stmtNewApps);
$newApplicationsCount = mysqli_fetch_assoc($newApplicationsResult)['count'];
mysqli_stmt_close($stmtNewApps);

// 5. Unread Notifications Count
$notificationsQuery = "SELECT COUNT(*) as count FROM tblnotification 
                       WHERE USERID = ? AND STATUS = 'Unread'";
$stmtNotifications = mysqli_prepare($con, $notificationsQuery);
mysqli_stmt_bind_param($stmtNotifications, "i", $session_id);
mysqli_stmt_execute($stmtNotifications);
$notificationsResult = mysqli_stmt_get_result($stmtNotifications);
$unreadNotificationsCount = mysqli_fetch_assoc($notificationsResult)['count'];
mysqli_stmt_close($stmtNotifications);

// 6. Bookmarked Resumes Count
$bookmarksQuery = "SELECT COUNT(*) as count FROM tblbookmarkresume WHERE USERID = ?";
$stmtBookmarks = mysqli_prepare($con, $bookmarksQuery);
mysqli_stmt_bind_param($stmtBookmarks, "i", $session_id);
mysqli_stmt_execute($stmtBookmarks);
$bookmarksResult = mysqli_stmt_get_result($stmtBookmarks);
$bookmarksCount = mysqli_fetch_assoc($bookmarksResult)['count'];
mysqli_stmt_close($stmtBookmarks);

// 7. Shortlisted Candidates Count
$shortlistedQuery = "SELECT COUNT(*) as count FROM tbljobapplication ja 
                     INNER JOIN tbljob j ON ja.JOBID = j.JOBID 
                     WHERE j.EMPLOYERID = ? AND ja.APPLICATIONSTATUS = 'Shortlisted'";
$stmtShortlisted = mysqli_prepare($con, $shortlistedQuery);
mysqli_stmt_bind_param($stmtShortlisted, "i", $session_id);
mysqli_stmt_execute($stmtShortlisted);
$shortlistedResult = mysqli_stmt_get_result($stmtShortlisted);
$shortlistedCount = mysqli_fetch_assoc($shortlistedResult)['count'];
mysqli_stmt_close($stmtShortlisted);

// 8. Expired Jobs Count
$expiredJobsQuery = "SELECT COUNT(*) as count FROM tbljob 
                     WHERE EMPLOYERID = ? AND DEADLINE < CURDATE()";
$stmtExpired = mysqli_prepare($con, $expiredJobsQuery);
mysqli_stmt_bind_param($stmtExpired, "i", $session_id);
mysqli_stmt_execute($stmtExpired);
$expiredJobsResult = mysqli_stmt_get_result($stmtExpired);
$expiredJobsCount = mysqli_fetch_assoc($expiredJobsResult)['count'];
mysqli_stmt_close($stmtExpired);



?>

<head>
    <meta charset="utf-8" />
    <meta name="author" content="MUNext" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>MUNext - Creative Job Board</title>

    <!-- Custom CSS -->
    <link href="assets/css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/custom-dashboard.css">


    <link rel="stylesheet" href="assets/css/plugins/theme-stats.css">

</head>

<body>
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper">

        <!-- ============================================================== -->
        <!-- Top header  -->
        <!-- ============================================================== -->
        <!-- Start Navigation -->
        <?php include 'header.php' ?>
        <!-- End Navigation -->
        <div class="clearfix"></div>
        <!-- ============================================================== -->
        <!-- Top header  -->
        <!-- ============================================================== -->

        <!-- ======================= dashboard Detail ======================== -->

        <div class="dashboard-wrap bg-light">
            <a class="mobNavigation" data-toggle="collapse" href="#MobNav" role="button" aria-expanded="false"
                aria-controls="MobNav">
                <i class="fas fa-bars mr-2"></i>Dashboard Navigation
            </a>

            <?php include 'sidenav.php' ?>

            <div class="dashboard-content">
                <div class="dashboard-tlbar d-block mb-5">
                    <div class="row">
                        <div class="colxl-12 col-lg-12 col-md-12">
                            <h1 class="ft-medium">Hello, <?php echo $FULLNAME ?></h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item text-muted"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item"><a href="#" class="theme-cl">Dashboard</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="dashboard-widg-bar d-block">

                    <div class="stats-grid">
                        <!-- Active Jobs -->
                        <div class="stat-card success">
                            <div class="stat-card-content">
                                <div class="stat-info">
                                    <div class="stat-number"><?php echo $activeJobsCount; ?></div>
                                    <div class="stat-label">Active Jobs</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="lni lni-checkmark-circle"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Jobs -->
                        <div class="stat-card warning">
                            <div class="stat-card-content">
                                <div class="stat-info">
                                    <div class="stat-number"><?php echo $pendingJobsCount; ?></div>
                                    <div class="stat-label">Pending Jobs</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="lni lni-hourglass"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Total Applications -->
                        <div class="stat-card purple">
                            <div class="stat-card-content">
                                <div class="stat-info">
                                    <div class="stat-number"><?php echo $totalApplicationsCount; ?></div>
                                    <div class="stat-label">Total Applications</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="lni lni-users"></i>
                                </div>
                            </div>
                        </div>

                        <!-- New Applications -->
                        <div class="stat-card primary">
                            <div class="stat-card-content">
                                <div class="stat-info">
                                    <div class="stat-number"><?php echo $newApplicationsCount; ?></div>
                                    <div class="stat-label">New Applications</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="lni lni-inbox"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Notifications -->
                        <div class="stat-card danger">
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

                        <!-- Shortlisted -->
                        <div class="stat-card info">
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

                        <!-- Saved Resumes -->
                        <div class="stat-card danger">
                            <div class="stat-card-content">
                                <div class="stat-info">
                                    <div class="stat-number"><?php echo $bookmarksCount; ?></div>
                                    <div class="stat-label">Saved Resumes</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="lni lni-heart-filled"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Expired Jobs -->
                        <div class="stat-card warning">
                            <div class="stat-card-content">
                                <div class="stat-info">
                                    <div class="stat-number"><?php echo $expiredJobsCount; ?></div>
                                    <div class="stat-label">Expired Jobs</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="lni lni-close"></i>
                                </div>
                            </div>
                        </div>
                    </div>








                    <!-- Quick Actions -->
                    <div class="quick-actions">
                        <h4 class="quick-actions-title">
                            <i class="lni lni-rocket"></i>
                            Quick Actions
                        </h4>
                        <div class="quick-actions-grid">
                            <a href="dashboard-post-job.php" class="quick-action-btn">
                                <div class="quick-action-icon">
                                    <i class="lni lni-circle-plus"></i>
                                </div>
                                <div class="quick-action-text">
                                    <div class="quick-action-title">Post New Job</div>
                                    <div class="quick-action-desc">Create a new job listing</div>
                                </div>
                            </a>

                            <a href="dashboard-manage-jobs.php" class="quick-action-btn">
                                <div class="quick-action-icon">
                                    <i class="lni lni-briefcase"></i>
                                </div>
                                <div class="quick-action-text">
                                    <div class="quick-action-title">Manage Jobs</div>
                                    <div class="quick-action-desc">View and edit your jobs</div>
                                </div>
                            </a>

                            <a href="dashboard-manage-applications.php" class="quick-action-btn">
                                <div class="quick-action-icon">
                                    <i class="lni lni-users"></i>
                                </div>
                                <div class="quick-action-text">
                                    <div class="quick-action-title">View Applications</div>
                                    <div class="quick-action-desc">Review job applications</div>
                                </div>
                            </a>

                            <a href="company-detail.php" class="quick-action-btn">
                                <div class="quick-action-icon">
                                    <i class="lni lni-apartment"></i>
                                </div>
                                <div class="quick-action-text">
                                    <div class="quick-action-title">Company Profile</div>
                                    <div class="quick-action-desc">Update company info</div>
                                </div>
                            </a>
                        </div>
                    </div>






                    <div class="row">
                        <div class="col-lg-6 col-md-12">
                            <div class="dashboard-gravity-list with-icons">
                                <h4 class="mb-0 ft-medium">Notifications</h4>
                                <ul>

                                    <?php
                                    // Use prepared statements and escape output to follow best practices
                                    $stmt = mysqli_prepare($con, "SELECT ID, USERID, NOTE, DATETIME, TYPE, TYPEID FROM tblnotification WHERE USERID = ? AND STATUS = 'Unread'  ORDER BY ID DESC LIMIT 5");
                                    if ($stmt) {
                                        mysqli_stmt_bind_param($stmt, "i", $session_id);
                                        mysqli_stmt_execute($stmt);
                                        $result = mysqli_stmt_get_result($stmt);

                                        $count = 0;
                                        $processed_senders = []; // track senders already shown to avoid duplicates

                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $ID = (int) $row['ID'];
                                            $USERID = (int) $row['USERID'];
                                            $NOTE = htmlspecialchars($row['NOTE'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                                            $DATETIME = $row['DATETIME'];
                                            $TYPE = $row['TYPE'];
                                            $TYPEID = (int) $row['TYPEID'];

                                            // Default link / message
                                            $link_html = '<span>' . $NOTE . '</span>';

                                            if ($TYPE === "Job Application") {
                                                $link_html = '<a href="dashboard-manage-applications.php">You have a job application: &raquo; ' . $NOTE . '</a>';
                                                $count++;
                                            } else {
                                                // Assume this notification references a message; get sender
                                                $stmtMsg = mysqli_prepare($con, "SELECT SENDER_ID FROM tblmessages WHERE ID = ? LIMIT 1");
                                                if ($stmtMsg) {
                                                    mysqli_stmt_bind_param($stmtMsg, "i", $TYPEID);
                                                    mysqli_stmt_execute($stmtMsg);
                                                    $resMsg = mysqli_stmt_get_result($stmtMsg);
                                                    if ($rowMsg = mysqli_fetch_assoc($resMsg)) {
                                                        $SENDER_ID = (int) $rowMsg['SENDER_ID'];

                                                        // If we've already shown a notification for this sender, skip this notification
                                                        if (in_array($SENDER_ID, $processed_senders, true)) {
                                                            mysqli_stmt_close($stmtMsg);
                                                            continue; // skip duplicate notification for same sender
                                                        }

                                                        // Count messages from this sender to the current user
                                                        $stmtCount = mysqli_prepare($con, "SELECT COUNT(*) AS cnt FROM tblmessages WHERE SENDER_ID = ? AND RECIPIENT_ID = ? AND IS_READ = 0");
                                                        if ($stmtCount) {
                                                            mysqli_stmt_bind_param($stmtCount, "ii", $SENDER_ID, $session_id);
                                                            mysqli_stmt_execute($stmtCount);
                                                            $resCount = mysqli_stmt_get_result($stmtCount);
                                                            $rowCount = mysqli_fetch_assoc($resCount);
                                                            $msgCount = (int) ($rowCount['cnt'] ?? 0);
                                                            mysqli_stmt_close($stmtCount);
                                                        } else {
                                                            $msgCount = 1; // fallback
                                                        }

                                                        // Get sender name (if available)
                                                        $senderName = 'Someone';
                                                        $stmtUser = mysqli_prepare($con, "SELECT FNAME, ONAME FROM tblusers WHERE USERID = ? LIMIT 1");
                                                        if ($stmtUser) {
                                                            mysqli_stmt_bind_param($stmtUser, "i", $SENDER_ID);
                                                            mysqli_stmt_execute($stmtUser);
                                                            $resUser = mysqli_stmt_get_result($stmtUser);
                                                            if ($ru = mysqli_fetch_assoc($resUser)) {
                                                                $fname = trim($ru['FNAME'] ?? '');
                                                                $oname = trim($ru['ONAME'] ?? '');
                                                                $senderName = trim($fname . ' ' . $oname);
                                                                if ($senderName === '') $senderName = 'Someone';
                                                            }
                                                            mysqli_stmt_close($stmtUser);
                                                        }

                                                        // Build link showing number of messages from this sender
                                                        if ($msgCount > 1) {
                                                            $link_html = '<a href="dashboard-messages.php?userid=' . $SENDER_ID . '">You have ' . $msgCount . ' new messages from ' . htmlspecialchars($senderName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</a>';
                                                        } elseif ($msgCount === 1) {
                                                            $link_html = '<a href="dashboard-messages.php?userid=' . $SENDER_ID . '">You have a new message from ' . htmlspecialchars($senderName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</a>';
                                                        } else {
                                                            // fallback to notification note if no messages found
                                                            $link_html = '<a href="dashboard-messages.php?userid=' . $SENDER_ID . '">' . $NOTE . '</a>';
                                                        }

                                                        // mark this sender as processed so duplicates are avoided
                                                        $processed_senders[] = $SENDER_ID;
                                                        $count++;
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
                                        } // end while

                                        mysqli_stmt_close($stmt);
                                    } else {
                                        // Fallback if prepare fails (rare)
                                        $count = 0;
                                    }

                                    if (empty($count)) { ?>
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
                                    <?php } ?>

                                </ul>
                            </div>
                        </div>






                        <div class="col-lg-6 col-md-12">
                            <div class="dashboard-gravity-list invoices with-icons">
                                <h4 class="mb-0 ft-medium">New Application</h4>
                                <ul>
                                    <?php

									function timeago($date)
									{
										$timestamp = strtotime($date);

										$strTime = array("second", "minute", "hour", "day", "month", "year");
										$length = array("60", "60", "24", "30", "12", "10");

										$currentTime = time();
										if ($currentTime >= $timestamp) {
											$diff     = time() - $timestamp;
											for ($i = 0; $diff >= $length[$i] && $i < count($length) - 1; $i++) {
												$diff = $diff / $length[$i];
											}

											$diff = round($diff);
											return $diff . " " . $strTime[$i] . "(s) ago ";
										}
									}

									// $strTimeAgo = timeago($row["trans_date"]);

									?>

                                    <?php
                                    $queryuser = "SELECT 
                                        ja.ID,
                                        ja.APPLICANTID,
                                        ja.JOBID,
                                        ja.APPLICATIONSTATUS,
                                        ja.DATEAPPLIED,
                                        ja.SCREENINGSCORE,
                                        u.USERID,
                                        u.FNAME,
                                        u.ONAME,
                                        u.EMAIL,
                                        u.USERNAME,
                                        ap.CITY,
                                        ap.COUNTRY,
                                        ap.SEX,
                                        j.COMPANYID,
                                        j.JOBTITLE
                                    FROM tbljobapplication ja
                                    INNER JOIN tbljob j ON ja.JOBID = j.JOBID
                                    INNER JOIN tblapplicants ap ON ja.APPLICANTID = ap.USERID
                                    INNER JOIN tblusers u ON ja.APPLICANTID = u.USERID
                                    WHERE j.EMPLOYERID = '$session_id' AND ja.APPLICATIONSTATUS = 'Pending'
                                    ORDER BY ja.DATEAPPLIED DESC
                                    LIMIT 5" or die(mysqli_error($con));
                                    
                                    $resultuser = mysqli_query($con, $queryuser);
                                    $count = 0;
                                    while ($rowuser = mysqli_fetch_array($resultuser)) {
                                        $count++;

                                        $USERID = $rowuser['USERID'] ?? '';
                                        $USERNAME = $rowuser['USERNAME'] ?? '';
                                        $APPLICANTFNAME = $rowuser['FNAME'] ?? '';
                                        $APPLICANTONAME = $rowuser['ONAME'] ?? '';

                                        $CITY = $rowuser['CITY'] ?? '';
                                        $COUNTRY = $rowuser['COUNTRY'] ?? '';
                                        $SEX = $rowuser['SEX'] ?? '';
                                        $JOBID = $rowuser['JOBID'] ?? '';
                                        $DEADLINE = $rowuser['DEADLINE'] ?? '';
                                        $SCORE = $rowuser['SCREENINGSCORE'] ?? '';

                                        $JOBTITLE = $rowuser['JOBTITLE'] ?? '';
                                        $APPLICATIONID = $rowuser['ID'] ?? '';

                                        
                                        if ((!empty($FNAME)) and (!empty($OTHERNAMES))) {
                                            $APPLICANTFNAME = $rowuser['FNAME']  ?? '';
                                            $APPLICANTONAME = $rowuser['OTHERNAMES']  ?? '';
                                        }

                                    ?>

                                    <li><i class="dash-icon-box ti-briefcase dash-icon-box text-success bg-light-warning"
                                            style="font-size:20px;"></i>
                                        <strong class=" ft-medium text-dark">
                                            <?php echo $JOBTITLE ?></strong>
                                        <ul class="p-2">
                                            <li><i class="fas fa-user"></i>
                                                <b><?php echo $APPLICANTFNAME.' '. $APPLICANTONAME; ?></b>
                                            </li>
                                            <li class="unpaid"><?php echo $CITY ?>, <?php echo $COUNTRY ?></li>
                                            <li><?php echo $SEX ?></li>
                                            <li><b>Applied:</b>
                                                <?php echo timeago($rowuser['DATEAPPLIED']) ?></li>
                                        </ul>
                                        <div class="buttons-to-right">
                                            <?php if (!empty($APPLICATIONID)) { ?>
                                            <a href="dashboard-manage-applications.php?applicationid=<?php echo $APPLICATIONID ?>"
                                                class="button text-light bg-success">View</a>
                                            <?php } ?>
                                        </div>
                                    </li>

                                    <?php } if (empty($count)) { ?>
                                    <li class="text-center">
                                        <div
                                            class="p-3 bg-white rounded shadow-sm d-flex align-items-center justify-content-center">
                                            <i class="dash-icon-box ti-briefcase text-muted"
                                                style="font-size:20px;"></i>
                                            <div class="text-left ml-3">
                                                <strong class="d-block">No new applications</strong>
                                                <small class="text-muted">You're all caught up. New job applications
                                                    will appear here.</small>
                                            </div>
                                        </div>
                                    </li>
                                    <?php } ?>

                                </ul>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- footer -->
                <?php include 'footer.php' ?>

            </div>

        </div>
        <!-- ======================= dashboard Detail End ======================== -->

        <a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>


    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->

    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/slick.js"></script>
    <script src="assets/js/slider-bg.js"></script>
    <script src="assets/js/smoothproducts.js"></script>
    <script src="assets/js/snackbar.min.js"></script>
    <script src="assets/js/jQuery.style.switcher.js"></script>
    <script src="assets/js/custom.js"></script>
    <!-- ============================================================== -->
    <!-- This page plugins -->
    <!-- ============================================================== -->

</body>

<!-- Mirrored from themezhub.net/live-workplex/workplex/employer-dashboard.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Feb 2022 12:07:14 GMT -->

</html>