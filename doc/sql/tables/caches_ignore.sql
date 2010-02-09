SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_ignore`;
CREATE TABLE IF NOT EXISTS `cache_ignore` (
  `cache_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `cache_id` (`cache_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

