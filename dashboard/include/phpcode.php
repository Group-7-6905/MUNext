<?php
require 'session.php';

$query = mysqli_query($con, "SELECT * FROM tblusers WHERE USERID = '$session_id'") or die(mysqli_error($con));
$row = mysqli_fetch_array($query);

$USERID = $row['USERID'];
$FNAME = $row['FNAME'];
$ONAME = $row['ONAME'];
$EMAIL = $row['EMAIL'];
$USERNAME = $row['USERNAME'];
$PASS = $row['PASS'];
$ROLE = $row['ROLE'];
// $PICLOCATION = $row['PICLOCATION'];


$queryuser = "SELECT * from tblapplicants WHERE USERID = '$USERID'";
$resultuser = mysqli_query($con, $queryuser);
$rowuser = mysqli_fetch_array($resultuser);

$APPLICANTID = $rowuser['APPLICANTID'];
$FNAME = $rowuser['FNAME'];
$ONAME = $rowuser['OTHERNAMES'];
$APPLICANTPHOTO = $rowuser['APPLICANTPHOTO'];
$JOBCATEGORYID = $rowuser['JOBCATEGORYID'];
$JOBTITLE = $rowuser['JOBTITLE'];
$EXCOMPANYNAME = $rowuser['EXCOMPANYNAME'];
$EXJOBTITLE = $rowuser['EXJOBTITLE'];
$ABOUTME = $rowuser['ABOUTME'];
$ADDRESS = $rowuser['FULLADDRESS'];
$COUNTRY = $rowuser['COUNTRY'];
$CITY = $rowuser['CITY'];
$SEX = $rowuser['SEX'];
$BIRTHDATE = $rowuser['BIRTHDATE'];
$CONTACTNO = $rowuser['CONTACTNO'];
$DEGREE = $rowuser['DEGREE'];
$SCHOOLNAME = $rowuser['SCHOOLNAME'];
$SKILLS = $rowuser['SKILLS'];
$FB_link = $rowuser['FB_link'];
$LinkedIn_link = $rowuser['LinkedIn_link'];
// $CONTACTNO = $rowuser['CONTACTNO'];
$FULLNAME = $FNAME . ' ' . $ONAME;

$queryuser = "SELECT * from tbljobsubcategory WHERE ID = '$JOBCATEGORYID'";
$resultuser = mysqli_query($con, $queryuser);
$rowuser = mysqli_fetch_array($resultuser);

$SUBCATEGORY = $rowuser['SUBCATEGORY'];






$msg = '';
$msg2 = '';
$msg3 = '';
/////////////////////////Complete Profile/////////////////////////

////////////section 1/////////////////////////
if (isset($_POST['save_data'])) {

   $job_title = $_POST['job_title'];
   $job_categoryid = $_POST['job_categoryid'];
   $dob = $_POST['dob'];
   $phoneno = $_POST['phoneno'];
   $about_me = $_POST['about_me'];
   $sex = $_POST['sex'];
   

   // Get image name
   $image = $_FILES['image']['name'];
   $target = "../profile/" . basename($image);

   $image_url = "profile/" . $image;

   // Check file size
   if ($_FILES["image"]["size"] > 50000000) {
      $msg = "<div style='color:red'>File is too large!</div>";
   } else {

      $queryuser = "SELECT * from tblusers WHERE USERID = '$session_id'";
      $resultuser = mysqli_query($con, $queryuser);
      $rowuser = mysqli_fetch_array($resultuser);

      $USERID = $rowuser['USERID'];
      $FNAME = $rowuser['FNAME'];
      $ONAME = $rowuser['ONAME'];
      $EMAIL = $rowuser['EMAIL'];
      $USERNAME = $rowuser['USERNAME'];


      $query = "INSERT INTO tblapplicants (JOBCATEGORYID, JOBTITLE, USERID, FNAME, OTHERNAMES, SEX, BIRTHDATE, ABOUTME, USERNAME, EMAILADDRESS, CONTACTNO, APPLICANTPHOTO) VALUES ('$job_categoryid','$job_title', '$USERID', '$FNAME', '$ONAME', '$sex', '$dob', '$about_me', '$USERNAME','$EMAIL','$phoneno', '$image_url')" or die(mysqli_error($con));
      $result = mysqli_query($con, $query);

      if ((move_uploaded_file($_FILES['image']['tmp_name'], $target)) && ($result)) {


         ?><script>
alert('Data Saved!')
</script><?php 

         header('location: ./dashboard-add-profile.php#section23');
      } else {
         $msg = "<div style='color:red'>Error occured...!</div>";
         echo mysqli_error($con);
      }
   }
}
//////////////////Ends Section 1 ///////////////////////////




/////////section 2, 3 ///////////////////

