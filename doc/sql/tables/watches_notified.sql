SET NAMES 'utf8';
DROP TABLE IF EXISTS `watches_notified`;
CREATE TABLE IF NOT EXISTS `watches_notified` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `object_id` int(11) NOT NULL default '0',
  `object_type` int(11) NOT NULL default '0',
  `date_processed` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `user_id` (`user_id`,`object_id`,`object_type`),
  KEY `object_id` (`object_id`),
  KEY `date_processed` (`date_processed`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



