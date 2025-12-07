<!DOCTYPE html>
<html lang="zxx">

<?php 
require 'include/phpcode.php';


$applicantId = $session_id;
$selectedEmployerId = isset($_GET['empid']) ? (int)$_GET['empid'] : 0;




// Handle send message
// if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_message'])) {
//     $recipientId = (int)$_POST['recipient_id'];
//     $message = trim($_POST['message']);
    
//     if (!empty($message) && $recipientId > 0) {
//         $insertMsg = "INSERT INTO tblmessages (SENDER_ID, RECIPIENT_ID, MESSAGE, DATEPOSTED, IS_READ) 
//                       VALUES (?, ?, ?, NOW(), 0)";
//         $stmt = mysqli_prepare($con, $insertMsg);
//         mysqli_stmt_bind_param($stmt, "iis", $applicantId, $recipientId, $message);
        
//         if (mysqli_stmt_execute($stmt)) {
//             $_SESSION['success_msg'] = "Message sent successfully!";
//             header("Location: dashboard-messages.php?empid=$recipientId");
//             exit();
//         } else {
//             $_SESSION['error_msg'] = "Failed to send message.";
//         }
//     }
// }




// Mark messages as read when viewing conversation
if ($selectedEmployerId > 0) {
    $markReadQuery = "UPDATE tblmessages 
                      SET IS_READ = 1 
                      WHERE SENDER_ID = ? AND RECIPIENT_ID = ? AND IS_READ = 0";
    $stmtRead = mysqli_prepare($con, $markReadQuery);
    mysqli_stmt_bind_param($stmtRead, "ii", $selectedEmployerId, $applicantId);
    mysqli_stmt_execute($stmtRead);

    
    // Mark related notifications as read
    $markReadQuery = "UPDATE tblnotification 
                      SET STATUS = 'Read' 
                      WHERE USERID = ? AND TYPE = ? AND STATUS = 'Unread'";
    $stmtRead = mysqli_prepare($con, $markReadQuery);
    $notifType = 'Message';
    mysqli_stmt_bind_param($stmtRead, "is", $applicantId, $notifType);
    mysqli_stmt_execute($stmtRead);
}





// Get conversation list (all employers the applicant has messaged or received messages from)
$conversationsQuery = "SELECT 
    sub.EMPLOYER_ID,
    sub.COMPANYNAME,
    sub.EMAIL,
    sub.JOBTITLE,
    sub.LAST_MESSAGE,
    sub.LAST_MESSAGE_DATE,
    COALESCE((SELECT COUNT(*) 
              FROM tblmessages 
              WHERE SENDER_ID = sub.EMPLOYER_ID 
                AND RECIPIENT_ID = ? 
                AND IS_READ = 0), 0) as UNREAD_COUNT
FROM (
    SELECT DISTINCT
        CASE 
            WHEN m.SENDER_ID = ? THEN m.RECIPIENT_ID
            ELSE m.SENDER_ID
        END as EMPLOYER_ID,
        c.COMPANYNAME,
        u.EMAIL,
        (SELECT j.JOBTITLE 
         FROM tbljobapplication ja
         INNER JOIN tbljob j ON ja.JOBID = j.JOBID
         WHERE ja.APPLICANTID = ? 
           AND j.EMPLOYERID = CASE WHEN m.SENDER_ID = ? THEN m.RECIPIENT_ID ELSE m.SENDER_ID END
         ORDER BY ja.DATEAPPLIED DESC
         LIMIT 1) as JOBTITLE,
        (SELECT m2.MESSAGE 
         FROM tblmessages m2
         WHERE (m2.SENDER_ID = ? AND m2.RECIPIENT_ID = CASE WHEN m.SENDER_ID = ? THEN m.RECIPIENT_ID ELSE m.SENDER_ID END)
            OR (m2.SENDER_ID = CASE WHEN m.SENDER_ID = ? THEN m.RECIPIENT_ID ELSE m.SENDER_ID END AND m2.RECIPIENT_ID = ?)
         ORDER BY m2.DATEPOSTED DESC 
         LIMIT 1) as LAST_MESSAGE,
        (SELECT m2.DATEPOSTED 
         FROM tblmessages m2
         WHERE (m2.SENDER_ID = ? AND m2.RECIPIENT_ID = CASE WHEN m.SENDER_ID = ? THEN m.RECIPIENT_ID ELSE m.SENDER_ID END)
            OR (m2.SENDER_ID = CASE WHEN m.SENDER_ID = ? THEN m.RECIPIENT_ID ELSE m.SENDER_ID END AND m2.RECIPIENT_ID = ?)
         ORDER BY m2.DATEPOSTED DESC 
         LIMIT 1) as LAST_MESSAGE_DATE
    FROM tblmessages m
    INNER JOIN tblusers u ON (
        CASE 
            WHEN m.SENDER_ID = ? THEN m.RECIPIENT_ID
            ELSE m.SENDER_ID
        END = u.USERID
    )
    LEFT JOIN tblcompany c ON u.USERID = c.USERID
    WHERE m.SENDER_ID = ? OR m.RECIPIENT_ID = ?
) sub
WHERE sub.EMPLOYER_ID IS NOT NULL
ORDER BY sub.LAST_MESSAGE_DATE DESC";

