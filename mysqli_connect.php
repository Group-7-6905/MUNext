

<?php
//mysql_select_db('SchoolPortal',  mysql_connect('localhost','root',''))or die(mysql_error());
// $servername = "localhost";
// $username = "u269972180_fundsgrowths";
// $password = "Julietandrews120@gmail.com";
// $dbname = "u269972180_fundsgrowths";


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "erisdb";

// $servername = "localhost";
// $username = "portygrc_portico";
// $password = "mZMURW7kUHny";
// $dbname = "portygrc_portico";

// Create connection
$con = mysqli_connect($servername, $username, $password,$dbname);
mysqli_set_charset($con, 'utf8');

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error($con));
}


?>

