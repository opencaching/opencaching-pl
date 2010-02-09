SET NAMES 'utf8';
DROP TABLE IF EXISTS `news`;
CREATE TABLE IF NOT EXISTS `news` (
  `id` int(11) NOT NULL auto_increment,
  `date_posted` datetime NOT NULL default '0000-00-00 00:00:00',
  `content` text NOT NULL,
  `topic` int(11) NOT NULL default '0',
  `display` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