$stmt = mysqli_prepare($con, $conversationsQuery);
mysqli_stmt_bind_param($stmt, "iiiiiiiiiiiiiii", 
    $applicantId, // UNREAD_COUNT subquery
    $applicantId, // EMPLOYER_ID calculation
    $applicantId, $applicantId, // JOBTITLE subquery
    $applicantId, $applicantId, $applicantId, $applicantId, // LAST_MESSAGE subquery
    $applicantId, $applicantId, $applicantId, $applicantId, // LAST_MESSAGE_DATE subquery
    $applicantId, // JOIN condition
    $applicantId, $applicantId // WHERE clause
);
mysqli_stmt_execute($stmt);
$conversations = mysqli_stmt_get_result($stmt);

// Get selected conversation messages
$messages = null;
$selectedEmployer = null;

if ($selectedEmployerId > 0) {
    // Get selected employer details
    $employerQuery = "SELECT u.*, c.COMPANYNAME, c.COMPANYLOGO, c.COMPANYADDRESS, c.COMPANYEMAIL, c.COMPANYCITY, c.COMPANYCOUNTRY
                      FROM tblusers u
                      LEFT JOIN tblcompany c ON u.USERID = c.USERID
                      WHERE u.USERID = ?";
    $stmtEmp = mysqli_prepare($con, $employerQuery);
    mysqli_stmt_bind_param($stmtEmp, "i", $selectedEmployerId);
    mysqli_stmt_execute($stmtEmp);
    $empResult = mysqli_stmt_get_result($stmtEmp);
    $selectedEmployer = mysqli_fetch_assoc($empResult);
    
    // Get messages between applicant and selected employer
    $messagesQuery = "SELECT m.*, 
                      sender.FNAME as SENDER_FNAME, sender.ONAME as SENDER_ONAME
                      FROM tblmessages m
                      LEFT JOIN tblusers sender ON m.SENDER_ID = sender.USERID
                      WHERE (m.SENDER_ID = ? AND m.RECIPIENT_ID = ?)
                         OR (m.SENDER_ID = ? AND m.RECIPIENT_ID = ?)
                      ORDER BY m.DATEPOSTED ASC";
    $stmtMsg = mysqli_prepare($con, $messagesQuery);
    mysqli_stmt_bind_param($stmtMsg, "iiii", $applicantId, $selectedEmployerId, $selectedEmployerId, $applicantId);
    mysqli_stmt_execute($stmtMsg);
    $messages = mysqli_stmt_get_result($stmtMsg);
}

// Time ago function
function timeago($date) {
    if (empty($date)) return '';
    
    $timestamp = strtotime($date);
    $difference = time() - $timestamp;
    
    if ($difference < 60) {
        return "Just now";
    } elseif ($difference < 3600) {
        $minutes = floor($difference / 60);
        return $minutes . " min" . ($minutes > 1 ? 's' : '') . " ago";
    } elseif ($difference < 86400) {
        $hours = floor($difference / 3600);
        return $hours . " hour" . ($hours > 1 ? 's' : '') . " ago";
    } elseif ($difference < 604800) {
        $days = floor($difference / 86400);
        return $days . " day" . ($days > 1 ? 's' : '') . " ago";
    } else {
        return date('M d, Y', $timestamp);
    }
}
?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Messages - MUNext</title>
    <link href="assets/css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.lineicons.com/3.0/lineicons.css">

    <link rel="stylesheet" href="assets/css/custom-style.css">

</head>

