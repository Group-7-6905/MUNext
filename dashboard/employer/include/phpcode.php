<?php
require 'session.php';
$msg = '';
$msg2='';

$path = "../../";

// ==================== GET SITE SETTINGS ====================
require '../settings-manager.php';
//////////////////////////////////////////////////////////////////










//////////////////////////Admin Detail///////////////////////////////////
// fetch user row safely
$query = mysqli_query($con, "SELECT * FROM tblusers WHERE USERID = '$session_id'") or die(mysqli_error($con));
$row = mysqli_fetch_assoc($query) ?: []; // returns empty array when no row

$USERID   = $row['USERID']  ?? '';
$FNAME    = $row['FNAME']   ?? '';
$ONAME    = $row['ONAME']   ?? '';
$EMAIL    = $row['EMAIL']   ?? '';
$USERNAME = $row['USERNAME'] ?? '';
$PASS     = $row['PASS']    ?? '';
$ROLE     = $row['ROLE']    ?? '';
// $PICLOCATION = $row['PICLOCATION'] ?? '';
$FULLNAME = trim(($FNAME ?? '') . ' ' . ($ONAME ?? ''));

/////////////////////////Admin Details ends/////////////////////////////////////

///////////////////////Company Details///////////////////////
// fetch company row safely
$querycompany = mysqli_query($con, "SELECT * FROM tblcompany WHERE USERID = '$USERID'") or die(mysqli_error($con));
$rowcompany = mysqli_fetch_assoc($querycompany) ?: []; // returns empty array when no row
$COMPANYID         = $rowcompany['COMPANYID']         ?? '';
$COMPANYNAME       = $rowcompany['COMPANYNAME']       ?? '';
$COMPANYADDRESS    = $rowcompany['COMPANYADDRESS']    ?? '';
$COMPANYCONTACTNO  = $rowcompany['COMPANYCONTACTNO']  ?? '';
$COMPANYEMAIL      = $rowcompany['COMPANYEMAIL']      ?? '';
///////////////////////Company Details Ends///////////////////////






/////////////////Add Company/////////////////////////////

if (isset($_POST['add_company'])) {

   $name = mysqli_real_escape_string($con, trim($_POST['name']));
   $email = mysqli_real_escape_string($con, trim($_POST['email']));
   $contact = mysqli_real_escape_string($con, trim($_POST['contact']))?? '';
   $industry = mysqli_real_escape_string($con, trim($_POST['industry']));
   $specialism = mysqli_real_escape_string($con, trim($_POST['specialism']));
   $country = mysqli_real_escape_string($con, trim($_POST['country']));
   $city = mysqli_real_escape_string($con, trim($_POST['city']));
   $address = mysqli_real_escape_string($con, trim($_POST['address']));
   $about = mysqli_real_escape_string($con, trim($_POST['about']));
   $award = mysqli_real_escape_string($con, trim($_POST['award']))?? '';
   $award_year = mysqli_real_escape_string($con, trim($_POST['award_year']))?? '';
   $award_disc = mysqli_real_escape_string($con, trim($_POST['award_disc']))?? '';
   $websiteURL = mysqli_real_escape_string($con, trim($_POST['websiteURL'])) ?? '';


   $image = $_FILES['image']['name'];
   $target = $path . "company_logo/" . basename($image);
   $image_url = "company_logo/" . $image;

   if ($_FILES["image"]["size"] > 500000) {
      $msg = "<div style='color:red'>File is too large!</div>";
   } else {

      $query = "INSERT INTO tblcompany 
                (USERID, COMPANYNAME, COMPANYADDRESS, COMPANYWEBSITE, COMPANYCONTACTNO, COMPANYSTATUS, 
                 COMPANYABOUT, COMPANYEMAIL, COMPANYINDUSTRY, COMPANYSPECIALISM, 
                 COMPANYCOUNTRY, COMPANYCITY, COMPANYAWARD, COMPANYYEAR, 
                 COMPANYAWARDDESC, COMPANYLOGO) 
                VALUES 
                ('$session_id', '$name', '$address', '$websiteURL', '$contact', 'Active', '$about', 
                 '$email', '$industry', '$specialism', '$country', '$city', '$award', 
                 '$award_year', '$award_disc', '$image_url')";

      $result = mysqli_query($con, $query);

      if ($result && move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
         echo "<script>alert('Company Added!')</script>";
         header('location: ./company-detail.php?companyid=' . mysqli_insert_id($con));
         exit();
      } else {
         $msg = "<div style='color:red'>Error occurred...!</div>";
         echo mysqli_error($con);
      }
   }
}
/////////////////////////Add company ends/////////////////////////





