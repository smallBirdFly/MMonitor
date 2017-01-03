CREATE TABLE `scount1483424689` (
                  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                  `appkey` char(9) NOT NULL,
                  `page` int(11) NOT NULL,
                  `type` char(3) NOT NULL,
                  `ip` char(15) NOT NULL COMMENT '返回的内容',
                  `time` char(20) NOT NULL,
                  `referrer` varchar(100) NOT NULL,
                  `message` varchar(100) NOT NULL,
                  `hour` char(2) NOT NULL,
                  `visit` char(1) NOT NULL,
                  `created_at` datetime NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;INSERT INTO `scount1483424689` ( `appkey`,`page`,`type`, `ip`,`time`,`referrer`,`message`,`hour`,`visit`,`created_at`) VALUES('201612191','1','1','192.168.1.101','2016-12-20 16:59:41','','','16','1','2016-12-19 16:59:49'),('201612191','1','2','192.168.1.101','2016-12-20 17:59:41','','','17','0','2016-12-19 16:59:49'),('201612191','1','1','192.168.1.102','2016-12-19 16:59:41','','','16','1','2016-12-19 16:59:49'),('201612191','1','2','192.168.1.102','2016-12-19 17:59:41','','','17','0','2016-12-19 16:59:49'),('201612191','1','1','192.168.1.101','2016-12-19 18:59:41','','','18','0','2016-12-19 16:59:49'),('201612191','1','2','192.168.1.101','2016-12-19 19:59:41','','','19','0','2016-12-19 16:59:49'),('201612191','1','1','192.168.1.103','2016-12-19 16:59:41','','','16','0','2016-12-19 16:59:49'),('201612191','1','2','192.168.1.103','2016-12-19 17:20:41','','','17','0','2016-12-19 16:59:49'),('201612191','1','0','192.168.1.101','2016-12-19 16:59:41','','["error1","error2"]','16','0','2016-12-19 16:59:49'),('201612191','1','0','192.168.1.101','2016-12-19 06:59:41','','["error1","error2"]','06','0','2016-12-19 16:59:49'),('201612191','1','-1','192.168.1.102','2016-12-19 15:59:41','','["warn1","warn2"]','15','0','2016-12-19 16:59:49'),('201612191','1','-1','192.168.1.103','2016-12-19 16:30:41','','["warn1","warn2"]','16','0','2016-12-19 16:59:49'),('201612191','1','-1','192.168.1.101','2016-12-19 16:59:00','','["warn1","warn2"]','16','0','2016-12-19 16:59:49'),('201612191','2','-1','192.168.1.101','2016-12-19 16:31:41','','["error1","error2"]','16','0','2016-12-19 16:59:49'),('201612191','2','2','192.168.1.101','2016-12-19 16:32:41','','','16','0','2016-12-19 16:59:49'),('201612191','2','1','192.168.1.102','2016-12-19 16:33:41','','','16','1','2016-12-19 16:59:49'),('201612191','2','2','192.168.1.102','2016-12-19 16:33:59','','','16','0','2016-12-19 16:59:49'),('201612191','2','1','192.168.1.103','2016-12-19 16:34:41','','','16','0','2016-12-19 16:59:49'),('201612191','2','2','192.168.1.103','2016-12-19 16:35:41','','','16','0','2016-12-19 16:59:49'),('201612191','2','0','192.168.1.101','2016-12-19 16:36:41','','["error1","error2"]','16','0','2016-12-19 16:59:49'),('201612191','2','0','192.168.1.102','2016-12-19 16:37:41','','["error1","error2"]','16','0','2016-12-19 16:59:49'),('201612191','2','-1','192.168.1.101','2016-12-19 16:38:41','','["warn1","warn2"]','16','0','2016-12-19 16:59:49'),('201612192','3','1','192.168.1.101','2016-12-19 16:50:41','','','16','1','2016-12-19 16:59:49'),('201612192','3','2','192.168.1.101','2016-12-19 16:59:41','','','16','0','2016-12-19 16:59:49'),('201612192','3','1','192.168.1.102','2016-12-19 16:56:41','','','16','0','2016-12-19 16:59:49'),('201612192','3','2','192.168.1.102','2016-12-19 16:59:41','','','16','0','2016-12-19 16:59:49'),('201612192','3','1','192.168.1.103','2016-12-19 16:55:41','','','16','0','2016-12-19 16:59:49'),('201612192','3','2','192.168.1.103','2016-12-19 16:59:41','','','16','0','2016-12-19 16:59:49'),('201612192','3','1','192.168.1.101','2016-12-19 16:20:41','','','16','0','2016-12-19 16:59:49'),('201612192','3','2','192.168.1.101','2016-12-19 16:21:41','','','16','0','2016-12-19 16:59:49'),('201612192','3','0','192.168.1.101','2016-12-19 15:59:41','','["error1","error2"]','15','0','2016-12-19 16:59:49'),('201612192','3','0','192.168.1.102','2016-12-19 15:59:41','','["error1","error2"]','15','0','2016-12-19 16:59:49'),('201612192','3','-1','192.168.1.101','2016-12-19 16:01:41','','["warn1","warn2"]','16','0','2016-12-19 16:59:49'),('201612192','4','1','192.168.1.101','2016-12-19 16:44:41','','','16','0','2016-12-19 16:59:49'),('201612192','4','2','192.168.1.101','2016-12-19 16:15:41','','','16','0','2016-12-19 16:59:49'),('201612192','4','1','192.168.1.101','2016-12-19 16:17:41','','','16','0','2016-12-19 16:59:49'),('201612192','4','2','192.168.1.101','2016-12-19 16:18:41','','','16','0','2016-12-19 16:59:49'),('201612192','4','1','192.168.1.102','2016-12-19 16:02:41','','','16','0','2016-12-19 16:59:49'),('201612192','4','2','192.168.1.102','2016-12-19 16:03:41','','','16','0','2016-12-19 16:59:49'),('201612192','4','0','192.168.1.103','2016-12-19 16:10:41','','["error1","error2"]','16','0','2016-12-19 16:59:49'),('201612192','4','0','192.168.1.103','2016-12-19 16:11:41','','["error1","error2"]','16','0','2016-12-19 16:59:49'),('201612192','4','-1','192.168.1.102','2016-12-19 16:11:41','','["warn1","warn2"]','16','0','2016-12-19 16:59:49'),('201612192','4','-1','192.168.1.102','2016-12-19 16:12:41','','["warn1","warn2"]','16','0','2016-12-19 16:59:49'),('201612192','4','0','192.168.1.101','2016-12-19 16:45:41','','["error1","error2"]','16','0','2016-12-19 16:59:49'),('201612192','4','0','192.168.1.101','2016-12-19 16:19:41','','["error1","error2"]','16','0','2016-12-19 16:59:49'),('201612192','4','1','192.168.1.101','2016-12-19 16:04:41','','','16','0','2016-12-19 16:59:49'),('201612192','4','2','192.168.1.101','2016-12-19 16:05:41','','','16','0','2016-12-19 16:59:49'),('201612193','5','1','192.168.1.102','2016-12-19 16:07:41','','','16','1','2016-12-19 16:59:49'),('201612193','5','2','192.168.1.102','2016-12-19 16:08:41','','','16','0','2016-12-19 16:59:49'),('201612193','5','1','192.168.1.103','2016-12-19 16:16:41','','','16','0','2016-12-19 16:59:49'),('201612193','5','2','192.168.1.103','2016-12-19 16:17:41','','','16','0','2016-12-19 16:59:49'),('201612193','5','1','192.168.1.101','2016-12-19 16:18:41','','','16','0','2016-12-19 16:59:49'),('201612193','5','2','192.168.1.101','2016-12-19 16:19:41','','','16','0','2016-12-19 16:59:49'),('201612193','5','0','192.168.1.101','2016-12-19 16:20:41','','["error1","error2"]','16','0','2016-12-19 16:59:49'),('201612193','5','0','192.168.1.103','2016-12-19 16:21:41','','["error1","error2"]','16','0','2016-12-19 16:59:49'),('201612193','5','-1','192.168.1.103','2016-12-19 16:59:41','','["warn1","warn2"]','16','0','2016-12-19 16:59:49'),('201612193','5','-1','192.168.1.101','2016-12-19 16:59:41','','["warn1","warn2"]','16','0','2016-12-19 16:59:49'),('201612193','6','1','192.168.1.101','2016-12-19 16:22:41','','','16','1','2016-12-19 16:59:49'),('201612193','6','2','192.168.1.101','2016-12-19 16:23:41','','','16','0','2016-12-19 16:59:49'),('201612193','6','1','192.168.1.101','2016-12-19 16:25:41','','','16','0','2016-12-19 16:59:49'),('201612193','6','2','192.168.1.101','2016-12-19 16:26:41','','','16','0','2016-12-19 16:59:49'),('201612193','6','1','192.168.1.103','2016-12-19 16:46:41','','','16','0','2016-12-19 16:59:49'),('201612193','6','2','192.168.1.103','2016-12-19 16:47:41','','','16','0','2016-12-19 16:59:49'),('201612193','6','0','192.168.1.102','2016-12-19 16:59:41','','["error1","error2"]','16','0','2016-12-19 16:59:49'),('201612193','6','-1','192.168.1.103','2016-12-18 17:29:21','','["warn1","warn2"]','17','0','0000-00-00 00:00:00'),('201612193','5','2','192.168.1.103','2016-12-18 16:17:41','','','16','0','2016-12-19 16:59:49'),('201612193','5','1','192.168.1.101','2016-12-18 16:18:41','','','16','1','2016-12-19 16:59:49'),('201612193','5','2','192.168.1.101','2016-12-18 16:19:41','','','16','0','2016-12-19 16:59:49'),('201612193','5','0','192.168.1.101','2016-12-18 16:20:41','','["error1","error2"]','16','0','2016-12-19 16:59:49'),('201612193','5','0','192.168.1.103','2016-12-18 16:21:41','','["error1","error2"]','16','0','2016-12-19 16:59:49'),('201612193','5','-1','192.168.1.103','2016-12-18 16:59:41','','["warn1","warn2"]','16','0','2016-12-19 16:59:49'),('201612193','5','-1','192.168.1.101','2016-12-18 16:59:41','','["warn1","warn2"]','16','0','2016-12-19 16:59:49'),('201612193','6','1','192.168.1.101','2016-12-17 16:22:41','','','16','1','2016-12-19 16:59:49'),('201612193','6','2','192.168.1.101','2016-12-17 16:23:41','','','16','0','2016-12-19 16:59:49'),('201612193','6','1','192.168.1.101','2016-12-16 16:25:41','','','16','1','2016-12-19 16:59:49'),('201612193','6','2','192.168.1.101','2016-12-17 16:26:41','','','16','0','2016-12-19 16:59:49'),('201612193','6','1','192.168.1.103','2016-12-16 16:46:41','','','16','1','2016-12-19 16:59:49'),('201612193','6','2','192.168.1.103','2016-12-16 16:47:41','','','16','0','2016-12-19 16:59:49'),('201612193','6','0','192.168.1.102','2016-12-13 16:59:41','','["error1","error2"]','16','0','2016-12-19 16:59:49'),('201612191','1','1','192.168.1.101','2016-12-15 16:59:41','','','16','1','2016-12-19 16:59:49'),('201612191','1','2','192.168.1.101','2016-12-15 17:59:41','','','17','0','2016-12-19 16:59:49'),('201612191','1','1','192.168.1.102','2016-12-01 16:59:41','','','16','1','2016-12-19 16:59:49'),('201612191','1','2','192.168.1.102','2016-12-01 17:59:41','','','17','0','2016-12-19 16:59:49'),('201612191','1','1','192.168.1.101','2016-12-02 18:59:41','','','18','1','2016-12-19 16:59:49'),('201612191','1','2','192.168.1.101','2016-12-02 19:59:41','','','19','0','2016-12-19 16:59:49'),('201612191','1','1','192.168.1.103','2016-12-03 16:59:41','','','16','1','2016-12-19 16:59:49'),('201612191','1','2','192.168.1.103','2016-12-03 17:20:41','','','17','0','2016-12-19 16:59:49'),('201612191','1','0','192.168.1.101','2016-12-06 16:59:41','','["error1","error2"]','16','0','2016-12-19 16:59:49'),('201612191','1','0','192.168.1.101','2016-12-05 06:59:41','','["error1","error2"]','06','0','2016-12-19 16:59:49'),('201612191','1','-1','192.168.1.102','2016-12-08 15:59:41','','["warn1","warn2"]','15','0','2016-12-19 16:59:49'),('201612191','1','-1','192.168.1.103','2016-12-15 16:30:41','','["warn1","warn2"]','16','0','2016-12-19 16:59:49'),('201612191','1','-1','192.168.1.101','2016-12-14 16:59:00','','["warn1","warn2"]','16','0','2016-12-19 16:59:49'),('201612191','2','1','192.168.1.101','2016-12-08 16:31:41','','','16','1','2016-12-19 16:59:49'),('201612191','2','2','192.168.1.101','2016-12-08 16:32:41','','','16','0','2016-12-19 16:59:49'),('201612191','2','1','192.168.1.102','2016-12-09 16:33:41','','','16','1','2016-12-19 16:59:49'),('201612191','2','2','192.168.1.102','2016-12-09 16:33:59','','','16','0','2016-12-19 16:59:49'),('201612191','2','1','192.168.1.103','2016-12-10 16:34:41','','','16','1','2016-12-19 16:59:49'),('201612191','2','2','192.168.1.103','2016-12-10 16:35:41','','','16','0','2016-12-19 16:59:49'),('201612191','2','0','192.168.1.101','2016-11-29 16:36:41','','["error1","error2"]','16','0','2016-12-19 16:59:49'),('201612191','2','0','192.168.1.102','2016-11-30 16:37:41','','["error1","error2"]','16','0','2016-12-19 16:59:49'),('201612191','2','-1','192.168.1.101','2016-11-28 16:38:41','','["warn1","warn2"]','16','0','2016-12-19 16:59:49'),('201612192','3','1','192.168.1.101','2016-11-30 16:50:41','','','16','1','2016-12-19 16:59:49'),('201612192','3','2','192.168.1.101','2016-11-30 16:59:41','','','16','0','2016-12-19 16:59:49'),('201612192','3','1','192.168.1.102','2016-12-14 16:56:41','','','16','1','2016-12-19 16:59:49'),('201612192','3','2','192.168.1.102','2016-12-14 16:59:41','','','16','0','2016-12-19 16:59:49'),('201612192','3','1','192.168.1.103','2016-12-15 16:55:41','','','16','1','2016-12-19 16:59:49'),('201612192','3','2','192.168.1.103','2016-12-15 16:59:41','','','16','0','2016-12-19 16:59:49'),('201612192','3','1','192.168.1.101','2016-12-16 16:20:41','','','16','1','2016-12-19 16:59:49'),('201612192','3','2','192.168.1.101','2016-12-16 16:21:41','','','16','0','2016-12-19 16:59:49'),('201612192','3','0','192.168.1.101','2016-11-28 15:59:41','','["error1","error2"]','15','0','2016-12-19 16:59:49'),('201612192','3','0','192.168.1.102','2016-11-21 15:59:41','','["error1","error2"]','15','0','2016-12-19 16:59:49'),('201612192','3','-1','192.168.1.101','2016-11-26 16:01:41','','["warn1","warn2"]','16','0','2016-12-19 16:59:49'),('201612192','4','1','192.168.1.101','2016-12-12 16:44:41','','','16','1','2016-12-19 16:59:49'),('201612192','4','2','192.168.1.101','2016-12-13 16:15:41','','','16','0','2016-12-19 16:59:49'),('201612192','4','1','192.168.1.101','2016-12-13 16:17:41','','','16','1','2016-12-19 16:59:49'),('201612192','4','2','192.168.1.101','2016-12-13 16:18:41','','','16','0','2016-12-19 16:59:49'),('201612192','4','1','192.168.1.102','2016-12-06 16:02:41','','','16','1','2016-12-19 16:59:49'),('201612192','4','2','192.168.1.102','2016-12-06 16:03:41','','','16','0','2016-12-19 16:59:49'),('201612192','4','0','192.168.1.103','2016-12-02 16:10:41','','["error1","error2"]','16','0','2016-12-19 16:59:49'),('201612192','4','0','192.168.1.103','2016-12-02 16:11:41','','["error1","error2"]','16','0','2016-12-19 16:59:49'),('201612192','4','-1','192.168.1.102','2016-12-02 16:11:41','','["warn1","warn2"]','16','0','2016-12-19 16:59:49'),('201612192','4','-1','192.168.1.102','2016-12-02 16:12:41','','["warn1","warn2"]','16','0','2016-12-19 16:59:49'),('201612192','4','0','192.168.1.101','2016-12-03 16:45:41','','["error1","error2"]','16','0','2016-12-19 16:59:49'),('201612192','4','0','192.168.1.101','2016-12-03 16:19:41','','["error1","error2"]','16','0','2016-12-19 16:59:49'),('201612192','4','1','192.168.1.101','2016-12-11 16:04:41','','','16','1','2016-12-19 16:59:49'),('201612192','4','2','192.168.1.101','2016-12-11 16:05:41','','','16','0','2016-12-19 16:59:49'),('201612193','5','1','192.168.1.102','2016-12-13 16:07:41','','','16','1','2016-12-19 16:59:49'),('201612191','1','1','192.168.1.109','2016-12-25 19:26:21','','','19','0','0000-00-00 00:00:00'),('201612191','1','1','192.168.1.109','2016-12-25 19:26:21','','','19','0','0000-00-00 00:00:00'),('201612191','1','1','192.168.1.109','2016-12-25 19:26:21','','','19','0','0000-00-00 00:00:00'),('201612191','1','1','192.168.1.109','2016-12-25 19:26:21','','','19','0','0000-00-00 00:00:00'),('201612191','1','1','192.168.1.109','2016-12-25 19:26:21','','','19','0','0000-00-00 00:00:00'),('201612191','1','1','192.168.1.109','2016-12-25 19:26:21','','','19','0','0000-00-00 00:00:00'),('201612191','1','1','192.168.1.109','2016-12-25 19:26:21','','','19','1','0000-00-00 00:00:00'),('201612191','1','1','192.168.1.109','2016-12-25 19:26:21','','','19','0','0000-00-00 00:00:00'),('201612191','1','1','192.168.1.109','2016-12-26 07:26:21','','','19','1','0000-00-00 00:00:00'),('201612191','1','1','192.168.1.109','2016-12-26 07:26:21','','','19','0','0000-00-00 00:00:00'),('201612191','1','1','192.168.1.109','2016-12-26 07:26:21','','','19','0','0000-00-00 00:00:00'),('201612191','1','1','192.168.1.109','2016-12-26 07:26:21','','','19','1','0000-00-00 00:00:00'),('201612191','1','1','192.168.1.109','2016-12-26 07:26:21','','','19','0','0000-00-00 00:00:00'),('201612191','1','1','192.168.1.109','2016-12-26 07:26:21','','','19','0','0000-00-00 00:00:00'),('201612191','1','1','192.168.1.109','2016-12-26 07:26:21','','','19','0','0000-00-00 00:00:00'),('201612191','1','1','192.168.1.109','2016-12-26 07:26:21','','','19','1','0000-00-00 00:00:00'),('201612191','1','1','192.168.1.109','2016-12-29 10:57:25','','','23','1','0000-00-00 00:00:00'),('201612191','1','0','192.168.1.101','2016-12-29 15:51:22','','["error1","error2"]','','0','0000-00-00 00:00:00'),('201612191','1','0','192.168.1.109','2016-12-29 15:52:35','','["error1","error2"]','15','0','0000-00-00 00:00:00'),('201612191','1','-1','192.168.1.109','2016-12-28 15:52:32','','["warn1","warn2"]','15','0','0000-00-00 00:00:00'),('201612191','1','0','192.168.1.109','2016-12-29 05:18:13','','["err1","err2"]','05','0','0000-00-00 00:00:00'),('201612191','1','0','192.168.1.109','2016-12-29 05:18:13','','["err1","err2"]','05','0','0000-00-00 00:00:00'),('201612191','1','-1','192.168.1.109','2016-12-29 05:18:13','','["war1","war2"]','05','0','0000-00-00 00:00:00'),('201612191','1','-1','192.168.1.109','2016-12-29 05:18:13','','["war1","war2"]','05','0','0000-00-00 00:00:00'),('201612191','1','-1','192.168.1.109','2016-12-30 05:18:13','','["war1","war2"]','05','0','0000-00-00 00:00:00'),('201612191','1','-1','192.168.1.109','2016-12-30 05:18:13','','["war1","war2"]','05','0','0000-00-00 00:00:00'),('201612191','1','0','192.168.1.109','2016-12-30 15:58:56','','["error1","error2"]','15','0','0000-00-00 00:00:00'),('201612191','1','1','192.168.1.109','2017-01-03 09:11:52','','','09','1','0000-00-00 00:00:00'),('201612191','1','1','192.168.1.109','2017-01-02 09:12:40','','','09','0','0000-00-00 00:00:00'),('201612191','1','1','192.168.1.109','2017-01-02 09:13:07','','','09','1','0000-00-00 00:00:00'),('201612191','1','1','192.168.1.109','2017-01-03 14:09:29','','','14','1','2017-01-03 14:09:39'),('201612191','1','1','192.168.1.109','2017-01-03 14:09:14','','','14','0','2017-01-03 14:09:39'),('201612191','1','1','192.168.1.109','2017-01-03 14:09:14','','','14','0','2017-01-03 14:09:39')