SET NAMES 'utf8';
DROP TABLE IF EXISTS `logentries`;
CREATE TABLE IF NOT EXISTS `logentries` (
  `id` int(11) NOT NULL auto_increment,
  `module` varchar(30) NOT NULL,
  `eventid` int(11) NOT NULL default '0',
  `userid` int(11) NOT NULL default '0',
  `objectid1` int(11) NOT NULL default '0',
  `objectid2` int(11) NOT NULL default '0',
  `logtext` mediumtext NOT NULL,
  `details` blob NOT NULL,
  `logtime` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; 


