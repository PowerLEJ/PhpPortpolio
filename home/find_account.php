<?php 
    /**
     * 아이디 & 비밀번호 찾기 화면
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-11-09
     */

    // 메뉴 설정
    $activeMenuNum = 3;
    require_once __DIR__ . "/_navbar.php";

    if (isset($_GET['type'])) {
        $type = $_GET['type'];
    }
?>

<div class="container">
    <div class="row text-center">
        <h1><?= 1 == $type ? "아이디 찾기" : "비밀번호 찾기"; ?></h1>
    </div>
    <form method="POST" action="./home/request/find_account_req.php">
        <input type="hidden" name="d1" value="<?= $type ?>" />
        <div class="row">
            <?php 
                if(2 == $type) {
            ?>
            <div class="form-group">
                <div class="mb-3">
                    <label for="user_id" class="form-label">ID</label>
                    <input type="text" class="form-control" name="user_id" placeholder="ID">
                </div>
            </div>
            <?php 
                }
            ?>
            <div class="form-group">
                <div class="mb-3">
                    <label for="user_name" class="form-label">Name</label>
                    <input type="text" class="form-control" name="user_name" placeholder="Name">
                </div>
            </div>
            <div class="form-group">
                <div class="mb-3">
                    <label for="user_email" class="form-label">Email</label>
                    <input type="email" class="form-control" name="user_email" placeholder="id@example.com">
                </div>
            </div>
            <div class="form-group">
                <div class="mb-3">

                    <button type="submit" class="btn btn-dark btn-lg" style="margin-top: 15px; width: 100%;">
                        <?= 1 == $type ? "아이디 찾기" : "비밀번호 찾기"; ?>
                    </button>
                    
                    <div class="text-center" style="padding: 20px;">
                        <?php 
                            if(1 == $type) {
                        ?>
                            <a href="../index.php?url=find_account&type=2">비밀번호 찾기</a>
                        <?php 
                            } else if(2 == $type) {
                        ?>
                            <a href="../index.php?url=find_account&type=1">아이디 찾기</a>
                        <?php
                            }
                        ?>
                        &nbsp;|&nbsp;
                        <a href="../index.php?url=login">로그인</a>
                        &nbsp;|&nbsp;
                        <a href="../index.php?url=signup">회원가입</a>
                    </div>

                </div>
            </div>
        </div>
    </form>
</div>