<body>
    <!-- <div class="preloader"></div> -->

    <div id="main-wrapper">
        <?php include 'header.php' ?>
        <div class="clearfix"></div>

        <div class="dashboard-wrap bg-light">
            <a class="mobNavigation" data-toggle="collapse" href="#MobNav" role="button" aria-expanded="false">
                <i class="fas fa-bars mr-2"></i>Dashboard Navigation
            </a>

            <?php include 'sidenav.php' ?>

            <div class="dashboard-content">
                <div class="dashboard-tlbar d-block mb-4">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12">
                            <h1 class="ft-medium">
                                <i class="lni lni-comments"></i> Messages
                            </h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item text-muted"><a href="dashboard.php">Home</a></li>
                                    <li class="breadcrumb-item text-muted"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="#" class="theme-cl">Messages</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Success/Error Messages -->
                <?php if (!empty($_SESSION['success_msg'])): ?>
                <!-- <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="lni lni-checkmark-circle"></i> <?php echo $_SESSION['success_msg']; ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div> -->
                <?php unset($_SESSION['success_msg']); endif; ?>

                <?php if (!empty($_SESSION['error_msg'])): ?>
                <!-- <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="lni lni-cross-circle"></i> <?php echo $_SESSION['error_msg']; ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div> -->
                <?php unset($_SESSION['error_msg']); endif; ?>

                <!-- Messaging Container -->
                <div class="messaging-container">
                    <!-- Conversations Sidebar -->
                    <div class="conversations-sidebar">
                        <div class="conversations-header theme-bg">
                            <h4><i class="lni lni-comments"></i> Conversations</h4>
                            <small>Connect with employers</small>
                        </div>

                        <div class="conversations-search">
                            <input type="text" id="searchConversations" placeholder="Search conversations..."
                                class="form-control">
                        </div>

                        <div class="conversations-list">
                            <?php 
                            $conversationCount = mysqli_num_rows($conversations);
                            
                            if ($conversationCount > 0):
                                while ($conv = mysqli_fetch_assoc($conversations)):
                                    $employerId = $conv['EMPLOYER_ID'];
                                    $unreadCount = $conv['UNREAD_COUNT'];
                                    $isActive = ($employerId == $selectedEmployerId);
                                    $companyName = $conv['COMPANYNAME'] ?? 'Company';
                                    $jobTitle = $conv['JOBTITLE'] ?? '';
                                    $lastMessage = $conv['LAST_MESSAGE'] ?? 'No messages yet';
                                    $lastMessageDate = $conv['LAST_MESSAGE_DATE'];
                            ?>

                            <a href="dashboard-messages.php?empid=<?php echo $employerId; ?>"
                                class="conversation-item <?php echo $isActive ? 'active' : ''; ?>"
                                data-name="<?php echo strtolower($companyName); ?>">
                                <div class="conversation-avatar theme-bg">
                                    <?php echo strtoupper(substr($companyName, 0, 1)); ?>
                                </div>
                                <div class="conversation-details">
                                    <div class="conversation-name"><?php echo htmlspecialchars($companyName); ?></div>
                                    <?php if ($jobTitle): ?>
                                    <div class="conversation-job">
                                        <i class="lni lni-briefcase"></i> <?php echo htmlspecialchars($jobTitle); ?>
                                    </div>
                                    <?php endif; ?>
                                    <div class="conversation-last-message">
                                        <?php echo htmlspecialchars(substr($lastMessage, 0, 35)) . (strlen($lastMessage) > 35 ? '...' : ''); ?>
                                    </div>
                                </div>
                                <div class="conversation-meta">
                                    <div class="conversation-time">
                                        <?php echo timeago($lastMessageDate); ?>
                                    </div>
                                    <?php if ($unreadCount > 0): ?>
                                    <div class="unread-badge"><?php echo $unreadCount; ?></div>
                                    <?php endif; ?>
                                </div>
                            </a>

                            <?php 
                                endwhile;
                            else:
                            ?>

                            <div class="empty-state">
                                <i class="lni lni-inbox"></i>
                                <p>No conversations yet</p>
                                <small class="text-muted">Apply for jobs to start messaging employers</small>
                            </div>

                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Chat Area -->
                    <div class="chat-area">
                        <?php if ($selectedEmployer): ?>
                        <!-- Chat Header -->
                        <div class="chat-header">
                            <div class="chat-header-avatar">
                                <?php if (!empty($selectedEmployer['COMPANYLOGO'])): ?>
                                <img src="<?php echo $path.htmlspecialchars($selectedEmployer['COMPANYLOGO']); ?>"
                                    alt="<?php echo htmlspecialchars($selectedEmployer['COMPANYNAME']); ?>"
                                    onerror="this.parentElement.innerHTML='<?php echo strtoupper(substr($selectedEmployer['COMPANYNAME'], 0, 1)); ?>'">
                                <?php else: ?>
                                <?php echo strtoupper(substr($selectedEmployer['COMPANYNAME'], 0, 1)); ?>
                                <?php endif; ?>
                            </div>
                            <div class="chat-header-details">
                                <h5><?php echo htmlspecialchars($selectedEmployer['COMPANYNAME'] ?? 'Company'); ?></h5>
                                <p>
                                    <i class="lni lni-envelope"></i>
                                    <?php echo htmlspecialchars($selectedEmployer['COMPANYEMAIL']); ?>
                                    <?php if (!empty($selectedEmployer['COMPANYCITY'])): ?>
                                    <span class="ml-2">
                                        <i class="lni lni-map-marker"></i>
                                        <?php echo htmlspecialchars($selectedEmployer['COMPANYCITY'] . ', ' . $selectedEmployer['COMPANYCOUNTRY']); ?>
                                    </span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>

                        <!-- Chat Messages -->
                        <div class="chat-messages" id="chatMessages">
                            <?php 
                                if ($messages && mysqli_num_rows($messages) > 0):
                                    while ($msg = mysqli_fetch_assoc($messages)):
                                        $isSent = ($msg['SENDER_ID'] == $applicantId);
                                        $messageClass = $isSent ? 'sent' : 'received';
                                ?>

                            <div class="message <?php echo $messageClass; ?>">
                                <div class="message-content">
                                    <div class="message-bubble">
                                        <?php echo nl2br(htmlspecialchars($msg['MESSAGE'])); ?>
                                    </div>
                                    <div class="message-time">
                                        <?php echo timeago($msg['DATEPOSTED']); ?>
                                    </div>
                                </div>
                            </div>

                            <?php 
                                    endwhile;
                                else:
                                ?>

                            <div class="empty-state">
                                <i class="lni lni-comments"></i>
                                <p>No messages yet</p>
                                <small class="text-muted">Start the conversation below</small>
                            </div>

                            <?php endif; ?>
                        </div>

                        <!-- Chat Input -->
                        <div class="chat-input-area">
                            <form method="POST" class="chat-input-form" id="messageForm">
                                <input type="hidden" name="recipient_id" value="<?php echo $selectedEmployerId; ?>">
                                <input type="hidden" id="sendMessageHidden" name="send_message" value="1">
                                <textarea name="message" id="messageInput" placeholder="Type your message..." rows="1"
                                    required></textarea>
                                <button type="submit" name="send_message" class="btn-send theme-bg" id="sendBtn">
                                    <i class="lni lni-telegram" style="font-size: 1.5rem;"></i>
                                </button>
                            </form>
                        </div>
                        <?php else: ?>
                        <div class="empty-state">
                            <i class="lni lni-comments-reply"></i>
                            <h5>Select a conversation</h5>
                            <p class="text-muted">Choose a conversation from the sidebar to start messaging</p>
                            <p class="text-muted"><small>Or browse jobs and apply to connect with employers</small></p>
                            <a href="<?php echo $path?>browse-jobs.php"
                                class="btn btn-md btn-outline-secondary rounded fs-sm ft-medium pt-4">
                                <i class="lni lni-briefcase"></i> Browse Jobs
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>

        <?php include 'footer.php' ?>
    </div>

    <a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>
    <!-- Scripts -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/custom.js"></script>

    <script>
    $(document).ready(function() {
        // Auto-scroll to bottom of messages
        function scrollToBottom() {
            var chatMessages = document.getElementById('chatMessages');
            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        }

        // Scroll to bottom on page load
        scrollToBottom();

        // Search conversations
        $('#searchConversations').on('keyup', function() {
            var searchTerm = $(this).val().toLowerCase();

            $('.conversation-item').each(function() {
                var name = $(this).data('name');
                if (name && name.indexOf(searchTerm) !== -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Auto-resize textarea
        $('#messageInput').on('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
        // Handle Enter key (send on Enter, new line on Shift+Enter)
        $('#messageInput').on('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                $('#messageForm').submit();
            }
        });

        // Prevent empty messages
        $('#messageForm').on('submit', function(e) {
            var message = $('#messageInput').val().trim();
            if (message === '') {
                e.preventDefault();
                return false;
            }

            // Ensure a hidden field is present so the POST flag is always sent even if the button gets disabled
            $('#sendMessageHidden').val('1');

            // Disable send button shortly after submit to avoid preventing the button's value being included in the POST
            setTimeout(function() {
                $('#sendBtn').prop('disabled', true);
            }, 100);
        });

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
    </script>

</body>

</html>