<!DOCTYPE html>
<html lang="zxx">

<?php
if (!empty($_GET['companyid'])) {
   $companyid = $_GET['companyid'];
} else {
   header('location: dashboard-companys.php');
}
?>

<?php require 'include/phpcode.php'; ?>

<!-- Mirrored from themezhub.net/live-workplex/workplex/dashboard-post-job.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Feb 2022 12:07:20 GMT -->

<head>
   <meta charset="utf-8" />
   <meta name="author" content="Themezhub" />
   <meta name="viewport" content="width=device-width, initial-scale=1">

   <title>Workplex - Creative Job Board HTML Template</title>

   <!-- Custom CSS -->
   <link href="assets/css/styles.css" rel="stylesheet">

</head>
<?php

//////////////////////Get Company Details///////////////////////
$COMPANYID = '';
$COMPANYNAME = '';
$COMPANYADDRESS = '';
$COMPANYCONTACTNO = '';
$COMPANYSTATUS = '';
$COMPANYABOUT = '';
$COMPANYEMAIL = '';
$COMPANYINDUSTRY = '';
$COMPANYSPECIALISM = '';
$COMPANYCOUNTRY = '';
$COMPANYCITY = '';
$COMPANYAWARD = '';
$COMPANYYEAR = '';
$COMPANYAWARDDESC  = '';
$COMPANYLOGO = '';

   $query = "SELECT * from tblcompany WHERE COMPANYID = '$companyid'";
   $result = mysqli_query($con, $query);
   $row = mysqli_fetch_array($result);

   $COMPANYID = $row['COMPANYID'];
   $COMPANYNAME = $row['COMPANYNAME'];
   $COMPANYADDRESS = $row['COMPANYADDRESS'];
   $COMPANYCONTACTNO = $row['COMPANYCONTACTNO'];
   $COMPANYSTATUS = $row['COMPANYSTATUS'];
   $COMPANYABOUT = $row['COMPANYABOUT'];
   $COMPANYEMAIL = $row['COMPANYEMAIL'];
   $COMPANYINDUSTRY = $row['COMPANYINDUSTRY'];
   $COMPANYSPECIALISM = $row['COMPANYSPECIALISM'];
   $COMPANYCOUNTRY = $row['COMPANYCOUNTRY'];
   $COMPANYCITY = $row['COMPANYCITY'];
   $COMPANYAWARD = $row['COMPANYAWARD'];
   $COMPANYYEAR = $row['COMPANYYEAR'];
   $COMPANYAWARDDESC  = $row['COMPANYAWARDDESC'];
   $COMPANYLOGO = $row['COMPANYLOGO'];


//////////////////////Get Company Details Ends//////////////////////




?>


