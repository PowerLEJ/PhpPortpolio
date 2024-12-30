<?php 
    /**
     * 주가 상세보기
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-11-24
     */

    // 메뉴 설정
    $activeMenuNum = 6;
    require_once __DIR__ . "/_navbar.php";

    // 데이터베이스에서 로봇 정보 가져오기
    $robot_id = isset($_GET['id']) ? $_GET['id'] : 0;
    $sql = "SELECT * FROM robot_info WHERE idx = '$robot_id'";
    $result = mysqli_query($conn, $sql);


    $robot = null;
    if ($result && mysqli_num_rows($result) > 0) {
        $robot = mysqli_fetch_assoc($result);
        $robot_idx = $robot['idx'];
    } else {
        die("로봇 정보를 찾을 수 없습니다.");
    }

    $time_range = isset($_GET['time_range']) ? $_GET['time_range'] : 'hour';
    $now = date('Y-m-d H:i:s');
    
    switch ($time_range) {
        case 'day':
            $interval = '1 DAY';
            $group_by = 'YEAR(reg_date), MONTH(reg_date), WEEK(reg_date), DATE(reg_date)';
            break;
        case 'week':
            $interval = '1 WEEK';
            $group_by = 'YEAR(reg_date), MONTH(reg_date), WEEK(reg_date)';
            break;
        case 'month':
            $interval = '1 MONTH';
            $group_by = 'YEAR(reg_date), MONTH(reg_date)';
            break;
        case 'hour':
        default:
            $interval = '1 HOUR';
            $group_by = 'YEAR(reg_date), MONTH(reg_date), WEEK(reg_date), DATE(reg_date), HOUR(reg_date)';
            break;
    }
    
    // SQL 쿼리 작성
    $sql = "SELECT reg_date, $group_by as period, AVG(robot_price) as avg_price 
            FROM robot_stock_prices 
            WHERE robot_idx = '$robot_id' 
            GROUP BY $group_by
            ORDER BY period ASC;";
    
    // SQL 실행
    $result = mysqli_query($conn, $sql);
    
    $chart_data = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $chart_data[] = [
            'reg_date' => $row['reg_date'],
            'period' => $row['period'],
            'robot_price' => (float)$row['avg_price']
        ];
    }
    
?>

<style>
    .robot-info-container {
        display: flex;
    }

    .first {
        flex:1;
        width:30%;
        box-sizing: border-box;
    }

    .second{
        flex:1;
        margin: 0px 5%;
        width:30%;
        box-sizing: border-box;
    }

    .third{
        flex:1;
        width:30%;
        height: 300px;
        box-sizing: border-box;
    }
</style>

