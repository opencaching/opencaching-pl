-- 2014-06-28 Save history of user's nick
-- @author: Bogus z Polska

 create table user_nick_history (
id int(11) NOT NULL AUTO_INCREMENT COMMENT 'PK',
user_id int(11) NOT NULL COMMENT 'FK to user.user_id',
date_from datetime NOT NULL COMMENT 'Start date of the nick value',
date_to datetime COMMENT 'End date of the nick value, NULL if current',
username varchar(60) NOT NULL COMMENT 'The actual nick in a given period of time',
change_comment TEXT COMMENT 'Change comment',
change_by_user_id int(11) COMMENT 'User who changed the nick, FT to user.user_id',
PRIMARY KEY (id),
KEY user_nick_hist_user_id (user_id)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE user ENGINE=InnoDB;
