SET NAMES 'utf8';
DROP TABLE IF EXISTS `routes`;
CREATE TABLE IF NOT EXISTS `routes` (
  `route_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(60) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `radius` tinyint(1) default NULL,
   `options` blob NOT NULL,
  PRIMARY KEY  (`route_id`),
  KEY `name` (`name`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='routes name';
