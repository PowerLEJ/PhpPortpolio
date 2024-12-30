<?php
    /**
     * 관리자가 등록한 로봇에 대한 정보를 지도에 JSON으로 넘겨줌
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-11-23
     */

    require_once "../../config.php";

    $sql = sprintf("SELECT * FROM robot_info WHERE del_check = 0;");
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        http_response_code(500); // 서버 오류
        echo json_encode(["error" => "Database query failed."]);
        exit();
    }
    
    // 데이터를 저장할 배열 초기화
    $data = [];
    
    // 결과 데이터를 배열로 저장
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    // JSON 형식으로 결과 반환
    header('Content-Type: application/json');
    echo json_encode($data);
    
    exit();
?>
