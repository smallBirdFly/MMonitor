CREATE TABLE `scount1481263598` (
                `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                  `spmcode` varchar(40) NOT NULL,
                  `ip` char(15) NOT NULL COMMENT '返回的内容',
                  `time` char(20) NOT NULL,
                  `url` varchar(100) NOT NULL,
                  `message` varchar(100) NOT NULL,
                  `created_at` char(20) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;INSERT INTO `scount1481263598` ( `spmcode`, `ip`,`time`,`url`,  `message`,`created_at`) VALUES('123.12.-1','192.168.1.109','1481263594','','[""]','2016-12-09 14:12:36'),('123.12.1','192.168.1.109','1481263594','','','2016-12-09 14:12:36')