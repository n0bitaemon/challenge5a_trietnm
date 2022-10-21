<?php
require_once("../utils/exec_query.php");
require_once("../config/db.php");
require_once("../service/message.php");
session_start();
$user_sess = $_SESSION["user"];

$method = $_SERVER["REQUEST_METHOD"];

if($method == "GET"){
	//Get from_id from session and to_id from url
	$from_id = $user_sess["id"];
	$to_id = $_GET["id"];

	$db = new db();
	$conn = $db->connect();
	$msgService = new MessageService($conn);
	$msgList = $msgService->getMessagesToUser($from_id, $to_id);

	return $msgList;
}

if($method == "POST"){

}

if($method == "PUT"){

}

if($method == "DELETE"){

}

?>
