<?php 
    /**
     * 공지사항 상세보기
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-11-17
     */

    // 메뉴 설정
    $activeMenuNum = 5;
    require_once __DIR__ . "/_navbar.php";

    // 데이터베이스에서 공지사항 가져오기
    $notice_id = isset($_GET['id']) ? $_GET['id'] : 0;
    $sql = "SELECT * FROM notice_list WHERE idx = '$notice_id'";
    $result = mysqli_query($conn, $sql);

    // 세션에 마지막 조회 시간 저장
    $current_time = time();

    // 조회수 증가를 위한 시간 제한 (24시간)
    $time_limit = 24 * 60 * 60; // 24시간 (초)

    // 세션에 공지사항을 조회한 시간 정보가 없다면 조회수 증가
    if (!isset($_SESSION['viewed_notices'][$notice_id])) {
        $_SESSION['viewed_notices'][$notice_id] = $current_time;
        // 조회수 증가 여부 확인
        $update_views_sql = "UPDATE notice_list SET views = views + 1 WHERE idx = '$notice_id'";
        mysqli_query($conn, $update_views_sql);
    } else {
        // 세션에 저장된 조회 시간을 확인
        $last_view_time = $_SESSION['viewed_notices'][$notice_id];
        
        if ($current_time - $last_view_time > $time_limit) {
            // 24시간 이상 지난 경우 조회수 증가
            $update_views_sql = "UPDATE notice_list SET views = views + 1 WHERE idx = '$notice_id'";
            mysqli_query($conn, $update_views_sql);

            // 세션에 새로운 조회 시간 저장
            $_SESSION['viewed_notices'][$notice_id] = $current_time;
        }
    }

    $notice = null;
    if ($result && mysqli_num_rows($result) > 0) {
        $notice = mysqli_fetch_assoc($result);
        $pub_idx = $notice['pub_idx'];
    } else {
        die("공지사항을 찾을 수 없습니다.");
    }

    // 첨부파일 가져오기
    $file_sql = "SELECT * FROM notice_attach WHERE notice_idx = '$notice_id';";

    $file_result = mysqli_query($conn, $file_sql);
    $files = [];
    if ($file_result && mysqli_num_rows($file_result) > 0) {
        while ($file = mysqli_fetch_assoc($file_result)) {
            $files[] = $file;
        }
    }
?>


<div class="container">
    <div class="row text-center">
        <h1>공지사항</h1>
    </div>

    <div class="container">
        <h1><?php echo htmlspecialchars($notice['title']); ?></h1>

        <div style="text-align: right;">
            <p>
                <strong>조회수:</strong> <?php echo htmlspecialchars($notice['views']); ?>
                &nbsp;&nbsp;
                <strong>작성일:</strong> <?php echo htmlspecialchars($notice['reg_date']); ?>
            </p>
        </div>
        <hr />
        <p><?php echo nl2br(htmlspecialchars($notice['content'])); ?></p>

        <?php if (!empty($files)): ?>
            <?php foreach ($files as $file): ?>
                <?php
                    $file_path = htmlspecialchars($file['file_path']);
                    $file_name = htmlspecialchars($file['file_name']);
                    $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                ?>
                <div class="mb-3">
                    <?php if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif'])): ?>
                        <img src="<?php echo $file_path; ?>" alt="<?php echo $file_name; ?>" style="max-width: 200px;">
                        <a href="<?php echo $file_path; ?>" download="<?php echo $file_name; ?>"><i class="bi bi-download"></i></a>
                    <?php else: ?>
                        <a href="<?php echo $file_path; ?>" download="<?php echo $file_name; ?>"><?php echo $file_name; ?><i class="bi bi-download"></i></a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <hr />
        <div class="mt-4" style="text-align: right;">
            <a href="../index.php?url=notice_list" class="btn btn-secondary">목록보기</a>
            <?php if(($pub_idx == $user_idx) && 9 == $user_level): ?>
            <a href="../index.php?url=notice_edit&id=<?php echo $notice_id; ?>&action=edit" class="btn btn-primary">수정</a>
            <button class="btn btn-danger" onclick="deleteNotice('<?php echo $notice_id; ?>')">삭제</button>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="/home/js/notice_list.js"></script>