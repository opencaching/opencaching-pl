--
-- After oc.pl migration to mysql 5.7 many small issues needs to be fixed
--

-- rr_comment can be NULL (fix for Integrity constraint violation: 1048 Column 'rr_comment' cannot be null)
ALTER TABLE `cache_desc` CHANGE `rr_comment` `rr_comment` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;



