<?php require 'include/phpcode.php'; ?>
<?php

if (!empty($_POST["companyid"])) {
   $cid = intval($_POST['companyid']);
   if (!is_numeric($cid)) {

      echo htmlentities("invalid company");
      exit;
   } else {

      $query = "SELECT * from tblcompany WHERE COMPANYID = '$cid'";
      $result = mysqli_query($con, $query);
      $row = mysqli_fetch_array($result);
      ?>
<input type="text" class="form-control rounded" required name="company_location" placeholder="Location"
    value="<?php echo $row['COMPANYADDRESS'] . ' ' . $row['COMPANYCITY'] . ', ' . $row['COMPANYCOUNTRY'] ?> ">
<?php
   }
}                      
?>