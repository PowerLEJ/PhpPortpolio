<?php
    /**
     * 날씨 수집 api
     * 1시간마다 batch job
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-12-27
     */

    require_once "../../config.php";

    require_once "../MyLibraries/CommonLibrary.php";
    $commLib = new \Power\CommonLibrary(); // 공통 라이브러리 클래스 생성

    /**
     * 랜덤으로 두 변수 중 하나를 선택하고, + 또는 - 연산자를 선택하여 계산하는 함수
     * 
     * @param double $robot_price 현재 가격
     * @param double $robot_start_price 시작 가격
     * @param double $value 연산할 값
     * @return double 결과값 반환
     */
    function randomOperationResult($robot_price, $robot_start_price, $value) {

        // 두 변수 중 랜덤으로 하나를 선택
        $selected_price = rand(0, 1) ? $robot_price : $robot_start_price;

        // 랜덤으로 + 또는 - 연산자 선택
        $operation = rand(0, 1) ? '+' : '-';

        // 선택된 연산자에 따라 계산
        if ($operation == '+') { return $selected_price + $value; } 
        else { return $selected_price - $value; }

    } // function randomOperationResult($robot_price, $robot_start_price, $value) {}

    $sql = "SELECT idx, latitude, longitude, robot_price, robot_start_price FROM robot_info WHERE del_check = 0;";
    $result = mysqli_query($conn, $sql);
    while($row = mysqli_fetch_assoc($result)) {

        $value = $result_value = 0;
        $idx = "";

        $robot_idx = $row['idx'];
        $robot_price = $row['robot_price'];
        $robot_start_price = $row['robot_start_price'];
        $lat = $row['latitude'];
        $lon = $row['longitude'];
    
        // 날씨 수집 api
        $apiUrl = "http://api.openweathermap.org/data/2.5/weather?lat=" . $lat . "&lon=" . $lon . "&appid=" . WEATHER_MAP_API_KEY . "&units=metric&lang=en";
    
        $ch = curl_init();
    
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
    
        curl_close($ch);
    
        $data = json_decode($response);
    
        $code = $data->cod;
        
        if(429 != $code && 401 != $code && 200 == $code) {
            
            $temp = $data->main->temp; // 온도
            $temp_min = $data->main->temp_min; // 최저온도
            $temp_max = $data->main->temp_max; // 최고온도

            $value = $temp_max + $temp_min - $temp; // 값 = 최고온도 + 최저온도 - 온도

            $result_value = randomOperationResult($robot_price, $robot_start_price, $value); // 함수 호출

            $idx = $commLib->generateRandomString();
            
            $sql = sprintf("INSERT INTO robot_stock_prices (idx, robot_idx, robot_price) 
                                    VALUES ('%s', '%s', '%s');", $idx, $robot_idx, $result_value);
            $result_02 = mysqli_query($conn, $sql);

            $sql = "UPDATE robot_info SET robot_price = $result_value WHERE idx = '$robot_idx'";
            $result_02 = mysqli_query($conn, $sql);
    
        }
    }

?>
