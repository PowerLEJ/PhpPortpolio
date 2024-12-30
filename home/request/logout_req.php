<?php
    /**
     * 로그아웃
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-11-09
     */

    // 세션 시작
    session_start();

    // 세션 삭제
    session_unset();
    session_destroy();

    // 쿠키 삭제 (로그인 토큰 쿠키 삭제) 
    // time() - 3600 : 현재 시점보다 1시간 이전 시간으로 설정되어, 브라우저는 이 쿠키가 만료되었다고 인식하고 삭제
    setcookie('user_token', '', time() - 3600, "/", "", true, true);

    // 로그아웃 후 리디렉션
    header("Location: /");
    exit();
?>