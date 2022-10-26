<?php
require_once("../auth.php");
require_once("../utils/db.php");
require_once("../utils/const.php");
require_once("../service/user.php");
require_once("../service/quiz.php");

if($userSess["is_teacher"] !== 1){
    returnErrorPage(401);
}

$quizId = $_GET["id"];
if(!isset($quizId)){
    returnErrorPage(400);
}

$db = new db();
$conn = $db->connect();
$quizService = new QuizService($conn);

$quizDelete = $quizService->getQuizFromId($quizId);

//Delete All answer of this quiz
$quizAnswerList = $quizService->getAllAnswers($quizId);
foreach($quizAnswerList as $ans){
    $quizService->deleteAnswer($ans["id"]);
}

//Delete quiz
$quizService->deleteQuiz($quizId);

//Delete quiz file
$file = FILE_QUIZ_PATH.$quizDelete["file"];
unlink($file);

die(header("Location: /quiz"));
?>