<?php

$query = mysqli_query($con, "SELECT * FROM tblusers WHERE USERID = '$session_id'") or die(mysqli_error($con));
$row = mysqli_fetch_array($query);

$USERID = isset($row['USERID']) ? $row['USERID'] : '';
$FNAME = isset($row['FNAME']) ? $row['FNAME'] : '';
$ONAME = isset($row['ONAME']) ? $row['ONAME'] : '';
$EMAIL = isset($row['EMAIL']) ? $row['EMAIL'] : '';
$USERNAME = isset($row['USERNAME']) ? $row['USERNAME'] : '';
$PASS = isset($row['PASS']) ? $row['PASS'] : '';
$ROLE = isset($row['ROLE']) ? $row['ROLE'] : '';
// $PICLOCATION = isset($row['PICLOCATION']) ? $row['PICLOCATION'] : '';


$queryuser = "SELECT * from tblapplicants WHERE USERID = '$USERID'";
$resultuser = mysqli_query($con, $queryuser);
$rowuser = mysqli_fetch_array($resultuser);

$APPLICANTID = isset($rowuser['APPLICANTID']) ? $rowuser['APPLICANTID'] : '';
$FNAME = isset($rowuser['FNAME']) ? $rowuser['FNAME'] : '';
$ONAME = isset($rowuser['OTHERNAMES']) ? $rowuser['OTHERNAMES'] : '';
$APPLICANTPHOTO = isset($rowuser['APPLICANTPHOTO']) ? $rowuser['APPLICANTPHOTO'] : '';
$JOBCATEGORYID = isset($rowuser['JOBCATEGORYID']) ? $rowuser['JOBCATEGORYID'] : '';
$JOBTITLE = isset($rowuser['JOBTITLE']) ? $rowuser['JOBTITLE'] : '';
$EXCOMPANYNAME = isset($rowuser['EXCOMPANYNAME']) ? $rowuser['EXCOMPANYNAME'] : '';
$EXJOBTITLE = isset($rowuser['EXJOBTITLE']) ? $rowuser['EXJOBTITLE'] : '';
$ABOUTME = isset($rowuser['ABOUTME']) ? $rowuser['ABOUTME'] : '';
$ADDRESS = isset($rowuser['FULLADDRESS']) ? $rowuser['FULLADDRESS'] : '';
$COUNTRY = isset($rowuser['COUNTRY']) ? $rowuser['COUNTRY'] : '';
$CITY = isset($rowuser['CITY']) ? $rowuser['CITY'] : '';
$SEX = isset($rowuser['SEX']) ? $rowuser['SEX'] : '';
$BIRTHDATE = isset($rowuser['BIRTHDATE']) ? $rowuser['BIRTHDATE'] : '';
$CONTACTNO = isset($rowuser['CONTACTNO']) ? $rowuser['CONTACTNO'] : '';
$DEGREE = isset($rowuser['DEGREE']) ? $rowuser['DEGREE'] : '';
$SCHOOLNAME = isset($rowuser['SCHOOLNAME']) ? $rowuser['SCHOOLNAME'] : '';
$SKILLS = isset($rowuser['SKILLS']) ? $rowuser['SKILLS'] : '';
$FB_link = isset($rowuser['FB_link']) ? $rowuser['FB_link'] : '';
$LinkedIn_link = isset($rowuser['LinkedIn_link']) ? $rowuser['LinkedIn_link'] : '';
// $CONTACTNO = isset($rowuser['CONTACTNO']) ? $rowuser['CONTACTNO'] : '';
$FULLNAME = $FNAME . ' ' . $ONAME;

$queryuser = "SELECT * from tbljobsubcategory WHERE ID = '$JOBCATEGORYID'";
$resultuser = mysqli_query($con, $queryuser);
$rowuser = mysqli_fetch_array($resultuser);

$SUBCATEGORY = isset($rowuser['SUBCATEGORY']) ? $rowuser['SUBCATEGORY'] : '';

// ...existing code...










/////////////////// login //////////////////////////////

$Lerror = '';
$error = '';
$errorActv = '';

$chk = "";
if (!empty($_GET['chk'])) {
    $chk = $_GET['chk'];
}

