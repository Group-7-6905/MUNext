<?php
require 'session.php';
$msg = '';


//////////////////////////Admin Detail///////////////////////////////////
// fetch user row safely
$query = mysqli_query($con, "SELECT * FROM tblusers WHERE USERID = '$session_id'") or die(mysqli_error($con));
$row = mysqli_fetch_assoc($query) ?: []; // returns empty array when no row

$USERID   = $row['USERID']  ?? '';
$FNAME    = $row['FNAME']   ?? '';
$ONAME    = $row['ONAME']   ?? '';
$EMAIL    = $row['EMAIL']   ?? '';
$USERNAME = $row['USERNAME'] ?? '';
$PASS     = $row['PASS']    ?? '';
$ROLE     = $row['ROLE']    ?? '';
// $PICLOCATION = $row['PICLOCATION'] ?? '';
$FULLNAME = trim(($FNAME ?? '') . ' ' . ($ONAME ?? ''));

/////////////////////////Admin Details ends/////////////////////////////////////



$msg2='';
/////////////////Add Company/////////////////////////////


if (isset($_POST['add_company'])) {

   $name = $_POST['name'];
   $email = $_POST['email'];
   $contact = $_POST['contact'];
   $industry = $_POST['industry'];
   $specialism = $_POST['specialism'];

   $country = $_POST['country'];
   $city = $_POST['city'];
   $address = $_POST['address'];
   $about = $_POST['about'];
   $award = $_POST['award'];
   $award_year = $_POST['award_year'];
   $award_disc = $_POST['award_disc'];


   // Get image name
   $image = $_FILES['image']['name'];
   $target = "../company_logo/" . basename($image);

   $image_url = "company_logo/" . $image;

   // Check file size
   if ($_FILES["image"]["size"] > 500000) {
      $msg = "<div style='color:red'>File is too large!</div>";
   } else {


      $query = "INSERT INTO tblcompany (COMPANYNAME, COMPANYADDRESS, COMPANYCONTACTNO, COMPANYSTATUS, COMPANYABOUT, COMPANYEMAIL, COMPANYINDUSTRY, 	COMPANYSPECIALISM, COMPANYCOUNTRY, COMPANYCITY, COMPANYAWARD, COMPANYYEAR, COMPANYAWARDDESC, COMPANYLOGO) VALUES ('$name','$address','$contact','Active','$about','$email','$industry','$specialism','$country','$city','$award','$award_year','$award_disc', '$image_url')" or die(mysqli_error($con));
      $result = mysqli_query($con, $query);

      if ((move_uploaded_file($_FILES['image']['tmp_name'], $target)) && ($result)) {


         echo "<script>alert('Company Added!')</script>";

         header('location: ./dashboard-companys.php');
      } else {
         $msg = "<div style='color:red'>Error occured...!</div>";
         echo mysqli_error($con);
      }
   }
}
/////////////////////////Add company ends/////////////////////////





/////////////////Edit Company/////////////////////////////
// $msg = '';

if (isset($_POST['edit_company'])) {

   $name = $_POST['name'];
   $email = $_POST['email'];
   $contact = $_POST['contact'];
   $industry = $_POST['industry'];
   $specialism = $_POST['specialism'];

   $country = $_POST['country'];
   $city = $_POST['city'];
   $address = $_POST['address'];
   $about = $_POST['about'];
   $award = $_POST['award'];
   $award_year = $_POST['award_year'];
   $award_disc = $_POST['award_disc'];


   // Get image name
   $image = $_FILES['image']['name'];
   $target = "../company_logo/" . basename($image);

   $image_url = "company_logo/" . $image;

   // Check file size
   if ($_FILES["image"]["size"] > 500000) {
      $msg = "<div style='color:red'>File is too large!</div>";
   } else {



      $query = "UPDATE tblcompany SET COMPANYNAME = '$name', COMPANYADDRESS = '$address', COMPANYCONTACTNO = '$contact', COMPANYABOUT = '$about', COMPANYEMAIL = '$email', COMPANYINDUSTRY = '$industry', COMPANYSPECIALISM = '$specialism', COMPANYCOUNTRY = '$country', COMPANYCITY = '$city', COMPANYAWARD = '$award', COMPANYYEAR = '$award_year',COMPANYAWARDDESC = '$award_disc',COMPANYLOGO = '$image_url' WHERE COMPANYID = '$companyid'";

      $result = mysqli_query($con, $query);

      if ((move_uploaded_file($_FILES['image']['tmp_name'], $target)) && ($result)) {


         echo "<script>alert('Company Updated!')</script>";

         header('location: ./dashboard-companys.php');
      } else {
         $msg = "<div style='color:red'>Error occured...!</div>";
         echo mysqli_error($con);
      }
   }
}
/////////////////////////Edit company ends/////////////////////////



