<?php

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




////////////////////Apply Job//////////////////////////////


if (isset($_POST['apply_job'])) {
    # code...

    $JOBID = ($_POST['JOBID']);
    $APPLICANTID = ($_POST['APPLICANTID']);

    // Get image name
    $fileToUpload = $_FILES['fileToUpload']['name'];
    $target = "./resumes_cv/" . basename($fileToUpload);

    $fileToUpload_url = "resumes_cv/" . $fileToUpload;

    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 500000) {
        $msg = "<div style='color:red'>File is too large!</div>";
    } else {

        $status = 'Pending';

      $ques = $_POST['id'];
      $ans = $_POST['ans'];



            $score = 0;
            $overall = 0;

            for ($i = 0; $i < sizeof($ques); $i++) {
                // '" . $ques[$i] . "'
               //  if ($ans[$ques[$i]] == 1) {
               //      $ANSWER = 'A';
               //  }
               //  elseif ($ans[$ques[$i]] == 2) {
               //      $ANSWER = 'B';
               //  }
               //  elseif ($ans[$ques[$i]] == 3) {
               //      $ANSWER = 'C';
               //  }
               //  elseif ($ans[$ques[$i]] == 4) {
               //      $ANSWER = 'D';
               //  }else {
               //      $ANSWER = 'E';
               //  }
                

               //  echo $ques[$i] . '. ' . $ANSWER . ' ' . $ans[$ques[$i]] . '<br>';
                echo  $ANSWER = $ans[$ques[$i]] . '<br>';
               //  echo $ANSWER. '-- ans<br>';
            }
    }
}

/////////////////////End Apply Jobs///////////////////////