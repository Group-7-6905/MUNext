<?php
require 'session.php';

$path = "../../";

// ==================== GET SITE SETTINGS ====================
require '../settings-manager.php';
//////////////////////////////////////////////////////////////////



$query = mysqli_query($con, "SELECT * FROM tblusers WHERE USERID = '$session_id'") or die(mysqli_error($con));
$row = mysqli_fetch_array($query);

$USERID   = $row['USERID']  ?? '';
$FNAME    = $row['FNAME']   ?? '';
$ONAME    = $row['ONAME']   ?? '';
$EMAIL    = $row['EMAIL']   ?? '';
$USERNAME = $row['USERNAME'] ?? '';
$PASS     = $row['PASS']    ?? '';
$ROLE     = $row['ROLE']    ?? '';

// $PICLOCATION = $row['PICLOCATION'];


// fetch applicant row safely
$queryuser = "SELECT * from tblapplicants WHERE USERID = '$USERID'";
$resultuser = mysqli_query($con, $queryuser);
$rowuser = mysqli_fetch_assoc($resultuser);

$APPLICANTID     = $rowuser['APPLICANTID']     ?? '';
// prefer applicant FNAME/ONAME if present, otherwise keep user values
$FNAME           = $rowuser['FNAME']           ?? $FNAME;
$ONAME           = $rowuser['OTHERNAMES']     ?? $ONAME;
// $APPLICANTPHOTO  = $rowuser['APPLICANTPHOTO'] ?? '';
$APPLICANTPHOTO = $rowuser['APPLICANTPHOTO'] ?? 'dashboard/applicant/assets/img/avatar-default.svg';
$JOBCATEGORYID   = $rowuser['JOBCATEGORYID']  ?? '';
$JOBTITLE        = $rowuser['JOBTITLE']       ?? '';
$EXCOMPANYNAME   = $rowuser['EXCOMPANYNAME']  ?? '';
$EXJOBTITLE      = $rowuser['EXJOBTITLE']     ?? '';
$ABOUTME         = $rowuser['ABOUTME']        ?? '';
$ADDRESS         = $rowuser['FULLADDRESS']    ?? '';
$COUNTRY         = $rowuser['COUNTRY']        ?? '';
$CITY            = $rowuser['CITY']           ?? '';
$SEX             = $rowuser['SEX']            ?? '';
$BIRTHDATE       = $rowuser['BIRTHDATE']      ?? '';
$CONTACTNO       = $rowuser['CONTACTNO']      ?? '';
$DEGREE          = $rowuser['DEGREE']         ?? '';
$SCHOOLNAME      = $rowuser['SCHOOLNAME']     ?? '';
$SKILLS          = $rowuser['SKILLS']         ?? '';
$FB_link         = $rowuser['FB_link']        ?? '';
$LinkedIn_link   = $rowuser['LinkedIn_link']  ?? '';
$FULLNAME        = trim($FNAME . ' ' . $ONAME);




// fetch job subcategory safely
$queryuser = "SELECT * from tbljobsubcategory WHERE ID = '$JOBCATEGORYID'";
$resultuser = mysqli_query($con, $queryuser);
$rowuser = mysqli_fetch_assoc($resultuser);

$SUBCATEGORY = $rowuser['SUBCATEGORY'] ?? '';





$msg = '';
$msg2 = '';
$msg3 = '';
/////////////////////////Complete Profile/////////////////////////

