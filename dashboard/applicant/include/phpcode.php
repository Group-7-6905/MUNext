<?php
require 'session.php';

$path = "../../";
 date_default_timezone_set('America/St_Johns');

// ==================== GET SITE SETTINGS ====================
require '../settings-manager.php';
//////////////////////////////////////////////////////////////////
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







/////////////////////////Complete Profile/////////////////////////
$msg = '';
$msg2 = '';
$msg3 = '';


// In phpcode.php - make sure you have this:
$querycomp = "SELECT * FROM tblapplicants WHERE USERID = ?";
$stmtcomp = mysqli_prepare($con, $querycomp);
mysqli_stmt_bind_param($stmtcomp, "i", $session_id);
mysqli_stmt_execute($stmtcomp);
$resultcomp = mysqli_stmt_get_result($stmtcomp);
$rowcomp = mysqli_fetch_array($resultcomp);
mysqli_stmt_close($stmtcomp);

// Set all variables with null coalescing
$USERID = $rowcomp['USERID'] ?? '';
$APPLICANTID = $rowcomp['APPLICANTID'] ?? '';
$JOBTITLE = $rowcomp['JOBTITLE'] ?? '';
$JOBCATEGORYID = $rowcomp['JOBCATEGORYID'] ?? '';
$CONTACTNO = $rowcomp['CONTACTNO'] ?? '';
$SEX = $rowcomp['SEX'] ?? '';
$BIRTHDATE = $rowcomp['BIRTHDATE'] ?? '';
$ABOUTME = $rowcomp['ABOUTME'] ?? '';
$APPLICANTPHOTO = $rowcomp['APPLICANTPHOTO'] ?? '';
$COUNTRY = $rowcomp['COUNTRY'] ?? '';
$CITY = $rowcomp['CITY'] ?? '';
$ADDRESS = $rowcomp['FULLADDRESS'] ?? '';
// ... etc


