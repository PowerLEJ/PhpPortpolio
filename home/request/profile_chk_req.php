<?php 
    /**
     * 마이페이지 접근 전 비밀번호 확인
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-11-09
     */

    require_once "../../config.php";

    /**
     * login.php form post 처리
     */
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $user_idx = base64_decode($_POST['d1']);
        $password = $_POST['d2'];

        $sql = "SELECT * FROM user_info WHERE del_check = 0 AND idx = '$user_idx';";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);

        // 사용자가 존재하고 비밀번호가 일치하는지 확인
        if ($row && password_verify($password, $row['password'])) {
            echo "<script>location.replace('../../index.php?url=profile');</script>";
        } else {
            // 로그인 실패
            echo '<script>alert("비밀번호가 일치하지 않습니다.");</script>';
            echo '<script>history.back();</script>';
            exit();
        }
        
    }
?>