////////////section 1/////////////////////////
if (isset($_POST['save_data'])) {

   $job_title = $_POST['job_title'];
   $job_categoryid = $_POST['job_categoryid'];
   $dob = $_POST['dob'];
   $phoneno = $_POST['phoneno'];
   $about_me = $_POST['about_me'];
   $sex = $_POST['sex'];
   

   // Get image name
   $image = $_FILES['image']['name'];
   $target = "../../profile/" . basename($image);

   $image_url = "profile/" . $image;

   // Check file size
   if ($_FILES["image"]["size"] > 50000000) {
      $msg = "<div style='color:red'>File is too large!</div>";
   } else {

      $queryuser = "SELECT * from tblusers WHERE USERID = '$session_id'";
      $resultuser = mysqli_query($con, $queryuser);
      $rowuser = mysqli_fetch_array($resultuser);

      $USERID = $rowuser['USERID'];
      $FNAME = $rowuser['FNAME'];
      $ONAME = $rowuser['ONAME'];
      $EMAIL = $rowuser['EMAIL'];
      $USERNAME = $rowuser['USERNAME'];

      $queryuser = "SELECT * from tblapplicants WHERE USERID = '$session_id'";
      $resultuser = mysqli_query($con, $queryuser) or die(mysqli_error($con));
      $applicants_count = mysqli_num_rows($resultuser);
      
      if ($applicants_count < 0) {
        $query = "INSERT INTO tblapplicants (JOBCATEGORYID, JOBTITLE, USERID, FNAME, OTHERNAMES, SEX, BIRTHDATE, ABOUTME, USERNAME, EMAILADDRESS, CONTACTNO, APPLICANTPHOTO) VALUES ('$job_categoryid','$job_title', '$USERID', '$FNAME', '$ONAME', '$sex', '$dob', '$about_me', '$USERNAME','$EMAIL','$phoneno', '$image_url')" or die(mysqli_error($con));
      }else {
        $query = "UPDATE tblapplicants SET JOBCATEGORYID = '$job_categoryid', JOBTITLE = '$job_title', FNAME = '$FNAME',  OTHERNAMES = '$ONAME', BIRTHDATE = '$dob', SEX = '$sex', ABOUTME = '$about_me', EMAILADDRESS = '$EMAIL', CONTACTNO = '$phoneno', APPLICANTPHOTO = '$image_url'  WHERE USERID = '$USERID'";
      }
      
   
      $result = mysqli_query($con, $query);

      if ((move_uploaded_file($_FILES['image']['tmp_name'], $target)) && ($result)) {


         ?><script>
alert('Data Saved!')
</script><?php 

         header('location: ./dashboard-add-profile.php#section23');
      } else {
         $msg = "<div style='color:red'>Error occured...!</div>";
         echo mysqli_error($con);
      }
   }
}
//////////////////Ends Section 1 ///////////////////////////




/////////section 2, 3 ///////////////////

if (isset($_POST['save_info'])) {

   $country = $_POST['country'];
   $city = $_POST['city'];
   $address = $_POST['address'];
   $fb = $_POST['fb'];
   $lin = $_POST['lin'];



   $query = "UPDATE tblapplicants SET FULLADDRESS = '$address', CITY = '$city', COUNTRY = '$country',  FB_link = '$fb', LinkedIn_link = '$lin' WHERE USERID = '$USERID'";

   $result = mysqli_query($con, $query);

   if ($result) {

      ?><script>
alert('Personal Info Saved!')
</script><?php 

      header('location: ./dashboard-add-profile.php#section456');
   } else {
      $msg2 = "<div style='color:red'>Error occured...!</div>";
      echo mysqli_error($con);
   }
   
}
//////////////////////Ends section 2, 3 /////////////////////







/////////section 4, 5, 6 ///////////////////

if (isset($_POST['save_preview'])) {

   $employee_name = '';
   $job_title ='';

   if (!empty($_POST['company_name_select'])) {
      $employee_name = $_POST['company_name_select'];
      if ($employee_name == "Others (Please specify)") {
         $employee_name = $_POST['company_name_specify']; //
      }
   }

   if (!empty($_POST['job_title'])) {
      $job_title = $_POST['job_title'];
   }
   

   


   $schl_name = $_POST['schl_name'];
   $qualification = $_POST['qualification'];

   $skills = $_POST['skills'];

   $query = "UPDATE tblapplicants SET EXCOMPANYNAME = '$employee_name', EXJOBTITLE = '$job_title', DEGREE = '$qualification',  SCHOOLNAME = '$schl_name', SKILLS = '$skills' WHERE USERID = '$USERID'";

   $result = mysqli_query($con, $query);

   if ($result) {

      ?><script>
alert('Profile Completed!')
</script><?php 

      header('location: ./candidate-detail.php');
   } else {
      $msg3 = "<div style='color:red'>Error occured...!</div>";
      echo mysqli_error($con);
   }
   
}
//////////////////////Ends section 4, 5, 6  /////////////////////

