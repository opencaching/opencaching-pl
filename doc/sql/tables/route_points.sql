SET NAMES 'utf8';
DROP TABLE IF EXISTS `route_points`;
CREATE TABLE IF NOT EXISTS `route_points` (
  `route_id` int(11) default NULL,
   `point_nr` tinyint(1) default NULL,
  `lon` double default NULL,
  `lat` double default NULL,
  KEY  (`route_id`),
  KEY `lon` (`lon`,`lat`),
  KEY `lat` (`lat`),
  KEY `point_nr` (`point_nr`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='route points';
