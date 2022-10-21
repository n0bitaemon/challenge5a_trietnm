<?php
require_once("../auth.php");
require_once("../service/user.php");
require_once("../config/db.php");
session_start();

$user_sess = $_SESSION["user"];
$query = "SELECT * FROM account";

$db = new db();
$conn = $db->connect();
$userService = new UserService($conn);
$userList = $userService->getAllUsers();
//$members = get_multiple_results($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title></title>
</head>
<body>
	<h1>Danh sách thành viên</h1>
	<?php
	if($user_sess["is_teacher"] === 1){
		echo "<a href='../user/create.php'>Thêm người dùng</a>";
	}
	?>
	<table>
		<tr>
			<th>Họ và tên</th>
			<th>Email</th>
			<th>Số điện thoại</th>
			<th>Vai trò</th>
			<th>Hành động</th>
		</tr>
		<?php
		if($userList->rowCount() > 0){
			foreach($userList as $user){
				echo "<tr>";
				echo "<td>".$user["fullname"]."</td>";
				echo "<td>".$user["email"]."</td>";
				echo "<td>".$user["phone"]."</td>";
				echo "<td>".($user["is_teacher"]===1?"Giáo viên":"Học sinh")."</td>";
				echo "<td><a href='../user/profile.php?id=".$user["id"]."'>Xem thông tin</a></td>";
				if($user_sess["is_teacher"] === 1){
					if($user_sess["id"] !== $i["id"])
						echo "<td><a href='../user/delete.php?id=".$user["id"]."'>Xóa</a></td>";
					echo "<td><a href='../user/update.php?id=".$user["id"]."'>Sửa</a></td>";
				}
				echo "</tr>";
			}
		}
		?>
	</table>
</body>
</html>
