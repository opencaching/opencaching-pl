SET NAMES 'utf8';
DROP TABLE IF EXISTS `gk_item`;
CREATE TABLE IF NOT EXISTS `gk_item` (
  `id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `description` longtext NOT NULL,
  `userid` int(11) NOT NULL,
  `datecreated` datetime NOT NULL,
  `datemodified` datetime NOT NULL,
  `distancetravelled` float NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `typeid` int(11) NOT NULL,
  `stateid` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `stateid` (`stateid`),
  KEY `typeid` (`typeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