///////////////Add Job ///////////////////////////
if (isset($_POST['add_job'])) {

   $job_title = $_POST['job_title_select'];
   if ($job_title == "Others (Please specify)") {
      $job_title = $_POST['job_title_specify']; //
   }


   $job_categoryid = $_POST['job_categoryid']; //
   $workplace_policy = $_POST['workplace_policy']; //
   $job_desc = $_POST['job_desc']; //
   $companyid = $_POST['company']; //
   $job_type = $_POST['job_type']; //
   $career_level = $_POST['career_level']; //
   $experience = $_POST['experience']; //
   $qualification = $_POST['qualification']; //
   $gender = $_POST['gender']; //

   $salary = 'Not Specified';
   if (!empty($_POST['salary'])) {
      $salary = $_POST['salary']; //
   }

   $deadline = $_POST['deadline'];


   $query = "INSERT INTO tbljob (COMPANYID, WORKPLACE_POLICY, JOBTITLE, JOBCATEGORYID, SALARY, JOBTYPE, QUALIFICATION, JOBDESCRIPTION, PREFEREDSEX, CAREERLEVEL, WORKEXPERIENCE, DEADLINE, JOBSTATUS, DATEPOSTED) VALUES ('$companyid','$workplace_policy','$job_title', '$job_categoryid', '$salary','$job_type','$qualification','$job_desc','$gender','$career_level','$experience','$deadline','Vacancy', now())" or die(mysqli_error($con));
   $result = mysqli_query($con, $query);

   if (($result)) {

      $query = "SELECT * from tbljob order by JOBID desc";
      $result = mysqli_query($con, $query);
      $row = mysqli_fetch_array($result);

      // $JOBID = $row['JOBID'];
      $JOBID   = $row['JOBID']  ?? '';

      // for ($i = 0; $i < sizeof($question); $i++) {
      //    // '" . $question[$i] . "'
      //    $query = "INSERT INTO tbljobscreening_ques (job_id, question_id) VALUES ('$JOBID','$question')" or die(mysqli_error($con));
      //    $result = mysqli_query($con, $query);
      // }

      if (!empty($_POST['question'])) {
         $ques = $_POST['question'];

         for ($i = 0; $i < sizeof($ques); $i++) {
            // '" . $ques[$i] . "'
            $query = "INSERT INTO tbljobscreening_ques (job_id, question_id) VALUES ('$JOBID','" . $ques[$i] . "')" or die(mysqli_error($con));
            $result = mysqli_query($con, $query);
         }
      }

      echo "<script>alert('Job Added!')</script>";

      header('location: ./dashboard-manage-jobs.php');
   } else {
      $msg = "<div style='color:red'>Error occured...!</div>";
      echo mysqli_error($con);
   }
}
///////////////Add Job Ends /////////////////////




