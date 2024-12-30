<?php
    require_once "../../config.php";

    require_once "../MyLibraries/CommonLibrary.php";
    $commLib = new \Power\CommonLibrary(); // 공통 라이브러리 클래스 생성

    require_once "../../vendor/autoload.php"; // PhpSpreadsheet 로드

    use PhpOffice\PhpSpreadsheet\IOFactory;
    use PhpOffice\PhpSpreadsheet\Spreadsheet;

    // 파일이 업로드 되었는지 확인
    if ($_FILES['excel_file']['tmp_name']) {
        $file = $_FILES['excel_file']['tmp_name'];

        // 파일을 로드하여 Spreadsheet 객체로 변환
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();

        // 시트에서 데이터 읽기
        foreach ($sheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $data = [];
            foreach ($cellIterator as $cell) {
                $data[] = $cell->getValue();
            }

            // 첫 번째 행은 헤더이므로 스킵
            if ($row->getRowIndex() > 1) {
                $title = $data[0]; // 제목
                $reg_date = $data[1]; // 작성일
                $views = $data[2]; // 조회수
                $content = $data[3]; // 내용
                $pub_idx = base64_decode($_POST['d0']);

                $idx = $commLib->generateRandomString();

                $sql = "INSERT INTO notice_list (idx, pub_idx, title, views, content) 
                        VALUES ('$idx', '$pub_idx', '$title', '$views', '$content');";

                mysqli_query($conn, $sql);
            }
        }

        // 데이터베이스 연결 종료
        mysqli_close($conn);

        echo "엑셀 파일이 성공적으로 업로드되었습니다.";
    }
?>