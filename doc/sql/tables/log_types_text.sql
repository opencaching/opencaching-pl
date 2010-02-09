SET NAMES 'utf8';
DROP TABLE IF EXISTS `log_types_text`;
CREATE TABLE IF NOT EXISTS `log_types_text` (
  `id` int(11) NOT NULL auto_increment,
  `log_types_id` int(11) NOT NULL default '0',
  `lang` char(2) NOT NULL,
  `text_combo` varchar(255) NOT NULL,
  `text_listing` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `lang` (`lang`,`log_types_id`),
  KEY `log_types_id` (`log_types_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


