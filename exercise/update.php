<?php
require_once("../auth.php");
require_once("../utils/utils.php");
require_once("../service/exercise.php");
require_once("../utils/db.php");
require_once("../utils/const.php");

$db = new db();
$conn = $db->connect();
$exService = new ExerciseService($conn);

if($_SERVER["REQUEST_METHOD"] == "GET"){
    $exId = $_GET["id"];
    if(!isset($exId) || empty($exId)){
        returnErrorPage(400);
    }

    $exUpdate = $exService->getExerciseFromId($exId);
    if(!$exUpdate){
        returnErrorPage(409);
    }

    $title = $exUpdate["title"];
    $desc = $exUpdate["description"];
    $endDate = $exUpdate["end_date"];
    $published = $exUpdate["published"];
    $exFile = $exUpdate["file"];
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $isError = false;

    $exId = $_POST["id"];
    $exUpdate = $exService->getExerciseFromId($exId);

    if(!$exUpdate){
        returnErrorPage(409);
    }
    
    $title = htmlspecialchars($_POST["title"], ENT_QUOTES, "UTF-8");
    $desc = htmlspecialchars($_POST["description"], ENT_QUOTES, "UTF-8");
    $endDate = $_POST["end_date"];
    $published = $_POST["published"]=="on"? 1 : 0;
    $exFile = $exUpdate["file"];

    if(!isset($title) || empty($title)){
        $isError = true;
        $emptyTitleErr = "Tiêu đề không được trống";
    }else if(!isset($endDate) || empty($endDate)){
        $isError = true;
        $emptyDateErr = "Ngày hết hạn không được trống"; 
    }

    //Upload file
    if($isError === false){
        if(file_exists($_FILES["file"]["tmp_name"]) && is_uploaded_file($_FILES["file"]["tmp_name"])){
            $targetFile = genFileName(FILE_EX_PATH.basename($_FILES["file"]["name"]));
            $exFile = basename($targetFile);
            $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            if($fileType != "txt" && $fileType != "docx" && $fileType != "pdf"){
                $isError = true;
                $invalidFileTypeErr = "Định dạng file phải là txt, docx hoặc pdf";
            }

            if($isError === false){
                if(!move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)){
                    $isError = true;
                    $uploadFileErr = "Lỗi khi upload file";
                }else{
                    //Delete old file
                    if($exUpdate["file"]){
                        unlink(FILE_EX_PATH.$exUpdate["file"]);
                    }
                }

            }

        }
    }

    if($isError === false){
        $exService->updateExercise($exId, $title, $desc, $exFile, $endDate, $published);
        die(header("Location: /exercise"));
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("../layout/head.php") ?>
    <title>Document</title>
</head>

<body>
    <?php require_once("../layout/navbar.php") ?>
    <section class="content">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-12 mb-3">
                    <h1>Chỉnh sửa bài tập</h1>
                </div>
                <div class="col-12">
                    <form action="update.php?id=<?php echo $exId ?>" method="POST" enctype="multipart/form-data" class="form-block">
                        <input name="id" type="hidden" value="<?php echo $exId ?>">
                        <div class="mb-3">
                            <label for="exTitle" class="form-label">Tiêu đề</label>
                            <input name="title" type="text" value="<?php echo $title ?>" class="form-control" id="exTitle">
                            <p class="text-danger validate-err"><?php echo $emptyTitleErr ?></p>
                        </div>
                        <div class="mb-3">
                            <label for="exDesc" class="form-label">Mô tả</label>
                            <textarea name="description" id="exDesc" cols="30" rows="5" class="form-control"><?php echo $desc ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="exFile" class="form-label">Chọn file khác</label>
                            <input name="file" type="file" class="form-control" id="exFile">
                            <p class="text-danger validate-err"><?php echo $invalidFileTypeErr ?></p>
                            <p class="text-danger validate-err"><?php echo $uploadFileErr ?></p>
                            <p class="d-inline">File hiện tại: </p>
                            <div class="card mt-2 d-inline-block">
                                <div class="card-body p-0">
                                    <a class="d-block p-2 text-center" href="download.php?file=<?php echo $exUpdate['file'] ?>"><?php echo getFileName(FILE_EX_PATH.$exUpdate["file"])?></a>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="exerciseDate" class="form-label">Ngày hết hạn</label>
                            <input name="end_date" type="datetime-local" value="<?php echo $endDate ?>" id="exerciseDate" class="form-control">
                            <p class="text-danger validate-err"><?php echo $emptyDateErr ?></p>
                        </div>
                        <div class="mb-3 form-check">
                            <label for="exercisePublished" class="form-check-label">Giao bài</label>
                            <input name="published" type="checkbox" <?php echo $published === 1 ? "checked" : "" ?> class="form-check-input" id="exercisePublished">
                        </div>
                        <div class="mb-3">
                            <input type="submit" value="Cập nhật" class="btn btn-success">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</body>

</html>