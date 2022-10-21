<?php
require_once("../auth.php");
require_once("../config/db.php");
require_once("../service/user.php");
require_once("../service/message.php");
session_start();

$user_sess = $_SESSION["user"];
$toId = $_GET["to"];
if(isset($to_id) && !empty($to_id)){
	$db = new db();
	$conn = $db->connect();
	$msgService = new MessageService($conn);
	$userService = new UserService($conn);

	$toUser = $userService->getUserFromId($id);
	if(!$toUser){
		die("This user does not exist");
	}
	$msgList = $msgService->getAllMessagesToUser($user_sess["id"], $toId);
}else{
	die("No to_id specified");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Message to <?php echo $toUser["fullname"]?></title>
</head>
<body>
	<h1>Messages you sent to <?php echo $toUser["fullname"]?></h1>
	<table>
		<tr>
			<th>Ngay gui</th>
			<th>Noi dung</th>
		</tr>
		<?php
		foreach($msgList as $msg){
			echo "<tr>";
			echo "<td>".$msg["create_date"]."</td>";
			echo "<td>".$msg["content"]."</td>";
			echo "</tr>";
		}
		?>
	</table>
</body>
</html>
