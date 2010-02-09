SET NAMES 'utf8';
DROP TABLE IF EXISTS `email_user`;
CREATE TABLE IF NOT EXISTS `email_user` (
  `id` int(11) NOT NULL auto_increment,
  `ipaddress` varchar(20) NOT NULL,
  `date_generated` datetime NOT NULL default '0000-00-00 00:00:00',
  `from_user_id` int(11) NOT NULL default '0',
  `from_email` varchar(60) NOT NULL,
  `to_user_id` int(11) NOT NULL default '0',
  `to_email` varchar(60) NOT NULL,
  `mail_subject` varchar(255) NOT NULL,
  `mail_text` text NOT NULL,
  `send_emailaddress` int(1) NOT NULL default '0',
  `date_sent` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `from_user_id` (`from_user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


