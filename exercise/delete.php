<?php
require_once("../auth.php");
require_once("../utils/db.php");
require_once("../service/exercise.php");
require_once("../utils/const.php");
require_once("../utils/utils.php");

if($_SERVER["REQUEST_METHOD"] != "GET"){
    returnErrorPage(405);
}

$exId = $_GET["id"];
if(!isset($exId) || empty($exId)){
    returnErrorPage(400);
}

if($userSess["is_teacher"] !== 1){
    returnErrorPage(401);
}


$db = new db();
$conn = $db->connect();
$exService = new ExerciseService($conn);

$exDelete = $exService->getExerciseFromId($exId);

//Delete all answer
$exAnswerList = $exService->getAllAnswers($exId);
foreach($exAnswerList as $ans){
    $exService->deleteAnswer($ans["id"]);

    //Delete answer file
    $ansFile = FILE_ANS_PATH.$ans["ans_file"];
    unlink($ansFile);
}

//Delete exercise
$exService->deleteExercise($exId);

//Delete exercise file
$exFile = FILE_EX_PATH.$exDelete["file"];
unlink($exFile);


die(header("Location: /exercise"));
?>