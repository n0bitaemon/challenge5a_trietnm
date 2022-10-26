<?php 
require_once("../auth.php");
require_once("../utils/db.php");
require_once("../service/quiz.php");
require_once("../service/user.php");

$db = new db();
$conn = $db->connect();
$quizService = new QuizService($conn);
$userService = new UserService($conn);

$published = null;
if($userSess["is_teacher"] === 0){
    $published = 1;
}
$quizList = $quizService->getAllQuizzes($published = $published);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("../layout/head.php"); ?>
    <title>Câu đố</title>
</head>

<body>
    <?php require_once("../layout/navbar.php"); ?>

    <section class="content">
        <div class="container">
            <div class="row">
                <div class="col-12 mb-3">
                    <h1>Danh sách câu đố</h1>
                </div>
                <?php if($userSess["is_teacher"] === 1){ ?>
                <div class="col-12 mb-3">
                    <a href="create.php" class="btn btn-outline-success">Tạo câu đố mới</a>
                </div>
                <?php } ?>
                <?php if($quizList->rowCount() == 0){ ?>
                <p>Hiện tại không có câu đố nào</p>
                <?php }else {
                forEach($quizList as $quiz){ 
                    if($userSess["is_teacher"] === 0){
                        $quizAnswer = $quizService->getAnswerFromUser($userSess["id"], $quiz["id"]);
                        $finished = false;
                        if($quizAnswer){
                            $finished = true;
                        }
                    }
                ?>
                <div class="col-lg-4 col-md-6 col-sm-12 pb-5">
                    <div class="card <?php echo $finished === true ? 'done' : '' ?>">
                        <div class="card-header d-flex justify-content-between">
                            <p><?php echo $quiz["title"] ?></p>
                            <?php if($userSess["is_teacher"] === 1){ ?>
                            <p class="<?php echo $quiz['published'] === 0 ? 'text-danger' : 'text-success' ?>"><?php echo $quiz["published"] === 1 ? "Đã giao" : "Chưa giao" ?></p>
                            <?php }else{ ?>
                            <p class="<?php echo $finished === true ? 'text-success' : 'text-danger' ?>"><?php echo $finished === true ? "Hoàn thành" : "Chưa làm" ?></p>
                            <?php } ?>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php $user = $userService->getUserFromId($quiz["creator"]) ?>
                                <div class="col-7 text-muted"><a href="../quiz?id=<?php echo $quiz['creator'] ?>"><?php echo $user["fullname"] ?></a></div>
                                <div class="col-5 text-muted text-end"><?php echo $quiz["create_date"] ?></div>
                            </div>
                            <div class="card-text py-3"><?php echo $quiz["description"] ?></div>
                            <a href="detail.php?id=<?php echo $quiz['id'] ?>" class="btn btn-primary">Xem Câu đố</a>
                            <?php if($userSess["is_teacher"] === 1){ ?>
                            <a href="update.php?id=<?php echo $quiz['id'] ?>" class="btn btn-outline-info">Sửa</a>
                            <a onclick="setDeleteId(<?php echo $quiz['id'] ?>)" href="#" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">Xóa</a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <?php };} ?>
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
              Bạn có chắc rằng muốn xóa Câu đố này?
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