/////////////////////////End Complete Profile ends/////////////////////////








/////////////////////////Edit Profile/////////////////////////

////////////section 1/////////////////////////
if (isset($_POST['edit_data'])) {

   $job_title = $_POST['job_title'];
   $job_categoryid = $_POST['job_categoryid'];
   $dob = $_POST['dob'];
   $phoneno = $_POST['phoneno'];
   $about_me = $_POST['about_me'];
   $sex = $_POST['sex'];
   $FName = $_POST['FName'];
   $OName = $_POST['OName'];
   $email = $_POST['email'];
   

   if (!empty($_FILES['image']['name'])) {
       // Get image name
      $image = $_FILES['image']['name'];
      $target = "../profile/" . basename($image);

      $image_url = "profile/" . $image;

      // Check file size
      if ($_FILES["image"]["size"] > 50000000) {
         $msg = "<div style='color:red'>File is too large!</div>";
      } else {

         // $queryuser = "SELECT * from tblusers WHERE USERID = '$session_id'";
         // $resultuser = mysqli_query($con, $queryuser);
         // $rowuser = mysqli_fetch_array($resultuser);

         // $USERID = $rowuser['USERID'];
         // $FNAME = $rowuser['FNAME'];
         // $ONAME = $rowuser['ONAME'];
         // $EMAIL = $rowuser['EMAIL'];
         // $USERNAME = $rowuser['USERNAME'];


         // $query = "INSERT INTO tblapplicants (JOBCATEGORYID, JOBTITLE, USERID, FNAME, OTHERNAMES, SEX, BIRTHDATE, ABOUTME, USERNAME, EMAILADDRESS, CONTACTNO, APPLICANTPHOTO) VALUES ('$job_categoryid','$job_title', '$USERID', '$FNAME', '$ONAME', '$sex', '$dob', '$about_me', '$USERNAME','$EMAIL','$phoneno', '$image_url')" or die(mysqli_error($con));

         $query = "UPDATE tblapplicants SET JOBCATEGORYID = '$job_categoryid', JOBTITLE = '$job_title', FNAME = '$FName',  OTHERNAMES = '$OName', BIRTHDATE = '$dob', SEX = '$sex', ABOUTME = '$about_me', EMAILADDRESS = '$email', CONTACTNO = '$phoneno', APPLICANTPHOTO = '$image_url'  WHERE USERID = '$USERID'";
         
         $result = mysqli_query($con, $query);
         
         // Also update tblusers table for FNAME, ONAME, & EMAIL where USERID = '$USERID'
          $updateUserSql = "UPDATE tblusers SET FNAME = ?, ONAME = ?, EMAIL = ? WHERE USERID = ?";
         if ($stmtUser = mysqli_prepare($con, $updateUserSql)) {
             mysqli_stmt_bind_param($stmtUser, "ssss", $FName, $OName, $email, $USERID);
             if (!mysqli_stmt_execute($stmtUser)) {
                error_log("Failed updating tblusers: " . mysqli_stmt_error($stmtUser));
             }
             mysqli_stmt_close($stmtUser);
          } else {
             error_log("Prepare failed (tblusers): " . mysqli_error($con));
          }
         // Update End  


         
         if ((move_uploaded_file($_FILES['image']['tmp_name'], $target)) && ($result)) {
            
            ?><script>
alert('Data Saved!')
</script><?php 

            header('location: ./dashboard-my-profile.php');
         } else {
            $msg = "<div style='color:red'>Error occured...!</div> ".mysqli_error($con);
            echo mysqli_error($con);
         }
      }
   }else {
      $query = "UPDATE tblapplicants SET JOBCATEGORYID = '$job_categoryid', JOBTITLE = '$job_title', FNAME = '$FName',  OTHERNAMES = '$OName', BIRTHDATE = '$dob', SEX = '$sex', ABOUTME = '$about_me', EMAILADDRESS = '$email', CONTACTNO = '$phoneno' WHERE USERID = '$USERID'";

       $result = mysqli_query($con, $query);

        // Also update tblusers table for FNAME, ONAME, & EMAIL where USERID = '$USERID'
          $updateUserSql = "UPDATE tblusers SET FNAME = ?, ONAME = ?, EMAIL = ? WHERE USERID = ?";
         if ($stmtUser = mysqli_prepare($con, $updateUserSql)) {
             mysqli_stmt_bind_param($stmtUser, "ssss", $FName, $OName, $email, $USERID);
             if (!mysqli_stmt_execute($stmtUser)) {
                error_log("Failed updating tblusers: " . mysqli_stmt_error($stmtUser));
             }
             mysqli_stmt_close($stmtUser);
          } else {
             error_log("Prepare failed (tblusers): " . mysqli_error($con));
          }
         // Update End  

         if (($result)) {


            ?><script>
alert('Data Saved!')
</script><?php 

            header('location: ./dashboard-my-profile.php');
         } else {
            $msg = "<div style='color:red'>Error occured...!</div> ".mysqli_error($con);
            echo mysqli_error($con);
         }
   }
  
}
//////////////////Ends Section 1 ///////////////////////////




