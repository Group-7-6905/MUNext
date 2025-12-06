<!DOCTYPE html>
<html lang="zxx">



<?php require 'include/phpcode.php';



    // Redirect if profile is already complete
    $querycomp = "SELECT * from tblcompany WHERE USERID = '$session_id'";
    $resultcomp = mysqli_query($con, $querycomp);
    $rowcomp = mysqli_fetch_array($resultcomp);

    $USERID = $rowcomp['USERID']  ?? '';
    if (!empty($USERID)) {
        header("Location: dashboard-company-edit.php");
    }
?>


<head>
    <meta charset="utf-8" />
    <meta name="author" content="Themezhub" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>MUNext - Creative Job Board</title>

    <!-- Custom CSS -->
    <link href="assets/css/styles.css" rel="stylesheet">
    <link href="assets/css/custom-style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.lineicons.com/3.0/lineicons.css">

</head>



<body>

    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <!-- <div class=" preloader">
    </div> -->

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
                            <h1 class="ft-medium">Create Your Company</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item text-muted"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item text-muted"><a href="#">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="#" class="theme-cl">Add Company</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="dashboard-widg-bar d-block">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12">
                            <div class="_dashboard_content bg-white rounded mb-4">
                                <div class="_dashboard_content_header br-bottom py-3 px-3">
                                    <div class="_dashboard__header_flex">
                                        <h4 class="mb-0 ft-medium fs-md"><i
                                                class="fa fa-file mr-1 theme-cl fs-sm"></i>Create Company</h4>
                                    </div>
                                </div>

                                <div class="_dashboard_content_body py-3 px-3">
                                    <form class="row" method="post" enctype="multipart/form-data">
                                        <div class="col-xl-12 col-lg-12 col-md-12">
                                            <div class="row">

                                                <div class="col-xl-12 col-lg-12 col-md-12">
                                                    <div class="form-group">
                                                        <label class="text-dark ft-medium">Company Name <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control rounded" value=""
                                                            name="name" placeholder="Company Name" required>
                                                    </div>
                                                </div>



                                                <div class="col-xl-6 col-lg-6 col-md-6">
                                                    <div class="form-group">
                                                        <label class="text-dark ft-medium">Company Email Address <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control rounded" value=""
                                                            name="email" placeholder="Email" required>
                                                    </div>
                                                </div>

                                                <div class="col-xl-6 col-lg-6 col-md-6">
                                                    <div class="form-group">
                                                        <label class="text-dark ft-medium">Phone No.</label>
                                                        <input type="number" class="form-control rounded" value=""
                                                            name="contact" placeholder="1 234 567 890">
                                                    </div>
                                                </div>


                                                <div class="col-xl-6 col-lg-6 col-md-6">
                                                    <div class="form-group">
                                                        <label class="text-dark ft-medium">Industry <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-control rounded" name="industry" required>
                                                            <option value="">Select Industry</option>
                                                            <option>Accounting & Auditing</option>
                                                            <option>Advertising & Marketing</option>
                                                            <option>Aerospace & Defense</option>
                                                            <option>Agriculture & Farming</option>
                                                            <option>Airlines & Aviation</option>
                                                            <option>Animation & VFX</option>
                                                            <option>Architecture & Planning</option>
                                                            <option>Arts & Crafts</option>
                                                            <option>Automotive & Vehicle Manufacturing</option>
                                                            <option>Banking & Financial Services</option>
                                                            <option>Biotechnology & Life Sciences</option>
                                                            <option>Broadcasting & Media Production</option>
                                                            <option>Building Materials & Construction</option>
                                                            <option>Business Consulting & Services</option>
                                                            <option>Chemicals & Petrochemicals</option>
                                                            <option>Civil Engineering</option>
                                                            <option>Commercial Real Estate</option>
                                                            <option>Computer Hardware & Networking</option>
                                                            <option>Construction & Infrastructure</option>
                                                            <option>Consumer Electronics</option>
                                                            <option>Consumer Goods & FMCG</option>
                                                            <option>Cosmetics & Beauty Products</option>
                                                            <option>Cybersecurity</option>
                                                            <option>Data Analytics & Business Intelligence</option>
                                                            <option>Defense & Space Technology</option>
                                                            <option>E-commerce & Online Retail</option>
                                                            <option>Education & E-Learning</option>
                                                            <option>Electrical & Electronics Manufacturing</option>
                                                            <option>Energy & Utilities</option>
                                                            <option>Engineering Services</option>
                                                            <option>Entertainment & Recreation</option>
                                                            <option>Environmental Services</option>
                                                            <option>Event Management</option>
                                                            <option>Fashion & Apparel</option>
                                                            <option>Film Production & Distribution</option>
                                                            <option>Financial Technology (FinTech)</option>
                                                            <option>Food & Beverage Manufacturing</option>
                                                            <option>Food Processing & Packaging</option>
                                                            <option>Furniture & Home Furnishing</option>
                                                            <option>Gaming & Game Development</option>
                                                            <option>Government & Public Administration</option>
                                                            <option>Graphic Design & Creative Services</option>
                                                            <option>Healthcare & Medical Services</option>
                                                            <option>Hospitality & Hotel Management</option>
                                                            <option>Human Resources & Recruitment</option>
                                                            <option>Import & Export</option>
                                                            <option>Industrial Automation</option>
                                                            <option>Information Technology & Software</option>
                                                            <option>Insurance Services</option>
                                                            <option>Interior Design</option>
                                                            <option>Internet & Web Services</option>
                                                            <option>Investment Banking & Asset Management</option>
                                                            <option>Jewelry & Precious Metals</option>
                                                            <option>Journalism & News Media</option>
                                                            <option>Law & Legal Services</option>
                                                            <option>Logistics & Supply Chain</option>
                                                            <option>Luxury Goods & Services</option>
                                                            <option>Manufacturing & Production</option>
                                                            <option>Maritime & Shipping</option>
                                                            <option>Market Research</option>
                                                            <option>Mechanical Engineering</option>
                                                            <option>Medical Devices & Equipment</option>
                                                            <option>Mining & Metals</option>
                                                            <option>Mobile Technology & Apps</option>
                                                            <option>Music & Audio Production</option>
                                                            <option>Non-Profit & NGO</option>
                                                            <option>Oil & Gas</option>
                                                            <option>Outsourcing & Offshoring</option>
                                                            <option>Packaging & Containers</option>
                                                            <option>Paper & Forest Products</option>
                                                            <option>Pharmaceuticals & Healthcare</option>
                                                            <option>Photography & Video Production</option>
                                                            <option>Plastics & Polymers</option>
                                                            <option>Printing & Publishing</option>
                                                            <option>Private Equity & Venture Capital</option>
                                                            <option>Public Relations & Communications</option>
                                                            <option>Publishing & Content Creation</option>
                                                            <option>Quality Assurance & Testing</option>
                                                            <option>Railway & Transportation</option>
                                                            <option>Real Estate Development</option>
                                                            <option>Renewable Energy & Green Technology</option>
                                                            <option>Research & Development</option>
                                                            <option>Restaurant & Food Services</option>
                                                            <option>Retail & Consumer Products</option>
                                                            <option>Robotics & Automation</option>
                                                            <option>Sales & Business Development</option>
                                                            <option>Security & Investigation Services</option>
                                                            <option>Semiconductors & Electronics</option>
                                                            <option>Social Media & Digital Marketing</option>
                                                            <option>Software as a Service (SaaS)</option>
                                                            <option>Sports & Fitness</option>
                                                            <option>Staffing & HR Consulting</option>
                                                            <option>Telecommunications</option>
                                                            <option>Textiles & Garments</option>
                                                            <option>Tourism & Travel</option>
                                                            <option>Trading & Commodity Markets</option>
                                                            <option>Training & Professional Development</option>
                                                            <option>Translation & Localization</option>
                                                            <option>Transportation & Warehousing</option>
                                                            <option>Venture Capital & Startup Funding</option>
                                                            <option>Veterinary Services</option>
                                                            <option>Waste Management & Recycling</option>
                                                            <option>Water Treatment & Sanitation</option>
                                                            <option>Web Design & Development</option>
                                                            <option>Wellness & Alternative Medicine</option>
                                                            <option>Wholesale & Distribution</option>
                                                            <option>Other</option>
                                                        </select>

                                                    </div>
                                                </div>

                                                <div class="col-xl-6 col-lg-6 col-md-6">
                                                    <div class="form-group">
                                                        <label class="text-dark ft-medium">Specialisms <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-control rounded" name="specialism" required>
                                                            <option value="">Select Specialism</option>
                                                            <option>Accounting & Finance</option>
                                                            <option>Administrative & Office Support</option>
                                                            <option>Advertising & Brand Management</option>
                                                            <option>Aerospace Engineering</option>
                                                            <option>Agriculture & Agronomy</option>
                                                            <option>AI & Machine Learning</option>
                                                            <option>Analytics & Data Science</option>
                                                            <option>Animation & Motion Graphics</option>
                                                            <option>Architecture & Urban Planning</option>
                                                            <option>Art Direction & Creative Design</option>
                                                            <option>Auditing & Compliance</option>
                                                            <option>Automotive Engineering</option>
                                                            <option>Backend Development</option>
                                                            <option>Banking & Investment</option>
                                                            <option>Biochemistry & Molecular Biology</option>
                                                            <option>Biomedical Engineering</option>
                                                            <option>Biotechnology & Genetics</option>
                                                            <option>Blockchain & Cryptocurrency</option>
                                                            <option>Brand Management</option>
                                                            <option>Broadcasting & Production</option>
                                                            <option>Building & Construction Management</option>
                                                            <option>Business Analysis</option>
                                                            <option>Business Development & Sales</option>
                                                            <option>Chemical Engineering</option>
                                                            <option>Civil Engineering</option>
                                                            <option>Clinical Research</option>
                                                            <option>Cloud Computing & DevOps</option>
                                                            <option>Commercial Law</option>
                                                            <option>Community Management</option>
                                                            <option>Compensation & Benefits</option>
                                                            <option>Computer Science & Programming</option>
                                                            <option>Construction Management</option>
                                                            <option>Content Strategy & Marketing</option>
                                                            <option>Content Writing & Copywriting</option>
                                                            <option>Contract Management</option>
                                                            <option>Corporate Communications</option>
                                                            <option>Corporate Finance</option>
                                                            <option>Corporate Law & Governance</option>
                                                            <option>Cost Accounting</option>
                                                            <option>Creative Direction</option>
                                                            <option>Criminal Law & Justice</option>
                                                            <option>Customer Relationship Management (CRM)</option>
                                                            <option>Customer Service & Support</option>
                                                            <option>Customer Success Management</option>
                                                            <option>Cybersecurity & Information Security</option>
                                                            <option>Data Engineering</option>
                                                            <option>Data Science & Analytics</option>
                                                            <option>Database Administration</option>
                                                            <option>Dental Care & Dentistry</option>
                                                            <option>Digital Marketing</option>
                                                            <option>E-commerce Management</option>
                                                            <option>Economics & Economic Analysis</option>
                                                            <option>Electrical Engineering</option>
                                                            <option>Embedded Systems</option>
                                                            <option>Emergency Medicine</option>
                                                            <option>Employee Relations</option>
                                                            <option>Energy Management</option>
                                                            <option>Enterprise Architecture</option>
                                                            <option>Environmental Engineering</option>
                                                            <option>Environmental Science</option>
                                                            <option>Event Planning & Management</option>
                                                            <option>Facilities Management</option>
                                                            <option>Fashion Design & Styling</option>
                                                            <option>Film & Video Production</option>
                                                            <option>Financial Analysis & Planning</option>
                                                            <option>Financial Consulting</option>
                                                            <option>Food Science & Technology</option>
                                                            <option>Foreign Exchange & Trading</option>
                                                            <option>Forensic Accounting</option>
                                                            <option>Frontend Development</option>
                                                            <option>Full Stack Development</option>
                                                            <option>Fundraising & Donor Relations</option>
                                                            <option>Game Design & Development</option>
                                                            <option>General Medicine</option>
                                                            <option>Graphic Design</option>
                                                            <option>Growth Hacking & Marketing</option>
                                                            <option>Hardware Engineering</option>
                                                            <option>Health & Safety Management</option>
                                                            <option>Healthcare Administration</option>
                                                            <option>Hospitality Management</option>
                                                            <option>Human Resources Management</option>
                                                            <option>HVAC Engineering</option>
                                                            <option>Illustration & Visual Arts</option>
                                                            <option>Industrial Design</option>
                                                            <option>Industrial Engineering</option>
                                                            <option>Information Systems</option>
                                                            <option>Infrastructure Management</option>
                                                            <option>Insurance & Risk Management</option>
                                                            <option>Intellectual Property Law</option>
                                                            <option>Interior Design</option>
                                                            <option>Internal Audit</option>
                                                            <option>International Relations</option>
                                                            <option>Investment Banking</option>
                                                            <option>iOS Development</option>
                                                            <option>IT Support & Help Desk</option>
                                                            <option>Journalism & Reporting</option>
                                                            <option>Laboratory Technology</option>
                                                            <option>Learning & Development</option>
                                                            <option>Legal Counsel & Advisory</option>
                                                            <option>Logistics & Distribution</option>
                                                            <option>Maintenance & Repair</option>
                                                            <option>Management Consulting</option>
                                                            <option>Manufacturing Engineering</option>
                                                            <option>Market Research & Insights</option>
                                                            <option>Marketing Strategy</option>
                                                            <option>Materials Science</option>
                                                            <option>Mechanical Engineering</option>
                                                            <option>Media Buying & Planning</option>
                                                            <option>Medical Coding & Billing</option>
                                                            <option>Merchandising & Retail</option>
                                                            <option>Mergers & Acquisitions</option>
                                                            <option>Mobile App Development</option>
                                                            <option>Network Administration</option>
                                                            <option>Network Engineering</option>
                                                            <option>Nursing & Patient Care</option>
                                                            <option>Nutrition & Dietetics</option>
                                                            <option>Occupational Therapy</option>
                                                            <option>Operations Management</option>
                                                            <option>Optometry & Eye Care</option>
                                                            <option>Organizational Development</option>
                                                            <option>Patent Law</option>
                                                            <option>Payroll Management</option>
                                                            <option>Performance Marketing</option>
                                                            <option>Petroleum Engineering</option>
                                                            <option>Pharmaceutical Sciences</option>
                                                            <option>Pharmacy & Pharmacology</option>
                                                            <option>Photography & Videography</option>
                                                            <option>Physical Therapy & Rehabilitation</option>
                                                            <option>Physician & Medical Practice</option>
                                                            <option>Plant Management</option>
                                                            <option>Portfolio Management</option>
                                                            <option>Procurement & Purchasing</option>
                                                            <option>Product Design</option>
                                                            <option>Product Management</option>
                                                            <option>Production Management</option>
                                                            <option>Project Management</option>
                                                            <option>Property Management</option>
                                                            <option>Psychiatry & Mental Health</option>
                                                            <option>Public Affairs</option>
                                                            <option>Public Health</option>
                                                            <option>Public Relations</option>
                                                            <option>Quality Assurance & Testing</option>
                                                            <option>Quality Control & Management</option>
                                                            <option>Quantity Surveying</option>
                                                            <option>Radiology & Imaging</option>
                                                            <option>Real Estate & Property</option>
                                                            <option>Recruitment & Talent Acquisition</option>
                                                            <option>Regulatory Affairs & Compliance</option>
                                                            <option>Research & Development</option>
                                                            <option>Restaurant Management</option>
                                                            <option>Retail Management</option>
                                                            <option>Revenue Management</option>
                                                            <option>Risk Management</option>
                                                            <option>Robotics Engineering</option>
                                                            <option>Sales Engineering</option>
                                                            <option>Sales Management</option>
                                                            <option>Sales Operations</option>
                                                            <option>SAP Consulting</option>
                                                            <option>Search Engine Optimization (SEO)</option>
                                                            <option>Security Operations</option>
                                                            <option>Social Media Management</option>
                                                            <option>Social Work & Counseling</option>
                                                            <option>Software Architecture</option>
                                                            <option>Software Development</option>
                                                            <option>Software Engineering</option>
                                                            <option>Software Testing & QA</option>
                                                            <option>Sound Engineering & Audio Production</option>
                                                            <option>Speech Therapy</option>
                                                            <option>Sports Management</option>
                                                            <option>Strategic Planning</option>
                                                            <option>Structural Engineering</option>
                                                            <option>Supply Chain Management</option>
                                                            <option>Surgery & Surgical Specialties</option>
                                                            <option>System Administration</option>
                                                            <option>Systems Engineering</option>
                                                            <option>Talent Management</option>
                                                            <option>Tax Consulting & Planning</option>
                                                            <option>Teaching & Education</option>
                                                            <option>Technical Writing</option>
                                                            <option>Telecommunications Engineering</option>
                                                            <option>Training & Facilitation</option>
                                                            <option>Transportation & Logistics</option>
                                                            <option>Treasury Management</option>
                                                            <option>UI/UX Design</option>
                                                            <option>Underwriting</option>
                                                            <option>Urban Planning & Development</option>
                                                            <option>Veterinary Medicine</option>
                                                            <option>Warehouse Management</option>
                                                            <option>Waste Management</option>
                                                            <option>Water Resources Engineering</option>
                                                            <option>Web Design</option>
                                                            <option>Web Development</option>
                                                            <option>Wellness & Fitness Coaching</option>
                                                            <option>Other</option>
                                                        </select>
                                                    </div>
                                                </div>



                                                <div class="col-xl-6 col-lg-6 col-md-6">
                                                    <div class="form-group">
                                                        <label class="text-dark ft-medium">Country <span
                                                                class="text-danger">*</span></label>
                                                        <select id="country" name="country" class="form-control"
                                                            required>
                                                            <option value="" hidden>Select Country</option>
                                                            <option value="Afganistan">Afghanistan</option>
                                                            <option value="Albania">Albania</option>
                                                            <option value="Algeria">Algeria</option>
                                                            <option value="American Samoa">American Samoa</option>
                                                            <option value="Andorra">Andorra</option>
                                                            <option value="Angola">Angola</option>
                                                            <option value="Anguilla">Anguilla</option>
                                                            <option value="Antigua & Barbuda">Antigua & Barbuda</option>
                                                            <option value="Argentina">Argentina</option>
                                                            <option value="Armenia">Armenia</option>
                                                            <option value="Aruba">Aruba</option>
                                                            <option value="Australia">Australia</option>
                                                            <option value="Austria">Austria</option>
                                                            <option value="Azerbaijan">Azerbaijan</option>
                                                            <option value="Bahamas">Bahamas</option>
                                                            <option value="Bahrain">Bahrain</option>
                                                            <option value="Bangladesh">Bangladesh</option>
                                                            <option value="Barbados">Barbados</option>
                                                            <option value="Belarus">Belarus</option>
                                                            <option value="Belgium">Belgium</option>
                                                            <option value="Belize">Belize</option>
                                                            <option value="Benin">Benin</option>
                                                            <option value="Bermuda">Bermuda</option>
                                                            <option value="Bhutan">Bhutan</option>
                                                            <option value="Bolivia">Bolivia</option>
                                                            <option value="Bonaire">Bonaire</option>
                                                            <option value="Bosnia & Herzegovina">Bosnia & Herzegovina
                                                            </option>
                                                            <option value="Botswana">Botswana</option>
                                                            <option value="Brazil">Brazil</option>
                                                            <option value="British Indian Ocean Ter">British Indian
                                                                Ocean Ter</option>
                                                            <option value="Brunei">Brunei</option>
                                                            <option value="Bulgaria">Bulgaria</option>
                                                            <option value="Burkina Faso">Burkina Faso</option>
                                                            <option value="Burundi">Burundi</option>
                                                            <option value="Cambodia">Cambodia</option>
                                                            <option value="Cameroon">Cameroon</option>
                                                            <option value="Canada">Canada</option>
                                                            <option value="Canary Islands">Canary Islands</option>
                                                            <option value="Cape Verde">Cape Verde</option>
                                                            <option value="Cayman Islands">Cayman Islands</option>
                                                            <option value="Central African Republic">Central African
                                                                Republic</option>
                                                            <option value="Chad">Chad</option>
                                                            <option value="Channel Islands">Channel Islands</option>
                                                            <option value="Chile">Chile</option>
                                                            <option value="China">China</option>
                                                            <option value="Christmas Island">Christmas Island</option>
                                                            <option value="Cocos Island">Cocos Island</option>
                                                            <option value="Colombia">Colombia</option>
                                                            <option value="Comoros">Comoros</option>
                                                            <option value="Congo">Congo</option>
                                                            <option value="Cook Islands">Cook Islands</option>
                                                            <option value="Costa Rica">Costa Rica</option>
                                                            <option value="Cote DIvoire">Cote DIvoire</option>
                                                            <option value="Croatia">Croatia</option>
                                                            <option value="Cuba">Cuba</option>
                                                            <option value="Curaco">Curacao</option>
                                                            <option value="Cyprus">Cyprus</option>
                                                            <option value="Czech Republic">Czech Republic</option>
                                                            <option value="Denmark">Denmark</option>
                                                            <option value="Djibouti">Djibouti</option>
                                                            <option value="Dominica">Dominica</option>
                                                            <option value="Dominican Republic">Dominican Republic
                                                            </option>
                                                            <option value="East Timor">East Timor</option>
                                                            <option value="Ecuador">Ecuador</option>
                                                            <option value="Egypt">Egypt</option>
                                                            <option value="El Salvador">El Salvador</option>
                                                            <option value="Equatorial Guinea">Equatorial Guinea</option>
                                                            <option value="Eritrea">Eritrea</option>
                                                            <option value="Estonia">Estonia</option>
                                                            <option value="Ethiopia">Ethiopia</option>
                                                            <option value="Falkland Islands">Falkland Islands</option>
                                                            <option value="Faroe Islands">Faroe Islands</option>
                                                            <option value="Fiji">Fiji</option>
                                                            <option value="Finland">Finland</option>
                                                            <option value="France">France</option>
                                                            <option value="French Guiana">French Guiana</option>
                                                            <option value="French Polynesia">French Polynesia</option>
                                                            <option value="French Southern Ter">French Southern Ter
                                                            </option>
                                                            <option value="Gabon">Gabon</option>
                                                            <option value="Gambia">Gambia</option>
                                                            <option value="Georgia">Georgia</option>
                                                            <option value="Germany">Germany</option>
                                                            <option value="Ghana">Ghana</option>
                                                            <option value="Gibraltar">Gibraltar</option>
                                                            <option value="Great Britain">Great Britain</option>
                                                            <option value="Greece">Greece</option>
                                                            <option value="Greenland">Greenland</option>
                                                            <option value="Grenada">Grenada</option>
                                                            <option value="Guadeloupe">Guadeloupe</option>
                                                            <option value="Guam">Guam</option>
                                                            <option value="Guatemala">Guatemala</option>
                                                            <option value="Guinea">Guinea</option>
                                                            <option value="Guyana">Guyana</option>
                                                            <option value="Haiti">Haiti</option>
                                                            <option value="Hawaii">Hawaii</option>
                                                            <option value="Honduras">Honduras</option>
                                                            <option value="Hong Kong">Hong Kong</option>
                                                            <option value="Hungary">Hungary</option>
                                                            <option value="Iceland">Iceland</option>
                                                            <option value="Indonesia">Indonesia</option>
                                                            <option value="India">India</option>
                                                            <option value="Iran">Iran</option>
                                                            <option value="Iraq">Iraq</option>
                                                            <option value="Ireland">Ireland</option>
                                                            <option value="Isle of Man">Isle of Man</option>
                                                            <option value="Israel">Israel</option>
                                                            <option value="Italy">Italy</option>
                                                            <option value="Jamaica">Jamaica</option>
                                                            <option value="Japan">Japan</option>
                                                            <option value="Jordan">Jordan</option>
                                                            <option value="Kazakhstan">Kazakhstan</option>
                                                            <option value="Kenya">Kenya</option>
                                                            <option value="Kiribati">Kiribati</option>
                                                            <option value="Korea North">Korea North</option>
                                                            <option value="Korea Sout">Korea South</option>
                                                            <option value="Kuwait">Kuwait</option>
                                                            <option value="Kyrgyzstan">Kyrgyzstan</option>
                                                            <option value="Laos">Laos</option>
                                                            <option value="Latvia">Latvia</option>
                                                            <option value="Lebanon">Lebanon</option>
                                                            <option value="Lesotho">Lesotho</option>
                                                            <option value="Liberia">Liberia</option>
                                                            <option value="Libya">Libya</option>
                                                            <option value="Liechtenstein">Liechtenstein</option>
                                                            <option value="Lithuania">Lithuania</option>
                                                            <option value="Luxembourg">Luxembourg</option>
                                                            <option value="Macau">Macau</option>
                                                            <option value="Macedonia">Macedonia</option>
                                                            <option value="Madagascar">Madagascar</option>
                                                            <option value="Malaysia">Malaysia</option>
                                                            <option value="Malawi">Malawi</option>
                                                            <option value="Maldives">Maldives</option>
                                                            <option value="Mali">Mali</option>
                                                            <option value="Malta">Malta</option>
                                                            <option value="Marshall Islands">Marshall Islands</option>
                                                            <option value="Martinique">Martinique</option>
                                                            <option value="Mauritania">Mauritania</option>
                                                            <option value="Mauritius">Mauritius</option>
                                                            <option value="Mayotte">Mayotte</option>
                                                            <option value="Mexico">Mexico</option>
                                                            <option value="Midway Islands">Midway Islands</option>
                                                            <option value="Moldova">Moldova</option>
                                                            <option value="Monaco">Monaco</option>
                                                            <option value="Mongolia">Mongolia</option>
                                                            <option value="Montserrat">Montserrat</option>
                                                            <option value="Morocco">Morocco</option>
                                                            <option value="Mozambique">Mozambique</option>
                                                            <option value="Myanmar">Myanmar</option>
                                                            <option value="Nambia">Nambia</option>
                                                            <option value="Nauru">Nauru</option>
                                                            <option value="Nepal">Nepal</option>
                                                            <option value="Netherland Antilles">Netherland Antilles
                                                            </option>
                                                            <option value="Netherlands">Netherlands (Holland, Europe)
                                                            </option>
                                                            <option value="Nevis">Nevis</option>
                                                            <option value="New Caledonia">New Caledonia</option>
                                                            <option value="New Zealand">New Zealand</option>
                                                            <option value="Nicaragua">Nicaragua</option>
                                                            <option value="Niger">Niger</option>
                                                            <option value="Nigeria">Nigeria</option>
                                                            <option value="Niue">Niue</option>
                                                            <option value="Norfolk Island">Norfolk Island</option>
                                                            <option value="Norway">Norway</option>
                                                            <option value="Oman">Oman</option>
                                                            <option value="Pakistan">Pakistan</option>
                                                            <option value="Palau Island">Palau Island</option>
                                                            <option value="Palestine">Palestine</option>
                                                            <option value="Panama">Panama</option>
                                                            <option value="Papua New Guinea">Papua New Guinea</option>
                                                            <option value="Paraguay">Paraguay</option>
                                                            <option value="Peru">Peru</option>
                                                            <option value="Phillipines">Philippines</option>
                                                            <option value="Pitcairn Island">Pitcairn Island</option>
                                                            <option value="Poland">Poland</option>
                                                            <option value="Portugal">Portugal</option>
                                                            <option value="Puerto Rico">Puerto Rico</option>
                                                            <option value="Qatar">Qatar</option>
                                                            <option value="Republic of Montenegro">Republic of
                                                                Montenegro</option>
                                                            <option value="Republic of Serbia">Republic of Serbia
                                                            </option>
                                                            <option value="Reunion">Reunion</option>
                                                            <option value="Romania">Romania</option>
                                                            <option value="Russia">Russia</option>
                                                            <option value="Rwanda">Rwanda</option>
                                                            <option value="St Barthelemy">St Barthelemy</option>
                                                            <option value="St Eustatius">St Eustatius</option>
                                                            <option value="St Helena">St Helena</option>
                                                            <option value="St Kitts-Nevis">St Kitts-Nevis</option>
                                                            <option value="St Lucia">St Lucia</option>
                                                            <option value="St Maarten">St Maarten</option>
                                                            <option value="St Pierre & Miquelon">St Pierre & Miquelon
                                                            </option>
                                                            <option value="St Vincent & Grenadines">St Vincent &
                                                                Grenadines</option>
                                                            <option value="Saipan">Saipan</option>
                                                            <option value="Samoa">Samoa</option>
                                                            <option value="Samoa American">Samoa American</option>
                                                            <option value="San Marino">San Marino</option>
                                                            <option value="Sao Tome & Principe">Sao Tome & Principe
                                                            </option>
                                                            <option value="Saudi Arabia">Saudi Arabia</option>
                                                            <option value="Senegal">Senegal</option>
                                                            <option value="Seychelles">Seychelles</option>
                                                            <option value="Sierra Leone">Sierra Leone</option>
                                                            <option value="Singapore">Singapore</option>
                                                            <option value="Slovakia">Slovakia</option>
                                                            <option value="Slovenia">Slovenia</option>
                                                            <option value="Solomon Islands">Solomon Islands</option>
                                                            <option value="Somalia">Somalia</option>
                                                            <option value="South Africa">South Africa</option>
                                                            <option value="Spain">Spain</option>
                                                            <option value="Sri Lanka">Sri Lanka</option>
                                                            <option value="Sudan">Sudan</option>
                                                            <option value="Suriname">Suriname</option>
                                                            <option value="Swaziland">Swaziland</option>
                                                            <option value="Sweden">Sweden</option>
                                                            <option value="Switzerland">Switzerland</option>
                                                            <option value="Syria">Syria</option>
                                                            <option value="Tahiti">Tahiti</option>
                                                            <option value="Taiwan">Taiwan</option>
                                                            <option value="Tajikistan">Tajikistan</option>
                                                            <option value="Tanzania">Tanzania</option>
                                                            <option value="Thailand">Thailand</option>
                                                            <option value="Togo">Togo</option>
                                                            <option value="Tokelau">Tokelau</option>
                                                            <option value="Tonga">Tonga</option>
                                                            <option value="Trinidad & Tobago">Trinidad & Tobago</option>
                                                            <option value="Tunisia">Tunisia</option>
                                                            <option value="Turkey">Turkey</option>
                                                            <option value="Turkmenistan">Turkmenistan</option>
                                                            <option value="Turks & Caicos Is">Turks & Caicos Is</option>
                                                            <option value="Tuvalu">Tuvalu</option>
                                                            <option value="Uganda">Uganda</option>
                                                            <option value="United Kingdom">United Kingdom</option>
                                                            <option value="Ukraine">Ukraine</option>
                                                            <option value="United Arab Erimates">United Arab Emirates
                                                            </option>
                                                            <option value="United States of America">United States of
                                                                America</option>
                                                            <option value="Uraguay">Uruguay</option>
                                                            <option value="Uzbekistan">Uzbekistan</option>
                                                            <option value="Vanuatu">Vanuatu</option>
                                                            <option value="Vatican City State">Vatican City State
                                                            </option>
                                                            <option value="Venezuela">Venezuela</option>
                                                            <option value="Vietnam">Vietnam</option>
                                                            <option value="Virgin Islands (Brit)">Virgin Islands (Brit)
                                                            </option>
                                                            <option value="Virgin Islands (USA)">Virgin Islands (USA)
                                                            </option>
                                                            <option value="Wake Island">Wake Island</option>
                                                            <option value="Wallis & Futana Is">Wallis & Futana Is
                                                            </option>
                                                            <option value="Yemen">Yemen</option>
                                                            <option value="Zaire">Zaire</option>
                                                            <option value="Zambia">Zambia</option>
                                                            <option value="Zimbabwe">Zimbabwe</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-xl-6 col-lg-6 col-md-6">
                                                    <div class="form-group">
                                                        <label class="text-dark ft-medium">City <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" value=""
                                                            placeholder="City" name="city" required />
                                                    </div>
                                                </div>

                                                <div class="col-xl-12 col-lg-12 col-md-12">
                                                    <div class="form-group">
                                                        <label class="text-dark ft-medium">Street Address <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" value=""
                                                            placeholder="#10 Marke Juger, SBI Road" name="address"
                                                            required />
                                                    </div>
                                                </div>

                                                <div class="col-xl-6 col-lg-6 col-md-6">
                                                    <div class="form-group">
                                                        <label class="text-dark ft-medium">Website URL</label>
                                                        <input type="url" class="form-control" value=""
                                                            placeholder="https://example.com" name="websiteURL" />
                                                    </div>
                                                </div>

                                                <div class="col-xl-12 col-lg-12 col-md-12">
                                                    <div class="form-group">
                                                        <label class="text-dark ft-medium">About the Company <span
                                                                class="text-danger">*</span></label>
                                                        <textarea class="form-control rounded"
                                                            placeholder="Company Description" name="about"
                                                            required></textarea>
                                                    </div>
                                                </div>

                                                <div class="col-xl-6 col-lg-6 col-md-12">
                                                    <label class="text-dark ft-medium">Company Logo</label>
                                                    <div class="custom-file avater_uploads mb-2">
                                                        <label class="custom-file-label" for="customFile"><i
                                                                class="fa fa-logo"></i>COMPANY LOGO</label>
                                                    </div>

                                                    <div class="form-group custom-file">
                                                        <input type="file" name="image" class="custom-file-input"
                                                            id="customFile" accept=".png, .jpg,.jpeg,.gif,.bmp,.svg"" 
                                                            value="" />
                                                        <label class=" custom-file-label" for="customFile">Choose
                                                        file</label>
                                                    </div>
                                                </div>


                                                <div class="mt-5 col-xl-12 col-lg-12 col-md-12">
                                                    <div class="_dashboard__header_flex mb-4">
                                                        <h4 class="mb-0 ft-medium fs-md"><i
                                                                class="fa fa-file mr-1 theme-cl fs-sm"></i>Award/Recognition
                                                            <span class="small font-italic">optional</span>
                                                        </h4>
                                                    </div>
                                                    <div class="col-xl-4 col-lg-6 col-md-6">
                                                        <div class="form-group">
                                                            <label class="text-dark ft-medium">Awards</label>
                                                            <input type="text" class="form-control" value=""
                                                                placeholder="Award" name="award" />
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-4 col-lg-6 col-md-6">
                                                        <div class="form-group">
                                                            <label class="text-dark ft-medium">Year</label>
                                                            <input type="number" class="form-control" value=""
                                                                placeholder="Year" name="award_year" />
                                                            <code>e.g 2020</code>
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-12 col-lg-12 col-md-12">
                                                        <div class="form-group">
                                                            <label class="text-dark ft-medium">Award Description</label>
                                                            <textarea class="form-control rounded" value="" rows="2"
                                                                placeholder="Awards Description"
                                                                name="award_disc"></textarea>
                                                        </div>
                                                    </div>
                                                </div>




                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <button type="submit" name="add_company"
                                                            class="btn btn-md ft-medium text-light rounded theme-bg">Add
                                                            Company</button>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <?php
										// $img = imagecreate(500, 300);
										// $bg = imagecolorallocate($img, 205, 257, 25);
										// $fg = imagecolorallocate($img, 0, 0, 0); //black
										// imagefilledrectangle($img, 0, 0, 500, 300, $bg);
										// imagestring($img, 100, 200, 50, "TEXT", $fg);
										// imagepng($img, "IMAGE.png");
										?>
                                        <!-- <img src="<?php echo 'IMAGE.png' ?>"> -->

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- footer -->
                <?php include 'footer.php' ?>
            </div>

        </div>
        <!-- ======================= dashboard Detail End ======================== -->

        <a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>


    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->

    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/slick.js"></script>
    <script src="assets/js/slider-bg.js"></script>
    <script src="assets/js/smoothproducts.js"></script>
    <script src="assets/js/snackbar.min.js"></script>
    <script src="assets/js/jQuery.style.switcher.js"></script>
    <script src="assets/js/custom.js"></script>
    <!-- ============================================================== -->
    <!-- This page plugins -->
    <!-- ============================================================== -->

</body>

<!-- Mirrored from themezhub.net/live-workplex/workplex/dashboard-post-job.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Feb 2022 12:07:20 GMT -->

</html>