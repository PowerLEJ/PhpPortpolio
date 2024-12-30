<?php 
    /**
     * 마이페이지 접근 전 이메일 확인 or 비밀번호 제출 화면
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-11-09
     */

    // 메뉴 설정
    $activeMenuNum = 4;
    require_once __DIR__ . "/_navbar.php";
?>

<div class="container">
    <div class="row text-center">
        <h1>마이페이지</h1>
    </div>
    <?php 
        // 이메일 확인을 안했을 때
        if(0 == $user_level) {
    ?>
            <form method="POST" action="./home/request/verify_email_hand_req.php">
                <input type="hidden" name="d1" value="<?= base64_encode($user_email) ?>">
                <input type="hidden" name="d2" value="<?= base64_encode($user_token) ?>">
                <div class="row">
                    <div class="form-group text-center">
                        <div class="mt-1 mb-3">
                            <p>이메일 확인 후 접근 가능합니다.</p>
                            <button type="submit" class="btn btn-dark btn-lg">이메일 확인</button>
                        </div>
                    </div>
                </div>
            </form>
    <?php 
        } 
        // 마이페이지 접근을 위한 비밀번호 확인
        else {
    ?>
            <form method="POST" action="./home/request/profile_chk_req.php">
                <input type="hidden" name="d1" value="<?= base64_encode($user_idx) ?>">
                <div class="row">
                        <div class="mt-1 text-center">
                            <p>기존 비밀번호 입력 후 마이페이지에 접근 가능합니다.</p>
                        </div>
                    <div class="form-group">
                        <div class="mb-3">
                            <label for="d2" class="form-label">Password</label>
                            <input type="password" name="d2" class="form-control" placeholder="Password">
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <div class="mb-3">
                            <button type="submit" class="btn btn-dark btn-lg" style="margin-top: 15px;">마이페이지 접근</button>
                        </div>
                    </div>
                </div>
            </form>
    <?php 
        }
    ?>
</div>
