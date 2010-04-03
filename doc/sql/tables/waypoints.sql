SET NAMES 'utf8';
DROP TABLE IF EXISTS `waypoints`;
CREATE TABLE IF NOT EXISTS `waypoints` (
  `cache_id` int(11) default NULL,
  `longitude` double default NULL,
  `latitude` double default NULL,
  `type` int(11) default NULL,
  `status` int(11) default NULL,
   `name` varchar(60) default NULL,
   `desc` varchar(240) default NULL,
  PRIMARY KEY  (`cache_id`),
  KEY `longitude` (`longitude`,`latitude`),
  KEY `latitude` (`latitude`),
  KEY `status` (`status`),
  KEY `type` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='cache waypoints' ;
