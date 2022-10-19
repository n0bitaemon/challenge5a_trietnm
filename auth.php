<?php
session_start();
if(!isset($_SESSION["user"])){
	die("You are not logged in!");
}
?>
