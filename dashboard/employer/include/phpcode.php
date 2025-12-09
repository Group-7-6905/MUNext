<?php
require 'session.php';
$msg = '';
$msg2='';

$path = "../../";
 date_default_timezone_set('America/St_Johns');

// ==================== GET SITE SETTINGS ====================
require '../settings-manager.php';

// ==================== EMAIL FUNCTIONS ====================
require_once $path."include/email-functions.php";







/**
 * ==================== LOGIN HISTORY HELPER FUNCTIONS ====================
 * This file contains functions to track and manage user login history
 * Functions include: recording logins, getting client info, parsing user agent
 */

// ==================== GET CLIENT IP ADDRESS ====================
function getClientIP() {
    $ipaddress = '';
    
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    } else {
        $ipaddress = 'UNKNOWN';
    }
    
    // Validate IP address
    if (filter_var($ipaddress, FILTER_VALIDATE_IP) === false) {
        $ipaddress = 'UNKNOWN';
    }
    
    return $ipaddress;
}

// ==================== GET USER AGENT ====================
function getUserAgent() {
    return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown';
}

// ==================== PARSE USER AGENT FOR BROWSER ====================
function getBrowserInfo($user_agent) {
    $browser = 'Unknown Browser';
    
    $browser_array = array(
        '/msie/i'      => 'Internet Explorer',
        '/firefox/i'   => 'Firefox',
        '/safari/i'    => 'Safari',
        '/chrome/i'    => 'Chrome',
        '/edge/i'      => 'Edge',
        '/opera/i'     => 'Opera',
        '/netscape/i'  => 'Netscape',
        '/maxthon/i'   => 'Maxthon',
        '/konqueror/i' => 'Konqueror',
        '/mobile/i'    => 'Mobile Browser'
    );
    
    foreach ($browser_array as $regex => $value) {
        if (preg_match($regex, $user_agent)) {
            $browser = $value;
            break;
        }
    }
    
    return $browser;
}

// ==================== PARSE USER AGENT FOR OPERATING SYSTEM ====================
function getOperatingSystem($user_agent) {
    $os_platform = 'Unknown OS';
    
    $os_array = array(
        '/windows nt 10/i'      => 'Windows 10',
        '/windows nt 6.3/i'     => 'Windows 8.1',
        '/windows nt 6.2/i'     => 'Windows 8',
        '/windows nt 6.1/i'     => 'Windows 7',
        '/windows nt 6.0/i'     => 'Windows Vista',
        '/windows nt 5.2/i'     => 'Windows Server 2003/XP x64',
        '/windows nt 5.1/i'     => 'Windows XP',
        '/windows xp/i'         => 'Windows XP',
        '/windows nt 5.0/i'     => 'Windows 2000',
        '/windows me/i'         => 'Windows ME',
        '/win98/i'              => 'Windows 98',
        '/win95/i'              => 'Windows 95',
        '/win16/i'              => 'Windows 3.11',
        '/macintosh|mac os x/i' => 'Mac OS X',
        '/mac_powerpc/i'        => 'Mac OS 9',
        '/linux/i'              => 'Linux',
        '/ubuntu/i'             => 'Ubuntu',
        '/iphone/i'             => 'iPhone',
        '/ipod/i'               => 'iPod',
        '/ipad/i'               => 'iPad',
        '/android/i'            => 'Android',
        '/blackberry/i'         => 'BlackBerry',
        '/webos/i'              => 'Mobile'
    );
    
    foreach ($os_array as $regex => $value) {
        if (preg_match($regex, $user_agent)) {
            $os_platform = $value;
            break;
        }
    }
    
    return $os_platform;
}

// ==================== DETECT DEVICE TYPE ====================
function getDeviceType($user_agent) {
    $device = 'Desktop';
    
    if (preg_match('/mobile|android|kindle|silk|midp|phone|blackberry/i', $user_agent)) {
        $device = 'Mobile';
    } elseif (preg_match('/tablet|ipad|playbook/i', $user_agent)) {
        $device = 'Tablet';
    }
    
    return $device;
}

