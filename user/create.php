<?php
require_once("../auth.php");
require_once("../utils/utils.php");
require_once("../service/user.php");
require_once("../utils/db.php");
require_once("../utils/const.php");
session_start();

$user_sess = $_SESSION["user"];
if($user_sess["is_teacher"] !== 1){
	returnErrorPage(401);
}
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $isError = false;

	$username = htmlspecialchars($_POST["username"], ENT_QUOTES, "UTF-8");
	$password = $_POST["password"];
    $rePassword = $_POST["repassword"];
	$fullname = htmlspecialchars($_POST["fullname"], ENT_QUOTES, "UTF-8");
	$email = htmlspecialchars($_POST["email"], ENT_QUOTES, "UTF-8");
	$phone = htmlspecialchars($_POST["phone"], ENT_QUOTES, "UTF-8");

    //Validate
    if(!isset($fullname) || empty($fullname)){
        $isError = true;
        $emptyFullNameErr = "Họ tên không được trống";
    }else if(!isset($username) || empty($username)){
        $isError = true;
        $emptyUsrErr = "Tên đăng nhập không được trống";
    }else if(!isset($password) || empty($password)){
        $isError = true;
        $emptyPwdErr = "Mật khẩu không được trống"; 
    }else if($password !== $rePassword){
        $isError = true;
        $notMatchPwdErr = "Mật khẩu không khớp";
    }

	if($isError === false){
        //Config query
        $db = new db();
        $conn = $db->connect();		
        $userService = new UserService($conn);

        $userService->create($username, md5($password), $fullname, $email, $phone);
        die(header("Location: /user"));
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
                    <h1>Thêm học sinh mới</h1>
                </div>
                <div class="col-12">
                    <form action="create.php" method="POST" enctype="multipart/form-data" class="form-block">
                        <div class="row g-3">
                            <div class="col-md-6 col-sm-12">
                                <label for="userFullname" class="form-label">Họ tên</label>
                                <input name="fullname" type="text" value="<?php echo $fullname ?>" class="form-control" id="userFullname" placeholder="Nguyễn Văn A">
                                <p class="text-danger validate-err"><?php echo $emptyFullNameErr ?></p>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <label for="userUsername" class="form-label">Tên đăng nhập</label>
                                <input name="username" type="text" value="<?php echo $username ?>" class="form-control" id="userUsername">
                                <p class="text-danger validate-err"><?php echo $emptyUsrErr ?></p>
                            </div>
                            <div class="col-12">
                                <label for="userPassword" class="form-label">Mật khẩu</label>
                                <input name="password" type="password" class="form-control" id="userPassword">
                                <p class="text-danger validate-err"><?php echo $emptyPwdErr ?></p>
                            </div>
                            <div class="col-12">
                                <label for="userRepassword" class="form-label">Nhập lại mật khẩu</label>
                                <input name="repassword" type="password" class="form-control" id="userRepassword">
                                <p class="text-danger validate-err"><?php echo $emptyRePwdErr ?></p>
                                <p class="text-danger validate-err"><?php echo $notMatchPwdErr ?></p>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <label for="userEmail" class="form-label">Email</label>
                                <input name="email" type="email" value="<?php echo $email ?>" class="form-control" id="userEmail" placeholder="abc@example.com">
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <label for="userPhone" class="form-label">Số điện thoại</label>
                                <input name="phone" type="tel" value="<?php echo $phone ?>" class="form-control" id="userPhone" placeholder="0123456789">
                            </div>
                            <div class="col-12 mb-5">
                                <input type="submit" value="Tạo mới" class="btn btn-outline-success">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</body>

</html>