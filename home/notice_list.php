<?php 
    /**
     * 공지사항 리스트 화면
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-11-17
     */

    // 메뉴 설정
    $activeMenuNum = 5;
    require_once __DIR__ . "/_navbar.php";

?>

<div class="container">
    <div class="row text-center">
        <h1>공지사항 리스트</h1>
    </div>
    <div id="about_search">
        <div class="row mb-2">
            <div class="mb-2 col-md-4">
                <input type="text" class="form-control" id="search_input" placeholder="검색어를 입력하세요" />
            </div>
            <div class="mb-2 col-md-1">
                <button type="button" id="search_button" class="btn btn-dark" style="width: 100%;">검색</button>
            </div>
        </div>

        <?php if(9 == $user_level): ?>
            <div class="row mb-2">
                <div class="mb-2 col-md-1">
                    <button id="delete_selected" class="btn btn-outline-danger" style="width: 100%;" title="공지사항 삭제">
                        <i class="bi bi-trash-fill"></i>
                    </button>
                </div>
                <div class="mb-2 col-md-1">
                    <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#addNoticeModal" style="width: 100%;" title="공지사항 등록">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                </div>
                <div class="mb-2 col-md-2"></div>
                <div class="mb-2 col-md-4">
                    <form action="./home/excel/upload_excel.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="d0" value="<?= base64_encode($user_idx) ?>">
                        <input type="file" name="excel_file" id="excel_file" class="form-control" required>
                    </div>
                <div class="mb-2 col-md-2" title="Excel Upload">
                        <button type="submit" class="btn btn-outline-primary" style="width: 100%;">
                            <i class="bi bi-file-earmark-excel-fill"></i>
                            &nbsp;Upload
                        </button>
                    </form>
                </div>
                <div class="mb-2 col-md-2" title="Excel Download">
                    <a href="./home/excel/download_excel.php" class="btn btn-outline-success" role="button" style="width: 100%;">
                        <i class="bi bi-file-earmark-excel-fill"></i>&nbsp;Download
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div id="notice_table"></div>

    <div class="modal fade" id="addNoticeModal" tabindex="-1" aria-labelledby="addNoticeLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addNoticeLabel">공지사항 등록</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="notice_form" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="code" value="notice_add">
                        <input type="hidden" name="d0" value="<?= base64_encode($user_idx) ?>">
                        <div class="mb-3">
                            <label for="title" class="form-label">제목</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">내용</label>
                            <textarea class="form-control" id="content" name="content" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="files" class="form-label">첨부파일</label>
                            <input type="file" class="form-control" id="files" name="files[]" multiple>
                        </div>
                        <button type="submit" class="btn btn-dark mt-2" style="width: 100%;">등록</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/home/js/notice_list.js"></script>