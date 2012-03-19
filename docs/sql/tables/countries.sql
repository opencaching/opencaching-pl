SET NAMES 'utf8';
DROP TABLE IF EXISTS `countries`;
CREATE TABLE IF NOT EXISTS `countries` (
  `country_id` int(11) NOT NULL auto_increment,
  `pl` varchar(128) NOT NULL,
  `en` varchar(128) NOT NULL,
  `short` char(2) NOT NULL,
  `list_default_pl` int(1) NOT NULL default '0',
  `sort_pl` varchar(128) NOT NULL,
  `list_default_en` int(1) NOT NULL default '0',
  `sort_en` varchar(128) NOT NULL,
  PRIMARY KEY  (`country_id`),
  UNIQUE KEY `short` (`short`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

