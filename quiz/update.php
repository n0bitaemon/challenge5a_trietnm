<?php
require_once("../auth.php");
require_once("../utils/utils.php");
require_once("../service/quiz.php");
require_once("../utils/db.php");
require_once("../utils/const.php");

$db = new db();
$conn = $db->connect();
$quizService = new QuizService($conn);

if($_SERVER["REQUEST_METHOD"] == "GET"){
    $quizId = $_GET["id"];
    if(!isset($quizId)){
        returnErrorPage(400);
    }

    $quizUpdate = $quizService->getQuizFromId($quizId);
    if(!$quizUpdate){
        returnErrorPage(409);
    }

    $title = $quizUpdate["title"];
    $desc = $quizUpdate["description"];
    $hint = $quizUpdate["hint"];
    $endDate = $quizUpdate["end_date"];
    $published = $quizUpdate["published"] === "on";
    $quizFile = $quizUpdate["file"];
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $isError = false;

    $quizId = $_POST["id"];
    $quizUpdate = $quizService->getQuizFromId($quizId);

    if(!$quizUpdate){
        returnErrorPage(409);
    }

    $title = htmlspecialchars($_POST["title"], ENT_QUOTES, "UTF-8");
    $desc = htmlspecialchars($_POST["description"], ENT_QUOTES, "UTF-8");
    $hint = htmlspecialchars($_POST["hint"], ENT_QUOTES, "UTF-8");
    $endDate = $_POST["end_date"];
    $published = $_POST["published"]=="on" ? 1 : 0;
    $quizFile = $quizUpdate["file"];

    //Validate
    if(!isset($title) || empty($title)){
        $isError = true;
        $emptyTitleErr = "Tiêu đề không được trống";
    }else if(!isset($hint) || empty($hint)){
        $isError = true;
        $emptyHintErr = "Gợi ý không được trống";
    }else if(!isset($endDate) || empty($endDate)){
        $isError = true;
        $emptyDateErr = "Ngày hết hạn không được trống"; 
    }

    //Upload file
    if($isError === false){
        if(file_exists($_FILES["file"]["tmp_name"]) && is_uploaded_file($_FILES["file"]["tmp_name"])){
            $targetFile = genFileName(FILE_QUIZ_PATH.htmlspecialchars(basename($_FILES["file"]["name"]), ENT_QUOTES, "UTF-8"));
            $quizFile = basename($targetFile);
            $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
            if($fileType != "txt"){
                $isError = true;
                $invalidFileTypeErr = "Định dạng file phải là txt";
            }

            if($isError === false){
                if(!move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)){
                    $isError = true;
                    $uploadFileErr = "Lỗi khi upload file";
                }else{
                    //Delete old file
                    if($quizUpdate["file"]){
                        unlink(FILE_EX_PATH.$quizUpdate["file"]);
                    }
                }
            }
        }
    }

    if($isError === false){
        $quizService->updateQuiz($quizId, $title, $desc, $hint, $quizFile, $endDate, $published);
        die(header("Location: /quiz"));
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
                    <h1>Chỉnh sửa câu đố</h1>
                </div>
                <div class="col-12">
                    <form action="update.php" method="POST" enctype="multipart/form-data" class="form-block">
                        <input name="id" type="hidden" value="<?php echo $quizId ?>">
                        <div class="mb-3">
                            <label for="quizTitle" class="form-label">Tên câu đố</label>
                            <input name="title" type="text" value="<?php echo $title ?>" class="form-control" id="quizTitle">
                            <p class="text-danger validate-err"><?php echo $emptyTitleErr ?></p>
                        </div>
                        <div class="mb-3">
                            <label for="quizDesc" class="form-label">Mô tả</label>
                            <textarea name="description" id="quizDesc" cols="30" rows="5" class="form-control"><?php echo $desc ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="quizHint" class="form-label">Gợi ý</label>
                            <textarea name="hint" id="quizHint" cols="30" rows="5" class="form-control"><?php echo $hint ?></textarea>
                            <p class="text-danger validate-err"><?php echo $emptyHintErr ?></p>
                        </div>
                        <div class="mb-3">
                            <label for="quizFile" class="form-label">Chọn file khác</label>
                            <input name="file" type="file" value="<?php echo $quizFile ?>" class="form-control mb-2" id="quizFile" accept="text/plain">
                            <p class="text-danger validate-err"><?php echo $invalidFileTypeErr ?></p>
                            <p class="text-danger validate-err"><?php echo $uploadFileErr ?></p>
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