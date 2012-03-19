SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_loc`;
CREATE TABLE IF NOT EXISTS `cache_loc` (
  `cache_id` int(11) NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `lang` varchar(2) NOT NULL,
  `country` varchar(120) DEFAULT NULL,
  `adm1` varchar(120) DEFAULT NULL,
  `adm2` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`cache_id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;