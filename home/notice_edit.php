<?php
// notice_edit.php
/**
 * 공지사항 수정하기
 * 
 * @author      LEJ <ej28power@naver.com>
 * @version     0.0.0.0
 * @since       2024-11-17
 */

// 메뉴 설정
$activeMenuNum = 5;
require_once __DIR__ . "/_navbar.php";

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    // 공지사항 정보를 가져오기
    $sql = "SELECT * FROM notice_list WHERE idx = '$id' AND del_check = 0";
    $result = mysqli_query($conn, $sql);
    $notice = mysqli_fetch_assoc($result);

    // 첨부파일 정보 가져오기
    $file_sql = "SELECT * FROM notice_attach WHERE notice_idx = '$id'";
    $file_result = mysqli_query($conn, $file_sql);
    $files = mysqli_fetch_all($file_result, MYSQLI_ASSOC);
} else {
    die("잘못된 접근입니다.");
}
?>

<div class="container">
    <div class="row text-center">
        <h1>공지사항 수정하기</h1>
    </div>
    
    <hr />

    <div class="container">
        <form action="./home/request/notice_req.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="code" value="notice_edit">
            <input type="hidden" name="notice_id" value="<?php echo $notice['idx']; ?>">
            
            <div class="form-group mb-3">
                <label for="title">제목</label>
                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($notice['title']); ?>" required>
            </div>
    
            <div class="form-group mb-3">
                <label for="content">내용</label>
                <textarea name="content" class="form-control" rows="5" required><?php echo htmlspecialchars($notice['content']); ?></textarea>
            </div>
    
            <div class="form-group mb-3">
                <label for="files">첨부파일</label>
                <input type="file" name="files[]" class="form-control" multiple>
            </div>
    
            <div class="mb-3">
                <?php if ($files): ?>
        
                        <?php foreach ($files as $file): ?>
                            <div class="mb-3">
                                <?php echo htmlspecialchars($file['file_name']); ?>
                                <button type="button" class="btn btn-danger btn-sm delete-file" data-file-id="<?php echo $file['idx']; ?>">X</button>
                            </div>
                        <?php endforeach; ?>
        
                <?php else: ?>
                    <p>현재 첨부된 파일이 없습니다.</p>
                <?php endif; ?>
            </div>
    
            <hr />
    
            <div style="text-align: right;">
                <button type="submit" class="btn btn-primary">수정</button>
            </div>
    
        </form>
    </div>

</div>

<script src="/home/js/notice_list.js"></script>