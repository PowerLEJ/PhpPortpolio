<?php 
    /**
     * 관리자 문의 리스트
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-12-28
     */

    // 메뉴 설정
    $activeMenuNum = 8;
    require_once __DIR__ . "/_navbar.php";

    $searchOption = isset($_GET['search_option']) ? $_GET['search_option'] : '';
    $searchInput = isset($_GET['search_input']) ? $_GET['search_input'] : '';

    $totalPages = 0;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10; // 한 페이지에 보여줄 갯수
    $offset = ($page - 1) * $limit;

    // 기본 카운트 쿼리
    $sql_count = "SELECT COUNT(DISTINCT cm.user_idx) AS total
                  FROM chat_messages cm
                  LEFT JOIN user_info ui ON cm.user_idx = ui.idx
                  WHERE cm.del_check = 0";

    if ($searchOption && $searchInput) {
        if ($searchOption == 'user_name') {
            $sql_count .= " AND ui.user_name LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%'";
        } elseif ($searchOption == 'user_msg') {
            $sql_count .= " AND cm.user_msg LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%'";
        } elseif ($searchOption == 'reg_date') {
            $sql_count .= " AND DATE(cm.reg_date) = '" . mysqli_real_escape_string($conn, $searchInput) . "'";
        }
    }

    // 총 레코드 수 조회
    $result_count = mysqli_query($conn, $sql_count);
    $row_count = mysqli_fetch_assoc($result_count);
    $totalRecords = $row_count['total'];

    // 총 페이지 수 계산
    $totalPages = ceil($totalRecords / $limit);

    $num_01 = 0;
?>

<style>
    .msg_need {
        background-color:rgb(204, 204, 204) !important;
    }
</style>

