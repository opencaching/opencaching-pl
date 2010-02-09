SET NAMES 'utf8';
DROP TABLE IF EXISTS `email_schemas`;
CREATE TABLE IF NOT EXISTS `email_schemas` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `shortdesc` varchar(100) NOT NULL,
  `text` varchar(10000) NOT NULL,
  `receiver` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

