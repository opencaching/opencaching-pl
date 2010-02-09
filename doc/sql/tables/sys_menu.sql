SET NAMES 'utf8';
DROP TABLE IF EXISTS `sys_menu`;
CREATE TABLE IF NOT EXISTS `sys_menu` (
  `id` smallint(6) NOT NULL auto_increment,
  `id_string` varchar(80) NOT NULL,
  `title` varchar(80) NOT NULL,
  `menustring` varchar(80) NOT NULL,
  `access` tinyint(4) NOT NULL,
  `href` varchar(80) NOT NULL,
  `visible` tinyint(1) NOT NULL,
  `parent` smallint(6) NOT NULL,
  `position` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id_string` (`id_string`),
  KEY `parent` (`parent`,`position`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



