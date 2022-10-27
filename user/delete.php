<?php
require_once("../auth.php");
require_once("../utils/db.php");
require_once("../service/user.php");
require_once("../service/quiz.php");
require_once("../service/exercise.php");
require_once("../service/message.php");
require_once("../utils/const.php");

if($userSess["is_teacher"] !== 1){
	returnErrorPage(401);
}

$userId = $_GET["id"];
if(!isset($userId)){
	returnErrorPage(400);
}

$db = new db();
$conn = $db->connect();
$userService = new UserService($conn);
$quizService = new QuizService($conn);
$exService = new ExerciseService($conn);
$msgService = new MessageService($conn);

$userDelete = $userService->getUserFromId($userId);
if(!$userDelete){
	returnErrorPage(409);
}
if($userId == $userSess["id"]){
	returnErrorPage(409);
}
if($userDelete["is_teacher"] === 1){
	returnErrorPage(409);
}

//Delete all messages
$msgFromList = $msgService->getAllMessagesFromUser($userId);
foreach($msgFromList as $msg){
	$msgService->deleteMessage($msg["id"]);
}
$msgToList = $msgService->getAllMessagesToUser($userId);
foreach($msgToList as $msg){
	$msgService->deleteMessage($msg["id"]);
}

//Delete all exercise answers
$exList = $exService->getAllAnswersFromUser($userId);
foreach($exList as $ex){
	$exService->deleteAnswer($ex["id"]);
}

//Delete all quiz answers
$quizList = $quizService->getAllAnswersFromUser($userId);
foreach($quizList as $quiz){
	$quizService->deleteAnswer($quiz["id"]);
}

//Delete user
$userService->delete($userId);

//Delete avatar
$userAvatar = FILE_AVATAR_PATH.$userDelete["avatar"];
unlink($userAvatar);

die(header("Location: /user"));
?>