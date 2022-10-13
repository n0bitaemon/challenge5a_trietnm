<?php
define("HOST", "localhost");
define("USERNAME", "n0bita");
define("PASSWORD", "trietsuper");
define("DATABASE", "classroom");

function connect(){
	$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DATABASE);
	if(!isset($conn)){
		die(header("Location: error/500.php"));
	}
	return $conn;
}

function get_single_result($query){
	$conn = connect();
	$result = mysqli_query($conn, $query);
	if(!isset($result)){
		die(header("Location: error/500.php"));
	}
	$result_render = mysqli_fetch_array($result);
	if(!isset($result_render)){
		return -1;
	}
	return $result_render;
}
?>