///////////////Edit Job ///////////////////////////
if (isset($_POST['edit_job'])) {


   $job_title = $_POST['job_title_select'];
   if ($job_title == "Others (Please specify)") {
      $job_title = $_POST['job_title_specify']; //
   }

   $job_categoryid = $_POST['job_categoryid'];
   $workplace_policy = $_POST['workplace_policy']; //
   $job_desc = $_POST['job_desc']; //
   $companyid = $_POST['company']; //
   $job_type = $_POST['job_type']; //
   $career_level = $_POST['career_level']; //
   $experience = $_POST['experience']; //
   $qualification = $_POST['qualification']; //
   $gender = $_POST['gender']; //

   $salary = 'Not Specified';
   if (!empty($_POST['salary'])) {
      $salary = $_POST['salary']; //
   }

   $deadline = $_POST['deadline'];

   // $question = '';



   $query = "UPDATE tbljob SET COMPANYID = '$companyid', WORKPLACE_POLICY = '$workplace_policy', JOBTITLE = '$job_title', JOBCATEGORYID = '$job_categoryid', SALARY = '$salary', JOBTYPE = '$job_type', QUALIFICATION = '$qualification',JOBDESCRIPTION = '$job_desc', PREFEREDSEX = '$gender', CAREERLEVEL = '$career_level',WORKEXPERIENCE = '$experience', DEADLINE = '$deadline' WHERE JOBID = '$id'";

   $result = mysqli_query($con, $query);

   if (($result)) {

      $delete_sql = "DELETE from tbljobscreening_ques where job_id='$id'";
      mysqli_query($con, $delete_sql);

      $query = "SELECT * from tbljob where JOBID ='$id' order by JOBID desc";
      $result = mysqli_query($con, $query);
      $row = mysqli_fetch_array($result);

      // $JOBID = $row['JOBID'];
       $JOBID   = $row['JOBID']  ?? '';



      if (!empty($_POST['question'])) {
         $ques = $_POST['question'];

         /////////////// $c = sizeof($ques); 

         for ($i = 0; $i < sizeof($ques); $i++) {
            // '" . $ques[$i] . "'
            $query = "INSERT INTO tbljobscreening_ques (job_id, question_id) VALUES ('$JOBID','" . $ques[$i] . "')" or die(mysqli_error($con));
            $result = mysqli_query($con, $query);
         }
      }


      echo "<script>alert('Job Updated!')</script>";

      header('location: ./dashboard-manage-jobs.php');
   } else {
      $msg = "<div style='color:red'>Error occured...!</div>";
      echo mysqli_error($con);
   }
}
///////////////Edit Job Ends /////////////////////





////////////Add Screening Questions ////////////

if (isset($_POST['add_question'])) {


   $q_title = $_POST['q_title']; //
   $question = $_POST['question']; //
   // $ans = $_POST['ans'];   
   $opt_A = $_POST['opt_A']; //
   $opt_B = $_POST['opt_B']; //
   $opt_C = $_POST['opt_C']; //
   $opt_D = $_POST['opt_D']; //
   $opt_E = $_POST['opt_E']; //



   $ideal_ans_opt = $_POST['ideal_ans_opt']; //

   if ($ideal_ans_opt == "A") {
      $ideal_answer = $_POST['opt_A'];
   }
   elseif ($ideal_ans_opt == "B") {
      $ideal_answer = $_POST['opt_B'];
   } elseif ($ideal_ans_opt == "C") {
      $ideal_answer = $_POST['opt_C'];
   } elseif ($ideal_ans_opt == "D") {
      $ideal_answer = $_POST['opt_D'];
   } else{
      $ideal_answer = $_POST['opt_E'];
   }



   $status = 'Active';

   $query = "INSERT INTO tblscreening (q_title, question, opt_A, opt_B, opt_C, opt_D, opt_E, status) VALUES ('$q_title', '$question', '$opt_A', '$opt_B', '$opt_C', '$opt_D', '$opt_E', '$status')" or die(mysqli_error($con));
   $result = mysqli_query($con, $query);

   if (($result)) {
      $query = "SELECT * from tblscreening order by id desc";
      $result = mysqli_query($con, $query);
      $row = mysqli_fetch_array($result);

      // $question_id = $row['id'];
       $question_id   = $row['id']  ?? '';

      $query = "INSERT INTO tblscreening_answer (question_id, ideal_ans_opt, ideal_ans) VALUES ('$question_id', '$ideal_ans_opt', '$ideal_answer')" or die(mysqli_error($con));
      $results = mysqli_query($con, $query);
      if (($results)) {

         echo "<script>alert('Question Added!')</script>";

      // header('location: ./dashboard-jobs.php');
      } else {
         $msg = "<div style='color:red'>Error occured...!</div>";
         echo mysqli_error($con);
      }

   } else {
      $msg = "<div style='color:red'>Error occured...!</div>";
      echo mysqli_error($con);
   }
}

/////////////End Screening Questions ///////////





////////////Edit Screening Questions ////////////

