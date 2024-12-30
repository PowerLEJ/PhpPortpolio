<?php
    /**
     * 이메일 인증 성공 후 정회원으로 업데이트
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-11-09
     */

    require_once "../../config.php";

    if (isset($_GET['d1'])) {
        $token = $_GET['d1'];

        // 토큰을 사용하여 사용자를 검색하고 인증 상태를 업데이트
        $sql = "SELECT * FROM user_info WHERE user_token = '$token';";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);

        if ($row) {
            $sql = sprintf("UPDATE user_info SET user_level = 1, user_token = NULL WHERE user_id = '%s';", $row['user_id']);
            $result = mysqli_query($conn, $sql);

            // 세션 시작
            session_start();

            // 세션 삭제
            session_unset();
            session_destroy();

            // 쿠키 삭제 (로그인 토큰 쿠키 삭제) 
            // time() - 3600 : 현재 시점보다 1시간 이전 시간으로 설정되어, 브라우저는 이 쿠키가 만료되었다고 인식하고 삭제
            setcookie('user_token', '', time() - 3600, "/", "", true, true);
            

            echo "<script>alert('이메일이 확인되었습니다. 로그인 해주세요.');</script>";
            echo "<script>location.replace('../../index.php?url=login');</script>";

        } else {
            echo "<script>alert('잘못된 접근입니다.[1]');</script>";
            echo "<script>location.replace('/');</script>";
        }
    } else {
        echo "<script>alert('잘못된 접근입니다.[2]');</script>";
        echo "<script>location.replace('/');</script>";
    }
?>