SET NAMES 'utf8';
DROP TABLE IF EXISTS `npa_areas`;
CREATE TABLE `npa_areas` (
  `id` int(10) unsigned NOT NULL auto_increment,
   `linkid` int(4) DEFAULT NULL,
  `sitename` varchar(255) NOT NULL,
  `sitecode` varchar(255) NOT NULL,
  `sitetype` char(1) NOT NULL,
  `shape` linestring NOT NULL,
  PRIMARY KEY  (`id`),
  SPATIAL KEY `shape` (`shape`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ;
