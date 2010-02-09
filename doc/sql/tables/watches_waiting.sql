SET NAMES 'utf8';
DROP TABLE IF EXISTS `watches_waiting`;
CREATE TABLE IF NOT EXISTS `watches_waiting` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `object_id` int(11) NOT NULL default '0',
  `object_type` int(11) NOT NULL default '0',
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `watchtext` mediumtext NOT NULL,
  `watchtype` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `object_id` (`object_id`),
  KEY `date_added` (`date_added`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



