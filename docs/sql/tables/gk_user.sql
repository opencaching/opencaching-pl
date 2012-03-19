SET NAMES 'utf8';
DROP TABLE IF EXISTS `gk_user`;
CREATE TABLE IF NOT EXISTS `gk_user` (
  `id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


