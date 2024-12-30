<?php
    /**
     * 회원이 예약 리스트에서 프로그램 예약, 수정, 취소 & 관리자가 예약 리스트에서 프로그램 수정
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-11-16
     */

    require_once "../../config.php";

    require_once "../MyLibraries/CommonLibrary.php";
    $commLib = new \Power\CommonLibrary(); // 공통 라이브러리 클래스 생성

    $action = $_POST['action'] ?? $_GET['action'] ?? null;

    if ($action === 'book') {
        $idx = $commLib->generateRandomString();

        $user_idx = base64_decode($_POST['d0']);

        $programIdx = $_POST['program_idx'];
        $userCount = $_POST['user_count'];
        $userPhone = $_POST['user_phone'];

        // 인원 체크
        $sql = "SELECT * FROM booking_info WHERE idx = '$programIdx'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $current_booking_count = $row['booking_count'];
        $participant_count = $row['participant_count'];

        // 새로운 예약 인원 수를 계산하여 기존 예약 인원 수에 더함
        $new_booking_count = $current_booking_count + $userCount;

        if($new_booking_count > $participant_count) {
            echo '<script>alert("예약 가능 인원을 초과했습니다. 다시 확인해 주세요.");</script>';
            echo '<script>history.back();</script>';
            exit();
        }

        // 예약 중복 확인
        $checkExistingBooking = "SELECT * FROM booking_list WHERE user_idx = '$user_idx' AND program_idx = '$programIdx'";
        $result = mysqli_query($conn, $checkExistingBooking);
        
        if (mysqli_num_rows($result) > 0) {
            echo "<script>alert('이미 예약한 프로그램이 있습니다. 예약 리스트에서 수정해주세요.'); location.href = '../../index.php?url=booking_list&bt=1';</script>";
        } else {

            $sql = "INSERT INTO booking_list (idx, program_idx, user_idx, user_count, user_phone) 
                    VALUES ('$idx', '$programIdx', '$user_idx', $userCount, '$userPhone')";
            mysqli_query($conn, $sql);

            $sql = sprintf("UPDATE booking_info SET booking_count = booking_count + %d WHERE idx = '%s'", $userCount, $programIdx);
            mysqli_query($conn, $sql);

            echo "<script>alert('예약이 완료되었습니다.'); location.href = '../../index.php?url=booking_list&bt=1';</script>";
        }

    } else if ($action == 'edit') {

        $bookingIdx = $_POST['booking_idx'];
        $userCount = $_POST['user_count'];
        $userPhone = $_POST['user_phone'];

        // 예약 정보 가져오기
        $sql = "SELECT bi.idx, bi.program_date, bi.participant_count, bi.booking_count, bl.user_count
                FROM booking_list bl
                JOIN booking_info bi ON bl.program_idx = bi.idx
                WHERE bl.idx = '$bookingIdx' AND bl.del_check = 0";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

        $programDate = $row['program_date'];
        $participantCount = $row['participant_count'];
        $bookingCount = $row['booking_count'];
        $origin_userCount = $row['user_count'];

        // 예약 수정 가능 여부 체크 (프로그램 시작일 7일 이전)
        if (strtotime($programDate) >= strtotime("+7 days")) {

            // 수정 가능한 경우, 예약 수가 꽉 찼는지 확인
            $availableSeats = $participantCount - $bookingCount + $origin_userCount; // 기존 본인이 예약한 인원수 일단 더함

            // 예약 수정 가능
            if ($availableSeats >= $userCount) {

                $updateSql = "UPDATE booking_list SET user_count = '$userCount', user_phone = '$userPhone' WHERE idx = '$bookingIdx'";
                
                if (mysqli_query($conn, $updateSql)) {
                    // 예약 수정 후 booking_count 업데이트
                    $newBookingCount = $bookingCount + $userCount - $origin_userCount; // 아까 기존 본인이 예약한 인원수 더한 거 그냥 뺌
                        
                    $updateBookingCountSql = sprintf("UPDATE booking_info SET booking_count = '%s' WHERE idx = '%s'", $newBookingCount, $row['idx']);
                    mysqli_query($conn, $updateBookingCountSql);

                    echo "<script>alert('예약 변경이 완료되었습니다.'); location.href = '../../index.php?url=booking_list&bt=1';</script>";

                } else {
                    echo "<script>alert('예약 변경 중 오류가 발생했습니다.');</script>";
                    echo '<script>history.back();</script>';
                    exit();
                }
            } else {
                echo "<script>alert('예약 자리가 부족합니다.');</script>";
                echo '<script>history.back();</script>';
                exit();
            }
        } else {
            echo "<script>alert('프로그램 일정 7일 이내는 예약 변경이 불가능합니다.');</script>";
            echo '<script>history.back();</script>';
            exit();
        }
    } else if ($action == 'cancel') {
        // 예약 취소 처리
        $bookingIdx = $_GET['booking_idx'];

        // 예약 정보 가져오기
        $sql = "SELECT bi.idx, bi.program_date, bi.participant_count, bi.booking_count, bl.user_count
                FROM booking_list bl
                JOIN booking_info bi ON bl.program_idx = bi.idx
                WHERE bl.idx = '$bookingIdx' AND bl.del_check = 0";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

        $programDate = $row['program_date'];
        $participantCount = $row['participant_count'];
        $bookingCount = $row['booking_count'];
        $userCount = $row['user_count']; // 취소하려는 사용자가 예약한 인원 수

        // 예약 취소 가능 여부 체크 (프로그램 시작일 7일 이전)
        if (strtotime($programDate) >= strtotime("+7 days")) {
            // 취소 가능한 경우, booking_count에서 예약한 인원 수만큼 차감 처리
            $newBookingCount = $bookingCount - $userCount;

            // 예약 취소
            $cancelSql = sprintf("UPDATE booking_list SET cancel_check = 1, cancel_date='%s', user_count=0 WHERE idx = '%s'", date('Y-m-d H:i:s'), $bookingIdx);
            if (mysqli_query($conn, $cancelSql)) {
                // booking_info 테이블의 booking_count 업데이트
                $updateBookingCountSql = sprintf("UPDATE booking_info SET booking_count = '%s' WHERE idx = '%s';", $newBookingCount, $row['idx']);

                if (mysqli_query($conn, $updateBookingCountSql)) {
                    echo "<script>alert('예약이 취소되었습니다.'); location.href = '../../index.php?url=booking_list&bt=1';</script>";
                } else {
                    echo "<script>alert('오류가 발생하였습니다.[0]');</script>"; // 예약 취소 후 booking_count 업데이트 실패
                    echo '<script>history.back();</script>';
                    exit();
                }
            } else {
                echo "<script>alert('오류가 발생하였습니다.[1]');</script>";
                echo '<script>history.back();</script>';
                exit();
            }
        } else {
            echo "<script>alert('프로그램 일정 7일 이내는 예약 취소가 불가능합니다.');</script>";
            echo '<script>history.back();</script>';
            exit();
        }
    } else if ($action === 'modify') {
        // 관리자가 프로그램 정보를 변경하는 부분
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $programIdx = mysqli_real_escape_string($conn, $_POST['program_idx']);
            $programName = mysqli_real_escape_string($conn, $_POST['program_name']);
            $programDate = mysqli_real_escape_string($conn, $_POST['program_date']);
            $programTime = mysqli_real_escape_string($conn, $_POST['program_time']);
            $participantCount = mysqli_real_escape_string($conn, $_POST['participant_count']);
            $programPlace = mysqli_real_escape_string($conn, $_POST['program_place']);
            $programContent = mysqli_real_escape_string($conn, $_POST['program_content']);

            // 기존 JSON 데이터 가져오기
            $query = "SELECT uploaded_files FROM booking_info WHERE idx = '$programIdx'";
            $result = mysqli_query($conn, $query);
            $row = mysqli_fetch_assoc($result);
            $existingFiles = json_decode($row['uploaded_files'], true) ?: [];


            // 파일 업로드 처리
            $uploaded_files = [];
            if (!empty($_FILES['uploaded_files']['name'][0])) {
                $upload_dir = "../../uploads/uploads_book/";
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

                foreach ($_FILES['uploaded_files']['name'] as $key => $filename) {
                    $tmp_name = $_FILES['uploaded_files']['tmp_name'][$key];
                    $file_ext = pathinfo($filename, PATHINFO_EXTENSION);
                    $new_filename = uniqid() . '.' . $file_ext;

                    if (move_uploaded_file($tmp_name, $upload_dir . $new_filename)) {
                        $uploaded_files[] = $upload_dir . $new_filename;
                    }
                }
            }

            $uploaded_files_json = json_encode($uploaded_files, JSON_UNESCAPED_UNICODE);
            
            $updateSql = "UPDATE booking_info SET
                            program_name = '$programName',
                            program_date = '$programDate',
                            program_time = '$programTime',
                            participant_count = '$participantCount',
                            program_place = '$programPlace',
                            program_content = '$programContent',
                            uploaded_files = '$uploaded_files_json'
                          WHERE idx = '$programIdx'";

            if (mysqli_query($conn, $updateSql)) {
                
                echo "<script>alert('프로그램 정보 변경이 완료되었습니다.'); location.href = '../../index.php?url=booking_list';</script>";
            } else {
                echo "<script>alert('실패하였습니다.');</script>";
                echo '<script>history.back();</script>';
                exit();
            }
        }
    }

?>

