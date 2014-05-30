-- 2014-05-28 Detailed cache access logging - OKAPI extension
-- @author: Bogus z Polska

alter table CACHE_ACCESS_LOGS add column (okapi_consumer_key varchar(20) comment 'OKAPI consumer key');
