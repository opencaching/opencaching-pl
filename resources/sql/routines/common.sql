DELIMITER ;;

-- DELIMITER must be in the first line.
-- Do not include the delimiter (double semicolon) in comments or strings.

-- Changes will be automatically installed on production sites.
-- On developer sites, use http://local.opencaching.pl/Admin.DbUpdate/run


--
-- Just do nothing
--
DROP PROCEDURE IF EXISTS nop;;

CREATE PROCEDURE nop()
BEGIN
END;;


DELIMITER ;
