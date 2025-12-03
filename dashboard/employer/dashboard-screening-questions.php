<?php
require_once('include/phpcode.php');

// Check if employer is logged in


$employerId = $session_id;
$jobId = isset($_GET['jobid']) ? (int)$_GET['jobid'] : 0;

// Verify the job belongs to this employer
$checkQuery = "SELECT j.*, c.COMPANYNAME 
               FROM tbljob j 
               LEFT JOIN tblcompany c ON j.EMPLOYERID = c.USERID 
               WHERE j.JOBID = ? AND j.EMPLOYERID = ?";
$stmt = mysqli_prepare($con, $checkQuery);
mysqli_stmt_bind_param($stmt, "ii", $jobId, $employerId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$job = mysqli_fetch_assoc($result);

if (!$job) {
    $_SESSION['error_msg'] = "Job not found or access denied.";
    header("Location: dashboard-manage-jobs.php");
    exit();
}

// Handle Add Question
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_question'])) {
    $question = trim($_POST['question']);
    $questionType = $_POST['question_type'];
    $isRequired = isset($_POST['is_required']) ? 1 : 0;
    $correctAnswer = trim($_POST['correct_answer']);
    $points = (int)$_POST['points'];
    $options = null;
    
    // Validate inputs
    if (empty($question)) {
        $_SESSION['error_msg'] = "Question text is required.";
    } else {
        // Handle multiple choice options
        if ($questionType == 'multiple_choice' && !empty($_POST['options'])) {
            $optionsArray = array_filter(array_map('trim', $_POST['options']));
            if (count($optionsArray) < 2) {
                $_SESSION['error_msg'] = "Multiple choice questions need at least 2 options.";
            } else {
                $options = json_encode($optionsArray);
            }
        }
        
        if (!isset($_SESSION['error_msg'])) {
            // Insert question
            $insertQuery = "INSERT INTO tblscreeningquestions 
                            (JOBID, QUESTION, QUESTIONTYPE, OPTIONS, ISREQUIRED, CORRECTANSWER, POINTS) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($con, $insertQuery);
            mysqli_stmt_bind_param($stmt, "isssssi", $jobId, $question, $questionType, $options, 
                                   $isRequired, $correctAnswer, $points);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success_msg'] = "Screening question added successfully!";
                header("Location: dashboard-screening-questions.php?jobid=$jobId");
                exit();
            } else {
                $_SESSION['error_msg'] = "Error adding question: " . mysqli_error($con);
            }
        }
    }
}

// Handle Edit Question
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_question'])) {
    $questionId = (int)$_POST['question_id'];
    $question = trim($_POST['question']);
    $questionType = $_POST['question_type'];
    $isRequired = isset($_POST['is_required']) ? 1 : 0;
    $correctAnswer = trim($_POST['correct_answer']);
    $points = (int)$_POST['points'];
    $options = null;
    
    if ($questionType == 'multiple_choice' && !empty($_POST['options'])) {
        $optionsArray = array_filter(array_map('trim', $_POST['options']));
        $options = json_encode($optionsArray);
    }
    
    $updateQuery = "UPDATE tblscreeningquestions 
                    SET QUESTION = ?, QUESTIONTYPE = ?, OPTIONS = ?, 
                        ISREQUIRED = ?, CORRECTANSWER = ?, POINTS = ?
                    WHERE QUESTIONID = ? AND JOBID = ?";
    
    $stmt = mysqli_prepare($con, $updateQuery);
    mysqli_stmt_bind_param($stmt, "sssssiii", $question, $questionType, $options, 
                           $isRequired, $correctAnswer, $points, $questionId, $jobId);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success_msg'] = "Question updated successfully!";
        header("Location: dashboard-screening-questions.php?jobid=$jobId");
        exit();
    } else {
        $_SESSION['error_msg'] = "Error updating question.";
    }
}

