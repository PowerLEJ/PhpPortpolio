<?php
    /**
     * 회원가입
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-11-09
     */

    require_once "../../config.php";

    require_once "../function/sendVerificationEmail_01.php";
    //    require_once __DIR__ . "/function/sendVerificationEmail_01.php";

    require_once "../MyLibraries/CommonLibrary.php";
    $commLib = new \Power\CommonLibrary(); // 공통 라이브러리 클래스 생성


    /**
     * 회원 가입 시 조건
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

    if (empty($_POST["password"])) {
        echo '<script>alert("비밀번호를 입력하세요.");</script>';
        echo '<script>history.back();</script>';
        exit();
    }

    if (empty($_POST["password_confirmation"])) {
        echo '<script>alert("비밀번호 확인을 입력하세요.");</script>';
        echo '<script>history.back();</script>';
        exit();
    }

    if ( ! filter_var($_POST["user_email"], FILTER_VALIDATE_EMAIL)) {
        echo '<script>alert("이메일 형식을 맞춰주세요.");</script>';
        echo '<script>history.back();</script>';
        exit();
    }

    if (strlen($_POST["password"]) < 8) {
        echo '<script>alert("비밀번호는 8글자 이상입니다.");</script>';
        echo '<script>history.back();</script>';
        exit();
    }

    if ( ! preg_match("/[a-z]/i", $_POST["password"])) {
        echo '<script>alert("비밀번호는 문자를 포함해야 합니다.");</script>';
        echo '<script>history.back();</script>';
        exit();
    }

    if ( ! preg_match("/[0-9]/", $_POST["password"])) {
        echo '<script>alert("비밀번호는 숫자를 포함해야 합니다.");</script>';
        echo '<script>history.back();</script>';
        exit();
    }

    if ($_POST["password"] !== $_POST["password_confirmation"]) {
        echo '<script>alert("비밀번호는와 비밀번호 확인이 일치하지 않습니다.");</script>';
        echo '<script>history.back();</script>';
        exit();
    }

    /**
     * signup.php form post 처리
     */
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        $user_name = $_POST['user_name'];
        $user_id = $_POST['user_id'];
        $user_email = $_POST['user_email'];
        $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
        
        // 인증 토큰 생성
        $token = bin2hex(random_bytes(16));

        // ID 중복 유무 확인
        $sql = "SELECT count(*) AS cnt FROM user_info WHERE del_check = 0 AND user_id = '$user_id';";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        
        if(0 < $row['cnt']) {
            echo "<script>alert('이미 존재하는 이메일입니다.');</script>";
            echo '<script>history.back();</script>';
            exit();
        } else {
            $sql = sprintf("INSERT INTO user_info(idx, user_name, user_id, user_email, password, user_token) VALUES ('%s', '%s', '%s', '%s', '%s', '%s')", $commLib->generateRandomString(), $user_name, $user_id, $user_email, $password, $token);
            mysqli_query($conn, $sql);
    
            // 회원 정보가 DB에 성공적으로 저장되었을 때 이메일 발송
            if (sendVerificationEmail($user_email, $token)) {
                echo "<script>alert('발송된 이메일을 확인하면 회원가입이 완료됩니다.');</script>";
            }
        }

    }

    echo "<script>location.replace('/');</script>";

?>