SET NAMES 'utf8';
DROP TABLE IF EXISTS `search_doubles`;
CREATE TABLE IF NOT EXISTS `search_doubles` (
  `hash` int(10) unsigned NOT NULL,
  `word` varchar(30) NOT NULL,
  `simple` varchar(30) NOT NULL,
  PRIMARY KEY  (`hash`,`word`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



