<?php 
    /**
     * 회원가입 화면
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-11-09
     */

    // 메뉴 설정
    $activeMenuNum = 2;
    require_once __DIR__ . "/_navbar.php";
?>

<div class="container">
    <div class="row text-center">
        <h1>회원가입</h1>
    </div>
    <form method="POST" action="./home/request/signup_req.php">
        <div class="row">
            <div class="form-group">
                <div class="mb-3">
                    <label for="user_id" class="form-label">ID*</label>
                    <input type="text" class="form-control" id="s_user_id" name="user_id" placeholder="ID" autocomplete="off"  onkeyup="checkUserId()" />
                    <p class="mt-2" id="message"></p>
                </div>
            </div>
            <div class="form-group">
                <div class="mb-3">
                    <label for="password" class="form-label">Password*</label>
                    <input type="password" class="form-control" name="password" placeholder="Password" autocomplete="off" />
                </div>
            </div>
            <div class="form-group">
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password*</label>
                    <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" autocomplete="off" />
                </div>
            </div>
            <div class="form-group">
                <div class="mb-3">
                    <label for="user_name" class="form-label">Name*</label>
                    <input type="text" class="form-control" name="user_name" placeholder="Name" autocomplete="off" />
                </div>
            </div>
            <div class="form-group">
                <div class="mb-3">
                    <label for="user_email" class="form-label">Email*</label>
                    <input type="email" class="form-control" name="user_email" placeholder="id@example.com" autocomplete="off" />
                </div>
            </div>
            <div class="form-group">
                <div class="mb-3">
                    <button type="submit" class="btn btn-dark btn-lg" style="margin-top: 15px; width: 100%;">회원가입</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="/home/js/signup.js"></script>