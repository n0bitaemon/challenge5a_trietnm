<?php
require_once("../auth.php");
require_once("../utils/utils.php");
require_once("../utils/db.php");
require_once("../service/exercise.php");
require_once("../service/user.php");
require_once("../utils/const.php");

$db = new db();
$conn = $db->connect();
$exService = new ExerciseService($conn);
$userService = new UserService($conn);

if($_SERVER["REQUEST_METHOD"] == "GET"){
    $exId = $_GET["id"];
    if(!isset($exId) || empty($exId)){
        returnErrorPage(400);
    }

    $exDetail = $exService->getExerciseFromId($exId);
    $exCreator = $userService->getUserFromId($exDetail["creator"]);

    //If user is student, then $exAnswer is his answer
    //If user is teacher, then $exAnswer is list of answers
}else if($_SERVER["REQUEST_METHOD"] == "POST"){
    $isError = false;
    $userId = $_POST["user_id"];
    $exId = $_POST["exercise_id"];

    $exAnswer = $exService->getAnswerFromUser($userId, $exId);
    if(!$exAnswer){
        returnErrorPage(404);
    }

    if(isset($_POST["submit_ans"])){
        //Submit Answer
        if($_FILES["file"]){
            $targetFile = genFileName(FILE_EX_PATH.basename($_FILES["file"]["name"]));
            $ansFile = basename($targetFile);
            $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            
            if($fileType != "txt" && $fileType != "docx"){
                $isError = true;
                $fileTypeErr = "Chỉ được upload file có đuôi txt, docx";
            }

            if($isError === false && !move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)){
                $isError = true;
                $uploadFileErr = "Lỗi khi upload file";
            }
        }else{
            $isError = true;
            $emptyFileErr = "Không có file nào được chọn";
        }

        if($isError === false){
            $isDone = 1;
            $exAnswer = $exService->getAnswerFromUser($userSess["id"], $exId);
            if(!$exAnswer){ 
                $exService->createAnswer($userId, $exId, $ansFile, $isDone);
            }else{
                $exService->updateAnswer($userId, $exId, $ansFile, $isDone);
            }
            die(header("Location: detail.php?id=$exId"));
        }

    }else if(isset($_POST["cancle_ans"])){
        $isDone = 0;
        $exService->updateAnswer($userId, $exId, $exAnswer["ans_file"], $isDone);
        die(header("Location: detail.php?id=$exId"));
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
            <div class="row">

                <!-- Left column -->
                <div class="col-lg-8 col-md-8 col-sm-12">
                    <div class="row">
                        <h1 class="col-12"><?php echo $exDetail["title"] ?></h1>
                        <div class="col-12">
                            <p class="text-muted"><?php echo $exCreator["fullname"]." - ".$exDetail["create_date"] ?></p>
                        </div>
                        <div class="col-6">
                            <p class="fw-bold text-start">?/10 điểm</p>
                        </div>
                        <div class="col-6">
                            <p class="fw-bold text-end">Đến hạn <?php echo $exDetail["end_date"] ?></p>
                        </div>
                        <hr>
                        <div class="col-12 mb-3">
                            <div class="col-6 py-1">
                                <div class="card">
                                    <div class="card-body p-0">
                                    <a class="d-block p-2 text-center" href="download.php?file=<?php echo $exDetail['file'] ?>"><?php echo getFileName(FILE_EX_PATH.$exDetail["file"])?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="text-muted">
                        <?php if($userSess["is_teacher"] === 1){ ?>
                        <div class="col-12 mb-">
                            <a href="update.php?id=<?php echo $exId ?>" class="btn btn-outline-primary">Chỉnh sửa</a>
                            <a onclick="setDeleteId(<?php echo $exDetail['id'] ?>)" href="#" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">Xóa</a>
                        </div>
                        <?php } ?>
                    </div>
                </div>

                <!--Left column -->
                <?php 
                if($userSess["is_teacher"] === 0){ 
                    $exAnswer = $exService->getAnswerFromUser($userSess["id"], $exId);   
                ?>
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-title d-flex justify-content-between">
                                        <h5 class="d-inline-block m-0">Bài tập</h5>
                                        <p class="d-inline-block text-success m-0"><?php echo $exAnswer && $exAnswer["is_done"] === 1 ? "Hoàn thành" : "Đã giao" ?></p>
                                    </div>
                                    <div class="d-grid gap-2 mt-3">
                                        <form action="detail.php" method="POST" enctype="multipart/form-data">
                                            <input name="user_id" value="<?php echo $userSess['id'] ?>" type="hidden">
                                            <input name="exercise_id" value="<?php echo $exId ?>" type="hidden">
                                            <?php if(!$exAnswer || $exAnswer["is_done"] === 0){ ?>
                                                <?php if($exAnswer["ans_file"]){ ?>
                                                <div class="card mb-2">
                                                    <div class="card-body p-0">
                                                    <a class="d-block p-2 text-center" href="download.php?file=<?php echo $exDetail['file'] ?>"><?php echo getFileName(FILE_EX_PATH.$exDetail["file"])?></a>
                                                    </div>
                                                </div>
                                                <?php }else{ ?>
                                                <p class='mb-2'>Chưa upload file</p>
                                                <?php } ?>
                                                <input name='file' type='file' class='form-control mb-2'>
                                                <p class="text-danger validate-err"><?php echo $emptyFileErr ?></p>
                                                <p class="text-danger validate-err"><?php echo $fileTypeErr ?></p>
                                                <input name='submit_ans' value='Nộp bài' type='submit' class='btn btn-primary'>
                                            <?php }else{ ?>
                                                <div class="col-12 mb-3 py-1">
                                                    <div class="card">
                                                        <div class="card-body p-0">
                                                            <a class="d-block p-3" href="download.php?file=<?php echo $exAnswer['ans_file'] ?>"><?php echo $exAnswer["ans_file"]?></a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <form action="detail.php" method="POST">
                                                    <input name="user_id" value="<?php echo $userSess['id'] ?>" type="hidden">
                                                    <input name="exercise_id" value="<?php echo $exId ?>" type="hidden">
                                                    <input name='cancle_ans' value='Hủy nộp bài' type='submit' class='btn btn-danger'>
                                                </form>
                                            <?php } ?>
                                        </form>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php }else if($userSess["is_teacher"] === 1){ ?>
                <div class="col-12 mt-5">
                    <h3>Danh sách bài làm</h3>
                    <table class="table">
                        <thead>
                            <th scope="col">#</th>
                            <th scope="col">Họ tên</th>
                            <th scope="col">Điểm</th>
                            <th scope="col">Ngày nộp</th>
                            <th scope="col">File bài làm</th>
                        </thead>
                        <tbody>
                            <?php 
                            $exAnswers = $exService->getAllAnswers($exId);
                            foreach($exAnswers as $key=>$ans){ 
                                if($ans["is_done"] === 1){
                                    $userAnswer = $userService->getUserFromId($ans["user_id"]);
                            ?>
                            <tr>
                                <th scope="row"><?php echo $key+1 ?></th>
                                <td><a href="answer.php?id=<?php echo $userAnswer['id'] ?>"><?php echo $userAnswer["fullname"] ?></td>
                                <td><?php echo $ans["score"] ? $ans["score"] : "Chưa chấm" ?></td>
                                <td><?php echo $ans["submit_date"] ?></td>
                                <td><a href="download.php?file=<?php echo $ans['ans_file'] ?>" class="btn btn-sm btn-outline-primary">Download</a></td>
                            </tr>
                            <?php } } ?>
                        </tbody>
                    </table>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <!--Modal-->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-sm-down">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              Bạn có chắc rằng muốn xóa bài tập này?
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <a type="button" class="btn btn-danger" id="deleteBtn">Xóa</a>
            </div>
          </div>
        </div>
      </div>
    <!--End modal-->

    <script>
        function setDeleteId(id){
            deleteBtn = document.getElementById("deleteBtn");
            deleteBtn.href = "delete.php?id=" + id;
        }
    </script>
</body>

</html>