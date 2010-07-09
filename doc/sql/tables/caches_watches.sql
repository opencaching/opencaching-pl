SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_watches`;
CREATE TABLE IF NOT EXISTS `cache_watches` (
  `id` int(11) NOT NULL auto_increment,
  `cache_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `last_executed` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `cache_id` (`cache_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

