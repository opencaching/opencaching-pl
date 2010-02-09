SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_attrib`;
CREATE TABLE IF NOT EXISTS `cache_attrib` (
  `id` int(11) NOT NULL default '0',
  `language` char(2) NOT NULL,
  `text_short` varchar(20) NOT NULL,
  `text_long` varchar(60) NOT NULL,
  `icon_large` varchar(60) NOT NULL,
  `icon_no` varchar(60) NOT NULL,
  `icon_undef` varchar(60) NOT NULL,
  `category` tinyint(2) NOT NULL default '0',
  `default` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`language`,`id`),
  KEY `category` (`category`,`id`),
  KEY `default` (`default`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
