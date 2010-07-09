SET NAMES 'utf8';
DROP TABLE IF EXISTS `sysconfig`;
CREATE TABLE IF NOT EXISTS `sysconfig` (
  `name` varchar(60) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY  (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



