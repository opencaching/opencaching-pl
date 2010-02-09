SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_rating`;
CREATE TABLE IF NOT EXISTS `cache_rating` (
  `cache_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY  (`cache_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


