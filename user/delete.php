<?php
require_once("../auth.php");
require_once("../config/db.php");
require_once("../service/user.php");
session_start();

$user_sess = $_SESSION["user"];
$id = $_GET["id"];
if(isset($id) && !empty($id)){
	if($user_sess["is_teacher"] !== 1){
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
$userService = new UserService($conn);
$userService->delete($id);

die(header("Location: ../class/members.php"));
?>
