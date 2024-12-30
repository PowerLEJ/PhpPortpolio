<?php 
    /**
     * 회원이 홈 화면에서 프로그램 예약
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-11-16
     */

    require_once "../../config.php";

    require_once "../MyLibraries/CommonLibrary.php";
    $commLib = new \Power\CommonLibrary(); // 공통 라이브러리 클래스 생성

    /**
     * 회원이 프로그램 예약 시 조건
     */
    if (empty($_POST["program_idx"])) {
        echo '<script>alert("프로그램명을 선택하세요.");</script>';
        echo '<script>history.back();</script>';
        exit();
    }

    if (empty($_POST["user_count"])) {
        echo '<script>alert("인원을 입력하세요.");</script>';
        echo '<script>history.back();</script>';
        exit();
    }

    if (empty($_POST["user_phone"])) {
        echo '<script>alert("인원을 입력하세요.");</script>';
        echo '<script>history.back();</script>';
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        // 폼 데이터 받아오기
        $user_idx = isset($_POST['d0']) ? base64_decode($_POST['d0']) : null;
        $program_idx = isset($_POST['program_idx']) ? $_POST['program_idx'] : null;
        $user_count = isset($_POST['user_count']) ? (int)$_POST['user_count'] : 0;
        $user_phone = isset($_POST['user_phone']) ? $_POST['user_phone'] : null;

        // 전화번호에서 '-'를 제거
        $user_phone = str_replace("-", "", $user_phone);
        
        // 1. 프로그램의 현재 예약 인원 수(booking_count)와 최대 참여 인원(participant_count)을 가져옴
        $sql_get_program_info = "SELECT booking_count, participant_count FROM booking_info WHERE idx = '$program_idx'";
        $result = mysqli_query($conn, $sql_get_program_info);
        $program = mysqli_fetch_assoc($result);
        
        if (!$program) {
            echo '<script>alert("프로그램 정보가 존재하지 않습니다.");</script>';
            echo '<script>history.back();</script>';
            exit();
        }

        $current_booking_count = $program['booking_count'];
        $participant_count = $program['participant_count'];
        
        // 2. 새로운 예약 인원 수를 계산하여 기존 예약 인원 수에 더함
        $new_booking_count = $current_booking_count + $user_count;

        // 3. 새로운 예약 인원 수가 최대 참여 인원보다 많으면 예약을 진행하지 않고, 경고 메시지를 출력함
        if ($new_booking_count > $participant_count) {
            echo '<script>alert("예약 가능 인원을 초과했습니다. 다시 확인해 주세요.");</script>';
            echo '<script>history.back();</script>';
            exit();
        }

        // 예약 중복 확인
        $checkExistingBooking = "SELECT * FROM booking_list WHERE user_idx = '$user_idx' AND program_idx = '$program_idx' AND cancel_check=0 AND del_check=0;";
        $result = mysqli_query($conn, $checkExistingBooking);
        
        if (mysqli_num_rows($result) > 0) {
            echo "<script>alert('이미 예약한 프로그램이 있습니다. 예약 리스트에서 수정해주세요.'); location.href = '../../index.php?url=booking_list&bt=1';</script>";
            exit();
        }

        // 4. booking_info 테이블의 booking_count를 업데이트
        $sql_update_booking_count = "UPDATE booking_info SET booking_count = $new_booking_count WHERE idx = '$program_idx'";
        mysqli_query($conn, $sql_update_booking_count);


        // 예약 리스트에 인서트
        $sql = sprintf("INSERT INTO booking_list (idx, program_idx, user_idx, user_count, user_phone) 
                                VALUES ('%s', '%s', '%s', '%s', '%s');", 
                        $commLib->generateRandomString(), 
                        $program_idx, $user_idx, $user_count, $user_phone);
        mysqli_query($conn, $sql);

        echo "<script>alert('등록되었습니다.');</script>";
        echo "<script>location.replace('../../index.php?url=booking_list');</script>";

    }


?>