CREATE TABLE `scount1481369355` (
                `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                  `spmcode` varchar(40) NOT NULL,
                  `ip` char(15) NOT NULL COMMENT '返回的内容',
                  `time` char(20) NOT NULL,
                  `referrer` varchar(100) NOT NULL,
                  `message` varchar(100) NOT NULL,
                  `created_at` char(20) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;INSERT INTO `scount1481369355` ( `spmcode`, `ip`,`time`,`referrer`,  `message`,`created_at`) VALUES('123.1','192.168.1.109','2016-12-10 18:55:43','','','2016-12-10 19:00:35'),('123.1','192.168.1.109','2016-12-10 19:03:28','','','2016-12-10 19:03:36'),('123.1','192.168.1.109','2016-12-10 19:10:09','','','2016-12-10 19:10:17'),('123.1','192.168.1.109','2016-12-10 19:26:21','','','2016-12-10 19:26:28')