if (isset($_POST['save_info'])) {

   $country = $_POST['country'];
   $city = $_POST['city'];
   $address = $_POST['address'];
   $fb = $_POST['fb'];
   $lin = $_POST['lin'];



   $query = "UPDATE tblapplicants SET FULLADDRESS = '$address', CITY = '$city', COUNTRY = '$country',  FB_link = '$fb', LinkedIn_link = '$lin' WHERE USERID = '$USERID'";

   $result = mysqli_query($con, $query);

   if ($result) {

      ?><script>
alert('Personal Info Saved!')
</script><?php 

      header('location: ./dashboard-add-profile.php#section456');
   } else {
      $msg2 = "<div style='color:red'>Error occured...!</div>";
      echo mysqli_error($con);
   }
   
}
//////////////////////Ends section 2, 3 /////////////////////







/////////section 4, 5, 6 ///////////////////

if (isset($_POST['save_preview'])) {

   $employee_name = '';
   $job_title ='';

   if (!empty($_POST['company_name_select'])) {
      $employee_name = $_POST['company_name_select'];
      if ($employee_name == "Others (Please specify)") {
         $employee_name = $_POST['company_name_specify']; //
      }
   }

   if (!empty($_POST['job_title'])) {
      $job_title = $_POST['job_title'];
   }
   

   


   $schl_name = $_POST['schl_name'];
   $qualification = $_POST['qualification'];

   $skills = $_POST['skills'];

   $query = "UPDATE tblapplicants SET EXCOMPANYNAME = '$employee_name', EXJOBTITLE = '$job_title', DEGREE = '$qualification',  SCHOOLNAME = '$schl_name', SKILLS = '$skills' WHERE USERID = '$USERID'";

   $result = mysqli_query($con, $query);

   if ($result) {

      ?><script>
alert('Profile Completed!')
</script><?php 

      header('location: ./candidate-detail.php');
   } else {
      $msg3 = "<div style='color:red'>Error occured...!</div>";
      echo mysqli_error($con);
   }
   
}
//////////////////////Ends section 4, 5, 6  /////////////////////

/////////////////////////End Complete Profile ends/////////////////////////








/////////////////////////Edit Profile/////////////////////////

////////////section 1/////////////////////////
if (isset($_POST['edit_data'])) {

   $job_title = $_POST['job_title'];
   $job_categoryid = $_POST['job_categoryid'];
   $dob = $_POST['dob'];
   $phoneno = $_POST['phoneno'];
   $about_me = $_POST['about_me'];
   $sex = $_POST['sex'];
   $FName = $_POST['FName'];
   $OName = $_POST['OName'];
   $email = $_POST['email'];
   

   if (!empty($_FILES['image']['name'])) {
       // Get image name
      $image = $_FILES['image']['name'];
      $target = "../profile/" . basename($image);

      $image_url = "profile/" . $image;

      // Check file size
      if ($_FILES["image"]["size"] > 50000000) {
         $msg = "<div style='color:red'>File is too large!</div>";
      } else {

         // $queryuser = "SELECT * from tblusers WHERE USERID = '$session_id'";
         // $resultuser = mysqli_query($con, $queryuser);
         // $rowuser = mysqli_fetch_array($resultuser);

         // $USERID = $rowuser['USERID'];
         // $FNAME = $rowuser['FNAME'];
         // $ONAME = $rowuser['ONAME'];
         // $EMAIL = $rowuser['EMAIL'];
         // $USERNAME = $rowuser['USERNAME'];


         // $query = "INSERT INTO tblapplicants (JOBCATEGORYID, JOBTITLE, USERID, FNAME, OTHERNAMES, SEX, BIRTHDATE, ABOUTME, USERNAME, EMAILADDRESS, CONTACTNO, APPLICANTPHOTO) VALUES ('$job_categoryid','$job_title', '$USERID', '$FNAME', '$ONAME', '$sex', '$dob', '$about_me', '$USERNAME','$EMAIL','$phoneno', '$image_url')" or die(mysqli_error($con));

         $query = "UPDATE tblapplicants SET JOBCATEGORYID = '$job_categoryid', JOBTITLE = '$job_title', FNAME = '$FName',  OTHERNAMES = '$OName', BIRTHDATE = '$dob', SEX = '$sex', ABOUTME = '$about_me', EMAILADDRESS = '$email', CONTACTNO = '$phoneno', APPLICANTPHOTO = '$image_url'  WHERE USERID = '$USERID'";
         
         $result = mysqli_query($con, $query);
         

         if ((move_uploaded_file($_FILES['image']['tmp_name'], $target)) && ($result)) {


            ?><script>
alert('Data Saved!')
</script><?php 

            header('location: ./dashboard-my-profile.php');
         } else {
            $msg = "<div style='color:red'>Error occured...!</div> ".mysqli_error($con);
            echo mysqli_error($con);
         }
      }
   }else {
      $query = "UPDATE tblapplicants SET JOBCATEGORYID = '$job_categoryid', JOBTITLE = '$job_title', FNAME = '$FName',  OTHERNAMES = '$OName', BIRTHDATE = '$dob', SEX = '$sex', ABOUTME = '$about_me', EMAILADDRESS = '$email', CONTACTNO = '$phoneno' WHERE USERID = '$USERID'";

       $result = mysqli_query($con, $query);

         if (($result)) {


            ?><script>
alert('Data Saved!')
</script><?php 

            header('location: ./dashboard-my-profile.php');
         } else {
            $msg = "<div style='color:red'>Error occured...!</div> ".mysqli_error($con);
            echo mysqli_error($con);
         }
   }
  
}
//////////////////Ends Section 1 ///////////////////////////




