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