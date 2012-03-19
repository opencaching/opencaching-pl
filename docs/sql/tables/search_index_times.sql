SET NAMES 'utf8';
DROP TABLE IF EXISTS `search_index_times`;
CREATE TABLE IF NOT EXISTS `search_index_times` (
  `object_type` tinyint(4) NOT NULL,
  `object_id` int(11) NOT NULL,
  `last_refresh` datetime NOT NULL,
  PRIMARY KEY  (`object_type`,`object_id`),
  KEY `last_refresh` (`last_refresh`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



