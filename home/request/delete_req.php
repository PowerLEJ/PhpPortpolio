<?php
    /**
     * 삭제
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-11-16
     */

    require_once "../../config.php";

    $code = isset($_POST['code']) ? $_POST['code'] : '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        try {
            switch ($code) {
                case 'book_info':
                    // 예약 정보 삭제
                    if (!empty($_POST['selected_ids'])) {
                        $selectedIds = $_POST['selected_ids']; // 선택된 예약 ID 배열

                        for ($i = 0; $i < count($selectedIds); $i++) {
                            // 해당 ID의 uploaded_files와 program_idx 가져오기
                            $sql = sprintf("SELECT uploaded_files, idx FROM booking_info WHERE idx = '%s';", $selectedIds[$i]);
                            $result = mysqli_query($conn, $sql);

                            if ($result && $row = mysqli_fetch_assoc($result)) {
                                $uploadedFiles = json_decode($row['uploaded_files'], true); // JSON 데이터를 배열로 디코드
                                $programIdx = $row['idx']; // 프로그램 IDX

                                if (is_array($uploadedFiles)) {
                                    foreach ($uploadedFiles as $filePath) {
                                        // 파일 삭제
                                        if (file_exists($filePath)) {
                                            if (!unlink($filePath)) {
                                                throw new Exception("파일 삭제 실패: " . $filePath);
                                            }
                                        }
                                    }
                                }

                                // booking_info 테이블 업데이트
                                $updateInfoSql = sprintf(
                                    "UPDATE booking_info 
                                    SET uploaded_files = NULL, del_check = 1, del_date = '%s' 
                                    WHERE idx = '%s';",
                                    date('Y-m-d H:i:s'),
                                    $selectedIds[$i]
                                );
                                $updateInfoResult = mysqli_query($conn, $updateInfoSql);

                                if (!$updateInfoResult) {
                                    throw new Exception('파일 삭제는 성공했지만 booking_info 업데이트에 실패했습니다.');
                                }

                                // booking_list 테이블 업데이트
                                $updateListSql = sprintf(
                                    "UPDATE booking_list 
                                    SET del_check = 1, del_date = '%s' 
                                    WHERE program_idx = '%s';",
                                    date('Y-m-d H:i:s'),
                                    $programIdx
                                );
                                $updateListResult = mysqli_query($conn, $updateListSql);

                                if (!$updateListResult) {
                                    throw new Exception('booking_list 업데이트에 실패했습니다.');
                                }
                            } else {
                                throw new Exception('파일 정보를 불러오지 못했습니다.');
                            }
                        }

                        echo "<script>alert('선택된 항목과 첨부파일이 삭제되었습니다.'); location.href='../../index.php?url=booking_list';</script>";
                    }
                    break;

                case "notice_list":
                    // 공지사항 삭제
                    $selectedIds = $_POST['ids'] ?? []; // 선택된 공지사항 ID 배열

                    if (!empty($selectedIds)) {
                        foreach ($selectedIds as $id) {
                            // 첨부파일 삭제
                            $fileQuery = "SELECT file_path FROM notice_attach WHERE notice_idx = '$id'";
                            $fileResult = mysqli_query($conn, $fileQuery);

                            if ($fileResult && mysqli_num_rows($fileResult) > 0) {
                                while ($fileRow = mysqli_fetch_assoc($fileResult)) {
                                    $filePath = $fileRow['file_path'];
                                    if (file_exists($filePath)) {
                                        if (!unlink($filePath)) {
                                            throw new Exception("파일 삭제 실패: " . $filePath);
                                        }
                                    }
                                }

                                // 첨부파일 테이블에서 데이터 삭제
                                $deleteFileQuery = "DELETE FROM notice_attach WHERE notice_idx = '$id'";
                                if (!mysqli_query($conn, $deleteFileQuery)) {
                                    throw new Exception("첨부파일 삭제 실패: " . mysqli_error($conn));
                                }
                            }

                            // 공지사항 테이블 업데이트
                            $updateQuery = "
                                UPDATE notice_list 
                                SET del_check = 1, del_date = NOW() 
                                WHERE idx = '$id'
                            ";
                            $updateResult = mysqli_query($conn, $updateQuery);

                            if (!$updateResult) {
                                throw new Exception("공지사항 삭제 중 오류가 발생했습니다: ID $id");
                            }
                        }

                        echo "선택한 공지사항이 삭제되었습니다.";
                    } else {
                        throw new Exception("삭제할 항목이 선택되지 않았습니다.");
                    }
                    break;

                case "delete_file":
                    // 파일 삭제 요청 처리
                    if (isset($_POST['file_id'])) {
                        $file_id = mysqli_real_escape_string($conn, $_POST['file_id']);

                        // 첨부파일 정보 가져오기
                        $sql = "SELECT * FROM notice_attach WHERE idx = '$file_id'";
                        $result = mysqli_query($conn, $sql);
                        $file = mysqli_fetch_assoc($result);

                        if ($file) {
                            // 파일 경로 삭제
                            $file_path = $file['file_path'];
                            if (file_exists($file_path)) {
                                if (!unlink($file_path)) {
                                    throw new Exception("파일 삭제 실패: " . $file_path);
                                }
                            }

                            // 데이터베이스에서 파일 삭제
                            $delete_sql = "DELETE FROM notice_attach WHERE idx = '$file_id'";
                            if (!mysqli_query($conn, $delete_sql)) {
                                throw new Exception("파일 삭제 실패: " . mysqli_error($conn));
                            }

                            echo "파일이 성공적으로 삭제되었습니다.";
                        } else {
                            throw new Exception("파일을 찾을 수 없습니다.");
                        }
                    }
                    break;
                case "robot_info":
                    // 로봇 정보 삭제
                    $selectedIds = $_POST['selected_ids'] ?? []; // 선택된 로봇 ID 배열
                    $js = $_POST['js'] ?? 0;
    
                    if (!empty($selectedIds)) {
                        $now_time = date('Y-m-d H:i:s'); // 현재 시간
    
                        foreach ($selectedIds as $robotId) {
                            // 1. 로봇 정보 삭제 (robot_info 테이블)
                            $sql = sprintf("SELECT uploaded_files, idx FROM robot_info WHERE idx = '%s';", $robotId);
                            $result = mysqli_query($conn, $sql);
    
                            if ($result && $row = mysqli_fetch_assoc($result)) {
                                $uploadedFiles = $row['uploaded_files']; // 파일 목록
    
                                // 2. uploaded_files 값이 비어 있지 않다면 JSON 디코딩
                                if (!empty($uploadedFiles)) {
                                    $uploadedFilesArray = json_decode($uploadedFiles, true); // JSON 데이터를 배열로 디코드
    
                                    // 3. 로봇 이미지 파일 삭제
                                    if (is_array($uploadedFilesArray)) {
                                        foreach ($uploadedFilesArray as $filePath) {
                                            if (file_exists($filePath)) {
                                                if (!unlink($filePath)) {
                                                    throw new Exception("파일 삭제 실패: " . $filePath);
                                                }
                                            }
                                        }
                                    }
                                }
    
                                // 4. robot_info 테이블 업데이트 (삭제 표시 및 삭제일자)
                                $updateRobotInfoSql = sprintf(
                                    "UPDATE robot_info 
                                    SET del_check = 1, del_date = '%s' 
                                    WHERE idx = '%s';",
                                    $now_time,
                                    $robotId
                                );
                                $updateRobotInfoResult = mysqli_query($conn, $updateRobotInfoSql);
    
                                if (!$updateRobotInfoResult) {
                                    throw new Exception('로봇 정보 업데이트에 실패했습니다.');
                                }
    
                                // 5. robot_stock 테이블에서 해당 로봇 관련 항목 업데이트 (삭제 표시 및 삭제일자)
                                $updateRobotStockSql = sprintf(
                                    "UPDATE robot_stock 
                                    SET del_check = 1, del_date = '%s' 
                                    WHERE robot_idx = '%s';",
                                    $now_time,
                                    $robotId
                                );
                                $updateRobotStockResult = mysqli_query($conn, $updateRobotStockSql);
    
                                if (!$updateRobotStockResult) {
                                    throw new Exception('robot_stock 업데이트에 실패했습니다.');
                                }
                            } else {
                                throw new Exception('로봇 정보 불러오기 실패');
                            }
                        }
                        
                        if($js == 1) {
                            echo "선택된 로봇과 관련된 항목들이 삭제되었습니다.";
                        } else {
                            echo "<script>alert('선택된 로봇과 관련된 항목들이 삭제되었습니다.'); location.href='../../index.php?url=robot_info';</script>";
                        }
                        
                    } else {
                        throw new Exception("삭제할 로봇 항목이 선택되지 않았습니다.");
                    }
                    break;
                case "chat_info":
                    // 문의 정보 삭제
                    $selectedIds = $_POST['selected_ids'] ?? []; // 선택된 user_idx 배열
    
                    if (!empty($selectedIds)) {
                        $now_time = date('Y-m-d H:i:s'); // 현재 시간
    
                        foreach ($selectedIds as $userId) {
                            // 1. 문의 정보 삭제 (chat_messages 테이블)
                            $sql = sprintf("SELECT * FROM chat_messages WHERE user_idx = '%s' AND del_check=0;", $userId);
                            $result = mysqli_query($conn, $sql);
    
                            if ($result && $row = mysqli_fetch_assoc($result)) {
                                
                                // 2. chat_messages 테이블 업데이트 (삭제 표시 및 삭제일자)
                                $updateChatInfoSql = sprintf(
                                    "UPDATE chat_messages 
                                    SET del_check = 1, del_date = '%s' 
                                    WHERE user_idx = '%s' AND del_check=0;",
                                    $now_time,
                                    $userId
                                );
                                $updateChatInfoResult = mysqli_query($conn, $updateChatInfoSql);
    
                                if (!$updateChatInfoResult) {
                                    throw new Exception('robot_stock 업데이트에 실패했습니다.');
                                }
                            } else {
                                throw new Exception('문의 정보 불러오기 실패');
                            }
                        }
                        
                        echo "<script>alert('문의가 삭제되었습니다.'); location.href='../../index.php?url=chat_list';</script>";
                        
                    } else {
                        throw new Exception("삭제할 문의를 선택해주세요.");
                    }
                    break;
                default:
                    break;
            } // switch ($code) {}
        } catch (Exception $e) {
            // 예외 처리: 오류 메시지를 출력하고, 추가 작업을 할 수 있음
            echo "<script>alert('오류 발생: " . $e->getMessage() . "');</script>";
            exit(); // 오류 발생 시 종료
        } // try {}
    } // if ($_SERVER['REQUEST_METHOD'] === 'POST')
?>
