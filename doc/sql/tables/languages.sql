SET NAMES 'utf8';
DROP TABLE IF EXISTS `languages`;
CREATE TABLE IF NOT EXISTS `languages` (
  `id` int(11) NOT NULL auto_increment,
  `short` char(2) NOT NULL,
  `pl` varchar(60) NOT NULL,
  `en` varchar(60) NOT NULL,
  `list_default_pl` int(1) NOT NULL default '0',
  `list_default_en` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `short` (`short`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1; 


