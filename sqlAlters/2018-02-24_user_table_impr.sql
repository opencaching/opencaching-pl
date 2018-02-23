-- 2018-02-24
-- @author: deg-pl

ALTER TABLE `user` CHANGE `new_pw_code` `new_pw_code` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Code to change password';
ALTER TABLE `user` CHANGE `new_email_code` `new_email_code` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Code to change email';

ALTER TABLE `user` ADD `new_pw_exp` DATETIME NULL DEFAULT NULL COMMENT 'new_pw_code expiration date' AFTER `new_pw_code`;
ALTER TABLE `user` ADD `new_email_exp` DATETIME NULL DEFAULT NULL COMMENT 'new_email_code expiration date' AFTER `new_email_code`;
