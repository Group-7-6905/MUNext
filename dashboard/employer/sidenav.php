<div class="collapse" id="MobNav">
    <div class="dashboard-nav">
        <div class="dashboard-inner">
            <ul data-submenu-title="Main Navigation">
                <li class="active"><a href="./"><i class="lni lni-dashboard mr-2"></i>Dashboard</a>
                </li>


                <?php
                if (((empty($COMPANYNAME)) || (empty($COMPANYID)))) {
            ?>
                <li><a href="dashboard-company-add.php" class="text-warning"><i
                            class="lni lni-add-files mr-2"></i>Create Company</a></li>
                <?php } else { ?>


                <!-- <li><a href="dashboard-post-job.php"><i class="lni lni-files mr-2"></i>Post New Job</a></li> -->


                <li><a href="dashboard-post-job.php"><i class="lni lni-files mr-2"></i>Jobs & Postings</a></li>

                <li><a href="dashboard-manage-jobs.php"><i class="lni lni-add-files mr-2"></i>Manage Jobs</a></li>


                <?php
            $countQuery = "SELECT COUNT(*) as appliedjobs
                                FROM tbljobapplication ja
                                INNER JOIN tbljob j ON ja.JOBID = j.JOBID
                                WHERE j.EMPLOYERID = ?";

								// $result = mysqli_query($con, $query);
                                $employerID=$session_id;
								
                                $stmt = mysqli_prepare($con, $countQuery);
                                mysqli_stmt_bind_param($stmt, "i", $employerID);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                $row = mysqli_fetch_assoc($result);  
                                
                                $appliedjobs = $row['appliedjobs'];

            ?>


                <li><a href="dashboard-manage-applications.php"><i class="lni lni-briefcase mr-2"></i>Manage Applicants
                        <?php if ($appliedjobs > 0) { ?><span class="count-tag bg-danger"><?php echo $appliedjobs ?>
                            <?php } ?></span></a>
                </li>


                <?php
            $querybook = "SELECT * from tblbookmarkresume where USERID = '$session_id' ORDER BY ID DESC" or die(mysqli_error($con));
            $resultbook = mysqli_query($con, $querybook);
            $bookmark = mysqli_num_rows($resultbook);
            ?>
                <li><a href="dashboard-shortlisted-resume.php"><i class="lni lni-bookmark mr-2"></i>BookmarkResumes
                        <?php if ($bookmark > 0) { ?><span
                            class="count-tag bg-warning"><?php echo $bookmark ?></span><?php } ?></a></li>


                <?php
                // Get unread message count
                $unreadQuery = "SELECT COUNT(*) as unread_count 
                                FROM tblmessages 
                                WHERE RECIPIENT_ID = ? AND IS_READ = 0";
                $stmtUnread = mysqli_prepare($con, $unreadQuery);
                mysqli_stmt_bind_param($stmtUnread, "i", $session_id);
                mysqli_stmt_execute($stmtUnread);
                $unreadResult = mysqli_stmt_get_result($stmtUnread);
                $unreadData = mysqli_fetch_assoc($unreadResult);
                $unreadCount = $unreadData['unread_count'];
                ?>

                <li>
                    <a href="dashboard-messages.php">
                        <i class="lni lni-comments mr-2"></i>Messages
                        <?php if ($unreadCount > 0): ?>
                        <span class="count-tag"><?php echo $unreadCount ?></span>
                        <?php endif; ?>
                    </a>
                </li>



                <?php }?>









            </ul>

            <ul data-submenu-title="My Accounts">
                <!-- <li><a href="dashboard-companys.php"><i class="fa fa-building mr-2"></i>Company Profile</a></li> -->
                <li><a href="company-detail.php"><i class="fa fa-building mr-2"></i>Company Profile</a></li>

                <?php if (((empty($COMPANYNAME)) || (empty($COMPANYID)))){ echo '<li><a href="dashboard-company-add.php"><i class="lni lni-user mr-2"></i>Account Settings </a></li>'; } else{ echo '<li><a href="dashboard-company-edit.php"><i class="lni lni-user mr-2"></i>Account Settings </a></li>'; }  ?>

                <li><a href="dashboard-change-password.php"><i class="lni lni-lock-alt mr-2"></i>Change Password</a>
                </li>
                <!-- <li><a href="javascript:void(0);"><i class="lni lni-trash-can mr-2"></i>Delete Account</a></li> -->
                <li><a href="logout.php"><i class="lni lni-power-switch mr-2"></i>Log Out</a></li>
            </ul>
        </div>
    </div>
</div>