-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- 主機: localhost
-- 建立日期: Mar 31, 2009, 03:11 PM
-- 伺服器版本: 5.0.51
-- PHP 版本: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- 資料庫: `timmy_timesheet`
--

-- --------------------------------------------------------

--
-- 資料表格式： `cs_approved`
--

CREATE TABLE IF NOT EXISTS `cs_approved` (
  `key` varchar(22) NOT NULL,
  `date` date NOT NULL,
  `user_id` int(11) NOT NULL,
  `approved_by` varchar(80) NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`key`),
  KEY `date` (`date`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 列出以下資料庫的數據： `cs_approved`
--


-- --------------------------------------------------------

--
-- 資料表格式： `cs_event`
--

CREATE TABLE IF NOT EXISTS `cs_event` (
  `id` int(11) NOT NULL auto_increment,
  `date` date NOT NULL,
  `start_time` int(11) NOT NULL default '0',
  `end_time` int(11) NOT NULL default '0',
  `duration` float NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `repeat_id` int(11) NOT NULL default '0',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `created_by` varchar(80) NOT NULL,
  `type` char(1) NOT NULL,
  `name` varchar(80) NOT NULL,
  `description` text,
  PRIMARY KEY  (`id`),
  KEY `date` (`date`),
  KEY `start_time` (`start_time`),
  KEY `end_time` (`end_time`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 列出以下資料庫的數據： `cs_event`
--


-- --------------------------------------------------------

--
-- 資料表格式： `cs_func`
--

CREATE TABLE IF NOT EXISTS `cs_func` (
  `id` int(11) NOT NULL auto_increment,
  `key` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `status` int(2) NOT NULL default '0',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `key` (`key`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- 列出以下資料庫的數據： `cs_func`
--

INSERT INTO `cs_func` (`id`, `key`, `name`, `description`, `status`, `timestamp`) VALUES
(1, 'PAGE::FUNC', 'Function Control', 'Admin User - Add new function', 1, '2009-03-31 12:22:40'),
(2, 'PAGE::ROLE', 'Role Control', 'Admin User - Manage user role', 1, '2009-03-31 12:22:47'),
(3, 'PAGE::USER', 'User Control', 'Admin User - Manage login a/c and access right', 1, '2009-03-31 12:36:56'),
(5, 'PAGE::WEEK', 'View(Week)', '', 1, '2009-03-31 14:52:17');

-- --------------------------------------------------------

--
-- 資料表格式： `cs_role`
--

CREATE TABLE IF NOT EXISTS `cs_role` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `status` int(2) NOT NULL default '0',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 列出以下資料庫的數據： `cs_role`
--

INSERT INTO `cs_role` (`id`, `name`, `description`, `status`, `timestamp`) VALUES
(1, 'Administrator', 'DP - Administrator', 1, '2009-03-31 14:57:52');

-- --------------------------------------------------------

--
-- 資料表格式： `cs_role_func_rel`
--

CREATE TABLE IF NOT EXISTS `cs_role_func_rel` (
  `role_id` int(11) NOT NULL,
  `func_id` int(11) NOT NULL,
  PRIMARY KEY  (`role_id`,`func_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 列出以下資料庫的數據： `cs_role_func_rel`
--

INSERT INTO `cs_role_func_rel` (`role_id`, `func_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 5);

-- --------------------------------------------------------

--
-- 資料表格式： `cs_user`
--

CREATE TABLE IF NOT EXISTS `cs_user` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `login` varchar(30) NOT NULL,
  `password` varchar(40) NOT NULL,
  `status` char(1) NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `auth_type` varchar(10) NOT NULL,
  `auth_login` varchar(100) NOT NULL,
  `auth_domain` varchar(100) NOT NULL,
  `last_login` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `login` (`login`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=126 ;

--
-- 列出以下資料庫的數據： `cs_user`
--

INSERT INTO `cs_user` (`id`, `name`, `email`, `login`, `password`, `status`, `timestamp`, `auth_type`, `auth_login`, `auth_domain`, `last_login`) VALUES
(1, 'lawrence', 'lawrence.yau@cshk.com', 'lawrence', 'be28b38a2534a4e3ce73056c150e7bb0', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(2, 'jessica', 'jessica.szeto@cshk.com', 'jessica', 'd494020ff8ec181ef98ed97ac3f25453', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(3, 'warren', 'warren.chau@cshk.com', 'warren', 'f62377ab1490ff7ab8a39ac836cf349b', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(4, 'brian', 'brian.wong@cshk.com', 'brian', '49aa104309e366164767e735aada3134', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(6, 'jonathan', 'jonathan.chan@cshk.com', 'jonathan', 'c4666872eb55192742a4ce9db2c6d1c2', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(7, 'eva', 'eva.wong@cshk.com', 'eva', 'ae5eb824ef87499f644c3f11a7176157', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(9, 'cindy', 'cindy.lo@cshk.com', 'cindy', '7c2066fbca686b96bcedb8092ee32023', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(10, 'paul', 'paul.lee@cshk.com', 'paul', '6c63212ab48e8401eaf6b59b95d816a9', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(11, 'antony', 'antony.leung@cshk.com', 'antony', '9da90c78b6e182a7328693f2f7d527d8', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(12, 'tat', 'tat.lam@cshk.com', 'tat', '872e384a07f7ea1e05fc742f5be6008b', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(13, 'sin man', 'sinman.cheng@cshk.com', 'sin man', '7b86d7216bf9821e265947cbc199a95c', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(15, 'robert', 'robert.li@cshk.com', 'robert', '0ebe55ea34d9d3a4195d73a81a54f767', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(16, 'alice teng', 'alice.teng@cshk.com', 'alice teng', '230eda617c810d9233e7a9b814ac9b2a', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(17, 'winnie poon', 'winnie.poon@cshk.com', 'winnie poon', 'f5a10d2257beee4b63a41c8dc6e8f0f1', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(19, 'michelle', 'michelle.tam@cshk.com', 'michelle', '27fbae9e432b05e6d700329325ca0297', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(21, 'karen', 'karen.leung@cshk.com', 'karen', 'ba952731f97fb058035aa399b1cb3d5c', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(25, 'flora', 'flora.li@cshk.com', 'flora', '979c8e8f8271e3431249f935cd7d3f4c', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(27, 'bel', 'bel.wong@cshk.com', 'bel', '49b10fbde180f30ecd23a4155ecc5a6f', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(28, 'carman chan', 'carman.chan@cshk.com', 'carman chan', '9e243f49d7ea53ded23864740dc67a1a', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(30, 'tracy', 'tracy.cheng@cshk.com', 'tracy', '5713a878bf70c6f6b95854af26c9aaf3', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(31, 'stephanie', 'stephanie.tang@cshk.com', 'stephanie', '59f29878f0ca6fffa2485e3c5e3b5443', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(34, 'kenneth', 'kenneth.kwan@cshk.com', 'kenneth', '7ca955bd92ca8b00548ddf36d2e79217', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(35, 'cat lau', 'cat.lau@cshk.com', 'cat lau', '1a36cff0c8da8fb25e15a585dc6c3014', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(36, 'gary', 'gary.cheung@cshk.com', 'gary', '03b083fd0aadc8883198881ba88111ab', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(38, 'simon cham', ' ', 'simon cham', 'c10b2168f337db7f377154ea57c43324', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(39, 'ally', 'ally.huang@cshk.com', 'ally', '2a72a24c6814bf96c61e992a611de48e', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(40, 'carol', 'carol.tsui@cshk.com', 'carol', 'a9a0198010a6073db96434f6cc5f22a8', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(41, 'pui kwan', 'puikwan.lo@cshk.com', 'pui kwan', '5a20e811474bef19dd12bf8c5be04e82', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(42, 'jacky', 'jacky.chung@cshk.com', 'jacky', '9661fd65249b026ebea0f49927e82f0e', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(43, 'dennis', 'dennis.lim@cshk.com', 'dennis', '5f45f856b134b34e6945721f7f13c0cb', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(44, 'debby', 'debby.lau@apviewingstudio.com', 'debby', 'f64998a181d5463a720ec1ad7f413b5e', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(45, 'maggie', 'maggie.lam@cshk.com', 'maggie', '6f45fd03183771afe63ad85fbe39b858', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(46, 'cheng hoi man', 'hoiman.cheng@cshk.com', 'cheng hoi man', 'd74a07c7c8d83c041cef262b14410e1f', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(47, 'nicole', 'nicole.tam@cshk.com', 'nicole', 'fa1c0721b9d73d2ef25822fc8315638d', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(49, 'hing chuen', 'hingchuen.tong@cshk.com', 'hing chuen', '2a27f561206be2b2a7040164f41bffe8', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(50, 'derek', 'derek.yu@cshk.com', 'derek', '1bd245874dd783efba78dccb8c281994', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(51, 'gordon', 'gordon.hui@cshk.com', 'gordon', 'e10adc3949ba59abbe56e057f20f883e', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(52, 'timmy', 'timmy.tin@cshk.com', 'timmy', '6d02d81ae54bdce62edd5d054d3a4297', 'A', '2009-03-31 09:33:00', '', '', '', '0000-00-00 00:00:00'),
(53, 'willis', 'willis.li@cshk.com', 'willis', '482de1638fb45ae326201ce715051114', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(54, 'silvia', 'silvia.so@cshk.com', 'silvia', '29a39ab2fe3b9a4e931ebbd3ed106993', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(55, 'laurel', 'laurel.qi@cshk.com', 'laurel', '9b9939f2281b302cf0d22eaf596c6c89', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(56, 'vicky so', 'vicky.so@cshk.com', 'vicky so', '6c29df5275f8bf08d369c7828c1414d6', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(57, 'kaho', 'kaho.kwok@cshk.com', 'kaho', '1d91158968052d7f87acd6acd5f47248', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(58, 'emily', 'emily.chung@cshk.com', 'emily', '5f8352868f179c38de5c36c6618db9fd', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(59, 'olive', 'olive.wong@cshk.com', 'olive', 'b9b8a1efd3374cd4cfbcefcb7a42b699', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(60, 'carmen leung', 'carmen.leung@cshk.com', 'carmen leung', '8ad10104833ad02d11340d8dd6da7d41', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(65, 'agnes', 'agnes.yam@cshk.com', 'agnes', '3d8dcf0c67e2211f867a712d84fa0ee0', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(67, 'cherry', 'cherry.chan@cshk.com', 'cherry', 'aff851103f525147b805f94db2ed90ec', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(68, 'man', 'chungman.lee@cshk.com', 'man', '4b7aa3c07202673fa8e35725d1a2ca03', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(69, 'connie', 'connie.sin@cshk.com', 'connie', 'cca9760d54a2f7c30970e5aac661fff6', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(72, 'phoebe', 'phoebe.chan@cshk.com', 'phoebe', 'd83207be6d1d2fa391717b4c6565739f', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(73, 'samantha', 'samantha.lam@cshk.com', 'samantha', 'fecf4d772f647e978c7daa4b3b446ada', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(76, 'anna', 'anna.lai@cshk.com', 'anna', '9258ff4a4748aa876ecbeaf94aa62695', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(79, 'louis', 'louis.tong@cshk.com', 'louis', 'e528376ba5042a7fafc8e5f071e6585b', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(83, 'cat tam', 'cat.tam@cshk.com', 'cat tam', 'a569ce7af0ff1c8c708e6728d9bc653a', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(84, 'clive', 'clive.tam@cshk.com', 'clive', 'fa1ab1caa2126b3f48ddc272f1dd4781', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(89, 'harold', 'harold.yeung@cshk.com', 'harold', '3ec80812dcbb8071c67ff20c14eaaf21', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(90, 'henry', 'henry.lam@cshk.com', 'henry', '09692419957cb9c9f571ddf5bac70a75', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(92, 'john', 'john.ho@cshk.com', 'john', 'd8abd3081f4916e98d53e16bc8a57b7b', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(95, 'tammy', 'tammy.ho@cshk.com', 'tammy', 'f726f5a065a6b896c2022d4951d0af83', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(96, 'van', 'van.lam@cshk.com', 'van', '41cbe71f9cacffa82f1627883b54b47c', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(97, 'wing', 'wing.so@cshk.com', 'wing', '4d682ec4eed27c53849758bc13b6e179', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(120, 'ellen', 'ellen.cheung@cshk.com', 'ellen', '405202abf77681ffe8eb49404eb55b13', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(121, 'tong', 'kaitong.fung@cshk.com', 'tong', '64e4c8e497697894c0156be2b76ccf13', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00'),
(122, 'shan', 'hoishan.li@cshk.com', 'shan', '4d4a6f53e0dffbadd9a0ce557ebb52a3', 'A', '2009-03-30 18:00:00', '', '', '', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- 資料表格式： `cs_user_func_rel`
--

CREATE TABLE IF NOT EXISTS `cs_user_func_rel` (
  `user_id` int(11) NOT NULL,
  `func_id` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`,`func_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 列出以下資料庫的數據： `cs_user_func_rel`
--


-- --------------------------------------------------------

--
-- 資料表格式： `cs_user_role_rel`
--

CREATE TABLE IF NOT EXISTS `cs_user_role_rel` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`,`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 列出以下資料庫的數據： `cs_user_role_rel`
--

INSERT INTO `cs_user_role_rel` (`user_id`, `role_id`) VALUES
(1, 1),
(52, 1);
