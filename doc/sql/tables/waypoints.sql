CREATE TABLE IF NOT EXISTS `waypoints` (
  `wp_id` int(11) NOT NULL auto_increment,
  `cache_id` int(11) NOT NULL default '0',
  `longitude` double default NULL,
  `latitude` double default NULL,
  `type` tinyint(1) default NULL,
  `status` tinyint(1) NOT NULL default '1',
  `stage` tinyint(1) NOT NULL default '0',
  `desc` varchar(600) default NULL,
  PRIMARY KEY  (`wp_id`,`cache_id`),
  KEY `cache_id` (`cache_id`),
  KEY `longitude` (`longitude`,`latitude`),
  KEY `latitude` (`latitude`),
  KEY `stage` (`stage`),
  KEY `status` (`status`),
  KEY `type` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='cache waypoints' AUTO_INCREMENT=1 ;
