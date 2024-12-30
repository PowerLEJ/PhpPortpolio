<?php
    require_once "../../config.php";

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // 기본값 설정
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $column_name = isset($_POST['column_name']) ? $_POST['column_name'] : 'reg_date';
    $order = isset($_POST['order']) ? $_POST['order'] : 'DESC';
    $query = isset($_POST['query']) ? $_POST['query'] : '';
    
    $records_per_page = 10;
    $start_from = ($page - 1) * $records_per_page;
    
    // 검색 조건 추가
    $search_condition = '';
    if (!empty($query)) {
        $search_condition = " AND title LIKE '%$query%' OR content LIKE '%$query%'";
    }
    
    // 데이터 가져오기
    $sql = "
        SELECT * FROM notice_list 
        WHERE del_check = 0 
        $search_condition 
        ORDER BY $column_name $order 
        LIMIT $start_from, $records_per_page
    ";
    $result = mysqli_query($conn, $sql);
    
    // 테이블 출력
    $output = '
    <table class="table table-hover">
        <tr>
            <th><input type="checkbox" id="check_all"></th>
            <th><a href="#" onclick="fetch_data(' . $page . ', \'title\', \'ASC\')">제목</a></th>
            <th><a href="#" onclick="fetch_data(' . $page . ', \'title\', \'ASC\')">내용</a></th>
            <th><a href="#" onclick="fetch_data(' . $page . ', \'reg_date\', \'DESC\')">작성일</a></th>
            <th><a href="#" onclick="fetch_data(' . $page . ', \'views\', \'DESC\')">조회수</a></th>
        </tr>
    ';

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $output .= '
            <tr>
                <td><input type="checkbox" class="row_checkbox" value="' . $row['idx'] . '"></td>
                <td><a href="index.php?url=notice_info&id=' . $row['idx'] . '">' . htmlspecialchars($row['title']) . '</a></td>
                <td><a href="index.php?url=notice_info&id=' . $row['idx'] . '">' . htmlspecialchars($row['content']) . '</a></td>
                <td>' . htmlspecialchars($row['reg_date']) . '</td>
                <td>' . htmlspecialchars($row['views']) . '</td>
            </tr>
            ';
        }
    } else {
        $output .= '<tr><td colspan="5">검색 결과가 없습니다.</td></tr>';
    }

    $output .= '</table>';

    
    // 페이지네이션
    $total_records_sql = "SELECT COUNT(*) AS total FROM notice_list WHERE del_check = 0 $search_condition";
    $total_records_result = mysqli_query($conn, $total_records_sql);
    $total_row = mysqli_fetch_assoc($total_records_result);
    $total_records = $total_row['total'];
    $total_pages = ceil($total_records / $records_per_page);

    $output .= '
    <nav>
        <ul class="pagination">
            ';

    // 이전 페이지 링크
    if ($page > 1) {
        $output .= '
        <li class="page-item">
            <a class="page-link" href="#" onclick="fetch_data(' . ($page - 1) . ', \'' . $column_name . '\', \'' . $order . '\', \'' . $query . '\')" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>';
    }

    // 페이지 버튼 보여줄 갯수
    $paginationRange = 3;
    $startPage = max(1, $page - floor($paginationRange / 2));
    $endPage = min($total_pages, $startPage + $paginationRange - 1);

    // 페이지 범위 조정
    if ($endPage - $startPage < $paginationRange - 1) {
        $startPage = max(1, $endPage - $paginationRange + 1);
    }

    // 페이지 번호 출력
    for ($i = $startPage; $i <= $endPage; $i++) {
        $output .= '
        <li class="page-item ' . ($i == $page ? 'active' : '') . '">
            <a class="page-link" href="#" onclick="fetch_data(' . $i . ', \'' . $column_name . '\', \'' . $order . '\', \'' . $query . '\')">' . $i . '</a>
        </li>';
    }

    // 다음 페이지 링크
    if ($page < $total_pages) {
        $output .= '
        <li class="page-item">
            <a class="page-link" href="#" onclick="fetch_data(' . ($page + 1) . ', \'' . $column_name . '\', \'' . $order . '\', \'' . $query . '\')" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>';
    }

    $output .= '
        </ul>
    </nav>';
    
    echo $output;
    
    // 데이터베이스 연결 종료
    mysqli_close($conn);
?>
    


    <script>

        $(document).ready(function () {
            // "전체 선택" 체크박스 동작
            $('#check_all').on('change', function () {
                $('.row_checkbox').prop('checked', $(this).prop('checked'));
            });

            // 선택 삭제 버튼 동작
            $('#delete_selected').on('click', function () {
                const selected = $('.row_checkbox:checked').map(function () {
                    return $(this).val();
                }).get();

                // 하나라도 선택되었으면 삭제 확인
                if (selected.length > 0) {
                    if (confirm('선택한 공지사항을 삭제하시겠습니까?')) {
                        $.ajax({
                            url: './home/request/delete_req.php',
                            method: 'POST',
                            data: { code: 'notice_list', ids: selected },
                            success: function (response) {
                                alert(response);
                                fetch_data(); // 테이블 새로고침
                            },
                            error: function () {
                                alert('삭제 중 오류가 발생했습니다.');
                            }
                        });
                    }
                } else {
                    alert('삭제할 공지사항을 선택하세요.');
                }
            });
        });

    </script>

