-- 2017-09-07: add indexes for truncating logs; see https://github.com/opencaching/opencaching-pl/issues/1200
-- @author: following5

ALTER TABLE `approval_status` ADD INDEX (`date_approval`);
ALTER TABLE `logentries` ADD INDEX (`logtime`);
ALTER TABLE `email_user` ADD INDEX (`date_generated`);
ALTER TABLE `CACHE_ACCESS_LOGS` ADD INDEX (`event_date`);
