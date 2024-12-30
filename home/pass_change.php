<?php 
    /**
     * 비밀번호 찾기에서 이메일을 통한 비밀번호 변경 화면
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-11-09
     */

    // 메뉴 설정
    $activeMenuNum = 3;
    require_once __DIR__ . "/_navbar.php";
?>

<div class="container">
    <div class="row text-center">
        <h1>비밀번호 변경</h1>
    </div>
    <form method="POST" action="./home/request/pass_change_req.php">
        <input type="hidden" name="d1" value="<?= $_GET['d1'] ?>" />
        <div class="row">
            <div class="form-group">
                <div class="mb-3">
                    <label for="password" class="form-label">Password*</label>
                    <input type="password" class="form-control" name="password" placeholder="Password">
                </div>
            </div>
            <div class="form-group">
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password*</label>
                    <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password">
                </div>
            </div>
            <div class="form-group">
                <div class="mb-3">
                    <button type="submit" class="btn btn-dark btn-lg" style="margin-top: 15px; width: 100%;">비밀번호 변경</button>
                </div>
            </div>
        </div>
    </form>
</div>
