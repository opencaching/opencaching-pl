--
-- Fixes of issue https://github.com/opencaching/opencaching-pl/issues/322
--

-- Remove 2016 election tables
DROP TABLE rr_ocpl_vote_2016;
DROP TABLE rr_ocpl_candidates_2016;

-- Remove notification column from user table
ALTER TABLE `user`
  DROP `rr_2016_notified_1`,
  DROP `rr_2016_notified_2`;

