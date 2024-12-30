<?php 
    /**
     * 결제 화면
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-12-29
     */

    // 메뉴 설정
    $activeMenuNum = 9;
    require_once __DIR__ . "/_navbar.php";

    // 내 금액
    $a_sum = $cnt = 0;
    $sql = sprintf("SELECT count(*) AS cnt FROM payments WHERE user_idx = '%s' AND (payment_status = 1 OR trade_status IN (1, 2))", $user_idx);
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);
    $cnt = $row['cnt'];
    if($cnt > 0) {
        $sql = sprintf("SELECT SUM(amount) AS a_sum FROM payments WHERE user_idx = '%s' AND (payment_status = 1 OR trade_status IN (1, 2))", $user_idx);
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        $a_sum = $row['a_sum'];
    }

?>

<div class="container">
    <div class="row text-center">
        <h1>충전하기</h1>
    </div>

    <form method="POST" action="./home/request/payment_request.php">
        <input type="hidden" id="u_i" name="u_i" value="<?= base64_encode($user_idx) ?>">
        <input type="hidden" id="item_name" name="item_name" value="충전">
        <div class="row">
            <div class="form-group">
                <div class="mb-3">
                    <label for="a_sum" class="form-label">보유 금액</label>
                    <input type="number" class="form-control" id="a_sum" name="a_sum" value="<?= $a_sum ?>" readonly>
                </div>
            </div>
            <div class="form-group">
                <div class="mb-3">
                    <label for="total_amount" class="form-label">결제 금액 (원)</label>
                    <input type="number" class="form-control" id="total_amount" name="total_amount">
                </div>
            </div>
            
            <div class="form-group">
                <div class="mb-3">
                    <button type="submit" class="btn btn-dark btn-lg" style="margin-top: 15px; width: 100%;">충전하기</button>
                </div>
            </div>
        </div>
    </form>
</div>