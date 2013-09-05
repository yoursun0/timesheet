-- --------------------------------------------------------

-- 
-- Table structure for table `mrbs_area`
-- 

CREATE TABLE `mrbs_area` (
  `id` int(11) NOT NULL auto_increment,
  `area_name` varchar(30) default NULL,
  `area_admin_email` text,
  `sort_order` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `sort_order` (`sort_order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `mrbs_entry`
-- 

CREATE TABLE `mrbs_entry` (
  `id` int(11) NOT NULL auto_increment,
  `start_time` int(11) NOT NULL default '0',
  `end_time` int(11) NOT NULL default '0',
  `entry_type` int(11) NOT NULL default '0',
  `repeat_id` int(11) NOT NULL default '0',
  `room_id` int(11) NOT NULL default '1',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `create_by` varchar(80) NOT NULL default '',
  `name` varchar(80) NOT NULL default '',
  `type` char(1) NOT NULL default 'E',
  `description` text,
  PRIMARY KEY  (`id`),
  KEY `idxStartTime` (`start_time`),
  KEY `idxEndTime` (`end_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `mrbs_repeat`
-- 

CREATE TABLE `mrbs_repeat` (
  `id` int(11) NOT NULL auto_increment,
  `start_time` int(11) NOT NULL default '0',
  `end_time` int(11) NOT NULL default '0',
  `rep_type` int(11) NOT NULL default '0',
  `end_date` int(11) NOT NULL default '0',
  `rep_opt` varchar(32) NOT NULL default '',
  `room_id` int(11) NOT NULL default '1',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `create_by` varchar(80) NOT NULL default '',
  `name` varchar(80) NOT NULL default '',
  `type` char(1) NOT NULL default 'E',
  `description` text,
  `rep_num_weeks` smallint(6) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

-- 
-- Table structure for table `mrbs_room`
-- 

CREATE TABLE `mrbs_room` (
  `id` int(11) NOT NULL auto_increment,
  `area_id` int(11) NOT NULL default '0',
  `room_name` varchar(25) NOT NULL default '',
  `description` varchar(60) default NULL,
  `capacity` int(11) NOT NULL default '0',
  `room_admin_email` text,
  `sort_order` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `sort_order` (`sort_order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `mrbs_users`
-- 

CREATE TABLE `mrbs_users` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(30) default NULL,
  `password` varchar(40) default NULL,
  `email` varchar(75) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;