if (isset($_POST['edit_question'])) {

   $q_title = $_POST['q_title']; //
   $question = $_POST['question']; //
   // $ans = $_POST['ans'];   
   $opt_A = $_POST['opt_A']; //
   $opt_B = $_POST['opt_B']; //
   $opt_C = $_POST['opt_C']; //
   $opt_D = $_POST['opt_D']; //
   $opt_E = $_POST['opt_E']; //



   $ideal_ans_opt = $_POST['ideal_ans_opt']; //

   if ($ideal_ans_opt == "A") {
      $ideal_answer = $_POST['opt_A'];
   } elseif ($ideal_ans_opt == "B") {
      $ideal_answer = $_POST['opt_B'];
   } elseif ($ideal_ans_opt == "C") {
      $ideal_answer = $_POST['opt_C'];
   } elseif ($ideal_ans_opt == "D") {
      $ideal_answer = $_POST['opt_D'];
   } else {
      $ideal_answer = $_POST['opt_E'];
   }



   $query = "UPDATE tblscreening SET q_title = '$q_title', question = '$question', opt_A = '$opt_A', opt_B = '$opt_B', opt_C = '$opt_C', opt_D = '$opt_D', opt_E = '$opt_E' WHERE id = '$id'";

   $result = mysqli_query($con, $query);

   if (($result)) {

      $question_id = $id;

      $query = "UPDATE tblscreening_answer SET ideal_ans_opt = '$ideal_ans_opt', ideal_ans = '$ideal_answer' WHERE question_id = '$question_id'";
      $results = mysqli_query($con, $query);
      if (($results)) {

         echo "<script>alert('Question Updated!')</script>";

         header('location: ./dashboard-screen-question-add.php');
      } else {
         $msg = "<div style='color:red'>Error occured...!</div>";
         echo mysqli_error($con);
      }
   } else {
      $msg = "<div style='color:red'>Error occured...!</div>";
      echo mysqli_error($con);
   }
}

/////////////End Edit Screening Questions ///////////


////////////Add Category ////////////
if (isset($_POST['add_category'])) {


   $category = $_POST['category']; //

   $query = "INSERT INTO tbljobcategory (CATEGORY) VALUES ('$category')" or die(mysqli_error($con));
   $result = mysqli_query($con, $query);

   if (($result)) {

      echo "<script>alert('Category Added!')</script>";

      // header('location: ./dashboard-job-category.php');


   } else {
      $msg = "<div style='color:red'>Error occured...!</div>";
      echo mysqli_error($con);
   }
}
/////////////End Category ///////////

////////////Edit Category ////////////
if (isset($_POST['edit_category'])) {


   $category = $_POST['category']; //

   $query = "UPDATE tbljobcategory SET CATEGORY = '$category' WHERE ID = '$categoryid'";

   $result = mysqli_query($con, $query);

   if (($result)) {

      echo "<script>alert('Category Updated!')</script>";

      header('location: ./dashboard-job-category.php');
   } else {
      $msg = "<div style='color:red'>Error occured...!</div>";
      echo mysqli_error($con);
   }
}
/////////////End Edit Category ///////////





////////////Add SubCategory ////////////

if (isset($_POST['add_subcategory'])) {


   $subcategory = $_POST['subcategory']; //
   $categoryid = $_POST['categoryid']; //

   $query = "INSERT INTO tbljobsubcategory (CATEGORYID,SUBCATEGORY) VALUES ('$categoryid', '$subcategory')" or die(mysqli_error($con));
   $result = mysqli_query($con, $query);

   if (($result)) {

      echo "<script>alert('Subcategory Added!')</script>";

      // header('location: ./dashboard-job-category.php');


   } else {
      $msg = "<div style='color:red'>Error occured...!</div>";
      echo mysqli_error($con);
   }
}

/////////////End SubCategory ///////////

////////////Edit Subcategory ////////////
if (isset($_POST['edit_subcategory'])) {

   $subcategory = $_POST['subcategory']; //
   $categoryid = $_POST['categoryid']; //

   $query = "UPDATE tbljobsubcategory SET CATEGORYID = '$categoryid', SUBCATEGORY = '$subcategory' WHERE ID = '$subcategoryid'";

   $result = mysqli_query($con, $query);

   if (($result)) {

      echo "<script>alert('Subcategory Updated!')</script>";

      header('location: ./dashboard-job-category.php');
   } else {
      $msg = "<div style='color:red'>Error occured...!</div>";
      echo mysqli_error($con);
   }
}
/////////////End Edit Subcategory ///////////







