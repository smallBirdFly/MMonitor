CREATE TABLE `scount1479350001` (
                `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                  `spm_code` varchar(25) NOT NULL,
                  `tag` tinyint(4) NOT NULL COMMENT '页面上的模块',
                  `type` tinyint(4) NOT NULL COMMENT '返回的类型，0表示错误，1表示访问',
                  `content` smallint(6) NOT NULL COMMENT '返回的内容，访问则是次数，错误则是错误信息',
                  `created_at` int(11) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;INSERT INTO `scount1479350001` ( `spm_code`, `tag`, `type`, `content`, `created_at`, `updated_at`) VALUES