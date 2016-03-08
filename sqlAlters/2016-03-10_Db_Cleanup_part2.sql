--
-- There are a few unused tables which probably can be safety removed.
-- This is part2. of changes
--

--
-- nodes table contains never used in code, static values
--
DROP TABLE nodes;

--
-- object_types table contains never used in code, static values
--
DROP TABLE object_types;

--
-- search_doubles table is never used in code
--
DROP TABLE search_doubles;

--
-- sys_cron table is never used in code
--
DROP TABLE sys_cron;

--
-- sys_menu table contains never used in code, static values
--
DROP TABLE sys_menu;

--
-- sys_temptables table is never used in code
--
DROP TABLE sys_temptables;

--
-- tables rr_ocpl_* from years 2009-2015 are not used in current code
-- I (kojoty) backup the data from these tables and now it can be safty dropped from server
--
DROP TABLE rr_ocpl_candidates_2009;
DROP TABLE rr_ocpl_candidates_2010;
DROP TABLE rr_ocpl_candidates_2011;
DROP TABLE rr_ocpl_candidates_2012;
DROP TABLE rr_ocpl_candidates_2013;
DROP TABLE rr_ocpl_candidates_2014;
DROP TABLE rr_ocpl_candidates_2015;

DROP TABLE rr_ocpl_vote_2009;
DROP TABLE rr_ocpl_vote_2010;
DROP TABLE rr_ocpl_vote_2011;
DROP TABLE rr_ocpl_vote_2012;
DROP TABLE rr_ocpl_vote_2013;
DROP TABLE rr_ocpl_vote_2014;
DROP TABLE rr_ocpl_vote_2015;

