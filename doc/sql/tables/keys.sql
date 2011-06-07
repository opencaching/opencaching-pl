SET NAMES 'utf8';
DROP TABLE IF EXISTS `keys`;
CREATE TABLE IF NOT EXISTS `keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(40) NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'OC User ID for API Key',
  `level` int(2) NOT NULL,
  `ignore_limits` tinyint(1) NOT NULL DEFAULT '0',
  `date_created` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

