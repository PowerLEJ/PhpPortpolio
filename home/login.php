<?php 
    /**
     * 로그인 화면
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
        <h1>로그인</h1>
    </div>
    <form method="POST" action="./home/request/login_req.php">
        <div class="row">
            <div class="form-group">
                <div class="mb-3">
                    <label for="user_id" class="form-label">ID</label>
                    <input type="text" class="form-control" name="user_id" placeholder="ID">
                </div>
            </div>
            <div class="form-group">
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" placeholder="Password">
                </div>
            </div>
            <div class="form-group">
                <div class="mb-3">
                    <button type="submit" class="btn btn-dark btn-lg" style="margin-top: 15px; width: 100%;">로그인</button>
                    
                    <div class="text-center" style="padding: 20px;">
                        <a href="../index.php?url=find_account&type=1">아이디 찾기</a>
                        &nbsp;|&nbsp;
                        <a href="../index.php?url=find_account&type=2">비밀번호 찾기</a>
                        &nbsp;|&nbsp;
                        <a href="../index.php?url=signup">회원가입</a>
                    </div>

                </div>
            </div>
        </div>
    </form>
</div>
