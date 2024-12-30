<?php
    /**
     * 공지사항
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-11-16
     */

    require_once "../../config.php";

    require_once "../MyLibraries/CommonLibrary.php";
    $commLib = new \Power\CommonLibrary(); // 공통 라이브러리 클래스 생성

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $code = $_POST['code'];

        if($code == "notice_add") {
            $pub_idx = mysqli_real_escape_string($conn, base64_decode($_POST['d0']));
    
            $title = mysqli_real_escape_string($conn, $_POST['title']);
            $content = mysqli_real_escape_string($conn, $_POST['content']);
    
            // 공지사항 데이터 저장
            $sql = sprintf("INSERT INTO notice_list (idx, pub_idx, title, content) VALUES ('%s', '%s', '%s', '%s');", $commLib->generateRandomString(), $pub_idx, $title, $content);
    
            if (mysqli_query($conn, $sql)) {
                
                
                $sql = sprintf("SELECT idx FROM notice_list WHERE pub_idx = '%s' AND title = '%s' AND content = '%s' AND del_check = 0 ORDER BY reg_date DESC LIMIT 1;",
                                $pub_idx, $title, $content    
                                );
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_array($result);
                $notice_id = $row['idx'];
                
                // 첨부파일 업로드 처리
                if (!empty($_FILES['files']['name'][0])) {
                    $upload_dir = "../../uploads/uploads_notice/";
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
    
                    foreach ($_FILES['files']['name'] as $key => $filename) {
                        $tmp_name = $_FILES['files']['tmp_name'][$key];
                        $unique_name = uniqid() . "_" . basename($filename);
                        $file_path = $upload_dir . $unique_name;
    
                        if (move_uploaded_file($tmp_name, $file_path)) {
                            $file_sql = sprintf("INSERT INTO notice_attach (idx, notice_idx, file_name, file_path) VALUES ('%s', '%s', '%s', '%s')", 
                                                        $commLib->generateRandomString(), $notice_id, $filename, $file_path);
                            mysqli_query($conn, $file_sql);
                        }
                    }
                }
    
                echo "공지사항이 성공적으로 등록되었습니다.";
            } else {
                echo "등록 오류: " . mysqli_error($conn);
            }

        }

        if ($code == 'notice_edit') {
            // 공지사항 수정 처리
            $notice_id = mysqli_real_escape_string($conn, $_POST['notice_id']);
            $title = mysqli_real_escape_string($conn, $_POST['title']);
            $content = mysqli_real_escape_string($conn, $_POST['content']);
            
            // 공지사항 업데이트
            $update_sql = "UPDATE notice_list SET title = '$title', content = '$content' WHERE idx = '$notice_id' AND del_check = 0";
            if (mysqli_query($conn, $update_sql)) {
    
                // 첨부파일 업로드 처리
                if (!empty($_FILES['files']['name'][0])) {
                    $upload_dir = "../../uploads/uploads_notice/";
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
    
                    foreach ($_FILES['files']['name'] as $key => $filename) {
                        $tmp_name = $_FILES['files']['tmp_name'][$key];
                        $unique_name = uniqid() . "_" . basename($filename);
                        $file_path = $upload_dir . $unique_name;
    
                        if (move_uploaded_file($tmp_name, $file_path)) {
                            $file_sql = sprintf("INSERT INTO notice_attach (idx, notice_idx, file_name, file_path) VALUES ('%s', '%s', '%s', '%s')", 
                                                        $commLib->generateRandomString(), $notice_id, $filename, $file_path);
                            mysqli_query($conn, $file_sql);
                        }
                    }
                }
    
                // 첨부파일 삭제 처리
                if (!empty($_POST['delete_files'])) {
                    foreach ($_POST['delete_files'] as $file_idx) {
                        // 삭제할 파일 정보 가져오기
                        $file_sql = "SELECT * FROM notice_attach WHERE idx = '$file_idx'";
                        $file_result = mysqli_query($conn, $file_sql);
                        $file = mysqli_fetch_assoc($file_result);
    
                        if ($file) {
                            // 파일 삭제
                            unlink($file['file_path']);
    
                            // 데이터베이스에서 파일 삭제
                            $delete_sql = "DELETE FROM notice_attach WHERE idx = '$file_idx'";
                            mysqli_query($conn, $delete_sql);
                        }
                    }
                }
    
                echo "공지사항이 성공적으로 수정되었습니다.";
                // 수정 후 notice_list로 리다이렉트
                header("Location: ../../index.php?url=notice_list");
            } else {
                echo "수정 오류: " . mysqli_error($conn);
            }
        }

    }

    mysqli_close($conn);
?>
