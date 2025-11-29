<!DOCTYPE html>
<html lang="en">

<?php
require('./mysqli_connect.php');
include 'session_check.php';
include "include/helper.php";
?>

<?php include 'include/head.php' ?>

<head>
    <link rel="stylesheet" href="assets/css/main.css">
</head>

<body>
    <div id="main-wrapper">
        <?php include 'include/header.php' ?>
        <div class="clearfix"></div>



        <div class="gray py-3">
            <div class="container">
                <div class="row">
                    <div class="colxl-12 col-lg-12 col-md-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active">About Us</li>
                            </ol>
                        </nav>
                        <h1>About MUNext</h1>
                        <p>Connecting Memorial University Talent with Opportunity</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main About Section -->
        <section class="about-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <h2 class="section-title">Welcome to MUNext</h2>
                        <p class="lead mb-4">Your dedicated career platform connecting Memorial University students,
                            alumni, and employers in a seamless recruitment ecosystem.</p>

                        <p class="mb-4">MUNext is a comprehensive job board platform specifically designed to bridge the
                            gap between Memorial University's talented student body and employers seeking exceptional
                            candidates. Built with the unique needs of the MUN community in mind, we provide a
                            specialized space where academic excellence meets professional opportunity.</p>

                        <p class="mb-4">Whether you're a student looking for your first internship, an alumnus seeking
                            career advancement, or an employer searching for MUN's best and brightest, MUNext is your
                            gateway to meaningful professional connections.</p>

                        <a href="browse-jobs.php" class="btn btn-custom">
                            <i class="lni lni-briefcase mr-2"></i>Explore Opportunities
                        </a>
                    </div>
                    <div class="col-lg-6">
                        <img src="assets/img/about-1.png" class="img-fluid rounded shadow-lg" alt="MUNext Platform" />
                    </div>
                </div>
            </div>
        </section>

        <!-- Mission & Vision Section -->
        <section class="about-section bg-light" id="mission">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="mission-card">
                            <div class="mission-icon">
                                <i class="lni lni-target"></i>
                            </div>
                            <h3 class="ft-bold mb-3" style="color: var(--primary-dark);">Our Mission</h3>
                            <p class="mb-0">To empower Memorial University students and alumni by providing a dedicated
                                platform that facilitates meaningful career connections, streamlines the job search
                                process, and supports the professional growth of the MUN community through innovative
                                recruitment solutions.</p>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="mission-card">
                            <div class="mission-icon">
                                <i class="lni lni-eye"></i>
                            </div>
                            <h3 class="ft-bold mb-3" style="color: var(--primary-dark);">Our Vision</h3>
                            <p class="mb-0">To become the premier employment hub for Memorial University, recognized for
                                connecting top-tier talent with exceptional career opportunities while fostering a
                                thriving professional community that extends beyond campus boundaries.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Statistics Section -->
        <section class="stats-section">
            <div class="container">
                <div class="row">
                    <div class="col-md-3 col-6">
                        <div class="stat-card">
                            <span class="stat-number" data-count="5000">1000+</span>
                            <span class="stat-label">Active Students</span>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-card">
                            <span class="stat-number" data-count="500">500+</span>
                            <span class="stat-label">Job Postings</span>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-card">
                            <span class="stat-number" data-count="200">200+</span>
                            <span class="stat-label">Partner Companies</span>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-card">
                            <span class="stat-number">95%</span>
                            <span class="stat-label">Success Rate</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- What We Offer Section -->
        <section class="about-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4 mb-lg-0 order-lg-2">
                        <h2 class="section-title ft-medium mb-4">What Makes MUNext Different?</h2>
                        <p class="mb-4">MUNext isn't just another job board – it's a purpose-built platform designed
                            specifically for the Memorial University community, offering features and benefits that set
                            us apart:</p>

                        <ul class="feature-list">
                            <li><strong>MUN-Focused Network:</strong> Connect exclusively with employers interested in
                                Memorial University talent</li>
                            <li><strong>Smart Matching:</strong> Advanced algorithms match candidates with opportunities
                                based on skills, experience, and preferences</li>
                            <li><strong>Comprehensive Profiles:</strong> Showcase your academic achievements, projects,
                                and professional experience</li>
                            <li><strong>Screening Questions:</strong> Employers can pre-qualify candidates to find the
                                best fit</li>
                            <li><strong>Real-Time Notifications:</strong> Stay updated on application status and new
                                opportunities</li>
                            <li><strong>Secure Platform:</strong> Your data is protected with enterprise-grade security
                            </li>
                            <li><strong>Mobile Friendly:</strong> Search and apply for jobs on any device, anywhere</li>
                            <li><strong>Career Resources:</strong> Access tips, guides, and support throughout your job
                                search</li>
                        </ul>
                    </div>
                    <div class="col-lg-6 order-lg-1">
                        <img src="assets/img/about-2.png" class="img-fluid rounded shadow-lg" alt="MUNext Features" />
                    </div>
                </div>
            </div>
        </section>

        <!-- Core Values Section -->
        <section class="about-section bg-light">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="section-title d-inline-block">Our Core Values</h2>
                    <p class="text-muted mt-3">The principles that guide everything we do at MUNext</p>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="value-card">
                            <div class="value-icon">
                                <i class="lni lni-users"></i>
                            </div>
                            <h4 class="ft-bold mb-3">Community First</h4>
                            <p class="mb-0">We prioritize the success and well-being of the Memorial University
                                community in every decision we make.</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="value-card">
                            <div class="value-icon">
                                <i class="lni lni-bulb"></i>
                            </div>
                            <h4 class="ft-bold mb-3">Innovation</h4>
                            <p class="mb-0">We continuously evolve our platform with cutting-edge features to enhance
                                the recruitment experience.</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="value-card">
                            <div class="value-icon">
                                <i class="lni lni-shield"></i>
                            </div>
                            <h4 class="ft-bold mb-3">Integrity</h4>
                            <p class="mb-0">We operate with transparency, honesty, and ethical practices in all our
                                interactions.</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="value-card">
                            <div class="value-icon">
                                <i class="lni lni-rocket"></i>
                            </div>
                            <h4 class="ft-bold mb-3">Excellence</h4>
                            <p class="mb-0">We strive for excellence in everything we deliver, ensuring the highest
                                quality experience for all users.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- Call to Action Section -->
        <section class="about-section"
            style="background: linear-gradient(135deg, #862633 0%, #6b1e28 100%); padding: 80px 0;">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 mx-auto text-center">
                        <h2 class="ft-bold text-white mb-4">Ready to Get Started?</h2>
                        <p class="text-white mb-4" style="font-size: 1.1rem; opacity: 0.9;">
                            Join thousands of Memorial University students and alumni who have found their dream careers
                            through MUNext.
                            Whether you're looking for internships, co-op positions, or full-time opportunities, we're
                            here to help you succeed.
                        </p>
                        <div class="mt-4">
                            <a href="login.php?role=applicants" class="btn btn-light ft-bold mr-3 mb-2 text-dark"
                                style="padding: 14px 35px; border-radius: 25px;">
                                <i class="lni lni-user mr-2"></i>Join as Candidate
                            </a>
                            <a href="login.php?role=employer" class="btn btn-outline-danger text-white ft-bold mb-2"
                                style="padding: 14px 35px; border-radius: 25px; border: 2px solid white;">
                                <i class="lni lni-briefcase mr-2"></i>Post Jobs
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- Development Team Section -->
        <section class="about-section">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="section-title d-inline-block">Meet the Development Team</h2>
                    <p class="text-muted mt-3">COMP 6905 Software Engineering - Group 7</p>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="team-card">
                            <div class="team-photo mx-auto team-role">
                                VM
                            </div>
                            <h5 class="ft-bold mb-2">Victor Muojeke</h5>
                            <p class="text-muted mb-0">Team Leader & Full Stack Developer</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="team-card">
                            <div class="team-photo mx-auto team-role">
                                CO
                            </div>
                            <h5 class="ft-bold mb-2">Chiemerie Obijiaku</h5>
                            <p class="text-muted mb-0">Full Stack & Lead Developer</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="team-card">
                            <div class="team-photo mx-auto team-role">
                                AO
                            </div>
                            <h5 class="ft-bold mb-2">Akinfenwa Oluwaseun</h5>
                            <p class="text-muted mb-0">Frontend Developer</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="team-card">
                            <div class="team-photo mx-auto team-role">
                                KE
                            </div>
                            <h5 class="ft-bold mb-2">Kelechukwu Ede</h5>
                            <p class="text-muted mb-0">Database Developer</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="team-card">
                            <div class="team-photo mx-auto team-role">
                                AI
                            </div>
                            <h5 class="ft-bold mb-2">Akefar Islam</h5>
                            <p class="text-muted mb-0">UI/UX Developer</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="team-card">
                            <div class="team-photo mx-auto team-role">
                                AE
                            </div>
                            <h5 class="ft-bold mb-2">Adiba Ekpunobi</h5>
                            <p class="text-muted mb-0">UI/UX Developer</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="team-card">
                            <div class="team-photo mx-auto team-role">
                                JO
                            </div>
                            <h5 class="ft-bold mb-2">Joshua Oseimobor</h5>
                            <p class="text-muted mb-0">Quality Assurance</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="team-card">
                            <div class="team-photo mx-auto team-role">
                                VO
                            </div>
                            <h5 class="ft-bold mb-2">Victor Onwosi</h5>
                            <p class="text-muted mb-0">System Integration</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>


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
    // Animate counter numbers
    $(document).ready(function() {
        $('.stat-number').each(function() {
            var $this = $(this);
            var countTo = $this.attr('data-count');

            if (countTo) {
                $({
                    countNum: 0
                }).animate({
                    countNum: countTo
                }, {
                    duration: 2000,
                    easing: 'linear',
                    step: function() {
                        $this.text(Math.floor(this.countNum) + '+');
                    },
                    complete: function() {
                        $this.text(this.countNum + '+');
                    }
                });
            }
        });
    });
    </script>
</body>

</html>