if (isset($_POST['login_btn'])) {
    $username = validate_input_text($_POST['username']);
    if (empty($username)) {
        $Lerror = "You forgot to enter your Username";
         echo "<script>
      window.onload = function() {
        const toast = document.getElementById('toast');
        toast.className = 'show error';
        toast.textContent = " . json_encode($Lerror) . ";
        setTimeout(() => { toast.className = toast.className.replace('show', ''); }, 3000);
      };
    </script>";
    }

    $password = validate_input_text($_POST['password']);
    if (empty($password)) {
        $Lerror = "You forgot to enter your password";
         echo "<script>
      window.onload = function() {
        const toast = document.getElementById('toast');
        toast.className = 'show error';
        toast.textContent = " . json_encode($Lerror) . ";
        setTimeout(() => { toast.className = toast.className.replace('show', ''); }, 3000);
      };
    </script>";
    }






    if (empty($Lerror)) {
        // sql query

        $query = mysqli_query($con, "SELECT * FROM tblusers WHERE USERNAME = '$username'") or die(mysqli_error($con));
        $count = mysqli_num_rows($query);
        $row = mysqli_fetch_array($query);


        if ($count > 0) {
            session_unset();
            // verify password
            if (password_verify($password, $row['PASS'])) {



                
                    $_SESSION['userID'] = $row['USERID'];
                    $_SESSION['loggedin_time'] = time();

                     $message = "Login Successful!";
                // echo "<script>alert('Incorrect Username or Password!')</script>";
                 echo "<script>
                  window.onload = function() {
                    const toast = document.getElementById('toast');
                    toast.className = 'show success';
                    toast.textContent = " . json_encode($message) . ";
                    setTimeout(() => { toast.className = toast.className.replace('show', ''); }, 3000);
                  };
                </script>";
                    
                if ($row['ROLE'] == "Administrator") {
                              // header("location: ./admin/");
                              echo "<script>
                          setTimeout(function() {
                            window.location.href = './admin/';
                          }, 4000);
                        </script>";
             
                        exit();
                } else {
                  
                    if (!empty($_POST['url'])) {
                        $url = $_POST['url'];
                        // header("location: ./$url");
                        echo "<script>
                        setTimeout(function() {
                          window.location.href = './$url';
                        }, 3000);
                      </script>";
                    } else {
                                // header("location: ./dashboard/");
                                echo "<script>
                        setTimeout(function() {
                          window.location.href = './dashboard/';
                        }, 3000);
                      </script>";
                    }

                    exit();
                }
            } else {
                $Lerror = "<div style='color:red'>Incorrect Username or Password!</div>";
                $error = "Incorrect Username or Password!";
                // echo "<script>alert('Incorrect Username or Password!')</script>";
                 echo "<script>
      window.onload = function() {
        const toast = document.getElementById('toast');
        toast.className = 'show error';
        toast.textContent = " . json_encode($error) . ";
        setTimeout(() => { toast.className = toast.className.replace('show', ''); }, 3000);
      };
    </script>";

            }
        } else {
            $Lerror = "<div style='color:red'>Username does not exist!</div>";
            $error = "Username does not exist!";
            // echo "<script>alert('Username does not exist!')</script>";
             echo "<script>
      window.onload = function() {
        const toast = document.getElementById('toast');
        toast.className = 'show error';
        toast.textContent = " . json_encode($error) . ";
        setTimeout(() => { toast.className = toast.className.replace('show', ''); }, 3000);
      };
    </script>";

        }
    } else {
        $Lerror = "<div style='color:red'>Please Fill out Username and password to login!</div>";
        $error = "Please Fill out Username and password to login!";
        
         echo "<script>
      window.onload = function() {
        const toast = document.getElementById('toast');
        toast.className = 'show error';
        toast.textContent = " . json_encode($error) . ";
        setTimeout(() => { toast.className = toast.className.replace('show', ''); }, 3000);
      };
    </script>";
    }
}
/////////////////// login Ends//////////////////////////////






/////////////////// Sign Up //////////////////////////////


