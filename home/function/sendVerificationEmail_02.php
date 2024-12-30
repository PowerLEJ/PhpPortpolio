<?php 
    /**
     * 계정 찾기를 통한 이메일 전송
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-11-09
     */

    require dirname(__DIR__) . '/libs/PHPMailer/src/PHPMailer.php';
    require dirname(__DIR__) . '/libs/PHPMailer/src/SMTP.php';
    require dirname(__DIR__) . '/libs/PHPMailer/src/Exception.php';
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    /**
     * 계정 찾기 이메일을 전송하는 함수
    * 
    * @param string $conn DB 연결
    * @param string $user_name 이름
    * @param string $user_email 이메일
    * @param string $user_id 아이디
    * @return boolean 성공 유무 반환
    */
    function sendVerificationEmail($conn, $user_name, $user_email, $user_id) {

        $mail = new PHPMailer(true);

        $find_type = 1;

        if(isset($user_id) && null != $user_id && '' != $user_id) { $find_type = 2; }

        $sql = sprintf("SELECT * FROM user_info WHERE del_check = 0 AND user_name = '%s' AND user_email = '%s';", $user_name, $user_email);
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        $user_idx = $row['idx'];

        if(1 == $find_type) { $user_id = $row['user_id']; }

        try {
            // SMTP 설정
            $mail->isSMTP();
            $mail->Host = 'smtp.naver.com'; // SMTP 서버 주소 (예: Gmail)
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER; // 발신자 이메일 주소
            $mail->Password = SMTP_PASS; // 발신자 이메일 비밀번호
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8'; // 한글 깨짐 방지
    
            // 이메일 발신자 및 수신자 설정
            $mail->setFrom(SMTP_USER, '로봇 관리자'); // 발신자 정보
            $mail->addAddress($user_email); // 수신자 이메일
    
            // 이메일 내용 설정
            $mail->isHTML(true);
            $mail->Subject = '로봇 사이트 계정 찾기';
            if(1 == $find_type) {
                $mail->Body = "아이디는 $user_id 입니다.";
            } else if(2 == $find_type) {
                $mail->Body = "아래의 링크를 통해 비밀번호를 변경하세요.<br /><br />
                            <a href='http://" . WEB_SITE . "/index.php?url=pass_change&d1=" . base64_encode($user_idx) . "'>비밀번호 변경</a>";
            }
    
            // 이메일 발송
            if ($mail->send()) {
                echo "<script>alert('이메일을 발송하였습니다.');</script>";
            } else {
                echo "<script>alert('이메일 발송에 살패하였습니다 : " . $mail->ErrorInfo . "');</script>";
            }

            return true;

        } catch (Exception $e) {
            echo "<script>alert('Mailer Error: " . $mail->ErrorInfo . "');</script>"; // 오류 메시지 출력 (디버그용)
            return false;
        }

    } // function sendVerificationEmail($conn, $user_name, $user_email, $user_id) {}

?>