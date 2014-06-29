-- 2014-06-29 Issue #128, keep original geocache's owner in DB
-- @author: Bogus z Polska
 
alter table caches add column org_user_id int(11) COMMENT 'Origianl user_id, who created the geocache';

create index caches_org_user_id on caches(org_user_id);  

create index chowner_user_id on chowner(user_id);  
create index chowner_cache_id on chowner(cache_id);  

alter table pictures engine=innodb;
alter table chowner engine=innodb;
