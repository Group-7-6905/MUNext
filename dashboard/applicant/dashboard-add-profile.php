<!DOCTYPE html>
<html lang="zxx">

<?php 
require 'include/phpcode.php'; 



    // Redirect if profile is already complete
    $querycomp = "SELECT * from tblapplicants WHERE USERID = '$session_id'";
    $resultcomp = mysqli_query($con, $querycomp);
    $rowcomp = mysqli_fetch_array($resultcomp);

    $USERID = $rowcomp['USERID']  ?? '';
    if (!empty($USERID && $profileCompletion > 90)) {
        // header("Location: dashboard-my-profile.php");
        header("Refresh:1; url= dashboard-my-profile.php");
    }
        

?>

<head>
    <meta charset="utf-8" />
    <meta name="author" content="Themezhub" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>MUNext - Complete Profile</title>

    <!-- Custom CSS -->
    <link href="assets/css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/custom-style.css">
</head>

<body>

    <div id="main-wrapper">

        <!-- Top header -->
        <?php include 'header.php' ?>

        <div class="clearfix"></div>

        <!-- Dashboard Section -->
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
                            <h1 class="ft-medium">Complete Profile</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item text-muted"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item text-muted"><a href="#">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="#" class="theme-cl">Add Profile</a></li>
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
                                                class="fa fa-file mr-1 theme-cl fs-sm"></i>1. Complete Your Profile</h4>
                                    </div>
                                </div>

                                <div class="_dashboard_content_body py-3 px-3">
                                    <form class="row" method="post" enctype="multipart/form-data">
                                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                            <?php if (!empty($APPLICANTPHOTO)): ?>
                                            <div class="jb-list01-thumb">
                                                <img src="<?php echo $path.$APPLICANTPHOTO ?>"
                                                    class="custom-file avater_uploads img-fluid" width="90" alt=""
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
                                            </div>
                                            <?php else: ?>
                                            <div class="custom-file avater_uploads">
                                                <label class="custom-file-label" for="customFile"><i
                                                        class="fa fa-user"></i></label>
                                            </div>
                                            <?php endif ?></php>
                                            <br>

                                            <div class="col-xl-12 col-lg-12 col-md-12">
                                                <div class="form-group custom-file">
                                                    <input type="file" name="image" class="custom-file-input"
                                                        id="customFile" accept=".png, .jpg,.jpeg,.gif,.bmp,.svg">
                                                    <label class="custom-file-label" for="customFile">Choose
                                                        file</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">
                                            <div class="row">
                                                <div class="col-xl-6 col-lg-6">
                                                    <div class="form-group">
                                                        <label class="text-dark ft-medium">Professional Title <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control rounded"
                                                            placeholder="e.g. Web Designer" name="job_title" required
                                                            value="<?php echo htmlspecialchars($JOBTITLE); ?>">
                                                    </div>
                                                </div>

                                                <div class="col-xl-6 col-lg-6">
                                                    <div class="form-group">
                                                        <label class="text-dark ft-medium">Job Category <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-control rounded" name="job_categoryid"
                                                            required>
                                                            <option value="" selected hidden>Select Job Category
                                                            </option>
                                                            <?php if (!empty($JOBCATEGORYID)) { ?>
                                                            <option value="<?php echo $JOBCATEGORYID ?>" selected>
                                                                <?php echo $SUBCATEGORY ?></option>
                                                            <?php } ?>

                                                            <?php
                                                            $query = "SELECT * FROM tbljobsubcategory ORDER BY SUBCATEGORY ASC";
                                                            $run = mysqli_query($con, $query);
                                                            while ($row = mysqli_fetch_array($run)) {
                                                                if ($row['ID'] != $JOBCATEGORYID) {
                                                            ?>
                                                            <option value="<?php echo $row['ID'] ?>">
                                                                <?php echo $row['SUBCATEGORY'] ?></option>
                                                            <?php 
                                                                }
                                                            } 
                                                            ?>
                                                            <option value="0">Others</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-xl-6 col-lg-6">
                                                    <div class="form-group">
                                                        <label class="text-dark ft-medium">Phone Number <span
                                                                class="text-danger">*</span></label>
                                                        <input type="tel" class="form-control rounded"
                                                            placeholder="Phone Number" name="phoneno" required
                                                            value="<?php echo htmlspecialchars($CONTACTNO); ?>">
                                                    </div>
                                                </div>

                                                <div class="col-xl-6 col-lg-6">
                                                    <div class="form-group">
                                                        <label class="text-dark ft-medium">Gender <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-control rounded" name="sex" required>
                                                            <option value="" hidden>Choose gender</option>
                                                            <option <?php echo ($SEX == 'Male') ? 'selected' : ''; ?>>
                                                                Male</option>
                                                            <option <?php echo ($SEX == 'Female') ? 'selected' : ''; ?>>
                                                                Female</option>
                                                            <option
                                                                <?php echo ($SEX == 'Prefer not to answer') ? 'selected' : ''; ?>>
                                                                Prefer not to answer</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label class="text-dark ft-medium">Date Of Birth <span
                                                                class="text-danger">*</span></label>
                                                        <input type="date" class="form-control rounded" name="dob"
                                                            required value="<?php echo $BIRTHDATE; ?>">
                                                    </div>
                                                </div>

                                                <div class="col-xl-12 col-lg-12">
                                                    <div class="form-group">
                                                        <label class="text-dark ft-medium">About Me <span
                                                                class="text-danger">*</span></label>
                                                        <textarea name="about_me" class="form-control with-light"
                                                            rows="4"
                                                            placeholder="You can write about your years of experience, industry, or skills. People also talk about their achievements or previous job experience"
                                                            required><?php echo htmlspecialchars($ABOUTME); ?></textarea>
                                                    </div>
                                                </div>

                                                <div class="col-xl-12 col-lg-12">
                                                    <div class="form-group">
                                                        <?php echo $msg ?>
                                                    </div>
                                                </div>

                                                <div class="col-xl-12 col-lg-12">
                                                    <div class="form-group">
                                                        <button type="submit" name="save_data"
                                                            class="btn btn-md ft-medium text-light rounded theme-bg">
                                                            💾 Save Data & Continue
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>


                    <?php
                    // Only show additional forms if basic profile is saved
                    if (!empty($USERID)) {
                    ?>
                    <!-- FORM 2: Contact Information & Social Links -->
                    <form method="post" id="section23">
                        <div class="row">
                            <div class="col-lg-12 col-md-12">
                                <div class="_dashboard_content bg-white rounded mb-4">
                                    <div class="_dashboard_content_header br-bottom py-3 px-3">
                                        <div class="_dashboard__header_flex">
                                            <h4 class="mb-0 ft-medium fs-md"><i
                                                    class="fas fa-map-marker-alt mr-1 theme-cl fs-sm"></i>2. Contact
                                                Information</h4>
                                        </div>
                                    </div>

                                    <div class="_dashboard_content_body py-3 px-3">
                                        <div class="row">
                                            <div class="col-xl-6 col-lg-6 col-md-12">
                                                <div class="form-group">
                                                    <label class="text-dark ft-medium">Country <span
                                                            class="text-danger">*</span></label>
                                                    <select id="country" name="country" class="form-control" required>
                                                        <?php if (!empty($COUNTRY)) { ?> <option
                                                            value="<?php echo $COUNTRY; ?>" hidden>
                                                            <?php echo $COUNTRY; ?></option>
                                                        <?php } else { ?>
                                                        <option value="" hidden>Select Country</option>
                                                        <?php } ?>

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
                                                        <option value="British Indian Ocean Ter">British Indian Ocean
                                                            Ter</option>
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
                                                        <option value="Dominican Republic">Dominican Republic</option>
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
                                                        <option value="French Southern Ter">French Southern Ter</option>
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
                                                        <option value="Netherland Antilles">Netherland Antilles</option>
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
                                                        <option value="Republic of Montenegro">Republic of Montenegro
                                                        </option>
                                                        <option value="Republic of Serbia">Republic of Serbia</option>
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
                                                        <option value="St Vincent & Grenadines">St Vincent & Grenadines
                                                        </option>
                                                        <option value="Saipan">Saipan</option>
                                                        <option value="Samoa">Samoa</option>
                                                        <option value="Samoa American">Samoa American</option>
                                                        <option value="San Marino">San Marino</option>
                                                        <option value="Sao Tome & Principe">Sao Tome & Principe</option>
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
                                                        <option value="Vatican City State">Vatican City State</option>
                                                        <option value="Venezuela">Venezuela</option>
                                                        <option value="Vietnam">Vietnam</option>
                                                        <option value="Virgin Islands (Brit)">Virgin Islands (Brit)
                                                        </option>
                                                        <option value="Virgin Islands (USA)">Virgin Islands (USA)
                                                        </option>
                                                        <option value="Wake Island">Wake Island</option>
                                                        <option value="Wallis & Futana Is">Wallis & Futana Is</option>
                                                        <option value="Yemen">Yemen</option>
                                                        <option value="Zaire">Zaire</option>
                                                        <option value="Zambia">Zambia</option>
                                                        <option value="Zimbabwe">Zimbabwe</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-xl-6 col-lg-6 col-md-12">
                                                <div class="form-group">
                                                    <label class="text-dark ft-medium">City <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control rounded" placeholder="City"
                                                        name="city" required
                                                        value="<?php echo htmlspecialchars($CITY); ?>">
                                                </div>
                                            </div>

                                            <div class="col-xl-12 col-lg-12 col-md-12">
                                                <div class="form-group">
                                                    <label class="text-dark ft-medium">Full Address <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control rounded"
                                                        placeholder="#10 Marke Juger, SBI Road" name="address" required
                                                        value="<?php echo htmlspecialchars($ADDRESS); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12 col-md-12">
                                <div class="_dashboard_content bg-white rounded mb-4">
                                    <div class="_dashboard_content_header br-bottom py-3 px-3">
                                        <div class="_dashboard__header_flex">
                                            <h4 class="mb-0 ft-medium fs-md"><i
                                                    class="ti-facebook mr-1 theme-cl fs-sm"></i>3. Social Accounts</h4>
                                        </div>
                                    </div>

                                    <div class="_dashboard_content_body py-3 px-3">
                                        <div class="row">
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                                <div class="form-group">
                                                    <label class="text-dark ft-medium">Facebook </label>
                                                    <input type="url" class="form-control rounded"
                                                        placeholder="https://www.facebook.com/yourprofile" name="fb"
                                                        value="<?php echo htmlspecialchars($FB_link); ?>">
                                                </div>
                                            </div>

                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                                <div class="form-group">
                                                    <label class="text-dark ft-medium">LinkedIn </label>
                                                    <input type="url" class="form-control rounded"
                                                        placeholder="https://www.linkedin.com/in/yourprofile" name="lin"
                                                        value="<?php echo htmlspecialchars($LinkedIn_link); ?>">
                                                </div>
                                            </div>

                                            <div class="col-xl-12 col-lg-12">
                                                <div class="form-group">
                                                    <?php echo $msg2 ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-12 col-lg-12">
                                <div class="form-group">
                                    <button type="submit" name="save_info"
                                        class="btn btn-md ft-medium text-light rounded theme-bg">
                                        💾 Save Contact Info
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <?php
                    // Only show education/experience forms if contact info is complete
                    if (!empty($COUNTRY) && !empty($CITY) && !empty($ADDRESS)) {
                    ?>

                    <!-- FORM 3: Education, Experience & Skills -->
                    <form method="post" id="section456">
                        <div class="row">
                            <!-- Education Section -->
                            <div class="col-lg-12 col-md-12">
                                <div class="_dashboard_content bg-white rounded mb-4">
                                    <div class="_dashboard_content_header br-bottom py-3 px-3">
                                        <div class="_dashboard__header_flex">
                                            <h4 class="mb-0 ft-medium fs-md"><i
                                                    class="fas fa-graduation-cap mr-1 theme-cl fs-sm"></i>4. Education
                                                Details <code>Most current level of education</code></h4>
                                        </div>
                                    </div>

                                    <div class="_dashboard_content_body py-3 px-3">
                                        <div class="row">
                                            <div class="col-xl-12 col-lg-12">
                                                <div class="gray rounded p-3 mb-3 position-relative">
                                                    <div class="form-group">
                                                        <label class="text-dark ft-medium">School Name <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control rounded"
                                                            placeholder="School Name" name="schl_name" required
                                                            value="<?php echo htmlspecialchars($SCHOOLNAME) ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="text-dark ft-medium">Qualification <span
                                                                class="text-danger">*</span></label>
                                                        <select class="custom-select rounded" name="qualification"
                                                            required>
                                                            <option value="" hidden>Please select</option>
                                                            <option
                                                                <?php echo ($DEGREE == 'B.Sc') ? 'selected' : ''; ?>>
                                                                B.Sc</option>
                                                            <option
                                                                <?php echo ($DEGREE == 'Master Degree') ? 'selected' : ''; ?>>
                                                                Master Degree</option>
                                                            <option
                                                                <?php echo ($DEGREE == 'BPharma') ? 'selected' : ''; ?>>
                                                                BPharma</option>
                                                            <option
                                                                <?php echo ($DEGREE == 'P.H.D.') ? 'selected' : ''; ?>>
                                                                P.H.D.</option>
                                                            <option <?php echo ($DEGREE == 'HND') ? 'selected' : ''; ?>>
                                                                HND</option>
                                                            <option <?php echo ($DEGREE == 'OND') ? 'selected' : ''; ?>>
                                                                OND</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Experience Section -->
                            <div class="col-lg-12 col-md-12">
                                <div class="_dashboard_content bg-white rounded mb-4">
                                    <div class="_dashboard_content_header br-bottom py-3 px-3">
                                        <div class="_dashboard__header_flex">
                                            <h4 class="mb-0 ft-medium fs-md"><i
                                                    class="fas fa-briefcase mr-1 theme-cl fs-sm"></i>5. Experience
                                                Details (Optional) <code>Most recent experience</code></h4>
                                        </div>
                                    </div>

                                    <div class="_dashboard_content_body py-3 px-3">
                                        <div class="row">
                                            <div class="col-xl-12 col-lg-12">
                                                <div class="gray rounded p-3 mb-3 position-relative">
                                                    <div class="form-group">
                                                        <label class="text-dark ft-medium">Employer</label>
                                                        <select class="custom-select rounded" name="company_name_select"
                                                            id="company_select" onchange="toggleCompanyInput()">
                                                            <option value="">Select Company</option>
                                                            <?php if (!empty($EXCOMPANYNAME)) { ?>
                                                            <option value="<?php echo $EXCOMPANYNAME ?>" selected>
                                                                <?php echo $EXCOMPANYNAME ?></option>
                                                            <?php } ?>

                                                            <?php 
                                                            $sql = "SELECT * FROM tblcompany ORDER BY COMPANYNAME ASC";
                                                            $run = mysqli_query($con, $sql);
                                                            while ($row = mysqli_fetch_array($run)) { 
                                                                if ($row['COMPANYNAME'] != $EXCOMPANYNAME) {
                                                            ?>
                                                            <option
                                                                value="<?php echo htmlspecialchars($row['COMPANYNAME']); ?>">
                                                                <?php echo htmlspecialchars($row['COMPANYNAME']); ?>
                                                            </option>
                                                            <?php 
                                                                }
                                                            } 
                                                            ?>
                                                            <option value="Others (Please specify)">Others (Please
                                                                specify)</option>
                                                        </select>
                                                    </div>

                                                    <div class="form-group" id="company_other" style="display: none;">
                                                        <input type="text" class="form-control rounded"
                                                            placeholder="Company Name" name="company_name_specify">
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="text-dark ft-medium">Job Title</label>
                                                        <input type="text" class="form-control rounded"
                                                            placeholder="Designation Title" name="job_title"
                                                            value="<?php echo htmlspecialchars($EXJOBTITLE) ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Skills Section -->
                            <div class="col-lg-12 col-md-12">
                                <div class="_dashboard_content bg-white rounded mb-4">
                                    <div class="_dashboard_content_header br-bottom py-3 px-3">
                                        <div class="_dashboard__header_flex">
                                            <h4 class="mb-0 ft-medium fs-md"><i
                                                    class="fas fa-star mr-1 theme-cl fs-sm"></i>6. Skills Details</h4>
                                        </div>
                                    </div>

                                    <div class="_dashboard_content_body py-3 px-3">
                                        <div class="row">
                                            <div class="col-xl-12 col-lg-12">
                                                <div class="gray rounded p-3 mb-3 position-relative">
                                                    <div class="form-group">
                                                        <label class="text-dark ft-medium">Skills Name *
                                                            <code>Separate with a comma</code></label>
                                                        <input type="text" class="form-control rounded"
                                                            placeholder="e.g MySQL, Javascript, Python, React"
                                                            name="skills" required
                                                            value="<?php echo htmlspecialchars($SKILLS) ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-12 col-lg-12">
                                                <div class="form-group">
                                                    <?php echo $msg3 ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12 col-md-12">
                                <button type="submit" name="save_preview"
                                    class="btn btn-md ft-medium text-light rounded theme-bg">
                                    ✅ Save & Preview Profile
                                </button>
                            </div>
                        </div>
                    </form>

                    <?php 
                    } // End if contact info complete
                    } // End if profile exists
                    ?>

                </div>

                <!-- Footer -->
                <?php include 'footer.php' ?>
            </div>

        </div>

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
    // Toggle company input field
    function toggleCompanyInput() {
        var selectValue = document.getElementById('company_select').value;
        var otherInput = document.getElementById('company_other');

        if (selectValue === "Others (Please specify)") {
            otherInput.style.display = "block";
            otherInput.querySelector('input').required = true;
        } else {
            otherInput.style.display = "none";
            otherInput.querySelector('input').required = false;
        }
    }

    // File input label update
    document.getElementById('customFile').addEventListener('change', function(e) {
        var fileName = e.target.files[0] ? e.target.files[0].name : 'Choose file';
        var label = e.target.nextElementSibling;
        label.textContent = fileName;
    });
    </script>

</body>

</html>