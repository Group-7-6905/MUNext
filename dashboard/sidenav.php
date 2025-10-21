<div class="collapse" id="MobNav">
   <div class="dashboard-nav">
      <div class="dashboard-inner">
         <ul data-submenu-title="Main Navigation">
            <li class="active"><a href="./"><i class="lni lni-dashboard mr-2"></i>Dashboard</a></li>

            <?php if (((empty($APPLICANTPHOTO)) || (empty($DEGREE)) || (empty($CITY)) || (empty($ADDRESS)) || (empty($SKILLS)) || (empty($LinkedIn_link)))) {
            ?>
               <li><a href="dashboard-add-profile.php" class="text-warning"><i class="lni lni-add-files mr-2"></i>Complete Profile</a></li>
            <?php } else { ?>
               <li><a href="dashboard-my-profile.php"><i class="lni lni-files mr-2"></i>Manage Profile</a></li>
            <?php  } ?>


            <!-- <li><a href="dashboard-post-job.php"><i class="lni lni-files mr-2"></i>Post New Job</a></li>
            <li><a href="dashboard-manage-jobs.php"><i class="lni lni-add-files mr-2"></i>Manage Jobs</a></li> -->


            <li><a href="dashboard-applied-jobs.php"><i class="lni lni-briefcase mr-2"></i>Applied jobs</a></li>
            <?php
            $Alert = 0;
            if ((!empty($JOBTITLE)) or (!empty($EXJOBTITLE))) {

               $queryJOB = "SELECT * from tbljob WHERE JOBTITLE LIKE '%$JOBTITLE%' or JOBCATEGORYID = '$JOBCATEGORYID' ORDER BY JOBID DESC" or die(mysqli_error($con));

               if (!empty($EXJOBTITLE)) {
                  $queryJOB = "SELECT * from tbljob WHERE JOBTITLE LIKE '%$JOBTITLE%' or  JOBTITLE LIKE '%$EXJOBTITLE%' or JOBCATEGORYID = '$JOBCATEGORYID' ORDER BY JOBID DESC" or die(mysqli_error($con));
               }
               $resultJOB = mysqli_query($con, $queryJOB);

               while ($rowJOB = mysqli_fetch_array($resultJOB)) {
                  $Alert++;
               }
            }
            ?>
            <li><a href="dashboard-alert-job.php"><i class="ti-bell mr-2"></i>Alert Jobs <?php if ($Alert > 0) { ?><span class="count-tag bg-warning"><?php echo $Alert ?></span> <?php } ?></a></li>

            <li><a href="dashboard-saved-jobs.php"><i class="lni lni-bookmark mr-2"></i>Bookmark Jobs</a></li>
            <!-- <li><a href="dashboard-packages.php"><i class="lni lni-mastercard mr-2"></i>Packages</a></li> -->
            <?php
            $queryJOB = "SELECT * from tblfeedback WHERE SENTBY = '$session_id' and STATUS ='Unread'" or die(mysqli_error($con));
            $resultJOB = mysqli_query($con, $queryJOB);
            $msgcount = mysqli_num_rows($resultJOB);
            ?>
            <li><a href="dashboard-messages.php"><i class="lni lni-envelope mr-2"></i>Messages <?php if ($msgcount > 0) { ?><span class="count-tag"><?php echo $msgcount ?></span><?php } ?></a></li>
         </ul>
         <ul data-submenu-title="My Account">
            <?php if (((empty($APPLICANTPHOTO)) || (empty($DEGREE)) || (empty($CITY)) || (empty($ADDRESS)) || (empty($SKILLS)) || (empty($LinkedIn_link)))) {
            ?>
               <!-- <li><a href="dashboard-add-profile.php"><i class="lni lni-add-files mr-2"></i>About Me</a></li> -->
            <?php } else { ?>
               <li><a href="candidate-detail.php"><i class="lni lni-user mr-2"></i>About Me </a></li>
            <?php  } ?>



            <li><a href="dashboard-change-password.php"><i class="lni lni-lock-alt mr-2"></i>Change Password</a></li>
            <!-- <li><a href="javascript:void(0);"><i class="lni lni-trash-can mr-2"></i>Delete Account</a></li> -->
            <li><a href="logout.php"><i class="lni lni-power-switch mr-2"></i>Log Out</a></li>
         </ul>
      </div>
   </div>
</div>