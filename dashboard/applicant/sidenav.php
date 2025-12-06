<div class="collapse" id="MobNav">
    <div class="dashboard-nav">
        <div class="dashboard-inner">
            <ul data-submenu-title="Main Navigation">
                <li class="active"><a href="./"><i class="lni lni-dashboard mr-2"></i>Dashboard</a></li>

                <?php 
                
                if ($profileCompletion < 100){
            ?>
                <li><a href="dashboard-add-profile.php" class="text-warning"><i
                            class="lni lni-add-files mr-2"></i>Complete Profile</a></li>
                <?php } else { ?>
                <li><a href="dashboard-my-profile.php"><i class="lni lni-files mr-2"></i>Manage Profile</a></li>


                <li><a href="dashboard-applied-jobs.php"><i class="lni lni-briefcase mr-2"></i>Applied jobs</a></li>

                <?php
                $alertJobsCount = 0;

                $JOBTITLE = isset($JOBTITLE) ? trim($JOBTITLE) : '';
                $EXJOBTITLE = isset($EXJOBTITLE) ? trim($EXJOBTITLE) : '';
                $SKILLS = isset($SKILLS) ? trim($SKILLS) : '';
                $JOBCATEGORYID = isset($JOBCATEGORYID) ? $JOBCATEGORYID : null;

                // Build WHERE parts and parameters safely for a prepared statement
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

                if (count($conditions) > 0) {
                    $where = implode(' OR ', $conditions);
                    $sql = "SELECT *, COUNT(*) AS cnt FROM tbljob WHERE {$where}";

                    if ($stmt = mysqli_prepare($con, $sql)) {
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

                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        if ($row = mysqli_fetch_assoc($result)) {
                            if ($row['JOBSTATUS'] == 'Active'){
                                $alertJobsCount = (int)$row['cnt'];
                            }
                        }
                        mysqli_stmt_close($stmt);
                    }
                }
                ?>

                <li><a href="dashboard-alert-job.php"><i class="ti-bell mr-2"></i>Alert Jobs
                        <?php if ($alertJobsCount > 0): ?><span
                            class="count-tag bg-info"><?php echo $alertJobsCount; ?></span><?php endif; ?></a></li>

                <?php  } ?>



                <?php
            // Get Saved Job count for applicant
            $SavedJobQuery = "SELECT COUNT(*) as SavedJob_count 
                            FROM tblbookmarkjob 
                            WHERE APPLICANTID = ?";
            $stmtSavedJob = mysqli_prepare($con, $SavedJobQuery);
            mysqli_stmt_bind_param($stmtSavedJob, "i", $session_id);
            mysqli_stmt_execute($stmtSavedJob);
            $SavedJobResult = mysqli_stmt_get_result($stmtSavedJob);
            $SavedJobData = mysqli_fetch_assoc($SavedJobResult);
            $SavedJobCount = $SavedJobData['SavedJob_count'];
            ?>


                <li>
                    <a href="dashboard-saved-jobs.php"><i class="lni lni-bookmark mr-2"></i>Bookmark Jobs
                        <?php if ($SavedJobCount > 0): ?>
                        <span class="count-tag bg-warning"><?php echo $SavedJobCount; ?></span>
                        <?php endif; ?>
                    </a>
                </li>






                <?php
            // Get unread message count for applicant
            $unreadMsgQuery = "SELECT COUNT(*) as unread_count 
                            FROM tblmessages 
                            WHERE RECIPIENT_ID = ? AND IS_READ = 0";
            $stmtUnreadMsg = mysqli_prepare($con, $unreadMsgQuery);
            mysqli_stmt_bind_param($stmtUnreadMsg, "i", $session_id);
            mysqli_stmt_execute($stmtUnreadMsg);
            $unreadMsgResult = mysqli_stmt_get_result($stmtUnreadMsg);
            $unreadMsgData = mysqli_fetch_assoc($unreadMsgResult);
            $unreadMsgCount = $unreadMsgData['unread_count'];
            ?>

                <!-- Add to navigation menu -->
                <li>
                    <a href="dashboard-messages.php">
                        <i class="lni lni-comments mr-2"></i>Messages
                        <?php if ($unreadMsgCount > 0): ?>
                        <span class="count-tag"><?php echo $unreadMsgCount; ?></span>
                        <?php endif; ?>
                    </a>
                </li>



            </ul>






            <ul data-submenu-title="My Account">
                <?php if ($profileCompletion < 100){
            ?>
                <li><a href="dashboard-add-profile.php"><i class="lni lni-add-files mr-2"></i>About Me</a></li>
                <?php } else { ?>
                <li><a href="candidate-detail.php"><i class="lni lni-user mr-2"></i>About Me </a></li>
                <?php  } ?>



                <li><a href="dashboard-change-password.php"><i class="lni lni-lock-alt mr-2"></i>Change Password</a>
                </li>

                <li><a href="logout.php"><i class="lni lni-power-switch mr-2"></i>Log Out</a></li>
            </ul>
        </div>
    </div>
</div>