// ==================== RECORD LOGIN HISTORY ====================
function recordLoginHistory($con, $userid, $username, $email, $usertype, $status = 'Success', $failure_reason = null) {
    // Get client information
    $ip_address = getClientIP();
    $user_agent = getUserAgent();
    $browser = getBrowserInfo($user_agent);
    $os = getOperatingSystem($user_agent);
    $device = getDeviceType($user_agent);
    $session_id = session_id();
    
    // If userid is 0 (user not found), set it to NULL
    $userid_value = ($userid == 0) ? null : $userid;
    
    // Insert login history record
    if ($userid_value === null) {
        // For failed attempts where user doesn't exist, use NULL for USERID
        $query = "INSERT INTO tbl_login_history 
                  (USERID, USERNAME, EMAIL, USERTYPE, LOGIN_TIME, IP_ADDRESS, USER_AGENT, 
                   BROWSER, DEVICE, OPERATING_SYSTEM, LOGIN_STATUS, FAILURE_REASON, SESSION_ID) 
                  VALUES (NULL, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($con, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt, 
                "sssssssssss", 
                $username, 
                $email, 
                $usertype, 
                $ip_address, 
                $user_agent, 
                $browser, 
                $device, 
                $os, 
                $status, 
                $failure_reason, 
                $session_id
            );
            
            $result = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            
            return $result;
        }
    } else {
        // For valid users, include USERID
        $query = "INSERT INTO tbl_login_history 
                  (USERID, USERNAME, EMAIL, USERTYPE, LOGIN_TIME, IP_ADDRESS, USER_AGENT, 
                   BROWSER, DEVICE, OPERATING_SYSTEM, LOGIN_STATUS, FAILURE_REASON, SESSION_ID) 
                  VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($con, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt, 
                "isssssssssss", 
                $userid_value, 
                $username, 
                $email, 
                $usertype, 
                $ip_address, 
                $user_agent, 
                $browser, 
                $device, 
                $os, 
                $status, 
                $failure_reason, 
                $session_id
            );
            
            $result = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            
            return $result;
        }
    }
    
    return false;
}

// ==================== RECORD LOGOUT ====================
function recordLogout($con, $userid) {
    $session_id = session_id();
    
    // Update the most recent active login session with logout time
    $query = "UPDATE tbl_login_history 
              SET LOGOUT_TIME = NOW(), IS_ACTIVE = 0 
              WHERE USERID = ? AND SESSION_ID = ? AND LOGOUT_TIME IS NULL 
              ORDER BY LOGIN_TIME DESC LIMIT 1";
    
    $stmt = mysqli_prepare($con, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "is", $userid, $session_id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        return $result;
    }
    
    return false;
}

// ==================== GET USER LOGIN HISTORY ====================
function getUserLoginHistory($con, $userid, $limit = 10) {
    $query = "SELECT * FROM tbl_login_history 
              WHERE USERID = ? 
              ORDER BY LOGIN_TIME DESC 
              LIMIT ?";
    
    $stmt = mysqli_prepare($con, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $userid, $limit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $history = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $history[] = $row;
        }
        
        mysqli_stmt_close($stmt);
        return $history;
    }
    
    return [];
}

// ==================== GET FAILED LOGIN ATTEMPTS ====================
function getFailedLoginAttempts($con, $userid = null, $hours = 24, $ip_address = null) {
    // If no userid provided, check by IP address for failed attempts
    if ($userid === null && $ip_address !== null) {
        $query = "SELECT COUNT(*) as failed_count 
                  FROM tbl_login_history 
                  WHERE IP_ADDRESS = ? 
                  AND LOGIN_STATUS = 'Failed' 
                  AND LOGIN_TIME >= DATE_SUB(NOW(), INTERVAL ? HOUR)";
        
        $stmt = mysqli_prepare($con, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $ip_address, $hours);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            
            return $row['failed_count'];
        }
    } elseif ($userid !== null) {
        $query = "SELECT COUNT(*) as failed_count 
                  FROM tbl_login_history 
                  WHERE USERID = ? 
                  AND LOGIN_STATUS = 'Failed' 
                  AND LOGIN_TIME >= DATE_SUB(NOW(), INTERVAL ? HOUR)";
        
        $stmt = mysqli_prepare($con, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ii", $userid, $hours);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            
            return $row['failed_count'];
        }
    }
    
    return 0;
}

