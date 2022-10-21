<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Fanpage</title>
</head>
<body>
	<h1>Chào mừng đến với fanpage</h1>
	<?php
	if(!$_SESSION["user"]){
	?>
	<a href="login.php">Đăng nhập</a>
	<?php }else{ ?>
	<a href="user/profile.php">Thông tin người dùng</a> <br/>
	<a href="class/exercise.php">Bài tập</a> <br/>
	<a href="class/quiz.php">Giải đố</a> <br/>
	<a href="class/members.php">Thành viên</a> <br/>
	<a href="logout.php">Đăng xuất</a>
	<?php } ?>
</body>
</html>
