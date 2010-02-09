SET NAMES 'utf8';
DROP TABLE IF EXISTS `xmlsession`;
CREATE TABLE IF NOT EXISTS `xmlsession` (
  `id` int(11) NOT NULL auto_increment,
  `last_use` datetime NOT NULL default '0000-00-00 00:00:00',
  `users` int(11) NOT NULL default '0',
  `caches` int(11) NOT NULL default '0',
  `cachedescs` int(11) NOT NULL default '0',
  `cachelogs` int(11) NOT NULL default '0',
  `pictures` int(11) NOT NULL default '0',
  `removedobjects` int(11) NOT NULL default '0',
  `modified_since` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_created` datetime NOT NULL default '0000-00-00 00:00:00',
  `cleaned` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



