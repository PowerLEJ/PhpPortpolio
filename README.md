## 버전 정보  

```
>> php --version
PHP 8.3.13 (cli) (built: Oct 22 2024 18:39:14) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.3.13, Copyright (c) Zend Technologies
    with Zend OPcache v8.3.13, Copyright (c), by Zend Technologies
```  

```
>> mariadb --version
mariadb from 11.5.2-MariaDB, client 15.2 for osx10.19 (arm64) using  EditLine wrapper
```  

```
>> composer --version
Composer version 2.8.2 2024-10-29 16:12:11
PHP version 8.3.13 (/opt/homebrew/Cellar/php/8.3.13_1/bin/php)
Run the "diagnose" command to get more detailed diagnostics output.
```  

## 설치  
```
composer require phpoffice/phpspreadsheet
composer require cboden/ratchet
```  

## config.php 설정  
```
    // DB
    define('MYSQL_HOST', '');
    define('MYSQL_USER', '');
    define('MYSQL_PASSWORD', '');
    define('MYSQL_DB', '');
    define('MYSQL_PORT', '');
    define('MYSQL_CHARSET', 'utf8mb4');

    $conn = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB, MYSQL_PORT);

    // PHPMailer
    define('SMTP_USER', '');
    define('SMTP_PASS', '');
    define('WEB_SITE', '');

    // developers.kakao.com JavaScript Key (Kakao Map API)
    define('KAKAO_MAP_API_KEY', '');

    // developers.kakao.com Admin Key (KakaoPay API)
    define('YOUR_KAKAO_ADMIN_KEY', '');

    // OpenWeatherMap API
    define('WEATHER_MAP_API_KEY', '');
```  

## 웹소켓 서버 실행  
```
php -e chat/server.php
```  

## OpenWeatherMap API json 결과 예시  
### 무료 플랜에서는 하루에 최대 1,000회 API 호출 가능  
```json
{
    "coord": {
        "lon": 126.9788,
        "lat": 37.5674
    },
    "weather": [
        {
            "id": 802,
            "main": "Clouds",
            "description": "scattered clouds",
            "icon": "03d"
        }
    ],
    "base": "stations",
    "main": {
        "temp": 0.89,
        "feels_like": -3.59,
        "temp_min": -0.31,
        "temp_max": 1.66,
        "pressure": 1024,
        "humidity": 39,
        "sea_level": 1024,
        "grnd_level": 1017
    },
    "visibility": 10000,
    "wind": {
        "speed": 4.63,
        "deg": 270
    },
    "clouds": {
        "all": 40
    },
    "dt": 1735284653,
    "sys": {
        "type": 1,
        "id": 8105,
        "country": "KR",
        "sunrise": 1735253141,
        "sunset": 1735287621
    },
    "timezone": 32400,
    "id": 1835848,
    "name": "Seoul",
    "cod": 200
}
```  

# About My Project Content  

로봇과 관련된 컨텐츠가 모인 웹 사이트다.  
관리자가 실제 로봇을 지도에 등록하고, 사용자는 포인트를 결제한 후 로봇을 매수, 매도할 수 있다.  
지도에서 원하는 위치에 있는 로봇 정보를 볼 수 있다.  
예약 페이지에서는 다양한 프로그램을 신청할 수 있다.  
로봇이 실제로 있는 지역에 방문하여 체험한 후 매매할 수 있는 등의 체험 프로그램이 있는 컨셉이다.  
로봇의 가격은 매수자와 매도자의 가치 거래가로 이루어지는 것이 아니라 날씨 정보를 참고한 랜덤 계산으로 1시간마다 가격이 변동되는 알고리즘으로 구성했다.  
관리자는 공지사항에 글을 직접 작성할 수도 있고 엑셀로 한 번에 등록할 수도 있고 글의 내용을 한 번에 내려받기도 가능하다.  
사용자는 채팅을 통해 문의가 가능하고 관리자는 답장해야 할 리스트를 한 눈에 볼 수 있다.  
회원가입은 이메일 인증 후에 가입이 완료되고, 로그인 전후의 사용 가능 기능에 차별이 있다.  
아이디, 비빌번호 찾기도 이메일 인증을 통해 가능하다.  
마이페이지에서 비밀번호 일치 후 계정 정보를 수정할 수 있다.  

## 로그인 전 홈화면  
![001](/md_img/001.png){: width="100%" height="100%"}{: .center}  

## 사용자 로그인 후 홈화면  
![018](/md_img/018.png){: width="100%" height="100%"}{: .center}  

## 관리자 로그인 후 홈화면  
![002](/md_img/002.png){: width="100%" height="100%"}{: .center}  

## 관리자가 보는 공지사항  

