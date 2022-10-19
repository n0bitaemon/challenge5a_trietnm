<?php
session_start();
if(session_destroy()){
	die(header("Location: login.php"));
}
?>
