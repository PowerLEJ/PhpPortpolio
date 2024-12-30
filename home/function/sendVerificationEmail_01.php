<?php 
    /**
     * 이메일 인증 링크 전송
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
     * 확인 이메일을 전송하는 함수
     * 
     * @param string $user_email 이메일
     * @param string $token 토큰
     * @return boolean 성공 유무 반환
     */
    function sendVerificationEmail($user_email, $token) {

        $mail = new PHPMailer(true);
    
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
            $mail->Subject = '로봇 사이트 이메일 확인';
            $mail->Body = "이메일 확인을 위해 아래의 링크를 클릭하세요.<br /><br />
                            <a href='http://" . WEB_SITE . "/home/request/verify_email_req.php?d1=$token'>이메일 확인</a>";
    
            // 이메일 발송
            if ($mail->send()) {
                echo "<script>alert('확인 이메일을 발송하였습니다.!');</script>";
            } else {
                echo "<script>alert('이메일 발송에 살패하였습니다 : " . $mail->ErrorInfo . "');</script>";
            }

            return true;

        } catch (Exception $e) {
            echo "<script>alert('Mailer Error: " . $mail->ErrorInfo . "');</script>"; // 오류 메시지 출력 (디버그용)
            return false;
        }

    } // function sendVerificationEmail($user_email, $token) {}

?>