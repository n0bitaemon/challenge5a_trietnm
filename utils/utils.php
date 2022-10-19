<?php
function gen_filename($path){
	$file = pathinfo($path);
	$filename = $file["filename"];
	$extension = $file["extension"];
	$dirname = $file["dirname"];

	$tmp_filename = $filename;
	$path = $dirname."/".$tmp_filename.".".$extension;
	while(file_exists($path)){
		$tmp_filename = $filename . "($i)";
		$path = $dirname."/".$tmp_filename.".".$extension;
		$i = $i + 1;
	}
	return $path;
}
?>
