SET NAMES 'utf8';
DROP TABLE IF EXISTS `caches_attributes`;
CREATE TABLE IF NOT EXISTS `caches_attributes` (
  `cache_id` int(11) NOT NULL,
  `attrib_id` int(11) NOT NULL,
  PRIMARY KEY  (`cache_id`,`attrib_id`),
  KEY `attrib_id` (`attrib_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