### notice_list.php의 일부  
```html
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
```  

### download_excel.php
```php
<?php
    require_once "../../config.php";

    require_once '../../vendor/autoload.php'; // PhpSpreadsheet 라이브러리 로드

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // 데이터 가져오기
    $sql = "SELECT * FROM notice_list WHERE del_check = 0";
    $result = mysqli_query($conn, $sql);

    // 엑셀 시트 생성
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', '제목');
    $sheet->setCellValue('B1', '작성일');
    $sheet->setCellValue('C1', '조회수');
    $sheet->setCellValue('D1', '내용');

    $rowNum = 2;
    while ($row = mysqli_fetch_assoc($result)) {
        $sheet->setCellValue('A' . $rowNum, $row['title']);
        $sheet->setCellValue('B' . $rowNum, $row['reg_date']);
        $sheet->setCellValue('C' . $rowNum, $row['views']);
        $sheet->setCellValue('D' . $rowNum, $row['content']);
        $rowNum++;
    }

    // 파일 다운로드
    $writer = new Xlsx($spreadsheet);


    $filename = "notice_list_" . date('YmdHis') . ".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
    exit;


    // 데이터베이스 연결 종료
    mysqli_close($conn);
?>
```  

### upload_excel.php  
```php
<?php
    require_once "../../config.php";

    require_once "../MyLibraries/CommonLibrary.php";
    $commLib = new \Power\CommonLibrary(); // 공통 라이브러리 클래스 생성

    require_once "../../vendor/autoload.php"; // PhpSpreadsheet 로드

    use PhpOffice\PhpSpreadsheet\IOFactory;
    use PhpOffice\PhpSpreadsheet\Spreadsheet;

    // 파일이 업로드 되었는지 확인
    if ($_FILES['excel_file']['tmp_name']) {
        $file = $_FILES['excel_file']['tmp_name'];

        // 파일을 로드하여 Spreadsheet 객체로 변환
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();

        // 시트에서 데이터 읽기
        foreach ($sheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $data = [];
            foreach ($cellIterator as $cell) {
                $data[] = $cell->getValue();
            }

            // 첫 번째 행은 헤더이므로 스킵
            if ($row->getRowIndex() > 1) {
                $title = $data[0]; // 제목
                $reg_date = $data[1]; // 작성일
                $views = $data[2]; // 조회수
                $content = $data[3]; // 내용
                $pub_idx = base64_decode($_POST['d0']);

                $idx = $commLib->generateRandomString();

                $sql = "INSERT INTO notice_list (idx, pub_idx, title, views, content) 
                        VALUES ('$idx', '$pub_idx', '$title', '$views', '$content');";

                mysqli_query($conn, $sql);
            }
        }

        // 데이터베이스 연결 종료
        mysqli_close($conn);

        echo "엑셀 파일이 성공적으로 업로드되었습니다.";
    }
?>
```  

![003](/md_img/003.png){: width="100%" height="100%"}{: .center}  

## 사용자가 보는 공지사항  
![019](/md_img/019.png){: width="100%" height="100%"}{: .center}  

## 매매 화면  
![004](/md_img/004.png){: width="100%" height="100%"}{: .center}  

### trade_list.php의 일부  
```php
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
```  

![005](/md_img/005.png){: width="100%" height="100%"}{: .center}  
![006](/md_img/006.png){: width="100%" height="100%"}{: .center}  

### echart의 일부  
```js
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
```  
![007](/md_img/007.png){: width="100%" height="100%"}{: .center}  

## 관리자 로그인 시 예약 화면  
![008](/md_img/008.png){: width="100%" height="100%"}{: .center}  

## 사용자 로그인 시 예약 화면  
![009](/md_img/009.png){: width="100%" height="100%"}{: .center}  

## 관리자의 문의 리스트  
![010](/md_img/010.png){: width="100%" height="100%"}{: .center}  

## 문의 채팅  
![011](/md_img/011.png){: width="100%" height="100%"}{: .center}  