/////////section 2, 3 ///////////////////

if (isset($_POST['edit_info'])) {

   $country = $_POST['country'];
   $city = $_POST['city'];
   $address = $_POST['address'];
   $fb = $_POST['fb'];
   $lin = $_POST['lin'];



   $query = "UPDATE tblapplicants SET FULLADDRESS = '$address', CITY = '$city', COUNTRY = '$country',  FB_link = '$fb', LinkedIn_link = '$lin' WHERE USERID = '$USERID'";

   $result = mysqli_query($con, $query);

   if ($result) {

      ?><script>
alert('Personal Info Saved!')
</script><?php 

      header('location: ./dashboard-my-profile.php#section23');
   } else {
      $msg2 = "<div style='color:red'>Error occured...!</div>";
      echo mysqli_error($con);
   }
   
}
//////////////////////Ends section 2, 3 /////////////////////







/////////section 4, 5, 6 ///////////////////

if (isset($_POST['edit_preview'])) {

   $employee_name = '';
   $job_title ='';

   if (!empty($_POST['company_name_select'])) {
      $employee_name = $_POST['company_name_select'];
      if ($employee_name == "Others (Please specify)") {
         $employee_name = $_POST['company_name_specify']; //
      }
   }

   if (!empty($_POST['job_title'])) {
      $job_title = $_POST['job_title'];
   }
   

   


   $schl_name = $_POST['schl_name'];
   $qualification = $_POST['qualification'];

   $skills = $_POST['skills'];

   $query = "UPDATE tblapplicants SET EXCOMPANYNAME = '$employee_name', EXJOBTITLE = '$job_title', DEGREE = '$qualification',  SCHOOLNAME = '$schl_name', SKILLS = '$skills' WHERE USERID = '$USERID'";

   $result = mysqli_query($con, $query);

   if ($result) {

      ?><script>
alert('Profile Completed!')
</script><?php 

      header('location: ./dashboard-my-profile.php#section456');
   } else {
      $msg3 = "<div style='color:red'>Error occured...!</div>";
      echo mysqli_error($con);
   }
   
}
//////////////////////Ends section 4, 5, 6  /////////////////////

/////////////////////////End Complete Profile ends/////////////////////////







