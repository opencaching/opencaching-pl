
-- Column to store powertrail_only map filter checkbox value for user

alter table `map_settings` add column `powertrail_only` int(1) default 0 after `be_ftf`;