<div class="container">
    <div class="row text-center">
        <h1>로봇 정보</h1>
    </div>

    <div class="container">
        <h1><?php echo htmlspecialchars($robot['robot_name']); ?></h1>

        <div style="text-align: right;">
            <p>
                <strong>상장일:</strong> <?php echo htmlspecialchars(date("Y-m-d", strtotime($robot['reg_date']))); ?>
            </p>
        </div>
        <hr />
        
        <div class="robot-info-container">
            <div class="first">
                <p>가격 : <?= htmlspecialchars($robot['robot_price']); ?></p>
                <p>재고 : <?= htmlspecialchars($robot['robot_left_count']); ?> / <?= htmlspecialchars($robot['robot_count']); ?></p>
                
                <form action="./home/request/robot_req.php" method="POST">
                    <input type="hidden" name="d0" value=<?= base64_encode($user_idx) ?> />
                    <input type="hidden" name="code" value="buy">
                    <input type="hidden" name="robot_idx" value=<?= $robot_idx ?> />
                    <input type="number" name="user_buy_count" class="form-control" required />
                    <button type="submit" class="btn btn-success" style="margin-top: 5px; width: 100%;" <?= empty($user_idx) ? "disabled" : ""; ?> >매수</button>
                </form>
                <br />

                <form action="./home/request/robot_req.php" method="POST">
                    <input type="hidden" name="d0" value=<?= base64_encode($user_idx) ?> />
                    <input type="hidden" name="code" value="sell">
                    <input type="hidden" name="robot_idx" value=<?= $robot_idx ?> />
                    <input type="number" name="user_sell_count" class="form-control" required />
                    <button type="submit" class="btn btn-danger" style="margin-top: 5px; width: 100%;" <?= empty($user_idx) ? "disabled" : ""; ?> >매도</button>
                </form>
                <br />

                <p>장소 : <?= htmlspecialchars($robot['robot_place']); ?></p>
                <p>내용 : <?php echo nl2br(htmlspecialchars($robot['robot_content'])); ?></p>
            </div>
            <div class="second">
                <div id="map" style="width: 350px; height: 350px;"></div>
            </div>

            <div class="third">
                <?php 
                    $uploadedFiles = json_decode($robot['uploaded_files'], true);
                    if (!empty($uploadedFiles)): 
                ?>
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <div id="imageSlider" class="position-relative text-center" style="cursor: pointer;">
                            <!-- 이전 버튼 -->
                            <span id="prevImage" class="slider-button slider-button-left">&lt;</span>

                            <!-- 현재 이미지 -->
                            <img id="currentImage" src="<?= htmlspecialchars($uploadedFiles[0]) ?>" 
                                alt="Program Image" 
                                style="max-width: 100%; max-height: 300px; object-fit: contain; margin-bottom: 10px;">

                            <!-- 다음 버튼 -->
                            <span id="nextImage" class="slider-button slider-button-right">&gt;</span>
                        </div>
                        <div  style="display: none;">
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
            </div>
        </div>

        <div>
            <button class="btn btn-warning" onclick="updateChart('<?= $robot_idx ?>', 'hour')">1시간</button>
            <button class="btn btn-warning" onclick="updateChart('<?= $robot_idx ?>', 'day')">날</button>
            <button class="btn btn-warning" onclick="updateChart('<?= $robot_idx ?>', 'week')">주</button>
            <button class="btn btn-warning" onclick="updateChart('<?= $robot_idx ?>', 'month')">달</button>
        </div>

        <div id="my_chart" style="width:100%; height: 400px;"></div>
        
        <hr />
        <div class="mt-4" style="text-align: right;">
            <a href="../index.php?url=trade_list" class="btn btn-secondary">목록보기</a>
            <?php if(9 == $user_level): ?>
            <a href="../index.php?url=trade_edit&id=<?php echo $robot_id; ?>&action=edit" class="btn btn-primary">수정</a>
            <button class="btn btn-danger" onclick="deleteRobot('<?php echo $robot_id; ?>')">삭제</button>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // 이미지 배열과 현재 인덱스를 초기화
    const uploadedFiles = <?= json_encode($uploadedFiles) ?>;
    let currentIndex = 0;


    // 카카오 맵 초기화
    var mapContainer = document.getElementById('map'); // 지도를 표시할 div
    var mapOptions = {
        center: new kakao.maps.LatLng(<?php echo $robot['latitude']; ?>, <?php echo $robot['longitude']; ?>), // 로봇 정보에 맞춰 기본 위치 설정
        level: 3 // 지도 확대 수준
    };
    var map = new kakao.maps.Map(mapContainer, mapOptions);

    var marker = new kakao.maps.Marker({
        position: new kakao.maps.LatLng(<?php echo $robot['latitude']; ?>, <?php echo $robot['longitude']; ?>)
    });
    marker.setMap(map);
</script>

<script src="/home/js/trade_info.js"></script>

<script>

    function updateChart(robot_idx, time_range) {
        window.location.href = window.location.pathname + '?url=trade_info&id=' + robot_idx + '&time_range=' + time_range;
    }

    var responseData = <?php echo json_encode(['time_range' => $time_range, 'data' => $chart_data]); ?>;
    var chartData = responseData.data;
    var timeRange = responseData.time_range;

    var chartDom = document.getElementById('my_chart');
    var myChart = echarts.init(chartDom);
    var option;

    // 차트 초기화
    function initChart() {
        option = {
            title: {
                text: '로봇 가격 변화'
            },
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data: ['로봇 가격']
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            toolbox: {
                feature: {
                    saveAsImage: {}
                }
            },
            dataZoom: [
                {
                    type: 'inside', // 내부 스크롤 방식 (마우스 스크롤로 줌)
                    xAxisIndex: [0], // x축에 대한 줌
                    yAxisIndex: [0], // y축에 대한 줌 추가
                    start: 0,
                    end: 100
                }
            ],
            xAxis: {
                type: 'category',
                boundaryGap: false,
                data: [] // 차트 데이터
            },
            yAxis: {
                type: 'value'
            },
            series: [
                {
                    name: '로봇 가격',
                    type: 'line',
                    stack: 'Total',
                    data: [], // 차트에 표시될 실제 데이터
                    smooth: true
                }
            ]
        };
        myChart.setOption(option);
    }

    window.onload = function() {
        initChart();
        var xAxisData = [];
        var seriesData = [];

        chartData.forEach(function(item) {
            var date = new Date(item.reg_date);

            if (timeRange === 'hour') {
                xAxisData.push(date.toISOString().slice(0, 16).replace('T', ' '));  // YYYY-MM-DD HH:MM
            } else {
                xAxisData.push(date.toISOString().slice(0, 10));  // YYYY-MM-DD
            }

            seriesData.push(item.robot_price);
        });

        myChart.setOption({
            xAxis: {
                data: xAxisData
            },
            yAxis: {
                min: Math.floor(Math.min(...seriesData)) - 100,
                max: Math.floor(Math.max(...seriesData)) + 100
            },
            series: [{
                data: seriesData
            }]
        });
    };

    window.addEventListener('resize', function() {
        myChart.resize();
    });

</script>