// ==================== HANDLE PASSWORD CHANGE ====================
$userId = $session_id;
if (isset($_POST['change_password'])) {
    $oldPassword = trim($_POST['oldpassword'] ?? '');
    $newPassword = trim($_POST['newpassword'] ?? '');
    $confirmPassword = trim($_POST['rnewpassword'] ?? '');
    
    // Validation
    if (empty($oldPassword)) {
        $errors[] = "Current password is required.";
    }
    
    if (empty($newPassword)) {
        $errors[] = "New password is required.";
    } elseif (strlen($newPassword) < 8) {
        $errors[] = "New password must be at least 8 characters long.";
    } elseif (!preg_match('/[A-Z]/', $newPassword)) {
        $errors[] = "New password must contain at least one uppercase letter.";
    } elseif (!preg_match('/[a-z]/', $newPassword)) {
        $errors[] = "New password must contain at least one lowercase letter.";
    } elseif (!preg_match('/[0-9]/', $newPassword)) {
        $errors[] = "New password must contain at least one number.";
    }
    
    if (empty($confirmPassword)) {
        $errors[] = "Please confirm your new password.";
    } elseif ($newPassword !== $confirmPassword) {
        $errors[] = "New password and confirmation do not match.";
    }
    
    if ($newPassword === $oldPassword && !empty($newPassword)) {
        $errors[] = "New password must be different from current password.";
    }
    
    // If no validation errors, proceed with password change
    if (empty($errors)) {
        // Verify current password
        $verifyQuery = "SELECT PASSWORD FROM tblusers WHERE USERID = ?";
        $stmtVerify = mysqli_prepare($con, $verifyQuery);
        mysqli_stmt_bind_param($stmtVerify, "i", $userId);
        mysqli_stmt_execute($stmtVerify);
        $verifyResult = mysqli_stmt_get_result($stmtVerify);
        
        if ($userRow = mysqli_fetch_assoc($verifyResult)) {
            $currentHashedPassword = $userRow['PASSWORD'];
            
            // Check if old password matches
            if (password_verify($oldPassword, $currentHashedPassword) || md5($oldPassword) === $currentHashedPassword) {
                // Hash new password
                $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                
                // Update password
                $updateQuery = "UPDATE tblusers SET PASSWORD = ? WHERE USERID = ?";
                $stmtUpdate = mysqli_prepare($con, $updateQuery);
                mysqli_stmt_bind_param($stmtUpdate, "si", $newHashedPassword, $userId);
                
                if (mysqli_stmt_execute($stmtUpdate)) {
                    $success = true;
                    $_SESSION['success_msg'] = "Password changed successfully!";
                    
                    // Optional: Log the password change
                    $logQuery = "INSERT INTO tbl_activity_log (USER_ID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
                                 VALUES (?, 'password_change', 'User changed their password', ?, NOW())";
                    $stmtLog = mysqli_prepare($con, $logQuery);
                    $ipAddress = $_SERVER['REMOTE_ADDR'];
                    mysqli_stmt_bind_param($stmtLog, "is", $userId, $ipAddress);
                    mysqli_stmt_execute($stmtLog);
                } else {
                    $errors[] = "Failed to update password. Please try again.";
                }
            } else {
                $errors[] = "Current password is incorrect.";
            }
        } else {
            $errors[] = "User not found.";
        }
    }
}
// ==================== END HANDLE PASSWORD CHANGE ====================









/////////////////// Send Message  ///////////////

// Handle send message
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_message'])) {
    $recipientId = (int)$_POST['recipient_id'];
    $message = trim($_POST['message']);
    $senderId = (int)$session_id; // from session.php
    
    if (!empty($message) && $recipientId > 0) {
        $insertMsg = "INSERT INTO tblmessages (SENDER_ID, RECIPIENT_ID, MESSAGE, DATEPOSTED, IS_READ) 
                      VALUES (?, ?, ?, NOW(), 0)";
        $okMsg = false;
      if ($stmtMsg = mysqli_prepare($con, $insertMsg)) {
         mysqli_stmt_bind_param($stmtMsg, "iis", $senderId, $recipientId, $message);
         $okMsg = mysqli_stmt_execute($stmtMsg);
         mysqli_stmt_close($stmtMsg);
      } else {
         $_SESSION['error_msg'] = "Prepare failed (message): " . mysqli_error($con);
      }

      if ($okMsg) {
         // get the ID of the inserted message reliably
         $messageId = mysqli_insert_id($con);

         $queryapplicant = "SELECT * from tblapplicants where USERID = '$senderId'";
         $resultapplicant = mysqli_query($con, $queryapplicant);
         $rowapplicant = mysqli_fetch_array($resultapplicant);
         $FULLNAME = $rowapplicant['FNAME'].' '.$rowapplicant['OTHERNAMES'];


         // prepare notification
         $type = "Message";
         $status = "Unread";
         $note = "New message from $FULLNAME (message id: {$messageId})";

         $insertNot = "INSERT INTO tblnotification (USERID, TYPE, TYPEID, STATUS, DATETIME, NOTE) VALUES (?, ?, ?, ?, NOW(), ?)";
         $okNot = false;
         if ($stmtNot = mysqli_prepare($con, $insertNot)) {
            // params: USERID(int), TYPE(string), TYPEID(int), STATUS(string), NOTE(string)
            mysqli_stmt_bind_param($stmtNot, "isiss", $recipientId, $type, $messageId, $status, $note);
            $okNot = mysqli_stmt_execute($stmtNot);
            mysqli_stmt_close($stmtNot);
         } else {
            $_SESSION['error_msg'] = "Prepare failed (notification): " . mysqli_error($con);
         }

         if ($okNot) {
            mysqli_commit($con);
             $_SESSION['success_msg'] = "Message sent successfully!";
            // echo "<script>alert('Message Sent!')</script>";
         } else {
            mysqli_rollback($con);
            $_SESSION['error_msg'] = "Failed to store notification.";
         }
      } else {
         mysqli_rollback($con);
         // if not already set, record error
         if (!isset($_SESSION['error_msg'])) {
            $_SESSION['error_msg'] = "Failed to store message: " . mysqli_error($con);
         }
      }
   }

   // optionally redirect or continue page flow
   // header('Location: ' . $_SERVER['PHP_SELF']);
   // exit();
}
///////////////////End Send Message///////////////















