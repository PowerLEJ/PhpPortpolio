<?php 
    /**
     * 매매 리스트 화면
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-11-16
     */

    // 메뉴 설정
    $activeMenuNum = 6;
    require_once __DIR__ . "/_navbar.php";

    $type = ((isset($_GET['tt']) && !empty($_GET['tt'])) ? $_GET['tt'] : 0); // trade_list의 type (0: 현재가, 1: 매매 내역, 2: 보유 내역)

    $searchOption = isset($_GET['search_option']) ? $_GET['search_option'] : '';
    $selectOption = isset($_GET['select_option']) ? $_GET['select_option'] : '';
    $searchInput = isset($_GET['search_input']) ? $_GET['search_input'] : '';
    
    $totalPages = 0;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10; // 한 페이지에 보여줄 갯수
    $offset = ($page - 1) * $limit;

    $num_01 = $num_02 = $num_03 = 0;

    // 프로그램 목록 조회
    $sql = "SELECT * FROM robot_info WHERE del_check = 0";
    $result = mysqli_query($conn, $sql);

    // 프로그램 목록을 배열로 저장
    $programs = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $programs[] = $row;
    }
?>


<div class="container">
    <div class="row text-center">
        <h1>매매 리스트</h1>
    </div>

    <nav class="book-nav">
        <ul class="book-nav-list">
            <li class="book-nav-item <?= $type == 0 ? 'active' : '' ?>">
                <h3>
                    <a href="../index.php?url=trade_list&tt=0" class="book-nav-link" onclick="showTab('now_trade', this)">현재가</a>
                </h3>
            </li>
            <?php if ($user_level == 1 or $user_level == 9): ?>
                <li class="book-nav-item <?= $type == 1 ? 'active' : '' ?>">
                    <h3>
                        <a href="../index.php?url=trade_list&tt=1" class="book-nav-link" onclick="showTab('my_trade', this)">매매 내역</a>
                    </h3>
                </li>
                <li class="book-nav-item <?= $type == 2 ? 'active' : '' ?>">
                    <h3>
                        <a href="../index.php?url=trade_list&tt=2" class="book-nav-link" onclick="showTab('my_trade', this)">보유 내역</a>
                    </h3>
                </li>
            <?php endif; ?>
        </ul>
    </nav>


    <div id="about_search">
        <form action="index.php" method="GET">
            <input type="hidden" name="url" value="<?= $selectURI ?>" />
            <input type="hidden" name="tt" value="<?= $type ?>" />
            <div class="row">
                <div class="mb-2 col-md-2">
                    <select name="search_option" class="form-select" onchange="this.form.submit()">
                        <option value="all">전체</option>
                        <option value="robot_name" <?= ($searchOption == "robot_name") ? " selected" : "" ?>>로봇명</option>
                        <?php 
                            if($type == 0 || $type == 1):
                        ?>
                            <option value="robot_price" <?= ($searchOption == "robot_price") ? " selected" : "" ?>>가격</option>
                            <option value="robot_count" <?= ($searchOption == "robot_count") ? " selected" : "" ?>>갯수</option>
                        <?php 
                            endif;    
                        ?>
                        <option value="reg_date" <?= ($searchOption == "reg_date") ? " selected" : "" ?>>상장일</option>
                    </select>
                </div>
                <?php if($type == 1): ?>
                <div class="mb-2 col-md-2">
                    <select name="select_option" class="form-select" onchange="this.form.submit()">
                        <option value="all">거래</option>
                        <option value="robot_buy" <?= ($selectOption == "robot_buy") ? " selected" : "" ?>>매수</option>
                        <option value="robot_sell" <?= ($selectOption == "robot_sell") ? " selected" : "" ?>>매도</option>
                    </select>
                </div>
                <?php endif; ?>
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



    <div id="now_trade" class="tab-content" style="display: <?= $type == 0 ? 'block' : 'none'; ?>;">
        <table class="table table-hover">
            <thead>
                <tr>
                    <?php if ($user_level == 9): ?>
                        <th>
                            <input type="checkbox" id="checkAll" onclick="toggleAllCheckboxes(this)" />
                        </th>
                    <?php endif; ?>
                    <th>No</th>
                    <th>로봇명</th>
                    <th>가격</th>
                    <th>갯수</th>
                    <th>장소</th>
                    <th>내용</th>
                    <th>상장일</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if(0 == $type) {
                        $whereClause = "WHERE del_check = 0 ";

                        if ($searchOption) {

                            if($searchInput) {
                                switch ($searchOption) {
                                    case 'robot_name':
                                        $whereClause .= " AND robot_name LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%'";
                                        break;
                                    case 'robot_price':
                                        $whereClause .= " AND robot_price LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%'";
                                        break;
                                    case 'robot_count':
                                        $whereClause .= " AND robot_count LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%'";
                                        break;
                                    case 'reg_date':
                                        $whereClause .= " AND reg_date LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%'";
                                        break;
                                    case 'all':
                                        $whereClause .= " AND (robot_name LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%' 
                                            OR robot_count LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%' 
                                            OR reg_date LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%' 
                                            OR robot_price LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%')";
                                        break;
                                }
                            }
                            
                        }
                    
                        // totalPages 구하기 위해
                        $countSql = "SELECT COUNT(*) FROM robot_info $whereClause";
                        $countResult = mysqli_query($conn, $countSql);
                        $totalRecords = mysqli_fetch_row($countResult)[0];
                        $totalPages = ceil($totalRecords / $limit);

                        $sql = "SELECT * FROM robot_info $whereClause ORDER BY reg_date DESC LIMIT $offset, $limit";
                        $result = mysqli_query($conn, $sql);
                    }


                    while ($row = mysqli_fetch_assoc($result)):
                        $rowId = htmlspecialchars($row['idx']);
                        $num_01++;
                ?>
                    <tr>
                        <?php if ($user_level == 9): ?>    
                            <td>
                                <input type="checkbox" class="row-checkbox" name="selected_ids[]" value="<?= $rowId ?>" />
                            </td>
                        <?php endif; ?>
                        <td><?= $num_01; ?></td>
                        <td><a href="index.php?url=trade_info&id=<?= $row['idx'] ?>"><?= htmlspecialchars($row['robot_name']) ?></a></td>
                        <td><?= htmlspecialchars($row['robot_price']) ?></td>
                        <td><?= htmlspecialchars($row['robot_left_count']) ?> / <?= htmlspecialchars($row['robot_count']) ?></td>
                        <td><?= htmlspecialchars($row['robot_place']) ?></td>
                        <td><?= htmlspecialchars($row['robot_content']) ?></td>
                        <td><?= htmlspecialchars($row['reg_date']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>



    <div id="my_trade" class="tab-content" style="display: <?= $type == 1 ? 'block' : 'none'; ?>;">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>로봇명</th>
                    <th>거래종류</th>
                    <th>거래단가</th>
                    <th>거래갯수</th>
                    <th>거래총액</th>
                    <th>거래일</th>
                </tr>
            </thead>
            <tbody>
                <?php

                    if ($type == 1) {
                        $userIdx = $user_idx; // 사용자의 IDX
                    
                        $whereClause = "WHERE rs.user_idx = '$userIdx' AND ri.del_check = 0 ";
                        $whereTrade = " ";
                    
                        if ($searchOption) {
                            if ($searchInput) {
                                switch ($searchOption) {
                                    case 'robot_name':
                                        $whereClause .= " AND ri.robot_name LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%'";
                                        break;
                                    case 'robot_price':
                                        $whereClause .= " AND rs.user_robot_price LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%'";
                                        break;
                                    case 'robot_count':
                                        $whereClause .= " AND rs.user_robot_count LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%'";
                                        break;
                                    case 'reg_date':
                                        $whereClause .= " AND ri.reg_date LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%'";
                                        break;
                                    case 'all':
                                        $whereClause .= " AND (ri.robot_name LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%' 
                                                            OR ri.robot_price LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%' 
                                                            OR ri.robot_count LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%' 
                                                            OR ri.reg_date LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%')";
                                        break;
                                }
                            }
                        }

                        if($selectOption) {
                            switch ($selectOption) {
                                case 'robot_buy':
                                    $whereTrade .= " AND rs.user_status = 0 ";
                                    break;
                                case 'robot_sell':
                                    $whereTrade .= " AND rs.user_status = 1 ";
                                    break;
                            }
                        }
                    
                        // 전체 매매 내역 수 조회
                        $countSql = "SELECT COUNT(*) FROM robot_stock rs 
                                        JOIN robot_info ri ON rs.robot_idx = ri.idx 
                                        $whereClause $whereTrade";
                        $countResult = mysqli_query($conn, $countSql);
                        $totalRecords = mysqli_fetch_row($countResult)[0];
                        $totalPages = ceil($totalRecords / $limit);
                    
                        // 매매 내역 데이터 조회
                        $sql = "SELECT 
                                rs.idx AS trade_idx,
                                ri.robot_name,
                                rs.user_trade_price,
                                rs.user_robot_price,
                                rs.user_robot_count,
                                rs.user_status,
                                rs.user_trade_time
                            FROM 
                                robot_stock rs
                            JOIN 
                                robot_info ri ON rs.robot_idx = ri.idx
                            $whereClause $whereTrade 
                            ORDER BY rs.user_trade_time DESC 
                            LIMIT $offset, $limit";
                        $result = mysqli_query($conn, $sql);

                    }

                    while ($row = mysqli_fetch_assoc($result)):
                        $num_02++;
                ?>
                    <tr>
                        <td style="color: <?= $row['user_status'] == 0 ? 'green' : 'red' ?>;"><?= $num_02; ?></td>
                        <td style="color: <?= $row['user_status'] == 0 ? 'green' : 'red' ?>;"><?= htmlspecialchars($row['robot_name']) ?></td>
                        <td style="color: <?= $row['user_status'] == 0 ? 'green' : 'red' ?>;"><?=  $row['user_status'] == 0 ? "매수" : "매도"; ?></td>
                        <td style="color: <?= $row['user_status'] == 0 ? 'green' : 'red' ?>;"><?= htmlspecialchars($row['user_robot_price']) ?></td>
                        <td style="color: <?= $row['user_status'] == 0 ? 'green' : 'red' ?>;"><?= htmlspecialchars($row['user_robot_count']) ?></td>
                        <td style="color: <?= $row['user_status'] == 0 ? 'green' : 'red' ?>;"><?= htmlspecialchars($row['user_trade_price']) ?></td>
                        <td style="color: <?= $row['user_status'] == 0 ? 'green' : 'red' ?>;"><?= htmlspecialchars($row['user_trade_time']) ?></td>
                        
                    </tr>

                <?php endwhile; ?>
            </tbody>
        </table>
    </div>



    <?php

        if ($type == 2) {
            $userIdx = $user_idx; // 사용자의 IDX
            
            // 검색 조건 추가
            $whereClause = ""; // 기본적으로 WHERE 절은 빈 상태로 시작

            if ($searchOption) {
                if ($searchInput) {
                    switch ($searchOption) {
                        case 'robot_name':
                            // 로봇 이름 검색
                            $whereClause .= " AND ri.robot_name LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%'";
                            break;
                        case 'reg_date':
                            // 등록일 검색
                            $whereClause .= " AND ri.reg_date LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%'";
                            break;
                        case 'all':
                            // 로봇 이름과 등록일을 모두 검색
                            $whereClause .= " AND (ri.robot_name LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%' 
                                                OR ri.reg_date LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%')";
                            break;
                    }
                }
            }
            
            // 사용자가 보유한 로봇들에 대해 집계 쿼리
            $sql = "
                SELECT 
                    rs.robot_idx,
                    ri.robot_name,
                    ri.robot_price,                        -- 로봇의 기본 가격
                    AVG(rs.user_robot_price) AS avg_robot_price,    -- 사용자 평균 로봇 가격
                    SUM(CASE WHEN rs.user_status = 0 THEN rs.user_robot_count ELSE -rs.user_robot_count END) AS total_robot_count,  -- user_status가 0일 때 더하고, 1일 때 빼기
                    SUM(CASE WHEN rs.user_status = 0 THEN rs.user_trade_price ELSE -rs.user_trade_price END) AS total_trade_price   -- user_status가 0일 때 더하고, 1일 때 빼기
                FROM 
                    robot_stock rs
                JOIN 
                    robot_info ri ON rs.robot_idx = ri.idx
                WHERE 
                    rs.user_idx = '$userIdx'
                    AND ri.del_check = 0
                    $whereClause  -- 검색 조건 추가
                GROUP BY 
                    rs.robot_idx, ri.robot_name, ri.robot_price
                ORDER BY 
                    ri.robot_name;  -- 로봇 이름 기준으로 정렬
            ";
            
            $result = mysqli_query($conn, $sql);
            $num_03 = 1; // 번호 시작
    ?>
            <div id="my_trade" class="tab-content" style="display: <?= $type == 2 ? 'block' : 'none'; ?>;">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>로봇명</th>
                            <th>평균매수가</th>
                            <th>보유수량</th>
                            <th>매수총액</th>
                            <th>평가금액</th>
                            <th>수익률</th>
                            <th>평가손익</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <?php 
                                if(number_format($row['total_robot_count']) > 0):
                                    // 수익률 계산: (robot_price - avg_robot_price) / avg_robot_price * 100
                                    $profit_margin = (($row['robot_price'] - $row['avg_robot_price']) / $row['avg_robot_price']) * 100;
                                    
                                    // 수익금액 계산: (robot_price - avg_robot_price) * total_robot_count
                                    $profit_amount = ($row['robot_price'] - $row['avg_robot_price']) * $row['total_robot_count'];
                            ?>
                                <tr>
                                    <td><?= $num_03++; ?></td>
                                    <td><?= htmlspecialchars($row['robot_name']) ?></td>
                                    <td><?= number_format($row['avg_robot_price'], 2) ?></td>
                                    <td><?= number_format($row['total_robot_count']) ?></td>
                                    <td><?= number_format($row['total_trade_price'], 2) ?></td>
                                    <td><?= number_format($row['total_trade_price'] + $profit_amount, 2) ?></td>
                                    <td>
                                        <?php 
                                        // 수익률이 음수일 경우 빨간색, 양수일 경우 녹색으로 표시
                                        if ($profit_margin < 0) {
                                            echo "<span style='color: red;'>".number_format($profit_margin, 2)."%</span>";
                                        } else {
                                            echo "<span style='color: green;'>+".number_format($profit_margin, 2)."%</span>";
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        // 수익금액 표시: 음수일 경우 빨간색, 양수일 경우 녹색
                                        if ($profit_amount < 0) {
                                            echo "<span style='color: red;'>".number_format($profit_amount, 2)."</span>";
                                        } else {
                                            echo "<span style='color: green;'>+".number_format($profit_amount, 2)."</span>";
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
    <?php
        }
    ?>



    <?php if($type == 0 && $user_level == 9): ?>
        <div style="float: right;">
            <form id="deleteForm" action="./home/request/delete_req.php" method="POST">
                <input type="hidden" name="code" value="robot_info">
                <button type="button" class="btn btn-outline-danger" onclick="submitDeleteForm()">삭제</button>
            </form>
        </div>
    <?php endif; ?>

    <nav>
        <ul class="pagination">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="index.php?url=trade_list&tt=<?= $type ?>&search_option=<?= $searchOption ?>&select_option=<?= $selectOption ?>&search_input=<?= $searchInput ?>&page=<?= $page - 1 ?>" aria-label="Previous">
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
                    <a class="page-link" href="index.php?url=trade_list&tt=<?= $type ?>&search_option=<?= $searchOption ?>&select_option=<?= $selectOption ?>&search_input=<?= $searchInput ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="index.php?url=trade_list&tt=<?= $type ?>&search_option=<?= $searchOption ?>&select_option=<?= $selectOption ?>&search_input=<?= $searchInput ?>&page=<?= $page + 1 ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>

</div>

<script src="/home/js/trade_list.js"></script>