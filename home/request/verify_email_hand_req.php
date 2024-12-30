<?php
    /**
     * 마이페이지를 통한 이메일 인증 메일 전송
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-11-09
     */

   require_once "../../config.php";

   require_once "../function/sendVerificationEmail_01.php";

    /**
     * profile.php > 이메일 확인 버튼 form post 처리
     */
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $user_email = base64_decode($_POST['d1']);
        $user_token = base64_decode($_POST['d2']);
        
        if (sendVerificationEmail($user_email, $user_token)) {
            
            // 세션 시작
            session_start();
            
            // 세션 삭제
            session_unset();
            session_destroy();
            
            // 쿠키 삭제 (로그인 토큰 쿠키 삭제) 
            // time() - 3600 : 현재 시점보다 1시간 이전 시간으로 설정되어, 브라우저는 이 쿠키가 만료되었다고 인식하고 삭제
            setcookie('user_token', '', time() - 3600, "/", "", true, true);

            echo "<script>alert('발송된 이메일을 확인하면 처리가 완료됩니다.\n이메일 확인 후 다시 로그인 해주세요.');</script>";
        }

    }

    echo "<script>location.replace('/');</script>";

?>