<?php
require_once("../auth.php");
require_once("../utils/const.php");

$file = $_GET["file"];
if(isset($file)){
    if(!empty($file)){
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=output.txt");
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: binary");
        
        readfile(FILE_EX_PATH.$file);
        die();
    }else{
        returnErrorPage(404);
    }
}else{
    returnErrorPage(400);
}
?>