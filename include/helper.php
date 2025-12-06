<?php

include "include/toast.php";
 date_default_timezone_set('America/St_Johns');
 require_once "include/email-functions.php";


$query = mysqli_query($con, "SELECT * FROM tblusers WHERE USERID = '$session_id'") or die(mysqli_error($con));
$row = mysqli_fetch_array($query);

$USERID = isset($row['USERID']) ? $row['USERID'] : '';
// $FNAME = isset($row['FNAME']) ? $row['FNAME'] : '';
// $ONAME = isset($row['ONAME']) ? $row['ONAME'] : '';
$EMAIL = isset($row['EMAIL']) ? $row['EMAIL'] : '';
$USERNAME = isset($row['USERNAME']) ? $row['USERNAME'] : '';
$PASS = isset($row['PASS']) ? $row['PASS'] : '';
$ROLE = isset($row['ROLE']) ? $row['ROLE'] : '';
// $PICLOCATION = isset($row['PICLOCATION']) ? $row['PICLOCATION'] : '';


$queryuser = "SELECT * from tblapplicants WHERE USERID = '$USERID'";
$resultuser = mysqli_query($con, $queryuser);
$rowuser = mysqli_fetch_array($resultuser);

$APPLICANTID = isset($rowuser['APPLICANTID']) ? $rowuser['APPLICANTID'] : '';
$FNAME = isset($rowuser['FNAME']) ? $rowuser['FNAME'] : '';
$ONAME = isset($rowuser['OTHERNAMES']) ? $rowuser['OTHERNAMES'] : '';
$APPLICANTPHOTO = isset($rowuser['APPLICANTPHOTO']) ? $rowuser['APPLICANTPHOTO'] : '';
$JOBCATEGORYID = isset($rowuser['JOBCATEGORYID']) ? $rowuser['JOBCATEGORYID'] : '';
$JOBTITLE = isset($rowuser['JOBTITLE']) ? $rowuser['JOBTITLE'] : '';
$EXCOMPANYNAME = isset($rowuser['EXCOMPANYNAME']) ? $rowuser['EXCOMPANYNAME'] : '';
$EXJOBTITLE = isset($rowuser['EXJOBTITLE']) ? $rowuser['EXJOBTITLE'] : '';
$ABOUTME = isset($rowuser['ABOUTME']) ? $rowuser['ABOUTME'] : '';
$ADDRESS = isset($rowuser['FULLADDRESS']) ? $rowuser['FULLADDRESS'] : '';
$COUNTRY = isset($rowuser['COUNTRY']) ? $rowuser['COUNTRY'] : '';
$CITY = isset($rowuser['CITY']) ? $rowuser['CITY'] : '';
$SEX = isset($rowuser['SEX']) ? $rowuser['SEX'] : '';
$BIRTHDATE = isset($rowuser['BIRTHDATE']) ? $rowuser['BIRTHDATE'] : '';
$CONTACTNO = isset($rowuser['CONTACTNO']) ? $rowuser['CONTACTNO'] : '';
$DEGREE = isset($rowuser['DEGREE']) ? $rowuser['DEGREE'] : '';
$SCHOOLNAME = isset($rowuser['SCHOOLNAME']) ? $rowuser['SCHOOLNAME'] : '';
$SKILLS = isset($rowuser['SKILLS']) ? $rowuser['SKILLS'] : '';
$FB_link = isset($rowuser['FB_link']) ? $rowuser['FB_link'] : '';
$LinkedIn_link = isset($rowuser['LinkedIn_link']) ? $rowuser['LinkedIn_link'] : '';
// $CONTACTNO = isset($rowuser['CONTACTNO']) ? $rowuser['CONTACTNO'] : '';
$FULLNAME = $FNAME . ' ' . $ONAME;

$queryuser = "SELECT * from tbljobsubcategory WHERE ID = '$JOBCATEGORYID'";
$resultuser = mysqli_query($con, $queryuser);
$rowuser = mysqli_fetch_array($resultuser);

