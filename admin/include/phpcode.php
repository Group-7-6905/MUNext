<?php
require 'session.php';
$msg = '';
$path='../';
 date_default_timezone_set('America/St_Johns');




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






///////////////Add Job ///////////////////////////
if (isset($_POST['add_job'])) {

   $job_title = $_POST['job_title_select'];
   if ($job_title == "Others (Please specify)") {
      $job_title = $_POST['job_title_specify']; //
   }


   $job_categoryid = $_POST['job_categoryid']; //
   $workplace_policy = $_POST['workplace_policy']; //
   $job_desc = $_POST['job_desc']; //
   $companyid = $_POST['company']; //
   $job_type = $_POST['job_type']; //
   $career_level = $_POST['career_level']; //
   $experience = $_POST['experience']; //
   $qualification = $_POST['qualification']; //
   $gender = $_POST['gender']; //

   $salary = 'Not Specified';
   if (!empty($_POST['salary'])) {
      $salary = $_POST['salary']; //
   }

   $deadline = $_POST['deadline'];


   $query = "INSERT INTO tbljob (COMPANYID, WORKPLACE_POLICY, JOBTITLE, JOBCATEGORYID, SALARY, JOBTYPE, QUALIFICATION, JOBDESCRIPTION, PREFEREDSEX, CAREERLEVEL, WORKEXPERIENCE, DEADLINE, JOBSTATUS, DATEPOSTED) VALUES ('$companyid','$workplace_policy','$job_title', '$job_categoryid', '$salary','$job_type','$qualification','$job_desc','$gender','$career_level','$experience','$deadline','Vacancy', now())" or die(mysqli_error($con));
   $result = mysqli_query($con, $query);

   if (($result)) {

      $query = "SELECT * from tbljob order by JOBID desc";
      $result = mysqli_query($con, $query);
      $row = mysqli_fetch_array($result);

      // $JOBID = $row['JOBID'];
      $JOBID   = $row['JOBID']  ?? '';

      // for ($i = 0; $i < sizeof($question); $i++) {
      //    // '" . $question[$i] . "'
      //    $query = "INSERT INTO tbljobscreening_ques (job_id, question_id) VALUES ('$JOBID','$question')" or die(mysqli_error($con));
      //    $result = mysqli_query($con, $query);
      // }

      if (!empty($_POST['question'])) {
         $ques = $_POST['question'];

         for ($i = 0; $i < sizeof($ques); $i++) {
            // '" . $ques[$i] . "'
            $query = "INSERT INTO tbljobscreening_ques (job_id, question_id) VALUES ('$JOBID','" . $ques[$i] . "')" or die(mysqli_error($con));
            $result = mysqli_query($con, $query);
         }
      }

      echo "<script>alert('Job Added!')</script>";

      header('location: ./dashboard-manage-jobs.php');
   } else {
      $msg = "<div style='color:red'>Error occured...!</div>";
      echo mysqli_error($con);
   }
}
///////////////Add Job Ends /////////////////////




///////////////Edit Job ///////////////////////////
if (isset($_POST['edit_job'])) {


   $job_title = $_POST['job_title_select'];
   if ($job_title == "Others (Please specify)") {
      $job_title = $_POST['job_title_specify']; //
   }

   $job_categoryid = $_POST['job_categoryid'];
   $workplace_policy = $_POST['workplace_policy']; //
   $job_desc = $_POST['job_desc']; //
   $companyid = $_POST['company']; //
   $job_type = $_POST['job_type']; //
   $career_level = $_POST['career_level']; //
   $experience = $_POST['experience']; //
   $qualification = $_POST['qualification']; //
   $gender = $_POST['gender']; //

   $salary = 'Not Specified';
   if (!empty($_POST['salary'])) {
      $salary = $_POST['salary']; //
   }

   $deadline = $_POST['deadline'];

   // $question = '';



   $query = "UPDATE tbljob SET COMPANYID = '$companyid', WORKPLACE_POLICY = '$workplace_policy', JOBTITLE = '$job_title', JOBCATEGORYID = '$job_categoryid', SALARY = '$salary', JOBTYPE = '$job_type', QUALIFICATION = '$qualification',JOBDESCRIPTION = '$job_desc', PREFEREDSEX = '$gender', CAREERLEVEL = '$career_level',WORKEXPERIENCE = '$experience', DEADLINE = '$deadline' WHERE JOBID = '$id'";

   $result = mysqli_query($con, $query);

   if (($result)) {

      $delete_sql = "DELETE from tbljobscreening_ques where job_id='$id'";
      mysqli_query($con, $delete_sql);

      $query = "SELECT * from tbljob where JOBID ='$id' order by JOBID desc";
      $result = mysqli_query($con, $query);
      $row = mysqli_fetch_array($result);

      // $JOBID = $row['JOBID'];
       $JOBID   = $row['JOBID']  ?? '';



      if (!empty($_POST['question'])) {
         $ques = $_POST['question'];

         /////////////// $c = sizeof($ques); 

         for ($i = 0; $i < sizeof($ques); $i++) {
            // '" . $ques[$i] . "'
            $query = "INSERT INTO tbljobscreening_ques (job_id, question_id) VALUES ('$JOBID','" . $ques[$i] . "')" or die(mysqli_error($con));
            $result = mysqli_query($con, $query);
         }
      }


      echo "<script>alert('Job Updated!')</script>";

      header('location: ./dashboard-manage-jobs.php');
   } else {
      $msg = "<div style='color:red'>Error occured...!</div>";
      echo mysqli_error($con);
   }
}
///////////////Edit Job Ends /////////////////////







