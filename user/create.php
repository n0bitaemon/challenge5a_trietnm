<?php
require_once("../auth.php");
require_once("../utils/utils.php");
require_once("../service/user.php");
require_once("../config/db.php");
session_start();
$IMAGES_PATH = "../static/images/";
$user_sess = $_SESSION["user"];
if($user_sess["is_teacher"] !== 1){
	die(require_once("../error/401.php"));
}
if($_SERVER["REQUEST_METHOD"] == "POST"){
	$username = $_POST["username"];
	$password = $_POST["password"];
	$fullname = $_POST["fullname"];
	$email = $_POST["email"];
	$phone = $_POST["phone"];

	//Upload avatar
	if($_FILES["avatar"]){
		$target_file = gen_filename($IMAGES_PATH.basename($_FILES["avatar"]["name"]));
		$avatar = basename($target_file);
		$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

		if($_FILES["avatar"]["size"] > 500000){
			die("File too large");
		}
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif"){
			die("File is not image");
		}

		if(!move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)){
			die("Cannot upload image");
		}

	}else{
		$avatar = "default.png";
	}

	//Config query
	$db = new db();
	$conn = $db->connect();		
	$userService = new UserService($conn);

	$userService->create($username, $password, $fullname, $avatar, $email, $phone);
	die(header("Location: ../class/members.php"));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title></title>
</head>
<body>
	<h1>Tạo người dùng mới</h1>
	<form action="create.php" method="POST" enctype="multipart/form-data">
		Tên đăng nhập: <input type="text" name="username"> <br/>
		Mật khẩu: <input type="password" name="password"> <br/>
		Họ tên: <input type="text" name="fullname"> <br/>
		Email: <input type="email" name="email"> <br/>
		Số điện thoại: <input type="tel" name="phone" pattern="[0-9]{10}"> <br/>
		Avatar: <input type="file" name="avatar"> <br/>
		<input type="submit" value="Tạo mới">
	</form>
</body>
</html>
