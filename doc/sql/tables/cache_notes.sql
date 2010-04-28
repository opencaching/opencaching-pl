SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_notes`;
CREATE TABLE IF NOT EXISTS `cache_notes` (
  `note_id` int(11) NOT NULL auto_increment,
  `cache_id` int(11) default NULL,
  `user_id` int(11) default NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `desc_html` tinyint(1) NOT NULL default '1',
  `desc` text NOT NULL,
  PRIMARY KEY  (`note_id`),
  KEY `cache_id` (`cache_id`),
  KEY `user_id` (`user_id`),
  KEY `date` (`date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='cache notes' ;
