SET NAMES 'utf8';
DROP TABLE IF EXISTS `sys_cron`;
CREATE TABLE IF NOT EXISTS `sys_cron` (
  `name` varchar(60) NOT NULL,
  `last_run` datetime NOT NULL,
  PRIMARY KEY  (`name`),
  KEY `last_run` (`last_run`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



