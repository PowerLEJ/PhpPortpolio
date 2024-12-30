<?php 
    /**
     * 메뉴바
     * 
     * @author      LEJ <ej28power@naver.com>
     * @version     0.0.0.0
     * @since       2024-11-09
     */

    // 세션 시작
    if (!isset($_SESSION)) {
        session_start();
    }

    $user_idx = '';
    $user_id = '';
    $user_name = '';
    $user_email = '';
    $user_level = 0;

    // 쿠키로 로그인 상태 유지하기 (쿠키에 로그인 토큰이 있을 때)
    if (isset($_COOKIE['user_token'])) {
        $token = $_COOKIE['user_token'];

        // DB에서 해당 토큰이 유효한지 확인
        $sql = "SELECT * FROM user_info WHERE user_token = '$token'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);

        // 로그인 상태 확인 후 변수 설정
        if (isset($_SESSION['user_id'])) {
            $user_idx = $row['idx'];
            $user_id = $row['user_id'];
            $user_name = $row['user_name'];
            $user_email = $row['user_email'];
            $user_level = $row['user_level'];
            $user_token = $row['user_token'];
        }
    }

?>

<header>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/" id="home"><i class="bi bi-robot"></i>&nbsp;Robot</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
    
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?= ('1' == $activeMenuNum) ? 'active' : '' ?>" aria-current="page" href="/">홈</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ('5' == $activeMenuNum) ? 'active' : '' ?>" href="../index.php?url=notice_list">공지사항</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ('6' == $activeMenuNum) ? 'active' : '' ?>" href="../index.php?url=trade_list">매매</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ('7' == $activeMenuNum) ? 'active' : '' ?>" href="../index.php?url=booking_list">예약</a>
                    </li>
                    <?php 
                        if(isset($user_id) && '' != $user_id && null != $user_id):
                    ?>
                        <li class="nav-item">
                                <?php 
                                    if($user_level == 9):
                                ?>
                                    <a class="nav-link <?= ('8' == $activeMenuNum) ? 'active' : '' ?>" href="../index.php?url=chat_list">문의</a>
                                <?php 
                                    elseif($user_level == 1):
                                ?>
                                    <a class="nav-link <?= ('8' == $activeMenuNum) ? 'active' : '' ?>" href="../index.php?url=chat_do">문의</a>
                                <?php 
                                    endif;
                                ?>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ('9' == $activeMenuNum) ? 'active' : '' ?>" href="../index.php?url=payment">충전</a>
                        </li>
                    <?php 
                        endif;
                    ?>

                </ul>
                    
                <ul class="navbar-nav mb-2 mb-lg-0">
                    <?php 
                        if(isset($user_id) && '' != $user_id && null != $user_id) {
                    ?>
                        <li class="nav-item" style="float: right;">
                            <a class="nav-link <?= ('4' == $activeMenuNum) ? 'active' : '' ?>" href="../index.php?url=profile_chk">
                                <i class="bi bi-person-circle"></i><?php echo "  $user_id($user_name)";?>
                            </a>
                        </li>
                        <li class="nav-item" style="float: right;">
                            <a class="nav-link" href="../home/request/logout_req.php">로그아웃</a>
                        </li>
                    <?php 
                        } else {
                    ?>
                        <li class="nav-item" style="float: right;">
                            <a class="nav-link <?= ('2' == $activeMenuNum) ? 'active' : '' ?>" href="../index.php?url=signup">회원가입</a>
                        </li>
    
                        <li class="nav-item" style="float: right;">
                            <a class="nav-link <?= ('3' == $activeMenuNum) ? 'active' : '' ?>" href="../index.php?url=login">로그인</a>
                        </li>
                    <?php 
                        }
                    ?>
                </ul>
    
            </div>
        </div>
    </nav>
</header>

<input type="hidden" id="d0" name="d0" value="<?= base64_encode($user_idx) ?>">
<input type="hidden" id="d1" name="d1" value="<?= base64_encode($user_id) ?>">
<input type="hidden" id="d2" name="d2" value="<?= base64_encode($user_name) ?>">
<input type="hidden" id="d3" name="d3" value="<?= base64_encode($user_email) ?>">
<input type="hidden" id="d4" name="d4" value="<?= base64_encode($user_level) ?>">
<input type="hidden" id="d5" name="d5" value="<?= base64_encode($user_token) ?>">