/////////section 2, 3 ///////////////////

if (isset($_POST['edit_info'])) {

   $country = $_POST['country'];
   $city = $_POST['city'];
   $address = $_POST['address'];
   $fb = $_POST['fb'];
   $lin = $_POST['lin'];



   $query = "UPDATE tblapplicants SET FULLADDRESS = '$address', CITY = '$city', COUNTRY = '$country',  FB_link = '$fb', LinkedIn_link = '$lin' WHERE USERID = '$USERID'";

   $result = mysqli_query($con, $query);

   if ($result) {

      ?><script>
alert('Personal Info Saved!')
</script><?php 

      header('location: ./dashboard-my-profile.php#section23');
   } else {
      $msg2 = "<div style='color:red'>Error occured...!</div>";
      echo mysqli_error($con);
   }
   
}
//////////////////////Ends section 2, 3 /////////////////////







/////////section 4, 5, 6 ///////////////////

if (isset($_POST['edit_preview'])) {

   $employee_name = '';
   $job_title ='';

   if (!empty($_POST['company_name_select'])) {
      $employee_name = $_POST['company_name_select'];
      if ($employee_name == "Others (Please specify)") {
         $employee_name = $_POST['company_name_specify']; //
      }
   }

   if (!empty($_POST['job_title'])) {
      $job_title = $_POST['job_title'];
   }
   

   


   $schl_name = $_POST['schl_name'];
   $qualification = $_POST['qualification'];

   $skills = $_POST['skills'];

   $query = "UPDATE tblapplicants SET EXCOMPANYNAME = '$employee_name', EXJOBTITLE = '$job_title', DEGREE = '$qualification',  SCHOOLNAME = '$schl_name', SKILLS = '$skills' WHERE USERID = '$USERID'";

   $result = mysqli_query($con, $query);

   if ($result) {

      ?><script>
alert('Profile Completed!')
</script><?php 

      header('location: ./dashboard-my-profile.php#section456');
   } else {
      $msg3 = "<div style='color:red'>Error occured...!</div>";
      echo mysqli_error($con);
   }
   
}
//////////////////////Ends section 4, 5, 6  /////////////////////

/////////////////////////End Complete Profile ends/////////////////////////







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






/////////////////// Send Message  ///////////////
if (isset($_POST['send_msg'])) {

   $adminid = $_POST['adminid'];
   $message = $_POST['message'];


   $STATUS = "Unread";

   // $query = "UPDATE tblfeedback SET APPLICATIONSTATUS = '$applicationstatus' WHERE ID = '$jobapplicationid' and JOBID = '$jobID'";
   // $result = mysqli_query($con, $query);

   $query = "INSERT INTO tblfeedback (APPLICANTID, ADMINID, SENTBY, FEEDBACK, DATETIME, STATUS) VALUES ('$session_id', '$adminid', '$session_id', '$message', now(), '$STATUS')" or die(mysqli_error($con));
   $result = mysqli_query($con, $query);

   $query = "SELECT * from tblfeedback order by FEEDBACKID desc";
   $result = mysqli_query($con, $query);
   $row = mysqli_fetch_array($result);
   $MSGID = $row['FEEDBACKID'];

   $TYPE = "Message";
   $STATUS = "Unread";
   $NOTE = "Message";

   $query = "INSERT INTO tblnotification (USERID, TYPE, TYPEID, STATUS, DATETIME, NOTE) VALUES ('$session_id', '$TYPE', '$MSGID', '$STATUS', now(), '$NOTE')" or die(mysqli_error($con));

   $result = mysqli_query($con, $query);

   if (($result)) {

      // echo "<script>alert('Message Sent!')</script>";
   } else {
      $msg = "<div style='color:red'>Error occured...!</div>";
      echo mysqli_error($con);
   }
}

///////////////////End Send Message///////////////
?>