// ==================== HANDLE ADD CATEGORY ====================
$categoryid = isset($_GET['categoryid']) ? (int)$_GET['categoryid'] : 0;
$subcategoryid = isset($_GET['subcategoryid']) ? (int)$_GET['subcategoryid'] : 0;

if (isset($_POST['add_category'])) {
    $category = trim($_POST['category']);
    
    if (!empty($category)) {
        // Check if category already exists
        $checkQuery = "SELECT ID FROM tbljobcategory WHERE CATEGORY = ?";
        $stmtCheck = mysqli_prepare($con, $checkQuery);
        mysqli_stmt_bind_param($stmtCheck, "s", $category);
        mysqli_stmt_execute($stmtCheck);
        
        if (mysqli_num_rows(mysqli_stmt_get_result($stmtCheck)) > 0) {
            $_SESSION['error_msg'] = "Category already exists!";
        } else {
            $insertQuery = "INSERT INTO tbljobcategory (CATEGORY) VALUES (?)";
            $stmtInsert = mysqli_prepare($con, $insertQuery);
            mysqli_stmt_bind_param($stmtInsert, "s", $category);
            
            if (mysqli_stmt_execute($stmtInsert)) {
                $_SESSION['success_msg'] = "Category added successfully!";
            } else {
                $_SESSION['error_msg'] = "Failed to add category.";
            }
        }
    }
    
    header("Location: admin-job-category.php");
    exit();
}

// ==================== HANDLE EDIT CATEGORY ====================
if (isset($_POST['edit_category']) && $categoryid > 0) {
    $category = trim($_POST['category']);
    
    if (!empty($category)) {
        // Check if another category with same name exists
        $checkQuery = "SELECT ID FROM tbljobcategory WHERE CATEGORY = ? AND ID != ?";
        $stmtCheck = mysqli_prepare($con, $checkQuery);
        mysqli_stmt_bind_param($stmtCheck, "si", $category, $categoryid);
        mysqli_stmt_execute($stmtCheck);
        
        if (mysqli_num_rows(mysqli_stmt_get_result($stmtCheck)) > 0) {
            $_SESSION['error_msg'] = "Category name already exists!";
        } else {
            $updateQuery = "UPDATE tbljobcategory SET CATEGORY = ? WHERE ID = ?";
            $stmtUpdate = mysqli_prepare($con, $updateQuery);
            mysqli_stmt_bind_param($stmtUpdate, "si", $category, $categoryid);
            
            if (mysqli_stmt_execute($stmtUpdate)) {
                $_SESSION['success_msg'] = "Category updated successfully!";
            } else {
                $_SESSION['error_msg'] = "Failed to update category.";
            }
        }
    }
    
    header("Location: admin-job-category.php");
    exit();
}

// ==================== HANDLE ADD SUBCATEGORY ====================
if (isset($_POST['add_subcategory'])) {
    $categoryIdForSub = (int)$_POST['categoryid'];
    $subcategory = trim($_POST['subcategory']);
    
    if (!empty($subcategory) && $categoryIdForSub > 0) {
        // Check if subcategory already exists in this category
        $checkQuery = "SELECT ID FROM tbljobsubcategory WHERE SUBCATEGORY = ? AND CATEGORYID = ?";
        $stmtCheck = mysqli_prepare($con, $checkQuery);
        mysqli_stmt_bind_param($stmtCheck, "si", $subcategory, $categoryIdForSub);
        mysqli_stmt_execute($stmtCheck);
        
        if (mysqli_num_rows(mysqli_stmt_get_result($stmtCheck)) > 0) {
            $_SESSION['error_msg'] = "Subcategory already exists in this category!";
        } else {
            $insertQuery = "INSERT INTO tbljobsubcategory (CATEGORYID, SUBCATEGORY) VALUES (?, ?)";
            $stmtInsert = mysqli_prepare($con, $insertQuery);
            mysqli_stmt_bind_param($stmtInsert, "is", $categoryIdForSub, $subcategory);
            
            if (mysqli_stmt_execute($stmtInsert)) {
                $_SESSION['success_msg'] = "Subcategory added successfully!";
            } else {
                $_SESSION['error_msg'] = "Failed to add subcategory.";
            }
        }
    }
    
    header("Location: admin-job-category.php");
    exit();
}

