<?php
	include '../../mysqli_connect.php';
	include 'functions.php';
	session_start();

	if(!isset($_SESSION["userID"]) || (trim ($_SESSION["userID"] == ""))) {
		header("location:../../login.php?a=login");
		exit();
	}
	if(isset($_SESSION["userID"])) {
		if(isLoginSessionExpired()) {
			header("Location:logout.php?session_expired=1");
		}
	}


	$session_id = $_SESSION["userID"];


	if (($_SESSION["role"] != 'Employer')) {
			header("Location:logout.php?session_expired=1");
    	die("Access denied");
	}
	