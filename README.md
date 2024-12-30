## 버전 정보  

```
>> php --version
PHP 8.3.13 (cli) (built: Oct 22 2024 18:39:14) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.3.13, Copyright (c) Zend Technologies
    with Zend OPcache v8.3.13, Copyright (c), by Zend Technologies
```  

```
>> mariadb --version
mariadb from 11.5.2-MariaDB, client 15.2 for osx10.19 (arm64) using  EditLine wrapper
```  

```
>> composer --version
Composer version 2.8.2 2024-10-29 16:12:11
PHP version 8.3.13 (/opt/homebrew/Cellar/php/8.3.13_1/bin/php)
Run the "diagnose" command to get more detailed diagnostics output.
```  

## 설치  
```
composer require phpoffice/phpspreadsheet
composer require cboden/ratchet
```  

## config.php 설정  
```
    // DB
    define('MYSQL_HOST', '');
    define('MYSQL_USER', '');
    define('MYSQL_PASSWORD', '');
    define('MYSQL_DB', '');
    define('MYSQL_PORT', '');
    define('MYSQL_CHARSET', 'utf8mb4');

    $conn = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB, MYSQL_PORT);

    // PHPMailer
    define('SMTP_USER', '');
    define('SMTP_PASS', '');
    define('WEB_SITE', '');

    // developers.kakao.com JavaScript Key (Kakao Map API)
    define('KAKAO_MAP_API_KEY', '');

    // developers.kakao.com Admin Key (KakaoPay API)
    define('YOUR_KAKAO_ADMIN_KEY', '');

    // OpenWeatherMap API
    define('WEATHER_MAP_API_KEY', '');
```  

## 웹소켓 서버 실행  
```
php -e chat/server.php
```  

## OpenWeatherMap API json 결과 예시  
### 무료 플랜에서는 하루에 최대 1,000회 API 호출 가능  
```json
{
    "coord": {
        "lon": 126.9788,
        "lat": 37.5674
    },
    "weather": [
        {
            "id": 802,
            "main": "Clouds",
            "description": "scattered clouds",
            "icon": "03d"
        }
    ],
    "base": "stations",
    "main": {
        "temp": 0.89,
        "feels_like": -3.59,
        "temp_min": -0.31,
        "temp_max": 1.66,
        "pressure": 1024,
        "humidity": 39,
        "sea_level": 1024,
        "grnd_level": 1017
    },
    "visibility": 10000,
    "wind": {
        "speed": 4.63,
        "deg": 270
    },
    "clouds": {
        "all": 40
    },
    "dt": 1735284653,
    "sys": {
        "type": 1,
        "id": 8105,
        "country": "KR",
        "sunrise": 1735253141,
        "sunset": 1735287621
    },
    "timezone": 32400,
    "id": 1835848,
    "name": "Seoul",
    "cod": 200
}
```  