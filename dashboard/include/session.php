<?php
	include '../mysqli_connect.php';
	include 'functions.php';
	session_start();

	if(!isset($_SESSION["userID"]) || (trim ($_SESSION["userID"] == ""))) {
		header("location:../login.php?a=login");
		exit();
	}
	if(isset($_SESSION["id"])) {
		if(isLoginSessionExpired()) {
			header("Location:logout.php?session_expired=1");
		}
	}


	$session_id = $_SESSION["userID"];
