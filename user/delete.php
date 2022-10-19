<?php
require_once("../auth.php");
require_once("../utils/exec_query.php");
require_once("../config/db.php");
require_once("../model/user.php");
session_start();

$user_sess = $_SESSION["user"];
$id = $_GET["id"];
if(isset($id)){
	if($user_sess["is_teacher"] !== "1"){
		die("You are not teacher");
	}else{
		if($id === $user_sess["id"]){
			die("You can not delete your own account");
		}
	}
}else{
	die("No id specified");
}

$db = new db();
$conn = $db->connect();
$user = new user($conn);
$user->id = $id;
$user->delete();

die(header("Location: ../class/members.php"));
?>
