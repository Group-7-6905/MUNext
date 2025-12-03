<!DOCTYPE html>
<html lang="en">

<?php
// Start session and connect to database
session_start();
require('./mysqli_connect.php');

include 'dashboard/settings-manager.php';

if (!isMaintenanceMode()) {
    header("Location: ./index.php");
}


// Get maintenance message from settings
$maintenanceMessage = 'We are currently performing scheduled maintenance. We will be back shortly.';
$estimatedTime = '';

$settingsQuery = "SELECT setting_key, setting_value FROM tbl_settings WHERE setting_key IN ('maintenance_message', 'maintenance_end_time')";
$settingsResult = mysqli_query($con, $settingsQuery);

if ($settingsResult) {
    while ($row = mysqli_fetch_assoc($settingsResult)) {
        if ($row['setting_key'] == 'maintenance_message' && !empty($row['setting_value'])) {
            $maintenanceMessage = $row['setting_value'];
        }
        if ($row['setting_key'] == 'maintenance_end_time' && !empty($row['setting_value'])) {
            $estimatedTime = $row['setting_value'];
        }
    }
}

// Get site name
$siteName = 'MUNext';
$siteQuery = "SELECT setting_value FROM tbl_settings WHERE setting_key = 'site_name'";
$siteResult = mysqli_query($con, $siteQuery);
if ($siteResult && $row = mysqli_fetch_assoc($siteResult)) {
    if (!empty($row['setting_value'])) {
        $siteName = $row['setting_value'];
    }
}
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Maintenance - <?php echo htmlspecialchars($siteName); ?></title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/img/favicon.png">

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="stylesheet" href="assets/css/main.css">
</head>

<body class="body-main">
    <!-- Animated Background -->
    <div class="animated-bg">
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
    </div>

    <!-- Main Container -->
    <div class="maintenance-container">
        <!-- Icon -->
        <div class="maintenance-icon">
            <i class="fas fa-tools"></i>
        </div>

        <!-- Title -->
        <h1 class="maintenance-title">We'll Be Right Back!</h1>

        <!-- Message -->
        <p class="maintenance-message">
            <?php echo nl2br(htmlspecialchars($maintenanceMessage)); ?>
        </p>

        <!-- Progress Bar -->
        <div class="progress-bar">
            <div class="progress-fill"></div>
        </div>

        <!-- Time Estimate (if available) -->
        <?php if (!empty($estimatedTime)): ?>
        <div class="time-estimate">
            <div class="time-estimate-label">Estimated Back Online</div>
            <div class="time-estimate-value">
                <i class="far fa-clock"></i> <?php echo htmlspecialchars($estimatedTime); ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Loader -->
        <div class="loader-container">
            <div class="loader"></div>
            <div class="loader-text">Working on improvements...</div>
        </div>

        <!-- Contact Info -->
        <div class="contact-info">
            <div class="contact-title">Need Immediate Assistance?</div>
            <div class="contact-links">
                <a href="mailto:support@munext.com" class="contact-link">
                    <i class="fas fa-envelope"></i>
                    Email Us
                </a>
                <a href="tel:+1234567890" class="contact-link">
                    <i class="fas fa-phone"></i>
                    Call Support
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="maintenance-footer">
        &copy; <?php echo date('Y'); ?> <a href="index.php"><?php echo htmlspecialchars($siteName); ?></a>. All rights
        reserved.
    </div>

    <!-- Auto-refresh script (check every 30 seconds if maintenance mode is off) -->
    <script>
    // Auto-refresh every 30 seconds to check if site is back online
    setTimeout(function() {
        location.reload();
    }, 30000);

    // Optional: Add a countdown timer if estimated time is provided
    <?php if (!empty($estimatedTime)): ?>
    // You can add countdown logic here if needed
    <?php endif; ?>
    </script>
</body>

</html>