--
-- Table for keeping info about admin notes
--

CREATE TABLE IF NOT EXISTS `admin_user_notes` (
  `note_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `cache_id` int(11) DEFAULT NULL,
  `automatic` tinyint(1) DEFAULT NULL COMMENT 'bool note type',
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `content` varchar(4096) DEFAULT NULL,
  PRIMARY KEY (`note_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

