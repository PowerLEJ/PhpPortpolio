<?php
    /**
     * 메세지 요소 인터페이스
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-12-28
     */

    use Ratchet\MessageComponentInterface;
    use Ratchet\ConnectionInterface;

    class Chat implements MessageComponentInterface {
        // 현재 연결된 모든 클라이언트 저장
        protected $clients;

        public function __construct() {
            $this->clients = new \SplObjectStorage;
        }

        // 새로운 클라이언트가 연결되면 호출되는 메소드
        public function onOpen(ConnectionInterface $conn) {
            // 클라이언트 연결을 저장
            $this->clients->attach($conn);
            echo "New connection! ({$conn->resourceId})\n";
        }

        // 클라이언트로부터 메시지가 오면 호출되는 메소드
        public function onMessage(ConnectionInterface $from, $msg) {
            // 모든 클라이언트에게 메시지 전송
            foreach ($this->clients as $client) {
                // 자신에게 메시지를 보내지 않음
                if ($from !== $client) {
                    $client->send($msg);
                }
            }
            echo "Message from {$from->resourceId}: $msg\n";
        }

        // 클라이언트 연결이 끊기면 호출되는 메소드
        public function onClose(ConnectionInterface $conn) {
            // 연결 종료시 클라이언트 삭제
            $this->clients->detach($conn);
            echo "Connection {$conn->resourceId} has disconnected\n";
        }

        // 에러 발생시 호출되는 메소드
        public function onError(ConnectionInterface $conn, \Exception $e) {
            echo "Error: {$e->getMessage()}\n";
            $conn->close();
        }
    }
?>