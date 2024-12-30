document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".delete-file").forEach(function (button) {
        button.addEventListener("click", function () {
            var fileId = button.getAttribute("data-file-id");

            if (confirm("정말로 이 파일을 삭제하시겠습니까?")) {
                deleteFile(fileId, button);
            }
        });
    });
});


function deleteFile(fileId, button) {
    
    var formData = new FormData();
    formData.append("code", "robot_img_del");
    formData.append("file_id", fileId);
    formData.append("robot_id", document.querySelector('input[name="robot_id"]').value);

    fetch("./home/request/robot_req.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(response => {
        if (response.success) {
            button.parentElement.remove();
            alert("파일이 삭제되었습니다.");
        } else {
            alert("파일 삭제에 실패했습니다. " + (response.message || ""));
        }
    })
    .catch(error => {
        console.error("Error during file deletion:", error);
        alert("서버 오류가 발생했습니다.");
    });
}