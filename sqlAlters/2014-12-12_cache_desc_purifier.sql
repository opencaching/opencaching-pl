-- 2014-12-12 Store information, that HTML Purifier has been used when saving geocache description
-- @author: Bogus z Polska
 
-- THIS IS SPARTAAAAAA!
alter table `cache_desc` modify column desc_html tinyint(1) not null default '0' comment 'Format in which `desc` column is encoded: 0 - DO NOT USE (unknown format based on HTML); 1 - unsafe HTML (needs to be purified before it is included on a HTML page); 2 - safe HTML (may be included "as is" on HTML pages, without any further processing).';
alter table `cache_desc` modify column desc_htmledit tinyint(1) not null default '0' comment 'Unused';
alter table `cache_desc` modify column `desc` mediumtext default null comment 'HTML formatted geocache description';
alter table `cache_desc` modify column short_desc mediumtext default null comment 'Plain text short description';
alter table `cache_desc` modify column hint mediumtext default null comment 'HTML-escaped hint, will contain <br />s';
