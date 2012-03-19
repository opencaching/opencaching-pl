SET NAMES 'utf8';
DROP TABLE IF EXISTS `chowner`;
CREATE TABLE IF NOT EXISTS `chowner` (
  `id` int(11) NOT NULL auto_increment,
  `cache_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

