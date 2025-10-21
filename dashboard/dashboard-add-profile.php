<!DOCTYPE html>
<html lang="zxx">

<?php require 'include/phpcode.php'; ?>

<!-- Mirrored from themezhub.net/live-workplex/workplex/candidate-dashboard.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Feb 2022 12:07:05 GMT -->

<head>
	<meta charset="utf-8" />
	<meta name="author" content="Themezhub" />
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Workplex - Creative Job Board HTML Template</title>

	<!-- Custom CSS -->
	<link href="assets/css/styles.css" rel="stylesheet">

</head>

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
		<?php include 'header.php' ?>

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
										<h4 class="mb-0 ft-medium fs-md"><i class="fa fa-file mr-1 theme-cl fs-sm"></i>1. Complete Your Profile</h4>
									</div>
								</div>

								<div class="_dashboard_content_body py-3 px-3">
									<form class="row" method="post" enctype="multipart/form-data">
										<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
											<?php
											if (!empty($APPLICANTPHOTO)) { ?>
												<div class="jb-list01-thumb">
													<img src="../<?php echo $APPLICANTPHOTO ?>" class="img-fluid" width="90" alt="" />
												</div>
											<?php } else { ?>
												<div class="custom-file avater_uploads">
													<!-- <input type="file" class="custom-file-input" id="customFile" name="image"> -->
													<label class="custom-file-label" for="customFile"><i class="fa fa-user"></i></label>
												</div>
											<?php } ?><br>

											<div class="col-xl-12 col-lg-12 col-md-12">
												<div class="form-group">
													<label class="text-dark ft-medium">Image Profile</label>
													<input type="file" name="image" class="form-control-file" id="customFile" required>
												</div>
											</div>
										</div>

										<div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">
											<div class="row">

												<!-- <div class="col-xl-6 col-lg-6">
													<div class="form-group">
														<label class="text-dark ft-medium">First Name</label>
														<input type="text" class="form-control rounded" placeholder="Full Name" name="FName">
													</div>
												</div>
												<div class="col-xl-6 col-lg-6">
													<div class="form-group">
														<label class="text-dark ft-medium">Other Names</label>
														<input type="text" class="form-control rounded" placeholder="Full Name" name="OName">
													</div>
												</div>
												<div class="col-xl-12 col-lg-12 col-12">
													<div class="form-group">
														<label class="text-dark ft-medium">Email</label>
														<input type="email" class="form-control rounded" value="uppcl@gmail.com" name="email" value="">
													</div>
												</div> -->
												<div class="col-xl-6 col-lg-6">
													<div class="form-group">
														<label class="text-dark ft-medium">Professional Title</label>
														<input type="text" class="form-control rounded" placeholder="e.g. Web Designer" name="job_title" required value="<?php if (!empty($JOBTITLE)) echo $JOBTITLE; ?>">
													</div>
												</div>
												<div class="col-xl-6 col-lg-6">
													<div class="form-group">
														<label class="text-dark ft-medium">Job category</label>
														<select class="form-control rounded" name="job_categoryid" required>
															<?php if (!$JOBCATEGORYID) { ?>
																<option value="<?php echo $JOBCATEGORYID ?>" hidden><?php echo $SUBCATEGORY ?></option>
															<?php } else { ?>
																<option hidden>Choose Job Category</option>
															<?php } ?>

															<?php
															$query = "SELECT * from tbljobsubcategory ORDER BY SUBCATEGORY ASC" or die(mysqli_error($con));

															$run = mysqli_query($con, $query);

															while ($row = mysqli_fetch_array($run)) {
															?>
																<option value="<?php echo $row['ID'] ?>"><?php echo $row['SUBCATEGORY'] ?></option>

															<?php } ?>
															<option value="0">Others</option>

														</select>
													</div>
												</div>
												<div class="col-xl-6 col-lg-6">
													<div class="form-group">
														<label class="text-dark ft-medium">Phone Number</label>
														<input type="number" class="form-control rounded" placeholder="Phone Number" name="phoneno" required value="<?php if (!empty($CONTACTNO)) echo $CONTACTNO; ?>">
													</div>
												</div>

												<div class="col-xl-6 col-lg-6">
													<div class="form-group">
														<label class="text-dark ft-medium">Gender</label>
														<select class="form-control rounded" name="sex" required>

															<?php if (!empty($SEX)) { ?><option selected hidden><?php echo $SEX; ?></option> <?php } else { ?>
																<option hidden>Choose gender</option>
															<?php } ?>
															<option>Male</option>
															<option>Female</option>
														</select>
													</div>
												</div>

												<div class="col-12">
													<div class="form-group">
														<label class="text-dark ft-medium">Date Of Birth</label>
														<input type="date" class="form-control rounded" placeholder="dd-mm-yyyy" name="dob" required value="<?php if (!empty($BIRTHDATE)) echo $BIRTHDATE; ?>">
													</div>
												</div>

												<div class="col-xl-12 col-lg-12">
													<div class="form-group">
														<label class="text-dark ft-medium">About Me</label>
														<textarea name="about_me" class="form-control with-light" placeholder="You can write about your years of experience, industry, or skills. People also talk about their achievements or previous job experience" required><?php if (!empty($ABOUTME)) echo $ABOUTME; ?></textarea>
													</div>
												</div>


												<div class="col-xl-12 col-lg-12">
													<div class="form-group">
														<?php echo $msg ?>
													</div>
												</div>

												<div class="col-xl-12 col-lg-12">
													<div class="form-group">
														<?php 
														// if ($USERID != $session_id) {
														?>
															<button type="submit" name="save_data" class="btn btn-md ft-medium text-light rounded theme-bg">Save Data & Continue</button>
														<?php 
														// } 
													?>
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

					$querycomp = "SELECT * from tblapplicants WHERE USERID = '$session_id'";
					$resultcomp = mysqli_query($con, $querycomp);
					$rowcomp = mysqli_fetch_array($resultcomp);

					$USERID = $rowcomp['USERID'];
					if ($USERID == $session_id) {
					?>

						<form method="post">
							<div class="row">

								<div class="col-lg-12 col-md-12" id="section23">
									<div class="_dashboard_content bg-white rounded mb-4">
										<div class="_dashboard_content_header br-bottom py-3 px-3">
											<div class="_dashboard__header_flex">
												<h4 class="mb-0 ft-medium fs-md"><i class="fas fa-lock mr-1 theme-cl fs-sm"></i>2. Contact Information</h4>
											</div>
										</div>

										<div class="_dashboard_content_body py-3 px-3">
											<!-- <form class="row"> -->
											<div class="row">
												<div class="col-xl-6 col-lg-6 col-md-12">
													<div class="form-group">
														<label class="text-dark ft-medium">Country</label>
														<select id="country" name="country" class="form-control" required>
															<?php if (!empty($COUNTRY)) { ?> <option value="<?php echo $COUNTRY; ?>" hidden><?php echo $COUNTRY; ?></option>
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
												<div class="col-xl-6 col-lg-6 col-md-12">
													<div class="form-group">
														<label class="text-dark ft-medium">City</label>
														<input type="text" class="form-control rounded" placeholder="City" name="city" required value="<?php if (!empty($CITY)) {
																																															echo $CITY;
																																														} ?>">
													</div>
												</div>

												<div class="col-xl-12 col-lg-12 col-md-12">
													<div class="form-group">
														<label class="text-dark ft-medium">Full Address</label>
														<input type="text" class="form-control rounded" placeholder="#10 Marke Juger, SBI Road" name="address" required value="<?php if (!empty($ADDRESS)) {
																																																							echo $ADDRESS;
																																																						} ?>">
													</div>
												</div>

												<div class="col-xl-12 col-lg-12">
													<div class="form-group">
														<!-- <button type="submit" class="btn btn-md ft-medium text-light rounded theme-bg">Save Changes</button> -->
													</div>
												</div>
											</div>
											<!-- </form> -->
										</div>
									</div>
								</div>

								<div class="col-lg-12 col-md-12">
									<div class="_dashboard_content bg-white rounded mb-4">
										<div class="_dashboard_content_header br-bottom py-3 px-3">
											<div class="_dashboard__header_flex">
												<h4 class="mb-0 ft-medium fs-md"><i class="ti-facebook mr-1 theme-cl fs-sm"></i>3. Social Accounts</h4>
											</div>
										</div>

										<div class="_dashboard_content_body py-3 px-3">
											<!-- <form class="row"> -->
											<div class="row">
												<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
													<div class="form-group">
														<label class="text-dark ft-medium">Facebook</label>
														<input type="text" class="form-control rounded" placeholder="https://www.facebook.com/" name="fb" required value="<?php if (!empty($FB_link)) {
																																																					echo $FB_link;
																																																				} ?>">
													</div>
												</div>

												<!-- <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
											<div class="form-group">
												<label class="text-dark ft-medium">Twitter</label>
												<input type="text" class="form-control rounded" placeholder="https://www.twitter.com/">
											</div>
										</div> -->
												<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
													<div class="form-group">
														<label class="text-dark ft-medium">LinkedIn</label>
														<input type="text" class="form-control rounded" placeholder="https://www.linkedin.com/" name="lin" required value="<?php if (!empty($LinkedIn_link)) {
																																																						echo $LinkedIn_link;
																																																					} ?>">
													</div>
												</div>
												<!-- s -->

												<!-- <div class="col-xl-12 col-lg-12">
												<div class="form-group">
													<button type="submit" class="btn btn-md ft-medium text-light rounded theme-bg">Save Changes</button>
												</div>
											</div> -->

											</div>
											<div class="col-xl-12 col-lg-12">
												<div class="form-group">
													<?php echo $msg2 ?>
												</div>
											</div>
											<!-- </form> -->
										</div>
									</div>
								</div>

							</div>
							<div class="col-xl-12 col-lg-12">
								<div class="form-group">
									<?php if ((empty($COUNTRY) || (empty($CITY)) || (empty($ADDRESS)) || (empty($FB_link)) || (empty($LinkedIn_link)))) {
									?>
										<button type="submit" name="save_info" class="btn btn-md ft-medium text-light rounded theme-bg">Save Info</button>
									<?php } ?>
								</div>
							</div>
						</form>
						<hr>

						<?php if (!((empty($COUNTRY) || (empty($CITY)) || (empty($ADDRESS)) || (empty($FB_link)) || (empty($LinkedIn_link))))) {
						?>
							<!-- Add Education -->
							<form method="post">
								<div class="row">
									<div class="col-lg-12 col-md-12">
										<div class="_dashboard_content bg-white rounded mb-4" id="section456">
											<div class="_dashboard_content_header br-bottom py-3 px-3">
												<div class="_dashboard__header_flex">
													<h4 class="mb-0 ft-medium fs-md"><i class="fas fa-graduation-cap mr-1 theme-cl fs-sm"></i>4. Education Details <code>Most current level of education</code></h4>
												</div>
											</div>

											<div class="_dashboard_content_body py-3 px-3">
												<div class="row">
													<div class="col-xl-12 col-lg-12">
														<div class="gray rounded p-3 mb-3 position-relative">
															<!-- <button class="aps-clone"><i class="fas fa-times"></i></button> -->
															<div class="form-group">
																<label class="text-dark ft-medium">School Name</label>
																<input type="text" class="form-control rounded" placeholder="School Name" name="schl_name" required value="<?php if (!empty($SCHOOLNAME)) echo $SCHOOLNAME ?>">
															</div>
															<div class="form-group">
																<label class="text-dark ft-medium">Qualification</label>
																<!-- <input type="text" class="form-control rounded" placeholder="Qualification Title" name="qualification" required> -->
																<select class="custom-select rounded" name="qualification" required>
																	<?php if (!empty($DEGREE)) { ?><option value="<?php echo $DEGREE ?>" hidden><?php echo $DEGREE ?></option>
																	<?php } else { ?>
																		<option value="" hidden>Please select</option>
																	<?php } ?>
																	<option>B.Sc</option>
																	<option>Master Degree</option>
																	<option>BPharma</option>
																	<option>P.H.D.</option>
																	<option>HND</option>
																	<option>OND</option>
																</select>
															</div>
															<!-- <div class="form-row">
															<div class="col-6">
																<div class="form-group">
																	<label class="text-dark ft-medium">Start Date</label>
																	<input type="date" class="form-control rounded" placeholder="dd-mm-yyyy" name="schl_startdate" required>
																</div>
															</div>
															<div class="col-6">
																<div class="form-group">
																	<label class="text-dark ft-medium">End Date</label>
																	<input type="date" class="form-control rounded" placeholder="dd-mm-yyyy" name="schl_enddate" required>
																</div>
															</div>
														</div> -->
															<!-- <div class="form-group">
														<label class="text-dark ft-medium">Note</label>
														<textarea class="form-control ht-80" placeholder="Note Optional"></textarea>
													</div> -->
														</div>
													</div>
													<div class="col-xl-12 col-lg-12">
														<div class="form-group">
															<!-- <button type="submit" class="btn gray ft-medium apply-btn fs-sm rounded"><i class="fas fa-plus mr-1"></i>Add Education</button> -->
														</div>
													</div>

												</div>
											</div>
										</div>
									</div>
								</div>


								<!-- Add Experience -->
								<div class="row">
									<div class="col-lg-12 col-md-12">
										<div class="_dashboard_content bg-white rounded mb-4">
											<div class="_dashboard_content_header br-bottom py-3 px-3">
												<div class="_dashboard__header_flex">
													<h4 class="mb-0 ft-medium fs-md"><i class="fas fa-graduation-cap mr-1 theme-cl fs-sm"></i>5. Experience Details (Optional)<code>Most recent experience</code></h4>
												</div>
											</div>

											<div class="_dashboard_content_body py-3 px-3">
												<div class="row">
													<div class="col-xl-12 col-lg-12">
														<div class="gray rounded p-3 mb-3 position-relative">
															<!-- <button class="aps-clone"><i class="fas fa-times"></i></button> -->
															<div class="form-group">
																<label class="text-dark ft-medium">Employer</label>
																<select class="custom-select rounded" name="company_name_select" id="company_select" onchange="set_company()">

																	<?php if (!empty($EXCOMPANYNAME)) { ?><option value="<?php echo $EXCOMPANYNAME ?>" hidden><?php echo $EXCOMPANYNAME ?></option>
																	<?php } else { ?>
																		<option value="">Select Company</option>
																	<?php } ?>

																	<?php $sql = "SELECT * from tblcompany ORDER BY COMPANYNAME ASC";
																	$run = mysqli_query($con, $sql);
																	while ($row = mysqli_fetch_array($run)) { ?>
																		<option value="<?php echo ($row['COMPANYNAME']); ?>">
																			<?php echo ($row['COMPANYNAME']); ?>
																		</option>
																	<?php
																	} ?>
																	<option>Others (Please specify)</option>
																</select>
															</div>
															<style>
																#company_other {
																	display: none;
																}
															</style>

															<div class="form-group">
																<input type="text" class="form-control rounded" placeholder="Company Name" name="company_name_specify" id="company_other">
															</div>

															<script>
																function set_company() {
																	var title_select = $('#company_select').val(); //document.getElementById("title_select").innerHTML

																	if (title_select == "Others (Please specify)") {
																		document.getElementById("company_other").style.display = "block";
																	} else {
																		document.getElementById("company_other").style.display = "none";
																	}
																	// document.getElementById("set_wallet").innerHTML = set_wallet;
																}
															</script>
															<div class="form-group">
																<label class="text-dark ft-medium">Job Title</label>
																<input type="text" class="form-control rounded" placeholder="Designation Title" name="job_title" value="<?php if (!empty($EXJOBTITLE)) echo $EXJOBTITLE ?>">
															</div>
															<!-- <div class="form-row">
															<div class="col-6">
																<div class="form-group">
																	<label class="text-dark ft-medium">Start Date</label>
																	<input type="date" class="form-control rounded" placeholder="dd-mm-yyyy" name="job_startdate">
																</div>
															</div>
															<div class="col-6">
																<div class="form-group">
																	<label class="text-dark ft-medium">End Date</label>
																	<input type="date" class="form-control rounded" placeholder="dd-mm-yyyy" name="job_enddate">
																</div>
															</div>
														</div> -->
															<!-- <div class="form-group">
															<label class="text-dark ft-medium">Note</label>
															<textarea class="form-control ht-80" placeholder="Note Optional"></textarea>
														</div> -->
														</div>
													</div>
													<div class="col-xl-12 col-lg-12">
														<div class="form-group">
															<!-- <button type="submit" class="btn gray ft-medium apply-btn fs-sm rounded"><i class="fas fa-plus mr-1"></i>Add Experience</button> -->
														</div>
													</div>

												</div>
											</div>
										</div>
									</div>
								</div>

								<!-- Add Skills -->
								<div class="row">
									<div class="col-lg-12 col-md-12">
										<div class="_dashboard_content bg-white rounded mb-4">
											<div class="_dashboard_content_header br-bottom py-3 px-3">
												<div class="_dashboard__header_flex">
													<h4 class="mb-0 ft-medium fs-md"><i class="fas fa-graduation-cap mr-1 theme-cl fs-sm"></i>6. Skills Details</h4>
												</div>
											</div>

											<div class="_dashboard_content_body py-3 px-3">
												<div class="row">
													<div class="col-xl-12 col-lg-12">
														<div class="gray rounded p-3 mb-3 position-relative">
															<!-- <button class="aps-clone"><i class="fas fa-times"></i></button> -->
															<div class="form-group">
																<label class="text-dark ft-medium">Skills Name <code>Seperate with a comma</code></label>
																<input type="text" class="form-control rounded" placeholder="e.g Mysql, Javascript, ..." name="skills" required value="<?php if (!empty($SKILLS)) echo $SKILLS ?>">
															</div>
															<!-- <div class="form-group">
														<label class="text-dark ft-medium">Percentage</label>
														<input type="text" class="form-control rounded" placeholder="e.x. 80%">
													</div> -->
														</div>
													</div>
													<div class="col-xl-12 col-lg-12">
														<div class="form-group">
															<!-- <button type="submit" class="btn gray ft-medium apply-btn fs-sm rounded"><i class="fas fa-plus mr-1"></i>Add Skills</button> -->
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

								<!-- Add Skills -->
								<div class="row">
									<div class="col-lg-12 col-md-12">
										<button type="submit" name="save_preview" class="btn btn-md ft-medium text-light rounded theme-bg">Save & Preview</button>
									</div>
								</div>
							</form>
					<?php }
					} ?>
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

<!-- Mirrored from themezhub.net/live-workplex/workplex/dashboard-add-Profile.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Feb 2022 12:08:06 GMT -->

</html>