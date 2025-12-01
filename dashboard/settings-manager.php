<?php
/**
 * MUNext Settings Manager
 * Include this file in your include/phpcode.php to load all settings globally
 * 
 * Usage:
 * - Add "require 'include/settings-manager.php';" in phpcode.php after database connection
 * - Access settings anywhere using $appSettings array
 */

// Initialize global settings array
global $appSettings;
$appSettings = array();

// ==================== LOAD ALL SETTINGS FROM DATABASE ====================
$settingsQuery = "SELECT setting_key, setting_value, setting_description FROM tbl_settings";
$settingsResult = mysqli_query($con, $settingsQuery);

if ($settingsResult && mysqli_num_rows($settingsResult) > 0) {
    while ($row = mysqli_fetch_assoc($settingsResult)) {
        $appSettings[$row['setting_key']] = $row['setting_value'];
    }
}

// ==================== HELPER FUNCTIONS ====================

/**
 * Get a setting value with optional default
 * 
 * @param string $key Setting key
 * @param mixed $default Default value if setting not found
 * @return mixed Setting value or default
 */
function getSetting($key, $default = '') {
    global $appSettings;
    return isset($appSettings[$key]) ? $appSettings[$key] : $default;
}

/**
 * Check if a boolean setting is enabled (value = '1')
 * 
 * @param string $key Setting key
 * @return bool True if enabled, false otherwise
 */
function isSettingEnabled($key) {
    global $appSettings;
    return isset($appSettings[$key]) && $appSettings[$key] == '1';
}

/**
 * Check if maintenance mode is active
 * 
 * @return bool True if maintenance mode is on
 */
function isMaintenanceMode() {
    return isSettingEnabled('maintenance_mode');
}

/**
 * Check if auto-approve jobs is enabled
 * 
 * @return bool True if auto-approve is on
 */
function autoApproveJobs() {
    return isSettingEnabled('auto_approve_jobs');
}

/**
 * Check if auto-approve companies is enabled
 * 
 * @return bool True if auto-approve is on
 */
function autoApproveCompanies() {
    return isSettingEnabled('auto_approve_companies');
}

/**
 * Get job status based on auto-approve setting
 * 
 * @return string 'Active' or 'Pending'
 */
function getNewJobStatus() {
    return autoApproveJobs() ? 'Active' : 'Pending';
}

/**
 * Get company status based on auto-approve setting
 * 
 * @return string 'Active' or 'Pending'
 */
function getNewCompanyStatus() {
    return autoApproveCompanies() ? 'Active' : 'Pending';
}

/**
 * Get site name
 * 
 * @return string Site name
 */
function getSiteName() {
    return getSetting('site_name', 'MUNext');
}

/**
 * Get site email
 * 
 * @return string Site email
 */
function getSiteEmail() {
    return getSetting('site_email', 'info@munext.com');
}

/**
 * Refresh settings from database (call after updating settings)
 */
function refreshSettings() {
    global $con, $appSettings;
    
    $appSettings = array();
    $settingsQuery = "SELECT setting_key, setting_value FROM tbl_settings";
    $settingsResult = mysqli_query($con, $settingsQuery);
    
    if ($settingsResult && mysqli_num_rows($settingsResult) > 0) {
        while ($row = mysqli_fetch_assoc($settingsResult)) {
            $appSettings[$row['setting_key']] = $row['setting_value'];
        }
    }
}

// ==================== MAINTENANCE MODE CHECK ====================
// Automatically redirect to maintenance page if maintenance mode is enabled
// (You can customize this to exclude admin users)

if (isMaintenanceMode()) {
    // Get current page
    $currentPage = basename($_SERVER['PHP_SELF']);
    
    // Pages that are allowed during maintenance
    $allowedPages = array('maintenance.php', 'login.php', 'logout.php');
    
    // Check if user is admin (customize this check based on your auth system)
    $isAdmin = false;
    if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        $isAdmin = true;
    }
    
    // Redirect to maintenance page if not admin and not on allowed page
    if (!$isAdmin && !in_array($currentPage, $allowedPages)) {
        // Only redirect if maintenance.php exists
        if (file_exists('maintenance.php')) {
            header("Location: ./maintenance.php");
            exit();
        }
    }
}





// ==================== DEBUG MODE (Optional) ====================
// Uncomment to see all loaded settings (remove in production)
/*
if (isset($_GET['debug_settings']) && $_GET['debug_settings'] == '1') {
    echo "<pre>";
    echo "Loaded Settings:\n";
    print_r($appSettings);
    echo "</pre>";
}
*/

?>