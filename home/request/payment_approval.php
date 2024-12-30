<?php
    /**
     * 결제 성공
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-12-29
     */

    require_once "../../config.php";

    require_once "../MyLibraries/CommonLibrary.php";
    $commLib = new \Power\CommonLibrary(); // 공통 라이브러리 클래스 생성

    // 세션 시작
    session_start();

    // 결제 성공 후 리디렉션 URL에서 전달된 값 확인
    if (!isset($_GET['pg_token']) || empty($_GET['pg_token'])) {
        echo "<script>alert('필수 파라미터가 누락되었습니다.');</script>";
        exit;
    }

    // 전달된 파라미터 받기
    $pgToken = $_GET['pg_token'];  // 결제 토큰
    $tid = $_SESSION['tid'];  // 결제 거래 ID (세션에서 가져옴)

    if (!$tid) {
        echo "<script>alert('tid가 누락되었습니다.');</script>";
        exit;
    }

    // 세션에서 partner_order_id와 partner_user_id 가져오기
    $partnerOrderId = $_SESSION['partner_order_id'];
    $partnerUserId = $_SESSION['partner_user_id'];

    // 세션 값이 없을 경우 오류 처리
    if (!$partnerOrderId || !$partnerUserId) {
        echo "<script>alert('주문 ID 또는 사용자 ID가 누락되었습니다.');</script>";
        exit;
    }

    // 카카오페이 관리자 키 (카카오페이 개발자 콘솔에서 발급한 ADMIN_KEY)
    $adminKey = YOUR_KAKAO_ADMIN_KEY;  // 카카오페이의 ADMIN_KEY를 사용

    // 결제 승인 API 호출을 위한 파라미터 설정
    $apiUrl = 'https://kapi.kakao.com/v1/payment/approve';  // 결제 승인 API 엔드포인트

    $data = [
        'cid' => 'TC0ONETIME',  // 테스트용 가맹점 ID
        'tid' => $tid,  // 결제 거래 ID (세션에서 가져옴)
        'partner_order_id' => $partnerOrderId,  // 주문 ID
        'partner_user_id' => $partnerUserId,  // 사용자 ID
        'pg_token' => $pgToken  // 결제 토큰
    ];

    // cURL을 이용한 POST 요청
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: KakaoAK {$adminKey}",  // 카카오페이의 ADMIN_KEY
        "Content-Type: application/x-www-form-urlencoded"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

    // 응답 받기
    $response = curl_exec($ch);
    curl_close($ch);

    // 응답을 디버깅용으로 출력
    $responseObj = json_decode($response, true);

    // 결제 승인 결과 확인
    if ($responseObj === null) {
        // echo "API 응답 오류: " . $response;
        exit;
    }

    // 결제 승인 성공 시 처리
    if (isset($responseObj['msg'])) {
        // echo '결제 승인 실패: ' . $responseObj['msg'];  // 실패 메시지
    } else {
        // echo "결제 승인 성공: " . print_r($responseObj, true);

        // 결제 성공 시 DB에 결제 정보 저장
        try {
            // 결제 금액을 DB에서 가져오기 (예: 카카오페이 응답에서 금액 확인)
            $paymentAmount = $responseObj['amount']['total'];  // 결제 금액 (카카오페이 응답에서 확인)

            if($paymentAmount) { $status = 1; } // 결제 성공

            // 사용자 idx와 결제 금액을 저장하는 SQL 쿼리
            $sql = sprintf("INSERT INTO payments (idx, user_idx, amount, order_id, tid, payment_status) VALUES ('%s', '%s', '%s', '%s', '%s', '%s');", 
                                $commLib->generateRandomString(), $partnerUserId, $paymentAmount, $partnerOrderId, $tid, $status);

            $result = mysqli_query($conn, $sql);

            // 쿼리 실행
            if ($result) {
                echo "<script>alert('결제 정보가 성공적으로 완료되었습니다.'); location.href='../../index.php?url=payment';</script>";
            } else {
                echo "<script>alert('결제 정보 저장 실패'); location.href='../../index.php?url=payment';</script>";
            }

        } catch (Exception $e) {
            echo "<script>alert('예외 발생 " . $e->getMessage() . "'); location.href='../../index.php?url=payment';</script>";
        }
    }
?>
