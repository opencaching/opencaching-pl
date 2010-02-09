SET NAMES 'utf8';
DROP TABLE IF EXISTS `map_settings`;
CREATE TABLE IF NOT EXISTS `map_settings` (
  `user_id` int(11) NOT NULL,
  `unknown` int(1) default '1',
  `traditional` int(1) default '1',
  `multicache` int(1) default '1',
  `virtual` int(1) default '1',
  `webcam` int(1) default '1',
  `event` int(1) default '1',
  `quiz` int(1) default '1',
  `math` int(1) default '1',
  `mobile` int(1) default '1',
  `drivein` int(1) default '1',
  `ignored` int(1) default '0',
  `own` int(1) default '1',
  `found` int(1) default '1',
  `notyetfound` int(1) default '1',
  `geokret` int(1) default '1',
  `showsign` int(1) default '0',
  `showwp` int(1) NOT NULL default '0',
  `active` int(1) default '1',
  `notactive` int(1) default '0',
  `maptype` int(1) default '3',
  `cachelimit` int(1) default '4',
  `cachesort` int(1) default '1',
  `archived` int(1) default '0',
  `be_ftf` int(1) default '0',
  `de` int(1) default '1',
  `pl` int(1) default '1',
  `min_score` int(1) NOT NULL default '-3',
  `max_score` int(1) NOT NULL default '3',
  `noscore` int(1) NOT NULL default '1',
  PRIMARY KEY  (`user_id`),
  KEY `min_score` (`min_score`),
  KEY `max_score` (`max_score`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


