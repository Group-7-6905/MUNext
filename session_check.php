<?php
session_start();
$session_id='';
if (isset($_SESSION["userID"]) and (trim($_SESSION["userID"] != ""))) {
    $session_id = $_SESSION["userID"];
}