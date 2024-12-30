<?php 
    /**
     * 홈 화면
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-11-09
     */

    // 메뉴 설정
    $activeMenuNum = 1;
    require_once __DIR__ . "/_navbar.php";

    $type = ((isset($_GET['dt']) && !empty($_GET['dt'])) ? $_GET['dt'] : 0);

    if((empty($user_idx) || $user_level != 9) && empty($_GET['dt'])) { $type = 1; }

    // 프로그램 목록 조회
    $sql = "SELECT * FROM robot_info WHERE del_check = 0";
    $result = mysqli_query($conn, $sql);

    // 프로그램 목록을 배열로 저장
    $robots = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $robots[] = $row;
    }
?>

<div id="map" style="width: 100%; height: 800px;"></div>


<div class="form-container" style="height: <?= $type == 0 ? '650px' : 'auto' ?>;">
    <h4>
        <nav class="book-nav">
            <ul class="book-nav-list">
                <li class="book-nav-item">
                    <a href="#" onclick="toggleZoom()"><i id="zoom-icon" class="bi bi-pin-fill"></i></a>
                </li>
                <?php if($user_level == 9): ?>
                <li class="book-nav-item <?= $type == 0 ? 'active' : '' ?>">
                    <a href="../index.php?dt=0" class="book-nav-link" onclick="showTab('robot_reg', this)">로봇 정보 등록</a>
                </li>
                <?php endif; ?>
                <li class="book-nav-item <?= $type == 1 ? 'active' : '' ?>">
                    <a href="../index.php?dt=1" class="book-nav-link" onclick="showTab('robot_buy', this)">로봇 매수</a>
                </li>

                <li class="book-nav-item <?= $type == 2 ? 'active' : '' ?>">
                    <a href="../index.php?dt=2" class="book-nav-link" onclick="showTab('robot_sell', this)">로봇 매도</a>
                </li>

            </ul>
        </nav>
    </h4>

    <div style="margin-top: 15px;"></div>

    <?php if(($type == 0) && ($user_level == 9)): ?>
        <div class="robot_reg" style="display: <?= $type == 0 ? 'block' : 'none'; ?>;">
            <form action="./home/request/robot_req.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="code" value="regist">
                <div class="mb-3 row">
                    <div class="col-sm-4">
                        <label for="form_01" class="form-label">로봇명*</label>
                        <input class="form-control" type="text" id="form_01" name="robot_name">
                    </div>
                    <div class="col-sm-4">
                        <label for="form_02" class="form-label">로봇 가격*</label>
                        <input class="form-control" type="text" id="form_02" name="robot_price">
                    </div>
                    <div class="col-sm-4">
                        <label for="form_03" class="form-label">로봇 갯수*</label>
                        <input class="form-control" type="text" id="form_03" name="robot_count">
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <label for="form_04" class="form-label">첨부 파일</label>
                        <input class="form-control" type="file" id="form_04" name="uploaded_files[]" multiple>
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <label for="form_05" class="form-label">로봇 설명</label>
                        <input class="form-control" type="text" id="form_05" name="robot_content">
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <label for="form_06" class="form-label">로봇 장소</label>
                        <input class="form-control" type="text" id="form_06" name="robot_place">
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-10">
                        <label for="venue_address" class="form-label">주소</label>
                        <input class="form-control" type="text" id="venue_address" name="venue_address">
                    </div>
                    <div class="col-sm-2" style="margin-top: 30px;">
                        <button type="button" class="btn btn-dark" onclick="searchAddress()" style="width: 100%;">찾기</button>
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-6">
                        <label for="latitude" class="form-label">위도*</label>
                        <input class="form-control" type="text" id="latitude" name="latitude">
                    </div>
                    <div class="col-sm-6">
                        <label for="longitude" class="form-label">경도*</label>
                        <input class="form-control" type="text" id="longitude" name="longitude">
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
        
    <?php elseif($type == 1): ?>
        <div class="robot_buy" style="display: <?= $type == 1 ? 'block' : 'none'; ?>;">
            <form action="./home/request/robot_req.php" method="POST">
                <input type="hidden" name="d0" value=<?= base64_encode($user_idx) ?> />
                <input type="hidden" name="code" value="buy">
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <label for="robot_idx" class="form-label">로봇명*</label>
                        <select class="form-select" name="robot_idx" id="robot_idx" onchange="showRobotDetails()">
                            <option value="">선택</option>
                            <?php foreach ($robots as $robot): ?>
                                <option value="<?php echo $robot['idx']; ?>"
                                        data-name="<?php echo $robot['robot_name']; ?>"
                                        data-count="<?php echo $robot['robot_count']; ?>"
                                        data-left_count="<?php echo $robot['robot_left_count']; ?>"
                                        data-price="<?php echo $robot['robot_price']; ?>"
                                        data-content="<?php echo $robot['robot_content']; ?>"
                                        data-place="<?php echo $robot['robot_place']; ?>"
                                        data-venue_address="<?php echo $robot['venue_address']; ?>"
                                        data-latitude="<?php echo $robot['latitude']; ?>"
                                        data-longitude="<?php echo $robot['longitude']; ?>"
                                        data-images='<?php echo $robot['uploaded_files']; ?>'
                                >
                                    <?php echo $robot['robot_name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <label for="form_08" class="form-label">갯수*</label>
                        <input class="form-control" type="number" id="form_08" name="user_buy_count">
                    </div>
                </div>
                <?php if ($user_level == 1 || $user_level == 9): ?>
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-dark" style="margin-top: 5px; width: 100%;">매수</button>
                    </div>
                </div>
                <?php endif; ?>
    
                <div id="robot_details">
                    <div class="mb-3 row">
                        <div class="col-sm-6">
                            <strong>가격:</strong> <span id="robot_price"></span>
                        </div>
                        <div class="col-sm-6">
                            <strong>재고:</strong> <span id="robot_left_count"></span> / <span id="robot_count"></span>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-sm-6">
                            <strong>내용:</strong> <span id="robot_content"></span>
                        </div>
                        <div class="col-sm-6">
                            <strong>장소:</strong> <span id="robot_place"></span>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <strong>주소:</strong> <span id="venue_address"></span>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-sm-6">
                            <strong>위도:</strong> <span id="latitude"></span>
                        </div>
                        <div class="col-sm-6">
                            <strong>경도:</strong> <span id="longitude"></span>
                        </div>
                    </div>
                    <div id="robot_images" class="mb-3 row" style="margin-top: 10px;">
                        <div class="col-sm-12">
                            <div id="imageSlider" class="position-relative text-center">
                                <!-- 이전 버튼 -->
                                <span id="prevImage" class="slider-button slider-button-left">&lt;</span>
    
                                <!-- 현재 이미지 -->
                                <img id="currentImage" 
                                    src="" 
                                    alt="robot Image" 
                                    style="width: 100%; max-height: 150px; object-fit: contain; margin-bottom: 10px; display: none;">
    
                                <!-- 다음 버튼 -->
                                <span id="nextImage" class="slider-button slider-button-right">&gt;</span>
                            </div>
                            
                                <div id="thumbnails" class="d-flex flex-wrap mt-3" style="gap: 10px;"></div>
                            
                        </div>
                    </div>
                </div>
    
                
            </form>

        </div>


    <?php elseif($type == 2): ?>
        <div class="robot_sell" style="display: <?= $type == 2 ? 'block' : 'none'; ?>;">
            <form action="./home/request/robot_req.php" method="POST">
                <input type="hidden" name="d0" value=<?= base64_encode($user_idx) ?> />
                <input type="hidden" name="code" value="sell">
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <label for="robot_idx" class="form-label">로봇명*</label>
                        <select class="form-select" name="robot_idx" id="robot_idx" onchange="showRobotDetails()">
                            <option value="">선택</option>
                            <?php foreach ($robots as $robot): ?>
                                <option value="<?php echo $robot['idx']; ?>"
                                        data-name="<?php echo $robot['robot_name']; ?>"
                                        data-count="<?php echo $robot['robot_count']; ?>"
                                        data-left_count="<?php echo $robot['robot_left_count']; ?>"
                                        data-price="<?php echo $robot['robot_price']; ?>"
                                        data-content="<?php echo $robot['robot_content']; ?>"
                                        data-place="<?php echo $robot['robot_place']; ?>"
                                        data-venue_address="<?php echo $robot['venue_address']; ?>"
                                        data-latitude="<?php echo $robot['latitude']; ?>"
                                        data-longitude="<?php echo $robot['longitude']; ?>"
                                        data-images='<?php echo $robot['uploaded_files']; ?>'
                                >
                                    <?php echo $robot['robot_name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <label for="form_10" class="form-label">갯수*</label>
                        <input class="form-control" type="number" id="form_10" name="user_sell_count">
                    </div>
                </div>
                <?php if ($user_level == 1 || $user_level == 9): ?>
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-dark" style="margin-top: 5px; width: 100%;">매도</button>
                    </div>
                </div>
                <?php endif; ?>
    
                <div id="robot_details">
                    <div class="mb-3 row">
                        <div class="col-sm-6">
                            <strong>가격:</strong> <span id="robot_price"></span>
                        </div>
                        <div class="col-sm-6">
                            <strong>재고:</strong> <span id="robot_left_count"></span> / <span id="robot_count"></span>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-sm-6">
                            <strong>내용:</strong> <span id="robot_content"></span>
                        </div>
                        <div class="col-sm-6">
                            <strong>장소:</strong> <span id="robot_place"></span>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <strong>주소:</strong> <span id="venue_address"></span>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-sm-6">
                            <strong>위도:</strong> <span id="latitude"></span>
                        </div>
                        <div class="col-sm-6">
                            <strong>경도:</strong> <span id="longitude"></span>
                        </div>
                    </div>
                    <div id="robot_images" class="mb-3 row" style="margin-top: 10px;">
                        <div class="col-sm-12">
                            <div id="imageSlider" class="position-relative text-center">
                                <!-- 이전 버튼 -->
                                <span id="prevImage" class="slider-button slider-button-left">&lt;</span>
    
                                <!-- 현재 이미지 -->
                                <img id="currentImage" 
                                    src="" 
                                    alt="robot Image" 
                                    style="width: 100%; max-height: 150px; object-fit: contain; margin-bottom: 10px; display: none;">
    
                                <!-- 다음 버튼 -->
                                <span id="nextImage" class="slider-button slider-button-right">&gt;</span>
                            </div>
                            <div id="thumbnails" class="d-flex flex-wrap mt-3" style="gap: 10px;"></div>
                        </div>
                    </div>
                </div>
            </form>        
        </div>
    <?php endif; ?>
</div>

<script src="/home/js/dashboard.js"></script>