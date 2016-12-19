-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-12-19 08:32:43
-- 服务器版本： 10.1.13-MariaDB
-- PHP Version: 5.6.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mmonitor`
--

-- --------------------------------------------------------

--
-- 表的结构 `access_log`
--

CREATE TABLE `access_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `ip` char(15) NOT NULL DEFAULT '',
  `spmcode` char(40) NOT NULL DEFAULT '',
  `time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `cache`
--

CREATE TABLE `cache` (
  `id` varchar(128) NOT NULL,
  `expire` int(11) NOT NULL,
  `data` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `cache`
--

INSERT INTO `cache` (`id`, `expire`, `data`) VALUES
('963454f612a8b5fb4a63ba1e97f028a1', 0, 0x613a323a7b693a303b613a323a7b693a303b613a323a7b693a303b4f3a31353a227969695c7765625c55726c52756c65223a31343a7b733a343a226e616d65223b733a32393a223c636f6e74726f6c6c65723a5c772b3e2f3c616374696f6e3a5c772b3e223b733a373a227061747465726e223b733a34323a22235e283f503c6134636632363639613e5c772b292f283f503c6134376363386339323e5c772b29242375223b733a343a22686f7374223b4e3b733a353a22726f757465223b733a32313a223c636f6e74726f6c6c65723e2f3c616374696f6e3e223b733a383a2264656661756c7473223b613a303a7b7d733a363a22737566666978223b4e3b733a343a2276657262223b4e3b733a343a226d6f6465223b4e3b733a31323a22656e636f6465506172616d73223b623a313b733a31353a22002a00706c616365686f6c64657273223b613a323a7b733a393a22613463663236363961223b733a31303a22636f6e74726f6c6c6572223b733a393a22613437636338633932223b733a363a22616374696f6e223b7d733a32363a22007969695c7765625c55726c52756c65005f74656d706c617465223b733a32333a222f3c636f6e74726f6c6c65723e2f3c616374696f6e3e2f223b733a32373a22007969695c7765625c55726c52756c65005f726f75746552756c65223b733a34323a22235e283f503c6134636632363639613e5c772b292f283f503c6134376363386339323e5c772b29242375223b733a32383a22007969695c7765625c55726c52756c65005f706172616d52756c6573223b613a303a7b7d733a32393a22007969695c7765625c55726c52756c65005f726f757465506172616d73223b613a323a7b733a31303a22636f6e74726f6c6c6572223b733a31323a223c636f6e74726f6c6c65723e223b733a363a22616374696f6e223b733a383a223c616374696f6e3e223b7d7d693a313b4f3a31353a227969695c7765625c55726c52756c65223a31343a7b733a343a226e616d65223b733a33383a223c636f6e74726f6c6c65723a5c772b3e2f3c616374696f6e3a5c772b3e2f3c69643a5c642b3e223b733a373a227061747465726e223b733a36313a22235e283f503c6134636632363639613e5c772b292f283f503c6134376363386339323e5c772b292f283f503c6162663339363735303e5c642b29242375223b733a343a22686f7374223b4e3b733a353a22726f757465223b733a32313a223c636f6e74726f6c6c65723e2f3c616374696f6e3e223b733a383a2264656661756c7473223b613a303a7b7d733a363a22737566666978223b4e3b733a343a2276657262223b4e3b733a343a226d6f6465223b4e3b733a31323a22656e636f6465506172616d73223b623a313b733a31353a22002a00706c616365686f6c64657273223b613a333a7b733a393a22613463663236363961223b733a31303a22636f6e74726f6c6c6572223b733a393a22613437636338633932223b733a363a22616374696f6e223b733a393a22616266333936373530223b733a323a226964223b7d733a32363a22007969695c7765625c55726c52756c65005f74656d706c617465223b733a32383a222f3c636f6e74726f6c6c65723e2f3c616374696f6e3e2f3c69643e2f223b733a32373a22007969695c7765625c55726c52756c65005f726f75746552756c65223b733a34323a22235e283f503c6134636632363639613e5c772b292f283f503c6134376363386339323e5c772b29242375223b733a32383a22007969695c7765625c55726c52756c65005f706172616d52756c6573223b613a313a7b733a323a226964223b733a383a22235e5c642b242375223b7d733a32393a22007969695c7765625c55726c52756c65005f726f757465506172616d73223b613a323a7b733a31303a22636f6e74726f6c6c6572223b733a31323a223c636f6e74726f6c6c65723e223b733a363a22616374696f6e223b733a383a223c616374696f6e3e223b7d7d7d693a313b733a33323a226364613635616562386462353963613933653962626262623833336637663464223b7d693a313b4e3b7d);

-- --------------------------------------------------------

--
-- 表的结构 `daycount`
--

CREATE TABLE `daycount` (
  `id` int(10) UNSIGNED NOT NULL,
  `spmcode` varchar(25) NOT NULL,
  `type` char(3) NOT NULL DEFAULT '',
  `ip` char(15) NOT NULL,
  `time` datetime NOT NULL,
  `referrer` varchar(100) NOT NULL DEFAULT '',
  `message` varchar(100) NOT NULL DEFAULT '',
  `num` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `daycount`
--

INSERT INTO `daycount` (`id`, `spmcode`, `type`, `ip`, `time`, `referrer`, `message`, `num`, `created_at`, `updated_at`) VALUES
(134, '123.10', '-1', '192.168.1.109', '2016-12-19 10:32:17', '', '["sdjfsdlk"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(135, '123.10', '-1', '192.168.1.109', '2016-12-18 10:32:22', '', '["sdjfsdlk"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(136, '123.10', '-1', '192.168.1.109', '2016-12-18 10:32:26', '', '["sdjfsdlk"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(137, '123.10', '-1', '192.168.1.109', '2016-12-16 10:32:28', '', '["sdjfsdlk"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(138, '123.10', '0', '192.168.1.109', '2016-12-16 10:31:57', '', '["sdjfsdlk"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(139, '123.10', '0', '192.168.1.109', '2016-12-16 10:32:03', '', '["sdjfsdlk"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(140, '123.10', '0', '192.168.1.109', '2016-12-16 10:32:05', '', '["sdjfsdlk","123213"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(141, '123.10', '0', '192.168.1.109', '2016-12-16 10:32:06', '', '["sdjfsdlk","123213"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(142, '123.10', '1', '192.168.1.109', '2016-12-15 10:32:38', '', '', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(143, '123.10', '1', '192.168.1.109', '2016-12-16 10:32:51', '', '', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(144, '123.10', '2', '192.168.1.109', '2016-12-15 10:32:45', '', '', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(145, '123.10', '2', '192.168.1.109', '2016-12-16 10:32:55', '', '', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(146, '123.10', '-1', '192.168.1.108', '2016-12-16 10:32:17', '', '["sdjfsdlk"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(147, '123.10', '-1', '192.168.1.108', '2016-12-16 10:32:22', '', '["sdjfsdlk","123213"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(148, '123.10', '-1', '192.168.1.108', '2016-12-16 10:32:26', '', '["sdjfsdlk"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(149, '123.10', '-1', '192.168.1.108', '2016-12-16 10:32:28', '', '["sdjfsdlk"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(150, '123.10', '0', '192.168.1.108', '2016-12-16 10:31:57', '', '["sdjfsdlk"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(151, '123.10', '0', '192.168.1.108', '2016-12-16 10:32:03', '', '["sdjfsdlk"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(152, '123.10', '0', '192.168.1.108', '2016-12-16 10:32:05', '', '["sdjfsdlk"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(153, '123.10', '0', '192.168.1.108', '2016-12-16 10:32:06', '', '["sdjfsdlk","123213"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(154, '123.10', '1', '192.168.1.108', '2016-12-15 10:32:38', '', '', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(155, '123.10', '1', '192.168.1.108', '2016-12-16 10:32:51', '', '', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(156, '123.10', '2', '192.168.1.108', '2016-12-15 10:32:45', '', '', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(157, '123.10', '2', '192.168.1.108', '2016-12-16 10:32:55', '', '', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(158, '123.11', '-1', '192.168.1.109', '2016-12-15 10:32:17', '', '["sdjfsdlk"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(159, '123.11', '-1', '192.168.1.109', '2016-12-16 10:32:22', '', '["sdjfsdlk"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(160, '123.11', '-1', '192.168.1.109', '2016-12-16 10:32:26', '', '["sdjfsdlk"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(161, '123.11', '-1', '192.168.1.109', '2016-12-16 10:32:28', '', '["sdjfsdlk","123213"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(162, '123.11', '0', '192.168.1.109', '2016-12-16 10:31:57', '', '["sdjfsdlk"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(163, '123.11', '0', '192.168.1.109', '2016-12-16 10:32:03', '', '["sdjfsdlk"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(164, '123.11', '0', '192.168.1.109', '2016-12-16 10:32:05', '', '["sdjfsdlk","123213"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(165, '123.11', '0', '192.168.1.109', '2016-12-16 10:32:06', '', '["sdjfsdlk"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(166, '123.11', '1', '192.168.1.109', '2016-12-15 09:32:38', '', '', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(167, '123.11', '1', '192.168.1.109', '2016-12-16 10:32:51', '', '', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(168, '123.11', '2', '192.168.1.109', '2016-12-15 10:32:45', '', '', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(169, '123.11', '2', '192.168.1.109', '2016-12-16 10:32:55', '', '', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(170, '123.11', '-1', '192.168.1.108', '2016-12-16 10:32:17', '', '["sdjfsdlk"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(171, '123.11', '-1', '192.168.1.108', '2016-12-16 10:32:22', '', '["sdjfsdlk"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(172, '123.11', '-1', '192.168.1.108', '2016-12-16 10:32:26', '', '["sdjfsdlk","123213"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(173, '123.11', '-1', '192.168.1.108', '2016-12-16 10:32:28', '', '["sdjfsdlk"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(174, '123.11', '0', '192.168.1.108', '2016-12-16 10:31:57', '', '["sdjfsdlk"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(175, '123.11', '0', '192.168.1.108', '2016-12-16 10:32:03', '', '["sdjfsdlk","123213"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(176, '123.11', '0', '192.168.1.108', '2016-12-16 10:32:05', '', '["sdjfsdlk"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(177, '123.11', '0', '192.168.1.108', '2016-12-16 10:32:06', '', '["sdjfsdlk","123213"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(178, '123.11', '1', '192.168.1.108', '2016-12-15 10:32:38', '', '', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(179, '123.11', '1', '192.168.1.108', '2016-12-16 10:32:51', '', '', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(180, '123.11', '2', '192.168.1.108', '2016-12-15 10:32:45', '', '', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(181, '123.11', '2', '192.168.1.108', '2016-12-16 10:32:55', '', '', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(182, '123.11', '-1', '192.168.1.109', '2016-12-15 10:32:17', '', '["sdjfsdlk"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(183, '123.11', '-1', '192.168.1.109', '2016-12-16 10:32:22', '', '["sdjfsdlk"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(184, '123.11', '-1', '192.168.1.109', '2016-12-16 10:32:26', '', '["sdjfsdlk","123213"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(185, '123.11', '-1', '192.168.1.109', '2016-12-16 10:32:28', '', '["sdjfsdlk"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(186, '123.11', '0', '192.168.1.109', '2016-12-16 10:31:57', '', '["sdjfsdlk","123213"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(187, '123.11', '0', '192.168.1.109', '2016-12-16 10:32:03', '', '["sdjfsdlk"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(188, '123.11', '0', '192.168.1.109', '2016-12-16 10:32:05', '', '["sdjfsdlk"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(189, '123.11', '0', '192.168.1.109', '2016-12-16 10:32:06', '', '["sdjfsdlk","123213"]', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(190, '123.11', '1', '192.168.1.109', '2016-12-15 10:32:38', '', '', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(191, '123.11', '1', '192.168.1.109', '2016-12-16 10:32:51', '', '', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(192, '123.11', '2', '192.168.1.109', '2016-12-15 10:32:45', '', '', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13'),
(193, '123.11', '2', '192.168.1.109', '2016-12-16 10:32:55', '', '', 0, '2016-12-16 10:33:13', '2016-12-16 10:33:13');

-- --------------------------------------------------------

--
-- 表的结构 `ipaddress`
--

CREATE TABLE `ipaddress` (
  `id` int(10) UNSIGNED NOT NULL,
  `ipaddress` char(15) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `ipaddress`
--

INSERT INTO `ipaddress` (`id`, `ipaddress`, `created_at`, `updated_at`) VALUES
(5, '192.168.1.109', '2016-12-16 10:23:20', '2016-12-16 10:23:20');

-- --------------------------------------------------------

--
-- 表的结构 `message`
--

CREATE TABLE `message` (
  `id` int(10) UNSIGNED NOT NULL,
  `sid` int(11) NOT NULL,
  `message` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `scount`
--

CREATE TABLE `scount` (
  `id` int(10) UNSIGNED NOT NULL,
  `spmcode` varchar(25) NOT NULL,
  `type` char(3) NOT NULL DEFAULT '',
  `ip` char(15) NOT NULL COMMENT '返回内容',
  `time` datetime NOT NULL,
  `referrer` varchar(100) NOT NULL DEFAULT '',
  `message` varchar(100) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL COMMENT '时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `session`
--

CREATE TABLE `session` (
  `id` varchar(64) NOT NULL,
  `expire` int(11) NOT NULL,
  `data` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `unique_id`
--

CREATE TABLE `unique_id` (
  `id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `webs`
