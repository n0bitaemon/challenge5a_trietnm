<?php
require_once("utils/exec_query.php");
require_once("config/db.php");
require_once("service/user.php");
session_start();
if($_SERVER["REQUEST_METHOD"] == "POST"){
	$username = $_POST["username"];
	$password = $_POST["password"];	

	$db = new db();
	$conn = $db->connect();
	$userService = new UserService($conn);

	$userLogin = $userService->getUserWithCredentials($username, $password);
	if(!$userLogin){
		$error = "Wrong username or password";
	}else{
		$user_sess = array();
		$user_sess["id"] = $userLogin["id"];
		$user_sess["is_teacher"] = $userLogin["is_teacher"];
		$_SESSION["user"] = $user_sess;
		die(header("Location: index.php"));
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title></title>
</head>
<body>
	<h1>Trang đăng nhập</h1>
	<form action="login.php" method="POST">
		Username: <input type="text" name="username"> <br>
		Password: <input type="password" name="password"> <br>
		<?php
		if(isset($error)){
			echo "<p style='color: red'>".$error."</p>";
		}
		?>
		<input type="submit" value="Đăng nhập">
	</form>
</body>
</html>