$SUBCATEGORY = isset($rowuser['SUBCATEGORY']) ? $rowuser['SUBCATEGORY'] : '';

// ...existing code...










/////////////////// login Functions //////////////////////////////

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









$Lerror = '';
$error = '';
$errorActv = '';

// ==================== LOGIN FUNCTIONALITY ====================
if (isset($_POST['login_btn'])) {
    $username = validate_input_text($_POST['username']);
    $password = validate_input_text($_POST['password']);
    
    // Validation
    if (empty($username)) {
        $Lerror = "Please enter your Username";
        Toast::error($Lerror);
    } elseif (empty($password)) {
        $Lerror = "Please enter your password";
        Toast::error($Lerror);
    } else {
        // Check user credentials
        $query = "SELECT * FROM tblusers WHERE USERNAME = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            
            // Verify password
            if (password_verify($password, $row['PASS'])) {
                // Check user status
                if ($row['USERSTATUS'] != 'Active') {
                    // Account is not active
                    $statusMessage = '';
                    switch ($row['USERSTATUS']) {
                        case 'Pending':
                            $statusMessage = "Your account is pending approval. Please wait for admin verification.";
                            break;
                        case 'Suspended':
                            $statusMessage = "Your account has been suspended. Please contact support for assistance.";
                            break;
                        case 'Inactive':
                            $statusMessage = "Your account is inactive. Please contact support to reactivate.";
                            break;
                        default:
                            $statusMessage = "Your account status is: " . $row['USERSTATUS'] . ". Please contact support.";
                    }
                    
                    // Record failed login attempt due to status
                    recordLoginHistory(
                        $con, 
                        $row['USERID'], 
                        $row['USERNAME'], 
                        $row['EMAIL'], 
                        $row['ROLE'], 
                        'Blocked', 
                        'Account status: ' . $row['USERSTATUS']
                    );
                    
                    $Lerror = $statusMessage;
                    Toast::error($statusMessage);
                } else {
                    // Check for suspicious activity
                    $suspiciousCheck = checkSuspiciousActivity($con, $row['USERID']);
                    
                    if ($suspiciousCheck['is_suspicious']) {
                        // Log failed attempt due to suspicious activity
                        recordLoginHistory(
                            $con, 
                            $row['USERID'], 
                            $row['USERNAME'], 
                            $row['EMAIL'], 
                            $row['ROLE'], 
                            'Blocked', 
                            $suspiciousCheck['reason']
                        );
                        
                        $Lerror = "Login blocked due to suspicious activity. Please contact support.";
                        Toast::error($Lerror);
                    } else {
                        // Successful login
                        session_regenerate_id(true); // Prevent session fixation
                        
                        $_SESSION['userID'] = $row['USERID'];
                        $_SESSION['loggedin_time'] = time();
                        $_SESSION['role'] = $row['ROLE'];
                        $_SESSION['username'] = $row['USERNAME'];
                        
                        // Record successful login
                        recordLoginHistory(
                            $con, 
                            $row['USERID'], 
                            $row['USERNAME'], 
                            $row['EMAIL'], 
                            $row['ROLE'], 
                            'Success'
                        );
                        
                        // logEmail($con, $email, $templateName, $subject, $status);

                        Toast::success("Login Successful! Redirecting...");
                        
                        // Redirect based on role
                        if ($row['ROLE'] == "Admin") {
                            echo "<script>
                                setTimeout(function() {
                                    window.location.href = './admin/';
                                }, 1500);
                            </script>";
                            exit();
                        } else {
                            // Check for redirect URL
                            if (!empty($_POST['url'])) {
                                $url = $_POST['url'];
                                echo "<script>
                                    setTimeout(function() {
                                        window.location.href = './$url';
                                    }, 1500);
                                </script>";
                            } else {
                                echo "<script>
                                    setTimeout(function() {
                                        window.location.href = './dashboard/';
                                    }, 1500);
                                </script>";
                            }
                            exit();
                        }
                    }
                }
            } else {
                // Failed login - wrong password
                recordLoginHistory(
                    $con, 
                    $row['USERID'], 
                    $row['USERNAME'], 
                    $row['EMAIL'], 
                    $row['ROLE'], 
                    'Failed', 
                    'Incorrect password'
                );
                
                $Lerror = "<div style='color:red'>Incorrect Username or Password!</div>";
                Toast::error("Incorrect Username or Password!");
            }
        } else {
            // User not found
            recordLoginHistory(
                $con, 
                0, 
                $username, 
                'Unknown', 
                'Unknown', 
                'Failed', 
                'User not found'
            );
            
            $Lerror = "<div style='color:red'>Username does not exist!</div>";
            Toast::error("Username does not exist!");
        }
        
        mysqli_stmt_close($stmt);
    }
}
/* ======================= LOGIN FUNCTIONALITY ENDS ===========*/



