SET NAMES 'utf8';
DROP TABLE IF EXISTS `nodes`;
CREATE TABLE IF NOT EXISTS `nodes` (
  `id` tinyint(4) NOT NULL auto_increment,
  `name` varchar(60) NOT NULL,
  `url` varchar(260) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



