SET NAMES 'utf8';
DROP TABLE IF EXISTS `logentries_types`;
CREATE TABLE IF NOT EXISTS `logentries_types` (
  `id` int(11) NOT NULL auto_increment,
  `module` varchar(30) NOT NULL,
  `eventname` varchar(30) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; 


