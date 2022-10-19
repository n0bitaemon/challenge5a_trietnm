<?php
require_once("../auth.php");
require_once("../utils/exec_query.php");
require_once("../utils/utils.php");
require_once("../config/db.php");
require_once("../model/user.php");
session_start();

$IMAGES_PATH = "../static/images/";

$user_sess = $_SESSION["user"];
$id = $_GET["id"];
if(isset($id) && $id !== $user_sess["id"]){
	if($user_sess["is_teacher"] !== "1"){
		die("You are not teacher");
	}
}else{
	$id = $user_sess["id"];
}

$db = new db();
$conn = $db->connect();
$user = new user($conn);
$user->id = $id;
$result = $user->getUserFromId();

if($_SERVER["REQUEST_METHOD"] == "POST"){
	$id = $_POST["id"];
	$email = $_POST["email"];
	$phone = $_POST["phone"];
		
	//Upload avatar
	if($_FILES["avatar"]){
		$target_file = gen_filename($IMAGES_PATH.basename($_FILES["avatar"]["name"]));
		$filename = basename($target_file);
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
		$filename = $user["avatar"];
	}

	$user_update = new user($conn);
	$user_update->id = $id;
	$user_update->email = $email;
	$user_update->phone = $phone;
	$user_update->avatar = $filename;
	
	$user_update->update();
	/*
	$query_update = "UPDATE account SET email='$email',phone='$phone',avatar='$filename' WHERE id=$id";
	execute($query_update);
	 */
	die(header("Location: profile.php?id=$id"));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title></title>
</head>
<body>
	<h1>Update user</h1>
	<form action="update.php" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="id" value="<?php echo $result['id']; ?>"> <br>
		Username: <?php echo $result["username"]; ?><br>
		Fullname: <?php echo $result["fullname"]; ?> <br>
		Email: <input type="text" name="email" value="<?php echo $result['email']; ?>"> <br>
		Số điện thoại: <input type="text" name="phone" value="<?php echo $result['phone']; ?>"> <br>
		<img src="<?php echo $IMAGES_PATH.$result["avatar"]; ?>" alt='Hình ảnh bị lỗi' width='200' height='200'/> <br/>
		Change avatar: <input type="file" name="avatar"> <br/>
		<input type="submit" value="Change">
	</form>
</body>
</htl>