--

CREATE TABLE `webs` (
  `id` int(10) UNSIGNED NOT NULL,
  `spm_code` varchar(25) NOT NULL,
  `tag` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `webs`
--

INSERT INTO `webs` (`id`, `spm_code`, `tag`) VALUES
(1, '2014.123456.123.123', 1),
(2, '2014.123456.123.123', 2),
(3, '2014.123456.123.123', 3),
(4, '2014.123456.123.124', 1),
(5, '2014.123456.123.124', 2),
(6, '2014.123456.123.124', 3),
(7, '11111', 1),
(8, '11111', 1),
(9, '11111', 1),
(10, '11111', 1),
(11, '11111', 1),
(12, '1111', 1),
(13, '1111', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access_log`
--
ALTER TABLE `access_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `daycount`
--
ALTER TABLE `daycount`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ipaddress`
--
ALTER TABLE `ipaddress`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scount`
--
ALTER TABLE `scount`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `unique_id`
--
ALTER TABLE `unique_id`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `webs`
--
ALTER TABLE `webs`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `access_log`
--
ALTER TABLE `access_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `daycount`
--
ALTER TABLE `daycount`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=194;
--
-- 使用表AUTO_INCREMENT `ipaddress`
--
ALTER TABLE `ipaddress`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- 使用表AUTO_INCREMENT `message`
--
ALTER TABLE `message`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `scount`
--
ALTER TABLE `scount`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `unique_id`
--
ALTER TABLE `unique_id`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `webs`
--
ALTER TABLE `webs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
