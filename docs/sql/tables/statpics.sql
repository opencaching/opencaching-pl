SET NAMES 'utf8';
DROP TABLE IF EXISTS `statpics`;
CREATE TABLE IF NOT EXISTS `statpics` (
  `id` int(11) NOT NULL auto_increment,
  `tplpath` varchar(200) NOT NULL,
  `previewpath` varchar(200) NOT NULL,
  `description` varchar(80) NOT NULL,
  `maxtextwidth` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



