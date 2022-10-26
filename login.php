<?php
require_once("utils/db.php");
require_once("service/user.php");
session_start();
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $isError = false;
	$username = $_POST["username"];
	$password = $_POST["password"];

    //Validate
    if(!isset($username) || empty($username)){
        $isError = true;
        $usernameEmptyErr = "Tên đăng nhập không được trống";
    }
    if(!isset($password) || empty($password)){
        $isError = true;
        $passwordEmptyErr = "Mật khẩu không được trống";
    }

    if($isError === false){
        $db = new db();
        $conn = $db->connect();
        $userService = new UserService($conn);

        $userLogin = $userService->getUserWithCredentials($username, md5($password));
        if(!$userLogin){
            $isError = true;
            $invalidCredentialsErr = "Sai tên đăng nhập hoặc mật khẩu";
        }
    }

    //Check username and password
    if($isError === false){
        $userSess = array();
        $userSess["id"] = $userLogin["id"];
        $userSess["is_teacher"] = $userLogin["is_teacher"];
        $_SESSION["user"] = $userSess;
        die(header("Location: /exercise"));
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("layout/head.php") ?>
    <style>
        body{
            background-image: url("/static/images/background.jpg");
        }
    </style>
    <title>Document</title>
</head>

<body>
    <div class="jumbotron d-flex align-items-center h-100">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="container block-login">
                        <div class="row justify-content-center">
                            <h1 class="col-12 mt-5 mb-3 text-center">Đăng nhập</h1>
                            <form action="login.php" method="POST" class="col-6 justify-content-center mb-5">
                                <div class="mb-3">
                                    <label for="inputUsername" class="form-label">Tài khoản</label>
                                    <input name="username" value="<?php echo $username ?>" type="text" class="form-control" id="inputUsername" placeholder="">
                                    <p class="text-danger validate-err"><?php echo $usernameEmptyErr?> </p>
                                </div>
                                <div class="mb-3">
                                    <label for="inputPassword" class="form-label">Mật khẩu</label>
                                    <input name="password" type="password" class="form-control" id="inputPassword">
                                    <p class="text-danger validate-err"><?php echo $passwordEmptyErr?> </p>
                                </div>
								<p class="text-danger validate-err"><?php echo $invalidCredentialsErr?> </p>
                                <div class="mb-3">
                                    <button class="btn btn-outline-success">Đăng nhập</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>