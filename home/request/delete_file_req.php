<?php
    require_once "../../config.php";

    $programIdx = mysqli_real_escape_string($conn, $_GET['program_idx']);
    $filePath = urldecode(mysqli_real_escape_string($conn, $_GET['file_path']));

    // 파일 삭제
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // JSON에서 파일 경로 제거
    $query = "SELECT uploaded_files FROM booking_info WHERE idx = '$programIdx'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $files = json_decode($row['uploaded_files'], true) ?: [];

    $updatedFiles = array_filter($files, function ($file) use ($filePath) {
        return $file !== $filePath;
    });

    // 업데이트된 JSON 저장
    $newJson = mysqli_real_escape_string($conn, json_encode($updatedFiles));
    $updateQuery = "UPDATE booking_info SET uploaded_files = '$newJson' WHERE idx = '$programIdx'";
    mysqli_query($conn, $updateQuery);

    echo "<script>alert('파일이 삭제되었습니다.'); location.href = '../../index.php?url=booking_list';</script>";
?>
