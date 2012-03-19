SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_visits`;
CREATE TABLE IF NOT EXISTS `cache_visits` (
  `cache_id` int(11) NOT NULL default '0',
  `user_id_ip` varchar(15) NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  `last_visited` datetime default NULL,
  PRIMARY KEY  (`cache_id`,`user_id_ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


