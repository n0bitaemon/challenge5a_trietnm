<?php 
require_once("../auth.php");
require_once("../utils/exec_query.php");
require_once("../config/db.php");
require_once("../service/user.php");
require_once("../service/message.php");
session_start();
$IMAGES_PATH = "../static/images/";

$id = $_GET["id"];
$user_sess = $_SESSION["user"];
if(!isset($id)){
	$id = $user_sess["id"];
}

$db = new db();
$conn = $db->connect();
$userService = new UserService($conn);
$msgService = new MessageService($conn);

$userProfile = $userService->getUserFromId($id);

if(!$userProfile){
	die("User not found with id $id");
}

if(isset($_POST["send_msg"]) && !empty($_POST["message"])){
	//Send message
	$message = $_POST["message"];
	$msgService->sendMessage($user_sess["id"], $id, $message);
}

//Get all messages
$msgList = $msgService->getAllMessagesToUser($user_sess["id"], $id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Profile</title>
</head>
<body>
	<h1>Thông tin người dùng</h1>
	<?php
	echo "<p>Tên đăng nhập: ".$userProfile["username"]."</p>";
	echo "<p>Họ và tên: ".$userProfile["fullname"]."</p>";
	echo "<p>Email: ".$userProfile["email"]."</p>";
	echo "<p>Số điện thoại: ".$userProfile["phone"]."</p>";
	echo "<p>Vai trò: ".($userProfile["is_teacher"]==="1"?"Giáo viên":"Học sinh")."</p>";
	echo "<p>Avatar: </p><img src='".$IMAGES_PATH.($userProfile["avatar"]?$userProfile["avatar"]:"default.png")."' alt='Hình ảnh bị lỗi' width='200' height='200'/><br/>";
	if($id === $user_sess["id"] || $user_sess["is_teacher"] === 1){
		echo "<a href='update.php?id=".$userProfile["id"]."'>Sửa</a>";
	}
	if($user_sess["is_teacher"] === 1 && $user_sess["id"] != $id){
		echo "<a href='delete.php?id=".$userProfile["id"]."'>Xóa</a><br/>";
	}
	if($user_sess["id"] !== $userProfile["id"]){
	?>
		<form action="profile.php?id=<?php echo $id?>" method="POST">
			<textarea name="message" cols="30" rows="10"></textarea><br/>
			<input type="submit" name="send_msg" value="Gửi tin nhắn">
		</form>
		<h1>Các tin nhắn đã gửi</h1>
		<table>
			<tr>
				<th>Nội dung</th>
				<th>Ngày gửi</th>
				<th>Hành động</th>
			</tr>
			<?php
			foreach($msgList as $msg){
				echo "<tr>";
				echo "<td>".$msg["content"]."</td>";
				echo "<td>".$msg["create_date"]."</td>";
				echo "<td><a href=''>Sửa</a><a href=''>Xóa</a></td>";
				echo "</tr>";
			}
			?>
		</table>		
	<?php
	}
	?>
</body>
</html>
