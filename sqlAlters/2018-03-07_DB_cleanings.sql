-- 2018-03-07
-- @author: deg-pl

-- See issue #1113. Last bulletin code I removed yesterday
ALTER TABLE `user` DROP `get_bulletin`;

-- After password reminder refactoring, new_pw_date is not used
ALTER TABLE `user` DROP `new_pw_date`;
