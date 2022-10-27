<?php
require_once("../auth.php");
require_once("../service/user.php");
require_once("../utils/db.php");
session_start();

$user_sess = $_SESSION["user"];
$query = "SELECT * FROM account";

$db = new db();
$conn = $db->connect();
$userService = new UserService($conn);
$userList = $userService->getAllUsers();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("../layout/head.php") ?>
    <title>Danh sách thành viên</title>
</head>

<body>
    <?php require_once("../layout/navbar.php") ?>

    <section class="content">
        <div class="container">
            <div class="row">
                <div class="col-12 mb-3">
                    <h1>Danh sách thành viên</h1>
                </div>
					      <?php if($user_sess["is_teacher"] === 1){ ?>
                <div class="col-12 mb-3">
                    <a href="create.php" class="btn btn-outline-success">Thêm học sinh</a>
                </div>
					      <?php } ?>
                <p>Lớp có <b><?php echo $userList->rowCount() ?></b> thành viên</p>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Họ tên</th>
                            <th scope="col">Email</th>
                            <th scope="col">Số điện thoại</th>
                            <th scope="col">Vai trò</th>
                        </tr>
                    </thead>
                    <tbody>
						<?php foreach($userList as $key=>$user){ ?>
							<tr>
								<th scope="row"><?php echo $key+1 ?></th>
								<td><a href="profile.php?id=<?php echo $user['id']?>"><?php echo $user["fullname"] ?></a></td>
								<td><?php echo $user["email"] ?> </td>
								<td><?php echo $user["phone"] ?></td>
								<td><?php echo $user["is_teacher"] === 1 ? "Giáo viên" : "Học sinh" ?></td>
                <?php if($user_sess["is_teacher"] === 1){ ?>
								<td class="action">
                  <?php if($user["id"] === $user_sess["id"] || $user["is_teacher"] !== 1){ ?>
									<a href="update.php?id=<?php echo $user['id'] ?>" class="btn btn-primary btn-sm">Chỉnh sửa</a>
                  <?php } ?>
                  <?php if($user_sess["id"] !== $user["id"] && $user["is_teacher"] !== 1){ ?>
									<a onclick="setDeleteId(<?php echo $user['id'] ?>)" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">Xóa</a>
                  <?php } ?>
								</td>
                <?php } ?>
							</tr>
						<?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!--Modal-->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-sm-down">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              Bạn có chắc rằng muốn xóa người dùng này?
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <a id="deleteBtn" type="button" class="btn btn-danger">Xóa</a>
            </div>
          </div>
        </div>
      </div>
    <!--End modal-->
    
    <script>
      function setDeleteId(id){
        let deleteBtn = document.getElementById("deleteBtn");
        deleteBtn.href = "delete.php?id=" + id;
      }
    </script>
</body>

</html>
