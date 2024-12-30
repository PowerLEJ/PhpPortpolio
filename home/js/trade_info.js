function deleteRobot(id) {
    if (confirm("로봇 정보를 삭제하시겠습니까?")) {
        $.post('./home/request/delete_req.php', { code: 'robot_info', selected_ids: [id], js: 1 }, function (response) {
            alert(response);
            location.href = '../index.php?url=trade_list';
        }).fail(function () {
            alert('삭제 중 오류가 발생했습니다.');
        });
    }
}

// 이미지와 썸네일을 전환하는 함수
function updateImage() {
    const currentImage = document.getElementById('currentImage');
    currentImage.src = uploadedFiles[currentIndex];
}

// 이전 이미지
document.getElementById('prevImage').addEventListener('click', function() {
    if (currentIndex > 0) {
        currentIndex--;
        updateImage();
    } else {
        // 첫 번째 이미지일 때는 마지막 이미지로
        currentIndex = uploadedFiles.length - 1;
        updateImage();
    }
});

// 다음 이미지
document.getElementById('nextImage').addEventListener('click', function() {
    if (currentIndex < uploadedFiles.length - 1) {
        currentIndex++;
        updateImage();
    } else {
        // 마지막 이미지일 때는 첫 번째 이미지로
        currentIndex = 0;
        updateImage();
    }
});

// 썸네일 클릭 시 해당 이미지로 이동
document.querySelectorAll('.thumbnail').forEach(thumbnail => {
    thumbnail.addEventListener('click', function() {
        currentIndex = parseInt(this.getAttribute('data-index'));
        updateImage();
    });
});


