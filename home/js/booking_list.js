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


function openEditForm(programId) {
    var modalElement = document.getElementById('editProgramModal' + programId);
    var modal = new bootstrap.Modal(modalElement);
    modal.show();
}

function openBookingForm(programId) {
    var modalElement = document.getElementById('bookProgramModal' + programId);
    var modal = new bootstrap.Modal(modalElement);
    modal.show();
}

function openModifyBookForm(programId) {
    var modalElement = document.getElementById('bookModifyModal' + programId);
    var modal = new bootstrap.Modal(modalElement);
    modal.show();
}

function openAddProgramForm() {
    var modalElement = document.getElementById('addProgramModal');
    var modal = new bootstrap.Modal(modalElement);
    modal.show();
}

function openBookDoProgramForm() {
    var modalElement = document.getElementById('bookDoProgramModal');
    var modal = new bootstrap.Modal(modalElement);
    modal.show();
}


function confirmCancel(bookingIdx) {
    if (confirm("이 예약을 취소하시겠습니까?")) {
        window.location.href = "../home/request/booking_list_req.php?action=cancel&booking_idx=" + bookingIdx;
    }
}

function toggleAllCheckboxes(source) {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = source.checked;
    });
}

function submitDeleteForm() {
    const form = document.getElementById('deleteForm');
    const checkboxes = document.querySelectorAll('input[name="selected_ids[]"]:checked');

    if (checkboxes.length === 0) {
        alert('삭제할 항목을 선택하세요.');
        return;
    }

    checkboxes.forEach((checkbox) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'selected_ids[]';
        input.value = checkbox.value;
        form.appendChild(input);
    });

    if (confirm('선택된 항목을 삭제하시겠습니까?')) {
        form.submit();
    }
}










document.addEventListener('DOMContentLoaded', function () {
    // 모든 썸네일을 가져와서 이벤트 리스너 등록
    document.querySelectorAll('.thumbnail').forEach(thumbnail => {
        const bookingIdx = thumbnail.closest('.modal').id.replace('bookModifyModal', '');  // 해당 모달의 booking_idx 추출
        const currentImage = document.getElementById('currentImage' + bookingIdx);
        const prevButton = document.getElementById('prevImage' + bookingIdx);
        const nextButton = document.getElementById('nextImage' + bookingIdx);

        let currentIndex = 0;
        const images = [];

        // 썸네일 클릭 시 해당 이미지로 이동
        thumbnail.closest('.modal').querySelectorAll('.thumbnail').forEach((thumb, index) => {
            images.push(thumb.dataset.src);  // 이미지 src를 배열에 추가
            thumb.addEventListener('click', function () {
                currentIndex = index;
                updateCurrentImage();
            });
        });

        // 이미지 업데이트 함수
        function updateCurrentImage() {
            currentImage.src = images[currentIndex];
        }

        // 이전 이미지 표시
        prevButton.addEventListener('click', function () {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            updateCurrentImage();
        });

        // 다음 이미지 표시
        nextButton.addEventListener('click', function () {
            currentIndex = (currentIndex + 1) % images.length;
            updateCurrentImage();
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    // 모든 썸네일을 가져와서 이벤트 리스너 등록
    document.querySelectorAll('.thumbnail').forEach(thumbnail => {
        const bookingIdx = thumbnail.closest('.modal').id.replace('bookProgramModal', '');  // 해당 모달의 booking_idx 추출
        const currentImage = document.getElementById('currentImage' + bookingIdx);
        const prevButton = document.getElementById('prevImage' + bookingIdx);
        const nextButton = document.getElementById('nextImage' + bookingIdx);

        let currentIndex = 0;
        const images = [];

        // 썸네일 클릭 시 해당 이미지로 이동
        thumbnail.closest('.modal').querySelectorAll('.thumbnail').forEach((thumb, index) => {
            images.push(thumb.dataset.src);  // 이미지 src를 배열에 추가
            thumb.addEventListener('click', function () {
                currentIndex = index;
                updateCurrentImage();
            });
        });

        // 이미지 업데이트 함수
        function updateCurrentImage() {
            currentImage.src = images[currentIndex];
        }

        // 이전 이미지 표시
        prevButton.addEventListener('click', function () {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            updateCurrentImage();
        });

        // 다음 이미지 표시
        nextButton.addEventListener('click', function () {
            currentIndex = (currentIndex + 1) % images.length;
            updateCurrentImage();
        });
    });
});








































function showProgramDetails() {

    const programSelect = document.getElementById("program_idx");
    const selectedOption = programSelect.options[programSelect.selectedIndex];

    // 세부 정보 초기화
    document.getElementById("program_date").innerText = "";
    document.getElementById("program_time").innerText = "";
    document.getElementById("program_place").innerText = "";
    document.getElementById("program_content").innerText = "";
    document.getElementById("program_participant").innerText = "";
    document.getElementById("program_booking").innerText = "";

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
        document.getElementById("program_details").style.display = "none";
        return;
    }

    // 선택된 프로그램의 데이터 가져오기
    const programDate = selectedOption.getAttribute("data-date");
    const programTime = selectedOption.getAttribute("data-time");
    const programPlace = selectedOption.getAttribute("data-place");
    const programContent = selectedOption.getAttribute("data-content");
    const programParticipant = selectedOption.getAttribute("data-participant");
    const programBooking = selectedOption.getAttribute("data-booking");
    const imagesJSON = selectedOption.getAttribute("data-images");

    // 세부 정보 업데이트
    document.getElementById("program_date").innerText = programDate;
    document.getElementById("program_time").innerText = programTime;
    document.getElementById("program_place").innerText = programPlace;
    document.getElementById("program_content").innerText = programContent;
    document.getElementById("program_participant").innerText = programParticipant;
    document.getElementById("program_booking").innerText = programBooking;

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
    document.getElementById("program_details").style.display = "block";
}


// 페이지 로드 시 select 요소를 검사하여 비활성화 여부를 결정
document.addEventListener("DOMContentLoaded", function() {
    const selectElement = document.getElementById("program_idx");
    const options = selectElement.getElementsByTagName("option");

    for (let option of options) {
        const participantCount = parseInt(option.getAttribute('data-participant'));
        const bookingCount = parseInt(option.getAttribute('data-booking'));

        if (participantCount === bookingCount) {
            option.disabled = true;  // 참여 인원과 예약 인원이 같으면 해당 옵션 비활성화
        }
    }
});


function validateForm() {
    // 선택된 프로그램이 없는지 확인
    const programIdx = document.getElementById("program_idx").value;
    
    if (programIdx === "") {
        alert("프로그램명을 선택하세요.");
        return false;  // 폼 제출을 막음
    }

    // 추가적인 유효성 검사를 넣을 수 있습니다 (예: 인원 수, 전화번호 등)
    const userCount = document.querySelector("input[name='user_count']").value;
    const userPhone = document.querySelector("input[name='user_phone']").value;

    if (userCount === "") {
        alert("인원을 입력하세요.");
        return false;
    }

    if (userPhone === "") {
        alert("연락처를 입력하세요.");
        return false;
    }

    return true;  // 폼 제출
}