/////////////////Edit Company/////////////////////////////
// $msg = '';

if (isset($_POST['edit_company'])) {

   $name = mysqli_real_escape_string($con, trim($_POST['name']));
   $email = mysqli_real_escape_string($con, trim($_POST['email']));
   $contact = mysqli_real_escape_string($con, trim($_POST['contact']))?? '';
   $industry = mysqli_real_escape_string($con, trim($_POST['industry']));
   $specialism = mysqli_real_escape_string($con, trim($_POST['specialism']));
   $country = mysqli_real_escape_string($con, trim($_POST['country']));
   $city = mysqli_real_escape_string($con, trim($_POST['city']));
   $address = mysqli_real_escape_string($con, trim($_POST['address']));
   $about = mysqli_real_escape_string($con, trim($_POST['about']));
   $award = mysqli_real_escape_string($con, trim($_POST['award']))?? '';
   $award_year = mysqli_real_escape_string($con, trim($_POST['award_year']))?? '';
   $award_disc = mysqli_real_escape_string($con, trim($_POST['award_disc']))?? '';

   $websiteURL = mysqli_real_escape_string($con, trim($_POST['websiteURL'])) ?? '';
   
   $companyid = mysqli_real_escape_string($con, trim($_POST['companyid']));
   
   

   if(empty($_FILES['image']['name'])){
      $image_url = mysqli_real_escape_string($con, trim($_POST['COMPANYLOGO']));
   }else{
      $image = $_FILES['image']['name'];
      $target = $path . "company_logo/" . basename($image);
      $image_url = "company_logo/" . $image;

      if ($_FILES["image"]["size"] > 500000) {
         $msg = "<div style='color:red'>File is too large!</div>";
         goto end;
      }
      move_uploaded_file($_FILES['image']['tmp_name'], $target);
   }

   $query = "UPDATE tblcompany SET 
         COMPANYNAME = '$name', 
         COMPANYADDRESS = '$address', 
         COMPANYCONTACTNO = '$contact', 
         COMPANYABOUT = '$about', 
         COMPANYEMAIL = '$email', 
         COMPANYWEBSITE = '$websiteURL',
         COMPANYINDUSTRY = '$industry', 
         COMPANYSPECIALISM = '$specialism', 
         COMPANYCOUNTRY = '$country', 
         COMPANYCITY = '$city', 
         COMPANYAWARD = '$award', 
         COMPANYYEAR = '$award_year',
         COMPANYAWARDDESC = '$award_disc',
         COMPANYLOGO = '$image_url' 
         WHERE COMPANYID = '$companyid' AND USERID = '$session_id'";

         
      $result = mysqli_query($con, $query);

      if ($result) {
         echo "<script>alert('Company Updated!')</script>";
         header('location: ./company-detail.php?companyid=' . $companyid);
         exit();
      } else {
         $msg = "<div style='color:red'>Error occurred...!</div>";
         echo mysqli_error($con);
      }
   }
end:
/////////////////////////Edit company ends/////////////////////////



























////////////////////////Save/Bookmark Resume..../////////////////