// ==================== REGISTRATION FUNCTIONALITY ====================
if (isset($_POST['register_btn'])) {
    $role = validate_input_text($_POST['role']);
    
    // Check if registration is allowed for this role
    if ($role == 'Applicant' && !$userRegAllowed) {
        $alertText = "<div class='alert alert-danger'>User registration is currently disabled. Please contact support.</div>";
        Toast::error("User registration is currently disabled!");
        goto skip_registration;
    }
    
    if ($role == 'Employer' && !$compRegAllowed) {
        $alertText = "<div class='alert alert-danger'>Employer registration is currently disabled. Please contact support.</div>";
        Toast::error("Employer registration is currently disabled!");
        goto skip_registration;
    }
    
    // Validate inputs
    $firstName = validate_input_text($_POST['Fname']);
    if (empty($firstName)) {
        $alertText = "Please enter your first name";
        Toast::error($alertText);
        goto skip_registration;
    }
    
    $lastName = validate_input_text($_POST['Lname']);
    if (empty($lastName)) {
        $alertText = "Please enter your last name";
        Toast::error($alertText);
        goto skip_registration;
    }
    
    $username = validate_input_text($_POST['username']);
    if (empty($username)) {
        $alertText = "Please enter a username";
        Toast::error($alertText);
        goto skip_registration;
    }
    
    $email = validate_input_email($_POST['email']);
    if (empty($email)) {
        $alertText = "Please enter a valid email address";
        Toast::error($alertText);
        goto skip_registration;
    }
    
    $password = validate_input_text($_POST['password']);
    if (empty($password)) {
        $alertText = "Please enter a password";
        Toast::error($alertText);
        goto skip_registration;
    }
    
    if (strlen($password) < 6) {
        $alertText = "Password must be at least 6 characters long";
        Toast::error($alertText);
        goto skip_registration;
    }
    
    $confirm_pwd = validate_input_text($_POST['cpassword']);
    if ($password != $confirm_pwd) {
        $alertText = "Passwords do not match!";
        Toast::error($alertText);
        goto skip_registration;
    }
    
    // Check if email already exists
    $checkEmailQuery = "SELECT USERID FROM tblusers WHERE EMAIL = ?";
    $stmtEmail = mysqli_prepare($con, $checkEmailQuery);
    mysqli_stmt_bind_param($stmtEmail, "s", $email);
    mysqli_stmt_execute($stmtEmail);
    $emailResult = mysqli_stmt_get_result($stmtEmail);
    
    if (mysqli_num_rows($emailResult) > 0) {
        $alertText = "<div style='color:red'>Email address is already registered!</div>";
        Toast::error("Email address is already registered!");
        mysqli_stmt_close($stmtEmail);
        goto skip_registration;
    }
    mysqli_stmt_close($stmtEmail);
    
    // Check if username already exists
    $checkUserQuery = "SELECT USERID FROM tblusers WHERE USERNAME = ?";
    $stmtUser = mysqli_prepare($con, $checkUserQuery);
    mysqli_stmt_bind_param($stmtUser, "s", $username);
    mysqli_stmt_execute($stmtUser);
    $userResult = mysqli_stmt_get_result($stmtUser);
    
    if (mysqli_num_rows($userResult) > 0) {
        $alertText = "<div style='color:red'>Username is already taken!</div>";
        Toast::error("Username is already taken!");
        mysqli_stmt_close($stmtUser);
        goto skip_registration;
    }
    mysqli_stmt_close($stmtUser);
    
    // Hash password
    $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
    
    // Determine user status based on role and settings
    $userStatus = 'Active'; // Default for applicants
    
    if ($role == 'Employer') {
        // For employers, check auto-approve setting
        $userStatus = $autoApproveCompanies ? 'Active' : 'Pending';
    }
    
    // Handle admin role
    if (!empty($_POST['admin'])) {
        $admin = validate_input_text($_POST['admin']);
        $role = $admin;
        $userStatus = 'Pending'; // Admins always need approval
    }
    
    // Insert new user
    $insertQuery = "INSERT INTO tblusers (FNAME, ONAME, EMAIL, USERNAME, PASS, ROLE, USERSTATUS, DATECREATED) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmtInsert = mysqli_prepare($con, $insertQuery);
    mysqli_stmt_bind_param($stmtInsert, "sssssss", $firstName, $lastName, $email, $username, $hashed_pass, $role, $userStatus);
    
    if (mysqli_stmt_execute($stmtInsert)) {
        $newUserId = mysqli_insert_id($con);
        
        // Send welcome email
        $fullName = $firstName . ' ' . $lastName;
        $emailSent = sendWelcomeEmail($con, $email, $fullName, $role);
        
        // Success message based on status
        if ($userStatus == 'Pending') {
            $alertText = '<div class="alert alert-success">Account created successfully! Your account is pending approval. You will receive an email once approved.</div>';
            Toast::success("Account created! Pending approval.");
        } else {
            $alertText = '<div class="alert alert-success">Account created successfully! You can now log in.</div>';
            Toast::success("Account created successfully!");
        }
        
        if (!$emailSent) {
            error_log("Failed to send welcome email to: {$email}");
        }
        
        // Redirect to login page after 3 seconds
        echo "<script>
            setTimeout(function() {
                window.location.href = 'login.php?chk=successful';
            }, 3000);
        </script>";
        
    } else {
        $alertText = "<div style='color:red'>Error while creating account. Please try again.</div>";
        Toast::error("Error while creating account!");
        error_log("Registration error: " . mysqli_error($con));
    }
    
    mysqli_stmt_close($stmtInsert);
    
    skip_registration:
}
/* ======================= REGISTRATION FUNCTIONALITY ENDS ===========*/














