<?php
    /**
     * 라우터(Router) 형태의 인덱스(Index) 파일
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-11-09
     */

    /* PHP Operating mode. */
    error_reporting(0);
    /* PHP Development mode. */
    // error_reporting(E_ALL);
    // ini_set('display_errors', TRUE);
    // ini_set('display_startup_errors', TRUE);
    date_default_timezone_set('Asia/Seoul');

    require_once __DIR__ . "/config.php";

    require_once __DIR__ . '/home/libs/log4php/Logger.php';
    Logger::configure("./config.xml");
    $pathName = pathinfo($_SERVER['SCRIPT_FILENAME']);
    $log = Logger::getLogger($pathName['basename']);

    // Form으로 전송된 데이터 추출하기
    $selectURI = ((isset($_GET['url']) && !empty($_GET['url'])) ? $_GET['url'] : '');

    // White List
    $aryPHPList = array();
    $aryPHPList = [
                    "dashboard", "_header", "_footer", "_navbar",
                    "signup", "find_account", "pass_change",
                    "login", "profile_chk", "profile",
                    "booking_list", 
                    "notice_list", "notice_info", "notice_edit",
                    "trade_list", "trade_info", "trade_edit",
                    "chat_do", "chat_list",
                    "payment",
                ];

    $body = 'dashboard';

    // 호출 방법: http://[domain]:port_number/index.php?url=[file_name]
    if (isset($selectURI) && !empty($selectURI)) {
        if (in_array($selectURI, $aryPHPList)) {
            $body = $selectURI;
        } else {
            header('Location: /');
            exit();
        }
    }

    require_once __DIR__ .  "/home/_header.php";
    require_once __DIR__ .  "/home/" . $body . ".php";
    require_once __DIR__ .  "/home/_footer.php";

    // Clean up code
    if (isset($aryPHPList)) {
        unset($aryPHPList);
    }
?>