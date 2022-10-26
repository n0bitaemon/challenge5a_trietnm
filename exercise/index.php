<?php 
require_once("../auth.php");
require_once("../utils/db.php");
require_once("../service/exercise.php");
require_once("../service/user.php");

$db = new db();
$conn = $db->connect();
$exService = new ExerciseService($conn);
$userService = new UserService($conn);

$published = null;
if($userSess["is_teacher"] === 0){
    $published = 1;
}
$exList = $exService->getAllExercises($published = $published);
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
                    <h1>Danh sách bài tập</h1>
                </div>
                <?php if($userSess["is_teacher"] === 1){ ?>
                <div class="col-12 mb-3">
                    <a href="create.php" class="btn btn-outline-success">Tạo bài tập mới</a>
                </div>
                <?php } ?>
                <?php if($exList->rowCount() == 0){ ?>
                <p>Hiện tại không có bài tập nào</p>
                <?php 
                }else {
                    forEach($exList as $ex){ 
                        $exAnswer = $exService->getAnswerFromUser($userSess["id"], $ex["id"]);
                ?>
                <div class="col-lg-4 col-md-6 col-sm-12 pb-5">
                    <div class="card <?php echo $userSess['is_teacher'] === 0 && $exAnswer ? "done" : "" ?>">
                        <div class="card-header d-flex justify-content-between">
                            <p class="m-0"><?php echo $ex["title"] ?></p>
                            <?php 
                            if($userSess["is_teacher"] === 0){
                                if($exAnswer){
                                    echo "<p class='text-success m-0'>Hoàn thành</p>";
                                }else{
                                    echo "<p class='text-danger m-0'>Chưa làm</p>";
                                }
                            }else{
                                if($ex["published"] === 1){
                                    echo "<p class='text-success m-0'>Đã giao</p>";
                                }else{
                                    echo "<p class='text-danger m-0'>Chưa giao</p>";
                                }
                            }
                            ?>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php $user = $userService->getUserFromId($ex["creator"]) ?>
                                <div class="col-7 text-muted"><a href="../exercise?id=<?php echo $ex['creator'] ?>"><?php echo $user["fullname"] ?></a></div>
                                <div class="col-5 text-muted text-end"><?php echo $ex["create_date"] ?></div>
                            </div>
                            <div class="card-text py-3"><?php echo $ex["description"] ?></div>
                            <a href="detail.php?id=<?php echo $ex['id'] ?>" class="btn btn-primary">Xem bài tập</a>
                            <?php if($userSess["is_teacher"] === 1){ ?>
                            <a href="update.php?id=<?php echo $ex['id'] ?>" class="btn btn-outline-info">Sửa</a>
                            <a onclick="setDeleteId(<?php echo $ex['id'] ?>)" href="#" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">Xóa</a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <?php } } ?>
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