<?php
    // trade_edit.php
    /**
     * 로봇 정보 수정하기
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-12-25
     */

    // 메뉴 설정
    $activeMenuNum = 6;
    require_once __DIR__ . "/_navbar.php";

    if (isset($_GET['id'])) {
        $id = mysqli_real_escape_string($conn, $_GET['id']);
        // 로봇 정보 정보를 가져오기
        $sql = "SELECT * FROM robot_info WHERE idx = '$id' AND del_check = 0";
        $result = mysqli_query($conn, $sql);
        $robot = mysqli_fetch_assoc($result);

        // JSON 디코딩하여 이미지 파일 정보 가져오기
        $uploadedFiles = json_decode($robot['uploaded_files'], true);
    } else {
        die("잘못된 접근입니다.");
    }
?>

<div class="container">
    <div class="row text-center">
        <h1>로봇 정보 수정하기</h1>
    </div>
    
    <hr />

    <div class="container">
        <form action="./home/request/robot_req.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="code" value="robot_edit">
            <input type="hidden" name="robot_id" value="<?php echo $robot['idx']; ?>">
            
            <div class="form-group mb-3">
                <label for="robot_name">로봇명*</label>
                <input type="text" name="robot_name" class="form-control" value="<?php echo htmlspecialchars($robot['robot_name']); ?>" required>
            </div>
    
            <div class="form-group mb-3">
                <label for="robot_price">로봇 가격*</label>
                <input type="number" name="robot_price" class="form-control" value="<?php echo htmlspecialchars($robot['robot_price']); ?>" step="0.01" min="0" required>
            </div>

            <div class="form-group mb-3">
                <label for="robot_count">로봇 갯수*</label>
                &nbsp;&nbsp;( <?= htmlspecialchars($robot['robot_left_count']); ?> / <?= htmlspecialchars($robot['robot_count']); ?> )
                <input type="number" name="robot_count" class="form-control" value="<?php echo htmlspecialchars($robot['robot_count']); ?>" min="1" required>
            </div>

            <div class="form-group mb-3">
                <label for="robot_content">로봇 내용</label>
                <textarea name="robot_content" class="form-control" rows="5"><?php echo htmlspecialchars($robot['robot_content']); ?></textarea>
            </div>
    
            <div class="form-group mb-3">
                <label for="robot_place">로봇 장소</label>
                <input type="text" name="robot_place" class="form-control" value="<?php echo htmlspecialchars($robot['robot_place']); ?>">
            </div>

            <div class="form-group mb-3">
                <label for="venue_address">로봇 주소</label>
                <input type="text" name="venue_address" class="form-control" value="<?php echo htmlspecialchars($robot['venue_address']); ?>" id="venue_address">
                <button type="button" class="btn btn-dark" onclick="searchAddress()" style="width: 100%;">찾기</button>
            </div>
            

            <div id="map" style="width: 500px; height: 500px;"></div>

            <div class="form-group mb-3">
                <label for="latitude">위도*</label>
                <input type="number" name="latitude" class="form-control" value="<?php echo htmlspecialchars($robot['latitude']); ?>" step="0.0000001" min="-90" max="90" id="latitude" required>
            </div>

            <div class="form-group mb-3">
                <label for="longitude">경도*</label>
                <input type="number" name="longitude" class="form-control" value="<?php echo htmlspecialchars($robot['longitude']); ?>" step="0.0000001" min="-180" max="180" id="longitude" required>
            </div>

            <div class="form-group mb-3">
                <label for="files">로봇 이미지</label>
                <input type="file" name="uploaded_files[]" class="form-control" multiple>
            </div>

            <div class="mb-3">
                <label>현재 첨부된 이미지</label>
                <?php if ($uploadedFiles && is_array($uploadedFiles)): ?>
                    <?php foreach ($uploadedFiles as $file): ?>
                        <div class="mb-3">
                            <img src="<?php echo $file; ?>" alt="uploaded_image" style="width: 100px; height: 100px; object-fit: cover;">
                            <button type="button" class="btn btn-danger btn-sm delete-file" data-file-id="<?php echo $file; ?>">X</button>
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

<script src="/home/js/trade_edit.js"></script>

<script>
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

    // 주소 검색 후 위도, 경도 자동 입력 및 지도 업데이트
    function searchAddress() {
        var address = document.getElementById('venue_address').value;
        var geocoder = new kakao.maps.services.Geocoder();

        geocoder.addressSearch(address, function(result, status) {
            if (status === kakao.maps.services.Status.OK) {
                // 검색 결과를 통해 위도, 경도 값 업데이트
                var latitude = result[0].y;
                var longitude = result[0].x;

                // 위도, 경도 필드에 값 설정
                document.getElementById('latitude').value = latitude;
                document.getElementById('longitude').value = longitude;

                // 지도에 마커 추가
                var position = new kakao.maps.LatLng(latitude, longitude);
                marker.setPosition(position);
                map.setCenter(position);
            } else {
                alert('주소를 찾을 수 없습니다.');
            }
        });
    }

    // 지도 클릭 시 위도, 경도 자동 입력
    kakao.maps.event.addListener(map, 'click', function(mouseEvent) {
        var position = mouseEvent.latLng;
        var latitude = position.getLat();
        var longitude = position.getLng();

        // 위도, 경도 필드에 값 설정
        document.getElementById('latitude').value = latitude;
        document.getElementById('longitude').value = longitude;

        // 마커 위치 업데이트
        marker.setPosition(position);
    });
</script>