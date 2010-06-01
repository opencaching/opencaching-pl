SET NAMES 'utf8';
DROP TABLE IF EXISTS `approval_status`;
CREATE TABLE IF NOT EXISTS `approval_status` (
  `cache_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `date_approval` datetime default NULL,
  PRIMARY KEY  (`cache_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
