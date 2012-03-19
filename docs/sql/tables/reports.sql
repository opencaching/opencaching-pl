SET NAMES 'utf8';
DROP TABLE IF EXISTS `reports`;
CREATE TABLE IF NOT EXISTS `reports` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `cache_id` int(11) NOT NULL,
  `type` tinyint(10) NOT NULL default '4',
  `text` varchar(4096) NOT NULL,
  `note` text NOT NULL,
  `submit_date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `status` tinyint(3) NOT NULL default '0',
  `changed_by` int(11) NOT NULL default '0',
  `changed_date` timestamp NULL default NULL,
  `responsible_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



