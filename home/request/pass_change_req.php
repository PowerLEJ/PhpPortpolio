<?php 
    /**
     * 비밀번호 변경
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-11-09
     */

    require_once "../../config.php";

    /**
     * 비밀번호 변경 시 조건
     */
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
     * pass_change.php form post 처리
     */
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $user_idx = base64_decode($_POST['d1']);
        $password = password_hash($_POST["password"], PASSWORD_BCRYPT);

        $sql = "SELECT * FROM user_info WHERE del_check = 0 AND idx = '$user_idx';";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);

        if ($row) {
            $sql = "UPDATE user_info SET password = '$password' WHERE del_check = 0 AND idx = '$user_idx'";
            mysqli_query($conn, $sql);

            // 비밀번호 변경 성공 후 로그인 페이지
            echo '<script>alert("비밀번호가 변경되었습니다.");</script>';
            echo "<script>location.replace('../../index.php?url=login');</script>";
        } else {
            // 비밀번호 변경 실패
            echo '<script>alert("비밀번호 변경에 실패하였습니다.");</script>';
            echo '<script>history.back();</script>';
            exit();
        }
        
    }
?>