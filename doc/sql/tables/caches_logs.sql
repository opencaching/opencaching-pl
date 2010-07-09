SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_logs`;
CREATE TABLE IF NOT EXISTS `cache_logs` (
  `id` int(11) NOT NULL auto_increment,
  `cache_id` int(11) default NULL,
  `user_id` int(11) default NULL,
  `type` int(11) default NULL,
  `date` datetime default NULL,
  `text` mediumtext,
  `text_html` tinyint(1) NOT NULL default '0',
  `text_htmledit` tinyint(1) NOT NULL default '0',
  `last_modified` datetime default NULL,
  `uuid` varchar(36) default NULL,
  `picturescount` int(11) NOT NULL default '0',
  `mp3count` int(11) NOT NULL default '0',
  `date_created` datetime NOT NULL default '0000-00-00 00:00:00',
  `owner_notified` int(1) NOT NULL default '0',
  `node` tinyint(4) NOT NULL default '0',
  `deleted` tinyint(1) NOT NULL default '0',
  `encrypt` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `cache_id` (`cache_id`),
  KEY `user_id` (`user_id`),
  KEY `date` (`date`),
  KEY `owner_notified` (`owner_notified`),
  KEY `last_modified` (`last_modified`),
  KEY `type` (`type`),
  KEY `date_created` (`date_created`),
  KEY `deleted` (`deleted`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

