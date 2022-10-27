<?php
require_once("../auth.php");
require_once("../utils/db.php");
require_once("../service/message.php");
require_once("../service/user.php");
session_start();

$userSess = $_SESSION["user"];
$db = new db();
$conn = $db->connect();
$msgService = new MessageService($conn);
$userService = new UserService($conn);

$msgList = $msgService->getAllMessagesToUser($userSess["id"]);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once("../layout/head.php") ?>
    <title>Document</title>
</head>
<body>
    <?php require_once("../layout/navbar.php") ?>
    <div class="content">
        <div class="container">
            <div class="row">
                <h1>Tin nhắn của bạn</h1>
                <table class="table">
                    <thead>
                        <th scope="col">#</th>
                        <th scope="col">Người gửi</th>
                        <th scope="col">Nội dung</th>
                        <th scope="col">Ngày gửi</th>                        
                    </thead>
                    <?php foreach($msgList as $key=>$msg){ ?>
                    <tbody>
                        <th scope="row"><?php echo $key+1 ?></th>
                        <?php
                        $user = $userService->getUserFromID($msg["from_id"]);
                        ?>
                        <td><a href="profile.php?id=<?php echo $msg['from_id'] ?>"><?php echo $user["fullname"] ?></a></td>
                        <td><?php echo $msg["content"] ?></td>
                        <td><?php echo $msg["create_date"] ?></td>
                    </tbody>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
    
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
</body>
</html>