// Handle Delete Question
if (isset($_GET['delete_question'])) {
    $questionId = (int)$_GET['delete_question'];
    
    $deleteQuery = "DELETE FROM tblscreeningquestions WHERE QUESTIONID = ? AND JOBID = ?";
    $stmt = mysqli_prepare($con, $deleteQuery);
    mysqli_stmt_bind_param($stmt, "ii", $questionId, $jobId);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success_msg'] = "Question deleted successfully!";
    } else {
        $_SESSION['error_msg'] = "Error deleting question.";
    }
    
    header("Location: dashboard-screening-questions.php?jobid=$jobId");
    exit();
}

// Get existing questions
$questionsQuery = "SELECT * FROM tblscreeningquestions WHERE JOBID = ? ORDER BY QUESTIONID ASC";
$stmt = mysqli_prepare($con, $questionsQuery);
mysqli_stmt_bind_param($stmt, "i", $jobId);
mysqli_stmt_execute($stmt);
$questions = mysqli_stmt_get_result($stmt);

// Get question to edit if edit mode
$editQuestion = null;
if (isset($_GET['edit_question'])) {
    $editQuestionId = (int)$_GET['edit_question'];
    $editQuery = "SELECT * FROM tblscreeningquestions WHERE QUESTIONID = ? AND JOBID = ?";
    $stmtEdit = mysqli_prepare($con, $editQuery);
    mysqli_stmt_bind_param($stmtEdit, "ii", $editQuestionId, $jobId);
    mysqli_stmt_execute($stmtEdit);
    $editResult = mysqli_stmt_get_result($stmtEdit);
    $editQuestion = mysqli_fetch_assoc($editResult);
}

// Common question templates
$templates = [
    [
        'question' => 'Do you have the legal right to work in this country?',
        'type' => 'yes_no',
        'answer' => 'Yes',
        'points' => 5
    ],
    [
        'question' => 'How many years of experience do you have in this field?',
        'type' => 'number',
        'answer' => '',
        'points' => 3
    ],
    [
        'question' => 'Are you willing to relocate if required?',
        'type' => 'yes_no',
        'answer' => '',
        'points' => 2
    ],
    [
        'question' => 'What is your expected salary range?',
        'type' => 'text',
        'answer' => '',
        'points' => 1
    ],
    [
        'question' => 'When is your earliest possible start date?',
        'type' => 'date',
        'answer' => '',
        'points' => 2
    ],
    [
        'question' => 'Do you have a valid driver\'s license?',
        'type' => 'yes_no',
        'answer' => '',
        'points' => 2
    ],
    [
        'question' => 'What is your highest level of education?',
        'type' => 'multiple_choice',
        'answer' => '',
        'points' => 3,
        'options' => ['High School', 'Associate Degree', 'Bachelor\'s Degree', 'Master\'s Degree', 'Doctorate']
    ],
    [
        'question' => 'Are you available to work weekends if needed?',
        'type' => 'yes_no',
        'answer' => '',
        'points' => 2
    ]
];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Screening Questions - <?php echo htmlspecialchars($job['JOBTITLE']); ?></title>

    <!-- CSS Links -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.lineicons.com/3.0/lineicons.css">
    <link rel="stylesheet" href="css/style.css">



    <link href="assets/css/styles.css" rel="stylesheet">
    <link href="assets/css/custom-style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.lineicons.com/3.0/lineicons.css">
</head>

