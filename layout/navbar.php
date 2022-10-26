<?php
require_once("../utils/db.php");
require_once("../service/user.php");
session_start();

$uriSegments = explode("/", parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));
$menuOpt = $uriSegments[1];

$db = new db();
$conn = $db->connect();
$userService = new UserService($conn);

$userLogin = $userService->getUserFromId($userSess["id"]);
?>
<header class="main-header">
        <div class="container">
            <nav class="navbar navbar-expand-lg main-nav px-0">
                <div class="container-fluid">
                    <a class="navbar-brand" href="../exercise">TrietEdu</a>
                    <div id="mainMenu">
                        <ul class="navbar-nav ml-auto text-uppercase f1">
                            <li>
                                <a href="/exercise" <?php echo $menuOpt == "exercise" ? "class='active'" : "" ?>>Bài tập</a>
                            </li>
                            <li>
                                <a href="/quiz" <?php echo $menuOpt == "quiz" ? "class='active'" : "" ?>>Quiz</a>
                            </li>
                            <li>
                                <a href="/user" <?php echo $menuOpt == "user" ? "class='active'" : "" ?>>Lớp học</a>
                            </li>
                            <li>
                        </ul>
                    </div>
                    <div id="avatar">
                        <ul class="navbar-nav">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                  <?php echo $userLogin["fullname"] ?>
                                </a>
                                <ul class="dropdown-menu">
                                  <li><a class="dropdown-item" href="/user/profile.php">Thông tin cá nhân</a></li>
                                  <li><a class="dropdown-item" href="/user/update.php">Cập nhật hồ sơ</a></li>
                                  <li><a href="/user/messages.php" class="dropdown-item">Tin nhắn</a></li>
                                  <li><hr class="dropdown-divider"></li>
                                  <li><a class="dropdown-item" href="/logout.php">Đăng xuất</a></li>
                                </ul>
                              </li>
                        </ul>
                    </div>
                </div>
            </nav>

        </div>
    </header>