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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- 列出以下資料庫的數據： `cs_func`
--

INSERT INTO `cs_func` (`id`, `key`, `name`, `description`, `status`, `timestamp`) VALUES
(1, 'PAGE::FUNC', 'Function Control', 'Admin User - Add new function', 1, '2009-03-31 12:22:40'),
(2, 'PAGE::ROLE', 'Role Control', 'Admin User - Manage user role', 1, '2009-03-31 12:22:47'),
(3, 'PAGE::USER', 'User Control', 'Admin User - Manage login a/c and access right', 1, '2009-03-31 12:36:56');


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
(1, 3);

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