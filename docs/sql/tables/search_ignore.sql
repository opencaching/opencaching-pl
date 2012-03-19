SET NAMES 'utf8';
DROP TABLE IF EXISTS `search_ignore`;
CREATE TABLE IF NOT EXISTS `search_ignore` (
  `word` varchar(30) NOT NULL,
  PRIMARY KEY  (`word`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



