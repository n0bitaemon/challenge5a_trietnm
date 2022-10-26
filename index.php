<?php
session_start();
$userSess = $_SESSION["user"];
if(!isset($userSess)){
	die(header("Location: login.php"));
}else{
	die(header("Location: /exercise"));
}
?>