### 웹소켓 서버 server.php  
```php
<?php
    /**
     * 웹소켓 서버
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-12-28
     */

    require dirname(__DIR__) . '/vendor/autoload.php';  // autoload 파일 포함
    require dirname(__DIR__) . '/config.php';  // config.php 파일 포함

    require dirname(__DIR__) . "/home/MyLibraries/CommonLibrary.php";
    $commLib = new \Power\CommonLibrary();

    use Ratchet\MessageComponentInterface;
    use Ratchet\ConnectionInterface;
    use React\Socket\Server as ReactServer;
    use React\Socket\ServerInterface;
    use React\EventLoop\Factory as LoopFactory;

    class ChatServer implements MessageComponentInterface {
        protected $clients;
        private $db;  // 데이터베이스 연결 객체

        public function __construct($dbConnection) {
            // MySQL 데이터베이스 연결
            $this->clients = new \SplObjectStorage;
            $this->db = $dbConnection;  // config.php에서 전달된 DB 연결 객체
        }

        public function onOpen(ConnectionInterface $conn) {
            // 클라이언트가 연결되었을 때
            echo "New connection: ({$conn->resourceId})\n";
            $this->clients->attach($conn);
        }

        public function onClose(ConnectionInterface $conn) {
            // 클라이언트가 연결을 끊었을 때
            echo "Connection closed: ({$conn->resourceId})\n";
            $this->clients->detach($conn);
        }

        public function onMessage(ConnectionInterface $from, $msg) {

            global $commLib;

            echo "Message from ({$from->resourceId}): $msg\n";
        
            // 클라이언트로부터 전달된 JSON 메시지 파싱
            $messageData = json_decode($msg, true);
        
            // user_idx와 message를 추출
            $admin_idx = $messageData['admin_idx'];
            $user_idx = $messageData['user_idx'];
            $user_level = $messageData['user_level'];
            $message = $messageData['message'];
        
            // 메시지를 DB에 저장
            $idx = $commLib->generateRandomString();

            if(0 != $admin_idx) {
                $sql = "INSERT INTO chat_messages (idx, admin_idx, user_idx, user_level, user_msg) VALUES ('$idx', '$admin_idx', '$user_idx', '$user_level', '$message')";
            } else {
                $sql = "INSERT INTO chat_messages (idx, user_idx, user_level, user_msg) VALUES ('$idx', '$user_idx', '$user_level', '$message')";
            }

            $result = mysqli_query($this->db, $sql);
            // echo "SQL: $sql\n";
        
            // 모든 클라이언트에게 메시지 전송
            foreach ($this->clients as $client) {
                if ($from !== $client) {
                    // 보내는 사람을 제외한 다른 클라이언트에게 메시지 전송
                    $client->send($message);
                }
            }

        }

        public function onError(ConnectionInterface $conn, \Exception $e) {
            echo "Error: {$e->getMessage()}\n";
            $conn->close();
        }
    }

    // ReactPHP 이벤트 루프 생성
    $loop = LoopFactory::create();

    // React Socket 서버 생성
    $socket = new ReactServer("0.0.0.0:8080", $loop);

    // 웹소켓 서버 실행
    $server = new Ratchet\Server\IoServer(
        new Ratchet\Http\HttpServer(
            new Ratchet\WebSocket\WsServer(
                new ChatServer($conn)  // config.php에서 연결된 DB 객체를 전달
            )
        ),
        $socket,  // ReactPHP 소켓 서버 객체를 전달
        $loop     // 이벤트 루프 객체 전달
    );

    echo "WebSocket server started...\n";
    $server->run();
?>
```  

### chat_do.php의 js 일부  
```js
var socket = new WebSocket('ws://localhost:8080');

    socket.onopen = function(event) {
        console.log('웹소켓 서버에 연결되었습니다.');
    };

    socket.onmessage = function(event) {
        var user_name = <?= json_encode($your_name) ?>;

        var message = event.data;
        var chatListDiv = document.getElementById('chat_list');

        chatListDiv.innerHTML += "<div class='message admin'><div class='text'><b>" + user_name + "</b>: " + message + "</div></div>";
        chatListDiv.scrollTop = chatListDiv.scrollHeight;
    };
    
    socket.onclose = function(event) {
        console.log('웹소켓 서버 연결이 종료되었습니다.');
    };

    window.onload = function() {
        var chatListDiv = document.getElementById('chat_list');
        chatListDiv.scrollTop = chatListDiv.scrollHeight;
    };

    function sendMessage() {
        var messageInput = document.getElementById('message_input');
        var message = messageInput.value;

        var admin_idx = <?= ($user_level == 9 && isset($client) && "" != $client && null != $client) ? json_encode($user_idx) : "0"; ?>;
        var user_idx = <?= ($user_level == 9 && isset($client) && "" != $client && null != $client) ? json_encode($client) : json_encode($user_idx); ?>;
        var user_level = <?= json_encode($user_level) ?>;

        if (message.trim() !== "") {
            
            var messageData = {
                admin_idx: admin_idx,
                user_idx: user_idx,
                user_level: user_level,
                message: message,
            };

            socket.send(JSON.stringify(messageData));  
            
            var chatListDiv = document.getElementById('chat_list');
            chatListDiv.innerHTML += "<div class='message user'><div class='text'><b>나:</b> " + message + "</div></div>";  // 내 메시지 표시
            messageInput.value = "";
            chatListDiv.scrollTop = chatListDiv.scrollHeight;
        }
    }

    document.getElementById('message_input').addEventListener('keydown', function(event) {
        if (event.key === 'Enter' && !event.isComposing) {
            event.preventDefault();
            sendMessage();
        }
    });
```  

