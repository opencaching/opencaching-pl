SET NAMES 'utf8';
DROP TABLE IF EXISTS `route_points`;
CREATE TABLE IF NOT EXISTS `route_points` (
  `route_id` int(11) DEFAULT NULL,
  `point_nr` int(10) DEFAULT NULL,
  `lon` double DEFAULT NULL,
  `lat` double DEFAULT NULL,
  KEY `route_id` (`route_id`),
  KEY `lon` (`lon`,`lat`),
  KEY `lat` (`lat`),
  KEY `point_nr` (`point_nr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='route points';

