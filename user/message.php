<?php
require_once("../utils/exec_query.php");
session_start();
$user_sess = $_SESSION["user"];

$method = $_SERVER["REQUEST_METHOD"];

if($method == "GET"){
	//Get from_id from session and to_id from url
	$from_id = $user_sess["id"];
	$to_id = $_GET["id"];

	$query = "SELECT * FROM message WHERE from_id=$from_id";
	if(isset($to_id)){
		$query = $query." AND to_id=$to_id";
	}
	
	$results = get_multiple_results($query);
	return $results;
}

if($method == "POST"){

}

if($method == "PUT"){

}

if($method == "DELETE"){

}

?>
