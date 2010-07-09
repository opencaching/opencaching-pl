SET NAMES 'utf8';
DROP TABLE IF EXISTS `search_words`;
CREATE TABLE IF NOT EXISTS `search_words` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `word` varchar(255) NOT NULL,
  `simple` varchar(30) NOT NULL,
  `hash` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `hash` (`hash`,`word`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