// Fetch applicant profile with prepared statement (PROFILE COMPLETION)
$profileQuery = "SELECT 
    a.*,
    jsc.SUBCATEGORY,
    jsc.CATEGORYID
FROM tblapplicants a
LEFT JOIN tbljobsubcategory jsc ON a.JOBCATEGORYID = jsc.ID
WHERE a.USERID = ?";

$stmt = mysqli_prepare($con, $profileQuery);
mysqli_stmt_bind_param($stmt, "i", $session_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// if (mysqli_num_rows($result) == 0) {
//     header("Location: index.php");
//     exit();
// }

$profile = mysqli_fetch_assoc($result);

// Extract profile data with fallbacks

// $FULLNAME = $profile['FNAME'].', '.$profile['OTHERNAMES'] ?? 'N/A';

// $EMAIL = $profile['EMAILADDRESS'] ?? '';
// $USERNAME = $profile['USERNAME'] ?? '';
// $CONTACTNO = $profile['CONTACTNO'] ?? '';
// $ADDRESS = $profile['ADDRESS'] ?? '';
// $CITY = $profile['CITY'] ?? '';
// $COUNTRY = $profile['COUNTRY'] ?? '';
// $SEX = $profile['SEX'] ?? '';
// $BIRTHDATE = $profile['BIRTHDATE'] ?? '';
// $APPLICANTPHOTO = $profile['APPLICANTPHOTO'] ?? 'assets/img/avatar-default.svg';
// $ABOUTME = $profile['ABOUTME'] ?? 'No information provided.';
// $JOBTITLE = $profile['JOBTITLE'] ?? '';
// $EXJOBTITLE = $profile['EXJOBTITLE'] ?? '';
// $SKILLS = $profile['SKILLS'] ?? '';
// $DEGREE = $profile['DEGREE'] ?? '';
// $SCHOOLNAME = $profile['SCHOOLNAME'] ?? '';
// $EXCOMPANYNAME = $profile['EXCOMPANYNAME'] ?? '';
// $SUBCATEGORY = $profile['SUBCATEGORY'] ?? '';
// $CATEGORY = $profile['CATEGORY'] ?? '';
// $FB_link = $profile['FB_link'] ?? '';
// $LinkedIn_link = $profile['LinkedIn_link'] ?? '';

// Calculate profile completion
$requiredFields = [
    'FNAME', 'OTHERNAMES', 'EMAILADDRESS', 'CONTACTNO', 'FULLADDRESS', 'CITY', 'COUNTRY', 'SEX', 
    'BIRTHDATE', 'APPLICANTPHOTO', 'ABOUTME', 'JOBTITLE', 'SKILLS', 'DEGREE', 'SCHOOLNAME', 'FB_link', 'LinkedIn_link'
];
$completedFields = 0;
foreach ($requiredFields as $field) {
    if (!empty($profile[$field])) {
        $completedFields++;
    }
}
$profileCompletion = round(($completedFields / count($requiredFields)) * 100);


?>