////////////////////////Save/Bookmark Resume..../////////////////


if (isset($_POST['save_resume'])) {

   $jobID = $_POST['jobID'];
   $userID = $_POST['userID'];
   $jobapplicationid = $_POST['jobapplicationid'];
   


   $query = "INSERT INTO tblbookmarkresume (USERID, JOBAPPLICATIONID, JOBRESUMEID, DATETIME) VALUES ('$userID', '$jobapplicationid', '$jobID', now())" or die(mysqli_error($con));

   $result = mysqli_query($con, $query);


   if ($result) {

?>
<script>
// alert('Resume Saved!');
location.href = "./dashboard-manage-applications.php";
</script>
<?php

      //   header('location: ./dashboard-add-profile.php#section456');
   } else {
      $msg2 = "<div style='color:red'>Error occured...!</div>";
      echo mysqli_error($con);
   }
}
////////////////////////Save/Bookmark Resume..../////////////////


////////////////////Download Resume///////////////////////////

if (isset($_POST['download_resume'])) {

   $resume_url = $_POST['resume_url'];

   $filepath = '../' . $resume_url;

   if (file_exists($filepath)) {
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename=' . basename($filepath));
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize('../' . $resume_url));
      readfile('../' . $resume_url);
   }
}
////////////////////End Download Resume///////////////////////////





///////////////Change Password //////////////////
if (isset($_POST['change_password'])) {

   $oldpassword = $_POST['oldpassword'];
   $newpassword = $_POST['newpassword'];
   $rnewpassword = $_POST['rnewpassword'];

   if (trim($newpassword) == trim($rnewpassword)) {

      $query = mysqli_query($con, "SELECT * FROM tblusers WHERE USERID = '$session_id'") or die(mysqli_error($con));
      $count = mysqli_num_rows($query);
      $row = mysqli_fetch_array($query);

      if ($count > 0) {
         // verify password
         if (password_verify($oldpassword, $row['PASS'])) {

            $hashed_pass = password_hash($oldpassword, PASSWORD_DEFAULT);

            $query = "UPDATE tblusers SET PASS = '$hashed_pass' WHERE USERID = '$session_id'";

            $result = mysqli_query($con, $query);

            if ($result) {

      ?><script>
alert('Password Updated!')
</script><?php

                        // header('location: ./dashboard-my-profile.php#section23');
                     } else {
                        $msg2 = "<div style='color:red'>Error occured...!</div>";
                        echo mysqli_error($con);
                     }
                  } else {
                     $msg2 = "<div style='color:red'>Incorrect Password!</div>";
                  }
               }
            } else {
               $msg2 = "<div style='color:red'>Password do not match!<br>Please Retype Password</div>";
            }
         }

/////////////////End Change Password////////////////////



///////////////////Change Application Status///////////////
if (isset($_POST['applicationstatus'])) {
   
   $applicationstatus = $_POST['applicationstatus'];
   $jobID = $_POST['jobID'];

   $query = "SELECT * from tbljob where JOBID ='$jobID' order by JOBID desc";
   $result = mysqli_query($con, $query);
   $row = mysqli_fetch_array($result);
   $JOBTITLE = $row['JOBTITLE']  ?? '';

   $userID = $_POST['userID'];
   $jobapplicationid = $_POST['jobapplicationid'];
   $TYPE = "Job Application";
   $STATUS = "Unread";
   $NOTE = "Job Application for (". $JOBTITLE. ") status has changed to ". $applicationstatus;

   $query = "UPDATE tbljobapplication SET APPLICATIONSTATUS = '$applicationstatus' WHERE ID = '$jobapplicationid' and JOBID = '$jobID'";
   $result = mysqli_query($con, $query);

   $query = "INSERT INTO tblnotification (USERID, TYPE, TYPEID, STATUS, DATETIME, NOTE) VALUES ('$userID', '$TYPE', '$jobapplicationid', '$STATUS', now(), '$NOTE')" or die(mysqli_error($con));

   $result = mysqli_query($con, $query);

   if (($result)) {

      echo "<script>alert('Application $applicationstatus!')</script>";

   } else {
      $msg = "<div style='color:red'>Error occured...!</div>";
      echo mysqli_error($con);
   }
}