////////////////////Apply Job//////////////////////////////

// Check if user already applied
$checkAppliedQuery = "SELECT ID FROM tbljobapplication WHERE JOBID = ? AND APPLICANTID = ?";
$stmtCheck = mysqli_prepare($con, $checkAppliedQuery);
mysqli_stmt_bind_param($stmtCheck, "ii", $jobId, $userId);
mysqli_stmt_execute($stmtCheck);
$checkResult = mysqli_stmt_get_result($stmtCheck);

if (mysqli_num_rows($checkResult) > 0) {
    $_SESSION['error_msg'] = "You have already applied for this job.";
    header("Location: job-detail.php?jobid=$jobId");
    exit();
}

// Get user profile information
$userQuery = "SELECT * FROM tblusers WHERE USERID = ?";
$stmtUser = mysqli_prepare($con, $userQuery);
mysqli_stmt_bind_param($stmtUser, "i", $userId);
mysqli_stmt_execute($stmtUser);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtUser));

// Get screening questions
$questionsQuery = "SELECT * FROM tblscreeningquestions WHERE JOBID = ? ORDER BY QUESTIONID ASC";
$stmtQuestions = mysqli_prepare($con, $questionsQuery);
mysqli_stmt_bind_param($stmtQuestions, "i", $jobId);
mysqli_stmt_execute($stmtQuestions);
$questions = mysqli_stmt_get_result($stmtQuestions);
$totalQuestions = mysqli_num_rows($questions);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_application'])) {
    $errors = [];
    $resumeFileName = null;
    
    // Validate required screening questions
    if ($totalQuestions > 0) {
        mysqli_data_seek($questions, 0);
        while ($q = mysqli_fetch_assoc($questions)) {
            if ($q['ISREQUIRED']) {
                $questionId = $q['QUESTIONID'];
                if (!isset($_POST['answers'][$questionId]) || trim($_POST['answers'][$questionId]) === '') {
                    $errors[] = "Please answer all required questions.";
                    break;
                }
            }
        }
    }
    
    // Handle Resume Upload (Optional)
    if (isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']['error'] != UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['fileToUpload'];
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Error uploading file. Please try again.";
        } else {
            // Validate file
            $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain', 'application/rtf'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            
            $fileType = $file['type'];
            $fileSize = $file['size'];
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            // Validate file type
            if (!in_array($fileType, $allowedTypes) && !in_array($fileExtension, ['pdf', 'doc', 'docx', 'txt', 'rtf'])) {
                $errors[] = "Invalid file type. Please upload PDF, DOC, DOCX, TXT, or RTF files only.";
            }
            
            // Validate file size
            if ($fileSize > $maxSize) {
                $errors[] = "File size exceeds 5MB limit.";
            }
            
            // If validation passed, prepare to save file
            if (empty($errors)) {
                // Create upload directory if it doesn't exist
                $uploadDir = './resumes_cv/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // Generate unique filename
                $resumeFileName = 'resume_' . $userId . '_' . $jobId . '_' . time() . '.' . $fileExtension;
                $uploadPath = $uploadDir . $resumeFileName;
                
                // Move uploaded file
                if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    $errors[] = "Failed to save uploaded file.";
                    $resumeFileName = null;
                }
            }
        }
    }
    
    // Get cover letter
    $coverLetter = isset($_POST['cover_letter']) ? trim($_POST['cover_letter']) : null;
    
    // If no errors, proceed with database insertion
    if (empty($errors)) {
        mysqli_begin_transaction($con);
        
        try {
            // Insert job application
            $insertAppQuery = "INSERT INTO tbljobapplication 
                              (JOBID, APPLICANTID, DATEAPPLIED, APPLICATIONSTATUS, RESUME, COVERLETTER) 
                              VALUES (?, ?, NOW(), 'Pending', ?, ?)";
            $stmtApp = mysqli_prepare($con, $insertAppQuery);
            mysqli_stmt_bind_param($stmtApp, "iiss", $jobId, $userId, $resumeFileName, $coverLetter);
            
            if (!mysqli_stmt_execute($stmtApp)) {
                throw new Exception("Failed to insert application: " . mysqli_error($con));
            }
            
            $applicationId = mysqli_insert_id($con);
            
            // Calculate screening score
            $totalScore = 0;
            $maxScore = 0;
            
            // Insert screening answers if there are questions
            if ($totalQuestions > 0 && isset($_POST['answers'])) {
                mysqli_data_seek($questions, 0);
                
                while ($q = mysqli_fetch_assoc($questions)) {
                    $questionId = $q['QUESTIONID'];
                    $answer = isset($_POST['answers'][$questionId]) ? trim($_POST['answers'][$questionId]) : '';
                    
                    // Skip if no answer provided (for optional questions)
                    if ($answer === '') continue;
                    
                    // Calculate score for this answer
                    $answerScore = 0;
                    $maxScore += $q['POINTS'];
                    
                    if ($q['CORRECTANSWER'] && $answer) {
                        $correctAnswer = strtolower(trim($q['CORRECTANSWER']));
                        $userAnswer = strtolower(trim($answer));
                        
                        if ($q['QUESTIONTYPE'] == 'yes_no') {
                            if ($userAnswer == $correctAnswer) {
                                $answerScore = $q['POINTS'];
                            }
                        } elseif ($q['QUESTIONTYPE'] == 'number') {
                            if (is_numeric($answer) && is_numeric($q['CORRECTANSWER'])) {
                                if ((float)$answer >= (float)$q['CORRECTANSWER']) {
                                    $answerScore = $q['POINTS'];
                                }
                            }
                        } else {
                            if (strpos($userAnswer, $correctAnswer) !== false || $userAnswer == $correctAnswer) {
                                $answerScore = $q['POINTS'];
                            }
                        }
                    }
                    
                    $totalScore += $answerScore;
                    
                    // Insert answer
                    $insertAnsQuery = "INSERT INTO tblscreeninganswers 
                                      (APPLICATIONID, QUESTIONID, USERID, JOBID, ANSWER, SCORE) 
                                      VALUES (?, ?, ?, ?, ?, ?)";
                    $stmtAns = mysqli_prepare($con, $insertAnsQuery);
                    mysqli_stmt_bind_param($stmtAns, "iiiisi", $applicationId, $questionId, 
                                           $userId, $jobId, $answer, $answerScore);
                    
                    if (!mysqli_stmt_execute($stmtAns)) {
                        throw new Exception("Failed to insert answer: " . mysqli_error($con));
                    }
                }
                
                // Update application with screening score
                $screenPassed = ($maxScore > 0 && ($totalScore / $maxScore) >= 0.5) ? 1 : 0;
                
                $updateAppQuery = "UPDATE tbljobapplication 
                                  SET SCREENINGSCORE = ?, SCREENPASSED = ? 
                                  WHERE ID = ?";
                $stmtUpdate = mysqli_prepare($con, $updateAppQuery);
                mysqli_stmt_bind_param($stmtUpdate, "iii", $totalScore, $screenPassed, $applicationId);
                
                if (!mysqli_stmt_execute($stmtUpdate)) {
                    throw new Exception("Failed to update screening score: " . mysqli_error($con));
                }
            }
            
            // Commit transaction
            mysqli_commit($con);


             // Get employer details for email
            $detailsQuery = "SELECT 
                j.*, 
                u.EMAIL, u.FNAME, u.ONAME, c.COMPANYNAME 
                FROM tbljob j
                LEFT JOIN tblusers u ON j.EMPLOYERID = u.USERID
                LEFT JOIN tblcompany c ON j.EMPLOYERID = c.USERID
                WHERE j.JOBID = ?";
            $detailsStmt = mysqli_prepare($con, $detailsQuery);
            mysqli_stmt_bind_param($detailsStmt, "i", $jobid);
            mysqli_stmt_execute($detailsStmt);
            $detailsResult = mysqli_stmt_get_result($detailsStmt);
                    
            if ($details = mysqli_fetch_assoc($detailsResult)) {
                $employerName = $details['FNAME'] . ' ' . $details['ONAME'];
                $companyName = $details['COMPANYNAME'] ?? 'Your Company';
                $companyEmail = $details['EMAIL'] ?? $details['COMPANYEMAIL'];
                $jobTitle = $details['JOBTITLE'] ?? 'Job Title';
                
                 // Send email notification to employer 
                sendNewApplicationEmail($con, $companyEmail, $employerName, $applicantName, $jobTitle, $applicationId);
            }

            // Get applicant details for email
            $detailsQuery = "SELECT *
                FROM tblusers 
                WHERE USERID = ?";
            $detailsStmt = mysqli_prepare($con, $detailsQuery);
            mysqli_stmt_bind_param($detailsStmt, "i", $userId);
            mysqli_stmt_execute($detailsStmt);
            $detailsResult = mysqli_stmt_get_result($detailsStmt);
                    
            if ($details = mysqli_fetch_assoc($detailsResult)) {
                $applicantName = $details['FNAME'] . ' ' . $details['ONAME'];
                $applicantEmail = $details['EMAIL'] ?? '';

                 // Send job application confirmation to Applicant
                sendApplicationConfirmationEmail($con, $applicantEmail, $applicantName, $jobTitle, $companyName);
            }

            
            $_SESSION['success_msg'] = "Application submitted successfully! The employer will review your application.";
            header("Location: dashboard/applicant/dashboard-applied-jobs.php");
            exit();
            
        } catch (Exception $e) {
            mysqli_rollback($con);
            
            // Delete uploaded file if transaction failed
            if ($resumeFileName && file_exists($uploadDir . $resumeFileName)) {
                unlink($uploadDir . $resumeFileName);
            }
            
            $errors[] = "Error submitting application: " . $e->getMessage();
        }
    }
    
    if (!empty($errors)) {
        $_SESSION['error_msg'] = implode('<br>', $errors);
    }
}

