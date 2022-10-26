<?php
require_once("../auth.php");
require_once("../utils/db.php");
require_once("../service/quiz.php");
require_once("../service/user.php");
require_once("../utils/const.php");
require_once("../utils/utils.php");

$db = new db();
$conn = $db->connect();
$quizService = new QuizService($conn);
$userService = new UserService($conn);

if($_SERVER["REQUEST_METHOD"] == "GET"){
    $quizId = $_GET["id"];
    if(isset($quizId)){
        $published = null;
        if($userSess["is_teacher"] === 0){
            $published = 1;
        }

        $quizDetail = $quizService->getQuizFromId($quizId, $published);
        $quizCreator = $userService->getUserFromId($quizDetail["creator"]);
        $answerQuiz = $quizService->getAnswerFromUser($userSess["id"], $quizDetail["id"]);
        $answer = getFileName(FILE_QUIZ_PATH.$quizDetail["file"]);

        if($answerQuiz["answer"]){
            if($answerQuiz["answer"] == $answer){
                $isCorrect = true;
            }else{
                $isCorrect = false;
            }
        }else{
            $isCorrect = null;
        }
    }else{
        returnErrorPage(400);
    }
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if($_POST["create"]){
        //Creation
        $quizId = $_POST["quiz_id"];
        $userId = $_POST["user_id"];
        $answerCreate = htmlspecialchars($_POST["answer"], ENT_QUOTES, "UTF-8");

        $answerCheck = $quizService->getAnswerFromUser($userId, $quizId);
        if($answerCheck){
            returnErrorPage(409);
        }

        $quizService->createAnswer($userId, $quizId, $answerCreate);
        die(header("Location: detail.php?id=$quizId"));
    }else if($_POST["delete"]){
        $quizId = $_POST["id"];
        $quizService->deleteQuiz($quizId);
        die(header("Location: /quiz"));
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("../layout/head.php") ?>
    <title>Document</title>
</head>

<body>
    <?php require_once("../layout/navbar.php") ?>

    <section class="content">
        <div class="container-lg">
            <div style="max-width: 900px" class="row">
                <div class="col-12">
                    <div class="row">
                        <h1 class="col-12"><?php echo $quizDetail["title"] ?></h1>
                        <div class="col-12">
                            <p class="text-muted"><?php echo $quizCreator["fullname"]." - ".$quizDetail["create_date"] ?></p>
                        </div>
                        <div class="col-6">
                            <p class="fw-bold text-start">
                                <?php 
                                if($userSess["is_teacher"] === 0){
                                    if($isCorrect !== null){
                                        if($isCorrect === true){
                                            echo "Trả lời đúng";
                                        }else{
                                            echo "Trả lời sai";
                                        }
                                    }else{
                                        echo "Chưa trả lời";
                                    }
                                }
                                ?>
                            </p>
                        </div>
                        <div class="col-6">
                            <p class="fw-bold text-end"><?php echo $quizDetail["end_date"] ? "Đến hạn ".$quizDetail["end_date"] : "Không giới hạn thời gian" ?></p>
                        </div>
                        <hr>
                        <div class="col-12 mb-3">
                            <div class="row">
                                <div class="col-12">
                                    <p><b>Đề bài: </b>Hãy điền đáp án đúng</p>
                                    <p><?php echo $quizDetail["hint"] ?></p>
                                </div>
                                <?php if($userSess["is_teacher"] === 0 && !$answerQuiz){ ?>
                                <div class="col-12">
                                    <form method="POST" action="detail.php" class="form-block">
                                        <input name="quiz_id" type="hidden" value="<?php echo $quizDetail['id'] ?>">
                                        <input name="user_id" type="hidden" value="<?php echo $userSess['id'] ?>">
                                        <div class="mb-3">
                                            <label for="quizAnswer" class="form-label">Câu trả lời</label>
                                            <input name="answer" type="text" class="form-control" id="quizAnswer">
                                        </div>
                                        <div class="mb-3">
                                            <input name="create" type="submit" href="create_answer.php" class="btn btn-outline-success" value="Trả lời">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <hr>
                        <div class="col-12 mb-3">
                            <?php if($userSess["is_teacher"] === 1){
                                echo "<div><b>Đáp án: </b>".$answer."</p></div>";
                            }else{
                                if($answerQuiz){
                                    echo "<div><b>Đáp án của bạn: </b><p class='d-inline-block ".($isCorrect == true ? "text-success" : "text-danger")."'>".$answerQuiz["answer"]."</p></div>";
                                }
                            }
                            ?>
                            <?php if($isCorrect == true || $userSess["is_teacher"] === 1){ ?>
                            <p class="my-0"><b>Phần thưởng:</b></p>
                            <?php
                            foreach(file(FILE_QUIZ_PATH.$quizDetail["file"]) as $line){
                                echo "<p class='mb-0'>$line</p>";
                            } }
                            ?>
                        </div>
                        
                        <?php if($userSess["is_teacher"] === 1){ ?>
                        <hr class="text-muted">
                        <div class="col-12">
                            <a href="update.php?id=<?php echo $quizId ?>" class="btn btn-outline-primary">Chỉnh sửa</a>
                            <a onclick="setDeleteId(<?php echo $quizId ?>)" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">Xóa</a>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!--Modal-->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-sm-down">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Xóa câu đố</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              Bạn có chắc rằng muốn xóa câu đố này?
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <a id="deleteBtn" class="btn btn-danger">Xóa</a>
            </div>
          </div>
        </div>
      </div>
    <!--End modal-->
    
    <script>
        function setDeleteId(id){
            let deleteBtn = document.getElementById("deleteBtn");
            deleteBtn.href = "delete.php?id="+id;
        }
    </script>
</body>

</html>