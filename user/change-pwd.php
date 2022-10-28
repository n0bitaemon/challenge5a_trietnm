<?php
require_once("../auth.php");
require_once("../utils/utils.php");
require_once("../utils/db.php");
require_once("../service/user.php");
require_once("../utils/const.php");

$db = new db();
$conn = $db->connect();
$userService = new UserService($conn);

if($_SERVER["REQUEST_METHOD"] == "GET"){
    $userId = $_GET["id"];
    if(!isset($userId) || empty($userId)){
        $userId = $userSess["id"];
    }

    if($userSess["is_teacher"] !== 1 && $userSess["id"] != $userId){
        returnErrorPage(401);
    }

    $userUpdate = $userService->getUserFromId($userId);
    if(!$userUpdate){
        returnErrorPage(409);
    }else if($userUpdate["is_teacher"] && $userUpdate["id"] != $userSess["id"]){
        returnErrorPage(401);
    }
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $isError = false;
    $userId = $_POST["id"];
    if(!isset($userId)){
        returnErrorPage(400);
    }
    $userUpdate = $userService->getUserFromId($userId);
    if(!$userUpdate){
        returnErrorPage(409);
    }else if($userUpdate["is_teacher"] && $userUpdate["id"] != $userSess["id"]){
        returnErrorPage(401);
    }

    $oldPass = $_POST["oldPass"];
    $newPass = $_POST["newPass"];
    $rePass = $_POST["rePass"];

    if(!isset($oldPass) || empty($oldPass)){
        $isError = true;
        $emptyOldPassErr = "Hãy nhập password cũ";
    }else if(!isset($newPass) || empty($newPass)){
        $isError = true;
        $emptyNewPassErr = "Hãy nhập password mới";
    }else if(!isset($rePass) || empty($rePass)){
        $isError = true;
        $emptyRePassErr = "Hãy nhập lại password mới";
    }

    if($isError === false){
        if($userUpdate["password"] !== md5($oldPass)){
            $isError = true;
            $wrongPassErr = "Nhập sai mật khẩu";
        }else{
            if($newPass !== $rePass){
                $isError = true;
                $notMatchErr = "Mật khẩu không khớp";
            }
        }
    }

    if($isError === false){
        $userService->changePassword($userId, md5($newPass));
        die(header("Location: profile.php"));
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
            <div class="row d-flex justify-content-center">
                <div class="col-12 mb-3">
                    <h1>Đổi mật khẩu</h1>
                </div>
                <div class="col-12">
                    <form method="POST" class="form-block">
                        <div class="row g-3">
                            <input name="id" type="hidden" value="<?php echo $userId ?>">
                            <div class="col-12">
                                <label for="oldPass" class="form-label">Mật khẩu cũ</label>
                                <input name="oldPass" type="password" class="form-control" id="oldPass">
                                <p class="text-danger validate-err"><?php echo $emptyOldPassErr ?></p>
                                <p class="text-danger validate-err"><?php echo $wrongPassErr ?></p>
                            </div>
                            <div class="col-12">
                                <label for="newPass" class="form-label">Mật khẩu mới</label>
                                <input name="newPass" type="password"" class="form-control" id="newPass">
                                <p class="text-danger validate-err"><?php echo $emptyNewPassErr ?></p>
                            </div>
                            <div class="col-12">
                                <label for="rePass" class="form-label">Nhập lại mật khẩu</label>
                                <input name="rePass" type="password"" class="form-control" id="rePass">
                                <p class="text-danger validate-err"><?php echo $emptyRePassErr ?></p>
                                <p class="text-danger validate-err"><?php echo $notMatchErr ?></p>
                            </div>
                            <div class="col-12 mb-5">
                                <input type="submit" value="Thay đổi" class="btn btn-outline-success">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</body>

</html>
