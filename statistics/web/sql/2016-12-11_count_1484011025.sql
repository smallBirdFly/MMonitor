CREATE TABLE `2016-12-11_count_1484011025` (
                  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                  `appkey` char(9) NOT NULL,
                  `page` int(11) NOT NULL,
                  `type` char(3) NOT NULL,
                  `ip` char(15) NOT NULL COMMENT '返回的内容',
                  `time` char(20) NOT NULL,
                  `referrer` varchar(100) NOT NULL,
                  `message` varchar(100) NOT NULL,
                  `visit` char(1) NOT NULL,
                  `created_at` datetime NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;INSERT INTO `2016-12-11_count_1484011025` ( `appkey`,`page`,`type`, `ip`,`time`,`referrer`,`message`,`visit`,`created_at`) VALUES