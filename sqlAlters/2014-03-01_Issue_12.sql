-- 2014-03-01 issue #12
-- https://github.com/opencaching/opencaching-pl/issues/12
-- @author: Andrzej Łza Woźniak

ALTER TABLE `user` CHANGE `no_htmledit_flag` `power_trail_email` TINYINT(1) NOT NULL DEFAULT '1' COMMENT 'sending notification emails from geoPaths module if value = 1';
UPDATE `user` SET `power_trail_email`=1 WHERE 1

-- end

-- roll back: ALTER TABLE `user` CHANGE `power_trail_email` `no_htmledit_flag` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'unused';
