<?php 
    /**
     * 예약 리스트 화면
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-11-16
     */

    // 메뉴 설정
    $activeMenuNum = 7;
    require_once __DIR__ . "/_navbar.php";

    $type = ((isset($_GET['bt']) && !empty($_GET['bt'])) ? $_GET['bt'] : 0); // booking_list의 type (0: 프로그램, 1: 예약 내역)

    $searchOption = isset($_GET['search_option']) ? $_GET['search_option'] : '';
    $searchInput = isset($_GET['search_input']) ? $_GET['search_input'] : '';

    $startDate = isset($_GET['search_date1']) ? $_GET['search_date1'] : '';
    $endDate = isset($_GET['search_date2']) ? $_GET['search_date2'] : '';
    
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10; // 한 페이지에 보여줄 갯수
    $offset = ($page - 1) * $limit;

    $num_01 = $num_02 = 0;

    // 프로그램 목록 조회
    $sql = "SELECT * FROM booking_info WHERE del_check = 0";
    $result = mysqli_query($conn, $sql);

    // 프로그램 목록을 배열로 저장
    $programs = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $programs[] = $row;
    }
?>


<div class="container">
    <div class="row text-center">
        <h1>예약 리스트</h1>
    </div>

    <nav class="book-nav">
        <ul class="book-nav-list">
            <li class="book-nav-item <?= $type == 0 ? 'active' : '' ?>">
                <h3>
                    <a href="../index.php?url=booking_list&bt=0" class="book-nav-link" onclick="showTab('available_programs', this)">프로그램</a>
                </h3>
            </li>
            <?php if ($user_level == 1 or $user_level == 9): ?>
                <li class="book-nav-item <?= $type == 1 ? 'active' : '' ?>">
                    <h3>
                        <a href="../index.php?url=booking_list&bt=1" class="book-nav-link" onclick="showTab('user_bookings', this)">예약 내역</a>
                    </h3>
                </li>
            <?php endif; ?>
        </ul>
    </nav>


    <div id="about_search">
        <form action="index.php" method="GET">
            <input type="hidden" name="url" value="<?= $selectURI ?>" />
            <input type="hidden" name="bt" value="<?= $type ?>" />
            <div class="row">
                <?php if($user_level == 9 || $user_level == 1): ?>
                    <?php if($user_level == 9): ?>
                        <div class="mb-2 col-md-2">
                            <button type="button" class="btn btn-dark" style="width: 100%;" onclick="openAddProgramForm()">프로그램 등록</button>
                        </div>
                    <?php endif; ?>
                    <div class="mb-2 col-md-2">
                        <button type="button" class="btn btn-dark" style="width: 100%;" onclick="openBookDoProgramForm()">프로그램 예약</button>
                    </div>
                <?php endif; ?>
                <div class="mb-2 col-md-2">
                    <select name="search_option" class="form-select" onchange="this.form.submit()">
                        <option value="all">전체</option>
                        <option value="program_name" <?= ($searchOption == "program_name") ? " selected" : "" ?>>프로그램명</option>
                        <option value="user_count" <?= ($searchOption == "user_count") ? " selected" : "" ?>>인원</option>
                        <option value="date" <?= ($searchOption == "date") ? " selected" : "" ?>>일자</option>
                        <option value="time" <?= ($searchOption == "time") ? " selected" : "" ?>>시간</option>
                    </select>
                </div>
                <div class="mb-2 col-md-4">
                    <?php if ($searchOption == "date"): ?>
                        <div class="row">
                            <div class="col-md-5">
                                <input type="date" name="search_date1" class="form-control" value="<?= $startDate ? $startDate : ''?>" />
                            </div>
                            <div class="col-md-2 text-center">
                                ~
                            </div>
                            <div class="col-md-5">
                                <input type="date" name="search_date2" class="form-control" value="<?= $endDate ? $endDate : ''?>" />
                            </div>
                        </div>
                    <?php elseif ($searchOption == "time"): ?>
                        <input type="time" name="search_input" class="form-control" value="<?= $searchInput ? $searchInput : ''?>" />
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

    

    <div class="modal fade" id="addProgramModal" tabindex="-1" aria-labelledby="addProgramModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProgramModalLabel">관리자 프로그램 일정 등록</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="./home/request/booking_info_req.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3 row">
                            <div class="col-sm-6">
                                <label for="form_01" class="form-label">프로그램명*</label>
                                <input class="form-control" type="text" id="form_01" name="program_name">
                            </div>
                            <div class="col-sm-6">
                                <label for="form_02" class="form-label">장소*</label>
                                <input class="form-control" type="text" id="form_02" name="program_place">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-sm-12">
                                <label for="form_03" class="form-label">첨부 파일</label>
                                <input class="form-control" type="file" id="form_03" name="uploaded_files[]" multiple>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-sm-4">
                                <label for="form_04" class="form-label">인원*</label>
                                <input class="form-control" type="number" id="form_04" name="participant_count">
                            </div>
                            <div class="col-sm-4">
                                <label for="form_05" class="form-label">일자*</label>
                                <input class="form-control" type="date" id="form_05" name="program_date">
                            </div>
                            <div class="col-sm-4">
                                <label for="form_06" class="form-label">일시*</label>
                                <input class="form-control" type="time" id="form_06" name="program_time">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-sm-12">
                                <label for="form_07" class="form-label">프로그램 내용</label>
                                <input class="form-control" type="text" id="form_07" name="program_content">
                            </div>
                        </div>
                        <?php if ($user_level == 9): ?>
                            <div class="mb-3 row">
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-dark" style="margin-top: 5px; width: 100%;">저장</button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>


    

    <div class="modal fade" id="bookDoProgramModal" tabindex="-1" aria-labelledby="bookDoProgramModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookDoProgramModalLabel">프로그램 예약</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="./home/request/booking_do_req.php" method="POST">
                        <input type="hidden" name="d0" value=<?= base64_encode($user_idx) ?> />
                        <div class="mb-3 row">
                            <div class="col-sm-12">
                                <label for="program_idx" class="form-label">프로그램명*</label>
                                <select class="form-select" name="program_idx" id="program_idx" onchange="showProgramDetails()">
                                    <option value="">선택</option>
                                    <?php foreach ($programs as $program): ?>
                                        <option value="<?php echo $program['idx']; ?>"
                                                data-name="<?php echo $program['program_name']; ?>"
                                                data-date="<?php echo $program['program_date']; ?>"
                                                data-time="<?php echo $program['program_time']; ?>"
                                                data-place="<?php echo $program['program_place']; ?>"
                                                data-content="<?php echo $program['program_content']; ?>"
                                                data-participant="<?php echo $program['participant_count']; ?>"
                                                data-booking="<?php echo $program['booking_count']; ?>"
                                                data-images='<?php echo $program['uploaded_files']; ?>'
                                        >
                                            <?php echo $program['program_name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div id="program_details">
                            <div class="mb-3 row">
                                <div class="col-sm-4">
                                    <strong>일자:</strong> <span id="program_date"></span>
                                </div>
                                <div class="col-sm-4">
                                    <strong>일시:</strong> <span id="program_time"></span>
                                </div>
                                <div class="col-sm-4">
                                    <strong>인원:</strong> <span id="program_booking"></span> / <span id="program_participant"></span>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <div class="col-sm-12">
                                    <strong>장소:</strong> <span id="program_place"></span>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <div class="col-sm-12">
                                    <strong>내용:</strong> <span id="program_content"></span>
                                </div>
                            </div>
                            <div id="program_images" class="mb-3 row" style="margin-top: 10px;">

                                <div class="col-sm-12">
                                    <div id="imageSlider" class="position-relative text-center">
                                        <!-- 이전 버튼 -->
                                        <span id="prevImage" class="slider-button slider-button-left">&lt;</span>

                                        <!-- 현재 이미지 -->
                                        <img id="currentImage" 
                                            src="" 
                                            alt="Program Image" 
                                            style="width: 100%; max-height: 150px; object-fit: contain; margin-bottom: 10px; display: none;">

                                        <!-- 다음 버튼 -->
                                        <span id="nextImage" class="slider-button slider-button-right">&gt;</span>
                                    </div>
                                    
                                        <div id="thumbnails" class="d-flex flex-wrap mt-3" style="gap: 10px;"></div>
                                    
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <div class="col-sm-6">
                                <label for="form_08" class="form-label">인원*</label>
                                <input class="form-control" type="number" id="form_08" name="user_count">
                            </div>
                            <div class="col-sm-6">
                                <label for="form_09" class="form-label">전화번호*</label>
                                <input class="form-control" type="number" id="form_09" name="user_phone">
                            </div>
                        </div>
                        <?php if ($user_level == 1 || $user_level == 9): ?>
                        <div class="mb-3 row">
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-dark" style="margin-top: 5px; width: 100%;" onclick="return validateForm()">예약</button>
                            </div>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div id="available_programs" class="tab-content" style="display: <?= $type == 0 ? 'block' : 'none'; ?>;">
        <table class="table table-hover">
            <thead>
                <tr>
                    <?php if ($user_level == 9): ?>
                        <th>
                            <input type="checkbox" id="checkAll" onclick="toggleAllCheckboxes(this)" />
                        </th>
                    <?php endif; ?>
                    <th>No</th>
                    <th>프로그램명</th>
                    <th>일자</th>
                    <th>시간</th>
                    <th>인원</th>
                    <th>내용</th>
                    <th>예약</th>
                    <?php if ($user_level == 9): ?>
                        <th>수정</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                    if(0 == $type) {
                        $whereClause = "WHERE del_check = 0 AND program_date >= CURDATE()";

                        if ($searchOption) {

                            if($searchInput) {
                                switch ($searchOption) {
                                    case 'program_name':
                                        $whereClause .= " AND program_name LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%'";
                                        break;
                                    case 'user_count':
                                        $whereClause .= " AND participant_count LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%'";
                                        break;
                                    case 'date':
                                        
                                        if ($startDate && $endDate) {
                                            $whereClause .= " AND program_date BETWEEN '" . mysqli_real_escape_string($conn, $startDate) . "' AND '" . mysqli_real_escape_string($conn, $endDate) . "'";
                                        } else if ($startDate) {
                                            $whereClause .= " AND program_date >= '" . mysqli_real_escape_string($conn, $startDate) . "'";
                                        } else if ($endDate) {
                                            $whereClause .= " AND program_date <= '" . mysqli_real_escape_string($conn, $endDate) . "'";
                                        }
                                        break;
                                    case 'time':
                                        $whereClause .= " AND program_time LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%'";
                                        break;
                                    case 'all':
                                        $whereClause .= " AND (program_name LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%' 
                                            OR participant_count LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%' 
                                            OR program_date LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%' 
                                            OR program_time LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%')";
                                        break;
                                }
                            } else if($startDate || $endDate) {
                                switch ($searchOption) {
                                    case 'date':
                                        if ($startDate && $endDate) {
                                            $whereClause .= " AND program_date BETWEEN '" . mysqli_real_escape_string($conn, $startDate) . "' AND '" . mysqli_real_escape_string($conn, $endDate) . "'";
                                        } else if ($startDate) {
                                            $whereClause .= " AND program_date >= '" . mysqli_real_escape_string($conn, $startDate) . "'";
                                        } else if ($endDate) {
                                            $whereClause .= " AND program_date <= '" . mysqli_real_escape_string($conn, $endDate) . "'";
                                        }
                                        break;
                                }
                            }
                            
                        }
                    
                        // totalPages 구하기 위해
                        $countSql = "SELECT COUNT(*) FROM booking_info $whereClause";
                        $countResult = mysqli_query($conn, $countSql);
                        $totalRecords = mysqli_fetch_row($countResult)[0];
                        $totalPages = ceil($totalRecords / $limit);

                        $sql = "SELECT * FROM booking_info $whereClause ORDER BY program_date DESC LIMIT $offset, $limit";
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
                        <td><?= htmlspecialchars($row['program_name']) ?></td>
                        <td><?= htmlspecialchars($row['program_date']) ?></td>
                        <td><?= htmlspecialchars($row['program_time']) ?></td>
                        <td><?= htmlspecialchars($row['booking_count']) ?> / <?= htmlspecialchars($row['participant_count']) ?></td>
                        <td><?= htmlspecialchars($row['program_content']) ?></td>
                        <td>
                            <button class="btn btn-dark btn-sm" onclick="openBookingForm('<?= $rowId ?>')">예약</button>
                        </td>
                        <?php if ($user_level == 9): ?>
                            <td>
                                <button class="btn btn-dark btn-sm" onclick="openEditForm('<?= $rowId ?>')">수정</button>
                            </td>
                        <?php endif; ?>
                    </tr>






                    <div class="modal fade" id="bookProgramModal<?= $rowId ?>" tabindex="-1" aria-labelledby="bookProgramModalLabel<?= $rowId ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="bookProgramModalLabel<?= $rowId ?>"><?= htmlspecialchars($row['program_name']) ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">

                                    <div class="mb-3 row">
                                        <div class="col-sm-4">
                                            <strong>일자:</strong> <span><?= htmlspecialchars($row['program_date']) ?></span>
                                        </div>
                                        <div class="col-sm-4">
                                            <strong>일시:</strong> <span><?= htmlspecialchars($row['program_time']) ?></span>
                                        </div>
                                        <div class="col-sm-4">
                                            <strong>인원:</strong> <span><?= htmlspecialchars($row['booking_count']) ?></span> / <span><?= htmlspecialchars($row['participant_count']) ?></span>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <div class="col-sm-12">
                                            <strong>장소:</strong> <span><?= htmlspecialchars($row['program_place']) ?></span>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <div class="col-sm-12">
                                            <strong>내용:</strong> <span><?= htmlspecialchars($row['program_content']) ?></span>
                                        </div>
                                    </div>



                                    <?php 
                                        $uploadedFiles = json_decode($row['uploaded_files'], true);
                                        if (!empty($uploadedFiles)): 
                                    ?>
                                    <div class="mb-3 row">
                                        <div class="col-sm-12">
                                            <div id="imageSlider<?= $rowId ?>" class="position-relative text-center" style="cursor: pointer;">
                                                <!-- 이전 버튼 -->
                                                <span id="prevImage<?= $rowId ?>" class="slider-button slider-button-left">&lt;</span>

                                                <!-- 현재 이미지 -->
                                                <img class="currentImage" id="currentImage<?= $rowId ?>" src="<?= htmlspecialchars($uploadedFiles[0]) ?>" 
                                                    alt="Program Image" 
                                                    style="max-width: 100%; max-height: 300px; object-fit: contain; margin-bottom: 10px;">

                                                <!-- 다음 버튼 -->
                                                <span id="nextImage<?= $rowId ?>" class="slider-button slider-button-right">&gt;</span>
                                            </div>
                                            <div style="display: none;">
                                                <div class="d-flex flex-wrap mt-3">
                                                    <?php foreach ($uploadedFiles as $key => $file): ?>
                                                        <!-- 썸네일 -->
                                                        <img src="<?= htmlspecialchars($file) ?>" 
                                                            alt="Program Image" 
                                                            class="img-thumbnail me-2 thumbnail" 
                                                            style="width: 100px; height: 100px; object-fit: cover; cursor: pointer;" 
                                                            data-index="<?= $key ?>" 
                                                            data-src="<?= htmlspecialchars($file) ?>">
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <hr />

                                    <form action="./home/request/booking_list_req.php" method="post">
                                        <input type="hidden" name="action" value="book">
                                        <input type="hidden" name="d0" value="<?= base64_encode($user_idx) ?>">
                                        <input type="hidden" name="program_idx" value="<?= $rowId ?>">
                                        <div class="mb-3">
                                            <label for="user_count_<?= $rowId ?>" class="form-label">참여 인원*</label>
                                            <input type="number" name="user_count" id="user_count_<?= $rowId ?>" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="user_phone_<?= $rowId ?>" class="form-label">전화번호*</label>
                                            <input type="text" name="user_phone" id="user_phone_<?= $rowId ?>" class="form-control" required>
                                        </div>
                                        <?php if ($user_level == 1 || $user_level == 9): ?>
                                            <button type="submit" class="btn btn-dark" style="margin-top: 5px; width: 100%;">예약하기</button>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="editProgramModal<?= $rowId ?>" tabindex="-1" aria-labelledby="editProgramModalLabel<?= $rowId ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editProgramModalLabel<?= $rowId ?>">프로그램 수정</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="../home/request/booking_list_req.php" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="action" value="modify">
                                        <input type="hidden" name="program_idx" value="<?= $rowId ?>">
                                        <div class="mb-3">
                                            <label for="program_name_<?= $rowId ?>" class="form-label">프로그램명*</label>
                                            <input type="text" class="form-control" id="program_name_<?= $rowId ?>" name="program_name" value="<?= htmlspecialchars($row['program_name']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="program_date_<?= $rowId ?>" class="form-label">일자*</label>
                                            <input type="date" class="form-control" id="program_date_<?= $rowId ?>" name="program_date" value="<?= htmlspecialchars($row['program_date']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="program_time_<?= $rowId ?>" class="form-label">시간*</label>
                                            <input type="time" class="form-control" id="program_time_<?= $rowId ?>" name="program_time" value="<?= htmlspecialchars($row['program_time']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="participant_count_<?= $rowId ?>" class="form-label">최대 인원*</label>
                                            <input type="number" class="form-control" id="participant_count_<?= $rowId ?>" name="participant_count" value="<?= htmlspecialchars($row['participant_count']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="program_place_<?= $rowId ?>" class="form-label">프로그램 장소</label>
                                            <input type="text" class="form-control" id="program_place_<?= $rowId ?>" name="program_place" value="<?= htmlspecialchars($row['program_place']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="program_content_<?= $rowId ?>" class="form-label">프로그램 내용</label>
                                            <input type="text" class="form-control" id="program_content_<?= $rowId ?>" name="program_content" value="<?= htmlspecialchars($row['program_content']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="uploaded_files_<?= $rowId ?>" class="form-label">첨부 파일</label>
                                            <input class="form-control" type="file" id="uploaded_files_<?= $rowId ?>" name="uploaded_files[]" multiple>
                                        </div>
                                        <div class="mb-3">
                                            <?php
                                                // JSON 디코딩
                                                $files = json_decode($row['uploaded_files'], true) ?: [];

                                                // 파일 출력
                                                if (count($files) > 0) {
                                                    foreach ($files as $filePath) {
                                                        // 파일 이름 추출 (경로에서 마지막 '/' 뒤의 파일명만 가져옴)
                                                        $fileName = basename($filePath);

                                                        // 파일 출력
                                                        echo "$fileName 
                                                            <a href='#' style='color: red;' onclick=\"if(confirm('이 파일을 삭제하시겠습니까?')) { window.location.href = '../home/request/delete_file_req.php?program_idx={$rowId}&file_path=" . urlencode($filePath) . "'; } return false;\"> x </a><br />";
                                                    }
                                                }
                                            ?>
                                        </div>
                                        <?php if ($user_level == 9): ?>
                                            <button type="submit" class="btn btn-dark" style="margin-top: 5px; width: 100%;">수정하기</button>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>



    <div id="user_bookings" class="tab-content" style="display: <?= $type == 1 ? 'block' : 'none'; ?>;">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>프로그램명</th>
                    <th>일자</th>
                    <th>시간</th>
                    <th>예약 인원</th>
                    <th>전화번호</th>
                    <th>변경</th>
                    <th>취소</th>
                </tr>
            </thead>
            <tbody>
                <?php

                    if ($type == 1) {
                        $userIdx = $user_idx; // 사용자의 IDX
                    
                        $whereClause = "WHERE bl.user_idx = '$userIdx' AND bl.del_check = 0";
                    
                        if ($searchOption) {
                            if ($searchInput) {
                                switch ($searchOption) {
                                    case 'program_name':
                                        $whereClause .= " AND bi.program_name LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%'";
                                        break;
                                    case 'user_count':
                                        $whereClause .= " AND bl.user_count LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%'";
                                        break;
                                    case 'date':
                                        if ($startDate && $endDate) {
                                            $whereClause .= " AND bi.program_date BETWEEN '" . mysqli_real_escape_string($conn, $startDate) . "' AND '" . mysqli_real_escape_string($conn, $endDate) . "'";
                                        } elseif ($startDate) {
                                            $whereClause .= " AND bi.program_date >= '" . mysqli_real_escape_string($conn, $startDate) . "'";
                                        } elseif ($endDate) {
                                            $whereClause .= " AND bi.program_date <= '" . mysqli_real_escape_string($conn, $endDate) . "'";
                                        }
                                        break;
                                    case 'time':
                                        $whereClause .= " AND bi.program_time LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%'";
                                        break;
                                    case 'all':
                                        $whereClause .= " AND (bi.program_name LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%' 
                                                            OR bl.user_count LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%' 
                                                            OR bi.program_date LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%' 
                                                            OR bi.program_time LIKE '%" . mysqli_real_escape_string($conn, $searchInput) . "%')";
                                        break;
                                }
                            } elseif ($startDate || $endDate) {
                                if ($searchOption == 'date') {
                                    if ($startDate && $endDate) {
                                        $whereClause .= " AND bi.program_date BETWEEN '" . mysqli_real_escape_string($conn, $startDate) . "' AND '" . mysqli_real_escape_string($conn, $endDate) . "'";
                                    } elseif ($startDate) {
                                        $whereClause .= " AND bi.program_date >= '" . mysqli_real_escape_string($conn, $startDate) . "'";
                                    } elseif ($endDate) {
                                        $whereClause .= " AND bi.program_date <= '" . mysqli_real_escape_string($conn, $endDate) . "'";
                                    }
                                }
                            }
                        }
                    
                        // 전체 예약 내역 수 조회
                        $countSql = "SELECT COUNT(*) FROM booking_list bl 
                                        JOIN booking_info bi ON bl.program_idx = bi.idx 
                                        $whereClause";
                        $countResult = mysqli_query($conn, $countSql);
                        $totalRecords = mysqli_fetch_row($countResult)[0];
                        $totalPages = ceil($totalRecords / $limit);
                    
                        // 예약 내역 데이터 조회
                        $sql = "SELECT bl.idx AS booking_idx, 
                                        bi.program_name, bi.program_date, bi.program_time, bi.booking_count, bi.participant_count, bi.program_content, bi.program_place, bi.uploaded_files,
                                        bl.user_count, bl.user_phone, bl.cancel_check
                                FROM booking_list bl
                                JOIN booking_info bi ON bl.program_idx = bi.idx 
                                $whereClause
                                ORDER BY bl.cancel_check ASC, bi.program_date DESC 
                                LIMIT $offset, $limit";
                        $result = mysqli_query($conn, $sql);
                    }

                    while ($row = mysqli_fetch_assoc($result)):
                        $num_02++;
                ?>
                    <tr>
                        <td><?= $num_02; ?></td>
                        <td style="background-color: <?= htmlspecialchars($row['cancel_check']) == 1 ? 'gray' : '' ?>"><?= htmlspecialchars($row['program_name']) ?></td>
                        <td style="background-color: <?= htmlspecialchars($row['cancel_check']) == 1 ? 'gray' : '' ?>"><?= htmlspecialchars($row['program_date']) ?></td>
                        <td style="background-color: <?= htmlspecialchars($row['cancel_check']) == 1 ? 'gray' : '' ?>"><?= htmlspecialchars($row['program_time']) ?></td>
                        <td style="background-color: <?= htmlspecialchars($row['cancel_check']) == 1 ? 'gray' : '' ?>"><?= htmlspecialchars($row['user_count']) ?></td>
                        <td style="background-color: <?= htmlspecialchars($row['cancel_check']) == 1 ? 'gray' : '' ?>"><?= htmlspecialchars($row['user_phone']) ?></td>
                        <td style="background-color: <?= htmlspecialchars($row['cancel_check']) == 1 ? 'gray' : '' ?>">
                            <button class="btn btn-outline-dark btn-sm" onclick="openModifyBookForm('<?= $row['booking_idx'] ?>')" <?= htmlspecialchars($row['cancel_check']) == 1 ? 'disabled' : '' ?>>
                                변경
                            </button>
                        </td>
                        <td style="background-color: <?= htmlspecialchars($row['cancel_check']) == 1 ? 'gray' : '' ?>">
                            <button class="btn btn-secondary btn-sm" onclick="confirmCancel('<?= $row['booking_idx'] ?>')" <?= htmlspecialchars($row['cancel_check']) == 1 ? 'disabled' : '' ?>>
                                취소
                            </button>
                        </td>
                    </tr>

                    <div class="modal fade" id="bookModifyModal<?= $row['booking_idx'] ?>" tabindex="-1" aria-labelledby="bookModifyModalLabel<?= $row['booking_idx'] ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="bookModifyModalLabel<?= $row['booking_idx'] ?>">예약 변경</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">

                                    <div class="mb-3 row text-center">
                                        <div class="col-sm-12">
                                            <strong><span><?= htmlspecialchars($row['program_name']) ?></span></strong>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <div class="col-sm-4">
                                            <strong>일자:</strong> <span><?= htmlspecialchars($row['program_date']) ?></span>
                                        </div>
                                        <div class="col-sm-4">
                                            <strong>일시:</strong> <span><?= htmlspecialchars($row['program_time']) ?></span>
                                        </div>
                                        <div class="col-sm-4">
                                            <strong>인원:</strong> <span><?= htmlspecialchars($row['booking_count']) ?></span> / <span><?= htmlspecialchars($row['participant_count']) ?></span>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <div class="col-sm-12">
                                            <strong>장소:</strong> <span><?= htmlspecialchars($row['program_place']) ?></span>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <div class="col-sm-12">
                                            <strong>내용:</strong> <span><?= htmlspecialchars($row['program_content']) ?></span>
                                        </div>
                                    </div>

                                    <?php 
                                        $uploadedFiles = json_decode($row['uploaded_files'], true);
                                        if (!empty($uploadedFiles)): 
                                    ?>
                                    <div class="mb-3 row">
                                        <div class="col-sm-12">
                                            <div id="imageSlider<?= $row['booking_idx'] ?>" class="position-relative text-center" style="cursor: pointer;">
                                                <!-- 이전 버튼 -->
                                                <span id="prevImage<?= $row['booking_idx'] ?>" class="slider-button slider-button-left">&lt;</span>

                                                <!-- 현재 이미지 -->
                                                <img class="currentImage" id="currentImage<?= $row['booking_idx'] ?>" src="<?= htmlspecialchars($uploadedFiles[0]) ?>" 
                                                    alt="Program Image" 
                                                    style="max-width: 100%; max-height: 300px; object-fit: contain; margin-bottom: 10px;">

                                                <!-- 다음 버튼 -->
                                                <span id="nextImage<?= $row['booking_idx'] ?>" class="slider-button slider-button-right">&gt;</span>
                                            </div>
                                            <div style="display: none;">
                                                <div class="d-flex flex-wrap mt-3">
                                                    <?php foreach ($uploadedFiles as $key => $file): ?>
                                                        <!-- 썸네일 -->
                                                        <img src="<?= htmlspecialchars($file) ?>" 
                                                            alt="Program Image" 
                                                            class="img-thumbnail me-2 thumbnail" 
                                                            style="width: 100px; height: 100px; object-fit: cover; cursor: pointer;" 
                                                            data-index="<?= $key ?>" 
                                                            data-src="<?= htmlspecialchars($file) ?>">
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <hr />

                                    <form action="./home/request/booking_list_req.php" method="post">
                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" name="booking_idx" value="<?= $row['booking_idx'] ?>">
                                        <div class="mb-3">
                                            <label for="edit_user_count_<?= $row['booking_idx'] ?>" class="form-label">인원 수</label>
                                            <input type="number" name="user_count" id="edit_user_count_<?= $row['booking_idx'] ?>" class="form-control" value="<?= htmlspecialchars($row['user_count']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit_user_phone_<?= $row['booking_idx'] ?>" class="form-label">전화번호</label>
                                            <input type="text" name="user_phone" id="edit_user_phone_<?= $row['booking_idx'] ?>" class="form-control" value="<?= htmlspecialchars($row['user_phone']) ?>" required>
                                        </div>
                                        <button type="submit" class="btn btn-dark"  style="margin-top: 5px; width: 100%;">변경</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <?php if($type == 0 && $user_level == 9): ?>
        <div style="float: right;">
            <form id="deleteForm" action="./home/request/delete_req.php" method="POST">
                <input type="hidden" name="code" value="book_info">
                <button type="button" class="btn btn-outline-danger" onclick="submitDeleteForm()">삭제</button>
            </form>
        </div>
    <?php endif; ?>

    <nav>
        <ul class="pagination">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="index.php?url=booking_list&bt=<?= $type ?>&search_option=<?= $searchOption ?>&search_input=<?= $searchInput ?>&search_date1=<?= $startDate ?>&search_date2=<?= $endDate ?>&page=<?= $page - 1 ?>" aria-label="Previous">
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
                    <a class="page-link" href="index.php?url=booking_list&bt=<?= $type ?>&search_option=<?= $searchOption ?>&search_input=<?= $searchInput ?>&search_date1=<?= $startDate ?>&search_date2=<?= $endDate ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="index.php?url=booking_list&bt=<?= $type ?>&search_option=<?= $searchOption ?>&search_input=<?= $searchInput ?>&search_date1=<?= $startDate ?>&search_date2=<?= $endDate ?>&page=<?= $page + 1 ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>

</div>

<script src="/home/js/booking_list.js"></script>