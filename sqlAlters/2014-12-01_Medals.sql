-- table to store users medals --

CREATE TABLE IF NOT EXISTS `medals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `medal_type` int(11) NOT NULL,
  `prized_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- column added 2015-01-01
ALTER TABLE `medals` ADD `medal_level` INT NOT NULL ;
