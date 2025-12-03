<?php
require 'include/phpcode.php';


$applicationId = isset($_GET['appid']) ? (int)$_GET['appid'] : 0;

// Get application with screening data
$query = "SELECT 
    ja.*,
    u.FNAME, u.ONAME, u.EMAIL, ap.CONTACTNO, ap.FULLADDRESS,
    j.JOBTITLE, j.EMPLOYERID, j.COMPANYID
FROM tbljobapplication ja
INNER JOIN tblusers u ON ja.APPLICANTID = u.USERID
INNER JOIN tblapplicants ap ON ja.APPLICANTID = ap.USERID
INNER JOIN tbljob j ON ja.JOBID = j.JOBID
WHERE ja.ID = ?";

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $applicationId);
mysqli_stmt_execute($stmt);
$application = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$application) {
    die("Application not found");
}

// Verify employer owns this job
if ($application['EMPLOYERID'] != $session_id) {
    die("Access denied");
}

// Get screening answers
$answersQuery = "SELECT 
    sa.*, 
    sq.QUESTION, sq.QUESTIONTYPE, sq.OPTIONS, sq.CORRECTANSWER, sq.POINTS
FROM tblscreeninganswers sa
INNER JOIN tblscreeningquestions sq ON sa.QUESTIONID = sq.QUESTIONID
WHERE sa.APPLICATIONID = ?
ORDER BY sq.QUESTIONID ASC";

$stmt = mysqli_prepare($con, $answersQuery);
mysqli_stmt_bind_param($stmt, "i", $applicationId);
mysqli_stmt_execute($stmt);
$answers = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Screening Answers - <?php echo htmlspecialchars($application['FNAME'] . ' ' . $application['LNAME']); ?>
    </title>
    <link href="assets/css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.lineicons.com/3.0/lineicons.css">

    <link href="assets/css/custom-style.css" rel="stylesheet">

</head>

