SET NAMES 'utf8';
DROP TABLE IF EXISTS `queries`;
CREATE TABLE IF NOT EXISTS `queries` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(60) NOT NULL,
  `options` blob NOT NULL,
  `uuid` varchar(36) NOT NULL,
  `filters_count` int(11) NOT NULL default '0',
  `last_queried` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `UUID` (`uuid`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