<body>

    <body>

        <!-- ============================================================== -->
        <!-- Preloader - style you can find in spinners.css -->
        <!-- ============================================================== -->
        <!-- <div class="preloader"></div> -->

        <!-- ============================================================== -->
        <!-- Main wrapper - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <div id="main-wrapper">

            <!-- ============================================================== -->
            <!-- Top header  -->
            <!-- ============================================================== -->
            <!-- Start Navigation -->
            <?php include 'header.php' ?>
            <!-- End Navigation -->
            <div class="clearfix"></div>
            <!-- ============================================================== -->
            <!-- Top header  -->
            <!-- ============================================================== -->

            <!-- ======================= dashboard Detail ======================== -->

            <div class="dashboard-wrap bg-light">
                <a class="mobNavigation" data-toggle="collapse" href="#MobNav" role="button" aria-expanded="false"
                    aria-controls="MobNav">
                    <i class="fas fa-bars mr-2"></i>Dashboard Navigation
                </a>

                <?php include 'sidenav.php' ?>

                <div class="dashboard-content">
                    <div class="dashboard-tlbar d-block mb-5">
                        <div class="row">
                            <div class="colxl-12 col-lg-12 col-md-12">
                                <h1 class="ft-medium">Manage Jobs</h1>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb breadcrumb-custom">
                                        <li class="breadcrumb-item"><a href="dashboard.php"><i class="lni lni-home"></i>
                                                Dashboard</a></li>
                                        <li class="breadcrumb-item"><a href="dashboard-manage-jobs.php">Manage Jobs</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page">Screening Questions</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>

                    <div class="container mt-4 mb-5">

                        <!-- Job Info Bar -->
                        <div class="job-info-bar">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h3 class="mb-2 text-white"><i class="lni lni-briefcase"></i>
                                        <?php echo htmlspecialchars($job['JOBTITLE']); ?>
                                    </h3>
                                    <p class="mb-0">
                                        <i class="lni lni-building"></i>
                                        <?php echo htmlspecialchars($job['COMPANYNAME']); ?>
                                        <span class="mx-2">•</span>
                                        <i class="lni lni-calendar"></i> Posted:
                                        <?php echo date('M d, Y', strtotime($job['DATEPOSTED'])); ?>
                                    </p>
                                </div>
                                <div class="col-md-4 text-right">
                                    <span class="badge badge-light badge-lg">
                                        <i class="lni lni-question-circle"></i>
                                        <?php echo mysqli_num_rows($questions); ?> Questions
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Alert Messages -->
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

                        <div class="row">
                            <!-- Left Column: Add/Edit Question Form -->
                            <div class="col-md-5">
                                <div class="form-card">
                                    <h4 class="mb-4">
                                        <i class="lni lni-<?php echo $editQuestion ? 'pencil' : 'plus'; ?>"></i>
                                        <?php echo $editQuestion ? 'Edit Question' : 'Add Screening Question'; ?>
                                    </h4>

                                    <form method="POST" id="questionForm">
                                        <?php if ($editQuestion): ?>
                                        <input type="hidden" name="question_id"
                                            value="<?php echo $editQuestion['QUESTIONID']; ?>">
                                        <?php endif; ?>

                                        <div class="form-group">
                                            <label>Question Text <span class="text-danger">*</span></label>
                                            <textarea name="question" class="form-control" rows="3" required
                                                placeholder="e.g., Do you have at least 3 years of experience in PHP development?"><?php echo $editQuestion ? htmlspecialchars($editQuestion['QUESTION']) : ''; ?></textarea>
                                            <small class="form-text text-muted">Write a clear and specific
                                                question</small>
                                        </div>

                                        <div class="form-group">
                                            <label>Question Type <span class="text-danger">*</span></label>
                                            <select name="question_type" id="questionType" class="form-control"
                                                required>
                                                <option value="yes_no"
                                                    <?php echo ($editQuestion && $editQuestion['QUESTIONTYPE'] == 'yes_no') ? 'selected' : ''; ?>>
                                                    Yes/No
                                                </option>
                                                <option value="multiple_choice"
                                                    <?php echo ($editQuestion && $editQuestion['QUESTIONTYPE'] == 'multiple_choice') ? 'selected' : ''; ?>>
                                                    Multiple Choice
                                                </option>
                                                <option value="text"
                                                    <?php echo ($editQuestion && $editQuestion['QUESTIONTYPE'] == 'text') ? 'selected' : ''; ?>>
                                                    Text Answer
                                                </option>
                                                <option value="number"
                                                    <?php echo ($editQuestion && $editQuestion['QUESTIONTYPE'] == 'number') ? 'selected' : ''; ?>>
                                                    Number
                                                </option>
                                                <option value="date"
                                                    <?php echo ($editQuestion && $editQuestion['QUESTIONTYPE'] == 'date') ? 'selected' : ''; ?>>
                                                    Date
                                                </option>
                                            </select>
                                        </div>

                                        <!-- Multiple Choice Options -->
                                        <div id="optionsContainer"
                                            style="display: <?php echo ($editQuestion && $editQuestion['QUESTIONTYPE'] == 'multiple_choice') ? 'block' : 'none'; ?>;">
                                            <label>Answer Options</label>
                                            <div id="optionsList">
                                                <?php 
                            if ($editQuestion && $editQuestion['OPTIONS']) {
                                $options = json_decode($editQuestion['OPTIONS'], true);
                                foreach ($options as $index => $option) {
                                    echo '<div class="option-input-group">
                                            <input type="text" name="options[]" class="form-control" value="' . htmlspecialchars($option) . '" placeholder="Option ' . ($index + 1) . '">
                                            <i class="lni lni-trash-can remove-option" onclick="removeOption(this)"></i>
                                          </div>';
                                }
                            } else {
                            ?>
                                                <div class="option-input-group">
                                                    <input type="text" name="options[]" class="form-control"
                                                        placeholder="Option 1">
                                                </div>
                                                <div class="option-input-group">
                                                    <input type="text" name="options[]" class="form-control"
                                                        placeholder="Option 2">
                                                </div>
                                                <?php } ?>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mt-2"
                                                onclick="addOption()">
                                                <i class="lni lni-plus"></i> Add Option
                                            </button>
                                        </div>

                                        <div class="form-group">
                                            <label>Expected/Correct Answer (Optional)</label>
                                            <input type="text" name="correct_answer" class="form-control"
                                                value="<?php echo $editQuestion ? htmlspecialchars($editQuestion['CORRECTANSWER']) : ''; ?>"
                                                placeholder="e.g., Yes, or minimum value">
                                            <small class="form-text text-muted">Used for automatic screening
                                                scoring</small>
                                        </div>

                                        <div class="form-group">
                                            <label>Points/Weight</label>
                                            <input type="number" name="points" class="form-control"
                                                value="<?php echo $editQuestion ? $editQuestion['POINTS'] : '1'; ?>"
                                                min="0" max="10">
                                            <small class="form-text text-muted">Importance of this question
                                                (0-10)</small>
                                        </div>

                                        <div class="custom-checkbox mb-4">
                                            <input type="checkbox" name="is_required" class="checkbox-custom"
                                                id="isRequired"
                                                <?php echo (!$editQuestion || $editQuestion['ISREQUIRED']) ? 'checked' : ''; ?>>
                                            <label class="checkbox-custom-label" for="isRequired">
                                                Required Question?
                                            </label>
                                        </div>

                                        <div class="form-group">
                                            <?php if ($editQuestion): ?>
                                            <button type="submit" name="edit_question"
                                                class="btn btn-success btn-block">
                                                <i class="lni lni-checkmark"></i> Update Question
                                            </button>
                                            <a href="dashboard-screening-questions.php?jobid=<?php echo $jobId; ?>"
                                                class="btn btn-secondary btn-block">
                                                Cancel
                                            </a>
                                            <?php else: ?>
                                            <button type="submit" name="add_question"
                                                class="btn btn-secondary btn-block theme-bg">
                                                <i class="lni lni-plus"></i> Add Question
                                            </button>
                                            <?php endif; ?>
                                            <a href="dashboard-manage-jobs.php"
                                                class="btn btn-outline-secondary btn-block">
                                                <i class="lni lni-arrow-left"></i> Back to Jobs
                                            </a>
                                        </div>
                                    </form>

                                    <!-- Quick Templates -->
                                    <div class="mt-4">
                                        <h5>Quick Templates</h5>
                                        <button class="btn btn-sm btn-link" data-toggle="collapse"
                                            data-target="#templatesCollapse">
                                            <i class="lni lni-lightning"></i> Use a template question
                                        </button>

                                        <div class="collapse" id="templatesCollapse">
                                            <?php foreach ($templates as $template): ?>
                                            <div class="template-card"
                                                onclick="useTemplate(<?php echo htmlspecialchars(json_encode($template)); ?>)">
                                                <label
                                                    class="checkbox-custom-label"><strong><?php echo htmlspecialchars($template['question']); ?></strong></label>
                                                <br>
                                                <small class="text-muted">Type:
                                                    <?php echo ucwords(str_replace('_', ' ', $template['type'])); ?></small>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Existing Questions -->
                            <div class="col-md-7">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4><i class="lni lni-list"></i> Current Screening Questions</h4>
                                    <?php if (mysqli_num_rows($questions) > 0): ?>
                                    <span class="text-dark badge-lg">
                                        <?php echo mysqli_num_rows($questions); ?> Question(s)
                                    </span>
                                    <?php endif; ?>
                                </div>

                                <?php if (mysqli_num_rows($questions) > 0): ?>
                                <?php $qNum = 1; while ($q = mysqli_fetch_assoc($questions)): ?>
                                <div class="question-card">
                                    <div class="question-header">
                                        <div class="d-flex w-100">
                                            <div class="question-number"><?php echo $qNum++; ?></div>
                                            <div class="question-content">
                                                <div class="mb-2">

                                                    <span class="badge badge-info">
                                                        <?php echo ucwords(str_replace('_', ' ', $q['QUESTIONTYPE'])); ?>
                                                    </span>
                                                    <span class="badge badge-success">
                                                        <?php echo $q['POINTS']; ?> pts
                                                    </span>

                                                    <span
                                                        class="badge badge-<?php echo $q['ISREQUIRED'] ? 'danger' : 'secondary'; ?> badge-required">
                                                        <?php echo $q['ISREQUIRED'] ? 'Required' : 'Optional'; ?>
                                                    </span>
                                                </div>

                                                <h5 class="mb-2"><?php echo nl2br(htmlspecialchars($q['QUESTION'])); ?>
                                                </h5>

                                                <?php if ($q['QUESTIONTYPE'] == 'multiple_choice' && $q['OPTIONS']): ?>
                                                <div class="option-list">
                                                    <strong>Options:</strong>
                                                    <ul class="mb-0">
                                                        <?php 
                                            $options = json_decode($q['OPTIONS'], true);
                                            foreach ($options as $option): 
                                            ?>
                                                        <li><?php echo htmlspecialchars($option); ?></li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                                <?php endif; ?>

                                                <?php if ($q['CORRECTANSWER']): ?>
                                                <div class="expected-answer">
                                                    <small>
                                                        <i class="lni lni-checkmark-circle"></i>
                                                        <strong>Expected Answer:</strong>
                                                        <?php echo htmlspecialchars($q['CORRECTANSWER']); ?>
                                                    </small>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="question-actions">
                                            <a href="?jobid=<?php echo $jobId; ?>&edit_question=<?php echo $q['QUESTIONID']; ?>"
                                                class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="lni lni-pencil"></i>
                                            </a>
                                            <a href="?jobid=<?php echo $jobId; ?>&delete_question=<?php echo $q['QUESTIONID']; ?>"
                                                class="btn btn-sm btn-outline-danger" title="Delete"
                                                onclick="return confirm('Are you sure you want to delete this question? This action cannot be undone.')">
                                                <i class="lni lni-trash-can"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>

                                <!-- Summary -->
                                <div class="alert alert-info mt-3">
                                    <i class="lni lni-information"></i>
                                    <strong>Note:</strong> These questions will be shown to applicants when they apply
                                    for this job.
                                    Answers are used for screening and can be scored automatically.
                                </div>
                                <?php else: ?>
                                <div class="empty-state">
                                    <i class="lni lni-empty-file"></i>
                                    <h5 class="mt-3">No Screening Questions Yet</h5>
                                    <p class="text-muted">Add questions to screen applicants before they submit their
                                        application.</p>
                                    <p class="text-muted">This helps you filter candidates more effectively.</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>


                        <a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>

                    </div>

                    <!-- Footer -->
                    <?php include('footer.php'); ?>

                    <!-- Scripts -->
                    <script src="assets/js/jquery.min.js"></script>
                    <script src="assets/js/popper.min.js"></script>
                    <script src="assets/js/bootstrap.min.js"></script>
                    <script src="assets/js/slick.js"></script>
                    <script src="assets/js/slider-bg.js"></script>
                    <script src="assets/js/smoothproducts.js"></script>
                    <script src="assets/js/snackbar.min.js"></script>
                    <script src="assets/js/jQuery.style.switcher.js"></script>
                    <script src="assets/js/custom.js"></script>

                    <script>
                    // Show/hide options based on question type
                    document.getElementById('questionType').addEventListener('change', function() {
                        const optionsContainer = document.getElementById('optionsContainer');
                        if (this.value === 'multiple_choice') {
                            optionsContainer.style.display = 'block';
                        } else {
                            optionsContainer.style.display = 'none';
                        }
                    });

                    // Add more options for multiple choice
                    function addOption() {
                        const optionsList = document.getElementById('optionsList');
                        const optionCount = optionsList.getElementsByClassName('option-input-group').length + 1;

                        const optionGroup = document.createElement('div');
                        optionGroup.className = 'option-input-group';
                        optionGroup.innerHTML = `
        <input type="text" name="options[]" class="form-control" placeholder="Option ${optionCount}">
        <i class="lni lni-trash-can remove-option" onclick="removeOption(this)"></i>
    `;

                        optionsList.appendChild(optionGroup);
                    }

                    // Remove option
                    function removeOption(element) {
                        const optionsList = document.getElementById('optionsList');
                        if (optionsList.getElementsByClassName('option-input-group').length > 2) {
                            element.parentElement.remove();
                        } else {
                            alert('You need at least 2 options for multiple choice questions.');
                        }
                    }

                    // Use template
                    function useTemplate(template) {
                        // Set question text
                        document.querySelector('textarea[name="question"]').value = template.question;

                        // Set question type
                        document.getElementById('questionType').value = template.type;
                        document.getElementById('questionType').dispatchEvent(new Event('change'));

                        // Set correct answer if exists
                        if (template.answer) {
                            document.querySelector('input[name="correct_answer"]').value = template.answer;
                        }

                        // Set points
                        document.querySelector('input[name="points"]').value = template.points;

                        // Handle multiple choice options
                        if (template.type === 'multiple_choice' && template.options) {
                            setTimeout(function() {
                                const optionsList = document.getElementById('optionsList');
                                optionsList.innerHTML = '';

                                template.options.forEach(function(option, index) {
                                    const optionGroup = document.createElement('div');
                                    optionGroup.className = 'option-input-group';
                                    optionGroup.innerHTML = `
                    <input type="text" name="options[]" class="form-control" value="${option}" placeholder="Option ${index + 1}">
                    <i class="lni lni-trash-can remove-option" onclick="removeOption(this)"></i>
                `;
                                    optionsList.appendChild(optionGroup);
                                });
                            }, 100);
                        }

                        // Collapse templates
                        $('#templatesCollapse').collapse('hide');

                        // Scroll to form
                        $('html, body').animate({
                            scrollTop: $('#questionForm').offset().top - 100
                        }, 500);
                    }

                    // Form validation
                    document.getElementById('questionForm').addEventListener('submit', function(e) {
                        const questionType = document.getElementById('questionType').value;

                        if (questionType === 'multiple_choice') {
                            const options = document.querySelectorAll('input[name="options[]"]');
                            let filledOptions = 0;

                            options.forEach(function(option) {
                                if (option.value.trim() !== '') {
                                    filledOptions++;
                                }
                            });

                            if (filledOptions < 2) {
                                e.preventDefault();
                                alert('Please provide at least 2 options for multiple choice questions.');
                                return false;
                            }
                        }
                    });
                    </script>

    </body>

</html>