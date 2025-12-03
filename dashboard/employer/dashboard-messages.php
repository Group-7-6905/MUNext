<!DOCTYPE html>
<html lang="zxx">

<?php 
require 'include/phpcode.php';

$employerId = $session_id;
$selectedUserId = isset($_GET['userid']) ? (int)$_GET['userid'] : 0;




// Mark messages as read when viewing conversation
if ($selectedUserId > 0) {
    $markReadQuery = "UPDATE tblmessages 
                      SET IS_READ = 1 
                      WHERE SENDER_ID = ? AND RECIPIENT_ID = ? AND IS_READ = 0";
    $stmtRead = mysqli_prepare($con, $markReadQuery);
    mysqli_stmt_bind_param($stmtRead, "ii", $selectedUserId, $employerId);
    mysqli_stmt_execute($stmtRead);


    $markReadQuery = "UPDATE tblnotification 
                      SET STATUS = 'Read' 
                      WHERE USERID = ? AND STATUS = 'Unread'";
    $stmtRead = mysqli_prepare($con, $markReadQuery);
    mysqli_stmt_bind_param($stmtRead, "i", $employerId);
    mysqli_stmt_execute($stmtRead);

}

// Get conversation list - Fixed subquery correlation
$conversationsQuery = "SELECT 
    sub.USER_ID,
    sub.FNAME,
    sub.ONAME,
    sub.EMAIL,
    sub.LAST_MESSAGE,
    sub.LAST_MESSAGE_DATE,
    COALESCE((SELECT COUNT(*) 
              FROM tblmessages 
              WHERE SENDER_ID = sub.USER_ID 
                AND RECIPIENT_ID = ? 
                AND IS_READ = 0), 0) as UNREAD_COUNT
FROM (
    SELECT DISTINCT
        CASE 
            WHEN m.SENDER_ID = ? THEN m.RECIPIENT_ID
            ELSE m.SENDER_ID
        END as USER_ID,
        u.FNAME,
        u.ONAME,
        u.EMAIL,
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
    WHERE m.SENDER_ID = ? OR m.RECIPIENT_ID = ?
) sub
ORDER BY sub.LAST_MESSAGE_DATE DESC";

$stmt = mysqli_prepare($con, $conversationsQuery);
mysqli_stmt_bind_param($stmt, "iiiiiiiiiiiii", 
    $employerId, // UNREAD_COUNT subquery
    $employerId, // USER_ID calculation
    $employerId, $employerId, $employerId, $employerId, // LAST_MESSAGE subquery
    $employerId, $employerId, $employerId, $employerId, // LAST_MESSAGE_DATE subquery
    $employerId, // JOIN condition
    $employerId, $employerId // WHERE clause
);
mysqli_stmt_execute($stmt);
$conversations = mysqli_stmt_get_result($stmt);






// Get selected conversation messages
$messages = null;
$selectedUser = null;

