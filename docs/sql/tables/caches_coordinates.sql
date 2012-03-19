SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_coordinates`;
CREATE TABLE IF NOT EXISTS `cache_coordinates` (
  `id` int(11) NOT NULL auto_increment,
  `cache_id` int(11) NOT NULL,
  `date_modified` datetime NOT NULL,
  `longitude` double NOT NULL,
  `latitude` double NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `cache_id` (`cache_id`,`date_modified`),
  KEY `longitude` (`longitude`),
  KEY `latitude` (`latitude`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