// ==================== CHECK FOR SUSPICIOUS ACTIVITY ====================
function checkSuspiciousActivity($con, $userid) {
    // Check for multiple failed attempts in last hour
    $failed_attempts = getFailedLoginAttempts($con, $userid, 1);
    
    if ($failed_attempts >= 5) {
        return [
            'is_suspicious' => true,
            'reason' => 'Multiple failed login attempts detected',
            'failed_count' => $failed_attempts
        ];
    }
    
    // Check for logins from different locations in short time
    $query = "SELECT DISTINCT IP_ADDRESS 
              FROM tbl_login_history 
              WHERE USERID = ? 
              AND LOGIN_TIME >= DATE_SUB(NOW(), INTERVAL 1 HOUR) 
              AND LOGIN_STATUS = 'Success'";
    
    $stmt = mysqli_prepare($con, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $userid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $ip_count = mysqli_num_rows($result);
        mysqli_stmt_close($stmt);
        
        if ($ip_count >= 3) {
            return [
                'is_suspicious' => true,
                'reason' => 'Multiple logins from different IP addresses',
                'ip_count' => $ip_count
            ];
        }
    }
    
    return ['is_suspicious' => false];
}

// ==================== CHECK IP RATE LIMITING ====================
function checkIPRateLimit($con, $ip_address, $minutes = 15, $max_attempts = 10) {
    $query = "SELECT COUNT(*) as attempt_count 
              FROM tbl_login_history 
              WHERE IP_ADDRESS = ? 
              AND LOGIN_STATUS = 'Failed' 
              AND LOGIN_TIME >= DATE_SUB(NOW(), INTERVAL ? MINUTE)";
    
    $stmt = mysqli_prepare($con, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "si", $ip_address, $minutes);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        return $row['attempt_count'] >= $max_attempts;
    }
    
    return false;
}

// ==================== GET ACTIVE SESSIONS ====================
function getActiveSessions($con, $userid) {
    $query = "SELECT * FROM tbl_login_history 
              WHERE USERID = ? 
              AND IS_ACTIVE = 1 
              AND LOGOUT_TIME IS NULL 
              ORDER BY LOGIN_TIME DESC";
    
    $stmt = mysqli_prepare($con, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $userid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $sessions = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $sessions[] = $row;
        }
        
        mysqli_stmt_close($stmt);
        return $sessions;
    }
    
    return [];
}

// ==================== TERMINATE SESSION ====================
function terminateSession($con, $login_id) {
    $query = "UPDATE tbl_login_history 
              SET LOGOUT_TIME = NOW(), IS_ACTIVE = 0 
              WHERE LOGIN_ID = ?";
    
    $stmt = mysqli_prepare($con, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $login_id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        return $result;
    }
    
    return false;
}

