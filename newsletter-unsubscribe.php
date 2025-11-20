<?php
session_start();
require('./mysqli_connect.php');

$message = '';
$success = false;

if (isset($_GET['email']) && !empty($_GET['email'])) {
    $email = mysqli_real_escape_string($con, $_GET['email']);
    
    $query = "UPDATE tblnewsletter SET STATUS = 'Unsubscribed', UNSUBSCRIBE_DATE = NOW() WHERE EMAIL = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    
    if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
        $message = 'You have been successfully unsubscribed from our newsletter.';
        $success = true;
    } else {
        $message = 'Email not found or already unsubscribed.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsubscribe - MUNext</title>
    <link href="assets/css/styles.css" rel="stylesheet">
    <style>
    .unsubscribe-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        padding: 20px;
    }

    .unsubscribe-box {
        background: white;
        padding: 50px;
        border-radius: 15px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
        max-width: 500px;
        text-align: center;
    }
    </style>
</head>

<body>
    <div class="unsubscribe-container">
        <div class="unsubscribe-box">
            <h2><?php echo $success ? 'Unsubscribed' : 'Error'; ?></h2>
            <p><?php echo $message; ?></p>
            <?php if ($success): ?>
            <p class="mt-3">We're sorry to see you go. You can resubscribe anytime from our homepage.</p>
            <?php endif; ?>
            <a href="index.php" class="btn btn-primary mt-4">Go to Homepage</a>
        </div>
    </div>
</body>

</html>