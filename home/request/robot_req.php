<?php 
    /**
     * 관리자가 홈 화면에서 로봇 정보 등록 & 사용자가 로봇 매수, 매도
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-11-23
     */

    require_once "../../config.php";

    require_once "../MyLibraries/CommonLibrary.php";
    $commLib = new \Power\CommonLibrary(); // 공통 라이브러리 클래스 생성

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $code = $_POST['code'] ? $_POST['code'] : "";

        switch ($code)
        {
            case "regist":
                {
                    // 필수 값 체크
                    if (empty($_POST["robot_name"]) || empty($_POST["robot_price"]) || 
                        empty($_POST["latitude"]) || empty($_POST["latitude"])) {
                        echo '<script>alert("필수 항목을 모두 입력하세요.");</script>';
                        echo '<script>history.back();</script>';
                        exit();
                    }

                    $robot_name = $_POST['robot_name'] ? $_POST['robot_name'] : "";
                    $robot_count = $_POST['robot_count'] ? $_POST['robot_count'] : "";
                    $robot_price = $_POST['robot_price'] ? $_POST['robot_price'] : "";
                    $robot_content = $_POST['robot_content'] ? $_POST['robot_content'] : "";
                    $robot_place = $_POST['robot_place'] ? $_POST['robot_place'] : "";
                    $venue_address = $_POST['venue_address'] ? $_POST['venue_address'] : "";
                    $latitude = $_POST['latitude'] ? $_POST['latitude'] : "";
                    $longitude = $_POST['longitude'] ? $_POST['longitude'] : "";

                    // 파일 업로드 처리
                    $uploaded_files = [];
                    if (!empty($_FILES['uploaded_files']['name'][0])) {
                        $upload_dir = "../../uploads/uploads_robot/";
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

                    // 데이터베이스 저장
                    $idx = $commLib->generateRandomString();
                    $uploaded_files_json = json_encode($uploaded_files, JSON_UNESCAPED_UNICODE);

                    $sql = sprintf(
                        "INSERT INTO robot_info (idx, robot_name, robot_count, robot_left_count, robot_price, robot_start_price, robot_content, robot_place, 
                                                venue_address, latitude, longitude, uploaded_files) 
                                VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
                        $idx, $robot_name, $robot_count, $robot_count, $robot_price, $robot_price, $robot_content, $robot_place, 
                        $venue_address, $latitude, $longitude, 
                        $uploaded_files_json
                    );

                    if (mysqli_query($conn, $sql)) {
                        echo "<script>alert('등록되었습니다.');</script>";
                        echo "<script>location.replace('../../index.php');</script>";
                    } else {
                        echo "<script>alert('등록 중 오류가 발생했습니다.');</script>";
                        echo "<script>history.back();</script>";
                    }

                    break;
                }
            case "buy":
                {
                    // 필수 값 체크
                    if (empty($_POST["robot_idx"]) || empty($_POST["user_buy_count"])) {
                        echo '<script>alert("필수 항목을 모두 입력하세요.");</script>';
                        echo '<script>history.back();</script>';
                        exit();
                    }

                    $user_idx = base64_decode($_POST['d0']); // 사용자 IDX
                    $robot_idx = $_POST['robot_idx']; // 로봇 IDX
                    $buy_count = $_POST['user_buy_count'];


                    // 보유 금액
                    $a_sum = $cnt = 0;
                    $sql = sprintf("SELECT count(*) AS cnt FROM payments WHERE user_idx = '%s' AND (payment_status = 1 OR trade_status IN (1, 2))", $user_idx);
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_array($result);
                    $cnt = $row['cnt'];
                    if($cnt > 0) {
                        $sql = sprintf("SELECT SUM(amount) AS a_sum FROM payments WHERE user_idx = '%s' AND (payment_status = 1 OR trade_status IN (1, 2))", $user_idx);
                        $result = mysqli_query($conn, $sql);
                        $row = mysqli_fetch_array($result);
                        $a_sum = $row['a_sum'];
                    }

                    if($a_sum <= 0) {
                        echo '<script>alert("보유 금액이 부족합니다. 충전 후 이용해주세요. [1]");</script>';
                        echo '<script>history.back();</script>';
                        exit();
                    }

                    // 로봇 정보 가져오기
                    $sql = "SELECT robot_left_count, robot_price FROM robot_info 
                            WHERE idx = '$robot_idx' AND del_check = 0;";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($result);

                    $robot_price = $row['robot_price']; // 로봇의 현재 가격 (매수 가격)
                    $robot_left_count = $row['robot_left_count'];

                    $buy_price = $robot_price * $buy_count;

                    if($buy_price > $a_sum) {
                        echo '<script>alert("보유 금액이 부족합니다. 충전 후 이용해주세요. [2]");</script>';
                        echo '<script>history.back();</script>';
                        exit();
                    }

                    if (!$row) {
                        echo '<script>alert("해당 로봇 정보를 찾을 수 없습니다.");</script>';
                        echo '<script>history.back();</script>';
                        exit();
                    }

                    if ($robot_left_count <= 0) {
                        echo '<script>alert("로봇의 재고가 부족합니다.");</script>';
                        echo '<script>history.back();</script>';
                        exit();
                    }

                    // 매수 처리
                    $new_left_count = $robot_left_count - $buy_count; // 로봇 남은 갯수 차감

                    // 로봇 정보 업데이트
                    $update_sql = "UPDATE robot_info 
                                SET robot_left_count = $new_left_count 
                                WHERE idx = '$robot_idx' AND del_check = 0;";
                    if (!mysqli_query($conn, $update_sql)) {
                        echo '<script>alert("로봇 재고 업데이트에 실패했습니다.");</script>';
                        echo '<script>history.back();</script>';
                        exit();
                    }

                    // 매수 처리
                    $transaction_idx = $commLib->generateRandomString(); // 거래 고유 IDX 생성
                    $insert_sql = "INSERT INTO robot_stock (idx, robot_idx, user_idx, user_robot_count, user_trade_price, user_robot_price, user_status) 
                                VALUES ('$transaction_idx', '$robot_idx', '$user_idx', '$buy_count', '$buy_price' , '$robot_price', 0)";
                    if (!mysqli_query($conn, $insert_sql)) {
                        echo '<script>alert("매수 처리 중 오류가 발생했습니다. [1]");</script>';
                        echo '<script>history.back();</script>';
                        exit();
                    }

                    // payment 업데이트
                    $payment_idx = $commLib->generateRandomString();
                    $sql_payment = sprintf("INSERT INTO payments (idx, user_idx, amount, trade_status) VALUES ('%s', '%s', '%s', '%s');", $payment_idx, $user_idx, -$buy_price, 1);
                    if (!mysqli_query($conn, $sql_payment)) {
                        echo '<script>alert("매수 처리 중 오류가 발생했습니다. [2]");</script>';
                        echo '<script>history.back();</script>';
                        exit();
                    }


                    
                    echo '<script>alert("매수 성공!");</script>';
                    echo '<script>location.replace("../../index.php?url=trade_list&tt=1");</script>';
                    
                    break;
                }
            case "sell":
                {
                    // 필수 값 체크
                    if (empty($_POST["robot_idx"]) || empty($_POST["user_sell_count"])) {
                        echo '<script>alert("필수 항목을 모두 입력하세요.");</script>';
                        echo '<script>history.back();</script>';
                        exit();
                    }

                    $user_idx = base64_decode($_POST['d0']); // 사용자 ID
                    $robot_idx = $_POST['robot_idx']; // 로봇 ID
                    $sell_count = $_POST['user_sell_count']; 

                    // 로봇 정보 가져오기
                    $sql = "SELECT robot_left_count, robot_price FROM robot_info 
                                 WHERE idx = '$robot_idx' AND del_check = 0;";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($result);
                    $robot_price = $row['robot_price']; // 로봇의 현재 가격 (매도 가격)

                    $sell_price = $robot_price * $sell_count;


                    // 사용자 로봇 거래 정보 확인
                    $sql = "SELECT user_robot_count FROM robot_stock 
                            WHERE user_idx = '$user_idx' AND robot_idx = '$robot_idx';";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($result);

                    if (!$row) {
                        echo '<script>alert("매수한 로봇 정보가 없습니다.");</script>';
                        echo '<script>history.back();</script>';
                        exit();
                    }
                    
                    // 사용자 로봇 거래 정보 확인 (매수)
                    $user_robot_count = 0;
                    $sql = "SELECT user_robot_count FROM robot_stock 
                            WHERE user_idx = '$user_idx' AND robot_idx = '$robot_idx' AND user_status = 0;";
                    $result = mysqli_query($conn, $sql);
                    while($row = mysqli_fetch_assoc($result)) {
                        $user_robot_count += $row['user_robot_count'];
                    }

                    // 사용자 로봇 거래 정보 확인 (매도)
                    $sql = "SELECT user_robot_count FROM robot_stock 
                            WHERE user_idx = '$user_idx' AND robot_idx = '$robot_idx' AND user_status = 1;";
                    $result = mysqli_query($conn, $sql);
                    while($row = mysqli_fetch_assoc($result)) {
                        $user_robot_count -= $row['user_robot_count'];
                    }
                    
                    // 매도할 수량이 보유 수량을 초과할 경우 처리
                    if ($sell_count > $user_robot_count) {
                        echo '<script>alert("매도할 수량이 보유한 로봇 수량을 초과합니다.");</script>';
                        echo '<script>history.back();</script>';
                        exit();
                    }

                    if ($user_robot_count <= 0) {
                        echo '<script>alert("보유한 로봇이 없습니다.");</script>';
                        echo '<script>history.back();</script>';
                        exit();
                    }
                
                    // 로봇 정보 가져오기
                    $sql_robot = "SELECT robot_left_count FROM robot_info 
                                  WHERE idx = '$robot_idx' AND del_check = 0;";
                    $result_robot = mysqli_query($conn, $sql_robot);
                    $robot_row = mysqli_fetch_assoc($result_robot);
                
                    if (!$robot_row) {
                        echo '<script>alert("해당 로봇 정보를 찾을 수 없습니다.");</script>';
                        echo '<script>history.back();</script>';
                        exit();
                    }
                
                    $robot_left_count = $robot_row['robot_left_count'];

                    // 매도 처리
                    $transaction_idx = $commLib->generateRandomString(); // 거래 고유 IDX 생성
                    $insert_sql = "INSERT INTO robot_stock (idx, robot_idx, user_idx, user_robot_count, user_trade_price, user_robot_price, user_status) 
                                VALUES ('$transaction_idx', '$robot_idx', '$user_idx', '$sell_count', '$sell_price', '$robot_price', 1)";
                    if (!mysqli_query($conn, $insert_sql)) {
                        echo '<script>alert("매수 처리 중 오류가 발생했습니다. [1]");</script>';
                        echo '<script>history.back();</script>';
                        exit();
                    }

                
                    // 로봇의 남은 갯수 업데이트 (매도 시)
                    $new_left_count = $robot_left_count + $sell_count; // 로봇 남은 갯수 증가
                    $update_robot_sql = "UPDATE robot_info 
                                         SET robot_left_count = $new_left_count 
                                         WHERE idx = '$robot_idx' AND del_check = 0";
                    if (!mysqli_query($conn, $update_robot_sql)) {
                        echo '<script>alert("로봇 재고 업데이트에 실패했습니다.");</script>';
                        echo '<script>history.back();</script>';
                        exit();
                    }

                    // payment 업데이트
                    $payment_idx = $commLib->generateRandomString();
                    $sql_payment = sprintf("INSERT INTO payments (idx, user_idx, amount, trade_status) VALUES ('%s', '%s', '%s', '%s');", $payment_idx, $user_idx, $sell_price, 2);
                    if (!mysqli_query($conn, $sql_payment)) {
                        echo '<script>alert("매도 처리 중 오류가 발생했습니다. [2]");</script>';
                        echo '<script>history.back();</script>';
                        exit();
                    }

                    echo '<script>alert("매도 성공!");</script>';
                    echo '<script>location.replace("../../index.php?url=trade_list&tt=1");</script>';

                    break;
                }
                case "robot_edit":
                    // 필수 값 체크
                    if (empty($_POST["robot_name"]) || empty($_POST["robot_price"]) || 
                        empty($_POST["latitude"]) || empty($_POST["longitude"])) {
                        echo '<script>alert("필수 항목을 모두 입력하세요.");</script>';
                        echo '<script>history.back();</script>';
                        exit();
                    }
                
                    // POST로 전달된 데이터
                    $robot_id = $_POST['robot_id'];
                    $robot_name = $_POST['robot_name'];
                    $robot_count = $_POST['robot_count'];
                    $robot_price = $_POST['robot_price'];
                    $robot_content = $_POST['robot_content'];
                    $robot_place = $_POST['robot_place'];
                    $venue_address = $_POST['venue_address'];
                    $latitude = $_POST['latitude'];
                    $longitude = $_POST['longitude'];


                    // 기존 파일 가져오기 (기존 파일 유지)
                    $sql = "SELECT robot_count, robot_left_count, uploaded_files FROM robot_info WHERE idx = '$robot_id' AND del_check = 0";
                    $result = mysqli_query($conn, $sql);
                    $robot = mysqli_fetch_assoc($result);


                    // 로봇 남은 갯수 계산 논리
                    $origin_robot_count = $origin_robot_left_count = $cal_count = 0;
                    $origin_robot_count = $robot['robot_count'];
                    $origin_robot_left_count = $robot['robot_left_count'];

                    if($robot_count != $origin_robot_count) { $cal_count = $robot_count - $origin_robot_count; }
                    $robot_left_count = $origin_robot_left_count + $cal_count;

                    if($robot_left_count < 0) {
                        echo "<script>alert('로봇 갯수 논리 오류입니다.');</script>";
                        echo "<script>history.back();</script>";
                        exit();
                    }
                    
                
                    // 파일 업로드 처리 (기존 파일은 유지, 새로운 파일만 업로드)
                    $uploaded_files = [];
                    if (!empty($_FILES['uploaded_files']['name'][0])) {
                        $upload_dir = "../../uploads/uploads_robot/";
                        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                
                        // 새로 업로드된 파일들 처리
                        foreach ($_FILES['uploaded_files']['name'] as $key => $filename) {
                            $tmp_name = $_FILES['uploaded_files']['tmp_name'][$key];
                            $file_ext = pathinfo($filename, PATHINFO_EXTENSION);
                            $new_filename = uniqid() . '.' . $file_ext;
                
                            if (move_uploaded_file($tmp_name, $upload_dir . $new_filename)) {
                                // 새로 업로드된 파일 경로를 배열에 추가
                                $uploaded_files[] = $upload_dir . $new_filename;
                            }
                        }
                    }

                    
                
                    // 기존 파일이 있다면 JSON 디코딩하여 배열로 변환
                    $existing_files = !empty($robot['uploaded_files']) ? json_decode($robot['uploaded_files'], true) : [];
                
                    // 새로 업로드된 파일과 기존 파일을 합침
                    $all_files = array_merge($existing_files, $uploaded_files);
                
                    // 합쳐진 파일 목록을 JSON 형식으로 저장
                    $uploaded_files_json = json_encode($all_files, JSON_UNESCAPED_UNICODE);
                
                    // 로봇 정보 업데이트 SQL
                    $sql = sprintf(
                        "UPDATE robot_info SET 
                            robot_name = '%s',
                            robot_count = '%s',
                            robot_left_count = '%s',
                            robot_price = '%s',
                            robot_content = '%s',
                            robot_place = '%s',
                            venue_address = '%s',
                            latitude = '%s',
                            longitude = '%s',
                            uploaded_files = '%s'
                        WHERE idx = '%s'",
                        $robot_name, $robot_count, $robot_left_count, $robot_price, $robot_content, $robot_place,
                        $venue_address, $latitude, $longitude, $uploaded_files_json, $robot_id
                    );
                
                    // SQL 실행 및 결과 처리
                    if (mysqli_query($conn, $sql)) {
                        echo "<script>alert('로봇 정보가 수정되었습니다.');</script>";
                        echo "<script>location.replace('../../index.php?url=trade_list');</script>";
                    } else {
                        echo "<script>alert('수정 중 오류가 발생했습니다.');</script>";
                        echo "<script>history.back();</script>";
                        exit();
                    }
                    break;
                
                
            case "robot_img_del":        
                // Check if POST variables are set
                if (isset($_POST['file_id']) && isset($_POST['robot_id'])) {
                    $fileId = mysqli_real_escape_string($conn, $_POST['file_id']);
                    $robotId = mysqli_real_escape_string($conn, $_POST['robot_id']);

                    // Fetch the robot's current uploaded files
                    $sql = "SELECT uploaded_files FROM robot_info WHERE idx = '$robotId' AND del_check = 0";
                    $result = mysqli_query($conn, $sql);
                    if ($result && mysqli_num_rows($result) > 0) {
                        $robot = mysqli_fetch_assoc($result);
                        $uploadedFiles = json_decode($robot['uploaded_files'], true); // JSON decode

                        // Check if the file exists in the uploaded_files array
                        if ($uploadedFiles && in_array($fileId, $uploadedFiles)) {
                            // Delete the file from the server
                            $filePath = $fileId; // Assuming fileId is the file path
                            if (file_exists($filePath)) {
                                if (unlink($filePath)) {
                                    // Remove the file from the uploaded_files array
                                    $key = array_search($fileId, $uploadedFiles);
                                    if ($key !== false) {
                                        unset($uploadedFiles[$key]);
                                    }

                                    // Re-index array and update the database
                                    $updatedFiles = json_encode(array_values($uploadedFiles));
                                    $updateSql = "UPDATE robot_info SET uploaded_files = '$updatedFiles' WHERE idx = '$robotId'";
                                    if (mysqli_query($conn, $updateSql)) {
                                        echo json_encode(["success" => true]);
                                    } else {
                                        echo json_encode(["success" => false, "message" => "Failed to update database."]);
                                    }
                                } else {
                                    echo json_encode(["success" => false, "message" => "Failed to delete file from server."]);
                                }
                            } else {
                                echo json_encode(["success" => false, "message" => "File does not exist on server."]);
                            }
                        } else {
                            echo json_encode(["success" => false, "message" => "File ID not found in uploaded files."]);
                        }
                    } else {
                        echo json_encode(["success" => false, "message" => "Robot not found or has been deleted."]);
                    }
                } else {
                    echo json_encode(["success" => false, "message" => "Invalid request parameters."]);
                }
    
                break;
            default:
                {
                    echo "<script>alert('잘못된 접근입니다.');</script>";
                    echo "<script>history.back();</script>";
                    break;
                }
        }

    } // if ($_SERVER['REQUEST_METHOD'] == 'POST') {}


?>