<?php
require_once("../auth.php");
require_once("../config/db.php");
require_once("../service/message.php");
session_start();

$user_sess = $_SESSION["user"];
$id = $_GET["id"];
if(isset($id) && !empty($id)){
	$db = new db();
	$conn = $db->connect();
	$msgService = new MessageService($conn);
	
	$currentMsg = $msgService->getMessageFromId($id);
	if($currentMsg["from_id"] != $user_sess["id"]){
		die("You are not sender, you cannot delete this message");
	}
	
	$msgService->deleteMessage($id);
	die(header("Location: messages.php?to=".$currentMsg["to_id"]));
}else{
	die("No id specified!");
}
?>
