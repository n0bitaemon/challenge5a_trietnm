<?php 
require_once("../auth.php");
require_once("../utils/db.php");
require_once("../service/user.php");
require_once("../service/message.php");
require_once("../service/quiz.php");
require_once("../service/exercise.php");
require_once("../utils/const.php");

session_start();

$db = new db();
$conn = $db->connect();
$userService = new UserService($conn);
$msgService = new MessageService($conn);
$quizService = new QuizService($conn);
$exService = new Exerciseservice($conn);

if($_SERVER["REQUEST_METHOD"] == "GET"){
    $id = $_GET["id"];
    if(!isset($id)){
        $id = $userSess["id"];
    }

    //Get user profile
    $userProfile = $userService->getUserFromId($id);
    if(!$userProfile){
        returnErrorPage(409);
    }

    $published = 1;

    //Get all answered quiz
    $quizTotal = $quizService->getAllQuizzes($published)->rowCount();
    $quizDoneList = $quizService->getAllAnswersFromUser($id);

    //Get all answered exercise
    $exTotal = $exService->getAllExercises($published)->rowCount();
    $exDoneList = $exService->getAllAnswersFromUser($id);
}

//Get user's messages
$messages = $msgService->getAllMessagesFromUserToUser($userSess["id"], $id);

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $isError = false;
    if(isset($_POST["send_msg"]) && !empty($_POST["message"])){
        //Send message
        $sender = $_POST["sender"];
        $receiver = $_POST["receiver"];
        $message = htmlspecialchars($_POST["message"], ENT_QUOTES, "UTF-8");

        if(!isset($message) || empty($message)){
            $isError = true;
            $emptyMsgErr = "Tin nhắn không được trống";
        }

        if($isError === false){
            $msgService->sendMessage($sender, $receiver, $message);
            die(header("Location: profile.php?id=$receiver"));
        }
    }else if(isset($_POST["update_msg"])){
        $msgId = $_POST["id"];
        $receiver = $_POST["receiver"];
        $message = htmlspecialchars($_POST["message"], ENT_QUOTES, "UTF-8");
        $msgService->updateMessage($msgId, $message);
        die(header("Location: profile.php?id=$receiver"));
    }else if(isset($_POST["delete_msg"])){
        $msgId = $_POST["id"];
        $receiver = $_POST["receiver"];
        $msgService->deleteMessage($msgId);
        die(header("Location: profile.php?id=$receiver"));
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
        <div class="container">
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="row">
                        <div class="col-lg-3 col-sm-12">
                            <img style="width: 200px;" src="<?php echo FILE_AVATAR_PATH.$userProfile['avatar'] ?>" alt="" class="my-3 rounded">
                        </div>
                        <div class="col-lg-9 col-sm-12 pt-5">
                            <h1>Thông tin cơ bản</h1>
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>Họ tên</td>
                                        <td><?php echo $userProfile["fullname"] ?></td>
                                    </tr>
                                    <tr>
                                        <td>Email</td>
                                        <td><?php echo $userProfile["email"] ?></td>
                                    </tr>
                                    <tr>
                                        <td>Số điện thoại</td>
                                        <td><?php echo $userProfile["phone"] ?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <?php if($userSess["is_teacher"] === 1 || $userSess["id"] == $id){ ?>
                            <a href="update.php?id=<?php echo $userProfile['id'] ?>" class="btn btn-outline-primary">Thay đổi</a>
                            <?php if($userSess["is_teacher"] === 1 && $userSess["id"] != $id && $userProfile["is_teacher"] !== 1){ ?>
                            <a class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">Xóa</a>
                            <?php } } ?>
                        </div>
                    </div>
                </div>
                <?php if($userProfile["is_teacher"] !== 1){ ?>
                <div class="col-7 mb-3">
                    <a class="h3" href="#exerciseHistory" data-bs-toggle="collapse" role="button" aria-expanded="false"
                        aria-controls="exerciseHistory">Lịch sử làm bài</a>
                    <div class="collapse" id="exerciseHistory">
                        <p>Đã hoàn tất <b><?php echo $exDoneList->rowCount()."/".$exTotal ?></b> bài tập</p>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Tên bài</th>
                                    <th scope="col">Thời gian nộp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                foreach($exDoneList as $key=>$ex){ 
                                    $exDetail = $exService->getExerciseFromId($ex["exercise_id"]);
                                ?>
                                <tr>
                                    <th scope="row"><?php echo $key+1 ?></th>
                                    <td><a href="../exercise/detail.php?id=<?php echo $ex['exercise_id'] ?>"><?php echo $exDetail["title"] ?></a></td>
                                    <td><?php echo $ex["submit_date"] ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-7 mb-3">
                    <a class="h3" href="#quizHistory" data-bs-toggle="collapse" role="button" aria-expanded="false"
                        aria-controls="quizHistory">Lịch sử giải đố</a>
                    <div class="collapse" id="quizHistory">
                        <p>Đã hoàn tất <b><?php echo $quizDoneList->rowCount()."/".$quizTotal ?></b> câu đố</p>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Tên bài</th>
                                    <th scope="col">Kết quả</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                foreach($quizDoneList as $key=>$quiz){ 
                                    $quizDetail = $quizService->getQuizFromId($quiz["quiz_id"]);
                                    $quizAnswer = pathinfo($quizDetail["file"])["filename"];
                                ?>
                                <tr>
                                    <th scope="row"><?php echo $key+1 ?></th>
                                    <td><a href="../quiz/detail.php?id=<?php echo $quizDetail['id'] ?>"><?php echo $quizDetail["title"] ?></a></td>
                                    <td><?php echo $quiz["answer"] === $quizAnswer ? "Đúng" : "Sai" ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php } ?>
                <?php if($userSess["id"] != $id){ ?>
                <div class="col-12 mb-3">
                    <a class="h3" href="#userMessages" data-bs-toggle="collapse" role="button" aria-expanded="false"
                        aria-controls="userMessages">Tin nhắn đã gửi</a>
                    <div class="collapse show" id="userMessages">
						<?php if($messages->rowCount()){ ?>
                        <p>Đã gửi <b><?php echo $messages->rowCount() ?></b> tin nhắn</p>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Ngày gửi</th>
                                    <th scope="col">Nội dung</th>
                                    <th scope="col">Hành động</th>
                                </tr>
                            </thead>
                            <body>
								<?php foreach($messages as $msg){ ?>
                                <tr>
                                    <td><?php echo $msg["create_date"] ?></td>
                                    <td><?php echo str_replace("\n", "<br/>", $msg["content"]) ?></td>
                                    <td>
                                        <form action="profile.php" method="POST">
                                            <input name="id" type="hidden" value="<?php echo $msg['id'] ?>">
                                            <input name="receiver" type="hidden" value="<?php echo $id ?>">
                                            <a class="btn btn-sm btn-outline-primary" onclick="setMsgUpdate(<?php echo $msg['id'].','.$id ?>)" data-bs-toggle="modal" data-bs-target="#updateMsgModal">Chỉnh sửa</a>
                                            <a class="btn btn-sm btn-outline-danger" onclick="setMsgDelete(<?php echo $msg['id'].','.$id ?>)" data-bs-toggle="modal" data-bs-target="#deleteMsgModal">Xóa</a>
                                        </form>
                                    </td>
                                </tr>
								<?php } ?>
                            </body>
                        </table>
						<?php }else{ ?>
						<p>Bạn chưa gửi tin nhắn nào đến người này</p>
						<?php } ?>
                        <form action="profile.php" method="POST" class="form-block">
                            <input name="sender" type="hidden" value="<?php echo $userSess['id'] ?>">
                            <input name="receiver" type="hidden" value="<?php echo $id ?>" >
                            <div class="mb-3">
                                <label for="inputMessage" class="form-label">Nhập tin nhắn</label>
                                <textarea name="message" id="inputMessage" cols="30" rows="5" class="form-control"></textarea>
                                <p class="text-danger validate-err"><?php echo $emptyMsgErr ?></p>
                            </div>
                            <input type="submit" value="Gửi tin nhắn" name="send_msg" class="btn btn-outline-success">
                        </form>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <!--Delete modal-->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Xóa người dùng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Bạn có chắc rằng muốn xóa người dùng này?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger">Xóa</button>
                </div>
            </div>
        </div>
    </div>
    <!--End delete modal-->
    <!--Update message modal-->
    <div class="modal fade" id="updateMsgModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-sm-down">
            <form action="profile.php" method="POST">
                <input type="hidden" name="id" id="msgUpdateIdInput">
                <input type="hidden" name="receiver" id="receiverUpdateIdInput">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Thay đổi tin nhắn</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Nhập nội dung tin nhắn mới</p>
                        <input id="msgUpdateContent" name="message" type="text" class="form-control">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <input name="update_msg" type="submit" value="Chỉnh sửa" class="btn btn-primary">
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!--End update message modal-->
    <!--Delete message modal-->
    <div class="modal fade" id="deleteMsgModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Xóa tin nhắn</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Bạn có chắc muốn xóa tin nhắn này?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <form action="profile.php" method="POST">
                        <input id="msgDeleteIdInput" name="id" type="hidden">
                        <input id="receiverDeleteIdInput" name="receiver" type="hidden">
                        <input name="delete_msg" type="submit" value="Xóa" class="btn btn-danger">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End delete message modal-->

    <script>
        function setMsgDelete(msgId, receiverId){
            let msgIdInput = document.getElementById("msgDeleteIdInput");
            let receiverIdInput =  document.getElementById("receiverDeleteIdInput");

            msgIdInput.value = msgId;
            receiverIdInput.value = receiverId;
        }

        function setMsgUpdate(msgId, receiverId){
            let msgIdInput = document.getElementById("msgUpdateIdInput");
            let receiverIdInput = document.getElementById("receiverUpdateIdInput");

            msgIdInput.value = msgId;
            receiverIdInput.value = receiverId;
        }

    </script>
</body>

</html>
