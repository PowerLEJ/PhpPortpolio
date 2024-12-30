<?php
    /**
     * 문의하기
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-12-28
     */

    // 메뉴 설정
    $activeMenuNum = 8;
    require_once __DIR__ . "/_navbar.php";

    $client = isset($_GET['client']) ? $_GET['client'] : '';

    if($user_level == 9 && isset($client) && '' != $client && null != $client) {
        $sql = sprintf("SELECT admin_idx, user_idx, user_msg, reg_date FROM chat_messages WHERE user_idx='%s' AND del_check=0 ORDER BY reg_date DESC LIMIT 20;", $client);
        $result = mysqli_query($conn, $sql);

        $sql = sprintf("SELECT user_name FROM user_info WHERE idx = '%s' AND del_check=0;", $client);
        $result_user = mysqli_query($conn, $sql);
        $row_user = mysqli_fetch_assoc($result_user);
        $your_name = $row_user['user_name'];
        
    } else {
        $sql = sprintf("SELECT admin_idx, user_idx, user_msg, reg_date FROM chat_messages WHERE user_idx = '%s' AND del_check=0 ORDER BY reg_date DESC LIMIT 20;", $user_idx);
        $result = mysqli_query($conn, $sql);

        $sql = sprintf("SELECT user_name FROM user_info WHERE user_level=9 AND del_check=0 LIMIT 1;");
        $result_user = mysqli_query($conn, $sql);
        $row_user = mysqli_fetch_assoc($result_user);
        $your_name = $row_user['user_name'];
    }    
?>

<style>
    #chat_container {
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        max-width: 500px;
        margin: 50px auto;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }

    #chat_list {
        max-height: 400px;
        overflow-y: auto;
        padding: 10px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .message {
        display: flex;
        max-width: 80%;
        padding: 10px;
        border-radius: 20px;
        font-size: 14px;
        word-break: break-word;
        margin-bottom: 10px;
    }

    /* "나"의 메시지는 우측 정렬 */
    .message.user {
        background-color:rgb(253, 255, 120);
        align-self: flex-end; /* 우측 정렬 */
    }

    /* 상대방(관리자)의 메시지는 좌측 정렬 */
    .message.admin {
        background-color:rgb(204, 204, 204);
        align-self: flex-start; /* 좌측 정렬 */
    }

    .message .text {
        max-width: 90%;
    }

    #message_input {
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 30px;
        width: calc(100% - 60px);
        font-size: 14px;
        margin-right: 10px;
        outline: none;
        transition: border-color 0.3s;
    }

    #message_input:focus {
        border-color:rgb(255, 247, 0);
    }

    .send_button {
        width: 40px;
        height: 40px;
        background-color: rgb(199, 236, 255);
        border: none;
        color: black;
        border-radius: 10%;
        cursor: pointer;
        font-size: 15px;
        transition: background-color 0.3s;
    }

    .send_button:hover {
        background-color:rgb(104, 177, 255);
    }

    #message_box {
        display: flex;
        justify-content: space-between;
        margin-top: 10px;
    }

</style>

<h1 style="text-align: center; color: #333;">문의하기</h1>

<div id="chat_container">
    <div id="chat_list">
        <?php 
            $messages = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $messages[] = $row;
            }

            foreach (array_reverse($messages) as $row) {
                if(isset($row['admin_idx']) && '' != $row['admin_idx'] && null != $row['admin_idx']) {
                    $user = $row['admin_idx'];
                } else {
                    $user = $row['user_idx'];
                }

                $sql = sprintf("SELECT user_name FROM user_info WHERE idx = '%s' AND del_check=0;", $user);
                $result_user = mysqli_query($conn, $sql);
                $row_user = mysqli_fetch_assoc($result_user);
                $chat_name = $row_user['user_name'];
        ?>
            <div class="message <?= $user_idx != $user ? 'admin' : 'user' ?>">
                <div class="text">
                    <b><?= $user_idx != $user ? $chat_name : "나"; ?>:</b> <?= $row['user_msg']; ?>
                </div>
            </div>
        <?php 
            }
        ?>
    </div>

    <div id="message_box">
        <input type="text" id="message_input" placeholder="메시지를 입력하세요..." />
        <button class="send_button" onclick="sendMessage()">전송</button>
    </div>
</div>

<script>
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
    
</script>
