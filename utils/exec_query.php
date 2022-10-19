<?php
define("HOST", "localhost");
define("USERNAME", "n0bita");
define("PASSWORD", "trietsuper");
define("DATABASE", "classroom");

function connect(){
	$mysqli = new mysqli(HOST, USERNAME, PASSWORD, DATABASE);
	if($mysqli->connect_errno){
		die(require_once("../error/500.php"));
	}
	return $mysqli;
}

function execute($query){
	$mysqli = connect();
	if(!$mysqli->query($query)){
		die(require_once("../error/500.php"));
	}
}

function get_multiple_results($query){
	$mysqli = connect();
	if(!$result = $mysqli->query($query)){
		die(require_once("../error/500.php"));
	}
	$result_render = $result->fetch_all(MYSQLI_ASSOC);
	$result->free_result();
	if(!$result_render){
		return -1;
	}
	return $result_render;
}

function get_single_result($query){
	$mysqli = connect();
	if(!$result = $mysqli->query($query)){
		die(require_once("../error/500.php"));
	}

	$result_render = $result->fetch_assoc();
	$result->free_result();
	if(!$result_render){
		return -1;
	}
	return $result_render;
}
?>
