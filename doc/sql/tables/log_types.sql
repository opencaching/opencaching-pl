SET NAMES 'utf8';
DROP TABLE IF EXISTS `log_types`;
CREATE TABLE IF NOT EXISTS `log_types` (
  `id` int(11) NOT NULL auto_increment,
  `cache_status` int(1) NOT NULL default '0',
  `permission` char(1) NOT NULL,
  `pl` varchar(60) NOT NULL,
  `en` varchar(60) NOT NULL,
  `icon_small` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


