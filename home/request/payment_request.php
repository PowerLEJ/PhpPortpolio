<?php
    /**
     * 결제 요청
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-12-29
     */

    require_once "../../config.php";

    // POST로 전달된 값 확인 (상품명, 결제 금액)
    if (!isset($_POST['item_name']) || !isset($_POST['total_amount']) || empty($_POST['item_name']) || empty($_POST['total_amount'])) {
        echo "상품명(item_name) 또는 결제 금액(total_amount)이 전달되지 않았습니다.";
        exit;
    }

    // 카카오 API 키 설정 (ADMIN_KEY로 수정)
    $adminKey = YOUR_KAKAO_ADMIN_KEY;  // 카카오페이의 ADMIN_KEY를 사용

    // 결제 정보
    $itemName = $_POST['item_name'];  // 결제 상품명
    $totalAmount = $_POST['total_amount'];  // 결제 금액
    $user_idx = base64_decode($_POST['u_i']); // 사용자 idx

    // 주문 번호와 사용자 ID 생성 (예: timestamp를 이용)
    $partnerOrderId = 'ORDER_' . time();  // 주문 ID (주문 번호)
    $partnerUserId = $user_idx;// 'USER_' . rand(1000, 9999);  // 사용자 ID (랜덤 사용자 ID)

    // 카카오 결제 API 엔드포인트
    $apiUrl = 'https://kapi.kakao.com/v1/payment/ready';  // 결제 준비 API 엔드포인트

    // 결제 요청에 필요한 파라미터
    $data = [
        'cid' => 'TC0ONETIME',  // 가맹점 ID (테스트용 가맹점 ID)
        'partner_order_id' => $partnerOrderId,  // 주문 ID
        'partner_user_id' => $partnerUserId,  // 사용자 ID
        'item_name' => $itemName,  // 상품명
        'quantity' => 1,  // 상품 수량
        'total_amount' => $totalAmount,  // 결제 금액
        'tax_free_amount' => 0,  // 면세 금액 (기본값 0)
        'approval_url' => 'http://localhost/home/request/payment_approval.php',  // 결제 성공 후 리디렉션 URL
        'fail_url' => 'http://localhost/index.php?url=payment',  // 결제 실패 후 리디렉션 URL
        'cancel_url' => 'http://localhost/index.php?url=payment',  // 결제 취소 후 리디렉션 URL
    ];

    // cURL을 이용한 POST 요청
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: KakaoAK {$adminKey}",  // ADMIN_KEY 사용
        "Content-Type: application/x-www-form-urlencoded"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

    // 응답 받기
    $response = curl_exec($ch);
    curl_close($ch);

    // 응답을 디버깅용으로 출력
    $responseObj = json_decode($response, true);

    // 응답 확인
    if ($responseObj === null) {
        echo "API 응답 오류: " . $response;
        exit;
    }

    // 결제 준비 성공시 결제 페이지로 리디렉션
    if (isset($responseObj['tid'])) {

        session_start();
        $_SESSION['tid'] = $responseObj['tid'];
        $_SESSION['partner_order_id'] = $partnerOrderId;
        $_SESSION['partner_user_id'] = $partnerUserId;
        
        // 결제 준비 성공
        // 카카오페이 결제 페이지로 리디렉션
        $redirectUrl = $responseObj['next_redirect_pc_url']; // PC용 URL(next_redirect_pc_url)을 사용 (모바일의 경우 next_redirect_mobile_url)
        header('Location: ' . $redirectUrl);
        exit;

    } else {
        // 결제 준비 실패
        if (isset($responseObj['msg'])) {
            echo '결제 준비 실패: ' . $responseObj['msg'];  // 오류 메시지
        } else {
            echo '결제 준비 실패: ' . print_r($responseObj, true); // 응답 전체 출력
        }
    }
?>
