CREATE TABLE `user_info` (
  `idx` char(32) NOT NULL COMMENT '사용자의 IDX',
  `user_level` tinyint(4) DEFAULT 0 COMMENT '사용자 레벨 (회원 : 0, 정회원 : 1, 최고관리자 : 9)',
  `user_name` varchar(200) DEFAULT NULL COMMENT '사용자 이름',
  `user_id` varchar(200) DEFAULT NULL COMMENT '사용자 ID',
  `password` blob DEFAULT NULL COMMENT '사용자 비밀번호',
  `user_email` varchar(200) DEFAULT NULL COMMENT '사용자 이메일',
  `user_token` char(64) DEFAULT NULL COMMENT '사용자 토큰',
  `reg_date` datetime DEFAULT current_timestamp() COMMENT '등록일',
  `del_check` tinyint(4) DEFAULT 0 COMMENT '탈퇴 체크 (0:기본, 1:탈퇴)',
  `del_date` datetime DEFAULT NULL COMMENT '탈퇴일',
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci COMMENT = '사용자 정보';


CREATE TABLE `booking_info` (
  `idx` char(32) NOT NULL COMMENT '프로그램의 IDX',
  `program_name` varchar(100) DEFAULT NULL COMMENT '프로그램명',
  `program_content` text DEFAULT NULL COMMENT '프로그램 내용',
  `program_date` date DEFAULT NULL COMMENT '프로그램 일자',
  `program_time` time DEFAULT NULL COMMENT '프로그램 시작 시간',
  `participant_count` int(11) DEFAULT 0 COMMENT '프로그램 수용 가능 인원',
  `booking_count` int(11) DEFAULT 0 COMMENT '프로그램 예약된 인원',
  `program_place` varchar(255) DEFAULT NULL COMMENT '프로그램 수행 장소',
  `uploaded_files` text DEFAULT NULL COMMENT '프로그램에 대한 이미지 JSON',
  `reg_date` datetime DEFAULT current_timestamp() COMMENT '등록일',
  `del_check` tinyint(4) DEFAULT 0 COMMENT '삭제 체크 (0:기본, 1:삭제)',
  `del_date` datetime DEFAULT NULL COMMENT '삭제일',
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci COMMENT = '프로그램의 정보';

CREATE TABLE `booking_list` (
  `idx` char(32) NOT NULL COMMENT '예약 정보의 IDX',
  `program_idx` char(32) NOT NULL COMMENT '프로그램의 IDX',
  `user_idx` char(32) NOT NULL COMMENT '회원의 IDX',
  `user_count` int(11) DEFAULT 0 COMMENT '회원이 예약한 인원',
  `user_phone` varchar(100) DEFAULT NULL COMMENT '회원의 전화번호',
  `cancel_check` tinyint(4) DEFAULT 0 COMMENT '예약 취소 체크 (0:기본, 1:취소)',
  `cancel_date` datetime DEFAULT NULL COMMENT '예약 취소일',
  `reg_date` datetime DEFAULT current_timestamp() COMMENT '등록일',
  `del_check` tinyint(4) DEFAULT 0 COMMENT '삭제 체크 (0:기본, 1:삭제)',
  `del_date` datetime DEFAULT NULL COMMENT '삭제일',
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci COMMENT = '회원의 프로그램 예약 정보';


CREATE TABLE notice_list (
  `idx` char(32) NOT NULL COMMENT '공지사항의 IDX',
  `pub_idx` char(32) NOT NULL COMMENT '게시자의 IDX',
  `title` VARCHAR(255) NOT NULL COMMENT '공지사항의 제목',
  `content` TEXT NOT NULL COMMENT '공지사항의 내용',
  `views` int(11) DEFAULT 0 COMMENT '조회수',
  `reg_date` datetime DEFAULT current_timestamp() COMMENT '등록일',
  `up_date` datetime DEFAULT NULL COMMENT '수정일',
  `del_check` tinyint(4) DEFAULT 0 COMMENT '삭제 체크 (0:기본, 1:삭제)',
  `del_date` datetime DEFAULT NULL COMMENT '삭제일',
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci COMMENT = '공지사항의 정보';

CREATE TABLE notice_attach (
  `idx` char(32) NOT NULL COMMENT '공지사항 첨부파일의 IDX',
  `notice_idx` char(32) NOT NULL COMMENT '공지사항의 IDX',
  `file_name` VARCHAR(255) NOT NULL COMMENT '공지사항 첨부파일의 이름',
  `file_path` VARCHAR(255) NOT NULL COMMENT '공지사항의 첨부파일의 경로',
  `reg_date` datetime DEFAULT current_timestamp() COMMENT '등록일',
PRIMARY KEY (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci COMMENT = '공지사항의 첨부파일';


CREATE TABLE `robot_info` (
  `idx` char(32) NOT NULL COMMENT '로봇의 IDX',
  `robot_name` varchar(100) DEFAULT NULL COMMENT '로봇명',
  `robot_start_price` DECIMAL(10, 2) DEFAULT 0 COMMENT '로봇 시작 가격',
  `robot_price` DECIMAL(10, 2) DEFAULT 0 COMMENT '로봇 가격',
  `robot_count` INT DEFAULT 1 COMMENT '로봇 갯수',
  `robot_left_count` INT DEFAULT 1 COMMENT '로봇 남은 갯수',
  `robot_content` text DEFAULT NULL COMMENT '로봇 내용',
  `robot_place` varchar(255) DEFAULT NULL COMMENT '로봇 장소',
  `venue_address` varchar(255) DEFAULT NULL COMMENT '로봇 주소',
  `latitude` decimal(10,7) DEFAULT 0.0000000 COMMENT '로봇 주소의 위도',
  `longitude` decimal(10,7) DEFAULT 0.0000000 COMMENT '로봇 주소의 경도',
  `uploaded_files` text DEFAULT NULL COMMENT '로봇에 대한 이미지 JSON',
  `reg_date` datetime DEFAULT current_timestamp() COMMENT '등록일',
  `del_check` tinyint(4) DEFAULT 0 COMMENT '삭제 체크 (0:기본, 1:삭제)',
  `del_date` datetime DEFAULT NULL COMMENT '삭제일',
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci COMMENT = '로봇의 정보';

CREATE TABLE `robot_stock` (
  `idx` char(32) NOT NULL COMMENT '매수매도의 IDX',
  `robot_idx` char(32) NOT NULL COMMENT '로봇의 IDX',
  `user_idx` char(32) NOT NULL COMMENT '사용자의 IDX',
  `user_robot_price` decimal(10,2) DEFAULT NULL COMMENT '현재 로봇 가격',
  `user_robot_count` INT DEFAULT 0 COMMENT '사용자의 로봇 갯수',
  `user_trade_price` decimal(10,2) DEFAULT 0.00 COMMENT '매수 매도 총액',
  `user_status` tinyint(4) DEFAULT 0 COMMENT '거래 상태 (0: 매수, 1: 매도)',
  `user_trade_time` datetime DEFAULT current_timestamp() COMMENT '매수 매도 일시',
  `del_check` tinyint(4) DEFAULT 0 COMMENT '삭제 체크 (0:기본, 1:삭제)',
  `del_date` datetime DEFAULT NULL COMMENT '삭제일',
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci COMMENT='매수매도의 정보';


CREATE TABLE `robot_stock_prices` (
  `idx` char(32) NOT NULL COMMENT '로봇 가격에 대한 IDX',
  `robot_idx` char(32) NOT NULL COMMENT '로봇의 IDX',
  `robot_price` decimal(10,2) DEFAULT NULL COMMENT '현재 로봇 가격',
  `reg_date` datetime DEFAULT current_timestamp() COMMENT '로봇 가격 정보 등록일',
  `del_check` tinyint(4) DEFAULT 0 COMMENT '삭제 체크 (0:기본, 1:삭제)',
  `del_date` datetime DEFAULT NULL COMMENT '삭제일',
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci COMMENT='로봇 가격의 정보';


CREATE TABLE chat_messages (
  `idx` char(32) NOT NULL COMMENT 'IDX',
  `user_level` tinyint(4) DEFAULT 0 COMMENT '사용자 레벨 (회원 : 0, 정회원 : 1, 최고관리자 : 9)',
  `admin_idx` char(32) COMMENT '관리자의 IDX',
  `user_idx` char(32) COMMENT '사용자의 IDX',
  `user_msg` TEXT COMMENT '사용자의 메시지',
  `reg_date` datetime DEFAULT current_timestamp() COMMENT '메시지의 등록일',
  `del_check` tinyint(4) DEFAULT 0 COMMENT '삭제 체크 (0:기본, 1:삭제)',
  `del_date` datetime DEFAULT NULL COMMENT '삭제일',
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci COMMENT='메시지 정보';


CREATE TABLE payments (
  `idx` char(32) NOT NULL COMMENT 'IDX',
  `user_idx` char(32) NOT NULL COMMENT 'user IDX',
  `amount` DECIMAL(10, 2) NOT NULL COMMENT '결제 금액 (totalAmount)',
  `order_id` VARCHAR(255) COMMENT '주문 ID',
  `tid` VARCHAR(255) COMMENT '결제 거래 ID',
  `payment_status` tinyint(4) DEFAULT 0 COMMENT '결제 상태 (1:성공, 0:실패, 2:취소)',
  `trade_status` tinyint(4) DEFAULT 0 COMMENT '매수매도 상태 (0:기본, 1:매수, 2:매도)',
  `user_trade_time` datetime COMMENT '매수 매도 일시',
  `reg_date` datetime DEFAULT current_timestamp() COMMENT '메시지의 등록일',
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci COMMENT='결제 정보';