if ($selectedUserId > 0) {
    // Get selected user details with photo
    $userQuery = "SELECT u.*, ap.APPLICANTPHOTO 
                  FROM tblusers u
                  LEFT JOIN tblapplicants ap ON u.USERID = ap.USERID
                  WHERE u.USERID = ?";
    $stmtUser = mysqli_prepare($con, $userQuery);
    mysqli_stmt_bind_param($stmtUser, "i", $selectedUserId);
    mysqli_stmt_execute($stmtUser);
    $userResult = mysqli_stmt_get_result($stmtUser);
    $selectedUser = mysqli_fetch_assoc($userResult);
    
    // Get messages between employer and selected user
    $messagesQuery = "SELECT m.*, 
                      sender.FNAME as SENDER_FNAME, sender.ONAME as SENDER_ONAME
                      FROM tblmessages m
                      LEFT JOIN tblusers sender ON m.SENDER_ID = sender.USERID
                      WHERE (m.SENDER_ID = ? AND m.RECIPIENT_ID = ?)
                         OR (m.SENDER_ID = ? AND m.RECIPIENT_ID = ?)
                      ORDER BY m.DATEPOSTED ASC";
    $stmtMsg = mysqli_prepare($con, $messagesQuery);
    mysqli_stmt_bind_param($stmtMsg, "iiii", $employerId, $selectedUserId, $selectedUserId, $employerId);
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
    <link href="assets/css/custom-style.css" rel="stylesheet">


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
                <?php if (isset($_SESSION['success_msg'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="lni lni-checkmark-circle"></i> <?php echo $_SESSION['success_msg']; ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
                <?php unset($_SESSION['success_msg']); endif; ?>

                <?php if (isset($_SESSION['error_msg'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="lni lni-cross-circle"></i> <?php echo $_SESSION['error_msg']; ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
                <?php unset($_SESSION['error_msg']); endif; ?>

                <!-- Messaging Container -->
                <div class="messaging-container">
                    <!-- Conversations Sidebar -->
                    <div class="conversations-sidebar">
                        <div class="conversations-header theme-bg-1">
                            <h4><i class="lni lni-comments"></i> Conversations</h4>
                            <small>Connect with applicants</small>
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
                                    $userId = $conv['USER_ID'];
                                    $unreadCount = $conv['UNREAD_COUNT'];
                                    $isActive = ($userId == $selectedUserId);
                                    $userName = trim($conv['FNAME'] . ' ' . ($conv['ONAME'] ?? ''));
                                    $lastMessage = $conv['LAST_MESSAGE'] ?? 'No messages yet';
                                    $lastMessageDate = $conv['LAST_MESSAGE_DATE'];
                            ?>

                            <a href="dashboard-messages.php?userid=<?php echo $userId; ?>"
                                class="conversation-item <?php echo $isActive ? 'active' : ''; ?>"
                                data-name="<?php echo strtolower($userName); ?>">
                                <div class="conversation-avatar">
                                    <?php echo strtoupper(substr($userName, 0, 1)); ?>
                                </div>
                                <div class="conversation-details">
                                    <div class="conversation-name"><?php echo htmlspecialchars($userName); ?></div>
                                    <div class="conversation-last-message">
                                        <?php echo htmlspecialchars(substr($lastMessage, 0, 40)) . (strlen($lastMessage) > 40 ? '...' : ''); ?>
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
                                <small class="text-muted">Start messaging applicants from the applications page</small>
                            </div>

                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Chat Area -->
                    <div class="chat-area">
                        <?php if ($selectedUser): ?>
                        <!-- Chat Header -->
                        <div class="chat-header">
                            <div class="chat-header-avatar theme-bg-1">
                                <?php if (!empty($selectedUser['APPLICANTPHOTO'])): ?>
                                <img src="../../<?php echo htmlspecialchars($selectedUser['APPLICANTPHOTO']); ?>"
                                    alt="<?php echo htmlspecialchars($selectedUser['FNAME']); ?>"
                                    onerror="this.parentElement.innerHTML='<?php echo strtoupper(substr($selectedUser['FNAME'], 0, 1)); ?>'">
                                <?php else: ?>
                                <?php echo strtoupper(substr($selectedUser['FNAME'], 0, 1)); ?>
                                <?php endif; ?>
                            </div>
                            <div class="chat-header-details">
                                <h5><?php echo htmlspecialchars(trim($selectedUser['FNAME'] . ' ' . ($selectedUser['ONAME'] ?? ''))); ?>
                                </h5>
                                <p><i class="lni lni-envelope"></i>
                                    <?php echo htmlspecialchars($selectedUser['EMAIL']); ?></p>
                            </div>
                            <div class="ml-auto">
                                <a href="dashboard-candidate-detail.php?applicantid=<?php echo $selectedUserId; ?>"
                                    class="btn btn-md rounded fs-sm ft-small btn btn-sm btn-outline-default">
                                    <i class="lni lni-user"></i> View Profile
                                </a>
                            </div>
                        </div>

                        <!-- Chat Messages -->
                        <div class="chat-messages" id="chatMessages">
                            <?php 
                                if ($messages && mysqli_num_rows($messages) > 0):
                                    while ($msg = mysqli_fetch_assoc($messages)):
                                        $isSent = ($msg['SENDER_ID'] == $employerId);
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
                                <input type="hidden" name="recipient_id" value="<?php echo $selectedUserId; ?>">
                                <input type="hidden" id="sendMessageHidden" name="send_message" value="1">
                                <textarea name="message" id="messageInput" placeholder="Type your message..." rows="1"
                                    required></textarea>
                                <button type="submit" name="send_message" class="btn-send theme-bg-1" id="sendBtn">
                                    <i class="lni lni-telegram" style="font-size: 1.5rem;"></i>
                                </button>
                            </form>
                        </div>
                        <?php else: ?>
                        <div class="empty-state">
                            <i class="lni lni-comments-reply"></i>
                            <h5>Select a conversation</h5>
                            <p class="text-muted">Choose a conversation from the sidebar to start messaging</p>
                            <p class="text-muted"><small>Or visit the applications page to message an applicant</small>
                            </p>
                            <a href="dashboard-manage-applications.php"
                                class="btn btn-md btn-outline-secondary rounded fs-sm ft-medium pt-4">
                                <i class="lni lni-users"></i>
                                View Applications
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>

        <?php include 'footer.php' ?>
        <a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>
    </div>

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