<?php 
    /**
     * 관리자가 홈 화면에서 프로그램 일정 등록
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-11-16
     */

    require_once "../../config.php";

    require_once "../MyLibraries/CommonLibrary.php";
    $commLib = new \Power\CommonLibrary(); // 공통 라이브러리 클래스 생성

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        // 필수 값 체크
        if (empty($_POST["program_name"]) || empty($_POST["participant_count"]) || 
            empty($_POST["program_date"]) || empty($_POST["program_time"])) {
            echo '<script>alert("필수 항목을 모두 입력하세요.");</script>';
            echo '<script>history.back();</script>';
            exit();
        }

        $program_name = $_POST['program_name'];
        $program_content = $_POST['program_content'];
        $program_date = $_POST['program_date'];
        $program_time = $_POST['program_time'];
        $program_place = $_POST['program_place'];
        $participant_count = $_POST['participant_count'];


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

        $idx = $commLib->generateRandomString();
        $uploaded_files_json = json_encode($uploaded_files, JSON_UNESCAPED_UNICODE);

        $sql = sprintf(
            "INSERT INTO booking_info (idx, program_name, program_content, program_date, program_time, program_place, 
                                        participant_count, uploaded_files) 
            VALUES ('%s', '%s', '%s', '%s', '%s', '%s', 
                    '%d', '%s')",
            $idx, $program_name, $program_content, $program_date, $program_time, $program_place, 
            $participant_count, $uploaded_files_json
        );

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('등록되었습니다.');</script>";
            echo "<script>location.replace('../../index.php?url=booking_list');</script>";
        } else {
            echo "<script>alert('등록 중 오류가 발생했습니다.');</script>";
            echo "<script>history.back();</script>";
        }

    }


?>