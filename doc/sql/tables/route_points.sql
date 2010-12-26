SET NAMES 'utf8';
DROP TABLE IF EXISTS `route_points`;
CREATE TABLE IF NOT EXISTS `route_points` (
  `route_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
   `point_nr` tinyint(1) default NULL,
  `lon` double default NULL,
  `lat` double default NULL,
  PRIMARY KEY  (`route_id`,`user_id`),
  KEY `user_id` (`user_id`),
  KEY `lon` (`lon`,`lat`),
  KEY `lat` (`lat`),
  KEY `point_nr` (`point_nr`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='route points';
