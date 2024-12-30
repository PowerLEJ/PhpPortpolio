<?php
    /**
     * 개인정보 변경
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-11-09
     */

    require_once "../../config.php";

    /**
     * 개인정보 변경 시 조건
     */
    if (empty($_POST["user_name"])) {
        echo '<script>alert("이름을 입력하세요.");</script>';
        echo '<script>history.back();</script>';
        exit();
    }

    if (empty($_POST["user_id"])) {
        echo '<script>alert("아이디를 입력하세요.");</script>';
        echo '<script>history.back();</script>';
        exit();
    }

    if (empty($_POST["user_email"])) {
        echo '<script>alert("이메일을 입력하세요.");</script>';
        echo '<script>history.back();</script>';
        exit();
    }

    if (!filter_var($_POST["user_email"], FILTER_VALIDATE_EMAIL)) {
        echo '<script>alert("이메일 형식을 맞춰주세요.");</script>';
        echo '<script>history.back();</script>';
        exit();
    }

    if (strlen($_POST["password"]) > 0 && strlen($_POST["password_confirmation"]) > 0) {
        if (strlen($_POST["password"]) < 8) {
            echo '<script>alert("비밀번호는 8글자 이상입니다.");</script>';
            echo '<script>history.back();</script>';
            exit();
        }
    
        if (!preg_match("/[a-z]/i", $_POST["password"])) {
            echo '<script>alert("비밀번호는 문자를 포함해야 합니다.");</script>';
            echo '<script>history.back();</script>';
            exit();
        }
    
        if (!preg_match("/[0-9]/", $_POST["password"])) {
            echo '<script>alert("비밀번호는 숫자를 포함해야 합니다.");</script>';
            echo '<script>history.back();</script>';
            exit();
        }
    
        if ($_POST["password"] !== $_POST["password_confirmation"]) {
            echo '<script>alert("비밀번호는와 비밀번호 확인이 일치하지 않습니다.");</script>';
            echo '<script>history.back();</script>';
            exit();
        }
    }


    /**
     * signup.php form post 처리
     */
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $user_idx = base64_decode($_POST['d0']);
        $user_id = $_POST['user_id'];
        $user_name = $_POST['user_name'];
        $user_email = $_POST['user_email'];

        // ID 존재 확인
        $sql = "SELECT count(*) AS cnt FROM user_info WHERE del_check = 0 AND idx = '$user_idx';";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        
        if(1 == $row['cnt']) {

            if(0 < strlen($_POST["password"]) && 0 < strlen($_POST["password_confirmation"])) {

                $password = password_hash($_POST["password"], PASSWORD_BCRYPT);

                $sql = sprintf("UPDATE user_info SET user_name = '%s', user_email = '%s', password='%s' 
                                    WHERE idx = '%s';", $user_name, $user_email, $password, $user_idx);
                mysqli_query($conn, $sql);


                // 세션 시작
                session_start();

                // 세션 삭제
                session_unset();
                session_destroy();

                // 쿠키 삭제 (로그인 토큰 쿠키 삭제) 
                // time() - 3600 : 현재 시점보다 1시간 이전 시간으로 설정되어, 브라우저는 이 쿠키가 만료되었다고 인식하고 삭제
                setcookie('user_token', '', time() - 3600, "/", "", true, true);


                echo "<script>alert('비밀번호가 변경되어 로그아웃되었습니다.');</script>";
                echo "<script>location.replace('/');</script>";

            } else {
                $sql = sprintf("UPDATE user_info SET user_name = '%s', user_email = '%s' 
                                    WHERE idx = '%s';", $user_name, $user_email, $user_idx);
                mysqli_query($conn, $sql);
            }

            echo "<script>alert('변경되었습니다.');</script>";
            echo "<script>location.replace('/');</script>";

        } else {
            echo "<script>alert('잘못된 접근입니다.');</script>";
            echo '<script>history.back();</script>';
            exit();
        }

    }

    echo "<script>location.replace('/');</script>";

?>