// Reset pointer for display
if ($totalQuestions > 0) {
    mysqli_data_seek($questions, 0);
}





// Helper functions
function getVal($field) {
    global $user;
    if (isset($row[$field]) && $row[$field] !== '') return $row[$field];
    if (isset($GLOBALS[$field]) && $GLOBALS[$field] !== '') return $GLOBALS[$field];
    return '';
}

function esc($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

$name = trim(getVal('FNAME') . ' ' . getVal('ONAME'));
$email = getVal('EMAIL');
$phone = getVal('CONTACTNO');
$country = getVal('COUNTRY');
$city = getVal('CITY');
$address = getVal('ADDRESS');
$about = getVal('ABOUTME');
$degree = getVal('DEGREE');
$school = getVal('SCHOOLNAME');
$exCompany = getVal('EXCOMPANYNAME');
$exTitle = getVal('EXJOBTITLE');
$skills = getVal('SKILLS');
$fb = getVal('FB_link');
$linkedin = getVal('LinkedIn_link');

/////////////////////End Apply Jobs///////////////////////





























////////////////////////Save/Bookmark Job..../////////////////

if (isset($_POST['save_job'])) {

    $jobID = $_POST['jobID'];
    $userID = $_POST['userID'];



    $query = "INSERT INTO tblbookmarkjob (APPLICANTID, JOBID, DATETIME) VALUES ('$userID','$jobID', now())" or die(mysqli_error($con));

    $result = mysqli_query($con, $query);


    if ($result) {

        ?>
<script>
// alert('Job Saved!');
// location.href = "./dashboard-applied-jobs.php";
</script>
<?php

        //   header('location: ./dashboard-add-profile.php#section456');
    } else {
        $msg2 = "<div style='color:red'>Error occured...!</div>";
        echo mysqli_error($con);
    }
}
////////////////////////Save/Bookmark Job..../////////////////






















function validate_input_text($textValue)
{
    if (!empty($textValue)) {
        $trim_text = trim($textValue);
        // remove HTML tags and encode special characters to prevent XSS
        $no_tags = strip_tags($trim_text);
        $sanitize_str = htmlspecialchars($no_tags, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        return $sanitize_str;
    }
    return '';
}

function validate_input_email($emailValue)
{
    if (!empty($emailValue)) {
        $trim_text = trim($emailValue);
        // remove illegal character
        $sanitize_str = filter_var($trim_text, FILTER_SANITIZE_EMAIL);
        return $sanitize_str;
    }
    return '';
}

// profile image
function upload_profile($path, $file)
{
    $targetDir = $path;
    $default = "beard.png";

    // get the filename
    $filename = basename($file['name']);
    $targetFilePath = $targetDir . $filename;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    if (!empty($filename)) {
        // allow certain file format
        $allowType = array('jpg', 'png', 'jpeg', 'gif', 'pdf');
        if (in_array($fileType, $allowType)) {
            // upload file to the server
            if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
                return $targetFilePath;
            }
        }
    }

    // return default image
    return $path . $default;
}


// get user info
function get_user_info($con, $userID)
{
    $query = "SELECT firstName, lastName, email, profileImage FROM user WHERE userID=?";
    $q = mysqli_stmt_init($con);

    mysqli_stmt_prepare($q, $query);

    // bind the statement
    mysqli_stmt_bind_param($q, 'i', $userID);

    // execute sql statement
    mysqli_stmt_execute($q);
    $result = mysqli_stmt_get_result($q);

    $row = mysqli_fetch_array($result);
    return empty($row) ? false : $row;
}

?>