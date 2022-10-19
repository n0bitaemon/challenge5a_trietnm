<?php 
require_once("../auth.php");
require_once("../utils/exec_query.php");
require_once("../config/db.php");
require_once("../model/user.php");
session_start();
$IMAGES_PATH = "../static/images/";

$id = $_GET["id"];
$user_sess = $_SESSION["user"];
if(!isset($id)){
	$id = $user_sess["id"];
}

$db = new db();
$conn = $db->connect();
$user = new User($conn);
$user->id = $id;
$result = $user->getUserFromId();

if(!$result){
	die("User not found with id $id");
}

if(isset($_POST["send_msg"]) && !empty($_POST["message"])){
	//Send message
	$message = $_POST["message"];
	$query = "INSERT INTO message (from_id,to_id,content) VALUES(".$user_sess["id"].",$id,'$message')";
	execute($query);
}

//Get all messages
$query = "SELECT * FROM message WHERE from_id=".$user_sess["id"]." AND to_id=$id";
$messages = get_multiple_results($query);
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
	echo "<p>Tên đăng nhập: ".$result["username"]."</p>";
	echo "<p>Họ và tên: ".$result["fullname"]."</p>";
	echo "<p>Email: ".$result["email"]."</p>";
	echo "<p>Số điện thoại: ".$result["phone"]."</p>";
	echo "<p>Vai trò: ".($result["is_teacher"]==="1"?"Giáo viên":"Học sinh")."</p>";
	echo "<p>Avatar: </p><img src='".$IMAGES_PATH.($result["avatar"]?$result["avatar"]:"default.png")."' alt='Hình ảnh bị lỗi' width='200' height='200'/><br/>";
	if($id === $user_sess["id"] || $user_sess["is_teacher"] === "1"){
		echo "<a href='update.php?id=".$result["id"]."'>Sửa</a>";
	}
	if($user_sess["is_teacher"] === "1" && $user_sess["id"] != $id){
		echo "<a href='delete.php?id=".$result["id"]."'>Xóa</a><br/>";
	}
	if($user_sess["id"] !== $result["id"]){
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
			foreach($messages as $msg){
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
