SET NAMES 'utf8';
DROP TABLE IF EXISTS `scores`;
CREATE TABLE IF NOT EXISTS `scores` (
  `id` int(11) NOT NULL auto_increment,
  `cache_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `score` float NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `cache_id` (`cache_id`),
  KEY `user_id` (`user_id`),
  KEY `score` (`score`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



