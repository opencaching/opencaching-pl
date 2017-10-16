-- 2017-10-13 
-- @author: kojoty


-- sysSessionsAfterInsert trigger is no more needed
-- - user.last_log is updated manually
DROP TRIGGER IF EXISTS sysSessionsAfterInsert;


-- this column is never used 
ALTER TABLE `user` DROP `login_faults`;