if (isset($_POST['register_btn'])) {
    # code...

    $role = validate_input_text($_POST['role']);
    

    $firstName = validate_input_text($_POST['Fname']);
    if (empty($firstName)) {
        $error = "You forgot to enter your first Name";
        $ferror = "You forgot to enter your first Name";
         echo "<script>
      window.onload = function() {
        const toast = document.getElementById('toast');
        toast.className = 'show error';
        toast.textContent = " . json_encode($error) . ";
        setTimeout(() => { toast.className = toast.className.replace('show', ''); }, 3000);
      };
    </script>";
    }

    $lastName = validate_input_text($_POST['Lname']);
    if (empty($lastName)) {
        $error = "You forgot to enter your last Name";
        $lerror = "You forgot to enter your last Name";
         echo "<script>
      window.onload = function() {
        const toast = document.getElementById('toast');
        toast.className = 'show error';
        toast.textContent = " . json_encode($error) . ";
        setTimeout(() => { toast.className = toast.className.replace('show', ''); }, 3000);
      };
    </script>";
    }

    $username = validate_input_text($_POST['username']);
    if (empty($username)) {
        $error = "You forgot to enter your Username";
        $lerror = "You forgot to enter your Username";
         echo "<script>
      window.onload = function() {
        const toast = document.getElementById('toast');
        toast.className = 'show error';
        toast.textContent = " . json_encode($error) . ";
        setTimeout(() => { toast.className = toast.className.replace('show', ''); }, 3000);
      };
    </script>";
    }

    $email = validate_input_email($_POST['email']);
    if (empty($email)) {
        $error = "You forgot to enter your Email";
        $eerror = "You forgot to enter your Email";
         echo "<script>
      window.onload = function() {
        const toast = document.getElementById('toast');
        toast.className = 'show error';
        toast.textContent = " . json_encode($error) . ";
        setTimeout(() => { toast.className = toast.className.replace('show', ''); }, 3000);
      };
    </script>";
    }


    $password = validate_input_text($_POST['password']);
    if (empty($password)) {
        $error = "You forgot to enter your password";
        $perror = "You forgot to enter your password";
        echo "<script>
      window.onload = function() {
        const toast = document.getElementById('toast');
        toast.className = 'show error';
        toast.textContent = " . json_encode($error) . ";
        setTimeout(() => { toast.className = toast.className.replace('show', ''); }, 3000);
      };
    </script>";
    }

    $confirm_pwd = validate_input_text($_POST['cpassword']);
    if (empty($confirm_pwd)) {
        $error = "You forgot to enter your Confirm Password";
        echo "<script>
      window.onload = function() {
        const toast = document.getElementById('toast');
        toast.className = 'show error';
        toast.textContent = " . json_encode($error) . ";
        setTimeout(() => { toast.className = toast.className.replace('show', ''); }, 3000);
      };
    </script>";
    }
    if ($password != $confirm_pwd) {
        $error = "Retype Confirm Password";
        echo "<script>
      window.onload = function() {
        const toast = document.getElementById('toast');
        toast.className = 'show error';
        toast.textContent = " . json_encode($error) . ";
        setTimeout(() => { toast.className = toast.className.replace('show', ''); }, 3000);
      };
    </script>";
        goto a;
    }


    if (empty($error)) {

        // register a new user
        $hashed_pass = password_hash($password, PASSWORD_DEFAULT);


        $query = "SELECT * from tblusers" or die(mysqli_error($con));
        $run = mysqli_query($con, $query);
        while ($row = mysqli_fetch_array($run)) {

            $demail = $row['EMAIL'];
            $dusername = $row['USERNAME'];
            if ($demail == $email) {
                # code...
                // echo '<script>alert("E-mail Already existing")</script>';
                
                $message = "E-mail provided is already existing";
                $error = "<div style='color:red'>E-mail provided is already existing</div>";
            
                echo "<script>
      window.onload = function() {
        const toast = document.getElementById('toast');
        toast.className = 'show error';
        toast.textContent = " . json_encode($message) . ";
        setTimeout(() => { toast.className = toast.className.replace('show', ''); }, 3000);
      };
    </script>";
              

                goto a;
            }
            if ($dusername == $username) {
                # code...
                // echo '<script>alert("Username Already existing")</script>';

                $message = "Username provided is already existing";
                $error = "<div style='color:red'>Username provided is already existing</div>";

                      echo "<script>
      window.onload = function() {
        const toast = document.getElementById('toast');
        toast.className = 'show error';
        toast.textContent = " . json_encode($message) . ";
        setTimeout(() => { toast.className = toast.className.replace('show', ''); }, 3000);
      };
    </script>";

                goto a;
            }
        }


        $AUTOKEY = $role; //userid //employeeid //APPLICANT //FILEID

        $queryrand = mysqli_query($con, "SELECT * FROM tblautonumbers WHERE AUTOKEY = '$AUTOKEY'") or die(mysqli_error($con));
        $rowrand = mysqli_fetch_array($queryrand);

        $USERID = substr(rand($rowrand['AUTOSTART'], $rowrand['AUTOEND']), 0, 6);
        // $status = "Unverified";
        $FULLNAME = $firstName . ' ' . $lastName;

        if (!empty($_POST['admin'])) {
            $admin = validate_input_text($_POST['admin']);
            $ROLE = $admin;
        } else {
            $ROLE = $role; //Administrator //Employee //Applicant
        }

        $query = "INSERT into tblusers (IDN, FNAME, ONAME, EMAIL, USERNAME, PASS, ROLE) values ('$USERID', '$firstName', '$lastName', '$email', '$username', '$hashed_pass', '$ROLE')" or die(mysqli_error($con));
        $result = mysqli_query($con, $query);

        if ($result) {

            // start a new session if not already started
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            // create session variable
            $_SESSION['userID'] = mysqli_insert_id($con);
            $_SESSION['loggedin_time'] = time();

            // echo "<script>alert('Account Created!')</script>";

            // header('location: ./login.php?chk=successful');
            if ($ROLE == "Applicant") {
                              // header("location: ./admin/");
                              echo "<script>
                          setTimeout(function() {
                            window.location.href = './dashboard/';
                          }, 3000);
                        </script>";
             
                        // exit();
            } else {
                                // header("location: ./dashboard/");
                                echo "<script>
                        setTimeout(function() {
                          window.location.href = './dashboard/create-profile.php';
                        }, 3000);
                      </script>";
            }
            
            $message = "Account Created Successfully!";
                      // echo "<script>alert('Incorrect Username or Password!')</script>";
                      echo "<script>
            window.onload = function() {
              const toast = document.getElementById('toast');
              toast.className = 'show success';
              toast.textContent = " . json_encode($message) . ";
              setTimeout(() => { toast.className = toast.className.replace('show', ''); }, 3000);
            };
          </script>";

          // exit();


        } else {
            $error = "<div style='color:red'>Error while registration...!</div>";
            $message = "Error while registration...!";

                 echo "<script>
      window.onload = function() {
        const toast = document.getElementById('toast');
        toast.className = 'show error';
        toast.textContent = " . json_encode($message) . ";
        setTimeout(() => { toast.className = toast.className.replace('show', ''); }, 3000);
      };
    </script>";

            echo mysqli_error($con);
        }
    } else {
        //$error =  "<div style='color:red'>Not validate!</div>";
    }

    a:
}

