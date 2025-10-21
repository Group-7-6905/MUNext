<div class="collapse" id="MobNav">
   <div class="dashboard-nav">
      <div class="dashboard-inner">
         <ul data-submenu-title="Main Navigation">
            <li class="active"><a href="./"><i class="lni lni-dashboard mr-2"></i>Dashboard</a>
            </li>
            <li><a href="dashboard-companys.php"><i class="fa fa-building mr-2"></i>Company</a></li>
            <li><a href="dashboard-job-category.php"><i class="lni lni-book mr-2"></i>Job Category</a></li>
            <li><a href="dashboard-post-job.php"><i class="lni lni-files mr-2"></i>Post New Job</a></li>
            <li><a href="dashboard-manage-jobs.php"><i class="lni lni-add-files mr-2"></i>Manage Jobs</a></li>
            <?php
            $query = "SELECT * from tbljobapplication where APPLICATIONSTATUS = 'Pending' ORDER BY ID DESC" or die(mysqli_error($con));
            $run = mysqli_query($con, $query);
            $appliedjobs = mysqli_num_rows($run);
            ?>
            <li><a href="dashboard-manage-applications.php"><i class="lni lni-briefcase mr-2"></i>Manage Applicants <?php if ($appliedjobs > 0) { ?><span class="count-tag bg-danger"><?php echo $appliedjobs ?> <?php } ?></span></a>
            </li>

            <?php
            $querybook = "SELECT * from tblbookmarkresume where USERID = '$session_id' ORDER BY ID DESC" or die(mysqli_error($con));
            $resultbook = mysqli_query($con, $querybook);
            $bookmark = mysqli_num_rows($resultbook);
            ?>
            <li><a href="dashboard-shortlisted-resume.php"><i class="lni lni-bookmark mr-2"></i>BookmarkResumes <?php if ($bookmark > 0) { ?><span class="count-tag bg-warning"><?php echo $bookmark ?></span><?php } ?></a></li>
            <li><a href="dashboard-screen-question-add.php"><i class="lni lni-mastercard mr-2"></i>Screening Questions</a></li>

            <?php
            $queryJOB = "SELECT * from tblfeedback WHERE SENTBY = '$session_id' and STATUS ='Unread'" or die(mysqli_error($con));
            $resultJOB = mysqli_query($con, $queryJOB);
            $msgcount = mysqli_num_rows($resultJOB);
            ?>

            <li><a href="dashboard-messages.php"><i class="lni lni-envelope mr-2"></i>Messages<?php if ($msgcount > 0) { ?><span class="count-tag"><?php echo $msgcount ?></span><?php } ?></a></li>


         </ul>
         <ul data-submenu-title="My Accounts">
            <!-- <li><a href="dashboard-my-profile.php"><i class="lni lni-user mr-2"></i>My Profile </a></li> -->
            <li><a href="dashboard-change-password.php"><i class="lni lni-lock-alt mr-2"></i>Change Password</a></li>
            <!-- <li><a href="javascript:void(0);"><i class="lni lni-trash-can mr-2"></i>Delete Account</a></li> -->
            <li><a href="logout.php"><i class="lni lni-power-switch mr-2"></i>Log Out</a></li>
         </ul>
      </div>
   </div>
</div>