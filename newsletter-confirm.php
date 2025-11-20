<?php
session_start();
require('./mysqli_connect.php');

$message = '';
$success = false;

if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = mysqli_real_escape_string($con, $_GET['token']);
    
    // Find the subscription
    $query = "SELECT ID, EMAIL, IS_VERIFIED FROM tblnewsletter WHERE VERIFICATION_TOKEN = ? AND STATUS = 'Active'";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        if ($row['IS_VERIFIED'] == 1) {
            $message = 'Your subscription is already confirmed!';
            $success = true;
        } else {
            // Verify the subscription
            $updateQuery = "UPDATE tblnewsletter SET IS_VERIFIED = 1, VERIFICATION_TOKEN = NULL WHERE ID = ?";
            $stmtUpdate = mysqli_prepare($con, $updateQuery);
            mysqli_stmt_bind_param($stmtUpdate, "i", $row['ID']);
            
            if (mysqli_stmt_execute($stmtUpdate)) {
                $message = 'Thank you! Your subscription has been confirmed successfully.';
                $success = true;
                
                //  Send welcome email
                sendUnsubcriptionEmail($row['EMAIL']);
                
            } else {
                $message = 'An error occurred. Please try again.';
            }
        }
    } else {
        $message = 'Invalid or expired confirmation link.';
    }
} else {
    $message = 'Invalid confirmation link.';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter Confirmation - MUNext</title>
    <link href="assets/css/styles.css" rel="stylesheet">
    <style>
    .confirmation-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 20px;
    }

    .confirmation-box {
        background: white;
        padding: 50px;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        max-width: 500px;
        text-align: center;
    }

    .icon-box {
        width: 80px;
        height: 80px;
        margin: 0 auto 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
    }

    .success {
        background: #d4edda;
        color: #155724;
    }

    .error {
        background: #f8d7da;
        color: #721c24;
    }
    </style>
</head>

<body>
    <div class="confirmation-container">
        <div class="confirmation-box">
            <div class="icon-box <?php echo $success ? 'success' : 'error'; ?>">
                <?php echo $success ? '✓' : '✗'; ?>
            </div>
            <h2><?php echo $success ? 'Success!' : 'Oops!'; ?></h2>
            <p><?php echo $message; ?></p>
            <a href="index.php" class="btn btn-primary mt-4">Go to Homepage</a>
        </div>
    </div>
</body>

</html>