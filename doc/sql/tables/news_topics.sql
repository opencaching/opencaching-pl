SET NAMES 'utf8';
DROP TABLE IF EXISTS `news_topics`;
CREATE TABLE IF NOT EXISTS `news_topics` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