<div class="container">
    <div class="row text-center">
        <h1>문의 리스트</h1>
    </div>

    <div id="about_search">
        <form action="index.php" method="GET">
            <input type="hidden" name="url" value="<?= $selectURI ?>" />
            <div class="row">
                <div class="mb-2 col-md-2">
                    <select name="search_option" class="form-select" onchange="this.form.submit()">
                        <option value="all">전체</option>
                        <option value="user_name" <?= ($searchOption == "user_name") ? " selected" : "" ?>>이름</option>
                        <option value="user_msg" <?= ($searchOption == "user_msg") ? " selected" : "" ?>>내용</option>
                        <option value="reg_date" <?= ($searchOption == "reg_date") ? " selected" : "" ?>>등록일</option>
                    </select>
                </div>
                <div class="mb-2 col-md-4">
                    <?php if ($searchOption == "reg_date"): ?>
                        <input type="date" name="search_input" class="form-control" placeholder="검색어 입력" value="<?= $searchInput ? $searchInput : ''?>" />
                    <?php else: ?>
                        <input type="text" name="search_input" class="form-control" placeholder="검색어 입력" value="<?= $searchInput ? $searchInput : ''?>" />
                    <?php endif; ?>
                </div>
                <div class="mb-2 col-md-1">
                    <button type="submit" class="btn btn-dark" style="width: 100%;">검색</button>
                </div>
            </div>
        </form>
    </div>

    <div id="now_trade" class="tab-content">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th><input type="checkbox" id="checkAll" onclick="toggleAllCheckboxes(this)" /></th>
                    <th>No</th>
                    <th>이름</th>
                    <th>내용</th>
                    <th>등록일</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    // 검색 조건에 맞는 데이터 가져오기
                    $sql = "SELECT DISTINCT cm.user_idx
                            FROM chat_messages cm
                            LEFT JOIN user_info ui ON cm.user_idx = ui.idx
                            WHERE cm.del_check = 0";

                    if ($searchOption && $searchInput) {
                        if ($searchOption == 'user_name') {
                            $sql .= " AND ui.user_name LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%'";
                        } elseif ($searchOption == 'user_msg') {
                            $sql .= " AND cm.user_msg LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%'";
                        } elseif ($searchOption == 'reg_date') {
                            $sql .= " AND DATE(cm.reg_date) = '" . mysqli_real_escape_string($conn, $searchInput) . "'";
                        } elseif ($searchOption == 'all') {
                            $sql .= " AND (
                                        ui.user_name LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%'
                                        OR cm.user_msg LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%'
                                        OR DATE(cm.reg_date) = '" . mysqli_real_escape_string($conn, $searchInput) . "'
                                      )";
                        }
                    }

                    $sql .= " ORDER BY cm.reg_date DESC LIMIT $offset, $limit";

                    $result = mysqli_query($conn, $sql);
                    while ($row = mysqli_fetch_assoc($result)):
                        $chat_list_idx = $row['user_idx'];

                        // 최신 메시지 가져오기
                        $sql_chat = sprintf("SELECT admin_idx, user_msg, reg_date FROM chat_messages WHERE user_idx='%s' AND del_check=0 ORDER BY reg_date DESC LIMIT 1;", $chat_list_idx);
                        $result_chat = mysqli_query($conn, $sql_chat);
                        $row_chat = mysqli_fetch_assoc($result_chat);
                        $admin_idx = $row_chat['admin_idx'];
                        $user_msg = $row_chat['user_msg'];
                        $reg_date = $row_chat['reg_date'];

                        // 사용자 이름 가져오기
                        $sql_user = sprintf("SELECT user_name FROM user_info WHERE idx='%s' AND del_check=0;", $chat_list_idx);
                        $result_user = mysqli_query($conn, $sql_user);
                        $row_user = mysqli_fetch_assoc($result_user);
                        $chat_list_name = $row_user['user_name'];

                        $num_01++;
                ?>
                    <tr>
                        <td class="<?= isset($admin_idx) && '' != $admin_idx && null != $admin_idx ? '' : 'msg_need' ?>">
                            <input type="checkbox" class="row-checkbox" name="selected_ids[]" value="<?= $chat_list_idx ?>" />
                        </td>
                        <td class="<?= isset($admin_idx) && '' != $admin_idx && null != $admin_idx ? '' : 'msg_need' ?>"><?= $num_01; ?></td>
                        <td class="<?= isset($admin_idx) && '' != $admin_idx && null != $admin_idx ? '' : 'msg_need' ?>">
                            <a href="../index.php?url=chat_do&client=<?= $chat_list_idx ?>"><?= $chat_list_name; ?></a>
                        </td>
                        <td class="<?= isset($admin_idx) && '' != $admin_idx && null != $admin_idx ? '' : 'msg_need' ?>"><?= htmlspecialchars($user_msg) ?></td>
                        <td class="<?= isset($admin_idx) && '' != $admin_idx && null != $admin_idx ? '' : 'msg_need' ?>"><?= htmlspecialchars($reg_date) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div style="float: right;">
        <form id="deleteForm" action="./home/request/delete_req.php" method="POST">
            <input type="hidden" name="code" value="chat_info">
            <button type="button" class="btn btn-outline-danger" onclick="submitDeleteForm()">삭제</button>
        </form>
    </div>

    <nav>
        <ul class="pagination">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="index.php?url=chat_list&search_option=<?= $searchOption ?>&search_input=<?= $searchInput ?>&page=<?= $page - 1 ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php
                // 페이지 버튼 보여줄 갯수
                $paginationRange = 3;
                $startPage = max(1, $page - floor($paginationRange / 2));
                $endPage = min($totalPages, $startPage + $paginationRange - 1);

                // 페이지 범위 조정
                if ($endPage - $startPage < $paginationRange - 1) {
                    $startPage = max(1, $endPage - $paginationRange + 1);
                }

                for ($i = $startPage; $i <= $endPage; $i++):
            ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="index.php?url=chat_list&search_option=<?= $searchOption ?>&search_input=<?= $searchInput ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="index.php?url=chat_list&search_option=<?= $searchOption ?>&search_input=<?= $searchInput ?>&page=<?= $page + 1 ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<script src="/home/js/chat_list.js"></script>