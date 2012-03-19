SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_type`;
CREATE TABLE IF NOT EXISTS `cache_type` (
  `id` int(11) NOT NULL auto_increment,
  `sort` int(11) NOT NULL default '100',
  `short` varchar(10) NOT NULL,
  `pl` varchar(60) NOT NULL,
  `en` varchar(60) NOT NULL,
  `icon_large` varchar(60) NOT NULL,
  `icon_small` varchar(60) NOT NULL,
  `color` varchar(7) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


