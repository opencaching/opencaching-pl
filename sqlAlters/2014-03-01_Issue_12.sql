-- 2014-03-01 issue #12
-- https://code.google.com/p/opencaching-pl/issues/detail?id=12&colspec=ID%20Type%20Status%20Summary%20Priority%20Component%20Reporter%20Owner%20Modified%20Stars
-- @author: Andrzej Łza Woźniak

ALTER TABLE `user` CHANGE `no_htmledit_flag` `power_trail_email` TINYINT(1) NOT NULL DEFAULT '1' COMMENT 'sending notification emails from geoPaths module if value = 1';
UPDATE `user` SET `power_trail_email`=1 WHERE 1

-- end

-- roll back: ALTER TABLE `user` CHANGE `power_trail_email` `no_htmledit_flag` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'unused';
