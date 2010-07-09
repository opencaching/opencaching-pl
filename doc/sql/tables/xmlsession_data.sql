SET NAMES 'utf8';
DROP TABLE IF EXISTS `xmlsession_data`;
CREATE TABLE IF NOT EXISTS `xmlsession_data` (
  `session_id` int(11) NOT NULL default '0',
  `object_type` int(11) NOT NULL default '0',
  `object_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`session_id`,`object_type`,`object_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



