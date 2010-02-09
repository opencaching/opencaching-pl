SET NAMES 'utf8';
DROP TABLE IF EXISTS `sys_logins`;
CREATE TABLE IF NOT EXISTS `sys_logins` (
  `id` int(11) NOT NULL auto_increment,
  `remote_addr` varchar(15) NOT NULL,
  `success` tinyint(1) NOT NULL,
  `timestamp` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `timestamp` (`timestamp`),
  KEY `remote_addr` (`remote_addr`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