// FORM 1: Save Basic Profile Data
if (isset($_POST['save_data'])) {
    $job_title = trim($_POST['job_title']);
    $job_categoryid = trim($_POST['job_categoryid']);
    $phoneno = trim($_POST['phoneno']);
    $sex = trim($_POST['sex']);
    $dob = trim($_POST['dob']);
    $about_me = trim($_POST['about_me']);
    
    // Handle image upload
    $image_path = $APPLICANTPHOTO; // Keep existing if no new upload
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'];
        
        if (in_array($image_ext, $allowed_ext)) {
            $new_image_name = 'applicant_' . $session_id . '_' . time() . '.' . $image_ext;
            $upload_path = 'profile/' . $new_image_name;
            $target = "../../profile/" . basename($new_image_name);

            
            // Create directory if it doesn't exist
            if (!file_exists('../../profile')) {
                mkdir('../../profile', 0777, true);
            }
            
            if (move_uploaded_file($image_tmp, $target)) {
                $image_path = $upload_path;
            }
        }
    }
    
    // Check if profile exists
    if (empty($USERID)) {
        // Check if user already has a profile
        $queryuser = "SELECT * FROM tblusers WHERE USERID = ?";
        $stmt = mysqli_prepare($con, $queryuser);
        mysqli_stmt_bind_param($stmt, "i", $session_id);
        mysqli_stmt_execute($stmt);
        $resultuser = mysqli_stmt_get_result($stmt);
        $rowuser = mysqli_fetch_array($resultuser);

        $USERID = $rowuser['USERID'];
        $FNAME = $rowuser['FNAME'];
        $ONAME = $rowuser['ONAME'];
        $EMAIL = $rowuser['EMAIL'];
        $USERNAME = $rowuser['USERNAME'];

        // INSERT new profile
        $stmt = $con->prepare("INSERT INTO tblapplicants (JOBCATEGORYID, JOBTITLE, USERID, FNAME, OTHERNAMES, SEX, BIRTHDATE, ABOUTME, USERNAME, EMAILADDRESS, CONTACTNO, APPLICANTPHOTO) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        // $stmt = mysqli_prepare($con, $sql);
        $stmt->bind_param( "isisssssssss", $job_categoryid,$job_title, $USERID, $FNAME, $ONAME, $sex, $dob, $about_me, $USERNAME, $EMAIL, $phoneno, $image_path);

    } else {
        // UPDATE existing profile
        $stmt = $con->prepare("UPDATE tblapplicants SET 
                JOBTITLE = ?, JOBCATEGORYID = ?, CONTACTNO = ?, SEX = ?, BIRTHDATE = ?, ABOUTME = ?, APPLICANTPHOTO = ? 
                WHERE USERID = ?");
        // $stmt = mysqli_prepare($con, $sql);
        $stmt->bind_param("ssissssi", $job_title, $job_categoryid, $phoneno, $sex, $dob, $about_me, $image_path, $session_id);
    }
    
    if ($stmt->execute()) {
        $msg = '<div class="alert alert-success">✅ Profile data saved successfully!</div>';

        // Reload data
        // header("Refresh:1");
        header('location: ./dashboard-add-profile.php#section23');
    } else {
        $msg = '<div class="alert alert-danger">❌ Error: ' . mysqli_error($con) . '</div>';
    }
     $stmt->close();
}




// FORM 2: Save Contact & Social Info
if (isset($_POST['save_info'])) {
    $country = trim($_POST['country']);
    $city = trim($_POST['city']);
    $address = trim($_POST['address']);
    $fb = trim($_POST['fb'])?? '';
    $lin = trim($_POST['lin'])?? '';
    
    $stmt = $con->prepare("UPDATE tblapplicants SET 
            COUNTRY = ?, CITY = ?, FULLADDRESS = ?, FB_LINK = ?, LINKEDIN_LINK = ? 
            WHERE USERID = ?");
    // $stmt = mysqli_prepare($con, $sql);
    $stmt->bind_param("sssssi", $country, $city, $address, $fb, $lin, $session_id);
    
    if ($stmt->execute()) {
        $msg2 = '<div class="alert alert-success">✅ Contact information saved successfully!</div>';
        // header("Refresh:1");
        header('location: ./dashboard-add-profile.php#section456');
    } else {
        $msg2 = '<div class="alert alert-danger">❌ Error: ' . mysqli_error($con) . '</div>';
    }
    $stmt->close();

}



// FORM 3: Save Education, Experience & Skills
if (isset($_POST['save_preview'])) {
    $schl_name = trim($_POST['schl_name']);
    $qualification = trim($_POST['qualification']);
    $company_name_select = trim($_POST['company_name_select'] ?? '');
    $company_name_specify = trim($_POST['company_name_specify'] ?? '');
    $job_title_ex = trim($_POST['job_title'] ?? '');
    $skills = trim($_POST['skills']);
    
    // Determine company name
    $company_name = ($company_name_select == 'Others (Please specify)') ? $company_name_specify : $company_name_select;
    
    $stmt = $con->prepare("UPDATE tblapplicants SET 
            SCHOOLNAME = ?, DEGREE = ?, EXCOMPANYNAME = ?, EXJOBTITLE = ?, SKILLS = ? 
            WHERE USERID = ?");
    // $stmt = mysqli_prepare($con, $sql);
    $stmt->bind_param( "sssssi", $schl_name, $qualification, $company_name, $job_title_ex, $skills, $session_id);
    
    if ($stmt->execute()) {
        $msg3 = '<div class="alert alert-success">✅ Profile completed successfully! Redirecting to preview...</div>';
      //   header("Refresh:1; url=candidate-detail.php");
        header("Location: candidate-detail.php");
    } else {
        $msg3 = '<div class="alert alert-danger">❌ Error: ' . mysqli_error($con) . '</div>';
    }
    $stmt->close();

}
/////////////////////////End Complete Profile ends/////////////////////////








/////////////////////////Edit Profile/////////////////////////

////////////section 1/////////////////////////
if (isset($_POST['edit_data'])) {

   $job_title = trim($_POST['job_title']);
   $job_categoryid = trim($_POST['job_categoryid']);
   $dob = trim($_POST['dob']);
   $phoneno = trim($_POST['phoneno']);
   $about_me = trim($_POST['about_me']);
   $sex = trim($_POST['sex']);
   $FName = trim($_POST['FName']);
   $OName = trim($_POST['OName']);
   $email = trim($_POST['email']);
   

    // Handle image upload
    $image_path = $APPLICANTPHOTO; // Keep existing if no new upload
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'];
        
        if (in_array($image_ext, $allowed_ext)) {
            $new_image_name = 'applicant_' . $session_id . '_' . time() . '.' . $image_ext;
            $upload_path = 'profile/' . $new_image_name;
            $target = "../../profile/" . basename($new_image_name);




            
            // Create directory if it doesn't exist
            if (!file_exists('../../profile')) {
                mkdir('../../profile', 0777, true);
            }
            
            if (move_uploaded_file($image_tmp, $target)) {
                $image_path = $upload_path;
            }
        }
    }
          // UPDATE existing profile
        $stmt = $con->prepare("UPDATE tblapplicants SET 
                JOBTITLE = ?, JOBCATEGORYID = ?, FNAME = ?,  OTHERNAMES = ?, EMAILADDRESS = ?, CONTACTNO = ?, SEX = ?, BIRTHDATE = ?, ABOUTME = ?, APPLICANTPHOTO = ? 
                WHERE USERID = ?");
        // $stmt = mysqli_prepare($con, $sql);
        $stmt->bind_param("sissssssssi", $job_title, $job_categoryid, $FName, $OName, $email, $phoneno, $sex, $dob, $about_me, $image_path, $session_id);
         
      if ($stmt->execute()) { 
         // Also update tblusers table for FNAME, ONAME, & EMAIL where USERID = '$USERID'
         $stmt = $con->prepare("UPDATE tblusers SET FNAME = ?, ONAME = ?, EMAIL = ? WHERE USERID = ?");
         $stmt->bind_param("ssss", $FName, $OName, $email, $USERID);
         if ($stmt->execute()) {
            $msg = '<div class="alert alert-success">✅ Profile data saved successfully!</div>';
            // Reload data
            header("Refresh:1");
             }
         else {
             $msg = '<div class="alert alert-danger">❌ Error: ' . mysqli_error($con) . '</div>';
          }
      }
      $stmt->close();
   }

//////////////////Ends Section 1 ///////////////////////////




/////////section 2, 3 ///////////////////

if (isset($_POST['edit_info'])) {
$country = trim($_POST['country']);
    $city = trim($_POST['city']);
    $address = trim($_POST['address']);
    $fb = trim($_POST['fb'])?? '';
    $lin = trim($_POST['lin'])?? '';
    
    $stmt = $con->prepare("UPDATE tblapplicants SET 
            COUNTRY = ?, CITY = ?, FULLADDRESS = ?, FB_LINK = ?, LINKEDIN_LINK = ? 
            WHERE USERID = ?");
    // $stmt = mysqli_prepare($con, $sql);
    $stmt->bind_param("sssssi", $country, $city, $address, $fb, $lin, $session_id);
    
    if ($stmt->execute()) {
        $msg2 = '<div class="alert alert-success">✅ Contact information saved successfully!</div>';
        header("Refresh:1");
    } else {
        $msg2 = '<div class="alert alert-danger">❌ Error: ' . mysqli_error($con) . '</div>';
    }
    $stmt->close();
}
//////////////////////Ends section 2, 3 /////////////////////





/////////section 4, 5, 6 ///////////////////

if (isset($_POST['edit_preview'])) {
$schl_name = trim($_POST['schl_name']);
    $qualification = trim($_POST['qualification']);
    $company_name_select = trim($_POST['company_name_select'] ?? '');
    $company_name_specify = trim($_POST['company_name_specify'] ?? '');
    $job_title_ex = trim($_POST['job_title'] ?? '');
    $skills = trim($_POST['skills']);
    
    // Determine company name
    $company_name = ($company_name_select == 'Others (Please specify)') ? $company_name_specify : $company_name_select;
    
    $stmt = $con->prepare("UPDATE tblapplicants SET 
            SCHOOLNAME = ?, DEGREE = ?, EXCOMPANYNAME = ?, EXJOBTITLE = ?, SKILLS = ? 
            WHERE USERID = ?");
    // $stmt = mysqli_prepare($con, $sql);
    $stmt->bind_param( "sssssi", $schl_name, $qualification, $company_name, $job_title_ex, $skills, $session_id);
    
    if ($stmt->execute()) {
      $msg3 = '<div class="alert alert-success">✅ Profile completed updated!</div>';
      header("Refresh:1; url=candidate-detail.php");

    } else {
        $msg3 = '<div class="alert alert-danger">❌ Error: ' . mysqli_error($con) . '</div>';
    }
    $stmt->close();
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
    'BIRTHDATE', 'ABOUTME', 'JOBTITLE', 'SKILLS', 'DEGREE', 'SCHOOLNAME'
];
$completedFields = 0;
foreach ($requiredFields as $field) {
    if (!empty($profile[$field])) {
        $completedFields++;
    }
}
$profileCompletion = round(($completedFields / count($requiredFields)) * 100);


?>