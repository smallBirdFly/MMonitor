CREATE TABLE `scount1481350265` (
                `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                  `spmcode` varchar(40) NOT NULL,
                  `ip` char(15) NOT NULL COMMENT '返回的内容',
                  `time` char(20) NOT NULL,
                  `referrer` varchar(100) NOT NULL,
                  `message` varchar(100) NOT NULL,
                  `created_at` char(20) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;INSERT INTO `scount1481350265` ( `spmcode`, `ip`,`time`,`referrer`,  `message`,`created_at`) VALUES('123.0','192.168.1.109','2016-12-10 14:09:03','','["wqewqoieq"]','2016-12-10 14:12:00'),('123.1','192.168.1.109','2016-12-10 14:08:39','','','2016-12-10 14:12:00')