SET NAMES 'utf8';
DROP TABLE IF EXISTS `waypoints`;
CREATE TABLE IF NOT EXISTS `waypoints` (
  `wp_id` int(11) NOT NULL auto_increment,
  `cache_id` int(11) default NULL default '0',
  `longitude` double default NULL,
  `latitude` double default NULL,
  `type` int(1) default NULL,
  `status` int(1) NOT NULL default '1',
  `stage` varchar(2) NOT NULL default '0',
  `desc` varchar(300) default NULL,
  PRIMARY KEY  (`wp_id`,`cache_id`),
  KEY `cache_id` (`cache_id`),
  KEY `longitude` (`longitude`,`latitude`),
  KEY `latitude` (`latitude`),
  KEY `stage` (`stage`),
  KEY `status` (`status`),
  KEY `type` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='cache waypoints' ;
