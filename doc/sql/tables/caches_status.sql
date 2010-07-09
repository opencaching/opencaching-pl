SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_status`;
CREATE TABLE IF NOT EXISTS `cache_status` (
  `id` int(11) NOT NULL auto_increment,
  `pl` varchar(60) NOT NULL,
  `en` varchar(60) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


