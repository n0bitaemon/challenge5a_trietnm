<?php
function genFileName($path){
	$file = pathinfo($path);
	$filename = htmlspecialchars($file["filename"], ENT_QUOTES, "UTF-8");
	$extension = $file["extension"];
	$dirname = $file["dirname"];
	$curTime = microtime(true) * 10000;

	$path = $dirname."/".$curTime."_".base64_encode($filename).".".$extension;
	return $path;
}

function getFileName($path){
	$file = pathinfo($path);
	$filename = $file["filename"];

	//Get substr from _ until the end
	return base64_decode(substr($filename, strpos($filename, "_") + 1));
}

function isQuizNameValid($filename){
	return preg_match('/^[a-z_]+$/', $filename);
}

function returnErrorPage($errCode){
	switch($errCode){
		case 400:
			die(require_once("../error/400.php"));
			break;
		case 401: 
			die(require_once("../error/401.php"));
			break;
		case 404: 
			die(require_once("../error/404.php"));
			break;
		case 405: 
			die(require_once("../error/405.php"));
			break;
		case 409:
			die(require_once("../error/409.php"));
			break;
		case 500: 
			die(require_once("../error/500.php"));
			break;
		default:
			die(require_once("../error/unexpected.php"));
	}
}
?>