//////////////////////////////////////////

if (isset($_POST['applicationapprove'])) {

   $applicationstatus = 'Approved';
   $jobID = $_POST['jobID'];

   $query = "SELECT * from tbljob where JOBID ='$jobID' order by JOBID desc";
   $result = mysqli_query($con, $query);
   $row = mysqli_fetch_array($result);
   $JOBTITLE = $row['JOBTITLE']  ?? '';

   $userID = $_POST['userID'];
   $jobapplicationid = $_POST['jobapplicationid'];
   $TYPE = "Job Application";
   $STATUS = "Unread";
   $NOTE = "Job Application for (" . $JOBTITLE . ") status has changed to " . $applicationstatus;

   $query = "UPDATE tbljobapplication SET APPLICATIONSTATUS = '$applicationstatus' WHERE ID = '$jobapplicationid' and JOBID = '$jobID'";
   $result = mysqli_query($con, $query);

   $query = "INSERT INTO tblnotification (USERID, TYPE, TYPEID, STATUS, DATETIME, NOTE) VALUES ('$userID', '$TYPE', '$jobapplicationid', '$STATUS', now(), '$NOTE')" or die(mysqli_error($con));

   $result = mysqli_query($con, $query);

   if (($result)) {

      echo "<script>alert('Application $applicationstatus!')</script>";
   } else {
      $msg = "<div style='color:red'>Error occured...!</div>";
      echo mysqli_error($con);
   }
}
///////////////////End Change Application Status///////////////





/////////////////// Send Message  ///////////////
if (isset($_POST['send_msg'])) {

   $userID = $_POST['userID'];
   $message = $_POST['message'];


   $STATUS = "Unread";
   
   // $query = "UPDATE tblfeedback SET APPLICATIONSTATUS = '$applicationstatus' WHERE ID = '$jobapplicationid' and JOBID = '$jobID'";
   // $result = mysqli_query($con, $query);

   $query = "INSERT INTO tblfeedback (APPLICANTID, ADMINID, SENTBY, FEEDBACK, DATETIME, STATUS) VALUES ('$userID', '$session_id', '$session_id', '$message', now(), '$STATUS')" or die(mysqli_error($con));
   $result = mysqli_query($con, $query);

   $query = "SELECT * from tblfeedback order by FEEDBACKID desc";
   $result = mysqli_query($con, $query);
   $row = mysqli_fetch_array($result);
   $MSGID = $row['FEEDBACKID']  ?? '';

   $TYPE = "Message";
   $STATUS = "Unread";
   $NOTE = "Message";

   $query = "INSERT INTO tblnotification (USERID, TYPE, TYPEID, STATUS, DATETIME, NOTE) VALUES ('$userID', '$TYPE', '$MSGID', '$STATUS', now(), '$NOTE')" or die(mysqli_error($con));

   $result = mysqli_query($con, $query);

   if (($result)) {

      echo "<script>alert('Message Sent!')</script>";
   } else {
      $msg = "<div style='color:red'>Error occured...!</div>";
      echo mysqli_error($con);
   }
}

///////////////////End Send Message///////////////







/////////////////// Send Note  ///////////////
if (isset($_POST['send_note'])) {

   $userID = $_POST['userID'];
   $notice = $_POST['notice'];
   $jobID = $_POST['jobID'];

   $query = "SELECT * from tbljob where JOBID ='$jobID' order by JOBID desc";
   $result = mysqli_query($con, $query);
   $row = mysqli_fetch_array($result);
   $JOBTITLE = $row['JOBTITLE']  ?? '';

   $TYPE = "Job Application";
   $STATUS = "Unread";
   $NOTE = "Job Application Notice for (" . $JOBTITLE . ") says: ". $notice;

   $query = "INSERT INTO tblnotification (USERID, TYPE, TYPEID, STATUS, DATETIME, NOTE) VALUES ('$userID', '$TYPE', '$jobID', '$STATUS', now(), '$NOTE')" or die(mysqli_error($con));

   $result = mysqli_query($con, $query);

   if (($result)) {

      echo "<script>alert('Note Sent!')</script>";
   } else {
      $msg = "<div style='color:red'>Error occured...!</div>";
      echo mysqli_error($con);
   }
}

///////////////////End Send Note///////////////