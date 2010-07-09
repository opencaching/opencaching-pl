SET NAMES 'utf8';
DROP TABLE IF EXISTS `removed_objects`;
CREATE TABLE IF NOT EXISTS `removed_objects` (
  `id` int(11) NOT NULL auto_increment,
  `localID` int(11) NOT NULL default '0',
  `uuid` varchar(36) NOT NULL,
  `type` int(1) NOT NULL default '0',
  `removed_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `node` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `UUID` (`uuid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