// ==================== CLEAN OLD LOGIN HISTORY ====================
function cleanOldLoginHistory($con, $days = 90) {
    // Delete login history older than specified days
    $query = "DELETE FROM tbl_login_history 
              WHERE LOGIN_TIME < DATE_SUB(NOW(), INTERVAL ? DAY)";
    
    $stmt = mysqli_prepare($con, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $days);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        return $result;
    }
    
    return false;
}







































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

   $name = trim($_POST['name']);
   $email = trim($_POST['email']);
   $contact = trim($_POST['contact'])?? '';
   $industry = trim($_POST['industry']);
   $specialism = trim($_POST['specialism']);
   $country = trim($_POST['country']);
   $city = trim($_POST['city']);
   $address = trim($_POST['address']);
   $about = trim($_POST['about']);
   $award = trim($_POST['award'])?? '';
   $award_year = trim($_POST['award_year'])?? '';
   $award_disc = trim($_POST['award_disc'])?? '';
   $websiteURL = trim($_POST['websiteURL']) ?? '';


   $companyStatus = autoApproveCompanies() ? 'Active' : 'Pending';

    $image_path = $COMPANYLOGO ?? ''; // Keep existing if no new upload

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'];
        
        if (in_array($image_ext, $allowed_ext)) {
            $new_image_name = 'company_' . $session_id . '_' . time() . '.' . $image_ext;
            $upload_path = 'company_logo/' . $new_image_name;
            $target = $path . "company_logo/" . basename($new_image_name);
            

            
            // Create directory if it doesn't exist
            if (!file_exists('../../company_logo')) {
                mkdir('../../company_logo', 0777, true);
            }
            
            if (move_uploaded_file($image_tmp, $target)) {
                $image_path = $upload_path;
            }
        }
    }

     $stmt = $con->prepare("INSERT INTO tblcompany 
                (USERID, COMPANYNAME, COMPANYADDRESS, COMPANYWEBSITE, COMPANYCONTACTNO, COMPANYSTATUS, 
                 COMPANYABOUT, COMPANYEMAIL, COMPANYINDUSTRY, COMPANYSPECIALISM, 
                 COMPANYCOUNTRY, COMPANYCITY, COMPANYAWARD, COMPANYYEAR, 
                 COMPANYAWARDDESC, COMPANYLOGO, DATEREGISTERED) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now())");
                         
      $stmt->bind_param("isssssssssssssss", $session_id, $name, $address, $websiteURL, $contact, $companyStatus, $about, $email, $industry, $specialism, $country, $city, $award, $award_year, $award_disc, $image_path);
                

      // $result = mysqli_query($con, $query);

      if ($stmt->execute()) {
         $msg = '<div class="alert alert-success">✅ Profile data saved successfully!</div>';
         header('location: ./company-detail.php?companyid=' . mysqli_insert_id($con));
         exit();
      } else {
          $msg = '<div class="alert alert-danger">❌ Error: ' . mysqli_error($con) . '</div>';
      }
     $stmt->close();

   
}
/////////////////////////Add company ends/////////////////////////







/////////////////Edit Company/////////////////////////////
// $msg = '';

