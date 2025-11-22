<!DOCTYPE html>
<html lang="zxx">

<?php
require('./mysqli_connect.php');
include 'session_check.php';




$userId = $session_id;
$jobId = isset($_GET['jobid']) ? (int)$_GET['jobid'] : 0;

// Get job details
$jobQuery = "SELECT j.*, c.COMPANYNAME, c.COMPANYADDRESS 
             FROM tbljob j 
             LEFT JOIN tblcompany c ON j.COMPANYID = c.COMPANYID 
             WHERE j.JOBID = ? AND j.JOBSTATUS = 'Active'";
$stmt = mysqli_prepare($con, $jobQuery);
mysqli_stmt_bind_param($stmt, "i", $jobId);
mysqli_stmt_execute($stmt);
$jobResult = mysqli_stmt_get_result($stmt);
$job = mysqli_fetch_assoc($jobResult);

if (!$job) {
    $_SESSION['error_msg'] = "Job not found or is no longer available.";
    header("Location: job-list-v1.php");
    exit();
}


include "include/helper.php";

?>

<?php include 'include/head.php' ?>

<link rel="stylesheet" href="assets/css/apply-job.css">

<body>
    <div id="main-wrapper">
        <!-- Header -->
        <?php include 'include/header.php' ?>
        <div class="clearfix"></div>

        <!-- Main Content -->
        <section class="bg-light py-5 position-relative">
            <div class="container">
                <div class="application-container">

                    <!-- Job Header -->
                    <div class="job-header theme-bg">
                        <h2><i class="lni lni-briefcase"></i> <?php echo esc($job['JOBTITLE']); ?></h2>
                        <p><i class="lni lni-building"></i> <strong><?php echo esc($job['COMPANYNAME']); ?></strong></p>
                        <p><i class="lni lni-map-marker"></i> <?php echo esc($job['COMPANYADDRESS']); ?></p>
                        <p><i class="lni lni-timer"></i> Deadline:
                            <?php echo date('F d, Y', strtotime($job['DEADLINE'])); ?></p>
                    </div>

                    <!-- Alert Messages -->
                    <?php if (isset($_SESSION['error_msg'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="lni lni-cross-circle"></i> <?php echo $_SESSION['error_msg']; ?>
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                    <?php unset($_SESSION['error_msg']); endif; ?>

                    <!-- Progress Indicator -->
                    <div class="progress-container">
                        <div class="progress-steps">
                            <div class="progress-bar-fill" id="progressBarFill"></div>

                            <div class="step active" data-step="1">
                                <div class="step-circle">1</div>
                                <div class="step-label">Your Info</div>
                            </div>

                            <div class="step" data-step="2">
                                <div class="step-circle">2</div>
                                <div class="step-label">Resume</div>
                            </div>

                            <?php if ($totalQuestions > 0): ?>
                            <div class="step" data-step="3">
                                <div class="step-circle">3</div>
                                <div class="step-label">Screening</div>
                            </div>

                            <div class="step" data-step="4">
                                <div class="step-circle">4</div>
                                <div class="step-label">Cover Letter</div>
                            </div>

                            <div class="step" data-step="5">
                                <div class="step-circle">5</div>
                                <div class="step-label">Review</div>
                            </div>
                            <?php else: ?>
                            <div class="step" data-step="3">
                                <div class="step-circle">3</div>
                                <div class="step-label">Cover Letter</div>
                            </div>

                            <div class="step" data-step="4">
                                <div class="step-circle">4</div>
                                <div class="step-label">Review</div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Application Form -->
                    <form method="POST" id="applicationForm" enctype="multipart/form-data">

                        <!-- STEP 1: Your Information -->
                        <div class="step-content active" data-step="1">
                            <div class="section-card">
                                <h3 class="section-title">
                                    <i class="lni lni-user"></i> Your Information
                                </h3>

                                <div class="section-card">
                                    <!-- Basic Info -->
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="rounded-circle bg-secondary text-white text-center"
                                            style="width:56px;height:56px;line-height:56px;font-weight:600;">
                                            <?php echo ($name ? esc(mb_substr($name,0,1)) : 'U'); ?>
                                        </div>
                                        <div class="ml-3">
                                            <?php if ($name): ?>
                                            <div><strong><?php echo esc($name); ?></strong></div>
                                            <?php endif; ?>
                                            <?php if ($email): ?>
                                            <div class="text-muted small"><?php echo esc($email); ?></div>
                                            <?php endif; ?>
                                            <?php if ($phone): ?>
                                            <div class="text-muted small"><?php echo esc($phone); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Location -->
                                    <?php if ($city || $country): ?>
                                    <div class="mb-2">
                                        <strong>Location:</strong>
                                        <span class="text-muted">
                                            <?php echo esc(trim($city . ($city && $country ? ', ' : '') . $country)); ?>
                                        </span>
                                    </div>
                                    <?php endif; ?>

                                    <!-- About -->
                                    <?php if ($about): ?>
                                    <div class="mb-2">
                                        <strong>About</strong>
                                        <p class="mb-0 text-muted"><?php
                                            $trimmed = mb_strlen($about) > 250 ? mb_substr($about,0,250) . '…' : $about;
                                            echo esc($trimmed);
                                        ?></p>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Highlights -->
                                    <div class="row mt-3">
                                        <?php if ($degree || $school): ?>
                                        <div class="col-md-4 mb-2">
                                            <small class="text-muted">Education</small>
                                            <div><?php echo $degree ? '<strong>'.esc($degree).'</strong>' : ''; ?>
                                                <?php echo $school ? '<div class="text-muted small">'.esc($school).'</div>' : ''; ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <?php if ($exCompany || $exTitle): ?>
                                        <div class="col-md-4 mb-2">
                                            <small class="text-muted">Experience</small>
                                            <div><?php echo $exTitle ? '<strong>'.esc($exTitle).'</strong>' : ''; ?>
                                                <?php echo $exCompany ? '<div class="text-muted small">'.esc($exCompany).'</div>' : ''; ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <?php if ($skills): ?>
                                        <div class="col-md-4 mb-2">
                                            <small class="text-muted">Skills</small>
                                            <div><small
                                                    class="label label-light text-dark"><?php echo esc($skills); ?></small>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="alert alert-info-custom mt-3 mb-0">
                                        <i class="lni lni-information"></i>
                                        <small>Make sure your profile information is up to date.
                                            <a href="dashboard/applicant/dashboard-my-profile.php"
                                                target="_blank">Update Profile</a></small>
                                    </div>
                                </div>

                                <div class="form-navigation">
                                    <div></div>
                                    <button type="button" class="btn btn-next theme-bg" onclick="nextStep()">
                                        Next <i class="lni lni-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- STEP 2: Upload Resume -->
                        <div class="step-content" data-step="2">
                            <div class="section-card">
                                <h3 class="section-title">
                                    <i class="lni lni-files"></i> Upload Your Resume
                                </h3>

                                <div class="alert alert-info-custom">
                                    <i class="lni lni-information"></i>
                                    <?php if ($user && ($user['RESUME'] ?? false)): ?>
                                    <small>Resume is available on your profile. Upload a different resume here only if
                                        you want to submit a new version.</small>
                                    <?php else: ?>
                                    <small>Please upload your resume/CV. Accepted formats: PDF, DOC, DOCX (Max
                                        5MB)</small>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label class="dropzone" id="dropzone" tabindex="0">
                                        <input type="file" id="fileInput" class="custom-file-input" name="fileToUpload"
                                            accept=".pdf,.doc,.docx,.rtf,.txt" />
                                        <div class="drop-inner">
                                            <div class="dz-icon">↑</div>
                                            <div class="dz-text">
                                                <strong>Drop files here</strong>
                                                <div class="or">or <span class="select-link">Select files</span></div>
                                                <div class="hint">Upload a file (5MB max)</div>
                                            </div>
                                        </div>
                                    </label>

                                    <div id="filePreview" class="file-preview" hidden>
                                        <div class="file-thumb" id="fileThumb">PDF</div>
                                        <div class="file-meta">
                                            <div class="file-name" id="fileName"></div>
                                            <div class="file-size" id="fileSize"></div>
                                            <div class="file-status" id="fileStatus">✓ Ready to Upload!</div>
                                        </div>
                                        <button type="button" class="file-remove" id="fileRemove"
                                            aria-label="Remove file">🗑️</button>
                                    </div>

                                    <div id="uploadError" class="upload-error" hidden></div>
                                </div>

                                <div class="form-navigation">
                                    <button type="button" class="btn btn-prev" onclick="prevStep()">
                                        <i class="lni lni-arrow-left"></i> Previous
                                    </button>
                                    <button type="button" class="btn btn-next theme-bg" onclick="nextStep()">
                                        Next <i class="lni lni-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- STEP 3: Screening Questions (if any) -->
                        <?php if ($totalQuestions > 0): ?>
                        <div class="step-content" data-step="3">
                            <div class="section-card">
                                <h3 class="section-title">
                                    <i class="lni lni-question-circle"></i> Screening Questions
                                </h3>

                                <div class="alert alert-info-custom mb-4">
                                    <strong><i class="lni lni-lightbulb"></i> Important:</strong>
                                    Please answer the following questions carefully. Your responses will be used to
                                    evaluate your application.
                                    Questions with (<strong class="text-danger">*</strong>) must be answered.
                                </div>

                                <?php 
                                $qNum = 1; 
                                while ($q = mysqli_fetch_assoc($questions)): 
                                    $questionId = $q['QUESTIONID'];
                                    $isRequired = $q['ISREQUIRED'];
                                ?>

                                <div class="question-box">
                                    <div class="question-header">
                                        <div class="question-number"><?php echo $qNum++; ?></div>
                                        <div class="question-text">
                                            <?php echo nl2br(htmlspecialchars($q['QUESTION'])); ?>
                                            <?php if ($isRequired): ?>
                                            <span class="text-danger">*</span>
                                            <!-- <span class="required-badge small">Required</span> -->
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="question-answer">
                                        <?php if ($q['QUESTIONTYPE'] == 'yes_no'): ?>
                                        <!-- Yes/No Question -->
                                        <div class="radio-option">
                                            <input type="radio" name="answers[<?php echo $questionId; ?>]" value="Yes"
                                                class="form-check-input" id="yes_<?php echo $questionId; ?>"
                                                <?php echo $isRequired ? 'required' : ''; ?>>
                                            <label class="form-check-label ml-2" for="yes_<?php echo $questionId; ?>">
                                                <i class="lni lni-checkmark-circle"></i> Yes
                                            </label>
                                        </div>
                                        <div class="radio-option">
                                            <input type="radio" name="answers[<?php echo $questionId; ?>]" value="No"
                                                class="form-check-input" id="no_<?php echo $questionId; ?>"
                                                <?php echo $isRequired ? 'required' : ''; ?>>
                                            <label class="form-check-label ml-2" for="no_<?php echo $questionId; ?>">
                                                <i class="lni lni-cross-circle"></i> No
                                            </label>
                                        </div>

                                        <?php elseif ($q['QUESTIONTYPE'] == 'multiple_choice'): ?>
                                        <!-- Multiple Choice Question -->
                                        <?php 
                                        $options = json_decode($q['OPTIONS'], true);
                                        foreach ($options as $index => $option): 
                                        ?>
                                        <div class="radio-option">
                                            <input type="radio" name="answers[<?php echo $questionId; ?>]"
                                                value="<?php echo htmlspecialchars($option); ?>"
                                                class="form-check-input"
                                                id="option_<?php echo $questionId; ?>_<?php echo $index; ?>"
                                                <?php echo $isRequired ? 'required' : ''; ?>>
                                            <label class="form-check-label ml-2"
                                                for="option_<?php echo $questionId; ?>_<?php echo $index; ?>">
                                                <?php echo htmlspecialchars($option); ?>
                                            </label>
                                        </div>
                                        <?php endforeach; ?>

                                        <?php elseif ($q['QUESTIONTYPE'] == 'text'): ?>
                                        <!-- Text Answer -->
                                        <textarea name="answers[<?php echo $questionId; ?>]" class="form-control"
                                            rows="4" <?php echo $isRequired ? 'required' : ''; ?>
                                            placeholder="Type your answer here..."></textarea>
                                        <small class="form-text text-muted">
                                            <i class="lni lni-text-format"></i> Provide a detailed answer
                                        </small>

                                        <?php elseif ($q['QUESTIONTYPE'] == 'number'): ?>
                                        <!-- Number Input -->
                                        <input type="number" name="answers[<?php echo $questionId; ?>]"
                                            class="form-control" <?php echo $isRequired ? 'required' : ''; ?>
                                            placeholder="Enter a number" min="0" step="any">
                                        <small class="form-text text-muted">
                                            <i class="lni lni-calculator"></i> Enter a numeric value
                                        </small>

                                        <?php elseif ($q['QUESTIONTYPE'] == 'date'): ?>
                                        <!-- Date Input -->
                                        <input type="date" name="answers[<?php echo $questionId; ?>]"
                                            class="form-control" <?php echo $isRequired ? 'required' : ''; ?>>
                                        <small class="form-text text-muted">
                                            <i class="lni lni-calendar"></i> Select a date
                                        </small>

                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php endwhile; ?>

                                <div class="form-navigation">
                                    <button type="button" class="btn btn-prev" onclick="prevStep()">
                                        <i class="lni lni-arrow-left"></i> Previous
                                    </button>
                                    <button type="button" class="btn btn btn-next theme-bg" onclick="nextStep()">
                                        Next <i class="lni lni-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- STEP: Cover Letter -->
                        <div class="step-content" data-step="<?php echo $totalQuestions > 0 ? '4' : '3'; ?>">
                            <div class="section-card">
                                <h3 class="section-title">
                                    <i class="lni lni-text-format"></i> Cover Letter (Optional)
                                </h3>

                                <div class="alert alert-info-custom mb-3">
                                    <i class="lni lni-lightbulb"></i>
                                    <small>A well-written cover letter can significantly increase your chances of
                                        getting an interview.
                                        Explain why you're interested in this position and what makes you a great
                                        fit.</small>
                                </div>

                                <div class="form-group">
                                    <textarea name="cover_letter" id="coverLetter" class="form-control" rows="8"
                                        placeholder="Dear Hiring Manager,&#10;&#10;I am writing to express my strong interest in the <?php echo esc($job['JOBTITLE']); ?> position at <?php echo esc($job['COMPANYNAME']); ?>..."></textarea>
                                    <small class="form-text text-muted char-counter">
                                        Characters: <span class="current">0</span>/2000
                                    </small>
                                </div>

                                <div class="form-navigation">
                                    <button type="button" class="btn btn-prev" onclick="prevStep()">
                                        <i class="lni lni-arrow-left"></i> Previous
                                    </button>
                                    <button type="button" class="btn btn-next theme-bg" onclick="nextStep()">
                                        Next <i class="lni lni-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- STEP: Review & Submit -->
                        <div class="step-content" data-step="<?php echo $totalQuestions > 0 ? '5' : '4'; ?>">
                            <div class="section-card">
                                <h3 class="section-title">
                                    <i class="lni lni-checkmark-circle"></i> Review Your Application
                                </h3>

                                <div class="alert alert-info-custom mb-4">
                                    <strong><i class="lni lni-information"></i> Almost Done!</strong>
                                    Please review your application before submitting. You can go back to edit any
                                    section.
                                </div>

                                <!-- Review: Personal Info -->
                                <div class="review-section">
                                    <h5 class="mb-3">
                                        <i class="lni lni-user"></i> Personal Information
                                        <span class="edit-link float-right" onclick="goToStep(1)">
                                            <i class="lni lni-pencil"></i> Edit
                                        </span>
                                    </h5>
                                    <div class="review-item">
                                        <div class="review-label">Name</div>
                                        <div class="review-value"><?php echo esc($name) ?: 'Not provided'; ?></div>
                                    </div>
                                    <div class="review-item">
                                        <div class="review-label">Email</div>
                                        <div class="review-value"><?php echo esc($email) ?: 'Not provided'; ?></div>
                                    </div>
                                    <div class="review-item">
                                        <div class="review-label">Phone</div>
                                        <div class="review-value"><?php echo esc($phone) ?: 'Not provided'; ?></div>
                                    </div>
                                    <div class="review-item">
                                        <div class="review-label">Location</div>
                                        <div class="review-value">
                                            <?php echo esc(trim($city . ($city && $country ? ', ' : '') . $country)) ?: 'Not provided'; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Review: Resume -->
                                <div class="review-section">
                                    <h5 class="mb-3">
                                        <i class="lni lni-files"></i> Resume
                                        <span class="edit-link float-right" onclick="goToStep(2)">
                                            <i class="lni lni-pencil"></i> Edit
                                        </span>
                                    </h5>
                                    <div class="review-item">
                                        <div class="review-label">Resume File</div>
                                        <div class="review-value" id="reviewResume">
                                            <?php if ($user && ($user['RESUME'] ?? false)): ?>
                                            <i class="lni lni-checkmark-circle text-success"></i> Existing resume on
                                            file
                                            <?php else: ?>
                                            <span class="text-muted">No file selected</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Review: Screening Questions -->
                                <?php if ($totalQuestions > 0): ?>
                                <div class="review-section">
                                    <h5 class="mb-3">
                                        <i class="lni lni-question-circle"></i> Screening Questions
                                        <span class="edit-link float-right" onclick="goToStep(3)">
                                            <i class="lni lni-pencil"></i> Edit
                                        </span>
                                    </h5>
                                    <div class="review-item">
                                        <div class="review-value" id="reviewScreening">
                                            <span class="text-muted">Your answers will be reviewed</span>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <!-- Review: Cover Letter -->
                                <div class="review-section">
                                    <h5 class="mb-3">
                                        <i class="lni lni-text-format"></i> Cover Letter
                                        <span class="edit-link float-right"
                                            onclick="goToStep(<?php echo $totalQuestions > 0 ? '4' : '3'; ?>)">
                                            <i class="lni lni-pencil"></i> Edit
                                        </span>
                                    </h5>
                                    <div class="review-item">
                                        <div class="review-value" id="reviewCoverLetter">
                                            <span class="text-muted">Not provided</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Terms and Submit -->
                                <div class="mt-4 p-4 bg-white rounded border">
                                    <div class="custom-control custom-checkbox mb-3">
                                        <input type="checkbox" class="checkbox mr-2" id="agreeTerms" required>
                                        <label class="custom-control-label" for="agreeTerms">
                                            <strong>I confirm that all information provided is accurate and
                                                complete.</strong>
                                            I understand that providing false information may result in
                                            disqualification.
                                        </label>
                                    </div>

                                    <div class="form-navigation">
                                        <button type="button" class="btn btn-prev" onclick="prevStep()">
                                            <i class="lni lni-arrow-left"></i> Previous
                                        </button>
                                        <button type="submit" name="submit_application" class="btn btn-next theme-bg">
                                            <i class="lni lni-checkmark-circle"></i> Submit Application
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>

                    <!-- Back to Job Link -->
                    <div class="text-center mt-4">
                        <a href="job-detail.php?jobid=<?php echo $jobId; ?>" class="text-muted">
                            <i class="lni lni-arrow-left"></i> Back to Job Details
                        </a>
                    </div>

                </div>
            </div>
        </section>

        <!-- Footer -->
        <?php include 'include/footer.php' ?>

        <a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>
    </div>

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
    // Multi-Step Form Logic
    let currentStep = 1;
    const totalSteps = <?php echo $totalQuestions > 0 ? 5 : 4; ?>;

    function updateProgressBar() {
        const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
        document.getElementById('progressBarFill').style.width = progress + '%';

        // Update step indicators
        document.querySelectorAll('.step').forEach((step, index) => {
            const stepNum = index + 1;
            step.classList.remove('active', 'completed');

            if (stepNum < currentStep) {
                step.classList.add('completed');
            } else if (stepNum === currentStep) {
                step.classList.add('active');
            }
        });
    }

    function showStep(step) {
        // Hide all steps
        document.querySelectorAll('.step-content').forEach(content => {
            content.classList.remove('active');
        });

        // Show current step
        const currentContent = document.querySelector(`.step-content[data-step="${step}"]`);
        if (currentContent) {
            currentContent.classList.add('active');
        }

        currentStep = step;
        updateProgressBar();

        // Scroll to top smoothly
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    function nextStep() {
        // Validate current step before moving forward
        if (validateCurrentStep()) {
            if (currentStep < totalSteps) {
                showStep(currentStep + 1);
            }
        }
    }

    function prevStep() {
        if (currentStep > 1) {
            showStep(currentStep - 1);
        }
    }

    function goToStep(step) {
        if (step >= 1 && step <= totalSteps) {
            showStep(step);
        }
    }

    function validateCurrentStep() {
        const currentContent = document.querySelector(`.step-content[data-step="${currentStep}"]`);

        if (!currentContent) return true;

        // Check required fields in current step
        const requiredInputs = currentContent.querySelectorAll('[required]');
        let allValid = true;
        let firstInvalid = null;

        requiredInputs.forEach(input => {
            if (input.type === 'radio') {
                const name = input.name;
                const checked = currentContent.querySelector(`input[name="${name}"]:checked`);
                if (!checked) {
                    allValid = false;
                    if (!firstInvalid) firstInvalid = input.closest('.question-box');
                }
            } else if (input.type === 'checkbox') {
                if (!input.checked) {
                    allValid = false;
                    if (!firstInvalid) firstInvalid = input;
                }
            } else {
                if (!input.value.trim()) {
                    allValid = false;
                    if (!firstInvalid) firstInvalid = input;
                }
            }
        });

        if (!allValid) {
            alert('Please fill in all required fields before proceeding.');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
            return false;
        }

        // Update review section when moving to last step
        if (currentStep === totalSteps - 1) {
            updateReviewSection();
        }

        return true;
    }

    function updateReviewSection() {
        // Update resume review
        const fileName = document.getElementById('fileName');
        const reviewResume = document.getElementById('reviewResume');
        if (fileName && fileName.textContent) {
            reviewResume.innerHTML = '<i class="lni lni-checkmark-circle text-success"></i> ' + fileName.textContent;
        }

        // Update cover letter review
        const coverLetter = document.getElementById('coverLetter');
        const reviewCoverLetter = document.getElementById('reviewCoverLetter');
        if (coverLetter && coverLetter.value.trim()) {
            const preview = coverLetter.value.substring(0, 150) + (coverLetter.value.length > 150 ? '...' : '');
            reviewCoverLetter.innerHTML = '<div class="text-dark">' + escapeHtml(preview) + '</div>';
        } else {
            reviewCoverLetter.innerHTML = '<span class="text-muted">Not provided</span>';
        }

        // Update screening questions review
        <?php if ($totalQuestions > 0): ?>
        const reviewScreening = document.getElementById('reviewScreening');
        const answeredCount = document.querySelectorAll(
            'input[name^="answers"]:checked, textarea[name^="answers"]:not(:placeholder-shown), input[type="number"][name^="answers"]:not(:placeholder-shown), input[type="date"][name^="answers"]:not(:placeholder-shown)'
        ).length;
        reviewScreening.innerHTML = '<i class="lni lni-checkmark-circle text-success"></i> ' + answeredCount +
            ' question(s) answered';
        <?php endif; ?>
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // File Upload Logic
    (function() {
        const MAX_BYTES = 5 * 1024 * 1024; // 5MB
        const dropzone = document.getElementById('dropzone');
        const input = document.getElementById('fileInput');
        const preview = document.getElementById('filePreview');
        const thumb = document.getElementById('fileThumb');
        const nameEl = document.getElementById('fileName');
        const sizeEl = document.getElementById('fileSize');
        const statusEl = document.getElementById('fileStatus');
        const removeBtn = document.getElementById('fileRemove');
        const err = document.getElementById('uploadError');

        function formatBytes(bytes) {
            if (bytes === 0) return '0 B';
            const units = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(1024));
            return (bytes / Math.pow(1024, i)).toFixed(2) + ' ' + units[i];
        }

        function showError(message) {
            err.hidden = false;
            err.textContent = message;
        }

        function clearError() {
            err.hidden = true;
            err.textContent = '';
        }

        function handleFile(file) {
            clearError();
            if (!file) return;

            if (file.size > MAX_BYTES) {
                showError('File is too large. Maximum allowed size is 5MB.');
                return;
            }

            // Update preview
            nameEl.textContent = file.name;
            sizeEl.textContent = formatBytes(file.size);

            // Thumbnail
            const ext = file.name.split('.').pop().toLowerCase();
            if (['png', 'jpg', 'jpeg', 'gif', 'webp'].includes(ext)) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    thumb.style.backgroundImage = `url(${e.target.result})`;
                    thumb.textContent = '';
                    thumb.style.backgroundSize = 'cover';
                    thumb.style.backgroundPosition = 'center';
                }
                reader.readAsDataURL(file);
            } else {
                thumb.style.backgroundImage = '';
                thumb.textContent = ext.toUpperCase() === 'PDF' ? 'PDF' : ext.toUpperCase();
            }

            preview.hidden = false;
            statusEl.textContent = '✓ Ready to Upload!';
            statusEl.style.color = '#10b981';
        }

        // Drag & drop
        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.style.borderColor = '#862633';
        });

        dropzone.addEventListener('dragleave', () => {
            dropzone.style.borderColor = '#d6d6d6';
        });

        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.style.borderColor = '#d6d6d6';
            const f = e.dataTransfer.files[0];
            input.files = e.dataTransfer.files;
            handleFile(f);
        });

        // Click to open
        dropzone.addEventListener('click', () => input.click());

        dropzone.querySelector('.select-link').addEventListener('click', (e) => {
            e.stopPropagation();
            input.click();
        });

        // Keyboard access
        dropzone.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                input.click();
            }
        });

        // File selected
        input.addEventListener('change', (e) => handleFile(e.target.files[0]));

        // Remove file
        removeBtn.addEventListener('click', () => {
            input.value = '';
            preview.hidden = true;
            thumb.style.backgroundImage = '';
            thumb.textContent = 'PDF';
            clearError();
        });
    })();

    // Character counter for cover letter
    const coverLetter = document.getElementById('coverLetter');
    if (coverLetter) {
        const maxLength = 2000;
        const counter = coverLetter.nextElementSibling.querySelector('.current');

        coverLetter.addEventListener('input', function() {
            const length = this.value.length;
            counter.textContent = length;

            if (length > maxLength) {
                this.value = this.value.substring(0, maxLength);
                counter.parentElement.classList.add('text-danger');
            } else {
                counter.parentElement.classList.remove('text-danger');
            }
        });
    }

    // Form submission validation
    document.getElementById('applicationForm').addEventListener('submit', function(e) {
        if (!validateCurrentStep()) {
            e.preventDefault();
            return false;
        }

        // Final confirmation
        if (!confirm(
                'Are you sure you want to submit your application? Please review all information before submitting.'
            )) {
            e.preventDefault();
            return false;
        }
    });

    // Auto-save to localStorage
    const jobId = <?php echo $jobId; ?>;
    const userId = <?php echo $userId; ?>;
    const storageKey = `application_${userId}_${jobId}`;

    // Load saved data on page load
    window.addEventListener('DOMContentLoaded', function() {
        const savedData = localStorage.getItem(storageKey);
        if (savedData) {
            try {
                const data = JSON.parse(savedData);

                // Restore form data
                Object.keys(data).forEach(key => {
                    const inputs = document.querySelectorAll(`[name="${key}"]`);
                    inputs.forEach(input => {
                        if (input.type === 'radio') {
                            if (input.value === data[key]) {
                                input.checked = true;
                            }
                        } else if (input.type === 'checkbox') {
                            input.checked = data[key];
                        } else {
                            input.value = data[key];
                        }
                    });
                });

                // Ask user if they want to continue
                if (confirm(
                        'We found a previously saved draft. Would you like to continue from where you left off?'
                    )) {
                    console.log('Draft restored');
                } else {
                    localStorage.removeItem(storageKey);
                }
            } catch (e) {
                console.error('Error loading draft:', e);
            }
        }
    });

    // Save data on change
    document.querySelectorAll('input, textarea').forEach(element => {
        element.addEventListener('change', function() {
            const formData = {};

            document.querySelectorAll('input:not([type="file"]), textarea').forEach(input => {
                const name = input.name;
                if (name) {
                    if (input.type === 'radio') {
                        if (input.checked) {
                            formData[name] = input.value;
                        }
                    } else if (input.type === 'checkbox') {
                        formData[name] = input.checked;
                    } else {
                        formData[name] = input.value;
                    }
                }
            });

            localStorage.setItem(storageKey, JSON.stringify(formData));
        });
    });

    // Clear saved data on successful submit
    document.getElementById('applicationForm').addEventListener('submit', function() {
        localStorage.removeItem(storageKey);
    });

    // Initialize
    updateProgressBar();
    </script>

</body>

</html>