// Handle save resume (bookmark)
if (isset($_POST['save_resume'])) {
    $applicationId = (int)$_POST['jobapplicationid'];
    $jobIdToSave = (int)$_POST['jobID'];
    $employer = (int)$_POST['employer'];
    
    $insertBookmark = "INSERT INTO tblbookmarkresume (USERID, JOBAPPLICATIONID, JOBRESUMEID, DATETIME) VALUES (?, ?, ?,  now())";
    $stmtBk = mysqli_prepare($con, $insertBookmark);
    mysqli_stmt_bind_param($stmtBk, "iii", $employer, $applicationId, $jobIdToSave);
    
    if (mysqli_stmt_execute($stmtBk)) {
        $_SESSION['success_msg'] = "Resume bookmarked successfully!";
    }
    
    header("Location: " . $_SERVER['PHP_SELF'] . ($jobIdToSave > 0 ? "?jobid=$jobIdToSave" : ""));
    exit();
}
////////////////////////Save/Bookmark Resume..../////////////////




////////////////////////Remove /Bookmark Resume..../////////////////
// Handle delete bookmark
if (isset($_GET['type']) && $_GET['type'] == 'delete' && isset($_GET['bookmarkid'])) {
    $bookmarkId = (int)$_GET['bookmarkid'];
    $jobIdParam = isset($_GET['jobid']) ? (int)$_GET['jobid'] : 0;
    
    $deleteQuery = "DELETE FROM tblbookmarkresume WHERE ID = ? AND USERID = ? AND 	JOBRESUMEID = ?";
    $stmtDel = mysqli_prepare($con, $deleteQuery);
    mysqli_stmt_bind_param($stmtDel, "iii", $bookmarkId, $session_id, $jobIdParam);
    
    if (mysqli_stmt_execute($stmtDel)) {
        $_SESSION['success_msg'] = "Bookmark removed successfully!";
    }
    
    header("Location: dashboard-manage-applications.php" . ($jobIdParam > 0 ? "?jobid=$jobIdParam" : ""));
    exit();
}
////////////////////////Remove /Bookmark Resume..../////////////////








////////////////////Download Resume///////////////////////////

if (isset($_POST['download_resume'])) {

   $resume_url = $_POST['resume_url'];

   $filepath = $path.'resumes_cv/' . $resume_url;

   if (file_exists($filepath)) {
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename=' . basename($filepath));
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize($path .'resumes_cv/' . $resume_url));
      readfile($path .'resumes_cv/' . $resume_url);
   }
}
////////////////////End Download Resume///////////////////////////





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









///////////////////Change Application Status///////////////
if (isset($_POST['applicationstatus'])) {
   
   $applicationstatus = $_POST['applicationstatus'];
   $jobID = $_POST['jobID'];

   $query = "SELECT * from tbljob where JOBID ='$jobID' order by JOBID desc";
   $result = mysqli_query($con, $query);
   $row = mysqli_fetch_array($result);
   $JOBTITLE = $row['JOBTITLE']  ?? '';

   $userID = $_POST['userID'];
   $jobapplicationid = $_POST['jobapplicationid'];
   $TYPE = "Job Application";
   $STATUS = "Unread";
   $NOTE = "Job Application for (". $JOBTITLE. ") status has changed to ". $applicationstatus;

   $query = "UPDATE tbljobapplication SET APPLICATIONSTATUS = '$applicationstatus' WHERE ID = '$jobapplicationid' and JOBID = '$jobID'";
   $result = mysqli_query($con, $query);

   $query = "INSERT INTO tblnotification (USERID, TYPE, TYPEID, STATUS, DATETIME, NOTE) VALUES ('$userID', '$TYPE', '$jobapplicationid', '$STATUS', now(), '$NOTE')" or die(mysqli_error($con));

   $result = mysqli_query($con, $query);

   if (($result)) {

      echo "<script>alert('Application $applicationstatus!')</script>";

   } else {
      $msg = "<div style='color:red'>Error occured...!</div>";
      echo mysqli_error($con);
   }
}








//////////////////Job Status////////////////////////

