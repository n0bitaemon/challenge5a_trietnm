<?php
session_start();
$userSess = $_SESSION["user"];
if(!$userSess){
	die(header("Location: /login.php"));
}
?>
