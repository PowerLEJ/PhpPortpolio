<?php
    /**
     * 회원가입 시 조건 입력창 하단 AJAX Message
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-11-09
     */

    require_once "../../config.php";

    // 클라이언트에서 전달된 아이디 가져오기
    $userid = $_POST['userid'] ?? '';

    $response = ["exists" => false];

    // 아이디 중복 검사
    if (!empty($userid)) {

        $sql = "SELECT count(*) AS cnt FROM user_info WHERE del_check = 0 AND user_id = '$userid';";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);

        if ($row['cnt'] > 0) {
            $response["exists"] = true; // 아이디 중복
        }
    }

    // JSON 형식으로 결과 반환
    header('Content-Type: application/json');
    echo json_encode($response);

    exit();
?>
