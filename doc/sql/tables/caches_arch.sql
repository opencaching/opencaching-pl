SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_arch`;
CREATE TABLE IF NOT EXISTS `cache_arch` (
  `cache_id` int(11) NOT NULL,
  `step` int(11) NOT NULL,
  PRIMARY KEY  (`cache_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
