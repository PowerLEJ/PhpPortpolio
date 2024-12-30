// 아이디 중복 확인 함수
function checkUserId() {
    const userid = $("#s_user_id").val();
    const message = $("#message");

    if (userid === "") {
        message.text(""); // 빈 값일 때 메시지 초기화
        return;
    }

    $.ajax({
        type: "POST",
        url: "./home/request/check_userid_req.php",
        data: { userid: userid },
        dataType: "json",
        success: function(response) {
            if (response.exists) {
                message.text("이미 사용 중인 아이디입니다.").css("color", "red");
            } else {
                message.text("사용 가능한 아이디입니다.").css("color", "green");
            }
        },
        error: function() {
            message.text("오류가 발생했습니다.").css("color", "red");
        }
    });
}