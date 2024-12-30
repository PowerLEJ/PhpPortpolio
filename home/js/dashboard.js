function showTab(tabName, element) {    
    const navItems = document.querySelectorAll('.book-nav-item');
    navItems.forEach(item => item.classList.remove('active'));
    element.parentElement.classList.add('active');
    
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(tabContent => tabContent.style.display = 'none');
    
    const selectedTab = document.getElementById(tabName);
    if (selectedTab) {
        selectedTab.style.display = 'block';
    }
}

let isZoomable = false;
function toggleZoom() {
    isZoomable = !isZoomable;
    map.setZoomable(isZoomable);

    // 아이콘 변경
    const icon = document.querySelector("#zoom-icon");
    if (isZoomable) {        
        icon.classList.remove("bi-pin-fill");  // 축소 아이콘 제거
        icon.classList.add("bi-pin-angle");      // 확대 아이콘 추가
    } else {
        icon.classList.remove("bi-pin-angle");   // 확대 아이콘 제거
        icon.classList.add("bi-pin-fill");     // 축소 아이콘 추가
    }
}


var mapContainer = document.getElementById('map'), 
    mapOption = { 
        center: new kakao.maps.LatLng(37.5665, 126.9780), // Default center
        level: 3 
    };

var map = new kakao.maps.Map(mapContainer, mapOption);
map.setZoomable(false);

// Get 위도 경도
kakao.maps.event.addListener(map, 'click', function(mouseEvent) {        
    var latlng = mouseEvent.latLng; 
    document.getElementById('latitude').value = latlng.getLat();
    document.getElementById('longitude').value = latlng.getLng();
});

// 주소 검색 함수
function searchAddress() {
    var geocoder = new kakao.maps.services.Geocoder();
    var address = document.getElementById('venue_address').value;  // 주소 입력 값 받기
    geocoder.addressSearch(address, function(result, status) {
        if (status === kakao.maps.services.Status.OK) {
            var coords = new kakao.maps.LatLng(result[0].y, result[0].x); // 좌표 변환
            map.setCenter(coords);  // 지도 중심 이동
            document.getElementById('latitude').value = result[0].y; // 위도 입력 필드 설정
            document.getElementById('longitude').value = result[0].x; // 경도 입력 필드 설정
        } else {
            alert("주소를 찾을 수 없습니다. 지도에서 직접 선택해주세요."); // 주소 찾을 수 없을 경우 알림
        }
    });
}


// 마커
fetch("./home/request/map_markers_req.php")
    .then(response => response.json())
    .then(data => {
        data.forEach(markerData => {
            var cnt = 0;
            if(markerData.booking_count == markerData.participant_count) {
                cnt = 1;
            }
            
            createMarker(markerData, cnt);
        });
    })
    .catch(error => console.error('Error fetching marker data:', error));

// 마커 생성 함수
function createMarker(markerData, cnt) {
    var position = new kakao.maps.LatLng(markerData.latitude, markerData.longitude);

    // 기본 마커
    const marker = new kakao.maps.Marker({
        position: position,
        map: map,
        // 빨간색 또는 기본 색상 마커
        image: new kakao.maps.MarkerImage(
                cnt == 1
                ? 'http://t1.daumcdn.net/localimg/localimages/07/mapapidoc/marker_red.png' // 빨간 마커 이미지
                : 'http://t1.daumcdn.net/localimg/localimages/07/mapapidoc/markerStar.png', // 기본 마커 이미지
            new kakao.maps.Size(24, 35) // 마커 크기
        )
    });

    var infoContent = `
        <div style="padding:10px; width:200px;">
            <h4 class="text-center" style="font-weight: bold;">${markerData.robot_name}</h4>
            <span style="font-weight: bold;">가격: </span>${markerData.robot_price}<br />
            <span style="font-weight: bold;">갯수: </span>${markerData.robot_left_count} / ${markerData.robot_count}<br />
            <span style="font-weight: bold;">내용: </span>${markerData.robot_content}<br />
            <span style="font-weight: bold;">장소: </span>${markerData.robot_place}<br />
            <span style="font-weight: bold;">주소: </span>${markerData.venue_address}<br />
        </div>
    `;
    
    var infoWindow = new kakao.maps.InfoWindow({
        content: infoContent
    });

    var isInfoVisible = false; // 정보창 보임 여부

    kakao.maps.event.addListener(marker, 'click', function () {
        if (isInfoVisible) {
            infoWindow.close(); // 정보창 닫기
        } else {
            infoWindow.open(map, marker); // 정보창 열기
        }
        isInfoVisible = !isInfoVisible; // 상태 반전
    });
}


