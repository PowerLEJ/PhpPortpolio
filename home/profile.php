<?php 
    /**
     * 마이페이지 화면
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
    <form method="POST" action="./home/request/profile_req.php">
        <input type="hidden" name="d0" value="<?= base64_encode($user_idx) ?>">
        <div class="row animate-box">
            <div class="form-group">
                <div class="mb-3">
                    <label for="user_id" class="form-label">ID*</label>
                    <input type="text" class="form-control" name="user_id" placeholder="ID"  value="<?= $user_id ?>" readonly />
                </div>
            </div>
            <div class="form-group">
                <div class="mb-3">
                    <label for="password" class="form-label">Password*</label>
                    <input type="password" class="form-control" name="password" placeholder="Password" />
                </div>
            </div>
            <div class="form-group">
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password*</label>
                    <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" />
                </div>
            </div>
            <div class="form-group">
                <div class="mb-3">
                    <label for="user_name" class="form-label">Name*</label>
                    <input type="text" class="form-control" name="user_name" placeholder="Name" value="<?= $user_name ?>" />
                </div>
            </div>
            <div class="form-group">
                <div class="mb-3">
                    <label for="user_email" class="form-label">Email*</label>
                    <input type="email" class="form-control" name="user_email" placeholder="id@example.com"  value="<?= $user_email ?>" />
                </div>
            </div>
            <div class="form-group">
                <div class="mb-3">
                    <button type="submit" class="btn btn-dark btn-lg" style="margin-top: 15px; width: 100%;">개인정보 변경</button>
                </div>
            </div>
        </div>
    </form>
</div>
