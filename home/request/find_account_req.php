<?php
    /**
     * 계정 찾기
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-11-09
     */

   require_once "../../config.php";

   require_once "../function/sendVerificationEmail_02.php";

    /**
     * 계정 찾기 시 조건
     */
    if (empty($_POST["user_name"])) {
        echo '<script>alert("이름을 입력하세요.");</script>';
        echo '<script>history.back();</script>';
        exit();
    }
    
    if(2 == $_POST["d1"]) {
        if (empty($_POST["user_id"])) {
            echo '<script>alert("아이디를 입력하세요.");</script>';
            echo '<script>history.back();</script>';
            exit();
        }
    }

    if (empty($_POST["user_email"])) {
        echo '<script>alert("이메일을 입력하세요.");</script>';
        echo '<script>history.back();</script>';
        exit();
    }

    if ( ! filter_var($_POST["user_email"], FILTER_VALIDATE_EMAIL)) {
        echo '<script>alert("이메일 형식을 맞춰주세요.");</script>';
        echo '<script>history.back();</script>';
        exit();
    }

    /**
     * find_account.php form post 처리
     */
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $user_id = "";
        $find_type = $_POST['d1'];
        $user_name = $_POST['user_name'];
        if(2 == $find_type) {
            $user_id = $_POST['user_id'];
        }
        $user_email = $_POST['user_email'];
        
        $where = sprintf(" AND user_name = '%s' AND user_email = '%s';", $user_name, $user_email);

        if(2 == $find_type) {
            $where = sprintf(" AND user_name = '%s' AND user_email = '%s' AND user_id = '%s';", $user_name, $user_email, $user_id);
        }

        // 일치 여부 확인
        $sql = sprintf("SELECT count(*) AS cnt FROM user_info WHERE del_check = 0 %s", $where);
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        
        if(0 < $row['cnt']) {
            // 계정 찾기 이메일 발송
            if (sendVerificationEmail($conn, $user_name, $user_email, $user_id)) {
                echo "<script>alert('발송된 이메일을 확인하세요.');</script>";
            }
        } else {
            echo "<script>alert('존재하지 않는 정보입니다.');</script>";
            echo '<script>history.back();</script>';
            exit();
        }

    }

    echo "<script>location.replace('/');</script>";

?>