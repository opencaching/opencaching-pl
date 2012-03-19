SET NAMES 'utf8';
DROP TABLE IF EXISTS `waypoint_type`;
CREATE TABLE IF NOT EXISTS `waypoint_type` (
  `id` int(11) NOT NULL auto_increment,
  `pl` varchar(60) NOT NULL,
  `en` varchar(60) NOT NULL,
  `icon` varchar(60) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