<body>

   <!-- ============================================================== -->
   <!-- Preloader - style you can find in spinners.css -->
   <!-- ============================================================== -->
   <div class="preloader"></div>

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
         <a class="mobNavigation" data-toggle="collapse" href="#MobNav" role="button" aria-expanded="false" aria-controls="MobNav">
            <i class="fas fa-bars mr-2"></i>Dashboard Navigation
         </a>

         <?php include 'sidenav.php' ?>
         <div class="dashboard-content">
            <div class="dashboard-tlbar d-block mb-5">
               <div class="row">
                  <div class="colxl-12 col-lg-12 col-md-12">
                     <h1 class="ft-medium">Company</h1>
                     <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                           <li class="breadcrumb-item text-muted"><a href="#">Home</a></li>
                           <li class="breadcrumb-item text-muted"><a href="#">Dashboard</a></li>
                           <li class="breadcrumb-item"><a href="#" class="theme-cl">Edit Company</a></li>
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
                              <h4 class="mb-0 ft-medium fs-md"><i class="fa fa-file mr-1 theme-cl fs-sm"></i>Edit Company</h4>
                           </div>
                        </div>

                        <div class="_dashboard_content_body py-3 px-3">
                           <form class="row" method="post" enctype="multipart/form-data">
                              <div class="col-xl-12 col-lg-12 col-md-12">
                                 <div class="row">

                                    <div class="col-xl-12 col-lg-12 col-md-12">
                                       <div class="form-group">
                                          <label class="text-dark ft-medium">Company Name</label>
                                          <input type="text" class="form-control rounded" value="<?php echo $COMPANYNAME ?>" name="name" placeholder="Company Name" required>
                                       </div>
                                    </div>



                                    <div class="col-xl-6 col-lg-6 col-md-6">
                                       <div class="form-group">
                                          <label class="text-dark ft-medium">Company Email Address</label>
                                          <input type="text" class="form-control rounded" value="<?php echo $COMPANYEMAIL ?>" name="email" placeholder="Email" required>
                                       </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-6">
                                       <div class="form-group">
                                          <label class="text-dark ft-medium">Company Contact</label>
                                          <input type="number" class="form-control rounded" value="<?php echo $COMPANYCONTACTNO ?>" name="contact" placeholder="Contact" required>
                                       </div>
                                    </div>


                                    <div class="col-xl-6 col-lg-6 col-md-6">
                                       <div class="form-group">
                                          <label class="text-dark ft-medium">Industry</label>
                                          <select class="form-control rounded" name="industry" required>

                                             <?php if (!empty($_GET['COMPANYINDUSTRY'])) { ?>
                                                <option><?php echo $COMPANYNAME ?></option>
                                             <?php } ?>

                                             <option>IT & Software</option>
                                             <option>Bank Services</option>
                                             <option>Power Corporation</option>
                                             <option>Water Supply</option>
                                             <option>Chemical Plant</option>
                                             <option>Advertising</option>
                                             <option>Agriculture Industry </option>
                                             <option>Communications Industry </option>
                                             <option>Construction Industry </option>
                                             <option>Creative Industries </option>
                                             <option>Education</option>
                                             <option>Entertainment Industry </option>
                                             <option>Farming </option>
                                             <option>Fashion</option>
                                             <option>Finance</option>
                                             <option>Food Industry </option>
                                             <option>Green Industry </option>
                                             <option>Heavy Industry </option>
                                             <option>Hospitality Industry </option>
                                             <option>Information Industry </option>
                                             <option>Information Technology</option>
                                             <option>Infrastructure </option>
                                             <option>IT Industry </option>
                                             <option>Light Industry </option>
                                             <option>Manufacturing Industry</option>
                                             <option>Materials</option>
                                             <option>Media</option>
                                             <option>Music Industry </option>
                                             <option>Primary Industry </option>
                                             <option>Publishing</option>
                                             <option>Retail</option>
                                             <option>Robotics</option>
                                             <option>Secondary Industry </option>
                                             <option>Service Industry </option>
                                             <option>Space Industry </option>
                                             <option>Technology Industry </option>
                                             <option>Telecom</option>
                                             <option>Tertiary Sector </option>
                                          </select>
                                       </div>
                                    </div>


                                    <div class="col-xl-6 col-lg-6 col-md-6">
                                       <div class="form-group">
                                          <label class="text-dark ft-medium">Specialisms</label>
                                          <select class="form-control rounded" name="specialism" required>
                                             <?php if (!empty($_GET['COMPANYINDUSTRY'])) { ?>
                                                <option><?php echo $COMPANYSPECIALISM ?></option>
                                             <?php } ?>
                                             <option>Accounting</option>
                                             <option>Banking</option>
                                             <option>Software</option>
                                             <option>Hardware</option>
                                             <option>Hospital</option>
                                             <option>Fashion Design</option>
                                             <option>Life Sciences & Health Care</option>
                                             <option>Consumer & Industrial Products</option>
                                             <option>Energy & Resources</option>
                                             <option>Financial Services</option>
                                             <option>Public Sector</option>
                                             <option>Technology</option>
                                             <option>Technology, Media & Telecommunications</option>
                                             <option>Accommodation and Food Services</option>
                                             <option>Administration, Business Support</option>
                                             <option>Waste Management Services</option>
                                             <option>Agriculture</option>
                                             <option>Arts, Entertainment and Recreation</option>
                                             <option>Construction</option>
                                             <option>Educational Services</option>
                                             <option>Finance</option>
                                             <option>Finance and Insurance</option>
                                             <option>BPO Services</option>
                                             <option>Healthcare and Social Assistance
                                                Information</option>
                                             <option>Manufacturing</option>
                                             <option>Mining</option>
                                             <option>Professional, Scientific and Technical Services</option>
                                             <option>Real Estate and Rental and Leasing</option>
                                             <option>Retail Trade</option>
                                             <option>Transportation and Warehousing</option>
                                             <option>Utilities</option>
                                             <option>Industrial Machinery, Gas and Chemicals</option>
                                             <option>Consumer Goods and Services</option>
                                             <option>Business Franchises</option>
                                             <option>Advisory and Financial Services</option>
                                             <option>Life Sciences</option>
                                             <option>Online Retail</option>
                                             <option>Retail Market Reports</option>
                                             <option>Specialist Engineering, Infrastructure and Contractors</option>
                                             <option>Wholesale Trade</option>

                                          </select>
                                       </div>
                                    </div>




                                    <div class="col-xl-6 col-lg-6 col-md-6">
                                       <div class="form-group">
                                          <label class="text-dark ft-medium">Country</label>
                                          <select id="country" name="country" class="form-control" required>
                                             <option>Select Country</option>
                                             <?php if (!empty($_GET['COMPANYINDUSTRY'])) { ?>
                                                <option><?php echo $COMPANYCOUNTRY ?></option>
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
                                             <option value="Bosnia & Herzegovina">Bosnia & Herzegovina</option>
                                             <option value="Botswana">Botswana</option>
                                             <option value="Brazil">Brazil</option>
                                             <option value="British Indian Ocean Ter">British Indian Ocean Ter</option>
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
                                             <option value="Central African Republic">Central African Republic</option>
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
                                             <option value="Netherlands">Netherlands (Holland, Europe)</option>
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
                                             <option value="Republic of Montenegro">Republic of Montenegro</option>
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
                                             <option value="St Pierre & Miquelon">St Pierre & Miquelon</option>
                                             <option value="St Vincent & Grenadines">St Vincent & Grenadines</option>
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
                                             <option value="United Arab Erimates">United Arab Emirates</option>
                                             <option value="United States of America">United States of America</option>
                                             <option value="Uraguay">Uruguay</option>
                                             <option value="Uzbekistan">Uzbekistan</option>
                                             <option value="Vanuatu">Vanuatu</option>
                                             <option value="Vatican City State">Vatican City State</option>
                                             <option value="Venezuela">Venezuela</option>
                                             <option value="Vietnam">Vietnam</option>
                                             <option value="Virgin Islands (Brit)">Virgin Islands (Brit)</option>
                                             <option value="Virgin Islands (USA)">Virgin Islands (USA)</option>
                                             <option value="Wake Island">Wake Island</option>
                                             <option value="Wallis & Futana Is">Wallis & Futana Is</option>
                                             <option value="Yemen">Yemen</option>
                                             <option value="Zaire">Zaire</option>
                                             <option value="Zambia">Zambia</option>
                                             <option value="Zimbabwe">Zimbabwe</option>
                                          </select>
                                       </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-6">
                                       <div class="form-group">
                                          <label class="text-dark ft-medium">City</label>
                                          <input type="text" class="form-control" value="<?php echo $COMPANYCITY ?>" placeholder="City" name="city" required />
                                       </div>
                                    </div>


                                    <div class="col-xl-12 col-lg-12 col-md-12">
                                       <div class="form-group">
                                          <label class="text-dark ft-medium">Full Address</label>
                                          <input type="text" class="form-control" value="<?php echo $COMPANYADDRESS ?>" placeholder="#10 Marke Juger, SBI Road" name="address" required />
                                       </div>
                                    </div>

                                    <div class="col-xl-12 col-lg-12 col-md-12">
                                       <div class="form-group">
                                          <label class="text-dark ft-medium">About the Company</label>
                                          <textarea class="form-control rounded" placeholder="Company Description" name="about" required> <?php echo $COMPANYABOUT ?></textarea>
                                       </div>
                                    </div>

                                    <div class="dash_auth_thumb rounded p-1 border d-inline-flex mx-auto mb-3">
                                       <img src="../<?php echo $COMPANYLOGO ?>" class="img-fluid" width="100" alt="" />
                                    </div>

                                    <div class="col-xl-12 col-lg-12 col-md-12">
                                       <div class="form-group">
                                          <label class="text-dark ft-medium">Company Logo (Optional)</label>
                                          <input type="file" class="form-control" value="<?php echo $COMPANYNAME ?>" name="image" required />
                                       </div>
                                    </div>


                                    <div class="col-xl-4 col-lg-6 col-md-6">
                                       <div class="form-group">
                                          <label class="text-dark ft-medium">Awards</label>
                                          <input type="text" class="form-control" value="<?php echo $COMPANYAWARD ?>" placeholder="Award" name="award" required />
                                       </div>
                                    </div>

                                    <div class="col-xl-4 col-lg-6 col-md-6">
                                       <div class="form-group">
                                          <label class="text-dark ft-medium">Year</label>
                                          <input type="number" class="form-control" value="<?php echo $COMPANYYEAR ?>" placeholder="Year" name="award_year" required />
                                          <code>e.g 2020</code>
                                       </div>
                                    </div>

                                    <div class="col-xl-12 col-lg-12 col-md-12">
                                       <div class="form-group">
                                          <label class="text-dark ft-medium">Award Description</label>
                                          <textarea class="form-control rounded" rows="2" placeholder="Awards Description" name="award_disc" required><?php echo $COMPANYAWARDDESC ?></textarea>
                                       </div>
                                    </div>




                                    <div class="col-12">
                                       <div class="form-group">
                                          <button type="submit" name="edit_company" class="btn btn-md ft-medium text-light rounded theme-bg">Save </button>
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