if (isset($_POST['applicationapprove'])) {

   $applicationstatus = 'Approved';
   $jobID = $_POST['jobID'];

   $query = "SELECT * from tbljob where JOBID ='$jobID' order by JOBID desc";
   $result = mysqli_query($con, $query);
   $row = mysqli_fetch_array($result);
   $JOBTITLE = $row['JOBTITLE']  ?? '';

   $userID = $_POST['userID'];
   $jobapplicationid = $_POST['jobapplicationid'];
   $TYPE = "Job Application";
   $STATUS = "Unread";
   $NOTE = "Job Application for (" . $JOBTITLE . ") status has changed to " . $applicationstatus;

   $query = "UPDATE tbljobapplication SET APPLICATIONSTATUS = '$applicationstatus' WHERE ID = '$jobapplicationid' and JOBID = '$jobID'";
   $result = mysqli_query($con, $query);

   $query = "INSERT INTO tblnotification (USERID, TYPE, TYPEID, STATUS, DATETIME, NOTE) VALUES ('$userID', '$TYPE', '$jobapplicationid', '$STATUS', now(), '$NOTE')" or die(mysqli_error($con));

   $result = mysqli_query($con, $query);

   if (($result)) {

      echo "<script>alert('Application $applicationstatus!')</script>";
   } else {
      $msg = "<div style='color:red'>Error occured...!</div>";
      echo mysqli_error($con);
   }
}
///////////////////End Change Application Status///////////////








/////////////////// Send Message  ///////////////
if (isset($_POST['send_message']) && isset($_POST['recipient_id']) && isset($_POST['message'])) {
   $recipientId = (int)$_POST['recipient_id'];
   $message = trim($_POST['message']);
   $senderId = (int)$session_id; // from session.php

   // basic validation
   if ($recipientId <= 0 || $senderId <= 0 || $message === '') {
      $_SESSION['error_msg'] = "Invalid message data.";
   } else {
      // limit message length to a reasonable size
      $message = mb_substr($message, 0, 2000);

      // use transaction so both inserts succeed or fail together (requires InnoDB)
      mysqli_begin_transaction($con);
      
      // prepare and execute message insert
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

         $querycompany = "SELECT * from tblcompany where USERID = '$senderId'";
         $resultcompany = mysqli_query($con, $querycompany);
         $rowcompany = mysqli_fetch_array($resultcompany);
         $COMPANYNAME = $rowcompany['COMPANYNAME'];


         // prepare notification
         $type = "Message";
         $status = "Unread";
         $note = "New message from $COMPANYNAME (message id: {$messageId})";

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









/////////////////// Send Note  ///////////////
if (isset($_POST['send_note'])) {

   $userID = $_POST['userID'];
   $notice = $_POST['notice'];
   $jobID = $_POST['jobID'];

   $query = "SELECT * from tbljob where JOBID ='$jobID' order by JOBID desc";
   $result = mysqli_query($con, $query);
   $row = mysqli_fetch_array($result);
   $JOBTITLE = $row['JOBTITLE']  ?? '';

   $TYPE = "Job Application";
   $STATUS = "Unread";
   $NOTE = "Job Application Notice for (" . $JOBTITLE . ") says: ". $notice;

   $query = "INSERT INTO tblnotification (USERID, TYPE, TYPEID, STATUS, DATETIME, NOTE) VALUES ('$userID', '$TYPE', '$jobID', '$STATUS', now(), '$NOTE')" or die(mysqli_error($con));

   $result = mysqli_query($con, $query);

   if (($result)) {

      echo "<script>alert('Note Sent!')</script>";
   } else {
      $msg = "<div style='color:red'>Error occured...!</div>";
      echo mysqli_error($con);
   }
}

///////////////////End Send Note///////////////










// // Custom 404 handler
// function handle_404() {
//     header("HTTP/1.0 404 Not Found");
    
//     // Check if user is in dashboard
//     if (isset($_SESSION['USERID']) || isset($_SESSION['COMPANYID'])) {
//         header("Location: dashboard-404.php");
//     } else {
//         header("Location: 404.php");
//     }
//     exit();
// }

// // Check if page exists
// if (!file_exists($_SERVER['SCRIPT_FILENAME'])) {
//     handle_404();
// }


unset($result); // Clear any temporary results