## 포인트 충전  
![013](/md_img/013.png){: width="100%" height="100%"}{: .center}  

### 개발자 버전 카카오페이 결제 기능  
![012](/md_img/012.png){: width="100%" height="100%"}{: .center}  

### payment_request.php  
```php
<?php
    /**
     * 결제 요청
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-12-29
     */

    require_once "../../config.php";

    // POST로 전달된 값 확인 (상품명, 결제 금액)
    if (!isset($_POST['item_name']) || !isset($_POST['total_amount']) || empty($_POST['item_name']) || empty($_POST['total_amount'])) {
        echo "상품명(item_name) 또는 결제 금액(total_amount)이 전달되지 않았습니다.";
        exit;
    }

    // 카카오 API 키 설정 (ADMIN_KEY로 수정)
    $adminKey = YOUR_KAKAO_ADMIN_KEY;  // 카카오페이의 ADMIN_KEY를 사용

    // 결제 정보
    $itemName = $_POST['item_name'];  // 결제 상품명
    $totalAmount = $_POST['total_amount'];  // 결제 금액
    $user_idx = base64_decode($_POST['u_i']); // 사용자 idx

    // 주문 번호와 사용자 ID 생성 (예: timestamp를 이용)
    $partnerOrderId = 'ORDER_' . time();  // 주문 ID (주문 번호)
    $partnerUserId = $user_idx;// 'USER_' . rand(1000, 9999);  // 사용자 ID (랜덤 사용자 ID)

    // 카카오 결제 API 엔드포인트
    $apiUrl = 'https://kapi.kakao.com/v1/payment/ready';  // 결제 준비 API 엔드포인트

    // 결제 요청에 필요한 파라미터
    $data = [
        'cid' => 'TC0ONETIME',  // 가맹점 ID (테스트용 가맹점 ID)
        'partner_order_id' => $partnerOrderId,  // 주문 ID
        'partner_user_id' => $partnerUserId,  // 사용자 ID
        'item_name' => $itemName,  // 상품명
        'quantity' => 1,  // 상품 수량
        'total_amount' => $totalAmount,  // 결제 금액
        'tax_free_amount' => 0,  // 면세 금액 (기본값 0)
        'approval_url' => 'http://localhost/home/request/payment_approval.php',  // 결제 성공 후 리디렉션 URL
        'fail_url' => 'http://localhost/index.php?url=payment',  // 결제 실패 후 리디렉션 URL
        'cancel_url' => 'http://localhost/index.php?url=payment',  // 결제 취소 후 리디렉션 URL
    ];

    // cURL을 이용한 POST 요청
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: KakaoAK {$adminKey}",  // ADMIN_KEY 사용
        "Content-Type: application/x-www-form-urlencoded"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

    // 응답 받기
    $response = curl_exec($ch);
    curl_close($ch);

    // 응답을 디버깅용으로 출력
    $responseObj = json_decode($response, true);

    // 응답 확인
    if ($responseObj === null) {
        echo "API 응답 오류: " . $response;
        exit;
    }

    // 결제 준비 성공시 결제 페이지로 리디렉션
    if (isset($responseObj['tid'])) {

        session_start();
        $_SESSION['tid'] = $responseObj['tid'];
        $_SESSION['partner_order_id'] = $partnerOrderId;
        $_SESSION['partner_user_id'] = $partnerUserId;
        
        // 결제 준비 성공
        // 카카오페이 결제 페이지로 리디렉션
        $redirectUrl = $responseObj['next_redirect_pc_url']; // PC용 URL(next_redirect_pc_url)을 사용 (모바일의 경우 next_redirect_mobile_url)
        header('Location: ' . $redirectUrl);
        exit;

    } else {
        // 결제 준비 실패
        if (isset($responseObj['msg'])) {
            echo '결제 준비 실패: ' . $responseObj['msg'];  // 오류 메시지
        } else {
            echo '결제 준비 실패: ' . print_r($responseObj, true); // 응답 전체 출력
        }
    }
?>
```  


## 계정 관련  
![014](/md_img/014.png){: width="100%" height="100%"}{: .center}  
![015](/md_img/015.png){: width="100%" height="100%"}{: .center}  
![016](/md_img/016.png){: width="100%" height="100%"}{: .center}  
![017](/md_img/017.png){: width="100%" height="100%"}{: .center}  
![020](/md_img/020.png){: width="100%" height="100%"}{: .center}  

## 보완할 점  
다국어 버전 개발  