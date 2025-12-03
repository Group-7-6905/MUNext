<?php
session_start();
require 'include/phpcode.php';

header('Content-Type: application/json');

if (!isset($_SESSION['COMPANYID']) || !isset($_GET['userid'])) {
    echo json_encode(['success' => false]);
    exit();
}

$employerId = $_SESSION['COMPANYID'];
$userId = (int)$_GET['userid'];
$lastMessageId = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;

// Get new messages
$query = "SELECT m.*, 
          sender.FNAME as SENDER_FNAME, sender.LNAME as SENDER_LNAME
          FROM tblmessages m
          LEFT JOIN tblusers sender ON m.SENDER_ID = sender.USERID
          WHERE ((m.SENDER_ID = ? AND m.RECIPIENT_ID = ?)
             OR (m.SENDER_ID = ? AND m.RECIPIENT_ID = ?))
             AND m.ID > ?
          ORDER BY m.DATEPOSTED ASC";

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "iiiii", $employerId, $userId, $userId, $employerId, $lastMessageId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$messages = [];
while ($row = mysqli_fetch_assoc($result)) {
    $messages[] = $row;
}

echo json_encode([
    'success' => true,
    'messages' => $messages
]);
?>