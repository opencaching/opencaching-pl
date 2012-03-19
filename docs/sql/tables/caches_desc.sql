SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_desc`;
CREATE TABLE IF NOT EXISTS `cache_desc` (
  `id` int(11) NOT NULL auto_increment,
  `cache_id` int(11) default NULL,
  `language` char(2) default NULL,
  `desc` mediumtext,
  `desc_html` tinyint(1) NOT NULL default '0',
  `desc_htmledit` tinyint(1) NOT NULL default '0',
  `hint` mediumtext,
  `short_desc` varchar(120) default NULL,
  `date_created` datetime NOT NULL,
  `last_modified` datetime default NULL,
  `uuid` varchar(36) default NULL,
  `node` tinyint(4) NOT NULL default '0',
  `rr_comment` mediumtext NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `cache_id` (`cache_id`,`language`),
  KEY `last_modified` (`last_modified`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

