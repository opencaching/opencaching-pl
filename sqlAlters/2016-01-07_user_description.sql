-- 2016-01-07 Extend user-description 1024->4096
-- github.com/opencaching/opencaching-pl/issues/218
-- @author: miklobit

ALTER TABLE  `user` CHANGE  `description`  `description` VARCHAR( 4096 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;