function showRobotDetails() {

    const robotSelect = document.getElementById("robot_idx");
    const selectedOption = robotSelect.options[robotSelect.selectedIndex];

    // 세부 정보 초기화
    document.getElementById("robot_price").innerText = "";
    document.getElementById("robot_count").innerText = "";
    document.getElementById("robot_left_count").innerText = "";
    document.getElementById("robot_content").innerText = "";
    document.getElementById("robot_place").innerText = "";
    document.getElementById("venue_address").innerText = "";
    document.getElementById("latitude").innerText = "";
    document.getElementById("longitude").innerText = "";

    // 이미지 슬라이더 초기화
    const currentImage = document.getElementById("currentImage");
    const thumbnails = document.getElementById("thumbnails");
    const prevButton = document.getElementById("prevImage");
    const nextButton = document.getElementById("nextImage");

    currentImage.style.display = "none";
    currentImage.src = "";
    thumbnails.innerHTML = "";

    // 버튼 초기화
    prevButton.onclick = null;
    nextButton.onclick = null;
    prevButton.style.visibility = "hidden"; // 버튼 숨김
    nextButton.style.visibility = "hidden"; // 버튼 숨김

    // 선택된 옵션이 비어 있는 경우
    if (selectedOption.value === "") {
        document.getElementById("robot_details").style.display = "none";
        return;
    }

    // 선택된 프로그램의 데이터 가져오기
    const robotPrice = selectedOption.getAttribute("data-price");
    const robotCount = selectedOption.getAttribute("data-count");
    const robotLeftCount = selectedOption.getAttribute("data-left_count");
    const robotContent = selectedOption.getAttribute("data-content");
    const robotPlace = selectedOption.getAttribute("data-place");
    const robotVenueAddress = selectedOption.getAttribute("data-venue_address");
    const robotLatitude = selectedOption.getAttribute("data-latitude");
    const robotLongitude = selectedOption.getAttribute("data-longitude");
    const imagesJSON = selectedOption.getAttribute("data-images");

    // 세부 정보 업데이트
    document.getElementById("robot_price").innerText = robotPrice;
    document.getElementById("robot_count").innerText = robotCount;
    document.getElementById("robot_left_count").innerText = robotLeftCount;
    document.getElementById("robot_content").innerText = robotContent;
    document.getElementById("robot_place").innerText = robotPlace;
    document.getElementById("venue_address").innerText = robotVenueAddress;
    document.getElementById("latitude").innerText = robotLatitude;
    document.getElementById("longitude").innerText = robotLongitude;

    // 이미지 슬라이더 설정
    if (imagesJSON) {
        const images = JSON.parse(imagesJSON);

        if(imagesJSON != "[]" && imagesJSON != "" && imagesJSON != null) {

            let currentIndex = 0;
    
            // 슬라이더 이미지 업데이트
            function updateSlider() {
                currentImage.src = images[currentIndex];
                currentImage.style.display = "block";
            }
    
            // 초기 이미지 설정
            updateSlider();
    
            // 이전/다음 버튼 동작
            prevButton.style.visibility = "visible"; // 버튼 표시
            nextButton.style.visibility = "visible"; // 버튼 표시
    
            prevButton.onclick = function () {
                currentIndex = (currentIndex - 1 + images.length) % images.length;
                updateSlider();
            };
            nextButton.onclick = function () {
                currentIndex = (currentIndex + 1) % images.length;
                updateSlider();
            };
    
            // 썸네일 생성 및 클릭 이벤트
            images.forEach((imagePath, index) => {
                const thumbnail = document.createElement("img");
                thumbnail.src = imagePath;
                thumbnail.alt = "Thumbnail";
                thumbnail.onclick = function () {
                    currentIndex = index;
                    updateSlider();
                };
                thumbnails.appendChild(thumbnail);
            });

        }
    }

    // 세부 정보 div 표시
    document.getElementById("robot_details").style.display = "block";
}


// 페이지 로드 시 select 요소를 검사하여 비활성화 여부를 결정
document.addEventListener("DOMContentLoaded", function() {
    const selectElement = document.getElementById("robot_idx");
    const options = selectElement.getElementsByTagName("option");

    for (let option of options) {
        const leftPriceCount = parseInt(option.getAttribute('data-left_count'));

        if (0 === leftPriceCount) {
            option.disabled = true;
        }
    }
});