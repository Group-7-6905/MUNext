<?php
session_start();

// Pull role from session (adjust key if your app stores it elsewhere)
$role = $_SESSION['role'] ?? null;
// Determine target based on role

if ($role === 'Applicant') {
    $target = './applicant/';
} elseif ($role === 'Employer') {
    $target = './employer/';
} else {
    // fallback if no role found (adjust to your login/home page)
    $target = '../login.php';
}

// Preferred: PHP header redirect (must run before output)
if (!headers_sent()) {
    header('Location: ' . $target, true, 302);
    // exit;
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="refresh" content="0;url=<?php echo htmlspecialchars($target, ENT_QUOTES); ?>">
    <title>Redirecting…</title>
    <script>
    window.location.replace(<?php echo json_encode($target); ?>);
    </script>
</head>

<body>
    <p>If you are not redirected automatically, <a href="<?php echo htmlspecialchars($target, ENT_QUOTES); ?>">click
            here</a>.</p>
</body>

</html>