<body>

    <div id="main-wrapper">
        <?php include 'header.php' ?>
        <div class="clearfix"></div>

        <div class="dashboard-wrap bg-light">
            <a class="mobNavigation" data-toggle="collapse" href="#MobNav" role="button" aria-expanded="false">
                <i class="fas fa-bars mr-2"></i>Dashboard Navigation
            </a>

            <?php include 'sidenav.php' ?>

            <div class="dashboard-content">
                <div class="container mt-5 mb-5">

                    <!-- Applicant Header -->
                    <div class="score-summary theme-bg">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h3 class="text-white">
                                    <?php echo htmlspecialchars($application['FNAME'] . ' ' . ($application['ONAME'])); ?>
                                </h3>
                                <p class="mb-1">
                                    <i class="lni lni-briefcase"></i> Applied for:
                                    <strong><?php echo htmlspecialchars($application['JOBTITLE']); ?></strong>
                                </p>
                                <p class="mb-1">
                                    <i class="lni lni-envelope"></i>
                                    <?php echo htmlspecialchars($application['EMAIL']); ?>
                                </p>
                                <p class="mb-0">
                                    <i class="lni lni-calendar"></i> Applied:
                                    <?php echo date('M d, Y', strtotime($application['DATEAPPLIED'])); ?>
                                </p>
                            </div>

                            <?php if ($application['SCREENINGSCORE'] !== null): ?>
                            <div class="text-right">
                                <h2 class="mb-2 text-white">
                                    <strong><?php echo $application['SCREENINGSCORE']; ?></strong> points
                                </h2>
                                <?php if ($application['SCREENPASSED']): ?>
                                <span class="badge badge-success badge-lg">
                                    <i class="lni lni-checkmark-circle"></i> Passed Screening
                                </span>
                                <?php else: ?>
                                <span class="badge badge-warning badge-lg">
                                    <i class="lni lni-warning"></i> Review Required
                                </span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Back Button -->
                    <div class="mb-3">
                        <a href="dashboard-manage-applications.php?jobid=<?php echo $application['JOBID']; ?>"
                            class="btn btn-outline-secondary small rounded">
                            <i class="lni lni-arrow-left"></i> Back to Applications
                        </a>
                    </div>

                    <!-- Screening Answers -->
                    <h4 class="mb-4">
                        <i class="lni lni-question-circle"></i> Screening Questions & Answers
                    </h4>

                    <?php if (mysqli_num_rows($answers) > 0): ?>
                    <?php 
                        $qNum = 1; 
                        while ($ans = mysqli_fetch_assoc($answers)): 
                            $isCorrect = false;
                            $cardClass = 'neutral-answer';
                            
                            if ($ans['CORRECTANSWER']) {
                                $isCorrect = (strtolower(trim($ans['ANSWER'])) == strtolower(trim($ans['CORRECTANSWER'])));
                                $cardClass = $isCorrect ? 'correct-answer' : 'incorrect-answer';
                            }
                        ?>

                    <div class="answer-card <?php echo $cardClass; ?>">
                        <div class="d-flex align-items-start mb-3">
                            <div class="question-number"><?php echo $qNum++; ?></div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="mb-0"><?php echo nl2br(htmlspecialchars($ans['QUESTION'])); ?></h5>
                                    <div class="text-right">
                                        <small class="badge badge-info small ml-2"><?php echo $ans['POINTS']; ?>
                                            pts</small>
                                        <?php if ($ans['SCORE'] > 0): ?>
                                        <span class="badge badge-success ml-1">
                                            Scored: <?php echo $ans['SCORE']; ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <strong>Applicant's Answer:</strong>
                                    <div class="p-3 bg-white rounded mt-2 border">
                                        <?php echo nl2br(htmlspecialchars($ans['ANSWER'])); ?>
                                    </div>
                                </div>

                                <?php if ($ans['CORRECTANSWER']): ?>
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <strong>Expected Answer:</strong>
                                        <?php echo htmlspecialchars($ans['CORRECTANSWER']); ?>
                                        <?php if ($isCorrect): ?>
                                        <span class="text-success ml-2">
                                            <i class="lni lni-checkmark-circle"></i> Match!
                                        </span>
                                        <?php else: ?>
                                        <span class="text-danger ml-2">
                                            <i class="lni lni-cross-circle"></i> No Match
                                        </span>
                                        <?php endif; ?>
                                    </small>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php endwhile; ?>

                    <!-- Summary -->
                    <div class="alert alert-info mt-4">
                        <i class="lni lni-information"></i>
                        <strong>Note:</strong> This applicant answered
                        <?php echo mysqli_num_rows($answers); ?> screening question(s).
                        <?php if ($application['SCREENPASSED']): ?>
                        They passed the automatic screening with a score of
                        <?php echo $application['SCREENINGSCORE']; ?> points.
                        <?php else: ?>
                        Their screening score may require manual review.
                        <?php endif; ?>
                    </div>

                    <?php else: ?>
                    <div class="alert alert-info">
                        <i class="lni lni-information"></i>
                        No screening questions were answered for this application.
                    </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="mt-4 action-buttons">
                        <a href="dashboard-manage-applications.php?jobid=<?php echo $application['JOBID']; ?>"
                            class="btn btn-outline-secondary rounded">
                            <i class="lni lni-arrow-left"></i> Back to Applications
                        </a>
                        <a href="dashboard-candidate-detail.php?applicantid=<?php echo $application['APPLICANTID']; ?>&jobapplicationid=<?php echo $applicationId; ?>"
                            class="btn btn-primary rounded">
                            <i class="lni lni-user"></i> View Full Profile
                        </a>
                        <a href="mailto:<?php echo htmlspecialchars($application['EMAIL']); ?>"
                            class="btn btn-success rounded">
                            <i class="lni lni-envelope"></i> Contact Applicant
                        </a>
                        <?php if ($application['RESUME']): ?>
                        <a href="view-resume.php?appid=<?php echo $applicationId; ?>" target="_blank"
                            class="btn btn-info rounded">
                            <i class="lni lni-eye"></i> View Resume
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'footer.php' ?>
        <a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>
    </div>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/custom.js"></script>
</body>

</html>