/////////////////// Sign Up Ends/////////////////////////////







////////////////////Apply Job//////////////////////////////

if (isset($_POST['apply_job'])) {
    # code...

    $JOBID = validate_input_text($_POST['JOBID']);
    $APPLICANTID = validate_input_text($_POST['APPLICANTID']);

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


        $query = "INSERT into tbljobapplication (APPLICANTID, JOBID, RESUME_FILE, APPLICATIONSTATUS, APPLICATIONDATE) values ('$APPLICANTID', '$JOBID', '$fileToUpload_url', '$status', now())" or die(mysqli_error($con));
        $result = mysqli_query($con, $query);

        $queryjobapp = "SELECT * from tbljobapplication order by id desc";
        $resultjobapp = mysqli_query($con, $queryjobapp);
        $rowjobapp = mysqli_fetch_array($resultjobapp);
        $jobapp_id = $rowjobapp['ID'];


        if ((move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $target)) && ($result)) {

            $score = 0;
            $overall = 0;

            for ($i = 0; $i < sizeof($ques); $i++) {
               

                // if ($ans[$ques[$i]] == 1) {
                //     $ANSWER = 'A';
                // } elseif ($ans[$ques[$i]] == 2) {
                //     $ANSWER = 'B';
                // } elseif ($ans[$ques[$i]] == 3) {
                //     $ANSWER = 'C';
                // } elseif ($ans[$ques[$i]] == 4) {
                //     $ANSWER = 'D';
                // } else {
                //     $ANSWER = 'E';
                // }
                $ANSWER = $ans[$ques[$i]];

                // echo $ques[$i] . '. ' . $ANSWER . ' ' . $ans[$ques[$i]] . '<br>';
                

                $QUESTION_ID = $ques[$i];

                $queryques = "SELECT * from tblscreening WHERE id = '$QUESTION_ID'";
                $resultques = mysqli_query($con, $queryques);
                $rowques = mysqli_fetch_array($resultques);

                // $QUESTION = $rowques['question'];
                // $IDEAL_ANSWER = $rowques['ideal_ans'];

                $query = "INSERT into tblscreening_qa (JOBAPPLICATION_ID, APPLICANTID, JOBID, QUESTION_ID, APPLICANT_ANSWER) values ('$jobapp_id', '$APPLICANTID', '$JOBID', '$QUESTION_ID', '$ANSWER')" or die(mysqli_error($con));
                $results = mysqli_query($con, $query);


                ///Calculating Score//////
                $query_answer = "SELECT * from tblscreening_answer WHERE question_id = '$QUESTION_ID'";
                $result_answer = mysqli_query($con, $query_answer);
                $row_answer = mysqli_fetch_array($result_answer);
                $ideal_ans_opt = $row_answer['ideal_ans_opt'];


                if ($ANSWER == $ideal_ans_opt) {
                    $score++;
                }
                $overall++;
            }
            if ($results) {

                $query = "INSERT into tblscreening_score (jobapp_id, score, total_ques) values ('$jobapp_id', '$score', '$overall')" or die(mysqli_error($con));
                $resultss = mysqli_query($con, $query);

                $query = "UPDATE tbljobapplication SET SCORE = '$score' WHERE ID = '$jobapp_id'";
                $result = mysqli_query($con, $query);

                if ($resultss) {
?>
<script>
alert('Job Applied!');
location.href = "./dashboard/dashboard-applied-jobs.php";
</script>

<?php
                } else {
                ?>
<script>
alert('Error occured at Score insertion...!');
</script>

<?php
                    echo mysqli_error($con);
                }
            } else {
                ?>
<script>
alert('Error occured at Screening insertion...!');
</script>

<?php
            }
        } else {
            $msg = "<div style='color:red'>Error occured...!</div>";
            echo mysqli_error($con);
            ?>
<script>
alert('Error occured at Job Insertion...!');
</script>

<?php

        }
    }
}