// ==================== HANDLE EDIT SUBCATEGORY ====================
if (isset($_POST['edit_subcategory']) && $subcategoryid > 0) {
    $categoryIdForSub = (int)$_POST['categoryid'];
    $subcategory = trim($_POST['subcategory']);
    
    if (!empty($subcategory) && $categoryIdForSub > 0) {
        // Check if another subcategory with same name exists in this category
        $checkQuery = "SELECT ID FROM tbljobsubcategory WHERE SUBCATEGORY = ? AND CATEGORYID = ? AND ID != ?";
        $stmtCheck = mysqli_prepare($con, $checkQuery);
        mysqli_stmt_bind_param($stmtCheck, "sii", $subcategory, $categoryIdForSub, $subcategoryid);
        mysqli_stmt_execute($stmtCheck);
        
        if (mysqli_num_rows(mysqli_stmt_get_result($stmtCheck)) > 0) {
            $_SESSION['error_msg'] = "Subcategory name already exists in this category!";
        } else {
            $updateQuery = "UPDATE tbljobsubcategory SET CATEGORYID = ?, SUBCATEGORY = ? WHERE ID = ?";
            $stmtUpdate = mysqli_prepare($con, $updateQuery);
            mysqli_stmt_bind_param($stmtUpdate, "isi", $categoryIdForSub, $subcategory, $subcategoryid);
            
            if (mysqli_stmt_execute($stmtUpdate)) {
                $_SESSION['success_msg'] = "Subcategory updated successfully!";
            } else {
                $_SESSION['error_msg'] = "Failed to update subcategory.";
            }
        }
    }
    
    header("Location: admin-job-category.php");
    exit();
}
///////////////////////// End Category and Subcategory Management ////////////////////////






////////////////////Download Resume///////////////////////////

if (isset($_POST['download_resume'])) {

   $resume_url = $_POST['resume_url'];

   $filepath = '../' . $resume_url;

   if (file_exists($filepath)) {
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename=' . basename($filepath));
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize('../' . $resume_url));
      readfile('../' . $resume_url);
   }
}
////////////////////End Download Resume///////////////////////////










// ==================== INITIALIZE VARIABLES ====================
$msg = '';
$msgType = '';

// ==================== HANDLE PASSWORD CHANGE ====================
if (isset($_POST['change_password'])) {
    // Get and sanitize inputs
    $oldPassword = mysqli_real_escape_string($con, trim($_POST['oldpassword']));
    $newPassword = mysqli_real_escape_string($con, trim($_POST['newpassword']));
    $confirmPassword = mysqli_real_escape_string($con, trim($_POST['confirmpassword']));
    
    // Validation
    if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
        $msg = 'All fields are required.';
        $msgType = 'error';
    } elseif (strlen($newPassword) < 8) {
        $msg = 'New password must be at least 8 characters long.';
        $msgType = 'error';
    } elseif ($newPassword !== $confirmPassword) {
        $msg = 'New passwords do not match.';
        $msgType = 'error';
    } elseif ($oldPassword === $newPassword) {
        $msg = 'New password must be different from old password.';
        $msgType = 'error';
    } else {
        // Verify old password
        $query = "SELECT PASSWORD FROM tbluser WHERE USERID = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "i", $session_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        
        if ($user && password_verify($oldPassword, $user['PASSWORD'])) {
            // Hash new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // Update password
            $updateQuery = "UPDATE tbluser SET PASSWORD = ?, DATEUPDATED = NOW() WHERE USERID = ?";
            $updateStmt = mysqli_prepare($con, $updateQuery);
            mysqli_stmt_bind_param($updateStmt, "si", $hashedPassword, $session_id);
            
            if (mysqli_stmt_execute($updateStmt)) {
                // Log activity
                $activityQuery = "INSERT INTO tbl_activity_log (USERID, ACTION, DESCRIPTION, CREATED_AT) 
                                 VALUES (?, 'PASSWORD_CHANGE', 'Admin changed password', NOW())";
                $activityStmt = mysqli_prepare($con, $activityQuery);
                mysqli_stmt_bind_param($activityStmt, "i", $session_id);
                mysqli_stmt_execute($activityStmt);
                
                $msg = 'Password changed successfully! Please login again.';
                $msgType = 'success';
                
                // Optional: Force re-login after password change
                // session_destroy();
                // header('location: login.php?msg=password_changed');
            } else {
                $msg = 'Failed to update password. Please try again.';
                $msgType = 'error';
            }
        } else {
            $msg = 'Current password is incorrect.';
            $msgType = 'error';
        }
    }
}






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

//////////////////////////////////////////

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