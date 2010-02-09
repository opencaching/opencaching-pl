SET NAMES 'utf8';

DROP TABLE IF EXISTS `cache_countries`;
CREATE TABLE IF NOT EXISTS `cache_countries` (
  `id` int(11) NOT NULL auto_increment,
  `cache_id` int(11) NOT NULL,
  `date_modified` datetime NOT NULL,
  `country` char(2) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `cache_id` (`cache_id`,`date_modified`),
  KEY `country` (`country`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

