<?php
require_once("../auth.php");
require_once("../utils/utils.php");
require_once("../utils/db.php");
require_once("../service/user.php");
require_once("../utils/const.php");

$db = new db();
$conn = $db->connect();
$userService = new UserService($conn);

if($_SERVER["REQUEST_METHOD"] == "GET"){
    $id = $_GET["id"];
    if(isset($id) && $id != $userSess["id"]){
        if($userSess["is_teacher"] !== 1){
            returnErrorPage(400);
        }
    }else{
        $id = $userSess["id"];
    }

    $userUpdate = $userService->getUserFromId($id);
    if(!$userUpdate){
        returnErrorPage(409);
    }

    $username = $userUpdate["username"];
    $fullname = $userUpdate["fullname"];
    $email = $userUpdate["email"];
    $phone = $userUpdate["phone"];
    $avatar = $userUpdate["avatar"];
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $isError = false;
	$id = $_POST["id"];
    
    if($id != $userSess["id"] && $userSess["is_teacher"] !== 1){
        returnErrorPage(401);
    }

    $userUpdate = $userService->getUserFromId($id);
    if(!$userUpdate){
        returnErrorPage(409);
    }

    $username = $userUpdate["username"];
    $fullname = $userUpdate["password"];
	$email = htmlspecialchars($_POST["email"], ENT_QUOTES, "UTF-8");
	$phone = htmlspecialchars($_POST["phone"], ENT_QUOTES, "UTF-8");
    $urlAvatar = $_POST["url_avatar"];


    if($userSess["is_teacher"] === 1){
        if(!isset($fullname) || empty($fullname)){
            $isError = true;
            $emptyFullNameErr = "Họ tên không được trống";
        }else if(!isset($username) || empty($username)){
            $isError = true;
            $emptyUsrErr = "Username không được trống";
        }else{
            $username = htmlspecialchars($_POST["username"], ENT_QUOTES, "UTF-8");
            $fullname = htmlspecialchars($_POST["fullname"], ENT_QUOTES, "UTF-8");
        }
    }

    $avatar = $userUpdate["avatar"];
    if($isError === false){
        if(isset($urlAvatar) && !empty($urlAvatar)){
            if(getimagesize($urlAvatar) == false){
                $isError = true;
                $urlNotImageErr = "URL không phải hình ảnh";
            }
            if($isError === false){
                $extension = getimagesize($urlAvatar)[2];
                $targetFile = genFileName(FILE_AVATAR_PATH."avatar".$extension);
                $imgContent = file_get_contents($urlAvatar);
                if(!file_put_contents($targetFile, $imgContent)){
                    $isError = true;
                    $uploadFileFromUrlErr = "Lỗi khi upload file từ URL";
                }else{
                    $avatar = basename($targetFile);
                    if($userUpdate["avatar"]){
                        unlink(FILE_AVATAR_PATH.$userUpdate["avatar"]);
                    }
                }
            }
        }else{
            //Upload avatar
            if(file_exists($_FILES["avatar"]["tmp_name"]) && is_uploaded_file($_FILES["avatar"]["tmp_name"])){
                $targetFile = genFileName(FILE_AVATAR_PATH.basename($_FILES["avatar"]["name"]));
                $avatar = basename($targetFile);
                $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
                if($fileType != "jpg" && $fileType != "jpeg" && $fileType != "png"){
                    $isError = true;
                    $invalidFileTypeErr = "Định dạng file phải là jpg, jpeg hoặc png";
                }
                
                if($isError === false){
                    if(!move_uploaded_file($_FILES["avatar"]["tmp_name"], $targetFile)){
                        $isError = true;
                        $uploadFileErr = "Lỗi khi upload file";
                    }else{
                        if($userUpdate["avatar"]){
                            unlink(FILE_AVATAR_PATH.$userUpdate["avatar"]);
                        }
                    }
                }
            }
        }
    }
		

    if($isError === false){
        $userService->update($id, $email, $phone, $avatar);
        die(header("Location: profile.php?id=$id"));
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("../layout/head.php") ?>
    <title>Thay đổi thông tin</title>
</head>

<body>
    <?php require_once("../layout/navbar.php") ?>
    <section class="content">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-12 mb-3">
                    <h1>Thay đổi thông tin</h1>
                </div>
                <div class="col-12">
                    <form action="update.php" method="POST" enctype="multipart/form-data" class="form-block">
                        <div class="row g-3">
                            <input name="id" type="hidden" value="<?php echo $userUpdate['id'] ?>">
                            <?php if($userSess["is_teacher"] === 1){ ?>
                            <div class="col-md-6 col-sm-12">
                                <label for="userFullName" class="form-label">Họ tên</label>
                                <input name="fullname" type="text" value="<?php echo $userUpdate["fullname"] ?>" class="form-control" id="userFullName" placeholder="Nguyễn Văn A">
                                <p class="text-danger validate-err"><?php echo $emptyFullNameErr ?></p>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <label for="userUsername" class="form-label">Tên đăng nhập</label>
                                <input name="username" type="text" value="<?php echo $userUpdate["username"] ?>" class="form-control" id="userUsername">
                                <p class="text-danger validate-err"><?php echo $emptyUsrErr ?></p>
                            </div>
                            <?php } ?>
                            <div class="col-md-6 col-sm-12">
                                <label for="userEmail" class="form-label">Email</label>
                                <input name="email" type="email" value="<?php echo $email ?>" class="form-control" id="userEmail" placeholder="abc@example.com">
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <label for="userPhone" class="form-label">Số điện thoại</label>
                                <input name="phone" type="tel" value="<?php echo $phone ?>" class="form-control" id="userPhone" placeholder="0123456789">
                            </div>
                            <div class="col-12">
                                <label for="userAvatar" class="form-label">Upload avatar</label>
                                <input name="avatar" onchange="toggleInput()" type="file" class="form-control" id="userAvatar" accept="image/*">
                                <p class="text-danger validate-err"><?php echo $invalidFileTypeErr ?></p>
                                <p class="text-danger validate-err"><?php echo $uploadFileErr ?></p>
                                <label for="urlAvatar" class="form-label mt-3">Upload avatar from url</label>
                                <input name="url_avatar" onchange="toggleInput()" type="text" class="form-control" id="urlAvatar">
                                <p class="text-danger validate-err"><?php echo $uploadFileFromUrlErr ?></p>
                                <p class="text-danger validate-err"><?php echo $urlNotImageErr ?></p>
                                <div class="figure">
                                    <img style="width: 200px;" src="<?php echo FILE_AVATAR_PATH.$userUpdate['avatar'] ?>" alt="Không thể hiển thị hình ảnh" class="my-3 rounded">
                                    <figcaption class="figure-caption text-center">Avatar hiện tại</figcaption>
                                </div>
                            </div>
                            <div class="col-12 mb-5">
                                <input type="submit" value="Cập nhật" class="btn btn-outline-success">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script>
        function toggleInput(){
            let fileAvatarInput = document.querySelectorAll('input[name="avatar"]')[0];
            let urlAvatarInput = document.querySelectorAll('input[name="url_avatar"]')[0];
            
            fileAvatarInput.disabled = urlAvatarInput.value ? true : false;
            urlAvatarInput.disabled = fileAvatarInput.value ? true : false;
        }
    </script>
</body>

</html>