if (isset($_POST['edit_company'])) {
   
   $companyidquery = "SELECT * from tblcompany WHERE USERID = '$session_id'";
   $resultcompanyid = mysqli_query($con, $companyidquery);
   $rowcompanyid = mysqli_fetch_array($resultcompanyid);
   $COMPANYLOGO = $rowcompanyid['COMPANYLOGO'] ?? '';

   $name = trim($_POST['name'])?? '';
   $email = trim($_POST['email'])?? '';
   $contact = trim($_POST['contact'])?? '';
   $industry = trim($_POST['industry'])?? '';
   $specialism = trim($_POST['specialism'])?? '';
   $country = trim($_POST['country'])?? '';
   $city = trim($_POST['city'])?? '';
   $address = trim($_POST['address'])?? '';
   $about = trim($_POST['about'])?? '';
   $award = trim($_POST['award'])?? '';
   $award_year = trim($_POST['award_year'])?? '';
   $award_disc = trim($_POST['award_disc'])?? '';

   $websiteURL = trim($_POST['websiteURL']) ?? '';
   $companyid = trim($_POST['companyid'])?? '';
   
   
   $companyStatus = autoApproveCompanies() ? 'Active' : 'Pending';
   
    $image_path = $COMPANYLOGO ?? ''; // Keep existing if no new upload

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'];
        
        if (in_array($image_ext, $allowed_ext)) {
            $new_image_name = 'company_' . $session_id . '_' . time() . '.' . $image_ext;
            $upload_path = 'company_logo/' . $new_image_name;
            $target = $path . "company_logo/" . basename($new_image_name);
            

            
            // Create directory if it doesn't exist
            if (!file_exists('../../company_logo')) {
                mkdir('../../company_logo', 0777, true);
            }
            
            if (move_uploaded_file($image_tmp, $target)) {
                $image_path = $upload_path;
            }
        }
    }

   $stmt = $con->prepare("UPDATE tblcompany SET 
         COMPANYNAME = ?, 
         COMPANYADDRESS = ?, 
         COMPANYCONTACTNO = ?, 
         COMPANYABOUT = ?, 
         COMPANYEMAIL = ?, 
         COMPANYWEBSITE = ?,
         COMPANYINDUSTRY = ?, 
         COMPANYSPECIALISM = ?, 
         COMPANYCOUNTRY = ?, 
         COMPANYCITY = ?, 
         COMPANYAWARD = ?, 
         COMPANYYEAR = ?,
         COMPANYAWARDDESC = ?,
         COMPANYLOGO = ? 
         WHERE COMPANYID = ? AND USERID = ?");

$stmt->bind_param("ssssssssssssssii", $name, $address, $contact, $about, $email, $websiteURL, $industry, $specialism, $country, $city, $award, $award_year, $award_disc, $image_path, $companyid, $session_id);
         
      if ($stmt->execute()) { 
         $msg = '<div class="alert alert-success">✅ Profile data saved successfully!</div>';
         header('location: ./company-detail.php');
         exit();
       } else {
             $msg = '<div class="alert alert-danger">❌ Error: ' . mysqli_error($con) . '</div>';
          }
     $stmt->close();
      } 
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
        $verifyQuery = "SELECT PASS FROM tblusers WHERE USERID = ?";
        $stmtVerify = mysqli_prepare($con, $verifyQuery);
        mysqli_stmt_bind_param($stmtVerify, "i", $userId);
        mysqli_stmt_execute($stmtVerify);
        $verifyResult = mysqli_stmt_get_result($stmtVerify);
        
        if ($userRow = mysqli_fetch_assoc($verifyResult)) {
            $currentHashedPassword = $userRow['PASS'];
            
            // Check if old password matches
            if (password_verify($oldPassword, $currentHashedPassword) || md5($oldPassword) === $currentHashedPassword) {
                // Hash new password
                $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                
                // Update password
                $updateQuery = "UPDATE tblusers SET PASS = ? WHERE USERID = ?";
                $stmtUpdate = mysqli_prepare($con, $updateQuery);
                mysqli_stmt_bind_param($stmtUpdate, "si", $newHashedPassword, $userId);
                
                if (mysqli_stmt_execute($stmtUpdate)) {
                    $success = true;
                    $_SESSION['success_msg'] = "Password changed successfully!";
                    
                    // Optional: Log the password change
                    $logQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, IP_ADDRESS, CREATED_AT) 
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
// if (isset($_POST['applicationstatus'])) {
   
//    $applicationstatus = $_POST['applicationstatus'];
//    $jobID = $_POST['jobID'];

//    $query = "SELECT * from tbljob where JOBID ='$jobID' order by JOBID desc";
//    $result = mysqli_query($con, $query);
//    $row = mysqli_fetch_array($result);
//    $JOBTITLE = $row['JOBTITLE']  ?? '';

//    $userID = $_POST['userID'];
//    $jobapplicationid = $_POST['jobapplicationid'];
//    $TYPE = "Job Application";
//    $STATUS = "Unread";
//    $NOTE = "Job Application for (". $JOBTITLE. ") status has changed to ". $applicationstatus;

//    $query = "UPDATE tbljobapplication SET APPLICATIONSTATUS = '$applicationstatus' WHERE ID = '$jobapplicationid' and JOBID = '$jobID'";
//    $result = mysqli_query($con, $query);

//    $query = "INSERT INTO tblnotification (USERID, TYPE, TYPEID, STATUS, DATETIME, NOTE) VALUES ('$userID', '$TYPE', '$jobapplicationid', '$STATUS', now(), '$NOTE')" or die(mysqli_error($con));

//    $result = mysqli_query($con, $query);

//    if (($result)) {

//       echo "<script>alert('Application $applicationstatus!')</script>";

//    } else {
//       $msg = "<div style='color:red'>Error occured...!</div>";
//       echo mysqli_error($con);
//    }
// }








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