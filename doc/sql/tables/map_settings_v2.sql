SET NAMES 'utf8';
DROP TABLE IF EXISTS `map_settings_v2`;
CREATE TABLE IF NOT EXISTS `map_settings_v2` (
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
  `active` int(1) default '0',
  `notactive` int(1) default '1',
  `maptype` int(1) default '3',
  `cachelimit` int(1) default '4',
  `cachesort` int(1) default '1',
  `archived` int(1) default '0',
  `be_ftf` int(1) default '0',
  `de` int(1) default '1',
  `pl` int(1) default '1',
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