/////////////////////End Apply Jobs///////////////////////




////////////////////////Save/Bookmark Job..../////////////////

if (isset($_POST['save_job'])) {

    $jobID = $_POST['jobID'];
    $userID = $_POST['userID'];



    $query = "INSERT INTO tblbookmarkjob (APPLICANTID, JOBID, DATETIME) VALUES ('$userID','$jobID', now())" or die(mysqli_error($con));

    $result = mysqli_query($con, $query);


    if ($result) {

        ?>
<script>
alert('Job Saved!');
// location.href = "./dashboard-applied-jobs.php";
</script>
<?php

        //   header('location: ./dashboard-add-profile.php#section456');
    } else {
        $msg2 = "<div style='color:red'>Error occured...!</div>";
        echo mysqli_error($con);
    }
}
////////////////////////Save/Bookmark Job..../////////////////






















function validate_input_text($textValue)
{
    if (!empty($textValue)) {
        $trim_text = trim($textValue);
        // remove illegal character
        $sanitize_str = filter_var($trim_text, FILTER_SANITIZE_STRING);
        return $sanitize_str;
    }
    return '';
}

function validate_input_email($emailValue)
{
    if (!empty($emailValue)) {
        $trim_text = trim($emailValue);
        // remove illegal character
        $sanitize_str = filter_var($trim_text, FILTER_SANITIZE_EMAIL);
        return $sanitize_str;
    }
    return '';
}

// profile image
function upload_profile($path, $file)
{
    $targetDir = $path;
    $default = "beard.png";

    // get the filename
    $filename = basename($file['name']);
    $targetFilePath = $targetDir . $filename;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    if (!empty($filename)) {
        // allow certain file format
        $allowType = array('jpg', 'png', 'jpeg', 'gif', 'pdf');
        if (in_array($fileType, $allowType)) {
            // upload file to the server
            if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
                return $targetFilePath;
            }
        }
    }

    // return default image
    return $path . $default;
}


// get user info
function get_user_info($con, $userID)
{
    $query = "SELECT firstName, lastName, email, profileImage FROM user WHERE userID=?";
    $q = mysqli_stmt_init($con);

    mysqli_stmt_prepare($q, $query);

    // bind the statement
    mysqli_stmt_bind_param($q, 'i', $userID);

    // execute sql statement
    mysqli_stmt_execute($q);
    $result = mysqli_stmt_get_result($q);

    $row = mysqli_fetch_array($result);
    return empty($row) ? false : $row;
}

?>