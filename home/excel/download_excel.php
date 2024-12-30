<?php
    require_once "../../config.php";

    require_once '../../vendor/autoload.php'; // PhpSpreadsheet 라이브러리 로드

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // 데이터 가져오기
    $sql = "SELECT * FROM notice_list WHERE del_check = 0";
    $result = mysqli_query($conn, $sql);

    // 엑셀 시트 생성
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', '제목');
    $sheet->setCellValue('B1', '작성일');
    $sheet->setCellValue('C1', '조회수');
    $sheet->setCellValue('D1', '내용');

    $rowNum = 2;
    while ($row = mysqli_fetch_assoc($result)) {
        $sheet->setCellValue('A' . $rowNum, $row['title']);
        $sheet->setCellValue('B' . $rowNum, $row['reg_date']);
        $sheet->setCellValue('C' . $rowNum, $row['views']);
        $sheet->setCellValue('D' . $rowNum, $row['content']);
        $rowNum++;
    }

    // 파일 다운로드
    $writer = new Xlsx($spreadsheet);


    $filename = "notice_list_" . date('YmdHis') . ".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
    exit;


    // 데이터베이스 연결 종료
    mysqli_close($conn);
?>
