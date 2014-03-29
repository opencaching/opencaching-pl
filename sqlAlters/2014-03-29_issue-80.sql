-- 2014-03-29 Fixing issue 80
-- @author: boguslaw.szczepanowski

alter table cache_size add column ro varchar(60) not null;
alter table cache_status add column ro varchar(60) not null;
alter table cache_type add column ro varchar(60) not null;
alter table countries add column ro varchar(128) not null;
alter table countries add column list_default_ro int(1) not null default 0;
alter table countries add column sort_ro varchar(128) not null;
alter table languages add column ro varchar(60) not null;
alter table languages add column list_default_ro int(1) not null default 0;
alter table log_types add column ro varchar(60) not null;
alter table waypoint_type add column ro varchar(60) not null;

-- need to consult code about this
alter table map_settings add column ro int(1) not null default 1;
alter table map_settings_v2 add column ro int(1) not null default 1;
