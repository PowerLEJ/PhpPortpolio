<?php 
    /**
     * 로그인
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

        $user_id = $_POST['user_id'];
        $password = $_POST['password'];

        $sql = "SELECT * FROM user_info WHERE del_check = 0 AND user_id = '$user_id';";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);

        // 사용자가 존재하고 비밀번호가 일치하는지 확인
        if ($row && password_verify($password, $row['password'])) {

            // 세션 시작
            session_start();

            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['user_name'] = $row['user_name'];
            $_SESSION['user_email'] = $row['user_email'];

            // 세션 하이재킹 방지: 세션 ID 갱신
            session_regenerate_id(true);

            // 로그인 토큰 생성 (고유한 토큰)
            $token = bin2hex(random_bytes(32)); // 고유 토큰 생성

            // 토큰을 쿠키에 저장 (24시간 동안 유효) 60 * 60 * 24 * 1(일) = 86,400 [하루(24시간)]
            setcookie('user_token', $token, time() + 86400, "/", "", true, true);  // Secure 및 HttpOnly 옵션 설정

            // 사용자 입력값을 안전하게 처리
            $token = mysqli_real_escape_string($conn, $token);
            $user_id = mysqli_real_escape_string($conn, $user_id);
            
            // 쿠키에 저장된 토큰을 DB와 연결하여 로그인 유지 (토큰을 DB에 저장하여 추후 검증)
            $sql = "UPDATE user_info SET user_token = '$token' WHERE del_check = 0 AND user_id = '$user_id'";
            mysqli_query($conn, $sql);

            // 로그인 성공 후 리디렉션
            header("Location: /");
            exit();

        } else {
            // 로그인 실패
            echo '<script>alert("아이디 또는 비밀번호가 일치하지 않습니다.");</script>';
            echo '<script>history.back();</script>';
            exit();
        }
        
    }
?>