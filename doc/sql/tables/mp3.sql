SET NAMES 'utf8';

DROP TABLE IF EXISTS `mp3`;
CREATE TABLE IF NOT EXISTS `mp3` (
  `id` int(11) NOT NULL auto_increment,
  `uuid` varchar(36) NOT NULL,
  `url` varchar(255) NOT NULL,
  `last_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `title` varchar(250) default NULL,
  `date_created` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_url_check` datetime NOT NULL default '0000-00-00 00:00:00',
  `object_id` int(11) NOT NULL default '0',
  `object_type` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `local` int(1) NOT NULL default '1',
  `unknown_format` int(1) NOT NULL default '0',
  `display` int(1) NOT NULL default '1',
  `node` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `last_modified` (`last_modified`),
  KEY `url` (`url`),
  KEY `title` (`title`),
  KEY `object_id` (`object_id`),
  KEY `uuid` (`uuid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



