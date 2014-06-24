-- 2014-06-02 Detailed cache access logging
-- @author: Bogus z Polska

create table CACHE_ACCESS_LOGS (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  event_date datetime NOT NULL,
  cache_id int(11) NOT NULL,
  user_id int(11),
  source varchar(2) not null comment 'B - browser - main opencaching site, M - mobile, O - okapi, J - Java',
  event varchar(32) not null comment 'viewcache, viewlogs, ... ',
  ip_addr varchar(32) not null comment 'request IP',
  user_agent varchar(128) comment 'User-Agent HTTP header',
  forwarded_for varchar(128) comment 'X-Forwarded-For HTTP header',
  okapi_consumer_key varchar(20) comment 'OKAPI consumer key',
  info_text varchar(2048) comment 'Free info text',
  PRIMARY KEY (id),
  KEY access_logs_cache_id (cache_id),
  KEY access_logs_user_id (user_id)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- alter table CACHE_ACCESS_LOGS add column info_text varchar(2048) comment 'Free info text';
