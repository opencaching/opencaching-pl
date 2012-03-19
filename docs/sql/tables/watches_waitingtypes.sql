SET NAMES 'utf8';
DROP TABLE IF EXISTS `watches_waitingtypes`;
CREATE TABLE IF NOT EXISTS `watches_waitingtypes` (
  `id` int(11) NOT NULL auto_increment,
  `watchtype` varchar(30) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



